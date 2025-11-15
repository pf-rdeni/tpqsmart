<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class ResetNilaiIndex extends BaseController
{
    /**
     * Menampilkan halaman index reset nilai dengan menu
     */
    public function index()
    {
        // Cek apakah user adalah Admin
        if (!in_groups('Admin')) {
            return redirect()->to('/auth/index')->with('error', 'Akses ditolak');
        }

        $data = [
            'page_title' => 'Reset Nilai',
        ];

        return view('backend/nilai/resetNilaiIndex', $data);
    }
}

