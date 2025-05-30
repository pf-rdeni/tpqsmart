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
        'IdTpq',
        'IdSantri',
        'IdKelas',
        'IdMateri',
        'IdGuru',
        'IdTahunAjaran',
        'Semester',
        'Nilai', 
        'created_at', 
        'updated_at'
    ];
    
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDataNilaiDetail($IdSantri = null, $IdSemester = null)
    {

        $sql =
        'SELECT n.Id, n.IdTahunAjaran, n.IdTpq, n.IdKelas, k.NamaKelas,
                    s.IdSantri, s.NamaSantri, n.IdMateri, m.Kategori, m.NamaMateri, n.Semester, n.Nilai
                FROM tbl_nilai n
                JOIN tbl_kelas k ON n.IdKelas = k.IdKelas
                JOIN tbl_santri_baru s ON n.IdSantri = s.IdSantri
                JOIN tbl_materi_pelajaran m ON n.IdMateri = m.IdMateri
                WHERE 1=1';

        if ($IdSantri !== null) {
            $sql .= ' AND n.IdSantri = ' . $IdSantri;
        }
        if ($IdSemester !== null) {
            $sql .= ' AND n.Semester = "' . $IdSemester . '"';
        }

        $sql .= ' ORDER BY n.IdMateri ASC';

        return db_connect()->query($sql);
    }


    // Retrieve nilai data per semester
    public function getDataNilaiPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {

        $sql = 'SELECT n.IdSantri, s.NamaSantri, s.JenisKelamin, IdTahunAjaran, n.Semester, k.NamaKelas, k.IdKelas,
                       SUM(n.Nilai) AS TotalNilai, ROUND(AVG(n.Nilai), 2) AS NilaiRataRata
                FROM tbl_nilai n
                JOIN tbl_santri_baru s ON n.IdSantri = s.IdSantri
                JOIN tbl_kelas k ON n.IdKelas = k.IdKelas
                WHERE n.IdKelas IN (' . implode(',', $IdKelas) . ')
                AND n.Semester = "' . $semester . '"
                AND n.IdTpq = "' . $IdTpq . '"
                AND n.IdTahunAjaran  IN (' . implode(',', $IdTahunAjaran) . ')

                GROUP BY n.IdSantri, n.Semester
                ORDER BY k.IdKelas ASC, n.Semester ASC, TotalNilai DESC';

        return db_connect()->query($sql);
    }

    // Insert nilai data
    public function insertNilai($data)
    {
        return !empty($data) ? $this->insert($data) : false;
    }

    // getDataNilaiPerSantri
    public function getDataNilaiPerSantri($IdSantri, $semester)
    {
        $sql = 'SELECT n.Id, n.IdTpq, n.IdSantri, n.IdKelas, n.IdMateri, n.IdGuru, n.IdTahunAjaran, n.Semester, n.Nilai,
                       m.Kategori, m.NamaMateri
                FROM tbl_nilai n
                JOIN tbl_materi_pelajaran m ON n.IdMateri = m.IdMateri
                WHERE n.IdSantri = ' . $IdSantri . ' AND n.Semester = "' . $semester . '"
                ORDER BY n.IdMateri ASC';

        return db_connect()->query($sql)->getResult();
    }

    // getDataNilaiPerKelas IdKelas dan IdTahunAjaran in array
    public function getDataNilaiPerKelas($IdTpq, $IdKelas = null, $IdTahunAjaran = null, $Semester)
    {
        // Connect to the database
        $db = db_connect();

        // Siapkan kondisi WHERE
        $whereConditions = ['n.IdTpq = ?'];
        $params = [$IdTpq];

        if ($IdKelas !== null) {
            $whereConditions[] = 'n.IdKelas IN ?';
            $params[] = $IdKelas;
        }

        if ($IdTahunAjaran !== null) {
            $whereConditions[] = 'n.IdTahunAjaran IN ?';
            $params[] = $IdTahunAjaran;
        }

        $whereConditions[] = 'n.Semester = ?';
        $params[] = $Semester;

        $whereClause = implode(' AND ', $whereConditions);

        // Query untuk mendapatkan kolom dinamis berdasarkan IdMateri
        $materiQuery = $db->query(
            "
            SELECT GROUP_CONCAT(
                DISTINCT CONCAT('MAX(CASE WHEN n.IdMateri = \"', n.IdMateri, '\" THEN n.Nilai END) AS \"', m.NamaMateri, '\"')
            ) AS dynamic_columns
            FROM tbl_nilai n
            JOIN tbl_materi_pelajaran m ON n.IdMateri = m.IdMateri
            WHERE $whereClause
        ",
            $params
        );

        // Ambil hasil kolom dinamis
        $materiResult = $materiQuery->getRow();
        $dynamicColumns = $materiResult->dynamic_columns;

        if ($dynamicColumns) {
            // Bangun query utama
            $finalQuery = "
                SELECT n.IdSantri AS 'IdSantri', s.NamaSantri AS 'Nama Santri', n.IdKelas, k.NamaKelas AS 'Nama Kelas',  
                       IdTahunAjaran AS 'Tahun Ajaran', Semester, $dynamicColumns
                FROM tbl_nilai n
                JOIN tbl_kelas k ON n.IdKelas = k.IdKelas
                JOIN tbl_santri_baru s ON n.IdSantri = s.IdSantri
                WHERE $whereClause
                GROUP BY IdSantri, IdTahunAjaran, Semester
                ORDER BY n.IdKelas ASC, s.NamaSantri ASC
            ";

            // Eksekusi query akhir
            $finalResult = $db->query($finalQuery, $params);

            // Ambil data sebagai array
            $data = $finalResult->getResultArray();
        } else {
            $data = []; // Jika tidak ada data
        }

        // Kembalikan atau tampilkan hasil
        return $data;
    }
}
