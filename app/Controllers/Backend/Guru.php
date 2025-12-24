<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\HelpFunctionModel;
use App\Models\FkpqModel;
use App\Models\SignatureModel;
use App\Models\GuruBerkasModel;
use App\Models\TpqModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Guru extends BaseController
{
    protected $DataModels;
    protected $helpFunction;
    protected $fkpqModel;
    protected $signatureModel;
    protected $guruBerkasModel;
    protected $tpqModel;

    public function __construct()
    {
        $this->DataModels = new GuruModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->fkpqModel = new FkpqModel();
        $this->signatureModel = new SignatureModel();
        $this->guruBerkasModel = new GuruBerkasModel();
        $this->tpqModel = new TpqModel();
    }

    public function show()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        // query data guru berdasarkan IdTpq jika idtpq tidak ada maka akan menampilkan semua data guru
        if ($IdTpq == null) {
            $data = [
                'page_title' => 'Data Guru',
                'guru' => $this->DataModels->findAll(),
                'tpq' => $this->helpFunction->getDataTpq()
            ];
        } else {
            $data = [
                'page_title' => 'Data Guru',
                'guru' => $this->DataModels->where('IdTpq', $IdTpq)->findAll(),
                'tpq' => $this->helpFunction->getDataTpq()
            ];
        }
        return view('backend/guru/guru', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Data Guru',
            'tpq' => $this->helpFunction->getDataTpq()
        ];
        return view('backend/guru/create', $data);
    }

    public function store()
    {
        // Validasi input - semua field wajib kecuali GelarDepan dan GelarBelakang
        $rules = [
            'IdGuru' => 'required|min_length[16]|max_length[16]|is_unique[tbl_guru.IdGuru]',
            'Nama' => 'required',
            'IdTpq' => 'required',
            'TempatTugas' => 'required',
            'TanggalMulaiTugas' => 'required',
            'NoHp' => 'required|min_length[10]|max_length[13]',
            'JenisKelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'TempatLahir' => 'required',
            'TanggalLahir' => 'required',
            'PendidikanTerakhir' => 'required|in_list[SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3]',
            'Alamat' => 'required',
            'Rt' => 'required',
            'Rw' => 'required',
            'KelurahanDesa' => 'required|in_list[Teluk Sasah,Busung,Kuala Sempang,Tanjung Permai,Teluk Lobam]',
            'NoRekBpr' => 'permit_empty|numeric|max_length[11]',
            'NoRekRiauKepri' => 'permit_empty|numeric|max_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Gabungkan gelar dengan nama
        $nama = $this->request->getPost('Nama');
        $gelarDepan = $this->request->getPost('GelarDepan');
        $gelarBelakang = $this->request->getPost('GelarBelakang');

        $namaLengkap = '';
        if (!empty($gelarDepan)) {
            $namaLengkap .= trim($gelarDepan) . ' ';
        }
        $namaLengkap .= trim($nama);
        if (!empty($gelarBelakang)) {
            $namaLengkap .= ', ' . trim($gelarBelakang);
        }

        // Simpan data
        $data = [
            'IdGuru' => $this->request->getPost('IdGuru'),
            'Nama' => $namaLengkap,
            'IdTpq' => $this->request->getPost('IdTpq'),
            'TempatTugas' => $this->request->getPost('TempatTugas'),
            'TanggalMulaiTugas' => $this->request->getPost('TanggalMulaiTugas'),
            'NoHp' => $this->request->getPost('NoHp'),
            'JenisKelamin' => $this->request->getPost('JenisKelamin'),
            'TempatLahir' => $this->request->getPost('TempatLahir'),
            'TanggalLahir' => $this->request->getPost('TanggalLahir'),
            'PendidikanTerakhir' => $this->request->getPost('PendidikanTerakhir'),
            'Alamat' => $this->request->getPost('Alamat'),
            'Rt' => $this->request->getPost('Rt'),
            'Rw' => $this->request->getPost('Rw'),
            'KelurahanDesa' => $this->request->getPost('KelurahanDesa'),
            'NoRekBpr' => $this->request->getPost('NoRekBpr'),
            'NoRekRiauKepri' => $this->request->getPost('NoRekRiauKepri'),
        ];

        $this->DataModels->insert($data);

        return redirect()->to(base_url('backend/guru/show'))->with('success', 'Data guru berhasil ditambahkan');
    }
    //validasi nik
    public function validateNik()
    {
        $nik = $this->request->getPost('IdGuru');
        
        // Validasi format NIK (16 digit angka)
        if (empty($nik)) {
            return $this->response->setJSON([
                'exists' => false,
                'message' => 'NIK harus diisi',
                'data' => null,
                'valid' => false
            ]);
        }
        
        // Validasi format: harus 16 digit angka
        if (!preg_match('/^\d{16}$/', $nik)) {
            return $this->response->setJSON([
                'exists' => false,
                'message' => 'Format NIK tidak valid. NIK harus 16 digit angka.',
                'data' => null,
                'valid' => false
            ]);
        }
        
        // Cek apakah NIK sudah terdaftar
        $exists = $this->DataModels->where('IdGuru', $nik)->first();
        return $this->response->setJSON([
            'exists' => !empty($exists), // true jika NIK ditemukan, false jika tidak
            'message' => !empty($exists) ? 'NIK sudah terdaftar' : 'NIK belum terdaftar',
            'data' => $exists,
            'valid' => true
        ]);
    }

    //fungsi untuk menghapus data guru
    public function delete($id)
    {
        try {
            // Cek apakah data guru ada
            $guru = $this->DataModels->find($id);
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Hapus data guru
            $this->DataModels->delete($id);

            // Jika request AJAX, kembalikan response JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data guru berhasil dihapus'
                ]);
            }

            // Jika bukan AJAX, redirect dengan flash message
            return redirect()->to(base_url('backend/guru/show'))->with('success', 'Data guru berhasil dihapus');
        } catch (\Exception $e) {
            // Jika request AJAX, kembalikan response JSON dengan error
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data guru: ' . $e->getMessage()
                ]);
            }

            // Jika bukan AJAX, redirect dengan flash message error
            return redirect()->to(base_url('backend/guru/show'))->with('error', 'Gagal menghapus data guru: ' . $e->getMessage());
        }
    }

    // Fungsi untuk mengambil data guru
    public function getData($id)
    {
        try {
            $guru = $this->DataModels->find($id);
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Pisahkan nama dan gelar
            $namaParts = explode(',', $guru['Nama']);
            $namaLengkap = trim($namaParts[0]);

            // Cek apakah ada gelar belakang
            if (count($namaParts) > 1) {
                $guru['GelarBelakang'] = trim($namaParts[1]);
            }

            // Cek apakah ada gelar depan
            $namaWords = explode(' ', $namaLengkap);
            if (in_array($namaWords[0], ['dr.', 'Dr.', 'Prof.'])) {
                $guru['GelarDepan'] = $namaWords[0];
                array_shift($namaWords);
                $guru['Nama'] = implode(' ', $namaWords);
            } else {
                $guru['Nama'] = $namaLengkap;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $guru
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data guru: ' . $e->getMessage()
            ]);
        }
    }

    // Fungsi untuk update data guru
    public function update()
    {
        try {
            // Validasi input - semua field wajib kecuali GelarDepan dan GelarBelakang
            $rules = [
                'IdGuru' => 'required|min_length[16]|max_length[16]',
                'Nama' => 'required',
                'IdTpq' => 'required',
                'TempatTugas' => 'required',
                'TanggalMulaiTugas' => 'required',
                'NoHp' => 'required|min_length[10]|max_length[13]',
                'Status' => 'required|in_list[0,1]',
                'JenisKelamin' => 'required|in_list[Laki-laki,Perempuan]',
                'TempatLahir' => 'required',
                'TanggalLahir' => 'required',
                'PendidikanTerakhir' => 'required|in_list[SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3]',
                'Alamat' => 'required',
                'Rt' => 'required',
                'Rw' => 'required',
                'KelurahanDesa' => 'required|in_list[Teluk Sasah,Busung,Kuala Sempang,Tanjung Permai,Teluk Lobam]',
                'NoRekBpr' => 'permit_empty|numeric|max_length[11]',
                'NoRekRiauKepri' => 'permit_empty|numeric|max_length[10]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Cek apakah data guru ada
            $guru = $this->DataModels->find($this->request->getPost('IdGuru'));
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Gabungkan gelar dengan nama
            $nama = $this->request->getPost('Nama');
            $gelarDepan = $this->request->getPost('GelarDepan');
            $gelarBelakang = $this->request->getPost('GelarBelakang');

            $namaLengkap = '';
            if (!empty($gelarDepan)) {
                $namaLengkap .= trim($gelarDepan) . ' ';
            }
            $namaLengkap .= trim($nama);
            if (!empty($gelarBelakang)) {
                $namaLengkap .= ', ' . trim($gelarBelakang);
            }

            // Update data
            $data = [
                'Nama' => $namaLengkap,
                'IdTpq' => $this->request->getPost('IdTpq'),
                'TempatTugas' => $this->request->getPost('TempatTugas'),
                'TanggalMulaiTugas' => $this->request->getPost('TanggalMulaiTugas'),
                'NoHp' => $this->request->getPost('NoHp'),
                'JenisKelamin' => $this->request->getPost('JenisKelamin'),
                'TempatLahir' => $this->request->getPost('TempatLahir'),
                'TanggalLahir' => $this->request->getPost('TanggalLahir'),
                'PendidikanTerakhir' => $this->request->getPost('PendidikanTerakhir'),
                'Alamat' => $this->request->getPost('Alamat'),
                'Rt' => $this->request->getPost('Rt'),
                'Rw' => $this->request->getPost('Rw'),
                'KelurahanDesa' => $this->request->getPost('KelurahanDesa'),
                'Status' => $this->request->getPost('Status'),
                'NoRekBpr' => $this->request->getPost('NoRekBpr'),
                'NoRekRiauKepri' => $this->request->getPost('NoRekRiauKepri'),
            ];

            $this->DataModels->update($this->request->getPost('IdGuru'), $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data guru berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui data guru: ' . $e->getMessage()
            ]);
        }
    }

    // Halaman Pengajuan Insentif
    public function pengajuanInsentif()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        // query data guru berdasarkan IdTpq jika idtpq tidak ada maka akan menampilkan semua data guru
        if ($IdTpq == null) {
            $data = [
                'page_title' => 'Pengajuan Insentif Guru',
                'guru' => $this->DataModels->findAll(),
                'tpq' => $this->helpFunction->getDataTpq()
            ];
        } else {
            $data = [
                'page_title' => 'Pengajuan Insentif Guru',
                'guru' => $this->DataModels->where('IdTpq', $IdTpq)->findAll(),
                'tpq' => $this->helpFunction->getDataTpq()
            ];
        }
        return view('backend/guru/pengajuanInsentif', $data);
    }

    // Update Penerima Insentif via AJAX
    public function updatePenerimaInsentif()
    {
        try {
            $IdGuru = $this->request->getPost('IdGuru');
            $JenisPenerimaInsentif = $this->request->getPost('JenisPenerimaInsentif');

            // Validasi
            if (empty($IdGuru)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Guru harus diisi'
                ]);
            }

            // Validasi pilihan
            $validOptions = ['Guru Ngaji', 'Mubaligh', 'Fardu Kifayah'];
            if (!empty($JenisPenerimaInsentif) && !in_array($JenisPenerimaInsentif, $validOptions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pilihan Penerima Insentif tidak valid'
                ]);
            }

            // Cek apakah data guru ada
            $guru = $this->DataModels->find($IdGuru);
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Update data
            $this->DataModels->update($IdGuru, [
                'JenisPenerimaInsentif' => $JenisPenerimaInsentif
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Penerima Insentif berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui Penerima Insentif: ' . $e->getMessage()
            ]);
        }
    }

    // Generate PDF Surat Pernyataan Tidak Berstatus ASN
    public function printSuratPernyataanAsn($idGuru)
    {
        try {
            helper('nilai');
            
            // Ambil data guru
            $guru = $this->DataModels->find($idGuru);
            if (!$guru) {
                return redirect()->back()->with('error', 'Data guru tidak ditemukan');
            }

            // Format tanggal Indonesia
            $tanggalSurat = formatTanggalIndonesia(date('Y-m-d'), 'd F Y');
            $tanggalLahirFormatted = !empty($guru['TanggalLahir']) ? formatTanggalIndonesia($guru['TanggalLahir'], 'd F Y') : '';

            // Siapkan data untuk view
            $data = [
                'guru' => $guru,
                'tanggalSurat' => $tanggalSurat,
                'tanggalLahirFormatted' => $tanggalLahirFormatted,
                'alamatLengkap' => $guru['Alamat'] . ', RT ' . $guru['Rt'] . ' / RW ' . $guru['Rw'] . ', ' . $guru['KelurahanDesa']
            ];

            // Load view untuk PDF
            $html = view('backend/guru/pdf/suratPernyataanAsn', $data);

            // Setup Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'print');
            $options->set('isJavascriptEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Format filename
            $filename = 'Surat_Pernyataan_ASN_' . str_replace(' ', '_', $guru['Nama']) . '_' . date('Y-m-d') . '.pdf';

            // Clear output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Guru: printSuratPernyataanAsn - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // Generate PDF Surat Pernyataan Tidak Sedang Menerima Insentif
    public function printSuratPernyataanInsentif($idGuru)
    {
        try {
            helper('nilai');
            
            // Ambil data guru
            $guru = $this->DataModels->find($idGuru);
            if (!$guru) {
                return redirect()->back()->with('error', 'Data guru tidak ditemukan');
            }

            // Format tanggal Indonesia
            $tanggalSurat = formatTanggalIndonesia(date('Y-m-d'), 'd F Y');
            $tanggalLahirFormatted = !empty($guru['TanggalLahir']) ? formatTanggalIndonesia($guru['TanggalLahir'], 'd F Y') : '';

            // Siapkan data untuk view
            $data = [
                'guru' => $guru,
                'tanggalSurat' => $tanggalSurat,
                'tanggalLahirFormatted' => $tanggalLahirFormatted,
                'alamatLengkap' => $guru['Alamat'] . ', RT ' . $guru['Rt'] . ' / RW ' . $guru['Rw'] . ', ' . $guru['KelurahanDesa']
            ];

            // Load view untuk PDF
            $html = view('backend/guru/pdf/suratPernyataanInsentif', $data);

            // Setup Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'print');
            $options->set('isJavascriptEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Format filename
            $filename = 'Surat_Pernyataan_Insentif_' . str_replace(' ', '_', $guru['Nama']) . '_' . date('Y-m-d') . '.pdf';

            // Clear output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Guru: printSuratPernyataanInsentif - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // Generate PDF Surat Rekomendasi
    public function printSuratRekomendasi($idGuru)
    {
        try {
            helper('nilai');
            
            // Ambil data guru
            $guru = $this->DataModels->find($idGuru);
            if (!$guru) {
                return redirect()->back()->with('error', 'Data guru tidak ditemukan');
            }

            // Ambil data FKPQ untuk kop lembaga
            $fkpqData = $this->fkpqModel->GetData();
            $fkpqRow = null;
            if (!empty($fkpqData) && !empty($fkpqData[0])) {
                $fkpqRow = $fkpqData[0];
            }

            // Generate atau ambil signature QR code untuk Ketua FKPQ
            $signatureKetuaFkpq = null;
            if (!empty($guru['IdGuru'])) {
                // Cek apakah signature sudah ada untuk surat rekomendasi guru ini
                $existingSignature = $this->signatureModel->where([
                    'IdGuru' => $guru['IdGuru'],
                    'JenisDokumen' => 'Surat Rekomendasi',
                    'SignatureData' => 'Ketua FKPQ',
                    'StatusValidasi' => 'Valid'
                ])->first();

                if ($existingSignature) {
                    // Gunakan signature yang sudah ada
                    $signatureKetuaFkpq = $existingSignature;
                } else {
                    // Generate signature baru
                    helper('signature');
                    $token = generateUniqueSignatureToken($this->signatureModel);
                    $qrCodeData = generateSignatureQRCode($token);

                    if ($qrCodeData) {
                        // Simpan signature ke database
                        $signatureData = [
                            'Token' => $token,
                            'IdGuru' => $guru['IdGuru'],
                            'IdTpq' => $guru['IdTpq'] ?? null,
                            'JenisDokumen' => 'Surat Rekomendasi',
                            'SignatureData' => 'Ketua FKPQ',
                            'QrCode' => $qrCodeData['filename'],
                            'StatusValidasi' => 'Valid',
                            'TanggalTtd' => date('Y-m-d H:i:s')
                        ];

                        $signatureId = $this->signatureModel->insert($signatureData);
                        if ($signatureId) {
                            $signatureKetuaFkpq = $this->signatureModel->find($signatureId);
                        }
                    }
                }
            }

            // Format tanggal Indonesia
            $tanggalSurat = formatTanggalIndonesia(date('Y-m-d'), 'd F Y');

            // Siapkan data untuk view
            $data = [
                'guru' => $guru,
                'tanggalSurat' => $tanggalSurat,
                'alamatLengkap' => $guru['Alamat'] . ', RT ' . $guru['Rt'] . ' / RW ' . $guru['Rw'] . ', ' . $guru['KelurahanDesa'],
                'fkpqData' => $fkpqRow,
                'signatureKetuaFkpq' => $signatureKetuaFkpq
            ];

            // Load view untuk PDF
            $html = view('backend/guru/pdf/suratRekomendasi', $data);

            // Setup Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'print');
            $options->set('isJavascriptEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Format filename
            $filename = 'Surat_Rekomendasi_' . str_replace(' ', '_', $guru['Nama']) . '_' . date('Y-m-d') . '.pdf';

            // Clear output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Guru: printSuratRekomendasi - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // Generate PDF Lampiran (KTP + Rekening BPR)
    public function printLampiran($idGuru)
    {
        try {
            helper('nilai');

            // Ambil data guru
            $guru = $this->DataModels->find($idGuru);
            if (!$guru) {
                return redirect()->back()->with('error', 'Data guru tidak ditemukan');
            }

            // Ambil berkas KTP
            $berkasKtp = $this->guruBerkasModel->getBerkasAktifByGuruAndType($idGuru, 'KTP');

            // Ambil berkas Rekening BPR
            $berkasBpr = $this->guruBerkasModel->getBerkasAktifByGuruAndType($idGuru, 'Buku Rekening', 'BPR');

            // Validasi berkas
            if (!$berkasKtp) {
                return redirect()->back()->with('error', 'Berkas KTP tidak ditemukan. Silakan upload KTP terlebih dahulu.');
            }

            if (!$berkasBpr) {
                return redirect()->back()->with('error', 'Berkas Rekening BPR tidak ditemukan. Silakan upload Buku Rekening BPR terlebih dahulu.');
            }

            // Path file
            $ktpPath = FCPATH . 'uploads/berkas/' . $berkasKtp['NamaFile'];
            $bprPath = FCPATH . 'uploads/berkas/' . $berkasBpr['NamaFile'];

            // Validasi file exists
            if (!file_exists($ktpPath)) {
                return redirect()->back()->with('error', 'File KTP tidak ditemukan di server.');
            }

            if (!file_exists($bprPath)) {
                return redirect()->back()->with('error', 'File Rekening BPR tidak ditemukan di server.');
            }

            // Convert image to base64 untuk PDF
            $ktpBase64 = base64_encode(file_get_contents($ktpPath));
            $ktpMimeType = mime_content_type($ktpPath);
            $ktpDataUri = 'data:' . $ktpMimeType . ';base64,' . $ktpBase64;

            $bprBase64 = base64_encode(file_get_contents($bprPath));
            $bprMimeType = mime_content_type($bprPath);
            $bprDataUri = 'data:' . $bprMimeType . ';base64,' . $bprBase64;

            // Siapkan data untuk view
            $data = [
                'guru' => $guru,
                'ktpDataUri' => $ktpDataUri,
                'bprDataUri' => $bprDataUri,
                'ktpFileName' => $berkasKtp['NamaFile'],
                'bprFileName' => $berkasBpr['NamaFile']
            ];

            // Load view untuk PDF
            $html = view('backend/guru/pdf/lampiran', $data);

            // Setup Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'print');
            $options->set('isJavascriptEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Format filename
            $filename = 'Lampiran_KTP_BPR_' . str_replace(' ', '_', $guru['Nama']) . '_' . date('Y-m-d') . '.pdf';

            // Clear output buffer
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Guru: printLampiran - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // Halaman Berkas Lampiran
    public function showBerkasLampiran()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        // Query data guru berdasarkan IdTpq jika idtpq tidak ada maka akan menampilkan semua data guru
        if ($IdTpq == null) {
            $guruList = $this->DataModels->findAll();
        } else {
            $guruList = $this->DataModels->where('IdTpq', $IdTpq)->findAll();
        }

        // Ambil data berkas untuk setiap guru
        $guruWithBerkas = [];
        foreach ($guruList as $guru) {
            $berkasAktif = $this->guruBerkasModel->getBerkasAktifByGuru($guru['IdGuru']);

            // Organize berkas by type
            $berkasByType = [];
            $bukuRekeningList = [];
            $bukuRekeningDataBerkas = []; // Untuk tracking DataBerkas yang sudah ada

            foreach ($berkasAktif as $berkas) {
                if ($berkas['NamaBerkas'] === 'Buku Rekening') {
                    // Untuk Buku Rekening, simpan sebagai array multiple files
                    // Pastikan tidak ada duplikasi berdasarkan DataBerkas
                    $dataBerkasKey = $berkas['DataBerkas'] ?? 'null';
                    if (!isset($bukuRekeningDataBerkas[$dataBerkasKey])) {
                        $bukuRekeningList[] = $berkas;
                        $bukuRekeningDataBerkas[$dataBerkasKey] = true;
                    }
                } else {
                    // Untuk KTP dan KK, hanya satu file
                    $berkasByType[$berkas['NamaBerkas']] = $berkas;
                }
            }
            // Simpan array Buku Rekening
            if (!empty($bukuRekeningList)) {
                $berkasByType['Buku Rekening'] = $bukuRekeningList;
            }

            // Get TPQ name
            $tpqData = $this->tpqModel->GetData($guru['IdTpq']);
            $namaTpq = !empty($tpqData) && !empty($tpqData[0]) ? $tpqData[0]['NamaTpq'] : '-';

            $guruWithBerkas[] = [
                'guru' => $guru,
                'berkas' => $berkasByType,
                'namaTpq' => $namaTpq
            ];
        }

        $data = [
            'page_title' => 'Berkas Lampiran Guru',
            'guruWithBerkas' => $guruWithBerkas,
            'tpq' => $this->helpFunction->getDataTpq()
        ];

        return view('backend/guru/berkasLampiran', $data);
    }

    // Check Berkas Lampiran untuk validasi sebelum generate PDF
    public function checkBerkasLampiran()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Request harus menggunakan AJAX'
                ]);
            }

            $idGuru = $this->request->getPost('IdGuru');

            if (empty($idGuru)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdGuru tidak tersedia'
                ]);
            }

            // Cek berkas KTP
            $berkasKtp = $this->guruBerkasModel->getBerkasAktifByGuruAndType($idGuru, 'KTP');
            $missingBerkas = [];

            if (!$berkasKtp) {
                $missingBerkas[] = 'KTP';
            } else {
                // Cek file KTP exists
                $ktpPath = FCPATH . 'uploads/berkas/' . $berkasKtp['NamaFile'];
                if (!file_exists($ktpPath)) {
                    $missingBerkas[] = 'KTP';
                }
            }

            // Cek berkas Rekening BPR
            $berkasBpr = $this->guruBerkasModel->getBerkasAktifByGuruAndType($idGuru, 'Buku Rekening', 'BPR');
            if (!$berkasBpr) {
                $missingBerkas[] = 'Rekening BPR';
            } else {
                // Cek file BPR exists
                $bprPath = FCPATH . 'uploads/berkas/' . $berkasBpr['NamaFile'];
                if (!file_exists($bprPath)) {
                    $missingBerkas[] = 'Rekening BPR';
                }
            }

            // Jika ada berkas yang belum di-upload
            if (!empty($missingBerkas)) {
                $berkasList = implode(' dan ', $missingBerkas);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Berkas ' . $berkasList . ' belum di-upload. Silakan upload terlebih dahulu di menu Berkas Lampiran.',
                    'missingBerkas' => $missingBerkas,
                    'uploadUrl' => base_url('backend/guru/showBerkasLampiran')
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Berkas lengkap'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Guru: checkBerkasLampiran - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Upload Berkas
    public function uploadBerkas()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Request harus menggunakan AJAX'
                ]);
            }

            $idGuru = $this->request->getPost('IdGuru');
            $namaBerkas = $this->request->getPost('NamaBerkas');
            $dataBerkas = $this->request->getPost('DataBerkas');
            $berkasCropped = $this->request->getPost('berkas_cropped');
            $editBerkasId = $this->request->getPost('editBerkasId'); // ID berkas yang sedang di-edit

            // Validasi
            if (empty($idGuru)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdGuru tidak tersedia'
                ]);
            }

            if (empty($namaBerkas) || !in_array($namaBerkas, ['KTP', 'KK', 'Buku Rekening'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama Berkas tidak valid'
                ]);
            }

            // Validasi DataBerkas untuk Buku Rekening
            if ($namaBerkas === 'Buku Rekening') {
                if (empty($dataBerkas) || !in_array($dataBerkas, ['BPR', 'BRK'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Nama Bank harus dipilih (BPR atau BRK)'
                    ]);
                }
            } else {
                // Untuk KTP dan KK, DataBerkas harus null
                $dataBerkas = null;
            }

            // Cek apakah guru ada
            $guru = $this->DataModels->find($idGuru);
            if (!$guru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }

            // Validasi IdTpq untuk operator
            $sessionIdTpq = session()->get('IdTpq');
            if ($sessionIdTpq != null && $guru['IdTpq'] != $sessionIdTpq) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengupload berkas guru ini'
                ]);
            }

            // Buat direktori uploads/berkas jika belum ada
            $uploadPath = FCPATH . 'uploads/berkas/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Tentukan apakah ini edit atau upload baru
            $isEditMode = !empty($editBerkasId);
            $berkasToUpdate = null;

            if ($isEditMode) {
                // Mode edit: validasi bahwa berkas yang di-edit ada dan milik guru yang benar
                $berkasToUpdate = $this->guruBerkasModel->find($editBerkasId);
                if (!$berkasToUpdate || $berkasToUpdate['IdGuru'] != $idGuru) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Data berkas tidak ditemukan atau tidak memiliki akses'
                    ]);
                }

                // Untuk edit, tidak perlu deactivate file lain karena kita akan update record yang sama
                // File lama akan dihapus setelah file baru berhasil disimpan
            } else {
                // Mode upload baru: cek apakah sudah ada file aktif dengan NamaBerkas yang sama
                // Untuk Buku Rekening, cek berdasarkan DataBerkas juga
                // Untuk KTP dan KK, hanya satu file aktif (replace yang lama)
                $existingBerkas = $this->guruBerkasModel->getBerkasAktifByGuruAndType($idGuru, $namaBerkas, $dataBerkas);
                if ($existingBerkas) {
                    // Set status file lama menjadi nonaktif
                    $this->guruBerkasModel->deactivateBerkasByType($idGuru, $namaBerkas, $dataBerkas);

                    // Hapus file fisik lama
                    $oldFilePath = $uploadPath . $existingBerkas['NamaFile'];
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
            }

            // Handle base64 image dari crop
            if (!empty($berkasCropped)) {
                if (preg_match('/^data:image\/(\w+);base64,/', $berkasCropped, $type)) {
                    $data = substr($berkasCropped, strpos($berkasCropped, ',') + 1);
                    $data = base64_decode($data);

                    if ($data === false) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal decode base64 image'
                        ]);
                    }

                    $extension = strtolower($type[1] ?? 'jpg');
                    if ($extension === 'jpeg') {
                        $extension = 'jpg';
                    }

                    // Validasi extension
                    if (!in_array($extension, ['jpg', 'png', 'jpeg'])) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Format file tidak didukung. Hanya JPG, JPEG, dan PNG yang diperbolehkan'
                        ]);
                    }

                    // Generate nama file baru (untuk edit dan upload baru)
                    $namaBerkasSanitized = str_replace(' ', '_', $namaBerkas);
                    $fileNamePrefix = $namaBerkasSanitized;
                    if ($dataBerkas) {
                        $fileNamePrefix .= '_' . $dataBerkas;
                    }
                    $newFileName = $fileNamePrefix . '_' . $idGuru . '_' . time() . '.' . $extension;

                    $filePath = $uploadPath . $newFileName;

                    // Simpan file
                    if (file_put_contents($filePath, $data)) {
                        // Pastikan variabel edit mode tersedia (gunakan yang sudah didefinisikan di atas)
                        // $isEditMode dan $berkasToUpdate sudah didefinisikan di baris 783-794
                        $updateResult = false;

                        if ($isEditMode && $berkasToUpdate) {
                            // Mode edit: UPDATE record yang ada
                            $berkasData = [
                                'NamaFile' => $newFileName,
                                'Status' => 1
                            ];

                            // Update record yang ada
                            $updateResult = $this->guruBerkasModel->update($editBerkasId, $berkasData);

                            // Log untuk debugging
                            log_message('info', 'Guru: uploadBerkas - Edit mode. editBerkasId: ' . $editBerkasId . ', Update result: ' . ($updateResult ? 'success' : 'failed') . ', berkasToUpdate exists: ' . ($berkasToUpdate ? 'yes' : 'no'));

                            if ($updateResult) {
                                // Hapus file lama jika berbeda
                                if (isset($berkasToUpdate['NamaFile']) && $berkasToUpdate['NamaFile'] != $newFileName) {
                                    $oldFilePath = $uploadPath . $berkasToUpdate['NamaFile'];
                                    if (file_exists($oldFilePath)) {
                                        @unlink($oldFilePath);
                                    }
                                }

                                return $this->response->setJSON([
                                    'success' => true,
                                    'message' => 'Berkas berhasil diperbarui',
                                    'berkas_url' => base_url('uploads/berkas/' . $newFileName),
                                    'berkas_id' => $editBerkasId
                                ]);
                            } else {
                                // Jika update gagal, log error dan fallback ke insert
                                log_message('error', 'Guru: uploadBerkas - Update failed for editBerkasId: ' . $editBerkasId . ', falling back to insert');
                            }
                        }

                        // Mode upload baru atau update gagal: INSERT record baru
                        // Hanya insert jika BUKAN mode edit, atau jika update gagal
                        if (!$isEditMode || !$berkasToUpdate || !$updateResult) {
                            // Mode upload baru: INSERT record baru
                            $berkasData = [
                                'IdGuru' => $idGuru,
                                'IdTpq' => $guru['IdTpq'],
                                'NamaBerkas' => $namaBerkas,
                                'DataBerkas' => $dataBerkas,
                                'NamaFile' => $newFileName,
                                'Status' => 1
                            ];

                            $this->guruBerkasModel->insert($berkasData);

                            // Log untuk debugging
                            log_message('info', 'Guru: uploadBerkas - Insert mode. editBerkasId: ' . ($editBerkasId ?? 'null') . ', isEditMode: ' . ($isEditMode ? 'true' : 'false') . ', berkasToUpdate: ' . ($berkasToUpdate ? 'exists' : 'null') . ', updateResult: ' . ($updateResult ? 'true' : 'false'));

                            return $this->response->setJSON([
                                'success' => true,
                                'message' => 'Berkas berhasil diupload',
                                'berkas_url' => base_url('uploads/berkas/' . $newFileName),
                                'berkas_id' => $this->guruBerkasModel->getInsertID()
                            ]);
                        }
                    } else {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal menyimpan berkas'
                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Format base64 image tidak valid'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File berkas tidak ditemukan'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Guru: uploadBerkas - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Delete Berkas
    public function deleteBerkas($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Request harus menggunakan AJAX'
                ]);
            }

            // Ambil data berkas
            $berkas = $this->guruBerkasModel->find($id);
            if (!$berkas) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data berkas tidak ditemukan'
                ]);
            }

            // Validasi ownership
            $sessionIdTpq = session()->get('IdTpq');
            if ($sessionIdTpq != null) {
                $guru = $this->DataModels->find($berkas['IdGuru']);
                if (!$guru || $guru['IdTpq'] != $sessionIdTpq) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk menghapus berkas ini'
                    ]);
                }
            }

            // Hapus file fisik
            $uploadPath = FCPATH . 'uploads/berkas/';
            $filePath = $uploadPath . $berkas['NamaFile'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }

            // Delete record dari database
            $this->guruBerkasModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Berkas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Guru: deleteBerkas - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Get Berkas By ID (for AJAX)
    public function getBerkasById($id)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Request harus menggunakan AJAX'
                ]);
            }

            // Ambil data berkas
            $berkas = $this->guruBerkasModel->find($id);
            if (!$berkas) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data berkas tidak ditemukan'
                ]);
            }

            // Validasi ownership
            $sessionIdTpq = session()->get('IdTpq');
            if ($sessionIdTpq != null) {
                $guru = $this->DataModels->find($berkas['IdGuru']);
                if (!$guru || $guru['IdTpq'] != $sessionIdTpq) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk melihat berkas ini'
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $berkas
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Guru: getBerkasById - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Get Berkas By Guru (for AJAX)
    public function getBerkasByGuru($idGuru)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Request harus menggunakan AJAX'
                ]);
            }

            // Validasi ownership
            $sessionIdTpq = session()->get('IdTpq');
            if ($sessionIdTpq != null) {
                $guru = $this->DataModels->find($idGuru);
                if (!$guru || $guru['IdTpq'] != $sessionIdTpq) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk melihat berkas guru ini'
                    ]);
                }
            }

            $berkas = $this->guruBerkasModel->getBerkasByGuru($idGuru);

            return $this->response->setJSON([
                'success' => true,
                'data' => $berkas
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Guru: getBerkasByGuru - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Update Status Berkas
    public function updateStatusBerkas()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Request harus menggunakan AJAX'
                ]);
            }

            $id = $this->request->getPost('id');
            $status = $this->request->getPost('status');

            if (empty($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID berkas tidak tersedia'
                ]);
            }

            $status = (int)$status;
            if (!in_array($status, [0, 1])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
            }

            // Ambil data berkas
            $berkas = $this->guruBerkasModel->find($id);
            if (!$berkas) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data berkas tidak ditemukan'
                ]);
            }

            // Validasi ownership
            $sessionIdTpq = session()->get('IdTpq');
            if ($sessionIdTpq != null) {
                $guru = $this->DataModels->find($berkas['IdGuru']);
                if (!$guru || $guru['IdTpq'] != $sessionIdTpq) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk mengubah status berkas ini'
                    ]);
                }
            }

            // Update status
            $this->guruBerkasModel->update($id, ['Status' => $status]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status berkas berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Guru: updateStatusBerkas - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Check data untuk print bulk (validasi sebelum print)
    public function checkBulkInsentifData()
    {
        // Set header untuk JSON response
        $this->response->setHeader('Content-Type', 'application/json');

        try {
            // Hanya Admin yang bisa akses
            if (!in_groups('Admin')) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak'
                ]);
            }

            // Ambil parameter
            $jenisPenerimaInsentif = $this->request->getGet('jenisPenerimaInsentif');
            $filterTpq = $this->request->getGet('filterTpq'); // Bisa array atau single value

            // Validasi: Jenis Penerima Insentif wajib dipilih
            if (empty($jenisPenerimaInsentif)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Jenis Penerima Insentif wajib dipilih'
                ]);
            }

            // Handle multiple TPQ filter
            $filterTpqArray = [];
            if (!empty($filterTpq)) {
                if (is_array($filterTpq)) {
                    $filterTpqArray = array_filter($filterTpq); // Remove empty values
                } else {
                    $filterTpqArray = [$filterTpq];
                }
            }

            // Ambil data guru berdasarkan filter
            $guruQuery = $this->DataModels->builder();

            $filterInfo = [];

            if (!empty($filterTpqArray)) {
                if (count($filterTpqArray) === 1) {
                    $guruQuery->where('IdTpq', $filterTpqArray[0]);
                    $tpqData = $this->tpqModel->GetData($filterTpqArray[0]);
                    if (!empty($tpqData) && !empty($tpqData[0])) {
                        $filterInfo['tpq'] = $tpqData[0]['NamaTpq'] . ' - ' . ($tpqData[0]['KelurahanDesa'] ?? '-');
                    } else {
                        $filterInfo['tpq'] = 'TPQ yang dipilih';
                    }
                } else {
                    $guruQuery->whereIn('IdTpq', $filterTpqArray);
                    // Ambil nama semua TPQ yang dipilih
                    $tpqNames = [];
                    foreach ($filterTpqArray as $tpqId) {
                        $tpqData = $this->tpqModel->GetData($tpqId);
                        if (!empty($tpqData) && !empty($tpqData[0])) {
                            $tpqNames[] = $tpqData[0]['NamaTpq'] . ' - ' . ($tpqData[0]['KelurahanDesa'] ?? '-');
                        }
                    }
                    $filterInfo['tpq'] = implode(', ', $tpqNames);
                }
            } else {
                $filterInfo['tpq'] = 'Semua TPQ';
            }

            // Jenis Penerima Insentif wajib dipilih
            $guruQuery->where('JenisPenerimaInsentif', $jenisPenerimaInsentif);
            $filterInfo['jenis'] = $jenisPenerimaInsentif;

            $count = $guruQuery->countAllResults(false);

            if ($count === 0) {
                $message = 'Tidak ada data guru yang ditemukan untuk filter yang dipilih.';
                $message .= '<br><br><strong>Filter yang dipilih:</strong>';
                $message .= '<br>- TPQ: ' . $filterInfo['tpq'];
                $message .= '<br>- Jenis Penerima Insentif: ' . $filterInfo['jenis'];
                $message .= '<br><br><strong>Kemungkinan penyebab:</strong>';
                $message .= '<br>1. Belum ada guru yang sesuai dengan filter yang dipilih';
                $message .= '<br>2. Kolom "Penerima Insentif" di tabel belum diisi/dipilih untuk guru-guru tersebut';
                $message .= '<br><br><strong>Solusi:</strong>';
                $message .= '<br>- Pastikan di tabel "Pengajuan Insentif", kolom "Penerima Insentif" sudah dipilih untuk setiap guru';
                $message .= '<br>- Atau pilih filter lain yang sesuai dengan data yang tersedia';

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message,
                    'count' => 0
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Ditemukan {$count} guru untuk filter yang dipilih.",
                'count' => $count,
                'filter' => $filterInfo
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Guru: checkBulkInsentifData - Error: ' . $e->getMessage());
            log_message('error', 'Guru: checkBulkInsentifData - Stack trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memvalidasi data: ' . $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Guru: checkBulkInsentifData - Fatal Error: ' . $e->getMessage());
            log_message('error', 'Guru: checkBulkInsentifData - Stack trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan fatal: ' . $e->getMessage()
            ]);
        }
    }

    // Print Bulk Insentif - Generate PDF bulk untuk multiple guru
    public function printBulkInsentif()
    {
        try {
            // Hanya Admin yang bisa akses
            if (!in_groups('Admin')) {
                log_message('error', 'Guru: printBulkInsentif - Akses ditolak, bukan Admin');
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak'
                ]);
            }

            // Set memory limit dan timeout untuk bulk processing
            ini_set('memory_limit', '512M');
            set_time_limit(600);
            mb_internal_encoding('UTF-8');

            // Disable output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Ambil parameter
            $fileTypes = $this->request->getGet('fileTypes');
            $jenisPenerimaInsentif = $this->request->getGet('jenisPenerimaInsentif');
            $filterTpq = $this->request->getGet('filterTpq'); // Bisa array atau single value

            // Validasi: Jenis Penerima Insentif wajib dipilih
            if (empty($jenisPenerimaInsentif)) {
                log_message('warning', 'Guru: printBulkInsentif - Jenis Penerima Insentif tidak dipilih');
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Jenis Penerima Insentif wajib dipilih'
                ]);
            }

            // Handle multiple TPQ filter
            $filterTpqArray = [];
            if (!empty($filterTpq)) {
                if (is_array($filterTpq)) {
                    $filterTpqArray = array_filter($filterTpq); // Remove empty values
                } else {
                    $filterTpqArray = [$filterTpq];
                }
            }

            log_message('info', "Guru: printBulkInsentif - Request: fileTypes={$fileTypes}, jenisPenerimaInsentif={$jenisPenerimaInsentif}, filterTpq=" . json_encode($filterTpqArray));

            if (empty($fileTypes)) {
                log_message('warning', 'Guru: printBulkInsentif - File types kosong');
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Silakan pilih minimal satu file yang akan di print'
                ]);
            }

            $fileTypesArray = explode(',', $fileTypes);
            log_message('info', "Guru: printBulkInsentif - File types array: " . json_encode($fileTypesArray));

            // Ambil data guru berdasarkan filter
            $guruQuery = $this->DataModels->builder();

            if (!empty($filterTpqArray)) {
                if (count($filterTpqArray) === 1) {
                    $guruQuery->where('IdTpq', $filterTpqArray[0]);
                    log_message('info', "Guru: printBulkInsentif - Filter TPQ (single): {$filterTpqArray[0]}");
                } else {
                    $guruQuery->whereIn('IdTpq', $filterTpqArray);
                    log_message('info', "Guru: printBulkInsentif - Filter TPQ (multiple): " . json_encode($filterTpqArray));
                }
            }

            // Jenis Penerima Insentif wajib dipilih
            $guruQuery->where('JenisPenerimaInsentif', $jenisPenerimaInsentif);
            log_message('info', "Guru: printBulkInsentif - Filter Jenis Penerima Insentif: {$jenisPenerimaInsentif}");

            $listGuru = $guruQuery->get()->getResultArray();

            if (empty($listGuru)) {
                log_message('warning', 'Guru: printBulkInsentif - Tidak ada guru yang ditemukan');

                // Buat pesan error yang lebih detail
                $message = 'Tidak ada guru yang ditemukan berdasarkan filter yang dipilih.';
                $message .= '<br><br><strong>Filter yang dipilih:</strong>';

                if (!empty($filterTpqArray)) {
                    if (count($filterTpqArray) === 1) {
                        $tpqData = $this->tpqModel->GetData($filterTpqArray[0]);
                        $tpqName = !empty($tpqData) && !empty($tpqData[0])
                            ? $tpqData[0]['NamaTpq'] . ' - ' . ($tpqData[0]['KelurahanDesa'] ?? '-')
                            : 'TPQ yang dipilih';
                        $message .= '<br>- TPQ: ' . $tpqName;
                    } else {
                        $tpqNames = [];
                        foreach ($filterTpqArray as $tpqId) {
                            $tpqData = $this->tpqModel->GetData($tpqId);
                            if (!empty($tpqData) && !empty($tpqData[0])) {
                                $tpqNames[] = $tpqData[0]['NamaTpq'] . ' - ' . ($tpqData[0]['KelurahanDesa'] ?? '-');
                            }
                        }
                        $message .= '<br>- TPQ: ' . implode(', ', $tpqNames);
                    }
                } else {
                    $message .= '<br>- TPQ: Semua TPQ';
                }

                // Jenis Penerima Insentif wajib dipilih
                $message .= '<br>- Jenis Penerima Insentif: ' . $jenisPenerimaInsentif;

                $message .= '<br><br><strong>Kemungkinan penyebab:</strong>';
                $message .= '<br>1. Belum ada guru yang sesuai dengan filter yang dipilih';
                $message .= '<br>2. Kolom "Penerima Insentif" di tabel belum diisi/dipilih untuk guru-guru tersebut';
                $message .= '<br><br><strong>Solusi:</strong>';
                $message .= '<br>- Pastikan di tabel "Pengajuan Insentif", kolom "Penerima Insentif" sudah dipilih untuk setiap guru';
                $message .= '<br>- Atau pilih filter lain yang sesuai dengan data yang tersedia';

                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => $message
                ]);
            }

            log_message('info', "Guru: printBulkInsentif - Memproses " . count($listGuru) . " guru");

            // Panggil method untuk generate dan zip
            // Pass array TPQ untuk handle multiple TPQ
            return $this->printBulkInsentifZip($listGuru, $fileTypesArray, $filterTpqArray);
        } catch (\Exception $e) {
            log_message('error', 'Guru: printBulkInsentif - Error: ' . $e->getMessage());
            log_message('error', 'Guru: printBulkInsentif - Stack trace: ' . $e->getTraceAsString());

            // Return JSON error untuk AJAX
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat PDF bulk: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'Gagal membuat PDF bulk: ' . $e->getMessage());
        }
    }

    /**
     * Print PDF bulk dengan metode zip (satu per satu lalu di-zip)
     * Jika multiple TPQ, buat ZIP terpisah per TPQ
     */
    private function printBulkInsentifZip($listGuru, $fileTypesArray, $filterTpqArray = [])
    {
        // Buat temporary directory
        $tempDir = sys_get_temp_dir() . '/insentif_bulk_' . uniqid();
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $pdfFiles = [];
        $successCount = 0;
        $errorCount = 0;
        $errors = []; // Array untuk menyimpan error detail

        log_message('info', "Guru: printBulkInsentifZip - Temp directory: {$tempDir}");
        log_message('info', "Guru: printBulkInsentifZip - Total guru: " . count($listGuru));
        log_message('info', "Guru: printBulkInsentifZip - File types: " . json_encode($fileTypesArray));

        try {
            // Generate PDF satu per satu untuk setiap guru
            foreach ($listGuru as $index => $guru) {
                try {
                    log_message('info', "Guru: printBulkInsentifZip - Memproses guru " . ($index + 1) . " dari " . count($listGuru) . ": " . $guru['Nama'] . " (ID: " . $guru['IdGuru'] . ")");

                    // Generate PDF untuk setiap jenis file yang dipilih
                    $pdfsToMerge = [];
                    $pdfTypes = []; // Simpan jenis file untuk setiap PDF yang berhasil di-generate

                    foreach ($fileTypesArray as $fileType) {
                        try {
                            log_message('info', "Guru: printBulkInsentifZip - Generate {$fileType} untuk guru: {$guru['Nama']}");
                            $pdfContent = $this->generatePdfByType($guru, $fileType);
                            if ($pdfContent) {
                                $pdfsToMerge[] = $pdfContent;
                                $pdfTypes[] = $fileType; // Simpan jenis file
                                log_message('info', "Guru: printBulkInsentifZip - Berhasil generate {$fileType} untuk guru: {$guru['Nama']}");
                            } else {
                                log_message('warning', "Guru: printBulkInsentifZip - PDF content null untuk {$fileType}, guru: {$guru['Nama']}");
                            }
                        } catch (\Exception $e) {
                            $errorMsg = "Error generate {$fileType} untuk guru {$guru['Nama']} ({$guru['IdGuru']}): " . $e->getMessage();
                            $errors[] = $errorMsg;
                            log_message('error', "Guru: printBulkInsentifZip - {$errorMsg}");
                            log_message('error', "Guru: printBulkInsentifZip - Stack trace: " . $e->getTraceAsString());
                            // Continue dengan file lain
                        }
                    }

                    if (empty($pdfsToMerge)) {
                        $errorCount++;
                        $errorMsg = "Tidak ada PDF yang berhasil dibuat untuk guru: {$guru['Nama']} ({$guru['IdGuru']})";
                        $errors[] = $errorMsg;
                        log_message('warning', "Guru: printBulkInsentifZip - {$errorMsg}");
                        continue;
                    }

                    log_message('info', "Guru: printBulkInsentifZip - Berhasil generate " . count($pdfsToMerge) . " PDF untuk guru: {$guru['Nama']}");

                    // Merge PDF menjadi satu (jika FPDI tersedia)
                    $mergedPdf = $this->mergePdfs($pdfsToMerge);

                    $namaGuru = preg_replace('/[^a-zA-Z0-9_-]/', '_', str_replace(' ', '_', $guru['Nama']));

                    if ($mergedPdf && !empty($mergedPdf)) {
                        // Jika merge berhasil, simpan sebagai satu file
                        log_message('info', "Guru: printBulkInsentifZip - Berhasil merge PDF untuk guru: {$guru['Nama']}, size: " . strlen($mergedPdf) . " bytes");

                        $pdfFilename = $namaGuru . '_' . $guru['IdGuru'] . '.pdf';
                        $pdfPath = $tempDir . '/' . $pdfFilename;

                        file_put_contents($pdfPath, $mergedPdf);

                        $pdfFiles[] = [
                            'path' => $pdfPath,
                            'name' => $pdfFilename,
                            'IdTpq' => $guru['IdTpq'] // Simpan IdTpq untuk grouping
                        ];
                    } else {
                        // Jika merge tidak tersedia atau gagal, simpan sebagai file individual
                        log_message('info', "Guru: printBulkInsentifZip - FPDI tidak tersedia atau merge gagal, menyimpan file individual untuk guru: {$guru['Nama']}");

                        $fileTypeNames = [
                            'asn' => 'ASN',
                            'insentif' => 'Insentif',
                            'rekomendasi' => 'Rekomendasi',
                            'lampiran' => 'Lampiran'
                        ];

                        foreach ($pdfsToMerge as $pdfIndex => $pdfContent) {
                            // Ambil nama file type dari array $pdfTypes yang sesuai dengan index
                            $fileTypeName = 'File' . ($pdfIndex + 1);
                            if (isset($pdfTypes[$pdfIndex]) && isset($fileTypeNames[$pdfTypes[$pdfIndex]])) {
                                $fileTypeName = $fileTypeNames[$pdfTypes[$pdfIndex]];
                            }

                            $pdfFilename = $namaGuru . '_' . $guru['IdGuru'] . '_' . $fileTypeName . '.pdf';
                            $pdfPath = $tempDir . '/' . $pdfFilename;

                            file_put_contents($pdfPath, $pdfContent);

                            $pdfFiles[] = [
                                'path' => $pdfPath,
                                'name' => $pdfFilename,
                                'IdTpq' => $guru['IdTpq'] // Simpan IdTpq untuk grouping
                            ];
                        }
                    }

                    $successCount++;

                    // Free memory
                    unset($mergedPdf, $pdfsToMerge);
                    gc_collect_cycles();
                } catch (\Exception $e) {
                    $errorCount++;
                    log_message('error', "Guru: printBulkInsentifZip - Error untuk guru {$guru['IdGuru']}: " . $e->getMessage());
                    continue;
                }
            }

            if (empty($pdfFiles)) {
                throw new \Exception('Tidak ada PDF yang berhasil dibuat');
            }

            log_message('info', "Guru: printBulkInsentifZip - Berhasil membuat {$successCount} PDF, {$errorCount} error");

            // Group PDF files berdasarkan TPQ
            $pdfFilesByTpq = [];
            foreach ($pdfFiles as $pdfFile) {
                $idTpq = $pdfFile['IdTpq'] ?? null;
                if (!isset($pdfFilesByTpq[$idTpq])) {
                    $pdfFilesByTpq[$idTpq] = [];
                }
                $pdfFilesByTpq[$idTpq][] = $pdfFile;
            }

            log_message('info', "Guru: printBulkInsentifZip - PDF files grouped by TPQ: " . count($pdfFilesByTpq) . " TPQ");

            // Buat ZIP file berdasarkan TPQ
            $foldersPerTpq = []; // Untuk cleanup nanti
            try {
                // Jika multiple TPQ, buat folder per TPQ lalu zip semua folder
                if (count($pdfFilesByTpq) > 1 || (count($filterTpqArray) > 1)) {
                    // Buat folder untuk setiap TPQ dan pindahkan PDF files ke folder tersebut
                    foreach ($pdfFilesByTpq as $idTpq => $files) {
                        // Ambil nama TPQ
                        $tpqData = $this->tpqModel->GetData($idTpq);
                        $namaTpq = 'TPQ_' . $idTpq;
                        $kelurahanDesa = '';
                        if (!empty($tpqData) && !empty($tpqData[0])) {
                            $namaTpq = preg_replace('/[^a-zA-Z0-9_-]/', '_', $tpqData[0]['NamaTpq']);
                            $kelurahanDesa = !empty($tpqData[0]['KelurahanDesa'])
                                ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $tpqData[0]['KelurahanDesa'])
                                : '';
                        }

                        // Buat nama folder
                        $folderName = $namaTpq;
                        if (!empty($kelurahanDesa)) {
                            $folderName .= '_' . $kelurahanDesa;
                        }

                        // Buat folder di tempDir
                        $tpqFolder = $tempDir . '/' . $folderName;
                        if (!is_dir($tpqFolder)) {
                            @mkdir($tpqFolder, 0755, true);
                        }

                        // Pindahkan PDF files ke folder TPQ
                        foreach ($files as $pdfFile) {
                            if (file_exists($pdfFile['path'])) {
                                $newPath = $tpqFolder . '/' . $pdfFile['name'];
                                @rename($pdfFile['path'], $newPath);
                                // Update path di array untuk cleanup nanti
                                $pdfFile['path'] = $newPath;
                            }
                        }

                        $foldersPerTpq[] = $tpqFolder;
                        log_message('info', "Guru: printBulkInsentifZip - Created folder for TPQ {$idTpq}: {$folderName}");
                    }

                    // Buat ZIP utama yang berisi semua folder per TPQ
                    $mainZipFilename = $this->createMainZipFromTpqFolders($foldersPerTpq, $tempDir);
                    $zipFilename = $mainZipFilename;
                } else {
                    // Single TPQ, buat ZIP langsung
                    $idTpq = !empty($filterTpqArray) && count($filterTpqArray) === 1 ? $filterTpqArray[0] : (key($pdfFilesByTpq) ?? null);
                    $zipFilename = $this->createZipFromPdfsInsentif($pdfFiles, $idTpq, $tempDir);
                }

                $zipFileSize = filesize($zipFilename);
                $zipBasename = basename($zipFilename);
                log_message('info', "Guru: printBulkInsentifZip - Final ZIP file created: {$zipFilename}");
                log_message('info', "Guru: printBulkInsentifZip - ZIP basename: {$zipBasename}");
                log_message('info', "Guru: printBulkInsentifZip - ZIP file size: {$zipFileSize} bytes");
            } catch (\Exception $zipError) {
                log_message('error', "Guru: printBulkInsentifZip - Error creating ZIP: " . $zipError->getMessage());
                log_message('error', "Guru: printBulkInsentifZip - ZIP error stack: " . $zipError->getTraceAsString());
                throw $zipError;
            }

            // Cleanup temporary PDF files (setelah ZIP dibuat)
            // PDF files akan dihapus setelah ZIP dibuat, tidak perlu dihapus sekarang
            // karena masih diperlukan untuk ZIP per TPQ

            // Output ZIP file
            if (file_exists($zipFilename)) {
                $fileSize = filesize($zipFilename);
                $downloadFilename = basename($zipFilename);

                log_message('info', "Guru: printBulkInsentifZip - ZIP file exists: {$zipFilename}");
                log_message('info', "Guru: printBulkInsentifZip - Download filename: {$downloadFilename}");
                log_message('info', "Guru: printBulkInsentifZip - File size: {$fileSize} bytes");

                if ($fileSize === 0) {
                    log_message('error', "Guru: printBulkInsentifZip - ZIP file kosong!");
                    throw new \Exception('File ZIP kosong. Silakan cek log untuk detail error.');
                }

                if (ob_get_level()) {
                    ob_end_clean();
                }

                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
                header('Content-Length: ' . $fileSize);
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                header('X-Content-Type-Options: nosniff');

                log_message('info', "Guru: printBulkInsentifZip - Sending ZIP file to client: {$downloadFilename}");
                readfile($zipFilename);
                log_message('info', "Guru: printBulkInsentifZip - ZIP file sent successfully");

                // Cleanup ZIP file dan temporary files
                register_shutdown_function(function () use ($zipFilename, $tempDir, $pdfFiles, $foldersPerTpq) {
                    sleep(2);
                    if (file_exists($zipFilename)) {
                        @unlink($zipFilename);
                    }
                    // Cleanup folders per TPQ (jika multiple)
                    foreach ($foldersPerTpq as $folderPerTpq) {
                        if (is_dir($folderPerTpq)) {
                            $this->deleteDirectory($folderPerTpq);
                        }
                    }
                    // Cleanup temporary PDF files (jika masih ada di root tempDir)
                    foreach ($pdfFiles as $pdfFile) {
                        if (file_exists($pdfFile['path'])) {
                            @unlink($pdfFile['path']);
                        }
                    }
                    if (is_dir($tempDir)) {
                        $this->deleteDirectory($tempDir);
                    }
                });

                exit();
            } else {
                throw new \Exception('Gagal membuat file ZIP');
            }
        } catch (\Exception $e) {
            // Cleanup on error
            foreach ($pdfFiles as $pdfFile) {
                if (file_exists($pdfFile['path'])) {
                    @unlink($pdfFile['path']);
                }
            }
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
            throw $e;
        }
    }

    /**
     * Generate PDF berdasarkan jenis file
     */
    private function generatePdfByType($guru, $fileType)
    {
        helper('nilai');

        $html = '';
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultMediaType', 'print');
        $options->set('isJavascriptEnabled', false);

        switch ($fileType) {
            case 'asn':
                $tanggalSurat = formatTanggalIndonesia(date('Y-m-d'), 'd F Y');
                $tanggalLahirFormatted = !empty($guru['TanggalLahir']) ? formatTanggalIndonesia($guru['TanggalLahir'], 'd F Y') : '';
                $data = [
                    'guru' => $guru,
                    'tanggalSurat' => $tanggalSurat,
                    'tanggalLahirFormatted' => $tanggalLahirFormatted,
                    'alamatLengkap' => $guru['Alamat'] . ', RT ' . $guru['Rt'] . ' / RW ' . $guru['Rw'] . ', ' . $guru['KelurahanDesa']
                ];
                $html = view('backend/guru/pdf/suratPernyataanAsn', $data);
                break;

            case 'insentif':
                $tanggalSurat = formatTanggalIndonesia(date('Y-m-d'), 'd F Y');
                $tanggalLahirFormatted = !empty($guru['TanggalLahir']) ? formatTanggalIndonesia($guru['TanggalLahir'], 'd F Y') : '';
                $data = [
                    'guru' => $guru,
                    'tanggalSurat' => $tanggalSurat,
                    'tanggalLahirFormatted' => $tanggalLahirFormatted,
                    'alamatLengkap' => $guru['Alamat'] . ', RT ' . $guru['Rt'] . ' / RW ' . $guru['Rw'] . ', ' . $guru['KelurahanDesa']
                ];
                $html = view('backend/guru/pdf/suratPernyataanInsentif', $data);
                break;

            case 'rekomendasi':
                // Validasi: hanya untuk Guru Ngaji
                if (($guru['JenisPenerimaInsentif'] ?? '') !== 'Guru Ngaji') {
                    return null;
                }

                $fkpqData = $this->fkpqModel->GetData();
                $fkpqRow = null;
                if (!empty($fkpqData) && !empty($fkpqData[0])) {
                    $fkpqRow = $fkpqData[0];
                }

                $signatureKetuaFkpq = null;
                if (!empty($guru['IdGuru'])) {
                    $existingSignature = $this->signatureModel->where([
                        'IdGuru' => $guru['IdGuru'],
                        'JenisDokumen' => 'Surat Rekomendasi',
                        'SignatureData' => 'Ketua FKPQ',
                        'StatusValidasi' => 'Valid'
                    ])->first();

                    if ($existingSignature) {
                        $signatureKetuaFkpq = $existingSignature;
                    } else {
                        helper('signature');
                        $token = generateUniqueSignatureToken($this->signatureModel);
                        $qrCodeData = generateSignatureQRCode($token);

                        if ($qrCodeData) {
                            $signatureData = [
                                'Token' => $token,
                                'IdGuru' => $guru['IdGuru'],
                                'IdTpq' => $guru['IdTpq'] ?? null,
                                'JenisDokumen' => 'Surat Rekomendasi',
                                'SignatureData' => 'Ketua FKPQ',
                                'QrCode' => $qrCodeData['filename'],
                                'StatusValidasi' => 'Valid',
                                'TanggalTtd' => date('Y-m-d H:i:s')
                            ];

                            $signatureId = $this->signatureModel->insert($signatureData);
                            if ($signatureId) {
                                $signatureKetuaFkpq = $this->signatureModel->find($signatureId);
                            }
                        }
                    }
                }

                $tanggalSurat = formatTanggalIndonesia(date('Y-m-d'), 'd F Y');
                $data = [
                    'guru' => $guru,
                    'tanggalSurat' => $tanggalSurat,
                    'alamatLengkap' => $guru['Alamat'] . ', RT ' . $guru['Rt'] . ' / RW ' . $guru['Rw'] . ', ' . $guru['KelurahanDesa'],
                    'fkpqData' => $fkpqRow,
                    'signatureKetuaFkpq' => $signatureKetuaFkpq
                ];
                $html = view('backend/guru/pdf/suratRekomendasi', $data);
                break;

            case 'lampiran':
                // Validasi berkas
                $berkasKtp = $this->guruBerkasModel->getBerkasAktifByGuruAndType($guru['IdGuru'], 'KTP');
                $berkasBpr = $this->guruBerkasModel->getBerkasAktifByGuruAndType($guru['IdGuru'], 'Buku Rekening', 'BPR');

                if (!$berkasKtp || !$berkasBpr) {
                    return null; // Skip jika berkas tidak lengkap
                }

                $ktpPath = FCPATH . 'uploads/berkas/' . $berkasKtp['NamaFile'];
                $bprPath = FCPATH . 'uploads/berkas/' . $berkasBpr['NamaFile'];

                if (!file_exists($ktpPath) || !file_exists($bprPath)) {
                    return null; // Skip jika file tidak ada
                }

                $ktpBase64 = base64_encode(file_get_contents($ktpPath));
                $ktpMimeType = mime_content_type($ktpPath);
                $ktpDataUri = 'data:' . $ktpMimeType . ';base64,' . $ktpBase64;

                $bprBase64 = base64_encode(file_get_contents($bprPath));
                $bprMimeType = mime_content_type($bprPath);
                $bprDataUri = 'data:' . $bprMimeType . ';base64,' . $bprBase64;

                $data = [
                    'guru' => $guru,
                    'ktpDataUri' => $ktpDataUri,
                    'bprDataUri' => $bprDataUri,
                    'ktpFileName' => $berkasKtp['NamaFile'],
                    'bprFileName' => $berkasBpr['NamaFile']
                ];
                $html = view('backend/guru/pdf/lampiran', $data);
                break;

            default:
                return null;
        }

        if (empty($html)) {
            return null;
        }

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Merge multiple PDFs menjadi satu
     * Menggunakan setasign/fpdi jika tersedia
     * Return null jika FPDI tidak tersedia (tidak throw error)
     */
    private function mergePdfs($pdfContents)
    {
        log_message('info', "Guru: mergePdfs - Mulai merge " . count($pdfContents) . " PDF files");

        // Cek apakah FPDI tersedia
        // FPDI membutuhkan FPDF untuk berfungsi
        $fpdiClass = '\setasign\Fpdi\Fpdi';
        $fpdiAvailable = class_exists($fpdiClass);

        if ($fpdiAvailable) {
            try {
                // Coba instansiasi FPDI - ini akan trigger error jika FPDF tidak tersedia
                // Kita catch error ini dan return null
                $pdf = new $fpdiClass();

                // Simpan setiap PDF ke temporary file untuk FPDI
                $tempFiles = [];
                $totalPages = 0;

                foreach ($pdfContents as $index => $pdfContent) {
                    if (empty($pdfContent)) {
                        log_message('warning', "Guru: mergePdfs - PDF content kosong untuk index {$index}");
                        continue;
                    }

                    $tempFile = sys_get_temp_dir() . '/pdf_temp_' . uniqid() . '_' . $index . '.pdf';
                    $writeResult = file_put_contents($tempFile, $pdfContent);

                    if ($writeResult === false) {
                        log_message('error', "Guru: mergePdfs - Gagal menulis temp file untuk index {$index}");
                        continue;
                    }

                    $tempFiles[] = $tempFile;

                    try {
                        $pageCount = $pdf->setSourceFile($tempFile);
                        log_message('info', "Guru: mergePdfs - PDF {$index} memiliki {$pageCount} halaman");
                        $totalPages += $pageCount;

                        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                            $templateId = $pdf->importPage($pageNo);
                            $size = $pdf->getTemplateSize($templateId);
                            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                            $pdf->useTemplate($templateId);
                        }
                    } catch (\Exception $e) {
                        log_message('warning', "Guru: mergePdfs - Error import PDF {$index}: " . $e->getMessage());
                        // Continue dengan PDF berikutnya
                    }
                }

                if ($totalPages === 0) {
                    log_message('warning', 'Guru: mergePdfs - Tidak ada halaman yang berhasil di-import, return null');
                    // Cleanup temp files
                    foreach ($tempFiles as $tempFile) {
                        if (file_exists($tempFile)) {
                            @unlink($tempFile);
                        }
                    }
                    return null;
                }

                log_message('info', "Guru: mergePdfs - Total halaman: {$totalPages}");

                $mergedContent = $pdf->Output('S');

                if (empty($mergedContent)) {
                    log_message('warning', 'Guru: mergePdfs - Merged content kosong, return null');
                    // Cleanup temp files
                    foreach ($tempFiles as $tempFile) {
                        if (file_exists($tempFile)) {
                            @unlink($tempFile);
                        }
                    }
                    return null;
                }

                log_message('info', "Guru: mergePdfs - Berhasil merge, size: " . strlen($mergedContent) . " bytes");

                // Cleanup temp files
                foreach ($tempFiles as $tempFile) {
                    if (file_exists($tempFile)) {
                        @unlink($tempFile);
                    }
                }

                return $mergedContent;
            } catch (\Error $e) {
                // Catch Error (bukan Exception) untuk menangani "Class not found"
                $errorMessage = $e->getMessage();
                log_message('warning', 'Guru: mergePdfs - Error menggunakan FPDI: ' . $errorMessage . ', return null');

                // Jika error adalah "Class FPDF not found", log pesan yang lebih jelas
                if (strpos($errorMessage, 'FPDF') !== false || strpos($errorMessage, 'FpdfTpl') !== false) {
                    log_message('warning', 'Guru: mergePdfs - Library FPDF tidak tersedia (diperlukan oleh FPDI), akan menyimpan file individual');
                }

                // Cleanup temp files jika ada
                if (isset($tempFiles)) {
                    foreach ($tempFiles as $tempFile) {
                        if (file_exists($tempFile)) {
                            @unlink($tempFile);
                        }
                    }
                }

                return null; // Return null instead of throwing exception
            } catch (\Exception $e) {
                log_message('warning', 'Guru: mergePdfs - Error menggunakan FPDI: ' . $e->getMessage() . ', return null');
                log_message('warning', 'Guru: mergePdfs - Stack trace: ' . $e->getTraceAsString());

                // Cleanup temp files jika ada
                if (isset($tempFiles)) {
                    foreach ($tempFiles as $tempFile) {
                        if (file_exists($tempFile)) {
                            @unlink($tempFile);
                        }
                    }
                }

                return null; // Return null instead of throwing exception
            }
        } else {
            // Jika FPDI tidak tersedia, return null (tidak throw error)
            log_message('info', 'Guru: mergePdfs - Library FPDI tidak tersedia, akan menyimpan file individual');
            return null;
        }
    }

    /**
     * Buat ZIP utama yang berisi multiple folder per TPQ
     */
    private function createMainZipFromTpqFolders($folders, $tempDir)
    {
        $zipName = 'Pengajuan_Insentif_MultipleTPQ_' . date('Y-m-d') . '.zip';
        $zipFilename = $tempDir . '/' . $zipName;

        if (!class_exists('ZipArchive')) {
            throw new \Exception('Extension ZipArchive tidak tersedia di server');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Tidak dapat membuat file ZIP utama');
        }

        // Tambahkan semua file dari setiap folder ke ZIP
        foreach ($folders as $folder) {
            if (is_dir($folder)) {
                $folderName = basename($folder);
                $folderPath = realpath($folder);

                // Iterate semua file di folder
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $filePath = $file->getRealPath();
                        // Dapatkan relative path dari folder root
                        $relativePath = $folderName . '/' . str_replace($folderPath . DIRECTORY_SEPARATOR, '', $filePath);
                        $relativePath = str_replace('\\', '/', $relativePath); // Normalize path separator
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }
        }

        $zip->close();

        log_message('info', "Guru: createMainZipFromTpqFolders - Main ZIP created: {$zipFilename}");
        return $zipFilename;
    }

    /**
     * Buat ZIP file dari array PDF files
     */
    private function createZipFromPdfsInsentif($pdfFiles, $filterTpq, $tempDir)
    {
        // Ambil nama TPQ dan Kelurahan/Desa jika filter ada
        $namaTpq = 'SemuaTPQ';
        $kelurahanDesa = '';
        if (!empty($filterTpq)) {
            $tpqData = $this->tpqModel->GetData($filterTpq);
            if (!empty($tpqData) && !empty($tpqData[0])) {
                $namaTpq = preg_replace('/[^a-zA-Z0-9_-]/', '_', $tpqData[0]['NamaTpq']);
                $kelurahanDesa = !empty($tpqData[0]['KelurahanDesa'])
                    ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $tpqData[0]['KelurahanDesa'])
                    : '';
            }
        }

        // Buat nama file dengan format: Pengajuan_Insentif_NamaTPQ_KelurahanDesa_Tanggal.zip
        $zipName = 'Pengajuan_Insentif_' . $namaTpq;
        if (!empty($kelurahanDesa)) {
            $zipName .= '_' . $kelurahanDesa;
        }
        $zipName .= '_' . date('Y-m-d') . '.zip';

        $zipFilename = $tempDir . '/' . $zipName;

        log_message('info', "Guru: createZipFromPdfsInsentif - Nama TPQ: {$namaTpq}");
        log_message('info', "Guru: createZipFromPdfsInsentif - Kelurahan/Desa: {$kelurahanDesa}");
        log_message('info', "Guru: createZipFromPdfsInsentif - ZIP name: {$zipName}");
        log_message('info', "Guru: createZipFromPdfsInsentif - Full path: {$zipFilename}");

        if (!class_exists('ZipArchive')) {
            throw new \Exception('Extension ZipArchive tidak tersedia di server');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Tidak dapat membuat file ZIP');
        }

        foreach ($pdfFiles as $pdfFile) {
            if (file_exists($pdfFile['path'])) {
                $zip->addFile($pdfFile['path'], $pdfFile['name']);
            }
        }

        $zip->close();

        log_message('info', "Guru: createZipFromPdfsInsentif - ZIP file created: {$zipFilename}");
        log_message('info', "Guru: createZipFromPdfsInsentif - ZIP filename (basename): " . basename($zipFilename));

        return $zipFilename;
    }

    /**
     * Hapus directory secara recursive
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}