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
        $SettingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $SettingNilai->NilaiAlphabet = session()->get('SettingNilaiAlphabet') ?? false;

        $datanilai = $this->DataNilai->GetDataNilaiDetail($IdSantri, $IdSemseter);

        $data = [
            'page_title' => 'Data Nilai',
            'nilai' => $datanilai,
            'guruPendamping' => $IdJabatan,
            'pageEdit' => $Edit,
            'settingNilai' => $SettingNilai,
        ];

        return view('/backend/nilai/nilaiSantriDetail', $data);
    }

    public function showSantriPerKelas($semester = null)
    {
        $IdGuru = session()->get('IdGuru');
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $dataSantri = $this->DataSantriBaru->GetDataSantriPerKelas($IdTahunAjaran, $IdKelas, $IdGuru);

        // Check IdSantri yang ada di data $dataSantri ke tbl_nilai filter by IdTahunAjaran dan Semester apakah nilai untuk semua IdMateri sudah semua atau belum jika belum maka buat status StatusPenilian = 0 
        foreach ($dataSantri as $key => $value) {
            $dataNilai = $this->DataNilai->getDataNilaiPerSantri($value->IdSantri, $semester);
            $dataSantri[$key]->StatusPenilaian = 1;
            foreach ($dataNilai as $nilai) {
                if ($nilai->Nilai == 0) {
                    $dataSantri[$key]->StatusPenilaian = 0;
                    break;
                }
            }
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
            'semester' => $semester
        ];

        return view('backend/santri/santriPerKelas', $data);
    }

    public function showSumaryPersemester($semester = null)
    {
        $datanilai = $this->DataNilai->getDataNilaiPerSemester($this->IdTpq, $this->IdKelas, $this->IdTahunAjaran, $semester);
        $dataKelas = [0 => 'SEMUA'];
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
        
        $dataKelas = [0 => 'SEMUA'];
        foreach ($datanilai as $nilai) {
            $dataKelas[$nilai['IdKelas']] = $nilai['Nama Kelas'];
        }

        $dataMateri = [];
        // ambill materi pelajaran untuk semua kelas jika kelas 'SEMUA'
        if (isset($dataKelas[0]) && $dataKelas[0] === 'SEMUA') {
            $dataMateri[0] = $this->helpFunction->getMateriPelajaranByKelas($IdTpq, $IdKelas, $semester);
        }

        foreach ($dataKelas as $idKelas => $namaKelas) {
            if ($idKelas !== 0) { // Skip jika bukan 'SEMUA'
                $dataMateri[$idKelas] = $this->helpFunction->getMateriPelajaranByKelas($IdTpq, $idKelas, $semester);
            }
        }

        return view('backend/nilai/nilaiSantriDetailPerKelas', [
            'page_title' => 'Detail Nilai Santri Semester ' . $semester,
            'dataNilai' => $datanilai,
            'dataKelas' => $dataKelas,
            'dataMateri' => $dataMateri,
        ]);
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
