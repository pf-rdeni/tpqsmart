<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\EncryptModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriBaruModel;

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
        $IdSantri = "";
        // Ambil tahun saat ini
        $tahunSekarang = date('Y');
        
        // Cari IdSantri terakhir dengan awalan tahun ini
        $lastSantri = $this->DataSantriBaru->like('IdSantri', $tahunSekarang, 'after')
                                          ->orderBy('IdSantri', 'DESC')
                                          ->first();
        
        if ($lastSantri) {
            // Jika sudah ada santri tahun ini, ambil nomor urut terakhir dan tambah 1
            $lastNumber = intval(substr($lastSantri['IdSantri'], 4));
            $newNumber = $lastNumber + 1;
            $IdSantri= $tahunSekarang . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada santri tahun ini, mulai dari 0001
            $IdSantri = $tahunSekarang . '0001';
        }

        // Fungsi untuk menangani upload file dengan nama yang menyertakan IdSantri
        $handleUpload = function ($file, $prefix) use ($IdSantri) {
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = strtoupper($prefix . '_'. $IdSantri . '.' . $file->getExtension());
                $targetPath = ROOTPATH . 'public/uploads/santri/' . $newName;

                // Hapus file lama jika ada
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }

                $file->move(ROOTPATH . 'public/uploads/santri', $newName, true); // true untuk overwrite
                return $newName;
            }
            return null;
        };

        // Handle upload untuk setiap file dan simpan nama file ke variabel
        $photoProfilName = $handleUpload($this->request->getFile('PhotoProfil'), 'PROFILE');
        $namaFileKIP = $handleUpload($this->request->getFile('FileKIP'), 'KIP'); 
        $namaFileKkSantri = $handleUpload($this->request->getFile('FileKkSantri'), 'KK_SANTRI');
        $namaFileKkAyah = $handleUpload($this->request->getFile('FileKKAyah'), 'KK_AYAH');
        $namaFileKkIbu = $handleUpload($this->request->getFile('FileKKIbu'), 'KK_IBU');
        // Update data yang akan disimpan dengan nama file yang baru
        $data['PhotoProfil'] = $photoProfilName;
        $data['FileKIP'] = $namaFileKIP;
        $data['FileKkSantri'] = $namaFileKkSantri; 
        $data['FileKkAyah'] = $namaFileKkAyah;
        $data['FileKkIbu'] = $namaFileKkIbu;

        // Handling checkbok kksamaayah sama dengan santri maka file kk ayah sama dengan file kk santri
        if($this->request->getPost('KKSamaAyah') == 'on')
            $namaFileKkAyah = $namaFileKkSantri;
        if($this->request->getPost('KKSamaDenganAyah') == 'on')
            $namaFileKkIbu = $namaFileKkSantri;


        // Data Wali Santri
        $statusWali = $this->request->getPost('StatusWali');

        if($statusWali == 'Ayah Kandung' || $statusWali == 'Ibu Kandung')
        {
            $orangTua = ($statusWali == 'Ayah Kandung') ? 'Ayah' : 'Ibu';
            // variable untuk data wali santri
            $namaWali = $this->request->getPost('Nama' . $orangTua);
            $statusWali = $this->request->getPost('Status' . $orangTua);  
            $nikWali = $this->request->getPost('Nik' . $orangTua);
            $kewarganegaraanWali = $this->request->getPost('Kewarganegaraan' . $orangTua);
            $tempatLahirWali = $this->request->getPost('TempatLahir' . $orangTua);
            $tanggalLahirWali = $this->request->getPost('TanggalLahir' . $orangTua);
            $pendidikanWali = $this->request->getPost('Pendidikan' . $orangTua);
            $pekerjaanUtamaWali = $this->request->getPost('PekerjaanUtama' . $orangTua);
            $penghasilanUtamaWali = $this->request->getPost('PenghasilanUtama' . $orangTua);
            $noHpWali = $this->request->getPost('NoHp' . $orangTua);
        }
        else{
            $statusWali = $this->request->getPost('StatusWali');
            $namaWali = $this->request->getPost('NamaWali');
            $nikWali = $this->request->getPost('NikWali');
            $kewarganegaraanWali = $this->request->getPost('KewarganegaraanWali');
            $tempatLahirWali = $this->request->getPost('TempatLahirWali');
            $tanggalLahirWali = $this->request->getPost('TanggalLahirWali');
            $pendidikanWali = $this->request->getPost('PendidikanWali');
            $pekerjaanUtamaWali = $this->request->getPost('PekerjaanUtamaWali');
            $penghasilanUtamaWali = $this->request->getPost('PenghasilanUtamaWali');
            $noHpWali = $this->request->getPost('NoHpWali');
        }

        // Alamat Tempat Tinggal Ayah Ibu dna santri

        // Variable untuk data alamat ayah
        $statusKepemilikanRumahAyah = "";
        $provinsiAyah = "";
        $kabupatenKotaAyah = "";
        $kecamatanAyah = "";
        $kelurahanDesaAyah = "";
        $rwAyah = "";
        $rtAyah = "";
        $kodePosAyah = "";
        $alamatAyah = "";

        // Variable untuk data alamat ibu
        $statusKepemilikanRumahIbu = "";
        $provinsiIbu = "";
        $kabupatenKotaIbu = "";
        $kecamatanIbu = "";
        $kelurahanDesaIbu = "";
        $rwIbu = "";
        $rtIbu = "";
        $kodePosIbu = "";
        $alamatIbu = "";

        // Tinggal Diluar Negeri
        $tinggalDiluarNegeriAyah = $this->request->getPost('TinggalDiluarNegeriAyah');
        if ($tinggalDiluarNegeriAyah == 'on')
        {
            $alamatAyah = $this->request->getPost('AlamatAyah');
        }
        else if($this->request->getPost('StatusAyah') == 'Masih Hidup'){
            // Variable untuk data alamat ayah
            $statusKepemilikanRumahAyah = $this->request->getPost('StatusKepemilikanRumahAyah');
            $provinsiAyah = $this->request->getPost('ProvinsiAyah');
            $kabupatenKotaAyah = $this->request->getPost('KabupatenKotaAyah');
            $kecamatanAyah = $this->request->getPost('KecamatanAyah');
            $kelurahanDesaAyah = $this->request->getPost('KelurahanDesaAyah');
            $rtAyah = $this->request->getPost('RTAyah');
            $rwAyah = $this->request->getPost('RWAyah');
            $alamatAyah = $this->request->getPost('AlamatAyah');
            $kodePosAyah = $this->request->getPost('KodePosAyah');
        }
        
        
        // handling tempat tinggal ibu jika sama dengan ayah kandung maka alamat ibu sama dengan alamat ayah
        if($this->request->getPost('AlamatIbuSamaDenganAyah') == 'on')
        {
            if($tinggalDiluarNegeriAyah == 'on')
                $tinggalDiluarNegeriIbu = 'on'; 
            else
            {
                $tinggalDiluarNegeriIbu = 'off';
                $statusKepemilikanRumahIbu = $statusKepemilikanRumahAyah;   
                $alamatIbu = $alamatAyah;
                $provinsiIbu = $provinsiAyah;
                $kabupatenKotaIbu = $kabupatenKotaAyah;
                $kecamatanIbu = $kecamatanAyah;
                $kelurahanDesaIbu = $kelurahanDesaAyah;
                $rtIbu = $rtAyah;
                $rwIbu = $rwAyah;
                $kodePosIbu = $kodePosAyah;
            }
        }
        else
        {
            $tinggalDiluarNegeriIbu = $this->request->getPost('TinggalDiluarNegeriIbu');
            if($tinggalDiluarNegeriIbu == 'on')
                $alamatIbu = $this->request->getPost('AlamatIbu');
            else
            {
                $tinggalDiluarNegeriIbu = 'off';
                $statusKepemilikanRumahIbu = $this->request->getPost('StatusKepemilikanRumahIbu');  
                $alamatIbu = $this->request->getPost('AlamatIbu');
                $provinsiIbu = $this->request->getPost('ProvinsiIbu');
                $kabupatenKotaIbu = $this->request->getPost('KabupatenKotaIbu');
                $kecamatanIbu = $this->request->getPost('KecamatanIbu');
                $kelurahanDesaIbu = $this->request->getPost('KelurahanDesaIbu');
                $rtIbu = $this->request->getPost('RTIbu');
                $rwIbu = $this->request->getPost('RWIbu');
                $kodePosIbu = $this->request->getPost('KodePosIbu');    
            }       
        }

        // buatkan data alamat santri jika sama dengan ayah kandung maka alamat santri sama dengan alamat ayah atau sama dengan alamat ibu
        // Set alamat santri berdasarkan status tempat tinggal
        // Ambil status tempat tinggal santri dari form
        $statusTinggal = $this->request->getPost('StatusTempatTinggal');

        // Cek apakah santri tinggal dengan orang tua
        if($statusTinggal == 'Tinggal dengan Ayah Kandung' || $statusTinggal == 'Tinggal dengan Ibu Kandung') {
            
            // Tentukan apakah tinggal dengan ayah atau ibu
            $orangTua = ($statusTinggal == 'Tinggal dengan Ayah Kandung') ? 'Ayah' : 'Ibu';

            // Salin data alamat dari orang tua ke santri
            $alamatSantri= $this->request->getPost('Alamat' . $orangTua);
            $provinsiSantri = $this->request->getPost('Provinsi' . $orangTua); 
            $kabupatenKotaSantri = $this->request->getPost('KabupatenKota' . $orangTua);
            $kecamatanSantri = $this->request->getPost('Kecamatan' . $orangTua);
            $kelurahanDesaSantri = $this->request->getPost('KelurahanDesa' . $orangTua);
            $rtSantri = $this->request->getPost('RT' . $orangTua);
            $rwSantri = $this->request->getPost('RW' . $orangTua);
            $kodePosSantri = $this->request->getPost('KodePos' . $orangTua);
        }
        // Jika tidak tinggal dengan orang tua, ambil alamat santri dari form
        else {
            $alamatSantri = $this->request->getPost('AlamatSantri');        
            $provinsiSantri = $this->request->getPost('ProvinsiSantri');
            $kabupatenKotaSantri = $this->request->getPost('KabupatenKotaSantri'); 
            $kecamatanSantri = $this->request->getPost('KecamatanSantri');
            $kelurahanDesaSantri = $this->request->getPost('KelurahanDesaSantri');
            $rtSantri = $this->request->getPost('RTSantri');
            $rwSantri = $this->request->getPost('RWSantri');
            $kodePosSantri = $this->request->getPost('KodePosSantri');
        }
   
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
            'StatusWali' => $statusWali,
            'NamaWali' => $namaWali,
            'NikWali' => $nikWali, 
            'KewarganegaraanWali' => $kewarganegaraanWali,
            'TempatLahirWali' => $tempatLahirWali,
            'TanggalLahirWali' => $tanggalLahirWali,
            'PendidikanWali' => $pendidikanWali,
            'PekerjaanUtamaWali' => $pekerjaanUtamaWali,
            'PenghasilanUtamaWali' => $penghasilanUtamaWali,
            'NoHpWali' => $noHpWali,

            // Data Alamat Ayah
            'TinggalDiluarNegeriAyah' => $tinggalDiluarNegeriAyah,  
            'StatusKepemilikanRumahAyah' => $statusKepemilikanRumahAyah,
            'ProvinsiAyah' => $provinsiAyah,
            'KabupatenKotaAyah' => $kabupatenKotaAyah,
            'KecamatanAyah' => $kecamatanAyah,
            'KelurahanDesaAyah' => $kelurahanDesaAyah,
            'RtAyah' => $rtAyah,
            'RwAyah' => $rwAyah,
            'AlamatAyah' => $alamatAyah,
            'KodePosAyah' => $kodePosAyah,

            // Data Alamat Ibu
            'TinggalDiluarNegeriIbu' => $tinggalDiluarNegeriIbu,  
            'StatusKepemilikanRumahIbu' => $statusKepemilikanRumahIbu,
            'ProvinsiIbu' => $provinsiIbu,
            'KabupatenKotaIbu' => $kabupatenKotaIbu,
            'KecamatanIbu' => $kecamatanIbu,
            'KelurahanDesaIbu' => $kelurahanDesaIbu,
            'RtIbu' => $rtIbu,
            'RwIbu' => $rwIbu,
            'AlamatIbu' => $alamatIbu,
            'KodePosIbu' => $kodePosIbu,

            // Data Alamat Santri
            'WaliSantri' => $this->request->getPost('WaliSantri'),
            'ProvinsiSantri' => $provinsiSantri,
            'KabupatenKotaSantri' => $kabupatenKotaSantri,
            'KecamatanSantri' => $kecamatanSantri,
            'KelurahanDesaSantri' => $kelurahanDesaSantri,
            'RtSantri' => $rtSantri,
            'RtSantri' => $rwSantri,
            'AlamatSantri' => $alamatSantri,
            'KodePosSantri' => $kodePosSantri,
            'JarakTempuhSantri' => $this->request->getPost('JarakTempuhSantri'),
            'TransportasiSantri' => $this->request->getPost('TransportasiSantri'),
            'WaktuTempuhSantri' => $this->request->getPost('WaktuTempuhSantri'),
            'TitikKoordinatSantri' => $this->request->getPost('TitikKoordinatSantri'),
        ];
        // Ubah semua value dalam array $data menjadi uppercase
        $data = array_map(function($value) {
            // Hanya ubah jika value adalah string dan tidak null
            return is_string($value) ? strtoupper($value) : $value;
        }, $data);
        // Ambil data dari form
        // Simpan data ke database
        $result = $this->DataSantriBaru->insert($data);

        if ($result) {
            return redirect()->to('backend/santri/createEmisStep')->with('success', 'Data santri berhasil disimpan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data santri');
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
        $data = [
            'page_title' => 'Data Santri Baru',
            'dataSantri' => $santri
        ];
        return view('backend/santri/listSantriBaru', $data);
    }

    public function showSantriPerKelas($encryptedIdGuru = null)
    {
        if($encryptedIdGuru !== null)
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
            'santri' => $datasantri=""
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


}