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

    /**
     * Get statistik kehadiran per kelas dalam periode tertentu
     */
    public function getStatistikPerKelas($IdTpq, $IdKelas, $startDate, $endDate, $IdTahunAjaran = null)
    {
        $builder = $this->select('Kehadiran, COUNT(*) as count')
            ->where('IdTpq', $IdTpq)
            ->where('IdKelas', $IdKelas)
            ->where('Tanggal >=', $startDate)
            ->where('Tanggal <=', $endDate);
        
        if ($IdTahunAjaran) {
            $builder->where('IdTahunAjaran', $IdTahunAjaran);
        }
        
        return $builder->groupBy('Kehadiran')->findAll();
    }

    /**
     * Get statistik kehadiran per hari dalam periode tertentu
     */
    public function getStatistikPerHari($IdTpq, $IdKelas, $startDate, $endDate, $IdTahunAjaran = null)
    {
        $builder = $this->select('Tanggal, Kehadiran, COUNT(*) as count')
            ->where('IdTpq', $IdTpq)
            ->where('IdKelas', $IdKelas)
            ->where('Tanggal >=', $startDate)
            ->where('Tanggal <=', $endDate);
        
        if ($IdTahunAjaran) {
            $builder->where('IdTahunAjaran', $IdTahunAjaran);
        }
        
        return $builder->groupBy('Tanggal, Kehadiran')->orderBy('Tanggal', 'ASC')->findAll();
    }

    /**
     * Get total jumlah absensi per kelas
     */
    public function getTotalAbsensiPerKelas($IdTpq, $startDate, $endDate, $IdTahunAjaran = null)
    {
        $builder = $this->select('IdKelas, COUNT(*) as total')
            ->where('IdTpq', $IdTpq)
            ->where('Tanggal >=', $startDate)
            ->where('Tanggal <=', $endDate);
        
        if ($IdTahunAjaran) {
            $builder->where('IdTahunAjaran', $IdTahunAjaran);
        }
        
        return $builder->groupBy('IdKelas')->findAll();
    }

    /**
     * Get statistik kehadiran per semester (Ganjil atau Genap)
     * Semester Ganjil: Juli-Desember (bulan 7-12)
     * Semester Genap: Januari-Juni (bulan 1-6)
     */
    public function getStatistikPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        // Tentukan rentang bulan berdasarkan semester
        // Asumsikan tahun ajaran dimulai dari Juli tahun sebelumnya sampai Juni tahun sekarang
        // Contoh: 2024/2025 = Juli 2024 - Juni 2025
        // Semester Ganjil: Juli-Desember (2024-07-01 sampai 2024-12-31)
        // Semester Genap: Januari-Juni (2025-01-01 sampai 2025-06-30)
        
        // Parse tahun ajaran
        // Jika IdTahunAjaran adalah ID (integer), perlu dikonversi dulu
        // Jika sudah string format "2024/2025", langsung parse
        $tahunAjaranStr = is_numeric($IdTahunAjaran) ? $IdTahunAjaran : (string)$IdTahunAjaran;
        
        // Cek apakah format sudah "YYYY/YYYY" atau hanya ID
        if (strpos($tahunAjaranStr, '/') !== false) {
            // Format sudah "2024/2025"
            $tahunAjaranParts = explode('/', $tahunAjaranStr);
            $tahunAwal = isset($tahunAjaranParts[0]) ? (int)$tahunAjaranParts[0] : date('Y');
            $tahunAkhir = isset($tahunAjaranParts[1]) ? (int)$tahunAjaranParts[1] : $tahunAwal + 1;
        } else {
            // Jika hanya ID, asumsikan tahun ajaran saat ini
            // Ambil dari tahun saat ini
            $currentYear = (int)date('Y');
            $currentMonth = (int)date('m');
            if ($currentMonth >= 7) {
                // Semester Ganjil: Juli-Desember tahun ini
                $tahunAwal = $currentYear;
                $tahunAkhir = $currentYear + 1;
            } else {
                // Semester Genap: Januari-Juni tahun ini
                $tahunAwal = $currentYear - 1;
                $tahunAkhir = $currentYear;
            }
        }
        
        if ($semester == 'Ganjil') {
            $startDate = $tahunAwal . '-07-01';
            $endDate = $tahunAwal . '-12-31';
        } else { // Genap
            $startDate = $tahunAkhir . '-01-01';
            $endDate = $tahunAkhir . '-06-30';
        }
        
        $builder = $this->select('Kehadiran, COUNT(*) as count')
            ->where('IdTpq', $IdTpq)
            ->where('IdKelas', $IdKelas)
            ->where('IdTahunAjaran', $IdTahunAjaran)
            ->where('Tanggal >=', $startDate)
            ->where('Tanggal <=', $endDate);
        
        return $builder->groupBy('Kehadiran')->findAll();
    }

    /**
     * Get list santri dengan statistik kehadiran per semester
     */
    public function getListSantriDenganStatistik($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        log_message('debug', '[LIST SANTRI STATISTIK] Params - IdTpq: ' . $IdTpq . ', IdKelas: ' . $IdKelas . ', IdTahunAjaran: ' . $IdTahunAjaran . ', Semester: ' . $semester);
        
        // Tentukan rentang bulan berdasarkan semester
        $tahunAjaranStr = is_numeric($IdTahunAjaran) ? $IdTahunAjaran : (string)$IdTahunAjaran;
        
        if (strpos($tahunAjaranStr, '/') !== false) {
            $tahunAjaranParts = explode('/', $tahunAjaranStr);
            $tahunAwal = isset($tahunAjaranParts[0]) ? (int)$tahunAjaranParts[0] : date('Y');
            $tahunAkhir = isset($tahunAjaranParts[1]) ? (int)$tahunAjaranParts[1] : $tahunAwal + 1;
        } else {
            $currentYear = (int)date('Y');
            $currentMonth = (int)date('m');
            if ($currentMonth >= 7) {
                $tahunAwal = $currentYear;
                $tahunAkhir = $currentYear + 1;
            } else {
                $tahunAwal = $currentYear - 1;
                $tahunAkhir = $currentYear;
            }
        }
        
        if ($semester == 'Ganjil') {
            $startDate = $tahunAwal . '-07-01';
            $endDate = $tahunAwal . '-12-31';
        } else { // Genap
            $startDate = $tahunAkhir . '-01-01';
            $endDate = $tahunAkhir . '-06-30';
        }
        
        log_message('debug', '[LIST SANTRI STATISTIK] Date range - Start: ' . $startDate . ', End: ' . $endDate);

        // Query untuk mendapatkan data santri dengan statistik kehadiran
        // Gunakan raw query untuk menghindari masalah dengan escape di join condition
        $sql = "
            SELECT 
                s.IdSantri,
                s.NamaSantri,
                s.IdKelas,
                k.NamaKelas,
                ? as Semester,
                ? as TahunAjaran,
                COALESCE(SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END), 0) as Hadir,
                COALESCE(SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END), 0) as Izin,
                COALESCE(SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END), 0) as Sakit,
                COALESCE(SUM(CASE WHEN a.Kehadiran = 'Alfa' THEN 1 ELSE 0 END), 0) as Alfa,
                COUNT(a.Id) as TotalAbsensi
            FROM tbl_santri_baru s
            LEFT JOIN tbl_kelas k ON s.IdKelas = k.IdKelas
            LEFT JOIN tbl_absensi_santri a ON s.IdSantri = a.IdSantri 
                AND a.IdKelas = s.IdKelas 
                AND a.IdTpq = s.IdTpq 
                AND a.IdTahunAjaran = ? 
                AND a.Tanggal >= ? 
                AND a.Tanggal <= ?
            WHERE s.IdTpq = ? 
                AND s.IdKelas = ? 
                AND s.Active = 1
            GROUP BY s.IdSantri, s.NamaSantri, s.IdKelas, k.NamaKelas
            ORDER BY s.NamaSantri ASC
        ";
        
        $query = $this->db->query($sql, [
            $semester,
            $IdTahunAjaran,
            $IdTahunAjaran,
            $startDate,
            $endDate,
            $IdTpq,
            $IdKelas
        ]);
        
        $result = $query->getResultArray();
        
        log_message('debug', '[LIST SANTRI STATISTIK] Total rows: ' . count($result));
        
        // Konversi tahun ajaran untuk setiap row jika perlu
        foreach ($result as &$row) {
            // Jika TahunAjaran masih ID (format: YYYYMMDD atau numeric), konversi ke format string
            $tahunAjaran = $row['TahunAjaran'];
            if (is_numeric($tahunAjaran) && strlen((string)$tahunAjaran) == 8) {
                $tahunAwal = substr((string)$tahunAjaran, 0, 4);
                $tahunAkhir = substr((string)$tahunAjaran, 4, 4);
                $row['TahunAjaran'] = $tahunAwal . '/' . $tahunAkhir;
            }
        }
        
        return $result;
    }

    /**
     * Get data kehadiran per kelas per hari untuk periode tertentu
     * Digunakan untuk Multi-Line Chart di dashboard kepala sekolah
     * 
     * @param int $IdTpq
     * @param string $startDate
     * @param string $endDate
     * @param mixed $IdTahunAjaran
     * @return array Data dengan format: [tanggal => [IdKelas => count_hadir]]
     */
    public function getKehadiranPerKelasPerHari($IdTpq, $startDate, $endDate, $IdTahunAjaran = null)
    {
        $builder = $this->select('Tanggal, IdKelas, COUNT(*) as count_hadir')
            ->where('IdTpq', $IdTpq)
            ->where('Kehadiran', 'Hadir')
            ->where('Tanggal >=', $startDate)
            ->where('Tanggal <=', $endDate);
        
        if ($IdTahunAjaran) {
            $builder->where('IdTahunAjaran', $IdTahunAjaran);
        }
        
        $result = $builder->groupBy('Tanggal, IdKelas')
            ->orderBy('Tanggal', 'ASC')
            ->orderBy('IdKelas', 'ASC')
            ->findAll();
        
        // Format data menjadi array yang lebih mudah digunakan
        $formattedData = [];
        foreach ($result as $row) {
            $tanggal = $row['Tanggal'] ?? $row->Tanggal ?? '';
            $idKelas = $row['IdKelas'] ?? $row->IdKelas ?? 0;
            $count = $row['count_hadir'] ?? $row->count_hadir ?? 0;
            
            if (!isset($formattedData[$tanggal])) {
                $formattedData[$tanggal] = [];
            }
            $formattedData[$tanggal][$idKelas] = (int)$count;
        }
        
        return $formattedData;
    }

    /**
     * Get data analitik statistik presensi komprehensif
     * Mendukung filter role: Admin (semua TPQ), Kepala TPQ/Operator (1 TPQ), Guru (kelas ampunya)
     * Ditambah filter status kehadiran (Hadir, Sakit, Izin, Alpa) jika dipilih
     */
    public function getAnalitikPresensi($IdTpq = null, $IdKelas = null, $startDate = null, $endDate = null, $IdTahunAjaran = null, $statusKehadiran = null)
    {
        // 1. KPI & Komposisi Keseluruhan
        $builderKpi = $this->db->table('tbl_absensi_santri a')
            ->select("
                COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END) as total_record,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN a.Kehadiran IN ('Alfa', 'Alpa') THEN 1 ELSE 0 END) as total_alpa
            ");

        $builderKpi->where('a.IdKelas !=', 10);
        $builderKpi->where('a.Kehadiran !=', 'Pra-Masuk');
        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderKpi->where('a.IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderKpi->whereIn('a.IdKelas', $IdKelas);
            } else {
                $builderKpi->where('a.IdKelas', $IdKelas);
            }
        }
        if (!empty($startDate)) {
            $builderKpi->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builderKpi->where('a.Tanggal <=', $endDate);
        }
        if (!empty($IdTahunAjaran)) {
            $builderKpi->where('a.IdTahunAjaran', $IdTahunAjaran);
        }
        if (!empty($statusKehadiran)) {
            if (in_array(strtolower($statusKehadiran), ['alpa', 'alfa'])) {
                $builderKpi->whereIn('a.Kehadiran', ['Alfa', 'Alpa']);
            } else {
                $builderKpi->where('a.Kehadiran', $statusKehadiran);
            }
        }

        $kpi = $builderKpi->get()->getRowArray() ?: [
            'total_record' => 0, 'total_hadir' => 0, 'total_sakit' => 0, 'total_izin' => 0, 'total_alpa' => 0
        ];

        // 2. Tren Kehadiran Time-Series (Per Tanggal)
        $builderTrend = $this->db->table('tbl_absensi_santri a')
            ->select("
                a.Tanggal,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.Kehadiran IN ('Alfa', 'Alpa') THEN 1 ELSE 0 END) as alpa,
                COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END) as total
            ")
            ->where('a.IdKelas !=', 10)
            ->where('a.Kehadiran !=', 'Pra-Masuk');

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderTrend->where('a.IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderTrend->whereIn('a.IdKelas', $IdKelas);
            } else {
                $builderTrend->where('a.IdKelas', $IdKelas);
            }
        }
        if (!empty($startDate)) {
            $builderTrend->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builderTrend->where('a.Tanggal <=', $endDate);
        }
        if (!empty($IdTahunAjaran)) {
            $builderTrend->where('a.IdTahunAjaran', $IdTahunAjaran);
        }
        if (!empty($statusKehadiran)) {
            if (in_array(strtolower($statusKehadiran), ['alpa', 'alfa'])) {
                $builderTrend->whereIn('a.Kehadiran', ['Alfa', 'Alpa']);
            } else {
                $builderTrend->where('a.Kehadiran', $statusKehadiran);
            }
        }

        $trendHarian = $builderTrend->groupBy('a.Tanggal')->orderBy('a.Tanggal', 'ASC')->get()->getResultArray();

        // 3. Fluktuasi Kehadiran Per Kelas Time-Series (Tanggal & Kelas)
        $builderFluktuasi = $this->db->table('tbl_absensi_santri a')
            ->select("
                a.Tanggal,
                a.IdKelas,
                MAX(k.NamaKelas) as NamaKelas,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END) as total
            ")
            ->join('tbl_kelas k', 'a.IdKelas = k.IdKelas', 'left')
            ->where('a.IdKelas !=', 10)
            ->where('a.Kehadiran !=', 'Pra-Masuk');

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderFluktuasi->where('a.IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderFluktuasi->whereIn('a.IdKelas', $IdKelas);
            } else {
                $builderFluktuasi->where('a.IdKelas', $IdKelas);
            }
        }
        if (!empty($startDate)) {
            $builderFluktuasi->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builderFluktuasi->where('a.Tanggal <=', $endDate);
        }
        if (!empty($IdTahunAjaran)) {
            $builderFluktuasi->where('a.IdTahunAjaran', $IdTahunAjaran);
        }
        if (!empty($statusKehadiran)) {
            if (in_array(strtolower($statusKehadiran), ['alpa', 'alfa'])) {
                $builderFluktuasi->whereIn('a.Kehadiran', ['Alfa', 'Alpa']);
            } else {
                $builderFluktuasi->where('a.Kehadiran', $statusKehadiran);
            }
        }

        $fluktuasiRaw = $builderFluktuasi->groupBy('a.Tanggal, a.IdKelas')
            ->orderBy('a.Tanggal', 'ASC')
            ->orderBy('a.IdKelas', 'ASC')
            ->get()->getResultArray();

        // 4. Ranking dan Perbandingan Total per Kelas
        $builderRanking = $this->db->table('tbl_absensi_santri a')
            ->select("
                a.IdKelas,
                MAX(k.NamaKelas) as NamaKelas,
                COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END) as total,
                COUNT(DISTINCT a.Tanggal) as jumlah_hari_efektif,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.Kehadiran IN ('Alfa', 'Alpa') THEN 1 ELSE 0 END) as alpa,
                ROUND((SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) / NULLIF(COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END), 0)) * 100, 1) as persentase_hadir
            ")
            ->join('tbl_kelas k', 'a.IdKelas = k.IdKelas', 'left')
            ->where('a.IdKelas !=', 10)
            ->where('a.Kehadiran !=', 'Pra-Masuk');

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderRanking->where('a.IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderRanking->whereIn('a.IdKelas', $IdKelas);
            } else {
                $builderRanking->where('a.IdKelas', $IdKelas);
            }
        }
        if (!empty($startDate)) {
            $builderRanking->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builderRanking->where('a.Tanggal <=', $endDate);
        }
        if (!empty($IdTahunAjaran)) {
            $builderRanking->where('a.IdTahunAjaran', $IdTahunAjaran);
        }
        if (!empty($statusKehadiran)) {
            if (in_array(strtolower($statusKehadiran), ['alpa', 'alfa'])) {
                $builderRanking->whereIn('a.Kehadiran', ['Alfa', 'Alpa']);
            } else {
                $builderRanking->where('a.Kehadiran', $statusKehadiran);
            }
        }

        $rankingKelasRaw = $builderRanking->groupBy('a.IdKelas')
            ->get()->getResultArray();

        // Cari hari efektif maksimal di antara kelas-kelas pada rentang waktu terpilih
        $maxHariEfektif = 0;
        foreach ($rankingKelasRaw as $row) {
            $hari = (int)($row['jumlah_hari_efektif'] ?? 0);
            if ($hari > $maxHariEfektif) {
                $maxHariEfektif = $hari;
            }
        }

        // Hitung skor performa berbobot: persentase_hadir * (hari_efektif / max_hari_efektif)
        $rankingKelas = [];
        foreach ($rankingKelasRaw as $row) {
            $hari = (int)($row['jumlah_hari_efektif'] ?? 0);
            $pctHadir = (float)($row['persentase_hadir'] ?? 0);
            $rasioHari = ($maxHariEfektif > 0) ? ($hari / $maxHariEfektif) : 1;
            $skorPerforma = round($pctHadir * $rasioHari, 1);

            $row['max_hari_efektif'] = $maxHariEfektif;
            $row['skor_performa'] = $skorPerforma;
            $row['rasio_hari'] = round($rasioHari * 100, 1);
            $rankingKelas[] = $row;
        }

        // Urutkan ranking berdasarkan skor performa tertinggi, lalu persentase kehadiran, lalu hari efektif
        usort($rankingKelas, function ($a, $b) {
            if ($b['skor_performa'] != $a['skor_performa']) {
                return ($b['skor_performa'] <=> $a['skor_performa']);
            }
            if ($b['persentase_hadir'] != $a['persentase_hadir']) {
                return ($b['persentase_hadir'] <=> $a['persentase_hadir']);
            }
            return ($b['jumlah_hari_efektif'] <=> $a['jumlah_hari_efektif']);
        });

        // 4b. Statistik Kerajinan / Kepatuhan Input Presensi Per Kelas
        $builderAllKelas = $this->db->table('tbl_kelas_santri ks')
            ->select('ks.IdKelas, k.NamaKelas')
            ->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left')
            ->where('ks.Status', 1)
            ->where('ks.IdKelas !=', 10);

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderAllKelas->where('ks.IdTpq', $IdTpq);
        }
        if (!empty($IdTahunAjaran)) {
            $builderAllKelas->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderAllKelas->whereIn('ks.IdKelas', $IdKelas);
            } else {
                $builderAllKelas->where('ks.IdKelas', $IdKelas);
            }
        }
        $masterKelasList = $builderAllKelas->groupBy('ks.IdKelas')->get()->getResultArray();

        // Jika masterKelasList kosong, fallback ke tbl_kelas biasa (Kecualikan Alumni IdKelas = 10)
        if (empty($masterKelasList)) {
            $fallbackBuilder = $this->db->table('tbl_kelas')
                ->select('IdKelas, NamaKelas')
                ->where('IdKelas !=', 10);
            if (!empty($IdKelas)) {
                if (is_array($IdKelas)) {
                    $fallbackBuilder->whereIn('IdKelas', $IdKelas);
                } else {
                    $fallbackBuilder->where('IdKelas', $IdKelas);
                }
            }
            $masterKelasList = $fallbackBuilder->get()->getResultArray();
        }

        // Peta data absensi kelas dari rankingKelasRaw (Kecualikan Alumni IdKelas = 10)
        $absensiKelasMap = [];
        $maxHariAbsen = 0;
        foreach ($rankingKelasRaw as $r) {
            if ((int)$r['IdKelas'] == 10) continue;
            $absensiKelasMap[$r['IdKelas']] = $r;
            $hari = (int)($r['jumlah_hari_efektif'] ?? 0);
            if ($hari > $maxHariAbsen) {
                $maxHariAbsen = $hari;
            }
        }

        // Ambil tanggal terakhir absen per kelas
        $builderLastDate = $this->db->table('tbl_absensi_santri a')
            ->select('a.IdKelas, MAX(a.Tanggal) as terakhir_absen')
            ->where('a.IdKelas !=', 10)
            ->groupBy('a.IdKelas');
        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderLastDate->where('a.IdTpq', $IdTpq);
        }
        if (!empty($startDate)) {
            $builderLastDate->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builderLastDate->where('a.Tanggal <=', $endDate);
        }
        if (!empty($IdTahunAjaran)) {
            $builderLastDate->where('a.IdTahunAjaran', $IdTahunAjaran);
        }
        $lastDateMap = [];
        foreach ($builderLastDate->get()->getResultArray() as $ld) {
            $lastDateMap[$ld['IdKelas']] = $ld['terakhir_absen'];
        }

        $kerajinanKelas = [];
        $totalRajin = 0;
        $totalCukup = 0;
        $totalJarang = 0;
        $processedKelasIds = [];

        foreach ($masterKelasList as $k) {
            $idK = $k['IdKelas'];
            $processedKelasIds[] = $idK;
            $absenData = $absensiKelasMap[$idK] ?? null;

            $jmlHari = $absenData ? (int)$absenData['jumlah_hari_efektif'] : 0;
            $totalPresensi = $absenData ? (int)$absenData['total'] : 0;
            $hadir = $absenData ? (int)$absenData['hadir'] : 0;
            $pctHadir = $absenData ? (float)$absenData['persentase_hadir'] : 0;
            $terakhirAbsen = $lastDateMap[$idK] ?? null;

            $rasioKeaktifan = ($maxHariAbsen > 0) ? round(($jmlHari / $maxHariAbsen) * 100, 1) : ($jmlHari > 0 ? 100 : 0);

            if ($rasioKeaktifan >= 80 && $jmlHari > 0) {
                $statusKerajinan = 'rajin';
                $labelStatus = 'Sangat Rajin / Disiplin';
                $badgeClass = 'badge-success';
                $colorHex = '#28a745';
                $totalRajin++;
            } elseif ($rasioKeaktifan >= 50 && $jmlHari > 0) {
                $statusKerajinan = 'cukup';
                $labelStatus = 'Cukup Rajin';
                $badgeClass = 'badge-warning';
                $colorHex = '#ffc107';
                $totalCukup++;
            } else {
                $statusKerajinan = 'jarang';
                $labelStatus = ($jmlHari == 0) ? 'Belum Pernah Absen' : 'Jarang Absen / Perlu Evaluasi';
                $badgeClass = 'badge-danger';
                $colorHex = '#dc3545';
                $totalJarang++;
            }

            $kerajinanKelas[] = [
                'IdKelas' => $idK,
                'NamaKelas' => $k['NamaKelas'] ?: 'Kelas ' . $idK,
                'jumlah_hari_absen' => $jmlHari,
                'max_hari_periode' => $maxHariAbsen,
                'total_presensi' => $totalPresensi,
                'persentase_hadir' => $pctHadir,
                'rasio_keaktifan' => $rasioKeaktifan,
                'status_kerajinan' => $statusKerajinan,
                'label_status' => $labelStatus,
                'badge_class' => $badgeClass,
                'color_hex' => $colorHex,
                'terakhir_absen' => $terakhirAbsen
            ];
        }

        // Sertakan juga kelas yang ada di log absensi jika belum ada di masterKelasList (Kecualikan Alumni)
        foreach ($absensiKelasMap as $idK => $absenData) {
            if ((int)$idK == 10 || in_array($idK, $processedKelasIds)) {
                continue;
            }
            $jmlHari = (int)$absenData['jumlah_hari_efektif'];
                $totalPresensi = (int)$absenData['total'];
                $pctHadir = (float)$absenData['persentase_hadir'];
                $terakhirAbsen = $lastDateMap[$idK] ?? null;
                $rasioKeaktifan = ($maxHariAbsen > 0) ? round(($jmlHari / $maxHariAbsen) * 100, 1) : 100;

                if ($rasioKeaktifan >= 80 && $jmlHari > 0) {
                    $statusKerajinan = 'rajin';
                    $labelStatus = 'Sangat Rajin / Disiplin';
                    $badgeClass = 'badge-success';
                    $colorHex = '#28a745';
                    $totalRajin++;
                } elseif ($rasioKeaktifan >= 50 && $jmlHari > 0) {
                    $statusKerajinan = 'cukup';
                    $labelStatus = 'Cukup Rajin';
                    $badgeClass = 'badge-warning';
                    $colorHex = '#ffc107';
                    $totalCukup++;
                } else {
                    $statusKerajinan = 'jarang';
                    $labelStatus = ($jmlHari == 0) ? 'Belum Pernah Absen' : 'Jarang Absen / Perlu Evaluasi';
                    $badgeClass = 'badge-danger';
                    $colorHex = '#dc3545';
                    $totalJarang++;
                }

                $kerajinanKelas[] = [
                    'IdKelas' => $idK,
                    'NamaKelas' => $absenData['NamaKelas'] ?: 'Kelas ' . $idK,
                    'jumlah_hari_absen' => $jmlHari,
                    'max_hari_periode' => $maxHariAbsen,
                    'total_presensi' => $totalPresensi,
                    'persentase_hadir' => $pctHadir,
                    'rasio_keaktifan' => $rasioKeaktifan,
                    'status_kerajinan' => $statusKerajinan,
                    'label_status' => $labelStatus,
                    'badge_class' => $badgeClass,
                    'color_hex' => $colorHex,
                    'terakhir_absen' => $terakhirAbsen
                ];
        }

        // Urutkan kelas dari yang paling rajin (hari absen terbanyak) ke yang paling jarang
        usort($kerajinanKelas, function ($a, $b) {
            if ($b['jumlah_hari_absen'] != $a['jumlah_hari_absen']) {
                return ($b['jumlah_hari_absen'] <=> $a['jumlah_hari_absen']);
            }
            return ($b['rasio_keaktifan'] <=> $a['rasio_keaktifan']);
        });

        $summaryKerajinan = [
            'total_kelas' => count($kerajinanKelas),
            'total_rajin' => $totalRajin,
            'total_cukup' => $totalCukup,
            'total_jarang' => $totalJarang,
            'max_hari_periode' => $maxHariAbsen
        ];

        // 4c. Analitik & Kerajinan Berdasarkan TPQ (Khusus Admin / Komparasi Antar-TPQ)
        $builderAllTpq = $this->db->table('tbl_tpq')
            ->select('IdTpq, NamaTpq')
            ->orderBy('NamaTpq', 'ASC');
        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderAllTpq->where('IdTpq', $IdTpq);
        }
        $masterTpqList = $builderAllTpq->get()->getResultArray();

        $builderAbsensiTpq = $this->db->table('tbl_absensi_santri a')
            ->select("
                a.IdTpq,
                MAX(t.NamaTpq) as NamaTpq,
                COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END) as total_presensi,
                COUNT(DISTINCT a.Tanggal) as jumlah_hari_aktif,
                MAX(a.Tanggal) as terakhir_absen,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.Kehadiran IN ('Alfa', 'Alpa') THEN 1 ELSE 0 END) as alpa,
                ROUND((SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) / NULLIF(COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END), 0)) * 100, 1) as persentase_hadir
            ")
            ->join('tbl_tpq t', 'a.IdTpq = t.IdTpq', 'left')
            ->where('a.IdKelas !=', 10)
            ->where('a.Kehadiran !=', 'Pra-Masuk');

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderAbsensiTpq->where('a.IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderAbsensiTpq->whereIn('a.IdKelas', $IdKelas);
            } else {
                $builderAbsensiTpq->where('a.IdKelas', $IdKelas);
            }
        }
        if (!empty($startDate)) {
            $builderAbsensiTpq->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builderAbsensiTpq->where('a.Tanggal <=', $endDate);
        }
        if (!empty($IdTahunAjaran)) {
            $builderAbsensiTpq->where('a.IdTahunAjaran', $IdTahunAjaran);
        }
        if (!empty($statusKehadiran)) {
            if (in_array(strtolower($statusKehadiran), ['alpa', 'alfa'])) {
                $builderAbsensiTpq->whereIn('a.Kehadiran', ['Alfa', 'Alpa']);
            } else {
                $builderAbsensiTpq->where('a.Kehadiran', $statusKehadiran);
            }
        }

        $absensiTpqRaw = $builderAbsensiTpq->groupBy('a.IdTpq')->get()->getResultArray();
        $absensiTpqMap = [];
        $maxHariTpq = 0;
        foreach ($absensiTpqRaw as $tpqRow) {
            $absensiTpqMap[$tpqRow['IdTpq']] = $tpqRow;
            $hari = (int)($tpqRow['jumlah_hari_aktif'] ?? 0);
            if ($hari > $maxHariTpq) {
                $maxHariTpq = $hari;
            }
        }

        $dataTpqAnalitik = [];
        $totalTpqRajin = 0;
        $totalTpqCukup = 0;
        $totalTpqJarang = 0;
        $totalTpqTidakAktif = 0;

        foreach ($masterTpqList as $tpq) {
            $idT = $tpq['IdTpq'];
            $absen = $absensiTpqMap[$idT] ?? null;

            $jmlHari = $absen ? (int)$absen['jumlah_hari_aktif'] : 0;
            $total = $absen ? (int)$absen['total_presensi'] : 0;
            $hadir = $absen ? (int)$absen['hadir'] : 0;
            $sakit = $absen ? (int)$absen['sakit'] : 0;
            $izin = $absen ? (int)$absen['izin'] : 0;
            $alpa = $absen ? (int)$absen['alpa'] : 0;
            $pctHadir = $absen ? (float)$absen['persentase_hadir'] : 0;
            $terakhir = $absen ? $absen['terakhir_absen'] : null;

            $rasio = ($maxHariTpq > 0) ? round(($jmlHari / $maxHariTpq) * 100, 1) : ($jmlHari > 0 ? 100 : 0);

            if ($jmlHari == 0) {
                $status = 'tidak_aktif';
                $labelStatus = 'Tidak Menjalankan Absensi';
                $badgeClass = 'badge-dark';
                $colorHex = '#6c757d';
                $totalTpqTidakAktif++;
            } elseif ($rasio >= 80) {
                $status = 'rajin';
                $labelStatus = 'Sangat Rajin / Disiplin';
                $badgeClass = 'badge-success';
                $colorHex = '#28a745';
                $totalTpqRajin++;
            } elseif ($rasio >= 50) {
                $status = 'cukup';
                $labelStatus = 'Cukup Rajin';
                $badgeClass = 'badge-warning';
                $colorHex = '#ffc107';
                $totalTpqCukup++;
            } else {
                $status = 'jarang';
                $labelStatus = 'Jarang Absen / Perlu Evaluasi';
                $badgeClass = 'badge-danger';
                $colorHex = '#dc3545';
                $totalTpqJarang++;
            }

            $dataTpqAnalitik[] = [
                'IdTpq' => $idT,
                'NamaTpq' => $tpq['NamaTpq'],
                'jumlah_hari_aktif' => $jmlHari,
                'max_hari_periode' => $maxHariTpq,
                'total_presensi' => $total,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'persentase_hadir' => $pctHadir,
                'rasio_keaktifan' => $rasio,
                'status_keaktifan' => $status,
                'label_status' => $labelStatus,
                'badge_class' => $badgeClass,
                'color_hex' => $colorHex,
                'terakhir_absen' => $terakhir
            ];
        }

        // Urutkan TPQ: yang aktif (hari terbanyak) di atas, yang tidak aktif di bawah
        usort($dataTpqAnalitik, function ($a, $b) {
            if ($b['jumlah_hari_aktif'] != $a['jumlah_hari_aktif']) {
                return ($b['jumlah_hari_aktif'] <=> $a['jumlah_hari_aktif']);
            }
            return ($b['total_presensi'] <=> $a['total_presensi']);
        });

        $summaryTpq = [
            'total_tpq' => count($dataTpqAnalitik),
            'total_rajin' => $totalTpqRajin,
            'total_cukup' => $totalTpqCukup,
            'total_jarang' => $totalTpqJarang,
            'total_tidak_aktif' => $totalTpqTidakAktif,
            'max_hari_periode' => $maxHariTpq
        ];

        // Hitung hari aktif/efektif per TPQ untuk baseline kalender Opsi B
        $builderHariTpq = $this->db->table('tbl_absensi_santri a')
            ->select('a.IdTpq, COUNT(DISTINCT a.Tanggal) as hari_efektif')
            ->where('a.IdKelas !=', 10);

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderHariTpq->where('a.IdTpq', $IdTpq);
        }
        if (!empty($startDate)) {
            $builderHariTpq->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builderHariTpq->where('a.Tanggal <=', $endDate);
        }
        if (!empty($IdTahunAjaran)) {
            $builderHariTpq->where('a.IdTahunAjaran', $IdTahunAjaran);
        }

        $hariTpqRows = $builderHariTpq->groupBy('a.IdTpq')->get()->getResultArray();
        $hariEfektifTpqMap = [];
        foreach ($hariTpqRows as $ht) {
            $hariEfektifTpqMap[$ht['IdTpq']] = (int)$ht['hari_efektif'];
        }

        // 5. Matriks Presensi Per Santri
        $statusAbsenFilterSql = "";
        if (!empty($statusKehadiran)) {
            if (in_array(strtolower($statusKehadiran), ['alpa', 'alfa'])) {
                $statusAbsenFilterSql = " AND a.Kehadiran IN ('Alfa', 'Alpa')";
            } else {
                $statusAbsenFilterSql = " AND a.Kehadiran = '{$statusKehadiran}'";
            }
        }

        $builderSantri = $this->db->table('tbl_santri_baru s')
            ->select("
                s.IdSantri,
                a.IdKelas,
                MAX(s.NamaSantri) as NamaSantri,
                MAX(COALESCE(NULLIF(s.NoHpAyah, ''), NULLIF(s.NoHpIbu, ''), NULLIF(s.NoHpWali, ''), NULLIF(s.NoHpSantri, ''), '')) as NoHpOrtu,
                MAX(s.Active) as Active,
                MAX(COALESCE(k.NamaKelas, 'Tanpa Kelas')) as NamaKelas,
                MAX(COALESCE(s.IdTpq, a.IdTpq)) as IdTpq,
                MAX(t.NamaTpq) as NamaTpq,
                COUNT(a.Id) as total_absen,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN a.Kehadiran IN ('Alfa', 'Alpa') THEN 1 ELSE 0 END) as total_alpa,
                SUM(CASE WHEN a.Kehadiran = 'Pra-Masuk' THEN 1 ELSE 0 END) as total_pra_masuk
            ")
            ->join('tbl_absensi_santri a', 's.IdSantri = a.IdSantri' . 
                (!empty($startDate) ? " AND a.Tanggal >= '{$startDate}'" : "") .
                (!empty($endDate) ? " AND a.Tanggal <= '{$endDate}'" : "") .
                (!empty($IdTahunAjaran) ? " AND a.IdTahunAjaran = '{$IdTahunAjaran}'" : "") .
                $statusAbsenFilterSql, 
                'inner')
            ->join('tbl_kelas k', 'a.IdKelas = k.IdKelas', 'left')
            ->join('tbl_tpq t', 's.IdTpq = t.IdTpq', 'left')
            ->where('s.Active', 1)
            ->where('a.IdKelas !=', 10);

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderSantri->where('s.IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderSantri->whereIn('a.IdKelas', $IdKelas);
            } else {
                $builderSantri->where('a.IdKelas', $IdKelas);
            }
        }

        $santriRecap = $builderSantri->groupBy('s.IdSantri, a.IdKelas')
            ->orderBy('a.IdKelas', 'ASC')
            ->orderBy('MAX(s.NamaSantri)', 'ASC')
            ->get()->getResultArray();

        foreach ($santriRecap as &$item) {
            $tpqId = $item['IdTpq'] ?? $IdTpq;
            $hadir = (int)$item['total_hadir'];
            $sakit = (int)$item['total_sakit'];
            $izin = (int)$item['total_izin'];
            $alfaCatat = (int)$item['total_alpa'];
            $praMasuk = (int)($item['total_pra_masuk'] ?? 0);
            $totalCatat = $hadir + $sakit + $izin + $alfaCatat;

            if (empty($statusKehadiran)) {
                // OPSI B: Samakan penyebut dengan Total Hari Efektif TPQ santri tersebut dikurangi hari Pra-Masuk
                $hariEfektifMaks = !empty($hariEfektifTpqMap[$tpqId]) ? $hariEfektifTpqMap[$tpqId] : ($totalCatat + $praMasuk);
                $targetHari = max(0, $hariEfektifMaks - $praMasuk);
                $targetHariFinal = max($targetHari, $totalCatat);

                if ($targetHariFinal > $totalCatat) {
                    $hariKosong = $targetHariFinal - $totalCatat;
                    $alpaFinal = $alfaCatat + $hariKosong;
                    $totalFinal = $targetHariFinal;
                } else {
                    $alpaFinal = $alfaCatat;
                    $totalFinal = $totalCatat;
                }
            } else {
                $alpaFinal = $alfaCatat;
                $totalFinal = $totalCatat;
            }

            $item['total_absen'] = $totalFinal;
            $item['total_alpa'] = $alpaFinal;
            $item['persentase_hadir'] = $totalFinal > 0 ? round(($hadir / $totalFinal) * 100, 1) : 0;
        }
        unset($item);

        // Singkronkan KPI keseluruhan dengan Opsi B agar 100% konsisten dengan tabel rekap
        if (empty($statusKehadiran) && !empty($santriRecap)) {
            $kpiHadir = 0;
            $kpiSakit = 0;
            $kpiIzin = 0;
            $kpiAlpa = 0;
            $kpiTotal = 0;
            foreach ($santriRecap as $s) {
                $kpiHadir += (int)$s['total_hadir'];
                $kpiSakit += (int)$s['total_sakit'];
                $kpiIzin += (int)$s['total_izin'];
                $kpiAlpa += (int)$s['total_alpa'];
                $kpiTotal += (int)$s['total_absen'];
            }
            $kpi = [
                'total_record' => $kpiTotal,
                'total_hadir' => $kpiHadir,
                'total_sakit' => $kpiSakit,
                'total_izin' => $kpiIzin,
                'total_alpa' => $kpiAlpa,
            ];
        }

        // 6. Komposisi Presensi Per Kelas (Hadir, Sakit, Izin, Alpa berbobot Opsi B)
        $komposisiKelasMap = [];
        foreach ($santriRecap as $s) {
            $idK = $s['IdKelas'];
            if (!isset($komposisiKelasMap[$idK])) {
                $komposisiKelasMap[$idK] = [
                    'IdKelas' => $idK,
                    'NamaKelas' => $s['NamaKelas'],
                    'hadir' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpa' => 0,
                    'total' => 0,
                    'jumlah_santri' => 0,
                    'jumlah_hari_efektif' => isset($absensiKelasMap[$idK]['jumlah_hari_efektif']) ? (int)$absensiKelasMap[$idK]['jumlah_hari_efektif'] : 0,
                ];
            }
            $komposisiKelasMap[$idK]['hadir'] += (int)$s['total_hadir'];
            $komposisiKelasMap[$idK]['sakit'] += (int)$s['total_sakit'];
            $komposisiKelasMap[$idK]['izin'] += (int)$s['total_izin'];
            $komposisiKelasMap[$idK]['alpa'] += (int)$s['total_alpa'];
            $komposisiKelasMap[$idK]['total'] += (int)$s['total_absen'];
            $komposisiKelasMap[$idK]['jumlah_santri']++;
        }

        $komposisiKelas = array_values($komposisiKelasMap);
        foreach ($komposisiKelas as &$kls) {
            $tot = $kls['total'];
            $kls['persentase_hadir'] = $tot > 0 ? round(($kls['hadir'] / $tot) * 100, 1) : 0;
            $kls['persentase_sakit'] = $tot > 0 ? round(($kls['sakit'] / $tot) * 100, 1) : 0;
            $kls['persentase_izin'] = $tot > 0 ? round(($kls['izin'] / $tot) * 100, 1) : 0;
            $kls['persentase_alpa'] = $tot > 0 ? round(($kls['alpa'] / $tot) * 100, 1) : 0;
        }
        unset($kls);

        // Urutkan kelas sesuai urutan IdKelas
        usort($komposisiKelas, function($a, $b) {
            return ($a['IdKelas'] <=> $b['IdKelas']);
        });

        return [
            'kpi' => $kpi,
            'trend_harian' => $trendHarian,
            'fluktuasi_kelas' => $fluktuasiRaw,
            'ranking_kelas' => $rankingKelas,
            'kerajinan_kelas' => $kerajinanKelas,
            'summary_kerajinan' => $summaryKerajinan,
            'analitik_tpq' => $dataTpqAnalitik,
            'summary_tpq' => $summaryTpq,
            'komposisi_kelas' => $komposisiKelas,
            'santri_recap' => $santriRecap
        ];
    }

    /**
     * Get detail riwayat absensi seorang santri
     */
    public function getRiwayatSantri($idSantri, $startDate = null, $endDate = null, $idTahunAjaran = null)
    {
        $builder = $this->db->table('tbl_absensi_santri a')
            ->select('a.*, k.NamaKelas, g.Nama as NamaGuru')
            ->join('tbl_kelas k', 'a.IdKelas = k.IdKelas', 'left')
            ->join('tbl_guru g', 'a.IdGuru = g.IdGuru', 'left')
            ->where('a.IdSantri', $idSantri)
            ->where('a.Kehadiran !=', 'Pra-Masuk');

        if (!empty($startDate)) {
            $builder->where('a.Tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('a.Tanggal <=', $endDate);
        }
        if (!empty($idTahunAjaran)) {
            $builder->where('a.IdTahunAjaran', $idTahunAjaran);
        }

        return $builder->orderBy('a.Tanggal', 'DESC')->get()->getResultArray();
    }

    /**
     * Mengambil daftar tanggal aktif TPQ yang belum diabsen per kelas (Konsep Opsi B)
     * Mendukung periode: 'minggu_ini', 'minggu_kemarin', 'bulan', 'semester'
     * Dilengkapi grouping per bulan untuk overview periode panjang (seperti semester)
     */
    public function getTanggalBelumAbsenPerKelasPeriode($IdTpq, $periode = 'bulan', $tanggalRef = null, $IdTahunAjaran = null)
    {
        if (empty($tanggalRef)) {
            $tanggalRef = date('Y-m-d');
        }

        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanIndo = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $tsRef = strtotime($tanggalRef);
        $dRef = date('d', $tsRef);
        $mRef = date('m', $tsRef);
        $yRef = date('Y', $tsRef);

        // Tentukan startDate, endDate, dan label berdasarkan periode
        switch ($periode) {
            case 'minggu_ini':
                $dayOfWeek = (int)date('N', $tsRef); // 1 (Senin) - 7 (Minggu)
                $startDate = date('Y-m-d', strtotime('-' . ($dayOfWeek - 1) . ' days', $tsRef));
                $endDate = date('Y-m-d', strtotime('+' . (7 - $dayOfWeek) . ' days', $tsRef));
                $periodeLabel = 'Minggu Ini (' . date('d M', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)) . ')';
                break;

            case 'minggu_kemarin':
                $dayOfWeek = (int)date('N', $tsRef);
                $seninIni = strtotime('-' . ($dayOfWeek - 1) . ' days', $tsRef);
                $startDate = date('Y-m-d', strtotime('-7 days', $seninIni));
                $endDate = date('Y-m-d', strtotime('+6 days', strtotime($startDate)));
                $periodeLabel = 'Minggu Kemarin (' . date('d M', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)) . ')';
                break;

            case 'semester':
                $mInt = (int)$mRef;
                if ($mInt >= 7) {
                    $startDate = $yRef . '-07-01';
                    $endDate = $yRef . '-12-31';
                    $periodeLabel = 'Semester Ganjil ' . $yRef . ' (Juli - Des ' . $yRef . ')';
                } else {
                    $startDate = $yRef . '-01-01';
                    $endDate = $yRef . '-06-30';
                    $periodeLabel = 'Semester Genap ' . $yRef . ' (Jan - Jun ' . $yRef . ')';
                }
                break;

            case 'bulan':
            default:
                $periode = 'bulan';
                $startDate = date('Y-m-01', $tsRef);
                $endDate = date('Y-m-t', $tsRef);
                $periodeLabel = 'Bulan ' . ($bulanIndo[$mRef] ?? '') . ' ' . $yRef;
                break;
        }

        $builder = $this->db->table('tbl_absensi_santri a')
            ->select('a.IdKelas, a.Tanggal')
            ->where('a.IdKelas !=', 10)
            ->where('a.Tanggal >=', $startDate)
            ->where('a.Tanggal <=', $endDate);

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builder->where('a.IdTpq', $IdTpq);
        }
        if (!empty($IdTahunAjaran)) {
            $builder->where('a.IdTahunAjaran', $IdTahunAjaran);
        }

        $rows = $builder->groupBy('a.IdKelas, a.Tanggal')->get()->getResultArray();

        $semuaTanggalTpq = [];
        $sudahAbsenMap = [];

        foreach ($rows as $r) {
            $tgl = $r['Tanggal'];
            $idK = (int)$r['IdKelas'];
            $semuaTanggalTpq[$tgl] = true;
            $sudahAbsenMap[$idK][$tgl] = true;
        }

        ksort($semuaTanggalTpq);
        $listTanggalTpq = array_keys($semuaTanggalTpq);
        $totalHariAktif = count($listTanggalTpq);

        // Kumpulkan daftar bulan unik yang ada di rentang tanggal aktif TPQ
        $bulanAktifList = [];
        foreach ($listTanggalTpq as $tgl) {
            $bKey = substr($tgl, 0, 7); // Y-m
            if (!isset($bulanAktifList[$bKey])) {
                $bParts = explode('-', $bKey);
                $bNama = ($bulanIndo[$bParts[1] ?? ''] ?? '') . ' ' . ($bParts[0] ?? '');
                $bulanAktifList[$bKey] = [
                    'bulan_key' => $bKey,
                    'bulan_label' => $bNama,
                    'tanggal_aktif' => []
                ];
            }
            $bulanAktifList[$bKey]['tanggal_aktif'][] = $tgl;
        }

        // Struktur hasil per kelas
        $hasilPerKelas = [];
        $builderKelas = $this->db->table('tbl_kelas')
            ->where('IdKelas !=', 10);
        $allKelas = $builderKelas->get()->getResultArray();

        foreach ($allKelas as $k) {
            $idK = (int)$k['IdKelas'];
            $sudahList = $sudahAbsenMap[$idK] ?? [];
            $totalSudah = count($sudahList);

            $belumList = [];
            $groupedByMonth = [];

            // Inisialisasi grup bulan dari bulanAktifList
            foreach ($bulanAktifList as $bKey => $bMeta) {
                $groupedByMonth[$bKey] = [
                    'bulan_key' => $bKey,
                    'bulan_label' => $bMeta['bulan_label'],
                    'total_aktif' => count($bMeta['tanggal_aktif']),
                    'total_sudah' => 0,
                    'total_belum' => 0,
                    'is_lengkap' => false,
                    'tanggal_list' => []
                ];
            }

            foreach ($listTanggalTpq as $tgl) {
                $bKey = substr($tgl, 0, 7);
                if (isset($sudahList[$tgl])) {
                    if (isset($groupedByMonth[$bKey])) {
                        $groupedByMonth[$bKey]['total_sudah']++;
                    }
                } else {
                    $ts = strtotime($tgl);
                    $dayIdx = (int)date('w', $ts);
                    $d = date('d', $ts);
                    $m = date('m', $ts);
                    $y = date('Y', $ts);
                    $tglItem = [
                        'tanggal' => $tgl,
                        'hari' => $hariIndo[$dayIdx],
                        'formatted' => date('d/m/Y', $ts),
                        'label' => $hariIndo[$dayIdx] . ', ' . $d . ' ' . ($bulanIndo[$m] ?? '') . ' ' . $y,
                        'badge_label' => $hariIndo[$dayIdx] . ', ' . $d . '/' . $m
                    ];

                    $belumList[] = $tglItem;
                    if (isset($groupedByMonth[$bKey])) {
                        $groupedByMonth[$bKey]['total_belum']++;
                        $groupedByMonth[$bKey]['tanggal_list'][] = $tglItem;
                    }
                }
            }

            foreach ($groupedByMonth as &$g) {
                $g['is_lengkap'] = ($g['total_aktif'] > 0 && $g['total_belum'] === 0);
            }
            unset($g);

            $hasilPerKelas[$idK] = [
                'IdKelas' => $idK,
                'total_hari_aktif_tpq' => $totalHariAktif,
                'total_sudah_absen' => $totalSudah,
                'total_belum_absen' => count($belumList),
                'belum_absen_list' => $belumList,
                'grouped_by_month' => array_values($groupedByMonth)
            ];
        }

        return [
            'periode' => $periode,
            'periode_label' => $periodeLabel,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_hari_aktif_tpq' => $totalHariAktif,
            'kelas_data' => $hasilPerKelas
        ];
    }

    /**
     * Mengambil daftar tanggal aktif TPQ pada bulan tertentu (Y-m) yang belum diabsen oleh masing-masing kelas (Konsep Opsi B)
     */
    public function getTanggalBelumAbsenPerKelasBulan($IdTpq, $bulan, $IdTahunAjaran = null)
    {
        $tanggalRef = $bulan . '-01';
        return $this->getTanggalBelumAbsenPerKelasPeriode($IdTpq, 'bulan', $tanggalRef, $IdTahunAjaran);
    }

    /**
     * Otomatis sinkronisasi status 'Pra-Masuk' untuk santri baru atau eksisting dalam suatu kelas dan tahun ajaran.
     * Mencegah absensi kelas berubah menjadi 'belum lengkap' dan mengamankan persentase statistik kehadiran.
     */
    public function syncPraMasukSantriKelas($IdTpq, $IdKelas = null, $IdTahunAjaran = null)
    {
        if (empty($IdTahunAjaran)) {
            $helpFunction = new \App\Models\HelpFunctionModel();
            $IdTahunAjaran = $helpFunction->getTahunAjaranSaatIni();
        }

        // 1. Ambil daftar tanggal absensi yang sudah terjadi di TPQ & Kelas pada Tahun Ajaran ini
        $builderTgl = $this->db->table('tbl_absensi_santri')
            ->select('IdKelas, Tanggal, IdGuru, IdTahunAjaran, IdTpq')
            ->where('IdKelas !=', 10);

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderTgl->where('IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderTgl->whereIn('IdKelas', $IdKelas);
            } else {
                $builderTgl->where('IdKelas', $IdKelas);
            }
        }
        if (!empty($IdTahunAjaran)) {
            $builderTgl->where('IdTahunAjaran', $IdTahunAjaran);
        }

        $allTglKelas = $builderTgl->groupBy('IdKelas, Tanggal')->orderBy('Tanggal', 'ASC')->get()->getResultArray();
        if (empty($allTglKelas)) {
            return 0;
        }

        // Group tanggal per kelas
        $tglPerKelas = [];
        $sampleMetaPerKelasTgl = [];
        foreach ($allTglKelas as $r) {
            $kId = (int)$r['IdKelas'];
            $tgl = $r['Tanggal'];
            $tglPerKelas[$kId][] = $tgl;
            $sampleMetaPerKelasTgl[$kId . '_' . $tgl] = [
                'IdGuru' => $r['IdGuru'] ?? null,
                'IdTpq' => $r['IdTpq'] ?? $IdTpq,
                'IdTahunAjaran' => $r['IdTahunAjaran'] ?? $IdTahunAjaran,
            ];
        }

        // 2. Ambil semua santri aktif di kelas terkait pada tahun ajaran ini beserta tanggal registrasi kelasnya
        $builderSantri = $this->db->table('tbl_kelas_santri ks')
            ->select('ks.IdSantri, ks.IdKelas, ks.IdTpq, ks.IdTahunAjaran, ks.created_at as tgl_registrasi_kelas, s.created_at as tgl_daftar_santri')
            ->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri', 'inner')
            ->where('ks.Status', 1)
            ->where('s.Active', 1)
            ->where('ks.IdKelas !=', 10);

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builderSantri->where('ks.IdTpq', $IdTpq);
        }
        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builderSantri->whereIn('ks.IdKelas', $IdKelas);
            } else {
                $builderSantri->where('ks.IdKelas', $IdKelas);
            }
        }
        if (!empty($IdTahunAjaran)) {
            $builderSantri->where('ks.IdTahunAjaran', $IdTahunAjaran);
        }

        $santriList = $builderSantri->get()->getResultArray();
        if (empty($santriList)) {
            return 0;
        }

        // 3. Ambil absensi pertama nyata dan semua record absensi yang sudah ada untuk santri-santri ini
        $santriIds = array_column($santriList, 'IdSantri');
        $existingAbsensiRows = $this->db->table('tbl_absensi_santri')
            ->select('IdSantri, Tanggal, Kehadiran')
            ->whereIn('IdSantri', $santriIds)
            ->where('IdTahunAjaran', $IdTahunAjaran)
            ->get()->getResultArray();

        $existingAbsenMap = [];
        $minRealTanggalMap = [];
        foreach ($existingAbsensiRows as $ea) {
            $sId = $ea['IdSantri'];
            $tgl = $ea['Tanggal'];
            $existingAbsenMap[$sId . '_' . $tgl] = $ea['Kehadiran'];

            // Catat tanggal absensi riil pertama santri (bukan Pra-Masuk)
            if ($ea['Kehadiran'] !== 'Pra-Masuk') {
                if (!isset($minRealTanggalMap[$sId]) || $tgl < $minRealTanggalMap[$sId]) {
                    $minRealTanggalMap[$sId] = $tgl;
                }
            }
        }

        // 4. Identifikasi tanggal sebelum santri masuk dan siapkan batch insert 'Pra-Masuk'
        $batchInsert = [];
        foreach ($santriList as $s) {
            $sId = $s['IdSantri'];
            $kId = (int)$s['IdKelas'];
            
            // Tanggal registrasi ke kelas oleh Operator/Admin (Prioritas Utama)
            $tglRegKelas = !empty($s['tgl_registrasi_kelas']) ? substr($s['tgl_registrasi_kelas'], 0, 10) : null;
            // Tanggal santri pertama kali didaftarkan di sistem
            $tglDaftar = !empty($s['tgl_daftar_santri']) ? substr($s['tgl_daftar_santri'], 0, 10) : null;

            // Titik awal santri aktif:
            // 1. Jika sudah pernah diabsen riil oleh guru, gunakan tanggal absensi riil pertama
            // 2. Jika belum pernah diabsen, gunakan tanggal diregistrasi ke kelas oleh Operator/Admin
            // 3. Fallback ke tanggal pendaftaran awal santri atau hari ini
            $tglMulai = $minRealTanggalMap[$sId] ?? ($tglRegKelas ?? ($tglDaftar ?? date('Y-m-d')));

            $daftarTglKelas = $tglPerKelas[$kId] ?? [];
            foreach ($daftarTglKelas as $tglK) {
                // Hanya suntikkan Pra-Masuk jika tanggal absensi kelas lebih awal dari tgl mulai santri
                if ($tglK < $tglMulai) {
                    $keyCheck = $sId . '_' . $tglK;
                    if (!isset($existingAbsenMap[$keyCheck])) {
                        $metaKey = $kId . '_' . $tglK;
                        $meta = $sampleMetaPerKelasTgl[$metaKey] ?? [];

                        $batchInsert[] = [
                            'IdSantri' => $sId,
                            'Tanggal' => $tglK,
                            'Kehadiran' => 'Pra-Masuk',
                            'Keterangan' => 'Pra-Masuk (Belum Terdaftar)',
                            'IdKelas' => $kId,
                            'IdTahunAjaran' => $s['IdTahunAjaran'] ?? ($meta['IdTahunAjaran'] ?? $IdTahunAjaran),
                            'IdGuru' => $meta['IdGuru'] ?? null,
                            'IdTpq' => $s['IdTpq'] ?? ($meta['IdTpq'] ?? $IdTpq),
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        // Tandai agar tidak double
                        $existingAbsenMap[$keyCheck] = 'Pra-Masuk';
                    }
                }
            }
        }

        if (!empty($batchInsert)) {
            $chunks = array_chunk($batchInsert, 200);
            foreach ($chunks as $chunk) {
                $this->db->table('tbl_absensi_santri')->insertBatch($chunk);
            }
            return count($batchInsert);
        }

        return 0;
    }
}
