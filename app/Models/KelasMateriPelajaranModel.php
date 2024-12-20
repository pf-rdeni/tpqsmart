<?php

namespace App\Models;
use CodeIgniter\Model;

class KelasMateriPelajaranModel extends Model
{
    protected $table = 'tbl_kelas_materi_pelajaran';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'IdKelas',
        'IdTpq',
        'IdMateri',
        'SemesterGanjil',
        'SemesterGenap',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
}
