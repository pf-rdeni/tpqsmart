<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class Customize extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'Customize AdminLTE'
        ];

        return view('backend/customize/index', $data);
    }
}

