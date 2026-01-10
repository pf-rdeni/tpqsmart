<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class Dokumentasi extends BaseController
{
    public function alurAbsensiGuru()
    {
        $data = [
            'page_title' => 'Alur Absensi Guru',
            'menu_open' => 'dokumentasi',
            'menu_active' => 'alur-absensi-guru'
        ];
        return view('backend/dokumentasi/absensiGuru/AlurAbsensiGuru', $data);
    }

    public function prosesMembuatAbsensi()
    {
        $data = [
            'page_title' => 'Dokumentasi: Proses Membuat Absensi',
        ];
        return view('backend/dokumentasi/absensiGuru/ProsesMembuatAbsensi', $data);
    }

    public function perlombaan()
    {
        $data = [
            'page_title' => 'Dokumentasi: Modul Perlombaan',
        ];
        return view('backend/dokumentasi/perlombaan/index', $data);
    }

    public function perlombaanSetup()
    {
        $data = [
            'page_title' => 'Dokumentasi: Setup Perlombaan',
        ];
        return view('backend/dokumentasi/perlombaan/setup', $data);
    }

    public function perlombaanPelaksanaan()
    {
        $data = [
            'page_title' => 'Dokumentasi: Pelaksanaan Perlombaan',
        ];
        return view('backend/dokumentasi/perlombaan/pelaksanaan', $data);
    }
}
