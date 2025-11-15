<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MunaqosahNilaiModel;
use App\Models\TpqModel;
use App\Models\HelpFunctionModel;

class ResetNilaiMunaqosah extends BaseController
{
    protected $munaqosahNilaiModel;
    protected $tpqModel;
    protected $helpFunctionModel;

    public function __construct()
    {
        $this->munaqosahNilaiModel = new MunaqosahNilaiModel();
        $this->tpqModel = new TpqModel();
        $this->helpFunctionModel = new HelpFunctionModel();
    }

    /**
     * Menampilkan halaman reset nilai munaqosah
     */
    public function index()
    {
        // Cek apakah user adalah Admin
        if (!in_groups('Admin')) {
            return redirect()->to('/auth/index')->with('error', 'Akses ditolak');
        }

        // Ambil data TPQ untuk dropdown dengan urutan ASC berdasarkan NamaTpq
        $dataTpq = $this->tpqModel->orderBy('NamaTpq', 'ASC')->findAll();
        
        // Ambil daftar tahun ajaran dari tabel munaqosah nilai
        $tahunAjaranList = $this->getTahunAjaranList();

        $data = [
            'page_title' => 'Hapus Nilai Munaqosah',
            'dataTpq' => $dataTpq,
            'tahunAjaranList' => $tahunAjaranList,
            'helpFunctionModel' => $this->helpFunctionModel,
        ];

        return view('backend/nilai/resetNilaiMunaqosah', $data);
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
        $TypeUjian = $this->request->getPost('TypeUjian');

        // Validasi minimal satu filter harus diisi
        if (empty($IdTpq) && empty($IdTahunAjaran) && empty($TypeUjian)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu filter harus diisi'
            ]);
        }

        try {
            $result = $this->munaqosahNilaiModel->getCountNilaiByFilter($IdTpq, $IdTahunAjaran, $TypeUjian);
            
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

        $selectedPeserta = $this->request->getPost('selectedPeserta');

        // Validasi selected peserta
        if (empty($selectedPeserta) || !is_array($selectedPeserta) || count($selectedPeserta) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu peserta harus dipilih'
            ]);
        }

        try {
            $result = $this->munaqosahNilaiModel->deleteNilaiBySelectedPeserta($selectedPeserta);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Hapus nilai munaqosah berhasil dilakukan',
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
     * Mendapatkan daftar tahun ajaran dari tabel munaqosah nilai
     */
    private function getTahunAjaranList()
    {
        $builder = $this->munaqosahNilaiModel->db->table('tbl_munaqosah_nilai');
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

