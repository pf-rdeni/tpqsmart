<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriMateriModel extends Model
{
    protected $table = 'tbl_kategori_materi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'IdKategoriMateri',
        'NamaKategoriMateri',
        'Status',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'array';
}

