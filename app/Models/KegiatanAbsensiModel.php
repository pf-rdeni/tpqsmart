<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanAbsensiModel extends Model
{
    protected $table            = 'tbl_kegiatan_absensi';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Or object
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'NamaKegiatan',
        'Tanggal',
        'JamMulai',
        'JamSelesai',
        'Lingkup',
        'IdTpq',
        'Tempat',
        'Detail',
        'IsActive',
        'CreatedBy',
        'Token',
        'JenisJadwal',
        'TanggalMulaiRutin',
        'TanggalAkhirRutin',
        'HariDalamMinggu',
        'TanggalDalamBulan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'NamaKegiatan' => 'required|min_length[3]',
        'Tanggal'      => 'required|valid_date',
        'Lingkup'      => 'required|in_list[Umum,TPQ]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
