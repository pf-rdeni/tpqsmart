<?php

namespace App\Controllers;

use App\Models\KegiatanAbsensiModel;
use App\Models\AbsensiGuruModel;

class AbsensiGuru extends BaseController
{
    protected $kegiatanModel;
    protected $absensiGuruModel;

    public function __construct()
    {
        $this->kegiatanModel = new KegiatanAbsensiModel();
        $this->absensiGuruModel = new AbsensiGuruModel();
    }

    public function index($token = null)
    {
        if (!$token) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 1. Find active event by Token
        $kegiatan = $this->kegiatanModel->where('Token', $token)->where('IsActive', 1)->first();

        if (!$kegiatan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 2. Fetch Attendance Records (joined with Guru info)
        $idKegiatan = $kegiatan['Id']; // Array return type
        $idTpqFilter = null;
        
        if ($kegiatan['Lingkup'] == 'TPQ' && !empty($kegiatan['IdTpq'])) {
            $idTpqFilter = $kegiatan['IdTpq'];
        }

        $attendanceRecords = $this->absensiGuruModel->getAbsensiByKegiatan($idKegiatan, $idTpqFilter);

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
}
