<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\EncryptModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriBaruModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Santri extends BaseController
{
    public $DataSantri;
    protected $encryptModel;
    protected $helpFunction;
    protected $DataSantriBaru;
    public function __construct()
    {
        $this->encryptModel = new EncryptModel();
        $this->DataSantri = new SantriModel();
        $this->DataSantriBaru = new SantriBaruModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    // fungsi untuk menampilkan form tambah santri
    public function createEmisStep()
    {
        // Ambil semua data TPQ
        $dataTpq = $this->helpFunction->getDataTpq();
        usort($dataTpq, function ($a, $b) {
            return strcmp($a['NamaTpq'], $b['NamaTpq']);
        });

        // Ambil data kelas
        $dataKelas = $this->helpFunction->getDataKelas();
        $data = [
            'page_title' => 'Form Data Tambah Santri',
            'dataTpq' => $dataTpq,
            'dataKelas' => $dataKelas
        ];

        return view('backend/santri/createEmisStep', $data);
    }
    // fungsi untuk menyimpan
    public function save()
    {
        // Inisialisasi log
        $logs = [];
        $logs[] = "ℹ️ INFO: Memulai proses save data santri";

        try {
            $IdSantri = "";
            // Ambil tahun saat ini
            $tahunSekarang = date('Y');
            $logs[] = "ℹ️ INFO: Tahun sekarang: " . $tahunSekarang;

            // Cari IdSantri terakhir
            $lastSantri = $this->DataSantriBaru->like('IdSantri', $tahunSekarang, 'after')
                ->orderBy('IdSantri', 'DESC')
                ->first();

            if ($lastSantri) {
                $lastNumber = intval(substr($lastSantri['IdSantri'], 4));
                $newNumber = $lastNumber + 1;
                $IdSantri = $tahunSekarang . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
                $logs[] = "✓ OK: ID Santri baru dibuat: " . $IdSantri;
            } else {
                $IdSantri = $tahunSekarang . '0001';
                $logs[] = "✓ OK: ID Santri pertama dibuat: " . $IdSantri;
            }

            // Fungsi untuk menangani upload file dengan nama yang menyertakan IdSantri
            $handleUpload = function ($file, $prefix) use ($IdSantri) {
                try {
                    if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                        return null;
                    }

                    if (!$file->isValid()) {
                        throw new \Exception('File tidak valid: ' . $file->getErrorString());
                    }

                    if ($file->hasMoved()) {
                        throw new \Exception('File sudah dipindahkan sebelumnya');
                    }

                    $randomNumber = uniqid();
                    $newName = $prefix . '_' . $IdSantri . '_' . $randomNumber . '.' . $file->getExtension();

                    // Tentukan path berdasarkan environment
                    if (ENVIRONMENT === 'production') {
                        $uploadPath = 'https://tpqsmart.simpedis.com/uploads/santri/';  // Path di server production
                    } else {
                        $uploadPath = ROOTPATH . 'public/uploads/santri/';
                    }

                    // Validasi direktori upload
                    if (!is_dir($uploadPath)) {
                        $logs[] = "❌ ERROR: Direktori upload tidak ditemukan: " . $uploadPath;
                        throw new \Exception('Direktori upload tidak ditemukan: ' . $uploadPath);
                    }

                    if (!is_writable($uploadPath)) {
                        $logs[] = "❌ ERROR: Direktori upload tidak dapat ditulis: " . $uploadPath;
                        throw new \Exception('Direktori upload tidak dapat ditulis: ' . $uploadPath);
                    }

                    $targetPath = $uploadPath . $newName;

                    // Hapus file lama jika ada
                    if (file_exists($targetPath)) {
                        if (!unlink($targetPath)) {
                            $logs[] = "❌ ERROR: Gagal menghapus file lama: " . $targetPath;
                            throw new \Exception('Gagal menghapus file lama: ' . $targetPath);
                        }
                    }

                    // Pindahkan file
                    if (!$file->move($uploadPath, $newName, true)) {
                        $logs[] = "❌ ERROR: Gagal memindahkan file ke: " . $targetPath;
                        throw new \Exception('Gagal memindahkan file ke: ' . $targetPath);
                    }

                    return $newName;
                } catch (\Exception $e) {
                    $logs[] = "❌ ERROR: Gagal mengupload file: " . $e->getMessage();
                    log_message('error', '[File Upload] ' . $e->getMessage());
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengupload file: ' . $e->getMessage(),
                        'error_details' => [
                            'file' => $prefix,
                            'error' => $e->getMessage(),
                            'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
                        ]
                    ]);
                }
            };

            // Handle upload untuk setiap file dan simpan nama file ke variabel
            $photoProfilName = $handleUpload($this->request->getFile('PhotoProfil'), 'Profile');
            $namaFileKIP = $handleUpload($this->request->getFile('FileKIP'), 'Kip');
            $namaFileKkSantri = $handleUpload($this->request->getFile('FileKkSantri'), 'KkSantri');
            $namaFileKkAyah = $handleUpload($this->request->getFile('FileKKAyah'), 'KkAyah');
            $namaFileKkIbu = $handleUpload($this->request->getFile('FileKKIbu'), 'KkIbu');
            $namaFileKKS = $handleUpload($this->request->getFile('FileKKS'), 'Kks');
            $namaFilePKH = $handleUpload($this->request->getFile('FilePKH'), 'Pkh');
            // Update data yang akan disimpan dengan nama file yang baru
            $data['PhotoProfil'] = $photoProfilName;
            $data['FileKIP'] = $namaFileKIP;
            $data['FileKkSantri'] = $namaFileKkSantri;
            $data['FileKkAyah'] = $namaFileKkAyah;
            $data['FileKkIbu'] = $namaFileKkIbu;
            $data['FileKKS'] = $namaFileKKS;
            $data['FilePKH'] = $namaFilePKH;

            // Handling checkbok kksamaayah sama dengan santri maka file kk ayah sama dengan file kk santri
            if ($this->request->getPost('KkAyahSamaDenganSantri') == 'on')
            $namaFileKkAyah = $namaFileKkSantri;
            if ($this->request->getPost('KkIbuSamaDenganAyahAtauSantri') == 'on')
            $namaFileKkIbu = $namaFileKkSantri;

            // Siapkan data untuk disimpan
            $data = [
                // Data TPQ
                'IdTpq' => $this->request->getPost('IdTpq'),
                'IdKelas' => $this->request->getPost('IdKelas'),
                'Agama' => 'Islam',
                // Data Santri
                'IdSantri' => $IdSantri,
                'PhotoProfil' => $photoProfilName,
                'NikSantri' => $this->request->getPost('NikSantri'),
                'NamaSantri' => $this->request->getPost('NamaSantri'),
                'JenisKelamin' => $this->request->getPost('JenisKelamin'),
                'NISN' => $this->request->getPost('NISN'),
                'TempatLahirSantri' => $this->request->getPost('TempatLahirSantri'),
                'TanggalLahirSantri' => $this->request->getPost('TanggalLahirSantri'),
                'AnakKe' => $this->request->getPost('AnakKe'),
                'JumlahSaudara' => $this->request->getPost('JumlahSaudara'),
                'CitaCita' => $this->request->getPost('CitaCita'),
                'CitaCitaLainya' => $this->request->getPost('CitaCitaLainya'),
                'Hobi' => $this->request->getPost('Hobi'),
                'HobiLainya' => $this->request->getPost('HobiLainya'),
                'NoHpSantri' => $this->request->getPost('NoHpSantri'),
                'EmailSantri' => $this->request->getPost('EmailSantri'),
                'KebutuhanKhusus' => $this->request->getPost('KebutuhanKhusus'),
                'KebutuhanKhususLainya' => $this->request->getPost('KebutuhanKhususLainya'),
                'KebutuhanDisabilitas' => $this->request->getPost('KebutuhanDisabilitas'),
                'KebutuhanDisabilitasLainya' => $this->request->getPost('KebutuhanDisabilitasLainya'),
                'YangBiayaSekolah' => $this->request->getPost('YangBiayaSekolah'),
                'NamaKepalaKeluarga' => $this->request->getPost('NamaKepalaKeluarga'),
                'NoKIP' => $this->request->getPost('NoKIP'),
                'IdKartuKeluarga' => $this->request->getPost('IdKartuKeluarga'),
                'FileKIP' => $namaFileKIP,
                'FileKkSantri' => $namaFileKkSantri,

                // Data Ayah
                'NamaAyah' => $this->request->getPost('NamaAyah'),
                'StatusAyah' => $this->request->getPost('StatusAyah'),
                'NikAyah' => $this->request->getPost('NikAyah'),
                'KewarganegaraanAyah' => $this->request->getPost('KewarganegaraanAyah'),
                'TempatLahirAyah' => $this->request->getPost('TempatLahirAyah'),
                'TanggalLahirAyah' => $this->request->getPost('TanggalLahirAyah'),
                'PendidikanAyah' => $this->request->getPost('PendidikanAyah'),
                'PekerjaanUtamaAyah' => $this->request->getPost('PekerjaanUtamaAyah'),
                'PenghasilanUtamaAyah' => $this->request->getPost('PenghasilanUtamaAyah'),
                'NoHpAyah' => $this->request->getPost('NoHpAyah'),
                'FileKkAyah' => $namaFileKkAyah,

                // Data Ibu
                'NamaIbu' => $this->request->getPost('NamaIbu'),
                'StatusIbu' => $this->request->getPost('StatusIbu'),
                'NikIbu' => $this->request->getPost('NikIbu'),
                'KewarganegaraanIbu' => $this->request->getPost('KewarganegaraanIbu'),
                'TempatLahirIbu' => $this->request->getPost('TempatLahirIbu'),
                'TanggalLahirIbu' => $this->request->getPost('TanggalLahirIbu'),
                'PendidikanIbu' => $this->request->getPost('PendidikanIbu'),
                'PekerjaanUtamaIbu' => $this->request->getPost('PekerjaanUtamaIbu'),
                'PenghasilanUtamaIbu' => $this->request->getPost('PenghasilanUtamaIbu'),
                'NoHpIbu' => $this->request->getPost('NoHpIbu'),
                'FileKkIbu' => $namaFileKkIbu,

                // Data Wali
                'StatusWali' => $this->request->getPost('StatusWali'),
                'NamaWali' => $this->request->getPost('NamaWali'),
                'NikWali' => $this->request->getPost('NikWali'),
                'KewarganegaraanWali' => $this->request->getPost('KewarganegaraanWali'),
                'TempatLahirWali' => $this->request->getPost('TempatLahirWali'),
                'TanggalLahirWali' => $this->request->getPost('TanggalLahirWali'),
                'PendidikanWali' => $this->request->getPost('PendidikanWali'),
                'PekerjaanUtamaWali' => $this->request->getPost('PekerjaanUtamaWali'),
                'PenghasilanUtamaWali' => $this->request->getPost('PenghasilanUtamaWali'),
                'NoHpWali' => $this->request->getPost('NoHpWali'),
                'NomorPKH' => $this->request->getPost('NomorPKH'),
                'NomorKKS' => $this->request->getPost('NomorKKS'),
                'FilePKH' => $namaFilePKH,
                'FileKKS' => $namaFileKKS,

                // Data Alamat Ayah
                'TinggalDiluarNegeriAyah' => $this->request->getPost('TinggalDiluarNegeriAyah'),
                'StatusKepemilikanRumahAyah' => $this->request->getPost('StatusKepemilikanRumahAyah'),
                'ProvinsiAyah' => $this->request->getPost('ProvinsiAyah'),
                'KabupatenKotaAyah' => $this->request->getPost('KabupatenKotaAyah'),
                'KecamatanAyah' => $this->request->getPost('KecamatanAyah'),
                'KelurahanDesaAyah' => $this->request->getPost('KelurahanDesaAyah'),
                'RtAyah' => $this->request->getPost('RTAyah'),
                'RwAyah' => $this->request->getPost('RWAyah'),
                'AlamatAyah' => $this->request->getPost('AlamatAyah'),
                'KodePosAyah' => $this->request->getPost('KodePosAyah'),

                // Data Alamat Ibu
                'TinggalDiluarNegeriIbu' => $this->request->getPost('TinggalDiluarNegeriIbu'),
                'StatusKepemilikanRumahIbu' => $this->request->getPost('StatusKepemilikanRumahIbu'),
                'ProvinsiIbu' => $this->request->getPost('ProvinsiIbu'),
                'KabupatenKotaIbu' => $this->request->getPost('KabupatenKotaIbu'),
                'KecamatanIbu' => $this->request->getPost('KecamatanIbu'),
                'KelurahanDesaIbu' => $this->request->getPost('KelurahanDesaIbu'),
                'RtIbu' => $this->request->getPost('RTIbu'),
                'RwIbu' => $this->request->getPost('RWIbu'),
                'AlamatIbu' => $this->request->getPost('AlamatIbu'),
                'KodePosIbu' => $this->request->getPost('KodePosIbu'),

                // Data Alamat Santri
                'WaliSantri' => $this->request->getPost('WaliSantri'),
                'ProvinsiSantri' => $this->request->getPost('ProvinsiSantri'),
                'KabupatenKotaSantri' => $this->request->getPost('KabupatenKotaSantri'),
                'KecamatanSantri' => $this->request->getPost('KecamatanSantri'),
                'KelurahanDesaSantri' => $this->request->getPost('KelurahanDesaSantri'),
                'RtSantri' => $this->request->getPost('RTSantri'),
                'RwSantri' => $this->request->getPost('RWSantri'),
                'AlamatSantri' => $this->request->getPost('AlamatSantri'),
                'KodePosSantri' => $this->request->getPost('KodePosSantri'),
                'JarakTempuhSantri' => $this->request->getPost('JarakTempuhSantri'),
                'TransportasiSantri' => $this->request->getPost('TransportasiSantri'),
                'WaktuTempuhSantri' => $this->request->getPost('WaktuTempuhSantri'),
                'TitikKoordinatSantri' => $this->request->getPost('TitikKoordinatSantri'),
            ];



            // Simpan data ke database
            // Ubah nilai array menjadi lowercase kemudian ucwords sebelum insert
            try {
                $processedData = array_map(function ($value) {
                    try {
                        // Skip jika value adalah null atau file
                        if ($value === null || strpos($value, '.') !== false) {
                            return $value;
                        }
                        // Hanya proses string 
                        if (is_string($value)) {
                            return ucwords(strtolower($value));
                        }
                        return $value;
                    } catch (\Exception $e) {
                        $logs[] = "❌ ERROR: Gagal memproses data: " . $e->getMessage();
                        log_message('error', 'Error saat memproses data: ' . $e->getMessage());
                        // Kembalikan nilai asli jika terjadi error
                        return $value;
                    }
                }, $data);
            } catch (\Exception $e) {
                $logs[] = "❌ ERROR: Gagal memproses data: " . $e->getMessage();
                throw new \Exception('Gagal memproses data: ' . $e->getMessage());
            }

            $result = $this->DataSantriBaru->insert($processedData);

            if ($result) {
                $logs[] = "✓ OK: Data berhasil disimpan";
                $this->saveLog($logs); // Menggunakan existing saveLog function
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data santri berhasil disimpan',
                    'redirect' => base_url('backend/santri/showSuccessEmisStep/' . $IdSantri)
                ]);
            } else {
                $logs[] = "❌ ERROR: Gagal menyimpan data: " . json_encode($this->DataSantriBaru->errors());
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->DataSantriBaru->errors()));
            }
        } catch (\Exception $e) {
            $logs[] = "❌ ERROR: " . $e->getMessage();
            $this->saveLog($logs); // Menggunakan existing saveLog function
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data santri: ' . $e->getMessage(),
                'errors' => $this->DataSantriBaru->errors(),
                'logs' => $logs
            ]);
        }
    }

    // Endpoint untuk cek NIK santri baru
    public function getNikSantri($nik)
    {
        $santriModel = new SantriBaruModel();
        $santri = $santriModel->getSantriByNIK($nik);

        // Set response dengan properti exists
        return $this->response->setJSON([
            'exists' => !empty($santri), // true jika santri ditemukan, false jika tidak
            'data' => $santri // data santri jika ada
        ]);
    }

    public function show()
    {
        $IdTpq = session()->get('IdTpq');
        $santri = $this->DataSantri->GetData($IdTpq);

        $kelas = $this->helpFunction->getDataKelas();
        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $santri,
            'dataKelas' => $kelas
        ];
        return view('backend/santri/listSantri', $data);
    }

    public function showSantriBaru()
    {
        $santri = $this->DataSantriBaru->GetData();
        $tpq = $this->helpFunction->getDataTpq();
        usort($tpq, function ($a, $b) {
            return strcmp($a['NamaTpq'], $b['NamaTpq']);
        });

        $data = [
            'page_title' => 'Data Santri Baru',
            'dataSantri' => $santri,
            'dataTpq' => $tpq
        ];
        return view('backend/santri/listSantriBaru', $data);
    }

    public function showSantriBaruPerKelasTpq($IdTpq = null)
    {
        $santriAll = $this->DataSantriBaru->GetDataPerKelasTpq($IdTpq);
        $namaTpq = $this->helpFunction->getNamaTpqById($IdTpq);
        // Mengelompokkan santri berdasarkan kelas
        $santriPerKelas = [
            'TK' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '1';
            }),
            'TKA' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '2';
            }),
            'TKB' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '3';
            }),
            'TPQ1' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '4';
            }),
            'TPQ2' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '5';
            }),
            'TPQ3' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '6';
            }),
            'TPQ4' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '7';
            }),
            'TPQ5' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '8';
            }),
            'TPQ6' => array_filter($santriAll, function ($s) {
                return $s['IdKelas'] == '9';
            })
        ];

        $data = [
            'page_title' => 'Data Santri Baru Per Kelas TPQ',
            'dataSantriTK' => $santriPerKelas['TK'],
            // 'dataSantriTKA' => $santriPerKelas['TKA'],
            // 'dataSantriTKB' => $santriPerKelas['TKB'],
            'dataSantriTPQ1' => $santriPerKelas['TPQ1'],
            'dataSantriTPQ2' => $santriPerKelas['TPQ2'],
            'dataSantriTPQ3' => $santriPerKelas['TPQ3'],
            'dataSantriTPQ4' => $santriPerKelas['TPQ4'],
            'dataSantriTPQ5' => $santriPerKelas['TPQ5'],
            'dataSantriTPQ6' => $santriPerKelas['TPQ6'],
            'namaTpq' => $namaTpq,
        ];

        return view('backend/santri/listSantriBaruPerKelasTpq', $data);
    }

    public function showSantriPerKelas($encryptedIdGuru = null)
    {
        if ($encryptedIdGuru !== null)
            $IdGuru = $this->encryptModel->decryptData($encryptedIdGuru);
        else
            $IdGuru = $encryptedIdGuru;

        $IdGuru = session()->get('IdGuru');
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $dataSantri = $this->DataSantri->GetDataSantriPerKelas($IdTahunAjaran, $IdKelas, $IdGuru);
        $data = [
            'page_title' => 'Data Santri Per Semester',
            'dataSantri' => $dataSantri
        ];

        return view('backend/santri/santriPerKelas', $data);
    }

    public function showKontakSantri($IdSantri = null)
    {

        $data = [
            'page_title' => 'Kontak Santri',
            'santri' => $datasantri = ""
        ];
        return view('backend/santri/kontakSantri', $data);
    }

    // Testing Dropzone 
    public function upload()
    {
        if ($this->request->getFile('file')) {
            $file = $this->request->getFile('file');
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move('./uploads/santri', $newName);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'File berhasil diupload',
                    'filename' => $newName
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengupload file'
        ]);
    }


    // public function generatePDF()
    // {
    //     try {
    //         // 1. Validasi request AJAX
    //         $this->saveLog("ℹ️ INFO: Validasi request AJAX");
    //         if (!$this->request->isAJAX()) {
    //             $this->saveLog("ERROR: Bukan request AJAX");
    //             return $this->response->setStatusCode(403)->setJSON([
    //                 'message' => 'Akses tidak diizinkan'
    //             ]);
    //         }

    //         // 2. Validasi data
    //         $data = $this->request->getJSON(true);
    //         $this->saveLog("✓ OK: Data diterima");

    //         if (empty($data)) {
    //             throw new \Exception('Tidak ada data yang dikirim');
    //         }

    //         if (empty($data['printNamaSantri'])) {
    //             throw new \Exception('Nama santri wajib diisi untuk mencetak PDF');
    //         }

    //         // 3. Konfigurasi DOMPDF
    //         $this->saveLog("ℹ️ INFO: Inisialisasi Konfigurasi DOMPDF");
    //         $options = new Options();
    //         $options->set('isHtml5ParserEnabled', true);
    //         $options->set('isRemoteEnabled', true);
    //         $options->set('isPhpEnabled', true);
    //         $dompdf = new Dompdf($options);
    //         $this->saveLog("✓ OK: Konfigurasi DOMPDF berhasil");

    //         // 4. Proses foto
    //         try {
    //             $this->saveLog("ℹ️ INFO: Initial proses foto santri untuk memastikan format gambar valid");
    //             $fotoSantri = $this->processFotoSantri($data['printFotoSantri'] ?? null);
    //             $this->saveLog("✓ OK: Foto santri berhasil diproses");
    //         } catch (\Exception $e) {
    //             $this->saveLog("⚠️ Foto tidak tersedia: " . $e->getMessage());
    //             $fotoSantri = null;
    //             $this->saveLog(" ℹ️ INFO: Foto santri tidak tersedia, dan akan dikosongkan");
    //         }

    //         // 5. Render HTML dan PDF
    //         $this->saveLog("ℹ️ INFO: Render HTML dan PDF");
    //         $html = view('backend/santri/pdf_template', [
    //             'data' => $data,
    //             'fotoSantri' => $fotoSantri
    //         ]);

    //         $dompdf->loadHtml($html);
    //         $dompdf->setPaper('A4', 'portrait');
    //         $dompdf->render();
    //         $this->saveLog("✓ OK: PDF berhasil dibuat");

    //         return $this->response
    //         ->setHeader('Content-Type', 'application/pdf')
    //         ->setBody($dompdf->output());
    //     } catch (\Exception $e) {
    //         $this->saveLog("❌ ERROR: " . $e->getMessage());

    //         return $this->response->setStatusCode(500)->setJSON([
    //             'message' => 'Gagal membuat PDF: ' . $e->getMessage()
    //         ]);
    //     }
    // }

    // Fungsi helper untuk menyimpan log
    private function saveLog($logs)
    {
        $logFile = WRITEPATH . 'logs/pdfGeneration.log';
        $logMessage = "\n=== " . date('Y-m-d H:i:s') . " ===\n";

        // Periksa apakah $logs adalah array atau string
        if (is_array($logs)) {
            $logMessage .= implode("\n", $logs);
        } else {
            $logMessage .= $logs;
        }

        $logMessage .= "\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Proses foto santri dari base64 ke format yang sesuai
     * @param string|null $base64Image
     * @return string|null
     */
    private function processFotoSantri(?string $base64Image): ?string
    {
        try {
            if (empty($base64Image)) {
                throw new \Exception('Foto santri tidak boleh kosong');
            }

            // Validasi format base64
            if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                throw new \Exception('Format gambar tidak valid - harus base64');
            }
            $this->saveLog("✓ OK: Format base64 valid");

            // Extract data gambar
            $this->saveLog("ℹ️ INFO: Extract data gambar");
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageData = base64_decode($base64Image);

            if (!$imageData) {
                throw new \Exception('Gagal decode base64 image');
            }
            $this->saveLog("✓ OK: Base64 berhasil di-decode");

            // Konversi ke image
            $this->saveLog("ℹ️ INFO: Konversi ke image");
            $srcImage = imagecreatefromstring($imageData);
            if (!$srcImage) {
                throw new \Exception('Gagal membuat image dari string data');
            }
            $this->saveLog("✓ OK: Image berhasil dibuat dari string");

            // Buat file JPEG temporary
            $this->saveLog("ℹ️ INFO: Buat file JPEG temporary");
            $jpegFile = tempnam(sys_get_temp_dir(), 'jpg');
            if (!$jpegFile) {
                throw new \Exception('Gagal membuat file temporary');
            }
            $this->saveLog("✓ OK: File temporary berhasil dibuat");

            // Simpan sebagai JPEG
            $this->saveLog("ℹ️ INFO: Simpan sebagai JPEG");
            if (!imagejpeg($srcImage, $jpegFile, 90)) {
                throw new \Exception('Gagal menyimpan gambar ke JPEG');
            }
            $this->saveLog("✓ OK: Gambar berhasil disimpan sebagai JPEG");

            // Baca data JPEG
            $this->saveLog("ℹ️ INFO: Baca data JPEG");
            $jpegData = file_get_contents($jpegFile);
            if (!$jpegData) {
                throw new \Exception('Gagal membaca file JPEG');
            }
            $this->saveLog("✓ OK: File JPEG berhasil dibaca");

            // Cleanup
            $this->saveLog("ℹ️ INFO: Cleanup image dan file temporary");
            imagedestroy($srcImage);
            if (file_exists($jpegFile)) {
                unlink($jpegFile);
            }
            $this->saveLog("✓ OK: Cleanup berhasil dilakukan");

            return 'data:image/jpeg;base64,' . base64_encode($jpegData);
        } catch (\Exception $e) {
            $this->saveLog("❌ ERROR: " . $e->getMessage());
            throw $e;
        }
    }

    public function generatePDFSantriBaru($IdSantri = null)
    {
        try {
            // Validasi ID Santri
            if (!$IdSantri) {
                $this->saveLog("❌ ERROR: ID Santri tidak ditemukan");
                throw new \Exception('ID Santri tidak ditemukan');
            }
            $this->saveLog("✓ OK: ID Santri valid: " . $IdSantri);

            // Ambil data santri dari database
            $this->saveLog("ℹ️ INFO: Mengambil data santri dari database");
            $dataSantri = $this->DataSantriBaru
                ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
                ->where('tbl_santri_baru.IdSantri', $IdSantri)
                ->first();

            if (!$dataSantri) {
                $this->saveLog("❌ ERROR: Data santri tidak ditemukan");
                throw new \Exception('Data santri tidak ditemukan');
            }
            $this->saveLog("✓ OK: Data santri berhasil diambil");

            // Siapkan data untuk template
            $data = [
                //nama tpq dan nama kelas
                'printNamaTpq' => $dataSantri['NamaTpq'],  // Menggunakan NamaTpq dari hasil join
                'printNamaKelas' => $dataSantri['NamaKelas'],  // Menggunakan NamaKelas dari hasil join
                //data santri
                'printNamaSantri' => $dataSantri['NamaSantri'],
                'printNikSantri' => $dataSantri['NikSantri'],
                'printTempatTTL' => $dataSantri['TempatLahirSantri'] . ', ' . $dataSantri['TanggalLahirSantri'],
                'printJenisKelamin' => $dataSantri['JenisKelamin'],
                'printAlamatSantri' => $dataSantri['AlamatSantri'],
                'printAnakKe' => $dataSantri['AnakKe'],
                'printJumlahSaudara' => $dataSantri['JumlahSaudara'],
                'printHobi' => $dataSantri['Hobi'],
                'printCitaCita' => $dataSantri['CitaCita'],
                //data ayah dan ibu
                'printNamaAyah' => $dataSantri['NamaAyah'],
                'printNamaIbu' => $dataSantri['NamaIbu'],
                'printFotoSantri' => null,
                //data alamat
                'printRtSantri' => $dataSantri['RtSantri'],
                'printRwSantri' => $dataSantri['RwSantri'],
                'printKelurahanDesaSantri' => $dataSantri['KelurahanDesaSantri'],
                'printKecamatanSantri' => $dataSantri['KecamatanSantri'],
                'printKabupatenKotaSantri' => $dataSantri['KabupatenKotaSantri'],
                'printProvinsiSantri' => $dataSantri['ProvinsiSantri'],
                'printKodePosSantri' => $dataSantri['KodePosSantri'],
                //data jarak dan transportasi
                'printJarakTempuhSantri' => $dataSantri['JarakTempuhSantri'],
                'printTransportasiSantri' => $dataSantri['TransportasiSantri'],
                'printWaktuTempuhSantri' => $dataSantri['WaktuTempuhSantri'],

            ];

            // Proses foto
            $this->saveLog("ℹ️ INFO: Memproses foto santri");
            if (!empty($dataSantri['PhotoProfil'])) {

                $this->saveLog("ℹ️ INFO: Menggunakan path development untuk foto");
                // Tentukan path berdasarkan environment
                if (ENVIRONMENT === 'production') {
                    $uploadPath = 'https://tpqsmart.simpedis.com/uploads/santri/';  // Path di server production
                } else {
                    $uploadPath = ROOTPATH . 'public/uploads/santri/';
                }

                $fotoPath = $uploadPath . $dataSantri['PhotoProfil'];
                $this->saveLog("ℹ️ INFO: Path foto: " . $fotoPath);
                $fotoData = file_exists($fotoPath) ? file_get_contents($fotoPath) : null;

                if ($fotoData) {
                    $data['printFotoSantri'] = 'data:image/jpeg;base64,' . base64_encode($fotoData);
                    $this->saveLog("✓ OK: Foto berhasil diproses");
                } else {
                    $this->saveLog("⚠️ WARN: Foto tidak ditemukan di path: " . $fotoPath);
                }
            }

            try {
                $this->saveLog("ℹ️ INFO: Initial proses foto santri untuk memastikan format gambar valid");
                $fotoSantri = $this->processFotoSantri($data['printFotoSantri'] ?? null);
                $this->saveLog("✓ OK: Foto santri berhasil diproses");
            } catch (\Exception $e) {
                $this->saveLog("⚠️ Foto tidak tersedia: " . $e->getMessage());
                $fotoSantri = null;
                $this->saveLog(" ℹ️ INFO: Foto santri tidak tersedia, dan akan dikosongkan");
            }
            // Konfigurasi DOMPDF
            $this->saveLog("ℹ️ INFO: Mengkonfigurasi DOMPDF");
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('isPhpEnabled', true);
            $dompdf = new Dompdf($options);
            $this->saveLog("✓ OK: Konfigurasi DOMPDF berhasil");

            // Generate PDF
            $this->saveLog("ℹ️ INFO: Memulai generate HTML");
            $html = view('backend/santri/pdf_template', [
                'data' => $data,
                'fotoSantri' => $fotoSantri
            ]);

            // log generate Load HTML ke DOMPDF 
            $this->saveLog("ℹ️ INFO: Memulai generate PDF");
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $this->saveLog("✓ OK: PDF berhasil di-generate");

            // Output PDF dengan header yang benar
            $filename = 'Data_Santri_' . str_replace(' ', '_', $dataSantri['NamaSantri']) . '.pdf';

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($dompdf->output());

        } catch (\Exception $e) {
            $this->saveLog("❌ ERROR: " . $e->getMessage());
            log_message('error', '[generatePDFSantriBaru] Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // fungsi baru untuk showSuccessEmisStep
    public function showSuccessEmisStep($IdSantri = null)
    {
        //ambil data santri dari database   
        $dataSantri = $this->DataSantriBaru->where('IdSantri', $IdSantri)->first();
        $data = [
            'page_title' => 'Data Santri Baru Berhasil Dikirim',
            'dataSantri' => $dataSantri
        ];
        return view('backend/santri/successEmisStep', $data);
    }
}
