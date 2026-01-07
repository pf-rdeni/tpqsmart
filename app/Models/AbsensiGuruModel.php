<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiGuruModel extends Model
{
    protected $table            = 'tbl_absensi_guru';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; // Using object for easier usage in views
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'IdKegiatan',
        'IdGuru',
        'StatusKehadiran',
        'WaktuAbsen',
        'Keterangan',
        'Latitude',
        'Longitude'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Relationships or Helpers can be added here
    public function getAbsensiByKegiatan($idKegiatan, $idTpq = null)
    {
        // Use raw query to handle collation mismatch safely
        $sql = "SELECT tbl_absensi_guru.*, tbl_guru.Nama as NamaGuru, tbl_guru.NoHp, tbl_guru.KelurahanDesa, tbl_guru.JenisKelamin, tbl_tpq.NamaTpq
                FROM tbl_absensi_guru
                JOIN tbl_guru ON CONVERT(tbl_guru.IdGuru USING utf8) = CONVERT(tbl_absensi_guru.IdGuru USING utf8)
                LEFT JOIN tbl_tpq ON tbl_tpq.IdTpq = tbl_guru.IdTpq
                WHERE IdKegiatan = ?";
        
        $params = [$idKegiatan];

        if ($idTpq) {
            $sql .= " AND tbl_guru.IdTpq = ?";
            $params[] = $idTpq;
        }

        $sql .= " ORDER BY tbl_guru.Nama ASC";

        return $this->db->query($sql, $params)->getResultObject();
    }
}
