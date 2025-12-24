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
}