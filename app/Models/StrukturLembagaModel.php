<?php

namespace App\Models;

use CodeIgniter\Model;

class StrukturLembagaModel extends Model
{
    protected $table = 'tbl_struktur_lembaga';
    protected $primaryKey = 'Id';
    protected $allowedFields = ['IdTpq', 'IdGuru', 'TanggalStart', 'TanggalAkhir', 'IdJabatan', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    
    protected $validationRules = [
        'IdTpq' => 'required|integer',
        'IdGuru' => 'required|integer',
        'TanggalStart' => 'required|valid_date',
        'TanggalAkhir' => 'permit_empty|valid_date',
        'IdJabatan' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'IdTpq' => [
            'required' => 'ID TPQ harus diisi',
            'integer' => 'ID TPQ harus berupa angka'
        ],
        'IdGuru' => [
            'required' => 'ID Guru harus diisi',
            'integer' => 'ID Guru harus berupa angka'
        ],
        'TanggalStart' => [
            'required' => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal mulai tidak valid'
        ],
        'TanggalAkhir' => [
            'valid_date' => 'Format tanggal akhir tidak valid'
        ],
        'IdJabatan' => [
            'required' => 'ID Jabatan harus diisi',
            'integer' => 'ID Jabatan harus berupa angka'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
