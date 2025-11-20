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
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->db = \Config\Database::connect();
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

        // Cek apakah user yang login adalah Admin
        $isAdmin = in_groups('Admin');

        $data = [
            'page_title' => 'Data User',
            'userData' => $userData,
            'dataGuru' => $dataGuru,
            'dataAuthGroups' => $dataAutGroups,
            'isAdmin' => $isAdmin
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

        // Cek apakah user yang login adalah Admin
        $isAdmin = in_groups('Admin');

        // Jika Admin, fullname bisa dari input manual atau dari guru
        // Jika bukan Admin, wajib pilih guru
        if ($isAdmin) {
            // Admin bisa input fullname manual atau pilih dari guru
            $fullNameManual = $this->request->getPost('fullname_manual');
            if (!empty($idNik)) {
                // Jika pilih guru, ambil nama dari guru
                $fullName = $this->helpFunction->getNamaGuruByIdNik($idNik);
            } elseif (!empty($fullNameManual)) {
                // Jika input manual, gunakan input manual
                $fullName = $fullNameManual;
            } else {
                // Jika kosong semua, gunakan username sebagai fallback
                $fullName = $this->request->getPost('username');
            }
        } else {
            // Bukan Admin, wajib pilih guru
            if (empty($idNik)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Nama Guru harus dipilih'
                ]);
            }
            $fullName = $this->helpFunction->getNamaGuruByIdNik($idNik);
        }

        // Validasi dan pastikan group_id sesuai untuk Panitia
        if (empty($groupsId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Group harus dipilih'
            ]);
        }

        // Ambil informasi group yang dipilih untuk validasi
        $selectedGroup = $this->helpFunction->getDataAuthGoups($groupsId);
        if (!empty($selectedGroup)) {
            $groupName = $selectedGroup[0]['name'] ?? '';

            // Pastikan group Panitia menggunakan group_id = 6
            if ($groupName === 'Panitia') {
                $groupsId = 6;
            }
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'fullname' => $fullName,
            'email' => $this->request->getPost('username') . '@tpqsmart.simpedis.com',
            'password_hash' => Password::hash($this->request->getPost('password')),
            'nik' => $idNik ?: null, // Bisa null jika Admin tidak pilih guru
            'active' => 1
        ];

        try {
            $returnUserId = $this->userModel->store($data);

            $groupData = [
                'group_id' => (int)$groupsId, // Pastikan integer
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

    /**
     * Halaman pengaturan auth_group
     */
    public function authGroup()
    {
        $authGroups = $this->helpFunction->getDataAuthGoups();

        $data = [
            'page_title' => 'Pengaturan Auth Group',
            'auth_groups' => $authGroups
        ];

        return view('backend/user/authGroup', $data);
    }

    /**
     * Create auth_group
     */
    public function createAuthGroup()
    {
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        if (empty($name)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama group tidak boleh kosong'
            ]);
        }

        // Cek apakah nama group sudah ada
        $existing = $this->db->table('auth_groups')
            ->where('name', $name)
            ->get()
            ->getRowArray();

        if ($existing) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama group sudah ada'
            ]);
        }

        try {
            $data = [
                'name' => $name,
                'description' => $description ?? ''
            ];

            $this->db->table('auth_groups')->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Auth group berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan auth group: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get auth_group by id
     */
    public function getAuthGroup($id)
    {
        $group = $this->db->table('auth_groups')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$group) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Auth group tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'group' => $group
        ]);
    }

    /**
     * Update auth_group
     */
    public function updateAuthGroup()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        if (empty($id) || empty($name)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Cek apakah nama group sudah ada (selain id yang sedang diupdate)
        $existing = $this->db->table('auth_groups')
            ->where('name', $name)
            ->where('id !=', $id)
            ->get()
            ->getRowArray();

        if ($existing) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama group sudah ada'
            ]);
        }

        try {
            $data = [
                'name' => $name,
                'description' => $description ?? ''
            ];

            $this->db->table('auth_groups')
                ->where('id', $id)
                ->update($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Auth group berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate auth group: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete auth_group
     */
    public function deleteAuthGroup($id)
    {
        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'ID tidak boleh kosong'
            ]);
        }

        // Cek apakah group masih digunakan oleh user
        $usersInGroup = $this->db->table('auth_groups_users')
            ->where('group_id', $id)
            ->countAllResults();

        if ($usersInGroup > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Group masih digunakan oleh ' . $usersInGroup . ' user. Hapus user terlebih dahulu.'
            ]);
        }

        try {
            $this->db->table('auth_groups')
                ->where('id', $id)
                ->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Auth group berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus auth group: ' . $e->getMessage()
            ]);
        }
    }

}
