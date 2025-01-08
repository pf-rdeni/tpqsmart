<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\HelpFunctionModel;

class Nilai extends BaseController
{
    protected $DataNilai;
    protected $helpFunction;

    public function __construct()
    {
        $this->DataNilai = new NilaiModel();
        $this->helpFunction = new HelpFunctionModel();
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

    public function showSumaryPersemester()
    {
        $datanilai = $this->DataNilai->getDataNilaiPerSemester();
        return view('backend/nilai/nilaiSantriPerSemester', [
            'page_title' => 'Rank Data Nilai',
            'nilai' => $datanilai
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
