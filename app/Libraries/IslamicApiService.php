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

            // Proses ayahs untuk menghapus bismillah dari ayat pertama
            $ayahs = $surahData['ayahs'] ?? [];
            if (!empty($ayahs) && isset($ayahs[0])) {
                // Cek apakah ayat pertama (index 0) mengandung bismillah
                $firstAyahText = $ayahs[0]['text'] ?? '';
                if (!empty($firstAyahText)) {
                    // Cek apakah text dimulai dengan بِسْمِ (bismillah)
                    $trimmedText = trim($firstAyahText);
                    $startsWithBismillah = (mb_substr($trimmedText, 0, 5) === 'بِسْمِ' || mb_strpos($trimmedText, 'بِسْمِ') === 0);

                    if ($startsWithBismillah) {
                        // Cari posisi "الرَّحِيمِ" atau variasi dengan alif wasla
                        // Urutkan dari yang paling spesifik ke yang lebih umum
                        $raheemPatterns = [
                            'حِيمِ',  // Dengan alif wasla
                            'الرَّحِيمِ',  // Standar
                            'لرَّحِيمِ'   // Tanpa alif
                        ];

                        $raheemPos = false;
                        $raheemLength = 0;

                        foreach ($raheemPatterns as $pattern) {
                            $pos = mb_strpos($firstAyahText, $pattern);
                            if ($pos !== false) {
                                $raheemPos = $pos;
                                $raheemLength = mb_strlen($pattern);
                                break;
                            }
                        }

                        if ($raheemPos !== false) {
                            // Ambil text setelah الرَّحِيمِ (mulai dari posisi setelah الرَّحِيمِ)
                            $afterRaheem = mb_substr($firstAyahText, $raheemPos + $raheemLength);
                            $afterRaheem = trim($afterRaheem);

                            // Jika ada text setelah bismillah, gunakan itu
                            if (!empty($afterRaheem)) {
                                $ayahs[0]['text'] = $afterRaheem;
                            } else {
                                // Jika tidak ada text setelahnya, berarti hanya bismillah saja
                                // Hapus seluruh bismillah dengan regex
                                $patterns = [
                                    '/بِسْمِ\s*[ٱا]?للَّهِ\s*[ٱا]?لرَّحْم[ٱا]?[َٰ]?نِ\s*[ٱا]?لرَّحِيمِ\s*/u',
                                    '/بِسْمِ.*?[ٱا]?للَّهِ.*?[ٱا]?لرَّحْم.*?[ٱا]?لرَّحِيمِ\s*/u'
                                ];

                                foreach ($patterns as $pattern) {
                                    $cleaned = preg_replace($pattern, '', $firstAyahText);
                                    if ($cleaned !== $firstAyahText) {
                                        $ayahs[0]['text'] = trim($cleaned);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return [
                'success' => true,
                'surah_number' => $surahNumber,
                'surah_name' => $surahData['name'] ?? '',
                'surah_name_arabic' => $surahData['name'] ?? '',
                'surah_name_english' => $surahData['englishName'] ?? '',
                'surah_name_english_translation' => $surahData['englishNameTranslation'] ?? '',
                'number_of_ayahs' => $surahData['numberOfAyahs'] ?? 0,
                'revelation_type' => $surahData['revelationType'] ?? '',
                'ayahs' => $ayahs,
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
     * Menggunakan parallel requests untuk performa lebih cepat
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

            // Hitung jumlah ayat
            $totalAyahs = $ayahEnd - $ayahStart + 1;

            // Jika hanya 1 ayat, gunakan method single untuk lebih cepat
            if ($totalAyahs === 1) {
                $singleAyah = $this->getAyah($surah, $ayahStart, $edition);
                if ($singleAyah['success']) {
                    return [
                        'success' => true,
                        'surah_number' => $surah,
                        'ayah_start' => $ayahStart,
                        'ayah_end' => $ayahEnd,
                        'surah_name' => $singleAyah['surah_name'] ?? '',
                        'surah_name_english' => $singleAyah['surah_name_english'] ?? '',
                        'total_ayahs' => 1,
                        'ayahs' => [[
                            'surah_number' => $surah,
                            'ayah_number' => $singleAyah['ayah_number'],
                            'text' => $singleAyah['text'],
                            'number' => $singleAyah['number'],
                            'number_in_surah' => $singleAyah['number_in_surah'],
                            'juz' => $singleAyah['juz'],
                            'manzil' => $singleAyah['manzil'],
                            'page' => $singleAyah['page'],
                            'ruku' => $singleAyah['ruku'],
                            'hizb_quarter' => $singleAyah['hizb_quarter'],
                        ]],
                        'raw_data' => ['note' => 'Single ayah request']
                    ];
                }
            }

            // Untuk multiple ayat, gunakan parallel requests dengan curl_multi
            $multiHandle = curl_multi_init();
            $curlHandles = [];
            $ayahUrls = [];

            // Get CA certificate path if available
            $caCertPath = null;
            $caCertPath = getenv('SSL_CERT_FILE');
            if (empty($caCertPath)) {
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

            // Prepare semua URL dan curl handles
            for ($ayahNum = $ayahStart; $ayahNum <= $ayahEnd; $ayahNum++) {
                $url = self::QURAN_BASE_URL . '/ayah/' . $surah . ':' . $ayahNum . '/' . $edition;
                $ayahUrls[$ayahNum] = $url;

                $ch = curl_init($url);

                // Konfigurasi curl dengan timeout yang lebih ketat untuk performa
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json',
                        'User-Agent: IslamicApiService/1.0'
                    ]
                ]);

                // SSL verification
                if (!empty($caCertPath) && file_exists($caCertPath)) {
                    curl_setopt($ch, CURLOPT_CAINFO, $caCertPath);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                } else {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                }

                curl_multi_add_handle($multiHandle, $ch);
                $curlHandles[$ayahNum] = $ch;
            }

            // Execute semua requests secara parallel
            $running = null;
            $maxWaitTime = 15; // Maximum wait time in seconds
            $startTime = time();

            do {
                $mrc = curl_multi_exec($multiHandle, $running);

                // Check timeout
                if ((time() - $startTime) > $maxWaitTime) {
                    log_message('warning', 'IslamicApiService::getAyahRange - Timeout waiting for parallel requests');
                    break;
                }

                // Wait a bit before checking again
                if ($running > 0) {
                    curl_multi_select($multiHandle, 0.1);
                }
            } while ($running > 0 && $mrc == CURLM_OK);

            // Process semua responses
            foreach ($curlHandles as $ayahNum => $ch) {
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $response = curl_multi_getcontent($ch);

                if ($httpCode === 200 && !empty($response)) {
                    $ayahData = json_decode($response, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($ayahData['data'])) {
                        $ayahItem = $ayahData['data'];

                        // Simpan info surah dari ayat pertama
                        if ($ayahNum === $ayahStart && isset($ayahItem['surah'])) {
                            $surahInfo = $ayahItem['surah'];
                            $firstAyahData = $ayahItem;
                        }

                        // Proses text ayat untuk menghapus bismillah dari ayat pertama
                        $ayahText = $ayahItem['text'] ?? '';
                        $ayahNumberInSurah = $ayahItem['numberInSurah'] ?? $ayahNum;

                        // Jika ini ayat pertama (numberInSurah = 1) dan mengandung bismillah
                        if ($ayahNumberInSurah == 1 && !empty($ayahText)) {
                            // Cek apakah text dimulai dengan بِسْمِ (bismillah)
                            $trimmedText = trim($ayahText);
                            $startsWithBismillah = (mb_substr($trimmedText, 0, 5) === 'بِسْمِ' || mb_strpos($trimmedText, 'بِسْمِ') === 0);

                            if ($startsWithBismillah) {
                                // Cari posisi "الرَّحِيمِ" atau variasi dengan alif wasla
                                // Urutkan dari yang paling spesifik ke yang lebih umum
                                $raheemPatterns = [
                                    'ٱلرَّحِيمِ',  // Dengan alif wasla
                                    'الرَّحِيمِ',  // Standar
                                    'لرَّحِيمِ'   // Tanpa alif
                                ];

                                $raheemPos = false;
                                $raheemLength = 0;

                                foreach ($raheemPatterns as $pattern) {
                                    $pos = mb_strpos($ayahText, $pattern);
                                    if ($pos !== false) {
                                        $raheemPos = $pos;
                                        $raheemLength = mb_strlen($pattern);
                                        break;
                                    }
                                }

                                if ($raheemPos !== false) {
                                    // Ambil text setelah الرَّحِيمِ (mulai dari posisi setelah الرَّحِيمِ)
                                    $afterRaheem = mb_substr($ayahText, $raheemPos + $raheemLength);
                                    $afterRaheem = trim($afterRaheem);

                                    // Jika ada text setelah bismillah, gunakan itu
                                    if (!empty($afterRaheem)) {
                                        $ayahText = $afterRaheem;
                                    } else {
                                        // Jika tidak ada text setelahnya, berarti hanya bismillah saja
                                        // Hapus seluruh bismillah dengan regex
                                        $patterns = [
                                            '/بِسْمِ\s*[ٱا]?للَّهِ\s*[ٱا]?لرَّحْم[ٱا]?[َٰ]?نِ\s*[ٱا]?لرَّحِيمِ\s*/u',
                                            '/بِسْمِ.*?[ٱا]?للَّهِ.*?[ٱا]?لرَّحْم.*?[ٱا]?لرَّحِيمِ\s*/u'
                                        ];

                                        foreach ($patterns as $pattern) {
                                            $cleaned = preg_replace($pattern, '', $ayahText);
                                            if ($cleaned !== $ayahText) {
                                                $ayahText = trim($cleaned);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $ayahs[] = [
                            'surah_number' => $surah,
                            'ayah_number' => $ayahNumberInSurah,
                            'text' => $ayahText,
                            'number' => $ayahItem['number'] ?? 0,
                            'number_in_surah' => $ayahNumberInSurah,
                            'juz' => $ayahItem['juz'] ?? 0,
                            'manzil' => $ayahItem['manzil'] ?? 0,
                            'page' => $ayahItem['page'] ?? 0,
                            'ruku' => $ayahItem['ruku'] ?? 0,
                            'hizb_quarter' => $ayahItem['hizbQuarter'] ?? 0,
                        ];
                    } else {
                        log_message('warning', "Gagal memparse response untuk ayat {$surah}:{$ayahNum}");
                    }
                } else {
                    $error = curl_error($ch);
                    log_message('warning', "Gagal mengambil ayat {$surah}:{$ayahNum}. HTTP: {$httpCode}, Error: {$error}");
                }

                curl_multi_remove_handle($multiHandle, $ch);
                curl_close($ch);
            }

            curl_multi_close($multiHandle);

            // Sort ayahs by ayah_number to ensure correct order
            usort($ayahs, function ($a, $b) {
                return $a['ayah_number'] <=> $b['ayah_number'];
            });

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
                'raw_data' => ['note' => 'Data diambil menggunakan parallel requests']
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

