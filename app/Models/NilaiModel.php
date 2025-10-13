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

    public function getDataNilaiDetail($IdSantri = null, $IdSemester = null, $IdTahunAjaran = null, $IdKelas = null)
    {
        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.Id, n.IdTahunAjaran, n.IdTpq, ks.IdKelas, k.NamaKelas, s.IdSantri, s.NamaSantri, n.IdMateri, m.Kategori, m.NamaMateri, n.Semester, n.Nilai');
        $builder->join('tbl_santri_baru s', 'n.IdSantri = s.IdSantri');
        $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = n.IdSantri AND ks.IdTahunAjaran = n.IdTahunAjaran');
        $builder->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas');
        $builder->join('tbl_materi_pelajaran m', 'n.IdMateri = m.IdMateri');

        if ($IdSantri !== null) {
            $builder->where('n.IdSantri', $IdSantri);
        }
        if ($IdSemester !== null) {
            $builder->where('n.Semester', $IdSemester);
        }
        if ($IdTahunAjaran !== null) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
            } else {
                $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
            }
        }
        if ($IdKelas !== null) {
            if (is_array($IdKelas)) {
                $builder->whereIn('ks.IdKelas', $IdKelas);
            } else {
                $builder->where('ks.IdKelas', $IdKelas);
            }
        }
        $builder->orderBy('n.IdMateri', 'ASC');

        return $builder->get();
    }

    /**
     * Mengurangi complex JOIN queries dengan caching dan query optimization
     * 
     * @param string|null $IdSantri
     * @param string|null $IdSemester
     * @param string|null $IdTahunAjaran
     * @param string|null $IdKelas
     * @return object
     */
    public function getDataNilaiDetailOptimized($IdSantri = null, $IdSemester = null, $IdTahunAjaran = null, $IdKelas = null)
    {
        // Start database transaction for consistency
        $this->db->transStart();

        try {
            // Use optimized query with proper indexing hints
            $query = "
                SELECT 
                    n.Id, 
                    n.IdTahunAjaran, 
                    n.IdTpq, 
                    ks.IdKelas, 
                    k.NamaKelas, 
                    s.IdSantri, 
                    s.NamaSantri, 
                    n.IdMateri, 
                    m.Kategori, 
                    m.NamaMateri, 
                    n.Semester, 
                    n.Nilai
                FROM tbl_nilai n
                INNER JOIN tbl_santri_baru s ON n.IdSantri = s.IdSantri
                INNER JOIN tbl_kelas_santri ks ON ks.IdSantri = n.IdSantri AND ks.IdTahunAjaran = n.IdTahunAjaran
                INNER JOIN tbl_kelas k ON k.IdKelas = ks.IdKelas
                INNER JOIN tbl_materi_pelajaran m ON n.IdMateri = m.IdMateri
                WHERE 1=1
            ";

            $params = [];

            // Add conditions dynamically
            if ($IdSantri !== null) {
                $query .= " AND n.IdSantri = ?";
                $params[] = $IdSantri;
            }

            if ($IdSemester !== null) {
                $query .= " AND n.Semester = ?";
                $params[] = $IdSemester;
            }

            if ($IdTahunAjaran !== null) {
                if (is_array($IdTahunAjaran)) {
                    $placeholders = str_repeat('?,', count($IdTahunAjaran) - 1) . '?';
                    $query .= " AND n.IdTahunAjaran IN ($placeholders)";
                    $params = array_merge($params, $IdTahunAjaran);
                } else {
                    $query .= " AND n.IdTahunAjaran = ?";
                    $params[] = $IdTahunAjaran;
                }
            }

            if ($IdKelas !== null) {
                if (is_array($IdKelas)) {
                    $placeholders = str_repeat('?,', count($IdKelas) - 1) . '?';
                    $query .= " AND ks.IdKelas IN ($placeholders)";
                    $params = array_merge($params, $IdKelas);
                } else {
                    $query .= " AND ks.IdKelas = ?";
                    $params[] = $IdKelas;
                }
            }

            $query .= " ORDER BY n.IdMateri ASC";

            // Execute query
            $result = $this->db->query($query, $params)->getResult();

            $this->db->transComplete();

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Log error
            log_message('error', 'Error in getDataNilaiDetailOptimized: ' . $e->getMessage());

            // Fallback to original method
            return $this->getDataNilaiDetail($IdSantri, $IdSemester, $IdTahunAjaran, $IdKelas);
        }
    }

    // Insert nilai data
    public function insertNilai($data)
    {
        return !empty($data) ? $this->insert($data) : false;
    }

    /**
     * Clear cache for nilai data
     * Call this method when nilai data is updated
     * 
     * @param string|null $IdSantri
     * @param string|null $IdSemester
     * @param string|null $IdTahunAjaran
     * @param string|null $IdKelas
     */
    public function clearNilaiCache($IdSantri = null, $IdSemester = null, $IdTahunAjaran = null, $IdKelas = null)
    {
        // Clear specific cache
        $cacheKey = 'nilai_detail_' . md5(serialize([
            'IdSantri' => $IdSantri,
            'IdSemester' => $IdSemester,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdKelas' => $IdKelas
        ]));

        cache()->delete($cacheKey);

        // Clear all nilai cache if no specific parameters
        if ($IdSantri === null && $IdSemester === null && $IdTahunAjaran === null && $IdKelas === null) {
            $cache = \Config\Services::cache();
            $cacheInfo = $cache->getCacheInfo();

            if (isset($cacheInfo['nilai_detail_'])) {
                foreach ($cacheInfo['nilai_detail_'] as $key => $value) {
                    if (strpos($key, 'nilai_detail_') === 0) {
                        cache()->delete($key);
                    }
                }
            }
        }
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


    /**
     * Tahap 1: Mendapatkan data santri dengan join ke tabel santri
     * Timing: Untuk mengukur waktu join dengan tabel santri
     */
    public function getDataSantriForNilai($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        $startTime = microtime(true);

        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.IdSantri, s.NamaSantri, s.JenisKelamin');
        $builder->join('tbl_santri_baru s', 'n.IdSantri = s.IdSantri');

        // Apply filters
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

        $result = $builder->get()->getResult();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        log_message('debug', "Tahap 1 - Data Santri: {$executionTime}ms");

        return $result;
    }

    /**
     * Tahap 2: Mendapatkan data kelas santri dengan join ke tabel kelas
     * Timing: Untuk mengukur waktu join dengan tabel kelas
     */
    public function getDataKelasSantriForNilai($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        $startTime = microtime(true);

        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.IdSantri, ks.IdTahunAjaran, ks.IdKelas, k.NamaKelas');
        $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = n.IdSantri AND ks.IdTahunAjaran = n.IdTahunAjaran');
        $builder->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas');

        // Apply filters
        if (is_array($IdKelas)) {
            $builder->whereIn('ks.IdKelas', $IdKelas);
        } else {
            $builder->where('ks.IdKelas', $IdKelas);
        }
        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('ks.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }
        $builder->where('n.Semester', $semester);
        $builder->where('n.IdTpq', $IdTpq);

        $result = $builder->get()->getResult();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        log_message('debug', "Tahap 2 - Data Kelas Santri: {$executionTime}ms");

        return $result;
    }

    /**
     * Tahap 3: Menghitung total nilai dan rata-rata per santri
     * Timing: Untuk mengukur waktu perhitungan agregasi
     */
    public function calculateNilaiAggregation($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        $startTime = microtime(true);

        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.IdSantri, n.Semester, SUM(n.Nilai) AS TotalNilai, ROUND(AVG(n.Nilai), 2) AS NilaiRataRata');
        $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = n.IdSantri AND ks.IdTahunAjaran = n.IdTahunAjaran');

        // Apply filters
        if (is_array($IdKelas)) {
            $builder->whereIn('ks.IdKelas', $IdKelas);
        } else {
            $builder->where('ks.IdKelas', $IdKelas);
        }
        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('ks.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }
        $builder->where('n.Semester', $semester);
        $builder->where('n.IdTpq', $IdTpq);

        $builder->groupBy(['n.IdSantri', 'n.Semester']);

        $result = $builder->get()->getResult();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        log_message('debug', "Tahap 3 - Perhitungan Agregasi: {$executionTime}ms");

        return $result;
    }

    /**
     * Tahap 4: Menghitung ranking per kelas
     * Timing: Untuk mengukur waktu perhitungan ranking
     */
    public function calculateRankingPerKelas($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        $startTime = microtime(true);

        $builder = $this->db->table('tbl_nilai n');
        $builder->select('n.IdSantri, ks.IdKelas, RANK() OVER (PARTITION BY ks.IdKelas ORDER BY AVG(n.Nilai) DESC) AS Rangking');
        $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = n.IdSantri AND ks.IdTahunAjaran = n.IdTahunAjaran');

        // Apply filters
        if (is_array($IdKelas)) {
            $builder->whereIn('ks.IdKelas', $IdKelas);
        } else {
            $builder->where('ks.IdKelas', $IdKelas);
        }
        if (is_array($IdTahunAjaran)) {
            $builder->whereIn('ks.IdTahunAjaran', $IdTahunAjaran);
        } else {
            $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }
        $builder->where('n.Semester', $semester);
        $builder->where('n.IdTpq', $IdTpq);

        $builder->groupBy(['n.IdSantri', 'ks.IdKelas']);

        $result = $builder->get()->getResult();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        log_message('debug', "Tahap 4 - Perhitungan Ranking: {$executionTime}ms");

        return $result;
    }

    /**
     * Fungsi utama yang menggabungkan semua tahap dengan monitoring waktu
     * Versi ini memecah query kompleks menjadi beberapa tahap untuk monitoring performa
     */
    public function getDataNilaiPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        $totalStartTime = microtime(true);

        log_message('debug', "=== MULAI getDataNilaiPerSemesterOptimized ===");

        // Tahap 1: Data Santri
        $dataSantri = $this->getDataSantriForNilai($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Tahap 2: Data Kelas Santri
        $dataKelasSantri = $this->getDataKelasSantriForNilai($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Tahap 3: Perhitungan Agregasi
        $nilaiAggregation = $this->calculateNilaiAggregation($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Tahap 4: Perhitungan Ranking
        $rankingData = $this->calculateRankingPerKelas($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Tahap 5: Penggabungan Data (Manual Join)
        $startTime = microtime(true);

        $result = [];
        $dataSantriMap = [];
        $dataKelasMap = [];
        $nilaiMap = [];
        $rankingMap = [];

        // Buat mapping untuk efisiensi
        foreach ($dataSantri as $santri) {
            $dataSantriMap[$santri->IdSantri] = $santri;
        }

        foreach ($dataKelasSantri as $kelas) {
            $dataKelasMap[$kelas->IdSantri] = $kelas;
        }

        foreach ($nilaiAggregation as $nilai) {
            $nilaiMap[$nilai->IdSantri] = $nilai;
        }

        foreach ($rankingData as $ranking) {
            $rankingMap[$ranking->IdSantri] = $ranking;
        }

        // Gabungkan data
        foreach ($dataSantriMap as $idSantri => $santri) {
            $kelas = $dataKelasMap[$idSantri] ?? null;
            $nilai = $nilaiMap[$idSantri] ?? null;
            $ranking = $rankingMap[$idSantri] ?? null;

            if ($kelas && $nilai && $ranking) {
                $result[] = (object) [
                    'IdSantri' => $idSantri,
                    'NamaSantri' => $santri->NamaSantri,
                    'JenisKelamin' => $santri->JenisKelamin,
                    'IdTahunAjaran' => $kelas->IdTahunAjaran,
                    'Semester' => $nilai->Semester,
                    'NamaKelas' => $kelas->NamaKelas,
                    'IdKelas' => $kelas->IdKelas,
                    'TotalNilai' => $nilai->TotalNilai,
                    'NilaiRataRata' => $nilai->NilaiRataRata,
                    'Rangking' => $ranking->Rangking
                ];
            }
        }

        // Sorting
        usort($result, function ($a, $b) {
            if ($a->IdKelas == $b->IdKelas) {
                if ($a->Semester == $b->Semester) {
                    return $b->TotalNilai <=> $a->TotalNilai;
                }
                return $a->Semester <=> $b->Semester;
            }
            return $a->IdKelas <=> $b->IdKelas;
        });

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        log_message('debug', "Tahap 5 - Penggabungan Data: {$executionTime}ms");

        $totalEndTime = microtime(true);
        $totalExecutionTime = ($totalEndTime - $totalStartTime) * 1000;

        log_message('debug', "=== TOTAL WAKTU getDataNilaiPerSemesterOptimized: {$totalExecutionTime}ms ===");

        return $result;
    }
}
