<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratTujuanMasterModel;
use App\Models\HelpFunctionModel;

class SuratTujuan extends BaseController
{
    protected $tujuanModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->tujuanModel  = new SuratTujuanMasterModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $tujuan  = $this->tujuanModel->getByTpq($isAdmin ? null : $idTpq);
        $listTpq = $this->helpFunction->getDataTpq(false);

        $data = [
            'page_title' => 'Database Tujuan Surat',
            'tujuan'     => $tujuan,
            'listTpq'    => $listTpq,
            'idTpq'      => $idTpq,
            'isAdmin'    => $isAdmin,
        ];

        return view('backend/administrasi/tujuan/index', $data);
    }

    public function store()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $rules = [
            'NamaTujuan'     => 'required|min_length[2]',
            'KategoriTujuan' => 'required|in_list[Pejabat Pemerintah,Lembaga,Internal,Perorangan]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $targetTpq = $isAdmin ? ($this->request->getPost('IdTpq') ?: null) : $idTpq;

        $saveData = [
            'IdTpq'          => $targetTpq,
            'KategoriTujuan' => $this->request->getPost('KategoriTujuan'),
            'NamaTujuan'     => $this->request->getPost('NamaTujuan'),
            'Jabatan'        => $this->request->getPost('Jabatan'),
            'Instansi'       => $this->request->getPost('Instansi'),
            'Alamat'         => $this->request->getPost('Alamat'),
            'Kota'           => $this->request->getPost('Kota'),
            'NoHp'           => $this->request->getPost('NoHp'),
            'Email'          => $this->request->getPost('Email'),
            'IsActive'       => 1,
        ];

        if ($this->tujuanModel->insert($saveData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tujuan surat berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan tujuan surat'
        ]);
    }

    public function update($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->tujuanModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq && !empty($existing['IdTpq'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki hak mengubah data ini']);
        }

        $rules = [
            'NamaTujuan'     => 'required|min_length[2]',
            'KategoriTujuan' => 'required|in_list[Pejabat Pemerintah,Lembaga,Internal,Perorangan]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $targetTpq = $isAdmin ? ($this->request->getPost('IdTpq') ?: null) : $existing['IdTpq'];

        $updateData = [
            'IdTpq'          => $targetTpq,
            'KategoriTujuan' => $this->request->getPost('KategoriTujuan'),
            'NamaTujuan'     => $this->request->getPost('NamaTujuan'),
            'Jabatan'        => $this->request->getPost('Jabatan'),
            'Instansi'       => $this->request->getPost('Instansi'),
            'Alamat'         => $this->request->getPost('Alamat'),
            'Kota'           => $this->request->getPost('Kota'),
            'NoHp'           => $this->request->getPost('NoHp'),
            'Email'          => $this->request->getPost('Email'),
        ];

        if ($this->tujuanModel->update($id, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tujuan surat berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal memperbarui tujuan surat'
        ]);
    }

    public function delete($id = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $existing = $this->tujuanModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if (!$isAdmin && $existing['IdTpq'] != $idTpq) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki hak menghapus data ini']);
        }

        if ($this->tujuanModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tujuan surat berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus tujuan surat'
        ]);
    }

    public function getByKategori($kategori = null)
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $data = $this->tujuanModel->getActiveByKategori($kategori, $isAdmin ? null : $idTpq);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }
}
