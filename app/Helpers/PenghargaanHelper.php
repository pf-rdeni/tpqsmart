<?php

namespace App\Helpers;

use App\Models\Backend\Penghargaan\PenghargaanJenjangModel;
use Config\Database;

/**
 * Engine kalkulasi penghargaan santri berbasis data aktif tbl_kelas_santri.
 */
class PenghargaanHelper
{
    /**
     * Ambil daftar santri aktif pada kelas dan tahun ajaran tertentu.
     * Sumber kebenaran wajib dari tbl_kelas_santri (Status = 1).
     */
    public static function getSantriAktifPerKelas(string $idKelas, string $idTahunAjaran, string $idTpq): array
    {
        $db = Database::connect();

        return $db->table('tbl_kelas_santri ks')
            ->select('ks.IdSantri, ks.IdKelas, ks.IdTpq, ks.IdTahunAjaran, sb.NamaSantri, sb.NISN as NIS, k.NamaKelas')
            ->join('tbl_santri_baru sb', 'sb.IdSantri = ks.IdSantri')
            ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left')
            ->where('ks.IdTpq', $idTpq)
            ->where('ks.IdTahunAjaran', $idTahunAjaran)
            ->where('ks.IdKelas', $idKelas)
            ->where('ks.Status', 1)
            ->orderBy('sb.NamaSantri', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar santri aktif pada seluruh kelas dalam satu jenjang.
     */
    public static function getSantriAktifPerJenjang(string $namaJenjang, string $idTahunAjaran, string $idTpq): array
    {
        $jenjangModel = new PenghargaanJenjangModel();
        $kelasIds     = $jenjangModel->getKelasIdsByJenjang($idTpq, $namaJenjang);

        if (empty($kelasIds)) {
            return [];
        }

        $db = Database::connect();

        return $db->table('tbl_kelas_santri ks')
            ->select('ks.IdSantri, ks.IdKelas, ks.IdTpq, ks.IdTahunAjaran, sb.NamaSantri, sb.NISN as NIS, k.NamaKelas')
            ->join('tbl_santri_baru sb', 'sb.IdSantri = ks.IdSantri')
            ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left')
            ->where('ks.IdTpq', $idTpq)
            ->where('ks.IdTahunAjaran', $idTahunAjaran)
            ->whereIn('ks.IdKelas', $kelasIds)
            ->where('ks.Status', 1)
            ->orderBy('k.NamaKelas', 'ASC')
            ->orderBy('sb.NamaSantri', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil seluruh santri aktif se-TPQ pada tahun ajaran tertentu (jika tidak dikelompokkan jenjang).
     */
    public static function getSantriAktifSeTpq(string $idTahunAjaran, string $idTpq): array
    {
        $db = Database::connect();

        return $db->table('tbl_kelas_santri ks')
            ->select('ks.IdSantri, ks.IdKelas, ks.IdTpq, ks.IdTahunAjaran, sb.NamaSantri, sb.NISN as NIS, k.NamaKelas')
            ->join('tbl_santri_baru sb', 'sb.IdSantri = ks.IdSantri')
            ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left')
            ->where('ks.IdTpq', $idTpq)
            ->where('ks.IdTahunAjaran', $idTahunAjaran)
            ->where('ks.Status', 1)
            ->orderBy('k.NamaKelas', 'ASC')
            ->orderBy('sb.NamaSantri', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Hitung rata-rata nilai santri secara batch dari tbl_nilai.
     *
     * @return array<string, array{rata_rata: float, nilai_max: float, jumlah_mapel: int}>
     */
    public static function hitungStatistikNilaiSantri(
        array $santriIds,
        string $semester,
        string $idTahunAjaran,
        string $idTpq,
        ?string $kategoriMateriFilter = null
    ): array {
        if (empty($santriIds)) {
            return [];
        }

        $db      = Database::connect();
        $builder = $db->table('tbl_nilai n')
            ->select('n.IdSantri, AVG(n.Nilai) as rata_rata, MAX(n.Nilai) as nilai_max, COUNT(n.Id) as jumlah_mapel')
            ->whereIn('n.IdSantri', $santriIds)
            ->where('n.IdTpq', $idTpq)
            ->where('n.IdTahunAjaran', $idTahunAjaran);

        if ($semester !== '' && strtolower($semester) !== 'tahunan') {
            $builder->where('n.Semester', $semester);
        }

        if ($kategoriMateriFilter !== null && $kategoriMateriFilter !== '') {
            $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = n.IdMateri', 'inner')
                ->like('m.Kategori', $kategoriMateriFilter);
        }

        $rows = $builder->groupBy('n.IdSantri')->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[(string) $row['IdSantri']] = [
                'rata_rata'    => round((float) ($row['rata_rata'] ?? 0), 2),
                'nilai_max'    => round((float) ($row['nilai_max'] ?? 0), 2),
                'jumlah_mapel' => (int) ($row['jumlah_mapel'] ?? 0),
            ];
        }

        return $result;
    }

    /**
     * Hitung statistik kehadiran santri secara batch dari tbl_absensi_santri.
     *
     * @return array<string, array{total_pertemuan: int, hadir: int, izin: int, sakit: int, alfa: int, persen_hadir: float}>
     */
    public static function hitungStatistikAbsensiSantri(
        array $santriIds,
        string $semester,
        string $idTahunAjaran,
        string $idTpq
    ): array {
        if (empty($santriIds)) {
            return [];
        }

        [$startDate, $endDate] = self::getRentangTanggalSemester($idTahunAjaran, $semester);

        $db   = Database::connect();
        $rows = $db->table('tbl_absensi_santri a')
            ->select("
                a.IdSantri,
                COUNT(CASE WHEN a.Kehadiran != 'Pra-Masuk' THEN 1 ELSE NULL END) as total_pertemuan,
                SUM(CASE WHEN a.Kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.Kehadiran = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.Kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.Kehadiran = 'Alfa' THEN 1 ELSE 0 END) as alfa
            ")
            ->whereIn('a.IdSantri', $santriIds)
            ->where('a.IdTpq', $idTpq)
            ->where('a.IdTahunAjaran', $idTahunAjaran)
            ->where('a.Tanggal >=', $startDate)
            ->where('a.Tanggal <=', $endDate)
            ->groupBy('a.IdSantri')
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $total = (int) ($row['total_pertemuan'] ?? 0);
            $hadir = (int) ($row['hadir'] ?? 0);
            $persen = $total > 0 ? round(($hadir / $total) * 100, 2) : 0.0;

            $result[(string) $row['IdSantri']] = [
                'total_pertemuan' => $total,
                'hadir'           => $hadir,
                'izin'            => (int) ($row['izin'] ?? 0),
                'sakit'           => (int) ($row['sakit'] ?? 0),
                'alfa'            => (int) ($row['alfa'] ?? 0),
                'persen_hadir'    => $persen,
            ];
        }

        return $result;
    }

    /**
     * Hitung skor gabungan berbobot.
     */
    public static function hitungSkorGabungan(float $nilaiRata, float $persenHadir, int $bobotNilai, int $bobotHadir): float
    {
        return round((($nilaiRata * $bobotNilai) + ($persenHadir * $bobotHadir)) / 100, 2);
    }

    /**
     * Konversi IdTahunAjaran + Semester menjadi rentang tanggal [startDate, endDate].
     */
    public static function getRentangTanggalSemester(string $idTahunAjaran, string $semester): array
    {
        $tahunAwal  = (int) date('Y');
        $tahunAkhir = $tahunAwal + 1;

        if (strpos($idTahunAjaran, '/') !== false) {
            $parts      = explode('/', $idTahunAjaran);
            $tahunAwal  = (int) ($parts[0] ?? $tahunAwal);
            $tahunAkhir = (int) ($parts[1] ?? ($tahunAwal + 1));
        } elseif (strlen($idTahunAjaran) === 8 && ctype_digit($idTahunAjaran)) {
            $tahunAwal  = (int) substr($idTahunAjaran, 0, 4);
            $tahunAkhir = (int) substr($idTahunAjaran, 4, 4);
        }

        $sem = strtolower($semester);
        if ($sem === 'ganjil') {
            return [$tahunAwal . '-07-01', $tahunAwal . '-12-31'];
        }
        if ($sem === 'genap') {
            return [$tahunAkhir . '-01-01', $tahunAkhir . '-06-30'];
        }

        return [$tahunAwal . '-07-01', $tahunAkhir . '-06-30'];
    }

    /**
     * Engine kalkulasi kandidat penerima berdasarkan jenis penghargaan, kriteria, dan scope.
     */
    public static function kalkulasiKandidat(
        array $jenis,
        ?array $kriteria,
        array $setting,
        string $idTpq,
        string $idTahunAjaran,
        string $semester,
        ?string $idKelas = null,
        ?string $namaJenjang = null
    ): array {
        // Ambil daftar santri aktif berdasarkan scope (Kelas, Jenjang, atau Se-TPQ)
        if (!empty($idKelas)) {
            $santriList = self::getSantriAktifPerKelas($idKelas, $idTahunAjaran, $idTpq);
        } elseif (!empty($namaJenjang)) {
            $santriList = self::getSantriAktifPerJenjang($namaJenjang, $idTahunAjaran, $idTpq);
        } else {
            $santriList = self::getSantriAktifSeTpq($idTahunAjaran, $idTpq);
        }

        if (empty($santriList)) {
            return [];
        }

        $santriIds = array_column($santriList, 'IdSantri');
        $kode      = strtoupper((string) ($jenis['Kode'] ?? ''));

        $kategoriMateri = ($kode === 'AKD-05') ? 'Qur' : null;
        $nilaiStats     = self::hitungStatistikNilaiSantri($santriIds, $semester, $idTahunAjaran, $idTpq, $kategoriMateri);
        $absensiStats   = self::hitungStatistikAbsensiSantri($santriIds, $semester, $idTahunAjaran, $idTpq);

        // Parameter threshold (override kriteria jika diisi, fallback ke setting global)
        $bobotNilai     = (int) ($kriteria['BobotNilai'] ?? $setting['BobotNilaiDefault'] ?? 70);
        $bobotHadir     = (int) ($kriteria['BobotHadir'] ?? $setting['BobotHadirDefault'] ?? 30);
        $nilaiMin       = isset($kriteria['NilaiMin']) && $kriteria['NilaiMin'] !== null ? (float) $kriteria['NilaiMin'] : null;
        $persenHadirMin = isset($kriteria['PersenHadirMin']) && $kriteria['PersenHadirMin'] !== null
            ? (float) $kriteria['PersenHadirMin']
            : ($kode === 'HAD-01' ? (float) ($setting['ThresholdHadirSempurna'] ?? 100) : null);
        $jumlahPemenang = isset($kriteria['JumlahPemenang']) && $kriteria['JumlahPemenang'] !== null
            ? (int) $kriteria['JumlahPemenang']
            : (int) ($setting['JumlahPemenangDefault'] ?? 3);

        // Untuk peningkatan (AKD-04, HAD-04, GAB-04), hitung data semester Ganjil sebagai pembanding
        $prevNilaiStats   = [];
        $prevAbsensiStats = [];
        if (in_array($kode, ['AKD-04', 'HAD-04', 'GAB-04'], true)) {
            $prevNilaiStats   = self::hitungStatistikNilaiSantri($santriIds, 'Ganjil', $idTahunAjaran, $idTpq);
            $prevAbsensiStats = self::hitungStatistikAbsensiSantri($santriIds, 'Ganjil', $idTahunAjaran, $idTpq);
        }

        $kandidat = [];
        foreach ($santriList as $santri) {
            $idSantri = (string) $santri['IdSantri'];
            $nStat    = $nilaiStats[$idSantri] ?? ['rata_rata' => 0.0, 'nilai_max' => 0.0, 'jumlah_mapel' => 0];
            $aStat    = $absensiStats[$idSantri] ?? ['total_pertemuan' => 0, 'hadir' => 0, 'alfa' => 0, 'persen_hadir' => 0.0];

            $nilaiRata   = $nStat['rata_rata'];
            $persenHadir = $aStat['persen_hadir'];
            $skor        = self::hitungSkorGabungan($nilaiRata, $persenHadir, $bobotNilai, $bobotHadir);
            $catatan     = '';

            // Filter threshold umum jika diset
            if ($nilaiMin !== null && $nilaiRata < $nilaiMin) {
                continue;
            }
            if ($persenHadirMin !== null && $persenHadir < $persenHadirMin) {
                continue;
            }

            // Aturan khusus per kode penghargaan
            if ($kode === 'AKD-03') {
                if ($nStat['nilai_max'] < 100) {
                    continue;
                }
                $catatan = 'Nilai Sempurna (100)';
            } elseif ($kode === 'HAD-01') {
                if ($aStat['total_pertemuan'] === 0) {
                    continue;
                }
                $catatan = "Hadir {$aStat['hadir']}/{$aStat['total_pertemuan']} pertemuan";
            } elseif ($kode === 'GAB-03') {
                if ($aStat['alfa'] > 0 || $aStat['total_pertemuan'] === 0) {
                    continue;
                }
                $catatan = '0 Alpha';
            } elseif (in_array($kode, ['AKD-04', 'HAD-04', 'GAB-04'], true)) {
                $prevN = $prevNilaiStats[$idSantri]['rata_rata'] ?? 0.0;
                $prevH = $prevAbsensiStats[$idSantri]['persen_hadir'] ?? 0.0;
                $delta = $kode === 'AKD-04'
                    ? round($nilaiRata - $prevN, 2)
                    : ($kode === 'HAD-04'
                        ? round($persenHadir - $prevH, 2)
                        : round(self::hitungSkorGabungan($nilaiRata - $prevN, $persenHadir - $prevH, $bobotNilai, $bobotHadir), 2));
                if ($delta <= 0) {
                    continue;
                }
                $skor    = $delta;
                $catatan = "Peningkatan +{$delta}";
            }

            // Pilih metrik pengurutan sesuai kategori
            $sortMetric = match ($jenis['Kategori'] ?? 'akademik') {
                'akademik'  => $kode === 'AKD-04' ? $skor : $nilaiRata,
                'kehadiran' => $kode === 'HAD-04' ? $skor : $persenHadir,
                'gabungan'  => $skor,
                default     => $skor,
            };

            $kandidat[] = [
                'IdSantri'        => $idSantri,
                'NamaSantri'      => $santri['NamaSantri'],
                'NIS'             => $santri['NIS'] ?? '',
                'IdKelas'         => $santri['IdKelas'],
                'NamaKelas'       => $santri['NamaKelas'] ?? '',
                'NilaiRataRata'   => $nilaiRata,
                'PersenKehadiran' => $persenHadir,
                'SkorGabungan'    => $skor,
                'SortMetric'      => $sortMetric,
                'CatatanKhusus'   => $catatan,
            ];
        }

        // Urutkan descending berdasarkan SortMetric, tie-breaker NilaiRataRata lalu PersenKehadiran
        usort($kandidat, static function ($a, $b) {
            $cmpMetric = $b['SortMetric'] <=> $a['SortMetric'];
            if ($cmpMetric !== 0) {
                return $cmpMetric;
            }
            $cmpNilai = $b['NilaiRataRata'] <=> $a['NilaiRataRata'];
            if ($cmpNilai !== 0) {
                return $cmpNilai;
            }
            return $b['PersenKehadiran'] <=> $a['PersenKehadiran'];
        });

        // Kategori yang sifatnya semua memenuhi syarat (HAD-01, AKD-03, GAB-03, KHS-*) tidak dipotong kecuali admin set JumlahPemenang > 0 secara eksplisit
        $semuaMemenuhi = in_array($kode, ['HAD-01', 'AKD-03', 'GAB-03'], true) || ($jenis['Kategori'] ?? '') === 'khusus';
        if (!$semuaMemenuhi && $jumlahPemenang > 0) {
            $kandidat = array_slice($kandidat, 0, $jumlahPemenang);
        }

        foreach ($kandidat as $idx => &$row) {
            $row['Peringkat'] = $idx + 1;
            unset($row['SortMetric']);
        }

        return $kandidat;
    }

    /**
     * Generate nomor seri unik berdasarkan format dari setting lembaga.
     */
    public static function generateNomorSeri(string $format, string $kodeTpq, string $semester, int $urutan): string
    {
        $semCode = strtoupper(substr($semester, 0, 3));
        $noPad   = str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);

        return str_replace(
            ['{TAHUN}', '{KODE_TPQ}', '{SEM}', '{BULAN}', '{NO}'],
            [date('Y'), $kodeTpq ?: 'TPQ', $semCode ?: 'GEN', date('m'), $noPad],
            $format
        );
    }

    /**
     * Resolve penandatangan (1-3 slot) untuk 1 penerima sertifikat beserta QR Code / file TTD.
     */
    public static function resolvePenandatanganSertifikat(array $penerima, array $sertifikat, bool $createQrIfMissing = true): array
    {
        $ttdConfigModel = new \App\Models\Backend\Penghargaan\PenghargaanTtdConfigModel();
        $idJenis        = (int) ($sertifikat['IdJenis'] ?? 0);
        $idTpq          = (string) ($sertifikat['IdTpq'] ?? $penerima['IdTpq'] ?? '0');
        $configs        = $ttdConfigModel->getByJenisAndTpq($idJenis, $idTpq, true);

        $db = Database::connect();

        // Fallback ke setting default jika lembaga belum mengatur TTD untuk jenis ini:
        // #1 Wali Kelas (jabatan_kelas, mode qr) & #2 Kepala TPQ (jabatan_lembaga, mode qr)
        if (empty($configs)) {
            $jabatanList = $db->table('tbl_jabatan')
                ->whereIn('NamaJabatan', ['Wali Kelas', 'Kepala TPQ'])
                ->get()
                ->getResultArray();
            $jabMap = [];
            foreach ($jabatanList as $jb) {
                $jabMap[$jb['NamaJabatan']] = (int) $jb['IdJabatan'];
            }

            $configs = [
                [
                    'UrutanTtd'     => 1,
                    'SumberTtd'     => 'jabatan_kelas',
                    'IdJabatan'     => $jabMap['Wali Kelas'] ?? null,
                    'NamaManual'    => null,
                    'JabatanManual' => null,
                    'LabelTtd'      => 'Wali Kelas',
                    'NamaJabatan'   => 'Wali Kelas',
                    'ModeTtd'       => 'qr',
                    'FileTtd'       => null,
                ],
                [
                    'UrutanTtd'     => 2,
                    'SumberTtd'     => 'jabatan_lembaga',
                    'IdJabatan'     => $jabMap['Kepala TPQ'] ?? null,
                    'NamaManual'    => null,
                    'JabatanManual' => null,
                    'LabelTtd'      => 'Kepala TPQ',
                    'NamaJabatan'   => 'Kepala TPQ',
                    'ModeTtd'       => 'qr',
                    'FileTtd'       => null,
                ],
            ];
        }

        $signatureModel = new \App\Models\SignatureModel();
        $result         = [];

        foreach ($configs as $cfg) {
            $urutan  = (int) $cfg['UrutanTtd'];
            $nama    = '';
            $jabatan = (string) ($cfg['LabelTtd'] ?? $cfg['NamaJabatan'] ?? 'Penandatangan');
            $idGuru  = '0';

            if ($cfg['SumberTtd'] === 'manual') {
                $nama    = (string) ($cfg['NamaManual'] ?? '');
                $jabatan = (string) ($cfg['JabatanManual'] ?: $jabatan);
            } elseif ($cfg['SumberTtd'] === 'jabatan_kelas' && !empty($cfg['IdJabatan']) && !empty($penerima['IdKelas'])) {
                $row = $db->table('tbl_guru_kelas gk')
                    ->select('gk.IdGuru, g.Nama, j.NamaJabatan')
                    ->join('tbl_guru g', 'g.IdGuru = gk.IdGuru', 'left')
                    ->join('tbl_jabatan j', 'j.IdJabatan = gk.IdJabatan', 'left')
                    ->where('gk.IdTpq', $idTpq)
                    ->where('gk.IdKelas', (string) $penerima['IdKelas'])
                    ->where('gk.IdTahunAjaran', (string) $sertifikat['IdTahunAjaran'])
                    ->where('gk.IdJabatan', (int) $cfg['IdJabatan'])
                    ->get()
                    ->getRowArray();

                if ($row) {
                    $idGuru  = (string) $row['IdGuru'];
                    $nama    = (string) ($row['Nama'] ?? '');
                    $jabatan = (string) ($cfg['LabelTtd'] ?: ($row['NamaJabatan'] ?? $jabatan));
                }
            } elseif ($cfg['SumberTtd'] === 'jabatan_lembaga' && !empty($cfg['IdJabatan'])) {
                $row = $db->table('tbl_struktur_lembaga sl')
                    ->select('sl.IdGuru, g.Nama, j.NamaJabatan')
                    ->join('tbl_guru g', 'g.IdGuru = sl.IdGuru', 'left')
                    ->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan', 'left')
                    ->where('sl.IdTpq', $idTpq)
                    ->where('sl.IdJabatan', (int) $cfg['IdJabatan'])
                    ->orderBy('sl.TanggalStart', 'DESC')
                    ->get()
                    ->getRowArray();

                if ($row) {
                    $idGuru  = (string) $row['IdGuru'];
                    $nama    = (string) ($row['Nama'] ?? '');
                    $jabatan = (string) ($cfg['LabelTtd'] ?: ($row['NamaJabatan'] ?? $jabatan));
                }
            }

            $qrDataUri  = null;
            $token      = null;
            $uploadPath = null;

            if ($cfg['ModeTtd'] === 'qr') {
                $sigKey   = 'penghargaan:' . ($penerima['Id'] ?? 0) . ':slot:' . $urutan;
                $existing = $signatureModel->where('JenisDokumen', 'Sertifikat')
                    ->where('IdSantri', (string) ($penerima['IdSantri'] ?? '0'))
                    ->like('SignatureData', $sigKey)
                    ->first();

                if ($existing) {
                    $token = (string) $existing['Token'];
                } elseif ($createQrIfMissing) {
                    $token       = bin2hex(random_bytes(8));
                    $semEnum     = in_array($sertifikat['Semester'] ?? '', ['Ganjil', 'Genap'], true) ? $sertifikat['Semester'] : 'Genap';
                    $payloadJson = json_encode([
                        'key'        => $sigKey,
                        'IdPenerima' => (int) ($penerima['Id'] ?? 0),
                        'UrutanTtd'  => $urutan,
                        'Nama'       => $nama,
                        'Jabatan'    => $jabatan,
                    ]);

                    $qrFilename = self::saveQrFile($token);
                    $signatureModel->insert([
                        'Token'          => $token,
                        'IdSantri'       => (string) ($penerima['IdSantri'] ?? '0'),
                        'IdKelas'        => (string) ($penerima['IdKelas'] ?? '0'),
                        'IdTahunAjaran'  => (string) ($sertifikat['IdTahunAjaran'] ?? '0'),
                        'Semester'       => $semEnum,
                        'IdGuru'         => $idGuru,
                        'IdTpq'          => $idTpq,
                        'JenisDokumen'   => 'Sertifikat',
                        'SignatureData'  => $payloadJson,
                        'QrCode'         => $qrFilename,
                        'StatusValidasi' => 'Valid',
                        'TanggalTtd'     => date('Y-m-d H:i:s'),
                    ]);
                }

                if ($token) {
                    $qrDataUri = self::generateQrSvgDataUri(base_url('signature/verifikasi/' . $token));
                }
            } elseif ($cfg['ModeTtd'] === 'upload' && !empty($cfg['FileTtd'])) {
                $absPath = FCPATH . ltrim((string) $cfg['FileTtd'], '/');
                if (is_file($absPath)) {
                    $ext        = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));
                    $mime       = $ext === 'png' ? 'image/png' : 'image/jpeg';
                    $uploadPath = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($absPath));
                }
            }

            $result[$urutan] = [
                'urutan'     => $urutan,
                'label'      => (string) ($cfg['LabelTtd'] ?? $jabatan),
                'nama'       => $nama,
                'jabatan'    => $jabatan,
                'mode'       => (string) $cfg['ModeTtd'],
                'token'      => $token,
                'qr_data'    => $qrDataUri,
                'ttd_upload' => $uploadPath,
            ];
        }

        return $result;
    }

    /**
     * Simpan file QR SVG ke public/uploads/qr/.
     */
    private static function saveQrFile(string $token): string
    {
        $dir = FCPATH . 'uploads/qr/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = 'sertifikat_' . $token . '.svg';
        $options  = new \chillerlan\QRCode\QROptions([
            'outputType'   => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
            'eccLevel'     => \chillerlan\QRCode\Common\EccLevel::L,
            'scale'        => 6,
            'imageBase64'  => false,
            'addQuietzone' => true,
        ]);

        $svg = (new \chillerlan\QRCode\QRCode($options))->render(base_url('signature/verifikasi/' . $token));
        file_put_contents($dir . $filename, $svg);

        return $filename;
    }

    /**
     * Generate QR Code sebagai SVG Data URI untuk disisipkan di HTML / mPDF.
     */
    public static function generateQrSvgDataUri(string $content): string
    {
        $options = new \chillerlan\QRCode\QROptions([
            'outputType'   => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
            'eccLevel'     => \chillerlan\QRCode\Common\EccLevel::L,
            'scale'        => 6,
            'imageBase64'  => true,
            'addQuietzone' => true,
        ]);

        return (new \chillerlan\QRCode\QRCode($options))->render($content);
    }

    /**
     * Ambil konteks peran & batasan kelas untuk modul Penghargaan.
     * - Operator/Admin/Kepala TPQ: penuh pada TPQ masing-masing.
     * - Guru: read-only, hanya melihat kelas yang diajar; hanya Wali Kelas yang boleh generate kelasnya sendiri.
     */
    public static function getRoleContext(?string $idTahunAjaran = null): array
    {
        $activeRole = (string) (session()->get('active_role') ?? '');
        $idTpq      = (string) (session()->get('IdTpq') ?? '0');
        $idGuru     = (string) (session()->get('IdGuru') ?? '');

        $isAdminOrOp = (function_exists('in_groups') && in_groups('Admin'))
            || $activeRole === 'operator'
            || $activeRole === 'kepala_tpq'
            || ($activeRole === '' && function_exists('in_groups') && (in_groups('Operator') || in_groups('Kepala Sekolah')));

        $isGuruOnly = !$isAdminOrOp && (
            $activeRole === 'guru'
            || (function_exists('in_groups') && in_groups('Guru'))
        );

        $waliKelasIds = [];
        $guruKelasIds = [];

        if ($isGuruOnly && $idGuru !== '' && $idTpq !== '') {
            $helpModel = new \App\Models\HelpFunctionModel();
            if (empty($idTahunAjaran)) {
                $idTahunAjaran = session()->get('IdTahunAjaran') ?: $helpModel->getTahunAjaranSaatIni();
            }

            $rows = $helpModel->getDataGuruKelas(
                IdGuru: $idGuru,
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran
            );

            foreach ($rows as $row) {
                $r       = is_object($row) ? (array) $row : $row;
                $idKelas = (string) ($r['IdKelas'] ?? '');
                if ($idKelas === '') {
                    continue;
                }
                $guruKelasIds[$idKelas] = $idKelas;
                if (($r['NamaJabatan'] ?? '') === 'Wali Kelas') {
                    $waliKelasIds[$idKelas] = $idKelas;
                }
            }
        }

        return [
            'isOperatorOrAdmin' => $isAdminOrOp,
            'isGuruOnly'        => $isGuruOnly,
            'isWaliKelas'       => !empty($waliKelasIds),
            'waliKelasIds'      => array_values($waliKelasIds),
            'guruKelasIds'      => array_values($guruKelasIds),
            'idTpq'             => $idTpq,
        ];
    }
}
