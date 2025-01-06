<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\HelpFunctionModel;
use Myth\Auth\Password;

class User extends BaseController
{
    protected $userModel;
    protected $helpFunction;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->helpFunction = new HelpFunctionModel();
    }
    public function index()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        $userData = $this->userModel->getAllUserData();

        // Mengecek setiap user data untuk password default
        foreach ($userData as &$user) {
            // Verifikasi apakah password_hash cocok dengan 'TpqSmart123'
            if (Password::verify('TpqSmart123', $user['password_hash'])) {
                $user['password_hash'] = 'TpqSmart123';
            }
            // Jika tidak cocok, biarkan password_hash terenkripsi
            else {
                $user['password_hash'] = '********';
            }
        }

        $dataGuru = $this->helpFunction->getDataGuru(IdTpq: $IdTpq);

        $dataAutGroups = $this->helpFunction->getDataAuthGoups();

        // jika IdTpq ada $dataAutGroup filter hanya diambil 'name' => 'Guru'
        if ($IdTpq) {
            $dataAutGroups = array_filter($dataAutGroups, function ($group) {
                return $group['name'] == 'Guru';
            });
        }

        $data = [
            'page_title' => 'Data User',
            'userData' => $userData,
            'dataGuru' => $dataGuru,
            'dataAuthGroups' => $dataAutGroups
        ];
        return view('backend/user/index', $data);
    }

    public function checkUsername($username)
    {
        $exists = $this->helpFunction->getUserByUsername($username) > 0;
        return $this->response->setJSON(['exists' => $exists]);
    }

    public function checkUserIdNikGuru($idNik)
    {
        $exists = $this->helpFunction->getGuruByIdNik($idNik) > 0;
        return $this->response->setJSON(['exists' => $exists]);
    }

    public function create()
    {

        $groupsId = $this->request->getPost('IdAuthGroup');
        $idNik = $this->request->getPost('IdNikGuru');
        $fullName = $this->helpFunction->getNamaGuruByIdNik($idNik);

        $data = [
            'username' => $this->request->getPost('username'),
            'fullname' => $fullName,
            'email' => $this->request->getPost('username') . '@tpqsmart.simpedis.com',
            'password_hash' => Password::hash($this->request->getPost('password')),
            'nik' => $idNik,
            'active' => 1
        ];

        try {
            $returnUserId = $this->userModel->store($data);

            $groupData = [
                'group_id' => $groupsId,
                'user_id' => $returnUserId
            ];

            $this->helpFunction->insertAuthGroupsUsers($groupData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pengguna berhasil ditambahkan',
                'user_id' => $returnUserId
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan data pengguna: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        return view('backend/user/edit');
    }

    public function delete($id)
    {
        try {
            $result = $this->userModel->delete($id);
            if (!$result) {
                throw new \Exception('Gagal menghapus data pengguna');
            }

            $this->helpFunction->deleteAuthGroupsUsers($id);

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

    public function get($id)
    {
        $user = $this->userModel->getUser($id);
        return $this->response->setJSON([
            'success' => true,
            'user' => $user
        ]);
    }

    // buat fungsi update user
    public function update()
    {
        // Mengambil data dari POST
        $id = $this->request->getPost('id');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');


        // Validasi data
        if (!$id || !$username || !$password) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        $data = [
            'username' => $username,
            'password_hash' => Password::hash($password),
        ];

        try {
            $this->userModel->updateUser($data, $id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pengguna berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah data pengguna: ' . $e->getMessage()
            ]);
        }
    }
    // buat fungsi update status user
    public function updateStatus()
    {
        // Mengambil raw input JSON
        $jsonData = $this->request->getJSON(true);

        // Mengambil nilai dari JSON
        $id = $jsonData['id'] ?? null;
        $status = $jsonData['active'] ?? null;

        // Validasi data
        if ($id === null || $status === null) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        $data = [
            'active' => $status
        ];

        try {
            $this->userModel->updateUser($data, $id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status user berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah status user: ' . $e->getMessage()
            ]);
        }
    }
}
