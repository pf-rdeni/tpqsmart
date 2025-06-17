<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriBaruModel;

class Nilai extends BaseController
{
    protected $DataNilai;
    protected $helpFunction;
    protected $DataSantriBaru;
    protected $IdTpq;
    protected $IdKelas;
    protected $IdTahunAjaran;
    protected $settingNilaiModel;

    public function __construct()
    {
        $this->IdTpq = session()->get('IdTpq');
        $this->IdKelas = session()->get('IdKelas');
        $this->IdTahunAjaran = session()->get('IdTahunAjaran');
        $this->DataNilai = new NilaiModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->DataSantriBaru = new SantriBaruModel();
    }

    public function showDetail($IdSantri, $IdSemseter, $Edit = null, $IdJabatan = null)
    {
        // ambil settingan nilai minimun dan maksimal dari session
        $settingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $settingNilai->NilaiAlphabet = session()->get('SettingNilaiAlphabet') ?? false;

        $datanilai = $this->DataNilai->GetDataNilaiDetail($IdSantri, $IdSemseter);

        $data = [
            'page_title' => 'Data Nilai',
            'nilai' => $datanilai,
            'guruPendamping' => $IdJabatan,
            'pageEdit' => $Edit,
            'settingNilai' => $settingNilai,
        ];

        return view('/backend/nilai/nilaiSantriDetail', $data);
    }

    public function showSantriPerKelas($semester = null)
    {
        $IdGuru = session()->get('IdGuru');
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $dataSantri = $this->DataSantriBaru->GetDataSantriPerKelas($IdTahunAjaran, $IdKelas, $IdGuru);

        // ambil settingan nilai minimun dan maksimal dari session
        $settingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $settingNilai->NilaiAlphabet = session()->get('SettingNilaiAlphabet') ?? false;

        // Optimasi pengecekan nilai dengan single query
        $allNilai = $this->DataNilai->getAllNilaiPerKelas($IdTahunAjaran, $semester, $this->IdTpq, $IdKelas);

        // Buat array untuk tracking nilai per santri
        $nilaiStatus = [];
        foreach ($allNilai as $nilai) {
            if ($nilai->Nilai == 0) {
                $nilaiStatus[$nilai->IdSantri] = 0;
            } else if (!isset($nilaiStatus[$nilai->IdSantri])) {
                $nilaiStatus[$nilai->IdSantri] = 1;
            }
        }

        // Set status penilaian untuk setiap santri
        foreach ($dataSantri as $key => $value) {
            $dataSantri[$key]->StatusPenilaian = $nilaiStatus[$value->IdSantri] ?? 0;
        }

        // Tambahkan data kelas tetap "SEMUA KELAS" di awal
        $dataKelas = [0 => 'SEMUA'];
        foreach ($dataSantri as $santri) {
            $dataKelas[$santri->IdKelas] = $santri->NamaKelas;
        }

        $data = [
            'page_title' => 'Data Santri Per Semester ' . $semester,
            'dataSantri' => $dataSantri,
            'dataKelas' => $dataKelas,
            'semester' => $semester,
            'settingNilai' => $settingNilai,
        ];

        return view('backend/santri/santriPerKelas', $data);
    }

    public function showSumaryPersemester($semester = null)
    {
        $datanilai = $this->DataNilai->getDataNilaiPerSemester($this->IdTpq, $this->IdKelas, $this->IdTahunAjaran, $semester);
        foreach ($datanilai->getResult() as $nilai) {
            $dataKelas[$nilai->IdKelas] = $nilai->NamaKelas;
        }
        return view('backend/nilai/nilaiSantriPerSemester', [
            'page_title' => 'Rank Data Nilai Semester ' . $semester,
            'nilai' => $datanilai,
            'dataKelas' => $dataKelas,
            'semester' => $semester
        ]);
    }

    public function showNilaiProfilDetail($IdSantri)
    {
        $datanilai = $this->DataNilai->GetDataNilaiDetail($IdSantri, 1);
        return view('backend/nilai/nilaiSantriDetailPersonal', [
            'page_title' => 'Detail Nilai',
            'nilai' => $datanilai
        ]);
    }

    public function showDetailNilaiSantriPerKelas($semester = null)
    {
        // ambil IdTpq dari session
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        $IdTpq = $this->IdTpq;


        // Buat querry dari tbl_nilai dengan menggabungkan tbl_santri_baru dan tbl_kelas
        $datanilai = $this->DataNilai->getDataNilaiPerKelas($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        foreach ($datanilai as $nilai) {
            $dataKelas[$nilai['IdKelas']] = $nilai['Nama Kelas'];
        }

        $dataMateri = [];
        // Ambil data materi pelajaran berdasarkan kelas
        foreach ($dataKelas as $idKelas => $namaKelas) {
            $dataMateri[$idKelas] = $this->helpFunction->getMateriPelajaranByKelas($IdTpq, $idKelas, $semester);
        }

        // ambil settingan nilai minimun dan maksimal dari session
        $settingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $settingNilai->NilaiAlphabet = session()->get('SettingNilaiAlphabet') ?? false;

        // ambil jika nilai settingan angka arabic dari tbl_tools 
        $settingNilai->NilaiArabic = session()->get('SettingNilaiArabic') ?? false;

        $data = [
            'page_title' => 'Data Nilai Santri Per Kelas',
            'dataKelas' => $dataKelas,
            'dataNilai' => $datanilai,
            'dataMateri' => $dataMateri,
            'settingNilai' => $settingNilai
        ];

        return view('backend/nilai/nilaiSantriDetailPerKelas', $data);
    }

    public function update($Edit = false)
    {
        try {
            //Get IdGuru dari session login
            $IdGuru = session()->get('IdGuru');
            $Id = $this->request->getVar('Id');

            // check jika radio button ada nilai maka nilai di ambil dari radio button
            if ($this->request->getVar('NilaiRadio') !== null) {
                $Nilai = $this->request->getVar('NilaiRadio');
            } else {
                // Jika tidak ada nilai dari radio button, ambil dari inputan teks
                $Nilai = $this->request->getVar('Nilai');
            }
            $this->DataNilai->save([
                'Id' => $Id,
                'IdGuru' => $IdGuru,
                'Nilai' => $Nilai,
            ]);
            // Mengembalikan respons JSON
            return $this->response->setJSON(['status' => 'success', 'newValue' => $Nilai, 'message' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            // Mengembalikan respons JSON dengan kesalahan
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
        
}
