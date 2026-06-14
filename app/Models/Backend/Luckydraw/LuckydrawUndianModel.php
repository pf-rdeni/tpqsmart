<?php

namespace App\Models\Backend\Luckydraw;

use CodeIgniter\Model;

class LuckydrawUndianModel extends Model
{
    protected $table            = 'tbl_luckydraw_undian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_barang',
        'no_undian',
        'status_diambil',
        'waktu_diambil',
        'created_at'
    ];

    public function getPemenangList($status = null)
    {
        $builder = $this->select('tbl_luckydraw_undian.*, tbl_luckydraw_barang.nama_barang, tbl_luckydraw_barang.no_barang, tbl_luckydraw_barang.kategori')
                        ->join('tbl_luckydraw_barang', 'tbl_luckydraw_barang.id = tbl_luckydraw_undian.id_barang')
                        ->orderBy('tbl_luckydraw_undian.created_at', 'DESC');

        if ($status !== null) {
            $builder->where('tbl_luckydraw_undian.status_diambil', $status);
        }

        return $builder->findAll();
    }
}
