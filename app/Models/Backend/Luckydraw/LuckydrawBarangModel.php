<?php

namespace App\Models\Backend\Luckydraw;

use CodeIgniter\Model;

class LuckydrawBarangModel extends Model
{
    protected $table            = 'tbl_luckydraw_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kegiatan',
        'no_barang',
        'kategori',
        'nama_barang',
        'jumlah'
    ];

    public function getBarangWithKategori()
    {
        // Adjust the join based on the actual kategori table name (assuming tbl_kategori or similar)
        // If we don't know the table, we'll just fetch all or we can join later.
        return $this->findAll();
    }

    public function getBarangWithSisa($idKegiatan = null)
    {
        $builder = $this->select('tbl_luckydraw_barang.*, (tbl_luckydraw_barang.jumlah - (SELECT COUNT(id) FROM tbl_luckydraw_undian WHERE id_barang = tbl_luckydraw_barang.id)) as sisa');
        if ($idKegiatan) {
            $builder->where('tbl_luckydraw_barang.id_kegiatan', $idKegiatan);
        }
        return $builder->findAll();
    }

    public function getNextNoBarang($idKegiatan = null)
    {
        $builder = $this->orderBy('id', 'DESC');
        if ($idKegiatan) {
            $builder->where('id_kegiatan', $idKegiatan);
        }
        $last = $builder->first();
        
        if ($last && is_numeric($last->no_barang)) {
            return (int)$last->no_barang + 1;
        }
        return 1;
    }
}
