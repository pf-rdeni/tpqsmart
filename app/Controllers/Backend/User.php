<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function index($filter = null)
    {
        $userDataSantri = $this->userModel
            ->select('users.id, users.active, users.username, tbl_santri_baru.NamaSantri as Nama, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa, "Santri" as kategori')
            ->join('tbl_santri_baru', 'users.nik = tbl_santri_baru.NikSantri', 'inner')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'inner')
            ->findAll();
        $userDataGuru = $this->userModel
            ->select('users.id, users.active, users.username, tbl_guru.Nama as Nama, tbl_tpq.NamaTpq, tbl_tpq.KelurahanDesa, "Guru" as kategori')
            ->join('tbl_guru', 'users.nik = tbl_guru.IdGuru', 'inner')
            ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_guru.IdTpq', 'inner')
            ->findAll();

        $userData = array_merge($userDataSantri, $userDataGuru);

        $data = [
            'page_title' => 'Data User',
            'userData' => $userData,
        ];
        return view('backend/user/index', $data);
    }

    public function create()
    {
        return view('backend/user/create');
    }

    public function edit($id)
    {
        return view('backend/user/edit');
    }

    public function delete($id)
    {
        try {
            // $result = $this->userModel->delete($id);
            $result = true;
            if (!$result) {
                throw new \Exception('Gagal menghapus data pengguna');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pengguna berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }
}
