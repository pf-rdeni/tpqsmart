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
use App\Models\ToolsModel;

/**
 * Dashboard Controller
 * Menangani halaman dashboard dan manajemen session untuk dashboard
 */
class Dashboard extends BaseController
{
    protected $helpFunctionModel;
    protected $santriModel;
    protected $tabunganModel;
    protected $toolsModel;
    protected $db;
    
    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
        $this->santriModel = new SantriModel();
        $this->tabunganModel = new TabunganModel();
        $this->toolsModel = new ToolsModel();
        $this->db = \Config\Database::connect();
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

        // Statistik progress penilaian per kelas (untuk kelas yang diajar guru)
        $statistikProgressNilaiPerKelas = $this->getStatistikProgressNilaiPerKelasGuru($idTahunAjaran, $idTpq, $idKelas);

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
            'StatistikProgressNilaiPerKelas' => $statistikProgressNilaiPerKelas,
        ];
    }

    private function getAdminDashboardData($idTpq, $idTahunAjaran)
    {
        $listKelas = $this->helpFunctionModel->getListKelas(
            IdTpq: $idTpq,
            IdTahunAjaran: $idTahunAjaran,
        );

        // Ambil jumlah santri per kelas (diperlukan untuk Operator dan Kepala TPQ)
        $jumlahSantriPerKelas = $this->helpFunctionModel->getJumlahSantriPerKelas(
            IdTpq: $idTpq,
            kelasIds: array_map(function ($kelas) {
                return is_object($kelas) ? $kelas->IdKelas : $kelas;
            }, $listKelas)
        );

        // Status input nilai per kelas (diperlukan untuk Operator dan Kepala TPQ)
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

        // Status input nilai semester (diperlukan untuk Operator dan Kepala TPQ)
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
        $statistikProgressNilaiPerTpq = [];
        if ($idTpqForQuery == 0) {
            $statistikSantriPerTpq = $this->helpFunctionModel->getStatistikSantriPerTpq();
            $statistikGuruPerTpq = $this->helpFunctionModel->getStatistikGuruPerTpq();
            $statistikSantriPerTpqPerKelas = $this->getStatistikSantriPerTpqPerKelas($idTahunAjaran);
            
            // Gabungkan data santri dan guru untuk menghitung rasio
            $statistikTpqDenganRasio = $this->gabungkanStatistikTpqDenganRasio($statistikSantriPerTpq, $statistikGuruPerTpq);

            // Statistik progress penilaian per TPQ dan per kelas (menggunakan fungsi yang lebih cepat)
            $statistikProgressNilaiPerTpq = $this->getStatistikProgressNilaiPerTpqSingle($idTahunAjaran, 0);
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
            'StatistikProgressNilaiPerTpq' => $statistikProgressNilaiPerTpq,
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
        // Simpan query parameter after_login untuk dipertahankan saat redirect
        $afterLogin = $this->request->getGet('after_login');
        $queryString = $afterLogin ? '?after_login=1' : '';

        // Cek jika user adalah JuriSertifikasi, redirect ke dashboard sertifikasi
        if (in_groups('JuriSertifikasi')) {
            return redirect()->to(base_url('backend/sertifikasi/dashboard') . $queryString);
        }

        // Cek jika user adalah PanitiaSertifikasi, redirect ke dashboard panitia sertifikasi
        if (in_groups('PanitiaSertifikasi')) {
            return redirect()->to(base_url('backend/sertifikasi/dashboardPanitiaSertifikasi') . $queryString);
        }

        // Cek jika user adalah Santri, redirect ke dashboard santri
        if (in_groups('Santri')) {
            return redirect()->to(base_url('backend/dashboard/santri') . $queryString);
        }

        // Cek jika user adalah Juri atau Panitia, redirect ke dashboard munaqosah
        if (in_groups('Juri') || in_groups('Panitia')) {
            return redirect()->to(base_url('backend/munaqosah/dashboard-munaqosah') . $queryString);
        }

        // Untuk Admin dan Operator, cek query parameter dashboard untuk redirect server-side
        if (in_groups('Admin') || in_groups('Operator')) {
            $dashboardParam = $this->request->getGet('dashboard');
            if ($dashboardParam === 'munaqosah') {
                return redirect()->to(base_url('backend/munaqosah/dashboard-munaqosah') . $queryString);
            }
            if ($dashboardParam === 'sertifikasi' && in_groups('Admin')) {
                return redirect()->to(base_url('backend/sertifikasi/dashboard-admin') . $queryString);
            }
            if ($dashboardParam === 'myauth' && in_groups('Admin')) {
                return redirect()->to(base_url('backend/auth') . $queryString);
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
            return redirect()->to(base_url('backend/dashboard/select-role') . $queryString);
        }
        
        // Set active role ke session
        session()->set('active_role', $activeRole);

        // Redirect berdasarkan peran aktif
        switch ($activeRole) {
            case 'admin':
                return redirect()->to(base_url('backend/dashboard/admin') . $queryString);
            case 'operator':
                return redirect()->to(base_url('backend/dashboard/operator') . $queryString);
            case 'kepala_tpq':
                return redirect()->to(base_url('backend/dashboard/kepala-tpq') . $queryString);
            case 'wali_kelas':
            case 'guru':
            default:
                // Pastikan user memiliki peran guru sebelum redirect
                if (in_array('guru', $userInfo['AllRoles']) || in_groups('Guru')) {
                    return redirect()->to(base_url('backend/dashboard/guru') . $queryString);
                } else {
                    // Jika tidak memiliki peran guru, redirect ke select-role atau dashboard sesuai peran yang ada
                    if ($userInfo['HasMultipleRoles']) {
                        return redirect()->to(base_url('backend/dashboard/select-role') . $queryString);
                    } else {
                        // Fallback ke peran pertama yang ada
                        $firstRole = !empty($userInfo['AllRoles']) ? $userInfo['AllRoles'][0] : 'pengguna';
                        switch ($firstRole) {
                            case 'operator':
                                return redirect()->to(base_url('backend/dashboard/operator') . $queryString);
                            case 'kepala_tpq':
                                return redirect()->to(base_url('backend/dashboard/kepala-tpq') . $queryString);
                            default:
                                return redirect()->to(base_url('backend/dashboard/select-role') . $queryString);
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
        
        // Statistik serah terima rapor per kelas (hanya kelas yang diajar)
        $statistikSerahTerimaRaporGanjil = $this->getStatistikSerahTerimaRaporPerKelas($idTpq, $idTahunAjaran, $idKelas, 'Ganjil');
        $statistikSerahTerimaRaporGenap = $this->getStatistikSerahTerimaRaporPerKelas($idTpq, $idTahunAjaran, $idKelas, 'Genap');
        $data['StatistikSerahTerimaRapor'] = [
            'Ganjil' => $statistikSerahTerimaRaporGanjil,
            'Genap' => $statistikSerahTerimaRaporGenap
        ];
        
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

        // Tentukan IdJabatan untuk URL showDetail
        // IdJabatan = 3 untuk Wali Kelas (bisa edit), 4 untuk Guru Pendamping (view saja)
        $userJabatanList = session()->get('IdJabatan') ?? [];
        $isWaliKelas = false;
        if (is_array($userJabatanList)) {
            $isWaliKelas = in_array(3, $userJabatanList);
        } else {
            $isWaliKelas = ($userJabatanList == 3);
        }
        $data['IdJabatanForUrl'] = $isWaliKelas ? 3 : 4;

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

        // Statistik progress penilaian per TPQ dan per kelas (untuk TPQ mereka saja)
        $statistikProgressNilaiPerTpq = $this->getStatistikProgressNilaiPerTpqSingle($idTahunAjaran, $idTpq);
        $data['StatistikProgressNilaiPerTpq'] = $statistikProgressNilaiPerTpq;

        // Statistik serah terima rapor per kelas (semua kelas kecuali Alumni)
        $statistikSerahTerimaRaporGanjil = $this->getStatistikSerahTerimaRaporPerKelas($idTpq, $idTahunAjaran, null, 'Ganjil');
        $statistikSerahTerimaRaporGenap = $this->getStatistikSerahTerimaRaporPerKelas($idTpq, $idTahunAjaran, null, 'Genap');
        $data['StatistikSerahTerimaRapor'] = [
            'Ganjil' => $statistikSerahTerimaRaporGanjil,
            'Genap' => $statistikSerahTerimaRaporGenap
        ];

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

        // Statistik progress penilaian per TPQ dan per kelas (untuk TPQ mereka saja)
        $statistikProgressNilaiPerTpq = $this->getStatistikProgressNilaiPerTpqSingle($idTahunAjaran, $idTpq);
        $data['StatistikProgressNilaiPerTpq'] = $statistikProgressNilaiPerTpq;

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
     * Dashboard untuk Santri
     */
    public function dashboardSantri()
    {
        // Cek apakah user adalah Santri
        if (!in_groups('Santri')) {
            return redirect()->to(base_url());
        }

        // Ambil NIK dari user yang login
        $userNik = user()->nik ?? null;
        if (empty($userNik)) {
            return redirect()->to(base_url())->with('error', 'Data user tidak valid');
        }

        // Inisialisasi session tahun ajaran
        $this->initSessionTahunAjaran();
        
        $idTahunAjaran = session()->get('IdTahunAjaran');
        
        // Ambil data santri berdasarkan NIK
        $santriModel = new \App\Models\SantriBaruModel();
        $santriData = $santriModel->getSantriByNik($userNik);
        
        if (empty($santriData)) {
            return redirect()->to(base_url())->with('error', 'Data santri tidak ditemukan');
        }

        $idSantri = $santriData['IdSantri'];
        $idTpq = $santriData['IdTpq'];
        $idKelas = $santriData['IdKelas'] ?? null;

        // Set session untuk santri
        session()->set('IdSantri', $idSantri);
        session()->set('IdTpq', $idTpq);
        if ($idKelas) {
            session()->set('IdKelas', $idKelas);
        }

        // Ambil data kelas dari tbl_kelas_santri untuk tahun ajaran saat ini
        $kelasSantri = $this->db->table('tbl_kelas_santri ks')
            ->select('ks.IdKelas, k.NamaKelas')
            ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'inner')
            ->where('ks.IdSantri', $idSantri)
            ->where('ks.IdTahunAjaran', $idTahunAjaran)
            ->where('ks.Status', 1)
            ->orderBy('k.NamaKelas', 'ASC')
            ->get()
            ->getRowArray();

        // Jika ada kelas di tbl_kelas_santri, gunakan itu
        if (!empty($kelasSantri)) {
            $idKelas = $kelasSantri['IdKelas'];
            $namaKelas = $kelasSantri['NamaKelas'];
        } else {
            // Fallback ke IdKelas dari tbl_santri_baru
            $namaKelas = $santriData['NamaKelas'] ?? 'Tidak Ada Kelas';
        }

        // Ambil data dashboard
        $data = $this->getSantriDashboardData($idSantri, $idTpq, $idKelas, $idTahunAjaran);
        
        // Tambahkan info user
        $data['page_title'] = 'Dashboard Santri';
        $data['NamaLogin'] = $santriData['NamaSantri'] ?? 'Santri';
        $data['PeranLogin'] = 'Santri';
        $data['IdSantri'] = $idSantri;
        $data['IdTpq'] = $idTpq;
        $data['IdKelas'] = $idKelas;
        $data['NamaKelas'] = $namaKelas;
        $data['TahunAjaran'] = $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran);

        return view('backend/dashboard/dashboardSantri', $data);
    }

    /**
     * Mengambil data dashboard untuk Santri
     */
    private function getSantriDashboardData($idSantri, $idTpq, $idKelas, $idTahunAjaran)
    {
        $data = [];
        
        // Ambil data profil santri lengkap
        $santriModel = new \App\Models\SantriBaruModel();
        $data['profilSantri'] = $santriModel->getProfilDetailSantri($idSantri);

        // Ambil data nilai untuk semester Ganjil dan Genap
        $nilaiModel = new \App\Models\NilaiModel();
        $nilaiGanjil = $nilaiModel->getDataNilaiPerSantri($idTpq, $idTahunAjaran, $idKelas, $idSantri, 'Ganjil');
        $nilaiGenap = $nilaiModel->getDataNilaiPerSantri($idTpq, $idTahunAjaran, $idKelas, $idSantri, 'Genap');
        
        // Tentukan semester saat ini berdasarkan bulan
        // Semester Ganjil: Juli-Desember (bulan 7-12)
        // Semester Genap: Januari-Juni (bulan 1-6)
        $currentMonth = (int)date('m');
        $semesterSaatIni = ($currentMonth >= 7) ? 'Ganjil' : 'Genap';
        
        // Tentukan apakah tahun ajaran ini adalah tahun ajaran saat ini
        $tahunAjaranSaatIni = $this->helpFunctionModel->getTahunAjaranSaatIni();
        $isTahunAjaranSaatIni = ($idTahunAjaran == $tahunAjaranSaatIni);
        
        // Tentukan apakah harus hide semester
        $hideGanjil = false;
        $hideGenap = false;
        
        if ($isTahunAjaranSaatIni) {
            // Jika tahun ajaran saat ini
            if ($semesterSaatIni == 'Ganjil') {
                // Semester saat ini Ganjil: hide Ganjil dan Genap
                $hideGanjil = true;
                $hideGenap = true;
            } else {
                // Semester saat ini Genap: hide Genap saja
                $hideGanjil = false;
                $hideGenap = true;
            }
        } else {
            // Tahun ajaran sebelumnya: tampilkan semua
            $hideGanjil = false;
            $hideGenap = false;
        }
        
        $data['nilaiGanjil'] = $nilaiGanjil;
        $data['nilaiGenap'] = $nilaiGenap;
        $data['hideGanjil'] = $hideGanjil;
        $data['hideGenap'] = $hideGenap;
        $data['semesterSaatIni'] = $semesterSaatIni;
        $data['isTahunAjaranSaatIni'] = $isTahunAjaranSaatIni;

        // Hitung rata-rata nilai per semester
        $data['rataRataGanjil'] = $this->hitungRataRataNilaiSantri($nilaiGanjil);
        $data['rataRataGenap'] = $this->hitungRataRataNilaiSantri($nilaiGenap);

        // Ambil data absensi per semester
        $absensiModel = new \App\Models\AbsensiModel();
        $data['absensiGanjil'] = $this->getAbsensiSantriPerSemester($idSantri, $idTpq, $idKelas, $idTahunAjaran, 'Ganjil');
        $data['absensiGenap'] = $this->getAbsensiSantriPerSemester($idSantri, $idTpq, $idKelas, $idTahunAjaran, 'Genap');

        // Ambil data tabungan jika ada
        $tabunganModel = new \App\Models\TabunganModel();
        $data['tabungan'] = $this->getTabunganSantri($idSantri, $idTahunAjaran);

        // Ambil data prestasi
        $prestasiModel = new \App\Models\PrestasiModel();
        $data['prestasi'] = $this->getPrestasiSantri($idSantri, $idTpq, $idTahunAjaran);

        // Ambil data status serah terima rapor
        $serahTerimaRaporModel = new \App\Models\SerahTerimaRaporModel();
        $data['serahTerimaRapor'] = $this->getSerahTerimaRaporSantri($idSantri, $idTahunAjaran);

        return $data;
    }

    /**
     * Ambil data prestasi santri
     */
    private function getPrestasiSantri($idSantri, $idTpq, $idTahunAjaran)
    {
        $prestasiModel = new \App\Models\PrestasiModel();
        
        // Ambil semua prestasi
        $allPrestasi = $prestasiModel
            ->select('tbl_prestasi.*, tbl_materi_pelajaran.NamaMateri, tbl_materi_pelajaran.Kategori')
            ->join('tbl_materi_pelajaran', 'tbl_prestasi.IdMateriPelajaran = tbl_materi_pelajaran.IdMateri')
            ->where('tbl_prestasi.IdSantri', $idSantri)
            ->where('tbl_prestasi.IdTpq', $idTpq)
            ->orderBy('tbl_prestasi.updated_at', 'DESC')
            ->orderBy('tbl_prestasi.Tanggal', 'DESC')
            ->findAll();

        // Ambil prestasi terbaru (5 terakhir)
        $prestasiTerbaru = array_slice($allPrestasi, 0, 5);

        // Hitung statistik
        $totalPrestasi = count($allPrestasi);
        $prestasiByJenis = [];
        foreach ($allPrestasi as $prestasi) {
            $jenis = $prestasi['JenisPrestasi'] ?? 'Lainnya';
            if (!isset($prestasiByJenis[$jenis])) {
                $prestasiByJenis[$jenis] = 0;
            }
            $prestasiByJenis[$jenis]++;
        }

        return [
            'total' => $totalPrestasi,
            'terbaru' => $prestasiTerbaru,
            'byJenis' => $prestasiByJenis
        ];
    }

    /**
     * Ambil data serah terima rapor untuk santri
     */
    private function getSerahTerimaRaporSantri($idSantri, $idTahunAjaran)
    {
        $serahTerimaRaporModel = new \App\Models\SerahTerimaRaporModel();
        
        // Ambil status untuk semester Ganjil dan Genap
        $statusGanjil = $serahTerimaRaporModel->getLatestStatus($idSantri, $idTahunAjaran, 'Ganjil');
        $statusGenap = $serahTerimaRaporModel->getLatestStatus($idSantri, $idTahunAjaran, 'Genap');
        
        // Ambil semua transaksi untuk kedua semester
        $allTransactionsGanjil = $serahTerimaRaporModel->getBySantri($idSantri, $idTahunAjaran, 'Ganjil');
        $allTransactionsGenap = $serahTerimaRaporModel->getBySantri($idSantri, $idTahunAjaran, 'Genap');
        
        return [
            'ganjil' => [
                'status' => $statusGanjil,
                'transactions' => $allTransactionsGanjil
            ],
            'genap' => [
                'status' => $statusGenap,
                'transactions' => $allTransactionsGenap
            ]
        ];
    }

    /**
     * Ambil statistik serah terima rapor per kelas
     * @param mixed $idTpq
     * @param mixed $idTahunAjaran
     * @param mixed $idKelas Array of kelas IDs atau null untuk semua kelas (kecuali Alumni)
     * @param mixed $semester 'Ganjil' atau 'Genap'
     * @return array Statistik per kelas
     */
    private function getStatistikSerahTerimaRaporPerKelas($idTpq, $idTahunAjaran, $idKelas = null, $semester = null)
    {
        $serahTerimaRaporModel = new \App\Models\SerahTerimaRaporModel();
        $db = \Config\Database::connect();

        // Jika semester tidak diberikan, gunakan semester saat ini
        if (empty($semester)) {
            $currentMonth = (int)date('n');
            $semester = ($currentMonth >= 7 && $currentMonth <= 12) ? 'Ganjil' : 'Genap';
        }

        // Ambil semua santri aktif per kelas dari tbl_kelas_santri (kecuali Alumni)
        // Pastikan: ks.Status = 1 (aktif di kelas)
        $builder = $db->table('tbl_kelas_santri ks');
        $builder->select('ks.IdKelas, k.NamaKelas, ks.IdSantri');
        $builder->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left');
        $builder->where('ks.IdTpq', $idTpq);
        $builder->where('ks.IdTahunAjaran', $idTahunAjaran);
        $builder->where('ks.Status', 1); // Status aktif di tbl_kelas_santri

        // Filter kelas jika diberikan
        if (!empty($idKelas)) {
            if (is_array($idKelas)) {
                $builder->whereIn('ks.IdKelas', $idKelas);
            } else {
                $builder->where('ks.IdKelas', $idKelas);
            }
        }
        
        $santriPerKelas = $builder->get()->getResultArray();

        // Kelompokkan santri per kelas
        $kelasData = [];
        foreach ($santriPerKelas as $row) {
            $idKelasRow = $row['IdKelas'];
            $namaKelas = $row['NamaKelas'];
            $idSantri = $row['IdSantri'];

            if (!isset($kelasData[$idKelasRow])) {
                $kelasData[$idKelasRow] = [
                    'IdKelas' => $idKelasRow,
                    'NamaKelas' => $namaKelas,
                    'TotalSantri' => 0,
                    'BelumDiserahkan' => 0,
                    'SudahDiserahkan' => 0,
                    'SudahDikembalikan' => 0
                ];
            }

            $kelasData[$idKelasRow]['TotalSantri']++;
        }

        // Ambil status terbaru untuk setiap santri
        foreach ($kelasData as $idKelasRow => &$kelas) {
            // Ambil semua santri di kelas ini
            $santriIds = [];
            foreach ($santriPerKelas as $row) {
                if ($row['IdKelas'] == $idKelasRow) {
                    $santriIds[] = $row['IdSantri'];
                }
            }

            if (empty($santriIds)) {
                continue;
            }

            // Ambil status terbaru untuk semua santri menggunakan subquery untuk efisiensi
            // Gunakan window function approach dengan subquery
            $subquery = $db->table('tbl_serah_terima_rapor str2');
            $subquery->select('str2.IdSantri, str2.Status, str2.TanggalTransaksi');
            $subquery->whereIn('str2.IdSantri', $santriIds);
            $subquery->where('str2.idTahunAjaran', $idTahunAjaran);
            $subquery->where('str2.Semester', $semester);
            $subquery->orderBy('str2.TanggalTransaksi', 'DESC');
            
            // Ambil status terbaru per santri (ambil yang pertama setelah diurutkan)
            $allStatus = $subquery->get()->getResultArray();

            // Kelompokkan status per santri (ambil yang terbaru berdasarkan TanggalTransaksi)
            $statusPerSantri = [];
            foreach ($allStatus as $statusRow) {
                $idSantri = $statusRow['IdSantri'];
                if (!isset($statusPerSantri[$idSantri])) {
                    $statusPerSantri[$idSantri] = $statusRow['Status'];
                }
            }

            // Hitung statistik
            foreach ($santriIds as $idSantri) {
                $status = $statusPerSantri[$idSantri] ?? 'Belum Diserahkan';
                
                if ($status === 'Belum Diserahkan') {
                    $kelas['BelumDiserahkan']++;
                } elseif ($status === 'Sudah Diserahkan') {
                    $kelas['SudahDiserahkan']++;
                } elseif ($status === 'Sudah Dikembalikan') {
                    $kelas['SudahDikembalikan']++;
                }
            }
        }

        // Konversi ke array indexed
        $result = array_values($kelasData);
        
        // Urutkan berdasarkan NamaKelas
        usort($result, function($a, $b) {
            return strcmp($a['NamaKelas'], $b['NamaKelas']);
        });

        return $result;
    }

    /**
     * Hitung rata-rata nilai santri
     */
    private function hitungRataRataNilaiSantri($nilaiData)
    {
        if (empty($nilaiData)) {
            return 0;
        }

        $total = 0;
        $count = 0;
        
        foreach ($nilaiData as $nilai) {
            $nilaiValue = is_object($nilai) ? ($nilai->Nilai ?? 0) : ($nilai['Nilai'] ?? 0);
            if ($nilaiValue > 0) {
                $total += $nilaiValue;
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 2) : 0;
    }

    /**
     * Ambil data absensi santri per semester
     */
    private function getAbsensiSantriPerSemester($idSantri, $idTpq, $idKelas, $idTahunAjaran, $semester)
    {
        // Tentukan tanggal awal dan akhir berdasarkan semester
        $tahunAwal = substr($idTahunAjaran, 0, 4);
        $tahunAkhir = substr($idTahunAjaran, 4, 4);

        if ($semester === 'Ganjil') {
            $startDate = $tahunAwal . '-07-01';
            $endDate = $tahunAwal . '-12-31';
        } else {
            $startDate = $tahunAkhir . '-01-01';
            $endDate = $tahunAkhir . '-06-30';
        }

        $absensiModel = new \App\Models\AbsensiModel();
        $absensiData = $absensiModel
            ->where('IdSantri', $idSantri)
            ->where('IdTpq', $idTpq)
            ->where('IdKelas', $idKelas)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('Tanggal >=', $startDate)
            ->where('Tanggal <=', $endDate)
            ->findAll();

        $statistik = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alfa' => 0,
            'total' => count($absensiData)
        ];

        foreach ($absensiData as $absensi) {
            $kehadiran = strtolower(trim($absensi['Kehadiran'] ?? ''));
            if ($kehadiran === 'hadir') {
                $statistik['hadir']++;
            } elseif ($kehadiran === 'izin') {
                $statistik['izin']++;
            } elseif ($kehadiran === 'sakit') {
                $statistik['sakit']++;
            } elseif ($kehadiran === 'alfa') {
                $statistik['alfa']++;
            }
        }

        // Hitung persentase
        if ($statistik['total'] > 0) {
            $statistik['persenHadir'] = round(($statistik['hadir'] / $statistik['total']) * 100, 1);
            $statistik['persenIzin'] = round(($statistik['izin'] / $statistik['total']) * 100, 1);
            $statistik['persenSakit'] = round(($statistik['sakit'] / $statistik['total']) * 100, 1);
            $statistik['persenAlfa'] = round(($statistik['alfa'] / $statistik['total']) * 100, 1);
        } else {
            $statistik['persenHadir'] = 0;
            $statistik['persenIzin'] = 0;
            $statistik['persenSakit'] = 0;
            $statistik['persenAlfa'] = 0;
        }

        return $statistik;
    }

    /**
     * Ambil data tabungan santri
     */
    private function getTabunganSantri($idSantri, $idTahunAjaran)
    {
        $tabunganModel = new \App\Models\TabunganModel();
        
        // Hitung saldo menggunakan method yang ada (semua transaksi, tidak hanya tahun ajaran tertentu)
        $saldo = $tabunganModel->calculateBalance($idSantri);
        
        // Ambil transaksi terbaru untuk tahun ajaran saat ini
        $builder = $tabunganModel->where('IdSantri', $idSantri);
        if (!empty($idTahunAjaran)) {
            $builder->where('IdTahunAjaran', $idTahunAjaran);
        }
        $transaksi = $builder->orderBy('TanggalTransaksi', 'DESC')
            ->orderBy('CreatedAt', 'DESC')
            ->limit(10)
            ->findAll();

        return [
            'saldo' => $saldo ?? 0,
            'transaksi' => $transaksi ?? []
        ];
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
        
        // Inisialisasi total per kolom (per kelas)
        $totalPerKelas = [];
        foreach ($kelasList as $kelas) {
            $totalPerKelas[$kelas['IdKelas']] = 0;
        }
        $grandTotal = 0;
        
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
                        $jumlah = (int)$row['JumlahSantri'];
                        $tpqData['Kelas'][$row['IdKelas']]['Jumlah'] = $jumlah;
                        $tpqData['Total'] += $jumlah;
                        
                        // Hitung total per kolom
                        $totalPerKelas[$row['IdKelas']] += $jumlah;
                    }
                }
            }
            
            // Hitung grand total
            $grandTotal += $tpqData['Total'];
            
            $result[] = $tpqData;
        }
        
        return [
            'data' => $result,
            'kelasList' => $kelasList,
            'totalPerKelas' => $totalPerKelas,
            'grandTotal' => $grandTotal
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
     * Mengambil statistik progress penilaian per kelas untuk guru (hanya kelas yang diajar)
     * Menggunakan fungsi getStatistikProgressNilaiPerTpqSingle yang lebih cepat
     * @param mixed $idTahunAjaran
     * @param mixed $idTpq
     * @param mixed $idKelas Array atau single ID kelas yang diajar guru
     * @return array
     */
    private function getStatistikProgressNilaiPerKelasGuru($idTahunAjaran, $idTpq, $idKelas)
    {
        // Normalisasi idKelas menjadi array
        if (is_array($idKelas)) {
            $kelasIds = array_filter($idKelas);
        } else {
            $kelasIds = !empty($idKelas) ? [$idKelas] : [];
        }

        if (empty($kelasIds)) {
            return ['Ganjil' => [], 'Genap' => []];
        }

        // Gunakan fungsi yang lebih cepat untuk mengambil data
        $statistikData = $this->getStatistikProgressNilaiPerTpqSingle($idTahunAjaran, $idTpq);

        // Konversi struktur dari per-TPQ menjadi per-kelas dan filter hanya kelas yang diajar
        $resultGanjil = [];
        $resultGenap = [];

        // Proses data semester Ganjil
        // Catatan: Konversi MDA sudah dilakukan di getStatistikProgressNilaiPerTpqSingle untuk single TPQ
        if (!empty($statistikData['Ganjil'])) {
            foreach ($statistikData['Ganjil'] as $tpqData) {
                if (!empty($tpqData['Kelas'])) {
                    foreach ($tpqData['Kelas'] as $kelas) {
                        // Filter hanya kelas yang diajar guru
                        if (in_array($kelas['IdKelas'], $kelasIds)) {
                            $resultGanjil[] = [
                                'IdKelas' => $kelas['IdKelas'],
                                'NamaKelas' => $kelas['NamaKelas'], // Sudah dikonversi MDA di getStatistikProgressNilaiPerTpqSingle
                                'TotalSantri' => $kelas['TotalSantri'],
                                'SudahDinilai' => $kelas['SudahDinilai'],
                                'BelumDinilai' => $kelas['BelumDinilai'],
                                'PersentaseSudah' => $kelas['PersentaseSudah'],
                                'StatusKelas' => $kelas['StatusKelas'] ?? null,
                                'StatusKelasColor' => $kelas['StatusKelasColor'] ?? null,
                                'Santri' => $kelas['Santri'] ?? []
                            ];
                        }
                    }
                }
            }
        }

        // Proses data semester Genap
        // Catatan: Konversi MDA sudah dilakukan di getStatistikProgressNilaiPerTpqSingle untuk single TPQ
        if (!empty($statistikData['Genap'])) {
            foreach ($statistikData['Genap'] as $tpqData) {
                if (!empty($tpqData['Kelas'])) {
                    foreach ($tpqData['Kelas'] as $kelas) {
                        // Filter hanya kelas yang diajar guru
                        if (in_array($kelas['IdKelas'], $kelasIds)) {
                            $resultGenap[] = [
                                'IdKelas' => $kelas['IdKelas'],
                                'NamaKelas' => $kelas['NamaKelas'], // Sudah dikonversi MDA di getStatistikProgressNilaiPerTpqSingle
                                'TotalSantri' => $kelas['TotalSantri'],
                                'SudahDinilai' => $kelas['SudahDinilai'],
                                'BelumDinilai' => $kelas['BelumDinilai'],
                                'PersentaseSudah' => $kelas['PersentaseSudah'],
                                'StatusKelas' => $kelas['StatusKelas'] ?? null,
                                'StatusKelasColor' => $kelas['StatusKelasColor'] ?? null,
                                'Santri' => $kelas['Santri'] ?? []
                            ];
                        }
                    }
                }
            }
        }

        // Urutkan berdasarkan IdKelas sesuai urutan $kelasIds
        usort($resultGanjil, function ($a, $b) use ($kelasIds) {
            $posA = array_search($a['IdKelas'], $kelasIds);
            $posB = array_search($b['IdKelas'], $kelasIds);
            return $posA <=> $posB;
        });

        usort($resultGenap, function ($a, $b) use ($kelasIds) {
            $posA = array_search($a['IdKelas'], $kelasIds);
            $posB = array_search($b['IdKelas'], $kelasIds);
            return $posA <=> $posB;
        });

        return [
            'Ganjil' => $resultGanjil,
            'Genap' => $resultGenap
        ];
    }

    /**
     * Mengambil statistik progress penilaian per TPQ dan per kelas untuk satu atau multiple TPQ (Operator/Kepala TPQ/Admin)
     * @param mixed $idTahunAjaran
     * @param mixed $idTpq Single ID atau array of IDs (0 untuk semua TPQ)
     * @return array
     */
    private function getStatistikProgressNilaiPerTpqSingle($idTahunAjaran, $idTpq)
    {
        // Normalisasi $idTpq menjadi array
        if ($idTpq == 0 || $idTpq === '0' || empty($idTpq)) {
            // Untuk admin, ambil semua TPQ
            $tpqList = $this->helpFunctionModel->getDataTpq(0);
            $tpqIds = array_column($tpqList, 'IdTpq');
        } elseif (is_array($idTpq)) {
            $tpqIds = array_filter($idTpq);
            $tpqList = $this->helpFunctionModel->getDataTpq($idTpq);
        } else {
            $tpqIds = [$idTpq];
            $tpqList = $this->helpFunctionModel->getDataTpq($idTpq);
        }

        if (empty($tpqList) || empty($tpqIds)) {
            return ['Ganjil' => [], 'Genap' => []];
        }

        // Buat mapping TPQ untuk akses cepat
        $tpqMap = [];
        foreach ($tpqList as $tpq) {
            $tpqMap[$tpq['IdTpq']] = $tpq;
        }

        // Buat placeholder untuk IN clause
        $placeholders = implode(',', array_fill(0, count($tpqIds), '?'));

        // Ambil koneksi database
        $db = db_connect();

        // Optimasi: Hitung completion per santri terlebih dahulu untuk semester Ganjil (menggunakan temporary table approach)
        // Query untuk mengambil data per santri untuk semester Ganjil (lebih efisien, tanpa subquery di JOIN)
        $querySantriGanjil = $db->query("
            SELECT 
                n.IdTpq,
                n.IdKelas,
                n.IdSantri,
                s.NamaSantri,
                s.PhotoProfil,
                COUNT(DISTINCT n.IdMateri) as total_materi,
                COUNT(DISTINCT CASE WHEN n.Nilai != 0 THEN n.IdMateri END) as materi_terisi
            FROM tbl_nilai n
            INNER JOIN tbl_santri_baru s ON s.IdSantri = n.IdSantri AND s.Active = 1
            WHERE n.IdTahunAjaran = ?
                AND n.Semester = 'Ganjil'
                AND n.IdTpq IN ($placeholders)
            GROUP BY n.IdTpq, n.IdKelas, n.IdSantri, s.NamaSantri, s.PhotoProfil
            ORDER BY n.IdTpq ASC, n.IdKelas ASC, s.NamaSantri ASC
        ", array_merge([$idTahunAjaran], $tpqIds));

        $dataSantriGanjil = $querySantriGanjil->getResultArray();

        // Optimasi: Query untuk mengambil statistik per kelas untuk semester Ganjil (menggunakan data santri yang sudah dihitung)
        // Buat array untuk menghitung completion per santri
        $santriCompleteGanjil = [];
        foreach ($dataSantriGanjil as $santri) {
            $key = $santri['IdTpq'] . '_' . $santri['IdKelas'] . '_' . $santri['IdSantri'];
            $santriCompleteGanjil[$key] = [
                'total_materi' => (int)$santri['total_materi'],
                'materi_terisi' => (int)$santri['materi_terisi']
            ];
        }

        // OPTIMASI: Query yang dioptimasi untuk statistik per kelas untuk semester Ganjil (kepala sekolah/operator/admin)
        // - Menghilangkan JOIN dengan tbl_tpq (data TPQ sudah diketahui dari parameter)
        // - Mengoptimalkan urutan WHERE clause untuk index (IdTahunAjaran, Semester, IdTpq)
        // - Menggunakan INNER JOIN dengan tbl_santri_baru untuk filter aktif (lebih efisien dengan index)
        // - Mengurangi kolom di GROUP BY (hanya yang diperlukan) - dari 6 kolom menjadi 3 kolom (tambah IdTpq untuk multiple TPQ)
        // - Menghilangkan kolom yang tidak digunakan dari SELECT (NamaTpq, KelurahanDesa)
        $queryGanjil = $db->query("
            SELECT 
                n.IdTpq,
                n.IdKelas,
                k.NamaKelas,
                COUNT(DISTINCT n.IdSantri) as total_santri
            FROM tbl_nilai n
            INNER JOIN tbl_santri_baru s ON s.IdSantri = n.IdSantri AND s.Active = 1
            LEFT JOIN tbl_kelas k ON k.IdKelas = n.IdKelas
            WHERE n.IdTahunAjaran = ?
                AND n.Semester = 'Ganjil'
                AND n.IdTpq IN ($placeholders)
            GROUP BY n.IdTpq, n.IdKelas, k.NamaKelas
            ORDER BY n.IdTpq ASC, k.NamaKelas ASC
        ", array_merge([$idTahunAjaran], $tpqIds));

        $dataGanjil = $queryGanjil->getResultArray();

        // Hitung sudah_dinilai berdasarkan data santri yang sudah dihitung
        foreach ($dataGanjil as &$row) {
            if (!empty($row['IdKelas'])) {
                $sudahDinilai = 0;
                foreach ($dataSantriGanjil as $santri) {
                    if ($santri['IdTpq'] == $row['IdTpq'] && $santri['IdKelas'] == $row['IdKelas']) {
                        $key = $santri['IdTpq'] . '_' . $santri['IdKelas'] . '_' . $santri['IdSantri'];
                        if (isset($santriCompleteGanjil[$key])) {
                            $complete = $santriCompleteGanjil[$key];
                            if ($complete['total_materi'] > 0 && $complete['total_materi'] == $complete['materi_terisi']) {
                                $sudahDinilai++;
                            }
                        }
                    }
                }
                $row['sudah_dinilai'] = $sudahDinilai;
            }
        }
        unset($row);

        // Optimasi: Hitung completion per santri terlebih dahulu untuk semester Genap
        $querySantriGenap = $db->query("
            SELECT 
                n.IdTpq,
                n.IdKelas,
                n.IdSantri,
                s.NamaSantri,
                s.PhotoProfil,
                COUNT(DISTINCT n.IdMateri) as total_materi,
                COUNT(DISTINCT CASE WHEN n.Nilai != 0 THEN n.IdMateri END) as materi_terisi
            FROM tbl_nilai n
            INNER JOIN tbl_santri_baru s ON s.IdSantri = n.IdSantri AND s.Active = 1
            WHERE n.IdTahunAjaran = ?
                AND n.Semester = 'Genap'
                AND n.IdTpq IN ($placeholders)
            GROUP BY n.IdTpq, n.IdKelas, n.IdSantri, s.NamaSantri, s.PhotoProfil
            ORDER BY n.IdTpq ASC, n.IdKelas ASC, s.NamaSantri ASC
        ", array_merge([$idTahunAjaran], $tpqIds));

        $dataSantriGenap = $querySantriGenap->getResultArray();

        // OPTIMASI: Query yang dioptimasi untuk statistik per kelas untuk semester Genap (kepala sekolah/operator/admin)
        // - Menghilangkan JOIN dengan tbl_tpq (data TPQ sudah diketahui dari parameter)
        // - Mengoptimalkan urutan WHERE clause untuk index (IdTahunAjaran, Semester, IdTpq)
        // - Menggunakan INNER JOIN dengan tbl_santri_baru untuk filter aktif (lebih efisien dengan index)
        // - Mengurangi kolom di GROUP BY (hanya yang diperlukan) - dari 6 kolom menjadi 3 kolom (tambah IdTpq untuk multiple TPQ)
        // - Menghilangkan kolom yang tidak digunakan dari SELECT (NamaTpq, KelurahanDesa)
        $queryGenap = $db->query("
            SELECT 
                n.IdTpq,
                n.IdKelas,
                k.NamaKelas,
                COUNT(DISTINCT n.IdSantri) as total_santri
            FROM tbl_nilai n
            INNER JOIN tbl_santri_baru s ON s.IdSantri = n.IdSantri AND s.Active = 1
            LEFT JOIN tbl_kelas k ON k.IdKelas = n.IdKelas
            WHERE n.IdTahunAjaran = ?
                AND n.Semester = 'Genap'
                AND n.IdTpq IN ($placeholders)
            GROUP BY n.IdTpq, n.IdKelas, k.NamaKelas
            ORDER BY n.IdTpq ASC, k.NamaKelas ASC
        ", array_merge([$idTahunAjaran], $tpqIds));

        $dataGenap = $queryGenap->getResultArray();

        // Hitung sudah_dinilai berdasarkan data santri yang sudah dihitung (lebih efisien di PHP)
        foreach ($dataGenap as &$row) {
            if (!empty($row['IdKelas'])) {
                $sudahDinilai = 0;
                foreach ($dataSantriGenap as $santri) {
                    if ($santri['IdTpq'] == $row['IdTpq'] && $santri['IdKelas'] == $row['IdKelas']) {
                        $totalMateri = (int)$santri['total_materi'];
                        $materiTerisi = (int)$santri['materi_terisi'];
                        if ($totalMateri > 0 && $totalMateri == $materiTerisi) {
                            $sudahDinilai++;
                        }
                    }
                }
                $row['sudah_dinilai'] = $sudahDinilai;
            }
        }
        unset($row);

        // OPTIMASI: Batch processing untuk konversi nama kelas ke MDA (menghindari query database dalam loop)
        // Kumpulkan semua TPQ ID dan nama kelas yang unik untuk semester Ganjil
        $tpqKelasMappingGanjil = []; // [tpqId][namaKelas] => mappedMdaKelas
        $uniqueTpqIdsForMappingGanjil = [];
        foreach ($dataGanjil as $row) {
            if (!empty($row['IdTpq']) && !empty($row['NamaKelas'])) {
                $tpqId = $row['IdTpq'];
                $namaKelas = $row['NamaKelas'];

                if (!in_array($tpqId, $uniqueTpqIdsForMappingGanjil)) {
                    $uniqueTpqIdsForMappingGanjil[] = $tpqId;
                }

                if (!isset($tpqKelasMappingGanjil[$tpqId][$namaKelas])) {
                    $tpqKelasMappingGanjil[$tpqId][$namaKelas] = null;
                }
            }
        }

        // Lakukan batch check MDA mapping untuk semua TPQ dan kelas sekaligus (semester Ganjil)
        foreach ($uniqueTpqIdsForMappingGanjil as $tpqId) {
            if (isset($tpqKelasMappingGanjil[$tpqId])) {
                $hasMda = $this->toolsModel->getSettingAsBool($tpqId, 'MDA_S1_ApakahMemilikiLembagaMDATA', false);
                if ($hasMda) {
                    $persamaanKelas = $this->toolsModel->getSettingAsString($tpqId, 'MDA_S1_PersamaanKelasMDA', '');
                    $kelasMapping = $this->helpFunctionModel->parseMdaKelasMapping($persamaanKelas);
                    foreach ($tpqKelasMappingGanjil[$tpqId] as $namaKelas => $value) {
                        $matchResult = $this->helpFunctionModel->matchKelasWithMdaMapping($namaKelas, $kelasMapping);
                        $tpqKelasMappingGanjil[$tpqId][$namaKelas] = $matchResult['mappedMdaKelas'];
                    }
                }
            }
        }

        // Organisir data untuk semester Ganjil (support multiple TPQ)
        $resultGanjil = [];

        // Inisialisasi data TPQ untuk semua TPQ
        $tpqDataMap = [];
        foreach ($tpqList as $tpq) {
            $tpqId = $tpq['IdTpq'];
            $tpqDataMap[$tpqId] = [
                'IdTpq' => $tpqId,
                'NamaTpq' => $tpq['NamaTpq'],
                'KelurahanDesa' => $tpq['KelurahanDesa'] ?? $tpq['Alamat'] ?? '',
                'Kelas' => [],
                'TotalSantri' => 0,
                'TotalSudahDinilai' => 0,
                'TotalBelumDinilai' => 0,
                'PersentaseSudah' => 0
            ];
        }

        // Isi data dari query
        foreach ($dataGanjil as $row) {
            $tpqId = $row['IdTpq'];
            if (!empty($row['IdKelas']) && isset($tpqDataMap[$tpqId])) {
                $totalSantri = (int)$row['total_santri'];
                $sudahDinilai = (int)($row['sudah_dinilai'] ?? 0);
                $belumDinilai = $totalSantri - $sudahDinilai;
                $persentase = $totalSantri > 0 ? round(($sudahDinilai / $totalSantri) * 100, 1) : 0;

                // Ambil data santri untuk kelas ini dan hitung ulang sudah dinilai berdasarkan 100%
                $santriList = [];
                $sudahDinilaiRecalc = 0;
                $adaNilai = false;
                foreach ($dataSantriGanjil as $santriRow) {
                    if ($santriRow['IdTpq'] == $tpqId && $santriRow['IdKelas'] == $row['IdKelas']) {
                        $totalMateri = (int)$santriRow['total_materi'];
                        $materiTerisi = (int)($santriRow['materi_terisi'] ?? 0);
                        $materiBelum = $totalMateri - $materiTerisi;
                        $persentaseSantri = $totalMateri > 0 ? round(($materiTerisi / $totalMateri) * 100, 1) : 0;

                        // Tentukan status: Sudah Dinilai (100%), Sedang Dinilai (1-99%), Belum Dinilai (0%)
                        $statusSantri = 'Belum Dinilai';
                        $statusColor = 'danger';
                        if ($totalMateri > 0 && $materiTerisi == $totalMateri) {
                            $statusSantri = 'Sudah Dinilai';
                            $statusColor = 'success';
                            $sudahDinilaiRecalc++;
                            $adaNilai = true;
                        } elseif ($materiTerisi > 0) {
                            $statusSantri = 'Sedang Dinilai';
                            $statusColor = 'warning';
                            $adaNilai = true;
                        }

                        $santriList[] = [
                            'IdSantri' => $santriRow['IdSantri'],
                            'NamaSantri' => $santriRow['NamaSantri'],
                            'PhotoProfil' => $santriRow['PhotoProfil'] ?? null,
                            'IdTpq' => $santriRow['IdTpq'],
                            'TotalMateri' => $totalMateri,
                            'MateriTerisi' => $materiTerisi,
                            'MateriBelum' => $materiBelum,
                            'PersentaseSudah' => $persentaseSantri,
                            'StatusSantri' => $statusSantri,
                            'StatusColor' => $statusColor
                        ];
                    }
                }

                // Gunakan recalculated value untuk konsistensi
                $sudahDinilai = $sudahDinilaiRecalc;
                $belumDinilai = $totalSantri - $sudahDinilai;
                $persentase = $totalSantri > 0 ? round(($sudahDinilai / $totalSantri) * 100, 1) : 0;

                // Tentukan status kelas
                $statusKelas = null;
                $statusKelasColor = null;
                if ($sudahDinilai > 0) {
                    $statusKelas = null;
                } elseif ($adaNilai) {
                    $statusKelas = 'Sedang Di Nilai';
                    $statusKelasColor = 'warning';
                }

                // OPTIMASI: Gunakan mapping yang sudah dihitung sebelumnya (tidak perlu query database lagi)
                $namaKelasOriginal = $row['NamaKelas'];
                $mappedMdaKelas = $tpqKelasMappingGanjil[$tpqId][$namaKelasOriginal] ?? null;
                $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda(
                    $namaKelasOriginal,
                    $mappedMdaKelas
                );

                $tpqDataMap[$tpqId]['Kelas'][] = [
                    'IdKelas' => $row['IdKelas'],
                    'NamaKelas' => $namaKelasDisplay,
                    'TotalSantri' => $totalSantri,
                    'SudahDinilai' => $sudahDinilai,
                    'BelumDinilai' => $belumDinilai,
                    'PersentaseSudah' => $persentase,
                    'StatusKelas' => $statusKelas,
                    'StatusKelasColor' => $statusKelasColor,
                    'Santri' => $santriList
                ];

                $tpqDataMap[$tpqId]['TotalSantri'] += $totalSantri;
                $tpqDataMap[$tpqId]['TotalSudahDinilai'] += $sudahDinilai;
                $tpqDataMap[$tpqId]['TotalBelumDinilai'] += $belumDinilai;
            }
        }

        // Hitung persentase total dan status TPQ untuk semua TPQ
        foreach ($tpqDataMap as $tpqId => $tpqData) {
            // Hitung persentase total
            if ($tpqData['TotalSantri'] > 0) {
                $tpqDataMap[$tpqId]['PersentaseSudah'] = round(($tpqData['TotalSudahDinilai'] / $tpqData['TotalSantri']) * 100, 1);
            }

            // Tentukan status TPQ
            $adaKelasSelesai = false;
            $adaNilaiTpq = false;
            foreach ($tpqData['Kelas'] as $kelas) {
                if ($kelas['SudahDinilai'] > 0) {
                    $adaKelasSelesai = true;
                    $adaNilaiTpq = true;
                    break;
                } elseif (!empty($kelas['StatusKelas']) && $kelas['StatusKelas'] == 'Sedang Di Nilai') {
                    $adaNilaiTpq = true;
                }
            }

            $tpqDataMap[$tpqId]['StatusTpq'] = null;
            $tpqDataMap[$tpqId]['StatusTpqColor'] = null;
            if ($adaKelasSelesai) {
                $tpqDataMap[$tpqId]['StatusTpq'] = null;
            } elseif ($adaNilaiTpq) {
                $tpqDataMap[$tpqId]['StatusTpq'] = 'Sedang Di Nilai';
                $tpqDataMap[$tpqId]['StatusTpqColor'] = 'warning';
            }

            if ($tpqData['TotalSantri'] > 0) {
                $resultGanjil[] = $tpqDataMap[$tpqId];
            }
        }

        // OPTIMASI: Batch processing untuk konversi nama kelas ke MDA (menghindari query database dalam loop)
        // Kumpulkan semua TPQ ID dan nama kelas yang unik untuk semester Genap
        $tpqKelasMappingGenap = []; // [tpqId][namaKelas] => mappedMdaKelas
        $uniqueTpqIdsForMappingGenap = [];
        foreach ($dataGenap as $row) {
            if (!empty($row['IdTpq']) && !empty($row['NamaKelas'])) {
                $tpqId = $row['IdTpq'];
                $namaKelas = $row['NamaKelas'];

                if (!in_array($tpqId, $uniqueTpqIdsForMappingGenap)) {
                    $uniqueTpqIdsForMappingGenap[] = $tpqId;
                }

                if (!isset($tpqKelasMappingGenap[$tpqId][$namaKelas])) {
                    $tpqKelasMappingGenap[$tpqId][$namaKelas] = null;
                }
            }
        }

        // Lakukan batch check MDA mapping untuk semua TPQ dan kelas sekaligus (semester Genap)
        foreach ($uniqueTpqIdsForMappingGenap as $tpqId) {
            if (isset($tpqKelasMappingGenap[$tpqId])) {
                $hasMda = $this->toolsModel->getSettingAsBool($tpqId, 'MDA_S1_ApakahMemilikiLembagaMDATA', false);
                if ($hasMda) {
                    $persamaanKelas = $this->toolsModel->getSettingAsString($tpqId, 'MDA_S1_PersamaanKelasMDA', '');
                    $kelasMapping = $this->helpFunctionModel->parseMdaKelasMapping($persamaanKelas);
                    foreach ($tpqKelasMappingGenap[$tpqId] as $namaKelas => $value) {
                        $matchResult = $this->helpFunctionModel->matchKelasWithMdaMapping($namaKelas, $kelasMapping);
                        $tpqKelasMappingGenap[$tpqId][$namaKelas] = $matchResult['mappedMdaKelas'];
                    }
                }
            }
        }

        // Organisir data untuk semester Genap (support multiple TPQ)
        $resultGenap = [];

        // Inisialisasi data TPQ untuk semua TPQ
        $tpqDataGenapMap = [];
        foreach ($tpqList as $tpq) {
            $tpqId = $tpq['IdTpq'];
            $tpqDataGenapMap[$tpqId] = [
                'IdTpq' => $tpqId,
                'NamaTpq' => $tpq['NamaTpq'],
                'KelurahanDesa' => $tpq['KelurahanDesa'] ?? $tpq['Alamat'] ?? '',
                'Kelas' => [],
                'TotalSantri' => 0,
                'TotalSudahDinilai' => 0,
                'TotalBelumDinilai' => 0,
                'PersentaseSudah' => 0
            ];
        }

        // Isi data dari query
        foreach ($dataGenap as $row) {
            $tpqId = $row['IdTpq'];
            if (!empty($row['IdKelas']) && isset($tpqDataGenapMap[$tpqId])) {
                $totalSantri = (int)$row['total_santri'];
                $sudahDinilai = (int)($row['sudah_dinilai'] ?? 0);
                $belumDinilai = $totalSantri - $sudahDinilai;
                $persentase = $totalSantri > 0 ? round(($sudahDinilai / $totalSantri) * 100, 1) : 0;

                // Ambil data santri untuk kelas ini dan hitung ulang sudah dinilai berdasarkan 100%
                $santriList = [];
                $sudahDinilaiRecalc = 0;
                $adaNilai = false;
                foreach ($dataSantriGenap as $santriRow) {
                    if ($santriRow['IdTpq'] == $tpqId && $santriRow['IdKelas'] == $row['IdKelas']) {
                        $totalMateri = (int)$santriRow['total_materi'];
                        $materiTerisi = (int)($santriRow['materi_terisi'] ?? 0);
                        $materiBelum = $totalMateri - $materiTerisi;
                        $persentaseSantri = $totalMateri > 0 ? round(($materiTerisi / $totalMateri) * 100, 1) : 0;

                        // Tentukan status: Sudah Dinilai (100%), Sedang Dinilai (1-99%), Belum Dinilai (0%)
                        $statusSantri = 'Belum Dinilai';
                        $statusColor = 'danger';
                        if ($totalMateri > 0 && $materiTerisi == $totalMateri) {
                            $statusSantri = 'Sudah Dinilai';
                            $statusColor = 'success';
                            $sudahDinilaiRecalc++;
                            $adaNilai = true;
                        } elseif ($materiTerisi > 0) {
                            $statusSantri = 'Sedang Dinilai';
                            $statusColor = 'warning';
                            $adaNilai = true;
                        }

                        $santriList[] = [
                            'IdSantri' => $santriRow['IdSantri'],
                            'NamaSantri' => $santriRow['NamaSantri'],
                            'PhotoProfil' => $santriRow['PhotoProfil'] ?? null,
                            'IdTpq' => $santriRow['IdTpq'],
                            'TotalMateri' => $totalMateri,
                            'MateriTerisi' => $materiTerisi,
                            'MateriBelum' => $materiBelum,
                            'PersentaseSudah' => $persentaseSantri,
                            'StatusSantri' => $statusSantri,
                            'StatusColor' => $statusColor
                        ];
                    }
                }

                // Gunakan recalculated value untuk konsistensi
                $sudahDinilai = $sudahDinilaiRecalc;
                $belumDinilai = $totalSantri - $sudahDinilai;
                $persentase = $totalSantri > 0 ? round(($sudahDinilai / $totalSantri) * 100, 1) : 0;

                // OPTIMASI: Gunakan mapping yang sudah dihitung sebelumnya (tidak perlu query database lagi)
                $namaKelasOriginal = $row['NamaKelas'];
                $mappedMdaKelas = $tpqKelasMappingGenap[$tpqId][$namaKelasOriginal] ?? null;
                $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda(
                    $namaKelasOriginal,
                    $mappedMdaKelas
                );

                $tpqDataGenapMap[$tpqId]['Kelas'][] = [
                    'IdKelas' => $row['IdKelas'],
                    'NamaKelas' => $namaKelasDisplay,
                    'TotalSantri' => $totalSantri,
                    'SudahDinilai' => $sudahDinilai,
                    'BelumDinilai' => $belumDinilai,
                    'PersentaseSudah' => $persentase,
                    'Santri' => $santriList
                ];

                $tpqDataGenapMap[$tpqId]['TotalSantri'] += $totalSantri;
                $tpqDataGenapMap[$tpqId]['TotalSudahDinilai'] += $sudahDinilai;
                $tpqDataGenapMap[$tpqId]['TotalBelumDinilai'] += $belumDinilai;
            }
        }

        // Hitung persentase total untuk semua TPQ
        foreach ($tpqDataGenapMap as $tpqId => $tpqData) {
            if ($tpqData['TotalSantri'] > 0) {
                $tpqDataGenapMap[$tpqId]['PersentaseSudah'] = round(($tpqData['TotalSudahDinilai'] / $tpqData['TotalSantri']) * 100, 1);
            }

            if ($tpqData['TotalSantri'] > 0) {
                $resultGenap[] = $tpqDataGenapMap[$tpqId];
            }
        }

        return [
            'Ganjil' => $resultGanjil,
            'Genap' => $resultGenap
        ];
    }

    /**
     * Mengambil statistik progress penilaian per TPQ dan per kelas
     * @param mixed $idTahunAjaran
     * @return array
     */
    /**
     * Mengambil dan mengorganisir statistik progress penilaian per TPQ dan per kelas untuk semester tertentu
     * @param mixed $idTahunAjaran
     * @param string $semester 'Ganjil' atau 'Genap'
     * @param array $tpqList List TPQ
     * @return array Data yang sudah diorganisir per TPQ
     */
    private function getStatistikProgressNilaiPerTpqBySemester($idTahunAjaran, $semester, $tpqList)
    {
        // Ambil koneksi database
        $db = db_connect();

        // OPTIMASI: Query untuk mengambil statistik progress penilaian per TPQ dan per kelas
        // - Menghilangkan LEFT JOIN subquery yang kompleks (menghitung sudah_dinilai di PHP lebih efisien)
        // - Mengoptimalkan urutan WHERE clause untuk index (IdTahunAjaran, Semester)
        // - Menggunakan INNER JOIN dengan tbl_santri_baru untuk filter aktif (lebih efisien dengan index)
        // - Mengurangi kompleksitas query dengan menghitung sudah_dinilai di PHP
        $query = $db->query("
            SELECT 
                t.IdTpq,
                t.NamaTpq,
                COALESCE(t.KelurahanDesa, t.Alamat, '') as KelurahanDesa,
                k.IdKelas,
                k.NamaKelas,
                COUNT(DISTINCT n.IdSantri) as total_santri
            FROM tbl_nilai n
            INNER JOIN tbl_santri_baru s ON s.IdSantri = n.IdSantri AND s.Active = 1
            INNER JOIN tbl_tpq t ON t.IdTpq = n.IdTpq
            LEFT JOIN tbl_kelas k ON k.IdKelas = n.IdKelas
            WHERE n.IdTahunAjaran = ?
                AND n.Semester = ?
            GROUP BY t.IdTpq, t.NamaTpq, t.KelurahanDesa, t.Alamat, k.IdKelas, k.NamaKelas
            ORDER BY t.NamaTpq ASC, k.NamaKelas ASC
        ", [$idTahunAjaran, $semester]);

        $data = $query->getResultArray();

        // Query untuk mengambil data per santri
        $querySantri = $db->query("
            SELECT 
                t.IdTpq,
                k.IdKelas,
                n.IdSantri,
                s.NamaSantri,
                s.PhotoProfil,
                COUNT(DISTINCT n.IdMateri) as total_materi,
                COUNT(DISTINCT CASE WHEN n.Nilai != 0 THEN n.IdMateri END) as materi_terisi
            FROM tbl_nilai n
            INNER JOIN tbl_santri_baru s ON s.IdSantri = n.IdSantri AND s.Active = 1
            INNER JOIN tbl_tpq t ON t.IdTpq = n.IdTpq
            LEFT JOIN tbl_kelas k ON k.IdKelas = n.IdKelas
            WHERE n.IdTahunAjaran = ?
                AND n.Semester = ?
            GROUP BY t.IdTpq, k.IdKelas, n.IdSantri, s.NamaSantri, s.PhotoProfil
            ORDER BY t.IdTpq ASC, k.IdKelas ASC, s.NamaSantri ASC
        ", [$idTahunAjaran, $semester]);

        $dataSantri = $querySantri->getResultArray();

        // OPTIMASI: Hitung sudah_dinilai berdasarkan data santri yang sudah dihitung (lebih efisien di PHP)
        foreach ($data as &$row) {
            if (!empty($row['IdKelas'])) {
                $sudahDinilai = 0;
                foreach ($dataSantri as $santri) {
                    if ($santri['IdTpq'] == $row['IdTpq'] && $santri['IdKelas'] == $row['IdKelas']) {
                        $totalMateri = (int)$santri['total_materi'];
                        $materiTerisi = (int)$santri['materi_terisi'];
                        if ($totalMateri > 0 && $totalMateri == $materiTerisi) {
                            $sudahDinilai++;
                        }
                    }
                }
                $row['sudah_dinilai'] = $sudahDinilai;
            }
        }
        unset($row);

        // OPTIMASI: Batch processing untuk konversi nama kelas ke MDA (menghindari query database dalam loop)
        // Kumpulkan semua TPQ ID dan nama kelas yang unik
        $tpqKelasMapping = []; // [tpqId][namaKelas] => mappedMdaKelas
        $uniqueTpqIdsForMapping = [];
        foreach ($data as $row) {
            if (!empty($row['IdTpq']) && !empty($row['NamaKelas'])) {
                $tpqId = $row['IdTpq'];
                $namaKelas = $row['NamaKelas'];

                // Kumpulkan TPQ ID yang unik
                if (!in_array($tpqId, $uniqueTpqIdsForMapping)) {
                    $uniqueTpqIdsForMapping[] = $tpqId;
                }

                // Kumpulkan nama kelas per TPQ (untuk menghindari duplikasi check)
                if (!isset($tpqKelasMapping[$tpqId][$namaKelas])) {
                    $tpqKelasMapping[$tpqId][$namaKelas] = null; // Akan diisi nanti
                }
            }
        }

        // Lakukan batch check MDA mapping untuk semua TPQ dan kelas sekaligus
        foreach ($uniqueTpqIdsForMapping as $tpqId) {
            if (isset($tpqKelasMapping[$tpqId])) {
                // Check terlebih dahulu apakah TPQ ini memiliki MDA (sekali per TPQ)
                $hasMda = $this->toolsModel->getSettingAsBool($tpqId, 'MDA_S1_ApakahMemilikiLembagaMDATA', false);

                if ($hasMda) {
                    // Ambil dan parse mapping kelas MDA sekali per TPQ (tidak di dalam loop kelas)
                    $persamaanKelas = $this->toolsModel->getSettingAsString($tpqId, 'MDA_S1_PersamaanKelasMDA', '');
                    $kelasMapping = $this->helpFunctionModel->parseMdaKelasMapping($persamaanKelas);

                    // Gunakan mapping yang sudah di-parse untuk semua kelas di TPQ ini
                    foreach ($tpqKelasMapping[$tpqId] as $namaKelas => $value) {
                        $matchResult = $this->helpFunctionModel->matchKelasWithMdaMapping($namaKelas, $kelasMapping);
                        $tpqKelasMapping[$tpqId][$namaKelas] = $matchResult['mappedMdaKelas'];
                    }
                }
            }
        }

        // Organisir data per TPQ
        $result = [];
        foreach ($tpqList as $tpq) {
            $tpqId = $tpq['IdTpq'];
            $tpqData = [
                'IdTpq' => $tpqId,
                'NamaTpq' => $tpq['NamaTpq'],
                'KelurahanDesa' => $tpq['KelurahanDesa'] ?? $tpq['Alamat'] ?? '',
                'Kelas' => [],
                'TotalSantri' => 0,
                'TotalSudahDinilai' => 0,
                'TotalBelumDinilai' => 0,
                'PersentaseSudah' => 0
            ];

            // Isi data dari query
            foreach ($data as $row) {
                if ($row['IdTpq'] == $tpqId && !empty($row['IdKelas'])) {
                    $totalSantri = (int)$row['total_santri'];
                    $sudahDinilai = (int)($row['sudah_dinilai'] ?? 0);
                    $belumDinilai = $totalSantri - $sudahDinilai;
                    $persentase = $totalSantri > 0 ? round(($sudahDinilai / $totalSantri) * 100, 1) : 0;
                    
                    // Ambil data santri untuk kelas ini dan hitung ulang sudah dinilai berdasarkan 100%
                    $santriList = [];
                    $sudahDinilaiRecalc = 0; // Recalculate berdasarkan 100% completion
                    $adaNilai = false; // Flag untuk cek apakah ada nilai yang sudah diisi
                    foreach ($dataSantri as $santriRow) {
                        if ($santriRow['IdTpq'] == $tpqId && $santriRow['IdKelas'] == $row['IdKelas']) {
                            $totalMateri = (int)$santriRow['total_materi'];
                            $materiTerisi = (int)($santriRow['materi_terisi'] ?? 0);
                            $materiBelum = $totalMateri - $materiTerisi;
                            $persentaseSantri = $totalMateri > 0 ? round(($materiTerisi / $totalMateri) * 100, 1) : 0;
                            
                            // Tentukan status: Sudah Dinilai (100%), Sedang Dinilai (1-99%), Belum Dinilai (0%)
                            $statusSantri = 'Belum Dinilai';
                            $statusColor = 'danger';
                            if ($totalMateri > 0 && $materiTerisi == $totalMateri) {
                                $statusSantri = 'Sudah Dinilai';
                                $statusColor = 'success';
                                $sudahDinilaiRecalc++;
                                $adaNilai = true;
                            } elseif ($materiTerisi > 0) {
                                $statusSantri = 'Sedang Dinilai';
                                $statusColor = 'warning';
                                $adaNilai = true;
                            }
                            
                            $santriList[] = [
                                'IdSantri' => $santriRow['IdSantri'],
                                'NamaSantri' => $santriRow['NamaSantri'],
                                'PhotoProfil' => $santriRow['PhotoProfil'] ?? null,
                                'TotalMateri' => $totalMateri,
                                'MateriTerisi' => $materiTerisi,
                                'MateriBelum' => $materiBelum,
                                'PersentaseSudah' => $persentaseSantri,
                                'StatusSantri' => $statusSantri,
                                'StatusColor' => $statusColor
                            ];
                        }
                    }
                    
                    // Gunakan recalculated value untuk konsistensi
                    $sudahDinilai = $sudahDinilaiRecalc;
                    $belumDinilai = $totalSantri - $sudahDinilai;
                    $persentase = $totalSantri > 0 ? round(($sudahDinilai / $totalSantri) * 100, 1) : 0;

                    // Tentukan status kelas (hanya untuk semester Ganjil)
                    $statusKelas = null;
                    $statusKelasColor = null;
                    if ($semester === 'Ganjil') {
                        if ($sudahDinilai > 0) {
                            // Ada yang sudah selesai, tampilkan persentase
                            $statusKelas = null; // null berarti tampilkan persentase
                        } elseif ($adaNilai) {
                            // Ada nilai tapi belum ada yang selesai
                            $statusKelas = 'Sedang Di Nilai';
                            $statusKelasColor = 'warning';
                        }
                        // Jika tidak ada nilai sama sekali, statusKelas tetap null (blank)
                    }

                    // OPTIMASI: Gunakan mapping yang sudah dihitung sebelumnya (tidak perlu query database lagi)
                    $namaKelasOriginal = $row['NamaKelas'];
                    $mappedMdaKelas = $tpqKelasMapping[$tpqId][$namaKelasOriginal] ?? null;
                    $namaKelasDisplay = $this->helpFunctionModel->convertKelasToMda(
                        $namaKelasOriginal,
                        $mappedMdaKelas
                    );

                    $kelasData = [
                        'IdKelas' => $row['IdKelas'],
                        'NamaKelas' => $namaKelasDisplay,
                        'TotalSantri' => $totalSantri,
                        'SudahDinilai' => $sudahDinilai,
                        'BelumDinilai' => $belumDinilai,
                        'PersentaseSudah' => $persentase,
                        'Santri' => $santriList
                    ];

                    // Tambahkan status kelas hanya untuk semester Ganjil
                    if ($semester === 'Ganjil') {
                        $kelasData['StatusKelas'] = $statusKelas;
                        $kelasData['StatusKelasColor'] = $statusKelasColor;
                    }

                    $tpqData['Kelas'][] = $kelasData;

                    $tpqData['TotalSantri'] += $totalSantri;
                    $tpqData['TotalSudahDinilai'] += $sudahDinilai;
                    $tpqData['TotalBelumDinilai'] += $belumDinilai;
                }
            }
            
            // Hitung persentase total
            if ($tpqData['TotalSantri'] > 0) {
                $tpqData['PersentaseSudah'] = round(($tpqData['TotalSudahDinilai'] / $tpqData['TotalSantri']) * 100, 1);
            }

            // Tentukan status TPQ berdasarkan kelas-kelasnya (hanya untuk semester Ganjil)
            if ($semester === 'Ganjil') {
                $adaKelasSelesai = false;
                $adaNilaiTpq = false;
                foreach ($tpqData['Kelas'] as $kelas) {
                    if ($kelas['SudahDinilai'] > 0) {
                        $adaKelasSelesai = true;
                        $adaNilaiTpq = true;
                        break;
                    } elseif (!empty($kelas['StatusKelas']) && $kelas['StatusKelas'] == 'Sedang Di Nilai') {
                        $adaNilaiTpq = true;
                    }
                }

                // Tentukan status TPQ
                $tpqData['StatusTpq'] = null;
                $tpqData['StatusTpqColor'] = null;
                if ($adaKelasSelesai) {
                    // Ada kelas yang sudah selesai, tampilkan persentase
                    $tpqData['StatusTpq'] = null; // null berarti tampilkan persentase
                } elseif ($adaNilaiTpq) {
                    // Ada nilai tapi belum ada yang selesai
                    $tpqData['StatusTpq'] = 'Sedang Di Nilai';
                    $tpqData['StatusTpqColor'] = 'warning';
                }
                // Jika tidak ada nilai sama sekali, StatusTpq tetap null (blank)
            }
            
            if ($tpqData['TotalSantri'] > 0) {
                $result[] = $tpqData;
            }
        }

        return $result;
    }

    /**
     * Mengambil statistik progress penilaian per TPQ dan per kelas untuk semua TPQ (Admin)
     * @param mixed $idTahunAjaran
     * @return array
     */
    private function getStatistikProgressNilaiPerTpq($idTahunAjaran)
    {
        // Ambil semua TPQ
        $tpqList = $this->helpFunctionModel->getDataTpq(0);

        // Gunakan fungsi baru untuk menghindari duplikasi kode
        $resultGanjil = $this->getStatistikProgressNilaiPerTpqBySemester($idTahunAjaran, 'Ganjil', $tpqList);
        $resultGenap = $this->getStatistikProgressNilaiPerTpqBySemester($idTahunAjaran, 'Genap', $tpqList);
        
        return [
            'Ganjil' => $resultGanjil,
            'Genap' => $resultGenap
        ];
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

    /**
     * AJAX endpoint untuk mengambil data materi per santri (sudah dinilai dan belum dinilai)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getMateriPerSantri()
    {
        try {
            $idSantri = $this->request->getGet('IdSantri');
            $idKelas = $this->request->getGet('IdKelas');
            $semester = $this->request->getGet('Semester');
            $idTpq = session()->get('IdTpq');
            $idTahunAjaran = session()->get('IdTahunAjaran');

            if (empty($idSantri) || empty($idKelas) || empty($semester)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap'
                ])->setStatusCode(400);
            }

            // Jika IdTpq tidak ada di session (misalnya untuk admin), ambil dari data santri
            if (empty($idTpq)) {
                // Coba ambil dari tbl_nilai berdasarkan IdSantri, IdKelas, dan Semester
                $nilaiData = $this->db->table('tbl_nilai')
                    ->select('IdTpq')
                    ->where('IdSantri', $idSantri)
                    ->where('IdKelas', $idKelas)
                    ->where('Semester', $semester)
                    ->where('IdTahunAjaran', $idTahunAjaran)
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                if (!empty($nilaiData) && !empty($nilaiData['IdTpq'])) {
                    $idTpq = $nilaiData['IdTpq'];
                } else {
                    // Fallback: ambil dari tbl_santri_baru
                    $santriData = $this->db->table('tbl_santri_baru')
                        ->select('IdTpq')
                        ->where('IdSantri', $idSantri)
                        ->where('Active', 1)
                        ->limit(1)
                        ->get()
                        ->getRowArray();

                    if (!empty($santriData) && !empty($santriData['IdTpq'])) {
                        $idTpq = $santriData['IdTpq'];
                    }
                }
            }

            // Jika masih tidak ada IdTpq, return error
            if (empty($idTpq)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTpq tidak ditemukan untuk santri ini'
                ])->setStatusCode(400);
            }

            // Ambil semua materi untuk kelas ini
            $allMateri = $this->helpFunctionModel->getMateriPelajaranByKelas($idTpq, $idKelas, $semester);
            
            // Ambil semua nilai untuk santri ini (termasuk yang 0)
            $builder = $this->db->table('tbl_nilai n');
            $builder->select('n.IdMateri, n.Nilai, m.NamaMateri, m.Kategori, kmp.UrutanMateri');
            $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = n.IdMateri');
            $builder->join('tbl_kelas_materi_pelajaran kmp', 'kmp.IdMateri = n.IdMateri AND kmp.IdKelas = n.IdKelas AND kmp.IdTpq = n.IdTpq', 'left');
            $builder->where('n.IdSantri', $idSantri);
            $builder->where('n.IdKelas', $idKelas);
            $builder->where('n.IdTpq', $idTpq);
            $builder->where('n.IdTahunAjaran', $idTahunAjaran);
            $builder->where('n.Semester', $semester);
            $builder->orderBy('kmp.UrutanMateri', 'ASC');
            $allNilaiData = $builder->get()->getResultArray();

            // Buat mapping nilai per materi
            $nilaiMap = [];
            foreach ($allNilaiData as $nilai) {
                $nilaiMap[$nilai['IdMateri']] = $nilai;
            }

            // Pisahkan materi menjadi sudah dinilai dan belum dinilai
            $materiSudahDinilai = [];
            $materiBelumDinilai = [];

            foreach ($allMateri as $materi) {
                $materiData = [
                    'IdMateri' => $materi->IdMateri,
                    'NamaMateri' => $materi->NamaMateri,
                    'Kategori' => $materi->Kategori ?? '',
                    'UrutanMateri' => $materi->UrutanMateri ?? 0,
                    'Nilai' => 0
                ];

                // Cek apakah materi ada di tabel nilai
                if (isset($nilaiMap[$materi->IdMateri])) {
                    $nilaiValue = (int)$nilaiMap[$materi->IdMateri]['Nilai'];
                    $materiData['Nilai'] = $nilaiValue;
                    
                    // Sudah dinilai jika nilai > 0
                    if ($nilaiValue > 0) {
                        $materiSudahDinilai[] = $materiData;
                    } else {
                        // Belum dinilai jika nilai = 0
                        $materiBelumDinilai[] = $materiData;
                    }
                } else {
                    // Belum dinilai jika tidak ada di tabel nilai sama sekali
                    $materiBelumDinilai[] = $materiData;
                }
            }

            // Urutkan berdasarkan UrutanMateri
            usort($materiSudahDinilai, function($a, $b) {
                return ($a['UrutanMateri'] ?? 0) <=> ($b['UrutanMateri'] ?? 0);
            });
            usort($materiBelumDinilai, function($a, $b) {
                return ($a['UrutanMateri'] ?? 0) <=> ($b['UrutanMateri'] ?? 0);
            });

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'sudahDinilai' => $materiSudahDinilai,
                    'belumDinilai' => $materiBelumDinilai
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getMateriPerSantri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data materi'
            ])->setStatusCode(500);
        }
    }
}


