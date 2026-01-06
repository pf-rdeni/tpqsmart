<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaJuriModel extends Model
{
    protected $table = 'tbl_lomba_juri';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'IdJuri',
        'cabang_id',
        'UsernameJuri',
        'NamaJuri',
        'Status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'cabang_id'     => 'required|integer',
        'UsernameJuri'  => 'required|max_length[100]',
    ];

    /**
     * Ambil juri berdasarkan cabang
     */
    public function getJuriByCabang($cabangId, $status = 'Aktif')
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.*, t.NamaTpq');
        $builder->join('tbl_lomba_cabang c', 'c.id = j.cabang_id', 'left');
        $builder->join('tbl_lomba_master l', 'l.id = c.lomba_id', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = l.IdTpq', 'left');
        $builder->where('j.cabang_id', $cabangId);
        
        if ($status !== null) {
            $builder->where('j.Status', $status);
        }
        
        $builder->orderBy('j.NamaJuri', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil juri berdasarkan username
     */
    public function getJuriByUsername($username)
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.*, c.NamaCabang, c.Tipe, c.Kategori, c.lomba_id, l.NamaLomba');
        $builder->join('tbl_lomba_cabang c', 'c.id = j.cabang_id', 'left');
        $builder->join('tbl_lomba_master l', 'l.id = c.lomba_id', 'left');
        $builder->where('j.UsernameJuri', $username);
        $builder->where('j.Status', 'Aktif');
        
        return $builder->get()->getRowArray();
    }

    /**
     * Ambil juri berdasarkan IdJuri
     */
    public function getJuriByIdJuri($idJuri)
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.*, c.NamaCabang, c.lomba_id, l.NamaLomba');
        $builder->join('tbl_lomba_cabang c', 'c.id = j.cabang_id', 'left');
        $builder->join('tbl_lomba_master l', 'l.id = c.lomba_id', 'left');
        $builder->where('j.IdJuri', $idJuri);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Generate IdJuri yang unik
     */
    public function generateIdJuri()
    {
        $prefix = 'JL'; // Juri Lomba
        
        $lastJuri = $this->orderBy('id', 'DESC')->first();
        
        $sequence = 1;
        if ($lastJuri && preg_match('/^JL(\d+)$/', $lastJuri['IdJuri'], $matches)) {
            $sequence = (int) $matches[1] + 1;
        }
        
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah username sudah ditugaskan sebagai juri untuk cabang ini
     */
    public function isUsernameAssigned($username, $cabangId, $excludeId = null)
    {
        $builder = $this->where('UsernameJuri', $username)
                        ->where('cabang_id', $cabangId);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Ambil daftar juri beserta info cabang
     */
    public function getJuriWithCabang($cabangId = null)
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.*, c.NamaCabang, c.lomba_id, l.NamaLomba');
        $builder->join('tbl_lomba_cabang c', 'c.id = j.cabang_id', 'left');
        $builder->join('tbl_lomba_master l', 'l.id = c.lomba_id', 'left');
        
        if ($cabangId !== null) {
            $builder->where('j.cabang_id', $cabangId);
        }
        
        $builder->orderBy('l.NamaLomba', 'ASC');
        $builder->orderBy('c.NamaCabang', 'ASC');
        $builder->orderBy('j.NamaJuri', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}
