<?php

namespace App\Models\Backend\Luckydraw;

use CodeIgniter\Model;

class LuckydrawUserKegiatanModel extends Model
{
    protected $table            = 'tbl_luckydraw_user_kegiatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'id_kegiatan'
    ];

    public function getKegiatanByUser($userId)
    {
        return $this->select('tbl_luckydraw_kegiatan.*')
                    ->join('tbl_luckydraw_kegiatan', 'tbl_luckydraw_kegiatan.id = tbl_luckydraw_user_kegiatan.id_kegiatan')
                    ->where('tbl_luckydraw_user_kegiatan.user_id', $userId)
                    ->findAll();
    }
}
