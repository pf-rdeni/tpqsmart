<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahJuriModel extends Model
{
    protected $table = 'tbl_munaqosah_juri';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'IdJuri',
        'IdTpq',
        'UsernameJuri',
        'IdGrupMateriUjian',
        'Status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'IdJuri' => 'required|max_length[50]|is_unique[tbl_munaqosah_juri.IdJuri,id,{id}]',
        'IdTpq' => 'permit_empty|integer',
        'UsernameJuri' => 'required|max_length[100]|is_unique[tbl_munaqosah_juri.UsernameJuri,id,{id}]',
        'IdGrupMateriUjian' => 'required|max_length[50]',
        'Status' => 'required|in_list[Aktif,Tidak Aktif]'
    ];

    protected $validationMessages = [
        'IdJuri' => [
            'required' => 'ID Juri harus diisi',
            'max_length' => 'ID Juri maksimal 50 karakter',
            'is_unique' => 'ID Juri sudah ada'
        ],
        'IdTpq' => [
            'integer' => 'ID TPQ harus berupa angka'
        ],
        'UsernameJuri' => [
            'required' => 'Username Juri harus diisi',
            'max_length' => 'Username Juri maksimal 100 karakter',
            'is_unique' => 'Username Juri sudah ada'
        ],
        'IdGrupMateriUjian' => [
            'required' => 'ID Grup Materi Ujian harus diisi',
            'max_length' => 'ID Grup Materi Ujian maksimal 50 karakter'
        ],
        'Status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus Aktif atau Tidak Aktif'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get juri with relations
     */
    public function getJuriWithRelations()
    {
        $builder = $this->db->table('tbl_munaqosah_juri j');
        $builder->select('j.*, t.NamaTpq, g.NamaMateriGrup');
        $builder->join('tbl_tpq t', 't.IdTpq = j.IdTpq', 'left');
        $builder->join('tbl_munaqosah_grup_materi_uji g', 'g.IdGrupMateriUjian = j.IdGrupMateriUjian', 'left');
        $builder->orderBy('j.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get juri by IdTpq
     */
    public function getJuriByTpq($idTpq)
    {
        return $this->where('IdTpq', $idTpq)->findAll();
    }

    /**
     * Get juri by IdGrupMateriUjian
     */
    public function getJuriByGrupMateri($idGrupMateriUjian)
    {
        return $this->where('IdGrupMateriUjian', $idGrupMateriUjian)->findAll();
    }

    /**
     * Get active juri
     */
    public function getJuriAktif()
    {
        return $this->where('Status', 'Aktif')->findAll();
    }

    /**
     * Generate next IdJuri
     */
    public function generateNextIdJuri()
    {
        // Ambil ID terakhir yang dimulai dengan 'J'
        $builder = $this->db->table($this->table);
        $builder->select('IdJuri');
        $builder->like('IdJuri', 'J', 'after');
        $builder->orderBy('IdJuri', 'DESC');
        $builder->limit(1);
        
        $result = $builder->get()->getRow();
        
        if ($result) {
            // Extract number dari ID terakhir (misal J001 -> 001)
            $lastId = $result->IdJuri;
            $number = intval(substr($lastId, 1)); // Ambil angka setelah 'J'
            $nextNumber = $number + 1;
            return 'J' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada data, mulai dari J001
            return 'J001';
        }
    }

    /**
     * Generate UsernameJuri based on grup materi and TPQ
     */
    public function generateUsernameJuri($idGrupMateriUjian, $idTpq = null)
    {
        // Ambil nama grup materi
        $grupModel = new \App\Models\MunaqosahGrupMateriUjiModel();
        $grup = $grupModel->where('IdGrupMateriUjian', $idGrupMateriUjian)->first();
        
        if (!$grup) {
            return null;
        }
        
        $namaGrup = strtolower(str_replace(' ', '.', $grup['NamaMateriGrup']));
        
        // Cari username yang sudah ada untuk grup ini
        $builder = $this->db->table($this->table);
        $builder->select('UsernameJuri');
        $builder->like('UsernameJuri', 'juri.' . $namaGrup, 'after');
        
        if ($idTpq) {
            $tpqSuffix = substr($idTpq, -3); // Ambil 3 digit terakhir
            $builder->like('UsernameJuri', '.' . $tpqSuffix . '.', 'both');
        }
        
        $builder->orderBy('UsernameJuri', 'DESC');
        $builder->limit(1);
        
        $result = $builder->get()->getRow();
        
        if ($result) {
            // Extract number dari username terakhir
            $lastUsername = $result->UsernameJuri;
            $pattern = '/juri\.' . preg_quote($namaGrup, '/') . '\.(\d+)$/';
            if (preg_match($pattern, $lastUsername, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
        } else {
            $nextNumber = 1;
        }
        
        // Format username
        if ($idTpq) {
            $tpqSuffix = substr($idTpq, -3);
            return 'juri.' . $namaGrup . '.' . $tpqSuffix . '.' . $nextNumber;
        } else {
            return 'juri.' . $namaGrup . '.' . $nextNumber;
        }
    }

    /**
     * Check if UsernameJuri exists
     */
    public function checkUsernameJuriExists($usernameJuri, $excludeId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, UsernameJuri');
        $builder->where('UsernameJuri', $usernameJuri);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        $result = $builder->get()->getRow();
        return $result;
    }

    /**
     * Get juri by IdJuri
     */
    public function getJuriByIdJuri($idJuri)
    {
        return $this->where('IdJuri', $idJuri)->first();
    }
}
