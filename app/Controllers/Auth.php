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

class Auth extends BaseController
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

    /**
     * Set Guru-related session data and scoring settings.
     * This centralizes logic previously in vendor auth controller.
     */
    public function setGuruSessionData($idGuru)
    {
        if ($idGuru == null) {
            return;
        }

        $dataGuruKelas = $this->helpFunctionModel->getDataGuruKelas(IdGuru: $idGuru);

        $IdKelasList = [];
        $IdJabatanList = [];
        $IdTahunAjaranList = [];
        $IdTpq = '';

        foreach ($dataGuruKelas as $dataGuru) {
            $IdKelasList[] = $dataGuru->IdKelas;
            $IdJabatanList[] = $dataGuru->IdJabatan;
            $IdTahunAjaranList[] = $dataGuru->IdTahunAjaran;
            $IdTpq = $dataGuru->IdTpq;
        }

        // Setting nilai minimum
        $settingNilai = $this->helpFunctionModel->getSettingLimitInputNilai(IdTpq: $IdTpq, SettingKey: 'Min');
        if ($settingNilai) {
            session()->set('SettingNilaiMin', $settingNilai);
        } else {
            $defaultSettingNilai = $this->helpFunctionModel->getSettingLimitInputNilai(IdTpq: 'default', SettingKey: 'Min');
            if ($defaultSettingNilai) {
                session()->set('SettingNilaiMin', $defaultSettingNilai);
            } else {
                session()->set('SettingNilaiMin', 0);
            }
        }

        // Setting nilai maksimum
        $settingNilai = $this->helpFunctionModel->getSettingLimitInputNilai(IdTpq: $IdTpq, SettingKey: 'Max');
        if ($settingNilai) {
            session()->set('SettingNilaiMax', $settingNilai);
        } else {
            $defaultSettingNilai = $this->helpFunctionModel->getSettingLimitInputNilai(IdTpq: 'default', SettingKey: 'Max');
            if ($defaultSettingNilai) {
                session()->set('SettingNilaiMax', $defaultSettingNilai);
            } else {
                session()->set('SettingNilaiMax', 100);
            }
        }

        // Setting nilai alfabet
        $settingNilaiAlfabet = $this->helpFunctionModel->getNilaiAlphabetSettings(IdTpq: $IdTpq);
        if ($settingNilaiAlfabet) {
            session()->set('SettingNilaiAlphabet', $settingNilaiAlfabet);
        } else {
            $defaultSettingNilaiAlfabet = $this->helpFunctionModel->getNilaiAlphabetSettings(IdTpq: 'default');
            if ($defaultSettingNilaiAlfabet) {
                session()->set('SettingNilaiAlphabet', $defaultSettingNilaiAlfabet);
            }
        }

        // Setting angka Arabic
        $settingNilaiArabic = $this->helpFunctionModel->getNilaiArabicSettings(IdTpq: $IdTpq);
        if ($settingNilaiArabic) {
            session()->set('SettingNilaiArabic', $settingNilaiArabic);
        } else {
            $defaultSettingNilaiArabic = $this->helpFunctionModel->getNilaiArabicSettings(IdTpq: 'default');
            if ($defaultSettingNilaiArabic) {
                session()->set('SettingNilaiArabic', $defaultSettingNilaiArabic);
            }
        }

        // Set session utama
        if ($dataGuruKelas) {
            $IdTahunAjaranList = array_unique($IdTahunAjaranList);
            $IdTahunAjaranList = array_values($IdTahunAjaranList);

            // Ambil IdKelas pada tahun ajaran terakhir
            $kelasOnLatestTa = $this->helpFunctionModel->getListKelas(
                IdTpq: $IdTpq,
                IdTahunAjaran: $IdTahunAjaranList[count($IdTahunAjaranList) - 1],
                IdGuru: $idGuru
            );
            $IdKelasList = array_map(function ($kelas) {
                return $kelas->IdKelas;
            }, $kelasOnLatestTa);

            session()->set('IdGuru', $idGuru);
            session()->set('IdKelas', $IdKelasList);
            session()->set('IdJabatan', $IdJabatanList);
            session()->set('IdTahunAjaranList', $IdTahunAjaranList);
            session()->set('IdTahunAjaran', $IdTahunAjaranList[count($IdTahunAjaranList) - 1]);
            session()->set('IdTpq', $IdTpq);
        } else {
            session()->set('IdGuru', $idGuru);
            $IdTpqArr = $this->helpFunctionModel->getIdTpq($idGuru);
            if (is_array($IdTpqArr) && isset($IdTpqArr['IdTpq'])) {
                session()->set('IdTpq', $IdTpqArr['IdTpq']);
            }

            // Get tahun ajaran dari tabel tbl_guru_kelas filter IdTpq ggrouped IdTahunAjaran
            $IdTahunAjaranArr = $this->helpFunctionModel->getListIdTahunAjaranFromGuruKelas(IdTpq: $IdTpqArr['IdTpq']);
            if (is_array($IdTahunAjaranArr) && !empty($IdTahunAjaranArr)) {
                session()->set('IdTahunAjaranList', $IdTahunAjaranArr);
                $idTahunAjaran = $IdTahunAjaranArr[count($IdTahunAjaranArr) - 1];
                session()->set('IdTahunAjaran', $idTahunAjaran);
            }
        }
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

        // Gabungkan data
        $result = [];
        if (is_array($kelasIds)) {
            foreach ($kelasIds as $idKelas) {
                if (isset($statusNilai[$idKelas])) {
                    $result[] = [
                        'IdKelas' => $idKelas,
                        'NamaKelas' => $namaKelas[$idKelas] ?? '',
                        'StatusInputNilai' => $statusNilai[$idKelas] ?? false
                    ];
                }
            }
        } else {
            // Kondisi jika $kelasIds bukan array (single ID)
            $idKelas = $kelasIds;
            if (isset($statusNilai[$idKelas])) {
                $result[] = [
                    'IdKelas' => $idKelas,
                    'NamaKelas' => $namaKelas[$idKelas] ?? '',
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

        return [
            'page_title' => 'Dashboard',
            'JumlahKelasDiajar' => $JumlahKelasDiajar,
            'TotalSantri' => $totalSantri,
            'TotalTabungan' => $saldoTabungan ?? 0,
            'TahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran),
            'StatusInputNilaiSemesterGanjil' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                IdKelas: $idKelas,
                Semester: 'Ganjil'
            ),
            'StatusInputNilaiSemesterGenap' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                IdKelas: $idKelas,
                Semester: 'Genap'
            ),
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

        return [
            'page_title' => 'Dashboard',
            'TotalWaliKelas' => $this->helpFunctionModel->getTotalWaliKelas(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
            ),
            'TotalSantri' => $this->helpFunctionModel->getTotalSantri(IdTpq: $idTpq),
            'TotalGuru' => $this->helpFunctionModel->getTotalGuru(IdTpq: $idTpq),
            'TotalKelas' => $this->helpFunctionModel->getTotalKelas(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
            ),
            'TotalSantriBaru' => $this->helpFunctionModel->getTotalSantriBaru(
                IdTpq: $idTpq,
                IdKelas: session()->get('IdKelas'),
            ),
            'TahunAjaran' => $this->helpFunctionModel->convertTahunAjaran($idTahunAjaran),
            'StatusInputNilaiSemesterGanjil' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                Semester: 'Ganjil'
            ),
            'StatusInputNilaiSemesterGenap' => $this->helpFunctionModel->getStatusInputNilai(
                IdTpq: $idTpq,
                IdTahunAjaran: $idTahunAjaran,
                Semester: 'Genap'
            ),
            'StatusInputNilaiPerKelasGanjil' => $statusInputNilaiPerKelasGanjil,
            'StatusInputNilaiPerKelasGenap' => $statusInputNilaiPerKelasGenap,
            'JumlahSantriPerKelas' => $jumlahSantriPerKelas,
        ];
    }



    public function index()
    {
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $idKelas = session()->get('IdKelas');
        $idGuru = session()->get('IdGuru');
        // Tentukan nama login dan peran login
        $namaLogin = null;
        $sapaanLogin = null;
        if (!empty($idGuru)) {
            $guruRow = $this->helpFunctionModel->getGuruById($idGuru);
            if (is_array($guruRow)) {
                if (isset($guruRow['Nama'])) {
                    $namaLogin = $guruRow['Nama'];
                }
                $jk = strtolower(trim($guruRow['JenisKelamin'] ?? ''));
                // Asumsi: 'P' atau 'Perempuan' => Ustadzah; lainnya => Ustadz
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

        // Tentukan peran login dengan cek langsung ke tbl_guru_kelas berdasarkan session
        $peranLogin = (in_groups('Admin') ? 'Admin' : (in_groups('Operator') ? 'Operator' : 'Pengguna'));
        if (in_groups('Guru')) {
            $peranLogin = 'Guru Kelas';
            try {
                $guruKelasRows = $this->helpFunctionModel->getDataGuruKelas(
                    IdGuru: $idGuru,
                    IdTpq: $idTpq,
                    IdKelas: $idKelas,
                    IdTahunAjaran: $idTahunAjaran
                );
                if (!empty($guruKelasRows)) {
                    foreach ($guruKelasRows as $row) {
                        if (isset($row->IdJabatan) && (int)$row->IdJabatan === 3) {
                            $peranLogin = 'Wali Kelas';
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // fallback tetap 'Guru Kelas' jika terjadi error
            }
        }

        if (in_groups('Guru')) {
            $data = $this->getGuruDashboardData($idTpq, $idTahunAjaran, $idKelas, $idGuru);
        } else if (in_groups('Admin') || in_groups('Operator')) {
            $idTahunAjaran = $this->helpFunctionModel->getTahunAjaranSaatIni();
            $data = $this->getAdminDashboardData($idTpq, $idTahunAjaran);
        } else {
            $data = ['page_title' => 'Dashboard'];
        }

        // Tambahkan NamaLogin dan PeranLogin ke data view
        $data['NamaLogin'] = $namaLogin;
        $data['PeranLogin'] = $peranLogin;
        $data['SapaanLogin'] = $sapaanLogin;

        // Jika wali kelas, kumpulkan daftar nama kelas yang diwalikan
        if ($peranLogin === 'Wali Kelas') {
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
                            $kelasNames[$row->NamaKelas] = true; // unique by name
                        }
                    }
                }
                $data['WaliKelasNamaKelas'] = implode(', ', array_keys($kelasNames));
            } catch (\Throwable $e) {
                $data['WaliKelasNamaKelas'] = '';
            }
        }

        return view('backend/dashboard/index', $data);
    }
    public function logout()
    {
        // Hapus session
        session()->destroy();

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
