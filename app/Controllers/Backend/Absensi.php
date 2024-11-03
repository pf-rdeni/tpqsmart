<?php namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\KelasModel;
use App\Models\AbsensiModel;

class Absensi extends BaseController
{
    public function index()
    {
        $santriModel = new SantriModel();
        $kelasModel = new KelasModel();
        $absensiModel = new AbsensiModel();

        // Ambil data session
        $IdGuru = session()->get('IdGuru');
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        
        // Ambil tanggal hari ini
        $tanggalHariIni = date('Y-m-d');
        
        // Ambil data santri berdasarkan kelas
        $santriList = $santriModel->GetDataSantriPerKelas($IdTahunAjaran, $IdKelas, $IdGuru);
        
        // Filter santri yang belum ada absensinya pada tanggal hari ini
        $santri = [];
        foreach ($santriList as $santriObj) {
            // Cek apakah absensi sudah ada untuk santri ini pada tanggal hari ini
            $cekAbsensi = $absensiModel
                ->where('IdSantri', $santriObj->IdSantri)
                ->where('Tanggal', $tanggalHariIni)
                ->first();

            // Jika belum ada absensi, masukkan santri ke dalam daftar
            if (!$cekAbsensi) {
                $santri[] = $santriObj;
            }
        }

        // Ambil semua data kelas untuk dropdown filter
        $kelas = $kelasModel->findAll();

        // Data yang akan dikirim ke view
        $data = [
            'page_title' => 'Absensi Santri',
            'santri' => $santri,
            'kelas' => $kelas,
            'selected_kelas' => $IdKelas
        ];

        // Menggunakan view 'backend/absensi/absensiSantri'
        return view('backend/absensi/absensiSantri', $data);
    }


    public function simpanAbsensi()
    {
        $absensiModel = new \App\Models\AbsensiModel();

        // Ambil data dari form
        $tanggal = $this->request->getPost('tanggal');
        $IdKelas = $this->request->getPost('IdKelas');
        $IdGuru = $this->request->getPost('IdGuru');
        $IdTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $kehadiran = $this->request->getPost('kehadiran');
        $keterangan = $this->request->getPost('keterangan');

        // Loop through the kehadiran to save attendance for each student
        foreach ($kehadiran as $IdSantri => $statusKehadiran) {
            $data = [
                'IdSantri' => $IdSantri,
                'Tanggal' => $tanggal,
                'Kehadiran' => $statusKehadiran,
                'Keterangan' => isset($keterangan[$IdSantri]) ? $keterangan[$IdSantri] : '', // Jika ada keterangan
                'IdKelas' => $IdKelas,
                'IdGuru' => $IdGuru,
                'IdTahunAjaran' => $IdTahunAjaran
            ];

            // Simpan data ke database
            $absensiModel->insert($data);
        }

        // Redirect kembali setelah absensi tersimpan
        return redirect()->to(base_url('backend/absensi'));
    }

    public function statistikKehadiran()
    {
        $model = new AbsensiModel();
        $kehadiran = $model->getKehadiran('2024-09-01', '2024-09-30'); // Ganti dengan rentang tanggal yang diinginkan
         $attendanceData = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0,
        ];
        // Loop through the results and populate the attendance data
        foreach ($kehadiran as $row) {
            $attendanceData[strtolower($row['Kehadiran'])] = (int) $row['count'];
        }
        return view('backend/absensi/statistikSantri', [
            'page_title' => 'Statistik',
            'data' => json_encode(array_values($attendanceData)),
        ]);
    }

}
