<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\AuthModel;
use Myth\Auth\Password;

class Auth extends BaseController
{
    protected $authModel;
    protected $db;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Check if user is Admin
     */
    protected function checkAdmin()
    {
        if (!in_groups('Admin')) {
            session()->setFlashdata('error', 'Akses ditolak. Hanya Admin yang dapat mengakses halaman ini.');
            redirect()->to(base_url())->send();
            exit;
        }
    }

    /**
     * Dashboard/Index page
     */
    public function index()
    {
        $this->checkAdmin();

        $stats = $this->authModel->getStatistics();
        $onlineUsers = $this->authModel->getOnlineUsers(null);
        $frequentLoginStats = $this->authModel->getFrequentLoginStatistics('all');
        $loginAttempts = $this->authModel->getLoginAttempts(100);
        
        $sessionConfig = config('Session');
        $sessionExpiration = $sessionConfig->expiration ?? 7200;
        $sessionExpirationMinutes = round($sessionExpiration / 60);

        $data = [
            'page_title' => 'Pengaturan MyAuth',
            'stats' => $stats,
            'online_users' => $onlineUsers,
            'frequent_login_stats' => $frequentLoginStats,
            'login_attempts' => $loginAttempts,
            'session_expiration_minutes' => $sessionExpirationMinutes
        ];

        return view('backend/auth/Index', $data);
    }

    /**
     * Users management page
     */
    public function users()
    {
        $this->checkAdmin();

        $users = $this->authModel->getAllUsersWithGroups();
        $groups = $this->authModel->getAllGroups();

        $data = [
            'page_title' => 'Manajemen User',
            'users' => $users,
            'groups' => $groups
        ];

        return view('backend/auth/Users', $data);
    }

    /**
     * Get user details
     */
    public function getUser($userId)
    {
        $this->checkAdmin();

        $user = $this->authModel->getUserWithGroups($userId);
        
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Update user groups
     */
    public function updateUserGroups()
    {
        $this->checkAdmin();

        $userId = $this->request->getPost('user_id');
        $groupIds = $this->request->getPost('group_ids') ?? [];

        if (empty($userId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'User ID tidak boleh kosong'
            ]);
        }

        try {
            // Get current groups
            $currentGroups = $this->db->table('auth_groups_users')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();
            
            $currentGroupIds = array_column($currentGroups, 'group_id');

            // Remove groups that are not in the new list
            foreach ($currentGroupIds as $groupId) {
                if (!in_array($groupId, $groupIds)) {
                    $this->authModel->removeUserFromGroup($userId, $groupId);
                }
            }

            // Add new groups
            foreach ($groupIds as $groupId) {
                if (!in_array($groupId, $currentGroupIds)) {
                    $this->authModel->addUserToGroup($userId, $groupId);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Group user berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate group user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset user password to default
     */
    public function resetPassword()
    {
        $this->checkAdmin();

        $userId = $this->request->getPost('user_id');
        $defaultPassword = $this->request->getPost('default_password') ?? 'TpqSmart123';
        $forceReset = $this->request->getPost('force_reset') === '1' || $this->request->getPost('force_reset') === true;

        if (empty($userId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'User ID tidak boleh kosong'
            ]);
        }

        try {
            // Get user
            $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ]);
            }

            // Hash the new password
            $passwordHash = Password::hash($defaultPassword);

            // Generate reset hash if force reset is true
            $resetHash = null;
            if ($forceReset) {
                $resetHash = bin2hex(random_bytes(16));
            }

            // Update user password
            $updateData = [
                'password_hash' => $passwordHash,
                'force_pass_reset' => $forceReset ? 1 : 0
            ];

            if ($forceReset && $resetHash) {
                $updateData['reset_hash'] = $resetHash;
                $updateData['reset_expires'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
            } else {
                $updateData['reset_hash'] = null;
                $updateData['reset_expires'] = null;
            }

            $this->db->table('users')->where('id', $userId)->update($updateData);

            $message = 'Password berhasil direset ke: ' . $defaultPassword;
            if ($forceReset) {
                $message .= ' dan user akan diwajibkan mengganti password saat login berikutnya';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'default_password' => $defaultPassword
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal reset password: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Groups management page
     */
    public function groups()
    {
        $this->checkAdmin();

        $groups = $this->authModel->getAllGroups();
        $permissions = $this->authModel->getAllPermissions();

        $data = [
            'page_title' => 'Manajemen Group',
            'groups' => $groups,
            'permissions' => $permissions
        ];

        return view('backend/auth/Groups', $data);
    }

    /**
     * Get group details
     */
    public function getGroup($groupId)
    {
        $this->checkAdmin();

        $group = $this->authModel->getGroup($groupId);
        
        if (!$group) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Group tidak ditemukan'
            ]);
        }

        $group['permissions'] = $this->authModel->getGroupPermissions($groupId);

        return $this->response->setJSON([
            'success' => true,
            'group' => $group
        ]);
    }

    /**
     * Create group
     */
    public function createGroup()
    {
        $this->checkAdmin();

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description') ?? '';

        if (empty($name)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama group tidak boleh kosong'
            ]);
        }

        // Check if group name already exists
        $exists = $this->db->table('auth_groups')
            ->where('name', $name)
            ->get()
            ->getRowArray();

        if ($exists) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama group sudah ada'
            ]);
        }

        try {
            $this->db->table('auth_groups')->insert([
                'name' => $name,
                'description' => $description
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Group berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan group: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update group
     */
    public function updateGroup()
    {
        $this->checkAdmin();

        $groupId = $this->request->getPost('group_id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description') ?? '';

        if (empty($groupId) || empty($name)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Check if group name already exists (excluding current group)
        $exists = $this->db->table('auth_groups')
            ->where('name', $name)
            ->where('id !=', $groupId)
            ->get()
            ->getRowArray();

        if ($exists) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama group sudah ada'
            ]);
        }

        try {
            $this->db->table('auth_groups')
                ->where('id', $groupId)
                ->update([
                    'name' => $name,
                    'description' => $description
                ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Group berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate group: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete group
     */
    public function deleteGroup($groupId)
    {
        $this->checkAdmin();

        if (empty($groupId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Group ID tidak boleh kosong'
            ]);
        }

        // Check if group is used by users
        $usersInGroup = $this->db->table('auth_groups_users')
            ->where('group_id', $groupId)
            ->countAllResults();

        if ($usersInGroup > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Group masih digunakan oleh ' . $usersInGroup . ' user. Hapus user terlebih dahulu.'
            ]);
        }

        try {
            // Delete group permissions first
            $this->db->table('auth_groups_permissions')
                ->where('group_id', $groupId)
                ->delete();

            // Delete group
            $this->db->table('auth_groups')
                ->where('id', $groupId)
                ->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Group berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus group: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update group permissions
     */
    public function updateGroupPermissions()
    {
        $this->checkAdmin();

        $groupId = $this->request->getPost('group_id');
        $permissionIds = $this->request->getPost('permission_ids') ?? [];

        if (empty($groupId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Group ID tidak boleh kosong'
            ]);
        }

        try {
            // Get current permissions
            $currentPermissions = $this->db->table('auth_groups_permissions')
                ->where('group_id', $groupId)
                ->get()
                ->getResultArray();
            
            $currentPermissionIds = array_column($currentPermissions, 'permission_id');

            // Remove permissions that are not in the new list
            foreach ($currentPermissionIds as $permissionId) {
                if (!in_array($permissionId, $permissionIds)) {
                    $this->authModel->removePermissionFromGroup($groupId, $permissionId);
                }
            }

            // Add new permissions
            foreach ($permissionIds as $permissionId) {
                if (!in_array($permissionId, $currentPermissionIds)) {
                    $this->authModel->addPermissionToGroup($groupId, $permissionId);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission group berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate permission group: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Permissions management page
     */
    public function permissions()
    {
        $this->checkAdmin();

        $permissions = $this->authModel->getAllPermissions();

        $data = [
            'page_title' => 'Manajemen Permission',
            'permissions' => $permissions
        ];

        return view('backend/auth/Permissions', $data);
    }

    /**
     * Create permission
     */
    public function createPermission()
    {
        $this->checkAdmin();

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description') ?? '';

        if (empty($name)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama permission tidak boleh kosong'
            ]);
        }

        // Check if permission name already exists
        $exists = $this->db->table('auth_permissions')
            ->where('name', $name)
            ->get()
            ->getRowArray();

        if ($exists) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama permission sudah ada'
            ]);
        }

        try {
            $this->db->table('auth_permissions')->insert([
                'name' => $name,
                'description' => $description
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan permission: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update permission
     */
    public function updatePermission()
    {
        $this->checkAdmin();

        $permissionId = $this->request->getPost('permission_id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description') ?? '';

        if (empty($permissionId) || empty($name)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Check if permission name already exists (excluding current permission)
        $exists = $this->db->table('auth_permissions')
            ->where('name', $name)
            ->where('id !=', $permissionId)
            ->get()
            ->getRowArray();

        if ($exists) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Nama permission sudah ada'
            ]);
        }

        try {
            $this->db->table('auth_permissions')
                ->where('id', $permissionId)
                ->update([
                    'name' => $name,
                    'description' => $description
                ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate permission: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete permission
     */
    public function deletePermission($permissionId)
    {
        $this->checkAdmin();

        if (empty($permissionId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Permission ID tidak boleh kosong'
            ]);
        }

        try {
            // Delete from group permissions
            $this->db->table('auth_groups_permissions')
                ->where('permission_id', $permissionId)
                ->delete();

            // Delete from user permissions
            $this->db->table('auth_users_permissions')
                ->where('permission_id', $permissionId)
                ->delete();

            // Delete permission
            $this->db->table('auth_permissions')
                ->where('id', $permissionId)
                ->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Permission berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus permission: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Login attempts page
     */
    public function loginAttempts()
    {
        $this->checkAdmin();

        $attempts = $this->authModel->getLoginAttempts(100);

        $data = [
            'page_title' => 'Riwayat Login',
            'attempts' => $attempts
        ];

        return view('backend/auth/LoginAttempts', $data);
    }

    /**
     * Password reset tokens page
     */
    public function passwordResets()
    {
        $this->checkAdmin();

        $tokens = $this->authModel->getPasswordResetTokens(100);

        $data = [
            'page_title' => 'Token Reset Password',
            'tokens' => $tokens
        ];

        return view('backend/auth/PasswordResets', $data);
    }

    /**
     * Online users page - shows who is currently logged in
     */
    public function onlineUsers()
    {
        $this->checkAdmin();

        // Get online users (no max idle limit, show all active sessions)
        // Pass null to use session expiration as limit
        $onlineUsers = $this->authModel->getOnlineUsers(null);
        $activeCount = $this->authModel->getActiveSessionsCount(null);
        
        // Get session expiration time for info
        $sessionConfig = config('Session');
        $sessionExpiration = $sessionConfig->expiration ?? 7200;
        $sessionExpirationMinutes = round($sessionExpiration / 60);

        $data = [
            'page_title' => 'User Online',
            'online_users' => $onlineUsers,
            'active_count' => $activeCount,
            'session_expiration_minutes' => $sessionExpirationMinutes
        ];

        return view('backend/auth/OnlineUsers', $data);
    }

    /**
     * Frequent login users page - shows users who login most frequently
     */
    public function frequentLoginUsers()
    {
        $this->checkAdmin();

        $period = $this->request->getGet('period') ?? 'all';
        $limit = (int)($this->request->getGet('limit') ?? 50);
        
        // Validate period
        $validPeriods = ['all', 'today', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods)) {
            $period = 'all';
        }
        
        // Validate limit
        if ($limit < 1 || $limit > 500) {
            $limit = 50;
        }

        $users = $this->authModel->getMostFrequentLoginUsers($limit, $period);
        $stats = $this->authModel->getFrequentLoginStatistics($period);

        $data = [
            'page_title' => 'User yang Sering Login',
            'users' => $users,
            'stats' => $stats,
            'period' => $period,
            'limit' => $limit
        ];

        return view('backend/auth/FrequentLoginUsers', $data);
    }
}

