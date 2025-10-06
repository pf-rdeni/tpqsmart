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

if (!function_exists('convertTahunAjaran')) {
    function convertTahunAjaran($TahunAjaran)
    {
        // jika array ambil index 0
        if (is_array($TahunAjaran)) {
            $TahunAjaran = $TahunAjaran[0];
        }
        $startYear = (int) substr($TahunAjaran, 0, 4);
        $endYear = (int) substr($TahunAjaran, 4);

        $StartYear = $startYear;
        $EndYear = $endYear;

        return $StartYear . '/' . $EndYear;
    }
}

//buat fungsi untuk merubah angka ke huruf arabic
if (!function_exists('angkaKeHurufArab')) {
    function angkaKeHurufArab($angka)
    {
        $hurufArab = [
            0 => '٠',
            1 => '١',
            2 => '٢',
            3 => '٣',
            4 => '٤',
            5 => '٥',
            6 => '٦',
            7 => '٧',
            8 => '٨',
            9 => '٩',
            '.' => '٫',  // Titik desimal Arab
            ',' => '٬'   // Koma desimal Arab
        ];

        $hasil = '';
        $angkaStr = strval($angka);

        for ($i = 0; $i < strlen($angkaStr); $i++) {
            $karakter = $angkaStr[$i];
            if (is_numeric($karakter)) {
                $digit = intval($karakter);
                $hasil .= $hurufArab[$digit];
            } elseif ($karakter === '.' || $karakter === ',') {
                $hasil .= $hurufArab[$karakter];
            } else {
                $hasil .= $karakter;
            }
        }

        return $hasil;
    }
}

// konversi huruf latin (A-E) ke aksara Arab
if (!function_exists('hurufLatinKeArab')) {
    function hurufLatinKeArab($huruf)
    {
        $map = [
            'A' => 'أ',
            'a' => 'أ',
            'B' => 'ب',
            'b' => 'ب',
            'C' => 'ج',
            'c' => 'ج',
            'D' => 'د',
            'd' => 'د',
            'E' => 'هـ',
            'e' => 'هـ',
            '+' => '+',
            '-' => '-',
            ' ' => ' '
        ];

        $result = '';
        $str = (string) $huruf;
        $len = mb_strlen($str, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($str, $i, 1, 'UTF-8');
            $result .= $map[$ch] ?? $ch;
        }

        return $result;
    }
}

// pembungkus: aktifkan konversi huruf ke Arab berdasarkan setting session
if (!function_exists('konversiHurufArabic')) {
    function konversiHurufArabic($huruf)
    {
        $settingNilaiArabic = session()->get('SettingNilaiArabic') ?? false;
        if ($settingNilaiArabic) {
            return hurufLatinKeArab($huruf);
        }
        return $huruf;
    }
}

// check settingan converi ke arab?
if (!function_exists('konversiNilaiAngkaArabic')) {
    function konversiNilaiAngkaArabic($nilai)
    {
        // ambil settingan dari session angka arabic
        $settingNilaiArabic = session()->get('SettingNilaiArabic') ?? false;
        if ($settingNilaiArabic) {
            // Jika settingan angka arabic aktif, konversi ke angka arab
            return angkaKeHurufArab($nilai);
        } else {
            // Jika tidak, kembalikan nilai apa adanya
            return $nilai;
        }
    }
}

//Check conversi terbilang ke arab
if (!function_exists('konversiTerbilangArabic')) {
    function konversiTerbilangArabic($angka)
    {
        // ambil settingan dari session angka arabic
        $settingNilaiArabic = session()->get('SettingNilaiArabic') ?? false;
        if ($settingNilaiArabic) {
            // Jika settingan angka arabic aktif, konversi ke terbilang arab
            $terbilang = angkaKeTerbilangArab($angka);
            // Balik urutan karakter dalam kata Arab untuk PDF
            return reverseArabicCharacters($terbilang);
        } else {
            // Jika tidak, kembalikan nilai apa adanya
            return formatTerbilang($angka);
        }
    }
}


// fungsi merubah angka ke huruf arabic dengan format terbilang bahasa arab

if (!function_exists('angkaKeTerbilangArab')) {
    function angkaKeTerbilangArab($angka)
    {
        $angka = floatval($angka);
        $bilangan = [
            0 => '',
            1 => 'واحد',
            2 => 'اثنان',
            3 => 'ثلاثة',
            4 => 'أربعة',
            5 => 'خمسة',
            6 => 'ستة',
            7 => 'سبعة',
            8 => 'ثمانية',
            9 => 'تسعة',
            10 => 'عشرة',
            11 => 'أحد عشر',
            12 => 'اثنا عشر',
            13 => 'ثلاثة عشر',
            14 => 'أربعة عشر',
            15 => 'خمسة عشر',
            16 => 'ستة عشر',
            17 => 'سبعة عشر',
            18 => 'ثمانية عشر',
            19 => 'تسعة عشر',
            20 => 'عشرون',
            30 => 'ثلاثون',
            40 => 'أربعون',
            50 => 'خمسون',
            60 => 'ستون',
            70 => 'سبعون',
            80 => 'ثمانون',
            90 => 'تسعون',
            100 => 'مائة',
            200 => 'مئتان',
            300 => 'ثلاثمائة',
            400 => 'أربعمائة',
            500 => 'خمسمائة',
            600 => 'ستمائة',
            700 => 'سبعمائة',
            800 => 'ثمانمائة',
            900 => 'تسعمائة',
            1000 => 'ألف',
            2000 => 'ألفان',
            3000 => 'ثلاثة آلاف',
            4000 => 'أربعة آلاف',
            5000 => 'خمسة آلاف',
            6000 => 'ستة آلاف',
            7000 => 'سبعة آلاف',
            8000 => 'ثمانية آلاف',
            9000 => 'تسعة آلاف',
            10000 => 'عشرة آلاف',
            100000 => 'مائة ألف',
            1000000 => 'مليون',
            1000000000 => 'مليار'
        ];

        if ($angka < 20) {
            return $bilangan[$angka];
        } elseif ($angka < 100) {
            $puluhan = floor($angka / 10) * 10;
            $satuan = $angka % 10;
            if ($satuan == 0) {
                return $bilangan[$puluhan];
            } else {
                return $bilangan[$puluhan] . ' و ' . $bilangan[$satuan];
            }
        } elseif ($angka < 1000) {
            $ratusan = floor($angka / 100) * 100;
            $sisa = $angka % 100;
            if ($sisa == 0) {
                return $bilangan[$ratusan];
            } else {
                return $bilangan[$ratusan] . ' و ' . angkaKeTerbilangArab($sisa);
            }
        } elseif ($angka < 1000000) {
            $ribuan = floor($angka / 1000);
            $sisa = $angka % 1000;
            if ($sisa == 0) {
                return angkaKeTerbilangArab($ribuan) . ' ألف';
            } else {
                return angkaKeTerbilangArab($sisa) . ' ألف و ' . angkaKeTerbilangArab($ribuan);
            }
        } elseif ($angka < 1000000000) {
            $jutaan = floor($angka / 1000000);
            $sisa = $angka % 1000000;
            if ($sisa == 0) {
                return angkaKeTerbilangArab($jutaan) . ' مليون';
            } else {
                return angkaKeTerbilangArab($jutaan) . ' مليون و ' . angkaKeTerbilangArab($sisa);
            }
        } else {
            $milyar = floor($angka / 1000000000);
            $sisa = $angka % 1000000000;
            if ($sisa == 0) {
                return angkaKeTerbilangArab($milyar) . ' مليار';
            } else {
                return angkaKeTerbilangArab($milyar) . ' مليار و ' . angkaKeTerbilangArab($sisa);
            }
        }
    }
}

if (!function_exists('formatTerbilangArab')) {
    function formatTerbilangArab($angka)
    {
        $angka = floatval($angka);
        $bagian_bulat = floor($angka);
        $bagian_desimal = $angka - $bagian_bulat;

        $hasil = angkaKeTerbilangArab($bagian_bulat);

        if ($bagian_desimal > 0) {
            $desimal_str = number_format($bagian_desimal, 2, '.', '');
            $desimal_str = rtrim($desimal_str, '0');
            $desimal_str = rtrim($desimal_str, '.');

            $hasil .= ' فاصلة ';
            for ($i = 0; $i < strlen($desimal_str); $i++) {
                if ($desimal_str[$i] != '.') {
                    $hasil .= angkaKeTerbilangArab($desimal_str[$i]) . ' ';
                }
            }
        }

        return trim($hasil);
    }
}

// Fungsi untuk membalik urutan karakter dalam kata Arab
if (!function_exists('reverseArabicCharacters')) {
    function reverseArabicCharacters($text)
    {
        // Hanya balik jika teks mengandung karakter Arab
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            // Pisahkan kata-kata
            $words = explode(' ', $text);
            $reversedWords = [];

            foreach ($words as $word) {
                // Balik urutan karakter dalam setiap kata
                $reversedWords[] = mb_strrev($word, 'UTF-8');
            }

            return implode(' ', $reversedWords);
        }

        return $text;
    }
}

// Fungsi untuk membalik string multibyte
if (!function_exists('mb_strrev')) {
    function mb_strrev($str, $encoding = 'UTF-8')
    {
        $length = mb_strlen($str, $encoding);
        $reversed = '';

        for ($i = $length - 1; $i >= 0; $i--) {
            $reversed .= mb_substr($str, $i, 1, $encoding);
        }

        return $reversed;
    }
}

// Update fungsi angkaKeHurufArabTerbilang untuk menggunakan formatTerbilangArab
if (!function_exists('angkaKeHurufArabTerbilang')) {
    function angkaKeHurufArabTerbilang($angka)
    {
        $terbilang = formatTerbilangArab($angka);
        return $terbilang;
    }
}