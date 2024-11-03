<?php namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table = 'tbl_absensi_santri';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'IdSantri',
        'Tanggal',
        'Kehadiran',
        'Keterangan',
        'IdKelas',
        'IdTahunAjaran',
        'IdGuru',
        'IdTpq',
        'created_at'
    ];

    public function getKehadiran($startDate, $endDate)
    {
        return $this->select('Kehadiran, COUNT(*) as count')
                    ->where('tanggal >=', $startDate)
                    ->where('tanggal <=', $endDate)
                    ->groupBy('Kehadiran')
                    ->findAll() ;
    }

}
