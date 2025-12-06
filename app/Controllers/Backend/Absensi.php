<?php namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\KelasModel;
use App\Models\AbsensiModel;
use App\Models\HelpFunctionModel;

class Absensi extends BaseController
{
    protected $helpFunction;

    public function __construct()
    {
        $this->helpFunction = new HelpFunctionModel();
    }

    public function index()
    {
        $santriModel = new SantriModel();
        $kelasModel = new KelasModel();
        $absensiModel = new AbsensiModel();

        // Ambil data session
        $IdTpq = session()->get('IdTpq');
        $IdGuru = session()->get('IdGuru');
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Ambil tanggal dari parameter atau gunakan tanggal hari ini
        $tanggalDipilih = $this->request->getGet('tanggal');
        $tanggalHariIni = date('Y-m-d');
        $tanggal = $tanggalDipilih ? $tanggalDipilih : $tanggalHariIni;

        // Ambil data santri berdasarkan kelas
        $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

        // Buat daftar kelas dari semua santri (sebelum filter absensi) untuk menampilkan tab
        $kelasListAll = [];
        foreach ($santriList as $santriObj) {
            if (!isset($kelasListAll[$santriObj->IdKelas])) {
                // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
                $namaKelasOriginal = $santriObj->NamaKelas ?? '';
                $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                $namaKelasMapped = $this->helpFunction->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );

                $kelasListAll[$santriObj->IdKelas] = [
                    'IdKelas' => $santriObj->IdKelas,
                    'NamaKelas' => $namaKelasMapped,
                    'IdTahunAjaran' => $santriObj->IdTahunAjaran
                ];
            }
        }

        // Filter santri yang belum ada absensinya pada tanggal yang dipilih dan konversi nama kelas ke MDA
        $santri = [];
        foreach ($santriList as $santriObj) {
            // Cek apakah absensi sudah ada untuk santri ini pada tanggal yang dipilih
            $cekAbsensi = $absensiModel
                ->where('IdSantri', $santriObj->IdSantri)
                ->where('Tanggal', $tanggal)
                ->first();

            // Jika belum ada absensi, masukkan santri ke dalam daftar
            if (!$cekAbsensi) {
                // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
                $namaKelasOriginal = $santriObj->NamaKelas ?? '';
                $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                $santriObj->NamaKelas = $this->helpFunction->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );

                $santri[] = $santriObj;
            }
        }

        // Ambil semua data kelas untuk dropdown filter
        $kelas = $kelasModel->findAll();

        // Ambil data guru yang mengabsen untuk setiap kelas pada tanggal tertentu
        $guruAbsensi = [];
        foreach ($kelasListAll as $kelasItem) {
            // Ambil satu record absensi untuk kelas ini pada tanggal tertentu untuk mendapatkan IdGuru
            $absensiRecord = $absensiModel
                ->where('IdKelas', $kelasItem['IdKelas'])
                ->where('Tanggal', $tanggal)
                ->where('IdTpq', $IdTpq)
                ->first();

            if ($absensiRecord) {
                // Ambil IdGuru dari record (bisa array atau object)
                $idGuruAbsensi = is_array($absensiRecord) ? ($absensiRecord['IdGuru'] ?? null) : ($absensiRecord->IdGuru ?? null);

                if ($idGuruAbsensi) {
                    // Ambil nama guru dari tabel guru
                    $guruModel = new \App\Models\GuruModel();
                    $guruData = $guruModel->find($idGuruAbsensi);
                    if ($guruData) {
                        $namaGuru = is_array($guruData) ? ($guruData['Nama'] ?? 'Tidak diketahui') : ($guruData->Nama ?? 'Tidak diketahui');
                        $jenisKelamin = is_array($guruData) ? ($guruData['JenisKelamin'] ?? null) : ($guruData->JenisKelamin ?? null);

                        // Format nama: capitalize first letter
                        $namaGuru = ucwords(strtolower($namaGuru));

                        // Tambahkan prefix berdasarkan jenis kelamin (case-insensitive)
                        $jenisKelaminLower = strtolower($jenisKelamin ?? '');
                        if (stripos($jenisKelaminLower, 'l') === 0 || stripos($jenisKelaminLower, 'laki') !== false) {
                            $namaGuru = 'Ustadz ' . $namaGuru;
                        } elseif (stripos($jenisKelaminLower, 'p') === 0 || stripos($jenisKelaminLower, 'perempuan') !== false) {
                            $namaGuru = 'Ustadzah ' . $namaGuru;
                        }

                        $guruAbsensi[$kelasItem['IdKelas']] = $namaGuru;
                    }
                }
            }
        }

        // Data yang akan dikirim ke view
        $data = [
            'page_title' => 'Absensi Santri',
            'santri' => $santri,
            'kelas' => $kelas,
            'selected_kelas' => $IdKelas,
            'tanggal_dipilih' => $tanggal,
            'tanggal_hari_ini' => $tanggalHariIni,
            'kelas_list_all' => $kelasListAll, // Kirim semua kelas untuk menampilkan tab
            'guru_absensi' => $guruAbsensi // Kirim data guru yang mengabsen
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
        $IdTpq = session()->get('IdTpq');

        // Cek apakah request adalah AJAX
        $isAjax = $this->request->isAJAX();

        try {
            // Loop through the kehadiran to save attendance for each student
            foreach ($kehadiran as $IdSantri => $statusKehadiran) {
                // Cek apakah sudah ada data absensi untuk santri ini pada tanggal ini
                $cekAbsensi = $absensiModel
                    ->where('IdSantri', $IdSantri)
                    ->where('Tanggal', $tanggal)
                    ->first();

                $data = [
                    'IdSantri' => $IdSantri,
                    'Tanggal' => $tanggal,
                    'Kehadiran' => $statusKehadiran,
                    'Keterangan' => isset($keterangan[$IdSantri]) ? $keterangan[$IdSantri] : '', // Jika ada keterangan
                    'IdKelas' => $IdKelas,
                    'IdGuru' => $IdGuru,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdTpq' => $IdTpq,
                ];

                // Jika sudah ada, update; jika belum, insert
                if ($cekAbsensi) {
                    $absensiModel->update($cekAbsensi->Id, $data);
                } else {
                    $absensiModel->insert($data);
                }
            }

            // Jika request adalah AJAX, return JSON
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data absensi berhasil disimpan!',
                    'tanggal' => $tanggal
                ]);
            }

            // Set flash message
            session()->setFlashdata('success', 'Data absensi berhasil disimpan!');

            // Redirect kembali setelah absensi tersimpan dengan parameter tanggal
            return redirect()->to(base_url('backend/absensi?tanggal=' . $tanggal));
        } catch (\Exception $e) {
            // Jika request adalah AJAX, return JSON error
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data absensi: ' . $e->getMessage()
                ]);
            }

            // Redirect dengan error message
            return redirect()->to(base_url('backend/absensi?tanggal=' . $tanggal))->with('error', 'Gagal menyimpan data absensi');
        }
    }

    public function statistikKehadiran()
    {
        $santriModel = new SantriModel();
        $absensiModel = new AbsensiModel();

        // Ambil data session
        $IdTpq = session()->get('IdTpq');
        $IdGuru = session()->get('IdGuru');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Ambil data santri berdasarkan kelas untuk mendapatkan daftar kelas
        $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, 0, $IdGuru);

        // Buat daftar kelas dari semua santri
        $kelasListAll = [];
        foreach ($santriList as $santriObj) {
            if (!isset($kelasListAll[$santriObj->IdKelas])) {
                // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
                $namaKelasOriginal = $santriObj->NamaKelas ?? '';
                $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                $namaKelasMapped = $this->helpFunction->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );

                $kelasListAll[$santriObj->IdKelas] = [
                    'IdKelas' => $santriObj->IdKelas,
                    'NamaKelas' => $namaKelasMapped,
                    'IdTahunAjaran' => $santriObj->IdTahunAjaran
                ];
            }
        }

        // Hitung periode minggu saat ini (Senin - Minggu)
        // Minggu pertama dimulai dari 1 Januari
        $today = date('Y-m-d');
        $year = date('Y');
        $januaryFirst = $year . '-01-01';
        $dayOfWeekJan = date('w', strtotime($januaryFirst)); // 0 = Minggu, 1 = Senin, dst
        $mondayOffsetJan = ($dayOfWeekJan == 0) ? -6 : (1 - $dayOfWeekJan);
        $firstMondayOfYear = date('Y-m-d', strtotime($januaryFirst . ' ' . $mondayOffsetJan . ' days'));

        // Hitung minggu ke berapa dari tahun ini
        $daysDiff = (strtotime($today) - strtotime($firstMondayOfYear)) / (60 * 60 * 24);
        $weekNumber = floor($daysDiff / 7);

        // Hitung Senin dari minggu ini
        $currentMonday = date('Y-m-d', strtotime($firstMondayOfYear . ' +' . ($weekNumber * 7) . ' days'));
        $startOfWeek = $currentMonday;
        $endOfWeek = date('Y-m-d', strtotime($startOfWeek . ' +6 days'));

        // Data yang akan dikirim ke view
        $data = [
            'page_title' => 'Statistik Kehadiran',
            'kelas_list_all' => $kelasListAll,
            'start_of_week' => $startOfWeek,
            'end_of_week' => $endOfWeek,
            'IdTahunAjaran' => $IdTahunAjaran
        ];

        return view('backend/absensi/statistikKehadiran', $data);
    }

    /**
     * Endpoint AJAX untuk mengambil data statistik kehadiran
     */
    public function getStatistikData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $absensiModel = new AbsensiModel();

        // Ambil data dari request
        $IdKelas = $this->request->getGet('IdKelas');
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        // Ambil data session
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        if (empty($IdKelas) || empty($startDate) || empty($endDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        // Get statistik per kelas
        $statistikPerKelas = $absensiModel->getStatistikPerKelas($IdTpq, $IdKelas, $startDate, $endDate, $IdTahunAjaran);

        log_message('debug', '[STATISTIK] Statistik per kelas raw: ' . json_encode($statistikPerKelas));

        // Get statistik per hari
        $statistikPerHari = $absensiModel->getStatistikPerHari($IdTpq, $IdKelas, $startDate, $endDate, $IdTahunAjaran);

        log_message('debug', '[STATISTIK] Statistik per hari raw: ' . json_encode($statistikPerHari));

        // Format data untuk response
        $kehadiranData = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0
        ];

        foreach ($statistikPerKelas as $row) {
            $kehadiran = strtolower(is_array($row) ? ($row['Kehadiran'] ?? '') : ($row->Kehadiran ?? ''));
            $count = is_array($row) ? ((int)($row['count'] ?? 0)) : ((int)($row->count ?? 0));
            if (isset($kehadiranData[$kehadiran])) {
                $kehadiranData[$kehadiran] = $count;
            }
        }

        log_message('debug', '[STATISTIK] Kehadiran data: ' . json_encode($kehadiranData));
        log_message('debug', '[STATISTIK] Statistik per kelas count: ' . count($statistikPerKelas));
        log_message('debug', '[STATISTIK] Statistik per hari count: ' . count($statistikPerHari));

        // Format data per hari (7 hari dalam seminggu)
        $hariData = [];
        $hariLabels = [];
        $hariDataArray = []; // Array untuk response JSON
        $currentDate = strtotime($startDate);
        $endDateTimestamp = strtotime($endDate);

        while ($currentDate <= $endDateTimestamp) {
            $dateStr = date('Y-m-d', $currentDate);
            $hariLabels[] = date('d/m', $currentDate) . ' (' . $this->getNamaHari(date('w', $currentDate)) . ')';

            $hariData[$dateStr] = [
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alfa' => 0
            ];

            $currentDate = strtotime('+1 day', $currentDate);
        }

        // Isi data per hari
        foreach ($statistikPerHari as $row) {
            $tanggal = is_array($row) ? ($row['Tanggal'] ?? '') : ($row->Tanggal ?? '');
            $kehadiran = strtolower(is_array($row) ? ($row['Kehadiran'] ?? '') : ($row->Kehadiran ?? ''));
            $count = is_array($row) ? ((int)($row['count'] ?? 0)) : ((int)($row->count ?? 0));

            if (isset($hariData[$tanggal]) && isset($hariData[$tanggal][$kehadiran])) {
                $hariData[$tanggal][$kehadiran] = $count;
            }
        }

        // Convert ke array untuk response JSON (urut sesuai tanggal)
        $currentDate = strtotime($startDate);
        while ($currentDate <= $endDateTimestamp) {
            $dateStr = date('Y-m-d', $currentDate);
            $hariDataArray[] = $hariData[$dateStr] ?? [
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alfa' => 0
            ];
            $currentDate = strtotime('+1 day', $currentDate);
        }

        return $this->response->setJSON([
            'success' => true,
            'kehadiran' => $kehadiranData,
            'hari_labels' => $hariLabels,
            'hari_data' => $hariDataArray
        ]);
    }

    /**
     * Helper function untuk mendapatkan nama hari
     */
    private function getNamaHari($dayOfWeek)
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $hari[$dayOfWeek] ?? '';
    }

    /**
     * Endpoint AJAX untuk mengambil data santri berdasarkan kelas dan tanggal
     */
    public function getSantriByKelasDanTanggal()
    {
        // Log debug
        log_message('debug', '[ABSENSI AJAX] Request received');
        log_message('debug', '[ABSENSI AJAX] Request method: ' . $this->request->getMethod());
        log_message('debug', '[ABSENSI AJAX] Is AJAX: ' . ($this->request->isAJAX() ? 'Yes' : 'No'));
        log_message('debug', '[ABSENSI AJAX] All GET params: ' . json_encode($this->request->getGet()));

        // Cek apakah request adalah AJAX
        if (!$this->request->isAJAX()) {
            log_message('error', '[ABSENSI AJAX] Invalid request - not AJAX');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request - must be AJAX'
            ]);
        }

        $santriModel = new SantriModel();
        $absensiModel = new AbsensiModel();

        // Ambil data dari request
        $IdKelas = $this->request->getGet('IdKelas');
        $tanggal = $this->request->getGet('tanggal');

        log_message('debug', '[ABSENSI AJAX] IdKelas: ' . $IdKelas);
        log_message('debug', '[ABSENSI AJAX] Tanggal: ' . $tanggal);

        // Ambil data session
        $IdTpq = session()->get('IdTpq');
        $IdGuru = session()->get('IdGuru');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        log_message('debug', '[ABSENSI AJAX] Session - IdTpq: ' . $IdTpq);
        log_message('debug', '[ABSENSI AJAX] Session - IdGuru: ' . $IdGuru);
        log_message('debug', '[ABSENSI AJAX] Session - IdTahunAjaran: ' . $IdTahunAjaran);

        if (empty($IdKelas) || empty($tanggal)) {
            log_message('error', '[ABSENSI AJAX] Missing parameters - IdKelas: ' . $IdKelas . ', Tanggal: ' . $tanggal);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IdKelas dan tanggal harus diisi'
            ]);
        }

        // Ambil data santri berdasarkan kelas
        $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

        // Filter santri yang belum ada absensinya pada tanggal yang dipilih dan konversi nama kelas ke MDA
        $santri = [];
        foreach ($santriList as $santriObj) {
            // Cek apakah absensi sudah ada untuk santri ini pada tanggal yang dipilih
            $cekAbsensi = $absensiModel
                ->where('IdSantri', $santriObj->IdSantri)
                ->where('Tanggal', $tanggal)
                ->first();

            // Jika belum ada absensi, masukkan santri ke dalam daftar
            if (!$cekAbsensi) {
                // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
                $namaKelasOriginal = $santriObj->NamaKelas ?? '';
                $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
                $santriObj->NamaKelas = $this->helpFunction->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );

                $santri[] = $santriObj;
            }
        }

        log_message('debug', '[ABSENSI AJAX] Total santri from query: ' . count($santriList));
        log_message('debug', '[ABSENSI AJAX] Santri belum diabsen: ' . count($santri));

        // Ambil data guru yang mengabsen untuk kelas ini pada tanggal tertentu
        $namaGuru = null;
        $absensiRecord = $absensiModel
            ->where('IdKelas', $IdKelas)
            ->where('Tanggal', $tanggal)
            ->where('IdTpq', $IdTpq)
            ->first();

        if ($absensiRecord) {
            // Ambil IdGuru dari record (bisa array atau object)
            $idGuruAbsensi = is_array($absensiRecord) ? ($absensiRecord['IdGuru'] ?? null) : ($absensiRecord->IdGuru ?? null);

            if ($idGuruAbsensi) {
                $guruModel = new \App\Models\GuruModel();
                $guruData = $guruModel->find($idGuruAbsensi);
                if ($guruData) {
                    $namaGuru = is_array($guruData) ? ($guruData['Nama'] ?? null) : ($guruData->Nama ?? null);
                    $jenisKelamin = is_array($guruData) ? ($guruData['JenisKelamin'] ?? null) : ($guruData->JenisKelamin ?? null);

                    if ($namaGuru) {
                        // Format nama: capitalize first letter
                        $namaGuru = ucwords(strtolower($namaGuru));

                        // Tambahkan prefix berdasarkan jenis kelamin (case-insensitive)
                        $jenisKelaminLower = strtolower($jenisKelamin ?? '');
                        if (stripos($jenisKelaminLower, 'l') === 0 || stripos($jenisKelaminLower, 'laki') !== false) {
                            $namaGuru = 'Ustad ' . $namaGuru;
                        } elseif (stripos($jenisKelaminLower, 'p') === 0 || stripos($jenisKelaminLower, 'perempuan') !== false) {
                            $namaGuru = 'Ustadzah ' . $namaGuru;
                        }
                    }
                }
            }
        }

        // Format data untuk response
        $santriData = [];
        foreach ($santri as $row) {
            $santriData[] = [
                'IdSantri' => $row->IdSantri,
                'NamaSantri' => $row->NamaSantri,
                'PhotoProfil' => $row->PhotoProfil ?? '',
                'JenisKelamin' => $row->JenisKelamin ?? '',
                'NamaKelas' => $row->NamaKelas
            ];
        }

        $response = [
            'success' => true,
            'santri' => $santriData,
            'count' => count($santriData),
            'tanggal' => $tanggal,
            'IdKelas' => $IdKelas,
            'nama_guru' => $namaGuru // Tambahkan nama guru
        ];

        log_message('debug', '[ABSENSI AJAX] Response count: ' . count($santriData));
        log_message('debug', '[ABSENSI AJAX] Nama guru: ' . ($namaGuru ?? 'Tidak ada'));
        log_message('debug', '[ABSENSI AJAX] Sending response');

        return $this->response->setJSON($response);
    }

    /**
     * Endpoint AJAX untuk mengambil data statistik per semester
     */
    public function getStatistikPerSemester()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $absensiModel = new AbsensiModel();

        // Ambil data dari request
        $IdKelas = $this->request->getGet('IdKelas');

        // Ambil data session
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        if (empty($IdKelas) || empty($IdTahunAjaran)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        // Get statistik untuk semester Ganjil dan Genap
        // Gunakan IdTahunAjaran langsung tanpa konversi karena database menggunakan format number (20252026)
        $statistikGanjil = $absensiModel->getStatistikPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, 'Ganjil');
        $statistikGenap = $absensiModel->getStatistikPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, 'Genap');

        // Format data untuk response
        $kehadiranGanjil = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0
        ];

        $kehadiranGenap = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0
        ];

        foreach ($statistikGanjil as $row) {
            $kehadiran = strtolower(is_array($row) ? ($row['Kehadiran'] ?? '') : ($row->Kehadiran ?? ''));
            $count = is_array($row) ? ((int)($row['count'] ?? 0)) : ((int)($row->count ?? 0));
            if (isset($kehadiranGanjil[$kehadiran])) {
                $kehadiranGanjil[$kehadiran] = $count;
            }
        }

        foreach ($statistikGenap as $row) {
            $kehadiran = strtolower(is_array($row) ? ($row['Kehadiran'] ?? '') : ($row->Kehadiran ?? ''));
            $count = is_array($row) ? ((int)($row['count'] ?? 0)) : ((int)($row->count ?? 0));
            if (isset($kehadiranGenap[$kehadiran])) {
                $kehadiranGenap[$kehadiran] = $count;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'ganjil' => $kehadiranGanjil,
            'genap' => $kehadiranGenap
        ]);
    }

    /**
     * Endpoint AJAX untuk mengambil list santri dengan statistik kehadiran
     */
    public function getListSantriStatistik()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $absensiModel = new AbsensiModel();

        // Ambil data dari request
        $IdKelas = $this->request->getGet('IdKelas');
        $semester = $this->request->getGet('semester'); // Ganjil atau Genap

        // Ambil data session
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        if (empty($IdKelas) || empty($IdTahunAjaran) || empty($semester)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        // Validasi semester
        if (!in_array($semester, ['Ganjil', 'Genap'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semester harus Ganjil atau Genap'
            ]);
        }

        try {
            // Get list santri dengan statistik
            $listSantri = $absensiModel->getListSantriDenganStatistik($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

            log_message('debug', '[STATISTIK LIST SANTRI] Total santri found: ' . count($listSantri));

            // Hitung persentase untuk setiap santri
            foreach ($listSantri as &$santri) {
                $total = (int)($santri['TotalAbsensi'] ?? 0);
                if ($total > 0) {
                    $santri['PersenHadir'] = round((((int)($santri['Hadir'] ?? 0) / $total) * 100), 1);
                    $santri['PersenIzin'] = round((((int)($santri['Izin'] ?? 0) / $total) * 100), 1);
                    $santri['PersenSakit'] = round((((int)($santri['Sakit'] ?? 0) / $total) * 100), 1);
                    $santri['PersenAlfa'] = round((((int)($santri['Alfa'] ?? 0) / $total) * 100), 1);
                } else {
                    $santri['PersenHadir'] = 0;
                    $santri['PersenIzin'] = 0;
                    $santri['PersenSakit'] = 0;
                    $santri['PersenAlfa'] = 0;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $listSantri
            ]);
        } catch (\Exception $e) {
            log_message('error', '[STATISTIK LIST SANTRI] Error: ' . $e->getMessage());
            log_message('error', '[STATISTIK LIST SANTRI] Trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint AJAX untuk mengambil data kehadiran per kelas per hari untuk periode 2 minggu
     * Digunakan untuk Multi-Line Chart di dashboard kepala sekolah
     */
    public function getKehadiranPerKelasPerHari()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $absensiModel = new AbsensiModel();
        $kelasModel = new KelasModel();
        $helpFunction = new HelpFunctionModel();

        // Ambil data dari request
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        // Ambil data session
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        if (empty($startDate) || empty($endDate) || empty($IdTpq) || empty($IdTahunAjaran)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        try {
            // Ambil data kehadiran per kelas per hari
            $kehadiranData = $absensiModel->getKehadiranPerKelasPerHari($IdTpq, $startDate, $endDate, $IdTahunAjaran);

            // Ambil daftar IdKelas yang unik dari data kehadiran
            $kelasIds = [];
            foreach ($kehadiranData as $tanggal => $kelasData) {
                if (is_array($kelasData)) {
                    foreach ($kelasData as $idKelas => $count) {
                        if (!in_array($idKelas, $kelasIds)) {
                            $kelasIds[] = $idKelas;
                        }
                    }
                }
            }

            // Jika tidak ada data kehadiran, ambil semua kelas dari TPQ
            if (empty($kelasIds)) {
                $kelasList = $helpFunction->getListKelas($IdTpq, $IdTahunAjaran, null, null, true);
                foreach ($kelasList as $kelas) {
                    $idKelas = is_array($kelas) ? ($kelas['IdKelas'] ?? 0) : ($kelas->IdKelas ?? 0);
                    if ($idKelas && !in_array($idKelas, $kelasIds)) {
                        $kelasIds[] = $idKelas;
                    }
                }
            }

            // Ambil nama kelas dari database
            $kelasMap = [];
            if (!empty($kelasIds)) {
                $db = db_connect();
                $builder = $db->table('tbl_kelas');
                $builder->select('IdKelas, NamaKelas');
                $builder->whereIn('IdKelas', $kelasIds);
                $kelasList = $builder->get()->getResultArray();

                foreach ($kelasList as $kelas) {
                    $idKelas = $kelas['IdKelas'] ?? 0;
                    $namaKelas = $kelas['NamaKelas'] ?? '';

                    // Konversi nama kelas ke MDA jika perlu
                    $mdaCheckResult = $helpFunction->checkMdaKelasMapping($IdTpq, $namaKelas);
                    $namaKelasDisplay = $helpFunction->convertKelasToMda($namaKelas, $mdaCheckResult['mappedMdaKelas']);

                    $kelasMap[$idKelas] = $namaKelasDisplay;
                }
            }

            // Generate semua tanggal dalam periode
            $tanggalList = [];
            $currentDate = new \DateTime($startDate);
            $endDateTime = new \DateTime($endDate);

            while ($currentDate <= $endDateTime) {
                $tanggalStr = $currentDate->format('Y-m-d');
                $tanggalList[] = $tanggalStr;
                $currentDate->modify('+1 day');
            }

            // Format data untuk chart: setiap kelas menjadi satu dataset
            $datasets = [];
            foreach ($kelasMap as $idKelas => $namaKelas) {
                $data = [];
                foreach ($tanggalList as $tanggal) {
                    // Ambil count untuk tanggal dan kelas ini
                    $count = isset($kehadiranData[$tanggal][$idKelas]) ? (int)$kehadiranData[$tanggal][$idKelas] : 0;
                    $data[] = $count;
                }

                $datasets[] = [
                    'label' => $namaKelas,
                    'data' => $data,
                    'IdKelas' => $idKelas
                ];
            }

            // Format label tanggal (format: d M atau d/m)
            $labels = [];
            foreach ($tanggalList as $tanggal) {
                $dateObj = new \DateTime($tanggal);
                $labels[] = $dateObj->format('d/m'); // Format: 25/11
            }

            return $this->response->setJSON([
                'success' => true,
                'labels' => $labels,
                'datasets' => $datasets,
                'tanggal_list' => $tanggalList
            ]);
        } catch (\Exception $e) {
            log_message('error', '[KEHADIRAN PER KELAS] Error: ' . $e->getMessage());
            log_message('error', '[KEHADIRAN PER KELAS] Trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman untuk mengubah absensi santri
     */
    public function ubahAbsensi()
    {
        $data = [
            'page_title' => 'Ubah Absensi Santri'
        ];

        return view('backend/absensi/ubahAbsensi', $data);
    }

    /**
     * AJAX endpoint untuk mendapatkan status absensi per kelas pada tanggal tertentu
     */
    public function getStatusAbsensiPerKelas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $tanggal = $this->request->getGet('tanggal');
        $IdTpq = session()->get('IdTpq');
        $IdGuru = session()->get('IdGuru');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        if (empty($tanggal)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal harus diisi'
            ]);
        }

        try {
            $santriModel = new SantriModel();
            $absensiModel = new AbsensiModel();
            $helpFunction = new HelpFunctionModel();
            $db = db_connect();

            // Ambil daftar kelas yang diajar oleh guru ini
            $kelasList = $helpFunction->getListKelas($IdTpq, $IdTahunAjaran, null, $IdGuru, false);
            
            $statusKelas = [];
            
            foreach ($kelasList as $kelas) {
                $IdKelas = is_array($kelas) ? ($kelas['IdKelas'] ?? null) : ($kelas->IdKelas ?? null);
                $NamaKelas = is_array($kelas) ? ($kelas['NamaKelas'] ?? null) : ($kelas->NamaKelas ?? null);
                
                if (!$IdKelas || !$NamaKelas) {
                    continue;
                }

                // Hitung total santri di kelas ini
                $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
                $totalSantri = count($santriList);

                // Hitung jumlah santri yang sudah diabsen pada tanggal ini
                $builder = $db->table('tbl_absensi_santri');
                $builder->select('COUNT(DISTINCT IdSantri) as total_absen');
                $builder->where('IdKelas', $IdKelas);
                $builder->where('Tanggal', $tanggal);
                $builder->where('IdTpq', $IdTpq);
                $builder->where('IdTahunAjaran', $IdTahunAjaran);
                $result = $builder->get()->getRowArray();
                $totalAbsen = (int)($result['total_absen'] ?? 0);

                // Hitung jumlah per kategori kehadiran
                $builder3 = $db->table('tbl_absensi_santri');
                $builder3->select('Kehadiran, COUNT(*) as jumlah');
                $builder3->where('IdKelas', $IdKelas);
                $builder3->where('Tanggal', $tanggal);
                $builder3->where('IdTpq', $IdTpq);
                $builder3->where('IdTahunAjaran', $IdTahunAjaran);
                $builder3->groupBy('Kehadiran');
                $kategoriResult = $builder3->get()->getResultArray();

                // Inisialisasi jumlah per kategori
                $jumlahHadir = 0;
                $jumlahIzin = 0;
                $jumlahSakit = 0;
                $jumlahAlfa = 0;

                foreach ($kategoriResult as $row) {
                    $kehadiran = strtolower($row['Kehadiran'] ?? '');
                    $jumlah = (int)($row['jumlah'] ?? 0);
                    
                    switch ($kehadiran) {
                        case 'hadir':
                            $jumlahHadir = $jumlah;
                            break;
                        case 'izin':
                            $jumlahIzin = $jumlah;
                            break;
                        case 'sakit':
                            $jumlahSakit = $jumlah;
                            break;
                        case 'alfa':
                            $jumlahAlfa = $jumlah;
                            break;
                    }
                }

                // Ambil nama guru yang mengabsen (jika ada)
                $builder2 = $db->table('tbl_absensi_santri a');
                $builder2->select('g.Nama, g.JenisKelamin');
                $builder2->join('tbl_guru g', 'g.IdGuru = a.IdGuru', 'left');
                $builder2->where('a.IdKelas', $IdKelas);
                $builder2->where('a.Tanggal', $tanggal);
                $builder2->where('a.IdTpq', $IdTpq);
                $builder2->where('a.IdTahunAjaran', $IdTahunAjaran);
                $builder2->limit(1);
                $guruData = $builder2->get()->getRowArray();

                $namaGuru = null;
                if ($guruData && !empty($guruData['Nama'])) {
                    $namaGuru = ucwords(strtolower($guruData['Nama']));
                    $jenisKelamin = strtolower($guruData['JenisKelamin'] ?? '');
                    if (stripos($jenisKelamin, 'l') === 0 || stripos($jenisKelamin, 'laki') !== false) {
                        $namaGuru = 'Ustadz ' . $namaGuru;
                    } elseif (stripos($jenisKelamin, 'p') === 0 || stripos($jenisKelamin, 'perempuan') !== false) {
                        $namaGuru = 'Ustadzah ' . $namaGuru;
                    }
                }

                // Tentukan status
                $status = 'belum_absen';
                if ($totalSantri > 0 && $totalAbsen > 0) {
                    if ($totalAbsen >= $totalSantri) {
                        $status = 'sudah_absen_semua';
                    } else {
                        $status = 'sebagian_absen';
                    }
                }

                $statusKelas[] = [
                    'IdKelas' => $IdKelas,
                    'NamaKelas' => $NamaKelas,
                    'totalSantri' => $totalSantri,
                    'totalAbsen' => $totalAbsen,
                    'status' => $status,
                    'namaGuru' => $namaGuru,
                    'jumlahHadir' => $jumlahHadir,
                    'jumlahIzin' => $jumlahIzin,
                    'jumlahSakit' => $jumlahSakit,
                    'jumlahAlfa' => $jumlahAlfa
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $statusKelas,
                'tanggal' => $tanggal
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil status absensi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX endpoint untuk mencari santri berdasarkan nama
     */
    public function searchSantri()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $keyword = $this->request->getGet('keyword');
        $tanggal = $this->request->getGet('tanggal');
        $IdTpq = session()->get('IdTpq');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdGuru = session()->get('IdGuru');

        if (empty($keyword) || strlen($keyword) < 2) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }

        $santriModel = new SantriModel();
        $absensiModel = new AbsensiModel();
        $helpFunction = new HelpFunctionModel();
        $db = db_connect();
        
        // Ambil daftar kelas yang diajar oleh guru ini
        $kelasList = $helpFunction->getListKelas($IdTpq, $IdTahunAjaran, null, $IdGuru, false);
        
        // Ekstrak IdKelas dari daftar kelas
        $idKelasArray = [];
        foreach ($kelasList as $kelas) {
            $IdKelas = is_array($kelas) ? ($kelas['IdKelas'] ?? null) : ($kelas->IdKelas ?? null);
            if ($IdKelas) {
                $idKelasArray[] = $IdKelas;
            }
        }
        
        // Jika guru tidak memiliki kelas, kembalikan array kosong
        if (empty($idKelasArray)) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
        
        // Cari santri berdasarkan nama, hanya dari kelas yang diajar oleh guru
        $builder = $db->table('tbl_kelas_santri ks');
        $builder->select('
            s.IdSantri,
            s.NamaSantri,
            s.JenisKelamin,
            s.PhotoProfil,
            k.IdKelas,
            k.NamaKelas
        ');
        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left');
        
        $builder->where('s.Active', 1);
        $builder->where('ks.Status', 1);
        $builder->where('ks.IdTpq', $IdTpq);
        $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        $builder->whereIn('ks.IdKelas', $idKelasArray); // Filter hanya kelas yang diajar oleh guru
        $builder->like('s.NamaSantri', $keyword);
        
        $builder->orderBy('s.NamaSantri', 'ASC');
        $builder->limit(20);
        
        $results = $builder->get()->getResultArray();

        // Ambil data absensi untuk setiap santri jika tanggal diberikan
        if (!empty($tanggal)) {
            foreach ($results as &$santri) {
                $absensi = $absensiModel
                    ->where('IdSantri', $santri['IdSantri'])
                    ->where('Tanggal', $tanggal)
                    ->first();

                if ($absensi) {
                    $santri['Kehadiran'] = is_array($absensi) ? ($absensi['Kehadiran'] ?? null) : ($absensi->Kehadiran ?? null);
                    $santri['Keterangan'] = is_array($absensi) ? ($absensi['Keterangan'] ?? null) : ($absensi->Keterangan ?? null);
                } else {
                    $santri['Kehadiran'] = null;
                    $santri['Keterangan'] = null;
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * AJAX endpoint untuk mengambil data absensi santri pada tanggal tertentu
     */
    public function getAbsensiSantri()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $IdSantri = $this->request->getGet('IdSantri');
        $tanggal = $this->request->getGet('tanggal');

        if (empty($IdSantri) || empty($tanggal)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IdSantri dan tanggal harus diisi'
            ]);
        }

        $absensiModel = new AbsensiModel();
        
        // Ambil data absensi
        $absensi = $absensiModel
            ->where('IdSantri', $IdSantri)
            ->where('Tanggal', $tanggal)
            ->first();

        // Ambil data santri
        $santriModel = new SantriModel();
        $db = db_connect();
        $builder = $db->table('tbl_kelas_santri ks');
        $builder->select('
            s.IdSantri,
            s.NamaSantri,
            s.JenisKelamin,
            s.PhotoProfil,
            k.IdKelas,
            k.NamaKelas
        ');
        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left');
        $builder->where('s.IdSantri', $IdSantri);
        $builder->where('ks.Status', 1);
        $builder->where('s.Active', 1);
        $santri = $builder->get()->getRowArray();

        if (!$santri) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data santri tidak ditemukan'
            ]);
        }

        $result = [
            'santri' => $santri,
            'absensi' => null
        ];

        if ($absensi) {
            $result['absensi'] = [
                'Id' => is_array($absensi) ? ($absensi['Id'] ?? null) : ($absensi->Id ?? null),
                'Kehadiran' => is_array($absensi) ? ($absensi['Kehadiran'] ?? null) : ($absensi->Kehadiran ?? null),
                'Keterangan' => is_array($absensi) ? ($absensi['Keterangan'] ?? null) : ($absensi->Keterangan ?? null),
                'Tanggal' => is_array($absensi) ? ($absensi['Tanggal'] ?? null) : ($absensi->Tanggal ?? null)
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * AJAX endpoint untuk menyimpan perubahan absensi
     */
    public function updateAbsensi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $IdSantri = $this->request->getPost('IdSantri');
        $tanggal = $this->request->getPost('tanggal');
        $kehadiran = $this->request->getPost('kehadiran');
        $keterangan = $this->request->getPost('keterangan');
        $IdTpq = session()->get('IdTpq');
        $IdGuru = session()->get('IdGuru');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        if (empty($IdSantri) || empty($tanggal) || empty($kehadiran)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IdSantri, tanggal, dan kehadiran harus diisi'
            ]);
        }

        // Validasi kehadiran
        $allowedKehadiran = ['Hadir', 'Izin', 'Sakit', 'Alfa'];
        if (!in_array($kehadiran, $allowedKehadiran)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status kehadiran tidak valid'
            ]);
        }

        // Ambil data kelas santri
        $santriModel = new SantriModel();
        $db = db_connect();
        $builder = $db->table('tbl_kelas_santri ks');
        $builder->select('k.IdKelas');
        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left');
        $builder->where('ks.IdSantri', $IdSantri);
        $builder->where('ks.IdTpq', $IdTpq);
        $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
        $builder->where('ks.Status', 1);
        $kelasData = $builder->get()->getRowArray();

        if (!$kelasData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data kelas santri tidak ditemukan'
            ]);
        }

        $IdKelas = $kelasData['IdKelas'];

        try {
            $absensiModel = new AbsensiModel();
            
            // Cek apakah sudah ada data absensi
            $cekAbsensi = $absensiModel
                ->where('IdSantri', $IdSantri)
                ->where('Tanggal', $tanggal)
                ->first();

            $data = [
                'IdSantri' => $IdSantri,
                'Tanggal' => $tanggal,
                'Kehadiran' => $kehadiran,
                'Keterangan' => $keterangan ?? '',
                'IdKelas' => $IdKelas,
                'IdGuru' => $IdGuru,
                'IdTahunAjaran' => $IdTahunAjaran,
                'IdTpq' => $IdTpq,
            ];

            if ($cekAbsensi) {
                // Update jika sudah ada
                $idAbsensi = is_array($cekAbsensi) ? ($cekAbsensi['Id'] ?? null) : ($cekAbsensi->Id ?? null);
                if ($idAbsensi) {
                    $absensiModel->update($idAbsensi, $data);
                } else {
                    // Jika Id tidak ditemukan, insert baru
                    $absensiModel->insert($data);
                }
            } else {
                // Insert jika belum ada
                $absensiModel->insert($data);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data absensi berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data absensi: ' . $e->getMessage()
            ]);
        }
    }
}
