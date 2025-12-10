<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriBaruModel;
use App\Models\MunaqosahNilaiModel;
use App\Models\MunaqosahKonfigurasiModel;
use App\Models\MunaqosahBobotNilaiModel;

class Nilai extends BaseController
{
    protected $DataNilai;
    protected $helpFunction;
    protected $DataSantriBaru;
    protected $IdTpq;
    protected $IdKelas;
    protected $IdTahunAjaran;
    protected $settingNilaiModel;
    protected $munaqosahNilaiModel;
    protected $munaqosahKonfigurasiModel;
    protected $munaqosahBobotNilaiModel;
    protected $db;

    public function __construct()
    {
        $this->IdTpq = session()->get('IdTpq');
        $this->IdKelas = session()->get('IdKelas');
        $this->IdTahunAjaran = session()->get('IdTahunAjaran');
        $this->DataNilai = new NilaiModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->DataSantriBaru = new SantriBaruModel();
        $this->munaqosahNilaiModel = new MunaqosahNilaiModel();
        $this->munaqosahKonfigurasiModel = new MunaqosahKonfigurasiModel();
        $this->munaqosahBobotNilaiModel = new MunaqosahBobotNilaiModel();
        $this->db = \Config\Database::connect();
    }

    public function showDetail($IdSantri, $IdSemseter)
    {
        log_message('info', '=== showDetail: START ===');
        log_message('info', 'showDetail Parameters - IdSantri: ' . json_encode($IdSantri) . ', IdSemseter: ' . json_encode($IdSemseter));

        // ambil settingan nilai minimun dan maksimal dari session
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Ambil IdKelas dan IdTpq dari data santri yang sedang dilihat, bukan dari session
        // Ini penting untuk pengecekan permission yang akurat
        $IdKelas = null;
        $IdTpq = null;

        // Coba ambil dari tbl_kelas_santri berdasarkan IdSantri dan IdTahunAjaran
        $db = db_connect();
        $kelasSantri = $db->table('tbl_kelas_santri ks')
            ->select('ks.IdKelas, ks.IdTpq')
            ->where('ks.IdSantri', $IdSantri)
            ->where('ks.IdTahunAjaran', $IdTahunAjaran)
            ->where('ks.Status', 1)
            ->orderBy('ks.IdKelas', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!empty($kelasSantri)) {
            $IdKelas = $kelasSantri['IdKelas'];
            $IdTpq = $kelasSantri['IdTpq'];
        } else {
            // Fallback: ambil dari session jika tidak ditemukan di tbl_kelas_santri
            $IdKelas = session()->get('IdKelas');
            $IdTpq = $this->IdTpq;
        }

        log_message('info', 'showDetail Data - IdTahunAjaran: ' . json_encode($IdTahunAjaran) . ', IdKelas (dari santri): ' . json_encode($IdKelas) . ', IdTpq (dari santri): ' . json_encode($IdTpq));

        $settingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $nilaiAlphabetEnabled = session()->get('SettingNilaiAlphabet') ?? false;
        log_message('info', "showDetail - NilaiAlphabetEnabled: " . ($nilaiAlphabetEnabled ? 'true' : 'false'));

        if ($nilaiAlphabetEnabled) {
            // Jika alphabet enabled, ambil detail settings
            // Gunakan IdTpq dari data santri untuk konsistensi
            $idTpqForSettings = !empty($IdTpq) ? $IdTpq : $this->IdTpq;
            log_message('info', "showDetail Query 1: getNilaiAlphabetSettings START - IdTpq: " . json_encode($idTpqForSettings));
            $queryStartTime = microtime(true);

            $settingNilai->NilaiAlphabet = $this->helpFunction->getNilaiAlphabetSettings($idTpqForSettings);

            $queryEndTime = microtime(true);
            $queryExecutionTime = ($queryEndTime - $queryStartTime) * 1000; // Convert to milliseconds
            log_message('info', "showDetail Query 1: getNilaiAlphabetSettings END - Execution Time: {$queryExecutionTime}ms");
            log_message('info', "showDetail Query 1: getNilaiAlphabetSettings Result - " . (is_null($settingNilai->NilaiAlphabet) ? 'NULL' : 'Object'));
        } else {
            $settingNilai->NilaiAlphabet = false;
        }

        try {
            // Gunakan method yang dioptimasi dengan caching
            log_message('info', 'showDetail Query 2: getDataNilaiDetailOptimized START - IdSantri: ' . json_encode($IdSantri) . ', IdSemseter: ' . json_encode($IdSemseter) . ', IdTahunAjaran: ' . json_encode($IdTahunAjaran) . ', IdKelas: ' . json_encode($IdKelas) . ', IdTpq: ' . json_encode($IdTpq));
            $queryStartTime = microtime(true);

            // TAMBAHKAN IdTpq sebagai parameter untuk menghindari double materi saat ada kelas yang sama di tahun yang sama dengan IdTpq berbeda
            $datanilai = $this->DataNilai->getDataNilaiDetailOptimized($IdSantri, $IdSemseter, $IdTahunAjaran, $IdKelas, $IdTpq);

            $queryEndTime = microtime(true);
            $queryExecutionTime = ($queryEndTime - $queryStartTime) * 1000; // Convert to milliseconds
            $resultCount = is_array($datanilai) ? count($datanilai) : (is_object($datanilai) ? count((array)$datanilai) : 0);
            log_message('info', "showDetail Query 2: getDataNilaiDetailOptimized END - Execution Time: {$queryExecutionTime}ms, Result Count: {$resultCount}");
        } catch (\Exception $e) {
            // Log error dan fallback ke method lama
            log_message('error', 'Error in showDetail optimized method: ' . $e->getMessage());
            log_message('info', 'showDetail Query 2 (FALLBACK): GetDataNilaiDetail START - IdSantri: ' . json_encode($IdSantri) . ', IdSemseter: ' . json_encode($IdSemseter) . ', IdTahunAjaran: ' . json_encode($IdTahunAjaran) . ', IdKelas: ' . json_encode($IdKelas) . ', IdTpq: ' . json_encode($IdTpq));
            $queryStartTime = microtime(true);

            // TAMBAHKAN IdTpq sebagai parameter untuk menghindari double materi saat ada kelas yang sama di tahun yang sama dengan IdTpq berbeda
            $datanilai = $this->DataNilai->GetDataNilaiDetail($IdSantri, $IdSemseter, $IdTahunAjaran, $IdKelas, $IdTpq);

            $queryEndTime = microtime(true);
            $queryExecutionTime = ($queryEndTime - $queryStartTime) * 1000; // Convert to milliseconds
            $resultCount = is_object($datanilai) ? $datanilai->getNumRows() : 0;
            log_message('info', "showDetail Query 2 (FALLBACK): GetDataNilaiDetail END - Execution Time: {$queryExecutionTime}ms, Result Count: {$resultCount}");
        }

        // LOGIKA PERMISSION BERDASARKAN USER GRUP DAN IdJabatan
        // Cek active_role dari session untuk menentukan peran yang sedang digunakan
        // Ini penting untuk user yang memiliki multiple roles (Guru, Operator, Kepala TPQ)
        $activeRole = session()->get('active_role');

        // Cek apakah user memiliki group Admin atau Operator (untuk pengecekan dasar)
        $hasAdminGroup = in_groups('Admin');
        $hasOperatorGroup = in_groups('Operator');

        // Tentukan apakah user sedang menggunakan peran Admin/Operator atau peran Guru
        // Jika active_role adalah 'guru' atau 'wali_kelas', maka user sedang menggunakan peran Guru
        // Jika active_role adalah 'operator' atau 'kepala_tpq' atau 'admin', maka user sedang menggunakan peran tersebut
        $isUsingGuruRole = in_array($activeRole, ['guru', 'wali_kelas']);
        $isUsingAdminRole = ($activeRole === 'admin' && $hasAdminGroup);
        $isUsingOperatorRole = ($activeRole === 'operator' && $hasOperatorGroup);
        $isUsingKepalaTpqRole = ($activeRole === 'kepala_tpq');

        // Jika active_role tidak ada atau tidak valid, cek berdasarkan group
        // Default: jika user memiliki group Guru dan tidak memilih peran lain, anggap sebagai Guru
        if (empty($activeRole) || (!in_array($activeRole, ['admin', 'operator', 'kepala_tpq', 'guru', 'wali_kelas']))) {
            if (in_groups('Guru')) {
                $isUsingGuruRole = true;
            } elseif ($hasAdminGroup) {
                $isUsingAdminRole = true;
            } elseif ($hasOperatorGroup) {
                $isUsingOperatorRole = true;
            }
        }

        // Tentukan isAdmin dan isOperator berdasarkan peran yang sedang digunakan
        // Jika user menggunakan peran Guru, maka bukan Admin/Operator meskipun memiliki group tersebut
        $isAdmin = $isUsingAdminRole;
        $isOperator = $isUsingOperatorRole || $isUsingKepalaTpqRole;

        // Ambil IdGuru dari session
        $IdGuru = session()->get('IdGuru');

        // Cek apakah guru adalah Wali Kelas untuk kelas yang sedang dilihat
        $isWaliKelasUntukKelasIni = false;
        $isGuruPendampingUntukKelasIni = false;

        // Hanya cek permission sebagai Guru jika user sedang menggunakan peran Guru
        if ($isUsingGuruRole && !empty($IdGuru) && !empty($IdKelas) && !empty($IdTpq) && !empty($IdTahunAjaran)) {
            // Cek apakah guru adalah Wali Kelas untuk kelas ini
            $guruKelasData = $this->helpFunction->getDataGuruKelas(
                IdGuru: $IdGuru,
                IdTpq: $IdTpq,
                IdKelas: $IdKelas,
                IdTahunAjaran: $IdTahunAjaran,
                IdJabatan: 3 // Wali Kelas
            );

            if (!empty($guruKelasData)) {
                $isWaliKelasUntukKelasIni = true;
            } else {
                // Jika bukan Wali Kelas, cek apakah dia Guru Pendamping untuk kelas ini
                // Cek semua data guru kelas untuk kelas ini (tanpa filter IdJabatan)
                $guruPendampingData = $this->helpFunction->getDataGuruKelas(
                    IdGuru: $IdGuru,
                    IdTpq: $IdTpq,
                    IdKelas: $IdKelas,
                    IdTahunAjaran: $IdTahunAjaran
                );

                // Jika ada data dan IdJabatan bukan 3 (Wali Kelas), berarti dia pendamping
                if (!empty($guruPendampingData)) {
                    foreach ($guruPendampingData as $gk) {
                        $gkArray = is_object($gk) ? (array)$gk : $gk;
                        $idJabatanGk = $gkArray['IdJabatan'] ?? null;
                        // Jika IdJabatan bukan 3 (Wali Kelas) dan tidak null, berarti dia pendamping
                        if ($idJabatanGk != 3 && $idJabatanGk != null) {
                            $isGuruPendampingUntukKelasIni = true;
                            break;
                        }
                    }
                }
            }
        }

        // Tentukan permission:
        // - Wali Kelas untuk kelas ini: bisa edit semua nilai (baik kosong maupun sudah ada)
        // - Guru Pendamping untuk kelas ini: hanya bisa edit jika nilai kosong, view jika nilai sudah ada
        // - Admin dan Operator: tidak bisa edit sama sekali (hanya view atau tidak akses)
        $canEditAll = $isWaliKelasUntukKelasIni; // Hanya Wali Kelas untuk kelas ini yang bisa edit semua
        $isGuruPendamping = $isGuruPendampingUntukKelasIni && !$isAdmin && !$isOperator; // Guru Pendamping untuk kelas ini (bukan Admin, bukan Operator)

        log_message('info', '=== showDetail: END ===');

        $data = [
            'page_title' => 'Data Nilai',
            'nilai' => $datanilai,
            'canEditAll' => $canEditAll, // Hanya Wali Kelas bisa edit semua
            'isGuruPendamping' => $isGuruPendamping, // Guru Pendamping bisa edit jika nilai kosong
            'isAdmin' => $isAdmin,
            'isOperator' => $isOperator,
            'settingNilai' => $settingNilai,
        ];

        return view('/backend/nilai/nilaiSantriDetail', $data);
    }

    public function showSantriPerKelas($semester = null)
    {
        $IdGuru = session()->get('IdGuru');
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $dataSantri = $this->DataSantriBaru->GetDataSantriPerKelas($IdTahunAjaran, $IdKelas, $IdGuru);

        // ambil settingan nilai minimun dan maksimal dari session
        $settingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $nilaiAlphabetEnabled = session()->get('SettingNilaiAlphabet') ?? false;

        if ($nilaiAlphabetEnabled) {
            // Jika alphabet enabled, ambil detail settings
            $settingNilai->NilaiAlphabet = $this->helpFunction->getNilaiAlphabetSettings($this->IdTpq);
        } else {
            $settingNilai->NilaiAlphabet = false;
        }

        // Optimasi pengecekan nilai dengan single query
        $allNilai = $this->DataNilai->getAllNilaiPerKelas($IdTahunAjaran, $semester, $this->IdTpq, $IdKelas);

        // Set status penilaian sementara (akan diupdate setelah progressData dihitung)
        $nilaiStatus = [];
        foreach ($allNilai as $nilai) {
            if ($nilai->Nilai == 0) {
                $nilaiStatus[$nilai->IdSantri] = 0;
            } else if (!isset($nilaiStatus[$nilai->IdSantri])) {
                $nilaiStatus[$nilai->IdSantri] = 1;
            }
        }

        // Set status penilaian sementara untuk setiap santri
        foreach ($dataSantri as $key => $value) {
            $dataSantri[$key]->StatusPenilaian = $nilaiStatus[$value->IdSantri] ?? 0;
        }

        // Tambahkan data kelas tetap "SEMUA KELAS" di awal
        $dataKelas = [0 => 'SEMUA'];
        foreach ($dataSantri as $santri) {
            $dataKelas[$santri->IdKelas] = $santri->NamaKelas;
        }

        // OPTIMASI: Ambil data materi per kelas dalam batch (mendukung array)
        $dataMateri = [];
        $allKelasIds = array_filter(array_keys($dataKelas), function($id) { return $id != 0; });
        if (!empty($allKelasIds)) {
            // Ambil semua materi untuk semua kelas dalam 1 query
            $allMateri = $this->helpFunction->getMateriPelajaranByKelas($this->IdTpq, $allKelasIds, $semester);
            // Group by kelas
            foreach ($allMateri as $materi) {
                if (!isset($dataMateri[$materi->IdKelas])) {
                    $dataMateri[$materi->IdKelas] = [];
                }
                $dataMateri[$materi->IdKelas][] = $materi;
            }
        }

        // OPTIMASI: Ambil semua nilai detail dalam 1 query (bukan N+1 query)
        $allNilaiDetail = $this->DataNilai->getAllNilaiDetailPerKelas(
            $this->IdTpq, 
            $IdTahunAjaran, 
            $IdKelas, 
            $semester
        );

        // Group nilai per santri dan materi untuk akses cepat
        $dataNilaiDetail = [];
        foreach ($allNilaiDetail as $nilai) {
            if (!isset($dataNilaiDetail[$nilai->IdSantri])) {
                $dataNilaiDetail[$nilai->IdSantri] = [];
            }
            $dataNilaiDetail[$nilai->IdSantri][$nilai->IdMateri] = $nilai;
        }

        // OPTIMASI: Hitung progress di controller (sekali), bukan di view (berulang)
        $progressData = [];
        foreach ($dataSantri as $santri) {
            $kelasIdForProgress = $santri->IdKelas;
            $totalMateri = 0;
            $materiTerisi = 0;
            
            if (isset($dataMateri[$kelasIdForProgress]) && isset($dataNilaiDetail[$santri->IdSantri])) {
                $materiKelas = $dataMateri[$kelasIdForProgress];
                $nilaiSantri = $dataNilaiDetail[$santri->IdSantri];
                
                foreach ($materiKelas as $materi) {
                    $totalMateri++;
                    $nilaiMateri = isset($nilaiSantri[$materi->IdMateri]) ? (int)$nilaiSantri[$materi->IdMateri]->Nilai : 0;
                    // Nilai dianggap terisi jika > 0
                    if ($nilaiMateri > 0) {
                        $materiTerisi++;
                    }
                }
            }
            
            // Hitung persentase
            $persentase = $totalMateri > 0 ? round(($materiTerisi / $totalMateri) * 100, 1) : 0;
            
            // Tentukan warna badge berdasarkan persentase
            $badgeColor = 'secondary';
            if ($persentase >= 100) {
                $badgeColor = 'success';
            } elseif ($persentase >= 75) {
                $badgeColor = 'info';
            } elseif ($persentase >= 50) {
                $badgeColor = 'warning';
            } elseif ($persentase > 0) {
                $badgeColor = 'danger';
            }
            
            $progressData[$santri->IdSantri] = [
                'totalMateri' => $totalMateri,
                'materiTerisi' => $materiTerisi,
                'persentase' => $persentase,
                'badgeColor' => $badgeColor
            ];
        }

        // Update StatusPenilaian berdasarkan progress - hanya 1 jika semua materi sudah terisi (100%)
        foreach ($dataSantri as $key => $santri) {
            if (isset($progressData[$santri->IdSantri])) {
                $persentase = $progressData[$santri->IdSantri]['persentase'];
                // StatusPenilaian = 1 hanya jika persentase = 100% (semua materi sudah terisi)
                $dataSantri[$key]->StatusPenilaian = ($persentase >= 100) ? 1 : 0;
            } else {
                // Jika tidak ada progress data, tetap gunakan status sebelumnya
                $dataSantri[$key]->StatusPenilaian = $dataSantri[$key]->StatusPenilaian ?? 0;
            }
        }

        // Ambil jabatan user yang login untuk menentukan permission
        $userJabatanList = session()->get('IdJabatan') ?? [];
        $isWaliKelas = false;
        if (is_array($userJabatanList)) {
            // IdJabatan = 3 adalah Wali Kelas
            $isWaliKelas = in_array(3, $userJabatanList);
        } else {
            $isWaliKelas = ($userJabatanList == 3);
        }

        $data = [
            'page_title' => 'Data Santri Per Semester ' . $semester,
            'dataSantri' => $dataSantri,
            'dataKelas' => $dataKelas,
            'dataMateri' => $dataMateri,
            'dataNilaiDetail' => $dataNilaiDetail,
            'progressData' => $progressData,
            'semester' => $semester,
            'settingNilai' => $settingNilai,
            'isWaliKelas' => $isWaliKelas, // Flag untuk menentukan apakah user login sebagai Wali Kelas
        ];

        return view('backend/santri/santriPerKelas', $data);
    }

    public function showNilaiProfilDetail($IdSantri = null)
    {
        // Jika user adalah Santri, ambil IdSantri dari user yang login
        $isSantri = in_groups('Santri');
        if ($isSantri && empty($IdSantri)) {
            $userNik = user()->nik ?? null;
            if (!empty($userNik)) {
                $santriData = $this->DataSantriBaru->getSantriByNik($userNik);
                if (!empty($santriData)) {
                    $IdSantri = $santriData['IdSantri'];
                }
            }
        }

        if (empty($IdSantri)) {
            return redirect()->to(base_url())->with('error', 'Data santri tidak ditemukan');
        }

        // Ambil data santri lengkap
        $santriDetail = $this->DataSantriBaru->getProfilDetailSantri($IdSantri);
        if (empty($santriDetail)) {
            return redirect()->to(base_url())->with('error', 'Data santri tidak ditemukan');
        }

        // Ambil IdTpq default
        $IdTpq = $santriDetail['IdTpq'] ?? $this->IdTpq;

        $db = db_connect();

        // Ambil semua kombinasi tahun ajaran, kelas, dan TPQ yang memiliki nilai dari tbl_nilai
        // Ini akan mengambil semua tahun ajaran yang memiliki data nilai, tidak hanya yang ada di tbl_kelas_santri
        $allNilaiKombinasi = $db->table('tbl_nilai n')
            ->select('n.IdTahunAjaran, n.IdKelas, n.IdTpq, k.NamaKelas')
            ->distinct()
            ->join('tbl_kelas k', 'k.IdKelas = n.IdKelas', 'inner')
            ->where('n.IdSantri', $IdSantri)
            ->orderBy('n.IdTahunAjaran', 'DESC')
            ->orderBy('k.NamaKelas', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil semua kelas santri dari berbagai tahun ajaran dari tbl_kelas_santri
        $allKelasSantri = $db->table('tbl_kelas_santri ks')
            ->select('ks.IdKelas, ks.IdTahunAjaran, ks.IdTpq, k.NamaKelas')
            ->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'inner')
            ->where('ks.IdSantri', $IdSantri)
            ->where('ks.Status', 1)
            ->orderBy('ks.IdTahunAjaran', 'DESC')
            ->orderBy('k.NamaKelas', 'ASC')
            ->get()
            ->getResultArray();

        // Gabungkan data dari tbl_nilai dan tbl_kelas_santri
        // Prioritas: jika ada di tbl_kelas_santri, gunakan data dari sana, jika tidak gunakan dari tbl_nilai
        $combinedKelas = [];

        // Tambahkan dari tbl_kelas_santri dulu (prioritas)
        foreach ($allKelasSantri as $kelas) {
            // Pastikan IdTpq tidak null
            $kelasIdTpq = !empty($kelas['IdTpq']) ? $kelas['IdTpq'] : $IdTpq;
            $key = $kelas['IdKelas'] . '_' . $kelas['IdTahunAjaran'] . '_' . $kelasIdTpq;
            if (!isset($combinedKelas[$key])) {
                $combinedKelas[$key] = $kelas;
                // Pastikan IdTpq di-set jika null
                if (empty($combinedKelas[$key]['IdTpq'])) {
                    $combinedKelas[$key]['IdTpq'] = $kelasIdTpq;
                }
            }
        }

        // Tambahkan dari tbl_nilai jika belum ada
        foreach ($allNilaiKombinasi as $nilai) {
            // Pastikan IdTpq tidak null
            $nilaiIdTpq = !empty($nilai['IdTpq']) ? $nilai['IdTpq'] : $IdTpq;
            $key = $nilai['IdKelas'] . '_' . $nilai['IdTahunAjaran'] . '_' . $nilaiIdTpq;
            if (!isset($combinedKelas[$key])) {
                $combinedKelas[$key] = [
                    'IdKelas' => $nilai['IdKelas'],
                    'IdTahunAjaran' => $nilai['IdTahunAjaran'],
                    'IdTpq' => $nilaiIdTpq,
                    'NamaKelas' => $nilai['NamaKelas']
                ];
            }
        }

        // Konversi ke array indexed
        $allKelasSantri = array_values($combinedKelas);

        // Jika masih tidak ada data, coba ambil dari tbl_santri_baru sebagai fallback
        if (empty($allKelasSantri)) {
            $IdKelas = $santriDetail['IdKelas'] ?? $this->IdKelas;
            $IdTahunAjaran = $this->IdTahunAjaran;
            if (!empty($IdKelas) && !empty($IdTahunAjaran)) {
                $kelasInfo = $db->table('tbl_kelas')
                    ->select('IdKelas, NamaKelas')
                    ->where('IdKelas', $IdKelas)
                    ->get()
                    ->getRowArray();

                if (!empty($kelasInfo)) {
                    $allKelasSantri = [[
                        'IdKelas' => $IdKelas,
                        'IdTahunAjaran' => $IdTahunAjaran,
                        'IdTpq' => $IdTpq,
                        'NamaKelas' => $kelasInfo['NamaKelas']
                    ]];
                }
            }
        }

        // Struktur data untuk setiap kelas dengan nilai Ganjil dan Genap
        $kelasData = [];
        foreach ($allKelasSantri as $kelas) {
            $IdKelas = $kelas['IdKelas'];
            $IdTahunAjaran = $kelas['IdTahunAjaran'];
            $IdTpqKelas = $kelas['IdTpq'] ?? $IdTpq;

            // Ambil data nilai untuk semester Ganjil
            $datanilaiGanjil = [];
            try {
                $datanilaiGanjil = $this->DataNilai->getDataNilaiDetailOptimized($IdSantri, 'Ganjil', $IdTahunAjaran, $IdKelas, $IdTpqKelas);
            } catch (\Exception $e) {
                log_message('error', 'Error in showNilaiProfilDetail Ganjil: ' . $e->getMessage());
                try {
                    $datanilaiGanjil = $this->DataNilai->GetDataNilaiDetail($IdSantri, 'Ganjil', $IdTahunAjaran, $IdKelas, $IdTpqKelas);
                } catch (\Exception $e2) {
                    log_message('error', 'Error in showNilaiProfilDetail Ganjil fallback: ' . $e2->getMessage());
                }
            }

            // Ambil data nilai untuk semester Genap
            $datanilaiGenap = [];
            try {
                $datanilaiGenap = $this->DataNilai->getDataNilaiDetailOptimized($IdSantri, 'Genap', $IdTahunAjaran, $IdKelas, $IdTpqKelas);
            } catch (\Exception $e) {
                log_message('error', 'Error in showNilaiProfilDetail Genap: ' . $e->getMessage());
                try {
                    $datanilaiGenap = $this->DataNilai->GetDataNilaiDetail($IdSantri, 'Genap', $IdTahunAjaran, $IdKelas, $IdTpqKelas);
                } catch (\Exception $e2) {
                    log_message('error', 'Error in showNilaiProfilDetail Genap fallback: ' . $e2->getMessage());
                }
            }

            // Bandingkan nilai dengan semester sebelumnya dan tambahkan status
            $datanilaiGanjil = $this->compareNilaiWithPreviousSemester($datanilaiGanjil, $IdSantri, 'Ganjil', $IdTahunAjaran, $IdKelas, $IdTpqKelas);
            $datanilaiGenap = $this->compareNilaiWithPreviousSemester($datanilaiGenap, $IdSantri, 'Genap', $IdTahunAjaran, $IdKelas, $IdTpqKelas);

            // Ambil wali kelas
            $waliKelas = null;
            if (!empty($IdKelas) && !empty($IdTpqKelas) && !empty($IdTahunAjaran)) {
                $waliKelas = $this->helpFunction->getWaliKelasByIdKelas($IdKelas, $IdTpqKelas, $IdTahunAjaran);
            }

            // Format tahun ajaran untuk display
            $tahunAjaranStr = (string)$IdTahunAjaran;
            $tahunAjaranDisplay = $tahunAjaranStr;
            if (strlen($tahunAjaranStr) == 8) {
                $tahunAjaranDisplay = substr($tahunAjaranStr, 0, 4) . '/' . substr($tahunAjaranStr, 4, 4);
            }

            // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
            $namaKelasOriginal = $kelas['NamaKelas'];
            $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpqKelas, $namaKelasOriginal);
            $namaKelasDisplay = $this->helpFunction->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );

            // Hitung statistik untuk semester Ganjil
            $statistikGanjil = $this->calculateNilaiStatistics($datanilaiGanjil);

            // Hitung statistik untuk semester Genap
            $statistikGenap = $this->calculateNilaiStatistics($datanilaiGenap);

            // Tentukan semester saat ini berdasarkan bulan
            // Semester Ganjil: Juli-Desember (bulan 7-12)
            // Semester Genap: Januari-Juni (bulan 1-6)
            $currentMonth = (int)date('m');
            $semesterSaatIni = ($currentMonth >= 7) ? 'Ganjil' : 'Genap';

            // Tentukan apakah tahun ajaran ini adalah tahun ajaran saat ini
            $isTahunAjaranSaatIni = ($IdTahunAjaran == $this->IdTahunAjaran);

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

            // Cek apakah setting munaqosah aktif untuk menampilkan nilai munaqosah
            // Cek setting dengan IdTpq spesifik terlebih dahulu, jika tidak ada cek dengan IdTpq = '0' (global)
            $munaqosahAktif = false;
            if (!empty($IdTpqKelas)) {
                // Cek setting untuk TPQ spesifik
                $munaqosahAktif = $this->munaqosahKonfigurasiModel->getSettingAsBool((string)$IdTpqKelas, 'AktiveTombolKelulusan', false);

                // Jika tidak ada setting untuk TPQ spesifik, cek setting global (IdTpq = '0')
                if (!$munaqosahAktif) {
                    $munaqosahAktif = $this->munaqosahKonfigurasiModel->getSettingAsBool('0', 'AktiveTombolKelulusan', false);
                }
            } else {
                // Jika IdTpq kosong, cek setting global
                $munaqosahAktif = $this->munaqosahKonfigurasiModel->getSettingAsBool('0', 'AktiveTombolKelulusan', false);
            }

            // Ambil data nilai munaqosah dan pra-munaqosah hanya jika setting aktif
            $nilaiMunaqosah = [];
            $nilaiPraMunaqosah = [];
            if ($munaqosahAktif) {
                $nilaiMunaqosah = $this->getNilaiMunaqosah($IdSantri, $IdTahunAjaran, $IdTpqKelas);
                $nilaiPraMunaqosah = $this->getNilaiPraMunaqosah($IdSantri, $IdTahunAjaran, $IdTpqKelas);
            }

            // Buat key unik untuk setiap kombinasi kelas dan tahun ajaran
            $key = $IdKelas . '_' . $IdTahunAjaran;
            $kelasData[$key] = [
                'IdKelas' => $IdKelas,
                'NamaKelas' => $namaKelasDisplay, // Nama kelas yang sudah dikonversi MDA jika perlu
                'IdTahunAjaran' => $IdTahunAjaran,
                'TahunAjaranDisplay' => $tahunAjaranDisplay,
                'IdTpq' => $IdTpqKelas,
                'nilaiGanjil' => $datanilaiGanjil,
                'nilaiGenap' => $datanilaiGenap,
                'statistikGanjil' => $statistikGanjil,
                'statistikGenap' => $statistikGenap,
                'nilaiMunaqosah' => $nilaiMunaqosah,
                'nilaiPraMunaqosah' => $nilaiPraMunaqosah,
                'munaqosahAktif' => $munaqosahAktif,
                'waliKelas' => $waliKelas ? ($waliKelas->Nama ?? '') : '',
                'hideGanjil' => $hideGanjil,
                'hideGenap' => $hideGenap,
            ];
        }

        // Ambil nama orang tua
        $namaOrangTua = '';
        if (!empty($santriDetail['NamaAyah'])) {
            $namaOrangTua = $santriDetail['NamaAyah'];
            if (!empty($santriDetail['NamaIbu'])) {
                $namaOrangTua .= ' / ' . $santriDetail['NamaIbu'];
            }
        } elseif (!empty($santriDetail['NamaIbu'])) {
            $namaOrangTua = $santriDetail['NamaIbu'];
        }

        // Ambil foto profil
        $photoUrl = base_url('images/no-photo.jpg');
        if (!empty($santriDetail['PhotoProfil'])) {
            $photoPath = FCPATH . 'uploads/santri/' . $santriDetail['PhotoProfil'];
            if (file_exists($photoPath)) {
                $photoUrl = base_url('uploads/santri/' . $santriDetail['PhotoProfil']);
            }
        }

        // Gabungkan semua data munaqosah dari semua kelas menjadi satu
        $allNilaiMunaqosah = [];
        $allNilaiPraMunaqosah = [];
        $munaqosahAktifGlobal = false;
        $tahunAjaranList = [];

        foreach ($kelasData as $kelas) {
            $munaqosahAktif = $kelas['munaqosahAktif'] ?? false;
            if ($munaqosahAktif) {
                $munaqosahAktifGlobal = true;
                $tahunAjaran = $kelas['IdTahunAjaran'] ?? '';
                if (!empty($tahunAjaran) && !in_array($tahunAjaran, $tahunAjaranList)) {
                    $tahunAjaranList[] = $tahunAjaran;
                }
                if (!empty($kelas['nilaiMunaqosah'])) {
                    foreach ($kelas['nilaiMunaqosah'] as $nilai) {
                        $nilai['NamaKelas'] = $kelas['NamaKelas'];
                        $nilai['TahunAjaranDisplay'] = $kelas['TahunAjaranDisplay'];
                        $allNilaiMunaqosah[] = $nilai;
                    }
                }
                if (!empty($kelas['nilaiPraMunaqosah'])) {
                    foreach ($kelas['nilaiPraMunaqosah'] as $nilai) {
                        $nilai['NamaKelas'] = $kelas['NamaKelas'];
                        $nilai['TahunAjaranDisplay'] = $kelas['TahunAjaranDisplay'];
                        $allNilaiPraMunaqosah[] = $nilai;
                    }
                }
            }
        }

        // Ambil data bobot untuk semua tahun ajaran yang ada
        $bobotMap = [];
        foreach ($tahunAjaranList as $tahunAjaran) {
            $bobotData = $this->munaqosahBobotNilaiModel->getBobotWithKategori($tahunAjaran);
            foreach ($bobotData as $row) {
                $catId = $row['IdKategoriMateri'] ?? '';
                if (!empty($catId)) {
                    // Gunakan tahun ajaran sebagai key untuk mapping
                    $key = $tahunAjaran . '_' . $catId;
                    $bobotMap[$key] = (float)($row['NilaiBobot'] ?? 0);
                }
            }

            // Jika tidak ada data untuk tahun ajaran spesifik, coba ambil dari default
            if (empty($bobotData)) {
                $defaultBobot = $this->munaqosahBobotNilaiModel->getBobotWithKategori('Default');
                foreach ($defaultBobot as $row) {
                    $catId = $row['IdKategoriMateri'] ?? '';
                    if (!empty($catId)) {
                        $key = $tahunAjaran . '_' . $catId;
                        if (!isset($bobotMap[$key])) {
                            $bobotMap[$key] = (float)($row['NilaiBobot'] ?? 0);
                        }
                    }
                }
            }
        }

        return view('backend/nilai/nilaiSantriDetailPersonal', [
            'page_title' => 'Detail Nilai',
            'kelasData' => $kelasData,
            'santri' => $santriDetail,
            'namaOrangTua' => $namaOrangTua,
            'photoUrl' => $photoUrl,
            'IdTahunAjaran' => $this->IdTahunAjaran,
            'allNilaiMunaqosah' => $allNilaiMunaqosah,
            'allNilaiPraMunaqosah' => $allNilaiPraMunaqosah,
            'munaqosahAktifGlobal' => $munaqosahAktifGlobal,
            'bobotMap' => $bobotMap,
        ]);
    }

    public function showDetailNilaiSantriPerKelas($semester = null)
    {
        // ambil IdTpq dari session
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        $IdTpq = $this->IdTpq;


        // Buat querry dari tbl_nilai dengan menggabungkan tbl_santri_baru dan tbl_kelas
        $datanilai = $this->DataNilai->getDataNilaiPerKelas($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        $dataKelas = [];
        foreach ($datanilai as $key => $nilai) {
            $namaKelasOriginal = $nilai['Nama Kelas'];

            // Check MDA mapping dan convert nama kelas jika sesuai
            $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
            $namaKelasDisplay = $this->helpFunction->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );

            // Simpan nama kelas yang sudah dikonversi untuk tab dan display
            if (!isset($dataKelas[$nilai['IdKelas']])) {
                $dataKelas[$nilai['IdKelas']] = $namaKelasDisplay;
            }

            // Update nama kelas di data nilai untuk ditampilkan di tabel
            $datanilai[$key]['Nama Kelas'] = $namaKelasDisplay;
        }

        $dataMateri = [];
        // Ambil data materi pelajaran berdasarkan kelas
        foreach ($dataKelas as $idKelas => $namaKelas) {
            $dataMateri[$idKelas] = $this->helpFunction->getMateriPelajaranByKelas($IdTpq, $idKelas, $semester);
        }

        // ambil settingan nilai minimun dan maksimal dari session
        $settingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $nilaiAlphabetEnabled = session()->get('SettingNilaiAlphabet') ?? false;

        if ($nilaiAlphabetEnabled) {
            // Jika alphabet enabled, ambil detail settings
            $settingNilai->NilaiAlphabet = $this->helpFunction->getNilaiAlphabetSettings($this->IdTpq);
        } else {
            $settingNilai->NilaiAlphabet = false;
        }

        // ambil jika nilai settingan angka arabic dari tbl_tools 
        $settingNilai->NilaiArabic = session()->get('SettingNilaiArabic') ?? false;

        $data = [
            'page_title' => 'Data Nilai Santri Per Kelas',
            'dataKelas' => $dataKelas,
            'dataNilai' => $datanilai,
            'dataMateri' => $dataMateri,
            'settingNilai' => $settingNilai
        ];

        return view('backend/nilai/nilaiSantriDetailPerKelas', $data);
    }

    public function update($Edit = false)
    {
        try {
            //Get IdGuru dari session login
            $IdGuru = session()->get('IdGuru');
            $Id = $this->request->getVar('Id');

            // check jika radio button ada nilai maka nilai di ambil dari radio button
            if ($this->request->getVar('NilaiRadio') !== null) {
                $Nilai = $this->request->getVar('NilaiRadio');
            } else {
                // Jika tidak ada nilai dari radio button, ambil dari inputan teks
                $Nilai = $this->request->getVar('Nilai');
            }

            $result = $this->DataNilai->save([
                'Id' => $Id,
                'IdGuru' => $IdGuru,
                'Nilai' => $Nilai,
            ]);

            if ($result) {
                // Clear cache setelah update nilai
                $this->DataNilai->clearNilaiCache();

                // Mengembalikan respons JSON
                return $this->response->setJSON(['status' => 'success', 'newValue' => $Nilai, 'message' => 'Data berhasil diperbarui']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data']);
            }
        } catch (\Exception $e) {
            // Mengembalikan respons JSON dengan kesalahan
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Membandingkan nilai dengan semester sebelumnya dan menambahkan status
     * 
     * @param array|object $currentNilai Data nilai semester saat ini
     * @param string $IdSantri ID Santri
     * @param string $currentSemester Semester saat ini (Ganjil/Genap)
     * @param string $IdTahunAjaran Tahun ajaran saat ini
     * @param string $IdKelas ID Kelas
     * @param string $IdTpq ID TPQ
     * @return array|object Data nilai dengan status perbandingan
     */
    private function compareNilaiWithPreviousSemester($currentNilai, $IdSantri, $currentSemester, $IdTahunAjaran, $IdKelas, $IdTpq)
    {
        // Konversi ke array jika object
        $nilaiArray = [];
        if (is_array($currentNilai)) {
            $nilaiArray = $currentNilai;
        } elseif (is_object($currentNilai) && method_exists($currentNilai, 'getResult')) {
            $nilaiArray = $currentNilai->getResult();
        } elseif (is_object($currentNilai)) {
            $nilaiArray = [$currentNilai];
        }

        if (empty($nilaiArray)) {
            return $currentNilai;
        }

        // Tentukan semester sebelumnya
        // Logika:
        // - Semester Genap (2025/2026) → bandingkan dengan Semester Ganjil (2025/2026) tahun ajaran yang sama
        // - Semester Ganjil (2025/2026) → bandingkan dengan Semester Genap (2024/2025) tahun ajaran sebelumnya
        $previousSemester = null;
        $previousTahunAjaran = null;

        if ($currentSemester == 'Genap') {
            // Semester sebelumnya adalah Ganjil dari tahun ajaran yang sama
            $previousSemester = 'Ganjil';
            $previousTahunAjaran = $IdTahunAjaran;
            log_message('debug', "Perbandingan nilai: Semester Genap ({$IdTahunAjaran}) dibandingkan dengan Semester Ganjil ({$previousTahunAjaran})");
        } else {
            // Semester sebelumnya adalah Genap dari tahun ajaran sebelumnya
            $previousSemester = 'Genap';
            // Gunakan fungsi dari HelpFunctionModel untuk mendapatkan tahun ajaran sebelumnya
            $previousTahunAjaran = $this->helpFunction->getTahunAjaranSebelumnyaDari($IdTahunAjaran);
            log_message('debug', "Perbandingan nilai: Semester Ganjil ({$IdTahunAjaran}) dibandingkan dengan Semester Genap ({$previousTahunAjaran})");
        }

        // Ambil nilai dari semester sebelumnya
        // Untuk semester Ganjil: cari di kelas sebelumnya di tahun ajaran sebelumnya
        // Untuk semester Genap: cari di kelas yang sama di tahun ajaran yang sama
        $previousNilai = [];

        $db = db_connect();

        // Tentukan kelas yang akan dicari
        $previousIdKelas = null;
        $searchInSpecificClass = false;

        if ($currentSemester == 'Ganjil') {
            // Untuk semester Ganjil, cari di kelas sebelumnya
            $previousIdKelas = $this->getPreviousKelas($IdKelas);
            $searchInSpecificClass = ($previousIdKelas !== null);
            log_message('debug', "Semester Ganjil: Mencari nilai di kelas sebelumnya - Kelas saat ini: {$IdKelas}, Kelas sebelumnya: " . ($previousIdKelas ?? 'null'));
        } else {
            // Untuk semester Genap, cari di kelas yang sama
            $previousIdKelas = $IdKelas;
            $searchInSpecificClass = true;
            log_message('debug', "Semester Genap: Mencari nilai di kelas yang sama - Kelas: {$IdKelas}");
        }

        // Cari kelas santri di tahun ajaran sebelumnya
        $previousKelasSantri = $db->table('tbl_kelas_santri')
            ->select('IdKelas, IdTpq')
            ->where('IdSantri', $IdSantri)
            ->where('IdTahunAjaran', $previousTahunAjaran);

        // Jika ada kelas sebelumnya yang ditentukan, filter berdasarkan kelas tersebut
        if ($searchInSpecificClass && $previousIdKelas !== null) {
            $previousKelasSantri->where('IdKelas', $previousIdKelas);
        }

        $previousKelasSantri = $previousKelasSantri->get()->getResultArray();

        log_message('debug', "Mencari nilai semester sebelumnya - IdSantri: {$IdSantri}, Semester: {$previousSemester}, TahunAjaran: {$previousTahunAjaran}, Kelas yang dicari: " . ($previousIdKelas ?? 'semua') . ", Jumlah kelas ditemukan: " . count($previousKelasSantri));

        // Jika ada kelas di tahun ajaran sebelumnya, cari nilai di kelas tersebut
        if (!empty($previousKelasSantri)) {
            foreach ($previousKelasSantri as $prevKelas) {
                $prevIdKelas = $prevKelas['IdKelas'];
                $prevIdTpq = $prevKelas['IdTpq'] ?? $IdTpq;

                try {
                    log_message('debug', "Mencari nilai di kelas: {$prevIdKelas}, TPQ: {$prevIdTpq}");
                    $previousNilaiResult = $this->DataNilai->getDataNilaiDetailOptimized($IdSantri, $previousSemester, $previousTahunAjaran, $prevIdKelas, $prevIdTpq);

                    $tempNilai = [];
                    if (is_array($previousNilaiResult)) {
                        $tempNilai = $previousNilaiResult;
                    } elseif (is_object($previousNilaiResult) && method_exists($previousNilaiResult, 'getResult')) {
                        $tempNilai = $previousNilaiResult->getResult();
                    } elseif (is_object($previousNilaiResult)) {
                        $tempNilai = [$previousNilaiResult];
                    }

                    // Gabungkan dengan nilai yang sudah ada (hindari duplikasi berdasarkan IdMateri)
                    foreach ($tempNilai as $temp) {
                        $idMateri = is_object($temp) ? $temp->IdMateri : ($temp['IdMateri'] ?? null);
                        if ($idMateri) {
                            // Cek apakah IdMateri sudah ada
                            $exists = false;
                            foreach ($previousNilai as $existing) {
                                $existingIdMateri = is_object($existing) ? $existing->IdMateri : ($existing['IdMateri'] ?? null);
                                if ($existingIdMateri == $idMateri) {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $previousNilai[] = $temp;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    log_message('debug', "Error mencari nilai di kelas {$prevIdKelas}: " . $e->getMessage());
                    // Fallback: coba dengan method lama
                    try {
                        $previousNilaiResult = $this->DataNilai->GetDataNilaiDetail($IdSantri, $previousSemester, $previousTahunAjaran, $prevIdKelas, $prevIdTpq);
                        if (is_object($previousNilaiResult) && method_exists($previousNilaiResult, 'getResult')) {
                            $tempNilai = $previousNilaiResult->getResult();
                            foreach ($tempNilai as $temp) {
                                $idMateri = is_object($temp) ? $temp->IdMateri : ($temp['IdMateri'] ?? null);
                                if ($idMateri) {
                                    $exists = false;
                                    foreach ($previousNilai as $existing) {
                                        $existingIdMateri = is_object($existing) ? $existing->IdMateri : ($existing['IdMateri'] ?? null);
                                        if ($existingIdMateri == $idMateri) {
                                            $exists = true;
                                            break;
                                        }
                                    }
                                    if (!$exists) {
                                        $previousNilai[] = $temp;
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e2) {
                        log_message('debug', "Error fallback mencari nilai di kelas {$prevIdKelas}: " . $e2->getMessage());
                    }
                }
            }
        } else {
            // Jika tidak ada kelas di tahun ajaran sebelumnya dengan filter kelas sebelumnya, 
            // coba cari di semua kelas di tahun ajaran sebelumnya sebagai fallback
            log_message('debug', "Tidak ada kelas di tahun ajaran sebelumnya dengan filter, mencoba mencari di semua kelas");
            $allPreviousKelas = $db->table('tbl_kelas_santri')
                ->select('IdKelas, IdTpq')
                ->where('IdSantri', $IdSantri)
                ->where('IdTahunAjaran', $previousTahunAjaran)
                ->where('Status', 1)
                ->get()
                ->getResultArray();

            log_message('debug', "Fallback: Jumlah semua kelas di tahun ajaran sebelumnya: " . count($allPreviousKelas));

            if (!empty($allPreviousKelas)) {
                foreach ($allPreviousKelas as $prevKelas) {
                    $prevIdKelas = $prevKelas['IdKelas'];
                    $prevIdTpq = $prevKelas['IdTpq'] ?? $IdTpq;

                    try {
                        log_message('debug', "Fallback: Mencari nilai di kelas: {$prevIdKelas}, TPQ: {$prevIdTpq}");
                        $previousNilaiResult = $this->DataNilai->getDataNilaiDetailOptimized($IdSantri, $previousSemester, $previousTahunAjaran, $prevIdKelas, $prevIdTpq);

                        $tempNilai = [];
                        if (is_array($previousNilaiResult)) {
                            $tempNilai = $previousNilaiResult;
                        } elseif (is_object($previousNilaiResult) && method_exists($previousNilaiResult, 'getResult')) {
                            $tempNilai = $previousNilaiResult->getResult();
                        } elseif (is_object($previousNilaiResult)) {
                            $tempNilai = [$previousNilaiResult];
                        }

                        log_message('debug', "Fallback: Ditemukan " . count($tempNilai) . " nilai di kelas {$prevIdKelas}");

                        foreach ($tempNilai as $temp) {
                            $idMateri = is_object($temp) ? $temp->IdMateri : ($temp['IdMateri'] ?? null);
                            if ($idMateri) {
                                $exists = false;
                                foreach ($previousNilai as $existing) {
                                    $existingIdMateri = is_object($existing) ? $existing->IdMateri : ($existing['IdMateri'] ?? null);
                                    if ($existingIdMateri == $idMateri) {
                                        $exists = true;
                                        break;
                                    }
                                }
                                if (!$exists) {
                                    $previousNilai[] = $temp;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        log_message('debug', "Error mencari nilai di kelas {$prevIdKelas} (fallback): " . $e->getMessage());
                        // Coba dengan method lama
                        try {
                            $previousNilaiResult = $this->DataNilai->GetDataNilaiDetail($IdSantri, $previousSemester, $previousTahunAjaran, $prevIdKelas, $prevIdTpq);
                            if (is_object($previousNilaiResult) && method_exists($previousNilaiResult, 'getResult')) {
                                $tempNilai = $previousNilaiResult->getResult();
                                foreach ($tempNilai as $temp) {
                                    $idMateri = is_object($temp) ? $temp->IdMateri : ($temp['IdMateri'] ?? null);
                                    if ($idMateri) {
                                        $exists = false;
                                        foreach ($previousNilai as $existing) {
                                            $existingIdMateri = is_object($existing) ? $existing->IdMateri : ($existing['IdMateri'] ?? null);
                                            if ($existingIdMateri == $idMateri) {
                                                $exists = true;
                                                break;
                                            }
                                        }
                                        if (!$exists) {
                                            $previousNilai[] = $temp;
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e2) {
                            log_message('debug', "Error fallback method lama di kelas {$prevIdKelas}: " . $e2->getMessage());
                        }
                    }
                }
            }
        }

        log_message('debug', "Total data nilai semester sebelumnya ditemukan: " . count($previousNilai) . " materi");

        // Buat mapping nilai sebelumnya berdasarkan IdMateri
        // Gunakan string untuk key agar matching lebih reliable
        $previousNilaiMap = [];
        foreach ($previousNilai as $prev) {
            $idMateri = is_object($prev) ? $prev->IdMateri : ($prev['IdMateri'] ?? null);
            $nilai = is_object($prev) ? $prev->Nilai : ($prev['Nilai'] ?? null);
            if ($idMateri !== null && $nilai !== null) {
                // Konversi IdMateri ke string untuk konsistensi
                $idMateriKey = (string)$idMateri;
                $previousNilaiMap[$idMateriKey] = (float)$nilai;
                log_message('debug', "Mapping nilai sebelumnya - IdMateri: {$idMateriKey}, Nilai: {$nilai}");
            }
        }

        log_message('debug', "Total mapping nilai sebelumnya: " . count($previousNilaiMap) . " materi");

        // Bandingkan dan tambahkan status
        $result = [];
        foreach ($nilaiArray as $nilai) {
            $idMateri = is_object($nilai) ? $nilai->IdMateri : ($nilai['IdMateri'] ?? null);
            $currentNilaiValue = is_object($nilai) ? $nilai->Nilai : ($nilai['Nilai'] ?? null);

            $status = 'baru'; // Default: materi baru
            $previousNilaiValue = null;

            if ($idMateri !== null) {
                // Konversi IdMateri ke string untuk matching
                $idMateriKey = (string)$idMateri;

                if (isset($previousNilaiMap[$idMateriKey])) {
                    $previousNilaiValue = $previousNilaiMap[$idMateriKey];
                    $currentNilaiFloat = (float)$currentNilaiValue;

                    log_message('debug', "Membandingkan nilai - IdMateri: {$idMateriKey}, Nilai sekarang: {$currentNilaiFloat}, Nilai sebelumnya: {$previousNilaiValue}");

                    if ($currentNilaiFloat > $previousNilaiValue) {
                        $status = 'naik';
                    } elseif ($currentNilaiFloat < $previousNilaiValue) {
                        $status = 'turun';
                    } else {
                        $status = 'sama';
                    }
                } else {
                    log_message('debug', "Materi baru - IdMateri: {$idMateriKey} tidak ditemukan di semester sebelumnya");
                }
            }

            // Tambahkan status ke data nilai
            if (is_object($nilai)) {
                $nilai->statusNilai = $status;
                $nilai->previousNilai = $previousNilaiValue;
            } else {
                $nilai['statusNilai'] = $status;
                $nilai['previousNilai'] = $previousNilaiValue;
            }

            $result[] = $nilai;
        }

        // Kembalikan dalam format yang sama dengan input
        if (is_array($currentNilai)) {
            return $result;
        } elseif (is_object($currentNilai) && method_exists($currentNilai, 'getResult')) {
            // Jika object dengan method getResult, kembalikan sebagai array
            return $result;
        } else {
            return !empty($result) ? $result[0] : $currentNilai;
        }
    }

    /**
     * Mendapatkan kelas sebelumnya berdasarkan ID kelas saat ini
     * @param string $idKelas ID kelas saat ini
     * @return string|null ID kelas sebelumnya, atau null jika tidak ada
     */
    private function getPreviousKelas($idKelas)
    {
        // Mapping kelas sebelumnya (kebalikan dari getNextKelas)
        $previousClassMapping = [
            2 => 1,   // TKQA -> TKQ
            3 => 2,   // TKQB -> TKQA
            4 => 3,   // TPQ1/SD1 -> TKQB
            5 => 4,   // TPQ2/SD2 -> TPQ1/SD1
            6 => 5,   // TPQ3/SD3 -> TPQ2/SD2
            7 => 6,   // TPQ4/SD4 -> TPQ3/SD3
            8 => 7,   // TPQ5/SD5 -> TPQ4/SD4
            9 => 8,   // TPQ6/SD6 -> TPQ5/SD5
            10 => 9,  // ALUMNI -> TPQ6/SD6
        ];

        return $previousClassMapping[$idKelas] ?? null;
    }

    /**
     * Menghitung statistik nilai (naik, turun, sama, baru)
     * @param array|object $nilaiData Data nilai
     * @return array Statistik dengan keys: naik, turun, sama, baru, total
     */
    private function calculateNilaiStatistics($nilaiData)
    {
        // Konversi ke array jika object
        $nilaiArray = [];
        if (is_array($nilaiData)) {
            $nilaiArray = $nilaiData;
        } elseif (is_object($nilaiData) && method_exists($nilaiData, 'getResult')) {
            $nilaiArray = $nilaiData->getResult();
        } elseif (is_object($nilaiData)) {
            $nilaiArray = [$nilaiData];
        }

        $statistik = [
            'naik' => 0,
            'turun' => 0,
            'sama' => 0,
            'baru' => 0,
            'total' => count($nilaiArray)
        ];

        foreach ($nilaiArray as $nilai) {
            $status = is_object($nilai) ? ($nilai->statusNilai ?? 'baru') : ($nilai['statusNilai'] ?? 'baru');

            switch ($status) {
                case 'naik':
                    $statistik['naik']++;
                    break;
                case 'turun':
                    $statistik['turun']++;
                    break;
                case 'sama':
                    $statistik['sama']++;
                    break;
                default:
                    $statistik['baru']++;
                    break;
            }
        }

        return $statistik;
    }

    /**
     * Ambil data nilai munaqosah untuk santri tertentu
     */
    private function getNilaiMunaqosah($IdSantri, $IdTahunAjaran, $IdTpq)
    {
        try {
            $builder = $this->db->table('tbl_munaqosah_nilai mn');
            $builder->select('mn.*, mp.NamaMateri, km.NamaKategoriMateri, r.NoPeserta');
            $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = mn.IdMateri', 'left');
            $builder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = mn.IdKategoriMateri', 'left');
            $builder->join('tbl_munaqosah_registrasi_uji r', 'r.NoPeserta = mn.NoPeserta AND r.IdTahunAjaran = mn.IdTahunAjaran AND r.TypeUjian = mn.TypeUjian', 'left');
            $builder->where('mn.IdSantri', $IdSantri);
            $builder->where('mn.IdTahunAjaran', $IdTahunAjaran);
            $builder->where('mn.IdTpq', $IdTpq);
            $builder->where('mn.TypeUjian', 'munaqosah');
            $builder->orderBy('km.NamaKategoriMateri', 'ASC');
            $builder->orderBy('mp.NamaMateri', 'ASC');

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error in getNilaiMunaqosah: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil data nilai pra-munaqosah untuk santri tertentu
     */
    private function getNilaiPraMunaqosah($IdSantri, $IdTahunAjaran, $IdTpq)
    {
        try {
            $builder = $this->db->table('tbl_munaqosah_nilai mn');
            $builder->select('mn.*, mp.NamaMateri, km.NamaKategoriMateri, r.NoPeserta');
            $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = mn.IdMateri', 'left');
            $builder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = mn.IdKategoriMateri', 'left');
            $builder->join('tbl_munaqosah_registrasi_uji r', 'r.NoPeserta = mn.NoPeserta AND r.IdTahunAjaran = mn.IdTahunAjaran AND r.TypeUjian = mn.TypeUjian', 'left');
            $builder->where('mn.IdSantri', $IdSantri);
            $builder->where('mn.IdTahunAjaran', $IdTahunAjaran);
            $builder->where('mn.IdTpq', $IdTpq);
            $builder->where('mn.TypeUjian', 'pra-munaqosah');
            $builder->orderBy('km.NamaKategoriMateri', 'ASC');
            $builder->orderBy('mp.NamaMateri', 'ASC');

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error in getNilaiPraMunaqosah: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil data summary nilai untuk setiap santri - SAMA PERSIS DENGAN RAPORT
     */
    private function getSummaryDataForSantri($IdTpq, $IdKelas, $IdTahunAjaran, $semester)
    {
        // Ambil data summary nilai per semester
        $summaryData = $this->DataNilai->getDataNilaiPerSemester($IdTpq, $IdKelas, $IdTahunAjaran, $semester);

        // Buat array dataKelas untuk struktur data
        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping
        $dataKelas = [];
        foreach ($summaryData as $nilai) {
            $namaKelasOriginal = $nilai->NamaKelas;

            // Check MDA mapping dan convert nama kelas jika sesuai
            $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
            $namaKelasDisplay = $this->helpFunction->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );

            // Simpan nama kelas yang sudah dikonversi
            if (!isset($dataKelas[$nilai->IdKelas])) {
                $dataKelas[$nilai->IdKelas] = $namaKelasDisplay;
            }

            // Update nama kelas di data summary untuk ditampilkan di tabel
            $nilai->NamaKelas = $namaKelasDisplay;
        }

        return [
            'nilai' => $summaryData,
            'dataKelas' => $dataKelas
        ];
    }

    public function showRanking($semester = null)
    {
        // Gunakan logika yang sama persis dengan raport
        $IdTpq = session()->get('IdTpq');
        $IdGuru = session()->get('IdGuru');
        $IdTahunAjaran = session()->get('IdTahunAjaran');

        // Jika semester tidak diberikan, default ke semester saat ini
        if ($semester === null) {
            $currentMonth = (int)date('m');
            $semester = ($currentMonth >= 7) ? 'Ganjil' : 'Genap';
        }

        // Cek apakah user adalah Operator
        $isOperator = in_groups('Operator');

        // Cek apakah user adalah Kepala Sekolah
        $jabatanData = $this->helpFunction->getStrukturLembagaJabatan($IdGuru, $IdTpq);
        $isKepalaSekolah = false;
        if (!empty($jabatanData)) {
            foreach ($jabatanData as $jabatan) {
                if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                    $isKepalaSekolah = true;
                    break;
                }
            }
        }

        // Ambil list id kelas dari tbl_kelas_santri - SAMA PERSIS DENGAN RAPORT
        // Operator dan Kepala Sekolah memiliki akses ke semua kelas
        if ($isKepalaSekolah || $isOperator) {
            $listIdKelas = $this->helpFunction->getListIdKelasFromKelasSantri($IdTpq, $IdTahunAjaran);
        } else {
            $listIdKelas = session()->get('IdKelas');
            // Jika tidak ada kelas di session, ambil semua kelas dari TPQ
            if (empty($listIdKelas)) {
                $listIdKelas = $this->helpFunction->getListIdKelasFromKelasSantri($IdTpq, $IdTahunAjaran);
            }
        }

        // Pastikan listIdKelas adalah array (bisa jadi null atau bukan array)
        if (empty($listIdKelas) || !is_array($listIdKelas)) {
            $listIdKelas = [];
        }

        // Untuk Operator, set IdGuru menjadi null agar getListKelas menggunakan mode admin/kepala sekolah
        $guruIdForKelas = ($isOperator && empty($IdGuru)) ? null : $IdGuru;

        // Ambil object data kelas - kirim flag isOperator agar diperlakukan seperti kepala sekolah
        $dataKelas = $this->helpFunction->getListKelas($IdTpq, $IdTahunAjaran, $listIdKelas, $guruIdForKelas, $isOperator);

        // Konversi nama kelas menjadi MDA jika sesuai dengan mapping - SAMA PERSIS DENGAN RAPORT
        foreach ($dataKelas as $kelas) {
            $namaKelasOriginal = $kelas->NamaKelas;

            // Check MDA mapping dan convert nama kelas jika sesuai
            $mdaCheckResult = $this->helpFunction->checkMdaKelasMapping($IdTpq, $namaKelasOriginal);
            $kelas->NamaKelas = $this->helpFunction->convertKelasToMda(
                $namaKelasOriginal,
                $mdaCheckResult['mappedMdaKelas']
            );
        }

        // Ambil data summary nilai untuk setiap santri - SAMA PERSIS DENGAN RAPORT
        $summaryData = $this->getSummaryDataForSantri($IdTpq, $listIdKelas, $IdTahunAjaran, $semester);

        // Buat array kelas untuk tab
        $dataKelasArray = [];
        foreach ($dataKelas as $kelas) {
            $dataKelasArray[$kelas->IdKelas] = $kelas->NamaKelas;
        }

        // Gunakan data dari summaryData yang sama dengan raport
        $rankingData = $summaryData['nilai'] ?? [];

        // Sort ranking data berdasarkan IdKelas dan Ranking
        usort($rankingData, function ($a, $b) {
            // Urutkan berdasarkan IdKelas dulu
            if ($a->IdKelas != $b->IdKelas) {
                return $a->IdKelas <=> $b->IdKelas;
            }
            // Jika IdKelas sama, urutkan berdasarkan Ranking
            if ($a->Rangking === null && $b->Rangking === null) {
                return 0;
            }
            if ($a->Rangking === null) {
                return 1; // a di akhir
            }
            if ($b->Rangking === null) {
                return -1; // b di akhir
            }
            return $a->Rangking <=> $b->Rangking;
        });

        // Kelompokkan data ranking per kelas
        $rankingPerKelas = [];
        foreach ($rankingData as $data) {
            $idKelas = $data->IdKelas;
            if (!isset($rankingPerKelas[$idKelas])) {
                $rankingPerKelas[$idKelas] = [];
            }
            $rankingPerKelas[$idKelas][] = $data;
        }

        // Sort ranking per kelas berdasarkan ranking
        foreach ($rankingPerKelas as $idKelas => &$data) {
            usort($data, function ($a, $b) {
                if ($a->Rangking === null && $b->Rangking === null) {
                    return 0;
                }
                if ($a->Rangking === null) {
                    return 1; // a di akhir
                }
                if ($b->Rangking === null) {
                    return -1; // b di akhir
                }
                return $a->Rangking <=> $b->Rangking;
            });
        }

        $data = [
            'page_title' => 'Rangking Santri Per Kelas - Semester ' . $semester,
            'rankingData' => $rankingData,
            'rankingPerKelas' => $rankingPerKelas,
            'dataKelas' => $dataKelasArray,
            'semester' => $semester,
            'IdTahunAjaran' => $IdTahunAjaran,
        ];

        return view('backend/nilai/ranking', $data);
    }
}
