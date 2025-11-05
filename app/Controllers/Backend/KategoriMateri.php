<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KategoriMateriModel;
use App\Models\MunaqosahKategoriKesalahanModel;

class KategoriMateri extends BaseController
{
    protected $kategoriMateriModel;
    protected $munaqosahKategoriKesalahanModel;

    public function __construct()
    {
        $this->kategoriMateriModel = new KategoriMateriModel();
        $this->munaqosahKategoriKesalahanModel = new MunaqosahKategoriKesalahanModel();
    }

    /**
     * Display list kategori materi
     */
    public function index()
    {
        $data = [
            'page_title' => 'Data Kategori Materi'
        ];
        return view('backend/KategoriMateri/index', $data);
    }

    /**
     * Get all kategori materi
     */
    public function getKategoriMateri()
    {
        try {
            $kategori = $this->kategoriMateriModel->orderBy('IdKategoriMateri', 'ASC')->findAll();
            return $this->response->setJSON([
                'success' => true,
                'data' => $kategori
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save kategori materi
     */
    public function saveKategoriMateri()
    {
        try {
            $rules = [
                'IdKategoriMateri' => 'required|max_length[50]|is_unique[tbl_kategori_materi.IdKategoriMateri]',
                'NamaKategoriMateri' => 'required|max_length[255]',
                'Status' => 'required|in_list[Aktif,Tidak Aktif]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'IdKategoriMateri' => strtoupper($this->request->getPost('IdKategoriMateri')),
                'NamaKategoriMateri' => $this->request->getPost('NamaKategoriMateri'),
                'Status' => $this->request->getPost('Status')
            ];

            if ($this->kategoriMateriModel->save($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data kategori materi berhasil disimpan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data',
                    'errors' => $this->kategoriMateriModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update kategori materi
     */
    public function updateKategoriMateri($id)
    {
        try {
            $rules = [
                'NamaKategoriMateri' => 'required|max_length[255]',
                'Status' => 'required|in_list[Aktif,Tidak Aktif]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'NamaKategoriMateri' => $this->request->getPost('NamaKategoriMateri'),
                'Status' => $this->request->getPost('Status')
            ];

            if ($this->kategoriMateriModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data kategori materi berhasil diupdate'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate data',
                    'errors' => $this->kategoriMateriModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete kategori materi
     */
    public function deleteKategoriMateri($id)
    {
        try {
            $kategori = $this->kategoriMateriModel->find($id);
            if (!$kategori) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // Check if kategori is used in kategori kesalahan
            $isUsed = $this->munaqosahKategoriKesalahanModel->where('IdKategoriMateri', $kategori['IdKategoriMateri'])->first();

            if ($isUsed) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori materi tidak dapat dihapus karena sudah digunakan'
                ]);
            }

            if ($this->kategoriMateriModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data kategori materi berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get kategori materi for dropdown
     */
    public function getKategoriMateriForDropdown()
    {
        try {
            $kategori = $this->kategoriMateriModel
                ->where('Status', 'Aktif')
                ->orderBy('NamaKategoriMateri', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $kategori
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }
}

