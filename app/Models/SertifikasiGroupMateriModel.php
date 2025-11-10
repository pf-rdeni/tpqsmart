<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikasiGroupMateriModel extends Model
{
    protected $table = 'tbl_sertifikasi_group_materi';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdGroupMateri',
        'NamaMateri',
    ];

    protected $validationRules = [
        'IdGroupMateri' => 'required|max_length[50]',
        'NamaMateri' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'IdGroupMateri' => [
            'required' => 'ID Group Materi harus diisi',
            'max_length' => 'ID Group Materi maksimal 50 karakter'
        ],
        'NamaMateri' => [
            'required' => 'Nama Materi harus diisi',
            'max_length' => 'Nama Materi maksimal 255 karakter'
        ],
    ];

    /**
     * Get all group materi
     */
    public function getAllGroupMateri()
    {
        return $this->orderBy('IdGroupMateri', 'ASC')->findAll();
    }

    /**
     * Get group materi by IdGroupMateri
     */
    public function getGroupMateriById($idGroupMateri)
    {
        return $this->where('IdGroupMateri', $idGroupMateri)->first();
    }
}

