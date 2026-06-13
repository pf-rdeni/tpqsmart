<?php

namespace App\Controllers\Backend\Luckydraw;

use App\Controllers\BaseController;
use App\Models\Backend\Luckydraw\LuckydrawUndianModel;
use App\Models\Backend\Luckydraw\LuckydrawBarangModel;

class LuckydrawUndian extends BaseController
{
    protected $undianModel;
    protected $barangModel;

    public function __construct()
    {
        $this->undianModel = new LuckydrawUndianModel();
        $this->barangModel = new LuckydrawBarangModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Input Pemenang Lucky Draw',
            'barang' => $this->barangModel->findAll(),
            'pemenang' => $this->undianModel->getPemenangList()
        ];
        return view('backend/luckydraw/undian/input', $data);
    }

    public function store()
    {
        $id_barang = $this->request->getPost('id_barang');
        $no_undian = $this->request->getPost('no_undian');

        // Check if number already exists
        $exist = $this->undianModel->where('no_undian', $no_undian)->first();
        if ($exist) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger">Nomor undian tersebut sudah terdaftar sebagai pemenang.</div>');
            return redirect()->to('/backend/luckydraw/undian');
        }

        $this->undianModel->save([
            'id_barang' => $id_barang,
            'no_undian' => $no_undian,
            'status_diambil' => 0
        ]);

        session()->setFlashdata('pesan', '<div class="alert alert-success">Pemenang berhasil ditambahkan.</div>');
        return redirect()->to('/backend/luckydraw/undian');
    }

    public function verifikasi()
    {
        $data = [
            'page_title' => 'Verifikasi Pemenang Lucky Draw',
            'pemenang' => $this->undianModel->getPemenangList()
        ];
        return view('backend/luckydraw/undian/verifikasi', $data);
    }

    public function prosesSerahTerima()
    {
        $no_undian = $this->request->getPost('no_undian');
        $pemenang = $this->undianModel->where('no_undian', $no_undian)->first();

        if ($pemenang) {
            if ($pemenang->status_diambil == 1) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Hadiah untuk nomor undian ini sudah diambil.']);
            }

            $this->undianModel->update($pemenang->id, [
                'status_diambil' => 1,
                'waktu_diambil' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Verifikasi berhasil. Status telah diubah menjadi "Sudah Diambil".']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor undian tidak ditemukan atau belum menang.']);
    }
}
