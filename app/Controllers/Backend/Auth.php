<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        $data = ['page_title' => 'Login'];
        return view('backend/auth/login', $data);
    }
    public function registerUser()
    {

        $data = ['page_title' => 'Register User'];
        return view('backend/auth/register',$data);
    }
    
    public function user()
    {

        $data = ['page_title' => 'Profil User'];
        return view('backend/user/index',$data);
    }

}
