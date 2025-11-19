<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahKategoriKesalahanModel extends Model
{
    protected $table = 'tbl_munaqosah_kategori_kesalahan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'IdKategoriKesalahan',
        'IdKategoriMateri',
        'NamaKategoriKesalahan',
        'NilaiMin',
        'NilaiMax',
        'Status',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'array';
}
