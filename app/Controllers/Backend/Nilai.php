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

    public function __construct()
    {
        $this->DataNilai = new NilaiModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->DataSantriBaru = new SantriBaruModel();
    }

    public function showDetail($IdSantri, $IdSemseter, $Edit = null, $IdJabatan = null)
    {
        $datanilai = $this->DataNilai->GetDataNilaiDetail($IdSantri, $IdSemseter);

        $data = [
            'page_title' => 'Data Nilai',
            'nilai' => $datanilai,
            'guruPendamping' => $IdJabatan,
            'pageEdit' => $Edit
        ];

        return view('/backend/nilai/nilaiSantriDetail', $data);
    }

    public function showSantriPerKelas($semester = null)
    {

        $IdGuru = session()->get('IdGuru');
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

        $data = [
            'page_title' => 'Data Santri Per Semester ' . $semester,
            'dataSantri' => $dataSantri,
            'semester' => $semester
        ];

        return view('backend/santri/santriPerKelas', $data);
    }

    public function showSumaryPersemester($semester = null)
    {
        $datanilai = $this->DataNilai->getDataNilaiPerSemester($semester);
        return view('backend/nilai/nilaiSantriPerSemester', [
            'page_title' => 'Rank Data Nilai ' . $semester,
            'nilai' => $datanilai,
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

    public function update($Edit = false)
    {
        try {
            //Get IdGuru dari session login
            $IdGuru = session()->get('IdGuru');
            $Id = $this->request->getVar('Id');
            $Nilai = $this->request->getVar('Nilai');
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
