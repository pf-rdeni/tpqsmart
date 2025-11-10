<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikasiGuruModel extends Model
{
    protected $table = 'tbl_sertifikasi_guru';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'noTest',
        'NoRek',
        'Nama',
        'NamaTpq',
    ];

    protected $validationRules = [
        'noTest' => 'required|max_length[50]',
        'NoRek' => 'permit_empty|max_length[50]',
        'Nama' => 'required|max_length[255]',
        'NamaTpq' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'noTest' => [
            'required' => 'Nomor test harus diisi',
            'max_length' => 'Nomor test maksimal 50 karakter'
        ],
        'Nama' => [
            'required' => 'Nama harus diisi',
            'max_length' => 'Nama maksimal 255 karakter'
        ],
    ];

    /**
     * Get guru by noTest
     */
    public function getGuruByNoTest($noTest)
    {
        return $this->where('noTest', $noTest)->first();
    }

    /**
     * Get all guru
     */
    public function getAllGuru()
    {
        return $this->orderBy('Nama', 'ASC')->findAll();
    }
}

