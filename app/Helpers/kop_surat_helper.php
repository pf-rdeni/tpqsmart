<?php

if (!function_exists('getKopSuratData')) {
    /**
     * Helper Unified untuk Merender Kop Surat Lembaga
     * 
     * Mendukung:
     * - Kop TPQ (tbl_tpq)
     * - Kop MDA / MDTA (tbl_mda)
     * - Kop FKPQ Forum Komunikasi (tbl_fkpq)
     * - Kop Kepanitiaan Custom (tbl_surat_kop_custom)
     * - Tanpa Kop
     *
     * @param string $sumberKop ('TPQ', 'MDA', 'FKPQ', 'Kepanitiaan', 'TanpaKop')
     * @param int|null $idTpq
     * @param int|string|null $idKopCustom
     * @return array ['imageSrc' => string|null, 'text' => array|null, 'hasGaris' => bool]
     */
    function getKopSuratData(?string $sumberKop, ?int $idTpq = null, $idKopCustom = null): array
    {
        $idKopCustom = !empty($idKopCustom) ? (int)$idKopCustom : null;
        $idTpq       = !empty($idTpq) ? (int)$idTpq : null;
        $db = \Config\Database::connect();
        $kopImageSrc = null;
        $kopText     = null;
        $hasGaris    = true;

        if ($sumberKop === 'TanpaKop') {
            return [
                'imageSrc' => null,
                'text'     => null,
                'hasGaris' => false,
            ];
        }

        if ($sumberKop === 'Kepanitiaan' && !empty($idKopCustom)) {
            $kopRow = $db->table('tbl_surat_kop_custom')->where('Id', (int)$idKopCustom)->get()->getRowArray();
            if ($kopRow && !empty($kopRow['GambarKop'])) {
                $path = FCPATH . 'uploads/administrasi/kop/' . $kopRow['GambarKop'];
                if (file_exists($path)) {
                    $ext  = pathinfo($path, PATHINFO_EXTENSION);
                    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
                    $kopImageSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }
        } elseif ($sumberKop === 'FKPQ') {
            $fkpq = $db->table('tbl_fkpq')->get()->getRowArray();
            if ($fkpq && !empty($fkpq['KopSurat'])) {
                $path = FCPATH . 'uploads/kop/' . $fkpq['KopSurat'];
                if (file_exists($path)) {
                    $ext  = pathinfo($path, PATHINFO_EXTENSION);
                    $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                    $kopImageSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }

            if (!$kopImageSrc) {
                $kopText = [
                    'nama'        => !empty($fkpq['NamaLembaga']) ? $fkpq['NamaLembaga'] : 'FORUM KOMUNIKASI PENDIDIKAN AL-QUR\'AN (FKPQ)',
                    'namaLembaga' => !empty($fkpq['NamaLembaga']) ? $fkpq['NamaLembaga'] : 'FORUM KOMUNIKASI PENDIDIKAN AL-QUR\'AN (FKPQ)',
                    'subJudul'    => 'KABUPATEN BINTAN - KEPULAUAN RIAU',
                    'alamat'      => !empty($fkpq['Alamat']) ? $fkpq['Alamat'] : 'Sekretariat: Kemenag Kab. Bintan',
                    'kontak'      => !empty($fkpq['NoTelp']) ? ('Telp/WA: ' . $fkpq['NoTelp']) : '',
                    'noIzin'      => '',
                ];
            }
        } elseif ($sumberKop === 'MDA') {
            $mda = $db->table('tbl_mda')->get()->getRowArray();
            if ($mda && !empty($mda['KopSurat'])) {
                $path = FCPATH . 'uploads/kop/' . $mda['KopSurat'];
                if (file_exists($path)) {
                    $ext  = pathinfo($path, PATHINFO_EXTENSION);
                    $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                    $kopImageSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }

            if (!$kopImageSrc) {
                $kopText = [
                    'nama'        => !empty($mda['NamaMda']) ? $mda['NamaMda'] : 'MADRASAH DINIYAH TAKMILIYAH AWALIYAH',
                    'namaLembaga' => !empty($mda['NamaMda']) ? $mda['NamaMda'] : 'MADRASAH DINIYAH TAKMILIYAH AWALIYAH',
                    'subJudul'    => 'KABUPATEN BINTAN',
                    'alamat'      => !empty($mda['Alamat']) ? $mda['Alamat'] : '',
                    'kontak'      => !empty($mda['NoTelp']) ? ('Telp: ' . $mda['NoTelp']) : '',
                    'noIzin'      => '',
                ];
            }
        } else {
            // Default: Kop TPQ
            $tpq = null;
            if (!empty($idTpq)) {
                $tpq = $db->table('tbl_tpq')->where('IdTpq', (int)$idTpq)->get()->getRowArray();
            }
            if (!$tpq) {
                $tpq = $db->table('tbl_tpq')->get()->getRowArray();
            }

            if ($tpq && !empty($tpq['KopSurat'])) {
                $path = FCPATH . 'uploads/kop/' . $tpq['KopSurat'];
                if (file_exists($path)) {
                    $ext  = pathinfo($path, PATHINFO_EXTENSION);
                    $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                    $kopImageSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }

            if (!$kopImageSrc) {
                $namaTpq = !empty($tpq['NamaTpq']) ? strtoupper($tpq['NamaTpq']) : 'TAMAN PENDIDIKAN AL-QUR\'AN';
                $kopText = [
                    'nama'        => $namaTpq,
                    'namaLembaga' => $namaTpq,
                    'subJudul'    => 'LEMBAGA PENDIDIKAN AL-QUR\'AN',
                    'alamat'      => !empty($tpq['Alamat']) ? $tpq['Alamat'] : '',
                    'kontak'      => !empty($tpq['NoTelp']) ? ('Telp/HP: ' . $tpq['NoTelp']) : '',
                    'noIzin'      => !empty($tpq['NomorIjinOperasional']) ? ('Izin Operasional: ' . $tpq['NomorIjinOperasional']) : '',
                ];
            }
        }

        return [
            'imageSrc' => $kopImageSrc,
            'text'     => $kopText,
            'hasGaris' => $hasGaris,
        ];
    }
}

if (!function_exists('formatNamaGelar')) {
    /**
     * Standardisasi format penulisan Nama dan Titel / Gelar
     * Contoh:
     * - "DeNI RUSaNDI" -> "Deni Rusandi"
     * - "DENI RUSANDI, S.PD" -> "Deni Rusandi, S.Pd"
     * - "DENI RUSANDI S.PD.I" -> "Deni Rusandi, S.Pd.I"
     * - "DR. H. ZAKIRMAN, S.KOM, M.M" -> "Dr. H. Zakirman, S.Kom, M.M"
     *
     * @param string|null $raw
     * @return string
     */
    function formatNamaGelar(?string $raw): string
    {
        if (empty($raw)) {
            return '';
        }
        $raw = trim($raw);

        static $gelarMap = [
            // Gelar Depan
            'dr'       => 'Dr.',
            'dr.'      => 'Dr.',
            'dokter'   => 'dr.',
            'drg'      => 'drg.',
            'drg.'     => 'drg.',
            'drs'      => 'Drs.',
            'drs.'     => 'Drs.',
            'dra'      => 'Dra.',
            'dra.'     => 'Dra.',
            'prof'     => 'Prof.',
            'prof.'    => 'Prof.',
            'ir'       => 'Ir.',
            'ir.'      => 'Ir.',
            'h'        => 'H.',
            'h.'       => 'H.',
            'hj'       => 'Hj.',
            'hj.'      => 'Hj.',
            'kh'       => 'K.H.',
            'kh.'      => 'K.H.',
            'k.h.'     => 'K.H.',
            'ust'      => 'Ust.',
            'ust.'     => 'Ust.',
            'ustadz'   => 'Ustadz',
            'kyai'     => 'Kyai',
            'tgk'      => 'Tgk.',
            'tgk.'     => 'Tgk.',

            // Gelar Belakang Sarjana & Diploma
            's.pd'     => 'S.Pd',
            'spd'      => 'S.Pd',
            's.pd.i'   => 'S.Pd.I',
            'spdi'     => 'S.Pd.I',
            's.kom'    => 'S.Kom',
            'skom'     => 'S.Kom',
            's.e'      => 'S.E',
            'se'       => 'S.E',
            's.ag'     => 'S.Ag',
            'sag'      => 'S.Ag',
            's.h'      => 'S.H',
            'sh'       => 'S.H',
            's.t'      => 'S.T',
            'st'       => 'S.T',
            's.sos'    => 'S.Sos',
            'ssos'     => 'S.Sos',
            's.si'     => 'S.Si',
            'ssi'      => 'S.Si',
            's.ked'    => 'S.Ked',
            'sked'     => 'S.Ked',
            's.kep'    => 'S.Kep',
            'skep'     => 'S.Kep',
            's.farm'   => 'S.Farm',
            'sfarm'    => 'S.Farm',
            's.psi'    => 'S.Psi',
            'spsi'     => 'S.Psi',
            's.sn'     => 'S.Sn',
            's.pt'     => 'S.Pt',
            's.p'      => 'S.P',
            'sp'       => 'S.P',
            's.ip'     => 'S.I.P',
            's.i.p'    => 'S.I.P',
            'sip'      => 'S.I.P',
            's.sos.i'  => 'S.Sos.I',
            'ssosi'    => 'S.Sos.I',
            's.hum'    => 'S.Hum',
            'shum'     => 'S.Hum',
            's.pd.sd'  => 'S.Pd.SD',
            'spdsd'    => 'S.Pd.SD',
            's.th.i'   => 'S.Th.I',
            'sthi'     => 'S.Th.I',
            's.sy'     => 'S.Sy',
            'ssy'      => 'S.Sy',
            's.e.i'    => 'S.E.I',
            'sei'      => 'S.E.I',
            's.ak'     => 'S.Ak',
            'sak'      => 'S.Ak',
            's.ab'     => 'S.A.B',
            'sab'      => 'S.A.B',
            's.ap'     => 'S.A.P',
            'sap'      => 'S.A.P',
            's.tr.kom' => 'S.Tr.Kom',
            's.tr.t'   => 'S.Tr.T',

            // Gelar Belakang Magister & Doktoral
            'm.pd'     => 'M.Pd',
            'mpd'      => 'M.Pd',
            'm.pd.i'   => 'M.Pd.I',
            'mpdi'     => 'M.Pd.I',
            'm.kom'    => 'M.Kom',
            'mkom'     => 'M.Kom',
            'm.m'      => 'M.M',
            'mm'       => 'M.M',
            'm.ag'     => 'M.Ag',
            'mag'      => 'M.Ag',
            'm.si'     => 'M.Si',
            'msi'      => 'M.Si',
            'm.h'      => 'M.H',
            'mh'       => 'M.H',
            'm.t'      => 'M.T',
            'mt'       => 'M.T',
            'm.sc'     => 'M.Sc',
            'msc'      => 'M.Sc',
            'm.a'      => 'M.A',
            'ma'       => 'M.A',
            'm.b.a'    => 'M.B.A',
            'mba'      => 'M.B.A',
            'b.a'      => 'B.A',
            'ba'       => 'B.A',
            'lc'       => 'Lc.',
            'lc.'      => 'Lc.',
            'ph.d'     => 'Ph.D',
            'phd'      => 'Ph.D',
            'll.m'     => 'LL.M',
            'llm'      => 'LL.M',
            'ak'       => 'Ak.',
            'ak.'      => 'Ak.',
            'cpa'      => 'CPA',
            'ca'       => 'CA'
        ];

        // Simpan bagian penjelas dalam kurung jika ada (misal: "(Wali Aqilah Gunawan)")
        $extraBracket = '';
        if (preg_match('/\s*(\([^)]+\))\s*$/', $raw, $mBracket)) {
            $raw = trim(substr($raw, 0, -strlen($mBracket[0])));
            $inner = trim($mBracket[1], " ()");
            $extraBracket = ' (' . mb_convert_case($inner, MB_CASE_TITLE, 'UTF-8') . ')';
        }

        // Pisahkan nama utama dan gelar di belakang koma
        $parts = array_map('trim', explode(',', $raw));
        $namaUtama = array_shift($parts);

        $words = preg_split('/\s+/', $namaUtama);
        $formattedWords = [];
        $gelarBelakangAuto = [];

        foreach ($words as $idx => $w) {
            $wClean = strtolower(trim($w));
            $wNoDot = rtrim($wClean, '.');

            // Jika kata di akhir dan merupakan gelar belakang (misal "Deni Rusandi S.Pd")
            if ($idx > 0 && (isset($gelarMap[$wClean]) || isset($gelarMap[$wNoDot]))) {
                $canonical = $gelarMap[$wClean] ?? $gelarMap[$wNoDot];
                if (!in_array($wNoDot, ['h', 'hj', 'dr', 'drs', 'dra', 'prof', 'ir', 'kh', 'ust', 'ustadz', 'kyai', 'tgk'])) {
                    $gelarBelakangAuto[] = $canonical;
                    continue;
                }
            }

            // Cek gelar depan
            if (isset($gelarMap[$wClean])) {
                $formattedWords[] = $gelarMap[$wClean];
            } elseif (isset($gelarMap[$wNoDot]) && in_array($wNoDot, ['h', 'hj', 'dr', 'drs', 'dra', 'prof', 'ir', 'kh', 'ust', 'ustadz', 'kyai', 'tgk'])) {
                $formattedWords[] = $gelarMap[$wNoDot];
            } else {
                // Partikel nama bin / binti dsb
                if (in_array($wClean, ['bin', 'binti', 'ibn', 'van', 'de']) && $idx > 0) {
                    $formattedWords[] = $wClean;
                } else {
                    $formattedWords[] = mb_convert_case($w, MB_CASE_TITLE, 'UTF-8');
                }
            }
        }

        $formattedNama = implode(' ', $formattedWords);

        // Format gelar belakang yang dipisahkan koma
        $allGelar = array_merge($gelarBelakangAuto, $parts);
        $formattedGelar = [];
        foreach ($allGelar as $g) {
            $gTrim = trim($g);
            if (empty($gTrim)) continue;
            $gLower = strtolower($gTrim);
            $gNoDot = rtrim($gLower, '.');

            if (isset($gelarMap[$gLower])) {
                $formattedGelar[] = $gelarMap[$gLower];
            } elseif (isset($gelarMap[$gNoDot])) {
                $formattedGelar[] = $gelarMap[$gNoDot];
            } else {
                if (strlen($gTrim) <= 4 && !str_contains($gTrim, '.')) {
                    $formattedGelar[] = strtoupper($gTrim);
                } else {
                    $formattedGelar[] = mb_convert_case($gTrim, MB_CASE_TITLE, 'UTF-8');
                }
            }
        }

        if (!empty($formattedGelar)) {
            $formattedNama .= ', ' . implode(', ', $formattedGelar);
        }

        return $formattedNama . $extraBracket;
    }
}
