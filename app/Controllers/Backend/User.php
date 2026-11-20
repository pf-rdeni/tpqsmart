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

        // jika bukan Admin, batasi grup yang dapat dipilih operator lembaga ke level TPQ
        if ($IdTpq && !in_groups('Admin')) {
            $dataAutGroups = array_values(array_filter($dataAutGroups, function ($group) {
                return in_array($group['name'], ['Guru', 'Sekretaris', 'Bendahara', 'Santri']);
            }));
        }

        // Ambil data santri untuk dropdown (tanpa filter kelas, untuk create user)
        $dataSantri = $this->userModel->getSantriForUserCreation($IdTpq, $IdTahunAjaran);

        // Ambil data kelas yang memiliki santri tanpa user account untuk dropdown
        $dataKelasForDropdown = $this->userModel->getKelasForSantriUserCreation($IdTpq, $IdTahunAjaran);

        // Cek apakah user yang login adalah Admin
        $isAdmin = in_groups('Admin');

        // Hitung statistik user
        $totalSantriUsers = 0;
        foreach ($userDataSantriPerKelas as $kData) {
            if (isset($kData['users']) && is_array($kData['users'])) {
                $totalSantriUsers += count($kData['users']);
            }
        }
        $totalGuruStaf = count($userDataGuru);
        $totalMultiRoles = count(array_filter($userDataGuru, fn($u) => ($u['total_groups'] ?? 0) > 1));
        $totalUsers = $totalGuruStaf + $totalSantriUsers;

        $stats = [
            'totalUsers'      => $totalUsers,
            'totalGuruStaf'   => $totalGuruStaf,
            'totalSantri'     => $totalSantriUsers,
            'totalMultiRoles' => $totalMultiRoles
        ];

        // List TPQ untuk filter Admin
        $listTpq = [];
        if ($isAdmin) {
            $listTpq = $this->db->table('tbl_tpq')
                ->select('IdTpq, NamaTpq')
                ->orderBy('NamaTpq', 'ASC')
                ->get()
                ->getResultArray();
        }

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
            'page_title'             => 'Data Pengguna & Hak Akses',
            'userDataGuru'           => $userDataGuru,
            'userDataSantriPerKelas' => $userDataSantriPerKelas,
            'dataKelas'              => $dataKelas,
            'dataGuru'               => $dataGuru,
            'dataSantri'             => $dataSantri,
            'dataSantriGrouped'      => $dataSantriGrouped,
            'dataKelasForDropdown'   => $dataKelasForDropdown,
            'dataAuthGroups'         => $dataAutGroups,
            'isAdmin'                => $isAdmin,
            'IdTahunAjaran'          => $IdTahunAjaran,
            'IdTpq'                  => $IdTpq,
            'stats'                  => $stats,
            'listTpq'                => $listTpq
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
        $selectedGroups = $this->request->getPost('groups'); // array of group IDs from multi-role checkboxes

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
        $finalGroupIds = [];

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
            $finalGroupIds = [4]; // Santri ID
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

            // Ambil multi-role checkboxes jika ada
            if (is_array($selectedGroups) && !empty($selectedGroups)) {
                $finalGroupIds = array_map('intval', array_filter($selectedGroups));
            } elseif (!empty($groupsId)) {
                $finalGroupIds = [(int)$groupsId];
            } else {
                $finalGroupIds = [3]; // Default Guru
            }

            // Batasi hak akses jika bukan Admin
            if (!$isAdmin) {
                $allowedTpqGroupIds = [3, 12, 13]; // Guru, Sekretaris Lembaga, Bendahara
                $finalGroupIds = array_values(array_intersect($finalGroupIds, $allowedTpqGroupIds));
                if (empty($finalGroupIds)) {
                    $finalGroupIds = [3];
                }
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
            $this->db->transStart();

            $returnUserId = $this->userModel->store($data);

            foreach ($finalGroupIds as $gid) {
                $groupData = [
                    'group_id' => (int)$gid,
                    'user_id' => $returnUserId
                ];
                $this->helpFunction->insertAuthGroupsUsers($groupData);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data relasi grup');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pengguna berhasil ditambahkan dengan ' . count($finalGroupIds) . ' peran',
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

    // buat fungsi update user (password dan/atau hak akses)
    public function update()
    {
        // Mengambil data dari POST
        $id = $this->request->getPost('id');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $selectedGroups = $this->request->getPost('groups'); // array of group IDs

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'User ID wajib diisi'
            ]);
        }

        $isAdmin = in_groups('Admin');
        $currentIdTpq = session()->get('IdTpq');

        $user = $this->db->table('users u')
            ->select('u.id, u.username, g.IdTpq')
            ->join('tbl_guru g', 'g.IdGuru = u.nik', 'left')
            ->where('u.id', $id)
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        if (!$isAdmin && !empty($user['IdTpq']) && $user['IdTpq'] != $currentIdTpq) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki hak untuk mengelola user lembaga lain'
            ]);
        }

        try {
            $this->db->transStart();

            // 1. Update password jika diisi
            if (!empty($password)) {
                $this->userModel->updateUser([
                    'password_hash' => Password::hash($password)
                ], $id);
            }

            // 2. Update peran / hak akses jika dikirimkan
            if (is_array($selectedGroups) && !empty($selectedGroups)) {
                $groupIds = array_map('intval', array_filter($selectedGroups));
                $allowedTpqGroupIds = [3, 12, 13];

                if (!$isAdmin) {
                    $groupIds = array_values(array_intersect($groupIds, $allowedTpqGroupIds));
                    if (!empty($groupIds)) {
                        $this->db->table('auth_groups_users')
                            ->where('user_id', $id)
                            ->whereIn('group_id', $allowedTpqGroupIds)
                            ->delete();

                        foreach ($groupIds as $gid) {
                            $this->db->table('auth_groups_users')->insert([
                                'group_id' => $gid,
                                'user_id'  => $id
                            ]);
                        }
                    }
                } else {
                    $this->db->table('auth_groups_users')->where('user_id', $id)->delete();
                    foreach ($groupIds as $gid) {
                        $this->db->table('auth_groups_users')->insert([
                            'group_id' => $gid,
                            'user_id'  => $id
                        ]);
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan perubahan ke basis data');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pengguna & hak akses berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengubah data pengguna: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * Generate user per kelas sekaligus
     * Membuat user account untuk semua santri di kelas yang dipilih yang belum memiliki user account
     */
    public function generateUserPerKelas()
    {
        try {
            // Ambil data dari JSON request
            $jsonData = $this->request->getJSON(true);
            $IdKelas = $jsonData['IdKelas'] ?? null;

            // Validasi
            if (empty($IdKelas)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Kelas harus dipilih'
                ]);
            }

            // Ambil IdTpq dan IdTahunAjaran dari session
            $IdTpq = session()->get('IdTpq');
            $IdTahunAjaran = session()->get('IdTahunAjaran');

            if (!$IdTahunAjaran) {
                $IdTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
            }

            // Ambil semua santri di kelas tersebut yang belum punya user account
            $santriList = $this->userModel->getSantriForUserCreation($IdTpq, $IdTahunAjaran);

            // Filter hanya santri di kelas yang dipilih
            $santriKelas = array_filter($santriList, function ($santri) use ($IdKelas) {
                return $santri['IdKelas'] == $IdKelas;
            });

            if (empty($santriKelas)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada santri di kelas ini yang belum memiliki user account'
                ]);
            }

            // Ambil group_id untuk Santri
            $santriGroup = $this->helpFunction->getDataAuthGoups(null);
            $santriGroupId = null;
            foreach ($santriGroup as $group) {
                if ($group['name'] === 'Santri') {
                    $santriGroupId = $group['id'];
                    break;
                }
            }

            if (!$santriGroupId) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Group Santri tidak ditemukan'
                ]);
            }

            // Hitung password default untuk santri
            $IdTpqStr = (string)($IdTpq ?? 0);
            $IdTpqLast3 = strlen($IdTpqStr) > 3 ? substr($IdTpqStr, -3) : str_pad($IdTpqStr, 3, '0', STR_PAD_LEFT);
            $defaultPasswordSantri = 'SmartSantriTpq' . $IdTpqLast3;

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Generate user untuk setiap santri
            foreach ($santriKelas as $santri) {
                try {
                    // Cek apakah user sudah ada
                    $existingUser = $this->helpFunction->getUserByUsername($santri['IdSantri']);
                    if ($existingUser > 0) {
                        $errorCount++;
                        $errors[] = "Santri {$santri['NamaSantri']} (NIK: {$santri['NikSantri']}) sudah memiliki user account";
                        continue;
                    }

                    // Buat data user
                    $userData = [
                        'username' => $santri['IdSantri'],
                        'fullname' => $santri['NamaSantri'],
                        'email' => $santri['IdSantri'] . '@tpqsmart.simpedis.com',
                        'password_hash' => Password::hash($defaultPasswordSantri),
                        'nik' => $santri['NikSantri'],
                        'active' => 1
                    ];

                    // Simpan user
                    $returnUserId = $this->userModel->store($userData);

                    // Tambahkan ke group Santri
                    $groupData = [
                        'group_id' => (int)$santriGroupId,
                        'user_id' => $returnUserId
                    ];
                    $this->helpFunction->insertAuthGroupsUsers($groupData);

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Gagal membuat user untuk {$santri['NamaSantri']}: " . $e->getMessage();
                    log_message('error', 'User: generateUserPerKelas - Error: ' . $e->getMessage());
                }
            }

            // Buat pesan response
            $message = "Berhasil membuat {$successCount} user account";
            if ($errorCount > 0) {
                $message .= ". {$errorCount} user gagal dibuat.";
            }

            return $this->response->setJSON([
                'success' => $successCount > 0,
                'message' => $message,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            log_message('error', 'User: generateUserPerKelas - Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal generate user: ' . $e->getMessage()
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

    /**
     * Mengambil detail hak akses / grup yang dimiliki suatu user
     */
    public function getUserGroups($userId)
    {
        $isAdmin = in_groups('Admin');
        $currentIdTpq = session()->get('IdTpq');

        // Cari data user
        $user = $this->db->table('users u')
            ->select('u.id, u.username, u.email, u.nik, COALESCE(g.Nama, u.fullname, u.username) as nama, t.IdTpq, t.NamaTpq')
            ->join('tbl_guru g', 'g.IdGuru = u.nik', 'left')
            ->join('tbl_tpq t', 't.IdTpq = g.IdTpq', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        // Validasi hak akses Operator
        if (!$isAdmin) {
            if (empty($user['IdTpq']) || $user['IdTpq'] != $currentIdTpq) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak untuk mengelola user dari lembaga lain'
                ]);
            }
        }

        // Ambil grup yang saat ini aktif untuk user ini
        $userGroups = $this->db->table('auth_groups_users agu')
            ->select('agu.group_id, ag.name, ag.description')
            ->join('auth_groups ag', 'ag.id = agu.group_id')
            ->where('agu.user_id', $userId)
            ->get()
            ->getResultArray();

        $assignedGroupIds = array_map(fn($g) => (int)$g['group_id'], $userGroups);

        // Ambil daftar grup yang diizinkan untuk di-assign
        $allGroups = $this->helpFunction->getDataAuthGoups();
        if (!$isAdmin) {
            // Operator hanya boleh assign peran level Lembaga TPQ
            $allowedGroupIds = [3, 12, 13]; // Guru, Sekretaris Lembaga, Bendahara
            $availableGroups = array_values(array_filter($allGroups, fn($g) => in_array((int)$g['id'], $allowedGroupIds)));
        } else {
            $availableGroups = $allGroups;
        }

        return $this->response->setJSON([
            'success' => true,
            'user' => $user,
            'assigned_group_ids' => $assignedGroupIds,
            'available_groups' => $availableGroups,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Memperbarui grup / hak akses yang dimiliki user
     */
    public function updateUserGroups()
    {
        $isAdmin = in_groups('Admin');
        $currentIdTpq = session()->get('IdTpq');

        $userId = $this->request->getPost('user_id');
        $groupIds = $this->request->getPost('groups'); // array of group IDs

        if (empty($userId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'User ID wajib diisi'
            ]);
        }

        if (!is_array($groupIds)) {
            $groupIds = empty($groupIds) ? [] : [$groupIds];
        }
        $groupIds = array_map('intval', array_filter($groupIds));

        // Cari data user
        $user = $this->db->table('users u')
            ->select('u.id, u.username, g.IdTpq')
            ->join('tbl_guru g', 'g.IdGuru = u.nik', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        $allowedTpqGroupIds = [3, 12, 13]; // Guru, Sekretaris Lembaga, Bendahara

        if (!$isAdmin) {
            // Validasi kepemilikan TPQ
            if (empty($user['IdTpq']) || $user['IdTpq'] != $currentIdTpq) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak untuk mengelola user lembaga lain'
                ]);
            }

            // Filter hanya grup yang diizinkan untuk Operator
            $groupIds = array_values(array_intersect($groupIds, $allowedTpqGroupIds));

            if (empty($groupIds)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Pilih minimal satu peran untuk user (misal: Guru)'
                ]);
            }
        } else {
            if (empty($groupIds)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Pilih minimal satu peran untuk user'
                ]);
            }
        }

        $this->db->transStart();

        if (!$isAdmin) {
            // Operator hanya mengubah grup level TPQ (tidak menghapus peran non-TPQ seperti Juri/Panitia)
            $this->db->table('auth_groups_users')
                ->where('user_id', $userId)
                ->whereIn('group_id', $allowedTpqGroupIds)
                ->delete();

            foreach ($groupIds as $gid) {
                $this->db->table('auth_groups_users')->insert([
                    'group_id' => $gid,
                    'user_id' => $userId
                ]);
            }
        } else {
            // Admin mengupdate seluruh grup
            $this->db->table('auth_groups_users')
                ->where('user_id', $userId)
                ->delete();

            foreach ($groupIds as $gid) {
                $this->db->table('auth_groups_users')->insert([
                    'group_id' => $gid,
                    'user_id' => $userId
                ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui hak akses peran user'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Hak akses dan peran user berhasil diperbarui'
        ]);
    }
}
