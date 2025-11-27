<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriBaruModel;

class Nilai extends BaseController
{
    protected $DataNilai;
    protected $helpFunction;
    protected $DataSantriBaru;
    protected $IdTpq;
    protected $IdKelas;
    protected $IdTahunAjaran;
    protected $settingNilaiModel;

    public function __construct()
    {
        $this->IdTpq = session()->get('IdTpq');
        $this->IdKelas = session()->get('IdKelas');
        $this->IdTahunAjaran = session()->get('IdTahunAjaran');
        $this->DataNilai = new NilaiModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->DataSantriBaru = new SantriBaruModel();
    }

    public function showDetail($IdSantri, $IdSemseter, $Edit = null, $IdJabatan = null)
    {
        log_message('info', '=== showDetail: START ===');
        log_message('info', 'showDetail Parameters - IdSantri: ' . json_encode($IdSantri) . ', IdSemseter: ' . json_encode($IdSemseter) . ', Edit: ' . json_encode($Edit) . ', IdJabatan: ' . json_encode($IdJabatan));

        // ambil settingan nilai minimun dan maksimal dari session
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $IdKelas = session()->get('IdKelas');
        $IdTpq = $this->IdTpq; // Ambil IdTpq dari session untuk filter query
        log_message('info', 'showDetail Session Data - IdTahunAjaran: ' . json_encode($IdTahunAjaran) . ', IdKelas: ' . json_encode($IdKelas) . ', IdTpq: ' . json_encode($IdTpq));

        $settingNilai = (object)[
            'NilaiMin' => session()->get('SettingNilaiMin'),
            'NilaiMax' => session()->get('SettingNilaiMax')
        ];

        // ambil jika settingan nilai alfabetic dari session
        $nilaiAlphabetEnabled = session()->get('SettingNilaiAlphabet') ?? false;
        log_message('info', "showDetail - NilaiAlphabetEnabled: " . ($nilaiAlphabetEnabled ? 'true' : 'false'));

        if ($nilaiAlphabetEnabled) {
            // Jika alphabet enabled, ambil detail settings
            log_message('info', "showDetail Query 1: getNilaiAlphabetSettings START - IdTpq: " . json_encode($this->IdTpq));
            $queryStartTime = microtime(true);

            $settingNilai->NilaiAlphabet = $this->helpFunction->getNilaiAlphabetSettings($this->IdTpq);

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

        log_message('info', '=== showDetail: END ===');

        $data = [
            'page_title' => 'Data Nilai',
            'nilai' => $datanilai,
            'guruPendamping' => $IdJabatan,
            'pageEdit' => $Edit,
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

        // Buat array untuk tracking nilai per santri
        $nilaiStatus = [];
        foreach ($allNilai as $nilai) {
            if ($nilai->Nilai == 0) {
                $nilaiStatus[$nilai->IdSantri] = 0;
            } else if (!isset($nilaiStatus[$nilai->IdSantri])) {
                $nilaiStatus[$nilai->IdSantri] = 1;
            }
        }

        // Set status penilaian untuk setiap santri
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

        $data = [
            'page_title' => 'Data Santri Per Semester ' . $semester,
            'dataSantri' => $dataSantri,
            'dataKelas' => $dataKelas,
            'dataMateri' => $dataMateri,
            'dataNilaiDetail' => $dataNilaiDetail,
            'progressData' => $progressData,
            'semester' => $semester,
            'settingNilai' => $settingNilai,
        ];

        return view('backend/santri/santriPerKelas', $data);
    }

    public function showSumaryPersemester($semester = null)
    {
        $datanilai = $this->DataNilai->getDataNilaiPerSemester($this->IdTpq, $this->IdKelas, $this->IdTahunAjaran, $semester);
        $dataKelas = [];
        foreach ($datanilai as $nilai) {
            $dataKelas = array_column($datanilai, 'NamaKelas', 'IdKelas');
        }
        return view('backend/nilai/nilaiSantriPerSemester', [
            'page_title' => 'Rank Data Nilai Semester ' . $semester,
            'nilai' => $datanilai,
            'dataKelas' => $dataKelas,
            'semester' => $semester
        ]);
    }

    public function showNilaiProfilDetail($IdSantri)
    {
        // Ambil IdTpq dari session untuk filter query
        $IdTpq = $this->IdTpq;
        
        try {
            // Gunakan method yang dioptimasi dengan caching
            // TAMBAHKAN IdTpq sebagai parameter untuk menghindari double materi saat ada kelas yang sama di tahun yang sama dengan IdTpq berbeda
            $datanilai = $this->DataNilai->getDataNilaiDetailOptimized($IdSantri, 1, null, null, $IdTpq);
        } catch (\Exception $e) {
            // Log error dan fallback ke method lama
            log_message('error', 'Error in showNilaiProfilDetail optimized method: ' . $e->getMessage());
            // TAMBAHKAN IdTpq sebagai parameter untuk menghindari double materi saat ada kelas yang sama di tahun yang sama dengan IdTpq berbeda
            $datanilai = $this->DataNilai->GetDataNilaiDetail($IdSantri, 1, null, null, $IdTpq);
        }

        return view('backend/nilai/nilaiSantriDetailPersonal', [
            'page_title' => 'Detail Nilai',
            'nilai' => $datanilai
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
        
}
