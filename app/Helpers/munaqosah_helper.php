<?php

if (!function_exists('getStatusAntrian')) {
    /**
     * Mendapatkan status antrian dalam format yang mudah dibaca
     *
     * @param bool $status
     * @return string
     */
    function getStatusAntrian($status)
    {
        return $status ? 'Selesai' : 'Belum';
    }
}

if (!function_exists('getStatusAntrianBadge')) {
    /**
     * Mendapatkan badge status antrian dengan warna
     *
     * @param bool $status
     * @return string
     */
    function getStatusAntrianBadge($status)
    {
        if ($status) {
            return '<span class="badge badge-success">Selesai</span>';
        } else {
            return '<span class="badge badge-warning">Belum</span>';
        }
    }
}

if (!function_exists('getTipeUjianBadge')) {
    /**
     * Mendapatkan badge tipe ujian dengan warna
     *
     * @param string $typeUjian
     * @return string
     */
    function getTipeUjianBadge($typeUjian)
    {
        if ($typeUjian == 'munaqosah') {
            return '<span class="badge badge-primary">Munaqosah</span>';
        } else {
            return '<span class="badge badge-warning">Pra-Munaqosah</span>';
        }
    }
}

if (!function_exists('getNilaiBadge')) {
    /**
     * Mendapatkan badge nilai dengan warna berdasarkan range
     *
     * @param float $nilai
     * @return string
     */
    function getNilaiBadge($nilai)
    {
        if ($nilai >= 80) {
            return '<span class="badge badge-success">' . number_format($nilai, 2) . '</span>';
        } elseif ($nilai >= 60) {
            return '<span class="badge badge-warning">' . number_format($nilai, 2) . '</span>';
        } else {
            return '<span class="badge badge-danger">' . number_format($nilai, 2) . '</span>';
        }
    }
}

if (!function_exists('getKategoriMateriOptions')) {
    /**
     * Mendapatkan opsi kategori materi ujian
     *
     * @return array
     */
    function getKategoriMateriOptions()
    {
        return [
            'Iqra' => 'Iqra',
            'Qur\'an' => 'Qur\'an',
            'Hafalan' => 'Hafalan',
            'Tajwid' => 'Tajwid',
            'Praktik' => 'Praktik'
        ];
    }
}

if (!function_exists('getTipeUjianOptions')) {
    /**
     * Mendapatkan opsi tipe ujian
     *
     * @return array
     */
    function getTipeUjianOptions()
    {
        return [
            'munaqosah' => 'Munaqosah',
            'pra-munaqosah' => 'Pra-Munaqosah'
        ];
    }
}

if (!function_exists('formatTanggalMunaqosah')) {
    /**
     * Format tanggal untuk display di sistem munaqosah
     *
     * @param string $tanggal
     * @param string $format
     * @return string
     */
    function formatTanggalMunaqosah($tanggal, $format = 'd/m/Y H:i')
    {
        if (empty($tanggal)) {
            return '-';
        }
        
        return date($format, strtotime($tanggal));
    }
}

if (!function_exists('generateNoPeserta')) {
    /**
     * Generate nomor peserta otomatis
     *
     * @param string $prefix
     * @param int $length
     * @return string
     */
    function generateNoPeserta($prefix = 'PESERTA', $length = 6)
    {
        $timestamp = date('YmdHis');
        $random = str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }
}

if (!function_exists('validateBobotNilai')) {
    /**
     * Validasi total bobot nilai tidak melebihi 100%
     *
     * @param array $bobotData
     * @param string $tahunAjaran
     * @param int $excludeId
     * @return bool
     */
    function validateBobotNilai($bobotData, $tahunAjaran, $excludeId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_munaqosah_bobot_nilai');
        $builder->select('SUM(NilaiBobot) as total');
        $builder->where('IdTahunAjaran', $tahunAjaran);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        $result = $builder->get()->getRow();
        $totalExisting = $result ? $result->total : 0;
        $newTotal = $totalExisting + $bobotData['NilaiBobot'];
        
        return $newTotal <= 100;
    }
}

if (!function_exists('getStatistikMunaqosah')) {
    /**
     * Mendapatkan statistik munaqosah untuk dashboard
     *
     * @param string $tahunAjaran
     * @return array
     */
    function getStatistikMunaqosah($tahunAjaran = null)
    {
        if (!$tahunAjaran) {
            $tahunAjaran = session()->get('IdTahunAjaran') ?? '2024/2025';
        }
        
        $db = \Config\Database::connect();
        
        // Total peserta
        $totalPeserta = $db->table('tbl_munaqosah_peserta')
            ->where('IdTahunAjaran', $tahunAjaran)
            ->countAllResults();
        
        // Sudah dinilai
        $sudahDinilai = $db->table('tbl_munaqosah_nilai')
            ->where('IdTahunAjaran', $tahunAjaran)
            ->countAllResults();
        
        // Dalam antrian
        $dalamAntrian = $db->table('tbl_munaqosah_antrian')
            ->where('IdTahunAjaran', $tahunAjaran)
            ->where('Status', false)
            ->countAllResults();
        
        // Belum dinilai
        $belumDinilai = $totalPeserta - $sudahDinilai;
        
        return [
            'total_peserta' => $totalPeserta,
            'sudah_dinilai' => $sudahDinilai,
            'dalam_antrian' => $dalamAntrian,
            'belum_dinilai' => max(0, $belumDinilai)
        ];
    }
}

if (!function_exists('getTotalPesertaSudahDinilaiSemuaKategori')) {
    /**
     * Menghitung total peserta yang sudah dinilai di SEMUA kategori
     * (cara perhitungan sama seperti di monitoring)
     * 
     * @param string $idTahunAjaran
     * @param string $typeUjian
     * @param int|null $idTpq
     * @return int
     */
    function getTotalPesertaSudahDinilaiSemuaKategori($idTahunAjaran, $typeUjian, $idTpq = null)
    {
        $db = \Config\Database::connect();
        
        // Ambil semua peserta yang terdaftar dengan kategori mereka (cara sama seperti buildMonitoringDataset)
        $builder = $db->table('tbl_munaqosah_registrasi_uji r');
        $builder->select('r.NoPeserta, r.IdKategoriMateri');
        $builder->where('r.IdTahunAjaran', $idTahunAjaran);
        $builder->where('r.TypeUjian', $typeUjian);
        
        if ($idTpq !== null) {
            if ($idTpq == 0) {
                $builder->where('r.IdTpq IS NULL');
            } else {
                $builder->where('r.IdTpq', $idTpq);
            }
        }
        
        $registrasiRows = $builder->get()->getResultArray();
        
        if (empty($registrasiRows)) {
            return 0;
        }
        
        // Kelompokkan peserta berdasarkan NoPeserta dan kumpulkan kategori mereka
        $pesertaKategori = [];
        foreach ($registrasiRows as $row) {
            $noPeserta = $row['NoPeserta'];
            $idKategori = $row['IdKategoriMateri'];
            
            if (!isset($pesertaKategori[$noPeserta])) {
                $pesertaKategori[$noPeserta] = [];
            }
            
            // Hanya tambahkan kategori yang tidak kosong
            if (!empty($idKategori)) {
                $pesertaKategori[$noPeserta][$idKategori] = true;
            }
        }
        
        // Ambil semua nilai untuk peserta-peserta ini dalam satu query
        $noPesertaList = array_keys($pesertaKategori);
        if (empty($noPesertaList)) {
            return 0;
        }
        
        $nilaiBuilder = $db->table('tbl_munaqosah_nilai n');
        $nilaiBuilder->select('n.NoPeserta, n.IdKategoriMateri, n.Nilai');
        $nilaiBuilder->where('n.IdTahunAjaran', $idTahunAjaran);
        $nilaiBuilder->where('n.TypeUjian', $typeUjian);
        $nilaiBuilder->where('n.Nilai >', 0);
        $nilaiBuilder->whereIn('n.NoPeserta', $noPesertaList);
        
        if ($idTpq !== null) {
            if ($idTpq == 0) {
                $nilaiBuilder->where('n.IdTpq IS NULL');
            } else {
                $nilaiBuilder->where('n.IdTpq', $idTpq);
            }
        }
        
        $nilaiRows = $nilaiBuilder->get()->getResultArray();
        
        // Kelompokkan nilai per peserta dan kategori
        $pesertaNilai = [];
        foreach ($nilaiRows as $row) {
            $noPeserta = $row['NoPeserta'];
            $idKategori = $row['IdKategoriMateri'];
            
            if (!isset($pesertaNilai[$noPeserta])) {
                $pesertaNilai[$noPeserta] = [];
            }
            
            if (!empty($idKategori)) {
                $pesertaNilai[$noPeserta][$idKategori] = true;
            }
        }
        
        // Untuk setiap peserta, cek apakah semua kategori mereka sudah dinilai
        $totalSudahDinilai = 0;
        
        foreach ($pesertaKategori as $noPeserta => $kategoriList) {
            if (empty($kategoriList)) {
                continue;
            }
            
            $kategoriIds = array_keys($kategoriList);
            $totalKategori = count($kategoriIds);
            
            // Cek apakah semua kategori sudah dinilai
            $kategoriDinilai = 0;
            if (isset($pesertaNilai[$noPeserta])) {
                foreach ($kategoriIds as $idKategori) {
                    if (isset($pesertaNilai[$noPeserta][$idKategori])) {
                        $kategoriDinilai++;
                    }
                }
            }
            
            // Jika semua kategori sudah dinilai, hitung peserta ini
            if ($kategoriDinilai >= $totalKategori) {
                $totalSudahDinilai++;
            }
        }
        
        return $totalSudahDinilai;
    }
}