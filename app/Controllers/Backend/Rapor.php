<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\NilaiModel;
use App\Models\SantriBaruModel;
use App\Models\SignatureModel;
use App\Models\QrCodeModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Rapor extends BaseController
{
    protected $helpFunctionModel;
    protected $nilaiModel;
    protected $santriBaruModel;
    protected $signatureModel;
    protected $qrCodeModel;

    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->nilaiModel = new NilaiModel();
        $this->santriBaruModel = new SantriBaruModel();
        $this->signatureModel = new SignatureModel();
        $this->qrCodeModel = new QrCodeModel();
        ini_set('memory_limit', '256M');
        set_time_limit(300);
        mb_internal_encoding('UTF-8');
    }

    /**
     * Setup Dompdf configuration dengan pengaturan optimal untuk karakter Arab
     */
    private function setupDompdfConfig()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        // Gunakan font yang mendukung huruf Arab dengan baik
        $options->set('defaultFont', 'Arial Unicode MS');
        // Aktifkan subsetting font untuk dukungan karakter luas
        $options->set('isFontSubsettingEnabled', true);
        // Aktifkan dukungan Unicode untuk karakter Arab
        $options->set('defaultMediaType', 'print');
        $options->set('isJavascriptEnabled', false);
        // Aktifkan dukungan untuk karakter kompleks seperti Arab
        $options->set('isFontSubsettingEnabled', true);

        return new Dompdf($options);
    }

    /**
     * Output PDF dengan headers yang sesuai
     */
    private function outputPdf($dompdf, $filename)
    {
        // Hapus semua output sebelumnya
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Set header
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        // Output PDF
        echo $dompdf->output();
        exit();
    }

    /**
     * Ambil data santri lengkap dengan nilai dan wali kelas
     */
    private function getSantriDataWithNilai($IdSantri, $IdTpq, $IdTahunAjaran, $semester)
    {
        // Ambil data santri dari tbl_kelas_santri join tbl_santri_baru
        $santri = $this->helpFunctionModel->getDetailSantriByKelasSantri(
            $IdSantri,
            $IdTahunAjaran,
            $IdTpq
        );

        if (empty($santri)) {
            return null;
        }

        // Ambil data nilai berdasarkan semester
        $nilai = $this->nilaiModel->getDataNilaiPerSantri(
            IdTpq: $IdTpq,
            IdTahunAjaran: $IdTahunAjaran,
            IdKelas: $santri['IdKelas'],
            IdSantri: $IdSantri,
            semester: $semester
        );

        // Ambil Nama Wali Kelas dari IdKelas data nilai ke tbl_guru_kelas
        $IdKelas = $santri['IdKelas'];
        $waliKelas = $this->helpFunctionModel->getWaliKelasByIdKelas(IdKelas: $IdKelas, IdTpq: $IdTpq, IdTahunAjaran: $IdTahunAjaran);
        $santri['WaliKelas'] = $waliKelas->Nama;
        // Ambil guru pendamping dari tbl_guru_kelas
        $guruPendamping = $this->helpFunctionModel->getGuruPendampingByIdKelas($IdKelas, $IdTpq, $IdTahunAjaran);
        $santri['GuruPendamping'] = !empty($guruPendamping) ? $guruPendamping : [];

        return [
            'santri' => $santri,
            'nilai' => $nilai
        ];
    }

    /**
     * Siapkan data untuk view rapor
     */
    private function prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester)
    {
        // Ambil data TPQ
        $tpq = $this->helpFunctionModel->getNamaTpqById($IdTpq);

        // Ambil data signature untuk santri ini
        $signatures = $this->signatureModel->where([
            'IdSantri' => $santriData['santri']['IdSantri'],
            'IdTahunAjaran' => $IdTahunAjaran,
            'Semester' => $semester,
            'JenisDokumen' => 'Rapor',
            'Status' => 'active'
        ])->findAll();

        return [
            'santri' => $santriData['santri'],
            'nilai' => $santriData['nilai'],
            'tpq' => $tpq,
            'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
            'semester' => $semester,
            'tanggal' => formatTanggalIndonesia(date('Y-m-d'), 'd F Y'),
            'signatures' => $signatures
        ];
    }

    /**
     * Ambil data summary nilai untuk setiap santri
     */
    private function getSummaryDataForSantri($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        // Ambil data summary menggunakan method yang sama seperti showSumaryPersemester
        $summaryData = $this->nilaiModel->getDataNilaiPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Buat array dataKelas untuk struktur yang sama dengan nilaiSantriPerSemester
        $dataKelas = [];
        foreach ($summaryData->getResult() as $nilai) {
            $dataKelas[$nilai->IdKelas] = $nilai->NamaKelas;
        }

        return [
            'nilai' => $summaryData,
            'dataKelas' => $dataKelas
        ];
    }

    public function index($semester = 'Ganjil')
    {
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdKelas = session()->get('IdKelas');
        $IdGuru = session()->get('IdGuru');
        $lisKelas = $this->helpFunctionModel->getListKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

        // Ambil data santri joint dengan tbl_kelas_santri dan tbl_kelas
        $builder = $this->santriBaruModel->join('tbl_kelas_santri', 'tbl_kelas_santri.IdSantri = tbl_santri_baru.IdSantri');
        $builder->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_kelas_santri.IdKelas');
        $builder->where('tbl_santri_baru.IdTpq', $IdTpq);

        if (is_array($IdKelas)) {
            $builder->whereIn('tbl_kelas_santri.IdKelas', $IdKelas);
        } else {
            $builder->where('tbl_kelas_santri.IdKelas', $IdKelas);
        }

        $builder->where('tbl_kelas_santri.IdTahunAjaran', $IdTahunAjaran);

        $listSantri = $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')->findAll();

        // Ambil data summary nilai untuk setiap santri
        $summaryData = $this->getSummaryDataForSantri($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Ambil data permission guru kelas untuk semua kelas
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdGuru = session()->get('IdGuru');
        $IdKelas = session()->get('IdKelas');

        $guruKelasPermissions = $this->helpFunctionModel->getGuruKelasPermissions($IdTpq, $IdGuru, $IdKelas, $IdTahunAjaran);

        $data = [
            'page_title' => 'Rapor Santri',
            'listKelas' => $lisKelas,
            'listSantri' => $listSantri,
            'nilai' => $summaryData['nilai'],
            'dataKelas' => $summaryData['dataKelas'],
            'semester' => $semester,
            'guruKelasPermissions' => $guruKelasPermissions
        ];

        return view('backend/rapor/index', $data);
    }

    public function getSantriByKelas($IdKelas)
    {
        $santriList = $this->santriBaruModel->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')->where([
            'tbl_santri_baru.IdTpq' => session()->get('IdTpq'),
            'tbl_santri_baru.IdKelas' => $IdKelas,
            'tbl_santri_baru.Active' => 1
        ])->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')->findAll();

        return $this->response->setJSON($santriList);
    }

    public function printPdf($IdSantri, $semester)
    {
        try {
            // Set memory limit dan timeout
            ini_set('memory_limit', '256M');
            set_time_limit(300);
            mb_internal_encoding('UTF-8');

            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');

            // Ambil data santri lengkap dengan nilai dan wali kelas
            $santriData = $this->getSantriDataWithNilai($IdSantri, $IdTpq, $IdTahunAjaran, $semester);

            if (!$santriData) {
                throw new \Exception('Data santri tidak ditemukan');
            }

            // Siapkan data untuk view rapor
            $data = $this->prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester);

            // Load view untuk PDF
            $html = view('backend/rapor/print', $data);

            // Setup Dompdf dan render PDF
            $dompdf = $this->setupDompdfConfig();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = str_replace(' ', '_', $santriData['santri']['NamaSantri']) . '_' . $IdTahunAjaran . '_' . $semester . '.pdf';
            $this->outputPdf($dompdf, $filename);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: printPdf - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function printPdfBulk($IdKelas, $semester)
    {
        try {
            // Set memory limit dan timeout untuk bulk processing
            ini_set('memory_limit', '512M');
            set_time_limit(600);
            mb_internal_encoding('UTF-8');

            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();

            // Ambil semua santri dalam kelas tersebut
            $listSantri = $this->santriBaruModel->where([
                'IdTpq' => $IdTpq,
                'IdKelas' => $IdKelas,
                'Active' => 1
            ])->findAll();

            if (empty($listSantri)) {
                throw new \Exception('Tidak ada santri dalam kelas ini');
            }

            // Setup Dompdf
            $dompdf = $this->setupDompdfConfig();

            // Gabungkan semua HTML
            $combinedHtml = '';
            foreach ($listSantri as $index => $santri) {
                // Ambil data santri lengkap dengan nilai dan wali kelas
                $santriData = $this->getSantriDataWithNilai($santri['IdSantri'], $IdTpq, $IdTahunAjaran, $semester);

                if (!$santriData) {
                    continue; // Skip jika data santri tidak ditemukan
                }

                // Siapkan data untuk view rapor
                $data = $this->prepareRaporData($santriData, $IdTpq, $IdTahunAjaran, $semester);

                // Load view untuk setiap santri
                $html = view('backend/rapor/print', $data);

                // Tambahkan margin bottom jika bukan santri terakhir
                if ($index < count($listSantri) - 1) {
                    $html = '<div style="margin-bottom: 30px;">' . $html . '</div>';
                }

                $combinedHtml .= $html;
            }

            // Load HTML gabungan ke Dompdf
            $dompdf->loadHtml($combinedHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = 'rapor_kelas_' . $IdKelas . '_' . $semester . '.pdf';
            $this->outputPdf($dompdf, $filename);
        } catch (\Exception $e) {
            log_message('error', 'Rapor: printPdfBulk - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Handle tanda tangan wali kelas
     */
    public function ttdWalas($IdSantri, $semester)
    {
        return $this->handleSignature($IdSantri, $semester, 'walas');
    }

    /**
     * Handle tanda tangan kepala sekolah
     */
    public function ttdKepsek($IdSantri, $semester)
    {
        return $this->handleSignature($IdSantri, $semester, 'kepsek');
    }

    /**
     * Generate token unik untuk tanda tangan
     */
    private function generateUniqueToken()
    {
        do {
            $token = base64_encode(random_bytes(24));
            $token = str_replace(['+', '/', '='], ['-', '_', ''], $token); // URL-safe

        } while ($this->signatureModel->where('Token', $token)->first());

        return $token;
    }

    /**
     * Hapus file QR code
     */
    private function deleteQRCodeFile($qrCodeFilename)
    {
        if (!empty($qrCodeFilename)) {
            $qrFilePath = FCPATH . 'uploads/qr/' . $qrCodeFilename;
            if (file_exists($qrFilePath)) {
                return unlink($qrFilePath);
            }
        }
        return false;
    }

    /**
     * Handle tanda tangan (untuk wali kelas dan kepala sekolah)
     */
    private function handleSignature($IdSantri, $semester, $signatureType)
    {
        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');
            $IdKelas = session()->get('IdKelas');

            // Cek permission berdasarkan tbl_guru_kelas
            $guruKelasPermission = $this->helpFunctionModel->checkGuruKelasPermission($IdTpq, $IdGuru, $IdKelas, $IdTahunAjaran);

            if (!$guruKelasPermission) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk kelas ini pada tahun ajaran ini.'
                ]);
            }

            // Cek permission berdasarkan jabatan dari tbl_guru_kelas
            if ($signatureType === 'walas' && $guruKelasPermission['NamaJabatan'] !== 'Wali Kelas') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk menandatangani sebagai wali kelas.'
                ]);
            }

            if ($signatureType === 'kepsek' && $guruKelasPermission['NamaJabatan'] !== 'Kepala Sekolah') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk menandatangani sebagai kepala sekolah.'
                ]);
            }

            // Cek apakah signature sudah ada
            $existingSignature = $this->signatureModel->where([
                'IdSantri' => $IdSantri,
                'IdTpq' => $IdTpq,
                'IdTahunAjaran' => $IdTahunAjaran,
                'IdGuru' => $IdGuru,
                'Semester' => $semester,
                'JenisDokumen' => 'Rapor',
                'Status' => 'active'
            ])->first();

            // Jika ada request untuk replace dan signature sudah ada
            if ($this->request->getPost('replace') && $existingSignature) {
                // Hapus file QR code lama jika ada
                $this->deleteQRCodeFile($existingSignature['QrCode']);
                // Hapus signature lama
                $this->signatureModel->delete($existingSignature['Id']);
            } elseif ($existingSignature && !$this->request->getPost('replace')) {
                $typeName = $signatureType === 'walas' ? 'wali kelas' : 'kepala sekolah';
                return $this->response->setJSON([
                    'status' => 'info',
                    'message' => "Tanda tangan {$typeName} untuk santri ini sudah pernah dibuat pada " . date('d F Y H:i', strtotime($existingSignature['TanggalTtd'])) . '. Apakah Anda ingin menggantinya?',
                    'existing_signature' => true,
                    'existing_id' => $existingSignature['Id']
                ]);
            }

            $kelasData = $this->helpFunctionModel->getIdKelasByTahunAjaranDanSemester($IdTpq, $IdTahunAjaran, $semester, $IdSantri);

            // Generate token unik
            $token = $this->generateUniqueToken();

            // Data untuk disimpan ke tbl_tanda_tangan
            $signatureData = [
                'Token' => $token,
                'IdSantri' => $IdSantri,
                'IdKelas' => !empty($kelasData) ? $kelasData[0]['IdKelas'] : null,
                'IdTahunAjaran' => $IdTahunAjaran,
                'Semester' => $semester,
                'IdGuru' => $IdGuru,
                'IdTpq' => $IdTpq,
                'JenisDokumen' => 'Rapor',
                'StatusValidasi' => 'Valid',
                'TanggalTtd' => date('Y-m-d H:i:s')
            ];

            // Simpan data tanda tangan
            $IdSignature = $this->signatureModel->insert($signatureData);

            if ($IdSignature) {
                // Generate QR Code
                $qrCodeData = $this->generateQRCode($token);

                if ($qrCodeData) {
                    // Update data tanda tangan dengan nama file QR
                    $this->signatureModel->where('Id', $IdSignature)
                        ->set(['QrCode' => $qrCodeData['filename']])
                        ->update();

                    $typeName = $signatureType === 'walas' ? 'wali kelas' : 'kepala sekolah';
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => "Tanda tangan {$typeName} berhasil disimpan dan QR Code telah dibuat."
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'warning',
                        'message' => 'Tanda tangan berhasil disimpan, namun gagal membuat QR Code.'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan tanda tangan.'
                ]);
            }
        } catch (\Exception $e) {
            $typeName = $signatureType === 'walas' ? 'wali kelas' : 'kepala sekolah';
            log_message('error', "Rapor: ttd{$typeName} - Error: " . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate QR Code untuk validasi tanda tangan
     */
    private function generateQRCode($token)
    {
        try {
            // URL untuk validasi tanda tangan
            $validationUrl = base_url("signature/validateSignature/{$token}");

            // Buat direktori jika belum ada
            if (!is_dir(FCPATH . 'uploads/qr')) {
                mkdir(FCPATH . 'uploads/qr', 0777, true);
            }

            // Generate QR Code
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                'scale' => 300,
                'imageBase64' => false,
                'addQuietzone' => true,
                'quietzoneSize' => 4,
            ]);

            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrString = $qrcode->render($validationUrl);

            // Simpan QR code sebagai file SVG
            $filename = 'signature_' . $token . '.svg';
            file_put_contents(FCPATH . 'uploads/qr/' . $filename, $qrString);

            return [
                'filename' => $filename,
                'url' => $validationUrl
            ];
        } catch (\Exception $e) {
            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
            return false;
        }
    }
}
