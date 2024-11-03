<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $data = ['page_title' => 'Login'];
        return view('backend/auth/login',$data);
    }
}
