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
            'barang' => $this->barangModel->getBarangWithKategori()
        ];
        return view('backend/luckydraw/barang/index', $data);
    }

    public function store()
    {
        $this->barangModel->save([
            'no_barang' => $this->request->getPost('no_barang'),
            'kategori' => $this->request->getPost('kategori'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah' => $this->request->getPost('jumlah'),
        ]);

        session()->setFlashdata('pesan', '<div class="alert alert-success">Data berhasil disimpan.</div>');
        return redirect()->to('/backend/luckydraw/barang');
    }

    public function update($id)
    {
        $this->barangModel->update($id, [
            'no_barang' => $this->request->getPost('no_barang'),
            'kategori' => $this->request->getPost('kategori'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah' => $this->request->getPost('jumlah'),
        ]);

        session()->setFlashdata('pesan', '<div class="alert alert-success">Data berhasil diupdate.</div>');
        return redirect()->to('/backend/luckydraw/barang');
    }

    public function delete($id)
    {
        $this->barangModel->delete($id);
        session()->setFlashdata('pesan', '<div class="alert alert-success">Data berhasil dihapus.</div>');
        return redirect()->to('/backend/luckydraw/barang');
    }
}
