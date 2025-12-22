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

