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

    public function index()
    {
        // 1. Find ANY active event
        // Logic: Get the latest active event.
        // If multiple active, maybe prioritized by date?
        $kegiatan = $this->kegiatanModel->where('IsActive', 1)
                                        ->orderBy('Tanggal', 'DESC')
                                        ->first();

        if (!$kegiatan) {
            return view('absensi_guru/index', [
                'hasAction' => false,
                'message'   => 'Tidak ada kegiatan aktif saat ini.',
                'page_title' => 'Absensi Guru'
            ]);
        }

        // 2. Fetch Attendance Records (joined with Guru info)
        $idKegiatan = $kegiatan['Id']; // Array return type
        $attendanceRecords = $this->absensiGuruModel->getAbsensiByKegiatan($idKegiatan);

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
            'belum' => count($belumHadir)
        ];

        foreach ($sudahHadir as $guru) {
            if ($guru->StatusKehadiran == 'Hadir') $stats['hadir']++;
            elseif ($guru->StatusKehadiran == 'Izin') $stats['izin']++;
            elseif ($guru->StatusKehadiran == 'Sakit') $stats['sakit']++;
        }

        $data = [
            'hasAction'  => true,
            'kegiatan'   => $kegiatan,
            'belumHadir' => $belumHadir,
            'sudahHadir' => $sudahHadir,
            'stats'      => $stats,
            'page_title' => 'Absensi Guru'
        ];

        return view('absensi_guru/index', $data);
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

        $update = $this->absensiGuruModel->update($idAbsensi, $data);

        if ($update) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update database']);
        }
    }
}
