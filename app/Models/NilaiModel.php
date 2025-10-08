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
        //Active=1
        $builder->where('s.Active', 1);

        if (is_array($IdKelas)) {
            $builder->whereIn('n.IdKelas', $IdKelas);
        } else {
            $builder->where('n.IdKelas', $IdKelas);
        }
        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
        }
        $builder->where('n.Semester', $semester);
        $builder->where('n.IdTpq', $IdTpq);


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
    public function getDataNilaiPerSantri($IdTpq, $IdTahunAjaran, $IdKelas, $IdSantri, $semester)
    {
        // Query untuk mendapatkan nilai santri
        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.*, m.NamaMateri, m.Kategori, k.NamaKelas, kmp.UrutanMateri');
        $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = n.IdMateri');
        $builder->join('tbl_kelas k', 'k.IdKelas = n.IdKelas');
        // Gunakan LEFT JOIN agar data nilai santri tetap muncul meskipun belum dimapping di kmp
        $builder->join('tbl_kelas_materi_pelajaran kmp', 'kmp.IdMateri = n.IdMateri AND kmp.IdKelas = n.IdKelas', 'left');

        // Handle IdTpq jika array
        if (is_array($IdTpq)) {
            $builder->whereIn('n.IdTpq', $IdTpq);
        } else {
            $builder->where('n.IdTpq', $IdTpq);
        }

        // Handle IdTahunAjaran jika array
        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
        }

        // Handle IdKelas jika array
        if (is_array($IdKelas)) {
            $builder->whereIn('n.IdKelas', $IdKelas);
        } else {
            $builder->where('n.IdKelas', $IdKelas);
        }

        $builder->where('n.IdSantri', $IdSantri);
        $builder->where('n.Semester', $semester);
        $builder->groupBy('n.IdMateri');
        $builder->orderBy('kmp.UrutanMateri', 'ASC');

        $nilaiSantri = $builder->get()->getResult();

        // Query untuk mendapatkan rata-rata kelas per materi
        $builderRataKelas = $this->db->table('tbl_nilai n');
        $builderRataKelas->select('n.IdMateri, m.NamaMateri, m.Kategori, ROUND(AVG(n.Nilai), 2) as RataKelas');
        $builderRataKelas->join('tbl_materi_pelajaran m', 'm.IdMateri = n.IdMateri');
        $builderRataKelas->join('tbl_kelas_materi_pelajaran kmp', 'kmp.IdMateri = n.IdMateri AND kmp.IdKelas = n.IdKelas');

        // Handle IdTpq jika array
        if (is_array($IdTpq)) {
            $builderRataKelas->whereIn('n.IdTpq', $IdTpq);
        } else {
            $builderRataKelas->where('n.IdTpq', $IdTpq);
        }

        // Handle IdTahunAjaran jika array
        if (is_array($IdTahunAjaran)) {
            $builderRataKelas->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builderRataKelas->where('n.IdTahunAjaran', $IdTahunAjaran);
        }

        // Handle IdKelas jika array
        if (is_array($IdKelas)) {
            $builderRataKelas->whereIn('n.IdKelas', $IdKelas);
        } else {
            $builderRataKelas->where('n.IdKelas', $IdKelas);
        }

        $builderRataKelas->where('n.Semester', $semester);
        $builderRataKelas->groupBy('n.IdMateri');
        $builderRataKelas->orderBy('kmp.UrutanMateri', 'ASC');

        $rataKelas = $builderRataKelas->get()->getResult();

        // Gabungkan data nilai santri dengan rata-rata kelas
        $result = [];
        foreach ($nilaiSantri as $nilai) {
            $rataKelasMateri = array_filter($rataKelas, function ($rk) use ($nilai) {
                return $rk->IdMateri == $nilai->IdMateri;
            });

            $nilai->RataKelas = !empty($rataKelasMateri) ? reset($rataKelasMateri)->RataKelas : 0;
            $result[] = $nilai;
        }

        return $result;
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
            if (is_array($IdKelas)) {
                $materiBuilder->whereIn('n.IdKelas', $IdKelas);
            } else {
                $materiBuilder->where('n.IdKelas', $IdKelas);
            }
        }
        if ($IdTahunAjaran !== null) {
            if (is_array($IdTahunAjaran)) {
                $materiBuilder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
            } else {
                $materiBuilder->where('n.IdTahunAjaran', $IdTahunAjaran);
            }
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
                if (is_array($IdKelas)) {
                    $builder->whereIn('n.IdKelas', $IdKelas);
                } else {
                    $builder->where('n.IdKelas', $IdKelas);
                }
            }
            if ($IdTahunAjaran !== null) {
                if (is_array($IdTahunAjaran)) {
                    $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
                } else {
                    $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
                }
            }
            $builder->where('n.Semester', $Semester);

            $builder->where('s.Active', 1);

            $builder->groupBy(['IdSantri', 'IdTahunAjaran', 'Semester']);
            $builder->orderBy('n.IdKelas', 'ASC');
            $builder->orderBy('s.NamaSantri', 'ASC');

            return $builder->get()->getResultArray();
        }

        return [];
    }

    public function getAllNilaiPerKelas($IdTahunAjaran, $semester, $IdTpq, $IdKelas)
    {
        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.IdSantri, n.Nilai');
        $builder->where('n.IdTpq', $IdTpq);
        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
        }
        if (is_array($IdKelas)) {
            $builder->whereIn('n.IdKelas', $IdKelas);
        } else {
            $builder->where('n.IdKelas', $IdKelas);
        }

        $builder->where('n.Semester', $semester);

        return $builder->get()->getResult();
    }
}
