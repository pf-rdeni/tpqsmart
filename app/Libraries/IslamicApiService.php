<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;
use Exception;

/**
 * IslamicApiService
 * 
 * Service untuk mengintegrasikan API Jadwal Sholat (MuslimSalat) 
 * dan Al-Qur'an (Quran Cloud)
 * 
 * @package App\Libraries
 */
class IslamicApiService
{
    /**
     * Base URL untuk MuslimSalat API
     */
    private const MUSLIMSALAT_BASE_URL = 'https://muslimsalat.com';

    /**
     * Base URL untuk Al-Qur'an API
     */
    private const QURAN_BASE_URL = 'https://api.alquran.cloud/v1';

    /**
     * HTTP Client instance
     */
    protected $client;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Konfigurasi untuk HTTP client
        $config = [
            'timeout' => 30,
            'connect_timeout' => 10,
        ];

        // Cek apakah file CA certificate ada
        $caCertPath = getenv('SSL_CERT_FILE');
        if (empty($caCertPath)) {
            // Coba beberapa lokasi umum untuk CA certificate
            $possiblePaths = [
                getcwd() . '/vendor/cacert.pem',
                getcwd() . '/cacert.pem',
                'C:/laragon/etc/ssl/cacert.pem',
                'D:/Projects/Laragon-installer/8.0-W64/etc/ssl/cacert.pem',
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $caCertPath = $path;
                    break;
                }
            }
        }

        // Jika file CA certificate ditemukan, gunakan untuk verifikasi
        if (!empty($caCertPath) && file_exists($caCertPath)) {
            $config['verify'] = $caCertPath;
        } else {
            // Untuk development, disable SSL verification jika CA cert tidak ditemukan
            // PERINGATAN: Jangan gunakan di production!
            $config['verify'] = false;
            log_message('warning', 'IslamicApiService: CA certificate tidak ditemukan, SSL verification dinonaktifkan. Ini tidak aman untuk production!');
        }

        $this->client = Services::curlrequest($config);
    }

    /**
     * Mendapatkan jadwal sholat berdasarkan nama kota
     * 
     * @param string $city Nama kota (contoh: "Jakarta", "Bandung")
     * @param string|null $date Tanggal dalam format YYYY-MM-DD (opsional, default hari ini)
     * @return array Data jadwal sholat
     * @throws Exception
     */
    public function getPrayerTimesByCity(string $city, ?string $date = null): array
    {
        try {
            if (empty($city)) {
                throw new Exception('Nama kota tidak boleh kosong');
            }

            $url = self::MUSLIMSALAT_BASE_URL . '/' . urlencode($city) . '.json';
            
            if ($date) {
                $url .= '?key=free&date=' . $date;
            } else {
                $url .= '?key=free';
            }

            $response = $this->client->get($url);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ($statusCode !== 200) {
                throw new Exception("Gagal mengambil data jadwal sholat. Status code: {$statusCode}");
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Gagal memparse response JSON: ' . json_last_error_msg());
            }

            if (!isset($data['items']) || empty($data['items'])) {
                throw new Exception('Data jadwal sholat tidak ditemukan untuk kota: ' . $city);
            }

            return [
                'success' => true,
                'city' => $data['query'] ?? $city,
                'country' => $data['country'] ?? '',
                'timezone' => $data['timezone'] ?? '',
                'date' => $data['items'][0]['date_for'] ?? date('Y-m-d'),
                'prayer_times' => [
                    'fajr' => $data['items'][0]['fajr'] ?? '',
                    'shurooq' => $data['items'][0]['shurooq'] ?? '',
                    'dhuhr' => $data['items'][0]['dhuhr'] ?? '',
                    'asr' => $data['items'][0]['asr'] ?? '',
                    'maghrib' => $data['items'][0]['maghrib'] ?? '',
                    'isha' => $data['items'][0]['isha'] ?? '',
                ],
                'raw_data' => $data
            ];
        } catch (Exception $e) {
            log_message('error', 'IslamicApiService::getPrayerTimesByCity - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'city' => $city
            ];
        }
    }

    /**
     * Mendapatkan jadwal sholat berdasarkan koordinat (latitude, longitude)
     * 
     * @param float $lat Latitude
     * @param float $long Longitude
     * @param string|null $date Tanggal dalam format YYYY-MM-DD (opsional, default hari ini)
     * @return array Data jadwal sholat
     * @throws Exception
     */
    public function getPrayerTimesByCoordinate(float $lat, float $long, ?string $date = null): array
    {
        try {
            if (!is_numeric($lat) || !is_numeric($long)) {
                throw new Exception('Koordinat tidak valid');
            }

            $url = self::MUSLIMSALAT_BASE_URL . '/' . $lat . ',' . $long . '.json';
            
            if ($date) {
                $url .= '?key=free&date=' . $date;
            } else {
                $url .= '?key=free';
            }

            $response = $this->client->get($url);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ($statusCode !== 200) {
                throw new Exception("Gagal mengambil data jadwal sholat. Status code: {$statusCode}");
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Gagal memparse response JSON: ' . json_last_error_msg());
            }

            if (!isset($data['items']) || empty($data['items'])) {
                throw new Exception('Data jadwal sholat tidak ditemukan untuk koordinat: ' . $lat . ',' . $long);
            }

            return [
                'success' => true,
                'latitude' => $lat,
                'longitude' => $long,
                'timezone' => $data['timezone'] ?? '',
                'date' => $data['items'][0]['date_for'] ?? date('Y-m-d'),
                'prayer_times' => [
                    'fajr' => $data['items'][0]['fajr'] ?? '',
                    'shurooq' => $data['items'][0]['shurooq'] ?? '',
                    'dhuhr' => $data['items'][0]['dhuhr'] ?? '',
                    'asr' => $data['items'][0]['asr'] ?? '',
                    'maghrib' => $data['items'][0]['maghrib'] ?? '',
                    'isha' => $data['items'][0]['isha'] ?? '',
                ],
                'raw_data' => $data
            ];
        } catch (Exception $e) {
            log_message('error', 'IslamicApiService::getPrayerTimesByCoordinate - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'latitude' => $lat,
                'longitude' => $long
            ];
        }
    }

    /**
     * Mendapatkan data surah lengkap berdasarkan nomor surah
     * 
     * @param int $surahNumber Nomor surah (1-114)
     * @param string $edition Edition/edisi (default: 'quran-uthmani')
     * @return array Data surah
     * @throws Exception
     */
    public function getSurah(int $surahNumber, string $edition = 'quran-uthmani'): array
    {
        try {
            if ($surahNumber < 1 || $surahNumber > 114) {
                throw new Exception('Nomor surah harus antara 1-114');
            }

            $url = self::QURAN_BASE_URL . '/surah/' . $surahNumber . '/' . $edition;

            $response = $this->client->get($url);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ($statusCode !== 200) {
                throw new Exception("Gagal mengambil data surah. Status code: {$statusCode}");
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Gagal memparse response JSON: ' . json_last_error_msg());
            }

            if (!isset($data['data']) || !isset($data['data']['ayahs'])) {
                throw new Exception('Data surah tidak ditemukan untuk surah: ' . $surahNumber);
            }

            $surahData = $data['data'];

            return [
                'success' => true,
                'surah_number' => $surahNumber,
                'surah_name' => $surahData['name'] ?? '',
                'surah_name_arabic' => $surahData['name'] ?? '',
                'surah_name_english' => $surahData['englishName'] ?? '',
                'surah_name_english_translation' => $surahData['englishNameTranslation'] ?? '',
                'number_of_ayahs' => $surahData['numberOfAyahs'] ?? 0,
                'revelation_type' => $surahData['revelationType'] ?? '',
                'ayahs' => $surahData['ayahs'] ?? [],
                'raw_data' => $data
            ];
        } catch (Exception $e) {
            log_message('error', 'IslamicApiService::getSurah - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'surah_number' => $surahNumber
            ];
        }
    }

    /**
     * Mendapatkan range ayat berdasarkan nomor surah dan range ayat
     * 
     * @param int $surah Nomor surah (1-114)
     * @param int $ayahStart Ayat awal
     * @param int $ayahEnd Ayat akhir
     * @param string $edition Edition/edisi (default: 'quran-uthmani')
     * @return array Data range ayat
     * @throws Exception
     */
    public function getAyahRange(int $surah, int $ayahStart, int $ayahEnd, string $edition = 'quran-uthmani'): array
    {
        try {
            if ($surah < 1 || $surah > 114) {
                throw new Exception('Nomor surah harus antara 1-114');
            }

            if ($ayahStart < 1 || $ayahEnd < 1) {
                throw new Exception('Nomor ayat harus lebih dari 0');
            }

            if ($ayahStart > $ayahEnd) {
                throw new Exception('Ayat awal tidak boleh lebih besar dari ayat akhir');
            }

            // Batasi range maksimal untuk menghindari request terlalu banyak
            $maxRange = 50;
            if (($ayahEnd - $ayahStart + 1) > $maxRange) {
                throw new Exception('Range ayat terlalu besar. Maksimal ' . $maxRange . ' ayat per request');
            }

            $ayahs = [];
            $surahInfo = [];
            $firstAyahData = null;

            // Ambil setiap ayat dalam range secara individual
            // Ini lebih reliable karena API mungkin tidak selalu mengembalikan array untuk range
            for ($ayahNum = $ayahStart; $ayahNum <= $ayahEnd; $ayahNum++) {
                try {
                    $url = self::QURAN_BASE_URL . '/ayah/' . $surah . ':' . $ayahNum . '/' . $edition;
                    
                    $response = $this->client->get($url);
                    $statusCode = $response->getStatusCode();
                    $body = $response->getBody();

                    if ($statusCode === 200) {
                        $ayahData = json_decode($body, true);
                        
                        if (json_last_error() === JSON_ERROR_NONE && isset($ayahData['data'])) {
                            $ayahItem = $ayahData['data'];
                            
                            // Simpan info surah dari ayat pertama
                            if ($ayahNum === $ayahStart && isset($ayahItem['surah'])) {
                                $surahInfo = $ayahItem['surah'];
                                $firstAyahData = $ayahItem;
                            }
                            
                            $ayahs[] = [
                                'surah_number' => $surah,
                                'ayah_number' => $ayahItem['numberInSurah'] ?? $ayahNum,
                                'text' => $ayahItem['text'] ?? '',
                                'number' => $ayahItem['number'] ?? 0,
                                'number_in_surah' => $ayahItem['numberInSurah'] ?? $ayahNum,
                                'juz' => $ayahItem['juz'] ?? 0,
                                'manzil' => $ayahItem['manzil'] ?? 0,
                                'page' => $ayahItem['page'] ?? 0,
                                'ruku' => $ayahItem['ruku'] ?? 0,
                                'hizb_quarter' => $ayahItem['hizbQuarter'] ?? 0,
                            ];
                        }
                    } else {
                        log_message('warning', "Gagal mengambil ayat {$surah}:{$ayahNum}. Status: {$statusCode}");
                    }
                } catch (Exception $e) {
                    log_message('warning', "Error mengambil ayat {$surah}:{$ayahNum} - " . $e->getMessage());
                    // Lanjutkan ke ayat berikutnya meskipun ada error
                    continue;
                }
            }

            if (empty($ayahs)) {
                throw new Exception('Tidak ada ayat yang berhasil diambil untuk surah ' . $surah . ' ayat ' . $ayahStart . '-' . $ayahEnd);
            }

            // Jika surah info masih kosong, ambil dari surah lengkap
            if (empty($surahInfo)) {
                try {
                    $surahData = $this->getSurah($surah, $edition);
                    if ($surahData['success']) {
                        $surahInfo = [
                            'name' => $surahData['surah_name_arabic'] ?? '',
                            'englishName' => $surahData['surah_name_english'] ?? ''
                        ];
                    }
                } catch (Exception $e) {
                    log_message('warning', 'Gagal mengambil info surah: ' . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'surah_number' => $surah,
                'ayah_start' => $ayahStart,
                'ayah_end' => $ayahEnd,
                'surah_name' => $surahInfo['name'] ?? '',
                'surah_name_english' => $surahInfo['englishName'] ?? '',
                'total_ayahs' => count($ayahs),
                'ayahs' => $ayahs,
                'raw_data' => ['note' => 'Data diambil per ayat dalam range']
            ];
        } catch (Exception $e) {
            log_message('error', 'IslamicApiService::getAyahRange - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'surah' => $surah,
                'ayah_start' => $ayahStart,
                'ayah_end' => $ayahEnd
            ];
        }
    }

    /**
     * Mendapatkan ayat spesifik berdasarkan nomor surah dan nomor ayat
     * 
     * @param int $surah Nomor surah (1-114)
     * @param int $ayah Nomor ayat
     * @param string $edition Edition/edisi (default: 'quran-uthmani')
     * @return array Data ayat
     * @throws Exception
     */
    public function getAyah(int $surah, int $ayah, string $edition = 'quran-uthmani'): array
    {
        try {
            if ($surah < 1 || $surah > 114) {
                throw new Exception('Nomor surah harus antara 1-114');
            }

            if ($ayah < 1) {
                throw new Exception('Nomor ayat harus lebih dari 0');
            }

            $url = self::QURAN_BASE_URL . '/ayah/' . $surah . ':' . $ayah . '/' . $edition;

            $response = $this->client->get($url);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ($statusCode !== 200) {
                throw new Exception("Gagal mengambil data ayat. Status code: {$statusCode}");
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Gagal memparse response JSON: ' . json_last_error_msg());
            }

            if (!isset($data['data'])) {
                throw new Exception('Data ayat tidak ditemukan untuk surah ' . $surah . ' ayat ' . $ayah);
            }

            $ayahData = $data['data'];

            return [
                'success' => true,
                'surah_number' => $surah,
                'ayah_number' => $ayah,
                'surah_name' => $ayahData['surah']['name'] ?? '',
                'surah_name_english' => $ayahData['surah']['englishName'] ?? '',
                'text' => $ayahData['text'] ?? '',
                'number' => $ayahData['number'] ?? 0,
                'number_in_surah' => $ayahData['numberInSurah'] ?? 0,
                'juz' => $ayahData['juz'] ?? 0,
                'manzil' => $ayahData['manzil'] ?? 0,
                'page' => $ayahData['page'] ?? 0,
                'ruku' => $ayahData['ruku'] ?? 0,
                'hizb_quarter' => $ayahData['hizbQuarter'] ?? 0,
                'raw_data' => $data
            ];
        } catch (Exception $e) {
            log_message('error', 'IslamicApiService::getAyah - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'surah' => $surah,
                'ayah' => $ayah
            ];
        }
    }

    /**
     * Mencari kata kunci dalam Al-Qur'an
     * 
     * @param string $keyword Kata kunci yang dicari
     * @param string $edition Edition/edisi (default: 'quran-uthmani')
     * @return array Hasil pencarian
     * @throws Exception
     */
    public function searchQuran(string $keyword, string $edition = 'quran-uthmani'): array
    {
        try {
            if (empty(trim($keyword))) {
                throw new Exception('Kata kunci tidak boleh kosong');
            }

            // Coba beberapa format endpoint search
            $endpoints = [
                self::QURAN_BASE_URL . '/search/' . urlencode($keyword) . '/all/' . $edition,
                self::QURAN_BASE_URL . '/search/' . urlencode($keyword) . '/' . $edition,
                self::QURAN_BASE_URL . '/search/' . urlencode($keyword),
            ];

            $lastError = null;
            $data = null;

            // Coba setiap endpoint sampai berhasil
            foreach ($endpoints as $url) {
                try {
                    $response = $this->client->get($url);
                    $statusCode = $response->getStatusCode();
                    $body = $response->getBody();

                    if ($statusCode === 200) {
                        $data = json_decode($body, true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($data)) {
                            break; // Berhasil, keluar dari loop
                        }
                    }
                    $lastError = "Status code: {$statusCode}";
                } catch (Exception $e) {
                    $lastError = $e->getMessage();
                    continue; // Coba endpoint berikutnya
                }
            }

            // Jika semua endpoint gagal, gunakan metode pencarian manual
            if (!$data || !isset($data['data'])) {
                log_message('info', 'IslamicApiService::searchQuran - Endpoint search tidak tersedia, menggunakan pencarian manual');
                return $this->searchQuranManual($keyword, $edition);
            }

            // Proses hasil dari API
            if (!isset($data['data']['matches']) && !isset($data['data'])) {
                return [
                    'success' => true,
                    'keyword' => $keyword,
                    'total_results' => 0,
                    'matches' => []
                ];
            }

            $matches = $data['data']['matches'] ?? [];
            if (empty($matches) && isset($data['data']) && is_array($data['data'])) {
                // Format response mungkin berbeda
                $matches = $data['data'];
            }

            return [
                'success' => true,
                'keyword' => $keyword,
                'total_results' => count($matches),
                'matches' => $matches,
                'raw_data' => $data
            ];
        } catch (Exception $e) {
            log_message('error', 'IslamicApiService::searchQuran - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'keyword' => $keyword
            ];
        }
    }

    /**
     * Pencarian manual dengan mencari di beberapa surah populer
     * Fallback jika endpoint search tidak tersedia
     * 
     * @param string $keyword Kata kunci
     * @param string $edition Edition
     * @return array Hasil pencarian
     */
    private function searchQuranManual(string $keyword, string $edition = 'quran-uthmani'): array
    {
        $matches = [];
        $keywordLower = mb_strtolower($keyword, 'UTF-8');
        
        // Cari di beberapa surah populer (1-10, 2, 36, 55, 67, 78, 112)
        $popularSurahs = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 36, 55, 67, 78, 112];
        $maxResults = 50; // Batasi hasil untuk performa
        
        foreach ($popularSurahs as $surahNum) {
            if (count($matches) >= $maxResults) {
                break;
            }
            
            try {
                $surahData = $this->getSurah($surahNum, $edition);
                if (!$surahData['success'] || empty($surahData['ayahs'])) {
                    continue;
                }
                
                foreach ($surahData['ayahs'] as $ayah) {
                    if (count($matches) >= $maxResults) {
                        break 2;
                    }
                    
                    $text = $ayah['text'] ?? '';
                    // Cek apakah keyword ada dalam teks (case insensitive untuk teks Arab)
                    if (!empty($text) && (
                        stripos($text, $keyword) !== false || 
                        stripos($text, $keywordLower) !== false ||
                        mb_stripos($text, $keyword, 0, 'UTF-8') !== false
                    )) {
                        $matches[] = [
                            'text' => $text,
                            'number' => $ayah['number'] ?? 0,
                            'numberInSurah' => $ayah['numberInSurah'] ?? 0,
                            'surah' => [
                                'number' => $surahNum,
                                'name' => $surahData['surah_name'] ?? '',
                                'englishName' => $surahData['surah_name_english'] ?? ''
                            ]
                        ];
                    }
                }
            } catch (Exception $e) {
                log_message('debug', 'IslamicApiService::searchQuranManual - Error pada surah ' . $surahNum . ': ' . $e->getMessage());
                continue;
            }
        }
        
        return [
            'success' => true,
            'keyword' => $keyword,
            'total_results' => count($matches),
            'matches' => $matches,
            'note' => 'Pencarian dilakukan pada surah populer karena endpoint search API tidak tersedia'
        ];
    }
}

