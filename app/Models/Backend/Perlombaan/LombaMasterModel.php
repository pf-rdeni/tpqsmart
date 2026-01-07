<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaMasterModel extends Model
{
    protected $table = 'tbl_lomba_master';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'NamaLomba',
        'Deskripsi',
        'TanggalMulai',
        'TanggalSelesai',
        'IdTpq',
        'IdTahunAjaran',
        'Status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'NamaLomba' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'NamaLomba' => [
            'required' => 'Nama lomba harus diisi',
            'max_length' => 'Nama lomba maksimal 255 karakter'
        ]
    ];

    /**
     * Ambil semua lomba yang aktif
     */
    public function getLombaAktif()
    {
        return $this->where('Status', 'aktif')
                    ->orderBy('TanggalMulai', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil lomba berdasarkan TPQ
     */
    public function getLombaByTpq($idTpq)
    {
        return $this->where('IdTpq', $idTpq)
                    ->orWhere('IdTpq IS NULL')
                    ->orderBy('TanggalMulai', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil lomba beserta daftar cabangnya
     */
    public function getLombaWithCabang($id)
    {
        $lomba = $this->find($id);
        if (!$lomba) {
            return null;
        }

        $cabangModel = new LombaCabangModel();
        $lomba['cabang'] = $cabangModel->getCabangByLomba($id);

        return $lomba;
    }

    /**
     * Ambil lomba beserta statistiknya
     */
    public function getLombaWithStats($id)
    {
        $db = \Config\Database::connect();
        
        // Get lomba with TPQ name
        $lomba = $db->table('tbl_lomba_master as l')
                    ->select('l.*, t.NamaTpq, t.KelurahanDesa')
                    ->join('tbl_tpq as t', 't.IdTpq = l.IdTpq', 'left')
                    ->where('l.id', $id)
                    ->get()
                    ->getRowArray();
        
        if (!$lomba) {
            return null;
        }
        
        // Hitung total cabang
        $cabangCount = $db->table('tbl_lomba_cabang')
                          ->where('lomba_id', $id)
                          ->countAllResults();
        
        // Hitung total peserta
        $pesertaCount = $db->table('tbl_lomba_peserta')
                           ->where('lomba_id', $id)
                           ->where('StatusPendaftaran', 'valid')
                           ->countAllResults();

        $lomba['total_cabang'] = $cabangCount;
        $lomba['total_peserta'] = $pesertaCount;

        return $lomba;
    }

    /**
     * Ambil semua lomba untuk tahun ajaran tertentu
     */
    public function getLombaByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                    ->orderBy('TanggalMulai', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil daftar lomba dengan detail TPQ (Nama & Kelurahan)
     * Digunakan untuk dropdown filter agar konsisten di seluruh menu.
     * 
     * @param int|null $idTpq Jika diisi, hanya ambil lomba milik TPQ tersebut + lomba umum (null)
     * @param string $status Filter status (default: aktif)
     * @param bool $includeUmum Jika false dan $idTpq diset, hanya ambil lomba TPQ tanpa lomba umum
     * @return array
     */
    public function getLombaListDetailed($idTpq = null, $status = 'aktif', $includeUmum = true)
    {
        $builder = $this->db->table($this->table . ' l');
        $builder->select('l.*, t.NamaTpq, t.KelurahanDesa');
        $builder->join('tbl_tpq t', 't.IdTpq = l.IdTpq', 'left');
        
        if ($idTpq !== null) {
            if ($includeUmum) {
                // Include lomba TPQ + lomba umum
                $builder->groupStart()
                        ->where('l.IdTpq', $idTpq)
                        ->orWhere('l.IdTpq IS NULL')
                        ->groupEnd();
            } else {
                // Hanya lomba TPQ, tanpa lomba umum
                $builder->where('l.IdTpq', $idTpq);
            }
        }

        if ($status) {
            $builder->where('l.Status', $status);
        }

        $builder->orderBy('l.TanggalMulai', 'DESC');
        $builder->orderBy('l.NamaLomba', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Ambil statistik global untuk dashboard
     * @param int|null $idTpq Filter berdasarkan TPQ (untuk Operator)
     * @return array
     */
    public function getGlobalStats($idTpq = null)
    {
        $db = \Config\Database::connect();
        
        $stats = [];
        
        // 1. Total Lomba (Milik TPQ + Umum/Pusat)
        $lombaBuilder = $db->table('tbl_lomba_master');
        if ($idTpq) {
            $lombaBuilder->groupStart()
                         ->where('IdTpq', $idTpq)
                         ->orWhere('IdTpq IS NULL')
                         ->groupEnd();
        }
        $stats['total_lomba'] = $lombaBuilder->where('Status', 'aktif')->countAllResults();
        
        // 2. Total Cabang Aktif
        $cabangBuilder = $db->table('tbl_lomba_cabang c');
        $cabangBuilder->join('tbl_lomba_master l', 'l.id = c.lomba_id');
        $cabangBuilder->where('l.Status', 'aktif');
        $cabangBuilder->where('c.Status', 'aktif');
        if ($idTpq) {
            $cabangBuilder->groupStart()
                          ->where('l.IdTpq', $idTpq)
                          ->orWhere('l.IdTpq IS NULL')
                          ->groupEnd();
        }
        $stats['total_cabang'] = $cabangBuilder->countAllResults();
        
        // 3. Total Peserta (Valid)
        $pesertaBuilder = $db->table('tbl_lomba_peserta p');
        $pesertaBuilder->join('tbl_lomba_master l', 'l.id = p.lomba_id');
        $pesertaBuilder->where('l.Status', 'aktif');
        $pesertaBuilder->where('p.StatusPendaftaran', 'valid');
        if ($idTpq) {
            $pesertaBuilder->where('p.IdTpq', $idTpq);
        }
        $stats['total_peserta'] = $pesertaBuilder->countAllResults();
        
        // 4. Total Juri
        $juriBuilder = $db->table('tbl_lomba_juri j');
        $juriBuilder->join('tbl_lomba_cabang c', 'c.id = j.cabang_id');
        $juriBuilder->join('tbl_lomba_master l', 'l.id = c.lomba_id');
        $juriBuilder->where('l.Status', 'aktif');
        $juriBuilder->where('j.Status', 'Aktif');
        if ($idTpq) {
            $juriBuilder->groupStart()
                        ->where('l.IdTpq', $idTpq)
                        ->orWhere('l.IdTpq IS NULL')
                        ->groupEnd();
        }
        $stats['total_juri'] = $juriBuilder->countAllResults();
        
        return $stats;
    }
}
