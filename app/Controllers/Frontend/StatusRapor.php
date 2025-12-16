<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\SerahTerimaRaporModel;
use App\Models\SantriBaruModel;
use App\Models\HelpFunctionModel;

class StatusRapor extends BaseController
{
    protected $serahTerimaRaporModel;
    protected $santriBaruModel;
    protected $helpFunctionModel;
    protected $db;

    public function __construct()
    {
        $this->serahTerimaRaporModel = new SerahTerimaRaporModel();
        $this->santriBaruModel = new SantriBaruModel();
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Halaman cek status rapor via hashkey
     * Dapat menerima HasKey sebagai parameter di URL: /cek-status-rapor/{hasKey}
     */
    public function index($hasKey = null)
    {
        // Jika HasKey diberikan langsung di URL, langsung tampilkan status
        if (!empty($hasKey)) {
            $data = $this->getStatusByHasKey($hasKey);
            
            if (empty($data)) {
                return redirect()->to(base_url('cek-status-rapor'))
                    ->with('error', 'HasKey tidak valid atau tidak ditemukan. Silakan masukkan HasKey yang benar.');
            }

            $data['page_title'] = 'Status Serah Terima Rapor';
            $data['isPublic'] = true;
            $data['hasKey'] = $hasKey;

            return view('frontend/rapor/statusRapor', $data);
        }
        
        // Jika tidak ada HasKey di URL, tampilkan form input
        $data = [
            'page_title' => 'Cek Status Serah Terima Rapor',
            'isPublic' => true,
            'hasKey' => null,
            'statusData' => null
        ];

        return view('frontend/statusRapor', $data);
    }

    /**
     * Get status data berdasarkan HasKey
     */
    private function getStatusByHasKey($hasKey)
    {
        if (empty($hasKey)) {
            return null;
        }
        
        // Ambil data serah terima berdasarkan HasKey
        $serahTerima = $this->serahTerimaRaporModel->getByHasKey($hasKey);
        
        if (empty($serahTerima)) {
            return null;
        }

        // Ambil data santri
        $santri = $this->santriBaruModel->where('IdSantri', $serahTerima['IdSantri'])->first();
        
        // Ambil semua transaksi untuk santri ini (semester dan tahun ajaran yang sama)
        $allTransactions = $this->serahTerimaRaporModel->getBySantri(
            $serahTerima['IdSantri'],
            $serahTerima['idTahunAjaran'],
            $serahTerima['Semester']
        );

        // Ambil data guru
        $guru = $this->db->table('tbl_guru')
            ->where('IdGuru', $serahTerima['IdGuru'])
            ->get()
            ->getRowArray();
        
        // Pastikan $guru adalah array, jika null set ke array kosong
        if (empty($guru)) {
            $guru = [];
        }

        // Ambil data kelas
        $kelas = $this->db->table('tbl_kelas')
            ->where('IdKelas', $serahTerima['IdKelas'])
            ->get()
            ->getRowArray();

        // Ambil data TPQ
        $tpq = $this->db->table('tbl_tpq')
            ->where('IdTpq', $serahTerima['IdTpq'])
            ->get()
            ->getRowArray();

        // Cari transaksi Serah dan Terima
        $transaksiSerah = null;
        $transaksiTerima = null;
        $guruTerima = null;

        foreach ($allTransactions as $trans) {
            if ($trans['Transaksi'] === 'Serah' && $trans['HasKey'] === $hasKey) {
                $transaksiSerah = $trans;
            }
            if ($trans['Transaksi'] === 'Terima') {
                $transaksiTerima = $trans;
                // Ambil data guru yang menerima pengembalian
                if (!empty($trans['IdGuru'])) {
                    $guruTerima = $this->db->table('tbl_guru')
                        ->where('IdGuru', $trans['IdGuru'])
                        ->get()
                        ->getRowArray();
                    
                    // Pastikan $guruTerima adalah array, jika null set ke array kosong
                    if (empty($guruTerima)) {
                        $guruTerima = [];
                    }
                }
            }
        }

        // Jika tidak ada guru terima, gunakan guru dari serah terima
        if (empty($guruTerima)) {
            $guruTerima = $guru;
        }

        return [
            'santri' => $santri,
            'kelas' => $kelas,
            'tpq' => $tpq,
            'guru' => $guru,
            'guruTerima' => $guruTerima,
            'transaksiSerah' => $transaksiSerah,
            'transaksiTerima' => $transaksiTerima,
            'statusData' => $serahTerima,
            'allTransactions' => $allTransactions
        ];
    }

    /**
     * API endpoint untuk mendapatkan status via AJAX
     */
    public function getStatusByHasKeyAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $hasKey = $this->request->getPost('hasKey');
        
        if (empty($hasKey)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'HasKey harus diisi'
            ]);
        }

        $data = $this->getStatusByHasKey($hasKey);

        if (empty($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'HasKey tidak valid atau tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }
}

