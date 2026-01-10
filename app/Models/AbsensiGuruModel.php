<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiGuruModel extends Model
{
    protected $table            = 'tbl_absensi_guru';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; // Menggunakan objek untuk penggunaan yang lebih mudah di view
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'IdKegiatan',
        'TanggalOccurrence',
        'IdGuru',
        'StatusKehadiran',
        'WaktuAbsen',
        'Keterangan',
        'Latitude',
        'Longitude'
    ];

    // Tanggal
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Relasi atau Helper dapat ditambahkan di sini

    // Penjelasan Proses:
    // Fungsi ini mengambil data absensi lengkap dengan join ke tabel Guru dan TPQ.
    // Menggunakan RAW QUERY untuk mengatasi masalah collation (charset) yang berbeda antar tabel jika ada.
    // Filter berdasarkan Kegiatan, TanggalOccurrence (opsional), dan IdTPQ (opsional).
    public function getAbsensiByKegiatan($idKegiatan, $idTpq = null, $tanggalOccurrence = null)
    {
        // Gunakan raw query untuk menangani ketidakcocokan collation dengan aman
        $sql = "SELECT tbl_absensi_guru.*, tbl_guru.Nama as NamaGuru, tbl_guru.NoHp, tbl_guru.KelurahanDesa, tbl_guru.JenisKelamin, tbl_tpq.NamaTpq
                FROM tbl_absensi_guru
                JOIN tbl_guru ON CONVERT(tbl_guru.IdGuru USING utf8) = CONVERT(tbl_absensi_guru.IdGuru USING utf8)
                LEFT JOIN tbl_tpq ON tbl_tpq.IdTpq = tbl_guru.IdTpq
                WHERE IdKegiatan = ?";
        
        $params = [$idKegiatan];

        if ($tanggalOccurrence) {
            $sql .= " AND tbl_absensi_guru.TanggalOccurrence = ?";
            $params[] = $tanggalOccurrence;
        }

        if ($idTpq) {
            $sql .= " AND tbl_guru.IdTpq = ?";
            $params[] = $idTpq;
        }

        $sql .= " ORDER BY tbl_guru.Nama ASC";

        return $this->db->query($sql, $params)->getResultObject();
    }
}
