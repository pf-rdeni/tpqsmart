<?php

if (!function_exists('parseAlphabeticNilai')) {
    function parseAlphabeticNilai($nilaiString)
    {
        $result = [];
        $nilaiArray = explode(',', $nilaiString);

        foreach ($nilaiArray as $item) {
            $parts = explode('=', $item);
            if (count($parts) == 2) {
                $label = trim($parts[0]);
                $value = (int)trim($parts[1]);
                $result[] = [
                    'Label' => $label,
                    'Value' => $value
                ];
            }
        }

        return $result;
    }
}

if (!function_exists('isValidNilaiAlphabet')) {
    function isValidNilaiAlphabet($settingNilai)
    {
        return isset($settingNilai->NilaiAlphabet) &&
            $settingNilai->NilaiAlphabet &&
            $settingNilai->NilaiAlphabet->Nilai_Alphabet;
    }
}

if (!function_exists('konversiNilaiHuruf')) {
    function konversiNilaiHuruf($nilai, $settingNilai = null)
    {
        if ($nilai === ' ') return ' ';

        if ($settingNilai && isValidNilaiAlphabet($settingNilai)) {
            $nilaiArray = parseAlphabeticNilai($settingNilai->NilaiAlphabet->Nilai_Alphabet_Persamaan);

            foreach ($nilaiArray as $item) {
                if ($nilai >= $item['Value']) {
                    return $item['Label'];
                }
            }
            return 'E'; // Default jika tidak memenuhi kriteria
        }

        // Default konversi jika tidak ada setting
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }
}

if (!function_exists('getAlphabetKelasSettings')) {
    function getAlphabetKelasSettings($settingNilai, $idKelas)
    {
        $result = [
            'isAlphabetKelas' => false,
            'transformedNilai' => []
        ];

        if (isValidNilaiAlphabet($settingNilai)) {
            $kelasArray = explode(',', $settingNilai->NilaiAlphabet->Nilai_Alphabet_Kelas);

            if (in_array($idKelas, $kelasArray)) {
                $result['isAlphabetKelas'] = true;
                $result['transformedNilai'] = parseAlphabeticNilai($settingNilai->NilaiAlphabet->Nilai_Alphabet_Persamaan);
            }
        }

        return $result;
    }
}

if (!function_exists('angkaKeKata')) {
    function angkaKeKata($angka)
    {
        $angka = floatval($angka);
        $bilangan = array(
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
            'dua belas',
            'tiga belas',
            'empat belas',
            'lima belas',
            'enam belas',
            'tujuh belas',
            'delapan belas',
            'sembilan belas'
        );

        if ($angka < 20) {
            return $bilangan[$angka];
        } elseif ($angka < 100) {
            $puluhan = floor($angka / 10);
            $satuan = $angka % 10;
            return $bilangan[$puluhan] . ' puluh' . ($satuan > 0 ? ' ' . $bilangan[$satuan] : '');
        } elseif ($angka < 1000) {
            $ratusan = floor($angka / 100);
            $sisa = $angka % 100;
            return ($ratusan > 1 ? $bilangan[$ratusan] . ' ' : 'se') . 'ratus' . ($sisa > 0 ? ' ' . angkaKeKata($sisa) : '');
        } elseif ($angka < 1000000) {
            $ribuan = floor($angka / 1000);
            $sisa = $angka % 1000;
            return ($ribuan > 1 ? angkaKeKata($ribuan) . ' ' : 'se') . 'ribu' . ($sisa > 0 ? ' ' . angkaKeKata($sisa) : '');
        } elseif ($angka < 1000000000) {
            $jutaan = floor($angka / 1000000);
            $sisa = $angka % 1000000;
            return ($jutaan > 1 ? angkaKeKata($jutaan) . ' ' : 'se') . 'juta' . ($sisa > 0 ? ' ' . angkaKeKata($sisa) : '');
        } elseif ($angka < 1000000000000) {
            $milyar = floor($angka / 1000000000);
            $sisa = $angka % 1000000000;
            return ($milyar > 1 ? angkaKeKata($milyar) . ' ' : 'se') . 'milyar' . ($sisa > 0 ? ' ' . angkaKeKata($sisa) : '');
        }
    }
}

if (!function_exists('formatTerbilang')) {
    function formatTerbilang($angka)
    {
        $angka = floatval($angka);
        $bagian_bulat = floor($angka);
        $bagian_desimal = $angka - $bagian_bulat;

        $hasil = angkaKeKata($bagian_bulat);

        if ($bagian_desimal > 0) {
            $desimal_str = number_format($bagian_desimal, 2, '.', '');
            $desimal_str = rtrim($desimal_str, '0');
            $desimal_str = rtrim($desimal_str, '.');

            $hasil .= ' Koma ';
            for ($i = 0; $i < strlen($desimal_str); $i++) {
                if ($desimal_str[$i] != '.') {
                    $hasil .= angkaKeKata($desimal_str[$i]) . ' ';
                }
            }
        }

        // Mengubah setiap kata menjadi Title Case
        $hasil = toTitleCase($hasil);

        return trim($hasil);
    }
}

if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia($date, $format = 'd F Y')
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $timestamp = strtotime($date);
        $dayName = date('l', $timestamp);
        $day = date('d', $timestamp);
        $month = (int)date('m', $timestamp);
        $year = date('Y', $timestamp);

        $result = '';
        switch ($format) {
            case 'l, d F Y': // Format: Senin, 15 Maret 2024
                $result = $hari[$dayName] . ', ' . $day . ' ' . $bulan[$month] . ' ' . $year;
                break;
            case 'd F Y': // Format: 15 Maret 2024
                $result = $day . ' ' . $bulan[$month] . ' ' . $year;
                break;
            case 'l': // Format: Senin
                $result = $hari[$dayName];
                break;
            case 'F Y': // Format: Maret 2024
                $result = $bulan[$month] . ' ' . $year;
                break;
            default:
                $result = $day . ' ' . $bulan[$month] . ' ' . $year;
        }

        return $result;
    }
}

if (!function_exists('toTitleCase')) {
    function toTitleCase($text)
    {
        // Decode HTML entities terlebih dahulu
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim($text);
        $words = explode(' ', $text);
        $result = '';

        foreach ($words as $word) {
            // Konversi seluruh kata ke lowercase terlebih dahulu
            $word = mb_strtolower($word, 'UTF-8');

            // Cek apakah kata mengandung tanda petik
            if (strpos($word, "'") !== false) {
                // Pisahkan kata berdasarkan tanda petik
                $parts = explode("'", $word);

                // Proses setiap bagian
                foreach ($parts as $key => $part) {
                    if (!empty($part)) {
                        if ($key === 0) {
                            // Untuk bagian pertama, ubah huruf pertama menjadi uppercase
                            $parts[$key] = mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8') .
                                mb_substr($part, 1, null, 'UTF-8');
                        } else {
                            // Untuk bagian setelah tanda petik, biarkan lowercase
                            $parts[$key] = $part;
                        }
                    }
                }

                // Gabungkan kembali dengan tanda petik
                $word = implode("'", $parts);
            }

            // Proses tanda hubung (-)
            if (strpos($word, '-') !== false) {
                $parts = explode('-', $word);
                foreach ($parts as $key => $part) {
                    if (!empty($part)) {
                        // Ubah huruf pertama setiap bagian menjadi uppercase
                        $parts[$key] = mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8') .
                            mb_substr($part, 1, null, 'UTF-8');
                    }
                }
                $word = implode('-', $parts);
            } else {
                // Jika tidak ada tanda hubung, gunakan title case biasa
                $word = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') .
                    mb_substr($word, 1, null, 'UTF-8');
            }

            $result .= $word . ' ';
        }

        return trim($result);
    }
}
