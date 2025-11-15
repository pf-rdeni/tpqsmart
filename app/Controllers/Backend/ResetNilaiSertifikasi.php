<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SertifikasiNilaiModel;

class ResetNilaiSertifikasi extends BaseController
{
    protected $sertifikasiNilaiModel;

    public function __construct()
    {
        $this->sertifikasiNilaiModel = new SertifikasiNilaiModel();
    }

    /**
     * Menampilkan halaman hapus nilai sertifikasi
     */
    public function index()
    {
        // Cek apakah user adalah Admin
        if (!in_groups('Admin')) {
            return redirect()->to('/auth/index')->with('error', 'Akses ditolak');
        }

        $data = [
            'page_title' => 'Hapus Nilai Sertifikasi',
        ];

        return view('backend/nilai/resetNilaiSertifikasi', $data);
    }

    /**
     * Mendapatkan count data yang akan dihapus berdasarkan filter
     */
    public function getCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Cek apakah user adalah Admin
        if (!in_groups('Admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ]);
        }

        $NoPeserta = $this->request->getPost('NoPeserta');
        
        // Jika NoPeserta kosong, set ke null untuk melihat semua data
        if (empty($NoPeserta) || trim($NoPeserta) === '') {
            $NoPeserta = null;
        }

        try {
            $result = $this->sertifikasiNilaiModel->getCountNilaiByFilter($NoPeserta);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Proses hapus nilai berdasarkan NoPeserta
     */
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Cek apakah user adalah Admin
        if (!in_groups('Admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ]);
        }

        $selectedPeserta = $this->request->getPost('selectedPeserta');

        // Validasi selected peserta
        if (empty($selectedPeserta) || !is_array($selectedPeserta) || count($selectedPeserta) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu peserta harus dipilih'
            ]);
        }

        try {
            $result = $this->sertifikasiNilaiModel->deleteNilaiBySelectedPeserta($selectedPeserta);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Hapus nilai sertifikasi berhasil dilakukan',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}

