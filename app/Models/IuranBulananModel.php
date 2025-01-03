<?php

namespace App\Models;

use CodeIgniter\Model;

class IuranBulananModel extends Model
{
    protected $table = 'tbl_iuran_bulanan';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'Bulan',
        'Kategori',
        'Nominal',
        'IdTahunAjaran',
        'IdSantri',
        'IdKelas',
        'IdTpq',
        'IdGuru'
    ];

    // Optional: Timestamps jika kamu menggunakan created_at dan updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    public function getIuranBulanan($IdSantri, $IdTahunAjaran, $Kategori = null) {
        $query = $this->db->table('tbl_iuran_bulanan')
            ->select('tbl_iuran_bulanan.Id, tbl_iuran_bulanan.Kategori, tbl_iuran_bulanan.Bulan, tbl_iuran_bulanan.IdKelas, tbl_iuran_bulanan.IdSantri, tbl_iuran_bulanan.IdTahunAjaran, tbl_iuran_bulanan.Nominal, tbl_iuran_bulanan.created_at AS TanggalSerahTerima, tbl_santri_baru.NamaSantri')
            ->join('tbl_santri_baru', 'tbl_santri_baru.IdSantri = tbl_iuran_bulanan.IdSantri')
                            ->where('tbl_iuran_bulanan.IdSantri', $IdSantri)
                            ->where('tbl_iuran_bulanan.IdTahunAjaran', $IdTahunAjaran)
                            ->orderBy('tbl_iuran_bulanan.Kategori', 'ASC')
                            ->orderBy('CAST(tbl_iuran_bulanan.Bulan AS UNSIGNED)', 'ASC');
        
        if ($Kategori !== null) {
            $query->where('tbl_iuran_bulanan.Kategori', $Kategori);
        }

        $results = $query->get()->getResult();

        return $results;
    }
}

