<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all users with their groups
     */
    public function getAllUsersWithGroups()
    {
        $builder = $this->db->table('users u');
        $builder->select('u.*, GROUP_CONCAT(ag.name SEPARATOR ", ") as user_groups');
        $builder->join('auth_groups_users agu', 'u.id = agu.user_id', 'left');
        $builder->join('auth_groups ag', 'agu.group_id = ag.id', 'left');
        $builder->groupBy('u.id');
        $builder->orderBy('u.id', 'DESC');
        
        $users = $builder->get()->getResultArray();
        
        // Convert force_pass_reset to integer for easier handling
        foreach ($users as &$user) {
            $user['force_pass_reset'] = isset($user['force_pass_reset']) ? (int)$user['force_pass_reset'] : 0;
        }
        
        return $users;
    }

    /**
     * Get user by ID with groups
     */
    public function getUserWithGroups($userId)
    {
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        
        if ($user) {
            $groups = $this->db->table('auth_groups_users agu')
                ->select('ag.id, ag.name, ag.description')
                ->join('auth_groups ag', 'agu.group_id = ag.id')
                ->where('agu.user_id', $userId)
                ->get()
                ->getResultArray();
            
            $user['groups'] = $groups;
        }
        
        return $user;
    }

    /**
     * Get all groups
     */
    public function getAllGroups()
    {
        return $this->db->table('auth_groups')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get group by ID
     */
    public function getGroup($groupId)
    {
        return $this->db->table('auth_groups')
            ->where('id', $groupId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions()
    {
        return $this->db->table('auth_permissions')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get permissions for a group
     */
    public function getGroupPermissions($groupId)
    {
        return $this->db->table('auth_groups_permissions agp')
            ->select('ap.id, ap.name, ap.description')
            ->join('auth_permissions ap', 'agp.permission_id = ap.id')
            ->where('agp.group_id', $groupId)
            ->get()
            ->getResultArray();
    }

    /**
     * Get permissions for a user
     */
    public function getUserPermissions($userId)
    {
        // Get permissions from groups
        $groupPermissions = $this->db->table('auth_groups_users agu')
            ->select('ap.id, ap.name, ap.description')
            ->join('auth_groups_permissions agp', 'agu.group_id = agp.group_id')
            ->join('auth_permissions ap', 'agp.permission_id = ap.id')
            ->where('agu.user_id', $userId)
            ->get()
            ->getResultArray();

        // Get direct user permissions
        $userPermissions = $this->db->table('auth_users_permissions aup')
            ->select('ap.id, ap.name, ap.description')
            ->join('auth_permissions ap', 'aup.permission_id = ap.id')
            ->where('aup.user_id', $userId)
            ->get()
            ->getResultArray();

        // Merge and remove duplicates
        $allPermissions = array_merge($groupPermissions, $userPermissions);
        $uniquePermissions = [];
        foreach ($allPermissions as $permission) {
            $uniquePermissions[$permission['id']] = $permission;
        }

        return array_values($uniquePermissions);
    }

    /**
     * Get login attempts
     */
    public function getLoginAttempts($limit = 100)
    {
        return $this->db->table('auth_logins')
            ->orderBy('date', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get password reset tokens
     */
    public function getPasswordResetTokens($limit = 100)
    {
        return $this->db->table('auth_tokens')
            ->where('type', 'password_reset')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Add user to group
     */
    public function addUserToGroup($userId, $groupId)
    {
        // Check if already exists
        $exists = $this->db->table('auth_groups_users')
            ->where('user_id', $userId)
            ->where('group_id', $groupId)
            ->get()
            ->getRowArray();

        if (!$exists) {
            return $this->db->table('auth_groups_users')->insert([
                'user_id' => $userId,
                'group_id' => $groupId
            ]);
        }

        return true;
    }

    /**
     * Remove user from group
     */
    public function removeUserFromGroup($userId, $groupId)
    {
        return $this->db->table('auth_groups_users')
            ->where('user_id', $userId)
            ->where('group_id', $groupId)
            ->delete();
    }

    /**
     * Add permission to group
     */
    public function addPermissionToGroup($groupId, $permissionId)
    {
        // Check if already exists
        $exists = $this->db->table('auth_groups_permissions')
            ->where('group_id', $groupId)
            ->where('permission_id', $permissionId)
            ->get()
            ->getRowArray();

        if (!$exists) {
            return $this->db->table('auth_groups_permissions')->insert([
                'group_id' => $groupId,
                'permission_id' => $permissionId
            ]);
        }

        return true;
    }

    /**
     * Remove permission from group
     */
    public function removePermissionFromGroup($groupId, $permissionId)
    {
        return $this->db->table('auth_groups_permissions')
            ->where('group_id', $groupId)
            ->where('permission_id', $permissionId)
            ->delete();
    }

    /**
     * Add permission to user
     */
    public function addPermissionToUser($userId, $permissionId)
    {
        // Check if already exists
        $exists = $this->db->table('auth_users_permissions')
            ->where('user_id', $userId)
            ->where('permission_id', $permissionId)
            ->get()
            ->getRowArray();

        if (!$exists) {
            return $this->db->table('auth_users_permissions')->insert([
                'user_id' => $userId,
                'permission_id' => $permissionId
            ]);
        }

        return true;
    }

    /**
     * Remove permission from user
     */
    public function removePermissionFromUser($userId, $permissionId)
    {
        return $this->db->table('auth_users_permissions')
            ->where('user_id', $userId)
            ->where('permission_id', $permissionId)
            ->delete();
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_users' => $this->db->table('users')->countAllResults(),
            'active_users' => $this->db->table('users')->where('active', 1)->countAllResults(),
            'inactive_users' => $this->db->table('users')->where('active', 0)->countAllResults(),
            'total_groups' => $this->db->table('auth_groups')->countAllResults(),
            'total_permissions' => $this->db->table('auth_permissions')->countAllResults(),
            'total_login_attempts' => $this->db->table('auth_logins')->countAllResults(),
            'failed_login_attempts' => $this->db->table('auth_logins')->where('success', 0)->countAllResults(),
            'successful_login_attempts' => $this->db->table('auth_logins')->where('success', 1)->countAllResults(),
        ];

        return $stats;
    }

    /**
     * Get online users by reading active session files
     * More accurate than just checking login time
     * 
     * @param int|null $maxIdleMinutes Maximum idle time in minutes (null = use session expiration)
     * @return array
     */
    public function getOnlineUsers($maxIdleMinutes = null)
    {
        $sessionPath = WRITEPATH . 'session/';
        $sessionConfig = config('Session');
        $sessionExpiration = $sessionConfig->expiration ?? 7200; // Default 2 hours
        // If maxIdleMinutes is null, use session expiration as limit
        $maxIdleTime = $maxIdleMinutes !== null ? ($maxIdleMinutes * 60) : $sessionExpiration;
        $currentTime = time();
        
        $onlineUsers = [];
        $seenUsers = [];
        $userSessions = []; // Store user_id => lastModified mapping

        // Get all session files
        $sessionFiles = glob($sessionPath . 'ci_session*');
        
        if (empty($sessionFiles)) {
            return $onlineUsers;
        }

        // Step 1: Collect all user IDs from session files
        foreach ($sessionFiles as $sessionFile) {
            // Skip index.html and directories
            $basename = basename($sessionFile);
            if ($basename === 'index.html' || is_dir($sessionFile)) {
                continue;
            }
            
            // Get file modification time (last activity)
            $lastModified = @filemtime($sessionFile);
            if (!$lastModified) {
                continue;
            }
            
            $idleTime = $currentTime - $lastModified;
            
            // Skip if file is too old (expired session)
            if ($idleTime > $sessionExpiration) {
                continue;
            }
            
            // Skip if user is idle too long (only if maxIdleTime is set and less than session expiration)
            if ($maxIdleTime < $sessionExpiration && $idleTime > $maxIdleTime) {
                continue;
            }
            
            // Read session file content
            $sessionData = @file_get_contents($sessionFile);
            if (!$sessionData) {
                continue;
            }
            
            // Extract user_id from session data
            // CodeIgniter stores session as serialized PHP data
            $userId = null;
            
            // Try multiple patterns to extract user_id
            // Pattern 1: Look for "logged_in" key with user ID (Myth Auth format)
            if (preg_match('/logged_in["\']?\s*[;:]\s*[iO]:\d+:[:"]([^";]+)[";]/', $sessionData, $matches)) {
                // This might be a serialized object/array, try to extract ID
                if (preg_match('/"id"[;:]\s*(\d+)/', $sessionData, $idMatch)) {
                    $userId = (int)$idMatch[1];
                }
            }
            
            // Pattern 2: Look for direct user_id in session
            if (!$userId && preg_match('/user_id["\']?\s*[;:]\s*[is]:\d+:[:"]([^";]+)[";]/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }
            
            // Pattern 3: Look for numeric patterns that might be user IDs
            // Myth Auth typically stores user object with id field
            if (!$userId) {
                // Try to find serialized object with id field
                if (preg_match('/"id"[;:]\s*[is]:\d+:[:"]([^";]+)[";]/', $sessionData, $matches)) {
                    $potentialId = trim($matches[1], '"');
                    if (is_numeric($potentialId) && $potentialId > 0) {
                        $userId = (int)$potentialId;
                    }
                }
            }
            
            // Pattern 4: Fallback - look for any numeric value after common auth keys
            if (!$userId) {
                $authKeys = ['logged_in', 'user_id', 'auth_user_id', 'ci_user_id'];
                foreach ($authKeys as $key) {
                    if (preg_match('/' . preg_quote($key, '/') . '["\']?\s*[;:]\s*(\d+)/', $sessionData, $matches)) {
                        $userId = (int)$matches[1];
                        break;
                    }
                }
            }
            
            // Pattern 5: Last resort - try to unserialize and search
            if (!$userId) {
                // Try to extract serialized data and search for user ID
                // Look for patterns like: i:123; or s:3:"123";
                if (preg_match_all('/[is]:\d+:[:"]([0-9]{1,6})[";]/', $sessionData, $allMatches)) {
                    // Get the largest reasonable number (likely user ID)
                    $numbers = array_map('intval', $allMatches[1]);
                    $numbers = array_filter($numbers, function($n) { return $n > 0 && $n < 1000000; });
                    if (!empty($numbers)) {
                        // Prefer numbers that look like user IDs (not too small, not too large)
                        $filtered = array_filter($numbers, function($n) { return $n >= 1 && $n <= 999999; });
                        if (!empty($filtered)) {
                            $userId = max($filtered);
                        }
                    }
                }
            }
            
            if (!$userId || isset($seenUsers[$userId])) {
                continue;
            }
            
            $seenUsers[$userId] = true;
            // Store user_id with their last activity time
            $userSessions[$userId] = $lastModified;
        }

        // If no users found, return empty array
        if (empty($userSessions)) {
            return $onlineUsers;
        }

        // Step 2: Bulk query - Get all user info with groups in one query
        $userIds = array_keys($userSessions);
        $usersData = $this->db->table('users')
            ->select('users.*, GROUP_CONCAT(ag.name SEPARATOR ", ") as user_groups')
            ->join('auth_groups_users agu', 'users.id = agu.user_id', 'left')
            ->join('auth_groups ag', 'agu.group_id = ag.id', 'left')
            ->whereIn('users.id', $userIds)
            ->where('users.active', 1)
            ->groupBy('users.id')
            ->get()
            ->getResultArray();

        // Create user data map for quick lookup
        $usersMap = [];
        foreach ($usersData as $user) {
            $usersMap[$user['id']] = $user;
        }

        // Step 3: Bulk query - Get all last logins in one query
        // Use a subquery to get the latest login for each user
        $subquery = $this->db->table('auth_logins')
            ->select('user_id, MAX(date) as max_date')
            ->whereIn('user_id', $userIds)
            ->where('success', 1)
            ->groupBy('user_id')
            ->getCompiledSelect();

        $lastLoginsData = $this->db->table('auth_logins al')
            ->select('al.user_id, al.date, al.ip_address')
            ->join("({$subquery}) latest", 'al.user_id = latest.user_id AND al.date = latest.max_date', 'inner')
            ->whereIn('al.user_id', $userIds)
            ->where('al.success', 1)
            ->get()
            ->getResultArray();

        // Create last login map for quick lookup
        $lastLoginsMap = [];
        foreach ($lastLoginsData as $login) {
            $lastLoginsMap[$login['user_id']] = $login;
        }

        // Step 4: Combine data and build result array
        foreach ($userSessions as $userId => $lastModified) {
            if (!isset($usersMap[$userId])) {
                continue;
            }

            $user = $usersMap[$userId];
            $lastLogin = $lastLoginsMap[$userId] ?? null;

            $idleTime = $currentTime - $lastModified;
            
            // Determine status based on idle time
            $status = 'away';
            $statusLabel = 'Away';
            $statusBadge = 'warning';
            
            if ($idleTime < 300) { // Less than 5 minutes
                $status = 'active';
                $statusLabel = 'Active';
                $statusBadge = 'success';
            } elseif ($idleTime < 900) { // Less than 15 minutes
                $status = 'idle';
                $statusLabel = 'Idle';
                $statusBadge = 'info';
            }
            
            $onlineUsers[] = [
                'user_id' => $userId,
                'username' => $user['username'],
                'email' => $user['email'],
                'fullname' => $user['fullname'],
                'user_image' => $user['user_image'],
                'user_groups' => $user['user_groups'],
                'last_activity' => date('Y-m-d H:i:s', $lastModified),
                'idle_time' => $idleTime,
                'idle_minutes' => round($idleTime / 60, 1),
                'idle_seconds' => $idleTime,
                'last_login' => $lastLogin['date'] ?? null,
                'ip_address' => $lastLogin['ip_address'] ?? null,
                'status' => $status,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge
            ];
        }
        
        // If no users found from session files, fallback to login-based approach
        // This ensures we still show some data even if session reading fails
        if (empty($onlineUsers)) {
            // Use session expiration minutes if maxIdleMinutes is null
            $fallbackMinutes = $maxIdleMinutes ?? round($sessionExpiration / 60);
            $timeThreshold = date('Y-m-d H:i:s', strtotime("-{$fallbackMinutes} minutes"));
            
            $builder = $this->db->table('auth_logins al');
            $builder->select('al.*, u.id as user_id, u.username, u.email, u.fullname, u.user_image, GROUP_CONCAT(ag.name SEPARATOR ", ") as user_groups');
            $builder->join('users u', 'al.user_id = u.id', 'inner');
            $builder->join('auth_groups_users agu', 'u.id = agu.user_id', 'left');
            $builder->join('auth_groups ag', 'agu.group_id = ag.id', 'left');
            $builder->where('al.success', 1);
            $builder->where('al.date >=', $timeThreshold);
            $builder->where('u.active', 1);
            $builder->groupBy('al.user_id, al.id');
            $builder->orderBy('al.date', 'DESC');
            
            $logins = $builder->get()->getResultArray();
            
            foreach ($logins as $login) {
                $userId = $login['user_id'];
                if (!isset($seenUsers[$userId])) {
                    $seenUsers[$userId] = true;
                    
                    // Calculate idle time from last login
                    $lastLoginTime = strtotime($login['date']);
                    $idleTime = time() - $lastLoginTime;
                    
                    // Determine status
                    $status = 'away';
                    $statusLabel = 'Away';
                    $statusBadge = 'warning';
                    
                    if ($idleTime < 300) {
                        $status = 'active';
                        $statusLabel = 'Active';
                        $statusBadge = 'success';
                    } elseif ($idleTime < 900) {
                        $status = 'idle';
                        $statusLabel = 'Idle';
                        $statusBadge = 'info';
                    }
                    
                    $onlineUsers[] = [
                        'user_id' => $userId,
                        'username' => $login['username'],
                        'email' => $login['email'],
                        'fullname' => $login['fullname'],
                        'user_image' => $login['user_image'],
                        'user_groups' => $login['user_groups'],
                        'last_activity' => $login['date'],
                        'idle_time' => $idleTime,
                        'idle_minutes' => round($idleTime / 60, 1),
                        'idle_seconds' => $idleTime,
                        'last_login' => $login['date'],
                        'ip_address' => $login['ip_address'],
                        'status' => $status,
                        'status_label' => $statusLabel,
                        'status_badge' => $statusBadge
                    ];
                }
            }
        }
        
        // Sort by last activity (most recent first)
        usort($onlineUsers, function($a, $b) {
            return $b['last_activity'] <=> $a['last_activity'];
        });
        
        return $onlineUsers;
    }

    /**
     * Get active sessions count based on session files
     */
    public function getActiveSessionsCount($maxIdleMinutes = null)
    {
        $onlineUsers = $this->getOnlineUsers($maxIdleMinutes);
        return count($onlineUsers);
    }
}

