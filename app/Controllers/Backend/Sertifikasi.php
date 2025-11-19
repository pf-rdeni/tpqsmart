<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SertifikasiGuruModel;
use App\Models\SertifikasiNilaiModel;
use App\Models\SertifikasiJuriModel;
use App\Models\SertifikasiGroupMateriModel;
use App\Models\SertifikasiMateriModel;
use App\Models\UserModel;
use App\Models\HelpFunctionModel;
use Myth\Auth\Password;

class Sertifikasi extends BaseController
{
    protected $sertifikasiGuruModel;
    protected $sertifikasiNilaiModel;
    protected $sertifikasiJuriModel;
    protected $sertifikasiGroupMateriModel;
    protected $sertifikasiMateriModel;
    protected $userModel;
    protected $helpFunction;
    protected $db;

    public function __construct()
    {
        $this->sertifikasiGuruModel = new SertifikasiGuruModel();
        $this->sertifikasiNilaiModel = new SertifikasiNilaiModel();
        $this->sertifikasiJuriModel = new SertifikasiJuriModel();
        $this->sertifikasiGroupMateriModel = new SertifikasiGroupMateriModel();
        $this->sertifikasiMateriModel = new SertifikasiMateriModel();
        $this->userModel = new UserModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Dashboard untuk JuriSertifikasi
     */
    public function dashboard()
    {
        // Ambil username juri dari user yang login
        $usernameJuri = user()->username;
        
        // Ambil informasi juri dari username juri
        $juriData = $this->sertifikasiJuriModel->getJuriByUsernameJuri($usernameJuri);
        
        if (empty($juriData)) {
            return redirect()->to(base_url('auth/index'))->with('error', 'Data juri tidak ditemukan');
        }

        // Convert object to array untuk kompatibilitas
        $juriDataArray = is_object($juriData) ? (array)$juriData : $juriData;
        
        // Ambil informasi group materi
        $groupMateri = $this->sertifikasiGroupMateriModel->getGroupMateriById($juriDataArray['IdGroupMateri']);
        
        // Ambil materi berdasarkan IdGroupMateri juri
        $materiList = $this->sertifikasiMateriModel->getMateriByGrupMateri($juriDataArray['IdGroupMateri']);
        
        // Statistik: Total Peserta (semua peserta yang terdaftar)
        $totalPeserta = $this->sertifikasiGuruModel->countAllResults();
        
        // Statistik: Total Peserta yang sudah dinilai oleh Juri Ini
        $totalPesertaDinilaiOlehJuriIni = $this->db->query("
            SELECT COUNT(DISTINCT NoPeserta) as total 
            FROM tbl_sertifikasi_nilai 
            WHERE IdJuri = ?
        ", [$juriDataArray['IdJuri']])->getRow()->total ?? 0;

        // Statistik: Peserta terakhir yang dinilai (5 terakhir)
        $pesertaTerakhir = $this->getPesertaTerakhirByJuri($juriDataArray['IdJuri']);
        
        $data = [
            'page_title' => 'Dashboard Sertifikasi',
            'juri_data' => $juriDataArray,
            'group_materi' => $groupMateri,
            'materi_list' => $materiList,
            'total_peserta' => $totalPeserta,
            'total_peserta_dinilai_oleh_juri_ini' => $totalPesertaDinilaiOlehJuriIni,
            'peserta_terakhir' => $pesertaTerakhir,
        ];

        return view('backend/sertifikasi/dashboard', $data);
    }

    /**
     * Dashboard untuk Admin
     */
    public function dashboardAdmin()
    {
        // Statistik: Total Peserta
        $totalPeserta = $this->sertifikasiGuruModel->countAllResults();

        // Statistik: Peserta yang sudah test (ada nilai)
        $pesertaSudahTest = $this->db->query("
            SELECT COUNT(DISTINCT NoPeserta) as total 
            FROM tbl_sertifikasi_nilai
        ")->getRow()->total ?? 0;

        // Statistik: Peserta yang belum test
        $pesertaBelumTest = $totalPeserta - $pesertaSudahTest;

        // Statistik: Total Juri
        $totalJuri = $this->sertifikasiJuriModel->countAllResults();

        // Statistik: Total Nilai yang sudah diinput
        $totalNilai = $this->sertifikasiNilaiModel->countAllResults();

        // Statistik: Total Materi
        $totalMateri = $this->sertifikasiMateriModel->countAllResults();

        // Ambil semua materi aktif
        $allMateri = $this->sertifikasiMateriModel->getMateriAktif();

        // Statistik per materi: berapa peserta yang sudah dinilai
        $statistikPerMateri = [];
        foreach ($allMateri as $materi) {
            $jumlahPesertaSudahDinilai = $this->db->query("
                SELECT COUNT(DISTINCT NoPeserta) as total 
                FROM tbl_sertifikasi_nilai 
                WHERE IdMateri = ?
            ", [$materi['IdMateri']])->getRow()->total ?? 0;

            $statistikPerMateri[] = [
                'IdMateri' => $materi['IdMateri'],
                'NamaMateri' => $materi['NamaMateri'],
                'jumlahPesertaSudahDinilai' => $jumlahPesertaSudahDinilai,
                'persentase' => $totalPeserta > 0 ? ($jumlahPesertaSudahDinilai / $totalPeserta) * 100 : 0
            ];
        }

        // Ambil 10 peserta terakhir yang sudah dinilai
        $pesertaTerakhir = $this->db->query("
            SELECT DISTINCT 
                sn.NoPeserta,
                sg.Nama as NamaGuru,
                sn.updated_at,
                GROUP_CONCAT(DISTINCT sj.usernameJuri SEPARATOR ', ') as usernameJuri
            FROM tbl_sertifikasi_nilai sn
            LEFT JOIN tbl_sertifikasi_guru sg ON sg.NoPeserta = sn.NoPeserta
            LEFT JOIN tbl_sertifikasi_juri sj ON sj.IdJuri = sn.IdJuri
            GROUP BY sn.NoPeserta, sg.Nama, sn.updated_at
            ORDER BY sn.updated_at DESC
            LIMIT 10
        ")->getResultArray();

        // Statistik per juri: berapa peserta yang sudah dinilai
        $statistikPerJuri = $this->db->query("
            SELECT 
                sj.IdJuri,
                sj.usernameJuri,
                sgm.NamaMateri as NamaGroupMateri,
                COUNT(DISTINCT sn.NoPeserta) as jumlahPesertaDinilai
            FROM tbl_sertifikasi_juri sj
            LEFT JOIN tbl_sertifikasi_nilai sn ON sn.IdJuri = sj.IdJuri
            LEFT JOIN tbl_sertifikasi_group_materi sgm ON sgm.IdGroupMateri = sj.IdGroupMateri
            GROUP BY sj.IdJuri, sj.usernameJuri, sgm.NamaMateri
            ORDER BY jumlahPesertaDinilai DESC
        ")->getResultArray();

        // Menu yang bisa diakses Admin
        $menuItems = [
            'list_peserta' => base_url('backend/sertifikasi/listPesertaSertifikasi'),
            'list_nilai' => base_url('backend/sertifikasi/listNilaiSertifikasi'),
            'list_juri' => base_url('backend/sertifikasi/listJuriSertifikasi'),
        ];

        $data = [
            'page_title' => 'Dashboard Sertifikasi - Admin',
            'total_peserta' => $totalPeserta,
            'peserta_sudah_test' => $pesertaSudahTest,
            'peserta_belum_test' => $pesertaBelumTest,
            'total_juri' => $totalJuri,
            'total_nilai' => $totalNilai,
            'total_materi' => $totalMateri,
            'statistik_per_materi' => $statistikPerMateri,
            'peserta_terakhir' => $pesertaTerakhir,
            'statistik_per_juri' => $statistikPerJuri,
            'menu_items' => $menuItems,
        ];

        return view('backend/sertifikasi/dashboardAdmin', $data);
    }

    /**
     * Dashboard untuk PanitiaSertifikasi
     */
    public function dashboardPanitiaSertifikasi()
    {
        // Statistik: Total Peserta
        $totalPeserta = $this->sertifikasiGuruModel->countAllResults();

        // Statistik: Peserta yang sudah test (ada nilai)
        $pesertaSudahTest = $this->db->query("
            SELECT COUNT(DISTINCT NoPeserta) as total 
            FROM tbl_sertifikasi_nilai
        ")->getRow()->total ?? 0;

        // Statistik: Peserta yang belum test
        $pesertaBelumTest = $totalPeserta - $pesertaSudahTest;

        // Statistik: Total Juri
        $totalJuri = $this->sertifikasiJuriModel->countAllResults();

        // Statistik: Total Nilai yang sudah diinput
        $totalNilai = $this->sertifikasiNilaiModel->countAllResults();

        // Statistik: Total Materi
        $totalMateri = $this->sertifikasiMateriModel->countAllResults();

        // Ambil semua materi aktif
        $allMateri = $this->sertifikasiMateriModel->getMateriAktif();

        // Statistik per materi: berapa peserta yang sudah dinilai
        $statistikPerMateri = [];
        foreach ($allMateri as $materi) {
            $jumlahPesertaSudahDinilai = $this->db->query("
                SELECT COUNT(DISTINCT NoPeserta) as total 
                FROM tbl_sertifikasi_nilai 
                WHERE IdMateri = ?
            ", [$materi['IdMateri']])->getRow()->total ?? 0;

            $statistikPerMateri[] = [
                'IdMateri' => $materi['IdMateri'],
                'NamaMateri' => $materi['NamaMateri'],
                'jumlahPesertaSudahDinilai' => $jumlahPesertaSudahDinilai,
                'persentase' => $totalPeserta > 0 ? ($jumlahPesertaSudahDinilai / $totalPeserta) * 100 : 0
            ];
        }

        // Ambil 10 peserta terakhir yang sudah dinilai
        $pesertaTerakhir = $this->db->query("
            SELECT DISTINCT 
                sn.NoPeserta,
                sg.Nama as NamaGuru,
                sn.updated_at,
                GROUP_CONCAT(DISTINCT sj.usernameJuri SEPARATOR ', ') as usernameJuri
            FROM tbl_sertifikasi_nilai sn
            LEFT JOIN tbl_sertifikasi_guru sg ON sg.NoPeserta = sn.NoPeserta
            LEFT JOIN tbl_sertifikasi_juri sj ON sj.IdJuri = sn.IdJuri
            GROUP BY sn.NoPeserta, sg.Nama, sn.updated_at
            ORDER BY sn.updated_at DESC
            LIMIT 10
        ")->getResultArray();

        // Statistik per juri: berapa peserta yang sudah dinilai
        $statistikPerJuri = $this->db->query("
            SELECT 
                sj.IdJuri,
                sj.usernameJuri,
                sgm.NamaMateri as NamaGroupMateri,
                COUNT(DISTINCT sn.NoPeserta) as jumlahPesertaDinilai
            FROM tbl_sertifikasi_juri sj
            LEFT JOIN tbl_sertifikasi_nilai sn ON sn.IdJuri = sj.IdJuri
            LEFT JOIN tbl_sertifikasi_group_materi sgm ON sgm.IdGroupMateri = sj.IdGroupMateri
            GROUP BY sj.IdJuri, sj.usernameJuri, sgm.NamaMateri
            ORDER BY jumlahPesertaDinilai DESC
        ")->getResultArray();

        $data = [
            'page_title' => 'Dashboard Panitia Sertifikasi',
            'total_peserta' => $totalPeserta,
            'peserta_sudah_test' => $pesertaSudahTest,
            'peserta_belum_test' => $pesertaBelumTest,
            'total_juri' => $totalJuri,
            'total_nilai' => $totalNilai,
            'total_materi' => $totalMateri,
            'statistik_per_materi' => $statistikPerMateri,
            'peserta_terakhir' => $pesertaTerakhir,
            'statistik_per_juri' => $statistikPerJuri,
        ];

        return view('backend/sertifikasi/dashboardPanitiaSertifikasi', $data);
    }

    /**
     * Halaman input nilai sertifikasi untuk juri
     */
    public function inputNilaiSertifikasi()
    {
        // Ambil username juri dari user yang login
        $usernameJuri = user()->username;
        
        // Ambil informasi juri dari username juri
        $juriData = $this->sertifikasiJuriModel->getJuriByUsernameJuri($usernameJuri);
        
        if (empty($juriData)) {
            return redirect()->to(base_url('auth/index'))->with('error', 'Data juri tidak ditemukan');
        }

        // Convert object to array untuk kompatibilitas
        $juriDataArray = is_object($juriData) ? (array)$juriData : $juriData;
        
        // Ambil 5 peserta terakhir yang sudah dinilai oleh juri ini
        $pesertaTerakhir = $this->getPesertaTerakhirByJuri($juriDataArray['IdJuri']);

        // Cek apakah juri adalah GMS002 (Materi Praktek)
        $isGMS002 = ($juriDataArray['IdGroupMateri'] === 'GMS002');

        $data = [
            'page_title' => 'Input Nilai Sertifikasi',
            'juri_data' => $juriDataArray,
            'peserta_terakhir' => $pesertaTerakhir,
            'is_gms002' => $isGMS002,
        ];

        return view('backend/sertifikasi/inputNilaiSertifikasi', $data);
    }

    /**
     * Cek peserta untuk input nilai
     */
    public function cekPeserta()
    {
        try {
            $noTest = $this->request->getPost('noTest');
            $idJuri = $this->request->getPost('IdJuri');

            if (empty($noTest)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_NO_TEST',
                    'message' => 'Nomor test tidak boleh kosong',
                ]);
            }

            if (empty($idJuri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_ID_JURI',
                    'message' => 'ID Juri tidak boleh kosong',
                ]);
            }

            // Ambil data guru
            $guruData = $this->sertifikasiGuruModel->getGuruByNoPeserta($noTest);
            
            if (empty($guruData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'GURU_NOT_FOUND',
                    'message' => 'Data guru dengan nomor test ' . $noTest . ' tidak ditemukan',
                ]);
            }

            // Convert object to array untuk kompatibilitas
            $guruDataArray = is_object($guruData) ? (array)$guruData : $guruData;

            // Ambil data juri
            $juriData = $this->sertifikasiJuriModel->getJuriByIdJuri($idJuri);
            
            if (empty($juriData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'JURI_NOT_FOUND',
                    'message' => 'Data juri tidak ditemukan',
                ]);
            }

            // Convert object to array
            $juriDataArray = is_object($juriData) ? (array)$juriData : $juriData;

            // Ambil group materi
            $groupMateri = $this->sertifikasiGroupMateriModel->getGroupMateriById($juriDataArray['IdGroupMateri']);
            
            if (empty($groupMateri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'GROUP_MATERI_NOT_FOUND',
                    'message' => 'Data group materi tidak ditemukan',
                ]);
            }

            // Ambil materi berdasarkan IdGroupMateri
            $materiList = $this->sertifikasiMateriModel->getMateriByGrupMateri($juriDataArray['IdGroupMateri']);

            // Filter materi jika juri adalah GMS002 dan ada filter pilihan
            $filterSM001 = $this->request->getPost('filterSM001'); // 'true' atau 'false' atau null
            $filterSM004 = $this->request->getPost('filterSM004'); // 'true' atau 'false' atau null

            if ($juriDataArray['IdGroupMateri'] === 'GMS002') {
                $selectedMateri = []; // Materi yang akan ditampilkan
                $excludedMateri = []; // Materi yang akan dikecualikan

                // Proses filter SM001
                if ($filterSM001 !== null) {
                    if ($filterSM001 === 'true' || $filterSM001 === true) {
                        // Hanya tampilkan SM001
                        $selectedMateri[] = 'SM001';
                    } else {
                        // Kecualikan SM001
                        $excludedMateri[] = 'SM001';
                    }
                }

                // Proses filter SM004
                if ($filterSM004 !== null) {
                    if ($filterSM004 === 'true' || $filterSM004 === true) {
                        // Hanya tampilkan SM004
                        $selectedMateri[] = 'SM004';
                    } else {
                        // Kecualikan SM004
                        $excludedMateri[] = 'SM004';
                    }
                }

                // Terapkan filter
                if (!empty($selectedMateri)) {
                    // Jika ada materi yang dipilih, hanya tampilkan yang dipilih
                    $materiList = array_filter($materiList, function ($materi) use ($selectedMateri) {
                        return in_array($materi['IdMateri'], $selectedMateri);
                    });
                    $materiList = array_values($materiList); // Re-index array
                } elseif (!empty($excludedMateri)) {
                    // Jika tidak ada yang dipilih tapi ada yang dikecualikan, kecualikan yang dikecualikan
                    $materiList = array_filter($materiList, function ($materi) use ($excludedMateri) {
                        return !in_array($materi['IdMateri'], $excludedMateri);
                    });
                    $materiList = array_values($materiList); // Re-index array
                }
            }

            // Cek apakah semua materi sudah dinilai oleh juri lain dan ambil informasi juri
            $allMateriSudahDinilai = true;
            $juriInfoByMateri = []; // Informasi juri yang sudah menilai per materi
            foreach ($materiList as $materi) {
                $existingNilaiWithJuri = $this->sertifikasiNilaiModel->getNilaiWithJuriInfoByMateri(
                    $noTest,
                    $juriDataArray['IdGroupMateri'],
                    $materi['IdMateri']
                );

                if (!empty($existingNilaiWithJuri)) {
                    // Simpan informasi juri yang sudah menilai
                    $juriInfoByMateri[$materi['IdMateri']] = [
                        'IdJuri' => $existingNilaiWithJuri['IdJuri'] ?? null,
                        'usernameJuri' => $existingNilaiWithJuri['usernameJuri'] ?? null,
                    ];
                } else {
                    $allMateriSudahDinilai = false;
                }
            }

            // Jika semua materi sudah dinilai oleh juri lain, tolak dengan informasi juri
            if ($allMateriSudahDinilai) {
                // Buat daftar juri yang sudah menilai
                $juriList = [];
                foreach ($juriInfoByMateri as $idMateri => $juriInfo) {
                    if (!empty($juriInfo['usernameJuri'])) {
                        $juriList[] = $juriInfo['usernameJuri'];
                    }
                }
                $juriList = array_unique($juriList);
                $juriListStr = !empty($juriList) ? ' oleh ' . implode(', ', $juriList) : '';

                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'ALL_MATERI_ALREADY_SCORED',
                    'message' => 'Semua materi untuk peserta ini sudah dinilai' . $juriListStr,
                    'juriInfoByMateri' => $juriInfoByMateri,
                ]);
            }

            // Ambil nilai yang sudah ada untuk setiap materi (dari juri yang login)
            $existingNilaiByMateri = [];
            if (!empty($materiList)) {
                foreach ($materiList as $materi) {
                    $existingNilai = $this->sertifikasiNilaiModel->checkNilaiExistsByMateri(
                        $noTest,
                        $idJuri,
                        $juriDataArray['IdGroupMateri'],
                        $materi['IdMateri']
                    );
                    $existingNilaiByMateri[$materi['IdMateri']] = $existingNilai;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'SUCCESS',
                'data' => [
                    'guru' => $guruDataArray,
                    'juri' => $juriData,
                    'groupMateri' => $groupMateri,
                    'materiList' => $materiList,
                    'existingNilaiByMateri' => $existingNilaiByMateri,
                    'allMateriSudahDinilai' => $allMateriSudahDinilai,
                    'juriInfoByMateri' => $juriInfoByMateri, // Informasi juri yang sudah menilai per materi
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in cekPeserta: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'SYSTEM_ERROR',
                'code' => 'INTERNAL_ERROR',
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan nilai sertifikasi
     */
    public function simpanNilai()
    {
        try {
            $noTest = $this->request->getPost('noTest');
            $idJuri = $this->request->getPost('IdJuri');
            $nilaiData = $this->request->getPost('nilai'); // Array of nilai per materi

            // Validasi parameter wajib
            if (empty($noTest)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_NO_TEST',
                    'message' => 'Nomor test tidak boleh kosong',
                ]);
            }

            if (empty($idJuri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_ID_JURI',
                    'message' => 'ID Juri tidak boleh kosong',
                ]);
            }

            if (empty($nilaiData) || !is_array($nilaiData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_NILAI',
                    'message' => 'Data nilai harus diisi dan berupa array',
                ]);
            }

            // Ambil data juri untuk mendapatkan IdGroupMateri
            $juriData = $this->sertifikasiJuriModel->getJuriByIdJuri($idJuri);
            if (empty($juriData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'JURI_NOT_FOUND',
                    'message' => 'Data juri tidak ditemukan',
                ]);
            }
            $juriDataArray = is_object($juriData) ? (array)$juriData : $juriData;
            $idGroupMateri = $juriDataArray['IdGroupMateri'];

            // Validasi setiap nilai
            foreach ($nilaiData as $idMateri => $nilai) {
                if (empty($nilai) || !is_numeric($nilai)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'VALIDATION_ERROR',
                        'code' => 'MISSING_NILAI',
                        'message' => 'Nilai untuk materi ' . $idMateri . ' harus diisi dan berupa angka',
                    ]);
                }

                $nilaiFloat = floatval($nilai);
                if ($nilaiFloat < 0 || $nilaiFloat > 100) {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'VALIDATION_ERROR',
                        'code' => 'NILAI_OUT_OF_RANGE',
                        'message' => 'Nilai untuk materi ' . $idMateri . ' harus dalam range 0-100',
                    ]);
                }
            }

            // Mulai transaksi database
            $this->db->transStart();

            // Simpan atau update nilai untuk setiap materi
            foreach ($nilaiData as $idMateri => $nilai) {
                $nilaiFloat = floatval($nilai);
                
                // Cek apakah nilai sudah ada
                $existingNilai = $this->sertifikasiNilaiModel->checkNilaiExistsByMateri(
                    $noTest,
                    $idJuri,
                    $idGroupMateri,
                    $idMateri
                );

                if ($existingNilai) {
                    // Update nilai yang sudah ada
                    $this->sertifikasiNilaiModel->update($existingNilai['id'], [
                        'Nilai' => $nilaiFloat,
                    ]);
                } else {
                    // Insert nilai baru
                    $this->sertifikasiNilaiModel->insert([
                        'NoPeserta' => $noTest,
                        'IdGroupMateri' => $idGroupMateri,
                        'IdMateri' => $idMateri,
                        'IdJuri' => $idJuri,
                        'Nilai' => $nilaiFloat,
                    ]);
                }
            }

            // Selesaikan transaksi
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATABASE_ERROR',
                    'code' => 'TRANSACTION_FAILED',
                    'message' => 'Gagal menyimpan data nilai',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'SUCCESS',
                'message' => 'Nilai berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in simpanNilai: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'SYSTEM_ERROR',
                'code' => 'INTERNAL_ERROR',
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman list nilai sertifikasi untuk admin
     */
    public function listNilaiSertifikasi()
    {
        // Ambil semua materi aktif untuk menentukan kolom
        $allMateri = $this->sertifikasiMateriModel->getMateriAktif();
        
        // Urutkan materi sesuai urutan yang diminta:
        // 1. Materi Pilihan Ganda (SM001)
        // 2. Tulis Al-Quran (SM004)
        // 3. Baca Al-Quran (SM002)
        // 4. Praktek Sholat (SM003)
        $urutanMateri = ['SM001', 'SM004', 'SM002', 'SM003'];
        usort($allMateri, function($a, $b) use ($urutanMateri) {
            $posA = array_search($a['IdMateri'], $urutanMateri);
            $posB = array_search($b['IdMateri'], $urutanMateri);
            
            // Jika tidak ditemukan, taruh di akhir
            if ($posA === false) $posA = 999;
            if ($posB === false) $posB = 999;
            
            return $posA <=> $posB;
        });
        
        // Ambil semua nilai dengan relations
        $nilaiData = $this->sertifikasiNilaiModel->getAllNilaiWithRelations();

        // Group by NoPeserta dan ambil nilai per materi (hanya satu nilai per materi)
        $groupedData = [];
        foreach ($nilaiData as $nilai) {
            $noPeserta = $nilai['NoPeserta'] ?? $nilai['noTest'] ?? null;
            if (!$noPeserta) continue;

            if (!isset($groupedData[$noPeserta])) {
                $groupedData[$noPeserta] = [
                    'noTest' => $nilai['noTest'] ?? $noPeserta,
                    'NoPeserta' => $noPeserta,
                    'NamaGuru' => $nilai['NamaGuru'] ?? '-',
                    'NoRek' => $nilai['NoRek'] ?? '-',
                    'NamaTpq' => $nilai['NamaTpq'] ?? '-',
                    'usernameJuri' => [], // Array untuk menyimpan semua username juri yang menilai
                    'nilaiByMateri' => [], // Array dengan key IdMateri
                ];
            }

            // Simpan username juri jika ada dan belum ada di array
            $usernameJuri = $nilai['usernameJuri'] ?? null;
            if ($usernameJuri && !in_array($usernameJuri, $groupedData[$noPeserta]['usernameJuri'])) {
                $groupedData[$noPeserta]['usernameJuri'][] = $usernameJuri;
            }

            // Simpan nilai per materi (hanya simpan yang pertama jika ada duplikat)
            $idMateri = $nilai['IdMateri'] ?? null;
            if ($idMateri && !isset($groupedData[$noPeserta]['nilaiByMateri'][$idMateri])) {
                $groupedData[$noPeserta]['nilaiByMateri'][$idMateri] = $nilai['Nilai'] ?? 0;
            }
        }

        // Konversi array usernameJuri menjadi string (dipisahkan koma)
        foreach ($groupedData as &$peserta) {
            $peserta['usernameJuri'] = !empty($peserta['usernameJuri']) ? implode(', ', $peserta['usernameJuri']) : '-';
        }
        unset($peserta);

        // Hitung jumlah dan rata-rata untuk setiap peserta
        // Rata-rata dihitung berdasarkan jumlah semua materi (bukan hanya yang ada nilainya)
        $totalMateri = count($allMateri);
        foreach ($groupedData as &$peserta) {
            $totalNilai = 0;
            $jumlahMateriDenganNilai = 0;
            
            // Hitung total nilai dan jumlah materi yang ada nilainya
            foreach ($allMateri as $materi) {
                $idMateri = $materi['IdMateri'];
                $nilai = $peserta['nilaiByMateri'][$idMateri] ?? null;
                
                if ($nilai !== null && $nilai !== '') {
                    $totalNilai += floatval($nilai);
                    $jumlahMateriDenganNilai++;
                } else {
                    // Jika belum ada nilai, set ke 0 untuk perhitungan
                    $totalNilai += 0;
                }
            }
            
            $peserta['jumlah'] = $totalNilai;
            // Rata-rata dihitung berdasarkan jumlah semua materi
            $peserta['rataRata'] = $totalMateri > 0 ? $totalNilai / $totalMateri : 0;
        }
        unset($peserta);

        $data = [
            'page_title' => 'List Nilai Sertifikasi',
            'nilai_data' => array_values($groupedData),
            'all_materi' => $allMateri, // Untuk header kolom
        ];

        return view('backend/sertifikasi/listNilaiSertifikasi', $data);
    }

    /**
     * Halaman list peserta sertifikasi (sudah test dan belum test)
     */
    public function listPesertaSertifikasi()
    {
        // Ambil semua peserta
        $allPeserta = $this->sertifikasiGuruModel->getAllGuru();

        // Ambil semua NoPeserta yang sudah ada nilai
        $pesertaSudahTest = $this->db->query("
            SELECT DISTINCT NoPeserta 
            FROM tbl_sertifikasi_nilai
        ")->getResultArray();

        // Buat array untuk memudahkan pengecekan
        $noPesertaSudahTest = [];
        foreach ($pesertaSudahTest as $peserta) {
            $noPesertaSudahTest[] = $peserta['NoPeserta'];
        }

        // Tambahkan status untuk setiap peserta
        $pesertaData = [];
        foreach ($allPeserta as $peserta) {
            $noPeserta = $peserta['NoPeserta'];
            $pesertaArray = is_object($peserta) ? (array)$peserta : $peserta;
            $pesertaArray['sudahTest'] = in_array($noPeserta, $noPesertaSudahTest);
            $pesertaData[] = $pesertaArray;
        }

        // Hitung statistik
        $totalPeserta = count($pesertaData);
        $sudahTest = count(array_filter($pesertaData, function ($p) {
            return $p['sudahTest'];
        }));
        $belumTest = $totalPeserta - $sudahTest;

        // Ambil data NamaTpq yang unik dari tabel sertifikasi_guru (grouped)
        $tpqList = $this->db->query("
            SELECT DISTINCT NamaTpq 
            FROM tbl_sertifikasi_guru 
            WHERE NamaTpq IS NOT NULL AND NamaTpq != '' 
            ORDER BY NamaTpq ASC
        ")->getResultArray();

        $data = [
            'page_title' => 'List Peserta Sertifikasi',
            'peserta_data' => $pesertaData,
            'total_peserta' => $totalPeserta,
            'sudah_test' => $sudahTest,
            'belum_test' => $belumTest,
            'tpq_list' => $tpqList,
        ];

        return view('backend/sertifikasi/listPesertaSertifikasi', $data);
    }

    /**
     * Get next NoPeserta untuk form tambah peserta
     */
    public function getNextNoPeserta()
    {
        try {
            $nextNoPeserta = $this->sertifikasiGuruModel->generateNextNoPeserta(100, 999);
            
            if ($nextNoPeserta === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat menghasilkan nomor peserta. Semua nomor dalam range sudah digunakan.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'NoPeserta' => $nextNoPeserta,
                'message' => 'Nomor peserta berhasil di-generate'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getNextNoPeserta: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store peserta sertifikasi baru
     */
    public function storePesertaSertifikasi()
    {
        try {
            // Validasi input
            $validation = \Config\Services::validation();
            $validation->setRules([
                'NoPeserta' => [
                    'label' => 'No Peserta',
                    'rules' => 'required|max_length[50]',
                    'errors' => [
                        'required' => 'No Peserta harus diisi',
                        'max_length' => 'No Peserta maksimal 50 karakter'
                    ]
                ],
                'Nama' => [
                    'label' => 'Nama Guru',
                    'rules' => 'required|max_length[255]',
                    'errors' => [
                        'required' => 'Nama Guru harus diisi',
                        'max_length' => 'Nama Guru maksimal 255 karakter'
                    ]
                ],
                'NoRek' => [
                    'label' => 'No Rek',
                    'rules' => 'permit_empty|max_length[50]',
                    'errors' => [
                        'max_length' => 'No Rek maksimal 50 karakter'
                    ]
                ],
                'NamaTpq' => [
                    'label' => 'Nama TPQ',
                    'rules' => 'permit_empty|max_length[255]',
                    'errors' => [
                        'max_length' => 'Nama TPQ maksimal 255 karakter'
                    ]
                ],
                'JenisKelamin' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'permit_empty|max_length[20]',
                    'errors' => [
                        'max_length' => 'Jenis Kelamin maksimal 20 karakter'
                    ]
                ],
                'Kecamatan' => [
                    'label' => 'Kecamatan',
                    'rules' => 'permit_empty|max_length[255]',
                    'errors' => [
                        'max_length' => 'Kecamatan maksimal 255 karakter'
                    ]
                ],
                'Note' => [
                    'label' => 'Catatan',
                    'rules' => 'permit_empty',
                ]
            ]);

            if (!$validation->run($this->request->getPost())) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validation->getErrors()
                ]);
            }

            // Cek duplikasi NoPeserta
            $noPeserta = $this->request->getPost('NoPeserta');
            
            if ($this->sertifikasiGuruModel->isNoPesertaExists($noPeserta)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No Peserta sudah terdaftar',
                    'errors' => [
                        'NoPeserta' => 'No Peserta ' . $noPeserta . ' sudah terdaftar di sistem'
                    ]
                ]);
            }

            // Siapkan data untuk insert
            // Convert nama ke uppercase
            $nama = strtoupper(trim($this->request->getPost('Nama')));
            
            $data = [
                'NoPeserta' => $noPeserta,
                'Nama' => $nama,
                'NoRek' => $this->request->getPost('NoRek') ?: null,
                'NamaTpq' => $this->request->getPost('NamaTpq') ?: null,
                'JenisKelamin' => $this->request->getPost('JenisKelamin') ?: null,
                'Kecamatan' => 'SERI KUALA LOBAM', // Fixed kecamatan
                'Note' => $this->request->getPost('Note') ?: null,
            ];

            // Simpan data menggunakan method model
            $insertedId = $this->sertifikasiGuruModel->insertPeserta($data);
            
            if ($insertedId !== false) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data peserta sertifikasi berhasil ditambahkan',
                    'data' => [
                        'id' => $insertedId,
                        'NoPeserta' => $noPeserta
                    ]
                ]);
            } else {
                $errors = $this->sertifikasiGuruModel->errors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data peserta',
                    'errors' => $errors ?: ['general' => 'Terjadi kesalahan saat menyimpan data']
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in storePesertaSertifikasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman nilai peserta untuk juri (hanya nilai yang dinilai oleh juri yang login)
     */
    public function nilaiPesertaSertifikasi()
    {
        // Ambil username juri dari user yang login
        $usernameJuri = user()->username;
        
        // Ambil informasi juri dari username juri
        $juriData = $this->sertifikasiJuriModel->getJuriByUsernameJuri($usernameJuri);
        
        if (empty($juriData)) {
            return redirect()->to(base_url('auth/index'))->with('error', 'Data juri tidak ditemukan');
        }

        // Convert object to array untuk kompatibilitas
        $juriDataArray = is_object($juriData) ? (array)$juriData : $juriData;
        
        // Ambil materi berdasarkan IdGroupMateri juri (bukan semua materi aktif)
        $allMateri = $this->sertifikasiMateriModel->getMateriByGrupMateri($juriDataArray['IdGroupMateri']);
        
        // Urutkan materi sesuai urutan yang diminta:
        // 1. Materi Pilihan Ganda (SM001)
        // 2. Tulis Al-Quran (SM004)
        // 3. Baca Al-Quran (SM002)
        // 4. Praktek Sholat (SM003)
        $urutanMateri = ['SM001', 'SM004', 'SM002', 'SM003'];
        usort($allMateri, function($a, $b) use ($urutanMateri) {
            $posA = array_search($a['IdMateri'], $urutanMateri);
            $posB = array_search($b['IdMateri'], $urutanMateri);
            
            // Jika tidak ditemukan, taruh di akhir
            if ($posA === false) $posA = 999;
            if ($posB === false) $posB = 999;
            
            return $posA <=> $posB;
        });
        
        // Ambil semua nilai yang dinilai oleh juri ini
        $nilaiData = $this->sertifikasiNilaiModel->getAllNilaiByIdJuri($juriDataArray['IdJuri']);

        // Group by NoPeserta dan ambil nilai per materi (hanya satu nilai per materi)
        $groupedData = [];
        foreach ($nilaiData as $nilai) {
            $noPeserta = $nilai['NoPeserta'] ?? $nilai['noTest'] ?? null;
            if (!$noPeserta) continue;

            if (!isset($groupedData[$noPeserta])) {
                $groupedData[$noPeserta] = [
                    'noTest' => $nilai['noTest'] ?? $noPeserta,
                    'NoPeserta' => $noPeserta,
                    'NamaGuru' => $nilai['NamaGuru'] ?? '-',
                    'NoRek' => $nilai['NoRek'] ?? '-',
                    'NamaTpq' => $nilai['NamaTpq'] ?? '-',
                    'nilaiByMateri' => [], // Array dengan key IdMateri
                ];
            }

            // Simpan nilai per materi (hanya simpan yang pertama jika ada duplikat)
            $idMateri = $nilai['IdMateri'] ?? null;
            if ($idMateri && !isset($groupedData[$noPeserta]['nilaiByMateri'][$idMateri])) {
                $groupedData[$noPeserta]['nilaiByMateri'][$idMateri] = $nilai['Nilai'] ?? 0;
            }
        }

        $data = [
            'page_title' => 'Nilai Peserta Sertifikasi',
            'nilai_data' => array_values($groupedData),
            'all_materi' => $allMateri, // Untuk header kolom
            'juri_data' => $juriDataArray,
        ];

        return view('backend/sertifikasi/nilaiPesertaSertifikasi', $data);
    }

    /**
     * Get peserta terakhir yang sudah dinilai oleh juri
     */
    private function getPesertaTerakhirByJuri($idJuri)
    {
        $builder = $this->db->table('tbl_sertifikasi_nilai sn');
        $builder->select('sn.NoPeserta, sn.NoPeserta as noTest, sn.updated_at, sg.Nama as NamaGuru, sj.usernameJuri');
        $builder->join('tbl_sertifikasi_guru sg', 'sg.NoPeserta = sn.NoPeserta', 'left');
        $builder->join('tbl_sertifikasi_juri sj', 'sj.IdJuri = sn.IdJuri', 'left');
        $builder->where('sn.IdJuri', $idJuri);
        $builder->groupBy('sn.NoPeserta, sg.Nama, sj.usernameJuri, sn.updated_at');
        $builder->orderBy('sn.updated_at', 'DESC');
        $builder->limit(5);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Halaman list juri sertifikasi untuk admin
     */
    public function listJuriSertifikasi()
    {
        // Ambil semua juri dengan relations
        $juriList = $this->sertifikasiJuriModel->getAllJuriWithRelations();

        $data = [
            'page_title' => 'Data Juri Sertifikasi',
            'juri_list' => $juriList,
        ];

        return view('backend/sertifikasi/listJuriSertifikasi', $data);
    }

    /**
     * Halaman create juri sertifikasi
     */
    public function createJuriSertifikasi()
    {
        // Ambil semua group materi untuk dropdown
        $groupMateriList = $this->sertifikasiGroupMateriModel->getAllGroupMateri();

        // Ambil semua auth groups untuk dropdown
        $authGroups = $this->helpFunction->getDataAuthGoups();

        // Generate ID Juri berikutnya
        $nextIdJuri = $this->generateNextIdJuri();

        $data = [
            'page_title' => 'Tambah Juri Sertifikasi',
            'group_materi_list' => $groupMateriList,
            'auth_groups' => $authGroups,
            'next_id_juri' => $nextIdJuri,
        ];

        return view('backend/sertifikasi/createJuriSertifikasi', $data);
    }

    /**
     * Generate ID Juri berikutnya berdasarkan ID terakhir
     */
    private function generateNextIdJuri()
    {
        // Ambil ID Juri terakhir berdasarkan IdJuri (bukan id primary key)
        $builder = $this->db->table('tbl_sertifikasi_juri');
        $builder->select('IdJuri');
        $builder->orderBy('IdJuri', 'DESC');
        $builder->limit(1);
        $lastJuri = $builder->get()->getRowArray();

        if (empty($lastJuri)) {
            // Jika belum ada data, mulai dari JS001
            return 'JS001';
        }

        $lastIdJuri = $lastJuri['IdJuri'];

        // Extract angka dari ID Juri (misalnya JS006 -> 6)
        if (preg_match('/JS(\d+)/', $lastIdJuri, $matches)) {
            $lastNumber = intval($matches[1]);
            $nextNumber = $lastNumber + 1;

            // Format dengan leading zero (JS007, JS008, dll)
            return 'JS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        // Jika format tidak sesuai, mulai dari JS001
        return 'JS001';
    }

    /**
     * Generate username juri berikutnya berdasarkan grup materi
     */
    public function generateNextUsernameJuri()
    {
        // Set response header untuk JSON
        $this->response->setContentType('application/json');

        try {
            $idGroupMateri = $this->request->getPost('IdGroupMateri');

            if (empty($idGroupMateri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Group Materi harus dipilih'
                ]);
            }

            // Ambil informasi grup materi
            $groupMateri = $this->sertifikasiGroupMateriModel->getGroupMateriById($idGroupMateri);
            if (empty($groupMateri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Group Materi tidak ditemukan'
                ]);
            }

            // Ambil username juri terakhir untuk grup materi ini
            $builder = $this->db->table('tbl_sertifikasi_juri');
            $builder->select('usernameJuri');
            $builder->where('IdGroupMateri', $idGroupMateri);
            $builder->orderBy('usernameJuri', 'DESC');
            $builder->limit(1);
            $lastJuri = $builder->get()->getRowArray();

            if (empty($lastJuri)) {
                // Jika belum ada data untuk grup ini, cek format dari data yang ada
                // Ambil contoh username dari grup lain untuk referensi format
                $exampleBuilder = $this->db->table('tbl_sertifikasi_juri');
                $exampleBuilder->select('usernameJuri');
                $exampleBuilder->limit(1);
                $exampleJuri = $exampleBuilder->get()->getRowArray();

                if ($exampleJuri) {
                    // Extract format dari contoh (misalnya juri.praktek.1 -> juri.praktek)
                    $exampleUsername = $exampleJuri['usernameJuri'];
                    if (preg_match('/^(.+?)\.\d+$/', $exampleUsername, $exampleMatches)) {
                        $baseFormat = $exampleMatches[1];
                        // Generate berdasarkan nama materi
                        $namaMateri = strtolower(str_replace(' ', '.', $groupMateri['NamaMateri']));
                        $namaMateri = preg_replace('/[^a-z0-9.]/', '', $namaMateri);
                        $namaMateri = str_replace(',', '', $namaMateri);
                        $namaMateri = preg_replace('/\.+/', '.', $namaMateri);
                        $namaMateri = trim($namaMateri, '.');
                        $nextUsername = 'juri.' . $namaMateri . '.1';
                    } else {
                        // Fallback ke format default
                        $namaMateri = strtolower(str_replace(' ', '.', $groupMateri['NamaMateri']));
                        $namaMateri = preg_replace('/[^a-z0-9.]/', '', $namaMateri);
                        $namaMateri = str_replace(',', '', $namaMateri);
                        $namaMateri = preg_replace('/\.+/', '.', $namaMateri);
                        $namaMateri = trim($namaMateri, '.');
                        $nextUsername = 'juri.' . $namaMateri . '.1';
                    }
                } else {
                    // Jika tidak ada data sama sekali, gunakan format default
                    $namaMateri = strtolower(str_replace(' ', '.', $groupMateri['NamaMateri']));
                    $namaMateri = preg_replace('/[^a-z0-9.]/', '', $namaMateri);
                    $namaMateri = str_replace(',', '', $namaMateri);
                    $namaMateri = preg_replace('/\.+/', '.', $namaMateri);
                    $namaMateri = trim($namaMateri, '.');
                    $nextUsername = 'juri.' . $namaMateri . '.1';
                }
            } else {
                $lastUsername = $lastJuri['usernameJuri'];

                // Extract nomor terakhir dari username yang sudah ada
                // Contoh: juri.praktek.6 -> extract "6", lalu buat "juri.praktek.7"
                if (preg_match('/^(.+?)\.(\d+)$/', $lastUsername, $matches)) {
                    $baseUsername = $matches[1]; // juri.praktek
                    $lastNumber = intval($matches[2]); // 6
                    $nextNumber = $lastNumber + 1; // 7
                    $nextUsername = $baseUsername . '.' . $nextNumber; // juri.praktek.7
                } else {
                    // Jika format tidak sesuai, tambahkan .1
                    $nextUsername = $lastUsername . '.1';
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'username' => $nextUsername
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in generateNextUsernameJuri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store juri sertifikasi baru
     */
    public function storeJuriSertifikasi()
    {
        try {
            $idJuri = $this->request->getPost('IdJuri');
            $idGroupMateri = $this->request->getPost('IdGroupMateri');
            $usernameJuri = $this->request->getPost('usernameJuri');
            $fullname = $this->request->getPost('fullname');
            $idAuthGroup = $this->request->getPost('IdAuthGroup');
            $createUser = $this->request->getPost('createUser'); // Checkbox untuk create user

            // Validasi
            if (empty($idJuri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Juri harus diisi'
                ]);
            }

            if (empty($idGroupMateri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Group Materi harus dipilih'
                ]);
            }

            if (empty($usernameJuri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Username Juri harus diisi'
                ]);
            }

            // Jika create user dicentang, validasi field user
            if ($createUser === 'true' || $createUser === true) {
                if (empty($fullname)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Nama Lengkap harus diisi jika ingin membuat user'
                    ]);
                }

                if (empty($idAuthGroup)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Group User harus dipilih jika ingin membuat user'
                    ]);
                }

                // Cek apakah username sudah ada di tabel users
                $existingUser = $this->userModel->where('username', $usernameJuri)->first();
                if ($existingUser) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Username sudah digunakan di sistem user'
                    ]);
                }
            }

            // Cek apakah IdJuri sudah ada
            $existingJuri = $this->sertifikasiJuriModel->getJuriByIdJuri($idJuri);
            if ($existingJuri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Juri sudah digunakan'
                ]);
            }

            // Cek apakah usernameJuri sudah ada di tabel juri
            $existingUsername = $this->sertifikasiJuriModel->getJuriByUsernameJuri($usernameJuri);
            if ($existingUsername) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Username Juri sudah digunakan'
                ]);
            }

            // Simpan data juri
            $this->sertifikasiJuriModel->insert([
                'IdJuri' => $idJuri,
                'IdGroupMateri' => $idGroupMateri,
                'usernameJuri' => $usernameJuri,
            ]);

            // Jika checkbox create user dicentang, buat user baru
            $userId = null;
            if ($createUser === 'true' || $createUser === true) {
                // Buat user dengan password default: TpqSmart123
                $defaultPassword = 'TpqSmart123';
                $passwordHash = Password::hash($defaultPassword);

                $userData = [
                    'username' => $usernameJuri,
                    'fullname' => $fullname,
                    'email' => $usernameJuri . '@tpqsmart.simpedis.com',
                    'password_hash' => $passwordHash,
                    'active' => 1
                ];

                $userId = $this->userModel->store($userData);

                // Tambahkan user ke group
                $groupData = [
                    'group_id' => (int)$idAuthGroup,
                    'user_id' => $userId
                ];

                $this->helpFunction->insertAuthGroupsUsers($groupData);
            }

            $message = 'Juri Sertifikasi berhasil ditambahkan';
            if ($createUser === 'true' || $createUser === true) {
                $message .= ' dan user berhasil dibuat dengan password default (TpqSmart123)';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in storeJuriSertifikasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman edit juri sertifikasi
     */
    public function editJuriSertifikasi($id)
    {
        // Ambil data juri
        $juri = $this->sertifikasiJuriModel->find($id);

        if (empty($juri)) {
            return redirect()->to(base_url('backend/sertifikasi/listJuriSertifikasi'))->with('error', 'Data juri tidak ditemukan');
        }

        // Ambil semua group materi untuk dropdown
        $groupMateriList = $this->sertifikasiGroupMateriModel->getAllGroupMateri();

        $data = [
            'page_title' => 'Edit Juri Sertifikasi',
            'juri' => $juri,
            'group_materi_list' => $groupMateriList,
        ];

        return view('backend/sertifikasi/editJuriSertifikasi', $data);
    }

    /**
     * Update juri sertifikasi
     */
    public function updateJuriSertifikasi($id)
    {
        try {
            $idJuri = $this->request->getPost('IdJuri');
            $idGroupMateri = $this->request->getPost('IdGroupMateri');
            $usernameJuri = $this->request->getPost('usernameJuri');
            $resetPassword = $this->request->getPost('resetPassword'); // Checkbox untuk reset password

            // Validasi
            if (empty($idJuri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Juri harus diisi'
                ]);
            }

            if (empty($idGroupMateri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Group Materi harus dipilih'
                ]);
            }

            if (empty($usernameJuri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Username Juri harus diisi'
                ]);
            }

            // Cek apakah data juri ada
            $juri = $this->sertifikasiJuriModel->find($id);
            if (empty($juri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data juri tidak ditemukan'
                ]);
            }

            // Cek apakah IdJuri sudah digunakan oleh juri lain
            $existingJuri = $this->sertifikasiJuriModel->getJuriByIdJuri($idJuri);
            if ($existingJuri && $existingJuri->id != $id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Juri sudah digunakan oleh juri lain'
                ]);
            }

            // Cek apakah usernameJuri sudah digunakan oleh juri lain
            $existingUsername = $this->sertifikasiJuriModel->getJuriByUsernameJuri($usernameJuri);
            if ($existingUsername && $existingUsername->id != $id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Username Juri sudah digunakan oleh juri lain'
                ]);
            }

            // Update data juri
            $this->sertifikasiJuriModel->update($id, [
                'IdJuri' => $idJuri,
                'IdGroupMateri' => $idGroupMateri,
                'usernameJuri' => $usernameJuri,
            ]);

            // Jika checkbox reset password dicentang, reset password user
            if ($resetPassword === 'true' || $resetPassword === true) {
                // Cari user berdasarkan usernameJuri
                $user = $this->userModel->where('username', $usernameJuri)->first();

                if ($user) {
                    // Reset password ke default: TpqSmart123
                    $defaultPassword = 'TpqSmart123';
                    $passwordHash = Password::hash($defaultPassword);

                    $this->userModel->update($user['id'], [
                        'password_hash' => $passwordHash
                    ]);
                } else {
                    // Jika user tidak ditemukan, beri peringatan tapi tetap lanjutkan update juri
                    log_message('warning', 'User dengan username ' . $usernameJuri . ' tidak ditemukan untuk reset password');
                }
            }

            $message = 'Juri Sertifikasi berhasil diupdate';
            if ($resetPassword === 'true' || $resetPassword === true) {
                $message .= ' dan password berhasil direset ke default (TpqSmart123)';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateJuriSertifikasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete juri sertifikasi
     */
    public function deleteJuriSertifikasi($id)
    {
        try {
            // Cek apakah data juri ada
            $juri = $this->sertifikasiJuriModel->find($id);
            if (empty($juri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data juri tidak ditemukan'
                ]);
            }

            // Cek apakah juri sudah memiliki nilai
            $nilaiCount = $this->sertifikasiNilaiModel->where('IdJuri', $juri['IdJuri'])->countAllResults();
            if ($nilaiCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Juri tidak dapat dihapus karena sudah memiliki ' . $nilaiCount . ' data nilai'
                ]);
            }

            // Start database transaction
            $this->db->transStart();

            // Cek apakah ada user dengan username yang sama dengan usernameJuri
            $user = $this->userModel->where('username', $juri['usernameJuri'])->first();

            if ($user) {
                // Hapus dari auth_groups_users
                $this->db->table('auth_groups_users')
                    ->where('user_id', $user['id'])
                    ->delete();

                // Hapus dari users
                $this->userModel->delete($user['id']);
            }

            // Hapus dari tbl_sertifikasi_juri
            $this->sertifikasiJuriModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            $message = 'Juri Sertifikasi berhasil dihapus';
            if ($user) {
                $message .= ' beserta user terkait';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in deleteJuriSertifikasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}

