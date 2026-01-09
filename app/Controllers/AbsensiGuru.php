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
        if (!$token) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 1. Find activity by Token (regardless of IsActive status)
        $kegiatan = $this->kegiatanModel->where('Token', $token)->first();

        // 2. Validate: Token Not Found/Invalid
        if (!$kegiatan) {
            return view('frontend/absensi/error', [
                'errorType' => 'invalid_token',
                'kegiatan' => null,
                'page_title' => 'Link Tidak Valid'
            ]);
        }

        // 3. Validate: Activity Not Active
        if ($kegiatan['IsActive'] != 1) {
            return view('frontend/absensi/error', [
                'errorType' => 'inactive',
                'kegiatan' => $kegiatan,
                'page_title' => 'Kegiatan Belum Aktif'
            ]);
        }

        // 4. Calculate current occurrence date
        $currentOccurrence = $this->calculateCurrentOccurrence($kegiatan);
        
        if (!$currentOccurrence) {
            // Calculate next occurrence for countdown
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

        // 5. Validate: Time Range for current occurrence
        $occurrenceDate = $currentOccurrence['date'];
        $startTime = $kegiatan['JamMulai'];
        $endTime = $kegiatan['JamSelesai'];
        
        $activityStart = strtotime("$occurrenceDate $startTime");
        $activityEnd = strtotime("$occurrenceDate $endTime");
        $now = time();

        // 5a. Check if accessing BEFORE start time
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
        // 5b. Check if accessing AFTER end time
        if ($now > $activityEnd) {
            $errorData = [
                'errorType' => 'after_end',
                'kegiatan' => $kegiatan,
                'page_title' => 'Sudah Berakhir'
            ];

            // If recurring, calculate next occurrence for info
            if (($kegiatan['JenisJadwal'] ?? 'sekali') !== 'sekali') {
                 $nextDate = $this->calculateNextOccurrence($kegiatan);
                 if ($nextDate) {
                     $errorData['nextOccurrence'] = $nextDate;
                 }
            }

            return view('frontend/absensi/error', $errorData);
        }

        // 6. Fetch Attendance Records for current occurrence
        $idKegiatan = $kegiatan['Id'];
        $idTpqFilter = null;
        
        if ($kegiatan['Lingkup'] == 'TPQ' && !empty($kegiatan['IdTpq'])) {
            $idTpqFilter = $kegiatan['IdTpq'];
        }

        // Get or create attendance records for this occurrence
        $attendanceRecords = $this->getOrCreateAttendanceForOccurrence(
            $idKegiatan,
            $occurrenceDate,
            $kegiatan['Lingkup'],
            $idTpqFilter
        );

        // 3. Separate into Present (Hadir) and Not Present (Alfa, Izin, Sakit)
        // NOTE: Plan said default default 'Alfa'. When user clicks 'Hadir', status becomes 'Hadir'.
        // What if status is 'Izin' or 'Sakit'? Should they be in "Sudah Absen" list?
        // Usually "Sudah Absen" implies "Hadir".
        // But let's assume "Belum Hadir" list contains 'Alfa'.
        // "Sudah Hadir" list contains 'Hadir'.
        // What about 'Izin'/'Sakit'?
        // Flowchart said: Display List: Belum Hadir -> Click Hadir -> Update Status.
        // So simple logic: Status == 'Alfa' -> Belum Hadir list.
        // Status != 'Alfa' -> Sudah Hadir list.

        $belumHadir = [];
        $sudahHadir = [];

        foreach ($attendanceRecords as $record) {
            // Check status. Accessing object properties as getAbsensiByKegiatan returns array of OBJECTS
            // Wait, getAbsensiByKegiatan uses findAll() in Model which respects returnType.
            // AbsensiGuruModel returnType defined as 'object'.
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
                // Assuming Alfa or empty is Alfa
                $stats['alfa']++;
                $statsTpq[$tpqName]['alfa']++;
            }
        }


        // Sort TPQ stats by name (optional)
        ksort($statsTpq);

        // Prepare location data for map visualization
        $locationData = [];
        foreach ($sudahHadir as $guru) {
            // Only include records with valid coordinates
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
            'belumHadir' => $belumHadir, // Still used for list display
            'sudahHadir' => $sudahHadir, // Still used for list display
            'stats'      => $stats,
            'statsTpq'   => $statsTpq,   // Passed to view
            'locationData' => $locationData, // For map visualization
            'page_title' => 'Absensi Guru'
        ];

        return view('frontend/absensi/index', $data);
    }

    public function hadir()
    {
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

        // Default to Hadir if not specified (backward compatibility)
        if (!$status) {
            $status = 'Hadir';
        }

        // Validate status
        if (!in_array($status, ['Hadir', 'Izin', 'Sakit'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid']);
        }

        $data = [
            'StatusKehadiran' => $status,
            'WaktuAbsen'      => date('Y-m-d H:i:s'),
            'Keterangan'      => $keterangan
        ];

        // Add location data if provided
        if ($latitude !== null && $latitude !== '' && $longitude !== null && $longitude !== '') {
            // Basic validation for latitude and longitude ranges
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
     * Calculate the current occurrence date for a given activity
     * Returns array with 'date' key or null if no valid occurrence today
     */
    protected function calculateCurrentOccurrence($kegiatan)
    {
        $today = date('Y-m-d');
        $jenisJadwal = $kegiatan['JenisJadwal'] ?? 'sekali';
        
        // Basic date range check
        if (!empty($kegiatan['TanggalMulaiRutin']) && $today < $kegiatan['TanggalMulaiRutin']) {
            return null;
        }

        // Check End Condition
        $jenisBatas = $kegiatan['JenisBatasAkhir'] ?? 'Tanggal';
        if ($jenisBatas === 'Tanggal' && !empty($kegiatan['TanggalAkhirRutin']) && $today > $kegiatan['TanggalAkhirRutin']) {
            return null;
        }
        
        // Interval Logic
        $interval = max(1, (int)($kegiatan['Interval'] ?? 1));
        $startDate = $kegiatan['TanggalMulaiRutin'];

        switch ($jenisJadwal) {
            case 'sekali':
                return ($kegiatan['Tanggal'] == $today) ? ['date' => $kegiatan['Tanggal']] : null;
                
            case 'harian':
                // (Diff Days) % Interval == 0
                $diffDays = (strtotime($today) - strtotime($startDate)) / (60 * 60 * 24);
                if ($diffDays >= 0 && $diffDays % $interval == 0) {
                     // Check 'Kejadian' Limit
                     if ($jenisBatas === 'Kejadian') {
                         $currentOccurrenceNum = ($diffDays / $interval) + 1;
                         if ($currentOccurrenceNum > $kegiatan['JumlahKejadian']) return null;
                     }
                     return ['date' => $today];
                }
                break;
                
            case 'mingguan':
                // Check Day Match
                $allowedDays = explode(',', $kegiatan['HariDalamMinggu'] ?? '');
                $todayDayOfWeek = date('N'); // 1-7
                
                if (!in_array($todayDayOfWeek, $allowedDays)) {
                    return null;
                }
                
                // Check Week Interval
                $startWeekMonday = date('Y-m-d', strtotime('last monday', strtotime("$startDate +1 day")));
                if (date('N', strtotime($startDate)) == 1) $startWeekMonday = $startDate;
                
                $todayMonday = date('Y-m-d', strtotime('last monday', strtotime("$today +1 day")));
                if (date('N', strtotime($today)) == 1) $todayMonday = $today;
                
                $diffWeeks = (strtotime($todayMonday) - strtotime($startWeekMonday)) / (60 * 60 * 24 * 7);
                
                if ($diffWeeks >= 0 && floor($diffWeeks) == $diffWeeks && $diffWeeks % $interval == 0) {
                    // Check 'Kejadian' Limit (Simple Approach: Date Limit should be used for safety)
                    if ($jenisBatas === 'Kejadian') {
                         // Rough check or assume logic handled elsewhere to set EndDate
                    }
                    return ['date' => $today];
                }
                break;
                
            case 'bulanan':
                // Check Month Interval
                $startYear = date('Y', strtotime($startDate));
                $startMonth = date('m', strtotime($startDate));
                $currYear = date('Y');
                $currMonth = date('m');
                
                $diffMonths = (($currYear - $startYear) * 12) + ($currMonth - $startMonth);
                
                if ($diffMonths >= 0 && $diffMonths % $interval == 0) {
                     // Check Pattern
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
                // Check Year Interval
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
     * Calculate the next occurrence date
     */
    protected function calculateNextOccurrence($kegiatan)
    {
        $today = date('Y-m-d');
        $jenisJadwal = $kegiatan['JenisJadwal'] ?? 'sekali';
        $startDate = $kegiatan['TanggalMulaiRutin'];
        $endDate = $kegiatan['TanggalAkhirRutin'];
        $interval = max(1, (int)($kegiatan['Interval'] ?? 1));
        $jenisBatas = $kegiatan['JenisBatasAkhir'] ?? 'Tanggal';

        // Check End Date/Limit Global Check
        if ($jenisBatas === 'Tanggal' && !empty($endDate) && $today >= $endDate) {
            return null;
        }

        // Determine Start Search Date: Tomorrow (since we want NEXT occurrence after today)
        // However, if the activity hasn't started yet, we might want the FIRST occurrence
        $searchDate = ($today < $startDate) ? $startDate : date('Y-m-d', strtotime('+1 day'));
        if ($searchDate < $startDate) $searchDate = $startDate;
        
        switch ($jenisJadwal) {
            case 'sekali':
                return ($kegiatan['Tanggal'] > $today) ? $kegiatan['Tanggal'] : null;

            case 'harian':
                $opsi = $kegiatan['OpsiPola'] ?? 'Interval';
                
                if ($opsi === 'Weekday') {
                    // Find next weekday >= searchDate
                    $nextDate = $searchDate;
                    while (date('N', strtotime($nextDate)) >= 6) { // 6=Sat, 7=Sun
                         $nextDate = date('Y-m-d', strtotime("$nextDate +1 day"));
                    }
                } else {
                    // Standard Interval Logic
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
                
                // Check End Date
                if ($jenisBatas === 'Tanggal' && !empty($endDate) && $nextDate > $endDate) return null;
                
                return $nextDate;

            case 'mingguan':
                 $allowedDays = explode(',', $kegiatan['HariDalamMinggu'] ?? '');
                 if (empty($allowedDays)) return null;
                 
                 $tempDate = strtotime($searchDate);
                 for ($i = 0; $i < 52 * 5; $i++) { // Max 5 years lookahead
                     $currDateStr = date('Y-m-d', $tempDate);
                     
                     // Check Week Interval
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
                     // Move to next Monday
                     $tempDate = strtotime('next monday', $tempDate);
                 }
                 break;

            case 'bulanan':
                 $intervalMonths = $interval;
                 
                 $currY = date('Y', strtotime($searchDate));
                 $currM = date('m', strtotime($searchDate));
                 $startY = date('Y', strtotime($startDate));
                 $startM = date('m', strtotime($startDate));
                 
                 // Calculate base offset month index
                 $startMonthIndex = ($startY * 12) + $startM;
                 $currMonthIndex = ($currY * 12) + $currM;
                 
                 // Look ahead
                 for ($i = 0; $i < 60; $i++) { // Max 5 years lookahead
                     $checkMonthIndex = $currMonthIndex + $i;
                     $diff = $checkMonthIndex - $startMonthIndex;
                     
                     if ($diff >= 0 && $diff % $intervalMonths == 0) {
                         // Valid month
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
                             // Check Kejadian limit if needed
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
                 
                 for ($i = 0; $i < 10; $i++) { // Max 10 intervals lookahead
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

        return null; // Should not reach here
    }

    /**
     * Get or create attendance records for a specific occurrence
     */
    protected function getOrCreateAttendanceForOccurrence($idKegiatan, $tanggalOccurrence, $lingkup, $idTpq)
    {
        // Check if attendance records exist for this occurrence
        $existing = $this->absensiGuruModel
            ->where('IdKegiatan', $idKegiatan)
            ->where('TanggalOccurrence', $tanggalOccurrence)
            ->findAll();
        
        if (!empty($existing)) {
            // Records exist, return them with joined data
            return $this->absensiGuruModel->getAbsensiByKegiatan($idKegiatan, $idTpq, $tanggalOccurrence);
        }
        
        // No records exist, generate them
        // Get guru list based on lingkup
        if ($lingkup == 'Umum') {
            $guruList = $this->guruModel->findAll();
        } else {
            // TPQ specific
            $guruList = $this->guruModel->where('IdTpq', $idTpq)->findAll();
        }
        
        // Generate attendance records
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
        
        // Fetch and return the newly created records with joined data
        return $this->absensiGuruModel->getAbsensiByKegiatan($idKegiatan, $idTpq, $tanggalOccurrence);
    }
    /**
     * Helper: Calculate Nth Weekday Date
     * e.g. "Second Friday of January 2024"
     */
    private function getNthWeekdayDate($year, $month, $nth, $dayIndex) {
        // nth: 1=First, 2=Second... 5=Last
        // dayIndex: 1=Mon, ..., 7=Sun
        
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
