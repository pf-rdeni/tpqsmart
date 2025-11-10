<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikasiJuriModel extends Model
{
    protected $table = 'tbl_sertifikasi_juri';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdJuri',
        'IdGroupMateri',
        'usernameJuri',
    ];

    protected $validationRules = [
        'IdJuri' => 'required|max_length[50]',
        'IdGroupMateri' => 'required|max_length[50]',
        'usernameJuri' => 'required|max_length[100]',
    ];

    protected $validationMessages = [
        'IdJuri' => [
            'required' => 'ID Juri harus diisi',
            'max_length' => 'ID Juri maksimal 50 karakter'
        ],
        'IdGroupMateri' => [
            'required' => 'ID Group Materi harus diisi',
            'max_length' => 'ID Group Materi maksimal 50 karakter'
        ],
        'usernameJuri' => [
            'required' => 'Username Juri harus diisi',
            'max_length' => 'Username Juri maksimal 100 karakter'
        ],
    ];

    /**
     * Get juri by usernameJuri
     */
    public function getJuriByUsernameJuri($usernameJuri)
    {
        $builder = $this->db->table($this->table . ' sj');
        $builder->select('sj.*, sgm.NamaMateri');
        $builder->join('tbl_sertifikasi_group_materi sgm', 'sgm.IdGroupMateri = sj.IdGroupMateri', 'left');
        $builder->where('sj.usernameJuri', $usernameJuri);
        $result = $builder->get()->getRow();
        return $result ? (object)$result : null;
    }

    /**
     * Get juri by IdJuri
     */
    public function getJuriByIdJuri($idJuri)
    {
        $builder = $this->db->table($this->table . ' sj');
        $builder->select('sj.*, sgm.NamaMateri');
        $builder->join('tbl_sertifikasi_group_materi sgm', 'sgm.IdGroupMateri = sj.IdGroupMateri', 'left');
        $builder->where('sj.IdJuri', $idJuri);
        $result = $builder->get()->getRow();
        return $result ? (object)$result : null;
    }

    /**
     * Get all juri with relations
     */
    public function getAllJuriWithRelations()
    {
        $builder = $this->db->table($this->table . ' sj');
        $builder->select('sj.*, sgm.NamaMateri');
        $builder->join('tbl_sertifikasi_group_materi sgm', 'sgm.IdGroupMateri = sj.IdGroupMateri', 'left');
        $builder->orderBy('sgm.IdGroupMateri', 'ASC');
        $builder->orderBy('sj.IdJuri', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get juri by IdGroupMateri
     */
    public function getJuriByGroupMateri($idGroupMateri)
    {
        return $this->where('IdGroupMateri', $idGroupMateri)->findAll();
    }
}

