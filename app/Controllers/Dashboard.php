<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\HelpFunctionModel;
use App\Models\SantriModel;
use App\Models\TabunganModel;

/**
 * Dashboard Controller
 * Menangani halaman dashboard dan manajemen session untuk dashboard
 */
class Dashboard extends BaseController
{
    protected $helpFunctionModel;
    protected $santriModel;
    protected $tabunganModel;
    
    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->santriModel = new SantriModel();
        $this->tabunganModel = new TabunganModel();
    }

    private function getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $kelasList, $semester)
    {
        // Ekstrak ID kelas dari array/object
        if (is_array($kelasList)) {
            $kelasIds = array_map(function ($kelas) {
                return is_object($kelas) ? $kelas->IdKelas : $kelas;
            }, $kelasList);
        } else {
            $kelasIds = $kelasList;
        }

        // Ambil semua data dalam satu query
        $statusNilai = $this->helpFunctionModel->getStatusInputNilaiBulk(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            IdKelas: $kelasIds,
            Semester: $semester
        );

        // Ambil semua nama kelas dalam satu query
        $namaKelas = $this->helpFunctionModel->getNamaKelasBulk($kelasIds);

        // Gabungkan data dan konversi nama kelas ke MDA jika sesuai
        $result = [];
        if (is_array($kelasIds)) {
            foreach ($kelasIds as $idKelas) {
                if (isset($statusNilai[$idKelas])) {
                    $namaKelasOriginal = $namaKelas[$idKelas] ?? '';

                    // Check MDA mapping dan convert nama kelas jika sesuai
                    $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($idTpq, $namaKelasOriginal);
                    $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda(
                        $namaKelasOriginal,
                        $mdaCheckResult['mappedMdaKelas']
                    );

                    $result[] = [
                        'IdKelas' => $idKelas,
                        'NamaKelas' => $namaKelasDisplay,
                        'StatusInputNilai' => $statusNilai[$idKelas] ?? false
                    ];
                }
            }
        } else {
            // Kondisi jika $kelasIds bukan array (single ID)
            $idKelas = $kelasIds;
            if (isset($statusNilai[$idKelas])) {
                $namaKelasOriginal = $namaKelas[$idKelas] ?? '';

                // Check MDA mapping dan convert nama kelas jika sesuai
                $mdaCheckResult = $this->helpFunctionModel->checkMdaKelasMapping($idTpq, $namaKelasOriginal);
                $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda(
                    $namaKelasOriginal,
                    $mdaCheckResult['mappedMdaKelas']
                );

                $result[] = [
                    'IdKelas' => $idKelas,
                    'NamaKelas' => $namaKelasDisplay,
                    'StatusInputNilai' => $statusNilai[$idKelas] ?? false
                ];
            }
        }
        return $result;
    }

    private function getGuruDashboardData($idTpq, $idTahunAjaran, $idKelas, $idGuru)
    {
        $saldoTabungan = $this->tabunganModel->getSaldoTabunganSantri(
            $idTpq,
            $idTahunAjaran,
            $idKelas,
            $idGuru
        );

        $totalSantri = $this->santriModel->getTotalSantri(
            $idTpq,
            $idTahunAjaran,
            $idKelas,
            $idGuru
        );

        if (is_array($idKelas)) {
            $JumlahKelasDiajar = count($idKelas);
        } else {
            $JumlahKelasDiajar = empty($idKelas) ? 0 : 1;
        }

        // Ambil jumlah santri per kelas
        $jumlahSantriPerKelas = $this->helpFunctionModel->getJumlahSantriPerKelas(
            IdTpq: $idTpq,
            kelasIds: $idKelas
        );

        $statusInputNilaiPerKelasGanjil = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $idKelas, 'Ganjil');
        $statusInputNilaiPerKelasGenap = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $idKelas, 'Genap');

        // Data statistik utama
        $pageTitle = 'Dashboard';
        $totalTabungan = $saldoTabungan ?? 0;
        $tahunAjaran = $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran);

        // Status input nilai semester
        $statusInputNilaiSemesterGanjil = $this->helpFunctionModel->getStatusInputNilai(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            IdKelas: $idKelas,
            Semester: 'Ganjil'
        );
        $statusInputNilaiSemesterGenap = $this->helpFunctionModel->getStatusInputNilai(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            IdKelas: $idKelas,
            Semester: 'Genap'
        );

        return [
            'page_title' => $pageTitle,
            'JumlahKelasDiajar' => $JumlahKelasDiajar,
            'TotalSantri' => $totalSantri,
            'TotalTabungan' => $totalTabungan,
            'TahunAjaran' => $tahunAjaran,
            'StatusInputNilaiSemesterGanjil' => $statusInputNilaiSemesterGanjil,
            'StatusInputNilaiSemesterGenap' => $statusInputNilaiSemesterGenap,
            'StatusInputNilaiPerKelasGanjil' => $statusInputNilaiPerKelasGanjil,
            'StatusInputNilaiPerKelasGenap' => $statusInputNilaiPerKelasGenap,
            'JumlahSantriPerKelas' => $jumlahSantriPerKelas,
        ];
    }

    private function getAdminDashboardData($idTpq, $idTahunAjaran)
    {
        $listKelas = $this->helpFunctionModel->getListKelas(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
        );

        // Ambil jumlah santri per kelas
        $jumlahSantriPerKelas = $this->helpFunctionModel->getJumlahSantriPerKelas(
            IdTpq: $idTpq,
            kelasIds: array_map(function ($kelas) {
                return is_object($kelas) ? $kelas->IdKelas : $kelas;
            }, $listKelas)
        );

        $statusInputNilaiPerKelasGanjil = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $listKelas, 'Ganjil');
        $statusInputNilaiPerKelasGenap = $this->getStatusInputNilaiPerKelas($idTpq, $idTahunAjaran, $listKelas, 'Genap');

        // Handle IdTpq untuk admin (IdTpq=0 atau null berarti semua TPQ)
        $idTpqForQuery = (empty($idTpq) || $idTpq == '0' || $idTpq == 0) ? 0 : $idTpq;

        // Data statistik utama
        $pageTitle = 'Dashboard';

        // Untuk admin (IdTpq=0), tampilkan jumlah TPQ, untuk lainnya tampilkan Wali Kelas
        if ($idTpqForQuery == 0) {
            $totalTpq = $this->helpFunctionModel->getTotalTpq();
            $totalWaliKelas = null; // Tidak digunakan untuk admin
        } else {
            $totalWaliKelas = $this->helpFunctionModel->getTotalWaliKelas(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
            );
            $totalTpq = null; // Tidak digunakan untuk operator/kepala TPQ
        }

        $totalSantri = $this->helpFunctionModel->getTotalSantri(IdTpq: $idTpq);
        $totalGuru = $this->helpFunctionModel->getTotalGuru(IdTpq: $idTpq);
        $totalKelas = $this->helpFunctionModel->getTotalKelas(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
        );
        $totalSantriBaru = $this->helpFunctionModel->getTotalSantriBaru(
            IdTpq: $idTpq,
            IdKelas: session()->get('IdKelas'),
        );
        $tahunAjaran = $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran);

        // Status input nilai semester
        $statusInputNilaiSemesterGanjil = $this->helpFunctionModel->getStatusInputNilai(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            Semester: 'Ganjil'
        );
        $statusInputNilaiSemesterGenap = $this->helpFunctionModel->getStatusInputNilai(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
            Semester: 'Genap'
        );

        // Statistik Santri
        $statistikSantri = $this->helpFunctionModel->getStatistikSantri($idTpqForQuery);

        // Statistik Guru
        $statistikGuru = $this->helpFunctionModel->getStatistikGuru($idTpqForQuery);

        // Statistik per TPQ (hanya untuk admin dengan IdTpq=0)
        $statistikSantriPerTpq = [];
        $statistikGuruPerTpq = [];
        $statistikTpqDenganRasio = [];
        $statistikSantriPerTpqPerKelas = [];
        if ($idTpqForQuery == 0) {
            $statistikSantriPerTpq = $this->helpFunctionModel->getStatistikSantriPerTpq();
            $statistikGuruPerTpq = $this->helpFunctionModel->getStatistikGuruPerTpq();
            $statistikSantriPerTpqPerKelas = $this->getStatistikSantriPerTpqPerKelas($idTahunAjaran);
            
            // Gabungkan data santri dan guru untuk menghitung rasio
            $statistikTpqDenganRasio = $this->gabungkanStatistikTpqDenganRasio($statistikSantriPerTpq, $statistikGuruPerTpq);
        }

        return [
            'page_title' => $pageTitle,
            'TotalWaliKelas' => $totalWaliKelas,
            'TotalTpq' => $totalTpq,
            'IsAdmin' => ($idTpqForQuery == 0),
            'TotalSantri' => $totalSantri,
            'TotalGuru' => $totalGuru,
            'TotalKelas' => $totalKelas,
            'TotalSantriBaru' => $totalSantriBaru,
            'TahunAjaran' => $tahunAjaran,
            'StatusInputNilaiSemesterGanjil' => $statusInputNilaiSemesterGanjil,
            'StatusInputNilaiSemesterGenap' => $statusInputNilaiSemesterGenap,
            'StatusInputNilaiPerKelasGanjil' => $statusInputNilaiPerKelasGanjil,
            'StatusInputNilaiPerKelasGenap' => $statusInputNilaiPerKelasGenap,
            'JumlahSantriPerKelas' => $jumlahSantriPerKelas,
            'StatistikSantri' => $statistikSantri,
            'StatistikGuru' => $statistikGuru,
            'StatistikSantriPerTpq' => $statistikSantriPerTpq,
            'StatistikGuruPerTpq' => $statistikGuruPerTpq,
            'StatistikTpqDenganRasio' => $statistikTpqDenganRasio,
            'StatistikSantriPerTpqPerKelas' => $statistikSantriPerTpqPerKelas,
        ];
    }

    /**
     * Helper method untuk inisialisasi session tahun ajaran
     */
    private function initSessionTahunAjaran()
    {
        $idTahunAjaranList = session()->get('IdTahunAjaranList');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $tahunAjaranSaatIni = $this->helpFunctionModel->getTahunAjaranSaatIni();

        // Jika IdTahunAjaranList null atau kosong, set ke tahun ajaran saat ini
        if (empty($idTahunAjaranList) || !is_array($idTahunAjaranList)) {
            $idTahunAjaranList = [$tahunAjaranSaatIni];
            session()->set('IdTahunAjaranList', $idTahunAjaranList);
        }

        // Jika IdTahunAjaran null, set ke tahun ajaran saat ini
        if ($idTahunAjaran == null) {
            $idTahunAjaran = $tahunAjaranSaatIni;
            session()->set('IdTahunAjaran', $idTahunAjaran);
        }

        // Pastikan tahun ajaran saat ini ada di list
        if (!in_array($tahunAjaranSaatIni, $idTahunAjaranList)) {
            $idTahunAjaranList[] = $tahunAjaranSaatIni;
            session()->set('IdTahunAjaranList', $idTahunAjaranList);
        }
    }

    /**
     * Helper method untuk mendapatkan semua peran yang dimiliki IdGuru
     * Mengembalikan array peran: ['kepala_tpq', 'operator', 'wali_kelas', 'guru']
     */
    private function getAllUserRoles($idGuru = null, $idTpq = null, $idTahunAjaran = null, $idKelas = null)
    {
        $roles = [];
        
        if (empty($idGuru)) {
            $idGuru = session()->get('IdGuru');
        }
        if (empty($idTpq)) {
            $idTpq = session()->get('IdTpq');
        }
        if (empty($idTahunAjaran)) {
            $idTahunAjaran = session()->get('IdTahunAjaran');
        }
        if (empty($idKelas)) {
            $idKelas = session()->get('IdKelas');
        }

        // Cek apakah user adalah Admin (prioritas tertinggi, tidak perlu cek peran lain)
        if (in_groups('Admin')) {
            return ['admin'];
        }

        // Cek apakah user adalah Operator (dari auth groups)
        if (in_groups('Operator')) {
            $roles[] = 'operator';
        }

        // Cek apakah IdGuru ada (untuk peran Kepala TPQ, Wali Kelas, Guru)
        if (!empty($idGuru) && !empty($idTpq)) {
            // Cek apakah guru adalah Kepala TPQ dari struktur lembaga
            try {
                $strukturLembaga = $this->helpFunctionModel->getStrukturLembagaJabatan($idGuru, $idTpq);
                foreach ($strukturLembaga as $jabatan) {
                    if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                        $roles[] = 'kepala_tpq';
                        break;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore error
            }

            // Cek apakah guru adalah Guru Kelas/Wali Kelas
            // Wali Kelas (IdJabatan = 3) dan Guru Kelas (IdJabatan = 2 atau lainnya) sama sebagai 'guru'
            // Jadi tidak perlu menambahkan 'wali_kelas' sebagai role terpisah
            // Cek 1: Apakah user memiliki group 'Guru'
            if (in_groups('Guru')) {
                $roles[] = 'guru';
            } else {
                // Cek 2: Apakah IdGuru memiliki data di tbl_guru_kelas (apapun IdJabatannya)
                // Jika ada data di tbl_guru_kelas, berarti memiliki peran Guru
                try {
                    $guruKelasRows = $this->helpFunctionModel->getDataGuruKelas(
                        IdGuru: $idGuru,
                        IdTpq: $idTpq
                    );
                    // Jika ada data di tbl_guru_kelas, berarti memiliki peran Guru
                    // Tidak perlu mengecek IdJabatan spesifik, karena semua IdJabatan di tbl_guru_kelas adalah peran Guru
                    if (!empty($guruKelasRows) && count($guruKelasRows) > 0) {
                        $roles[] = 'guru';
                    }
                } catch (\Throwable $e) {
                    // Ignore error
                }
            }
        }

        // Jika tidak ada peran yang ditemukan, return default
        if (empty($roles)) {
            $roles = ['pengguna'];
        }

        return array_unique($roles);
    }

    /**
     * Helper method untuk mendapatkan peran aktif dari session atau menentukan default
     */
    private function getActiveRole($allRoles = [])
    {
        if (empty($allRoles)) {
            $allRoles = $this->getAllUserRoles();
        }

        // Jika hanya satu peran, langsung return
        if (count($allRoles) === 1) {
            return $allRoles[0];
        }

        // Ambil peran aktif dari session
        $activeRole = session()->get('active_role');

        // Jika peran aktif tidak ada di list peran yang dimiliki, reset ke default
        if (!empty($activeRole) && in_array($activeRole, $allRoles)) {
            return $activeRole;
        }

        // Tentukan peran default berdasarkan prioritas
        $priorityRoles = ['admin', 'operator', 'kepala_tpq', 'wali_kelas', 'guru', 'pengguna'];
        foreach ($priorityRoles as $priorityRole) {
            if (in_array($priorityRole, $allRoles)) {
                return $priorityRole;
            }
        }

        // Fallback ke peran pertama
        return $allRoles[0];
    }

    /**
     * Helper method untuk mendapatkan data user (nama, sapaan, peran)
     */
    private function getUserInfo()
    {
        $idGuru = session()->get('IdGuru');
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $idKelas = session()->get('IdKelas');

        $namaLogin = null;
        $sapaanLogin = null;
        
        if (!empty($idGuru)) {
            $guruRow = $this->helpFunctionModel->getGuruById($idGuru);
            if (is_array($guruRow)) {
                if (isset($guruRow['Nama'])) {
                    $namaLogin = $guruRow['Nama'];
                }
                $jk = strtolower(trim($guruRow['JenisKelamin'] ?? ''));
                if ($jk === 'p' || $jk === 'perempuan' || $jk === 'female' || $jk === 'f') {
                    $sapaanLogin = 'Ustadzah';
                } else if ($jk === 'l' || $jk === 'laki-laki' || $jk === 'laki laki' || $jk === 'male' || $jk === 'm') {
                    $sapaanLogin = 'Ustadz';
                }
            }
        }
        
        if ($namaLogin === null && function_exists('user') && user()) {
            $namaLogin = user()->username ?? user()->email ?? 'Pengguna';
        }
        if ($sapaanLogin === null) {
            $sapaanLogin = 'Ustadz';
        }

        // Ambil semua peran yang dimiliki
        $allRoles = $this->getAllUserRoles($idGuru, $idTpq, $idTahunAjaran, $idKelas);
        $activeRole = $this->getActiveRole($allRoles);

        // Tentukan peran login untuk display
        $peranLoginMap = [
            'admin' => 'Admin',
            'operator' => 'Operator',
            'kepala_tpq' => 'Kepala TPQ',
            'wali_kelas' => 'Wali Kelas',
            'guru' => 'Guru Kelas',
            'pengguna' => 'Pengguna'
        ];
        $peranLogin = $peranLoginMap[$activeRole] ?? 'Pengguna';

        return [
            'NamaLogin' => $namaLogin,
            'SapaanLogin' => $sapaanLogin,
            'PeranLogin' => $peranLogin,
            'ActiveRole' => $activeRole,
            'AllRoles' => $allRoles,
            'IsKepalaTpq' => in_array('kepala_tpq', $allRoles),
            'IsOperator' => in_array('operator', $allRoles),
            'IsWaliKelas' => in_array('wali_kelas', $allRoles),
            'IsGuru' => in_array('guru', $allRoles),
            'HasMultipleRoles' => count($allRoles) > 1
        ];
    }

    /**
     * Halaman utama dashboard - redirect ke dashboard sesuai role
     */
    public function index()
    {
        // Cek jika user adalah JuriSertifikasi, redirect ke dashboard sertifikasi
        if (in_groups('JuriSertifikasi')) {
            return redirect()->to(base_url('backend/sertifikasi/dashboard'));
        }

        // Cek jika user adalah PanitiaSertifikasi, redirect ke dashboard panitia sertifikasi
        if (in_groups('PanitiaSertifikasi')) {
            return redirect()->to(base_url('backend/sertifikasi/dashboardPanitiaSertifikasi'));
        }

        // Cek jika user adalah Juri atau Panitia, redirect ke dashboard munaqosah
        if (in_groups('Juri') || in_groups('Panitia')) {
            return redirect()->to(base_url('backend/munaqosah/dashboard-munaqosah'));
        }

        // Untuk Admin dan Operator, cek query parameter dashboard untuk redirect server-side
        if (in_groups('Admin') || in_groups('Operator')) {
            $dashboardParam = $this->request->getGet('dashboard');
            if ($dashboardParam === 'munaqosah') {
                return redirect()->to(base_url('backend/munaqosah/dashboard-munaqosah'));
            }
            if ($dashboardParam === 'sertifikasi' && in_groups('Admin')) {
                return redirect()->to(base_url('backend/sertifikasi/dashboard-admin'));
            }
        }

        // Inisialisasi session tahun ajaran
        $this->initSessionTahunAjaran();

        // Redirect ke dashboard sesuai peran aktif
        $userInfo = $this->getUserInfo();
        $activeRole = $userInfo['ActiveRole'];

        // Simpan all roles dan active role ke session untuk digunakan di view (selalu update)
        session()->set('available_roles', $userInfo['AllRoles']);
        
        // Jika multiple peran dan belum ada peran aktif di session, tampilkan modal pemilihan
        if ($userInfo['HasMultipleRoles'] && empty($activeRole)) {
            // Redirect ke halaman pemilihan peran
            return redirect()->to(base_url('backend/dashboard/select-role'));
        }
        
        // Set active role ke session
        session()->set('active_role', $activeRole);

        // Redirect berdasarkan peran aktif
        switch ($activeRole) {
            case 'admin':
                return redirect()->to(base_url('backend/dashboard/admin'));
            case 'operator':
                return redirect()->to(base_url('backend/dashboard/operator'));
            case 'kepala_tpq':
                return redirect()->to(base_url('backend/dashboard/kepala-tpq'));
            case 'wali_kelas':
            case 'guru':
            default:
                // Pastikan user memiliki peran guru sebelum redirect
                if (in_array('guru', $userInfo['AllRoles']) || in_groups('Guru')) {
                    return redirect()->to(base_url('backend/dashboard/guru'));
                } else {
                    // Jika tidak memiliki peran guru, redirect ke select-role atau dashboard sesuai peran yang ada
                    if ($userInfo['HasMultipleRoles']) {
                        return redirect()->to(base_url('backend/dashboard/select-role'));
                    } else {
                        // Fallback ke peran pertama yang ada
                        $firstRole = !empty($userInfo['AllRoles']) ? $userInfo['AllRoles'][0] : 'pengguna';
                        switch ($firstRole) {
                            case 'operator':
                                return redirect()->to(base_url('backend/dashboard/operator'));
                            case 'kepala_tpq':
                                return redirect()->to(base_url('backend/dashboard/kepala-tpq'));
                            default:
                                return redirect()->to(base_url('backend/dashboard/select-role'));
                        }
                    }
                }
        }
    }

    /**
     * Halaman pemilihan peran jika user memiliki multiple peran
     */
    public function selectRole()
    {
        $userInfo = $this->getUserInfo();
        
        // Jika tidak multiple peran, redirect ke dashboard sesuai peran
        if (!$userInfo['HasMultipleRoles']) {
            return redirect()->to(base_url('backend/dashboard'));
        }

        $roleMap = [
            'operator' => [
                'label' => 'Operator',
                'icon' => 'user-cog',
                'description' => 'Mengelola data administrasi dan operasional TPQ',
                'color' => 'success'
            ],
            'kepala_tpq' => [
                'label' => 'Kepala TPQ',
                'icon' => 'user-shield',
                'description' => 'Mengawasi dan mengelola seluruh kegiatan akademik TPQ',
                'color' => 'purple'
            ],
            'wali_kelas' => [
                'label' => 'Wali Kelas',
                'icon' => 'chalkboard-teacher',
                'description' => 'Mengelola kelas yang diwalikan',
                'color' => 'info'
            ],
            'guru' => [
                'label' => 'Guru Kelas',
                'icon' => 'chalkboard-teacher',
                'description' => 'Mengajar dan mengelola kelas yang diajar',
                'color' => 'primary'
            ]
        ];

        // Filter role map berdasarkan available roles
        $availableRoles = [];
        foreach ($userInfo['AllRoles'] as $role) {
            if (isset($roleMap[$role])) {
                $availableRoles[$role] = $roleMap[$role];
            }
        }

        $data = [
            'page_title' => 'Pilih Peran',
            'available_roles' => $availableRoles,
            'user_info' => $userInfo
        ];

        return view('backend/dashboard/selectRole', $data);
    }

    /**
     * API untuk switch peran aktif
     */
    public function switchRole()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $input = $this->request->getJSON(true);
        $newRole = $input['role'] ?? null;

        if (empty($newRole)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Peran tidak boleh kosong'
            ]);
        }

        // Validasi peran dengan semua peran yang dimiliki user
        $userInfo = $this->getUserInfo();
        if (!in_array($newRole, $userInfo['AllRoles'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Peran tidak valid atau tidak tersedia untuk user ini'
            ]);
        }

        // Simpan peran aktif ke session
        session()->set('active_role', $newRole);

        // Tentukan redirect URL berdasarkan peran
        $redirectMap = [
            'admin' => base_url('backend/dashboard/admin'),
            'operator' => base_url('backend/dashboard/operator'),
            'kepala_tpq' => base_url('backend/dashboard/kepala-tpq'),
            'wali_kelas' => base_url('backend/dashboard/guru'),
            'guru' => base_url('backend/dashboard/guru')
        ];

        $redirectUrl = $redirectMap[$newRole] ?? base_url('backend/dashboard');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Peran berhasil diubah',
            'redirect' => $redirectUrl
        ]);
    }

    /**
     * Dashboard untuk Guru/Wali Kelas
     */
    public function dashboardGuru()
    {
        $this->initSessionTahunAjaran();
        
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $idKelas = session()->get('IdKelas');
        $idGuru = session()->get('IdGuru');

        // Cek peran aktif
        $userInfo = $this->getUserInfo();
        $activeRole = $userInfo['ActiveRole'];
        
        // Cek apakah user memiliki peran guru (baik dari group 'Guru' atau dari tbl_guru_kelas)
        $hasGuruRole = in_groups('Guru') || in_array('guru', $userInfo['AllRoles']);
        
        // Jika tidak memiliki peran guru, redirect ke halaman utama
        if (!$hasGuruRole) {
            return redirect()->to(base_url());
        }

        // Jika user memiliki multiple peran dan peran aktif bukan 'guru' atau 'wali_kelas',
        // tapi user mengakses dashboard guru secara langsung, set peran aktif ke 'guru'
        if ($userInfo['HasMultipleRoles'] && !in_array($activeRole, ['guru', 'wali_kelas'])) {
            // Jika user mengakses dashboard guru secara langsung, set peran aktif ke 'guru'
            session()->set('active_role', 'guru');
            $activeRole = 'guru';
            $userInfo['ActiveRole'] = 'guru';
        }

        $data = $this->getGuruDashboardData($idTpq, $idTahunAjaran, $idKelas, $idGuru);
        $data = array_merge($data, $userInfo);

        // Jika wali kelas, kumpulkan daftar nama kelas yang diwalikan
        if ($userInfo['PeranLogin'] === 'Wali Kelas') {
            try {
                $waliRows = $this->helpFunctionModel->getDataGuruKelas(
                    IdGuru: $idGuru,
                    IdTpq: $idTpq,
                    IdKelas: $idKelas,
                    IdTahunAjaran: $idTahunAjaran,
                    IdJabatan: 3
                );
                $kelasNames = [];
                if (!empty($waliRows)) {
                    foreach ($waliRows as $row) {
                        if (!empty($row->NamaKelas)) {
                            $kelasNames[$row->NamaKelas] = true;
                        }
                    }
                }
                $data['WaliKelasNamaKelas'] = implode(', ', array_keys($kelasNames));
            } catch (\Throwable $e) {
                $data['WaliKelasNamaKelas'] = '';
            }
        }

        return view('backend/dashboard/dashboardGuru', $data);
    }

    /**
     * Dashboard untuk Operator
     */
    public function dashboardOperator()
    {
        $userInfo = $this->getUserInfo();
        
        // Cek apakah user memiliki peran operator
        if (!in_array('operator', $userInfo['AllRoles'])) {
            return redirect()->to(base_url());
        }

        // Validasi peran aktif
        if ($userInfo['ActiveRole'] !== 'operator' && $userInfo['HasMultipleRoles']) {
            // Jika peran aktif bukan operator, tapi memiliki multiple peran, perlu konfirmasi
            // Tapi karena sudah ada validasi di index(), biarkan saja
        }

        $this->initSessionTahunAjaran();
        
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');

        $data = $this->getAdminDashboardData($idTpq, $idTahunAjaran);
        $data = array_merge($data, $userInfo);

        return view('backend/dashboard/dashboardOperator', $data);
    }

    /**
     * Dashboard untuk Kepala TPQ
     */
    public function dashboardKepalaTpq()
    {
        $userInfo = $this->getUserInfo();
        
        // Cek apakah user memiliki peran kepala_tpq
        if (!in_array('kepala_tpq', $userInfo['AllRoles'])) {
            return redirect()->to(base_url());
        }

        // Validasi peran aktif
        if ($userInfo['ActiveRole'] !== 'kepala_tpq' && $userInfo['HasMultipleRoles']) {
            // Jika peran aktif bukan kepala_tpq, tapi memiliki multiple peran, perlu konfirmasi
        }

        $this->initSessionTahunAjaran();
        
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $idGuru = session()->get('IdGuru');

        // Dashboard Kepala TPQ memiliki akses seperti Admin tapi untuk TPQ nya saja
        $data = $this->getAdminDashboardData($idTpq, $idTahunAjaran);
        $data = array_merge($data, $userInfo);

        return view('backend/dashboard/dashboardKepalaTpq', $data);
    }

    /**
     * Dashboard untuk Admin
     */
    public function dashboardAdmin()
    {
        if (!in_groups('Admin')) {
            return redirect()->to(base_url());
        }

        $this->initSessionTahunAjaran();
        
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');

        $data = $this->getAdminDashboardData($idTpq, $idTahunAjaran);
        $userInfo = $this->getUserInfo();
        $data = array_merge($data, $userInfo);

        return view('backend/dashboard/dashboardAdmin', $data);
    }

    /**
     * Mengambil statistik santri per TPQ dan per kelas (kecuali Alumni)
     * Menggunakan tbl_santri_baru sebagai sumber data utama seperti statistik lama
     * @param mixed $idTahunAjaran
     * @return array
     */
    private function getStatistikSantriPerTpqPerKelas($idTahunAjaran)
    {
        // Ambil semua TPQ
        $tpqList = $this->helpFunctionModel->getDataTpq(0);
        
        // Ambil koneksi database
        $db = db_connect();
        
        // Ambil semua kelas kecuali Alumni
        $kelasList = $db->table('tbl_kelas')
            ->where('NamaKelas !=', 'ALUMNI')
            ->where('NamaKelas !=', 'Alumni')
            ->where('NamaKelas !=', 'alumni')
            ->orderBy('NamaKelas', 'ASC')
            ->get()
            ->getResultArray();
        
        // Inisialisasi array hasil
        $result = [];
        
        // Query untuk mengambil jumlah santri per TPQ dan per kelas
        // Menggunakan tbl_santri_baru sebagai sumber data utama seperti statistik lama
        $query = $db->query("
            SELECT 
                t.IdTpq,
                t.NamaTpq,
                COALESCE(t.KelurahanDesa, t.Alamat, '') as KelurahanDesa,
                k.IdKelas,
                k.NamaKelas,
                COUNT(DISTINCT s.IdSantri) as JumlahSantri
            FROM tbl_santri_baru s
            INNER JOIN tbl_tpq t ON t.IdTpq = s.IdTpq
            LEFT JOIN tbl_kelas k ON k.IdKelas = s.IdKelas
                AND (k.NamaKelas != 'ALUMNI' AND k.NamaKelas != 'Alumni' AND k.NamaKelas != 'alumni')
            WHERE s.Active < 2
            GROUP BY t.IdTpq, t.NamaTpq, t.KelurahanDesa, t.Alamat, k.IdKelas, k.NamaKelas
            ORDER BY t.NamaTpq ASC, k.NamaKelas ASC
        ");
        
        $data = $query->getResultArray();
        
        // Organisir data per TPQ
        foreach ($tpqList as $tpq) {
            $tpqId = $tpq['IdTpq'];
            $tpqData = [
                'IdTpq' => $tpqId,
                'NamaTpq' => $tpq['NamaTpq'],
                'KelurahanDesa' => $tpq['KelurahanDesa'] ?? $tpq['Alamat'] ?? '',
                'Kelas' => [],
                'Total' => 0
            ];
            
            // Inisialisasi jumlah per kelas
            foreach ($kelasList as $kelas) {
                $tpqData['Kelas'][$kelas['IdKelas']] = [
                    'IdKelas' => $kelas['IdKelas'],
                    'NamaKelas' => $kelas['NamaKelas'],
                    'Jumlah' => 0
                ];
            }
            
            // Isi data dari query
            foreach ($data as $row) {
                if ($row['IdTpq'] == $tpqId && !empty($row['IdKelas'])) {
                    if (isset($tpqData['Kelas'][$row['IdKelas']])) {
                        $tpqData['Kelas'][$row['IdKelas']]['Jumlah'] = (int)$row['JumlahSantri'];
                        $tpqData['Total'] += (int)$row['JumlahSantri'];
                    }
                }
            }
            
            $result[] = $tpqData;
        }
        
        return [
            'data' => $result,
            'kelasList' => $kelasList
        ];
    }

    /**
     * Menggabungkan statistik santri dan guru per TPQ dengan menghitung rasio
     * @param array $statistikSantriPerTpq
     * @param array $statistikGuruPerTpq
     * @return array
     */
    private function gabungkanStatistikTpqDenganRasio($statistikSantriPerTpq, $statistikGuruPerTpq)
    {
        // Ambil data TPQ lengkap untuk mendapatkan KelurahanDesa
        $db = db_connect();
        $tpqData = $db->table('tbl_tpq')
            ->select('IdTpq, KelurahanDesa, Alamat')
            ->get()
            ->getResultArray();
        
        // Buat map untuk akses cepat data TPQ
        $tpqMap = [];
        foreach ($tpqData as $tpq) {
            $tpqMap[$tpq['IdTpq']] = $tpq;
        }
        
        // Buat map untuk akses cepat data guru
        $guruMap = [];
        foreach ($statistikGuruPerTpq as $guru) {
            $guruMap[$guru['IdTpq']] = $guru;
        }
        
        // Gabungkan data dan hitung rasio
        $result = [];
        foreach ($statistikSantriPerTpq as $santri) {
            $idTpq = $santri['IdTpq'];
            $totalGuru = $guruMap[$idTpq]['Total'] ?? 0;
            $totalSantri = $santri['Total'] ?? 0;
            $kelurahanDesa = $tpqMap[$idTpq]['KelurahanDesa'] ?? $tpqMap[$idTpq]['Alamat'] ?? '';
            
            // Hitung rasio (Guru:Santri)
            // Bulatkan ke atas karena orang tidak bisa dihitung pecahan
            $rasio = '';
            $rasioNumeric = 0;
            $rasioCeil = 0;
            if ($totalSantri > 0 && $totalGuru > 0) {
                $rasioNumeric = $totalSantri / $totalGuru;
                $rasioCeil = (int)ceil($rasioNumeric); // Bulatkan ke atas
                // Format rasio sebagai 1:X (misalnya 1:10 berarti 1 guru untuk 10 santri)
                $rasio = '1:' . $rasioCeil;
            } else if ($totalSantri > 0 && $totalGuru == 0) {
                $rasio = '-';
                $rasioNumeric = 0;
                $rasioCeil = 0;
            } else {
                $rasio = '-';
                $rasioNumeric = 0;
                $rasioCeil = 0;
            }
            
            // Tentukan warna badge berdasarkan rasio
            $badgeColor = 'secondary'; // default
            if ($rasioCeil > 0) {
                if ($rasioCeil < 9) {
                    $badgeColor = 'danger'; // Merah - rasio < 9
                } else if ($rasioCeil >= 9 && $rasioCeil <= 11) {
                    $badgeColor = 'success'; // Hijau - rasio >= 9 dan <= 11
                } else {
                    $badgeColor = 'info'; // Biru - rasio > 11
                }
            }
            
            $result[] = [
                'IdTpq' => $idTpq,
                'NamaTpq' => $santri['NamaTpq'],
                'KelurahanDesa' => $kelurahanDesa,
                'TotalSantri' => $totalSantri,
                'TotalGuru' => $totalGuru,
                'Rasio' => $rasio,
                'RasioNumeric' => $rasioNumeric,
                'RasioCeil' => $rasioCeil,
                'BadgeColor' => $badgeColor,
                'LakiLakiSantri' => $santri['LakiLaki'] ?? 0,
                'PerempuanSantri' => $santri['Perempuan'] ?? 0,
                'LakiLakiGuru' => $guruMap[$idTpq]['LakiLaki'] ?? 0,
                'PerempuanGuru' => $guruMap[$idTpq]['Perempuan'] ?? 0,
            ];
        }
        
        return $result;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Hapus session
        session()->destroy();

        // Clear localStorage untuk selectedDashboard (akan dihandle di client side)
        // Redirect ke halaman login
        return redirect()->to(base_url('login'));
    }

    /**
     * Update tahun ajaran dan list kelas dalam session
     */
    public function updateTahunAjaranDanKelas()
    {
        // Cek apakah request adalah AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        // Cek apakah user sudah login
        if (!logged_in()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User belum login'
            ]);
        }

        // Cek apakah user adalah Guru
        if (!in_groups('Guru') && !in_groups('Operator')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya guru yang dapat mengubah tahun ajaran'
            ]);
        }

        // Ambil data dari request
        $input = $this->request->getJSON(true);
        $tahunAjaran = $input['tahunAjaran'] ?? null;

        // Validasi input
        if (empty($tahunAjaran)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tahun ajaran tidak boleh kosong'
            ]);
        }

        // Cek apakah tahun ajaran ada dalam list yang diizinkan
        $tahunAjaranList = session()->get('IdTahunAjaranList');
        $tahunAjaranSaatIni = $this->helpFunctionModel->getTahunAjaranSaatIni();

        // Jika IdTahunAjaranList null atau kosong, set ke tahun ajaran saat ini
        if (empty($tahunAjaranList) || !is_array($tahunAjaranList)) {
            $tahunAjaranList = [$tahunAjaranSaatIni];
            session()->set('IdTahunAjaranList', $tahunAjaranList);
        }

        // Pastikan tahun ajaran saat ini ada di list
        if (!in_array($tahunAjaranSaatIni, $tahunAjaranList)) {
            $tahunAjaranList[] = $tahunAjaranSaatIni;
            session()->set('IdTahunAjaranList', $tahunAjaranList);
        }

        if (!in_array($tahunAjaran, $tahunAjaranList)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tahun ajaran tidak valid'
            ]);
        }

        try {
            // Update session IdTahunAjaran
            session()->set('IdTahunAjaran', $tahunAjaran);

            // Ambil list kelas berdasarkan tahun ajaran yang dipilih dan IdGuru
            $idTpq = session()->get('IdTpq');
            $idGuru = session()->get('IdGuru');
            $listKelas = $this->helpFunctionModel->getListKelas($idTpq, $tahunAjaran, null, $idGuru);

            // Ekstrak IdKelas dari hasil query
            $idKelasList = array_map(function ($kelas) {
                return $kelas->IdKelas;
            }, $listKelas);

            // Update session IdKelasList
            session()->set('IdKelas', $idKelasList);

            // Log aktivitas (opsional)
            log_message('info', 'User ' . user()->username . ' mengubah tahun ajaran ke ' . $tahunAjaran . ' dengan ' . count($idKelasList) . ' kelas');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tahun ajaran berhasil diubah',
                'tahunAjaran' => $tahunAjaran,
                'kelasCount' => count($idKelasList),
                'kelasList' => $listKelas
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error update tahun ajaran: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah tahun ajaran'
            ]);
        }
    }
}

