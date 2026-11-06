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
                COUNT(*) as total_record,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN a.Kehadiran = 'Alpa' THEN 1 ELSE 0 END) as total_alpa
            ");

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
            $builderKpi->where('a.Kehadiran', $statusKehadiran);
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
                SUM(CASE WHEN a.Kehadiran = 'Alpa' THEN 1 ELSE 0 END) as alpa,
                COUNT(*) as total
            ");

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
            $builderTrend->where('a.Kehadiran', $statusKehadiran);
        }

        $trendHarian = $builderTrend->groupBy('a.Tanggal')->orderBy('a.Tanggal', 'ASC')->get()->getResultArray();

        // 3. Fluktuasi Kehadiran Per Kelas Time-Series (Tanggal & Kelas)
        $builderFluktuasi = $this->db->table('tbl_absensi_santri a')
            ->select("
                a.Tanggal,
                a.IdKelas,
                k.NamaKelas,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                COUNT(*) as total
            ")
            ->join('tbl_kelas k', 'a.IdKelas = k.IdKelas', 'left');

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
            $builderFluktuasi->where('a.Kehadiran', $statusKehadiran);
        }

        $fluktuasiRaw = $builderFluktuasi->groupBy('a.Tanggal, a.IdKelas')
            ->orderBy('a.Tanggal', 'ASC')
            ->orderBy('a.IdKelas', 'ASC')
            ->get()->getResultArray();

        // 4. Ranking dan Perbandingan Total per Kelas
        $builderRanking = $this->db->table('tbl_absensi_santri a')
            ->select("
                a.IdKelas,
                k.NamaKelas,
                COUNT(*) as total,
                COUNT(DISTINCT a.Tanggal) as jumlah_hari_efektif,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.Kehadiran = 'Alpa' THEN 1 ELSE 0 END) as alpa,
                ROUND((SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
            ")
            ->join('tbl_kelas k', 'a.IdKelas = k.IdKelas', 'left');

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
            $builderRanking->where('a.Kehadiran', $statusKehadiran);
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

        // 5. Matriks Presensi Per Santri
        $statusAbsenFilterSql = !empty($statusKehadiran) ? " AND a.Kehadiran = '{$statusKehadiran}'" : "";
        $builderSantri = $this->db->table('tbl_santri_baru s')
            ->select("
                s.IdSantri,
                s.NamaSantri,
                COALESCE(NULLIF(s.NoHpAyah, ''), NULLIF(s.NoHpIbu, ''), NULLIF(s.NoHpWali, ''), NULLIF(s.NoHpSantri, ''), '') as NoHpOrtu,
                s.Active,
                COALESCE(k.NamaKelas, 'Tanpa Kelas') as NamaKelas,
                t.NamaTpq,
                COUNT(a.Id) as total_absen,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN a.Kehadiran = 'Alpa' THEN 1 ELSE 0 END) as total_alpa
            ")
            ->join('tbl_absensi_santri a', 's.IdSantri = a.IdSantri' . 
                (!empty($startDate) ? " AND a.Tanggal >= '{$startDate}'" : "") .
                (!empty($endDate) ? " AND a.Tanggal <= '{$endDate}'" : "") .
                (!empty($IdTahunAjaran) ? " AND a.IdTahunAjaran = '{$IdTahunAjaran}'" : "") .
                $statusAbsenFilterSql, 
                'inner')
            ->join('tbl_kelas k', 'a.IdKelas = k.IdKelas', 'left')
            ->join('tbl_tpq t', 's.IdTpq = t.IdTpq', 'left')
            ->where('s.Active', 1);

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
            ->orderBy('k.IdKelas', 'ASC')
            ->orderBy('s.NamaSantri', 'ASC')
            ->get()->getResultArray();

        foreach ($santriRecap as &$item) {
            $totalAbsen = (int)$item['total_absen'];
            $hadir = (int)$item['total_hadir'];
            $item['persentase_hadir'] = $totalAbsen > 0 ? round(($hadir / $totalAbsen) * 100, 1) : 0;
        }

        return [
            'kpi' => $kpi,
            'trend_harian' => $trendHarian,
            'fluktuasi_kelas' => $fluktuasiRaw,
            'ranking_kelas' => $rankingKelas,
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
            ->where('a.IdSantri', $idSantri);

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

}
