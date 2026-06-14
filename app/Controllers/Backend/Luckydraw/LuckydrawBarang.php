<?php

namespace App\Controllers\Backend\Luckydraw;

use App\Controllers\BaseController;
use App\Models\Backend\Luckydraw\LuckydrawBarangModel;

class LuckydrawBarang extends BaseController
{
    protected $barangModel;
    public function __construct()
    {
        $this->barangModel = new LuckydrawBarangModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Data Barang Lucky Draw',
            'barang' => $this->barangModel->getBarangWithSisa(),
            'next_no_barang' => $this->barangModel->getNextNoBarang()
        ];
        return view('backend/luckydraw/barang/index', $data);
    }

    public function store()
    {
        $kategori = trim($this->request->getPost('kategori'));
        $nama_barang = trim($this->request->getPost('nama_barang'));
        $isAjax = $this->request->isAJAX();

        // Validation for duplicate Kategori (Grup) + Nama Barang
        $exist = $this->barangModel->where('kategori', $kategori)
                                   ->where('nama_barang', $nama_barang)
                                   ->first();
        if ($exist) {
            $msg = 'Gagal! Kombinasi Kategori/Grup "' . $kategori . '" dan Nama Barang "' . $nama_barang . '" sudah terdaftar.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . esc($msg) . '</div>');
            return redirect()->to('/backend/luckydraw/barang');
        }

        // Generate auto number
        $no_barang = $this->barangModel->getNextNoBarang();

        $this->barangModel->save([
            'no_barang' => $no_barang,
            'kategori' => $kategori,
            'nama_barang' => $nama_barang,
            'jumlah' => $this->request->getPost('jumlah'),
        ]);

        $msg = 'Data barang berhasil disimpan dengan No Barang: ' . $no_barang;
        if ($isAjax) {
            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        }
        session()->setFlashdata('pesan', '<div class="alert alert-success">' . esc($msg) . '</div>');
        return redirect()->to('/backend/luckydraw/barang');
    }

    public function update($id)
    {
        $kategori = trim($this->request->getPost('kategori'));
        $nama_barang = trim($this->request->getPost('nama_barang'));
        $isAjax = $this->request->isAJAX();

        // Validation for duplicate Kategori (Grup) + Nama Barang (exclude current ID)
        $exist = $this->barangModel->where('kategori', $kategori)
                                   ->where('nama_barang', $nama_barang)
                                   ->where('id !=', $id)
                                   ->first();
        if ($exist) {
            $msg = 'Gagal! Kombinasi Kategori/Grup "' . $kategori . '" dan Nama Barang "' . $nama_barang . '" sudah digunakan oleh barang lain.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-danger">' . esc($msg) . '</div>');
            return redirect()->to('/backend/luckydraw/barang');
        }

        $this->barangModel->update($id, [
            'kategori' => $kategori,
            'nama_barang' => $nama_barang,
            'jumlah' => $this->request->getPost('jumlah'),
        ]);

        $msg = 'Data barang berhasil diperbarui.';
        if ($isAjax) {
            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        }
        session()->setFlashdata('pesan', '<div class="alert alert-success">' . esc($msg) . '</div>');
        return redirect()->to('/backend/luckydraw/barang');
    }

    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        // Delete all associated winners first
        $undianModel = new \App\Models\Backend\Luckydraw\LuckydrawUndianModel();
        $undianModel->where('id_barang', $id)->delete();

        // Delete the item itself
        $this->barangModel->delete($id);
        
        $msg = 'Data barang beserta seluruh data pemenangnya berhasil dihapus.';
        if ($isAjax) {
            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        }
        session()->setFlashdata('pesan', '<div class="alert alert-success">' . esc($msg) . '</div>');
        return redirect()->to('/backend/luckydraw/barang');
    }
}
