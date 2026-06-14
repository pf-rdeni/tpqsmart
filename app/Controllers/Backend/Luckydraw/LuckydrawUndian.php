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
            'barang' => $this->barangModel->getBarangWithSisa(),
            'pemenang' => $this->undianModel->getPemenangList(),
            'last_selected_id_barang' => session()->get('last_selected_id_barang')
        ];
        return view('backend/luckydraw/undian/input', $data);
    }

    public function store()
    {
        $id_barang = $this->request->getPost('id_barang');
        $no_undian = $this->request->getPost('no_undian');
        $isAjax = $this->request->isAJAX();

        // Store the selection in session so it persists after redirect
        session()->set('last_selected_id_barang', $id_barang);

        // Validate if item exists
        $barang = $this->barangModel->find($id_barang);
        if (!$barang) {
            $msg = 'Barang tidak ditemukan.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . $msg . '</div>');
            return redirect()->to('/backend/luckydraw/undian');
        }

        // Validate remaining stock
        $winnerCount = $this->undianModel->where('id_barang', $id_barang)->countAllResults();
        if ($winnerCount >= $barang->jumlah) {
            $msg = 'Gagal! Stok barang hadiah "' . esc($barang->nama_barang) . '" sudah terisi penuh / habis.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . $msg . '</div>');
            return redirect()->to('/backend/luckydraw/undian');
        }

        // Check if number already exists
        $exist = $this->undianModel->where('no_undian', $no_undian)->first();
        if ($exist) {
            $msg = 'Nomor undian tersebut sudah terdaftar sebagai pemenang.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . $msg . '</div>');
            return redirect()->to('/backend/luckydraw/undian');
        }

        $this->undianModel->save([
            'id_barang' => $id_barang,
            'no_undian' => $no_undian,
            'status_diambil' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $msg = 'Pemenang berhasil ditambahkan.';
        if ($isAjax) {
            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        }
        session()->setFlashdata('pesan', '<div class="alert alert-success">' . $msg . '</div>');
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
