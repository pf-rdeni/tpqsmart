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
