<?php

namespace App\Models;

use CodeIgniter\Model;

class PresentasiHasilModel extends Model
{
    protected $table      = 'tbl_presentasi_config';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'IdTpq', 'NamaKonfigurasi',
        'IdTahunAjaran', 'TypeUjian', 'ThresholdKKM', 'ThresholdAman',
        'NamaAcara', 'SubtitleCover', 'JudulCover', 'TaglineCover',
        'FooterKiri', 'FooterKanan', 'HeroImagePath',
        'TemaWarna', 'WarnaAksen', 'BackgroundType', 'BackgroundValue',
        'ConfigSlide2', 'ConfigSlide3', 'ConfigSlide5', 'ConfigSlide6',
        'ConfigSlide7', 'ConfigSlide8', 'ConfigSlide9', 'ConfigSlide10',
        'ConfigSlide11', 'ConfigSlide12',
        'SlideAktif', 'AnimasiTransisi',
    ];

    // ====================================================================
    // KONFIGURASI
    // ====================================================================

    /**
     * Ambil konfigurasi aktif untuk TPQ tertentu
     */
    public function getKonfigurasi($idTpq)
    {
        $row = $this->where('IdTpq', $idTpq)->orderBy('updated_at', 'DESC')->first();

        if (!$row) {
            return $this->getDefaultKonfigurasi();
        }

        // Decode JSON fields
        $jsonFields = ['ConfigSlide2','ConfigSlide3','ConfigSlide5','ConfigSlide6',
                       'ConfigSlide7','ConfigSlide8','ConfigSlide9','ConfigSlide10',
                       'ConfigSlide11','ConfigSlide12','SlideAktif'];

        foreach ($jsonFields as $field) {
            if (!empty($row[$field]) && is_string($row[$field])) {
                $row[$field] = json_decode($row[$field], true);
            }
        }

        // Merge dengan default untuk field yang kosong
        $default = $this->getDefaultKonfigurasi();
        foreach ($default as $key => $val) {
            if (!isset($row[$key]) || $row[$key] === null || $row[$key] === '') {
                $row[$key] = $val;
            }
        }

        return $row;
    }

    /**
     * Simpan/update konfigurasi
     */
    public function simpanKonfigurasi($idTpq, array $data)
    {
        // Encode JSON fields
        $jsonFields = ['ConfigSlide2','ConfigSlide3','ConfigSlide5','ConfigSlide6',
                       'ConfigSlide7','ConfigSlide8','ConfigSlide9','ConfigSlide10',
                       'ConfigSlide11','ConfigSlide12','SlideAktif'];

        foreach ($jsonFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = json_encode($data[$field], JSON_UNESCAPED_UNICODE);
            }
        }

        $existing = $this->where('IdTpq', $idTpq)->first();
        if ($existing) {
            $this->update($existing['id'], $data);
            return $existing['id'];
        } else {
            $data['IdTpq'] = $idTpq;
            return $this->insert($data);
        }
    }

    /**
     * Nilai default konfigurasi
     */
    public function getDefaultKonfigurasi(): array
    {
        return [
            'ThresholdKKM'     => 70,
            'ThresholdAman'    => 80,
            'TypeUjian'        => 'pra-munaqosah',
            'NamaAcara'        => 'Pertemuan Orang Tua & Santri',
            'SubtitleCover'    => 'Evaluasi Pra-Munaqosah Santri',
            'JudulCover'       => 'Kesiapan Menuju Munaqosah',
            'TaglineCover'     => 'Satu Tekad: 100% Lulus Munaqosah, Berangkat Wisuda Bersama',
            'FooterKiri'       => 'Pertemuan Orang Tua & Santri',
            'FooterKanan'      => 'Fokus Peningkatan & Perbaikan Materi',
            'TemaWarna'        => 'dark',
            'WarnaAksen'       => '#0284c7',
            'BackgroundType'   => 'gradient',
            'AnimasiTransisi'  => 'fade',
            'SlideAktif'       => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'ConfigSlide2'     => $this->defaultSlide2(),
            'ConfigSlide3'     => [],
            'ConfigSlide5'     => ['top_n' => 3],
            'ConfigSlide6'     => ['bottom_n' => 3],
            'ConfigSlide7'     => $this->defaultSlide7(),
            'ConfigSlide8'     => $this->defaultSlide8(),
            'ConfigSlide9'     => $this->defaultSlide9(),
            'ConfigSlide10'    => $this->defaultSlide10(),
            'ConfigSlide11'    => ['threshold_klinik' => 3, 'threshold_penguatan' => 2],
            'ConfigSlide12'    => $this->defaultSlide12(),
        ];
    }

    private function defaultSlide2(): array
    {
        return [
            'label'  => 'TUJUAN EVALUASI',
            'judul'  => 'Mengapa Ada Pra-Munaqosah?',
            'kartu'  => [
                ['icon' => 'fas fa-search', 'judul' => 'Cek Titik Lemah', 'deskripsi' => 'Mengetahui materi mana yang masih perlu perhatian khusus sebelum ujian sesungguhnya.'],
                ['icon' => 'fas fa-brain', 'judul' => 'Simulasi Mental', 'deskripsi' => 'Melatih santri menghadapi suasana dan tekanan ujian yang sebenarnya.'],
                ['icon' => 'fas fa-graduation-cap', 'judul' => 'Garansi Wisuda', 'deskripsi' => 'Memastikan seluruh santri siap dan layak tampil di panggung munaqosah.'],
            ],
        ];
    }

    private function defaultSlide7(): array
    {
        return [
            'label'  => 'ANALISIS KENDALA',
            'judul'  => 'Apa Saja yang Perlu Dilatih Ulang?',
            'kartu'  => [
                ['icon' => 'fas fa-bolt', 'judul' => 'Kerap Terburu-buru', 'deskripsi' => 'Santri cenderung membaca terlalu cepat sehingga makhorijul huruf kurang sempurna.'],
                ['icon' => 'fas fa-link', 'judul' => 'Hafalan Sambung Ayat', 'deskripsi' => 'Masih banyak yang terhenti saat diminta menyambung ayat secara spontan.'],
                ['icon' => 'fas fa-pen', 'judul' => 'Bentuk Huruf Tengah', 'deskripsi' => 'Penulisan huruf di posisi tengah kata masih sering tidak sesuai kaidah imla.'],
            ],
        ];
    }

    private function defaultSlide8(): array
    {
        return [
            'label'  => 'STRATEGI PENDAMPINGAN',
            'judul'  => 'Rencana Bimbingan Menurut Kebutuhan',
            'kelompok' => [
                ['nama' => 'Kelompok Siap', 'warna' => 'green', 'deskripsi' => 'Santri dengan semua nilai di atas ambang aman. Fokus pada penyempurnaan.'],
                ['nama' => 'Kelompok Poles', 'warna' => 'orange', 'deskripsi' => 'Santri dengan 1–2 materi di zona rawan. Butuh latihan intensif.'],
                ['nama' => 'Klinik Khusus', 'warna' => 'red', 'deskripsi' => 'Santri dengan nilai di bawah KKM di beberapa materi. Perlu bimbingan privat.'],
            ],
        ];
    }

    private function defaultSlide9(): array
    {
        return [
            'label'  => 'STRATEGI PERBAIKAN',
            'judul'  => '4 Langkah Nyata Menuju Lulus 100%',
            'langkah' => [
                ['nomor' => '01', 'konteks' => 'TPQ', 'icon' => 'fas fa-moon', 'judul' => 'Bimbingan Ba\'da Maghrib', 'deskripsi' => 'Sesi intensif 30 menit fokus hafalan dan tajwid setiap ba\'da Maghrib.', 'tag' => 'Ustadz', 'tag_warna' => 'blue'],
                ['nomor' => '02', 'konteks' => 'TPQ', 'icon' => 'fas fa-sun', 'judul' => 'Didikan Subuh', 'deskripsi' => 'Hafalan dan imla dilakukan pada program Didikan Subuh setiap Ahad pagi.', 'tag' => 'Ustadzah', 'tag_warna' => 'green'],
                ['nomor' => '03', 'konteks' => 'RUMAH', 'icon' => 'fas fa-home', 'judul' => 'Muroja\'ah Harian', 'deskripsi' => 'Santri diminta muroja\'ah minimal 15 menit setiap hari di rumah.', 'tag' => 'Orang Tua', 'tag_warna' => 'yellow'],
                ['nomor' => '04', 'konteks' => 'WALI SANTRI', 'icon' => 'fas fa-hands-helping', 'judul' => 'Pendampingan Orang Tua', 'deskripsi' => 'Wali santri mendampingi dan memantau perkembangan belajar anak di rumah.', 'tag' => 'Wali Santri', 'tag_warna' => 'purple'],
            ],
        ];
    }

    private function defaultSlide10(): array
    {
        return [
            'label'  => 'PERAN PENTING WALI SANTRI',
            'judul'  => 'Sinergi Ayah Bunda Mengawal Kelulusan',
            'kartu'  => [
                ['icon' => 'fas fa-clock', 'warna_bg' => 'blue', 'judul' => 'Kawal Jam Tambahan', 'deskripsi' => 'Pastikan santri hadir dalam setiap sesi bimbingan tambahan yang dijadwalkan.'],
                ['icon' => 'fas fa-pencil-alt', 'warna_bg' => 'yellow', 'judul' => 'Cek Menulis Al-Fatihah', 'deskripsi' => 'Minta santri menulis Al-Fatihah setiap hari dan periksa kerapian tulisannya.'],
                ['icon' => 'fas fa-heart', 'warna_bg' => 'green', 'judul' => 'Semangat & Doa Terbaik', 'deskripsi' => 'Berikan motivasi positif dan doa terbaik untuk keberhasilan ananda.'],
            ],
        ];
    }

    private function defaultSlide12(): array
    {
        return [
            'label'      => 'KOMITMEN BERSAMA KELULUSAN 100%',
            'judul'      => 'Bersama Mengantarkan Seluruh Santri Melenggang ke Panggung Wisuda',
            'subjudul'   => 'Dukungan penuh orang tua dan bimbingan terpadu para asatidz adalah kunci kesuksesan ananda di Munaqosah.',
            'badge_kiri' => ['icon' => '📅', 'teks' => 'Program Dimulai: Senin Pekan Depan'],
            'badge_kanan'=> ['icon' => '🎓', 'teks' => 'Target Bersama: 100% Lulus Munaqosah'],
        ];
    }

    // ====================================================================
    // DATA DARI DATABASE
    // ====================================================================

    /**
     * Slide 4: Sebaran nilai per materi
     * Returns per materi: jumlah santri di zona aman, cukup, kurang
     */
    public function getSebaranNilaiPerMateri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM = 70, $thresholdAman = 80)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                mp.NamaMateri,
                mp.IdMateri,
                COUNT(DISTINCT mn.NoPeserta) as total_santri,
                SUM(CASE WHEN mn.Nilai >= ? THEN 1 ELSE 0 END) as zona_aman,
                SUM(CASE WHEN mn.Nilai >= ? AND mn.Nilai < ? THEN 1 ELSE 0 END) as zona_cukup,
                SUM(CASE WHEN mn.Nilai < ? THEN 1 ELSE 0 END) as zona_kurang,
                ROUND(AVG(mn.Nilai), 1) as rata_rata
            FROM tbl_munaqosah_nilai mn
            LEFT JOIN tbl_materi_pelajaran mp ON mp.IdMateri = mn.IdMateri
            WHERE mn.IdTpq = ?
              AND mn.IdTahunAjaran = ?
              AND mn.TypeUjian = ?
            GROUP BY mp.IdMateri, mp.NamaMateri
            ORDER BY rata_rata DESC
        ";

        $result = $db->query($sql, [
            $thresholdAman,
            $thresholdKKM, $thresholdAman,
            $thresholdKKM,
            $idTpq, $idTahunAjaran, $typeUjian
        ])->getResultArray();

        // Hitung ringkasan
        $totalSantri = 0;
        $totalAman   = 0;
        if (!empty($result)) {
            $totalSantri = $result[0]['total_santri'] ?? 0;
            foreach ($result as &$row) {
                $row['zona_aman']   = (int)$row['zona_aman'];
                $row['zona_cukup']  = (int)$row['zona_cukup'];
                $row['zona_kurang'] = (int)$row['zona_kurang'];
                $row['total_santri']= (int)$row['total_santri'];
                $totalAman          = max($totalAman, $row['zona_aman']);
            }
        }

        return [
            'materi'      => $result,
            'totalSantri' => (int)$totalSantri,
            'totalAman'   => (int)$totalAman,
        ];
    }

    /**
     * Slide 5: Top-N materi terkuat (modal kelulusan)
     */
    public function getTopMateri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM = 70, $topN = 3)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                mp.NamaMateri,
                mp.IdMateri,
                COUNT(DISTINCT mn.NoPeserta) as total_santri,
                SUM(CASE WHEN mn.Nilai >= ? THEN 1 ELSE 0 END) as tuntas,
                ROUND(AVG(mn.Nilai), 1) as rata_rata,
                ROUND((SUM(CASE WHEN mn.Nilai >= ? THEN 1 ELSE 0 END) / COUNT(DISTINCT mn.NoPeserta)) * 100, 0) as persen_tuntas
            FROM tbl_munaqosah_nilai mn
            LEFT JOIN tbl_materi_pelajaran mp ON mp.IdMateri = mn.IdMateri
            WHERE mn.IdTpq = ?
              AND mn.IdTahunAjaran = ?
              AND mn.TypeUjian = ?
            GROUP BY mp.IdMateri, mp.NamaMateri
            ORDER BY persen_tuntas DESC, rata_rata DESC
            LIMIT ?
        ";

        return $db->query($sql, [$thresholdKKM, $thresholdKKM, $idTpq, $idTahunAjaran, $typeUjian, $topN])->getResultArray();
    }

    /**
     * Slide 6: Bottom-N materi terlemah (fokus perbaikan)
     */
    public function getBottomMateri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM = 70, $bottomN = 3)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT
                mp.NamaMateri,
                mp.IdMateri,
                COUNT(DISTINCT mn.NoPeserta) as total_santri,
                SUM(CASE WHEN mn.Nilai < ? THEN 1 ELSE 0 END) as perlu_perbaikan,
                ROUND(AVG(mn.Nilai), 1) as rata_rata,
                ROUND((SUM(CASE WHEN mn.Nilai < ? THEN 1 ELSE 0 END) / COUNT(DISTINCT mn.NoPeserta)) * 100, 0) as persen_kurang
            FROM tbl_munaqosah_nilai mn
            LEFT JOIN tbl_materi_pelajaran mp ON mp.IdMateri = mn.IdMateri
            WHERE mn.IdTpq = ?
              AND mn.IdTahunAjaran = ?
              AND mn.TypeUjian = ?
            GROUP BY mp.IdMateri, mp.NamaMateri
            ORDER BY persen_kurang DESC, rata_rata ASC
            LIMIT ?
        ";

        return $db->query($sql, [$thresholdKKM, $thresholdKKM, $idTpq, $idTahunAjaran, $typeUjian, $bottomN])->getResultArray();
    }

    /**
     * Slide 8 & 11: Pengelompokan santri berdasarkan jumlah materi di bawah threshold
     * Returns: ['siap'=>[], 'poles'=>[], 'klinik'=>[]]
     */
    public function getKelompokSantri($idTpq, $idTahunAjaran, $typeUjian, $thresholdKKM = 70, $thresholdAman = 80, $thresholdKlinik = 3, $thresholdPenguatan = 2)
    {
        $db = \Config\Database::connect();

        // Hitung per santri: berapa materi di bawah KKM, berapa di zona rawan (KKM <= nilai < aman)
        $sql = "
            SELECT
                mn.NoPeserta,
                s.NamaSantri,
                SUM(CASE WHEN mn.Nilai < ? THEN 1 ELSE 0 END) as jumlah_kurang,
                SUM(CASE WHEN mn.Nilai >= ? AND mn.Nilai < ? THEN 1 ELSE 0 END) as jumlah_rawan,
                GROUP_CONCAT(DISTINCT CASE WHEN mn.Nilai < ? THEN mp.NamaMateri ELSE NULL END ORDER BY mp.NamaMateri SEPARATOR ', ') as materi_kurang,
                ROUND(AVG(mn.Nilai), 1) as rata_rata
            FROM tbl_munaqosah_nilai mn
            LEFT JOIN tbl_santri_baru s ON s.IdSantri = mn.IdSantri
            LEFT JOIN tbl_materi_pelajaran mp ON mp.IdMateri = mn.IdMateri
            WHERE mn.IdTpq = ?
              AND mn.IdTahunAjaran = ?
              AND mn.TypeUjian = ?
            GROUP BY mn.NoPeserta, s.NamaSantri
            ORDER BY jumlah_kurang DESC, rata_rata ASC
        ";

        $rows = $db->query($sql, [
            $thresholdKKM,
            $thresholdKKM, $thresholdAman,
            $thresholdKKM,
            $idTpq, $idTahunAjaran, $typeUjian
        ])->getResultArray();

        $kelompok = [
            'klinik'    => [], // >= thresholdKlinik materi di bawah KKM
            'penguatan' => [], // thresholdPenguatan <= jumlah_kurang < thresholdKlinik (ATAU zona rawan)
            'poles'     => [], // 1 materi kurang atau hanya zona rawan
            'siap'      => [], // semua OK
        ];

        foreach ($rows as $row) {
            $jumlahKurang = (int)$row['jumlah_kurang'];
            $jumlahRawan  = (int)$row['jumlah_rawan'];

            if ($jumlahKurang >= $thresholdKlinik) {
                $kelompok['klinik'][]    = $row;
            } elseif ($jumlahKurang >= $thresholdPenguatan) {
                $kelompok['penguatan'][] = $row;
            } elseif ($jumlahKurang >= 1 || $jumlahRawan >= 2) {
                $kelompok['poles'][]     = $row;
            } else {
                $kelompok['siap'][]      = $row;
            }
        }

        return $kelompok;
    }

    /**
     * Info TPQ
     */
    public function getInfoTpq($idTpq)
    {
        $db = \Config\Database::connect();
        return $db->table('tbl_tpq')->where('IdTpq', $idTpq)->get()->getRowArray();
    }

    /**
     * Daftar tahun ajaran yang ada nilainya untuk TPQ ini
     */
    public function getTahunAjaranAvailable($idTpq)
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT DISTINCT IdTahunAjaran
            FROM tbl_munaqosah_nilai
            WHERE IdTpq = ?
            ORDER BY IdTahunAjaran DESC
        ", [$idTpq])->getResultArray();
    }

    /**
     * Hitung total santri unik dalam data nilai
     */
    public function getTotalSantri($idTpq, $idTahunAjaran, $typeUjian): int
    {
        $db = \Config\Database::connect();
        $row = $db->query("
            SELECT COUNT(DISTINCT NoPeserta) as total
            FROM tbl_munaqosah_nilai
            WHERE IdTpq = ? AND IdTahunAjaran = ? AND TypeUjian = ?
        ", [$idTpq, $idTahunAjaran, $typeUjian])->getRowArray();

        return $row ? (int)$row['total'] : 0;
    }
}
