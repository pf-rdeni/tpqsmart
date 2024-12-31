<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    protected $table = 'tbl_nilai';
    protected $primaryKey = 'Id';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'Id',
        'NilaiGanjil',
        'NilaiGenap',
        'IdTpq',
        'IdSantri',
        'IdKelas',
        'IdTahunAjaran',
        'IdMateri',
        'Catatan',
        'created_at', 
        'updated_at'
    ];
    
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDataNilaiDetail($IdSantri, $IdSemester)
    {
        if ($IdSemester == "Ganjil") {
            // Base SQL query
            $sql =
                'SELECT n.Id, n.IdTahunAjaran, n.IdTpq, n.IdKelas, 
                    s.IdSantri, s.NamaSantri, n.IdMateri, m.Kategori, m.NamaMateri, n.Catatan, "Ganjil" AS Semester, n.NilaiGanjil AS Nilai
                FROM tbl_nilai n
                JOIN tbl_santri_baru s ON n.IdSantri = s.IdSantri
                JOIN tbl_materi_pelajaran m ON n.IdMateri = m.IdMateri
                WHERE n.IdSantri = ' . $IdSantri;

            $sql .= ' ORDER BY n.IdMateri ASC';
        } else if ($IdSemester == "Genap") {
            // Base SQL query for Genap semester
            $sql =
                'SELECT n.Id, n.IdTahunAjaran, n.IdTpq, n.IdKelas, 
                    s.IdSantri, s.NamaSantri, n.IdMateri, m.Kategori, m.NamaMateri, n.Catatan, "Genap" AS Semester, n.NilaiGenap AS Nilai
                FROM tbl_nilai n
                JOIN tbl_santri_baru s ON n.IdSantri = s.IdSantri
                JOIN tbl_materi_pelajaran m ON n.IdMateri = m.IdMateri
                WHERE n.IdSantri = ' . $IdSantri;

            $sql .= ' ORDER BY n.IdMateri ASC';
        }

        

        return db_connect()->query($sql);
    }


    // Retrieve nilai data per semester
    public function getDataNilaiPerSemester()
    {
        $sql = 'SELECT n.IdSantri, s.Nama, s.JenisKelamin, IdTahunAjaran, n.Semester, 
                       SUM(n.Nilai) AS TotalNilai, ROUND(AVG(n.Nilai), 2) AS NilaiRataRata
                FROM tbl_nilai n
                JOIN tbl_santri s ON n.IdSantri = s.IdSantri
                GROUP BY n.IdSantri, n.Semester
                ORDER BY n.Semester ASC, TotalNilai DESC';

        return db_connect()->query($sql);
    }

    // Insert nilai data
    public function insertNilai($data)
    {
        return !empty($data) ? $this->insert($data) : false;
    }
}
