<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahAntrianModel extends Model
{
    protected $table = 'tbl_munaqosah_antrian';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'IdTahunAjaran',
        'KategoriMateriUjian',
        'Status',
        'Keterangan'
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]',
        'KategoriMateriUjian' => 'required|max_length[100]',
        'Status' => 'permit_empty|in_list[0,1]',
        'Keterangan' => 'permit_empty'
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
        'IdTahunAjaran' => [
            'required' => 'ID Tahun Ajaran harus diisi',
            'max_length' => 'ID Tahun Ajaran maksimal 50 karakter'
        ],
        'KategoriMateriUjian' => [
            'required' => 'Kategori materi ujian harus diisi',
            'max_length' => 'Kategori materi ujian maksimal 100 karakter'
        ],
        'Status' => [
            'in_list' => 'Status harus 0 atau 1'
        ]
    ];

    public function getAntrianByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getAntrianBelumSelesai($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                   ->where('Status', false)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getAntrianSelesai($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                   ->where('Status', true)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function updateStatus($id, $status)
    {
        return $this->update($id, ['Status' => $status]);
    }
}
