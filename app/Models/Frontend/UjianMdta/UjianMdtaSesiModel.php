<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaSesiModel extends Model
{
    protected $table      = 'tbl_ujian_mdta_sesi';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'IdJadwal',
        'IdSantri',
        'IdTpq',
        'AttemptKe',
        'TokenSesi',
        'WaktuMulai',
        'WaktuSelesai',
        'TambahanWaktuDetik',
        'NilaiAkhir',
        'StatusSesi',
        'IsRemedial',
        'IsRemedialAllowed',
        'TipePengerjaan',
        'FormatCetak',
        'FotoJawaban',
        'CatatanVerifikasi',
        'WaktuVerifikasi',
        'DiverifikasiOleh',
    ];

    /**
     * Buat sesi ujian baru untuk santri pada jadwal tertentu.
     * Generate token unik untuk akses URL lembar ujian.
     *
     * @param int    $idJadwal
     * @param string $idSantri
     * @param string $idTpq
     * @param int    $attemptKe
     * @return array ['id' => int, 'token' => string] atau false
     */
    public function buatSesi(int $idJadwal, string $idSantri, string $idTpq, int $attemptKe = 1)
    {
        // Cek apakah sudah ada sesi aktif (status 'sedang') untuk santri + jadwal ini
        $sesiAktif = $this->getSesiAktif($idSantri, $idJadwal);
        if ($sesiAktif) {
            return ['id' => $sesiAktif['id'], 'token' => $sesiAktif['TokenSesi']];
        }

        // Generate token unik
        $token = bin2hex(random_bytes(32)); // 64 karakter hex

        $this->insert([
            'IdJadwal'   => $idJadwal,
            'IdSantri'   => $idSantri,
            'IdTpq'      => $idTpq,
            'AttemptKe'  => $attemptKe,
            'IsRemedial' => ($attemptKe > 1 ? 1 : 0),
            'TokenSesi'  => $token,
            'WaktuMulai' => date('Y-m-d H:i:s'),
            'StatusSesi' => 'sedang',
        ]);

        $id = $this->getInsertID();
        return $id ? ['id' => $id, 'token' => $token] : false;
    }

    /**
     * Ambil sesi yang sedang aktif (status 'sedang') untuk santri + jadwal tertentu.
     *
     * @param string $idSantri
     * @param int    $idJadwal
     * @return array|null
     */
    public function getSesiAktif(string $idSantri, int $idJadwal): ?array
    {
        return $this->where('IdSantri', $idSantri)
                    ->where('IdJadwal', $idJadwal)
                    ->whereIn('StatusSesi', ['sedang', 'pause'])
                    ->first();
    }

    /**
     * Ambil sesi berdasarkan token (untuk lembar ujian santri).
     *
     * @param string $token
     * @return array|null
     */
    public function getSesiByToken(string $token): ?array
    {
        return $this->where('TokenSesi', $token)->first();
    }

    /**
     * Hitung nilai akhir santri berdasarkan jawaban yang tersimpan.
     * Rumus: (jumlah benar / JumlahSoal) × SkalaNilai
     * Update NilaiAkhir dan StatusSesi → 'selesai'.
     *
     * @param int $idSesi
     * @return float Nilai akhir
     */
    public function hitungNilai(int $idSesi): float
    {
        $sesi = $this->find($idSesi);
        if (!$sesi) {
            return 0;
        }

        // Ambil jadwal untuk mendapatkan JumlahSoal dan SkalaNilai (via Paket)
        $jadwalModel = new UjianMdtaJadwalModel();
        $jadwal      = $jadwalModel->find($sesi['IdJadwal']);
        if (!$jadwal) {
            return 0;
        }

        $paketModel = new UjianMdtaPaketModel();
        $paket      = $paketModel->find($jadwal['IdPaket']);
        $skalaNilai = $paket ? (int)$paket['SkalaNilai'] : 100;

        // Hitung total poin dari PG dan nilai koreksi Esai
        $jawabanModel = new UjianMdtaJawabanModel();
        $jawabanList  = $jawabanModel->getJawabanBySesi($idSesi);
        $jumlahSoal   = (int)($jadwal['JumlahSoal'] ?? 0);
        if ($jumlahSoal <= 0) {
            $jumlahSoal = count($jawabanList);
        }

        $totalPoin = 0;
        foreach ($jawabanList as $jwb) {
            if ($jwb['NilaiEsai'] !== null && $jwb['NilaiEsai'] !== '') {
                $totalPoin += min(1.0, max(0.0, (float)$jwb['NilaiEsai'] / 100.0));
            } else if ($jwb['IsBenar'] == 1) {
                $totalPoin += 1;
            }
        }

        $nilai = $jumlahSoal > 0
            ? round(($totalPoin / $jumlahSoal) * $skalaNilai, 2)
            : 0;

        // Update sesi
        $this->update($idSesi, [
            'NilaiAkhir'   => $nilai,
            'WaktuSelesai' => !empty($sesi['WaktuSelesai']) ? $sesi['WaktuSelesai'] : date('Y-m-d H:i:s'),
            'StatusSesi'   => in_array($sesi['StatusSesi'], ['sedang', 'pause']) ? 'selesai' : $sesi['StatusSesi'],
        ]);

        return $nilai;
    }

    /**
     * Tandai sesi sebagai timeout dan hitung nilai dari jawaban yang sudah tersimpan.
     *
     * @param int $idSesi
     * @return float Nilai akhir
     */
    public function timeout(int $idSesi): float
    {
        $nilai = $this->hitungNilai($idSesi);
        $this->update($idSesi, ['StatusSesi' => 'timeout']);
        return $nilai;
    }

    /**
     * Ambil daftar santri yang perlu mengikuti remedial pada jadwal tertentu.
     * (nilai < NilaiMinimum dan BolehRemedial = 1)
     *
     * @param int $idJadwal
     * @return array
     */
    public function getSantriPerluRemedial(int $idJadwal): array
    {
        $jadwalModel = new UjianMdtaJadwalModel();
        $jadwal      = $jadwalModel->find($idJadwal);
        if (!$jadwal || !$jadwal['BolehRemedial']) {
            return [];
        }

        $builder = $this->db->table($this->table . ' sesi');
        $builder->select('sesi.IdSantri, MAX(sesi.NilaiAkhir) as NilaiTerbaik, 
                          MAX(sesi.AttemptKe) as AttemptKe, sb.NamaSantri');
        $builder->join('tbl_santri_baru sb', 'sb.IdSantri = sesi.IdSantri', 'left');
        $builder->where('sesi.IdJadwal', $idJadwal);
        $builder->where('sesi.StatusSesi IN ("selesai", "timeout")');
        $builder->groupBy('sesi.IdSantri, sb.NamaSantri');
        $builder->having('NilaiTerbaik <', $jadwal['NilaiMinimum']);
        $builder->having('AttemptKe <', $jadwal['MaksRemedial'] + 1);
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil sisa waktu sesi dalam detik (termasuk tambahan waktu).
     * Return 0 jika pause atau selesai.
     *
     * @param string $token
     * @return int Sisa detik
     */
    public function getSisaWaktu(string $token): int
    {
        $sesi = $this->getSesiByToken($token);
        if (!$sesi || !in_array($sesi['StatusSesi'], ['sedang', 'pause'])) {
            return 0;
        }

        $jadwalModel   = new UjianMdtaJadwalModel();
        $jadwal        = $jadwalModel->find($sesi['IdJadwal']);
        $durasiDetik   = ((int)$jadwal['DurasiMenit'] * 60) + (int)($sesi['TambahanWaktuDetik'] ?? 0);
        $waktuMulai    = !empty($sesi['WaktuMulai']) ? strtotime($sesi['WaktuMulai']) : time();
        $waktuBerakhir = $waktuMulai + $durasiDetik;

        // Jika status sedang di-PAUSE oleh proktor, bekukan (freeze) sisa waktu pada saat pause dimulai
        if ($sesi['StatusSesi'] === 'pause') {
            $pauseTime = !empty($sesi['updated_at']) ? strtotime($sesi['updated_at']) : time();
            $elapsedAtPause = max(0, $pauseTime - $waktuMulai);
            return max(60, $durasiDetik - $elapsedAtPause);
        }

        return max(0, $waktuBerakhir - time());
    }

    public function getSesiByJadwal(int $idJadwal, ?int $attemptKe = null, ?string $idTpqFilter = null): array
    {
        return $this->getAllSantriMonitorByJadwal($idJadwal, $attemptKe, $idTpqFilter);
    }

    /**
     * Ambil SELURUH santri terdaftar di kelas jadwal ini (baik yang sudah mulai maupun yang belum mulai),
     * lengkap dengan status sesi live, sisa waktu, dan progress jawaban untuk attempt tertentu.
     *
     * @param int $idJadwal
     * @param int|null $attemptKe
     * @param string|null $idTpqFilter
     * @return array
     */
    public function ensureColumnsExist()
    {
        try {
            $fields = $this->db->getFieldNames($this->table);
            if (!in_array('TipePengerjaan', $fields)) {
                $this->db->query("ALTER TABLE tbl_ujian_mdta_sesi ADD COLUMN `TipePengerjaan` ENUM('online', 'manual') DEFAULT 'online' AFTER `StatusSesi`");
            }
            if (!in_array('FormatCetak', $fields)) {
                $this->db->query("ALTER TABLE tbl_ujian_mdta_sesi ADD COLUMN `FormatCetak` VARCHAR(50) NULL AFTER `TipePengerjaan`");
            }
            if (!in_array('FotoJawaban', $fields)) {
                $this->db->query("ALTER TABLE tbl_ujian_mdta_sesi ADD COLUMN `FotoJawaban` VARCHAR(255) NULL AFTER `FormatCetak`");
            }
            if (!in_array('HasilOmrRaw', $fields)) {
                $this->db->query("ALTER TABLE tbl_ujian_mdta_sesi ADD COLUMN `HasilOmrRaw` TEXT NULL AFTER `FotoJawaban`");
            }
            if (!in_array('CatatanVerifikasi', $fields)) {
                $this->db->query("ALTER TABLE tbl_ujian_mdta_sesi ADD COLUMN `CatatanVerifikasi` TEXT NULL AFTER `HasilOmrRaw`");
            }
            if (!in_array('WaktuVerifikasi', $fields)) {
                $this->db->query("ALTER TABLE tbl_ujian_mdta_sesi ADD COLUMN `WaktuVerifikasi` DATETIME NULL AFTER `CatatanVerifikasi`");
            }
            if (!in_array('DiverifikasiOleh', $fields)) {
                $this->db->query("ALTER TABLE tbl_ujian_mdta_sesi ADD COLUMN `DiverifikasiOleh` VARCHAR(100) NULL AFTER `WaktuVerifikasi`");
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error ensuring manual columns in model: ' . $e->getMessage());
        }
    }

    public function getAllSantriMonitorByJadwal(int $idJadwal, ?int $attemptKe = null, ?string $idTpqFilter = null): array
    {
        $this->ensureColumnsExist();

        $jadwalModel = new UjianMdtaJadwalModel();
        $jadwal      = $jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return [];
        }

        $attemptFilterSql = "";
        if ($attemptKe !== null && $attemptKe > 0) {
            $attemptFilterSql = " AND s2.AttemptKe = {$attemptKe}";
        }

        $joinCondition = "sesi.IdSantri = ks.IdSantri AND sesi.IdJadwal = {$idJadwal}";
        if ($attemptKe !== null && $attemptKe > 0) {
            $joinCondition .= " AND sesi.AttemptKe = {$attemptKe}";
        }
        $joinCondition .= " AND sesi.id = (SELECT MAX(s2.id) FROM tbl_ujian_mdta_sesi s2 WHERE s2.IdSantri = ks.IdSantri AND s2.IdJadwal = {$idJadwal}{$attemptFilterSql})";

        $builder = $this->db->table('tbl_kelas_santri ks');
        $builder->select('ks.IdSantri, ks.IdTpq, sb.NamaSantri, sb.NISN, k.NamaKelas, t.NamaTpq,
                          sesi.id as idSesi, sesi.AttemptKe, sesi.IsRemedial, sesi.IsRemedialAllowed, sesi.WaktuMulai, sesi.WaktuSelesai,
                          sesi.NilaiAkhir, sesi.StatusSesi, sesi.TambahanWaktuDetik, sesi.TipePengerjaan, sesi.FormatCetak, sesi.FotoJawaban,
                          (SELECT COUNT(*) FROM tbl_ujian_mdta_jawaban j WHERE j.IdSesi = sesi.id AND (j.IdPilihan IS NOT NULL OR j.JawabanEsai IS NOT NULL)) as TotalDijawab');
        $builder->join('tbl_santri_baru sb', 'sb.IdSantri = ks.IdSantri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = ks.IdTpq', 'left');
        $builder->join('tbl_ujian_mdta_sesi sesi', $joinCondition, 'left');
        $builder->where('ks.IdKelas', $jadwal['IdKelas']);
        
        if (!empty($idTpqFilter) && $idTpqFilter !== '0') {
            $builder->where('ks.IdTpq', $idTpqFilter);
        } else if ((string)$jadwal['IdTpq'] === '0') {
            $targetTpqVal = $jadwal['TargetTpq'] ?? 'all';
            if ($targetTpqVal !== 'all' && !empty($targetTpqVal)) {
                $targetTpqArr = array_map('trim', explode(',', $targetTpqVal));
                $builder->whereIn('ks.IdTpq', $targetTpqArr);
            } else {
                // targetTpqVal == 'all' -> Filter ke TPQ yang memiliki setting MDA_S1_ApakahMemilikiLembagaMDATA
                $toolsRows = $this->db->table('tbl_tools')
                    ->where('SettingKey', 'MDA_S1_ApakahMemilikiLembagaMDATA')
                    ->whereIn('LOWER(SettingValue)', ['1', 'true', 'yes', 'on', 'enabled', 'active'])
                    ->get()->getResultArray();
                $mdaTpqIds = array_filter(array_column($toolsRows, 'IdTpq'), function ($val) {
                    return !empty($val) && $val !== 'default';
                });
                if (!empty($mdaTpqIds)) {
                    $builder->whereIn('ks.IdTpq', $mdaTpqIds);
                }
            }
        } else {
            $builder->where('ks.IdTpq', $jadwal['IdTpq']);
        }



        $builder->where('ks.IdTahunAjaran', $jadwal['IdTahunAjaran']);
        $builder->where('ks.Status', 1);
        $builder->orderBy('t.NamaTpq', 'ASC');
        $builder->orderBy('sb.NamaSantri', 'ASC');



        $rows = $builder->get()->getResultArray();
        $durasiDetik = (int)$jadwal['DurasiMenit'] * 60;
        $nowTime     = time();

        foreach ($rows as &$r) {
            $r['StatusSesi'] = $r['StatusSesi'] ?? 'belum';
            $r['AttemptKe']  = $attemptKe ?? $r['AttemptKe'] ?? 1;

            if (in_array($r['StatusSesi'], ['sedang', 'pause']) && !empty($r['WaktuMulai'])) {
                $waktuMulai    = strtotime($r['WaktuMulai']);
                $totalDurasi   = $durasiDetik + (int)($r['TambahanWaktuDetik'] ?? 0);
                $r['SisaDetik'] = max(0, ($waktuMulai + $totalDurasi) - $nowTime);
            } else {
                $r['SisaDetik'] = 0;
            }
        }

        return $rows;
    }
}
