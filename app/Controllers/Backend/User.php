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
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Jika IdTahunAjaran tidak ada, ambil tahun ajaran saat ini
        if (!$IdTahunAjaran) {
            $IdTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        }

        // Ambil data user Guru
        $userDataGuru = $this->userModel->getUserDataGuru($IdTpq);

        // Mengecek setiap user data guru untuk password default
        foreach ($userDataGuru as &$user) {
            // Verifikasi apakah password_hash cocok dengan 'TpqSmart123'
            if (Password::verify('TpqSmart123', $user['password_hash'])) {
                $user['password_hash'] = 'TpqSmart123';
            }
            // Jika tidak cocok, biarkan password_hash terenkripsi
            else {
                $user['password_hash'] = '********';
            }
        }

        // Ambil data santri per kelas dari tbl_kelas_santri
        $userDataSantriPerKelas = $this->userModel->getUserDataSantriPerKelas($IdTpq, $IdTahunAjaran);

        // Mengecek setiap user data santri untuk password default
        // Ambil 3 digit terakhir dari IdTpq
        $IdTpqStr = (string)($IdTpq ?? 0);
        $IdTpqLast3 = strlen($IdTpqStr) > 3 ? substr($IdTpqStr, -3) : str_pad($IdTpqStr, 3, '0', STR_PAD_LEFT);
        $defaultPasswordSantri = 'SmartSantriTpq' . $IdTpqLast3;

        foreach ($userDataSantriPerKelas as $kelas => &$kelasData) {
            // Pastikan $kelasData adalah array dan memiliki key 'users'
            if (is_array($kelasData) && isset($kelasData['users']) && is_array($kelasData['users'])) {
                foreach ($kelasData['users'] as &$user) {
                    // Pastikan $user adalah array dan memiliki key 'password_hash'
                    if (is_array($user) && isset($user['password_hash'])) {
                        // Verifikasi apakah password_hash cocok dengan password default santri
                        if (Password::verify($defaultPasswordSantri, $user['password_hash'])) {
                            $user['password_hash'] = $defaultPasswordSantri;
                        }
                        // Jika tidak cocok, cek password default guru
                        elseif (Password::verify('TpqSmart123', $user['password_hash'])) {
                            $user['password_hash'] = 'TpqSmart123';
                        }
                        // Jika tidak cocok, biarkan password_hash terenkripsi
                        else {
                            $user['password_hash'] = '********';
                        }
                    }
                }
            }
        }

        // Ambil data kelas untuk tab
        $dataKelas = $this->userModel->getKelasWithSantri($IdTpq, $IdTahunAjaran);

        $dataGuru = $this->helpFunction->getDataGuru(IdTpq: $IdTpq);

        $dataAutGroups = $this->helpFunction->getDataAuthGoups();

        // jika IdTpq ada $dataAutGroup filter hanya diambil 'name' => 'Guru' atau 'Santri'
        if ($IdTpq) {
            $dataAutGroups = array_filter($dataAutGroups, function ($group) {
                return in_array($group['name'], ['Guru', 'Santri']);
            });
        }

        // Ambil data santri untuk dropdown (tanpa filter kelas, untuk create user)
        $dataSantri = $this->userModel->getSantriForUserCreation($IdTpq, $IdTahunAjaran);

        // Ambil data kelas yang memiliki santri tanpa user account untuk dropdown
        $dataKelasForDropdown = $this->userModel->getKelasForSantriUserCreation($IdTpq, $IdTahunAjaran);

        // Cek apakah user yang login adalah Admin
        $isAdmin = in_groups('Admin');

        // Group data santri berdasarkan kelas untuk JavaScript
        $dataSantriGrouped = [];
        foreach ($dataSantri as $santri) {
            $idKelas = $santri['IdKelas'];
            if (!isset($dataSantriGrouped[$idKelas])) {
                $dataSantriGrouped[$idKelas] = [
                    'IdKelas' => $idKelas,
                    'NamaKelas' => $santri['NamaKelas'],
                    'santri' => []
                ];
            }
            $dataSantriGrouped[$idKelas]['santri'][] = $santri;
        }

        $data = [
            'page_title' => 'Data User',
            'userDataGuru' => $userDataGuru,
            'userDataSantriPerKelas' => $userDataSantriPerKelas,
            'dataKelas' => $dataKelas,
            'dataGuru' => $dataGuru,
            'dataSantri' => $dataSantri,
            'dataSantriGrouped' => $dataSantriGrouped,
            'dataKelasForDropdown' => $dataKelasForDropdown,
            'dataAuthGroups' => $dataAutGroups,
            'isAdmin' => $isAdmin,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdTpq' => $IdTpq
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

    public function checkUserIdNikSantri($idNik)
    {
        $exists = $this->helpFunction->getSantriByIdNik($idNik) > 0;
        return $this->response->setJSON(['exists' => $exists]);
    }

    public function create()
    {
        $groupsId = $this->request->getPost('IdAuthGroup');
        $idNikGuru = $this->request->getPost('IdNikGuru');
        $idNikSantri = $this->request->getPost('IdNikSantri');

        // Cek apakah user yang login adalah Admin
        $isAdmin = in_groups('Admin');

        // Validasi group harus dipilih
        if (empty($groupsId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Group harus dipilih'
            ]);
        }

        // Ambil informasi group yang dipilih
        $selectedGroup = $this->helpFunction->getDataAuthGoups($groupsId);
        if (empty($selectedGroup)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Group tidak valid'
            ]);
        }

        $groupName = $selectedGroup[0]['name'] ?? '';
        $fullName = '';
        $idNik = null;

        // Handle berdasarkan jenis group
        if ($groupName === 'Santri') {
            // Untuk Santri, wajib pilih santri
            if (empty($idNikSantri)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Nama Santri harus dipilih'
                ]);
            }

            // Ambil nama santri dari NIK
            $santriData = $this->db->table('tbl_santri_baru')
                ->select('NamaSantri')
                ->where('NikSantri', $idNikSantri)
                ->get()
                ->getRowArray();

            if (empty($santriData)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            $fullName = $santriData['NamaSantri'];
            $idNik = $idNikSantri;
        } else {
            // Untuk Guru atau group lainnya
            if ($isAdmin) {
                // Admin bisa input fullname manual atau pilih dari guru
                $fullNameManual = $this->request->getPost('fullname_manual');
                if (!empty($idNikGuru)) {
                    // Jika pilih guru, ambil nama dari guru
                    $namaGuru = $this->helpFunction->getNamaGuruByIdNik($idNikGuru);
                    $fullName = $namaGuru && isset($namaGuru['Nama']) ? $namaGuru['Nama'] : '';
                    $idNik = $idNikGuru;
                } elseif (!empty($fullNameManual)) {
                    // Jika input manual, gunakan input manual
                    $fullName = $fullNameManual;
                    $idNik = null;
                } else {
                    // Jika kosong semua, gunakan username sebagai fallback
                    $fullName = $this->request->getPost('username');
                    $idNik = null;
                }
            } else {
                // Bukan Admin, wajib pilih guru
                if (empty($idNikGuru)) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Nama Guru harus dipilih'
                    ]);
                }
                $namaGuru = $this->helpFunction->getNamaGuruByIdNik($idNikGuru);
                $fullName = $namaGuru && isset($namaGuru['Nama']) ? $namaGuru['Nama'] : '';
                $idNik = $idNikGuru;
            }

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
            'nik' => $idNik,
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
