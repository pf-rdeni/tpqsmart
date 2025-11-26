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

}
