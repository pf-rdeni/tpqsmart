<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Libraries\IslamicApiService;

/**
 * IslamicController
 * 
 * Controller untuk menangani request terkait Jadwal Sholat dan Al-Qur'an
 * 
 * @package App\Controllers\Backend
 */
class IslamicController extends BaseController
{
    /**
     * Instance dari IslamicApiService
     */
    protected $islamicApiService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->islamicApiService = new IslamicApiService();
    }

    /**
     * Mendapatkan jadwal sholat berdasarkan nama kota
     * Endpoint: /jadwal-sholat/{city}
     * 
     * @param string|null $city Nama kota
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function jadwalSholatByCity(?string $city = null)
    {
        // Jika city tidak ada di URL, coba ambil dari query string
        if (empty($city)) {
            $city = $this->request->getGet('city');
        }

        // Jika masih kosong, gunakan default
        if (empty($city)) {
            $city = 'Jakarta';
        }

        $date = $this->request->getGet('date'); // Opsional: ?date=2024-01-15

        $result = $this->islamicApiService->getPrayerTimesByCity($city, $date);

        // Jika request meminta JSON
        if ($this->request->getHeaderLine('Accept') === 'application/json' || 
            $this->request->getGet('format') === 'json') {
            return $this->response->setJSON($result);
        }

        // Jika request meminta view
        $data = [
            'page_title' => 'Jadwal Sholat - ' . $city,
            'result' => $result,
            'city' => $city,
            'date' => $date ?? date('Y-m-d')
        ];

        return view('backend/jadwalSholat/jadwal_sholat', $data);
    }

    /**
     * Mendapatkan jadwal sholat berdasarkan koordinat
     * Endpoint: /jadwal-sholat/{lat}/{long}
     * 
     * @param string|null $lat Latitude (dari route segment)
     * @param string|null $long Longitude (dari route segment)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function jadwalSholatByCoordinate(?string $lat = null, ?string $long = null)
    {
        // Jika lat/long tidak ada di URL, coba ambil dari query string
        if (empty($lat) || empty($long)) {
            $lat = $this->request->getGet('lat');
            $long = $this->request->getGet('long');
        }

        // Validasi bahwa lat dan long adalah angka
        if (empty($lat) || empty($long) || !is_numeric($lat) || !is_numeric($long)) {
            $result = [
                'success' => false,
                'error' => 'Koordinat (latitude dan longitude) harus berupa angka yang valid'
            ];
            
            if ($this->request->getHeaderLine('Accept') === 'application/json' || 
                $this->request->getGet('format') === 'json') {
                return $this->response->setJSON($result);
            }

            $data = [
                'page_title' => 'Jadwal Sholat - Koordinat',
                'result' => $result
            ];
            return view('backend/jadwalSholat/jadwal_sholat', $data);
        }

        $date = $this->request->getGet('date'); // Opsional

        $result = $this->islamicApiService->getPrayerTimesByCoordinate((float)$lat, (float)$long, $date);

        // Jika request meminta JSON
        if ($this->request->getHeaderLine('Accept') === 'application/json' || 
            $this->request->getGet('format') === 'json') {
            return $this->response->setJSON($result);
        }

        // Jika request meminta view
        $data = [
            'page_title' => 'Jadwal Sholat - Koordinat',
            'result' => $result,
            'lat' => $lat,
            'long' => $long,
            'date' => $date ?? date('Y-m-d')
        ];

        return view('backend/jadwalSholat/jadwal_sholat', $data);
    }

    /**
     * Mendapatkan data surah lengkap
     * Endpoint: /surah/{id}
     * 
     * @param int|null $id Nomor surah (1-114)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function surah(?int $id = null)
    {
        // Jika id tidak ada di URL, coba ambil dari query string
        if (empty($id)) {
            $id = $this->request->getGet('id');
        }

        if (empty($id) || $id < 1 || $id > 114) {
            $result = [
                'success' => false,
                'error' => 'Nomor surah harus antara 1-114'
            ];
            
            if ($this->request->getHeaderLine('Accept') === 'application/json' || 
                $this->request->getGet('format') === 'json') {
                return $this->response->setJSON($result);
            }

            $data = [
                'page_title' => 'Surah Al-Qur\'an',
                'result' => $result
            ];
            return view('backend/quran/surah', $data);
        }

        $edition = $this->request->getGet('edition') ?? 'quran-uthmani'; // Opsional

        $result = $this->islamicApiService->getSurah((int)$id, $edition);

        // Jika request meminta JSON
        if ($this->request->getHeaderLine('Accept') === 'application/json' || 
            $this->request->getGet('format') === 'json') {
            return $this->response->setJSON($result);
        }

        // Jika request meminta view
        $data = [
            'page_title' => 'Surah ' . ($result['surah_name_english'] ?? 'Al-Qur\'an'),
            'result' => $result,
            'surah_id' => $id
        ];

        return view('backend/quran/surah', $data);
    }

    /**
     * Mendapatkan ayat spesifik atau range ayat
     * Endpoint: /surah/{id}/{ayah} atau /surah/{id}/{ayahStart}/{ayahEnd}
     * 
     * @param int|null $id Nomor surah (1-114)
     * @param int|null $ayah Nomor ayat atau ayat awal
     * @param int|null $ayahEnd Nomor ayat akhir (opsional, untuk range)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function ayah(?int $id = null, ?int $ayah = null, ?int $ayahEnd = null)
    {
        // Jika id/ayah tidak ada di URL, coba ambil dari query string
        if (empty($id) || empty($ayah)) {
            $id = $this->request->getGet('surah');
            $ayah = $this->request->getGet('ayah');
            $ayahEnd = $this->request->getGet('ayah_end');
        }

        if (empty($id) || $id < 1 || $id > 114) {
            $result = [
                'success' => false,
                'error' => 'Nomor surah harus antara 1-114'
            ];
            
            if ($this->request->getHeaderLine('Accept') === 'application/json' || 
                $this->request->getGet('format') === 'json') {
                return $this->response->setJSON($result);
            }

            $data = [
                'page_title' => 'Ayat Al-Qur\'an',
                'result' => $result
            ];
            return view('backend/quran/ayah', $data);
        }

        if (empty($ayah) || $ayah < 1) {
            $result = [
                'success' => false,
                'error' => 'Nomor ayat harus lebih dari 0'
            ];
            
            if ($this->request->getHeaderLine('Accept') === 'application/json' || 
                $this->request->getGet('format') === 'json') {
                return $this->response->setJSON($result);
            }

            $data = [
                'page_title' => 'Ayat Al-Qur\'an',
                'result' => $result
            ];
            return view('backend/quran/ayah', $data);
        }

        $edition = $this->request->getGet('edition') ?? 'quran-uthmani'; // Opsional

        // Cek apakah ini range ayat atau single ayat
        if (!empty($ayahEnd) && $ayahEnd > $ayah) {
            // Range ayat
            $result = $this->islamicApiService->getAyahRange((int)$id, (int)$ayah, (int)$ayahEnd, $edition);
            $isRange = true;
        } else {
            // Single ayat
            $result = $this->islamicApiService->getAyah((int)$id, (int)$ayah, $edition);
            $isRange = false;
        }

        // Jika request meminta JSON
        if ($this->request->getHeaderLine('Accept') === 'application/json' || 
            $this->request->getGet('format') === 'json') {
            return $this->response->setJSON($result);
        }

        // Jika request meminta view
        $data = [
            'page_title' => 'Surah ' . ($result['surah_name_english'] ?? '') . 
                           ($isRange ? ' Ayat ' . $ayah . '-' . $ayahEnd : ' Ayat ' . $ayah),
            'result' => $result,
            'surah_id' => $id,
            'ayah_id' => $ayah,
            'ayah_end' => $ayahEnd,
            'is_range' => $isRange
        ];

        return view('backend/quran/ayah', $data);
    }

    /**
     * Mencari kata kunci dalam Al-Qur'an
     * Endpoint: /quran/search?keyword=xxx
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function searchQuran()
    {
        $keyword = $this->request->getGet('keyword');

        if (empty($keyword)) {
            $result = [
                'success' => false,
                'error' => 'Parameter keyword harus diisi'
            ];
            
            if ($this->request->getHeaderLine('Accept') === 'application/json' || 
                $this->request->getGet('format') === 'json') {
                return $this->response->setJSON($result);
            }

            $data = [
                'page_title' => 'Pencarian Al-Qur\'an',
                'result' => $result,
                'keyword' => ''
            ];
            return view('backend/quran/search', $data);
        }

        $edition = $this->request->getGet('edition') ?? 'quran-uthmani'; // Opsional

        $result = $this->islamicApiService->searchQuran($keyword, $edition);

        // Jika request meminta JSON
        if ($this->request->getHeaderLine('Accept') === 'application/json' || 
            $this->request->getGet('format') === 'json') {
            return $this->response->setJSON($result);
        }

        // Jika request meminta view
        $data = [
            'page_title' => 'Pencarian Al-Qur\'an: ' . $keyword,
            'result' => $result,
            'keyword' => $keyword
        ];

        return view('backend/quran/search', $data);
    }
}

