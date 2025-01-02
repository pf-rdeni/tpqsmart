<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\GuruKelasModel;
use App\Models\HelpFunctionModel;

class GuruKelas extends BaseController
{
    protected $guruKelasModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->guruKelasModel = new GuruKelasModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function show()
    {
        $IdTpq = session()->get('IdTpq');
        $data = [
            'page_title' => 'Daftar Guru Kelas',
            'guruKelas' => $this->helpFunction->getDataGuruKelas(IdTpq: $IdTpq),
            'kelas' => $this->helpFunction->getDataKelas(),
            'dataTpq' => $IdTpq
        ];

        return view('backend/kelas/guruKelas', $data);
    }

    public function store()
    {
        $id = $this->request->getPost('Id');
        $idKelas = $this->request->getPost('IdKelas');
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $idJabatan = $this->request->getPost('IdJabatan');

        // Cek apakah kombinasi IdKelas, IdTahunAjaran dan IdJabatan=3 wali kelas sudah ada
        if ($idJabatan == 3) {
            $existing = $this->guruKelasModel
                ->select('tbl_guru_kelas.*, tbl_guru.Nama as NamaGuru, tbl_kelas.NamaKelas')
                ->join('tbl_guru', 'tbl_guru.IdGuru = tbl_guru_kelas.IdGuru')
                ->join('tbl_kelas', 'tbl_kelas.IdKelas = tbl_guru_kelas.IdKelas')
                ->where([
                    'tbl_guru_kelas.IdKelas' => $idKelas,
                    'tbl_guru_kelas.IdTahunAjaran' => $idTahunAjaran,
                    'tbl_guru_kelas.IdJabatan' => 3
                ])->first();

            // Jika data existing ditemukan dan ini adalah data baru (tidak ada $id)
            // ATAU jika ini adalah update tapi untuk record yang berbeda
            if ($existing && ($id === null || $id != $existing['Id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Wali Kelas untuk kelas {$existing['NamaKelas']} sudah ditempati oleh {$existing['NamaGuru']}!"
                ]);
            }
        }
        
        $data = [
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdKelas' => $idKelas,
            'IdGuru' => $this->request->getPost('IdGuru'),
            'IdTahunAjaran' => $idTahunAjaran,
            'IdJabatan' => $idJabatan,
        ];
        if ($id) 
             $data['Id'] = $id;
        
        $this->guruKelasModel->save($data);
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function delete($id)
    {
        try {
            $this->guruKelasModel->delete($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data guru kelas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data guru kelas: ' . $e->getMessage()
            ]);
        }
    }
}
