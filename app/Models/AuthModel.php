<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table = ''; // Not using Model's table, using direct db queries
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
        $attempts = $this->db->table('auth_logins al')
            ->select('al.*, u.username, u.fullname, u.user_image')
            ->join('users u', 'al.user_id = u.id', 'left')
            ->orderBy('al.date', 'DESC')
            ->orderBy('al.id', 'DESC') // Secondary sort untuk memastikan urutan konsisten
            ->limit($limit)
            ->get()
            ->getResultArray();

        // Parse user_agent for each attempt
        foreach ($attempts as &$attempt) {
            if (!empty($attempt['user_agent'])) {
                $parsed = $this->parseUserAgent($attempt['user_agent']);
                $attempt['device_info'] = $parsed['device'];
                $attempt['browser_info'] = $parsed['browser'];
                $attempt['browser_version'] = $parsed['version'];
                $attempt['device_detail'] = $parsed['device_detail'];
                $attempt['os_version'] = $parsed['os_version'];
                $attempt['device_brand'] = $parsed['device_brand'];
                $attempt['device_model'] = $parsed['device_model'];
            } else {
                $attempt['device_info'] = 'Unknown';
                $attempt['browser_info'] = 'Unknown';
                $attempt['browser_version'] = '';
                $attempt['device_detail'] = '';
                $attempt['os_version'] = '';
                $attempt['device_brand'] = '';
                $attempt['device_model'] = '';
            }
        }

        return $attempts;
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
            // Myth Auth stores: session()->set('logged_in', $this->user->id);
            // CodeIgniter stores session as serialized PHP array
            $userId = null;

            // Method 1: Try to unserialize session data properly (most accurate)
            // CodeIgniter session format: a:1:{s:10:"logged_in";i:123;}
            $sessionArray = @unserialize($sessionData);
            if ($sessionArray && is_array($sessionArray)) {
                // Check for logged_in key directly (Myth Auth format)
                if (isset($sessionArray['logged_in']) && is_numeric($sessionArray['logged_in'])) {
                    $userId = (int)$sessionArray['logged_in'];
                }
                // Fallback: check for user_id key
                elseif (isset($sessionArray['user_id']) && is_numeric($sessionArray['user_id'])) {
                    $userId = (int)$sessionArray['user_id'];
                }
            }

            // Method 2: If unserialize fails, use precise regex patterns
            // Pattern for: "logged_in";i:123; (integer format - most common)
            if (!$userId && preg_match('/"logged_in"[;:]\s*i:(\d+);/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            // Pattern for: "logged_in";s:3:"123"; (string format)
            if (!$userId && preg_match('/"logged_in"[;:]\s*s:\d+:"(\d+)";/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            // Pattern for: 'logged_in';i:123; (single quotes)
            if (!$userId && preg_match("/'logged_in'[;:]\s*i:(\d+);/", $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            // Pattern for: logged_in without quotes: logged_in;i:123;
            if (!$userId && preg_match('/logged_in[;:]\s*i:(\d+);/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            // Method 3: Fallback - look for user_id key (less common)
            if (!$userId && preg_match('/"user_id"[;:]\s*i:(\d+);/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            if (!$userId && preg_match('/"user_id"[;:]\s*s:\d+:"(\d+)";/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            // Method 4: More flexible pattern - look for logged_in followed by any integer
            if (!$userId && preg_match('/logged_in["\']?\s*[;:]\s*[is]:\d+:[:"]([0-9]+)[";]/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            // Method 5: Look for any numeric value after logged_in (with more flexible spacing)
            if (!$userId && preg_match('/logged_in["\']?\s*[;:]\s*(\d+)/', $sessionData, $matches)) {
                $userId = (int)$matches[1];
            }

            // Method 6: Try to find any integer after "logged_in" with various formats
            // This is a more permissive pattern as last resort
            if (!$userId) {
                // Look for pattern: s:10:"logged_in" followed by i:123 or s:3:"123"
                if (preg_match('/s:\d+:"logged_in"[;:]\s*[is]:\d+:[:"]([0-9]+)[";]/', $sessionData, $matches)) {
                    $userId = (int)$matches[1];
                }
            }

            // Method 7: Very permissive - find any occurrence of logged_in and extract nearby number
            // This handles edge cases where format might be slightly different
            if (!$userId) {
                // Find position of logged_in
                $pos = stripos($sessionData, 'logged_in');
                if ($pos !== false) {
                    // Extract substring after logged_in (up to 100 chars)
                    $substring = substr($sessionData, $pos, 100);
                    // Look for first integer in this substring
                    if (preg_match('/[is]:(\d+):[:"]([0-9]+)[";]/', $substring, $matches)) {
                        // Try the second match (the actual value)
                        if (isset($matches[2]) && is_numeric($matches[2])) {
                            $userId = (int)$matches[2];
                        }
                        // Or try the first match if it looks like a user ID
                        elseif (isset($matches[1]) && is_numeric($matches[1]) && $matches[1] > 0 && $matches[1] < 1000000) {
                            $userId = (int)$matches[1];
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
            ->select('users.*, GROUP_CONCAT(DISTINCT ag.name ORDER BY ag.name SEPARATOR ", ") as user_groups')
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
            $builder->select('al.*, u.id as user_id, u.username, u.email, u.fullname, u.user_image, GROUP_CONCAT(DISTINCT ag.name ORDER BY ag.name SEPARATOR ", ") as user_groups');
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

    /**
     * Get users with most frequent logins
     * 
     * @param int $limit Number of users to return
     * @param string $period Period filter: 'all', 'today', 'week', 'month', 'year'
     * @return array
     */
    public function getMostFrequentLoginUsers($limit = 10, $period = 'all')
    {
        $builder = $this->db->table('auth_logins al');
        $builder->select('al.user_id, COUNT(al.id) as login_count, u.username, u.email, u.fullname, u.user_image, u.active, MAX(al.date) as last_login, MIN(al.date) as first_login');
        $builder->join('users u', 'al.user_id = u.id', 'inner');
        $builder->where('al.success', 1);
        $builder->where('al.user_id IS NOT NULL');

        // Apply period filter
        if ($period !== 'all') {
            $dateFilter = $this->getPeriodDateFilter($period);
            if ($dateFilter) {
                $builder->where('al.date >=', $dateFilter);
            }
        }

        $builder->groupBy('al.user_id');
        $builder->orderBy('login_count', 'DESC');
        $builder->limit($limit);

        $users = $builder->get()->getResultArray();

        // Get user groups for each user
        foreach ($users as &$user) {
            $groups = $this->db->table('auth_groups_users agu')
                ->select('ag.name')
                ->join('auth_groups ag', 'agu.group_id = ag.id')
                ->where('agu.user_id', $user['user_id'])
                ->groupBy('ag.id, ag.name')
                ->get()
                ->getResultArray();

            // Remove duplicates using array_unique on the name column (double safety)
            $groupNames = array_unique(array_column($groups, 'name'));
            $user['user_groups'] = implode(', ', $groupNames);
        }

        return $users;
    }

    /**
     * Get statistics for most frequent login users
     * 
     * @param string $period Period filter: 'all', 'today', 'week', 'month', 'year'
     * @return array
     */
    public function getFrequentLoginStatistics($period = 'all')
    {
        $builder = $this->db->table('auth_logins al');
        $builder->select('COUNT(DISTINCT al.user_id) as total_active_users, COUNT(al.id) as total_logins');
        $builder->where('al.success', 1);
        $builder->where('al.user_id IS NOT NULL');

        // Apply period filter
        if ($period !== 'all') {
            $dateFilter = $this->getPeriodDateFilter($period);
            if ($dateFilter) {
                $builder->where('al.date >=', $dateFilter);
            }
        }

        $stats = $builder->get()->getRowArray();

        // Get top 5 users
        $topUsers = $this->getMostFrequentLoginUsers(5, $period);

        return [
            'total_active_users' => (int)($stats['total_active_users'] ?? 0),
            'total_logins' => (int)($stats['total_logins'] ?? 0),
            'top_users' => $topUsers
        ];
    }

    /**
     * Get date filter for period
     * 
     * @param string $period
     * @return string|null
     */
    private function getPeriodDateFilter($period)
    {
        switch ($period) {
            case 'today':
                return date('Y-m-d 00:00:00');
            case 'week':
                return date('Y-m-d 00:00:00', strtotime('-7 days'));
            case 'month':
                return date('Y-m-d 00:00:00', strtotime('-30 days'));
            case 'year':
                return date('Y-m-d 00:00:00', strtotime('-365 days'));
            default:
                return null;
        }
    }

    /**
     * Get login attempts statistics
     * 
     * @return array
     */
    public function getLoginAttemptsStatistics()
    {
        $today = date('Y-m-d 00:00:00');
        $weekAgo = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $monthAgo = date('Y-m-d 00:00:00', strtotime('-30 days'));

        // Get today's stats
        $todayTotal = $this->db->table('auth_logins')->where('date >=', $today)->countAllResults();
        $todaySuccessful = $this->db->table('auth_logins')->where('date >=', $today)->where('success', 1)->countAllResults();
        $todayFailed = $this->db->table('auth_logins')->where('date >=', $today)->where('success', 0)->countAllResults();

        $stats = [
            'total_attempts' => $this->db->table('auth_logins')->countAllResults(),
            'successful_logins' => $this->db->table('auth_logins')->where('success', 1)->countAllResults(),
            'failed_logins' => $this->db->table('auth_logins')->where('success', 0)->countAllResults(),
            'today_attempts' => $todayTotal,
            'today_successful' => $todaySuccessful,
            'today_failed' => $todayFailed,
            'week' => [
                'total' => $this->db->table('auth_logins')->where('date >=', $weekAgo)->countAllResults(),
                'successful' => $this->db->table('auth_logins')->where('date >=', $weekAgo)->where('success', 1)->countAllResults(),
                'failed' => $this->db->table('auth_logins')->where('date >=', $weekAgo)->where('success', 0)->countAllResults(),
            ],
            'month' => [
                'total' => $this->db->table('auth_logins')->where('date >=', $monthAgo)->countAllResults(),
                'successful' => $this->db->table('auth_logins')->where('date >=', $monthAgo)->where('success', 1)->countAllResults(),
                'failed' => $this->db->table('auth_logins')->where('date >=', $monthAgo)->where('success', 0)->countAllResults(),
            ],
        ];

        return $stats;
    }

    /**
     * Get device and browser statistics from login attempts
     * 
     * @return array
     */
    public function getDeviceBrowserStatistics()
    {
        // Get all login attempts with user_agent
        $attempts = $this->db->table('auth_logins')
            ->select('user_agent')
            ->where('user_agent IS NOT NULL')
            ->where('user_agent !=', '')
            ->get()
            ->getResultArray();

        $deviceStats = [];
        $browserStats = [];
        $total = count($attempts);

        foreach ($attempts as $attempt) {
            if (empty($attempt['user_agent'])) {
                continue;
            }

            $parsed = $this->parseUserAgent($attempt['user_agent']);
            $device = $parsed['device'];
            $browser = $parsed['browser'];
            // Use browser name only (without version) for top browsers
            $browserName = $browser;

            // Count devices
            if (!isset($deviceStats[$device])) {
                $deviceStats[$device] = 0;
            }
            $deviceStats[$device]++;

            // Count browsers (by name only, not version)
            if (!isset($browserStats[$browserName])) {
                $browserStats[$browserName] = 0;
            }
            $browserStats[$browserName]++;
        }

        // Sort by count (descending)
        arsort($deviceStats);
        arsort($browserStats);

        // Calculate percentages
        $devicePercentages = [];
        foreach ($deviceStats as $device => $count) {
            $devicePercentages[$device] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        }

        $browserPercentages = [];
        foreach ($browserStats as $browser => $count) {
            $browserPercentages[$browser] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        }

        // Get top 5 browsers
        $topBrowsers = array_slice($browserStats, 0, 5, true);

        return [
            'total_with_user_agent' => $total,
            'device_stats' => $deviceStats,
            'device_percentages' => $devicePercentages,
            'top_browsers' => $topBrowsers,
            'browser_percentages' => $browserPercentages
        ];
    }

    /**
     * Parse user agent string to extract device, browser, and version
     * Includes detailed device information for Android (version, brand, model)
     * 
     * @param string $userAgent
     * @return array
     */
    public function parseUserAgent($userAgent)
    {
        if (empty($userAgent)) {
            return [
                'device' => 'Unknown',
                'browser' => 'Unknown',
                'version' => '',
                'device_detail' => '',
                'os_version' => '',
                'device_brand' => '',
                'device_model' => ''
            ];
        }

        $device = 'Desktop';
        $browser = 'Unknown';
        $version = '';
        $deviceDetail = '';
        $osVersion = '';
        $deviceBrand = '';
        $deviceModel = '';

        // Detect Android and extract detailed info
        if (preg_match('/android/i', $userAgent)) {
            $device = 'Android';

            // Extract Android version (e.g., "Android 11", "Android 12")
            if (preg_match('/android\s+([0-9.]+)/i', $userAgent, $matches)) {
                $osVersion = 'Android ' . $matches[1];
            } elseif (preg_match('/android\s+([a-z0-9.]+)/i', $userAgent, $matches)) {
                // Handle codenames like "Android 10" or "Android 11"
                $osVersion = 'Android ' . $matches[1];
            }

            // Extract device model/brand from common patterns
            // Pattern 1: Samsung (SM-XXXXX)
            if (preg_match('/SM-([A-Z0-9]+)/i', $userAgent, $matches)) {
                $deviceBrand = 'Samsung';
                $deviceModel = 'SM-' . $matches[1];
            }
            // Pattern 2: Xiaomi (Mi, Redmi, POCO)
            elseif (preg_match('/(?:Mi|Redmi|POCO)\s+([A-Z0-9\s]+)/i', $userAgent, $matches)) {
                $deviceBrand = 'Xiaomi';
                $deviceModel = trim($matches[1]);
            }
            // Pattern 3: Oppo
            elseif (preg_match('/OPPO\s+([A-Z0-9]+)/i', $userAgent, $matches)) {
                $deviceBrand = 'Oppo';
                $deviceModel = $matches[1];
            }
            // Pattern 4: Vivo
            elseif (preg_match('/Vivo\s+([A-Z0-9]+)/i', $userAgent, $matches)) {
                $deviceBrand = 'Vivo';
                $deviceModel = $matches[1];
            }
            // Pattern 5: Realme
            elseif (preg_match('/RMX([0-9]+)/i', $userAgent, $matches)) {
                $deviceBrand = 'Realme';
                $deviceModel = 'RMX' . $matches[1];
            }
            // Pattern 6: OnePlus
            elseif (preg_match('/OnePlus\s+([A-Z0-9]+)/i', $userAgent, $matches)) {
                $deviceBrand = 'OnePlus';
                $deviceModel = $matches[1];
            }
            // Pattern 7: Huawei/Honor
            elseif (preg_match('/(?:Huawei|Honor)\s+([A-Z0-9\s]+)/i', $userAgent, $matches)) {
                $deviceBrand = preg_match('/honor/i', $userAgent) ? 'Honor' : 'Huawei';
                $deviceModel = trim($matches[1]);
            }
            // Pattern 8: Generic model number in parentheses (Linux; Android X; Model)
            elseif (preg_match('/\(Linux; Android [^;]+; ([^)]+)\)/i', $userAgent, $matches)) {
                $modelInfo = trim($matches[1]);
                // Try to extract brand from model
                if (preg_match('/^([A-Za-z]+)[\s\-]/', $modelInfo, $brandMatch)) {
                    $deviceBrand = ucfirst(strtolower($brandMatch[1]));
                    $deviceModel = $modelInfo;
                } else {
                    $deviceModel = $modelInfo;
                }
            }

            // Build device detail string
            $detailParts = [];
            if (!empty($deviceBrand)) {
                $detailParts[] = $deviceBrand;
            }
            if (!empty($deviceModel)) {
                $detailParts[] = $deviceModel;
            }
            if (!empty($osVersion)) {
                $detailParts[] = $osVersion;
            }
            $deviceDetail = !empty($detailParts) ? implode(' | ', $detailParts) : '';
        } elseif (preg_match('/iphone|ipod/i', $userAgent)) {
            $device = 'iPhone';
            // Extract iOS version
            if (preg_match('/OS\s+([0-9_]+)/i', $userAgent, $matches)) {
                $osVersion = 'iOS ' . str_replace('_', '.', $matches[1]);
                $deviceDetail = $osVersion;
            }
        } elseif (preg_match('/ipad/i', $userAgent)) {
            $device = 'iPad';
            // Extract iOS version
            if (preg_match('/OS\s+([0-9_]+)/i', $userAgent, $matches)) {
                $osVersion = 'iOS ' . str_replace('_', '.', $matches[1]);
                $deviceDetail = $osVersion;
            }
        } elseif (preg_match('/(blackberry|windows phone|mobile)/i', $userAgent)) {
            if (preg_match('/blackberry/i', $userAgent)) {
                $device = 'BlackBerry';
            } elseif (preg_match('/windows phone/i', $userAgent)) {
                $device = 'Windows Phone';
                // Extract Windows Phone version
                if (preg_match('/Windows Phone\s+([0-9.]+)/i', $userAgent, $matches)) {
                    $osVersion = 'Windows Phone ' . $matches[1];
                    $deviceDetail = $osVersion;
                }
            } else {
                $device = 'Mobile';
            }
        } elseif (preg_match('/tablet/i', $userAgent)) {
            $device = 'Tablet';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $device = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $device = 'Mac';
            // Extract macOS version
            if (preg_match('/Mac OS X\s+([0-9_]+)/i', $userAgent, $matches)) {
                $osVersion = 'macOS ' . str_replace('_', '.', $matches[1]);
                $deviceDetail = $osVersion;
            }
        } elseif (preg_match('/windows/i', $userAgent)) {
            $device = 'Windows';
            // Extract Windows version
            if (preg_match('/Windows NT\s+([0-9.]+)/i', $userAgent, $matches)) {
                $winVersion = $matches[1];
                $winVersions = [
                    '10.0' => 'Windows 10/11',
                    '6.3' => 'Windows 8.1',
                    '6.2' => 'Windows 8',
                    '6.1' => 'Windows 7',
                    '6.0' => 'Windows Vista',
                    '5.1' => 'Windows XP'
                ];
                $osVersion = $winVersions[$winVersion] ?? 'Windows ' . $winVersion;
                $deviceDetail = $osVersion;
            }
        }

        // Detect browser
        if (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
            if (preg_match('/edg\/([0-9.]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
            $browser = 'Chrome';
            if (preg_match('/chrome\/([0-9.]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
            if (preg_match('/firefox\/([0-9.]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $browser = 'Safari';
            if (preg_match('/version\/([0-9.]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/opera|opr/i', $userAgent)) {
            $browser = 'Opera';
            if (preg_match('/(?:opera|opr)\/([0-9.]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        } elseif (preg_match('/msie|trident/i', $userAgent)) {
            $browser = 'IE';
            if (preg_match('/(?:msie |rv:)([0-9.]+)/i', $userAgent, $matches)) {
                $version = $matches[1];
            }
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'version' => $version,
            'device_detail' => $deviceDetail,
            'os_version' => $osVersion,
            'device_brand' => $deviceBrand,
            'device_model' => $deviceModel
        ];
    }
}

