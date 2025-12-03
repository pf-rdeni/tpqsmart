<?php

namespace App\Models;

use Myth\Auth\Models\LoginModel as MythLoginModel;

/**
 * Custom LoginModel yang extend dari Myth Auth LoginModel
 * Menambahkan support untuk user_agent
 */
class LoginModel extends MythLoginModel
{
    protected $allowedFields = [
        'ip_address', 'email', 'user_id', 'date', 'success', 'user_agent',
    ];
}

