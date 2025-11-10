<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikasiGuruModel extends Model
{
    protected $table = 'tbl_sertifikasi_guru';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'NoRek',
        'Nama',
        'NamaTpq',
        'JenisKelamin',
        'Kecamatan',
        'Note',
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'NoRek' => 'permit_empty|max_length[50]',
        'Nama' => 'required|max_length[255]',
        'NamaTpq' => 'permit_empty|max_length[255]',
        'JenisKelamin' => 'permit_empty|max_length[20]',
        'Kecamatan' => 'permit_empty|max_length[255]',
        'Note' => 'permit_empty',
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
        'Nama' => [
            'required' => 'Nama harus diisi',
            'max_length' => 'Nama maksimal 255 karakter'
        ],
    ];

    /**
     * Get guru by NoPeserta
     */
    public function getGuruByNoPeserta($noPeserta)
    {
        return $this->where('NoPeserta', $noPeserta)->first();
    }

    /**
     * Get guru by noTest (backward compatibility)
     */
    public function getGuruByNoTest($noTest)
    {
        return $this->where('NoPeserta', $noTest)->first();
    }

    /**
     * Get all guru
     */
    public function getAllGuru()
    {
        return $this->orderBy('Nama', 'ASC')->findAll();
    }
}

