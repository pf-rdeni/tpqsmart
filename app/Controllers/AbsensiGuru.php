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
            return view('frontend/absensi/error', [
                'errorType' => 'no_occurrence',
                'kegiatan' => $kegiatan,
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
        if ($now > $activityEnd) {
            return view('frontend/absensi/error', [
                'errorType' => 'after_end',
                'kegiatan' => $kegiatan,
                'page_title' => 'Sudah Berakhir'
            ]);
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
        
        switch ($jenisJadwal) {
            case 'sekali':
                // Original behavior: use the Tanggal field
                return ['date' => $kegiatan['Tanggal']];
                
            case 'harian':
                // Check if today is within the recurring period
                if ($today >= $kegiatan['TanggalMulaiRutin'] && 
                    (empty($kegiatan['TanggalAkhirRutin']) || $today <= $kegiatan['TanggalAkhirRutin'])) {
                    return ['date' => $today];
                }
                break;
                
            case 'mingguan':
                // Check if today's day of week matches
                $todayDayOfWeek = date('N'); // 1=Monday, 7=Sunday
                if ($todayDayOfWeek == $kegiatan['HariDalamMinggu'] &&
                    $today >= $kegiatan['TanggalMulaiRutin'] &&
                    (empty($kegiatan['TanggalAkhirRutin']) || $today <= $kegiatan['TanggalAkhirRutin'])) {
                    return ['date' => $today];
                }
                break;
                
            case 'bulanan':
                // Check if today's date matches
                $todayDate = (int)date('d');
                if ($todayDate == $kegiatan['TanggalDalamBulan'] &&
                    $today >= $kegiatan['TanggalMulaiRutin'] &&
                    (empty($kegiatan['TanggalAkhirRutin']) || $today <= $kegiatan['TanggalAkhirRutin'])) {
                    return ['date' => $today];
                }
                break;
        }
        
        return null; // No valid occurrence today
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
}
