<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratKategoriModel;

class SuratKategori extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new SuratKategoriModel();
    }

    public function index()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $kategori = $this->kategoriModel->orderBy('Id', 'ASC')->findAll();

        $data = [
            'page_title' => 'Kategori Surat',
            'kategori'   => $kategori,
            'isAdmin'    => $isAdmin,
            'idTpq'      => $idTpq,
        ];

        return view('backend/administrasi/kategori/index', $data);
    }

    public function store()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        if (!$isAdmin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya Admin (FKPQ) yang berhak menambah kategori surat.'
            ]);
        }

        $rules = [
            'NamaKategori' => 'required|min_length[3]',
            'Kode'         => 'required|min_length[2]|is_unique[tbl_surat_kategori.Kode]',
            'JenisSurat'   => 'required|in_list[Keluar,Masuk,Keduanya]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $saveData = [
            'NamaKategori' => $this->request->getPost('NamaKategori'),
            'Kode'         => strtoupper(trim($this->request->getPost('Kode'))),
            'JenisSurat'   => $this->request->getPost('JenisSurat'),
            'Deskripsi'    => $this->request->getPost('Deskripsi'),
            'Icon'         => $this->request->getPost('Icon') ?: 'fas fa-envelope',
            'Warna'        => $this->request->getPost('Warna') ?: 'primary',
            'IsActive'     => 1,
        ];

        if ($this->kategoriModel->insert($saveData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori surat berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan kategori surat'
        ]);
    }

    public function update($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        if (!$isAdmin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya Admin yang berhak mengubah kategori surat.'
            ]);
        }

        $existing = $this->kategoriModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $rules = [
            'NamaKategori' => 'required|min_length[3]',
            'Kode'         => "required|min_length[2]|is_unique[tbl_surat_kategori.Kode,Id,{$id}]",
            'JenisSurat'   => 'required|in_list[Keluar,Masuk,Keduanya]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $updateData = [
            'NamaKategori' => $this->request->getPost('NamaKategori'),
            'Kode'         => strtoupper(trim($this->request->getPost('Kode'))),
            'JenisSurat'   => $this->request->getPost('JenisSurat'),
            'Deskripsi'    => $this->request->getPost('Deskripsi'),
            'Icon'         => $this->request->getPost('Icon') ?: 'fas fa-envelope',
            'Warna'        => $this->request->getPost('Warna') ?: 'primary',
        ];

        if ($this->kategoriModel->update($id, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori surat berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal memperbarui kategori surat'
        ]);
    }

    public function delete($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        if (!$isAdmin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya Admin yang berhak menghapus kategori surat.'
            ]);
        }

        if ($this->kategoriModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kategori surat berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus kategori surat'
        ]);
    }

    public function toggleActive($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        if (!$isAdmin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        if ($this->kategoriModel->toggleAktif($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status kategori berhasil diubah'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengubah status kategori'
        ]);
    }
}
