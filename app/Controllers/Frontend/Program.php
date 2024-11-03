<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class Program extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'Program TPQ Online'
        ];
        return view('frontend/home', $data);
    }
} 