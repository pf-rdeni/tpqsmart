<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\EncryptModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriBaruModel;
use App\Models\NilaiModel;
use App\Models\KelasModel;
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

    public function __construct()
    {
        $this->encryptModel = new EncryptModel();
        $this->DataSantri = new SantriModel();
        $this->DataSantriBaru = new SantriBaruModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->nilaiModel = new NilaiModel();
        $this->kelasModel = new KelasModel();
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
        $santriModel = new SantriBaruModel();
        $santri = $santriModel
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.NikSantri', $NikSantri)
            ->first();

        // Set response dengan properti exists
        return $this->response->setJSON([
            'exists' => !empty($santri), // true jika santri ditemukan, false jika tidak
            'data' => $santri // data santri jika ada
        ]);
    }

    // Endpoint untuk mendapatkan detail santri baru
    public function getDetailSantri($IdSantri)
    {
        $santri = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri)
            ->first();
        
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

        // Mulai membangun query dasar
        $query = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        // Jika IdTpq tidak ada, ambil semua data santri dengan pengurutan khusus
        if ($IdTpq == null) {
            $santri = $query
                ->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC')
                ->findAll();
        } else {
            // Jika IdTpq ada, terapkan filter IdTpq dan cek filter IdKelas
            $IdKelas = session()->get('IdKelas');

            // Terapkan filter IdKelas jika ada
            if ($IdKelas !== null) {
                // check jika IdKelas adalah array, maka filter where in
                if (is_array($IdKelas)) {
                    $query->whereIn('tbl_santri_baru.IdKelas', $IdKelas);
                } else {
                    $query->where('tbl_santri_baru.IdKelas', $IdKelas);
                }
            }

            // Tambahkan filter IdTpq dan pengurutan untuk kasus IdTpq tidak null
            $santri = $query
                ->where('tbl_santri_baru.IdTpq', $IdTpq)
                ->orderBy('tbl_santri_baru.IdKelas', 'ASC')
                ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->orderBy('tbl_santri_baru.Status', 'DESC')
                ->findAll();
        }

        $tpq = $this->helpFunction->getDataTpq();
        usort($tpq, function ($a, $b) {
            return strcmp($a['NamaTpq'], $b['NamaTpq']);
        });

        // Tambahkan query untuk menghitung jumlah santri per TPQ
        $santriPerTpq = $this->DataSantriBaru
            ->select('tbl_tpq.IdTpq, COUNT(tbl_santri_baru.IdSantri) as JumlahSantri')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'right')
            ->groupBy('tbl_tpq.IdTpq')
            ->findAll();

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
        $IdTpq = session()->get('IdTpq');

        $query = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');

        if ($IdTpq == null) {
            $santri = $query
                ->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC')
                ->findAll();
        } else {
            $IdKelas = session()->get('IdKelas');
            if ($IdKelas !== null) {
                if (is_array($IdKelas)) {
                    $query->whereIn('tbl_santri_baru.IdKelas', $IdKelas);
                } else {
                    $query->where('tbl_santri_baru.IdKelas', $IdKelas);
                }
            }

            $santri = $query
                ->where('tbl_santri_baru.IdTpq', $IdTpq)
                ->orderBy('tbl_santri_baru.IdKelas', 'ASC')
                ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
                ->orderBy('tbl_santri_baru.Status', 'DESC')
                ->findAll();
        }

        $data = [
            'page_title' => 'Profil Data Santri',
            'dataSantri' => $santri,
        ];
        return view('backend/santri/listDataProfilSantri', $data);
    }

    // Page: Profil Data Santri - Detail
    public function profilDetailSantri($IdSantri)
    {
        $santri = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa as KelurahanDesaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri)
            ->first();

        if (!$santri) {
            return redirect()->back()->with('error', 'Data santri tidak ditemukan');
        }

        $data = [
            'page_title' => 'Profil Detail Santri',
            'dataSantri' => $santri,
        ];
        return view('backend/santri/profilDatailSantri', $data);
    }

    public function showAturSantriBaru()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        // ambil IdKelas dari session
        $IdKelas = session()->get('IdKelas');

        // Mulai membangun query dengan builder pattern
        $builder = $this->DataSantriBaru
            ->select([
                'tbl_santri_baru.*',
                'tbl_kelas.NamaKelas',
                'tbl_tpq.NamaTpq',
                'tbl_tpq.KelurahanDesa'
            ])
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq');
        if ($IdTpq) {
            $builder->where('tbl_santri_baru.IdTpq', $IdTpq);
        }

        // Tambahkan filter Active=1 jika user adalah Guru
        if (in_groups('Guru')) {
            $builder->where('tbl_santri_baru.Active', 1);
        }

        // Terapkan filter IdKelas jika ada
        if ($IdKelas !== null) {
            if (is_array($IdKelas)) {
                $builder->whereIn('tbl_santri_baru.IdKelas', $IdKelas);
            } else {
                $builder->where('tbl_santri_baru.IdKelas', $IdKelas);
            }
        }

        // Tambahkan pengurutan
        $santri = $builder
            ->orderBy('tbl_santri_baru.IdKelas', 'ASC')
            ->orderBy('tbl_santri_baru.NamaSantri', 'ASC')
            ->orderBy('tbl_santri_baru.Status', 'DESC')
            ->findAll();

        $data = [
            'page_title' => 'Data Santri',
            'dataSantri' => $santri
        ];
        return view('backend/santri/aturSantriBaru', $data);
    }

    public function showSantriEmis()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        // jika IdTpq tidak ada, maka tampilkan semua data santri
        if ($IdTpq == null) {
            $santri = $this->DataSantriBaru
                ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
                ->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC')
                ->findAll();
        } else {
            $santri = $this->DataSantriBaru
                ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
                ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
                ->where('tbl_santri_baru.IdTpq', $IdTpq)
                ->orderBy('tbl_santri_baru.Status', 'DESC')
                ->orderBy('tbl_santri_baru.updated_at', 'DESC')
                ->findAll();
        }

        $tpq = $this->helpFunction->getDataTpq();
        usort($tpq, function ($a, $b) {
            return strcmp($a['NamaTpq'], $b['NamaTpq']);
        });

        // Tambahkan query untuk menghitung jumlah santri per TPQ
        $santriPerTpq = $this->DataSantriBaru
            ->select('tbl_tpq.IdTpq, COUNT(tbl_santri_baru.IdSantri) as JumlahSantri')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'right')
            ->groupBy('tbl_tpq.IdTpq')
            ->findAll();

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
        $santri = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa as KelurahanDesaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri)
            ->first();

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
        // Ambil data santri
        $santri = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri)
            ->first();

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

            // 2. Update status kelas lama di tbl_kelas_santri
            $db->table('tbl_kelas_santri')
                ->where('IdSantri', $IdSantri)
                ->where('IdTahunAjaran', $currentTahunAjaran)
                ->where('Status', 1)
                ->set(['Status' => 0])
                ->update();

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
        // Ambil data santri
        $santri = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri)
            ->first();

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
                'message' => 'TPQ santri berhasil diubah. Status Active diubah menjadi 0.'
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
        // ambil id tpq dari session
        if ($IdTpq == null)
            $IdTpq = session()->get('IdTpq');
        
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
            'dataSantriAll' => $santriAll,
            'dataSantriTK' => $santriPerKelas['TK'],
            'dataSantriTKA' => $santriPerKelas['TKA'],
            'dataSantriTKB' => $santriPerKelas['TKB'],
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

    public function showKontakSantri($IdSantri = null)
    {

        $data = [
            'page_title' => 'Kontak Santri',
            'santri' => $datasantri = ""
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

        $dataSantri = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq')
            ->where('tbl_santri_baru.IdSantri', $IdSantri)
            ->first();

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

            $data = [
                'printNamaTpq' => $dataSantri['NamaTpq'],
                'printNamaKelas' => $dataSantri['NamaKelas'],
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
                'printKepalaTpq' => $tpqRow['KepalaSekolah'] ?? '',
                // Data TPQ untuk kop lembaga
                'printAlamatTpq' => $tpqRow['AlamatTpq'] ?? '',
                'printKelurahanDesaTpq' => $tpqRow['KelurahanDesa'] ?? '',
                'printKecamatanTpq' => $tpqRow['Kecamatan'] ?? 'Seri Kuala Lobam',
                'printKabupatenKotaTpq' => $tpqRow['KabupatenKota'] ?? 'Bintan',
                'printProvinsiTpq' => $tpqRow['Provinsi'] ?? 'Kepulauan Riau',
                'printKodePosTpq' => $tpqRow['KodePos'] ?? '29152',
                'printTelpTpq' => $tpqRow['NoHp'] ?? '081234567890',
                'printEmailTpq' => $tpqRow['Email'] ?? $tpqRow['NamaTpq'] . '@TpqSmart.simpedis.com',
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
            // Info : 0 = baru daftar, 1 = aktif, 2 = no active
            $active = $json->active == 0 ? 2 : $json->active;

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

        // Ambil data santri dengan join ke tabel kelas dan TPQ
        $santri = $this->DataSantriBaru
            ->select('tbl_santri_baru.*, tbl_kelas.NamaKelas, tbl_tpq.NamaTpq')
            ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_santri_baru.IdKelas', 'left')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left')
            ->where('tbl_santri_baru.IdSantri', $IdSantri)
            ->first();

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

            log_message('info', '[processDeleteSantri] Successfully deleted santri IdSantri: ' . $IdSantri);
            return $this->response->setJSON(['success' => true, 'message' => 'Data santri berhasil dihapus permanen']);
        } catch (\Exception $e) {
            log_message('error', '[processDeleteSantri] Error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data santri: ' . $e->getMessage()]);
        }
    }
}
