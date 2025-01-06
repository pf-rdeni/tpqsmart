<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\GuruModel;


class Guru extends BaseController
{
    protected $DataModels;
    public function __construct()
    {
        $this->DataModels = new GuruModel();
    }

    public function show()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');
        // query data guru berdasarkan IdTpq jika idtpq tidak ada maka akan menampilkan semua data guru
        if ($IdTpq == null) {
            $data = [
                'page_title' => 'Data Guru',
                'guru' => $this->DataModels->findAll()
            ];
        } else {
            $data = [
                'page_title' => 'Data Guru',
                'guru' => $this->DataModels->where('IdTpq', $IdTpq)->findAll()
            ];
        }
        return view('backend/guru/guru', $data);
    }
}