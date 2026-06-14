<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\Backend\Luckydraw\LuckydrawUndianModel;

class Luckydraw extends BaseController
{
    protected $undianModel;

    public function __construct()
    {
        $this->undianModel = new LuckydrawUndianModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Cek Undian Lucky Draw',
        ];
        // We'll assume there is a frontend layout. Let's see if there is one. We can just use the backend layout if none exists or a basic layout.
        return view('frontend/luckydraw/index', $data);
    }

    public function search()
    {
        $no_undian = $this->request->getPost('no_undian');
        
        // Find if this number won
        $pemenang = $this->undianModel->select('tbl_luckydraw_undian.*, tbl_luckydraw_barang.nama_barang, tbl_luckydraw_barang.no_barang, tbl_luckydraw_barang.kategori')
                                      ->join('tbl_luckydraw_barang', 'tbl_luckydraw_barang.id = tbl_luckydraw_undian.id_barang')
                                      ->where('no_undian', $no_undian)
                                      ->first();

        if ($pemenang) {
            return $this->response->setJSON([
                'status' => 'success',
                'is_winner' => true,
                'data' => $pemenang
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'is_winner' => false,
            'message' => 'Maaf, nomor undian ' . $no_undian . ' belum beruntung.'
        ]);
    }

    public function list()
    {
        $data = [
            'page_title' => 'Daftar Pemenang Lucky Draw',
            'pemenang' => $this->undianModel->getPemenangList()
        ];
        return view('frontend/luckydraw/list', $data);
    }
}
