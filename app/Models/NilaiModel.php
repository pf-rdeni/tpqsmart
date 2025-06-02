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
        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.Id, n.IdTahunAjaran, n.IdTpq, n.IdKelas, k.NamaKelas, s.IdSantri, s.NamaSantri, n.IdMateri, m.Kategori, m.NamaMateri, n.Semester, n.Nilai');
        $builder->join('tbl_kelas k', 'n.IdKelas = k.IdKelas');
        $builder->join('tbl_santri_baru s', 'n.IdSantri = s.IdSantri');
        $builder->join('tbl_materi_pelajaran m', 'n.IdMateri = m.IdMateri');

        if ($IdSantri !== null) {
            $builder->where('n.IdSantri', $IdSantri);
        }
        if ($IdSemester !== null) {
            $builder->where('n.Semester', $IdSemester);
        }

        $builder->orderBy('n.IdMateri', 'ASC');

        return $builder->get();
    }


    // Retrieve nilai data per semester
    public function getDataNilaiPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.IdSantri, s.NamaSantri, s.JenisKelamin, IdTahunAjaran, n.Semester, k.NamaKelas, k.IdKelas, SUM(n.Nilai) AS TotalNilai, ROUND(AVG(n.Nilai), 2) AS NilaiRataRata, RANK() OVER (PARTITION BY n.IdKelas ORDER BY AVG(n.Nilai) DESC) AS Rangking');
        $builder->join('tbl_santri_baru s', 'n.IdSantri = s.IdSantri');
        $builder->join('tbl_kelas k', 'n.IdKelas = k.IdKelas');

        $builder->whereIn('n.IdKelas', $IdKelas);
        $builder->where('n.Semester', $semester);
        $builder->where('n.IdTpq', $IdTpq);
        $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);

        $builder->groupBy(['n.IdSantri', 'n.Semester']);
        $builder->orderBy('k.IdKelas', 'ASC');
        $builder->orderBy('n.Semester', 'ASC');
        $builder->orderBy('TotalNilai', 'DESC');

        return $builder->get();
    }

    // Insert nilai data
    public function insertNilai($data)
    {
        return !empty($data) ? $this->insert($data) : false;
    }

    // getDataNilaiPerSantri
    public function getDataNilaiPerSantri($IdSantri, $semester, $IdTpq = null, $IdTahunAjaran = null, $IdKelas = null)
    {
        // If IdTpq is not provided, use the session value
        if ($IdTpq === null) {
            $IdTpq = session()->get('IdTpq');
        }
        // If IdTahunAjaran is not provided, use the current year
        if ($IdTahunAjaran === null) {
            $IdTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();
        }
        // If IdKelas is not provided, use the class of the student
        if ($IdKelas === null) {
            $IdKelas = $this->santriBaruModel->getKelasSantri($IdSantri);
        }

        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.Id, n.IdTpq, n.IdSantri, n.IdKelas, n.IdMateri, n.IdGuru, n.IdTahunAjaran, n.Semester, n.Nilai, m.Kategori, m.NamaMateri');
        $builder->join('tbl_materi_pelajaran m', 'n.IdMateri = m.IdMateri');

        $builder->where('n.IdSantri', $IdSantri);
        $builder->where('n.Semester', $semester);
        $builder->where('n.IdTpq', $IdTpq);
        // If IdTahunAjaran is an array, use whereIn, otherwise use where
        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
        }

        //if IdKelas ada dan array
        if (is_array($IdKelas)) {
            $builder->whereIn('n.IdKelas', $IdKelas);
        } else {
            $builder->where('n.IdKelas', $IdKelas);
        }

        $builder->orderBy('n.IdMateri', 'ASC');

        return $builder->get()->getResult();
    }

    // getDataNilaiPerKelas IdKelas dan IdTahunAjaran in array
    public function getDataNilaiPerKelas($IdTpq, $IdKelas = null, $IdTahunAjaran = null, $Semester)
    {
        $db = \Config\Database::connect();

        // Query untuk mendapatkan kolom dinamis
        $materiBuilder = $db->table('tbl_nilai n');
        $materiBuilder->select("GROUP_CONCAT(DISTINCT CONCAT('MAX(CASE WHEN n.IdMateri = \"', n.IdMateri, '\" THEN n.Nilai END) AS \"', m.NamaMateri, '\"')) AS dynamic_columns");
        $materiBuilder->join('tbl_materi_pelajaran m', 'n.IdMateri = m.IdMateri');
        $materiBuilder->where('n.IdTpq', $IdTpq);

        if ($IdKelas !== null) {
            $materiBuilder->whereIn('n.IdKelas', $IdKelas);
        }
        if ($IdTahunAjaran !== null) {
            $materiBuilder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
        }
        $materiBuilder->where('n.Semester', $Semester);

        $materiResult = $materiBuilder->get()->getRow();
        $dynamicColumns = $materiResult->dynamic_columns;

        if ($dynamicColumns) {
            $builder = $db->table('tbl_nilai n');
            $builder->select("n.IdSantri AS 'IdSantri', s.NamaSantri AS 'Nama Santri', n.IdKelas, k.NamaKelas AS 'Nama Kelas', IdTahunAjaran AS 'Tahun Ajaran', Semester, $dynamicColumns");
            $builder->join('tbl_kelas k', 'n.IdKelas = k.IdKelas');
            $builder->join('tbl_santri_baru s', 'n.IdSantri = s.IdSantri');

            $builder->where('n.IdTpq', $IdTpq);
            if ($IdKelas !== null) {
                $builder->whereIn('n.IdKelas', $IdKelas);
            }
            if ($IdTahunAjaran !== null) {
                $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
            }
            $builder->where('n.Semester', $Semester);

            $builder->groupBy(['IdSantri', 'IdTahunAjaran', 'Semester']);
            $builder->orderBy('n.IdKelas', 'ASC');
            $builder->orderBy('s.NamaSantri', 'ASC');

            return $builder->get()->getResultArray();
        }

        return [];
    }
}
