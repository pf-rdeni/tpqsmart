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

    public function getBarangWithSisa()
    {
        return $this->select('tbl_luckydraw_barang.*, (tbl_luckydraw_barang.jumlah - (SELECT COUNT(id) FROM tbl_luckydraw_undian WHERE id_barang = tbl_luckydraw_barang.id)) as sisa')
                    ->findAll();
    }

    public function getNextNoBarang()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last && is_numeric($last->no_barang)) {
            return (int)$last->no_barang + 1;
        }
        return 1;
    }
}
