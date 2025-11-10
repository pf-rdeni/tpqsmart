<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikasiMateriModel extends Model
{
    protected $table = 'tbl_sertifikasi_materi';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdMateri',
        'NamaMateri',
        'IdGrupMateri',
        'Status',
    ];

    protected $validationRules = [
        'IdMateri' => 'required|max_length[50]',
        'NamaMateri' => 'required|max_length[255]',
        'IdGrupMateri' => 'required|max_length[50]',
        'Status' => 'required|in_list[Aktif,Tidak Aktif]',
    ];

    protected $validationMessages = [
        'IdMateri' => [
            'required' => 'ID Materi harus diisi',
            'max_length' => 'ID Materi maksimal 50 karakter'
        ],
        'NamaMateri' => [
            'required' => 'Nama Materi harus diisi',
            'max_length' => 'Nama Materi maksimal 255 karakter'
        ],
        'IdGrupMateri' => [
            'required' => 'ID Grup Materi harus diisi',
            'max_length' => 'ID Grup Materi maksimal 50 karakter'
        ],
        'Status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus Aktif atau Tidak Aktif'
        ],
    ];

    /**
     * Get all materi
     */
    public function getAllMateri()
    {
        return $this->orderBy('IdGrupMateri', 'ASC')
            ->orderBy('IdMateri', 'ASC')
            ->findAll();
    }

    /**
     * Get materi by IdGrupMateri (IdGroupMateri dari tabel group_materi)
     */
    public function getMateriByGrupMateri($idGroupMateri)
    {
        return $this->where('IdGrupMateri', $idGroupMateri)
            ->where('Status', 'Aktif')
            ->orderBy('IdMateri', 'ASC')
            ->findAll();
    }

    /**
     * Get materi by IdMateri
     */
    public function getMateriByIdMateri($idMateri)
    {
        return $this->where('IdMateri', $idMateri)->first();
    }

    /**
     * Get active materi
     */
    public function getMateriAktif()
    {
        return $this->where('Status', 'Aktif')
            ->orderBy('IdGrupMateri', 'ASC')
            ->orderBy('IdMateri', 'ASC')
            ->findAll();
    }
}

