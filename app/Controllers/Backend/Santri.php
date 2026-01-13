<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\EncryptModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriBaruModel;
use App\Models\NilaiModel;
use App\Models\KelasModel;
use App\Models\ToolsModel;
use App\Models\MdaModel;
use App\Models\TpqModel;
use App\Models\KelasMateriPelajaranModel;
use App\Models\SignatureModel;
use App\Models\Backend\Santri\VerifikasiSantriModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Santri extends BaseController
{
    public $DataSantri;
    protected $encryptModel;
    protected $helpFunction;
    protected $DataSantriBaru;
    protected $nilaiModel;
    protected $kelasModel;
    protected $toolsModel;
    protected $mdaModel;
    protected $tpqModel;
    protected $signatureModel;
    protected $verifikasiSantriModel;

    public function __construct()
    {
        $this->encryptModel = new EncryptModel();
        $this->DataSantri = new SantriModel();
        $this->DataSantriBaru = new SantriBaruModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->nilaiModel = new NilaiModel();
        $this->kelasModel = new KelasModel();
        $this->toolsModel = new ToolsModel();
        $this->mdaModel = new MdaModel();
        $this->tpqModel = new TpqModel();
        $this->signatureModel = new SignatureModel();
        $this->verifikasiSantriModel = new VerifikasiSantriModel();
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
            'dataKelas' => $dataKelas,
            'isPublic' => false  // Admin context
        ];

        return view('backend/santri/createEmisStep', $data);
    }

    // Fungsi untuk mengecek status MDA dan mapping kelas
    public function checkMdaStatus($idTpq = null)
    {
        if (empty($idTpq)) {
            return $this->response->setJSON([
                'hasMda' => false,
                'kelasMapping' => []
            ]);
        }

        $toolsModel = new \App\Models\ToolsModel();
        
        // Handle admin dengan IdTpq=0, gunakan 'default' sebagai gantinya
        $idTpqForQuery = (empty($idTpq) || $idTpq == '0' || $idTpq == 0) ? 'default' : $idTpq;

        // Check apakah memiliki lembaga MDA
        $hasMda = $toolsModel->getSettingAsBool($idTpqForQuery, 'MDA_S1_ApakahMemilikiLembagaMDATA', false);
        $kelasMapping = [];

        if ($hasMda) {
            // Ambil mapping persamaan kelas MDA
            $persamaanKelas = $toolsModel->getSettingAsString($idTpqForQuery, 'MDA_S1_PersamaanKelasMDA', '');
            
            // Parse mapping: TPQ3=MDA1, TPQ4=MDA2, TPQ5=MDA3, TPQ6=MDA4
            if (!empty($persamaanKelas)) {
                $pairs = explode(',', $persamaanKelas);
                foreach ($pairs as $pair) {
                    $pair = trim($pair);
                    if (strpos($pair, '=') !== false) {
                        list($tpqKelas, $mdaKelas) = explode('=', $pair, 2);
                        $kelasMapping[trim($tpqKelas)] = trim($mdaKelas);
                    }
                }
            }
        }

        return $this->response->setJSON([
            'hasMda' => $hasMda,
            'kelasMapping' => $kelasMapping
        ]);
    }

    // fungsi untuk menyimpan
    public function save()
    {
        // log header ================================
        log_message('info', 'Santri: save - Header');
        // Inisialisasi log
        $logs = [];
        log_message('info', 'Santri: save - Memulai proses save data santri');

        try {
            $IdSantri = "";
            // 1. Ambil tahun saat ini
            $tahunSekarang = date('Y');
            log_message('info', 'Santri: save - Tahun sekarang: ' . $tahunSekarang);

            // Cari IdSantri terakhir
            $lastSantri = $this->DataSantriBaru->like('IdSantri', $tahunSekarang, 'after')
                ->orderBy('IdSantri', 'DESC')
                ->first();

            // 2. Cek apakah ada data santri yang sudah ada dan ambil ID terakhir kemudian buat ID baru
            if ($lastSantri) {
                // Ambil 4 digit terakhir dari ID Santri
                $lastNumber = intval(substr($lastSantri['IdSantri'], -4));
                $newNumber = $lastNumber + 1;
                // Pastikan selalu 4 digit dengan str_pad
                $IdSantri = $tahunSekarang . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                log_message('info', 'Santri: save - ID Santri baru dibuat: ' . $IdSantri);
            } else {
                $IdSantri = $tahunSekarang . '0001';
                log_message('info', 'Santri: save - ID Santri pertama dibuat: ' . $IdSantri);
            }

            // Siapkan data untuk disimpan
            $data = [
                // Data TPQ
                'IdTpq' => $this->request->getPost('IdTpq'),
                'IdKelas' => $this->request->getPost('IdKelas'),
                'Agama' => 'Islam',
                // Data Santri
                'IdSantri' => $IdSantri,
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

                // Data Alamat Ayah
                'TinggalDiluarNegeriAyah' => $this->request->getPost('TinggalDiluarNegeriAyah'),
                'StatusKepemilikanRumahAyah' => $this->request->getPost('StatusKepemilikanRumahAyah'),
                'ProvinsiAyah' => $this->request->getPost('ProvinsiAyah'),
                'KabupatenKotaAyah' => $this->request->getPost('KabupatenKotaAyah'),
                'KecamatanAyah' => $this->request->getPost('KecamatanAyah'),
                'KelurahanDesaAyah' => $this->request->getPost('KelurahanDesaAyah'),
                'RtAyah' => $this->convertRTRW($this->request->getPost('RtAyah')),
                'RwAyah' => $this->convertRTRW($this->request->getPost('RwAyah')),
                'AlamatAyah' => $this->request->getPost('AlamatAyah'),
                'KodePosAyah' => $this->request->getPost('KodePosAyah'),

                // Data Alamat Ibu
                'TinggalDiluarNegeriIbu' => $this->request->getPost('TinggalDiluarNegeriIbu'),
                'StatusKepemilikanRumahIbu' => $this->request->getPost('StatusKepemilikanRumahIbu'),
                'ProvinsiIbu' => $this->request->getPost('ProvinsiIbu'),
                'KabupatenKotaIbu' => $this->request->getPost('KabupatenKotaIbu'),
                'KecamatanIbu' => $this->request->getPost('KecamatanIbu'),
                'KelurahanDesaIbu' => $this->request->getPost('KelurahanDesaIbu'),
                'RtIbu' => $this->convertRTRW($this->request->getPost('RtIbu')),
                'RwIbu' => $this->convertRTRW($this->request->getPost('RwIbu')),
                'AlamatIbu' => $this->request->getPost('AlamatIbu'),
                'KodePosIbu' => $this->request->getPost('KodePosIbu'),

                // Data Alamat Santri
                'StatusMukim' => $this->request->getPost('StatusMukim'),
                'StatusTempatTinggalSantri' => $this->request->getPost('StatusTempatTinggalSantri'),
                'ProvinsiSantri' => $this->request->getPost('ProvinsiSantri'),
                'KabupatenKotaSantri' => $this->request->getPost('KabupatenKotaSantri'),
                'KecamatanSantri' => $this->request->getPost('KecamatanSantri'),
                'KelurahanDesaSantri' => $this->request->getPost('KelurahanDesaSantri'),
                'RtSantri' => $this->convertRTRW($this->request->getPost('RtSantri')),
                'RwSantri' => $this->convertRTRW($this->request->getPost('RwSantri')),
                'AlamatSantri' => $this->request->getPost('AlamatSantri'),
                'KodePosSantri' => $this->request->getPost('KodePosSantri'),
                'JarakTempuhSantri' => $this->request->getPost('JarakTempuhSantri'),
                'TransportasiSantri' => $this->request->getPost('TransportasiSantri'),
                'WaktuTempuhSantri' => $this->request->getPost('WaktuTempuhSantri'),
                'TitikKoordinatSantri' => $this->request->getPost('TitikKoordinatSantri'),
            ];

            // Simpan data ke database
            // Ubah nilai array menjadi lowercase kemudian ucwords sebelum insert
            log_message('info', 'Santri: save - Memproses data merubah nilai array menjadi lowercase kemudian ucwords');
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
                        log_message('error', 'Santri: save - Error saat memproses data: ' . $e->getMessage());
                        // Kembalikan nilai asli jika terjadi error
                        return $value;
                    }
                }, $data);
            } catch (\Exception $e) {
                log_message('error', 'Santri: save - Error saat memproses data: ' . $e->getMessage());
                throw new \Exception('Gagal memproses data: ' . $e->getMessage());
            }

            // 3. Simpan data ke database
            $result = $this->DataSantriBaru->insert($processedData);

            if ($result === false) {
                // Ambil error dari model
                $errors = $this->DataSantriBaru->errors();
                log_message('error', 'Santri: save - Gagal menyimpan data: ' . json_encode($errors));
                throw new \Exception('Gagal menyimpan data: ' . json_encode($errors));
            }

            // 4. Proses upload file setelah data tersimpan
            $updateData = [];
            try {
                $photoProfilName = $this->uploadFile($this->request->getFile('PhotoProfil'), 'Profile', $IdSantri);
                if ($photoProfilName) $updateData['PhotoProfil'] = $photoProfilName;

                $namaFileKIP = $this->uploadFile($this->request->getFile('FileKIP'), 'Kip', $IdSantri);
                if ($namaFileKIP) $updateData['FileKIP'] = $namaFileKIP;

                $namaFileKkSantri = $this->uploadFile($this->request->getFile('FileKkSantri'), 'KkSantri', $IdSantri);
                if ($namaFileKkSantri) $updateData['FileKkSantri'] = $namaFileKkSantri;

                $namaFileKkAyah = $this->uploadFile($this->request->getFile('FileKKAyah'), 'KkAyah', $IdSantri);
                if ($namaFileKkAyah) $updateData['FileKkAyah'] = $namaFileKkAyah;

                $namaFileKkIbu = $this->uploadFile($this->request->getFile('FileKKIbu'), 'KkIbu', $IdSantri);
                if ($namaFileKkIbu) $updateData['FileKkIbu'] = $namaFileKkIbu;

                $namaFileKKS = $this->uploadFile($this->request->getFile('FileKKS'), 'Kks', $IdSantri);
                if ($namaFileKKS) $updateData['FileKKS'] = $namaFileKKS;

                $namaFilePKH = $this->uploadFile($this->request->getFile('FilePKH'), 'Pkh', $IdSantri);
                if ($namaFilePKH) $updateData['FilePKH'] = $namaFilePKH;

                // Handling checkbox
                if ($this->request->getPost('KkAyahSamaDenganSantri') == 'on' && $namaFileKkSantri) {
                    $updateData['FileKkAyah'] = $namaFileKkSantri;
                }
                if ($this->request->getPost('KkIbuSamaDenganAyahAtauSantri') == 'on' && $namaFileKkSantri) {
                    $updateData['FileKkIbu'] = $namaFileKkSantri;
                }

                // Update data dengan nama file yang berhasil diupload
                if (!empty($updateData)) {
                    $this->DataSantriBaru->update($result, $updateData);
                }
            } catch (\Exception $e) {
                log_message('error', 'Santri: save - Error saat mengupload file: ' . $e->getMessage());
            }

            log_message('info', 'Santri: save - Data berhasil disimpan');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data santri berhasil disimpan',
                'redirect' => base_url('backend/santri/showSuccessEmisStep/' . $IdSantri)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Santri: save - Error saat menyimpan data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data santri: ' . $e->getMessage(),
                'errors' => $this->DataSantriBaru->errors(), // Menambahkan detail error
                'debug' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    public function update()
    {
        // log header ================================
        log_message('info', 'Santri: update - Header');
        // Inisialisasi log
        $logs = [];
        log_message('info', 'Santri: update - Memulai proses update data santri');

        try {
            $IdSantri = $this->request->getPost('IdSantri');

            // Ambil data santri yang akan diupdate
            $existingSantri = $this->DataSantriBaru->where('IdSantri', $IdSantri)->first();
            if (!$existingSantri) {
                throw new \Exception('Data santri tidak ditemukan');
            }

            $IdDataSantri = $existingSantri['id'];

            // Handle upload untuk setiap file dan simpan nama file ke variabel
            $photoProfilName = $this->uploadFile($this->request->getFile('PhotoProfil'), 'Profile', $IdSantri, $existingSantri['PhotoProfil']);
            $namaFileKIP = $this->uploadFile($this->request->getFile('FileKIP'), 'Kip', $IdSantri, $existingSantri['FileKIP']);
            $namaFileKkSantri = $this->uploadFile($this->request->getFile('FileKkSantri'), 'KkSantri', $IdSantri, $existingSantri['FileKkSantri']);
            $namaFileKkAyah = $this->uploadFile($this->request->getFile('FileKKAyah'), 'KkAyah', $IdSantri, $existingSantri['FileKkAyah']);
            $namaFileKkIbu = $this->uploadFile($this->request->getFile('FileKKIbu'), 'KkIbu', $IdSantri, $existingSantri['FileKkIbu']);
            $namaFileKKS = $this->uploadFile($this->request->getFile('FileKKS'), 'Kks', $IdSantri, $existingSantri['FileKKS']);
            $namaFilePKH = $this->uploadFile($this->request->getFile('FilePKH'), 'Pkh', $IdSantri, $existingSantri['FilePKH']);

            // Handling checkbox kksamaayah sama dengan santri maka file kk ayah sama dengan file kk santri
            if ($this->request->getPost('KkAyahSamaDenganSantri') == 'on')
                $namaFileKkAyah = $namaFileKkSantri;
            if ($this->request->getPost('KkIbuSamaDenganAyahAtauSantri') == 'on')
                $namaFileKkIbu = $namaFileKkSantri;

            // Siapkan data untuk diupdate
            $data = [
                // Data TPQ
                'IdTpq' => $this->request->getPost('IdTpq'),
                'IdKelas' => $this->request->getPost('IdKelas'),
                'Agama' => 'Islam',
                // Data Santri
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

                // Data Alamat
                'TinggalDiluarNegeriAyah' => $this->request->getPost('TinggalDiluarNegeriAyah'),
                'StatusKepemilikanRumahAyah' => $this->request->getPost('StatusKepemilikanRumahAyah'),
                'ProvinsiAyah' => $this->request->getPost('ProvinsiAyah'),
                'KabupatenKotaAyah' => $this->request->getPost('KabupatenKotaAyah'),
                'KecamatanAyah' => $this->request->getPost('KecamatanAyah'),
                'KelurahanDesaAyah' => $this->request->getPost('KelurahanDesaAyah'),
                'RtAyah' => $this->convertRTRW($this->request->getPost('RtAyah')),
                'RwAyah' => $this->convertRTRW($this->request->getPost('RwAyah')),
                'AlamatAyah' => $this->request->getPost('AlamatAyah'),
                'KodePosAyah' => $this->request->getPost('KodePosAyah'),

                'TinggalDiluarNegeriIbu' => $this->request->getPost('TinggalDiluarNegeriIbu'),
                'StatusKepemilikanRumahIbu' => $this->request->getPost('StatusKepemilikanRumahIbu'),
                'ProvinsiIbu' => $this->request->getPost('ProvinsiIbu'),
                'KabupatenKotaIbu' => $this->request->getPost('KabupatenKotaIbu'),
                'KecamatanIbu' => $this->request->getPost('KecamatanIbu'),
                'KelurahanDesaIbu' => $this->request->getPost('KelurahanDesaIbu'),
                'RtIbu' => $this->convertRTRW($this->request->getPost('RtIbu')),
                'RwIbu' => $this->convertRTRW($this->request->getPost('RwIbu')),
                'AlamatIbu' => $this->request->getPost('AlamatIbu'),
                'KodePosIbu' => $this->request->getPost('KodePosIbu'),

                'StatusMukim' => $this->request->getPost('StatusMukim'),
                'StatusTempatTinggalSantri' => $this->request->getPost('StatusTempatTinggalSantri'),
                'ProvinsiSantri' => $this->request->getPost('ProvinsiSantri'),
                'KabupatenKotaSantri' => $this->request->getPost('KabupatenKotaSantri'),
                'KecamatanSantri' => $this->request->getPost('KecamatanSantri'),
                'KelurahanDesaSantri' => $this->request->getPost('KelurahanDesaSantri'),
                'RtSantri' => $this->convertRTRW($this->request->getPost('RtSantri')),
                'RwSantri' => $this->convertRTRW($this->request->getPost('RwSantri')),
                'AlamatSantri' => $this->request->getPost('AlamatSantri'),
                'KodePosSantri' => $this->request->getPost('KodePosSantri'),
                'JarakTempuhSantri' => $this->request->getPost('JarakTempuhSantri'),
                'TransportasiSantri' => $this->request->getPost('TransportasiSantri'),
                'WaktuTempuhSantri' => $this->request->getPost('WaktuTempuhSantri'),
                'TitikKoordinatSantri' => $this->request->getPost('TitikKoordinatSantri'),
            ];

            // Ubah nilai array menjadi lowercase kemudian ucwords sebelum update
            log_message('info', 'Santri: update - Memproses data merubah nilai array menjadi lowercase kemudian ucwords');
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
                        log_message('error', 'Santri: update - Error saat memproses data: ' . $e->getMessage());
                        return $value;
                    }
                }, $data);
            } catch (\Exception $e) {
                log_message('error', 'Santri: update - Error saat memproses data: ' . $e->getMessage());
                throw new \Exception('Gagal memproses data: ' . $e->getMessage());
            }

            // Update data ke database
            $result = $this->DataSantriBaru->update($IdDataSantri, $processedData);

            if ($result === false) {
                $errors = $this->DataSantriBaru->errors();
                log_message('error', 'Santri: update - Gagal mengupdate data: ' . json_encode($errors));
                throw new \Exception('Gagal mengupdate data: ' . json_encode($errors));
            }

            log_message('info', 'Santri: update - Data berhasil diupdate');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data santri berhasil diupdate',
                //'redirect' => base_url('backend/santri/showSuccessEmisStep/' . $IdSantri)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: update - Error saat mengupdate data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data santri: ' . $e->getMessage(),
                'errors' => $this->DataSantriBaru->errors(),
                'debug' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    // Endpoint untuk cek NIK santri baru
    public function getNikSantri($NikSantri)
    {
        $santri = $this->DataSantriBaru->getSantriByNik($NikSantri);

        // Set response dengan properti exists
        return $this->response->setJSON([
            'exists' => !empty($santri), // true jika santri ditemukan, false jika tidak
            'data' => $santri // data santri jika ada
        ]);
    }

    // Endpoint untuk mendapatkan detail santri baru
    public function getDetailSantri($IdSantri)
    {
        $santri = $this->DataSantriBaru->getDetailSantriById($IdSantri);
        
        return $this->response->setJSON([
            'success' => !empty($santri),
            'data' => $santri
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
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        $IdKelas = session()->get('IdKelas');

        // Ambil data santri menggunakan method dari model
        $santri = $this->DataSantriBaru->getListSantriBaru($IdTpq, $IdKelas);

        $tpq = $this->helpFunction->getDataTpq();
        usort($tpq, function ($a, $b) {
            return strcmp($a['NamaTpq'], $b['NamaTpq']);
        });

        // Ambil jumlah santri per TPQ menggunakan method dari model
        $santriPerTpq = $this->DataSantriBaru->getJumlahSantriPerTpq();

        // Gabungkan data jumlah santri ke array TPQ
        foreach ($tpq as &$t) {
            $jumlahSantri = 0;
            foreach ($santriPerTpq as $count) {
                if ($count['IdTpq'] == $t['IdTpq']) {
                    $jumlahSantri = $count['JumlahSantri'];
                    break;
                }
            }
            $t['JumlahSantri'] = $jumlahSantri;
        }

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        if (!empty($santri) && !empty($IdTpq)) {
            foreach ($santri as $key => $santriItem) {
                if (isset($santriItem['NamaKelas']) && !empty($santriItem['NamaKelas'])) {
                    $namaKelasOriginal = $santriItem['NamaKelas'];
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $santri[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                }
            }
        }

        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $santri,
            'dataTpq' => $tpq
        ];
        return view('backend/santri/listSantriBaru', $data);
    }

    // Page: Profil Data Santri - List
    public function showProfilSantri()
    {
        // Ambil filter dari request atau session
        $filterIdTpq = $this->request->getGet('filterIdTpq');
        $filterIdKelas = $this->request->getGet('filterIdKelas');

        $sessionIdTpq = session()->get('IdTpq');
        $sessionIdKelas = session()->get('IdKelas');
        $isOperator = in_groups('Operator');
        $isAdmin = in_groups('Admin');
        $isGuru = in_groups('Guru');
        $isKepalaTpq = in_groups('Kepala TPQ');

        // Tentukan IdTpq yang akan digunakan
        $IdTpq = null;
        if ($isAdmin) {
            $IdTpq = $filterIdTpq ?: $sessionIdTpq;
        } else {
            $IdTpq = $sessionIdTpq;
        }

        // Tentukan IdKelas yang akan digunakan
        $IdKelas = null;
        $idKelasArray = null;

        if ($isOperator && $IdTpq !== null) {
            // Jika Operator login, ambil semua kelas dari TPQ yang memiliki santri aktif
            $idKelasArray = $this->kelasModel->getAllKelasAktifByTpq($IdTpq);

            // Jika ada filter kelas dari request, gunakan filter tersebut
            if ($filterIdKelas && $filterIdKelas !== '') {
                // Handle single value atau string dengan koma
                if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                    $filterArray = array_filter(explode(',', $filterIdKelas));
                    $IdKelas = array_intersect($filterArray, $idKelasArray);
                } else {
                    if (in_array($filterIdKelas, $idKelasArray)) {
                        $IdKelas = $filterIdKelas;
                    }
                }
            }
        } else {
            // Untuk role lain, gunakan filter dari request atau session
            if ($filterIdKelas && $filterIdKelas !== '') {
                // Handle single value atau string dengan koma
                if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                    $IdKelas = array_filter(explode(',', $filterIdKelas));
                } else {
                    $IdKelas = $filterIdKelas;
                }
            } else {
                $IdKelas = $sessionIdKelas;
            }
        }

        // Jika ada idKelasArray untuk Operator, gunakan itu, jika tidak gunakan IdKelas
        if ($idKelasArray !== null && $IdKelas === null) {
            // Gunakan semua kelas dari TPQ
        } else if ($IdKelas !== null) {
            $idKelasArray = null; // Override dengan filter yang dipilih
        }

        // Ambil data profil santri menggunakan method dari model
        $santri = $this->DataSantriBaru->getProfilSantri($IdTpq, $IdKelas, $idKelasArray);

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        if (!empty($santri) && !empty($IdTpq)) {
            foreach ($santri as $key => $santriItem) {
                if (isset($santriItem['NamaKelas']) && !empty($santriItem['NamaKelas'])) {
                    $namaKelasOriginal = $santriItem['NamaKelas'];
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $santri[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                }
            }
        }

        // Get data TPQ untuk filter dropdown
        $dataTpq = $this->helpFunction->getDataTpq();
        // Urutkan berdasarkan NamaTpq ASC (case-insensitive)
        usort($dataTpq, function ($a, $b) {
            return strcasecmp($a['NamaTpq'] ?? '', $b['NamaTpq'] ?? '');
        });

        // Get data Kelas untuk filter dropdown dan tab
        $dataKelas = [];
        if ($isAdmin || $isOperator || $isKepalaTpq) {
            $dataKelas = $this->helpFunction->getDataKelas();
        } else if ($isGuru) {
            if ($sessionIdKelas && is_array($sessionIdKelas)) {
                $allKelas = $this->helpFunction->getDataKelas();
                $dataKelas = array_filter($allKelas, function ($kelas) use ($sessionIdKelas) {
                    return in_array($kelas['IdKelas'], $sessionIdKelas);
                });
                $dataKelas = array_values($dataKelas);
            }
        }

        // Terapkan mapping MDA pada nama kelas di filter dropdown
        if (!empty($dataKelas) && !empty($IdTpq)) {
            foreach ($dataKelas as $key => $kelas) {
                if (isset($kelas['NamaKelas']) && !empty($kelas['NamaKelas'])) {
                    $namaKelasOriginal = $kelas['NamaKelas'];
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $dataKelas[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                    $dataKelas[$key]['NamaKelasOriginal'] = $namaKelasOriginal;
                }
            }
        }

        // Kelompokkan santri per kelas untuk tab
        $santriPerKelas = [];
        $listIdKelas = [];
        if (!empty($santri)) {
            foreach ($santri as $santriItem) {
                $idKelasSantri = $santriItem['IdKelas'] ?? null;
                if ($idKelasSantri) {
                    if (!isset($santriPerKelas[$idKelasSantri])) {
                        $santriPerKelas[$idKelasSantri] = [];
                        $listIdKelas[] = $idKelasSantri;
                    }
                    $santriPerKelas[$idKelasSantri][] = $santriItem;
                }
            }
        }

        // Ambil object kelas untuk tab (hanya kelas yang memiliki santri)
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdGuru = session()->get('IdGuru');
        $guruIdForKelas = ($isOperator && empty($IdGuru)) ? null : $IdGuru;
        $dataKelasObject = [];
        if (!empty($listIdKelas)) {
            $dataKelasObject = $this->helpFunction->getListKelas($IdTpq, $IdTahunAjaran, $listIdKelas, $guruIdForKelas, $isOperator);

            // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
            foreach ($dataKelasObject as $kelas) {
                $namaKelasOriginal = $kelas->NamaKelas;
                $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                $kelas->NamaKelas = $this->helpFunction->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );
            }
        }

        // Cek apakah user adalah Kepala Sekolah
        $IdGuru = session()->get('IdGuru');
        $isKepalaSekolah = false;
        if ($IdGuru && $IdTpq) {
            $jabatanData = $this->helpFunction->getStrukturLembagaJabatan($IdGuru, $IdTpq);
            if (!empty($jabatanData)) {
                foreach ($jabatanData as $jabatan) {
                    if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                        $isKepalaSekolah = true;
                        break;
                    }
                }
            }
        }

        // Ambil status signature untuk profil santri per kelas (untuk semua user, bukan hanya Kepala Sekolah)
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $bulkSignatureStatus = [];
        if ($IdTahunAjaran && $IdTpq && !empty($dataKelasObject)) {
            // Ambil semua signature untuk profil santri (cari berdasarkan IdTpq dan IdTahunAjaran)
            // Untuk statistik, kita ambil semua signature Kepsek di TPQ ini, bukan hanya dari IdGuru tertentu
            $allSignatures = [];
            if (!empty($santri)) {
                $santriIds = array_column($santri, 'IdSantri');

                // Query untuk mengambil semua signature Kepsek di TPQ ini (tidak filter by IdGuru untuk statistik)
                $allSignatures = $this->signatureModel->where([
                    'IdTpq' => $IdTpq,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'JenisDokumen' => 'ProfilSantri',
                    'SignatureData' => 'Kepsek',
                    'StatusValidasi' => 'Valid'
                ])->whereIn('IdSantri', $santriIds)->findAll();

                // Debug: Log untuk memastikan data terambil
                log_message('debug', 'ProfilSantri - Signature count: ' . count($allSignatures));
                if (!empty($allSignatures)) {
                    log_message('debug', 'ProfilSantri - First signature: ' . json_encode($allSignatures[0]));
                }
            }

            // Hitung statistik per kelas
            foreach ($dataKelasObject as $kelas) {
                $kelasSantri = $santriPerKelas[$kelas->IdKelas] ?? [];
                $totalSantri = count($kelasSantri);
                $ttdKepsek = 0;

                if ($totalSantri > 0) {
                    $kelasSantriIds = array_column($kelasSantri, 'IdSantri');
                    foreach ($allSignatures as $sig) {
                        // Pastikan menggunakan array access yang benar
                        $sigIdSantri = is_array($sig) ? ($sig['IdSantri'] ?? null) : ($sig->IdSantri ?? null);
                        if ($sigIdSantri && in_array($sigIdSantri, $kelasSantriIds)) {
                            $ttdKepsek++;
                        }
                    }
                }

                $belumTtdKepsek = $totalSantri - $ttdKepsek;
                $allSignedKepsek = ($totalSantri > 0 && $ttdKepsek == $totalSantri);

                $bulkSignatureStatus[$kelas->IdKelas] = [
                    'total' => $totalSantri,
                    'ttd_kepsek' => $ttdKepsek,
                    'belum_ttd_kepsek' => $belumTtdKepsek,
                    'all_signed_kepsek' => $allSignedKepsek
                ];
            }
        }

        $data = [
            'page_title' => 'Profil Data Santri',
            'dataSantri' => $santri,
            'dataTpq' => $dataTpq,
            'dataKelas' => $dataKelas, // Untuk filter dropdown
            'dataKelasObject' => $dataKelasObject, // Untuk tab (object dengan IdKelas, NamaKelas)
            'santriPerKelas' => $santriPerKelas, // Data santri yang sudah dikelompokkan per kelas
            'currentIdTpq' => $IdTpq,
            'currentIdKelas' => $IdKelas ?: $idKelasArray,
            'isAdmin' => $isAdmin,
            'isOperator' => $isOperator,
            'isGuru' => $isGuru,
            'isKepalaTpq' => $isKepalaTpq,
            'isKepalaSekolah' => $isKepalaSekolah,
            'bulkSignatureStatus' => $bulkSignatureStatus,
        ];
        return view('backend/santri/listDataProfilSantri', $data);
    }

    /**
     * Menampilkan detail profil santri lengkap dengan segmentasi
     * Informasi Santri, Orang Tua, dan Alamat
     */
    public function detailProfilSantri()
    {
        // Ambil IdSantri dari session atau request
        $idSantri = session()->get('IdSantri');
        if (empty($idSantri)) {
            $idSantri = $this->request->getGet('id');
        }

        if (empty($idSantri)) {
            return redirect()->to(base_url('backend/dashboard/santri'))->with('error', 'ID Santri tidak ditemukan');
        }

        // Ambil data santri lengkap dari tabel santri_baru
        $santriData = $this->DataSantriBaru->getProfilDetailSantri($idSantri);

        if (empty($santriData)) {
            return redirect()->to(base_url('backend/dashboard/santri'))->with('error', 'Data santri tidak ditemukan');
        }

        // Siapkan data untuk view
        $data = [
            'page_title' => 'Detail Profil Santri',
            'santri' => $santriData,
            'NamaLogin' => $santriData['NamaSantri'] ?? 'Santri',
            'PeranLogin' => 'Santri',
        ];

        // Ambil foto profil
        $photoUrl = base_url('images/no-photo.jpg');
        if (!empty($santriData['PhotoProfil'])) {
            $photoPath = FCPATH . 'uploads/santri/' . $santriData['PhotoProfil'];
            if (file_exists($photoPath)) {
                $photoUrl = base_url('uploads/santri/' . $santriData['PhotoProfil']);
            }
        }
        $data['photoUrl'] = $photoUrl;

        return view('backend/santri/detailProfilSantri', $data);
    }

    // Page: Profil Data Santri - Detail

    public function showAturSantriBaru()
    {
        // Ambil filter dari request (untuk AJAX) atau session (untuk initial load)
        $filterIdTpq = $this->request->getGet('filterIdTpq');
        $filterIdKelas = $this->request->getGet('filterIdKelas');

        // Ambil IdTpq dari session untuk role-based filtering
        $sessionIdTpq = session()->get('IdTpq');
        $sessionIdKelas = session()->get('IdKelas');

        // Tentukan IdTpq yang akan digunakan berdasarkan role
        $IdTpq = null;
        $isAdmin = in_groups('Admin');
        $isGuru = in_groups('Guru');
        $isOperator = in_groups('Operator');
        $isKepalaTpq = in_groups('Kepala TPQ');

        if ($isAdmin) {
            // Admin bisa pilih semua TPQ
            $IdTpq = $filterIdTpq ?: $sessionIdTpq;
        } else {
            // Operator/Guru/Kepala TPQ: set sesuai TPQ mereka dan disable
            $IdTpq = $sessionIdTpq;
        }

        // Get data TPQ untuk filter dropdown
        $dataTpq = $this->helpFunction->getDataTpq();
        usort($dataTpq, function ($a, $b) {
            return strcmp($a['NamaTpq'], $b['NamaTpq']);
        });

        // Get data Kelas untuk filter dropdown
        $dataKelas = [];
        if ($isAdmin) {
            // Admin: bisa select semua kelas
            $dataKelas = $this->helpFunction->getDataKelas();
        } else if ($isOperator || $isKepalaTpq) {
            // Operator/Kepala TPQ: bisa select semua kelas (bisa filter berdasarkan TPQ yang dipilih)
            $dataKelas = $this->helpFunction->getDataKelas();
        } else if ($isGuru) {
            // Guru: hanya kelas yang dimiliki
            if ($sessionIdKelas && is_array($sessionIdKelas)) {
                $allKelas = $this->helpFunction->getDataKelas();
                $dataKelas = array_filter($allKelas, function ($kelas) use ($sessionIdKelas) {
                    return in_array($kelas['IdKelas'], $sessionIdKelas);
                });
                // Re-index array untuk menghindari masalah dengan array_filter
                $dataKelas = array_values($dataKelas);
            }
        }

        // Terapkan mapping MDA pada nama kelas di filter dropdown jika TPQ yang dipilih memiliki MDA aktif
        if (!empty($dataKelas) && !empty($IdTpq)) {
            foreach ($dataKelas as $key => $kelas) {
                if (isset($kelas['NamaKelas']) && !empty($kelas['NamaKelas'])) {
                    $namaKelasOriginal = $kelas['NamaKelas'];
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $dataKelas[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                    // Simpan nama kelas asli untuk referensi
                    $dataKelas[$key]['NamaKelasOriginal'] = $namaKelasOriginal;
                }
            }
        }

        // Terapkan filter IdKelas
        $IdKelas = null;
        if ($isAdmin || $isOperator || $isKepalaTpq) {
            // Admin/Operator/Kepala TPQ: gunakan filter dari request
            if ($filterIdKelas) {
                // Jika filterIdKelas adalah string dengan koma, convert ke array
                if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                    $IdKelas = array_filter(explode(',', $filterIdKelas));
                } else {
                    $IdKelas = $filterIdKelas;
                }
            }
        } else if ($isGuru) {
            // Guru: jika memiliki lebih dari satu kelas, bisa filter
            // Jika hanya satu kelas, gunakan kelas tersebut
            if ($sessionIdKelas && is_array($sessionIdKelas)) {
                $jumlahKelasGuru = count($sessionIdKelas);

                if ($jumlahKelasGuru > 1) {
                    // Guru memiliki lebih dari satu kelas: bisa filter
                    if ($filterIdKelas) {
                        // Validasi: pastikan filterIdKelas adalah bagian dari kelas yang dimiliki guru
                        $filterArray = [];
                        if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                            $filterArray = array_filter(explode(',', $filterIdKelas));
                        } else {
                            $filterArray = [$filterIdKelas];
                        }

                        // Filter hanya kelas yang dimiliki guru
                        $IdKelas = array_intersect($filterArray, $sessionIdKelas);

                        // Jika setelah filter kosong, gunakan semua kelas
                        if (empty($IdKelas)) {
                            $IdKelas = $sessionIdKelas;
                        }
                    } else {
                        // Tidak ada filter: gunakan semua kelas
                        $IdKelas = $sessionIdKelas;
                    }
                } else {
                    // Guru hanya memiliki satu kelas: gunakan kelas tersebut
                    $IdKelas = $sessionIdKelas;
                }
            } else if ($sessionIdKelas) {
                // Jika sessionIdKelas bukan array (single value)
                $IdKelas = $sessionIdKelas;
            }
        }

        // Ambil data santri menggunakan method dari model
        $santri = $this->DataSantriBaru->getListAturSantriBaru($IdTpq, $IdKelas, $isGuru);

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        if (!empty($santri) && !empty($IdTpq)) {
            foreach ($santri as $key => $santriItem) {
                if (isset($santriItem['NamaKelas']) && !empty($santriItem['NamaKelas'])) {
                    $namaKelasOriginal = $santriItem['NamaKelas'];
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $santri[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                }
            }
        }

        // Hitung jumlah kelas untuk guru (untuk menentukan apakah filter bisa diubah)
        $guruJumlahKelas = 0;
        if ($isGuru && $sessionIdKelas) {
            if (is_array($sessionIdKelas)) {
                $guruJumlahKelas = count($sessionIdKelas);
            } else {
                $guruJumlahKelas = 1;
            }
        }

        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $santri,
            'dataTpq' => $dataTpq,
            'dataKelas' => $dataKelas,
            'currentIdTpq' => $IdTpq,
            'currentIdKelas' => $IdKelas,
            'isAdmin' => $isAdmin,
            'isGuru' => $isGuru,
            'isOperator' => $isOperator,
            'isKepalaTpq' => $isKepalaTpq,
            'guruJumlahKelas' => $guruJumlahKelas
        ];

        // Jika request AJAX, return JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $santri,
                'dataTpq' => $dataTpq,
                'dataKelas' => $dataKelas
            ]);
        }

        return view('backend/santri/aturSantriBaru', $data);
    }

    public function updatePhotoProfil()
    {
        try {
            $idSantri = $this->request->getPost('idSantri');
            $idSantriBaru = $this->request->getPost('idSantriBaru');

            if (empty($idSantri) || empty($idSantriBaru)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Santri tidak boleh kosong'
                ]);
            }

            // Ambil data santri untuk mendapatkan nama file lama
            $santri = $this->DataSantriBaru->find($idSantri);
            if (!$santri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            // Ambil file dari request
            $file = $this->request->getFile('photo');
            if (!$file || !$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File tidak valid atau tidak ada file yang diupload'
                ]);
            }

            // Upload file menggunakan method yang sudah ada
            $oldFileName = $santri['PhotoProfil'] ?? null;
            $photoProfilName = $this->uploadFile($file, 'Profile', $idSantriBaru, $oldFileName);

            if (!$photoProfilName) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupload foto'
                ]);
            }

            // Update database
            $this->DataSantriBaru->update($idSantri, [
                'PhotoProfil' => $photoProfilName
            ]);

            log_message('info', 'Santri: updatePhotoProfil - Foto profil berhasil diupdate untuk ID: ' . $idSantri);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto profil berhasil diupdate',
                'photoName' => $photoProfilName
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: updatePhotoProfil - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function showSantriEmis()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        // Ambil data santri menggunakan method dari model
        $santri = $this->DataSantriBaru->getListSantriEmis($IdTpq);

        $tpq = $this->helpFunction->getDataTpq();
        usort($tpq, function ($a, $b) {
            return strcmp($a['NamaTpq'], $b['NamaTpq']);
        });

        // Ambil jumlah santri per TPQ menggunakan method dari model
        $santriPerTpq = $this->DataSantriBaru->getJumlahSantriPerTpq();

        // Gabungkan data jumlah santri ke array TPQ
        foreach ($tpq as &$t) {
            $jumlahSantri = 0;
            foreach ($santriPerTpq as $count) {
                if ($count['IdTpq'] == $t['IdTpq']) {
                    $jumlahSantri = $count['JumlahSantri'];
                    break;
                }
            }
            $t['JumlahSantri'] = $jumlahSantri;
        }

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        if (!empty($santri) && !empty($IdTpq)) {
            foreach ($santri as $key => $santriItem) {
                if (isset($santriItem['NamaKelas']) && !empty($santriItem['NamaKelas'])) {
                    $namaKelasOriginal = $santriItem['NamaKelas'];
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $santri[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                }
            }
        }

        $data = [
            'page_title' => 'Data Santri Update Emis',
            'dataSantri' => $santri,
            'dataTpq' => $tpq
        ];
        return view('backend/santri/dataSantriEmis', $data);
    }

    public function viewDetailSantriBaru($IdSantri = null)
    {
        $santri = $this->DataSantriBaru->GetData($IdSantri);
        return view('backend/santri/detailSantriBaru', $santri);
    }

    public function editSantri($IdSantri = null)
    {
        $santri = $this->DataSantriBaru->getDetailSantriForEdit($IdSantri);

        $data = [
            'page_title' => 'Edit Data Santri',
            'dataTpq' => $this->helpFunction->getDataTpq(),
            'dataKelas' => $this->helpFunction->getDataKelas(),
            'dataSantri' => $santri
        ];

        return view('backend/santri/editDataSantri', $data);
    }

    public function ubahKelas($IdSantri = null)
    {
        // Ambil data santri menggunakan method dari model
        $santri = $this->DataSantriBaru->getDetailSantriForUbahKelas($IdSantri);

        if (!$santri) {
            return redirect()->back()->with('error', 'Data santri tidak ditemukan');
        }

        // Ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        // Ambil tahun ajaran saat ini
        $currentTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

        // Cek apakah santri sudah ada di tbl_kelas_santri dengan tahun ajaran saat ini
        $existingKelasSantri = $this->kelasModel
            ->where('IdSantri', $IdSantri)
            ->where('IdTahunAjaran', $currentTahunAjaran)
            ->where('Status', 1)
            ->first();

        // Cek apakah ada nilai yang sudah diisi (Nilai > 0) untuk santri ini di tahun ajaran saat ini
        $existingNilai = $this->nilaiModel
            ->where('IdSantri', $IdSantri)
            ->where('IdTahunAjaran', $currentTahunAjaran)
            ->where('Nilai >', 0)
            ->first();

        // Cek total semua record nilai (baik yang ada nilai maupun tidak)
        $totalNilaiRecords = $this->nilaiModel
            ->where('IdSantri', $IdSantri)
            ->where('IdTahunAjaran', $currentTahunAjaran)
            ->countAllResults();

        $data = [
            'page_title' => 'Ubah Kelas Santri',
            'dataTpq' => $this->helpFunction->getDataTpq(),
            'dataKelas' => $this->helpFunction->getDataKelas(),
            'dataSantri' => $santri,
            'currentTahunAjaran' => $this->helpFunction->convertTahunAjaran($currentTahunAjaran),
            'existingKelasSantri' => $existingKelasSantri,
            'hasExistingNilai' => $existingNilai ? true : false,
            'existingNilaiCount' => $existingNilai ? $this->nilaiModel->where('IdSantri', $IdSantri)->where('IdTahunAjaran', $currentTahunAjaran)->where('Nilai >', 0)->countAllResults() : 0,
            'totalNilaiRecords' => $totalNilaiRecords
        ];

        return view('backend/santri/ubahKelas', $data);
    }

    public function processUbahKelas()
    {
        $IdSantri = $this->request->getPost('IdSantri');
        $IdKelasBaru = $this->request->getPost('IdKelas');

        // Validasi input
        if (!$IdSantri || !$IdKelasBaru) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        try {
            // Ambil data santri
            $santri = $this->DataSantriBaru->where('IdSantri', $IdSantri)->first();
            if (!$santri) {
                throw new \Exception('Data santri tidak ditemukan');
            }

            $IdTpq = $santri['IdTpq'];
            $IdKelasLama = $santri['IdKelas'];
            $currentTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

            // Jika kelas tidak berubah
            if ($IdKelasLama == $IdKelasBaru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kelas yang dipilih sama dengan kelas saat ini'
                ]);
            }

            // Cek apakah ada data nilai untuk dihapus (tidak perlu validasi lagi karena sudah di view)
            $totalNilaiRecords = $this->nilaiModel
                ->where('IdSantri', $IdSantri)
                ->where('IdTahunAjaran', $currentTahunAjaran)
                ->countAllResults();

            // Mulai transaksi database
            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Hapus semua nilai lama jika ada (konfirmasi sudah dilakukan di view)
            if ($totalNilaiRecords > 0) {
                $db->table('tbl_nilai')
                    ->where('IdSantri', $IdSantri)
                    ->where('IdTahunAjaran', $currentTahunAjaran)
                    ->delete();
            }

            // 2. Hapus record kelas lama di tbl_kelas_santri
            $db->table('tbl_kelas_santri')
                ->where('IdSantri', $IdSantri)
                ->where('IdTahunAjaran', $currentTahunAjaran)
                ->where('Status', 1)
                ->delete();

            // 3. Update IdKelas di tbl_santri_baru
            $db->table('tbl_santri_baru')
                ->where('IdSantri', $IdSantri)
                ->set(['IdKelas' => $IdKelasBaru])
                ->update();

            // 4. Insert kelas baru di tbl_kelas_santri
            $db->table('tbl_kelas_santri')->insert([
                'IdKelas' => $IdKelasBaru,
                'IdTpq' => $IdTpq,
                'IdSantri' => $IdSantri,
                'IdTahunAjaran' => $currentTahunAjaran,
                'Status' => 1
            ]);

            // 5. Buat data nilai baru untuk kelas baru (mengikuti logika updateNaikKelas)
            $listMateriPelajaran = $this->helpFunction->getKelasMateriPelajaran($IdKelasBaru, $IdTpq);
            $dataNilaiBaru = [];

            foreach ($listMateriPelajaran as $materiPelajaran) {
                if ($materiPelajaran->SemesterGanjil == 1) {
                    $dataNilaiBaru[] = [
                        'IdTpq' => $IdTpq,
                        'IdSantri' => $IdSantri,
                        'IdKelas' => $materiPelajaran->IdKelas,
                        'IdMateri' => $materiPelajaran->IdMateri,
                        'IdTahunAjaran' => $currentTahunAjaran,
                        'Semester' => "Ganjil"
                    ];
                }
                if ($materiPelajaran->SemesterGenap == 1) {
                    $dataNilaiBaru[] = [
                        'IdTpq' => $IdTpq,
                        'IdSantri' => $IdSantri,
                        'IdKelas' => $materiPelajaran->IdKelas,
                        'IdMateri' => $materiPelajaran->IdMateri,
                        'IdTahunAjaran' => $currentTahunAjaran,
                        'Semester' => "Genap"
                    ];
                }
            }

            // Insert data nilai baru
            if (!empty($dataNilaiBaru)) {
                $db->table('tbl_nilai')->insertBatch($dataNilaiBaru);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat memproses perubahan kelas');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kelas santri berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function ubahTpq($IdSantri = null)
    {
        // Ambil data santri menggunakan method dari model
        $santri = $this->DataSantriBaru->getDetailSantriForUbahTpq($IdSantri);

        if (!$santri) {
            return redirect()->back()->with('error', 'Data santri tidak ditemukan');
        }

        $data = [
            'page_title' => 'Ubah TPQ Santri (Pindah Sekolah)',
            'dataTpq' => $this->helpFunction->getDataTpq(),
            'dataSantri' => $santri
        ];

        return view('backend/santri/ubahTpq', $data);
    }

    public function processUbahTpq()
    {
        $IdSantri = $this->request->getPost('IdSantri');
        $IdTpqBaru = $this->request->getPost('IdTpq');

        // Validasi input dasar (validasi lengkap sudah dilakukan di view)
        if (!$IdSantri || !$IdTpqBaru) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        try {
            // Ambil data santri
            $santri = $this->DataSantriBaru->where('IdSantri', $IdSantri)->first();
            if (!$santri) {
                throw new \Exception('Data santri tidak ditemukan');
            }

            // Validasi TPQ tidak berubah (validasi ini tetap diperlukan untuk keamanan)
            $IdTpqLama = $santri['IdTpq'];
            if ($IdTpqLama == $IdTpqBaru) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'TPQ yang dipilih sama dengan TPQ saat ini'
                ]);
            }

            // Mulai transaksi database
            $db = \Config\Database::connect();
            $db->transStart();

            /**
             * PROSES PINDAH TPQ:
             * 1. Update IdTpq di tbl_santri_baru ke TPQ baru
             * 2. Set Active = 0 agar santri muncul sebagai santri baru di TPQ tujuan
             * 
             * CATATAN PENTING:
             * - Data lama di tbl_kelas_santri tetap ada dengan IdTpq lama (hak milik TPQ lama)
             * - Data lama di tbl_nilai tetap ada dengan IdTpq lama (hak milik TPQ lama)
             * - Data lama di tbl_absensi_santri tetap ada dengan IdTpq lama (hak milik TPQ lama)
             * - Saat santri diaktifkan di TPQ baru (via setKelasSantriBaru), akan generate:
             *   - Record baru di tbl_kelas_santri dengan IdTpq baru
             *   - Record baru di tbl_nilai dengan IdTpq baru
             *   - Semua data baru menggunakan IdTpq baru untuk konsistensi query
             */
            
            // 1. Update IdTpq dan set Active = 0 di tbl_santri_baru
            $this->DataSantriBaru->where('IdSantri', $IdSantri)
                ->set([
                    'IdTpq' => $IdTpqBaru,
                    'Active' => 0
                ])
                ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat memproses perubahan TPQ');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'TPQ santri berhasil diubah. Status Active diubah menjadi 0. Santri perlu diaktifkan di TPQ baru melalui menu Set Santri Baru.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteSantriBaru($IdSantri = null)
    {
        try {
            // Validasi IdSantri
            if ($IdSantri === null) {
                throw new \Exception('ID Santri tidak boleh kosong');
            }

            // Ambil data attachment
            $attachment = $this->DataSantriBaru->GetDataAttachment($IdSantri);

            // Tentukan path berdasarkan environment
            if (ENVIRONMENT === 'production') {
                $uploadPath = '/home/u1525344/public_html/tpqsmart/uploads/santri/';
                $thumbnailPath = $uploadPath . 'thumbnails/';
            } else {
                $uploadPath = ROOTPATH . 'public/uploads/santri/';
                $thumbnailPath = $uploadPath . 'thumbnails/';
            }

            // Hapus file-file terkait
            $files = [
                $attachment['FileKkSantri'],
                $attachment['FileKkAyah'],
                $attachment['FileKkIbu'],
                $attachment['PhotoProfil'],
                $attachment['FileKIP'],
                $attachment['FilePKH'],
                $attachment['FileKKS']
            ];

            foreach ($files as $file) {
                if (!empty($file)) {
                    // Hapus file utama
                    $filePath = $uploadPath . $file;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    // Hapus thumbnail jika ada
                    $thumbnailFile = $thumbnailPath . 'thumb_' . $file;
                    if (file_exists($thumbnailFile)) {
                        unlink($thumbnailFile);
                    }
                }
            }

            // Hapus data dari database menggunakan where clause
            $result = $this->DataSantriBaru->where('IdSantri', $IdSantri)->delete();

            if (!$result) {
                throw new \Exception('Gagal menghapus data santri');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data santri berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: deleteSantriBaru - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function showSantriBaruPerKelasTpq($IdTpq = null)
    {
        // Cek user group
        $isAdmin = in_groups('Admin');
        $isPanitiaUmum = in_groups('Panitia Umum');
        
        // ambil id tpq dari session atau parameter
        if ($IdTpq == null) {
            $IdTpq = session()->get('IdTpq');
        }
        
        // Untuk admin dan panitia umum, jika tidak ada IdTpq, tampilkan halaman pilih TPQ
        if (($isAdmin || $isPanitiaUmum) && (empty($IdTpq) || $IdTpq == 0)) {
            // Ambil semua data TPQ untuk dipilih
            $dataTpq = $this->helpFunction->getDataTpq(0);

            // Ambil jumlah santri per TPQ menggunakan method dari model
            $santriPerTpq = $this->DataSantriBaru->getJumlahSantriPerTpq();
            
            // Gabungkan data jumlah santri ke array TPQ
            foreach ($dataTpq as &$t) {
                $jumlahSantri = 0;
                foreach ($santriPerTpq as $count) {
                    if ($count['IdTpq'] == $t['IdTpq']) {
                        $jumlahSantri = $count['JumlahSantri'];
                        break;
                    }
                }
                $t['JumlahSantri'] = $jumlahSantri;
            }
            
            // Tampilkan halaman pilih TPQ
            $data = [
                'page_title' => 'Pilih TPQ - Daftar Santri Per Kelas',
                'dataTpq' => $dataTpq,
                'isAdmin' => $isAdmin,
                'isPanitiaUmum' => $isPanitiaUmum
            ];
            
            return view('backend/santri/selectTpqForSantriPerKelas', $data);
        }
        
        // Validasi IdTpq untuk non-admin
        if (empty($IdTpq) || $IdTpq == 0) {
            return redirect()->back()->with('error', 'TPQ tidak ditemukan. Silakan pilih TPQ terlebih dahulu.');
        }
        
        $santriAll = $this->DataSantriBaru->GetDataPerKelasTpq($IdTpq);
        $namaTpq = $this->helpFunction->getNamaTpqById($IdTpq);
        
        // Validasi jika namaTpq null
        if (empty($namaTpq) || !is_array($namaTpq)) {
            return redirect()->back()->with('error', 'Data TPQ tidak ditemukan.');
        }

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        if (!empty($santriAll) && !empty($IdTpq)) {
            foreach ($santriAll as $key => $santriItem) {
                if (isset($santriItem['NamaKelas']) && !empty($santriItem['NamaKelas'])) {
                    $namaKelasOriginal = $santriItem['NamaKelas'];
                    $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                    $santriAll[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );
                }
            }
        }

        // Mengelompokkan santri berdasarkan kelas secara dinamis
        $santriPerKelas = [];
        $kelasList = []; // Untuk menyimpan informasi kelas yang ada

        if (!empty($santriAll)) {
            // Ambil semua IdKelas unik dari data santri
            $uniqueKelas = [];
            foreach ($santriAll as $santri) {
                $idKelas = $santri['IdKelas'];
                if (!isset($uniqueKelas[$idKelas])) {
                    $uniqueKelas[$idKelas] = [
                        'IdKelas' => $idKelas,
                        'NamaKelas' => $santri['NamaKelas'] // Sudah dikonversi MDA
                    ];
                }
            }

            // Sort berdasarkan IdKelas
            ksort($uniqueKelas);

            // Buat array kelas list untuk view
            $kelasList = array_values($uniqueKelas);

            // Kelompokkan santri berdasarkan IdKelas
            foreach ($uniqueKelas as $idKelas => $kelasInfo) {
                $santriPerKelas[$idKelas] = array_filter($santriAll, function ($s) use ($idKelas) {
                    return $s['IdKelas'] == $idKelas;
                });
            }
        }

        $data = [
            'page_title' => 'Data Santri Baru Per Kelas TPQ',
            'dataSantriAll' => $santriAll,
            'santriPerKelas' => $santriPerKelas, // Array dinamis berdasarkan IdKelas
            'kelasList' => $kelasList, // List kelas yang ada untuk membuat tab
            'namaTpq' => $namaTpq ?? ['NamaTpq' => 'TPQ', 'Alamat' => ''],
            'IdTpq' => $IdTpq,
        ];

        return view('backend/santri/listSantriBaruPerKelasTpq', $data);
    }

    public function showKontakSantri($IdSantri = null)
    {
        // Ambil filter dari request atau session
        $filterIdTpq = $this->request->getGet('filterIdTpq');
        $filterIdKelas = $this->request->getGet('filterIdKelas');

        $sessionIdTpq = session()->get('IdTpq');
        $sessionIdKelas = session()->get('IdKelas');
        $sessionIdTahunAjaran = session()->get('IdTahunAjaran');
        $isOperator = in_groups('Operator');
        $isAdmin = in_groups('Admin');
        $isGuru = in_groups('Guru');
        $isKepalaTpq = in_groups('Kepala TPQ');
        $isSantri = in_groups('Santri');

        // Tentukan IdTpq yang akan digunakan
        $IdTpq = null;
        if ($isAdmin) {
            $IdTpq = $filterIdTpq ?: $sessionIdTpq;
        } else {
            $IdTpq = $sessionIdTpq;
        }

        // Tentukan IdKelas yang akan digunakan
        $IdKelas = null;
        $idKelasArray = null;

        // Jika user adalah Santri, ambil kelas dari santri yang login
        if ($isSantri) {
            $userNik = user()->nik ?? null;
            if (!empty($userNik)) {
                $santriData = $this->DataSantriBaru->getSantriByNik($userNik);
                if (!empty($santriData)) {
                    $IdTpq = $santriData['IdTpq'];

                    // Ambil kelas dari tbl_kelas_santri untuk tahun ajaran saat ini
                    if (!empty($sessionIdTahunAjaran)) {
                        $db = db_connect();
                        $kelasSantri = $db->table('tbl_kelas_santri ks')
                            ->select('ks.IdKelas, k.NamaKelas')
                            ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'inner')
                            ->where('ks.IdSantri', $santriData['IdSantri'])
                            ->where('ks.IdTahunAjaran', $sessionIdTahunAjaran)
                            ->where('ks.Status', 1)
                            ->orderBy('k.NamaKelas', 'ASC')
                            ->get()
                            ->getRowArray();

                        if (!empty($kelasSantri)) {
                            $IdKelas = $kelasSantri['IdKelas'];
                        }
                    }

                    // Fallback ke IdKelas dari tbl_santri_baru jika tidak ada di tbl_kelas_santri
                    if (empty($IdKelas) && !empty($santriData['IdKelas'])) {
                        $IdKelas = $santriData['IdKelas'];
                    }
                }
            }
        } elseif ($isOperator && $IdTpq !== null) {
            // Jika Operator login, ambil semua kelas dari TPQ yang memiliki santri aktif
            $idKelasArray = $this->kelasModel->getAllKelasAktifByTpq($IdTpq);

            // Jika ada filter kelas dari request, gunakan filter tersebut
            if ($filterIdKelas && $filterIdKelas !== '') {
                if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                    $filterArray = array_filter(explode(',', $filterIdKelas));
                    $IdKelas = array_intersect($filterArray, $idKelasArray);
                } else {
                    if (in_array($filterIdKelas, $idKelasArray)) {
                        $IdKelas = $filterIdKelas;
                    }
                }
            }
        } else {
            // Untuk role lain, gunakan filter dari request atau session
            if ($filterIdKelas && $filterIdKelas !== '') {
                if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                    $IdKelas = array_filter(explode(',', $filterIdKelas));
                } else {
                    $IdKelas = $filterIdKelas;
                }
            } else {
                $IdKelas = $sessionIdKelas;
            }
        }

        // Ambil data santri menggunakan GetDataSantriPerKelas untuk mendapatkan data lengkap dengan kontak
        $santri = [];
        if (!empty($IdTpq) && !empty($sessionIdTahunAjaran)) {
            if ($isSantri && !empty($IdKelas)) {
                // Untuk Santri, ambil hanya santri di kelas yang sama
                $santri = $this->DataSantri->GetDataSantriPerKelas($IdTpq, $sessionIdTahunAjaran, $IdKelas, null);
            } elseif ($isOperator && $idKelasArray !== null && empty($IdKelas)) {
                // Untuk Operator tanpa filter kelas, ambil semua kelas
                $santri = $this->DataSantri->GetDataSantriPerKelas($IdTpq, $sessionIdTahunAjaran, 0, null);
            } elseif (!empty($IdKelas)) {
                // Untuk role lain dengan filter kelas
                $santri = $this->DataSantri->GetDataSantriPerKelas($IdTpq, $sessionIdTahunAjaran, $IdKelas, null);
            }
        }

        // Konversi object ke array dan tambahkan data kontak dari tbl_santri_baru
        $santriData = [];
        if (!empty($santri)) {
            $santriIds = [];
            foreach ($santri as $s) {
                $santriIds[] = is_object($s) ? $s->IdSantri : $s['IdSantri'];
            }

            // Ambil data kontak lengkap dari tbl_santri_baru
            if (!empty($santriIds)) {
                $santriDetail = $this->DataSantriBaru->whereIn('IdSantri', $santriIds)
                    ->where('Active', 1)
                    ->findAll();

                // Buat mapping IdSantri => data detail
                $santriDetailMap = [];
                foreach ($santriDetail as $detail) {
                    $santriDetailMap[$detail['IdSantri']] = $detail;
                }

                // Gabungkan data
                foreach ($santri as $s) {
                    $idSantri = is_object($s) ? $s->IdSantri : $s['IdSantri'];
                    if (isset($santriDetailMap[$idSantri])) {
                        $detail = $santriDetailMap[$idSantri];
                        // Buat alamat lengkap
                        $alamatLengkap = '';
                        $alamatParts = [];
                        if (!empty($detail['AlamatSantri'])) {
                            $alamatParts[] = $detail['AlamatSantri'];
                        }
                        if (!empty($detail['RtSantri']) || !empty($detail['RwSantri'])) {
                            $rtRw = 'RT ' . ($detail['RtSantri'] ?? '') . ' / RW ' . ($detail['RwSantri'] ?? '');
                            $alamatParts[] = $rtRw;
                        }
                        if (!empty($detail['KelurahanDesaSantri'])) {
                            $alamatParts[] = $detail['KelurahanDesaSantri'];
                        }
                        if (!empty($detail['KecamatanSantri'])) {
                            $alamatParts[] = $detail['KecamatanSantri'];
                        }
                        if (!empty($detail['KabupatenKotaSantri'])) {
                            $alamatParts[] = $detail['KabupatenKotaSantri'];
                        }
                        if (!empty($detail['ProvinsiSantri'])) {
                            $alamatParts[] = $detail['ProvinsiSantri'];
                        }
                        if (!empty($detail['KodePosSantri'])) {
                            $alamatParts[] = $detail['KodePosSantri'];
                        }
                        $alamatLengkap = implode(', ', array_filter($alamatParts));

                        $santriData[] = [
                            'IdSantri' => $idSantri,
                            'IdKelas' => is_object($s) ? ($s->IdKelas ?? null) : ($s['IdKelas'] ?? $detail['IdKelas'] ?? null),
                            'NamaSantri' => is_object($s) ? $s->NamaSantri : ($s['NamaSantri'] ?? $detail['NamaSantri']),
                            'NamaKelas' => is_object($s) ? $s->NamaKelas : ($s['NamaKelas'] ?? ''),
                            'PhotoProfil' => is_object($s) ? ($s->PhotoProfil ?? null) : ($s['PhotoProfil'] ?? null),
                            'JenisKelamin' => is_object($s) ? $s->JenisKelamin : ($s['JenisKelamin'] ?? $detail['JenisKelamin']),
                            'NamaAyah' => $detail['NamaAyah'] ?? '',
                            'NamaIbu' => $detail['NamaIbu'] ?? '',
                            'NoHpAyah' => $detail['NoHpAyah'] ?? '',
                            'NoHpIbu' => $detail['NoHpIbu'] ?? '',
                            'NoHpSantri' => $detail['NoHpSantri'] ?? '',
                            'Alamat' => $alamatLengkap,
                            'AlamatSantri' => $detail['AlamatSantri'] ?? '',
                            'RtSantri' => $detail['RtSantri'] ?? '',
                            'RwSantri' => $detail['RwSantri'] ?? '',
                            'KelurahanDesaSantri' => $detail['KelurahanDesaSantri'] ?? '',
                            'KecamatanSantri' => $detail['KecamatanSantri'] ?? '',
                            'KabupatenKotaSantri' => $detail['KabupatenKotaSantri'] ?? '',
                            'ProvinsiSantri' => $detail['ProvinsiSantri'] ?? '',
                            'KodePosSantri' => $detail['KodePosSantri'] ?? '',
                            'TitikKoordinatSantri' => $detail['TitikKoordinatSantri'] ?? '',
                            'NamaTpq' => is_object($s) ? ($s->NamaTpq ?? '') : ($s['NamaTpq'] ?? ''),
                        ];
                    }
                }
            }
        }

        // Get data TPQ untuk filter dropdown (jika bukan Santri)
        $dataTpq = [];
        $dataKelas = [];
        if (!$isSantri) {
            $dataTpq = $this->helpFunction->getDataTpq();
            usort($dataTpq, function ($a, $b) {
                return strcasecmp($a['NamaTpq'] ?? '', $b['NamaTpq'] ?? '');
            });

            if ($isAdmin || $isOperator || $isKepalaTpq) {
                $dataKelas = $this->helpFunction->getDataKelas();
            } elseif ($isGuru) {
                if ($sessionIdKelas && is_array($sessionIdKelas)) {
                    $allKelas = $this->helpFunction->getDataKelas();
                    $dataKelas = array_filter($allKelas, function ($kelas) use ($sessionIdKelas) {
                        return in_array($kelas['IdKelas'], $sessionIdKelas);
                    });
                    $dataKelas = array_values($dataKelas);
                }
            }
        }

        $data = [
            'page_title' => 'Kontak Santri',
            'santri' => $santriData,
            'dataTpq' => $dataTpq,
            'dataKelas' => $dataKelas,
            'currentIdTpq' => $IdTpq,
            'currentIdKelas' => $IdKelas,
            'isAdmin' => $isAdmin,
            'isOperator' => $isOperator,
            'isGuru' => $isGuru,
            'isKepalaTpq' => $isKepalaTpq,
            'isSantri' => $isSantri,
        ];
        return view('backend/santri/kontakSantri', $data);
    }

    /**
     * Proses foto santri dari base64 ke format yang sesuai
     * @param string|null $base64Image
     * @return string|null
     */
    private function processFotoSantri(?string $base64Image): ?string
    {
        log_message('info', 'Santri: processFotoSantri - Header');
        try {
            if (empty($base64Image)) {
                throw new \Exception('Foto santri tidak boleh kosong');
            }

            // Validasi format base64
            if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                throw new \Exception('Format gambar tidak valid - harus base64');
            }
            log_message('info', 'Santri: processFotoSantri - Format base64 valid');

            // Extract data gambar
            log_message('info', 'Santri: processFotoSantri - Extract data gambar');
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageData = base64_decode($base64Image);

            if (!$imageData) {
                throw new \Exception('Gagal decode base64 image');
            }
            log_message('info', 'Santri: processFotoSantri - Base64 berhasil di-decode');

            // Konversi ke image
            log_message('info', 'Santri: processFotoSantri - Konversi ke image');
            $srcImage = imagecreatefromstring($imageData);
            if (!$srcImage) {
                throw new \Exception('Gagal membuat image dari string data');
            }
            log_message('info', 'Santri: processFotoSantri - Image berhasil dibuat dari string');

            // Buat file JPEG temporary
            log_message('info', 'Santri: processFotoSantri - Buat file JPEG temporary');
            $jpegFile = tempnam(sys_get_temp_dir(), 'jpg');
            if (!$jpegFile) {
                throw new \Exception('Gagal membuat file temporary');
            }
            log_message('info', 'Santri: processFotoSantri - File temporary berhasil dibuat');

            // Simpan sebagai JPEG
            log_message('info', 'Santri: processFotoSantri - Simpan sebagai JPEG');
            if (!imagejpeg($srcImage, $jpegFile, 90)) {
                throw new \Exception('Gagal menyimpan gambar ke JPEG');
            }
            log_message('info', 'Santri: processFotoSantri - Gambar berhasil disimpan sebagai JPEG');

            // Baca data JPEG
            log_message('info', 'Santri: processFotoSantri - Baca data JPEG');
            $jpegData = file_get_contents($jpegFile);
            if (!$jpegData) {
                throw new \Exception('Gagal membaca file JPEG');
            }
            log_message('info', 'Santri: processFotoSantri - File JPEG berhasil dibaca');

            // Cleanup
            log_message('info', 'Santri: processFotoSantri - Cleanup image dan file temporary');
            imagedestroy($srcImage);
            if (file_exists($jpegFile)) {
                unlink($jpegFile);
            }
            log_message('info', 'Santri: processFotoSantri - Cleanup berhasil dilakukan');
            log_message('info', 'Santri: processFotoSantri - Footer');
            return 'data:image/jpeg;base64,' . base64_encode($jpegData);
        } catch (\Exception $e) {
            log_message('error', 'Santri: processFotoSantri - Error saat memproses foto santri: ' . $e->getMessage());
            log_message('info', 'Santri: processFotoSantri - Footer');
            throw $e;
        }
    }

    // Tambahkan fungsi baru
    private function getDataSantri($IdSantri)
    {
        log_message('info', 'Santri: getDataSantri - Mengambil data santri dari database');

        $dataSantri = $this->DataSantriBaru->getDetailSantriById($IdSantri);

        if (!$dataSantri) {
            log_message('error', 'Santri: getDataSantri - Data santri tidak ditemukan');
            throw new \Exception('Data santri tidak ditemukan');
        }

        log_message('info', 'Santri: getDataSantri - Data santri berhasil diambil');
        return $dataSantri;
    }

    public function generatePDFSantriBaru($IdSantri = null)
    {
        log_message('info', 'Santri: generatePDFSantriBaru - Header');
        log_message('info', 'Santri: generatePDFSantriBaru - Menggunakan fungsi generatePDFSantriBaru(variable: IdSantri)');
        log_message('info', 'Santri: generatePDFSantriBaru - Memulai generate PDF Santri Baru');
        try {
            if (!$IdSantri) {
                log_message('error', 'Santri: generatePDFSantriBaru - ID Santri tidak ditemukan');
                throw new \Exception('ID Santri tidak ditemukan');
            }
            log_message('info', 'Santri: generatePDFSantriBaru - ID Santri valid: ' . $IdSantri);

            // Gunakan fungsi getDataSantri
            $dataSantri = $this->getDataSantri($IdSantri);
            // Ambil data TPQ untuk mendapatkan Kepala TPQ
            $tpqRow = $this->helpFunction->getNamaTpqById($dataSantri['IdTpq']);

            // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
            $namaKelasOriginal = $dataSantri['NamaKelas'];
            $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($dataSantri['IdTpq'], $namaKelasOriginal);
            $printNamaKelas = $this->helpFunction->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );

            // Siapkan data untuk template
            $data = [
                //nama tpq dan nama kelas
                'printNamaTpq' => $dataSantri['NamaTpq'],  // Menggunakan NamaTpq dari hasil join
                'printNamaKelas' => $printNamaKelas,  // Menggunakan NamaKelas yang sudah dikonversi
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
            log_message('info', 'Santri: generatePDFSantriBaru - Memproses foto santri');
            if (!empty($dataSantri['PhotoProfil'])) {
                // Tentukan path berdasarkan environment
                if (ENVIRONMENT === 'production') {
                    log_message('info', 'Santri: generatePDFSantriBaru - Environment: production');

                    $uploadPath = '/home/u1525344/public_html/tpqsmart/uploads/santri/';
                    $fotoPath = $uploadPath . $dataSantri['PhotoProfil'];
                    log_message('info', 'Santri: generatePDFSantriBaru - Path foto: ' . $fotoPath);
                    $fotoData = file_exists($fotoPath) ? file_get_contents($fotoPath) : null;
                } else {
                    log_message('info', 'Santri: generatePDFSantriBaru - Environment: development');
                    $uploadPath = ROOTPATH . 'public/uploads/santri/';
                    $fotoPath = $uploadPath . $dataSantri['PhotoProfil'];
                    log_message('info', 'Santri: generatePDFSantriBaru - Path foto: ' . $fotoPath);
                    $fotoData = file_exists($fotoPath) ? file_get_contents($fotoPath) : null;
                }

                if ($fotoData) {
                    $data['printFotoSantri'] = 'data:image/jpeg;base64,' . base64_encode($fotoData);
                    log_message('info', 'Santri: generatePDFSantriBaru - Foto berhasil diproses');
                } else {
                    log_message('error', 'Santri: generatePDFSantriBaru - Foto tidak ditemukan di path: ' . $fotoPath);
                }
            }

            try {
                log_message('info', 'Santri: generatePDFSantriBaru - Initial proses foto santri untuk memastikan format gambar valid');
                $fotoSantri = $this->processFotoSantri($data['printFotoSantri'] ?? null);
                log_message('info', 'Santri: generatePDFSantriBaru - Foto santri berhasil diproses');
            } catch (\Exception $e) {
                log_message('error', 'Santri: generatePDFSantriBaru - Foto tidak tersedia: ' . $e->getMessage());
                $fotoSantri = null;
                log_message('info', 'Santri: generatePDFSantriBaru - Foto santri tidak tersedia, dan akan dikosongkan');
            }
            // Konfigurasi DOMPDF
            log_message('info', 'Santri: generatePDFSantriBaru - Mengkonfigurasi DOMPDF');
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('isPhpEnabled', true);
            $dompdf = new Dompdf($options);
            log_message('info', 'Santri: generatePDFSantriBaru - Konfigurasi DOMPDF berhasil');

            // Generate PDF
            log_message('info', 'Santri: generatePDFSantriBaru - Memulai generate HTML');
            $html = view('backend/santri/pdf_template', [
                'data' => $data,
                'fotoSantri' => $fotoSantri
            ]);

            // log generate Load HTML ke DOMPDF 
            log_message('info', 'Santri: generatePDFSantriBaru - Memulai generate PDF');
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            log_message('info', 'Santri: generatePDFSantriBaru - PDF berhasil di-generate');

            // Output PDF dengan header yang benar
            $filename = 'Data_Santri_' . str_replace(' ', '_', $dataSantri['NamaSantri']) . '.pdf';
            // log footer 
            log_message('info', 'Santri: generatePDFSantriBaru - Footer');
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($dompdf->output());

        } catch (\Exception $e) {
            log_message('error', 'Santri: generatePDFSantriBaru - Error: ' . $e->getMessage());

            // log footer ================================
            log_message('info', 'Santri: generatePDFSantriBaru - Footer');
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    public function generatePDFprofilSantriRaport($IdSantri = null)
    {
        log_message('info', 'Santri: generatePDFprofilSantriRaport - Header');
        try {
            if (!$IdSantri) {
                throw new \Exception('ID Santri tidak ditemukan');
            }

            $dataSantri = $this->getDataSantri($IdSantri);
            $tpqRow = $this->helpFunction->getNamaTpqById($dataSantri['IdTpq']);

            // Check status MDA dan mapping kelas menggunakan helper function
            $idTpq = $dataSantri['IdTpq'];
            $namaKelasSantri = $dataSantri['NamaKelas'];

            // Gunakan helper function untuk check MDA mapping
            $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($idTpq, $namaKelasSantri);
            $useMdaData = $mdaCheckResult['useMdaData'];
            $mappedMdaKelas = $mdaCheckResult['mappedMdaKelas'];
            $mdaRow = null;

            // Jika sesuai, ambil data MDA
            if ($useMdaData) {
                $mdaData = $this->mdaModel->GetData($idTpq);
                if (!empty($mdaData) && !empty($mdaData[0])) {
                    $mdaRow = $mdaData[0];
                    log_message('info', 'Santri: generatePDFprofilSantriRaport - Menggunakan data MDA untuk kelas ' . $namaKelasSantri);
                } else {
                    // Jika data MDA tidak ditemukan, fallback ke TPQ
                    $useMdaData = false;
                    $mappedMdaKelas = null; // Reset mapped kelas karena tidak ada data MDA
                    log_message('warning', 'Santri: generatePDFprofilSantriRaport - Data MDA tidak ditemukan, menggunakan data TPQ');
                }
            }

            // Tentukan data yang akan digunakan (MDA atau TPQ)
            $lembagaType = $useMdaData && $mdaRow ? 'MDA' : 'TPQ';

            // Ambil data TPQ lengkap untuk fallback (field yang tidak ada di MDA)
            $tpqFullData = $this->tpqModel->GetData($idTpq);

            // Untuk field yang tidak ada di MDA, gunakan data TPQ sebagai fallback
            $kopLembaga = $useMdaData && $mdaRow ? ($mdaRow['KopLembaga'] ?? $tpqRow['KopLembaga'] ?? '') : ($tpqRow['KopLembaga'] ?? '');
            $kepalaSekolah = $useMdaData && $mdaRow ? ($mdaRow['KepalaSekolah'] ?? $tpqRow['KepalaSekolah'] ?? '') : ($tpqRow['KepalaSekolah'] ?? '');
            $alamatLembaga = $useMdaData && $mdaRow ? ($mdaRow['Alamat'] ?? $tpqRow['Alamat'] ?? '') : ($tpqRow['Alamat'] ?? '');
            $namaLembaga = $useMdaData && $mdaRow ? ($mdaRow['NamaTpq'] ?? $dataSantri['NamaTpq']) : $dataSantri['NamaTpq'];

            // Tentukan nama kelas yang akan ditampilkan menggunakan helper function
            // Jika match dengan mapping MDA, ganti dengan kelas MDA
            $printNamaKelas = $this->helpFunction->convertKelasToMda($namaKelasSantri, $mappedMdaKelas);

            $data = [
                'printNamaTpq' => $namaLembaga,
                'printNamaKelas' => $printNamaKelas,
                'printNamaSantri' => $dataSantri['NamaSantri'],
                'printNikSantri' => $dataSantri['NikSantri'],
                'printTempatTTL' => $dataSantri['TempatLahirSantri'] . ', ' . formatTanggalIndonesia($dataSantri['TanggalLahirSantri'], 'd F Y'),
                'printJenisKelamin' => $dataSantri['JenisKelamin'],
                'printAlamatSantri' => $dataSantri['AlamatSantri'],
                'printRtSantri' => $dataSantri['RtSantri'] ?? '',
                'printRwSantri' => $dataSantri['RwSantri'] ?? '',
                'printKelurahanDesaSantri' => $dataSantri['KelurahanDesaSantri'] ?? '',
                'printKecamatanSantri' => $dataSantri['KecamatanSantri'] ?? '',
                'printKabupatenKotaSantri' => $dataSantri['KabupatenKotaSantri'] ?? '',
                'printProvinsiSantri' => $dataSantri['ProvinsiSantri'] ?? '',
                'printNamaAyah' => $dataSantri['NamaAyah'],
                'printNamaIbu' => $dataSantri['NamaIbu'],
                'printTelp' => $dataSantri['NoHpSantri'] ?: ($dataSantri['NoHpAyah'] ?: $dataSantri['NoHpIbu']),
                'printPekerjaanAyah' => $dataSantri['PekerjaanUtamaAyah'],
                'printPekerjaanIbu' => $dataSantri['PekerjaanUtamaIbu'],
                'printTanggalDiterima' => formatTanggalIndonesia($dataSantri['created_at'], 'd F Y'),
                'printFotoSantri' => null,
                // Gunakan data dari MDA atau TPQ sesuai kondisi
                'printKepalaTpq' => $kepalaSekolah,
                'printKopLembaga' => $kopLembaga,
                'printAlamatTpq' => $alamatLembaga,
                // Field alamat lengkap tetap menggunakan data TPQ (karena MDA tidak punya field ini)
                'printKelurahanDesaTpq' => $tpqRow['KelurahanDesa'] ?? '',
                'printKecamatanTpq' => $tpqRow['Kecamatan'] ?? 'Seri Kuala Lobam',
                'printKabupatenKotaTpq' => $tpqRow['KabupatenKota'] ?? 'Bintan',
                'printProvinsiTpq' => $tpqRow['Provinsi'] ?? 'Kepulauan Riau',
                'printKodePosTpq' => $tpqRow['KodePos'] ?? '29152',
                'printTelpTpq' => $useMdaData && $mdaRow ? ($mdaRow['NoHp'] ?? $tpqRow['NoHp'] ?? '081234567890') : ($tpqRow['NoHp'] ?? '081234567890'),
                'printEmailTpq' => $tpqRow['Email'] ?? $namaLembaga . '@TpqSmart.simpedis.com',
                'printLembagaType' => $lembagaType, // Untuk label di view
            ];

            // Ambil data signature untuk kepala sekolah dari database
            // Untuk profil santri, kita ambil signature dengan JenisDokumen 'ProfilSantri'
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $signatures = [];

            if ($IdTahunAjaran) {
                // Query khusus untuk profil santri
                $builder = $this->signatureModel->db->table('tbl_tanda_tangan s');
                $builder->select('s.*, j.NamaJabatan, g.Nama as NamaGuru, NULL as IdKelas');
                $builder->join('tbl_struktur_lembaga sl', 'sl.IdGuru = s.IdGuru AND sl.IdTpq = s.IdTpq');
                $builder->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan');
                $builder->join('tbl_guru g', 'g.IdGuru = s.IdGuru');
                $builder->where('j.NamaJabatan', 'Kepala TPQ');
                $builder->where('s.IdSantri', $dataSantri['IdSantri']);
                $builder->where('s.IdTpq', $idTpq);
                $builder->where('s.IdTahunAjaran', $IdTahunAjaran);
                $builder->where('s.JenisDokumen', 'ProfilSantri');
                $builder->where('s.StatusValidasi', 'Valid');
                $builder->orderBy('s.TanggalTtd', 'DESC');

                $signatures = $builder->get()->getResultArray();
            }

            // Tambahkan signatures ke data
            $data['signatures'] = $signatures;

            if (!empty($dataSantri['PhotoProfil'])) {
                if (ENVIRONMENT === 'production') {
                    $uploadPath = '/home/u1525344/public_html/tpqsmart/uploads/santri/';
                } else {
                    $uploadPath = ROOTPATH . 'public/uploads/santri/';
                }
                $fotoPath = $uploadPath . $dataSantri['PhotoProfil'];
                $fotoData = file_exists($fotoPath) ? file_get_contents($fotoPath) : null;
                if ($fotoData) {
                    $data['printFotoSantri'] = 'data:image/jpeg;base64,' . base64_encode($fotoData);
                }
            }

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('isPhpEnabled', true);
            $dompdf = new Dompdf($options);

            $html = view('backend/santri/pdftemplateprofileraport', [
                'data' => $data,
            ]);

            // Gunakan ukuran kertas Folio (F4) portrait
            $dompdf->loadHtml($html);
            $dompdf->setPaper('folio', 'portrait');
            $dompdf->render();

            $filename = 'Profil_Santri_' . str_replace(' ', '_', $dataSantri['NamaSantri']) . '.pdf';
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($dompdf->output());
        } catch (\Exception $e) {
            log_message('error', 'Santri: generatePDFprofilSantriRaport - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // Print All Profil Santri berdasarkan filter
    public function generatePDFAllProfilSantri()
    {
        log_message('info', 'Santri: generatePDFAllProfilSantri - Header');
        try {
            // Ambil filter dari request
            $filterIdTpq = $this->request->getGet('filterIdTpq');
            $filterIdKelas = $this->request->getGet('filterIdKelas');

            $sessionIdTpq = session()->get('IdTpq');
            $sessionIdKelas = session()->get('IdKelas');
            $isOperator = in_groups('Operator');
            $isAdmin = in_groups('Admin');

            // Tentukan IdTpq yang akan digunakan
            $IdTpq = null;
            if ($isAdmin) {
                $IdTpq = $filterIdTpq ?: $sessionIdTpq;
            } else {
                $IdTpq = $sessionIdTpq;
            }

            // Tentukan IdKelas yang akan digunakan
            $IdKelas = null;
            $idKelasArray = null;

            if ($isOperator && $IdTpq !== null) {
                $idKelasArray = $this->kelasModel->getAllKelasAktifByTpq($IdTpq);

                if ($filterIdKelas && $filterIdKelas !== '') {
                    // Handle single value atau string dengan koma
                    if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                        $filterArray = array_filter(explode(',', $filterIdKelas));
                        $IdKelas = array_intersect($filterArray, $idKelasArray);
                    } else {
                        if (in_array($filterIdKelas, $idKelasArray)) {
                            $IdKelas = $filterIdKelas;
                        }
                    }
                }
            } else {
                if ($filterIdKelas && $filterIdKelas !== '') {
                    // Handle single value atau string dengan koma
                    if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                        $IdKelas = array_filter(explode(',', $filterIdKelas));
                    } else {
                        $IdKelas = $filterIdKelas;
                    }
                } else {
                    $IdKelas = $sessionIdKelas;
                }
            }

            if ($idKelasArray !== null && $IdKelas === null) {
                // Gunakan semua kelas dari TPQ
            } else if ($IdKelas !== null) {
                $idKelasArray = null;
            }

            // Ambil data profil santri menggunakan method dari model
            $santriList = $this->DataSantriBaru->getProfilSantri($IdTpq, $IdKelas, $idKelasArray);

            if (empty($santriList)) {
                return redirect()->back()->with('error', 'Tidak ada data santri untuk dicetak');
            }

            // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
            if (!empty($santriList) && !empty($IdTpq)) {
                foreach ($santriList as $key => $santriItem) {
                    if (isset($santriItem['NamaKelas']) && !empty($santriItem['NamaKelas'])) {
                        $namaKelasOriginal = $santriItem['NamaKelas'];
                        $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                        $santriList[$key]['NamaKelas'] = $this->helpFunction->convertKelasToMda(
                            $namaKelasOriginal,
                            $mdaCheckResult['mappedMdaKelas']
                        );
                    }
                }
            }

            // Generate PDF untuk semua santri
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('isPhpEnabled', true);
            $dompdf = new Dompdf($options);

            // Ambil template pertama untuk mendapatkan struktur HTML dan CSS
            $firstSantri = $santriList[0];
            $firstDataSantri = $this->getDataSantri($firstSantri['IdSantri']);
            $firstTpqRow = $this->helpFunction->getNamaTpqById($firstDataSantri['IdTpq']);

            $idTpq = $firstDataSantri['IdTpq'];
            $namaKelasSantri = $firstDataSantri['NamaKelas'];
            $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($idTpq, $namaKelasSantri);
            $useMdaData = $mdaCheckResult['useMdaData'];
            $mappedMdaKelas = $mdaCheckResult['mappedMdaKelas'];
            $mdaRow = null;

            if ($useMdaData) {
                $mdaData = $this->mdaModel->GetData($idTpq);
                if (!empty($mdaData) && !empty($mdaData[0])) {
                    $mdaRow = $mdaData[0];
                } else {
                    $useMdaData = false;
                    $mappedMdaKelas = null;
                }
            }

            $lembagaType = $useMdaData && $mdaRow ? 'MDA' : 'TPQ';
            $kopLembaga = $useMdaData && $mdaRow ? ($mdaRow['KopLembaga'] ?? $firstTpqRow['KopLembaga'] ?? '') : ($firstTpqRow['KopLembaga'] ?? '');
            $kepalaSekolah = $useMdaData && $mdaRow ? ($mdaRow['KepalaSekolah'] ?? $firstTpqRow['KepalaSekolah'] ?? '') : ($firstTpqRow['KepalaSekolah'] ?? '');
            $alamatLembaga = $useMdaData && $mdaRow ? ($mdaRow['Alamat'] ?? $firstTpqRow['Alamat'] ?? '') : ($firstTpqRow['Alamat'] ?? '');
            $namaLembaga = $useMdaData && $mdaRow ? ($mdaRow['NamaTpq'] ?? $firstDataSantri['NamaTpq']) : $firstDataSantri['NamaTpq'];
            $printNamaKelas = $this->helpFunction->convertKelasToMda($namaKelasSantri, $mappedMdaKelas);

            // Ambil data signature untuk kepala sekolah dari database (santri pertama)
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $firstSignatures = [];
            if ($IdTahunAjaran) {
                $builder = $this->signatureModel->db->table('tbl_tanda_tangan s');
                $builder->select('s.*, j.NamaJabatan, g.Nama as NamaGuru, NULL as IdKelas');
                $builder->join('tbl_struktur_lembaga sl', 'sl.IdGuru = s.IdGuru AND sl.IdTpq = s.IdTpq');
                $builder->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan');
                $builder->join('tbl_guru g', 'g.IdGuru = s.IdGuru');
                $builder->where('j.NamaJabatan', 'Kepala TPQ');
                $builder->where('s.IdSantri', $firstDataSantri['IdSantri']);
                $builder->where('s.IdTpq', $idTpq);
                $builder->where('s.IdTahunAjaran', $IdTahunAjaran);
                $builder->where('s.JenisDokumen', 'ProfilSantri');
                $builder->where('s.StatusValidasi', 'Valid');
                $builder->orderBy('s.TanggalTtd', 'DESC');
                $firstSignatures = $builder->get()->getResultArray();
            }

            // Render template pertama untuk mendapatkan struktur HTML
            $firstData = [
                'printNamaTpq' => $namaLembaga,
                'printNamaKelas' => $printNamaKelas,
                'printNamaSantri' => $firstDataSantri['NamaSantri'],
                'printNikSantri' => $firstDataSantri['NikSantri'],
                'printTempatTTL' => $firstDataSantri['TempatLahirSantri'] . ', ' . formatTanggalIndonesia($firstDataSantri['TanggalLahirSantri'], 'd F Y'),
                'printJenisKelamin' => $firstDataSantri['JenisKelamin'],
                'printAlamatSantri' => $firstDataSantri['AlamatSantri'],
                'printRtSantri' => $firstDataSantri['RtSantri'] ?? '',
                'printRwSantri' => $firstDataSantri['RwSantri'] ?? '',
                'printKelurahanDesaSantri' => $firstDataSantri['KelurahanDesaSantri'] ?? '',
                'printKecamatanSantri' => $firstDataSantri['KecamatanSantri'] ?? '',
                'printKabupatenKotaSantri' => $firstDataSantri['KabupatenKotaSantri'] ?? '',
                'printProvinsiSantri' => $firstDataSantri['ProvinsiSantri'] ?? '',
                'printNamaAyah' => $firstDataSantri['NamaAyah'],
                'printNamaIbu' => $firstDataSantri['NamaIbu'],
                'printTelp' => $firstDataSantri['NoHpSantri'] ?: ($firstDataSantri['NoHpAyah'] ?: $firstDataSantri['NoHpIbu']),
                'printPekerjaanAyah' => $firstDataSantri['PekerjaanUtamaAyah'],
                'printPekerjaanIbu' => $firstDataSantri['PekerjaanUtamaIbu'],
                'printTanggalDiterima' => formatTanggalIndonesia($firstDataSantri['created_at'], 'd F Y'),
                'printFotoSantri' => null,
                'printKepalaTpq' => $kepalaSekolah,
                'printKopLembaga' => $kopLembaga,
                'printAlamatTpq' => $alamatLembaga,
                'printKelurahanDesaTpq' => $firstTpqRow['KelurahanDesa'] ?? '',
                'printKecamatanTpq' => $firstTpqRow['Kecamatan'] ?? 'Seri Kuala Lobam',
                'printKabupatenKotaTpq' => $firstTpqRow['KabupatenKota'] ?? 'Bintan',
                'printProvinsiTpq' => $firstTpqRow['Provinsi'] ?? 'Kepulauan Riau',
                'printKodePosTpq' => $firstTpqRow['KodePos'] ?? '29152',
                'printTelpTpq' => $useMdaData && $mdaRow ? ($mdaRow['NoHp'] ?? $firstTpqRow['NoHp'] ?? '081234567890') : ($firstTpqRow['NoHp'] ?? '081234567890'),
                'printEmailTpq' => $firstTpqRow['Email'] ?? $namaLembaga . '@TpqSmart.simpedis.com',
                'printLembagaType' => $lembagaType,
                'signatures' => $firstSignatures, // Tambahkan signatures untuk santri pertama
            ];

            if (!empty($firstDataSantri['PhotoProfil'])) {
                if (ENVIRONMENT === 'production') {
                    $uploadPath = '/home/u1525344/public_html/tpqsmart/uploads/santri/';
                } else {
                    $uploadPath = ROOTPATH . 'public/uploads/santri/';
                }
                $fotoPath = $uploadPath . $firstDataSantri['PhotoProfil'];
                $fotoData = file_exists($fotoPath) ? file_get_contents($fotoPath) : null;
                if ($fotoData) {
                    $firstData['printFotoSantri'] = 'data:image/jpeg;base64,' . base64_encode($fotoData);
                }
            }

            $templateHtml = view('backend/santri/pdftemplateprofileraport', ['data' => $firstData]);

            // Ekstrak head dan body dari template pertama
            preg_match('/<head>(.*?)<\/head>/s', $templateHtml, $headMatches);
            preg_match('/<body>(.*?)<\/body>/s', $templateHtml, $bodyMatches);

            $headContent = $headMatches[1] ?? '';
            $firstBodyContent = $bodyMatches[1] ?? '';

            // Mulai HTML dengan head dari template pertama
            $htmlContent = '<html><head>' . $headContent . '</head><body>';

            // Tambahkan body content pertama dengan page break
            $htmlContent .= '<div style="page-break-after: always; page-break-inside: avoid; min-height: 100vh;">' . $firstBodyContent . '</div>';

            // Proses santri berikutnya
            $totalSantri = count($santriList);
            for ($i = 1; $i < $totalSantri; $i++) {
                $santri = $santriList[$i];
                $isLast = ($i === $totalSantri - 1);

                $dataSantri = $this->getDataSantri($santri['IdSantri']);
                $tpqRow = $this->helpFunction->getNamaTpqById($dataSantri['IdTpq']);

                $idTpq = $dataSantri['IdTpq'];
                $namaKelasSantri = $dataSantri['NamaKelas'];
                $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($idTpq, $namaKelasSantri);
                $useMdaData = $mdaCheckResult['useMdaData'];
                $mappedMdaKelas = $mdaCheckResult['mappedMdaKelas'];
                $mdaRow = null;

                if ($useMdaData) {
                    $mdaData = $this->mdaModel->GetData($idTpq);
                    if (!empty($mdaData) && !empty($mdaData[0])) {
                        $mdaRow = $mdaData[0];
                    } else {
                        $useMdaData = false;
                        $mappedMdaKelas = null;
                    }
                }

                $lembagaType = $useMdaData && $mdaRow ? 'MDA' : 'TPQ';
                $kopLembaga = $useMdaData && $mdaRow ? ($mdaRow['KopLembaga'] ?? $tpqRow['KopLembaga'] ?? '') : ($tpqRow['KopLembaga'] ?? '');
                $kepalaSekolah = $useMdaData && $mdaRow ? ($mdaRow['KepalaSekolah'] ?? $tpqRow['KepalaSekolah'] ?? '') : ($tpqRow['KepalaSekolah'] ?? '');
                $alamatLembaga = $useMdaData && $mdaRow ? ($mdaRow['Alamat'] ?? $tpqRow['Alamat'] ?? '') : ($tpqRow['Alamat'] ?? '');
                $namaLembaga = $useMdaData && $mdaRow ? ($mdaRow['NamaTpq'] ?? $dataSantri['NamaTpq']) : $dataSantri['NamaTpq'];
                $printNamaKelas = $this->helpFunction->convertKelasToMda($namaKelasSantri, $mappedMdaKelas);

                // Ambil data signature untuk kepala sekolah dari database
                $signatures = [];
                if ($IdTahunAjaran) {
                    $builder = $this->signatureModel->db->table('tbl_tanda_tangan s');
                    $builder->select('s.*, j.NamaJabatan, g.Nama as NamaGuru, NULL as IdKelas');
                    $builder->join('tbl_struktur_lembaga sl', 'sl.IdGuru = s.IdGuru AND sl.IdTpq = s.IdTpq');
                    $builder->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan');
                    $builder->join('tbl_guru g', 'g.IdGuru = s.IdGuru');
                    $builder->where('j.NamaJabatan', 'Kepala TPQ');
                    $builder->where('s.IdSantri', $dataSantri['IdSantri']);
                    $builder->where('s.IdTpq', $idTpq);
                    $builder->where('s.IdTahunAjaran', $IdTahunAjaran);
                    $builder->where('s.JenisDokumen', 'ProfilSantri');
                    $builder->where('s.StatusValidasi', 'Valid');
                    $builder->orderBy('s.TanggalTtd', 'DESC');
                    $signatures = $builder->get()->getResultArray();
                }

                $data = [
                    'printNamaTpq' => $namaLembaga,
                    'printNamaKelas' => $printNamaKelas,
                    'printNamaSantri' => $dataSantri['NamaSantri'],
                    'printNikSantri' => $dataSantri['NikSantri'],
                    'printTempatTTL' => $dataSantri['TempatLahirSantri'] . ', ' . formatTanggalIndonesia($dataSantri['TanggalLahirSantri'], 'd F Y'),
                    'printJenisKelamin' => $dataSantri['JenisKelamin'],
                    'printAlamatSantri' => $dataSantri['AlamatSantri'],
                    'printRtSantri' => $dataSantri['RtSantri'] ?? '',
                    'printRwSantri' => $dataSantri['RwSantri'] ?? '',
                    'printKelurahanDesaSantri' => $dataSantri['KelurahanDesaSantri'] ?? '',
                    'printKecamatanSantri' => $dataSantri['KecamatanSantri'] ?? '',
                    'printKabupatenKotaSantri' => $dataSantri['KabupatenKotaSantri'] ?? '',
                    'printProvinsiSantri' => $dataSantri['ProvinsiSantri'] ?? '',
                    'printNamaAyah' => $dataSantri['NamaAyah'],
                    'printNamaIbu' => $dataSantri['NamaIbu'],
                    'printTelp' => $dataSantri['NoHpSantri'] ?: ($dataSantri['NoHpAyah'] ?: $dataSantri['NoHpIbu']),
                    'printPekerjaanAyah' => $dataSantri['PekerjaanUtamaAyah'],
                    'printPekerjaanIbu' => $dataSantri['PekerjaanUtamaIbu'],
                    'printTanggalDiterima' => formatTanggalIndonesia($dataSantri['created_at'], 'd F Y'),
                    'printFotoSantri' => null,
                    'printKepalaTpq' => $kepalaSekolah,
                    'printKopLembaga' => $kopLembaga,
                    'printAlamatTpq' => $alamatLembaga,
                    'printKelurahanDesaTpq' => $tpqRow['KelurahanDesa'] ?? '',
                    'printKecamatanTpq' => $tpqRow['Kecamatan'] ?? 'Seri Kuala Lobam',
                    'printKabupatenKotaTpq' => $tpqRow['KabupatenKota'] ?? 'Bintan',
                    'printProvinsiTpq' => $tpqRow['Provinsi'] ?? 'Kepulauan Riau',
                    'printKodePosTpq' => $tpqRow['KodePos'] ?? '29152',
                    'printTelpTpq' => $useMdaData && $mdaRow ? ($mdaRow['NoHp'] ?? $tpqRow['NoHp'] ?? '081234567890') : ($tpqRow['NoHp'] ?? '081234567890'),
                    'printEmailTpq' => $tpqRow['Email'] ?? $namaLembaga . '@TpqSmart.simpedis.com',
                    'printLembagaType' => $lembagaType,
                    'signatures' => $signatures, // Tambahkan signatures untuk setiap santri
                ];

                if (!empty($dataSantri['PhotoProfil'])) {
                    if (ENVIRONMENT === 'production') {
                        $uploadPath = '/home/u1525344/public_html/tpqsmart/uploads/santri/';
                    } else {
                        $uploadPath = ROOTPATH . 'public/uploads/santri/';
                    }
                    $fotoPath = $uploadPath . $dataSantri['PhotoProfil'];
                    $fotoData = file_exists($fotoPath) ? file_get_contents($fotoPath) : null;
                    if ($fotoData) {
                        $data['printFotoSantri'] = 'data:image/jpeg;base64,' . base64_encode($fotoData);
                    }
                }

                // Render template dan ekstrak body content
                $profilTemplate = view('backend/santri/pdftemplateprofileraport', ['data' => $data]);
                preg_match('/<body>(.*?)<\/body>/s', $profilTemplate, $bodyMatches);
                $bodyContent = $bodyMatches[1] ?? '';

                // Tambahkan body content dengan page break jika bukan yang terakhir
                // Gunakan page-break-before untuk menghindari blank page di halaman genap
                if (!$isLast) {
                    $htmlContent .= '<div style="page-break-before: always; page-break-inside: avoid; min-height: 100vh;">' . $bodyContent . '</div>';
                } else {
                    $htmlContent .= '<div style="page-break-inside: avoid;">' . $bodyContent . '</div>';
                }
            }

            // Tutup HTML
            $htmlContent .= '</body></html>';

            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('folio', 'portrait');
            $dompdf->render();

            $filename = 'Profil_Santri_All_' . date('YmdHis') . '.pdf';
            log_message('info', 'Santri: generatePDFAllProfilSantri - Footer');
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($dompdf->output());
        } catch (\Exception $e) {
            log_message('error', 'Santri: generatePDFAllProfilSantri - Error: ' . $e->getMessage());
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

    private function uploadFile($file, $prefix, $IdSantri, $oldFileName = null)
    {
        log_message('info', 'Santri: uploadFile - Header');
        try {
            // Jika tidak ada file baru yang diupload, kembalikan nama file lama
            if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                log_message('info', 'Santri: uploadFile - Tidak ada file yang di upload');
                return $oldFileName;
            }

            if (!$file->isValid()) {
                log_message('error', 'Santri: uploadFile - File tidak valid: ' . $file->getErrorString());
                throw new \Exception('File tidak valid: ' . $file->getErrorString());
            }

            if ($file->hasMoved()) {
                log_message('error', 'Santri: uploadFile - File sudah dipindahkan sebelumnya');
                throw new \Exception('File sudah dipindahkan sebelumnya');
            }

            // Tentukan path berdasarkan environment
            if (ENVIRONMENT === 'production') {
                $uploadPath = '/home/u1525344/public_html/tpqsmart/uploads/santri/';
                $thumbnailPath = $uploadPath . 'thumbnails/';
            } else {
                $uploadPath = ROOTPATH . 'public/uploads/santri/';
                $thumbnailPath = $uploadPath . 'thumbnails/';
            }

            // Hapus file lama jika ada
            if ($oldFileName) {
                $oldFilePath = $uploadPath . $oldFileName;
                $oldThumbnailPath = $thumbnailPath . 'thumb_' . $oldFileName;

                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                    log_message('info', 'Santri: uploadFile - File lama berhasil dihapus: ' . $oldFilePath);
                }

                if (file_exists($oldThumbnailPath)) {
                    unlink($oldThumbnailPath);
                    log_message('info', 'Santri: uploadFile - Thumbnail lama berhasil dihapus: ' . $oldThumbnailPath);
                }
            }

            $randomNumber = uniqid();
            $extension = $file->getExtension();
            $newName = $prefix . '_' . $IdSantri . '_' . $randomNumber . '.' . $extension;

            // Validasi direktori
            if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
                if (!is_dir($uploadPath))
                    log_message('error', 'Tidak ditemukan Main Directory:' . $uploadPath);
                if (!is_writable($uploadPath))
                    log_message('error', 'Tidak memeiliki akses write ke Main Directory:' . $uploadPath);
                throw new \Exception('Direktori upload tidak valid atau tidak dapat ditulis');
            }

            $targetPath = $uploadPath . $newName;

            // Upload file baru
            if (!$file->move($uploadPath, $newName, true)) {
                throw new \Exception('Gagal memindahkan file');
            }

            // Buat thumbnail untuk file gambar
            $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($extension), $imageTypes)) {
                if (!is_dir($thumbnailPath)) {
                    mkdir($thumbnailPath, 0777, true);
                }
                
                $thumbnailTarget = $thumbnailPath . 'thumb_' . $newName;
                
                $image = \Config\Services::image();
                $image->withFile($targetPath)
                    ->fit(30, 40, 'center')
                    ->save($thumbnailTarget);

                log_message('info', 'Santri: uploadFile - Thumbnail berhasil dibuat: ' . $thumbnailTarget);
            }

            log_message('info', 'Santri: uploadFile - File baru berhasil diupload: ' . $newName);
            return $newName;

        } catch (\Exception $e) {
            log_message('error', 'Santri: uploadFile - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    // Tambahkan fungsi helper untuk konversi RT/RW
    private function convertRTRW($value)
    {
        // Hapus leading zeros dan non-numeric characters
        $value = preg_replace('/^0+|[^0-9]/', '', $value);

        // Convert ke integer kemudian format ke 3 digit
        return str_pad((int)$value, 3, '0', STR_PAD_LEFT);
    }

    public function updateStatusActive()
    {
        log_message('info', 'Santri: updateStatusActive - Header');
        try {
            $json = $this->request->getJSON();
            $id = $json->id;
            // Info : 0 = baru daftar, 1 = aktif, 2 = alumni/no active
            // Terima nilai langsung: 0, 1, atau 2
            $active = (int)$json->active;

            // Validasi nilai active
            if (!in_array($active, [0, 1, 2])) {
                log_message('error', 'Santri: updateStatusActive - Nilai active tidak valid: ' . $active);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nilai status tidak valid. Harus 0 (Santri Baru), 1 (Aktif), atau 2 (Alumni)'
                ]);
            }

            log_message('info', 'Santri: updateStatusActive - ID: ' . $id . ', Active: ' . $active);

            // Cek apakah data santri ada
            $santri = $this->DataSantriBaru->find($id);
            if (!$santri) {
                log_message('error', 'Santri: updateStatusActive - Data santri tidak ditemukan dengan ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            // Update status
            $result = $this->DataSantriBaru
                ->where('id', $id)
                ->set(['Active' => $active])
                ->update();

            if ($result === false) {
                log_message('error', 'Santri: updateStatusActive - Gagal mengupdate status');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui status'
                ]);
            }

            log_message('info', 'Santri: updateStatusActive - Status berhasil diperbarui');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: updateStatusActive - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fungsi untuk mengecek data nilai yang perlu dinormalisasi (tanpa menghapus)
     * Logika:
     * 1. Setiap IdSantri harus memiliki data nilai berdasarkan materi perkelas yang sudah disetting di tbl_kelas_materi_pelajaran
     * 2. IdSantri memiliki IdMateri dengan mengecek per kelas dan per semester
     * 3. IdSantri memastikan referensi ke master tbl_kelas_materi_pelajaran, dan munculkan jika memiliki double IdMateri yang sama (kelas, idTahunAjaran, idTpq, semester)
     * 4. Check juga jika terdapat IdMateri diluar yang seharusnya ada
     */
    public function checkNormalisasiNilai()
    {
        log_message('info', 'Santri: checkNormalisasiNilai - Start');

        try {
            $kelasMateriModel = new KelasMateriPelajaranModel();
            $invalidData = [];
            $duplicateData = [];
            $missingData = [];
            $totalChecked = 0;

            // 1. Ambil semua data santri dengan kelas mereka dari tbl_kelas_santri
            $db = \Config\Database::connect();
            $builderKelasSantri = $db->table('tbl_kelas_santri ks');
            $builderKelasSantri->select('ks.IdSantri, ks.IdKelas, ks.IdTahunAjaran, ks.IdTpq, s.NamaSantri, k.NamaKelas, t.NamaTpq');
            $builderKelasSantri->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri', 'left');
            $builderKelasSantri->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left');
            $builderKelasSantri->join('tbl_tpq t', 't.IdTpq = ks.IdTpq', 'left');
            $builderKelasSantri->where('ks.Status', 1); // Hanya kelas santri yang aktif
            $allKelasSantri = $builderKelasSantri->get()->getResultArray();

            log_message('info', 'Santri: checkNormalisasiNilai - Total santri dengan kelas: ' . count($allKelasSantri));

            // 2. Ambil semua data nilai dengan join ke tabel santri dan materi
            $builder = $this->nilaiModel->db->table('tbl_nilai n');
            $builder->select('n.*, s.NamaSantri, m.NamaMateri, k.NamaKelas, t.NamaTpq');
            $builder->join('tbl_santri_baru s', 's.IdSantri = n.IdSantri', 'left');
            $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = n.IdMateri', 'left');
            $builder->join('tbl_kelas k', 'k.IdKelas = n.IdKelas', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = n.IdTpq', 'left');
            $allNilai = $builder->get()->getResultArray();
            $totalChecked = count($allNilai);

            log_message('info', 'Santri: checkNormalisasiNilai - Total data nilai yang akan dicek: ' . $totalChecked);

            // 3. Buat mapping: IdSantri -> [IdKelas, IdTahunAjaran, IdTpq]
            $santriKelasMap = [];
            foreach ($allKelasSantri as $ks) {
                $key = $ks['IdSantri'] . '_' . $ks['IdTahunAjaran'] . '_' . $ks['IdTpq'];
                if (!isset($santriKelasMap[$key])) {
                    $santriKelasMap[$key] = [];
                }
                $santriKelasMap[$key][] = [
                    'IdKelas' => $ks['IdKelas'],
                    'IdTahunAjaran' => $ks['IdTahunAjaran'],
                    'IdTpq' => $ks['IdTpq'],
                    'NamaSantri' => $ks['NamaSantri'],
                    'NamaKelas' => $ks['NamaKelas'],
                    'NamaTpq' => $ks['NamaTpq']
                ];
            }

            // 4. Buat mapping: [IdKelas, IdTpq] -> [IdMateri] berdasarkan tbl_kelas_materi_pelajaran
            $kelasMateriMap = [];
            $allKelasMateri = $kelasMateriModel->findAll();
            foreach ($allKelasMateri as $km) {
                $key = $km['IdKelas'] . '_' . $km['IdTpq'];
                if (!isset($kelasMateriMap[$key])) {
                    $kelasMateriMap[$key] = [];
                }
                $kelasMateriMap[$key][] = [
                    'IdMateri' => $km['IdMateri'],
                    'SemesterGanjil' => $km['SemesterGanjil'],
                    'SemesterGenap' => $km['SemesterGenap']
                ];
            }

            // 5. Array untuk tracking duplikat dan data yang sudah dicek
            $seen = [];
            $nilaiBySantri = [];

            // 6. Kelompokkan nilai berdasarkan IdSantri
            foreach ($allNilai as $nilai) {
                $idSantri = $nilai['IdSantri'];
                $key = $idSantri . '_' . $nilai['IdTahunAjaran'] . '_' . $nilai['IdTpq'];

                if (!isset($nilaiBySantri[$key])) {
                    $nilaiBySantri[$key] = [];
                }
                $nilaiBySantri[$key][] = $nilai;
            }

            // 7. Untuk setiap santri, cek data nilainya
            foreach ($santriKelasMap as $santriKey => $kelasList) {
                list($idSantri, $idTahunAjaran, $idTpq) = explode('_', $santriKey);
                $santriInfo = $kelasList[0]; // Ambil info santri dari kelas pertama

                foreach ($kelasList as $kelasInfo) {
                    $idKelas = $kelasInfo['IdKelas'];
                    $kelasMateriKey = $idKelas . '_' . $idTpq;

                    // Ambil materi yang seharusnya ada untuk kelas ini
                    $materiSeharusnyaAda = $kelasMateriMap[$kelasMateriKey] ?? [];

                    // Ambil nilai santri untuk kelas ini
                    $nilaiSantri = [];
                    if (isset($nilaiBySantri[$santriKey])) {
                        foreach ($nilaiBySantri[$santriKey] as $nilai) {
                            if ($nilai['IdKelas'] == $idKelas) {
                                $nilaiSantri[] = $nilai;
                            }
                        }
                    }

                    // Cek duplikat dan materi yang tidak valid
                    $seenInKelas = [];
                    foreach ($nilaiSantri as $nilai) {
                        $nilaiId = $nilai['Id'];
                        $idMateri = $nilai['IdMateri'];
                        $semester = $nilai['Semester'];

                        // Buat key untuk tracking duplikat
                        $duplicateKey = $idSantri . '_' . $idMateri . '_' . $idKelas . '_' . $idTpq . '_' . $idTahunAjaran . '_' . $semester;

                        // Cek duplikat
                        if (isset($seenInKelas[$duplicateKey])) {
                            // Ini duplikat
                            $nilaiValue = $nilai['Nilai'] ?? 0;
                            $nilaiValue = (float)$nilaiValue;

                            // Tentukan kategori berdasarkan nilai
                            $kategori = 'aman'; // Default
                            $kategoriLabel = 'Aman';
                            $kategoriColor = 'success'; // Hijau

                            if ($nilaiValue > 0) {
                                $kategori = 'perhatian';
                                $kategoriLabel = 'Perhatian';
                                $kategoriColor = 'warning'; // Kuning
                            }

                            $duplicateData[] = [
                                'Id' => $nilaiId,
                                'NamaSantri' => $nilai['NamaSantri'] ?? $santriInfo['NamaSantri'],
                                'NamaMateri' => $nilai['NamaMateri'] ?? 'Tidak ditemukan',
                                'NamaKelas' => $nilai['NamaKelas'] ?? $kelasInfo['NamaKelas'],
                                'NamaTpq' => $nilai['NamaTpq'] ?? $santriInfo['NamaTpq'],
                                'IdSantri' => $idSantri,
                                'IdMateri' => $idMateri,
                                'IdKelas' => $idKelas,
                                'IdTpq' => $idTpq,
                                'IdTahunAjaran' => $idTahunAjaran,
                                'Semester' => $semester,
                                'Nilai' => $nilaiValue,
                                'created_at' => $nilai['created_at'] ?? '',
                                'updated_at' => $nilai['updated_at'] ?? '',
                                'type' => 'duplicate',
                                'kategori' => $kategori,
                                'kategori_label' => $kategoriLabel,
                                'kategori_color' => $kategoriColor,
                                'reason' => 'Duplikat: IdMateri yang sama untuk IdSantri, IdKelas, IdTpq, IdTahunAjaran, dan Semester yang sama'
                            ];
                        } else {
                            $seenInKelas[$duplicateKey] = $nilaiId;
                        }

                        // Cek apakah IdMateri ada di tbl_kelas_materi_pelajaran untuk kelas ini
                        $materiValid = false;
                        foreach ($materiSeharusnyaAda as $materi) {
                            if ($materi['IdMateri'] == $idMateri) {
                                // Cek semester
                                if (($semester == 'Ganjil' && $materi['SemesterGanjil'] == 1) ||
                                    ($semester == 'Genap' && $materi['SemesterGenap'] == 1)
                                ) {
                                    $materiValid = true;
                                    break;
                                }
                            }
                        }

                        if (!$materiValid) {
                            // Materi tidak valid untuk kelas dan semester ini
                            $invalidData[] = [
                                'Id' => $nilaiId,
                                'NamaSantri' => $nilai['NamaSantri'] ?? $santriInfo['NamaSantri'],
                                'NamaMateri' => $nilai['NamaMateri'] ?? 'Tidak ditemukan',
                                'NamaKelas' => $nilai['NamaKelas'] ?? $kelasInfo['NamaKelas'],
                                'NamaTpq' => $nilai['NamaTpq'] ?? $santriInfo['NamaTpq'],
                                'IdSantri' => $idSantri,
                                'IdMateri' => $idMateri,
                                'IdKelas' => $idKelas,
                                'IdTpq' => $idTpq,
                                'IdTahunAjaran' => $idTahunAjaran,
                                'Semester' => $semester,
                                'Nilai' => $nilai['Nilai'] ?? 0,
                                'created_at' => $nilai['created_at'] ?? '',
                                'updated_at' => $nilai['updated_at'] ?? '',
                                'type' => 'invalid',
                                'reason' => 'IdMateri tidak ada di tbl_kelas_materi_pelajaran untuk IdKelas dan IdTpq ini, atau tidak sesuai dengan semester'
                            ];
                        }
                    }
                }
            }

            $totalInvalid = count($invalidData);
            $totalDuplicate = count($duplicateData);
            $totalToDelete = $totalInvalid + $totalDuplicate;

            // Hitung rangkuman per TPQ
            $summaryByTpq = [];
            $allDataForSummary = array_merge($invalidData, $duplicateData);

            foreach ($allDataForSummary as $data) {
                $idTpq = $data['IdTpq'];
                $namaTpq = $data['NamaTpq'] ?? 'Tidak ditemukan';

                if (!isset($summaryByTpq[$idTpq])) {
                    $summaryByTpq[$idTpq] = [
                        'IdTpq' => $idTpq,
                        'NamaTpq' => $namaTpq,
                        'total_invalid' => 0,
                        'total_duplicate' => 0,
                        'total_duplicate_aman' => 0,      // Duplikat dengan nilai kosong
                        'total_duplicate_perhatian' => 0, // Duplikat dengan nilai > 0
                        'total' => 0,
                        'santri_terkena' => []  // Array untuk menyimpan IdSantri unik
                    ];
                }

                if ($data['type'] == 'invalid') {
                    $summaryByTpq[$idTpq]['total_invalid']++;
                } else if ($data['type'] == 'duplicate') {
                    $summaryByTpq[$idTpq]['total_duplicate']++;

                    // Hitung kategori duplikat
                    if (isset($data['kategori'])) {
                        if ($data['kategori'] == 'aman') {
                            $summaryByTpq[$idTpq]['total_duplicate_aman']++;
                        } else if ($data['kategori'] == 'perhatian') {
                            $summaryByTpq[$idTpq]['total_duplicate_perhatian']++;
                        }
                    }
                }

                // Kumpulkan IdSantri yang terkena (unik)
                if (isset($data['IdSantri']) && !in_array($data['IdSantri'], $summaryByTpq[$idTpq]['santri_terkena'])) {
                    $summaryByTpq[$idTpq]['santri_terkena'][] = $data['IdSantri'];
                }

                $summaryByTpq[$idTpq]['total']++;
            }

            // Hitung jumlah santri yang terkena per TPQ
            foreach ($summaryByTpq as $key => $tpq) {
                $summaryByTpq[$key]['jumlah_santri_terkena'] = count($tpq['santri_terkena']);
                // Hapus array santri_terkena dari output (tidak perlu dikirim ke frontend)
                unset($summaryByTpq[$key]['santri_terkena']);
            }

            // Convert to array untuk JSON
            $summaryByTpqArray = array_values($summaryByTpq);

            log_message('info', "Santri: checkNormalisasiNilai - Ditemukan {$totalInvalid} data tidak valid dan {$totalDuplicate} duplikat");

            return $this->response->setJSON([
                'success' => true,
                'total_checked' => $totalChecked,
                'total_invalid' => $totalInvalid,
                'total_duplicate' => $totalDuplicate,
                'total_to_delete' => $totalToDelete,
                'invalid_data' => $invalidData,
                'duplicate_data' => $duplicateData,
                'summary_by_tpq' => $summaryByTpqArray
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: checkNormalisasiNilai - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengecek data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fungsi untuk normalisasi data nilai
     * Menghapus data nilai berdasarkan ID yang dipilih
     * Untuk data duplikat, hanya menghapus duplikat dan menyisakan satu record
     */
    public function normalisasiNilai()
    {
        log_message('info', 'Santri: normalisasiNilai - Start');

        try {
            $json = $this->request->getJSON();
            $idsToDelete = $json->ids ?? [];

            if (empty($idsToDelete)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih untuk dihapus'
                ]);
            }

            // Validasi bahwa ids adalah array
            if (!is_array($idsToDelete)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format data tidak valid'
                ]);
            }

            log_message('info', 'Santri: normalisasiNilai - Akan memproses ' . count($idsToDelete) . ' records');

            // Ambil IdTpq dari session (TPQ saat ini)
            $currentIdTpq = session()->get('IdTpq');

            // Ambil semua data yang akan dihapus untuk analisis duplikat
            $dataToDelete = $this->nilaiModel->whereIn('Id', $idsToDelete)->findAll();

            // Kelompokkan berdasarkan kombinasi duplikat (tanpa IdSantri dan IdTpq)
            // Key: IdMateri_IdKelas_IdTahunAjaran_Semester
            // Ini memungkinkan beberapa IdSantri berbeda dalam satu grup (untuk kelas yang sama)
            $duplicateGroups = [];
            $invalidIds = [];
            $duplicateIds = [];

            foreach ($dataToDelete as $nilai) {
                $duplicateKey = $nilai['IdMateri'] . '_' . $nilai['IdKelas'] . '_' .
                    $nilai['IdTahunAjaran'] . '_' . $nilai['Semester'];

                if (!isset($duplicateGroups[$duplicateKey])) {
                    $duplicateGroups[$duplicateKey] = [];
                }
                $duplicateGroups[$duplicateKey][] = $nilai;
            }

            // Untuk setiap grup duplikat, tentukan mana yang harus disimpan
            foreach ($duplicateGroups as $key => $group) {
                if (count($group) > 1) {
                    // Ada duplikat dalam grup ini (beberapa IdSantri berbeda dengan kelas yang sama)
                    // Ambil status Active dari tbl_santri_baru untuk setiap IdSantri
                    $santriActiveMap = [];
                    $santriTpqMap = [];
                    foreach ($group as $item) {
                        $idSantri = $item['IdSantri'];
                        if (!isset($santriActiveMap[$idSantri])) {
                            $santri = $this->DataSantriBaru->where('IdSantri', $idSantri)->first();
                            $santriActiveMap[$idSantri] = $santri ? (int)($santri['Active'] ?? 0) : 0;
                            $santriTpqMap[$idSantri] = $santri ? (int)($santri['IdTpq'] ?? 0) : 0;
                        }
                    }

                    // Urutkan: prioritaskan santri yang Active = 1 di TPQ saat ini, kemudian yang Active = 1 di TPQ lain, kemudian yang memiliki nilai, kemudian yang tertua
                    usort($group, function ($a, $b) use ($santriActiveMap, $santriTpqMap, $currentIdTpq) {
                        $idSantriA = $a['IdSantri'];
                        $idSantriB = $b['IdSantri'];

                        $activeA = $santriActiveMap[$idSantriA] ?? 0;
                        $activeB = $santriActiveMap[$idSantriB] ?? 0;
                        $tpqA = $santriTpqMap[$idSantriA] ?? 0;
                        $tpqB = $santriTpqMap[$idSantriB] ?? 0;

                        // Prioritas 1: Santri yang Active = 1 (Aktif) di TPQ saat ini
                        if ($activeA == 1 && $tpqA == $currentIdTpq && ($activeB != 1 || $tpqB != $currentIdTpq)) return -1;
                        if ($activeB == 1 && $tpqB == $currentIdTpq && ($activeA != 1 || $tpqA != $currentIdTpq)) return 1;

                        // Prioritas 2: Santri yang Active = 1 (Aktif) di TPQ lain
                        if ($activeA == 1 && $activeB != 1) return -1;
                        if ($activeA != 1 && $activeB == 1) return 1;

                        // Prioritas 3: Jika status Active sama, prioritaskan yang memiliki nilai
                        $nilaiA = (float)($a['Nilai'] ?? 0);
                        $nilaiB = (float)($b['Nilai'] ?? 0);

                        if ($nilaiA > 0 && $nilaiB == 0) return -1;
                        if ($nilaiA == 0 && $nilaiB > 0) return 1;

                        // Prioritas 4: Jika sama, prioritaskan yang tertua (Id terkecil)
                        return $a['Id'] <=> $b['Id'];
                    });

                    // Simpan yang pertama (terbaik - santri aktif di TPQ saat ini), hapus yang lainnya
                    $keepId = $group[0]['Id'];
                    foreach ($group as $item) {
                        if ($item['Id'] != $keepId) {
                            $duplicateIds[] = $item['Id'];
                        }
                    }
                } else {
                    // Tidak ada duplikat, bisa langsung dihapus (ini adalah data invalid)
                    $invalidIds[] = $group[0]['Id'];
                }
            }

            // Gabungkan semua ID yang akan dihapus
            $finalIdsToDelete = array_merge($invalidIds, $duplicateIds);

            // Hapus duplikat dari array (jika ada)
            $finalIdsToDelete = array_unique($finalIdsToDelete);

            log_message('info', 'Santri: normalisasiNilai - Akan menghapus ' . count($finalIdsToDelete) . ' records (invalid: ' . count($invalidIds) . ', duplicate: ' . count($duplicateIds) . ')');

            $deletedCount = 0;
            $errors = [];

            // Hapus data berdasarkan ID yang sudah difilter
            if (!empty($finalIdsToDelete)) {
                try {
                    $deletedCount = $this->nilaiModel->whereIn('Id', $finalIdsToDelete)->delete();

                    if ($deletedCount === false) {
                        throw new \Exception('Gagal menghapus data');
                    }

                    log_message('info', 'Santri: normalisasiNilai - Berhasil menghapus ' . $deletedCount . ' records');
                } catch (\Exception $e) {
                    $errors[] = "Gagal menghapus data: " . $e->getMessage();
                    log_message('error', 'Santri: normalisasiNilai - Error menghapus data: ' . $e->getMessage());
                }
            }

            $message = "Normalisasi selesai. ";
            $message .= "Data yang dihapus: {$deletedCount} dari " . count($idsToDelete) . " yang dipilih. ";
            $message .= "(Invalid: " . count($invalidIds) . ", Duplikat: " . count($duplicateIds) . ")";

            if (count($idsToDelete) > count($finalIdsToDelete)) {
                $skipped = count($idsToDelete) - count($finalIdsToDelete);
                $message .= " Catatan: {$skipped} data duplikat disimpan (hanya duplikat yang dihapus, menyisakan satu record per grup).";
            }

            if (!empty($errors)) {
                $message .= " Terjadi beberapa error: " . implode(', ', $errors);
            }

            log_message('info', 'Santri: normalisasiNilai - ' . $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'data' => [
                    'total_selected' => count($idsToDelete),
                    'total_to_delete' => count($finalIdsToDelete),
                    'deleted_count' => $deletedCount,
                    'invalid_count' => count($invalidIds),
                    'duplicate_count' => count($duplicateIds),
                    'skipped_count' => count($idsToDelete) - count($finalIdsToDelete),
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: normalisasiNilai - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal melakukan normalisasi: ' . $e->getMessage()
            ]);
        }
    }

    public function updateVerifikasi()
    {
        $json = $this->request->getJSON();
        $id = $json->id;
        $status = $json->status;

        // Validasi status yang diperbolehkan
        $allowedStatus = ['Belum Diverifikasi', 'Sudah Diverifikasi', 'Perlu Perbaikan'];
        if (!in_array($status, $allowedStatus)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid']);
        }

        try {
            // Cek apakah data santri ada
            $santri = $this->DataSantriBaru->find($id);
            if (!$santri) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data santri tidak ditemukan']);
            }

            // Update status
            $this->DataSantriBaru->set('Status', $status)
                ->where('id', $id)
                ->update();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status verifikasi berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', '[updateVerifikasi] Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui status verifikasi: ' . $e->getMessage()
            ]);
        }
    }

    public function konfirmasiDeleteSantri($IdSantri = null)
    {
        if (!$IdSantri) {
            return redirect()->to('backend/santri/showAturSantriBaru')->with('error', 'ID Santri tidak ditemukan');
        }

        // Ambil data santri menggunakan method dari model
        $santri = $this->DataSantriBaru->getDetailSantriForDelete($IdSantri);

        if (!$santri) {
            return redirect()->to('backend/santri/showAturSantriBaru')->with('error', 'Data santri tidak ditemukan');
        }

        $currentTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

        // Cek data nilai yang ada
        $existingNilai = $this->nilaiModel
            ->where('IdSantri', $IdSantri)
            ->where('Nilai >', 0) // Check for filled values
            ->first();

        $totalNilaiRecords = $this->nilaiModel
            ->where('IdSantri', $IdSantri)
            ->countAllResults();

        // Cek data kelas santri
        $existingKelasSantri = $this->kelasModel
            ->where('IdSantri', $IdSantri)
            ->countAllResults();

        // Cek data absensi
        $existingAbsensi = $this->DataSantriBaru
            ->select('COUNT(*) as total')
            ->where('IdSantri', $IdSantri)
            ->countAllResults();

        $data = [
            'page_title' => 'Konfirmasi Hapus Santri',
            'dataSantri' => $santri,
            'currentTahunAjaran' => $currentTahunAjaran,
            'hasExistingNilai' => $existingNilai ? true : false,
            'existingNilaiCount' => $existingNilai ? $this->nilaiModel->where('IdSantri', $IdSantri)->where('Nilai >', 0)->countAllResults() : 0,
            'totalNilaiRecords' => $totalNilaiRecords,
            'existingKelasSantri' => $existingKelasSantri,
            'existingAbsensi' => $existingAbsensi
        ];

        return view('backend/santri/konfirmasiDeleteSantri', $data);
    }

    public function processDeleteSantri()
    {
        $IdSantri = $this->request->getPost('IdSantri');
        $confirmDelete = $this->request->getPost('confirmDelete');

        if (!$IdSantri) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID Santri tidak ditemukan']);
        }

        if (!$confirmDelete) {
            return $this->response->setJSON(['success' => false, 'message' => 'Konfirmasi penghapusan diperlukan']);
        }

        try {
            // Log untuk debugging
            log_message('info', '[processDeleteSantri] Starting deletion for IdSantri: ' . $IdSantri);

            // Ambil data santri untuk mendapatkan nama file yang akan dihapus
            $santriData = $this->DataSantriBaru->where('IdSantri', $IdSantri)->first();
            if (!$santriData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data santri tidak ditemukan']);
            }

            // Mulai transaksi database
            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Hapus data nilai (semua tahun ajaran)
            $nilaiDeleted = $db->table('tbl_nilai')
                ->where('IdSantri', $IdSantri)
                ->delete();
            log_message('info', '[processDeleteSantri] Deleted ' . $nilaiDeleted . ' records from tbl_nilai');

            // 2. Hapus data kelas santri (semua tahun ajaran)
            $kelasSantriDeleted = $db->table('tbl_kelas_santri')
                ->where('IdSantri', $IdSantri)
                ->delete();
            log_message('info', '[processDeleteSantri] Deleted ' . $kelasSantriDeleted . ' records from tbl_kelas_santri');

            // 3. Hapus data absensi (semua tahun ajaran)
            $absensiDeleted = $db->table('tbl_absensi_santri')
                ->where('IdSantri', $IdSantri)
                ->delete();
            log_message('info', '[processDeleteSantri] Deleted ' . $absensiDeleted . ' records from tbl_absensi_santri');

            // 4. Hapus data santri dari tbl_santri_baru
            $santriBaruDeleted = $db->table('tbl_santri_baru')
                ->where('IdSantri', $IdSantri)
                ->delete();
            log_message('info', '[processDeleteSantri] Deleted ' . $santriBaruDeleted . ' records from tbl_santri_baru');

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                log_message('error', '[processDeleteSantri] Transaction failed: ' . json_encode($error));
                throw new \Exception('Terjadi kesalahan saat menghapus data santri: ' . ($error['message'] ?? 'Unknown database error'));
            }

            // Hapus file-file terkait santri setelah transaksi database berhasil
            $this->deleteSantriFiles($santriData);

            log_message('info', '[processDeleteSantri] Successfully deleted santri IdSantri: ' . $IdSantri);
            return $this->response->setJSON(['success' => true, 'message' => 'Data santri dan file terkait berhasil dihapus permanen']);
        } catch (\Exception $e) {
            log_message('error', '[processDeleteSantri] Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data santri: ' . $e->getMessage()]);
        }
    }

    // Method untuk menghapus file-file terkait santri
    private function deleteSantriFiles($santriData)
    {
        try {
            // Tentukan path berdasarkan environment (sama seperti uploadFile)
            if (ENVIRONMENT === 'production') {
                $basePath = '/home/u1525344/public_html/tpqsmart/uploads/santri/';
                $thumbnailPath = $basePath . 'thumbnails/';
            } else {
                $basePath = ROOTPATH . 'public/uploads/santri/';
                $thumbnailPath = $basePath . 'thumbnails/';
            }

            $filesDeleted = 0;
            $filesToDelete = [];

            // Daftar file yang perlu dihapus (semua file disimpan di root folder uploads/santri/)
            if (!empty($santriData['PhotoProfil'])) {
                $filesToDelete[] = $basePath . $santriData['PhotoProfil'];
                // Hapus juga file thumbnail jika ada (disimpan di subfolder thumbnails/)
                $thumbnailFile = $thumbnailPath . 'thumb_' . $santriData['PhotoProfil'];
                if (file_exists($thumbnailFile)) {
                    $filesToDelete[] = $thumbnailFile;
                }
            }
            if (!empty($santriData['FileKIP'])) {
                $filesToDelete[] = $basePath . $santriData['FileKIP'];
            }
            if (!empty($santriData['FileKkSantri'])) {
                $filesToDelete[] = $basePath . $santriData['FileKkSantri'];
            }
            if (!empty($santriData['FileKKAyah'])) {
                $filesToDelete[] = $basePath . $santriData['FileKKAyah'];
            }
            if (!empty($santriData['FileKKIbu'])) {
                $filesToDelete[] = $basePath . $santriData['FileKKIbu'];
            }
            if (!empty($santriData['FileKKS'])) {
                $filesToDelete[] = $basePath . $santriData['FileKKS'];
            }
            if (!empty($santriData['FilePKH'])) {
                $filesToDelete[] = $basePath . $santriData['FilePKH'];
            }

            // Hapus setiap file
            foreach ($filesToDelete as $filePath) {
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        $filesDeleted++;
                        log_message('info', '[deleteSantriFiles] Deleted file: ' . $filePath);
                    } else {
                        log_message('warning', '[deleteSantriFiles] Failed to delete file: ' . $filePath);
                    }
                } else {
                    log_message('info', '[deleteSantriFiles] File not found: ' . $filePath);
                }
            }

            log_message('info', '[deleteSantriFiles] Successfully deleted ' . $filesDeleted . ' files for IdSantri: ' . $santriData['IdSantri']);
        } catch (\Exception $e) {
            log_message('error', '[deleteSantriFiles] Error: ' . $e->getMessage());
            // Tidak throw exception karena penghapusan file bukan critical error
        }
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

    /**
     * Handle tanda tangan bulk kepala sekolah untuk profil santri
     */
    public function ttdBulkKepsekProfil()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');

            // Baca data dari JSON body
            $jsonData = $this->request->getJSON(true);
            $filterIdTpq = $jsonData['filterIdTpq'] ?? $this->request->getPost('filterIdTpq');
            $filterIdKelas = $jsonData['filterIdKelas'] ?? $this->request->getPost('filterIdKelas');

            // Gunakan filter atau session
            $targetIdTpq = $filterIdTpq ?: $IdTpq;

            // Cek permission: hanya Kepala Sekolah yang bisa tanda tangan
            $jabatanData = $this->helpFunction->getStrukturLembagaJabatan($IdGuru, $targetIdTpq);
            $isKepalaSekolah = false;
            if (!empty($jabatanData)) {
                foreach ($jabatanData as $jabatan) {
                    if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                        $isKepalaSekolah = true;
                        break;
                    }
                }
            }

            if (!$isKepalaSekolah) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk menandatangani sebagai kepala sekolah.'
                ]);
            }

            // Ambil semua santri berdasarkan filter
            $santriList = $this->DataSantriBaru->where([
                'IdTpq' => $targetIdTpq,
                'Active' => 1
            ]);

            if ($filterIdKelas) {
                // Handle jika filterIdKelas adalah string dengan koma (multiple kelas)
                if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                    $kelasArray = array_filter(explode(',', $filterIdKelas));
                    $santriList->whereIn('IdKelas', $kelasArray);
                } else {
                    $santriList->where('IdKelas', $filterIdKelas);
                }
            }

            $santriList = $santriList->findAll();

            if (empty($santriList)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada santri yang ditemukan'
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($santriList as $santri) {
                $IdSantri = is_object($santri) ? $santri->IdSantri : $santri['IdSantri'];
                $IdKelas = is_object($santri) ? $santri->IdKelas : $santri['IdKelas'];

                // Cek apakah signature sudah ada untuk profil santri
                $existingSignature = $this->signatureModel->where([
                    'IdSantri' => $IdSantri,
                    'IdTpq' => $targetIdTpq,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdGuru' => $IdGuru,
                    'JenisDokumen' => 'ProfilSantri',
                    'SignatureData' => 'Kepsek',
                    'StatusValidasi' => 'Valid'
                ])->first();

                // Skip jika sudah ada
                if ($existingSignature) {
                    continue;
                }

                // Generate token unik
                $token = $this->generateUniqueToken();

                // Data untuk disimpan
                $signatureData = [
                    'Token' => $token,
                    'IdSantri' => $IdSantri,
                    'IdKelas' => $IdKelas,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'Semester' => '1', // Default semester 1 untuk profil santri
                    'IdGuru' => $IdGuru,
                    'IdTpq' => $targetIdTpq,
                    'JenisDokumen' => 'ProfilSantri', // Pastikan ini tersimpan
                    'SignatureData' => 'Kepsek',
                    'StatusValidasi' => 'Valid',
                    'TanggalTtd' => date('Y-m-d H:i:s')
                ];

                // Debug: Log data sebelum insert
                log_message('debug', 'ProfilSantri - Inserting signature data for IdSantri: ' . $IdSantri . ' - Data: ' . json_encode($signatureData));

                // Simpan data tanda tangan
                $IdSignature = $this->signatureModel->insert($signatureData);

                // Debug: Log hasil insert
                if ($IdSignature) {
                    log_message('debug', 'ProfilSantri - Signature inserted with ID: ' . $IdSignature);

                    // Verifikasi data yang tersimpan
                    $savedData = $this->signatureModel->find($IdSignature);
                    if ($savedData) {
                        log_message('debug', 'ProfilSantri - Saved signature data: ' . json_encode($savedData));
                        // Cek apakah JenisDokumen tersimpan
                        $savedJenisDokumen = is_array($savedData) ? ($savedData['JenisDokumen'] ?? null) : ($savedData->JenisDokumen ?? null);
                        if (empty($savedJenisDokumen)) {
                            log_message('error', 'ProfilSantri - WARNING: JenisDokumen is empty after insert!');
                        }
                    }

                    // Generate QR Code
                    $qrCodeData = $this->generateQRCode($token);

                    if ($qrCodeData) {
                        // Update data tanda tangan dengan nama file QR
                        $this->signatureModel->where('Id', $IdSignature)
                            ->set(['QrCode' => $qrCodeData['filename']])
                            ->update();
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Gagal membuat QR Code untuk santri: {$IdSantri}";
                    }
                } else {
                    $errorCount++;
                    $modelErrors = $this->signatureModel->errors();
                    log_message('error', 'ProfilSantri - Failed to insert signature for IdSantri: ' . $IdSantri . ' - Errors: ' . json_encode($modelErrors));
                    $errors[] = "Gagal menyimpan tanda tangan untuk santri: {$IdSantri}. " . (!empty($modelErrors) ? json_encode($modelErrors) : '');
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Tanda tangan kepala sekolah berhasil dibuat untuk {$successCount} profil santri" . ($errorCount > 0 ? ". {$errorCount} gagal." : "."),
                'successCount' => $successCount,
                'errorCount' => $errorCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: ttdBulkKepsekProfil - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batalkan/hapus tanda tangan bulk kepala sekolah untuk profil santri
     */
    public function cancelBulkKepsekProfil()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        try {
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');
            $IdGuru = session()->get('IdGuru');

            // Baca data dari JSON body
            $jsonData = $this->request->getJSON(true);
            $filterIdTpq = $jsonData['filterIdTpq'] ?? $this->request->getPost('filterIdTpq');
            $filterIdKelas = $jsonData['filterIdKelas'] ?? $this->request->getPost('filterIdKelas');

            // Gunakan filter atau session
            $targetIdTpq = $filterIdTpq ?: $IdTpq;

            // Cek permission: hanya Kepala Sekolah yang bisa membatalkan tanda tangan
            $jabatanData = $this->helpFunction->getStrukturLembagaJabatan($IdGuru, $targetIdTpq);
            $isKepalaSekolah = false;
            if (!empty($jabatanData)) {
                foreach ($jabatanData as $jabatan) {
                    if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                        $isKepalaSekolah = true;
                        break;
                    }
                }
            }

            if (!$isKepalaSekolah) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki permission untuk membatalkan tanda tangan sebagai kepala sekolah.'
                ]);
            }

            // Ambil semua santri berdasarkan filter
            $santriList = $this->DataSantriBaru->where([
                'IdTpq' => $targetIdTpq,
                'Active' => 1
            ]);

            if ($filterIdKelas) {
                // Handle jika filterIdKelas adalah string dengan koma (multiple kelas)
                if (is_string($filterIdKelas) && strpos($filterIdKelas, ',') !== false) {
                    $kelasArray = array_filter(explode(',', $filterIdKelas));
                    $santriList->whereIn('IdKelas', $kelasArray);
                } else {
                    $santriList->where('IdKelas', $filterIdKelas);
                }
            }

            $santriList = $santriList->findAll();

            if (empty($santriList)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada santri yang ditemukan'
                ]);
            }

            $santriIds = array_column($santriList, 'IdSantri');

            // Ambil signature yang akan dihapus untuk mendapatkan QR code
            $signatures = $this->signatureModel->where([
                'IdTpq' => $targetIdTpq,
                'IdTahunAjaran' => $IdTahunAjaran,
                'IdGuru' => $IdGuru,
                'JenisDokumen' => 'ProfilSantri',
                'SignatureData' => 'Kepsek',
                'StatusValidasi' => 'Valid'
            ])->whereIn('IdSantri', $santriIds)->findAll();

            // Hapus file QR code yang terkait
            foreach ($signatures as $signature) {
                if (!empty($signature['QrCode'])) {
                    $qrFilePath = FCPATH . 'uploads/qr/' . $signature['QrCode'];
                    if (file_exists($qrFilePath)) {
                        unlink($qrFilePath);
                    }
                }
            }

            // Hapus semua signature yang sesuai
            $deletedCount = $this->signatureModel->where([
                'IdTpq' => $targetIdTpq,
                'IdTahunAjaran' => $IdTahunAjaran,
                'IdGuru' => $IdGuru,
                'JenisDokumen' => 'ProfilSantri',
                'SignatureData' => 'Kepsek',
                'StatusValidasi' => 'Valid'
            ])->whereIn('IdSantri', $santriIds)->delete();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Tanda tangan kepala sekolah berhasil dibatalkan untuk {$deletedCount} profil santri"
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Santri: cancelBulkKepsekProfil - Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Verifikasi Data Santri
    public function verifikasiDataSantri()
    {
        $idTpq = session()->get('IdTpq');
        
        // Use the new model to fetch data
        $dataSantri = $this->verifikasiSantriModel->getSantriForVerification($idTpq);

        $operatorName = ucwords(strtolower(user()->fullname ?? user()->username));

        $data = [
            'page_title' => 'Verifikasi Data Santri',
            'dataSantri' => $dataSantri,
            'operatorName' => $operatorName
        ];

        return view('backend/santri/verifikasiDataSantri', $data);
    }

    // Halaman Detail Verifikasi - Perbandingan Data Santri dengan KK
    public function perbandinganDataSantri($id = null)
    {
        if (!$id) {
            return redirect()->to('backend/santri/verifikasiDataSantri')->with('error', 'ID Santri tidak valid');
        }

        // Get santri data - use direct query with LEFT JOIN to handle null IdKelas/IdTpq
        $builder = $this->DataSantriBaru->builder();
        $builder->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
            ->where('tbl_santri_baru.id', $id);
        $santri = $builder->get()->getRowArray();

        if (!$santri) {
            return redirect()->to('backend/santri/verifikasiDataSantri')->with('error', 'Data Santri tidak ditemukan');
        }

        // Process KK file - convert PDF to JPG if needed
        $kkImageUrl = null;
        $kkError = null;
        $fileKk = $santri['FileKkSantri'] ?? null;
        
        if ($fileKk) {
            $kkPath = FCPATH . 'uploads/santri/' . $fileKk;
            
            if (file_exists($kkPath)) {
                // Check if it's a PDF
                $extension = strtolower(pathinfo($fileKk, PATHINFO_EXTENSION));
                
                if ($extension === 'pdf') {
                    try {
                        // Convert PDF to image
                        $base64Image = $this->convertPdfToImage($kkPath);
                        $kkImageUrl = $base64Image;
                    } catch (\Exception $e) {
                        log_message('error', 'Santri perbandinganDataSantri: PDF conversion failed - ' . $e->getMessage());
                        $kkError = 'Gagal mengkonversi PDF: ' . $e->getMessage();
                    }
                } else {
                    // It's already an image
                    $kkImageUrl = base_url('uploads/santri/' . $fileKk);
                }
            } else {
                $kkError = 'File KK tidak ditemukan di server';
            }
        }

        $data = [
            'page_title' => 'Verifikasi Data Santri - ' . ($santri['NamaSantri'] ?? ''),
            'santri' => $santri,
            'kkImageUrl' => $kkImageUrl,
            'kkError' => $kkError
        ];

        return view('backend/santri/perbandinganDataSantri', $data);
    }

    /**
     * Convert PDF to Image (first page only)
     * Uses Imagick extension OR Ghostscript binary to convert PDF to JPEG
     */
    private function convertPdfToImage($pdfPath)
    {
        if (!file_exists($pdfPath)) {
            throw new \Exception('File PDF tidak ditemukan');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $pdfPath);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') {
            throw new \Exception('File bukan PDF yang valid');
        }

        // 1. Try Imagick Extension (Best Quality)
        if (extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick();
                $imagick->setResolution(300, 300);
                $imagick->readImage($pdfPath . '[0]');
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompressionQuality(90);
                $imagick->setImageBackgroundColor('white');
                $imagick = $imagick->flattenImages();
                $imageBlob = $imagick->getImageBlob();
                $imagick->clear();
                $imagick->destroy();
                return 'data:image/jpeg;base64,' . base64_encode($imageBlob);
            } catch (\Exception $e) {
                log_message('error', 'Santri: convertPdfToImage - Imagick failed, trying Ghostscript. Error: ' . $e->getMessage());
            }
        }

        // 2. Try Ghostscript Binary (Fallback)
        $gsBin = $this->getGhostscriptBinary();
        if ($gsBin) {
            return $this->convertPdfWithGhostscript($pdfPath, $gsBin);
        }

        throw new \Exception('Server tidak mendukung konversi PDF. Ekstensi Imagick tidak tersedia dan Ghostscript tidak ditemukan.');
    }

    /**
     * Get Ghostscript binary path
     */
    private function getGhostscriptBinary()
    {
        $binaries = ['gswin64c', 'gswin32c', 'gs'];
        
        foreach ($binaries as $binary) {
            $output = [];
            $returnVar = -1;
            $cmd = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "where $binary" : "which $binary";
            exec($cmd, $output, $returnVar);
            
            if ($returnVar === 0) {
                return $binary;
            }
        }

        // Check common default install locations on Windows
        $commonPaths = [
            'C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.03.1\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.03.0\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.02.1\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.02.0\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.01.2\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.01.1\\bin\\gswin64c.exe', 
            'C:\\Program Files\\gs\\gs10.01.0\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.00.0\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs9.56.1\\bin\\gswin64c.exe',
        ];

        foreach ($commonPaths as $path) {
            if (file_exists($path)) {
                return '"' . $path . '"';
            }
        }

        return false;
    }

    /**
     * Convert PDF to Image using Ghostscript CLI
     */
    private function convertPdfWithGhostscript($pdfPath, $gsBin)
    {
        try {
            $outputFile = tempnam(sys_get_temp_dir(), 'gs_conv_') . '.jpg';
            
            $cmd = "$gsBin -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=\"$outputFile\" \"$pdfPath\"";
            $cmd .= ' 2>&1';
            
            $output = [];
            $returnVar = -1;
            exec($cmd, $output, $returnVar);
            
            if ($returnVar !== 0) {
                log_message('error', 'Santri: convertPdfWithGhostscript - Failed. Output: ' . implode("\n", $output));
                if (file_exists($outputFile)) @unlink($outputFile);
                throw new \Exception('Ghostscript conversion failed (Exit Code: ' . $returnVar . ')');
            }
            
            if (!file_exists($outputFile) || filesize($outputFile) === 0) {
                throw new \Exception('Ghostscript did not generate output file');
            }
            
            $imageData = file_get_contents($outputFile);
            $base64 = base64_encode($imageData);
            @unlink($outputFile);
            
            return 'data:image/jpeg;base64,' . $base64;
            
        } catch (\Exception $e) {
            log_message('error', 'Santri: convertPdfWithGhostscript - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    // Update Status Verifikasi
    public function processVerifikasi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $id = $this->request->getPost('idSantri'); // This is the primary key 'id'
        $status = $this->request->getPost('status'); // 1 = Valid, 2 = Revisi

        if (!$id || !in_array($status, ['1', '2'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak valid']);
        }

        try {
            // Map status to ENUM values
            $statusEnum = ($status == '1') ? 'Sudah Diverifikasi' : 'Perlu Perbaikan';

            // Update directly by primary key
            $this->DataSantriBaru->update($id, ['Status' => $statusEnum]);
            $statusLabel = $status == '1' ? 'Valid' : 'Perlu Perbaikan';
            return $this->response->setJSON(['status' => 'success', 'message' => 'Status berhasil diupdate menjadi ' . $statusLabel]);
        } catch (\Exception $e) {
            log_message('error', 'Santri processVerifikasi: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengupdate status']);
        }
    }

    public function updateDataSantri()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $id = $this->request->getPost('idSantri'); // This is the primary key 'id'
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Santri tidak valid']);
        }

        $updateData = [
            'IdKartuKeluarga' => $this->request->getPost('IdKartuKeluarga'),
            'NikSantri' => $this->request->getPost('NikSantri'),
            'NamaSantri' => $this->request->getPost('NamaSantri'),
            'TempatLahirSantri' => $this->request->getPost('TempatLahirSantri'),
            'TanggalLahirSantri' => $this->request->getPost('TanggalLahirSantri'),
            'JenisKelamin' => $this->request->getPost('JenisKelamin'),
            'NamaAyah' => $this->request->getPost('NamaAyah'),
            'NamaIbu' => $this->request->getPost('NamaIbu'),
            'KelurahanDesaSantri' => $this->request->getPost('KelurahanDesaSantri'),
        ];

        // Filter out empty values
        $updateData = array_filter($updateData, fn($v) => $v !== null && $v !== '');

        try {
            // Update directly by primary key
            $this->DataSantriBaru->update($id, $updateData);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (\Exception $e) {
            log_message('error', 'Santri updateDataSantri: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengupdate data']);
        }
    }

    // Update File KK (Save cropped/edited image)
    public function updateFileKk()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $id = $this->request->getPost('idSantri'); // This is the primary key 'id'
        $croppedImageData = $this->request->getPost('croppedImageData');

        if (!$id || !$croppedImageData) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak valid']);
        }

        try {
            // Find santri by primary key
            $santri = $this->DataSantriBaru->find($id);
            if (!$santri) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data santri tidak ditemukan']);
            }

            // Decode base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $croppedImageData, $matches)) {
                $imageData = substr($croppedImageData, strpos($croppedImageData, ',') + 1);
                $imageData = base64_decode($imageData);
                
                if ($imageData === false) {
                    throw new \Exception('Gagal decode data gambar');
                }

                $oldFile = $santri['FileKkSantri'] ?? null;

                // Generate new filename using primary key id
                $newFileName = 'kk_' . $id . '_' . time() . '.jpg';
                $uploadPath = FCPATH . 'uploads/santri/';
                
                // Create directory if not exists
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Save new file
                if (file_put_contents($uploadPath . $newFileName, $imageData)) {
                    // Update database by primary key
                    $this->DataSantriBaru->update($id, ['FileKkSantri' => $newFileName]);
                    
                    // Delete old file if exists and different
                    if ($oldFile && $oldFile !== $newFileName && file_exists($uploadPath . $oldFile)) {
                        @unlink($uploadPath . $oldFile);
                    }

                    return $this->response->setJSON([
                        'status' => 'success', 
                        'message' => 'File KK berhasil disimpan',
                        'newFileUrl' => base_url('uploads/santri/' . $newFileName)
                    ]);
                } else {
                    throw new \Exception('Gagal menyimpan file');
                }
            } else {
                throw new \Exception('Format data gambar tidak valid');
            }
        } catch (\Exception $e) {
            log_message('error', 'Santri updateFileKk: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan file: ' . $e->getMessage()]);
        }
    }

    // Upload New KK File
    public function uploadNewKk()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $id = $this->request->getPost('idSantri'); // Primary key 'id'
        $file = $this->request->getFile('fileKk');

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Santri tidak valid']);
        }

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid']);
        }

        try {
            // Find santri by primary key
            $santri = $this->DataSantriBaru->find($id);
            if (!$santri) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data santri tidak ditemukan']);
            }
            
            $uploadPath = FCPATH . 'uploads/santri/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $oldFile = $santri['FileKkSantri'] ?? null;
            $mimeType = $file->getMimeType();
            $newFileName = 'kk_' . $id . '_' . time();

            if ($mimeType === 'application/pdf') {
                // Convert PDF to JPG
                $tempPath = $file->getTempName();
                $base64Image = $this->convertPdfToImage($tempPath);
                
                // Save the converted image
                $imageData = base64_decode(str_replace('data:image/jpeg;base64,', '', $base64Image));
                $newFileName .= '.jpg';
                file_put_contents($uploadPath . $newFileName, $imageData);
            } else {
                // It's an image, just move it
                $ext = $file->getExtension();
                $newFileName .= '.' . $ext;
                $file->move($uploadPath, $newFileName);
            }

            // Update database by primary key
            $this->DataSantriBaru->update($id, ['FileKkSantri' => $newFileName]);
            
            // Delete old file if exists and different
            if ($oldFile && $oldFile !== $newFileName && file_exists($uploadPath . $oldFile)) {
                @unlink($uploadPath . $oldFile);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'File KK berhasil diupload',
                'newFileUrl' => base_url('uploads/santri/' . $newFileName)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Santri uploadNewKk: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal upload: ' . $e->getMessage()]);
        }
    }

    // Check NIK Uniqueness
    public function checkNikUnique()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $nik = $this->request->getPost('nik');
        $idSantri = $this->request->getPost('idSantri');

        if (!$nik || strlen($nik) !== 16) {
            return $this->response->setJSON(['isUnique' => false, 'message' => 'NIK tidak valid']);
        }

        // Check if NIK exists in database (excluding current santri)
        $builder = $this->DataSantriBaru->builder();
        $builder->where('NikSantri', $nik);
        if ($idSantri) {
            $builder->where('id !=', $idSantri);
        }
        $existing = $builder->get()->getRowArray();

        if ($existing) {
            return $this->response->setJSON([
                'isUnique' => false,
                'usedBy' => $existing['NamaSantri'] ?? 'Santri lain'
            ]);
        }

        return $this->response->setJSON(['isUnique' => true]);
    }
}
