<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class Pages extends BaseController
{
    public function index()
    {
        $data = ['page_title' => 'Dashboard'];
        return view('backend/dashboard/index', $data);

    }

    public function about()
    {
        $data = ['pages_title' => 'About Me | MyPrograming'];
        return view('backend/dashboard/about', $data);
    }
    public function contact()
    {

        $data = [
            'page_title' => 'About Me | My Programing',
            'alamat' => [
                [
                    'tipe' => 'Admin Website',
                    'alamat' => 'Deni Rusandi',
                    'kota' => 'Phone: 081364290165 | Email: admin.app@simpedis.com'
                ],
                [
                    'tipe' => 'Sekretariat',
                    'alamat' => 'Masjid Al-Hikmah Kec. Seri Kuala Lobam',
                    'kota' => 'Kabupaten Bintan'
                ]
            ],
        ];
        return view('backend/dashboard/contact', $data);
    }

    public function login()
    {
        $data = ['page_title' => 'Login'];
        return view('backend/login/login', $data);
    }
}
