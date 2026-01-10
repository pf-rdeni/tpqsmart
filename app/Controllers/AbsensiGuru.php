<?php

namespace App\Controllers;

use App\Models\KegiatanAbsensiModel;
use App\Models\AbsensiGuruModel;
use App\Models\GuruModel;

class AbsensiGuru extends BaseController
{
    protected $kegiatanModel;
    protected $absensiGuruModel;
    protected $guruModel;

    public function __construct()
    {
        $this->kegiatanModel = new KegiatanAbsensiModel();
        $this->absensiGuruModel = new AbsensiGuruModel();
        $this->guruModel = new GuruModel();
    }

    public function index($token = null)
    {
        // Penjelasan Proses:
        // 1. Validasi Token: Memastikan token ada dan valid.
        // 2. Cek Aktif: Memastikan kegiatan statusnya aktif.
        // 3. Hitung Jadwal: Menentukan apakah hari ini jadwalnya (calculateCurrentOccurrence).
        // 4. Validasi Waktu: Memastikan akses dilakukan dalam rentang jam mulai - jam selesai.
        // 5. Persiapan Data: Jika valid, ambil/buat data absensi (getOrCreateAttendanceForOccurrence) dan siapkan untuk view.
        if (!$token) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 1. Cari kegiatan berdasarkan Token (terlepas dari status IsActive)
        $kegiatan = $this->kegiatanModel->where('Token', $token)->first();

        // 2. Validasi: Token Tidak Ditemukan/Tidak Valid
        if (!$kegiatan) {
            return view('frontend/absensi/error', [
                'errorType' => 'invalid_token',
                'kegiatan' => null,
                'page_title' => 'Link Tidak Valid'
            ]);
        }

        // 3. Validasi: Kegiatan Belum Aktif
        if ($kegiatan['IsActive'] != 1) {
            return view('frontend/absensi/error', [
                'errorType' => 'inactive',
                'kegiatan' => $kegiatan,
                'page_title' => 'Kegiatan Belum Aktif'
            ]);
        }

        // 4. Hitung tanggal kejadian saat ini
        $currentOccurrence = $this->calculateCurrentOccurrence($kegiatan);
        
        if (!$currentOccurrence) {
            // Hitung kejadian berikutnya untuk hitung mundur
            $nextDate = $this->calculateNextOccurrence($kegiatan);
            $activityStart = null;
            
            if ($nextDate) {
                $activityStart = strtotime("$nextDate " . $kegiatan['JamMulai']);
            }

            return view('frontend/absensi/error', [
                'errorType' => 'no_occurrence',
                'kegiatan' => $kegiatan,
                'nextOccurrence' => $nextDate,
                'activityStart' => $activityStart,
                'page_title' => 'Tidak Ada Jadwal Hari Ini'
            ]);
        }

        // 5. Validasi: Rentang Waktu untuk kejadian saat ini
        $occurrenceDate = $currentOccurrence['date'];
        $startTime = $kegiatan['JamMulai'];
        $endTime = $kegiatan['JamSelesai'];
        
        $activityStart = strtotime("$occurrenceDate $startTime");
        $activityEnd = strtotime("$occurrenceDate $endTime");
        $now = time();

        // 5a. Periksa jika mengakses SEBELUM waktu mulai
        if ($now < $activityStart) {
            return view('frontend/absensi/error', [
                'errorType' => 'before_start',
                'kegiatan' => $kegiatan,
                'activityStart' => $activityStart,
                'currentOccurrence' => $occurrenceDate,
                'page_title' => 'Belum Dimulai'
            ]);
        }

        // 5b. Check if accessing AFTER end time
        // 5b. Periksa jika mengakses SETELAH waktu selesai
        if ($now > $activityEnd) {
            $errorData = [
                'errorType' => 'after_end',
                'kegiatan' => $kegiatan,
                'page_title' => 'Sudah Berakhir'
            ];

            // Jika berulang, hitung kejadian berikutnya untuk info
            if (($kegiatan['JenisJadwal'] ?? 'sekali') !== 'sekali') {
                 $nextDate = $this->calculateNextOccurrence($kegiatan);
                 if ($nextDate) {
                     $errorData['nextOccurrence'] = $nextDate;
                 }
            }

            return view('frontend/absensi/error', $errorData);
        }

        // 6. Ambil Data Absensi untuk kejadian saat ini
        $idKegiatan = $kegiatan['Id'];
        $idTpqFilter = null;
        
        if ($kegiatan['Lingkup'] == 'TPQ' && !empty($kegiatan['IdTpq'])) {
            $idTpqFilter = $kegiatan['IdTpq'];
        }

        // Ambil atau buat data absensi untuk kejadian ini
        $attendanceRecords = $this->getOrCreateAttendanceForOccurrence(
            $idKegiatan,
            $occurrenceDate,
            $kegiatan['Lingkup'],
            $idTpqFilter
        );

        // 3. Pisahkan menjadi Hadir dan Belum Hadir (Alfa, Izin, Sakit)
        // CATATAN: Rencana mengatakan default 'Alfa'. Ketika pengguna mengklik 'Hadir', status menjadi 'Hadir'.
        // Bagaimana jika statusnya 'Izin' atau 'Sakit'? Haruskah mereka ada di daftar "Sudah Absen"?
        // Biasanya "Sudah Absen" menyiratkan "Hadir".
        // Tapi mari kita asumsikan daftar "Belum Hadir" berisi 'Alfa'.
        // Daftar "Sudah Hadir" berisi 'Hadir'.
        // Bagaimana dengan 'Izin'/'Sakit'?
        // Flowchart mengatakan: Tampilkan Daftar: Belum Hadir -> Klik Hadir -> Update Status.
        // Jadi logika sederhana: Status == 'Alfa' -> Daftar Belum Hadir.
        // Status != 'Alfa' -> Daftar Sudah Hadir.

        $belumHadir = [];
        $sudahHadir = [];

        foreach ($attendanceRecords as $record) {
            // Periksa status. Mengakses properti objek karena getAbsensiByKegiatan mengembalikan array OBJECT
            // Tunggu, getAbsensiByKegiatan menggunakan findAll() di Model yang menghormati returnType.
            // AbsensiGuruModel returnType didefinisikan sebagai 'object'.
            $status = $record->StatusKehadiran;

            if ($status == 'Alfa') {
                $belumHadir[] = $record;
            } else {
                $sudahHadir[] = $record;
            }
        }

        $stats = [
            'total' => count($attendanceRecords),
            'hadir' => 0,
            'izin'  => 0,
            'sakit' => 0,
            'alfa'  => 0
        ];

        $statsTpq = [];

        foreach ($attendanceRecords as $record) {
            $tpqName = $record->NamaTpq ?? '-';
            if (!isset($statsTpq[$tpqName])) {
                $statsTpq[$tpqName] = [
                    'hadir' => 0,
                    'izin'  => 0,
                    'sakit' => 0,
                    'alfa'  => 0,
                    'total' => 0
                ];
            }

            $statsTpq[$tpqName]['total']++;

            if ($record->StatusKehadiran == 'Hadir') {
                $stats['hadir']++;
                $statsTpq[$tpqName]['hadir']++;
            } elseif ($record->StatusKehadiran == 'Izin') {
                $stats['izin']++;
                $statsTpq[$tpqName]['izin']++;
            } elseif ($record->StatusKehadiran == 'Sakit') {
                $stats['sakit']++;
                $statsTpq[$tpqName]['sakit']++;
            } else {
                // Asumsikan Alfa atau kosong adalah Alfa
                $stats['alfa']++;
                $statsTpq[$tpqName]['alfa']++;
            }
        }


        // Urutkan statistik TPQ berdasarkan nama (opsional)
        ksort($statsTpq);

        // Siapkan data lokasi untuk visualisasi peta
        $locationData = [];
        foreach ($sudahHadir as $guru) {
            // Hanya sertakan data dengan koordinat yang valid
            if (!empty($guru->Latitude) && !empty($guru->Longitude)) {
                $locationData[] = [
                    'lat' => floatval($guru->Latitude),
                    'lng' => floatval($guru->Longitude),
                    'nama' => $guru->NamaGuru,
                    'status' => $guru->StatusKehadiran,
                    'waktu' => date('H:i', strtotime($guru->WaktuAbsen ?? 'now')),
                    'tpq' => $guru->NamaTpq ?? '-'
                ];
            }
        }

        $data = [
            'hasAction'  => true,
            'kegiatan'   => $kegiatan,
            'belumHadir' => $belumHadir, // Masih digunakan untuk tampilan daftar
            'sudahHadir' => $sudahHadir, // Masih digunakan untuk tampilan daftar
            'stats'      => $stats,
            'statsTpq'   => $statsTpq,   // Diserahkan ke view
            'locationData' => $locationData, // Untuk visualisasi peta
            'page_title' => 'Absensi Guru'
        ];

        return view('frontend/absensi/index', $data);
    }

    public function hadir()
    {
        // Penjelasan Proses:
        // 1. Validasi Request: Harus berupa AJAX request.
        // 2. Ambil Input: ID Absensi, Status (Hadir/Izin/Sakit), Lokasi (Lat/Long).
        // 3. Validasi Data: Pastikan status valid.
        // 4. Update Database: Simpan status kehadiran dan waktu saat ini ke database.
        // 5. Response: Kembalikan JSON success/biar UI bisa update.
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }

        $idAbsensi = $this->request->getPost('id');

        if (!$idAbsensi) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID missing']);
        }

        // Update status
        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        // Default ke Hadir jika tidak ditentukan (kompatibilitas mundur)
        if (!$status) {
            $status = 'Hadir';
        }

        // Validasi status
        if (!in_array($status, ['Hadir', 'Izin', 'Sakit'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid']);
        }

        $data = [
            'StatusKehadiran' => $status,
            'WaktuAbsen'      => date('Y-m-d H:i:s'),
            'Keterangan'      => $keterangan
        ];

        // Tambahkan data lokasi jika disediakan
        if ($latitude !== null && $latitude !== '' && $longitude !== null && $longitude !== '') {
            // Validasi dasar untuk rentang latitude dan longitude
            $lat = floatval($latitude);
            $lng = floatval($longitude);
            
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                $data['Latitude'] = $lat;
                $data['Longitude'] = $lng;
            }
        }

        $update = $this->absensiGuruModel->update($idAbsensi, $data);

        if ($update) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update database']);
        }
    }

    /**
     * Hitung tanggal kejadian saat ini untuk kegiatan tertentu
     * Mengembalikan array dengan kunci 'date' atau null jika tidak ada kejadian valid hari ini
     */
    protected function calculateCurrentOccurrence($kegiatan)
    {
        // Penjelasan Proses:
        // Fungsi ini menentukan apakah "Hari Ini" adalah jadwal kegiatan yang valid berdasarkan pola jadwal.
        // 1. Cek Tanggal & Batas: Apakah hari ini dalam rentang tanggal mulai/akhir.
        // 2. Cek Pola Jadwal:
        //    - Sekali: Apakah tanggal hari ini == tanggal kegiatan.
        //    - Harian: Apakah selisih hari cocok dengan interval.
        //    - Mingguan: Apakah hari ini (Senin-Minggu) ada dalam daftar hari yang dipilih DAN interval minggu cocok.
        //    - Bulanan/Tahunan: Apakah tanggal/hari ke-n bulan ini cocok.
        $today = date('Y-m-d');
        $jenisJadwal = $kegiatan['JenisJadwal'] ?? 'sekali';
        
        // Cek rentang tanggal dasar
        if (!empty($kegiatan['TanggalMulaiRutin']) && $today < $kegiatan['TanggalMulaiRutin']) {
            return null;
        }

        // Cek Kondisi Akhir
        $jenisBatas = $kegiatan['JenisBatasAkhir'] ?? 'Tanggal';
        if ($jenisBatas === 'Tanggal' && !empty($kegiatan['TanggalAkhirRutin']) && $today > $kegiatan['TanggalAkhirRutin']) {
            return null;
        }
        
        // Logika Interval
        $interval = max(1, (int)($kegiatan['Interval'] ?? 1));
        $startDate = $kegiatan['TanggalMulaiRutin'];

        switch ($jenisJadwal) {
            case 'sekali':
                return ($kegiatan['Tanggal'] == $today) ? ['date' => $kegiatan['Tanggal']] : null;
                
            case 'harian':
                // (Selisih Hari) % Interval == 0
                $diffDays = (strtotime($today) - strtotime($startDate)) / (60 * 60 * 24);
                if ($diffDays >= 0 && $diffDays % $interval == 0) {
                     // Cek Batas 'Kejadian'
                     if ($jenisBatas === 'Kejadian') {
                         $currentOccurrenceNum = ($diffDays / $interval) + 1;
                         if ($currentOccurrenceNum > $kegiatan['JumlahKejadian']) return null;
                     }
                     return ['date' => $today];
                }
                break;
                
            case 'mingguan':
                // Cek Kecocokan Hari
                $allowedDays = explode(',', $kegiatan['HariDalamMinggu'] ?? '');
                $todayDayOfWeek = date('N'); // 1-7
                
                if (!in_array($todayDayOfWeek, $allowedDays)) {
                    return null;
                }
                
                // Cek Interval Minggu
                $startWeekMonday = date('Y-m-d', strtotime('last monday', strtotime("$startDate +1 day")));
                if (date('N', strtotime($startDate)) == 1) $startWeekMonday = $startDate;
                
                $todayMonday = date('Y-m-d', strtotime('last monday', strtotime("$today +1 day")));
                if (date('N', strtotime($today)) == 1) $todayMonday = $today;
                
                $diffWeeks = (strtotime($todayMonday) - strtotime($startWeekMonday)) / (60 * 60 * 24 * 7);
                
                if ($diffWeeks >= 0 && floor($diffWeeks) == $diffWeeks && $diffWeeks % $interval == 0) {
                    // Cek Batas 'Kejadian' (Pendekatan Sederhana: Batas Tanggal harus digunakan untuk keamanan)
                    if ($jenisBatas === 'Kejadian') {
                         // Cek kasar atau asumsikan logika ditangani di tempat lain untuk mengatur Tanggal Akhir
                    }
                    return ['date' => $today];
                }
                break;
                
            case 'bulanan':
                // Cek Interval Bulan
                $startYear = date('Y', strtotime($startDate));
                $startMonth = date('m', strtotime($startDate));
                $currYear = date('Y');
                $currMonth = date('m');
                
                $diffMonths = (($currYear - $startYear) * 12) + ($currMonth - $startMonth);
                
                if ($diffMonths >= 0 && $diffMonths % $interval == 0) {
                     // Cek Pola
                     $opsi = $kegiatan['OpsiPola'] ?? 'Tanggal';
                     
                     if ($opsi == 'Tanggal') {
                         if ((int)date('d') == $kegiatan['TanggalDalamBulan']) {
                             return ['date' => $today];
                         }
                     } else {
                         // HariKe
                         $dayIndex = (int)($kegiatan['HariDalamMinggu'] ?? 0);
                         $target = $this->getNthWeekdayDate($currYear, $currMonth, $kegiatan['PosisiMinggu'], $dayIndex);
                         if ($target == $today) return ['date' => $today];
                     }
                }
                break;

            case 'tahunan':
                // Cek Interval Tahun
                $startYear = date('Y', strtotime($startDate));
                $currYear = date('Y');
                $diffYears = $currYear - $startYear;
                
                if ($diffYears >= 0 && $diffYears % $interval == 0) {
                     $targetMonth = $kegiatan['BulanTahun'];
                     if ((int)date('n') == $targetMonth) {
                         $opsi = $kegiatan['OpsiPola'] ?? 'Tanggal';
                         
                         if ($opsi == 'Tanggal') {
                             if ((int)date('d') == $kegiatan['TanggalDalamBulan']) {
                                 return ['date' => $today];
                             }
                         } else {
                             // HariKe
                             $dayIndex = (int)($kegiatan['HariDalamMinggu'] ?? 0);
                             $target = $this->getNthWeekdayDate($currYear, $targetMonth, $kegiatan['PosisiMinggu'], $dayIndex);
                             if ($target == $today) return ['date' => $today];
                         }
                     }
                }
                break;
        }
        
        return null;
    }

    /**
     * Hitung tanggal kejadian berikutnya
     */
    protected function calculateNextOccurrence($kegiatan)
    {
        // Penjelasan Proses:
        // Fungsi ini mencari tanggal jadwal BERIKUTNYA mulai dari besok (atau hari ini jika belum mulai).
        // Digunakan untuk menampilkan "Sesi Berikutnya: [Tanggal]" jika hari ini bukan jadwalnya atau sudah lewat.
        // Logikanya meloop ke depan (hari demi hari / minggu demi minggu) sampai menemukan tanggal yang cocok dengan pola.
        $today = date('Y-m-d');
        $jenisJadwal = $kegiatan['JenisJadwal'] ?? 'sekali';
        $startDate = $kegiatan['TanggalMulaiRutin'];
        $endDate = $kegiatan['TanggalAkhirRutin'];
        $interval = max(1, (int)($kegiatan['Interval'] ?? 1));
        $jenisBatas = $kegiatan['JenisBatasAkhir'] ?? 'Tanggal';

        // Cek Batas Tanggal Akhir/Batas Global
        if ($jenisBatas === 'Tanggal' && !empty($endDate) && $today >= $endDate) {
            return null;
        }

        // Tentukan Tanggal Mulai Pencarian: Besok (karena kita menginginkan kejadian BERIKUTNYA setelah hari ini)
        // Namun, jika kegiatan belum dimulai, kita mungkin menginginkan kejadian PERTAMA
        $searchDate = ($today < $startDate) ? $startDate : date('Y-m-d', strtotime('+1 day'));
        if ($searchDate < $startDate) $searchDate = $startDate;
        
        switch ($jenisJadwal) {
            case 'sekali':
                return ($kegiatan['Tanggal'] > $today) ? $kegiatan['Tanggal'] : null;

            case 'harian':
                $opsi = $kegiatan['OpsiPola'] ?? 'Interval';
                
                if ($opsi === 'Weekday') {
                    // Cari hari kerja berikutnya >= searchDate
                    $nextDate = $searchDate;
                    while (date('N', strtotime($nextDate)) >= 6) { // 6=Sat, 7=Sun
                         $nextDate = date('Y-m-d', strtotime("$nextDate +1 day"));
                    }
                } else {
                    // Logika Interval Standar
                    $diffDays = (strtotime($searchDate) - strtotime($startDate)) / (60 * 60 * 24);
                    if ($diffDays < 0) $diffDays = 0; // Fix negative diff
                    
                    $remainder = $diffDays % $interval;
                    $daysToAdd = ($remainder == 0) ? 0 : ($interval - $remainder);
                    
                    $nextDate = date('Y-m-d', strtotime("$searchDate +$daysToAdd days"));
                }
                
                // Check 'Kejadian' Limit
                if ($jenisBatas === 'Kejadian') {
                     $diffTotal = (strtotime($nextDate) - strtotime($startDate)) / (60 * 60 * 24);
                     $occurrenceNum = ($diffTotal / $interval) + 1;
                     if ($occurrenceNum > $kegiatan['JumlahKejadian']) return null;
                }
                
                // Cek Tanggal Akhir
                if ($jenisBatas === 'Tanggal' && !empty($endDate) && $nextDate > $endDate) return null;
                
                return $nextDate;

            case 'mingguan':
                 $allowedDays = explode(',', $kegiatan['HariDalamMinggu'] ?? '');
                 if (empty($allowedDays)) return null;
                 
                 $tempDate = strtotime($searchDate);
                 for ($i = 0; $i < 52 * 5; $i++) { // Maksimal 5 tahun ke depan
                     $currDateStr = date('Y-m-d', $tempDate);
                     
                     // Cek Interval Minggu
                     $startWeekMonday = date('Y-m-d', strtotime('last monday', strtotime("$startDate +1 day")));
                     if (date('N', strtotime($startDate)) == 1) $startWeekMonday = $startDate;
                     
                     $currWeekMonday = date('Y-m-d', strtotime('last monday', strtotime("$currDateStr +1 day")));
                     if (date('N', $tempDate) == 1) $currWeekMonday = $currDateStr;
                     
                     $diffWeeks = (strtotime($currWeekMonday) - strtotime($startWeekMonday)) / (60 * 60 * 24 * 7);
                     
                     if ($diffWeeks >= 0 && floor($diffWeeks) == $diffWeeks && $diffWeeks % $interval == 0) {
                         $currDayOfWeek = date('N', $tempDate);
                         sort($allowedDays);
                         
                         foreach ($allowedDays as $day) {
                             if ($day >= $currDayOfWeek) {
                                 $daysDiff = $day - $currDayOfWeek;
                                 $candidateDate = date('Y-m-d', strtotime("+$daysDiff days", $tempDate));
                                 
                                 if ($candidateDate >= $searchDate) {
                                     if ($jenisBatas === 'Tanggal' && !empty($endDate) && $candidateDate > $endDate) return null;
                                     return $candidateDate;
                                 }
                             }
                         }
                     }
                     // Pindah ke Senin berikutnya
                     $tempDate = strtotime('next monday', $tempDate);
                 }
                 break;

            case 'bulanan':
                 $intervalMonths = $interval;
                 
                 $currY = date('Y', strtotime($searchDate));
                 $currM = date('m', strtotime($searchDate));
                 $startY = date('Y', strtotime($startDate));
                 $startM = date('m', strtotime($startDate));
                 
                 // Hitung indeks bulan offset dasar
                 $startMonthIndex = ($startY * 12) + $startM;
                 $currMonthIndex = ($currY * 12) + $currM;
                 
                 for ($i = 0; $i < 60; $i++) { // Maksimal 5 tahun ke depan
                     $checkMonthIndex = $currMonthIndex + $i;
                     $diff = $checkMonthIndex - $startMonthIndex;
                     
                     if ($diff >= 0 && $diff % $intervalMonths == 0) {
                         // Bulan valid
                         $y = floor(($checkMonthIndex - 1) / 12);
                         $m = (($checkMonthIndex - 1) % 12) + 1;
                         
                         $opsi = $kegiatan['OpsiPola'] ?? 'Tanggal';
                         
                         if ($opsi == 'Tanggal') {
                             $candidate = "$y-" . sprintf('%02d', $m) . "-" . sprintf('%02d', $kegiatan['TanggalDalamBulan']);
                             if (!checkdate($m, $kegiatan['TanggalDalamBulan'], $y)) continue;
                         } else {
                             // HariKe
                             $dayIndex = (int)($kegiatan['HariDalamMinggu'] ?? 0);
                             $candidate = $this->getNthWeekdayDate($y, $m, $kegiatan['PosisiMinggu'], $dayIndex);
                         }
                         
                         if ($candidate >= $searchDate) {
                             if ($jenisBatas === 'Tanggal' && !empty($endDate) && $candidate > $endDate) return null;
                             // Cek batas Kejadian jika diperlukan
                             if ($jenisBatas === 'Kejadian') {
                                 $occ = ($diff / $intervalMonths) + 1;
                                 if ($occ > $kegiatan['JumlahKejadian']) return null;
                             }
                             return $candidate;
                         }
                     }
                 }
                 break;

            case 'tahunan':
                 $intervalYears = $interval;
                 $currY = date('Y', strtotime($searchDate));
                 $startY = date('Y', strtotime($startDate));
                 
                 for ($i = 0; $i < 10; $i++) { // Maksimal 10 interval ke depan
                     $checkY = $currY + $i;
                     $diff = $checkY - $startY;
                     
                     if ($diff >= 0 && $diff % $intervalYears == 0) {
                         $targetMonth = $kegiatan['BulanTahun'];
                         $opsi = $kegiatan['OpsiPola'] ?? 'Tanggal';
                         
                         if ($opsi == 'Tanggal') {
                             $candidate = "$checkY-" . sprintf('%02d', $targetMonth) . "-" . sprintf('%02d', $kegiatan['TanggalDalamBulan']);
                             if (!checkdate($targetMonth, $kegiatan['TanggalDalamBulan'], $checkY)) continue;
                         } else {
                             $dayIndex = (int)($kegiatan['HariDalamMinggu'] ?? 0);
                             $candidate = $this->getNthWeekdayDate($checkY, $targetMonth, $kegiatan['PosisiMinggu'], $dayIndex);
                         }
                         
                         if ($candidate >= $searchDate) {
                             if ($jenisBatas === 'Tanggal' && !empty($endDate) && $candidate > $endDate) return null;
                             return $candidate;
                         }
                     }
                 }
                 break;
        }

        return null; // Seharusnya tidak sampai ke sini
    }

    /**
     * Ambil atau buat data absensi untuk kejadian tertentu
     */
    protected function getOrCreateAttendanceForOccurrence($idKegiatan, $tanggalOccurrence, $lingkup, $idTpq)
    {
        // Penjelasan Proses:
        // 1. Cek DB: Apakah sudah ada data absensi untuk (ID Kegiatan + Tanggal Ini)?
        // 2. Jika Ada: Langsung kembalikan datanya.
        // 3. Jika Belum:
        //    - Ambil semua Guru yang sesuai lingkup (Semua Guru atau Guru TPQ tertentu).
        //    - Buat data awal untuk setiap guru dengan status 'Alfa'.
        //    - Insert Batch ke database.
        //    - Kembalikan data yang baru dibuat.
        // Periksa apakah data absensi ada untuk kejadian ini
        $existing = $this->absensiGuruModel
            ->where('IdKegiatan', $idKegiatan)
            ->where('TanggalOccurrence', $tanggalOccurrence)
            ->findAll();
        
        if (!empty($existing)) {
            // Data ada, kembalikan dengan data gabungan
            return $this->absensiGuruModel->getAbsensiByKegiatan($idKegiatan, $idTpq, $tanggalOccurrence);
        }
        
        // Tidak ada data, buat baru
        // Ambil daftar guru berdasarkan lingkup
        if ($lingkup == 'Umum') {
            $guruList = $this->guruModel->findAll();
        } else {
            // Khusus TPQ
            $guruList = $this->guruModel->where('IdTpq', $idTpq)->findAll();
        }
        
        // Buat data absensi
        $absensiData = [];
        foreach ($guruList as $guru) {
            $absensiData[] = [
                'IdKegiatan' => $idKegiatan,
                'TanggalOccurrence' => $tanggalOccurrence,
                'IdGuru' => $guru['IdGuru'],
                'StatusKehadiran' => 'Alfa',
                'WaktuAbsen' => null,
                'Keterangan' => null,
                'Latitude' => null,
                'Longitude' => null
            ];
        }
        
        if (!empty($absensiData)) {
            $this->absensiGuruModel->insertBatch($absensiData);
        }
        
        // Ambil dan kembalikan data yang baru dibuat dengan data gabungan
        return $this->absensiGuruModel->getAbsensiByKegiatan($idKegiatan, $idTpq, $tanggalOccurrence);
    }
    /**
     * Helper: Hitung Tanggal Hari Kerja ke-N
     * mis. "Jumat Kedua bulan Januari 2024"
     */
    private function getNthWeekdayDate($year, $month, $nth, $dayIndex) {
        // Penjelasan Proses:
        // Helper untuk mencari tanggal spesifik seperti "Jumat Kedua bulan Januari".
        // Digunakan untuk pola Bulanan/Tahunan tipe "HariKe".
        // nth: 1=Pertama, 2=Kedua... 5=Terakhir
        // dayIndex: 1=Senin, ..., 7=Minggu
        
        $timestamp = mktime(0, 0, 0, $month, 1, $year);
        $monthName = date('F', $timestamp);
        $dayNames = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
        $dayName = $dayNames[$dayIndex] ?? 'Monday';
        
        if ($nth >= 5) { // Last
            return date('Y-m-d', strtotime("last $dayName of $monthName $year"));
        } else {
            $ordinals = [1=>'first', 2=>'second', 3=>'third', 4=>'fourth'];
            $ordinal = $ordinals[$nth] ?? 'first';
            return date('Y-m-d', strtotime("$ordinal $dayName of $monthName $year"));
        }
    }
}
