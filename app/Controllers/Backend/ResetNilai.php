<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\TpqModel;
use App\Models\HelpFunctionModel;

class ResetNilai extends BaseController
{
    protected $nilaiModel;
    protected $tpqModel;
    protected $helpFunctionModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
        $this->tpqModel = new TpqModel();
        $this->helpFunctionModel = new HelpFunctionModel();
    }

    /**
     * Menampilkan halaman reset nilai
     */
    public function index()
    {
        // Cek apakah user adalah Admin
        if (!in_groups('Admin')) {
            return redirect()->to('/auth/index')->with('error', 'Akses ditolak');
        }

        // Ambil data TPQ untuk dropdown dengan urutan ASC berdasarkan NamaTpq
        $dataTpq = $this->tpqModel->orderBy('NamaTpq', 'ASC')->findAll();
        
        // Ambil daftar tahun ajaran dari tabel nilai
        $tahunAjaranList = $this->getTahunAjaranList();

        $data = [
            'page_title' => 'Reset Nilai',
            'dataTpq' => $dataTpq,
            'tahunAjaranList' => $tahunAjaranList,
            'helpFunctionModel' => $this->helpFunctionModel,
        ];

        return view('backend/nilai/resetNilai', $data);
    }

    /**
     * Mendapatkan count data yang akan direset berdasarkan filter
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

        $IdTpq = $this->request->getPost('IdTpq');
        $IdTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $Semester = $this->request->getPost('Semester');

        // Validasi minimal satu filter harus diisi
        if (empty($IdTpq) && empty($IdTahunAjaran) && empty($Semester)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu filter harus diisi'
            ]);
        }

        try {
            $result = $this->nilaiModel->getCountNilaiByFilter($IdTpq, $IdTahunAjaran, $Semester);
            
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
     * Proses reset nilai berdasarkan filter
     */
    public function reset()
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

        $selectedClasses = $this->request->getPost('selectedClasses');

        // Validasi selected classes
        if (empty($selectedClasses) || !is_array($selectedClasses) || count($selectedClasses) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu kelas harus dipilih'
            ]);
        }

        try {
            $result = $this->nilaiModel->resetNilaiBySelectedClasses($selectedClasses);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Reset nilai berhasil dilakukan',
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
     * Mendapatkan daftar tahun ajaran dari tabel nilai
     */
    private function getTahunAjaranList()
    {
        $builder = $this->nilaiModel->db->table('tbl_nilai');
        $builder->select('IdTahunAjaran');
        $builder->distinct();
        $builder->orderBy('IdTahunAjaran', 'DESC');
        
        $results = $builder->get()->getResultArray();
        
        $tahunAjaranList = [];
        foreach ($results as $row) {
            if (!empty($row['IdTahunAjaran'])) {
                $tahunAjaranList[] = $row['IdTahunAjaran'];
            }
        }
        
        return $tahunAjaranList;
    }
}

