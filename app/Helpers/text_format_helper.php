<?php

/**
 * Helper functions untuk format text
 * 
 * @package    App\Helpers
 * @category   Helper
 */

if (!function_exists('formatVisi')) {
    /**
     * Format Visi Lembaga untuk ditampilkan
     * 
     * @param string|null $visi Teks visi lembaga
     * @return string HTML formatted visi
     */
    function formatVisi($visi)
    {
        if (empty($visi)) {
            return '';
        }

        $visiLines = explode("\n", $visi);
        $html = '';

        foreach ($visiLines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $html .= '<p style="margin-bottom: 5px;">' . htmlspecialchars($line) . '</p>';
            }
        }

        return $html;
    }
}

if (!function_exists('formatMisi')) {
    /**
     * Format Misi Lembaga untuk ditampilkan
     * Mendeteksi otomatis apakah format list (1., 2., 3.) atau paragraf biasa
     * 
     * @param string|null $misi Teks misi lembaga
     * @param bool $useOlForList Gunakan <ol> untuk list (true) atau <ul> (false). Default true untuk ordered list
     * @return string HTML formatted misi
     */
    function formatMisi($misi, $useOlForList = true)
    {
        if (empty($misi)) {
            return '';
        }

        $misiLines = explode("\n", $misi);

        // Cek apakah format sudah seperti list (mengandung angka di awal)
        $isListFormat = false;
        foreach ($misiLines as $line) {
            $line = trim($line);
            if (preg_match('/^(\d+[\.\)]|[-•])\s/', $line)) {
                $isListFormat = true;
                break;
            }
        }

        if ($isListFormat) {
            // Tampilkan sebagai list (ordered atau unordered)
            $listTag = $useOlForList ? 'ol' : 'ul';
            $listClass = $useOlForList ? 'visi-misi-list' : 'visi-misi-list';
            $html = '<' . $listTag . ' class="' . $listClass . '" style="margin-left: 20px; padding-left: 0;">';
            foreach ($misiLines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    // Hapus nomor/bullet di awal jika ada, karena akan ditangani oleh list
                    $line = preg_replace('/^(\d+[\.\)]|[-•])\s+/', '', $line);
                    $html .= '<li style="margin-bottom: 5px;">' . htmlspecialchars($line) . '</li>';
                }
            }
            $html .= '</' . $listTag . '>';
            return $html;
        } else {
            // Tampilkan sebagai paragraf
            $html = '';
            foreach ($misiLines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $html .= '<p style="margin-bottom: 5px;">' . htmlspecialchars($line) . '</p>';
                }
            }
            return $html;
        }
    }
}

if (!function_exists('formatVisiMisi')) {
    /**
     * Format Visi dan Misi untuk ditampilkan (combined)
     * 
     * @param string|null $visi Teks visi lembaga
     * @param string|null $misi Teks misi lembaga
     * @return array Array dengan key 'visi' dan 'misi' yang berisi HTML formatted
     */
    function formatVisiMisi($visi = null, $misi = null)
    {
        return [
            'visi' => formatVisi($visi),
            'misi' => formatMisi($misi)
        ];
    }
}

if (!function_exists('formatNamaGuru')) {
    /**
     * Format nama guru dengan gelar depan dan belakang secara rapi sesuai EYD
     * 
     * @param string|null $namaRaw Nama guru mentah dari DB
     * @return string Nama terformat rapi
     */
    function formatNamaGuru($namaRaw)
    {
        if (empty($namaRaw)) {
            return '';
        }

        // Decode HTML entities
        $namaRaw = html_entity_decode($namaRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $namaRaw = trim($namaRaw);

        // Pisahkan nama dan gelar menggunakan koma jika ada
        $parts = explode(',', $namaRaw);
        $namaUtamaDanGelarDepan = trim($parts[0]);
        $gelarBelakangParts = array_slice($parts, 1);

        // Kamus Gelar Belakang Akademik/Keagamaan (case-insensitive) -> format EYD
        $kamusGelarBelakang = [
            's.pd' => 'S.Pd',
            'spd' => 'S.Pd',
            's.pd.i' => 'S.Pd.I',
            'spdi' => 'S.Pd.I',
            's.ag' => 'S.Ag',
            'sag' => 'S.Ag',
            's.ag.i' => 'S.Ag.I',
            'sagi' => 'S.Ag.I',
            'm.pd' => 'M.Pd',
            'mpd' => 'M.Pd',
            'm.pd.i' => 'M.Pd.I',
            'mpdi' => 'M.Pd.I',
            'm.ag' => 'M.Ag',
            'mag' => 'M.Ag',
            'm.ag.i' => 'M.Ag.I',
            'magi' => 'M.Ag.I',
            's.kom' => 'S.Kom',
            'skom' => 'S.Kom',
            'm.kom' => 'M.Kom',
            'mkom' => 'M.Kom',
            's.si' => 'S.Si',
            'ssi' => 'S.Si',
            'm.si' => 'M.Si',
            'msi' => 'M.Si',
            's.e' => 'S.E',
            'se' => 'S.E',
            'm.m' => 'M.M',
            'mm' => 'M.M',
            's.h' => 'S.H',
            'sh' => 'S.H',
            'm.h' => 'M.H',
            'mh' => 'M.H',
            's.sos' => 'S.Sos',
            'ssos' => 'S.Sos',
            's.psi' => 'S.Psi',
            'spsi' => 'S.Psi',
            's.th.i' => 'S.Th.I',
            'sthi' => 'S.Th.I',
            'm.th.i' => 'M.Th.I',
            'mthi' => 'M.Th.I',
            'lc' => 'Lc',
            'l.c' => 'Lc',
            's.sy' => 'S.Sy',
            'ssy' => 'S.Sy',
        ];

        // Cek apakah ada gelar belakang yang tertulis langsung tanpa koma di bagian nama utama
        $nameWordsForRear = explode(' ', $namaUtamaDanGelarDepan);
        while (count($nameWordsForRear) > 1) {
            $lastWord = end($nameWordsForRear);
            $lastWordClean = strtolower(str_replace([' ', '.'], '', $lastWord));
            if (isset($kamusGelarBelakang[$lastWordClean])) {
                array_unshift($gelarBelakangParts, $lastWord);
                array_pop($nameWordsForRear);
                $namaUtamaDanGelarDepan = implode(' ', $nameWordsForRear);
            } else {
                break;
            }
        }

        // Kamus Gelar Depan -> format EYD (case-sensitive lookup first, fallback to case-insensitive except for 'dr' / 'DR')
        $frontTitlesClean = [
            'dr' => 'dr.', 'dr.' => 'dr.',
            'Dr' => 'Dr.', 'Dr.' => 'Dr.',
            'DR' => 'Dr.', 'DR.' => 'Dr.',
            'prof' => 'Prof.', 'prof.' => 'Prof.',
            'Prof' => 'Prof.', 'Prof.' => 'Prof.',
            'PROF' => 'Prof.', 'PROF.' => 'Prof.',
            'ust' => 'Ust.', 'ust.' => 'Ust.',
            'Ust' => 'Ust.', 'Ust.' => 'Ust.',
            'UST' => 'Ust.', 'UST.' => 'Ust.',
            'ustadz' => 'Ustadz', 'ustadz.' => 'Ustadz',
            'Ustadz' => 'Ustadz', 'Ustadz.' => 'Ustadz',
            'USTADZ' => 'Ustadz', 'USTADZ.' => 'Ustadz',
            'ustad' => 'Ustad', 'ustad.' => 'Ustad',
            'Ustad' => 'Ustad', 'Ustad.' => 'Ustad',
            'USTAD' => 'Ustad', 'USTAD.' => 'Ustad',
            'kh' => 'K.H.', 'kh.' => 'K.H.', 'k.h.' => 'K.H.', 'k.h' => 'K.H.',
            'KH' => 'K.H.', 'KH.' => 'K.H.', 'K.H.' => 'K.H.', 'K.H' => 'K.H.',
            'h' => 'H.', 'h.' => 'H.',
            'H' => 'H.', 'H.' => 'H.',
            'hj' => 'Hj.', 'hj.' => 'Hj.',
            'Hj' => 'Hj.', 'Hj.' => 'Hj.',
            'HJ' => 'Hj.', 'HJ.' => 'Hj.',
            'dra' => 'Dra.', 'dra.' => 'Dra.',
            'Dra' => 'Dra.', 'Dra.' => 'Dra.',
            'DRA' => 'Dra.', 'DRA.' => 'Dra.',
            'drs' => 'Drs.', 'drs.' => 'Drs.',
            'Drs' => 'Drs.', 'Drs.' => 'Drs.',
            'DRS' => 'Drs.', 'DRS.' => 'Drs.',
        ];

        // Pisahkan gelar depan
        $words = explode(' ', $namaUtamaDanGelarDepan);
        $extractedFrontTitles = [];
        while (!empty($words)) {
            $firstWordRaw = $words[0];
            
            if (isset($frontTitlesClean[$firstWordRaw])) {
                $extractedFrontTitles[] = $frontTitlesClean[$firstWordRaw];
                array_shift($words);
            } else {
                $firstWordLower = strtolower($firstWordRaw);
                $firstWordClean = rtrim($firstWordLower, '.');
                
                // Jangan lakukan fallback case-insensitive untuk "dr/dr." / "DR/DR." karena bisa tertukar
                if ($firstWordClean !== 'dr' && isset($frontTitlesClean[$firstWordClean])) {
                    $extractedFrontTitles[] = $frontTitlesClean[$firstWordClean];
                    array_shift($words);
                } else {
                    break;
                }
            }
        }
        $mainName = implode(' ', $words);

        // Format nama utama menggunakan toTitleCase
        $formattedMainName = function_exists('toTitleCase') ? toTitleCase($mainName) : ucwords(strtolower($mainName));

        // Format gelar belakang
        $formattedRearTitles = [];
        foreach ($gelarBelakangParts as $gelar) {
            $gelarClean = trim($gelar);
            if ($gelarClean === '') continue;

            $searchKey = strtolower(str_replace([' ', '.'], '', $gelarClean));
            $searchKeyWithDots = strtolower(str_replace(' ', '', $gelarClean));

            $matchedGelar = null;
            if (isset($kamusGelarBelakang[$searchKey])) {
                $matchedGelar = $kamusGelarBelakang[$searchKey];
            } elseif (isset($kamusGelarBelakang[$searchKeyWithDots])) {
                $matchedGelar = $kamusGelarBelakang[$searchKeyWithDots];
            } elseif (isset($kamusGelarBelakang[strtolower($gelarClean)])) {
                $matchedGelar = $kamusGelarBelakang[strtolower($gelarClean)];
            }

            if ($matchedGelar !== null) {
                $formattedRearTitles[] = $matchedGelar;
            } else {
                if (strlen($gelarClean) <= 4) {
                    $formattedRearTitles[] = strtoupper($gelarClean);
                } else {
                    $formattedRearTitles[] = function_exists('toTitleCase') ? toTitleCase($gelarClean) : ucwords(strtolower($gelarClean));
                }
            }
        }

        // Gabungkan semuanya kembali
        $finalName = '';
        if (!empty($extractedFrontTitles)) {
            $finalName .= implode(' ', $extractedFrontTitles) . ' ';
        }
        $finalName .= $formattedMainName;
        if (!empty($formattedRearTitles)) {
            $finalName .= ', ' . implode(', ', $formattedRearTitles);
        }

        return trim($finalName);
    }
}

