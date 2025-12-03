<?php

namespace App\Authentication;

use Myth\Auth\Authentication\LocalAuthenticator as MythLocalAuthenticator;

/**
 * Custom LocalAuthenticator yang extend dari Myth Auth LocalAuthenticator
 * Menambahkan support untuk menyimpan user_agent saat login attempt
 */
class LocalAuthenticator extends MythLocalAuthenticator
{
    /**
     * Record a login attempt dengan user_agent
     *
     * @return bool|int|string
     */
    public function recordLoginAttempt(string $email, ?string $ipAddress, ?int $userID, bool $success)
    {
        $userAgent = service('request')->getUserAgent();
        
        return $this->loginModel->insert([
            'ip_address' => $ipAddress,
            'email'      => $email,
            'user_id'    => $userID,
            'date'       => date('Y-m-d H:i:s'),
            'success'    => (int) $success,
            'user_agent' => $userAgent ?: null,
        ]);
    }
}

