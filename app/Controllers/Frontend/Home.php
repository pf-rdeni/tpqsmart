<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'TPQ Online - Beranda'
        ];
        return view('frontend/home', $data);
    }
} 