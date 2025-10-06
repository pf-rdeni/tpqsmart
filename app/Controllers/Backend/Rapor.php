<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\NilaiModel;
use App\Models\SantriBaruModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Rapor extends BaseController
{
    protected $helpFunctionModel;
    protected $nilaiModel;
    protected $santriBaruModel;

    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->nilaiModel = new NilaiModel();
        $this->santriBaruModel = new SantriBaruModel();
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

        return [
            'santri' => $santriData['santri'],
            'nilai' => $santriData['nilai'],
            'tpq' => $tpq,
            'tahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($IdTahunAjaran),
            'semester' => $semester,
            'tanggal' => formatTanggalIndonesia(date('Y-m-d'), 'd F Y')
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
        $listSantri = $this->santriBaruModel->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->where([
                'tbl_santri_baru.IdTpq' => $IdTpq,
                'tbl_santri_baru.Active' => 1
            ])
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas')
            ->findAll();

        $data = [
            'page_title' => 'Rapor Santri',
            'listKelas' => $lisKelas,
            'listSantri' => $listSantri,
            'semester' => $semester
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
}
