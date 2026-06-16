<?php

namespace App\Models\Backend\Luckydraw;

use CodeIgniter\Model;

class LuckydrawKegiatanModel extends Model
{
    protected $table            = 'tbl_luckydraw_kegiatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kegiatan',
        'tanggal_kegiatan',
        'tempat_pelaksanaan',
        'kupon_min',
        'kupon_max',
        'status',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveKegiatan()
    {
        return $this->where('status', 'active')->findAll();
    }
}
