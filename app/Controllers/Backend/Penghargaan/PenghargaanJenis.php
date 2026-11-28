<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;

/**
 * Controller CRUD Master Jenis Penghargaan.
 */
class PenghargaanJenis extends BaseController
{
    protected $jenisModel;

    public function __construct()
    {
        $this->jenisModel = new PenghargaanJenisModel();
    }

    public function index()
    {
        $idTpq     = (string) (session()->get('IdTpq') ?? '0');
        $listJenis = $this->jenisModel->getAvailableByTpq($idTpq, false);

        return view('backend/penghargaan/jenis/index', [
            'page_title'  => 'Kelola Jenis Penghargaan',
            'menu_open'   => 'penghargaan',
            'menu_active' => 'penghargaan-jenis',
            'listJenis'   => $listJenis,
            'idTpq'       => $idTpq,
        ]);
    }

    public function store()
    {
        $idTpq = (string) (session()->get('IdTpq') ?? '0');
        $rules = [
            'Kode'            => 'required|max_length[20]',
            'NamaPenghargaan' => 'required|max_length[150]',
            'Kategori'        => 'required|in_list[akademik,kehadiran,gabungan,khusus]',
            'Momen'           => 'required|in_list[ganjil,genap,kenaikan,munaqosah,kapansaja]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->jenisModel->insert([
            'IdTpq'           => $idTpq,
            'Kode'            => strtoupper(trim((string) $this->request->getPost('Kode'))),
            'NamaPenghargaan' => trim((string) $this->request->getPost('NamaPenghargaan')),
            'Deskripsi'       => trim((string) $this->request->getPost('Deskripsi')),
            'Kategori'        => $this->request->getPost('Kategori'),
            'Momen'           => $this->request->getPost('Momen'),
            'IsDefault'       => 0,
            'IsActive'        => 1,
        ]);

        return redirect()->to(base_url('backend/penghargaan/jenis'))
            ->with('success', 'Jenis penghargaan baru berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $row = $this->jenisModel->find($id);
        if (!$row) {
            return redirect()->back()->with('error', 'Data jenis penghargaan tidak ditemukan.');
        }

        $rules = [
            'Kode'            => 'required|max_length[20]',
            'NamaPenghargaan' => 'required|max_length[150]',
            'Kategori'        => 'required|in_list[akademik,kehadiran,gabungan,khusus]',
            'Momen'           => 'required|in_list[ganjil,genap,kenaikan,munaqosah,kapansaja]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->jenisModel->update($id, [
            'Kode'            => strtoupper(trim((string) $this->request->getPost('Kode'))),
            'NamaPenghargaan' => trim((string) $this->request->getPost('NamaPenghargaan')),
            'Deskripsi'       => trim((string) $this->request->getPost('Deskripsi')),
            'Kategori'        => $this->request->getPost('Kategori'),
            'Momen'           => $this->request->getPost('Momen'),
        ]);

        return redirect()->to(base_url('backend/penghargaan/jenis'))
            ->with('success', 'Jenis penghargaan berhasil diperbarui.');
    }

    public function toggleActive(int $id)
    {
        $ok = $this->jenisModel->toggleActive($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Status berhasil diubah.' : 'Data tidak ditemukan.',
        ]);
    }

    public function delete(int $id)
    {
        $row = $this->jenisModel->find($id);
        if (!$row) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
        if (!empty($row['IsDefault'])) {
            return redirect()->back()->with('error', 'Jenis penghargaan bawaan sistem tidak dapat dihapus (gunakan tombol nonaktifkan).');
        }

        $this->jenisModel->delete($id);
        return redirect()->to(base_url('backend/penghargaan/jenis'))
            ->with('success', 'Jenis penghargaan berhasil dihapus.');
    }
}
