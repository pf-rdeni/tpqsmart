<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SertifikasiGuruModel;
use App\Models\SertifikasiNilaiModel;
use App\Models\SertifikasiJuriModel;
use App\Models\SertifikasiGroupMateriModel;
use App\Models\SertifikasiMateriModel;

class Sertifikasi extends BaseController
{
    protected $sertifikasiGuruModel;
    protected $sertifikasiNilaiModel;
    protected $sertifikasiJuriModel;
    protected $sertifikasiGroupMateriModel;
    protected $sertifikasiMateriModel;
    protected $db;

    public function __construct()
    {
        $this->sertifikasiGuruModel = new SertifikasiGuruModel();
        $this->sertifikasiNilaiModel = new SertifikasiNilaiModel();
        $this->sertifikasiJuriModel = new SertifikasiJuriModel();
        $this->sertifikasiGroupMateriModel = new SertifikasiGroupMateriModel();
        $this->sertifikasiMateriModel = new SertifikasiMateriModel();
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

        $data = [
            'page_title' => 'Input Nilai Sertifikasi',
            'juri_data' => $juriDataArray,
            'peserta_terakhir' => $pesertaTerakhir,
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
            $guruData = $this->sertifikasiGuruModel->getGuruByNoTest($noTest);
            
            if (empty($guruData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'GURU_NOT_FOUND',
                    'message' => 'Data guru dengan nomor test ' . $noTest . ' tidak ditemukan',
                ]);
            }

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

            // Cek apakah semua materi sudah dinilai oleh juri lain
            $allMateriSudahDinilai = true;
            foreach ($materiList as $materi) {
                $existingNilai = $this->sertifikasiNilaiModel->checkNilaiExistsByMateriAnyJuri(
                    $noTest,
                    $juriDataArray['IdGroupMateri'],
                    $materi['IdMateri']
                );
                
                if (empty($existingNilai)) {
                    $allMateriSudahDinilai = false;
                    break;
                }
            }

            // Jika semua materi sudah dinilai oleh juri lain, tolak
            if ($allMateriSudahDinilai) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'ALL_MATERI_ALREADY_SCORED',
                    'message' => 'Semua materi untuk peserta ini sudah dinilai oleh juri lain',
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
                    'guru' => $guruData,
                    'juri' => $juriData,
                    'groupMateri' => $groupMateri,
                    'materiList' => $materiList,
                    'existingNilaiByMateri' => $existingNilaiByMateri,
                    'allMateriSudahDinilai' => $allMateriSudahDinilai,
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
                    'nilaiByMateri' => [], // Array dengan key IdMateri
                ];
            }

            // Simpan nilai per materi (hanya simpan yang pertama jika ada duplikat)
            $idMateri = $nilai['IdMateri'] ?? null;
            if ($idMateri && !isset($groupedData[$noPeserta]['nilaiByMateri'][$idMateri])) {
                $groupedData[$noPeserta]['nilaiByMateri'][$idMateri] = $nilai['Nilai'] ?? 0;
            }
        }

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
        $builder->select('sn.NoPeserta as noTest, sn.updated_at, sg.Nama as NamaGuru, sj.usernameJuri');
        $builder->join('tbl_sertifikasi_guru sg', 'sg.noTest = sn.NoPeserta', 'left');
        $builder->join('tbl_sertifikasi_juri sj', 'sj.IdJuri = sn.IdJuri', 'left');
        $builder->where('sn.IdJuri', $idJuri);
        $builder->groupBy('sn.NoPeserta, sg.Nama, sj.usernameJuri, sn.updated_at');
        $builder->orderBy('sn.updated_at', 'DESC');
        $builder->limit(5);
        
        return $builder->get()->getResultArray();
    }
}

