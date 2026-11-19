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
