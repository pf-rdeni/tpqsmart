<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MunaqosahNilaiModel;
use App\Models\MunaqosahAntrianModel;
use App\Models\MunaqosahBobotNilaiModel;
use App\Models\MunaqosahMateriModel;
use App\Models\MunaqosahPesertaModel;
use App\Models\SantriModel;
use App\Models\TpqModel;
use App\Models\GuruModel;
use App\Models\MateriPelajaranModel;
use App\Models\HelpFunctionModel;
use App\Models\MunaqosahGrupMateriUjiModel;
use App\Models\MunaqosahAlquranModel;
use App\Models\MunaqosahRegistrasiUjiModel;
use App\Models\SantriBaruModel;
use App\Models\MunaqosahJuriModel;
use App\Models\MunaqosahKategoriKesalahanModel;
use App\Models\MunaqosahKonfigurasiModel;
use App\Models\MunaqosahJadwalUjianModel;
use Myth\Auth\Password;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
class Munaqosah extends BaseController
{
    protected $nilaiMunaqosahModel;
    protected $antrianMunaqosahModel;
    protected $bobotNilaiMunaqosahModel;
    protected $materiMunaqosahModel;
    protected $pesertaMunaqosahModel;
    protected $santriModel;
    protected $tpqModel;
    protected $guruModel;
    protected $materiPelajaranModel;
    protected $helpFunction;
    protected $grupMateriUjiMunaqosahModel;
    protected $munaqosahAlquranModel;
    protected $munaqosahRegistrasiUjiModel;
    protected $santriBaruModel;
    protected $munaqosahJuriModel;
    protected $munaqosahKategoriModel;
    protected $munaqosahKategoriKesalahanModel;
    protected $munaqosahKonfigurasiModel;
    protected $munaqosahJadwalUjianModel;
    protected $db;
    private array $bobotWeightCache = [];
    private array $kelulusanThresholdCache = [];
    
    public function __construct()
    {
        $this->nilaiMunaqosahModel = new MunaqosahNilaiModel();
        $this->antrianMunaqosahModel = new MunaqosahAntrianModel();
        $this->bobotNilaiMunaqosahModel = new MunaqosahBobotNilaiModel();
        $this->materiMunaqosahModel = new MunaqosahMateriModel();
        $this->pesertaMunaqosahModel = new MunaqosahPesertaModel();
        $this->santriModel = new SantriModel();
        $this->tpqModel = new TpqModel();
        $this->guruModel = new GuruModel();
        $this->materiPelajaranModel = new MateriPelajaranModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->grupMateriUjiMunaqosahModel = new MunaqosahGrupMateriUjiModel();
        $this->munaqosahAlquranModel = new MunaqosahAlquranModel();
        $this->munaqosahRegistrasiUjiModel = new MunaqosahRegistrasiUjiModel();
        $this->santriBaruModel = new SantriBaruModel();
        $this->munaqosahJuriModel = new MunaqosahJuriModel();
        $this->munaqosahKategoriKesalahanModel = new MunaqosahKategoriKesalahanModel();
        $this->munaqosahKonfigurasiModel = new MunaqosahKonfigurasiModel();
        $this->munaqosahJadwalUjianModel = new MunaqosahJadwalUjianModel();
        $this->db = \Config\Database::connect();
    }

    // ==================== NILAI MUNAQOSAH ====================
    
    public function index()
    {
        // Load helper
        helper('munaqosah');
        
        // Get statistik
        $statistik = getStatistikMunaqosah();
        
        $data = [
            'page_title' => 'Sistem Penilaian Munaqosah',
            'statistik' => $statistik
        ];
        return view('backend/Munaqosah/index', $data);
    }

    /**
     * Display input nilai juri form
     */
    public function inputNilaiJuri()
    {

        // Ambil tahun ajaran saat ini dari HelpFunctionModel
        $helpFunctionModel = new \App\Models\HelpFunctionModel();
        // Ambil tahun ajaran saat ini
        $currentTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();

        // Ambil username juri dari user yang login
        $usernameJuri = user()->username;
        // Ambil informasi juri dari username juri
        $juriData = $this->munaqosahJuriModel->getJuriByUsernameJuri($usernameJuri);
        // Ambil IdTpq dari data juri
        $idTpq = $juriData->IdTpq;
        // Ambil Typeujian dari data juri
        $typeUjian = $juriData->TypeUjian;
        // Ambil 5 peserta terakhir yang sudah dinilai oleh juri ini
        $pesertaTerakhir = $this->nilaiMunaqosahModel->getPesertaTerakhirByJuri(
            $juriData->IdJuri,
            $currentTahunAjaran,
            $typeUjian
        );
        // Ambil total peserta yang terdaftar di tbl_munaqosah_registrasi_uji
        $totalPesertaYangTerregister = $this->munaqosahRegistrasiUjiModel->getTotalRegisteredParticipants(
            $currentTahunAjaran,
            $typeUjian,
            $idTpq
        );
        // Ambil total peserta yang sudah dinilai oleh juri
        $totalPesertaSudahDinilai = $this->nilaiMunaqosahModel->getTotalPesertaByJuri(
            $idTpq,
            $juriData->IdJuri,
            $currentTahunAjaran,
            $typeUjian
        );

        $totalPesertaBelumDinilai = $totalPesertaYangTerregister - $totalPesertaSudahDinilai;

        // Get nilai min dan max from configuration
        // Use IdTpq from juri, fallback to 'default' if not found
        $configIdTpq = $idTpq ?? 'default';
        $nilaiMinimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NilaiMinimal', 40);
        $nilaiMaximal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NilaiMaximal', 99);

        $data = [
            'page_title' => 'Input Nilai Munaqosah',
            'current_tahun_ajaran' => $currentTahunAjaran,
            'juri_data' => $juriData,
            'peserta_terakhir' => $pesertaTerakhir,
            'total_peserta_sudah_dinilai' => $totalPesertaSudahDinilai,
            'total_peserta_belum_dinilai' => $totalPesertaBelumDinilai,
            'total_peserta_terdaftar' => $totalPesertaYangTerregister,
            'nilai_minimal' => $nilaiMinimal,
            'nilai_maximal' => $nilaiMaximal,
        ];
        return view('backend/Munaqosah/inputNilaiJuri', $data);
    }

    /**
     * Cek peserta untuk input nilai juri
     */
    public function cekPeserta()
    {
        try {
            $noPeserta = $this->request->getPost('noPeserta');
            $IdJuri = $this->request->getPost('IdJuri');
            $IdTahunAjaran = $this->request->getPost('IdTahunAjaran');
            $TypeUjian = $this->request->getPost('TypeUjian') ?? 'munaqosah';

            // Ambil data juri lengkap termasuk TypeUjian dan IdTpq
            $juriDataForCheck = $this->munaqosahJuriModel->getJuriByIdJuri($IdJuri);
            if (empty($juriDataForCheck)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'JURI_NOT_FOUND',
                    'message' => 'Data juri tidak ditemukan',
                    'details' => 'Data juri dengan ID ' . $IdJuri . ' tidak ditemukan'
                ]);
            }

            // Ambil TypeUjian dari data juri atau dari parameter
            $juriTypeUjian = $juriDataForCheck['TypeUjian'] ?? $TypeUjian;
            $idTpq = $juriDataForCheck['IdTpq'] ?? null;

            // Validasi IdTpq berdasarkan TypeUjian
            // Untuk pra-munaqosah, IdTpq harus ada
            // Untuk munaqosah, IdTpq boleh null (ini normal)
            if ($juriTypeUjian === 'pra-munaqosah' && empty($idTpq)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'ID_TPQ_REQUIRED_FOR_PRA_MUNAQOSAH',
                    'message' => 'ID TPQ wajib untuk Pra-Munaqosah',
                    'details' => 'Juri Pra-Munaqosah harus memiliki ID TPQ, namun ID TPQ tidak ditemukan untuk ID Juri ' . $IdJuri
                ]);
            }

            // Pastikan TypeUjian konsisten
            if (!empty($juriTypeUjian) && $juriTypeUjian !== $TypeUjian) {
                // Jika TypeUjian dari juri berbeda dengan parameter, gunakan yang dari juri
                $TypeUjian = $juriTypeUjian;
            }

            // Validasi parameter input
            if (empty($noPeserta)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_NO_PESERTA',
                    'message' => 'Nomor peserta tidak boleh kosong',
                    'details' => 'Parameter noPeserta harus diisi'
                ]);
            }

            if (empty($IdTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_TAHUN_AJARAN',
                    'message' => 'Tahun ajaran tidak boleh kosong',
                    'details' => 'Parameter IdTahunAjaran harus diisi'
                ]);
            }

            // Validasi format No Peserta
            if (!preg_match('/^[A-Z0-9]+$/', $noPeserta)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'INVALID_NO_PESERTA_FORMAT',
                    'message' => 'Format nomor peserta tidak valid',
                    'details' => 'Nomor peserta hanya boleh berisi huruf kapital dan angka'
                ]);
            }

            // Cek data registrasi peserta
            // Untuk munaqosah dengan juri tanpa IdTpq, kirim null
            // Untuk pra-munaqosah atau munaqosah dengan IdTpq, kirim IdTpq
            $idTpqForQuery = ($TypeUjian === 'munaqosah' && empty($idTpq)) ? null : ($idTpq ?? 0);
            $registrasi = $this->munaqosahRegistrasiUjiModel->getRegistrasiByNoPeserta($noPeserta, $TypeUjian, $IdTahunAjaran, $idTpqForQuery);

            if (empty($registrasi)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'PESERTA_NOT_FOUND',
                    'message' => 'Peserta dengan nomor ' . $noPeserta . ' tidak ditemukan',
                    'details' => 'Data registrasi peserta tidak ditemukan di database'
                ]);
            }

            // Ambil data santri
            $santriData = $this->santriBaruModel->getDetailSantri($registrasi['IdSantri']);
            if (empty($santriData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'SANTRI_NOT_FOUND',
                    'message' => 'Data santri tidak ditemukan',
                    'details' => 'Data santri dengan ID ' . $registrasi['IdSantri'] . ' tidak ditemukan'
                ]);
            }

            // Ambil data juri
            $juriData = null;
            if (!empty($IdJuri)) {
                $juriData = $this->munaqosahJuriModel->getJuriByIdJuri($IdJuri);
            }

            if (empty($juriData)) {
                // Jika tidak ada data juri, coba ambil dari session atau default
                $juriData = [
                    'IdJuri' => $IdJuri ?? 'J001',
                    'UsernameJuri' => session()->get('username') ?? 'Juri Default',
                    'IdGrupMateriUjian' => 'GM001' // Default grup
                ];
            }

            // Ambil data grup materi ujian
            $grupMateri = $this->grupMateriUjiMunaqosahModel->where('IdGrupMateriUjian', $juriData['IdGrupMateriUjian'])->first();
            if (!$grupMateri) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'GRUP_MATERI_NOT_FOUND',
                    'message' => 'Data grup materi ujian tidak ditemukan',
                    'details' => 'Grup materi dengan ID ' . $juriData['IdGrupMateriUjian'] . ' tidak ditemukan'
                ]);
            }

            // Ambil materi berdasarkan registrasi peserta dan grup materi ujian
            $materiData = $this->munaqosahRegistrasiUjiModel->getMateriByNoPesertaAndGrup($noPeserta, $juriData['IdGrupMateriUjian'], $TypeUjian, $IdTahunAjaran);
            if (empty($materiData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'MATERI_NOT_FOUND',
                    'message' => 'Tidak ada materi yang tersedia untuk peserta ini',
                    'details' => 'Peserta ' . $noPeserta . ' tidak memiliki materi untuk grup ' . $juriData['IdGrupMateriUjian']
                ]);
            }

            // Transform data materi untuk konsistensi
            $transformedMateriData = [];
            foreach ($materiData as $materi) {
                $namaKategori = $materi['NamaKategoriMateri'] ?? ($materi['KategoriMateriUjian'] ?? null);

                $transformedMateriData[] = [
                    'IdMateri' => $materi['IdMateri'],
                    'NamaMateri' => $materi['NamaMateri'],
                    'IdKategoriMateri' => $materi['IdKategoriMateri'] ?? null,
                    'NamaKategoriMateri' => $namaKategori,
                    'KategoriMateriUjian' => $namaKategori,
                    'IdGrupMateriUjian' => $materi['IdGrupMateriUjian'],
                    'WebLinkAyat' => isset($materi['WebLinkAyat']) ? $materi['WebLinkAyat'] : null,
                    'KategoriAsli' => isset($materi['KategoriAsli']) ? $materi['KategoriAsli'] : null
                ];
            }

            // Ambil kategori kesalahan untuk setiap kategori materi sekaligus
            $kategoriNames = array_unique(array_filter(array_map(static function ($materi) {
                return $materi['KategoriMateriUjian'] ?? $materi['NamaKategoriMateri'] ?? null;
            }, $transformedMateriData)));

            $errorCategoriesByKategori = [];
            if (!empty($kategoriNames)) {
                $errorCategoryRows = $this->munaqosahKategoriKesalahanModel
                    ->select('tbl_munaqosah_kategori_kesalahan.NamaKategoriKesalahan, tbl_kategori_materi.NamaKategoriMateri')
                    ->join('tbl_kategori_materi', 'tbl_kategori_materi.IdKategoriMateri = tbl_munaqosah_kategori_kesalahan.IdKategoriMateri', 'left')
                    ->whereIn('tbl_kategori_materi.NamaKategoriMateri', $kategoriNames)
                    ->where('tbl_munaqosah_kategori_kesalahan.Status', 'Aktif')
                    ->orderBy('tbl_kategori_materi.NamaKategoriMateri', 'ASC')
                    ->orderBy('tbl_munaqosah_kategori_kesalahan.IdKategoriKesalahan', 'ASC')
                    ->findAll();

                foreach ($errorCategoryRows as $row) {
                    $kategoriName = $row['NamaKategoriMateri'] ?? null;
                    $namaKesalahan = $row['NamaKategoriKesalahan'] ?? null;

                    if (!$kategoriName || !$namaKesalahan) {
                        continue;
                    }

                    if (!isset($errorCategoriesByKategori[$kategoriName])) {
                        $errorCategoriesByKategori[$kategoriName] = [];
                    }

                    if (!in_array($namaKesalahan, $errorCategoriesByKategori[$kategoriName], true)) {
                        $errorCategoriesByKategori[$kategoriName][] = $namaKesalahan;
                    }
                }

                foreach ($errorCategoriesByKategori as $kategori => $daftarKesalahan) {
                    $errorCategoriesByKategori[$kategori] = array_values($daftarKesalahan);
                }
            }

            // Cek apakah nilai sudah ada untuk peserta ini
            $existingNilai = $this->nilaiMunaqosahModel->where('NoPeserta', $noPeserta)
                ->where('IdTahunAjaran', $IdTahunAjaran)
                ->where('IdJuri', $juriData['IdJuri'])
                ->where('TypeUjian', $TypeUjian)
                ->findAll();

            $nilaiExists = !empty($existingNilai);

            // === VALIDASI ROOM SYSTEM ===
            $idGrupMateriUjian = $juriData['IdGrupMateriUjian'];
            $currentJuriRoom = $juriData['RoomId'] ?? null;

            // Cek apakah room validation aktif untuk grup materi ini
            $configIdTpq = $idTpq ?? 'default';
            $enableRoomValidation = $this->munaqosahKonfigurasiModel->getSetting(
                $configIdTpq,
                'EnableRoomValidation_' . $idGrupMateriUjian
            );

            $roomValidationInfo = [
                'enabled' => false,
                'currentRoom' => $currentJuriRoom,
                'existingRoom' => null,
                'juriCount' => 0,
                'maxJuri' => 2,
                'canProceed' => true,
                'message' => null
            ];

            if ($enableRoomValidation && !$nilaiExists) {
                $roomValidationInfo['enabled'] = true;

                // Cek apakah peserta sudah di-test di room lain untuk grup materi ini
                $existingRoom = $this->nilaiMunaqosahModel->getPesertaRoomByGrupMateri(
                    $noPeserta,
                    $idGrupMateriUjian,
                    $IdTahunAjaran,
                    $TypeUjian
                );

                if ($existingRoom && !empty($existingRoom['RoomId'])) {
                    $roomValidationInfo['existingRoom'] = $existingRoom['RoomId'];

                    // Jika room berbeda, tidak bisa proceed
                    if ($currentJuriRoom && $existingRoom['RoomId'] !== $currentJuriRoom) {
                        return $this->response->setJSON([
                            'success' => false,
                            'status' => 'ROOM_VALIDATION_ERROR',
                            'code' => 'DIFFERENT_ROOM',
                            'message' => 'Peserta sudah diuji di room lain',
                            'details' => "Peserta {$noPeserta} sudah diuji di room {$existingRoom['RoomId']}. Tidak bisa diuji di room {$currentJuriRoom}.",
                            'roomInfo' => [
                                'existingRoom' => $existingRoom['RoomId'],
                                'currentRoom' => $currentJuriRoom
                            ]
                        ]);
                    }
                }

                // Cek maksimal juri per room untuk grup materi ini
                $maxJuriPerRoom = $this->munaqosahKonfigurasiModel->getSettingAsInt(
                    $configIdTpq,
                    'MaxJuriPerRoom_' . $idGrupMateriUjian,
                    2 // default 2 juri
                );

                $currentJuriCount = $this->nilaiMunaqosahModel->countJuriByPesertaGrupMateri(
                    $noPeserta,
                    $idGrupMateriUjian,
                    $IdTahunAjaran,
                    $TypeUjian
                );

                $roomValidationInfo['juriCount'] = $currentJuriCount;
                $roomValidationInfo['maxJuri'] = $maxJuriPerRoom;

                if ($currentJuriCount >= $maxJuriPerRoom) {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'ROOM_VALIDATION_ERROR',
                        'code' => 'MAX_JURI_EXCEEDED',
                        'message' => 'Maksimal juri sudah tercapai',
                        'details' => "Peserta {$noPeserta} sudah dinilai oleh {$currentJuriCount} juri (maksimal {$maxJuriPerRoom}).",
                        'roomInfo' => [
                            'currentCount' => $currentJuriCount,
                            'maxCount' => $maxJuriPerRoom
                        ]
                    ]);
                }

                // Get list of juri who already scored
                $juriList = $this->nilaiMunaqosahModel->getJuriByPesertaGrupMateri(
                    $noPeserta,
                    $idGrupMateriUjian,
                    $IdTahunAjaran,
                    $TypeUjian
                );

                $roomValidationInfo['juriList'] = $juriList;
                $roomValidationInfo['message'] = "Peserta ini sudah dinilai oleh {$currentJuriCount} dari {$maxJuriPerRoom} juri di room " . ($existingRoom['RoomId'] ?? $currentJuriRoom);
            }

            // Siapkan data response
            $responseData = [
                'peserta' => [
                    'NoPeserta' => $registrasi['NoPeserta'],
                    'IdSantri' => $registrasi['IdSantri'],
                    'IdTpq' => $registrasi['IdTpq'],
                    'NamaSantri' => $santriData['NamaSantri'],
                    'NamaTpq' => $santriData['IdTpq'],
                    'NamaKelas' => $santriData['IdKelas']
                ],
                'juri' => [
                    'IdJuri' => $juriData['IdJuri'],
                    'UsernameJuri' => $juriData['UsernameJuri'],
                    'IdGrupMateriUjian' => $juriData['IdGrupMateriUjian'],
                    'RoomId' => $currentJuriRoom,
                    'NamaMateriGrup' => $grupMateri['NamaMateriGrup']
                ],
                'materi' => $transformedMateriData,
                'error_categories' => $errorCategoriesByKategori,
                'nilaiExists' => $nilaiExists,
                'existingNilai' => $existingNilai,
                'roomValidation' => $roomValidationInfo
            ];

            return $this->response->setJSON([
                'success' => true,
                'status' => 'SUCCESS',
                'code' => 'PESERTA_FOUND',
                'message' => 'Data peserta ditemukan',
                'data' => $responseData
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
     * Simpan nilai juri
     */
    public function simpanNilaiJuri()
    {
        try {
            $NoPeserta = $this->request->getPost('NoPeserta');
            $IdSantri = $this->request->getPost('IdSantri');
            $IdTpq = $this->request->getPost('IdTpq');
            $IdTahunAjaran = $this->request->getPost('IdTahunAjaran');
            $IdJuri = $this->request->getPost('IdJuri');
            $TypeUjian = $this->request->getPost('TypeUjian');
            $isEditMode = $this->request->getPost('isEditMode') === 'true';
            $nilaiData = $this->request->getPost('nilai');
            $catatanData = $this->request->getPost('catatan') ?? [];

            // Validasi parameter wajib
            if (empty($NoPeserta)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_NO_PESERTA',
                    'message' => 'Nomor peserta tidak boleh kosong',
                    'details' => 'Parameter NoPeserta harus diisi'
                ]);
            }

            if (empty($IdSantri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_ID_SANTRI',
                    'message' => 'ID Santri tidak boleh kosong',
                    'details' => 'Parameter IdSantri harus diisi'
                ]);
            }

            if (empty($IdTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_TAHUN_AJARAN',
                    'message' => 'Tahun ajaran tidak boleh kosong',
                    'details' => 'Parameter IdTahunAjaran harus diisi'
                ]);
            }

            if (empty($IdJuri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_ID_JURI',
                    'message' => 'ID Juri tidak boleh kosong',
                    'details' => 'Parameter IdJuri harus diisi'
                ]);
            }

            if (empty($nilaiData) || !is_array($nilaiData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_NILAI_DATA',
                    'message' => 'Data nilai tidak boleh kosong',
                    'details' => 'Parameter nilai harus berupa array dan tidak boleh kosong'
                ]);
            }

            // Validasi nilai
            foreach ($nilaiData as $idMateri => $nilai) {
                // Validasi format nilai
                if (!is_numeric($nilai)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'VALIDATION_ERROR',
                        'code' => 'INVALID_NILAI_FORMAT',
                        'message' => 'Format nilai tidak valid',
                        'details' => 'Nilai untuk materi ' . $idMateri . ' harus berupa angka'
                    ]);
                }

                $nilai = floatval($nilai);
                if ($nilai < 10 || $nilai > 99) {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'VALIDATION_ERROR',
                        'code' => 'NILAI_OUT_OF_RANGE',
                        'message' => 'Nilai di luar range yang diizinkan',
                        'details' => 'Nilai untuk materi ' . $idMateri . ' harus dalam range 10-99, nilai yang dimasukkan: ' . $nilai
                    ]);
                }
            }

            // Ambil data materi dari registrasi peserta untuk validasi
            $materiList = $this->munaqosahRegistrasiUjiModel->getMateriByNoPeserta($NoPeserta);

            if (empty($materiList)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'MATERI_NOT_FOUND',
                    'message' => 'Data materi tidak ditemukan',
                    'details' => 'Peserta ' . $NoPeserta . ' tidak memiliki materi yang terdaftar'
                ]);
            }

            $materiMap = [];
            foreach ($materiList as $materi) {
                $materiMap[$materi['IdMateri']] = $materi;
            }

            // Validasi apakah semua materi yang diminta ada dalam registrasi peserta
            $missingMateri = [];
            foreach (array_keys($nilaiData) as $idMateri) {
                if (!isset($materiMap[$idMateri])) {
                    $missingMateri[] = $idMateri;
                }
            }

            if (!empty($missingMateri)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATA_NOT_FOUND',
                    'code' => 'MATERI_MISSING',
                    'message' => 'Beberapa materi tidak ditemukan',
                    'details' => 'Materi dengan ID: ' . implode(', ', $missingMateri) . ' tidak terdaftar untuk peserta ' . $NoPeserta
                ]);
            }

            $this->db->transStart();

            if ($isEditMode) {
                // Mode edit: update nilai yang sudah ada
                foreach ($nilaiData as $idMateri => $nilai) {
                    if (isset($materiMap[$idMateri])) {
                        $materi = $materiMap[$idMateri];

                        $updateData = [
                            'Nilai' => floatval($nilai)
                        ];

                        // Tambahkan catatan jika ada
                        if (isset($catatanData[$idMateri]) && !empty($catatanData[$idMateri])) {
                            $updateData['Catatan'] = $catatanData[$idMateri];
                        }

                        $this->nilaiMunaqosahModel->where('NoPeserta', $NoPeserta)
                            ->where('IdTahunAjaran', $IdTahunAjaran)
                            ->where('IdJuri', $IdJuri)
                            ->where('TypeUjian', $TypeUjian)
                            ->where('IdMateri', $idMateri)
                            ->set($updateData)
                            ->update();
                    }
                }
            } else {
                // Mode baru: insert nilai baru

                // Ambil data juri untuk mendapatkan RoomId
                $juriData = $this->munaqosahJuriModel->getJuriByIdJuri($IdJuri);
                $currentJuriRoom = $juriData['RoomId'] ?? null;

                // === VALIDASI ROOM SEBELUM INSERT ===
                // Ambil grup materi dari data pertama di materiMap
                $firstMateri = reset($materiMap);
                $idGrupMateriUjian = $firstMateri['IdGrupMateriUjian'] ?? null;

                if ($idGrupMateriUjian) {
                    // Cek apakah room validation aktif untuk grup materi ini
                    $configIdTpq = $IdTpq ?? 'default';
                    $enableRoomValidation = $this->munaqosahKonfigurasiModel->getSetting(
                        $configIdTpq,
                        'EnableRoomValidation_' . $idGrupMateriUjian
                    );

                    if ($enableRoomValidation) {
                        // Cek apakah peserta sudah di-test di room lain untuk grup materi ini
                        $existingRoom = $this->nilaiMunaqosahModel->getPesertaRoomByGrupMateri(
                            $NoPeserta,
                            $idGrupMateriUjian,
                            $IdTahunAjaran,
                            $TypeUjian
                        );

                        if ($existingRoom && !empty($existingRoom['RoomId'])) {
                            // Jika room berbeda, tidak bisa proceed
                            if ($currentJuriRoom && $existingRoom['RoomId'] !== $currentJuriRoom) {
                                return $this->response->setJSON([
                                    'success' => false,
                                    'status' => 'ROOM_VALIDATION_ERROR',
                                    'code' => 'DIFFERENT_ROOM',
                                    'message' => 'Peserta sudah diuji di room lain',
                                    'details' => "Peserta {$NoPeserta} sudah diuji di room {$existingRoom['RoomId']}. Tidak bisa diuji di room {$currentJuriRoom}."
                                ]);
                            }
                        }

                        // Cek maksimal juri per room untuk grup materi ini
                        $maxJuriPerRoom = $this->munaqosahKonfigurasiModel->getSettingAsInt(
                            $configIdTpq,
                            'MaxJuriPerRoom_' . $idGrupMateriUjian,
                            2 // default 2 juri
                        );

                        $currentJuriCount = $this->nilaiMunaqosahModel->countJuriByPesertaGrupMateri(
                            $NoPeserta,
                            $idGrupMateriUjian,
                            $IdTahunAjaran,
                            $TypeUjian
                        );

                        if ($currentJuriCount >= $maxJuriPerRoom) {
                            return $this->response->setJSON([
                                'success' => false,
                                'status' => 'ROOM_VALIDATION_ERROR',
                                'code' => 'MAX_JURI_EXCEEDED',
                                'message' => 'Maksimal juri sudah tercapai',
                                'details' => "Peserta {$NoPeserta} sudah dinilai oleh {$currentJuriCount} juri (maksimal {$maxJuriPerRoom})."
                            ]);
                        }

                        // Cek apakah juri ini sudah pernah menilai peserta ini
                        $alreadyScored = $this->nilaiMunaqosahModel->checkJuriAlreadyScored(
                            $NoPeserta,
                            $IdJuri,
                            $idGrupMateriUjian,
                            $IdTahunAjaran,
                            $TypeUjian
                        );

                        if ($alreadyScored) {
                            return $this->response->setJSON([
                                'success' => false,
                                'status' => 'VALIDATION_ERROR',
                                'code' => 'ALREADY_SCORED',
                                'message' => 'Juri sudah menilai peserta ini',
                                'details' => "Juri {$IdJuri} sudah menilai peserta {$NoPeserta} sebelumnya."
                            ]);
                        }
                    }
                }

                $nilaiRecords = [];
                foreach ($nilaiData as $idMateri => $nilai) {
                    if (isset($materiMap[$idMateri])) {
                        $materi = $materiMap[$idMateri];

                        $catatan = '';
                        if (isset($catatanData[$idMateri]) && !empty($catatanData[$idMateri])) {
                            $catatan = $catatanData[$idMateri];
                        }

                        $nilaiRecords[] = [
                            'NoPeserta' => $NoPeserta,
                            'IdSantri' => $IdSantri,
                            'IdTpq' => $IdTpq,
                            'IdTahunAjaran' => $IdTahunAjaran,
                            'IdJuri' => $IdJuri,
                            'IdMateri' => $idMateri,
                            'IdGrupMateriUjian' => $materi['IdGrupMateriUjian'],
                            'RoomId' => $currentJuriRoom, // Tambahkan RoomId
                            'IdKategoriMateri' => $materi['IdKategoriMateri'] ?? null,
                            'TypeUjian' => $TypeUjian,
                            'Nilai' => floatval($nilai),
                            'Catatan' => $catatan
                        ];
                    }
                }

                if (!empty($nilaiRecords)) {
                    $this->nilaiMunaqosahModel->insertBatch($nilaiRecords);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'DATABASE_ERROR',
                    'code' => 'TRANSACTION_FAILED',
                    'message' => 'Gagal menyimpan data nilai',
                    'details' => 'Transaksi database gagal, data tidak tersimpan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'SUCCESS',
                'code' => $isEditMode ? 'NILAI_UPDATED' : 'NILAI_SAVED',
                'message' => $isEditMode ? 'Nilai berhasil diperbarui' : 'Nilai berhasil disimpan',
                'details' => 'Data nilai telah ' . ($isEditMode ? 'diperbarui' : 'disimpan') . ' dengan sukses'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in simpanNilaiJuri: ' . $e->getMessage());
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
     * Verify admin credentials for editing nilai
     */
    public function verifyAdminCredentials()
    {
        try {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Validasi parameter input
            if (empty($username)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_USERNAME',
                    'message' => 'Username tidak boleh kosong',
                    'details' => 'Parameter username harus diisi'
                ]);
            }

            if (empty($password)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'MISSING_PASSWORD',
                    'message' => 'Password tidak boleh kosong',
                    'details' => 'Parameter password harus diisi'
                ]);
            }

            // Validasi format username
            if (strlen($username) < 3) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'code' => 'INVALID_USERNAME_LENGTH',
                    'message' => 'Username terlalu pendek',
                    'details' => 'Username harus minimal 3 karakter'
                ]);
            }

            // Cek apakah user ada di database
            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('username', $username)->first();

            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'AUTHENTICATION_ERROR',
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'Username tidak ditemukan',
                    'details' => 'User dengan username ' . $username . ' tidak ditemukan di database'
                ]);
            }

            // Cek apakah user aktif
            if (!$user['active']) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'AUTHENTICATION_ERROR',
                    'code' => 'USER_INACTIVE',
                    'message' => 'User tidak aktif',
                    'details' => 'User dengan username ' . $username . ' tidak aktif'
                ]);
            }

            // Cek apakah user adalah admin (IdTpq = 0 atau null)
            if (!empty($user['IdTpq']) && $user['IdTpq'] != 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'AUTHORIZATION_ERROR',
                    'code' => 'NOT_ADMIN',
                    'message' => 'Hanya admin yang dapat mengedit nilai',
                    'details' => 'User dengan username ' . $username . ' bukan admin (IdTpq: ' . $user['IdTpq'] . ')'
                ]);
            }

            // Verify password
            if (!Password::verify($password, $user['password_hash'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'AUTHENTICATION_ERROR',
                    'code' => 'INVALID_PASSWORD',
                    'message' => 'Password tidak sesuai',
                    'details' => 'Password yang dimasukkan tidak sesuai dengan password user'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'status' => 'SUCCESS',
                'code' => 'ADMIN_VERIFIED',
                'message' => 'Kredensial admin valid',
                'details' => 'Admin dengan username ' . $username . ' berhasil diverifikasi'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in verifyAdminCredentials: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'SYSTEM_ERROR',
                'code' => 'INTERNAL_ERROR',
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    // ==================== ANTRIAN MUNAQOSAH ====================

    public function antrian()
    {
        $currentTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        $selectedTahun = $this->request->getGet('tahun') ?? $currentTahunAjaran;
        $selectedType = $this->request->getGet('type') ?? 'pra-munaqosah';
        $selectedGroup = $this->request->getGet('group');

        // Ambil IdTpq dari session (untuk admin TPQ)
        $sessionIdTpq = session()->get('IdTpq');
        $selectedTpq = $this->request->getGet('tpq');

        // Jika user login sebagai admin TPQ
        if (!empty($sessionIdTpq)) {
            // Set IdTpq dari session jika belum ada filter
            if (empty($selectedTpq)) {
                $selectedTpq = $sessionIdTpq;
            }
            // Set TypeUjian ke 'pra-munaqosah' secara otomatis
            $selectedType = 'pra-munaqosah';
            // Set TahunAjaran ke tahun saat ini
            $selectedTahun = $currentTahunAjaran;
        } else {
            // Admin super: ambil dari parameter GET
            $selectedType = $this->request->getGet('type') ?? 'pra-munaqosah';
            $selectedTahun = $this->request->getGet('tahun') ?? $currentTahunAjaran;
        }

        // Get list TPQ untuk dropdown (jika admin super)
        $tpqList = [];
        if (empty($sessionIdTpq)) {
            // Jika admin super (tidak ada IdTpq di session), tampilkan semua TPQ
            $tpqList = $this->tpqModel->GetData();
        } else {
            // Jika admin TPQ, hanya tampilkan TPQ yang sesuai
            $tpqList = $this->tpqModel->GetData($sessionIdTpq);
            if (!empty($tpqList) && !is_array($tpqList)) {
                $tpqList = [$tpqList];
            }
        }

        $grupList = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();

        if (!$selectedGroup && !empty($grupList)) {
            $selectedGroup = $grupList[0]['IdGrupMateriUjian'];
        }

        $typeOptions = [
            'pra-munaqosah' => 'Pra Munaqosah',
            'munaqosah' => 'Munaqosah'
        ];

        $filters = [
            'IdTahunAjaran' => $selectedTahun,
            'IdGrupMateriUjian' => $selectedGroup,
            'TypeUjian' => $selectedType,
        ];

        // Tambahkan filter IdTpq jika ada
        if (!empty($selectedTpq)) {
            $filters['IdTpq'] = $selectedTpq;
        }

        $queue = $this->antrianMunaqosahModel->getQueueWithDetails($filters);
        $statusCounts = $this->antrianMunaqosahModel->getStatusCounts($filters);

        $totalPeserta = array_sum($statusCounts);
        $totalSelesai = $statusCounts[2] ?? 0;
        $totalProses = $statusCounts[1] ?? 0;
        $totalMenunggu = $statusCounts[0] ?? 0;
        $totalAntrianAktif = max($totalPeserta - $totalSelesai, 0);
        $progressPersentase = $totalPeserta > 0 ? round(($totalSelesai / $totalPeserta) * 100) : 0;

        $rooms = [];
        $availableRooms = [];

        if ($selectedGroup) {
            // Ambil kapasitas maksimal ruangan dari konfigurasi berdasarkan grup materi
            $configIdTpq = $selectedTpq ?? '0';
            $settingKey = 'KapasitasRuanganMaksimal_' . $selectedGroup;
            $kapasitasMaksimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, $settingKey, 1);
            if ($kapasitasMaksimal <= 0) {
                $kapasitasMaksimal = 1;
            }

            $roomRows = $this->munaqosahJuriModel->getRoomsByGrupAndType($selectedGroup, $selectedType, $selectedTpq);
            $roomStatuses = [];

            foreach ($roomRows as $roomRow) {
                $roomId = $roomRow['RoomId'];
                $roomStatuses[$roomId] = [
                    'RoomId' => $roomId,
                    'occupied' => false,
                    'participant_count' => 0,
                    'participants' => [],
                    'max_capacity' => $kapasitasMaksimal,
                    'is_full' => false,
                ];
            }

            // Hitung jumlah peserta per ruangan
            foreach ($queue as $row) {
                if ((int) ($row['Status'] ?? 0) === 1 && !empty($row['RoomId'])) {
                    $roomId = $row['RoomId'];
                    if (!isset($roomStatuses[$roomId])) {
                        $roomStatuses[$roomId] = [
                            'RoomId' => $roomId,
                            'occupied' => false,
                            'participant_count' => 0,
                            'participants' => [],
                            'max_capacity' => $kapasitasMaksimal,
                            'is_full' => false,
                        ];
                    }

                    $roomStatuses[$roomId]['participant_count']++;
                    $roomStatuses[$roomId]['participants'][] = $row;
                    $roomStatuses[$roomId]['occupied'] = true;

                    // Tandai ruangan penuh jika mencapai kapasitas maksimal
                    if ($roomStatuses[$roomId]['participant_count'] >= $kapasitasMaksimal) {
                        $roomStatuses[$roomId]['is_full'] = true;
                    }
                }
            }

            $rooms = array_values($roomStatuses);

            foreach ($roomStatuses as $roomStatus) {
                // Ruangan tersedia jika belum penuh
                if (!$roomStatus['is_full']) {
                    $availableRooms[] = $roomStatus['RoomId'];
                }
            }
        }

        $data = [
            'page_title' => 'Data Antrian Munaqosah',
            'queue' => $queue,
            'groups' => $grupList,
            'selected_group' => $selectedGroup,
            'types' => $typeOptions,
            'selected_type' => $selectedType,
            'selected_tahun' => $selectedTahun,
            'selected_tpq' => $selectedTpq ?? '',
            'tpq_list' => $tpqList,
            'session_id_tpq' => $sessionIdTpq ?? '',
            'current_tahun' => $currentTahunAjaran,
            'rooms' => $rooms,
            'available_rooms' => $availableRooms,
            'statistics' => [
                'total' => $totalPeserta,
                'completed' => $totalSelesai,
                'waiting' => $totalMenunggu,
                'in_progress' => $totalProses,
                'queueing' => $totalAntrianAktif,
                'progress' => $progressPersentase,
            ],
        ];

        return view('backend/Munaqosah/listAntrian', $data);
    }

    public function registerAntrianAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $noPeserta = trim($this->request->getPost('NoPeserta'));
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $idGrupMateri = $this->request->getPost('IdGrupMateriUjian');
        $typeUjian = $this->request->getPost('TypeUjian');

        // Validasi input
        if (empty($noPeserta)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor peserta tidak boleh kosong'
            ]);
        }

        if (empty($idTahunAjaran)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tahun ajaran tidak boleh kosong'
            ]);
        }

        if (empty($idGrupMateri)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Grup materi ujian tidak boleh kosong'
            ]);
        }

        if (empty($typeUjian)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Type ujian tidak boleh kosong'
            ]);
        }

        // Cek apakah peserta terdaftar untuk grup materi dan tipe ujian yang dipilih
        $registrasi = $this->munaqosahRegistrasiUjiModel
            ->where('NoPeserta', $noPeserta)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('IdGrupMateriUjian', $idGrupMateri)
            ->where('TypeUjian', $typeUjian)
            ->first();

        if (!$registrasi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Peserta tidak terdaftar untuk grup materi dan tipe ujian yang dipilih.'
            ]);
        }

        // Ambil IdTpq dan IdSantri dari registrasi
        $idTpq = $registrasi['IdTpq'] ?? null;
        $idSantri = $registrasi['IdSantri'] ?? null;
        $kategoriId = $registrasi['IdKategoriMateri'] ?? null;

        // Ambil GroupPeserta dari jadwal ujian berdasarkan IdTpq, IdTahunAjaran, dan TypeUjian
        // Jika tidak ditemukan, default ke 'Group 1'
        $groupPeserta = 'Group 1'; // Default
        if (!empty($idTpq)) {
            $groupPeserta = $this->munaqosahJadwalUjianModel->getGroupPesertaByTpq($idTpq, $idTahunAjaran, $typeUjian);
        }

        // Cek apakah peserta sudah ada di antrian aktif dengan menggunakan IdTpq juga
        $existing = $this->antrianMunaqosahModel
            ->where('NoPeserta', $noPeserta)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('IdGrupMateriUjian', $idGrupMateri)
            ->where('TypeUjian', $typeUjian)
            ->whereIn('Status', [0, 1]);

        // Tambahkan filter IdTpq jika tersedia untuk check yang lebih akurat
        if (!empty($idTpq)) {
            $existing->where('IdTpq', $idTpq);
        }

        $existing = $existing->first();

        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Peserta sudah berada di antrian aktif untuk grup dan tipe ujian tersebut.'
            ]);
        }

        // Simpan data antrian
        $data = [
            'NoPeserta' => $noPeserta,
            'IdTahunAjaran' => $idTahunAjaran,
            'IdGrupMateriUjian' => $idGrupMateri,
            'TypeUjian' => $typeUjian,
            'IdKategoriMateri' => $kategoriId,
            'IdTpq' => $idTpq,
            'IdSantri' => $idSantri,
            'Status' => 0,
            'RoomId' => null,
            'Keterangan' => null,
            'GroupPeserta' => $groupPeserta
        ];

        if ($this->antrianMunaqosahModel->save($data)) {
            // Ambil data peserta untuk response
            $builder = $this->db->table('tbl_munaqosah_registrasi_uji r');
            $builder->select('r.NoPeserta, s.NamaSantri');
            $builder->join('tbl_santri_baru s', 's.IdSantri = r.IdSantri', 'left');
            $builder->where('r.NoPeserta', $noPeserta);
            $pesertaData = $builder->get()->getRowArray();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peserta berhasil diregistrasi ke antrian',
                'data' => [
                    'NoPeserta' => $noPeserta,
                    'NamaSantri' => $pesertaData['NamaSantri'] ?? '-'
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan data antrian: ' . implode(', ', $this->antrianMunaqosahModel->errors())
        ]);
    }

    public function autoAssignRoomAjax($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $antrian = $this->antrianMunaqosahModel->find($id);

        if (!$antrian) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data antrian tidak ditemukan'
            ]);
        }

        // Ambil filter dari antrian
        $idGrupMateri = $antrian['IdGrupMateriUjian'] ?? null;
        $typeUjian = $antrian['TypeUjian'] ?? null;
        $idTpq = $antrian['IdTpq'] ?? null;

        // Cari ruangan yang tersedia berdasarkan filter
        $availableRooms = $this->munaqosahJuriModel->getRoomsByGrupAndType($idGrupMateri, $typeUjian, $idTpq);

        if (empty($availableRooms)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada ruangan tersedia untuk grup materi dan tipe ujian ini'
            ]);
        }

        // Ambil kapasitas maksimal ruangan dari konfigurasi berdasarkan grup materi
        $configIdTpq = $idTpq ?? '0';
        $settingKey = !empty($idGrupMateri) ? 'KapasitasRuanganMaksimal_' . $idGrupMateri : 'KapasitasRuanganMaksimal';
        $kapasitasMaksimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, $settingKey, 1);

        // Jika kapasitas tidak di-set atau 0, default ke 1 (satu peserta per ruangan)
        if ($kapasitasMaksimal <= 0) {
            $kapasitasMaksimal = 1;
        }

        // Hitung jumlah peserta per ruangan yang sedang digunakan (Status = 1)
        $occupiedQuery = $this->antrianMunaqosahModel
            ->where('Status', 1)
            ->where('RoomId IS NOT NULL')
            ->where('RoomId !=', '');

        if (!empty($idGrupMateri)) {
            $occupiedQuery->where('IdGrupMateriUjian', $idGrupMateri);
        }

        if (!empty($typeUjian)) {
            $occupiedQuery->where('TypeUjian', $typeUjian);
        }

        if (!empty($idTpq)) {
            $occupiedQuery->where('IdTpq', $idTpq);
        }

        $occupiedData = $occupiedQuery->findAll();

        // Hitung jumlah peserta per ruangan
        $roomOccupancy = [];
        foreach ($occupiedData as $row) {
            if (!empty($row['RoomId'])) {
                $roomId = $row['RoomId'];
                if (!isset($roomOccupancy[$roomId])) {
                    $roomOccupancy[$roomId] = 0;
                }
                $roomOccupancy[$roomId]++;
            }
        }

        // Cari ruangan pertama yang masih memiliki kapasitas
        $selectedRoom = null;
        foreach ($availableRooms as $room) {
            $roomId = $room['RoomId'];
            $currentOccupancy = $roomOccupancy[$roomId] ?? 0;

            // Jika ruangan masih memiliki kapasitas (belum penuh)
            if ($currentOccupancy < $kapasitasMaksimal) {
                $selectedRoom = $roomId;
                break;
            }
        }

        if (empty($selectedRoom)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semua ruangan sudah mencapai kapasitas maksimal'
            ]);
        }

        // Assign ruangan ke antrian
        if ($this->antrianMunaqosahModel->updateStatus($id, 1, $selectedRoom)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Peserta masuk ke ruangan {$selectedRoom}",
                'data' => [
                    'roomId' => $selectedRoom
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengassign ruangan'
        ]);
    }

    public function updateStatusAntrianAjax($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request harus menggunakan AJAX'
            ]);
        }

        $status = (int) $this->request->getPost('status');

        $antrian = $this->antrianMunaqosahModel->find($id);

        if (!$antrian) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data antrian tidak ditemukan'
            ]);
        }

        // Update status tanpa room (untuk selesai atau keluar)
        if (in_array($status, [0, 2], true)) {
            $message = $status === 0 ? 'Peserta dikembalikan ke status menunggu.' : 'Peserta selesai mengikuti ujian.';

            if ($this->antrianMunaqosahModel->updateStatus($id, $status, null)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate status antrian'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Status tidak valid'
        ]);
    }

    public function updateStatusAntrian($id)
    {
        $status = (int) $this->request->getPost('status');
        $roomId = $this->request->getPost('room_id');

        $antrian = $this->antrianMunaqosahModel->find($id);

        if (!$antrian) {
            return redirect()->to('/backend/munaqosah/antrian')->with('error', 'Data antrian tidak ditemukan.');
        }

        if ($status === 1) {
            if (empty($roomId)) {
                return redirect()->back()->with('error', 'Pilih ruangan terlebih dahulu sebelum peserta masuk.');
            }

            $roomId = trim($roomId);

            $occupiedQuery = $this->antrianMunaqosahModel
                ->where('RoomId', $roomId)
                ->where('Status', 1);

            if (!empty($antrian['IdGrupMateriUjian'])) {
                $occupiedQuery->where('IdGrupMateriUjian', $antrian['IdGrupMateriUjian']);
            }

            if (!empty($antrian['TypeUjian'])) {
                $occupiedQuery->where('TypeUjian', $antrian['TypeUjian']);
            }

            // Gunakan IdTpq untuk check RoomId yang lebih akurat
            if (!empty($antrian['IdTpq'])) {
                $occupiedQuery->where('IdTpq', $antrian['IdTpq']);
            }

            $occupied = $occupiedQuery->first();

            if ($occupied && (int) $occupied['id'] !== (int) $id) {
                return redirect()->back()->with('error', "Ruangan {$roomId} sedang digunakan peserta lain.");
            }

            if ($this->antrianMunaqosahModel->updateStatus($id, 1, $roomId)) {
                return redirect()->to('/backend/munaqosah/antrian')->with('success', "Peserta masuk ke {$roomId}.");
            }

            return redirect()->back()->with('error', 'Gagal mengupdate status antrian.');
        }

        if (in_array($status, [0, 2], true)) {
            $message = $status === 0 ? 'Peserta dikembalikan ke status menunggu.' : 'Peserta selesai mengikuti ujian.';

            if ($this->antrianMunaqosahModel->updateStatus($id, $status, null)) {
                return redirect()->to('/backend/munaqosah/antrian')->with('success', $message);
            }

            return redirect()->back()->with('error', 'Gagal mengupdate status antrian.');
        }

        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    public function deleteAntrian($id)
    {
        if ($this->antrianMunaqosahModel->delete($id)) {
            return redirect()->to('/backend/munaqosah/antrian')->with('success', 'Data antrian berhasil dihapus');
        } else {
            return redirect()->to('/backend/munaqosah/antrian')->with('error', 'Gagal menghapus data antrian');
        }
    }

    // ==================== MONITORING STATUS ANTRIAN ====================

    public function monitoringStatusAntrian()
    {
        $currentTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        $selectedTahun = $this->request->getGet('tahun') ?? $currentTahunAjaran;
        $selectedType = $this->request->getGet('type') ?? 'pra-munaqosah';
        $selectedGroup = $this->request->getGet('group');
        $refreshInterval = (int)($this->request->getGet('interval') ?? 5); // Default 5 detik

        // Ambil IdTpq dari session (untuk admin TPQ)
        $sessionIdTpq = session()->get('IdTpq');
        $selectedTpq = $this->request->getGet('tpq');

        // Jika user login sebagai admin TPQ
        if (!empty($sessionIdTpq)) {
            if (empty($selectedTpq)) {
                $selectedTpq = $sessionIdTpq;
            }
            $selectedType = 'pra-munaqosah';
            $selectedTahun = $currentTahunAjaran;
        } else {
            $selectedType = $this->request->getGet('type') ?? 'pra-munaqosah';
            $selectedTahun = $this->request->getGet('tahun') ?? $currentTahunAjaran;
        }

        // Get list TPQ untuk dropdown (jika admin super)
        $tpqList = [];
        if (empty($sessionIdTpq)) {
            $tpqList = $this->tpqModel->GetData();
        } else {
            $tpqList = $this->tpqModel->GetData($sessionIdTpq);
            if (!empty($tpqList) && !is_array($tpqList)) {
                $tpqList = [$tpqList];
            }
        }

        $grupList = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();

        if (!$selectedGroup && !empty($grupList)) {
            $selectedGroup = $grupList[0]['IdGrupMateriUjian'];
        }

        $typeOptions = [
            'pra-munaqosah' => 'Pra Munaqosah',
            'munaqosah' => 'Munaqosah'
        ];

        $filters = [
            'IdTahunAjaran' => $selectedTahun,
            'IdGrupMateriUjian' => $selectedGroup,
            'TypeUjian' => $selectedType,
        ];

        if (!empty($selectedTpq)) {
            $filters['IdTpq'] = $selectedTpq;
        }

        $queue = $this->antrianMunaqosahModel->getQueueWithDetails($filters);
        $statusCounts = $this->antrianMunaqosahModel->getStatusCounts($filters);

        $totalPeserta = array_sum($statusCounts);
        $totalSelesai = $statusCounts[2] ?? 0;
        $totalProses = $statusCounts[1] ?? 0;
        $totalMenunggu = $statusCounts[0] ?? 0;
        $totalAntrianAktif = max($totalPeserta - $totalSelesai, 0);
        $progressPersentase = $totalPeserta > 0 ? round(($totalSelesai / $totalPeserta) * 100) : 0;

        $rooms = [];
        $availableRooms = [];

        if ($selectedGroup) {
            // Ambil kapasitas maksimal ruangan dari konfigurasi berdasarkan grup materi
            $configIdTpq = $selectedTpq ?? '0';
            $settingKey = 'KapasitasRuanganMaksimal_' . $selectedGroup;
            $kapasitasMaksimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, $settingKey, 1);
            if ($kapasitasMaksimal <= 0) {
                $kapasitasMaksimal = 1;
            }

            $roomRows = $this->munaqosahJuriModel->getRoomsByGrupAndType($selectedGroup, $selectedType, $selectedTpq);
            $roomStatuses = [];

            foreach ($roomRows as $roomRow) {
                $roomId = $roomRow['RoomId'];
                $roomStatuses[$roomId] = [
                    'RoomId' => $roomId,
                    'occupied' => false,
                    'participant_count' => 0,
                    'participants' => [],
                    'max_capacity' => $kapasitasMaksimal,
                    'is_full' => false,
                ];
            }

            // Hitung jumlah peserta per ruangan
            foreach ($queue as $row) {
                if ((int) ($row['Status'] ?? 0) === 1 && !empty($row['RoomId'])) {
                    $roomId = $row['RoomId'];
                    if (!isset($roomStatuses[$roomId])) {
                        $roomStatuses[$roomId] = [
                            'RoomId' => $roomId,
                            'occupied' => false,
                            'participant_count' => 0,
                            'participants' => [],
                            'max_capacity' => $kapasitasMaksimal,
                            'is_full' => false,
                        ];
                    }

                    $roomStatuses[$roomId]['participant_count']++;
                    $roomStatuses[$roomId]['participants'][] = $row;
                    $roomStatuses[$roomId]['occupied'] = true;

                    // Tandai ruangan penuh jika mencapai kapasitas maksimal
                    if ($roomStatuses[$roomId]['participant_count'] >= $kapasitasMaksimal) {
                        $roomStatuses[$roomId]['is_full'] = true;
                    }
                }
            }

            $rooms = array_values($roomStatuses);

            foreach ($roomStatuses as $roomStatus) {
                // Ruangan tersedia jika belum penuh
                if (!$roomStatus['is_full']) {
                    $availableRooms[] = $roomStatus['RoomId'];
                }
            }
        }

        $data = [
            'page_title' => 'Monitoring Status Antrian',
            'queue' => $queue,
            'groups' => $grupList,
            'selected_group' => $selectedGroup,
            'types' => $typeOptions,
            'selected_type' => $selectedType,
            'selected_tahun' => $selectedTahun,
            'selected_tpq' => $selectedTpq ?? '',
            'tpq_list' => $tpqList,
            'session_id_tpq' => $sessionIdTpq ?? '',
            'current_tahun' => $currentTahunAjaran,
            'rooms' => $rooms,
            'available_rooms' => $availableRooms,
            'refresh_interval' => $refreshInterval,
            'statistics' => [
                'total' => $totalPeserta,
                'completed' => $totalSelesai,
                'waiting' => $totalMenunggu,
                'in_progress' => $totalProses,
                'queueing' => $totalAntrianAktif,
                'progress' => $progressPersentase,
            ],
        ];

        return view('backend/Munaqosah/monitoringStatusAntrian', $data);
    }

    // ==================== INPUT REGISTRASI ANTRIAN ====================

    public function inputRegistrasiAntrian()
    {
        $currentTahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        $selectedTahun = $this->request->getGet('tahun') ?? $currentTahunAjaran;
        $selectedType = $this->request->getGet('type') ?? 'pra-munaqosah';
        $selectedGroup = $this->request->getGet('group');

        // Ambil IdTpq dari session (untuk admin TPQ)
        $sessionIdTpq = session()->get('IdTpq');
        $selectedTpq = $this->request->getGet('tpq');

        // Jika user login sebagai admin TPQ
        if (!empty($sessionIdTpq)) {
            if (empty($selectedTpq)) {
                $selectedTpq = $sessionIdTpq;
            }
            $selectedType = 'pra-munaqosah';
            $selectedTahun = $currentTahunAjaran;
        } else {
            $selectedType = $this->request->getGet('type') ?? 'pra-munaqosah';
            $selectedTahun = $this->request->getGet('tahun') ?? $currentTahunAjaran;
        }

        // Get list TPQ untuk dropdown (jika admin super)
        $tpqList = [];
        if (empty($sessionIdTpq)) {
            $tpqList = $this->tpqModel->GetData();
        } else {
            $tpqList = $this->tpqModel->GetData($sessionIdTpq);
            if (!empty($tpqList) && !is_array($tpqList)) {
                $tpqList = [$tpqList];
            }
        }

        $grupList = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();

        if (!$selectedGroup && !empty($grupList)) {
            $selectedGroup = $grupList[0]['IdGrupMateriUjian'];
        }

        $typeOptions = [
            'pra-munaqosah' => 'Pra Munaqosah',
            'munaqosah' => 'Munaqosah'
        ];

        $filters = [
            'IdTahunAjaran' => $selectedTahun,
            'IdGrupMateriUjian' => $selectedGroup,
            'TypeUjian' => $selectedType,
        ];

        if (!empty($selectedTpq)) {
            $filters['IdTpq'] = $selectedTpq;
        }

        $queue = $this->antrianMunaqosahModel->getQueueWithDetails($filters);
        $statusCounts = $this->antrianMunaqosahModel->getStatusCounts($filters);

        $totalPeserta = array_sum($statusCounts);
        $totalSelesai = $statusCounts[2] ?? 0;
        $totalProses = $statusCounts[1] ?? 0;
        $totalMenunggu = $statusCounts[0] ?? 0;
        $totalAntrianAktif = max($totalPeserta - $totalSelesai, 0);
        $progressPersentase = $totalPeserta > 0 ? round(($totalSelesai / $totalPeserta) * 100) : 0;

        $rooms = [];
        $availableRooms = [];

        if ($selectedGroup) {
            // Ambil kapasitas maksimal ruangan dari konfigurasi berdasarkan grup materi
            $configIdTpq = $selectedTpq ?? '0';
            $settingKey = 'KapasitasRuanganMaksimal_' . $selectedGroup;
            $kapasitasMaksimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, $settingKey, 1);
            if ($kapasitasMaksimal <= 0) {
                $kapasitasMaksimal = 1;
            }

            $roomRows = $this->munaqosahJuriModel->getRoomsByGrupAndType($selectedGroup, $selectedType, $selectedTpq);
            $roomStatuses = [];

            foreach ($roomRows as $roomRow) {
                $roomId = $roomRow['RoomId'];
                $roomStatuses[$roomId] = [
                    'RoomId' => $roomId,
                    'occupied' => false,
                    'participant_count' => 0,
                    'participants' => [],
                    'max_capacity' => $kapasitasMaksimal,
                    'is_full' => false,
                ];
            }

            // Hitung jumlah peserta per ruangan
            foreach ($queue as $row) {
                if ((int) ($row['Status'] ?? 0) === 1 && !empty($row['RoomId'])) {
                    $roomId = $row['RoomId'];
                    if (!isset($roomStatuses[$roomId])) {
                        $roomStatuses[$roomId] = [
                            'RoomId' => $roomId,
                            'occupied' => false,
                            'participant_count' => 0,
                            'participants' => [],
                            'max_capacity' => $kapasitasMaksimal,
                            'is_full' => false,
                        ];
                    }

                    $roomStatuses[$roomId]['participant_count']++;
                    $roomStatuses[$roomId]['participants'][] = $row;
                    $roomStatuses[$roomId]['occupied'] = true;

                    // Tandai ruangan penuh jika mencapai kapasitas maksimal
                    if ($roomStatuses[$roomId]['participant_count'] >= $kapasitasMaksimal) {
                        $roomStatuses[$roomId]['is_full'] = true;
                    }
                }
            }

            $rooms = array_values($roomStatuses);

            foreach ($roomStatuses as $roomStatus) {
                // Ruangan tersedia jika belum penuh
                if (!$roomStatus['is_full']) {
                    $availableRooms[] = $roomStatus['RoomId'];
                }
            }
        }

        $data = [
            'page_title' => 'Input Registrasi Antrian',
            'queue' => $queue,
            'groups' => $grupList,
            'selected_group' => $selectedGroup,
            'types' => $typeOptions,
            'selected_type' => $selectedType,
            'selected_tahun' => $selectedTahun,
            'selected_tpq' => $selectedTpq ?? '',
            'tpq_list' => $tpqList,
            'session_id_tpq' => $sessionIdTpq ?? '',
            'current_tahun' => $currentTahunAjaran,
            'rooms' => $rooms,
            'available_rooms' => $availableRooms,
            'statistics' => [
                'total' => $totalPeserta,
                'completed' => $totalSelesai,
                'waiting' => $totalMenunggu,
                'in_progress' => $totalProses,
                'queueing' => $totalAntrianAktif,
                'progress' => $progressPersentase,
            ],
        ];

        return view('backend/Munaqosah/inputRegistrasiAntrian', $data);
    }

    // ==================== BOBOT NILAI ====================

    public function bobotNilai()
    {
        // Ambil semua data bobot nilai beserta nama kategori
        $bobotData = $this->bobotNilaiMunaqosahModel->getBobotWithKategori();

        // Siapkan daftar kategori (id => nama) untuk kebutuhan tampilan/JS
        $kategoriList = [];
        foreach ($bobotData as $row) {
            if (!empty($row['IdKategoriMateri'])) {
                $kategoriList[$row['IdKategoriMateri']] = $row['NamaKategoriMateri'] ?? $row['IdKategoriMateri'];
            }
        }

        // Jika belum ada data bobot, gunakan daftar kategori aktif sebagai fallback
        if (empty($kategoriList)) {
            $kategoriAktif = $this->munaqosahKategoriModel
                ->select('IdKategoriMateri, NamaKategoriMateri')
                ->where('Status', 'Aktif')
                ->orderBy('NamaKategoriMateri', 'ASC')
                ->findAll();

            foreach ($kategoriAktif as $kategori) {
                $kategoriList[$kategori['IdKategoriMateri']] = $kategori['NamaKategoriMateri'];
            }
        }

        $data = [
            'page_title' => 'Data Bobot Nilai Munaqosah',
            'bobot' => $bobotData,
            'kategoriList' => $kategoriList
        ];
        return view('backend/Munaqosah/listBobotNilai', $data);
    }

    public function saveBobotNilai()
    {
        $rules = [
            'IdTahunAjaran' => 'required',
            'IdKategoriMateri' => 'required',
            'NilaiBobot' => 'required|decimal|greater_than[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $idKategori = strtoupper($this->request->getPost('IdKategoriMateri'));
        $kategoriExists = $this->munaqosahKategoriModel
            ->where('IdKategoriMateri', $idKategori)
            ->first();

        if (!$kategoriExists) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori materi tidak ditemukan'
            ]);
        }

        $data = [
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdKategoriMateri' => $idKategori,
            'NilaiBobot' => $this->request->getPost('NilaiBobot')
        ];

        if ($this->bobotNilaiMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data bobot nilai berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->bobotNilaiMunaqosahModel->errors()
            ]);
        }
    }

    public function updateBobotNilai($id)
    {
        $rules = [
            'IdTahunAjaran' => 'required',
            'IdKategoriMateri' => 'required',
            'NilaiBobot' => 'required|decimal|greater_than[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $idKategori = strtoupper($this->request->getPost('IdKategoriMateri'));
        $kategoriExists = $this->munaqosahKategoriModel
            ->where('IdKategoriMateri', $idKategori)
            ->first();

        if (!$kategoriExists) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kategori materi tidak ditemukan'
            ]);
        }

        $data = [
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdKategoriMateri' => $idKategori,
            'NilaiBobot' => $this->request->getPost('NilaiBobot')
        ];

        if ($this->bobotNilaiMunaqosahModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data bobot nilai berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->bobotNilaiMunaqosahModel->errors()
            ]);
        }
    }

    public function deleteBobotNilai($id)
    {
        if ($this->bobotNilaiMunaqosahModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data bobot nilai berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    // ==================== PESERTA MUNAQOSAH ====================

    public function pesertaMunaqosah()
    {
        // ambil tahun ajaran saat ini
        $tahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();
        // IdTpq dari session
        $idTpq = session()->get('IdTpq');
        // DataKelas dari help function model
        $dataKelas = $this->helpFunction->getDataKelas();
        $dataTpq = $this->helpFunction->getDataTpq($idTpq);
        $peserta = $this->pesertaMunaqosahModel->getPesertaWithRelations($idTpq);
        $data = [
            'page_title' => 'Data Peserta Munaqosah',
            'peserta' => $peserta,
            'dataKelas' => $dataKelas,
            'dataTpq' => $dataTpq,
            'tahunAjaran' => $tahunAjaran
        ];
        return view('backend/Munaqosah/listPesertaMunaqosah', $data);
    }

    public function savePesertaMunaqosah()
    {
        $rules = [
            'IdSantri' => 'required',
            'IdTpq' => 'required',
            'IdTahunAjaran' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Cek apakah santri sudah terdaftar
        if ($this->pesertaMunaqosahModel->isPesertaExists(
            $this->request->getPost('IdSantri'),
            $this->request->getPost('IdTahunAjaran')
        )) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Santri sudah terdaftar sebagai peserta munaqosah'
            ]);
        }

        $data = [
            'IdSantri' => $this->request->getPost('IdSantri'),
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran')
        ];

        if ($this->pesertaMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data peserta berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->pesertaMunaqosahModel->errors()
            ]);
        }
    }

    public function savePesertaMunaqosahMultiple()
    {
        $rules = [
            'santri_ids' => 'required',
            'IdTpq' => 'required',
            'IdTahunAjaran' => 'required'
        ];

        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            $detailedErrors = [];
            
            // Detail error untuk setiap field
            foreach ($validationErrors as $field => $error) {
                switch ($field) {
                    case 'santri_ids':
                        $detailedErrors[] = "Field 'santri_ids' tidak boleh kosong. Pastikan Anda telah memilih minimal satu santri.";
                        break;
                    case 'IdTpq':
                        $detailedErrors[] = "Field 'IdTpq' tidak boleh kosong. Pastikan TPQ telah dipilih.";
                        break;
                    case 'IdTahunAjaran':
                        $detailedErrors[] = "Field 'IdTahunAjaran' tidak boleh kosong. Pastikan tahun ajaran telah dipilih.";
                        break;
                    default:
                        $detailedErrors[] = "Field '{$field}': {$error}";
                        break;
                }
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validationErrors,
                'detailed_errors' => $detailedErrors,
                'error_count' => count($validationErrors)
            ]);
        }

        $santriIds = $this->request->getPost('santri_ids');
        $idTpqList = $this->request->getPost('IdTpq'); // Sekarang berupa array
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran');

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        // Buat mapping IdTpq untuk setiap santri
        $santriTpqMap = [];
        
        // Ambil data santri untuk mendapatkan IdTpq mereka
        $builder = $this->db->table('tbl_santri_baru');
        $builder->select('IdSantri, IdTpq');
        $builder->whereIn('IdSantri', $santriIds);
        $santriData = $builder->get()->getResultArray();
        
        // Buat mapping IdSantri -> IdTpq
        foreach ($santriData as $santri) {
            $santriTpqMap[$santri['IdSantri']] = $santri['IdTpq'];
        }

        foreach ($santriIds as $idSantri) {
            // Cek apakah santri sudah terdaftar
            if ($this->pesertaMunaqosahModel->isPesertaExists($idSantri, $idTahunAjaran)) {
                $errorCount++;
                $errors[] = "Santri ID {$idSantri} sudah terdaftar";
                continue;
            }

            // Gunakan IdTpq dari data santri, bukan dari input
            $idTpq = $santriTpqMap[$idSantri] ?? null;
            
            if (!$idTpq) {
                $errorCount++;
                $errors[] = "Santri ID {$idSantri} tidak memiliki data TPQ yang valid";
                continue;
            }

            $data = [
                'IdSantri' => $idSantri,
                'IdTpq' => $idTpq,
                'IdTahunAjaran' => $idTahunAjaran
            ];

            if ($this->pesertaMunaqosahModel->save($data)) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Gagal menyimpan santri ID {$idSantri}";
            }
        }

        if ($successCount > 0) {
            $message = "Berhasil menyimpan {$successCount} peserta";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} gagal";
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'detailed_errors' => $errors
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan semua data',
                'detailed_errors' => $errors
            ]);
        }
    }


    // ==================== MATERI MUNAQOSAH ====================

    public function materiMunaqosah()
    {
        // Get materi relation dari materiMunaqosahModel
        $materi = $this->materiMunaqosahModel->getMateriWithRelations();
        // Get materi pelajaran dari materiPelajaranModel
        $materiPelajaran = $this->materiPelajaranModel->findAll();
        // Get grup materi aktif
        $grupMateriAktif = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();

        // Mapping kategori materi pelajaran ke master kategori munaqosah
        $kategoriMaster = $this->munaqosahKategoriModel
            ->select('IdKategoriMateri, NamaKategoriMateri')
            ->findAll();

        $kategoriNameMap = [];
        foreach ($kategoriMaster as $km) {
            $kategoriNameMap[$km['IdKategoriMateri']] = $km['NamaKategoriMateri'];
        }

        foreach ($materiPelajaran as &$mp) {
            $kategoriLabel = $mp['Kategori'] ?? null;
            $idKategori = $this->mapKategoriToId($kategoriLabel);

            $mp['IdKategoriMateri'] = $idKategori;
            if ($idKategori && isset($kategoriNameMap[$idKategori])) {
                $mp['NamaKategoriMateri'] = $kategoriNameMap[$idKategori];
            } else {
                $mp['NamaKategoriMateri'] = $kategoriLabel ?? '-';
            }
        }
        unset($mp);

        $data = [
            'page_title' => 'Data Materi Munaqosah',
            'materi' => $materi,
            'materiPelajaran' => $materiPelajaran,
            'grupMateriAktif' => $grupMateriAktif
        ];
        return view('backend/Munaqosah/listMateriMunaqosah', $data);
    }

    public function saveMateriMunaqosah()
    {
        $rules = [
            'IdMateri' => 'required',
            'IdKategoriMateri' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'IdMateri' => $this->request->getPost('IdMateri'),
            'IdKategoriMateri' => $this->request->getPost('IdKategoriMateri')
        ];

        if ($this->materiMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->materiMunaqosahModel->errors()
            ]);
        }
    }

    public function saveMateriBatch()
    {
        $materiArray = $this->request->getPost('materi');

        if (!is_array($materiArray) || empty($materiArray)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal satu materi'
            ]);
        }

        // Validasi data
        $validMateri = [];
        $errors = [];

        foreach ($materiArray as $materi) {
            $idMateri = $materi['IdMateri'] ?? null;
            $idGrupMateri = $materi['IdGrupMateriUjian'] ?? null;
            $idKategoriMateri = $materi['IdKategoriMateri'] ?? null;

            if (empty($idMateri) || empty($idGrupMateri)) {
                $errors[] = "ID Materi dan ID Grup Materi Ujian harus diisi";
                continue;
            }

            if (empty($idKategoriMateri)) {
                $kategoriLabel = $materi['KategoriMateri'] ?? $materi['Kategori'] ?? null;
                $idKategoriMateri = $this->mapKategoriToId($kategoriLabel);
            } else {
                $idKategoriMateri = strtoupper(trim($idKategoriMateri));
            }

            if (empty($idKategoriMateri)) {
                $label = $materi['KategoriMateri'] ?? $materi['Kategori'] ?? '-';
                $errors[] = "Kategori materi '{$label}' belum memiliki mapping IdKategoriMateri yang valid";
                continue;
            }

            $materi['IdMateri'] = $idMateri;
            $materi['IdKategoriMateri'] = $idKategoriMateri;
            $materi['IdGrupMateriUjian'] = $idGrupMateri;

            $validMateri[] = $materi;
        }

        if (empty($validMateri)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data materi yang valid',
                'errors' => $errors
            ]);
        }

        // Cek duplikasi
        $idMateriArray = array_column($validMateri, 'IdMateri');
        $duplicateMateri = $this->materiMunaqosahModel->checkDuplicateMateri($idMateriArray);
        
        if (!empty($duplicateMateri)) {
            // Ambil info materi yang duplikat
            $materiInfo = $this->materiMunaqosahModel->getMateriInfo($duplicateMateri);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terdapat materi yang sudah ada di sistem',
                'duplicate_check' => true,
                'duplicate_materi' => $duplicateMateri,
                'materi_info' => $materiInfo,
                'errors' => $errors
            ]);
        }

        // Simpan data jika tidak ada duplikasi
        $savedCount = 0;
        $saveErrors = [];

        foreach ($validMateri as $materi) {
            $data = [
                'IdMateri' => $materi['IdMateri'],
                'IdKategoriMateri' => $materi['IdKategoriMateri'],
                'IdGrupMateriUjian' => $materi['IdGrupMateriUjian'],
                'Status' => 'Aktif'
            ];

            if ($this->materiMunaqosahModel->save($data)) {
                $savedCount++;
            } else {
                $saveErrors[] = "Gagal menyimpan materi ID: " . $materi['IdMateri'];
            }
        }
        
        if ($savedCount > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil menyimpan $savedCount materi",
                'saved_count' => $savedCount,
                'errors' => array_merge($errors, $saveErrors)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan semua materi',
            'errors' => array_merge($errors, $saveErrors)
        ]);
    }

    public function saveMateriBatchWithConfirmation()
    {
        $materiArray = $this->request->getPost('materi');
        $skipDuplicates = $this->request->getPost('skip_duplicates') === 'true';

        if (!is_array($materiArray) || empty($materiArray)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal satu materi'
            ]);
        }

        // Validasi data
        $validMateri = [];
        $errors = [];

        foreach ($materiArray as $materi) {
            $idMateri = $materi['IdMateri'] ?? null;
            $idGrupMateri = $materi['IdGrupMateriUjian'] ?? null;
            $idKategoriMateri = $materi['IdKategoriMateri'] ?? null;

            if (empty($idMateri) || empty($idGrupMateri)) {
                $errors[] = "ID Materi dan ID Grup Materi Ujian harus diisi";
                continue;
            }

            if (empty($idKategoriMateri)) {
                $kategoriLabel = $materi['KategoriMateri'] ?? $materi['Kategori'] ?? null;
                $idKategoriMateri = $this->mapKategoriToId($kategoriLabel);
            } else {
                $idKategoriMateri = strtoupper(trim($idKategoriMateri));
            }

            if (empty($idKategoriMateri)) {
                $label = $materi['KategoriMateri'] ?? $materi['Kategori'] ?? '-';
                $errors[] = "Kategori materi '{$label}' belum memiliki mapping IdKategoriMateri yang valid";
                continue;
            }

            $materi['IdMateri'] = $idMateri;
            $materi['IdKategoriMateri'] = $idKategoriMateri;
            $materi['IdGrupMateriUjian'] = $idGrupMateri;

            $validMateri[] = $materi;
        }

        if (empty($validMateri)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data materi yang valid',
                'errors' => $errors
            ]);
        }

        // Cek duplikasi
        $idMateriArray = array_column($validMateri, 'IdMateri');
        $duplicateMateri = $this->materiMunaqosahModel->checkDuplicateMateri($idMateriArray);
        
        if (!empty($duplicateMateri) && !$skipDuplicates) {
            // Ambil info materi yang duplikat
            $materiInfo = $this->materiMunaqosahModel->getMateriInfo($duplicateMateri);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terdapat materi yang sudah ada di sistem',
                'duplicate_check' => true,
                'duplicate_materi' => $duplicateMateri,
                'materi_info' => $materiInfo,
                'errors' => $errors
            ]);
        }

        // Filter materi yang tidak duplikat jika skip_duplicates = true
        $materiToSave = $validMateri;
        if ($skipDuplicates && !empty($duplicateMateri)) {
            $materiToSave = array_filter($validMateri, function($materi) use ($duplicateMateri) {
                return !in_array($materi['IdMateri'], $duplicateMateri);
            });
        }

        // Simpan data
        $savedCount = 0;
        $saveErrors = [];

        foreach ($materiToSave as $materi) {
            $data = [
                'IdMateri' => $materi['IdMateri'],
                'IdKategoriMateri' => $materi['IdKategoriMateri'],
                'IdGrupMateriUjian' => $materi['IdGrupMateriUjian'],
                'Status' => 'Aktif'
            ];

            if ($this->materiMunaqosahModel->save($data)) {
                $savedCount++;
            } else {
                $saveErrors[] = "Gagal menyimpan materi ID: " . $materi['IdMateri'];
            }
        }

        $message = "Berhasil menyimpan $savedCount materi";
        if ($skipDuplicates && !empty($duplicateMateri)) {
            $skippedCount = count($duplicateMateri);
            $message .= " (Melewati $skippedCount materi yang sudah ada)";
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'saved_count' => $savedCount,
            'skipped_count' => $skipDuplicates && !empty($duplicateMateri) ? count($duplicateMateri) : 0,
            'errors' => array_merge($errors, $saveErrors)
        ]);
    }

    public function updateMateriMunaqosah($id)
    {
        $rules = [
            'IdGrupMateriUjian' => 'required',
            'Status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'IdGrupMateriUjian' => $this->request->getPost('IdGrupMateriUjian'),
            'Status' => $this->request->getPost('Status')
        ];

        if ($this->materiMunaqosahModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->materiMunaqosahModel->errors()
            ]);
        }
    }

    public function deleteMateriMunaqosah($id)
    {
        // Ambil data materi yang akan dihapus
        $materi = $this->materiMunaqosahModel->find($id);
        if (!$materi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data materi tidak ditemukan'
            ]);
        }

        // Cek apakah IdMateri sudah digunakan di tabel nilai
        $isUsed = $this->materiMunaqosahModel->checkMateriUsedInNilai($materi['IdMateri']);
        
        if ($isUsed) {
            // Ambil informasi penggunaan
            $usageInfo = $this->materiMunaqosahModel->getMateriUsageInfo($materi['IdMateri']);
            $usageCount = $usageInfo ? $usageInfo->usage_count : 0;
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Materi tidak dapat dihapus karena sudah digunakan',
                'blocked_delete' => true,
                'usage_count' => $usageCount,
                'materi_info' => [
                    'IdMateri' => $materi['IdMateri'],
                    'IdGrupMateriUjian' => $materi['IdGrupMateriUjian']
                ]
            ]);
        }

        // Jika tidak digunakan, lanjutkan delete
        if ($this->materiMunaqosahModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    // ==================== GRUP MATERI UJIAN ====================

    public function grupMateriUjian()
    {
        $data = [
            'page_title' => 'Data Grup Materi Ujian',
            'grupMateri' => $this->grupMateriUjiMunaqosahModel->findAll()
        ];
        return view('backend/Munaqosah/listGrupMateriUjian', $data);
    }

    public function saveIdGrupMateriUjian()
    {
        $rules = [
            'IdIdGrupMateriUjian' => 'required|max_length[50]|is_unique[tbl_munaqosah_grup_materi_uji.IdIdGrupMateriUjian]',
            'NamaMateriGrup' => 'required|max_length[100]',
            'Status' => 'required|in_list[Aktif,Tidak Aktif]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Konversi nama materi grup ke huruf kapital
        $namaMateriGrup = strtoupper($this->request->getPost('NamaMateriGrup'));
        
        // Cek apakah nama grup materi sudah ada (case insensitive)
        $existingGrup = $this->grupMateriUjiMunaqosahModel->checkNamaMateriGrupExists($namaMateriGrup);
        
        if ($existingGrup) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama grup materi sudah ada',
                'duplicate_name' => true,
                'existing_name' => $existingGrup->NamaMateriGrup,
                'suggestion' => 'Gunakan nama yang berbeda untuk grup materi ini'
            ]);
        }

        $data = [
            'IdIdGrupMateriUjian' => $this->request->getPost('IdIdGrupMateriUjian'),
            'NamaMateriGrup' => $namaMateriGrup,
            'Status' => $this->request->getPost('Status')
        ];

        if ($this->grupMateriUjiMunaqosahModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data grup materi ujian berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'errors' => $this->grupMateriUjiMunaqosahModel->errors()
            ]);
        }
    }

    public function updateIdGrupMateriUjian($id)
    {
        $rules = [
            'NamaMateriGrup' => 'required|max_length[100]',
            'Status' => 'required|in_list[Aktif,Tidak Aktif]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Konversi nama materi grup ke huruf kapital
        $namaMateriGrup = strtoupper($this->request->getPost('NamaMateriGrup'));
        
        // Cek apakah nama grup materi sudah ada (case insensitive) - exclude current record
        $existingGrup = $this->grupMateriUjiMunaqosahModel->checkNamaMateriGrupExists($namaMateriGrup, $id);
        
        if ($existingGrup) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama grup materi sudah ada',
                'duplicate_name' => true,
                'existing_name' => $existingGrup->NamaMateriGrup,
                'suggestion' => 'Gunakan nama yang berbeda untuk grup materi ini'
            ]);
        }

        $data = [
            'NamaMateriGrup' => $namaMateriGrup,
            'Status' => $this->request->getPost('Status')
        ];

        if ($this->grupMateriUjiMunaqosahModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data grup materi ujian berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->grupMateriUjiMunaqosahModel->errors()
            ]);
        }
    }

    public function deleteIdGrupMateriUjian($id)
    {
        // Ambil data grup materi yang akan dihapus
        $grupMateri = $this->grupMateriUjiMunaqosahModel->find($id);
        if (!$grupMateri) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data grup materi tidak ditemukan'
            ]);
        }

        // Cek apakah IdIdGrupMateriUjian sudah digunakan di tabel materi
        $isUsed = $this->grupMateriUjiMunaqosahModel->checkGrupMateriUsed($grupMateri['IdIdGrupMateriUjian']);
        
        if ($isUsed) {
            // Ambil informasi penggunaan
            $usageInfo = $this->grupMateriUjiMunaqosahModel->getGrupMateriUsageInfo($grupMateri['IdIdGrupMateriUjian']);
            $usageCount = $usageInfo ? $usageInfo->usage_count : 0;
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Grup materi tidak dapat dihapus karena sudah digunakan',
                'blocked_delete' => true,
                'usage_count' => $usageCount,
                'grup_info' => [
                    'IdIdGrupMateriUjian' => $grupMateri['IdIdGrupMateriUjian'],
                    'NamaMateriGrup' => $grupMateri['NamaMateriGrup']
                ]
            ]);
        }

        // Jika tidak digunakan, lanjutkan delete
        if ($this->grupMateriUjiMunaqosahModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data grup materi berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function getGrupMateriAktif()
    {
        $grupMateri = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();
        return $this->response->setJSON($grupMateri);
    }

    public function getNextIdGrupMateriUjian()
    {
        $nextId = $this->grupMateriUjiMunaqosahModel->generateNextIdGrupMateriUjian();
        return $this->response->setJSON([
            'success' => true,
            'next_id' => $nextId
        ]);
    }

    // ==================== API METHODS ====================

    public function getSantriData($idKelas, $idTpq)
    {
        // Ambil IdTpq dari session jika user bukan admin
        $sessionIdTpq = session()->get('IdTpq');
        
        // Jika ada session IdTpq (user bukan admin), gunakan session IdTpq
        if ($sessionIdTpq) {
            $idTpq = $sessionIdTpq;
        }
        
        // Handle parameter 0 untuk "semua"
        $filterTpq = ($idTpq == 0) ? 0 : $idTpq;
        $filterKelas = ($idKelas == 0) ? 0 : $idKelas;
        
        $santri = $this->helpFunction->getDataSantriStatus(1, $filterTpq, $filterKelas);
        return $this->response->setJSON($santri);
    }

    public function getTpqData()
    {
        $tpq = $this->tpqModel->findAll();
        return $this->response->setJSON($tpq);
    }

    public function getGuruData()
    {
        $guru = $this->guruModel->findAll();
        return $this->response->setJSON($guru);
    }

    public function getMateriData()
    {
        $materi = $this->materiPelajaranModel->findAll();
        return $this->response->setJSON($materi);
    }

    public function getStatistikData()
    {
        helper('munaqosah');
        $statistik = getStatistikMunaqosah();
        return $this->response->setJSON($statistik);
    }

    public function getNilaiByPeserta($noPeserta)
    {
        $nilai = $this->nilaiMunaqosahModel->getNilaiByPeserta($noPeserta);
        return $this->response->setJSON($nilai);
    }

    public function getAntrianByStatus($status)
    {
        $tahunAjaran = session()->get('IdTahunAjaran') ?? '2024/2025';
        
        if ($status == 'belum') {
            $antrian = $this->antrianMunaqosahModel->getAntrianBelumSelesai($tahunAjaran);
        } else {
            $antrian = $this->antrianMunaqosahModel->getAntrianSelesai($tahunAjaran);
        }
        
        return $this->response->setJSON($antrian);
    }

    public function getBobotByTahunAjaran($tahunAjaran)
    {
        $bobot = $this->bobotNilaiMunaqosahModel->getBobotByTahunAjaran($tahunAjaran);
        return $this->response->setJSON($bobot);
    }

    public function getPesertaByTpq($idTpq)
    {
        $tahunAjaran = session()->get('IdTahunAjaran') ?? '2024/2025';
        $peserta = $this->pesertaMunaqosahModel->getPesertaByTpq($idTpq, $tahunAjaran);
        return $this->response->setJSON($peserta);
    }

    public function checkDataTerkait($idSantri)
    {
        try {
            $dataTerkait = [];
            $db = \Config\Database::connect();
            
            // Cek di tbl_nilai_munaqosah
            $nilaiMunaqosah = $db->table('tbl_munaqosah_nilai')
                ->where('IdSantri', $idSantri)
                ->get()
                ->getResultArray();
            
            if (!empty($nilaiMunaqosah)) {
                $dataTerkait['nilai_munaqosah'] = [
                    'count' => count($nilaiMunaqosah),
                    'data' => $nilaiMunaqosah
                ];
            }
            
            // Cek di tbl_munaqosah_antrian
            $antrianMunaqosah = $db->table('tbl_munaqosah_antrian')
                ->where('IdSantri', $idSantri)
                ->get()
                ->getResultArray();
            
            if (!empty($antrianMunaqosah)) {
                $dataTerkait['antrian_munaqosah'] = [
                    'count' => count($antrianMunaqosah),
                    'data' => $antrianMunaqosah
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data_terkait' => $dataTerkait,
                'total_terkait' => count($dataTerkait)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengecek data terkait: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePesertaMunaqosah($id)
    {
        try {
            // Ambil data peserta untuk mendapatkan IdSantri
            $peserta = $this->pesertaMunaqosahModel->find($id);
            if (!$peserta) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data peserta tidak ditemukan'
                ]);
            }
            
            $idSantri = $peserta['IdSantri'];
            $db = \Config\Database::connect();
            
            // Hapus data terkait terlebih dahulu
            $db->table('tbl_munaqosah_nilai')->where('IdSantri', $idSantri)->delete();
            $db->table('tbl_munaqosah_antrian')->where('IdSantri', $idSantri)->delete();
            
            // Hapus peserta munaqosah
            $this->pesertaMunaqosahModel->delete($id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peserta dan semua data terkait berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePesertaBySantri($idSantri)
    {
        try {
            // Cari peserta munaqosah berdasarkan IdSantri
            $peserta = $this->pesertaMunaqosahModel->where('IdSantri', $idSantri)->first();
            if (!$peserta) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data peserta tidak ditemukan'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Hapus data terkait terlebih dahulu
            $db->table('tbl_munaqosah_nilai')->where('IdSantri', $idSantri)->delete();
            $db->table('tbl_munaqosah_antrian')->where('IdSantri', $idSantri)->delete();
            
            // Hapus peserta munaqosah
            $this->pesertaMunaqosahModel->where('IdSantri', $idSantri)->delete();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peserta dan semua data terkait berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
            ]);
        }
    }

    public function updateStatusMateri($id)
    {
        try {
            // Debug: Log input data
            log_message('debug', 'Update Status Request - ID: ' . $id);
            log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));
            log_message('debug', 'Request Method: ' . $this->request->getMethod());
            log_message('debug', 'Request URI: ' . $this->request->getUri());
            
            $rules = [
                'status' => 'required|in_list[Aktif,Tidak Aktif]'
            ];

            if (!$this->validate($rules)) {
                log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if record exists
            $materi = $this->materiMunaqosahModel->find($id);
            if (!$materi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data materi tidak ditemukan'
                ]);
            }

            $data = [
                'Status' => $this->request->getPost('status')
            ];

            log_message('debug', 'Update data: ' . json_encode($data));

            if ($this->materiMunaqosahModel->update($id, $data)) {
                log_message('info', 'Status updated successfully for ID: ' . $id);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status materi berhasil diupdate'
                ]);
            } else {
                log_message('error', 'Failed to update status: ' . json_encode($this->materiMunaqosahModel->errors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate status materi',
                    'errors' => $this->materiMunaqosahModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in updateStatusMateri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    public function updateGrupMateri($id)
    {
        try {
            // Debug: Log input data
            log_message('debug', 'Update Grup Materi Request - ID: ' . $id);
            log_message('debug', 'POST Data: ' . json_encode($this->request->getPost()));
            log_message('debug', 'Request Method: ' . $this->request->getMethod());
            log_message('debug', 'Request URI: ' . $this->request->getUri());
            
            $rules = [
                'IdGrupMateriUjian' => 'required|max_length[50]'
            ];

            if (!$this->validate($rules)) {
                log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if record exists
            $materi = $this->materiMunaqosahModel->find($id);
            if (!$materi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data materi tidak ditemukan'
                ]);
            }

            // Check if new grup exists
            $grupExists = $this->grupMateriUjiMunaqosahModel->where('IdGrupMateriUjian', $this->request->getPost('IdGrupMateriUjian'))->first();
            if (!$grupExists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Grup materi ujian tidak ditemukan'
                ]);
            }

            $data = [
                'IdGrupMateriUjian' => $this->request->getPost('IdGrupMateriUjian')
            ];

            log_message('debug', 'Update data: ' . json_encode($data));

            if ($this->materiMunaqosahModel->update($id, $data)) {
                log_message('info', 'Grup materi updated successfully for ID: ' . $id);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Grup materi ujian berhasil diupdate'
                ]);
            } else {
                log_message('error', 'Failed to update grup materi: ' . json_encode($this->materiMunaqosahModel->errors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate grup materi ujian',
                    'errors' => $this->materiMunaqosahModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in updateGrupMateri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function saveBobotBatch()
    {
        try {
            $data = $this->request->getPost('data');
            
            if (!$data || !is_array($data)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak valid'
                ]);
            }

            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();

            $kategoriList = $this->munaqosahKategoriModel
                ->select('IdKategoriMateri')
                ->findAll();
            $allowedKategori = array_column($kategoriList, 'IdKategoriMateri');

            $sanitizedData = [];
            foreach ($data as $item) {
                if (empty($item['IdTahunAjaran']) || empty($item['IdKategoriMateri']) || !isset($item['NilaiBobot'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Data tidak lengkap'
                    ]);
                }

                $idKategori = strtoupper($item['IdKategoriMateri']);
                if (!in_array($idKategori, $allowedKategori, true)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Kategori materi dengan ID ' . $idKategori . ' tidak ditemukan'
                    ]);
                }

                $sanitizedData[] = [
                    'IdTahunAjaran' => $item['IdTahunAjaran'],
                    'IdKategoriMateri' => $idKategori,
                    'NilaiBobot' => $item['NilaiBobot']
                ];
            }

            // Hapus data lama untuk tahun ajaran yang sama
            $tahunAjaran = $sanitizedData[0]['IdTahunAjaran'];
            $bobotNilaiModel->where('IdTahunAjaran', $tahunAjaran)->delete();

            // Simpan data baru
            if ($bobotNilaiModel->insertBatch($sanitizedData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data bobot nilai berhasil disimpan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data bobot nilai'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteBobotByTahun()
    {
        try {
            $tahunAjaran = $this->request->getPost('IdTahunAjaran');
            
            if (!$tahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran tidak boleh kosong'
                ]);
            }

            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            if ($bobotNilaiModel->where('IdTahunAjaran', $tahunAjaran)->delete()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data bobot nilai berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data bobot nilai'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getDefaultBobot()
    {
        try {
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();

            // Ambil data default dari database
            $defaultData = $bobotNilaiModel->getDefaultBobot();
            
            if (empty($defaultData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data default tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $defaultData,
                'message' => 'Data default berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function duplicateDefaultBobot()
    {
        try {
            $tahunAjaran = $this->request->getPost('IdTahunAjaran');
            
            if (!$tahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran tidak boleh kosong'
                ]);
            }

            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();

            // Ambil data default
            $defaultData = $bobotNilaiModel->getDefaultBobot();
            
            if (empty($defaultData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data default tidak ditemukan'
                ]);
            }

            // Cek apakah tahun ajaran sudah ada
            $existingData = $bobotNilaiModel->where('IdTahunAjaran', $tahunAjaran)->first();
            if ($existingData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data untuk tahun ajaran ' . $tahunAjaran . ' sudah ada'
                ]);
            }

            // Duplicate data default dengan tahun ajaran baru
            $duplicateData = [];
            foreach ($defaultData as $item) {
                $duplicateData[] = [
                    'IdTahunAjaran' => $tahunAjaran,
                    'IdKategoriMateri' => $item['IdKategoriMateri'],
                    'NilaiBobot' => $item['NilaiBobot'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }

            // Simpan data duplicate
            if ($bobotNilaiModel->insertBatch($duplicateData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data default berhasil diduplikasi untuk tahun ajaran ' . $tahunAjaran
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menduplikasi data default'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getBobotByTahun($tahunAjaran)
    {
        try {
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();

            // Ambil data berdasarkan tahun ajaran
            $data = $bobotNilaiModel->getBobotWithKategori($tahunAjaran);
            
            if (empty($data)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data untuk tahun ajaran ' . $tahunAjaran . ' tidak ditemukan'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'message' => 'Data berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getTahunAjaranOptions()
    {
        try {
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            $data = $bobotNilaiModel->select('IdTahunAjaran')
                                   ->groupBy('IdTahunAjaran')
                                   ->orderBy('IdTahunAjaran', 'ASC')
                                   ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'message' => 'Data tahun ajaran berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function duplicateBobotData()
    {
        try {
            $sourceTahunAjaran = $this->request->getPost('sourceTahunAjaran');
            $targetTahunAjaran = $this->request->getPost('targetTahunAjaran');
            
            if (!$sourceTahunAjaran || !$targetTahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran sumber dan target harus diisi'
                ]);
            }
            
            if ($sourceTahunAjaran === $targetTahunAjaran) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran target tidak boleh sama dengan sumber'
                ]);
            }
            
            // Validasi format tahun ajaran target (harus 8 digit angka)
            if (!preg_match('/^\d{8}$/', $targetTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format tahun ajaran target harus berupa 8 digit angka (contoh: 20252026)'
                ]);
            }
            
            // Validasi tahun ajaran yang masuk akal
            $tahun1 = (int) substr($targetTahunAjaran, 0, 4);
            $tahun2 = (int) substr($targetTahunAjaran, 4, 4);
            $currentYear = (int) date('Y');
            
            if ($tahun2 !== $tahun1 + 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun kedua harus tahun pertama + 1 (contoh: 20252026)'
                ]);
            }
            
            if ($tahun1 < 2000 || $tahun1 > $currentYear + 10) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tahun ajaran harus antara 2000 dan ' . ($currentYear + 10)
                ]);
            }
            
            $bobotNilaiModel = new \App\Models\MunaqosahBobotNilaiModel();
            
            // Cek apakah data target sudah ada
            $existingData = $bobotNilaiModel->where('IdTahunAjaran', $targetTahunAjaran)->first();
            if ($existingData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data untuk tahun ajaran ' . $targetTahunAjaran . ' sudah ada'
                ]);
            }
            
            // Ambil data sumber
            $sourceData = $bobotNilaiModel->where('IdTahunAjaran', $sourceTahunAjaran)->findAll();
            if (empty($sourceData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data sumber tidak ditemukan'
                ]);
            }
            
            // Duplikasi data
            $duplicateData = [];
            foreach ($sourceData as $item) {
                $duplicateData[] = [
                    'IdTahunAjaran' => $targetTahunAjaran,
                    'IdKategoriMateri' => $item['IdKategoriMateri'],
                    'NilaiBobot' => $item['NilaiBobot'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            
            if ($bobotNilaiModel->insertBatch($duplicateData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil diduplikasi dari ' . $sourceTahunAjaran . ' ke ' . $targetTahunAjaran
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menduplikasi data'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== REGISTRASI PESERTA MUNAQOSAH ====================

    /**
     * Registrasi peserta munaqosah
     * @return \CodeIgniter\HTTP\Response
     */
    public function registrasiPesertaMunaqosah()
    {
        // Ambil tahun ajaran saat ini
        $tahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

        // Ambil data TPQ
        $idTpq = session()->get('IdTpq');
        if ($idTpq) {
            // User TPQ - hanya tampilkan TPQ mereka
            $tpq = [$this->tpqModel->find($idTpq)];
        } else {
            // Admin - tampilkan semua TPQ
            $tpq = $this->tpqModel->findAll();
        }
        
        // Ambil data kelas
        $kelas = $this->helpFunction->getDataKelas();
        
        $data = [
            'page_title' => 'Registrasi Peserta Munaqosah',
            'tahunAjaran' => $tahunAjaran,
            'tpq' => $tpq,
            'kelas' => $kelas
        ];
        
        return view('backend/Munaqosah/registrasiPesertaMunaqosah', $data);
    }

    /**
     * Get santri for registrasi peserta munaqosah
     * @return \CodeIgniter\HTTP\Response
     */
    public function getSantriForRegistrasi()
    {
        try {
            $filterTpq = $this->request->getGet('filterTpq') ?? 0;
            $filterKelas = $this->request->getGet('filterKelas') ?? 0;
            $typeUjian = $this->request->getGet('typeUjian') ?? 'munaqosah';
            $tahunAjaran = $this->helpFunction->getTahunAjaranSaatIni();

            // Check if user is admin (IdTpq = 0 or null means Admin)
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = empty($sessionIdTpq) || $sessionIdTpq == 0;

            // Force Pra-Munaqosah for non-admin users
            if (!$isAdmin) {
                $typeUjian = 'pra-munaqosah';
                // Force filter TPQ to user's TPQ only
                $filterTpq = $sessionIdTpq;
            }

            // Ambil data peserta munaqosah dengan relasi ke tabel santri
            $builder = $this->db->table('tbl_munaqosah_peserta mp');
            $builder->select('mp.*, s.*, t.NamaTpq, k.NamaKelas, 
                            mn_munaqosah.NoPeserta as NoPesertaMunaqosah,
                            mn_pra.NoPeserta as NoPesertaPraMunaqosah');
            $builder->join('tbl_santri_baru s', 's.IdSantri = mp.IdSantri', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = mp.IdTpq', 'left');
            $builder->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left');
            // Join untuk data munaqosah
            $builder->join('tbl_munaqosah_registrasi_uji mn_munaqosah', 'mn_munaqosah.IdSantri = mp.IdSantri AND mn_munaqosah.IdTahunAjaran = mp.IdTahunAjaran AND mn_munaqosah.TypeUjian = "munaqosah"', 'left');
            // Join untuk data pra-munaqosah
            $builder->join('tbl_munaqosah_registrasi_uji mn_pra', 'mn_pra.IdSantri = mp.IdSantri AND mn_pra.IdTahunAjaran = mp.IdTahunAjaran AND mn_pra.TypeUjian = "pra-munaqosah"', 'left');
            $builder->where('mp.IdTahunAjaran', $tahunAjaran);
            
            // Filter TPQ
            if ($filterTpq != 0) {
                $builder->where('mp.IdTpq', $filterTpq);
            }
            
            // Filter Kelas
            if ($filterKelas != 0) {
                $builder->where('s.IdKelas', $filterKelas);
            }

            // Group by untuk menghindari duplikasi
            $builder->groupBy('mp.IdSantri, mp.IdTpq, mp.IdTahunAjaran, mn_munaqosah.NoPeserta, mn_pra.NoPeserta');

            $builder->orderBy('mp.IdTpq', 'ASC');
            $builder->orderBy('s.NamaSantri', 'ASC');
            $builder->orderBy('mp.created_at', 'DESC');
            
            $santriData = $builder->get()->getResultArray();
            
            // Cek apakah santri sudah memiliki data di tabel nilai munaqosah
            $result = [];
            foreach ($santriData as $santri) {
                // Cek status berdasarkan type ujian yang dipilih
                if ($typeUjian === 'pra-munaqosah') {
                    $hasNilai = !empty($santri['NoPesertaPraMunaqosah']);
                    $santri['isPesertaPraMunaqosah'] = $hasNilai;
                    $santri['NoPesertaMunaqosah'] = $santri['NoPesertaPraMunaqosah'] ?? '-';
                } else {
                    $hasNilai = !empty($santri['NoPesertaMunaqosah']);
                    $santri['isPeserta'] = $hasNilai;
                    $santri['NoPesertaMunaqosah'] = $santri['NoPesertaMunaqosah'] ?? '-';
                }

                $result[] = $santri;
            }
            
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Error in getSantriForRegistrasi: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // Return detailed error information
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data santri',
                'error_details' => [
                    'error_message' => $e->getMessage(),
                    'error_type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'user_message' => 'Gagal memuat data santri. Silakan coba lagi atau hubungi administrator jika masalah berlanjut.'
            ]);
        }
    }

    /**
     * Process registrasi peserta munaqosah
     * @return \CodeIgniter\HTTP\Response
     */
    public function processRegistrasiPeserta()
    {
        try {
            // Validasi input data
            $santriIds = json_decode($this->request->getPost('santri_ids'), true);
            $tahunAjaran = $this->request->getPost('tahunAjaran');
            $typeUjian = $this->request->getPost('typeUjian') ?? 'munaqosah';

            // Check if user is admin (IdTpq = 0 or null means Admin)
            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = empty($sessionIdTpq) || $sessionIdTpq == 0;

            // Force Pra-Munaqosah for non-admin users
            if (!$isAdmin) {
                $typeUjian = 'pra-munaqosah';
                // Force filter TPQ to user's TPQ only
                $filterTpq = $sessionIdTpq;
            }
            
            // Detail validasi input
            $validationErrors = [];
            
            if (empty($santriIds)) {
                $validationErrors[] = "Parameter 'santri_ids' tidak boleh kosong";
            } elseif (!is_array($santriIds)) {
                $validationErrors[] = "Parameter 'santri_ids' harus berupa array";
            } elseif (count($santriIds) === 0) {
                $validationErrors[] = "Minimal harus memilih satu santri";
            }
            
            if (empty($tahunAjaran)) {
                $validationErrors[] = "Parameter 'tahunAjaran' tidak boleh kosong";
            } elseif (!is_numeric($tahunAjaran)) {
                $validationErrors[] = "Parameter 'tahunAjaran' harus berupa angka";
            }
            
            if (!empty($validationErrors)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi input gagal',
                    'detailed_errors' => $validationErrors,
                    'error_count' => count($validationErrors)
                ]);
            }

            // Validasi: cek apakah ada santri yang sudah memiliki data registrasi berdasarkan type ujian
            $existingRegistrasi = $this->munaqosahRegistrasiUjiModel->whereIn('IdSantri', $santriIds)
                                                      ->where('IdTahunAjaran', $tahunAjaran)
                ->where('TypeUjian', $typeUjian)
                                                      ->findAll();

            if (!empty($existingRegistrasi)) {
                $existingIds = array_unique(array_column($existingRegistrasi, 'IdSantri'));
                $existingCount = count($existingIds);
                $totalSelected = count($santriIds);
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Beberapa santri sudah memiliki data registrasi munaqosah',
                    'detailed_errors' => [
                        "Ditemukan {$existingCount} santri yang sudah memiliki data registrasi dari {$totalSelected} santri yang dipilih",
                        "ID Santri yang sudah memiliki data: " . implode(', ', $existingIds),
                        "Silakan pilih santri lain yang belum memiliki data registrasi munaqosah"
                    ],
                    'existing_santri_ids' => $existingIds,
                    'existing_count' => $existingCount,
                    'total_selected' => $totalSelected
                ]);
            }
            
            // Start database transaction
            $this->db->transStart();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Ambil semua data peserta munaqosah sekaligus
            $builder = $this->db->table('tbl_munaqosah_peserta mp');
            $builder->select('mp.*, s.*');
            $builder->join('tbl_santri_baru s', 's.IdSantri = mp.IdSantri', 'left');
            $builder->whereIn('mp.IdSantri', $santriIds);
            $builder->where('mp.IdTahunAjaran', $tahunAjaran);
            $allSantri = $builder->get()->getResultArray();
            
            // Buat mapping untuk akses cepat
            $santriMap = [];
            foreach ($allSantri as $santri) {
                $santriMap[$santri['IdSantri']] = $santri;
            }
            
            // Ambil data grup materi ujian aktif sekali saja
            $grupMateri = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();

            // Ambil master kategori untuk mapping ID -> Nama
            $kategoriMaster = $this->munaqosahKategoriModel
                ->select('IdKategoriMateri, NamaKategoriMateri')
                ->findAll();

            $kategoriNameById = [];
            $kategoriIdByName = [];
            foreach ($kategoriMaster as $kategori) {
                $idKat = $kategori['IdKategoriMateri'];
                $namaKat = strtoupper($kategori['NamaKategoriMateri']);
                $kategoriNameById[$idKat] = $namaKat;
                $kategoriIdByName[$namaKat] = $idKat;
            }

            // Ambil semua materi sekaligus
            $allMateri = [];
            foreach ($grupMateri as $grup) {
                $materi = $this->materiMunaqosahModel->getMateriByGrup($grup['IdGrupMateriUjian']);
                if (!empty($materi)) {
                    foreach ($materi as $m) {
                        $kategoriId = $m['IdKategoriMateri'] ?? null;
                        if (empty($kategoriId)) {
                            continue;
                        }
                        if (!isset($allMateri[$kategoriId])) {
                            $allMateri[$kategoriId] = [];
                        }
                        $allMateri[$kategoriId][] = $m;
                    }
                }
            }

            // Ambil data surah alquran untuk kategori QURAN
            $alquranMateri = $this->munaqosahAlquranModel->getSurahForMunaqosah();
            if (!empty($alquranMateri)) {
                $quranKategoriId = $kategoriIdByName['BACA AL-QURAN'] ?? ($kategoriIdByName["QUR'AN"] ?? null);
                if ($quranKategoriId) {
                    foreach ($alquranMateri as &$alquran) {
                        $alquran['IdKategoriMateri'] = $quranKategoriId;
                        if (empty($alquran['IdGrupMateriUjian'])) {
                            $alquran['IdGrupMateriUjian'] = 'GM001';
                        }
                    }
                    unset($alquran);
                    $allMateri[$quranKategoriId] = $alquranMateri;
                }
            }

            // Debug: Log data materi
            log_message('info', 'Grup materi: ' . json_encode($grupMateri));
            log_message('info', 'All materi: ' . json_encode($allMateri));

            // Get minRange and maxRange from configuration
            // Use IdTpq from session, fallback to 'default' if not found
            $configIdTpq = $sessionIdTpq ?? 'default';
            $minRange = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NoPesertaStart', 100);
            $maxRange = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NoPesertaEnd', 400);

            // Generate semua NoPeserta sekaligus dengan validasi ketat
            $noPesertaMap = [];
            $usedNoPeserta = [];
            
            foreach ($santriIds as $santriId) {
                try {
                    $noPeserta = $this->generateUniqueNoPeserta($tahunAjaran, $usedNoPeserta, $typeUjian, $minRange, $maxRange);

                    // Validasi ketat: pastikan nomor peserta dalam range
                    if ($noPeserta < $minRange || $noPeserta > $maxRange) {
                        throw new \Exception("Nomor peserta {$noPeserta} di luar range yang diizinkan ({$minRange}-{$maxRange})");
                    }

                    $noPesertaMap[$santriId] = $noPeserta;
                    $usedNoPeserta[] = $noPeserta; // Tambahkan ke daftar yang sudah digunakan

                    log_message('info', "Generated NoPeserta {$noPeserta} for santri {$santriId} with type {$typeUjian}");
                } catch (\Exception $e) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal generate nomor peserta: ' . $e->getMessage(),
                        'santri_id' => $santriId
                    ]);
                }
            }
            
            // Proses semua santri dan kumpulkan data untuk batch insert
            $allNilaiData = [];
            foreach ($santriIds as $santriId) {
                try {
                    if (!isset($santriMap[$santriId])) {
                        $errorCount++;
                        $errors[] = "Peserta munaqosah dengan ID {$santriId} tidak ditemukan";
                        continue;
                    }
                    
                    $santri = $santriMap[$santriId];
                    $noPeserta = $noPesertaMap[$santriId];
                    
                    // Debug: Log data santri
                    log_message('info', "Processing santri: {$santriId}, NoPeserta: {$noPeserta}");

                    // Generate data nilai untuk santri ini
                    foreach ($allMateri as $kategoriId => $materiList) {
                        if (!empty($materiList)) {
                            $kategoriNama = $kategoriNameById[$kategoriId] ?? $kategoriId;
                            // Pilih materi secara acak
                            $randomMateri = $materiList[array_rand($materiList)];

                            // Untuk kategori QURAN, gunakan data dari tabel alquran
                            if ($kategoriNama === 'QURAN' || $kategoriNama === "QUR'AN") {
                                $nilaiRecord = [
                                    'NoPeserta' => $noPeserta,
                                    'IdSantri' => $santriId,
                                    'IdTpq' => $santri['IdTpq'],
                                    'IdTahunAjaran' => $tahunAjaran,
                                    'IdMateri' => $randomMateri['IdMateri'], // id dari tbl_munaqosah_alquran
                                    'IdGrupMateriUjian' => $randomMateri['IdGrupMateriUjian'], // 'GM001'
                                    'IdKategoriMateri' => $kategoriId,
                                    'TypeUjian' => $typeUjian,
                                    'Nilai' => 0,
                                    'Catatan' => ''
                                ];
                            } else {
                                // Untuk kategori lain, gunakan data dari tabel materi biasa
                                $nilaiRecord = [
                                    'NoPeserta' => $noPeserta,
                                    'IdSantri' => $santriId,
                                    'IdTpq' => $santri['IdTpq'],
                                    'IdTahunAjaran' => $tahunAjaran,
                                    'IdMateri' => $randomMateri['IdMateri'],
                                    'IdGrupMateriUjian' => $randomMateri['IdGrupMateriUjian'],
                                    'IdKategoriMateri' => $kategoriId,
                                    'TypeUjian' => $typeUjian,
                                    'Nilai' => 0,
                                    'Catatan' => ''
                                ];
                            }
                            
                            $allNilaiData[] = $nilaiRecord;
                            
                            // Debug: Log setiap record
                            log_message('info', "Nilai record: " . json_encode($nilaiRecord));
                        }
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error processing santri ID {$santriId}: " . $e->getMessage();
                    log_message('error', "Error processing santri {$santriId}: " . $e->getMessage());
                }
            }
            
            // Insert semua data sekaligus
            if (!empty($allNilaiData)) {
                // Validasi final: cek range nomor peserta sebelum validasi lainnya
                $rangeValidation = $this->validateNoPesertaRange($allNilaiData, $minRange, $maxRange);
                if (!$rangeValidation['valid']) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi range gagal: ' . $rangeValidation['message']
                    ]);
                }

                // Validasi final: cek duplikasi NoPeserta sebelum insert dan perbaiki jika ada
                $finalValidation = $this->validateNoPesertaUniqueness($allNilaiData, $tahunAjaran, $typeUjian, $minRange, $maxRange);
                if (!$finalValidation['valid']) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi gagal: ' . $finalValidation['message']
                    ]);
                }
                
                // Gunakan data yang sudah diperbaiki jika ada
                if (isset($finalValidation['fixedData'])) {
                    $allNilaiData = $finalValidation['fixedData'];
                    log_message('info', 'Data telah diperbaiki: ' . $finalValidation['message']);
                }
                
                // Debug: Log data yang akan diinsert
                log_message('info', 'Data yang akan diinsert: ' . json_encode($allNilaiData));

                $result = $this->munaqosahRegistrasiUjiModel->insertBatch($allNilaiData);
                
                // Debug: Log hasil insert
                log_message('info', 'Hasil insert: ' . ($result ? 'Berhasil' : 'Gagal'));
                
                if (!$result) {
                    $errorCount++;
                    $modelErrors = $this->munaqosahRegistrasiUjiModel->errors();
                    $errors[] = "Gagal insert data ke database: " . implode(', ', $modelErrors);
                    
                    // Log detail error untuk debugging
                    log_message('error', 'Insert batch gagal: ' . json_encode($modelErrors));
                    log_message('error', 'Data yang gagal diinsert: ' . json_encode($allNilaiData));
                }
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                $transactionError = $this->db->error();
                log_message('error', 'Database transaction failed: ' . json_encode($transactionError));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Database transaction failed',
                    'detailed_errors' => [
                        'Transaction rollback terjadi karena kesalahan database',
                        'Error code: ' . ($transactionError['code'] ?? 'Unknown'),
                        'Error message: ' . ($transactionError['message'] ?? 'Unknown error')
                    ],
                    'database_error' => $transactionError
                ]);
            }
            
            $message = "Berhasil memproses {$successCount} santri";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} gagal";
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            // Log detail error untuk debugging
            log_message('error', 'Exception in processRegistrasiPeserta: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'detailed_errors' => [
                    'Exception message: ' . $e->getMessage(),
                    'File: ' . $e->getFile() . ' Line: ' . $e->getLine(),
                    'Silakan hubungi administrator jika masalah berlanjut'
                ],
                'exception_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode()
                ]
            ]);
        }
    }

    /**
     * Generate unique NoPeserta for peserta munaqosah
     * @param int $tahunAjaran
     * @param array $usedNoPeserta
     * @param string $typeUjian
     * @param int $minRange Range minimum dari konfigurasi
     * @param int $maxRange Range maksimum dari konfigurasi
     * @return int $noPeserta
     */
    private function generateUniqueNoPeserta($tahunAjaran, $usedNoPeserta = [], $typeUjian = 'munaqosah', $minRange = 100, $maxRange = 400)
    {
        // Validasi ketat: nomor peserta harus dalam range yang ditentukan dari konfigurasi
        $maxAttempts = 100; // Maksimal 100 percobaan untuk menghindari infinite loop

        // Generate random number between 100-400
        $noPeserta = rand($minRange, $maxRange);

        // Cek apakah NoPeserta sudah ada di database untuk tahun ajaran dan TypeUjian yang sama
        $existing = $this->munaqosahRegistrasiUjiModel->where('NoPeserta', $noPeserta)
                                            ->where('IdTahunAjaran', $tahunAjaran)
            ->where('TypeUjian', $typeUjian)
                                            ->first();
        
        // Cek apakah NoPeserta sudah digunakan dalam batch yang sama
        $isUsedInBatch = in_array($noPeserta, $usedNoPeserta);
        
        // Jika sudah ada di database atau sudah digunakan dalam batch, coba generate ulang
        $attempts = 0;
        while (($existing || $isUsedInBatch) && $attempts < $maxAttempts) {
            $noPeserta = rand($minRange, $maxRange);
            $existing = $this->munaqosahRegistrasiUjiModel->where('NoPeserta', $noPeserta)
                                                ->where('IdTahunAjaran', $tahunAjaran)
                ->where('TypeUjian', $typeUjian)
                                                ->first();
            $isUsedInBatch = in_array($noPeserta, $usedNoPeserta);
            $attempts++;
        }

        // Jika masih ada duplikasi setelah maxAttempts, cari nomor yang tersedia secara sequential
        if ($existing || $isUsedInBatch) {
            $noPeserta = $this->findAvailableNoPesertaInRange($tahunAjaran, $usedNoPeserta, $minRange, $maxRange, $typeUjian);
        }

        // Validasi final: pastikan nomor peserta dalam range yang benar
        if ($noPeserta < $minRange || $noPeserta > $maxRange) {
            log_message('error', "Generated NoPeserta out of range: {$noPeserta}. Expected range: {$minRange}-{$maxRange}");
            throw new \Exception("Tidak dapat menghasilkan nomor peserta dalam range {$minRange}-{$maxRange}. Semua nomor dalam range sudah digunakan.");
        }

        return $noPeserta;
    }

    /**
     * Find available NoPeserta in range
     * @param int $tahunAjaran
     * @param array $usedNoPeserta
     * @param int $minRange
     * @param int $maxRange
     * @param string $typeUjian
     * @return int $availableNoPeserta
     */
    private function findAvailableNoPesertaInRange($tahunAjaran, $usedNoPeserta, $minRange, $maxRange, $typeUjian = 'munaqosah')
    {
        // Cari nomor yang tersedia secara sequential dalam range
        for ($i = $minRange; $i <= $maxRange; $i++) {
            // Cek apakah nomor sudah digunakan dalam batch
            if (in_array($i, $usedNoPeserta)) {
                continue;
            }

            // Cek apakah nomor sudah ada di database
            $existing = $this->munaqosahRegistrasiUjiModel->where('NoPeserta', $i)
                                                ->where('IdTahunAjaran', $tahunAjaran)
                ->where('TypeUjian', $typeUjian)
                                                ->first();

            if (!$existing) {
                return $i; // Nomor tersedia ditemukan
            }
        }

        // Jika tidak ada nomor yang tersedia dalam range, throw exception
        throw new \Exception("Tidak ada nomor peserta yang tersedia dalam range {$minRange}-{$maxRange} untuk tahun ajaran {$tahunAjaran}");
    }

    private function mapKategoriToId(?string $kategori): ?string
    {
        if ($kategori === null) {
            return null;
        }

        $trimmed = trim($kategori);
        if ($trimmed === '') {
            return null;
        }

        $upper = strtoupper($trimmed);

        if (preg_match('/^KM\d{3}$/', $upper)) {
            return $upper;
        }

        $upper = str_replace(["\"", '`'], chr(39), $upper);
        $slug = preg_replace('/[\s\'`-]+/', '', $upper);
        if ($slug === null) {
            return null;
        }

        $mapping = [
            'DOA' => 'KM004',
            'AYATPILIHAN' => 'KM003',
            'QURAN' => 'KM001',
            'ALQURAN' => 'KM001',
            'IMLA' => 'KM006',
            'SHOLAT' => 'KM005',
            'SURATPENDEK' => 'KM002',
        ];

        return $mapping[$slug] ?? null;
    }

    /**
     * Validate NoPeserta Range
     * @param array $allNilaiData
     * @param int $minRange
     * @param int $maxRange
     * @return array $validatedData
     */
    private function validateNoPesertaRange($allNilaiData, $minRange, $maxRange)
    {
        $outOfRangeNumbers = [];

        foreach ($allNilaiData as $data) {
            $noPeserta = $data['NoPeserta'];

            // Validasi: pastikan nomor peserta dalam range yang benar
            if ($noPeserta < $minRange || $noPeserta > $maxRange) {
                $outOfRangeNumbers[] = $noPeserta;
            }
        }

        if (!empty($outOfRangeNumbers)) {
            $uniqueOutOfRange = array_unique($outOfRangeNumbers);
            return [
                'valid' => false,
                'message' => "Ditemukan nomor peserta di luar range {$minRange}-{$maxRange}: " . implode(', ', $uniqueOutOfRange)
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate NoPeserta Uniqueness
     * @param array $allNilaiData
     * @param int $tahunAjaran
     * @param string $typeUjian
     * @param int $minRange Range minimum dari konfigurasi
     * @param int $maxRange Range maksimum dari konfigurasi
     * @return array $validatedData
     */
    private function validateNoPesertaUniqueness($allNilaiData, $tahunAjaran, $typeUjian = 'munaqosah', $minRange = 100, $maxRange = 400)
    {
        // Group data by IdSantri untuk memastikan satu IdSantri = satu NoPeserta
        $santriNoPesertaMap = [];
        $duplicateSantri = [];
        
        foreach ($allNilaiData as $data) {
            $idSantri = $data['IdSantri'];
            $noPeserta = $data['NoPeserta'];
            
            if (!isset($santriNoPesertaMap[$idSantri])) {
                $santriNoPesertaMap[$idSantri] = $noPeserta;
            } else {
                // Jika IdSantri yang sama memiliki NoPeserta berbeda, ini error
                if ($santriNoPesertaMap[$idSantri] !== $noPeserta) {
                    $duplicateSantri[] = $idSantri;
                }
            }
        }
        
        if (!empty($duplicateSantri)) {
            return [
                'valid' => false,
                'message' => 'Error: Satu IdSantri memiliki NoPeserta yang berbeda: ' . implode(', ', $duplicateSantri)
            ];
        }
        
        // Ekstrak unique NoPeserta (karena satu IdSantri = satu NoPeserta)
        $uniqueNoPesertaList = array_unique(array_column($allNilaiData, 'NoPeserta'));

        // Cek apakah NoPeserta sudah ada di database untuk TypeUjian yang sama
        $existingNoPeserta = $this->munaqosahRegistrasiUjiModel->whereIn('NoPeserta', $uniqueNoPesertaList)
                                                      ->where('IdTahunAjaran', $tahunAjaran)
            ->where('TypeUjian', $typeUjian)
                                                      ->findAll();
        
        if (!empty($existingNoPeserta)) {
            // Jika ada duplikasi dengan database, perbaiki dengan mengubah NoPeserta yang konflik
            $fixedData = $this->fixDuplicateNoPesertaWithDatabase($allNilaiData, $tahunAjaran, $existingNoPeserta, $typeUjian, $minRange, $maxRange);
            return [
                'valid' => true,
                'message' => 'Duplikasi dengan database telah diperbaiki',
                'fixedData' => $fixedData
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Validasi berhasil',
            'fixedData' => $allNilaiData
        ];
    }

    /**
     * Fix duplicate NoPeserta with database
     * @param array $allNilaiData
     * @param int $tahunAjaran
     * @param array $existingNoPeserta
     * @param string $typeUjian
     * @param int $minRange Range minimum dari konfigurasi
     * @param int $maxRange Range maksimum dari konfigurasi
     * @return array $fixedData
     */
    private function fixDuplicateNoPesertaWithDatabase($allNilaiData, $tahunAjaran, $existingNoPeserta, $typeUjian = 'munaqosah', $minRange = 100, $maxRange = 400)
    {
        $fixedData = [];
        $usedNoPeserta = [];
        $existingNumbers = array_column($existingNoPeserta, 'NoPeserta');
        
        // Group data by IdSantri untuk memastikan konsistensi
        $santriNoPesertaMap = [];
        
        foreach ($allNilaiData as $data) {
            $idSantri = $data['IdSantri'];
            $noPeserta = $data['NoPeserta'];
            
            // Jika IdSantri belum diproses atau NoPeserta konflik
            if (!isset($santriNoPesertaMap[$idSantri]) || 
                in_array($noPeserta, $existingNumbers) || 
                in_array($noPeserta, $usedNoPeserta)) {

                // Generate NoPeserta baru untuk IdSantri ini dengan range dari konfigurasi
                $newNoPeserta = $this->generateUniqueNoPeserta($tahunAjaran, $usedNoPeserta, $typeUjian, $minRange, $maxRange);
                $santriNoPesertaMap[$idSantri] = $newNoPeserta;
                $usedNoPeserta[] = $newNoPeserta;
            }
            
            // Gunakan NoPeserta yang sudah ditetapkan untuk IdSantri ini
            $data['NoPeserta'] = $santriNoPesertaMap[$idSantri];
            $fixedData[] = $data;
        }
        
        return $fixedData;
    }

    /**
     * Generate unique key untuk HasKey peserta munaqosah
     * Menggunakan random bytes yang aman seperti di Rapor.php
     */
    private function generateUniqueHasKey()
    {
        do {
            $hasKey = base64_encode(random_bytes(24));
            $hasKey = str_replace(['+', '/', '='], ['-', '_', ''], $hasKey); // URL-safe

        } while ($this->db->table('tbl_munaqosah_peserta')->where('HasKey', $hasKey)->get()->getRow());

        return $hasKey;
    }

    /**
     * Format string menjadi Title Case (huruf kapital di awal setiap kata)
     */
    private function formatTitleCase($string)
    {
        if (empty($string)) {
            return $string;
        }
        
        // Decode HTML entities terlebih dahulu
        $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $string = trim($string);
        
        // Convert to title case
        $words = explode(' ', $string);
        $result = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                // Convert seluruh kata ke lowercase terlebih dahulu
                $word = mb_strtolower($word, 'UTF-8');
                
                // Cek apakah kata mengandung tanda petik
                if (strpos($word, "'") !== false) {
                    // Pisahkan kata berdasarkan tanda petik
                    $parts = explode("'", $word);
                    
                    // Proses setiap bagian
                    foreach ($parts as $key => $part) {
                        if (!empty($part)) {
                            if ($key === 0) {
                                // Untuk bagian pertama, ubah huruf pertama menjadi uppercase
                                $parts[$key] = mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8') .
                                    mb_substr($part, 1, null, 'UTF-8');
                            } else {
                                // Untuk bagian setelah tanda petik, biarkan lowercase
                                $parts[$key] = $part;
                            }
                        }
                    }
                    
                    // Gabungkan kembali dengan tanda petik
                    $word = implode("'", $parts);
                } else {
                    // Jika tidak ada tanda petik, gunakan title case biasa
                    $word = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') .
                        mb_substr($word, 1, null, 'UTF-8');
                }
                
                $result .= $word . ' ';
            }
        }
        
        return trim($result);
    }

    public function getDetailSantri()
    {
        $idSantri = $this->request->getPost('IdSantri');
        
        if (!$idSantri) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Santri tidak boleh kosong'
            ]);
        }

        try {
            // Ambil data santri dari SantriBaruModel
            $santriData = $this->santriBaruModel->getDetailSantri($idSantri);
            
            if (!$santriData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data santri berhasil diambil',
                'data' => $santriData
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getDetailSantri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data santri: ' . $e->getMessage()
            ]);
        }
    }

    public function printKartuUjian()
    {
        try {
            // Set memory limit dan timeout
            ini_set('memory_limit', '256M');
            set_time_limit(300);
            mb_internal_encoding('UTF-8');

            // Ambil data dari POST request
            $santriIds = $this->request->getPost('santri_ids');
            $typeUjian = $this->request->getPost('typeUjian') ?? 'munaqosah';
            $tahunAjaran = $this->request->getPost('tahunAjaran');
            $filterTpq = $this->request->getPost('filterTpq') ?? 0;
            $filterKelas = $this->request->getPost('filterKelas') ?? 0;

            // Validasi input
            if (empty($santriIds)) {
                throw new \Exception('Tidak ada santri yang dipilih untuk dicetak');
            }

            // Decode JSON jika berupa string
            if (is_string($santriIds)) {
                $santriIds = json_decode($santriIds, true);
            }

            if (!is_array($santriIds) || empty($santriIds)) {
                throw new \Exception('Data santri tidak valid');
            }

            // Ambil data peserta munaqosah dengan relasi
            $builder = $this->db->table('tbl_munaqosah_peserta mp');
            $builder->select('mp.*, s.*, t.NamaTpq, k.NamaKelas, 
                            mn.NoPeserta, mn.TypeUjian');
            $builder->join('tbl_santri_baru s', 's.IdSantri = mp.IdSantri', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = mp.IdTpq', 'left');
            $builder->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left');
            $builder->join('tbl_munaqosah_registrasi_uji mn', 'mn.IdSantri = mp.IdSantri AND mn.IdTahunAjaran = mp.IdTahunAjaran AND mn.TypeUjian = "' . $typeUjian . '"', 'left');
            $builder->whereIn('mp.IdSantri', $santriIds);
            $builder->where('mp.IdTahunAjaran', $tahunAjaran);
            $builder->where('mn.NoPeserta IS NOT NULL'); // Hanya ambil yang sudah ada nomor peserta

            // Filter TPQ
            if ($filterTpq != 0) {
                $builder->where('mp.IdTpq', $filterTpq);
            }

            // Filter Kelas
            if ($filterKelas != 0) {
                $builder->where('s.IdKelas', $filterKelas);
            }

            $builder->groupBy('mp.IdSantri');
            $builder->orderBy('mp.IdTpq', 'ASC');
            $builder->orderBy('s.NamaSantri', 'ASC');

            $pesertaData = $builder->get()->getResultArray();

            // Debug log untuk melihat jumlah data
            log_message('info', 'Print Kartu Ujian - Jumlah data peserta: ' . count($pesertaData));
            log_message('info', 'Print Kartu Ujian - Data peserta: ' . json_encode(array_column($pesertaData, 'IdSantri')));

            if (empty($pesertaData)) {
                throw new \Exception('Tidak ada data peserta yang ditemukan untuk dicetak');
            }

            // check HasKey IdSantri di tabel tbl_munaqosah_peserta dari data pesertaData
            $hasKey = $this->db->table('tbl_munaqosah_peserta')
                ->whereIn('IdSantri', array_column($pesertaData, 'IdSantri'))
                ->get()
                ->getResultArray();

            // Buat mapping IdSantri -> HasKey
            $hasKeyMap = [];
            foreach ($hasKey as $hk) {
                $hasKeyMap[$hk['IdSantri']] = $hk['HasKey'];
            }

            // Tambahkan HasKey ke data peserta
            foreach ($pesertaData as &$peserta) {
                $peserta['HasKey'] = $hasKeyMap[$peserta['IdSantri']] ?? null;
            }


            // Siapkan array untuk batch update
            $batchUpdateData = [];

            // Tambahkan QR code ke data peserta
            foreach ($pesertaData as &$peserta) {
                $noPeserta = $peserta['NoPeserta'];

                // Generate QR code langsung untuk nomor peserta
                $qrOptions = new QROptions([
                    'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                    'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                    'scale' => 2,
                    'imageBase64' => false,
                    'addQuietzone' => true,
                    'quietzoneSize' => 1,
                ]);

                // QR Code untuk nomor peserta
                $qrCode = new QRCode($qrOptions);
                $qrContent = (string)$noPeserta;
                $svgContent = $qrCode->render($qrContent);
                $base64Svg = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                $peserta['qrCode'] = '<img src="' . $base64Svg . '" style="width: 40px; height: 40px;" />';

                // QR Code footer untuk link
                $footerQrOptions = new QROptions([
                    'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                    'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                    'scale' => 1,
                    'imageBase64' => false,
                    'addQuietzone' => true,
                    'quietzoneSize' => 1,
                ]);

                // Jika HasKey sudah ada, gunakan HasKey yang sudah ada
                if ($peserta['HasKey'] != null && $peserta['HasKey'] != '') {
                    $hash = $peserta['HasKey'];
                } else {
                    // Generate unique key yang aman
                    $hash = $this->generateUniqueHasKey();
                    $batchUpdateData[] = [
                        'IdSantri' => $peserta['IdSantri'],
                        'HasKey' => $hash
                    ];
                }

                // QR Code footer untuk link hasil ujian
                $footerQrCode = new QRCode($footerQrOptions);
                $footerSvgContent = $footerQrCode->render('https://tpqsmart.simpedis.com/munaqosah/cek-status/' . $hash);
                $footerBase64Svg = 'data:image/svg+xml;base64,' . base64_encode($footerSvgContent);
                $peserta['footerQrCode'] = '<img src="' . $footerBase64Svg . '" style="width: 30px; height: 30px;" />';
            }

            // Batch update HasKey setelah loop selesai
            if (!empty($batchUpdateData)) {
                $this->db->table('tbl_munaqosah_peserta')->updateBatch($batchUpdateData, 'IdSantri');
                log_message('info', 'Batch update HasKey for ' . count($batchUpdateData) . ' participants');
            }

            // Siapkan data untuk view
            $data = [
                'peserta' => $pesertaData,
                'typeUjian' => $typeUjian,
                'tahunAjaran' => $tahunAjaran
            ];

            // Load view untuk PDF
            $html = view('backend/Munaqosah/printKartuUjian', $data);

            // Setup Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'Arial');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'print');
            $options->set('isJavascriptEnabled', false);
            $options->set('isCssFloatEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('debugPng', false);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('F4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = 'kartu_ujian_' . $typeUjian . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // Hapus semua output sebelumnya
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set header
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // Output PDF
            echo $dompdf->output();
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Munaqosah: printKartuUjian - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    // ==================== JURI MUNAQOSAH ====================

    /**
     * Halaman list juri munaqosah
     */
    public function listUserJuriMunaqosah()
    {
        // Ambil IdTpq dari session
        $idTpq = session()->get('IdTpq');
        // Ambil data grup materi ujian langsung dari model
        $DataGrupMateriUjian = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();
        // Ambil data TPQ untuk dropdown dari HelpFunctionModel
        $DataTpqDropdown = $this->helpFunction->getDataTpq($idTpq);
        // Ambil data juri dengan relasi untuk ditampilkan langsung
        $DataJuri = $this->munaqosahJuriModel->getJuriWithRelations($idTpq);

        $roomConfig = $this->getRoomIdRange($idTpq);

        $data = [
            'page_title' => 'Data Juri Munaqosah',
            'juri' => $DataJuri,
            'grupMateriUjian' => $DataGrupMateriUjian,
            'tpqDropdown' => $DataTpqDropdown,
            'roomOptions' => $roomConfig['rooms'],
            'roomIdMin' => $roomConfig['min'],
            'roomIdMax' => $roomConfig['max'],
        ];
        return view('backend/Munaqosah/listUserJuriMunaqosah', $data);
    }


    /**
     * Get grup materi ujian untuk dropdown
     */
    public function getGrupMateriUjian()
    {
        try {
            $grupMateri = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();
            return $this->response->setJSON([
                'success' => true,
                'data' => $grupMateri
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data grup materi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display data nilai juri page
     */
    public function dataNilaiJuri()
    {
        helper('munaqosah');

        $helpFunctionModel = new \App\Models\HelpFunctionModel();
        $currentTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();

        $idTpq = session()->get('IdTpq');
        $dataTpq = $this->helpFunction->getDataTpq($idTpq);

        // Cek apakah user adalah Juri
        $isJuri = in_groups('Juri');
        $currentJuriData = null;
        $currentJuriId = null;
        $currentTypeUjian = null;

        if ($isJuri) {
            // Ambil data juri yang sedang login
            $usernameJuri = user()->username;
            $currentJuriData = $this->munaqosahJuriModel->getJuriByUsernameJuri($usernameJuri);

            if ($currentJuriData) {
                $currentJuriId = $currentJuriData->IdJuri;
                $currentTypeUjian = $currentJuriData->TypeUjian ?? 'munaqosah';
            }
        }

        // Ambil daftar juri untuk dropdown (hanya untuk admin)
        $juriList = [];
        if (!$isJuri) {
            if ($idTpq && $idTpq != 0) {
                $juriList = $this->munaqosahJuriModel->getJuriByTpq($idTpq);
            } else {
                // Jika admin, ambil semua juri aktif
                $juriList = $this->munaqosahJuriModel->where('Status', 'Aktif')->findAll();
            }
        }

        // Ambil nilai min dan max dari konfigurasi
        $nilaiMinimal = 40;
        $nilaiMaximal = 99;
        if ($currentJuriData && !empty($currentJuriData->IdTpq)) {
            $configIdTpq = (string)$currentJuriData->IdTpq;
            $nilaiMinimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NilaiMinimal', 40);
            $nilaiMaximal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NilaiMaximal', 99);
        } else {
            $nilaiMinimal = $this->munaqosahKonfigurasiModel->getSettingAsInt('default', 'NilaiMinimal', 40);
            $nilaiMaximal = $this->munaqosahKonfigurasiModel->getSettingAsInt('default', 'NilaiMaximal', 99);
        }

        $data = [
            'page_title' => 'Data Nilai Juri',
            'current_tahun_ajaran' => $currentTahunAjaran,
            'tpqDropdown' => $dataTpq,
            'juriList' => $juriList,
            'isJuri' => $isJuri,
            'currentJuriId' => $currentJuriId,
            'currentJuriData' => $currentJuriData ? [
                'IdJuri' => $currentJuriData->IdJuri,
                'UsernameJuri' => $currentJuriData->UsernameJuri,
                'TypeUjian' => $currentJuriData->TypeUjian ?? 'munaqosah',
            ] : null,
            'currentTypeUjian' => $currentTypeUjian,
            'nilai_minimal' => $nilaiMinimal,
            'nilai_maximal' => $nilaiMaximal,
        ];

        return view('backend/Munaqosah/dataNilaiJuri', $data);
    }

    /**
     * Get data nilai juri untuk ditampilkan
     */
    public function getDataNilaiJuri()
    {
        $idJuri = $this->request->getPost('IdJuri');
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $typeUjian = $this->request->getPost('TypeUjian') ?? 'munaqosah';

        if (empty($idJuri)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IdJuri harus diisi'
            ]);
        }

        if (empty($idTahunAjaran)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IdTahunAjaran harus diisi'
            ]);
        }

        try {
            // Ambil data juri
            $juriData = $this->munaqosahJuriModel->getJuriByIdJuri($idJuri);
            if (empty($juriData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data juri tidak ditemukan'
                ]);
            }

            $idGrupMateriUjian = $juriData['IdGrupMateriUjian'] ?? null;
            if (empty($idGrupMateriUjian)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Juri tidak memiliki grup materi ujian'
                ]);
            }

            // Ambil data nilai yang sudah diinput oleh juri ini
            $nilaiBuilder = $this->db->table('tbl_munaqosah_nilai n');
            $nilaiBuilder->select('n.*, r.NoPeserta, r.IdSantri, r.IdTpq, r.TypeUjian as RegTypeUjian, 
                s.NamaSantri, t.NamaTpq, km.NamaKategoriMateri');
            $nilaiBuilder->join('tbl_munaqosah_registrasi_uji r', 'r.NoPeserta = n.NoPeserta AND r.IdKategoriMateri = n.IdKategoriMateri AND r.IdTahunAjaran = n.IdTahunAjaran AND r.TypeUjian = n.TypeUjian', 'left');
            $nilaiBuilder->join('tbl_santri_baru s', 's.IdSantri = n.IdSantri', 'left');
            $nilaiBuilder->join('tbl_tpq t', 't.IdTpq = n.IdTpq', 'left');
            $nilaiBuilder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = n.IdKategoriMateri', 'left');
            $nilaiBuilder->where('n.IdJuri', $idJuri);
            $nilaiBuilder->where('n.IdTahunAjaran', $idTahunAjaran);
            $nilaiBuilder->where('n.TypeUjian', $typeUjian);
            $nilaiBuilder->where('n.IdGrupMateriUjian', $idGrupMateriUjian);
            $nilaiBuilder->orderBy('n.NoPeserta', 'ASC');
            $nilaiBuilder->orderBy('n.IdKategoriMateri', 'ASC');

            $nilaiRows = $nilaiBuilder->get()->getResultArray();

            if (empty($nilaiRows)) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'categories' => [],
                        'rows' => [],
                        'meta' => [
                            'IdJuri' => $idJuri,
                            'UsernameJuri' => $juriData['UsernameJuri'] ?? '',
                            'IdGrupMateriUjian' => $idGrupMateriUjian,
                            'IdTahunAjaran' => $idTahunAjaran,
                            'TypeUjian' => $typeUjian,
                        ]
                    ]
                ]);
            }

            // Buat map kategori materi
            $categoriesMap = [];
            foreach ($nilaiRows as $row) {
                $catId = $row['IdKategoriMateri'];
                if (empty($catId)) continue;

                if (!isset($categoriesMap[$catId])) {
                    $categoriesMap[$catId] = [
                        'id' => $catId,
                        'name' => $row['NamaKategoriMateri'] ?? $catId,
                        'IdGrupMateriUjian' => $idGrupMateriUjian,
                    ];
                }
            }

            // Ambil konfigurasi MaxJuriPerRoom untuk grup materi
            $configIdTpq = (!empty($juriData['IdTpq'])) ? (string)$juriData['IdTpq'] : 'default';
            $settingKey = 'MaxJuriPerRoom_' . $idGrupMateriUjian;
            $maxJuriSetting = $this->munaqosahKonfigurasiModel->getSetting($configIdTpq, $settingKey);
            $maxJuri = ($maxJuriSetting !== null && is_numeric($maxJuriSetting)) ? (int)$maxJuriSetting : 2;

            // Set maxJuri untuk setiap kategori
            foreach ($categoriesMap as $catId => $catData) {
                $categoriesMap[$catId]['maxJuri'] = $maxJuri;
            }

            ksort($categoriesMap);
            $categories = array_values($categoriesMap);

            // Buat map peserta dengan nilai
            $pesertaMap = [];
            foreach ($nilaiRows as $row) {
                $np = $row['NoPeserta'];
                $catId = $row['IdKategoriMateri'];

                if (!isset($pesertaMap[$np])) {
                    $pesertaMap[$np] = [
                        'NoPeserta' => $np,
                        'IdSantri' => $row['IdSantri'],
                        'IdTpq' => $row['IdTpq'],
                        'NamaSantri' => $row['NamaSantri'] ?? '-',
                        'NamaTpq' => $row['NamaTpq'] ?? '-',
                        'TypeUjian' => $row['RegTypeUjian'] ?? $typeUjian,
                        'IdTahunAjaran' => $idTahunAjaran,
                        'nilai' => [],
                        'nilaiIds' => [], // Untuk menyimpan ID nilai untuk edit
                    ];
                }

                // Simpan nilai dengan ID
                if (!isset($pesertaMap[$np]['nilai'][$catId])) {
                    $pesertaMap[$np]['nilai'][$catId] = [];
                    $pesertaMap[$np]['nilaiIds'][$catId] = [];
                }

                // Simpan nilai dan ID
                $pesertaMap[$np]['nilai'][$catId][0] = (float)$row['Nilai'];
                $pesertaMap[$np]['nilaiIds'][$catId][0] = $row['id']; // ID dari tabel nilai untuk edit
            }

            // Convert pesertaMap ke array
            $rows = array_values($pesertaMap);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'rows' => $rows,
                    'meta' => [
                        'IdJuri' => $idJuri,
                        'UsernameJuri' => $juriData['UsernameJuri'] ?? '',
                        'IdGrupMateriUjian' => $idGrupMateriUjian,
                        'IdTahunAjaran' => $idTahunAjaran,
                        'TypeUjian' => $typeUjian,
                        'maxJuri' => $maxJuri,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getDataNilaiJuri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get detail nilai untuk edit
     */
    public function getDetailNilai()
    {
        $id = $this->request->getPost('id');

        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID nilai harus diisi'
            ]);
        }

        try {
            $nilai = $this->nilaiMunaqosahModel->find($id);
            if (empty($nilai)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data nilai tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $nilai
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getDetailNilai: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update nilai
     */
    public function updateNilai()
    {
        $id = $this->request->getPost('id');
        $nilai = $this->request->getPost('Nilai');
        $catatan = $this->request->getPost('Catatan');

        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID nilai harus diisi'
            ]);
        }

        if ($nilai === null || $nilai === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nilai harus diisi'
            ]);
        }

        $nilai = (float)$nilai;
        if ($nilai < 0 || $nilai > 100) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nilai harus antara 0-100'
            ]);
        }

        try {
            // Ambil data nilai yang akan diupdate
            $nilaiData = $this->nilaiMunaqosahModel->find($id);
            if (empty($nilaiData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data nilai tidak ditemukan'
                ]);
            }

            // Update data
            $updateData = [
                'Nilai' => $nilai,
                'Catatan' => $catatan ?? $nilaiData['Catatan'],
                'IsModified' => 1,
                'ModifiedBy' => session()->get('username') ?? 'System',
                'ModifiedAt' => date('Y-m-d H:i:s'),
                'ModificationReason' => 'Edit dari halaman Data Nilai Juri'
            ];

            if ($this->nilaiMunaqosahModel->update($id, $updateData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Nilai berhasil diperbarui'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui nilai',
                    'errors' => $this->nilaiMunaqosahModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in updateNilai: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Verify Admin/Operator credentials for editing nilai
     */
    public function verifyEditNilaiCredentials()
    {
        try {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $typeUjian = $this->request->getPost('typeUjian') ?? 'munaqosah';
            $idNilai = $this->request->getPost('idNilai'); // ID nilai yang akan diedit

            if (empty($username)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'message' => 'Username tidak boleh kosong'
                ]);
            }

            if (empty($password)) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'VALIDATION_ERROR',
                    'message' => 'Password tidak boleh kosong'
                ]);
            }

            // Verify user
            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('username', $username)->first();

            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'AUTHENTICATION_ERROR',
                    'message' => 'Username tidak ditemukan'
                ]);
            }

            if (!$user['active']) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'AUTHENTICATION_ERROR',
                    'message' => 'User tidak aktif'
                ]);
            }

            // Verify password using Myth\Auth\Password
            $passwordLib = new \Myth\Auth\Password();
            if (!$passwordLib->verify($password, $user['password_hash'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'AUTHENTICATION_ERROR',
                    'message' => 'Password tidak valid'
                ]);
            }

            // Check authorization based on typeUjian - query groups dari database
            $builder = $this->db->table('auth_groups_users agu');
            $builder->select('ag.name');
            $builder->join('auth_groups ag', 'ag.id = agu.group_id', 'inner');
            $builder->where('agu.user_id', $user['id']);

            $userGroups = $builder->get()->getResultArray();

            $isAdmin = false;
            $isOperator = false;

            foreach ($userGroups as $group) {
                if ($group['name'] === 'Admin') {
                    $isAdmin = true;
                }
                if ($group['name'] === 'Operator') {
                    $isOperator = true;
                }
            }

            if ($typeUjian === 'munaqosah') {
                // Untuk munaqosah, harus Admin
                if (!$isAdmin) {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'AUTHORIZATION_ERROR',
                        'message' => 'Hanya Admin yang dapat mengedit nilai Munaqosah'
                    ]);
                }
            } else if ($typeUjian === 'pra-munaqosah') {
                // Untuk pra-munaqosah, harus Operator
                if (!$isOperator) {
                    return $this->response->setJSON([
                        'success' => false,
                        'status' => 'AUTHORIZATION_ERROR',
                        'message' => 'Hanya Operator yang dapat mengedit nilai Pra-Munaqosah'
                    ]);
                }

                // Validasi IdTpq: Operator hanya bisa edit nilai dari juri yang berasal dari TPQ yang sama
                if (!empty($idNilai)) {
                    // Ambil data nilai untuk mendapatkan IdTpq dari juri
                    $nilaiData = $this->nilaiMunaqosahModel->find($idNilai);
                    if (!empty($nilaiData)) {
                        // Ambil IdJuri dari nilai
                        $idJuri = $nilaiData['IdJuri'] ?? null;
                        if (!empty($idJuri)) {
                            // Ambil data juri untuk mendapatkan IdTpq
                            $juriData = $this->munaqosahJuriModel->getJuriByIdJuri($idJuri);
                            $idTpqJuri = $juriData['IdTpq'] ?? null;

                            // Ambil IdTpq dari Operator (user)
                            // Operator biasanya terkait dengan TPQ melalui tabel tbl_guru atau langsung dari user
                            $operatorIdTpq = null;

                            // Coba ambil dari tabel guru jika user terkait dengan guru
                            $guruData = $this->db->table('tbl_guru')
                                ->where('IdGuru', $user['nik'])
                                ->get()
                                ->getRowArray();

                            if ($guruData && !empty($guruData)) {
                                $operatorIdTpq = $guruData['IdTpq'] ?? null;
                            } else {
                                // Jika tidak ada di guru, coba ambil dari user langsung (jika ada field IdTpq di users)
                                $operatorIdTpq = $user['IdTpq'] ?? null;
                            }

                            // Validasi: IdTpq Operator harus sama dengan IdTpq Juri
                            if (!empty($idTpqJuri) && !empty($operatorIdTpq)) {
                                // Konversi ke string untuk perbandingan yang konsisten
                                $idTpqJuriStr = (string)$idTpqJuri;
                                $operatorIdTpqStr = (string)$operatorIdTpq;

                                if ($idTpqJuriStr !== $operatorIdTpqStr) {
                                    return $this->response->setJSON([
                                        'success' => false,
                                        'status' => 'AUTHORIZATION_ERROR',
                                        'message' => 'Operator hanya dapat mengedit nilai dari juri yang berasal dari TPQ yang sama. IdTpq Operator: ' . $operatorIdTpqStr . ', IdTpq Juri: ' . $idTpqJuriStr
                                    ]);
                                }
                            } else if (empty($operatorIdTpq)) {
                                return $this->response->setJSON([
                                    'success' => false,
                                    'status' => 'AUTHORIZATION_ERROR',
                                    'message' => 'Operator tidak memiliki IdTpq yang valid'
                                ]);
                            }
                        }
                    }
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kredensial valid'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in verifyEditNilaiCredentials: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'SYSTEM_ERROR',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get data peserta untuk edit nilai (mirip cekPeserta)
     */
    public function getPesertaForEditNilai()
    {
        try {
            $noPeserta = $this->request->getPost('noPeserta');
            $idNilai = $this->request->getPost('idNilai');

            if (empty($noPeserta) && empty($idNilai)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No Peserta atau ID Nilai harus diisi'
                ]);
            }

            // Ambil data nilai
            $nilaiPertama = null;
            if (!empty($idNilai)) {
                $nilaiPertama = $this->nilaiMunaqosahModel->find($idNilai);
                if (empty($nilaiPertama)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Data nilai tidak ditemukan'
                    ]);
                }
                $noPeserta = $nilaiPertama['NoPeserta'];
            }

            // Ambil data registrasi peserta
            $registrasi = $this->munaqosahRegistrasiUjiModel
                ->where('NoPeserta', $noPeserta)
                ->where('IdTahunAjaran', $nilaiPertama['IdTahunAjaran'] ?? session()->get('IdTahunAjaran'))
                ->where('TypeUjian', $nilaiPertama['TypeUjian'] ?? 'munaqosah')
                ->first();

            if (empty($registrasi)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data registrasi peserta tidak ditemukan'
                ]);
            }

            // Ambil data santri
            $santriData = $this->santriBaruModel->getDetailSantri($registrasi['IdSantri']);
            if (empty($santriData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            // Ambil data juri dari nilai
            $juriData = $this->munaqosahJuriModel->getJuriByIdJuri($nilaiPertama['IdJuri'] ?? '');

            // Ambil grup materi
            $grupMateri = $this->grupMateriUjiMunaqosahModel
                ->where('IdGrupMateriUjian', $nilaiPertama['IdGrupMateriUjian'] ?? '')
                ->first();

            // Ambil materi berdasarkan registrasi
            $materiData = $this->munaqosahRegistrasiUjiModel->getMateriByNoPesertaAndGrup(
                $noPeserta,
                $nilaiPertama['IdGrupMateriUjian'] ?? '',
                $nilaiPertama['TypeUjian'] ?? 'munaqosah',
                $nilaiPertama['IdTahunAjaran'] ?? session()->get('IdTahunAjaran')
            );

            // Transform data materi untuk konsistensi
            $transformedMateriData = [];
            foreach ($materiData as $materi) {
                $namaKategori = $materi['NamaKategoriMateri'] ?? ($materi['KategoriMateriUjian'] ?? null);

                $transformedMateriData[] = [
                    'IdMateri' => $materi['IdMateri'],
                    'NamaMateri' => $materi['NamaMateri'],
                    'IdKategoriMateri' => $materi['IdKategoriMateri'] ?? null,
                    'NamaKategoriMateri' => $namaKategori,
                    'KategoriMateriUjian' => $namaKategori,
                    'IdGrupMateriUjian' => $materi['IdGrupMateriUjian'],
                    'WebLinkAyat' => isset($materi['WebLinkAyat']) ? $materi['WebLinkAyat'] : null,
                    'KategoriAsli' => isset($materi['KategoriAsli']) ? $materi['KategoriAsli'] : null
                ];
            }

            // Ambil semua nilai yang sudah ada untuk peserta ini (dari juri yang sama)
            $nilaiYangAda = $this->nilaiMunaqosahModel
                ->where('NoPeserta', $noPeserta)
                ->where('IdJuri', $nilaiPertama['IdJuri'] ?? '')
                ->where('IdTahunAjaran', $nilaiPertama['IdTahunAjaran'] ?? '')
                ->where('TypeUjian', $nilaiPertama['TypeUjian'] ?? '')
                ->findAll();

            // Buat map nilai yang sudah ada berdasarkan IdMateri
            $nilaiMap = [];
            foreach ($nilaiYangAda as $nilai) {
                $nilaiMap[$nilai['IdMateri']] = [
                    'id' => $nilai['id'],
                    'Nilai' => $nilai['Nilai'],
                    'Catatan' => $nilai['Catatan']
                ];
            }

            // Ambil kategori kesalahan per kategori materi (mirip cekPeserta)
            $kategoriNames = array_unique(array_filter(array_map(function ($materi) {
                return $materi['KategoriMateriUjian'] ?? $materi['NamaKategoriMateri'] ?? null;
            }, $transformedMateriData)));

            $errorCategoriesByKategori = [];
            if (!empty($kategoriNames)) {
                $errorCategoryRows = $this->munaqosahKategoriKesalahanModel
                    ->select('tbl_munaqosah_kategori_kesalahan.NamaKategoriKesalahan, tbl_kategori_materi.NamaKategoriMateri')
                    ->join('tbl_kategori_materi', 'tbl_kategori_materi.IdKategoriMateri = tbl_munaqosah_kategori_kesalahan.IdKategoriMateri', 'left')
                    ->whereIn('tbl_kategori_materi.NamaKategoriMateri', $kategoriNames)
                    ->where('tbl_munaqosah_kategori_kesalahan.Status', 'Aktif')
                    ->orderBy('tbl_kategori_materi.NamaKategoriMateri', 'ASC')
                    ->orderBy('tbl_munaqosah_kategori_kesalahan.IdKategoriKesalahan', 'ASC')
                    ->findAll();

                foreach ($errorCategoryRows as $row) {
                    $kategoriName = $row['NamaKategoriMateri'];
                    if (!isset($errorCategoriesByKategori[$kategoriName])) {
                        $errorCategoriesByKategori[$kategoriName] = [];
                    }
                    $errorCategoriesByKategori[$kategoriName][] = $row['NamaKategoriKesalahan'];
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'peserta' => [
                        'NoPeserta' => $noPeserta,
                        'IdSantri' => $santriData['IdSantri'],
                        'NamaSantri' => $santriData['NamaSantri'],
                        'IdTpq' => $registrasi['IdTpq'],
                    ],
                    'juri' => [
                        'IdJuri' => $juriData['IdJuri'] ?? '',
                        'UsernameJuri' => $juriData['UsernameJuri'] ?? '',
                        'NamaMateriGrup' => $grupMateri['NamaMateriGrup'] ?? '',
                        'RoomId' => $juriData['RoomId'] ?? '',
                    ],
                    'materi' => $transformedMateriData,
                    'nilai_yang_ada' => $nilaiMap,
                    'error_categories' => $errorCategoriesByKategori,
                    'typeUjian' => $nilaiPertama['TypeUjian'] ?? 'munaqosah',
                    'idTahunAjaran' => $nilaiPertama['IdTahunAjaran'] ?? session()->get('IdTahunAjaran'),
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getPesertaForEditNilai: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update nilai dengan alasan edit (versi baru)
     */
    public function updateNilaiWithReason()
    {
        $idNilai = $this->request->getPost('idNilai');
        $alasanEdit = $this->request->getPost('alasanEdit');
        $nilaiData = $this->request->getPost('nilai');
        $catatanData = $this->request->getPost('catatan') ?? [];

        if (empty($idNilai)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID nilai harus diisi'
            ]);
        }

        if (empty($alasanEdit)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Alasan edit harus diisi'
            ]);
        }

        if (empty($nilaiData) || !is_array($nilaiData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data nilai harus diisi'
            ]);
        }

        try {
            // Ambil data nilai pertama untuk mendapatkan info dasar
            $nilaiPertama = $this->nilaiMunaqosahModel->find($idNilai);
            if (empty($nilaiPertama)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data nilai tidak ditemukan'
                ]);
            }

            $noPeserta = $nilaiPertama['NoPeserta'];
            $idJuri = $nilaiPertama['IdJuri'];
            $typeUjian = $nilaiPertama['TypeUjian'];
            $idTahunAjaran = $nilaiPertama['IdTahunAjaran'];

            // Validasi nilai
            foreach ($nilaiData as $idMateri => $nilai) {
                if (!is_numeric($nilai)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Format nilai tidak valid untuk materi ' . $idMateri
                    ]);
                }

                $nilai = floatval($nilai);
                // Ambil nilai min/max dari konfigurasi
                $configIdTpq = $nilaiPertama['IdTpq'] ?? 'default';
                $nilaiMinimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NilaiMinimal', 40);
                $nilaiMaximal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NilaiMaximal', 99);

                if ($nilai < $nilaiMinimal || $nilai > $nilaiMaximal) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Nilai untuk materi ' . $idMateri . ' harus dalam range ' . $nilaiMinimal . '-' . $nilaiMaximal
                    ]);
                }
            }

            // Update semua nilai untuk peserta ini dari juri yang sama
            $updateCount = 0;
            foreach ($nilaiData as $idMateri => $nilai) {
                // Cari nilai berdasarkan IdMateri, NoPeserta, dan IdJuri
                $nilaiRecord = $this->nilaiMunaqosahModel
                    ->where('IdMateri', $idMateri)
                    ->where('NoPeserta', $noPeserta)
                    ->where('IdJuri', $idJuri)
                    ->where('IdTahunAjaran', $idTahunAjaran)
                    ->where('TypeUjian', $typeUjian)
                    ->first();

                if ($nilaiRecord) {
                    $catatan = isset($catatanData[$idMateri]) ? $catatanData[$idMateri] : $nilaiRecord['Catatan'];

                    $updateData = [
                        'Nilai' => (float)$nilai,
                        'Catatan' => $catatan,
                        'IsModified' => 1,
                        'ModifiedBy' => session()->get('username') ?? 'System',
                        'ModifiedAt' => date('Y-m-d H:i:s'),
                        'ModificationReason' => $alasanEdit
                    ];

                    if ($this->nilaiMunaqosahModel->update($nilaiRecord['id'], $updateData)) {
                        $updateCount++;
                    }
                }
            }

            if ($updateCount > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Nilai berhasil diperbarui (' . $updateCount . ' nilai)'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada nilai yang berhasil diperbarui'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in updateNilaiWithReason: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get TPQ data untuk dropdown
     */
    public function getTpqDataForJuri()
    {
        try {
            $tpq = $this->tpqModel->findAll();
            return $this->response->setJSON([
                'success' => true,
                'data' => $tpq
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data TPQ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate username juri berdasarkan grup materi dan TPQ
     */
    public function generateUsernameJuri()
    {
        try {
            $idGrupMateriUjian = $this->request->getPost('IdGrupMateriUjian');
            $idTpq = $this->request->getPost('IdTpq');
            // Ambil nama grup materi dari tbl_munaqosah_grup_materi_uji
            $namaGrupMateri = $this->grupMateriUjiMunaqosahModel->getGrupMateriById($idGrupMateriUjian);

            if (!$idGrupMateriUjian) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID Grup Materi Ujian harus diisi'
                ]);
            }

            $usernameJuri = $this->munaqosahJuriModel->generateUsernameJuri($namaGrupMateri, $idTpq);

            if (!$usernameJuri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal generate username juri'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'username' => $usernameJuri
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal generate username: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save juri baru
     */
    public function saveJuri()
    {
        try {
            $validation = \Config\Services::validation();

            // Set validation rules (TypeUjian ditentukan server-side berdasar IdTpq)
            $validation->setRules([
                'IdGrupMateriUjian' => 'required',
                'UsernameJuri' => 'required|max_length[100]|is_unique[tbl_munaqosah_juri.UsernameJuri]',
                'Status' => 'required|in_list[Aktif,Tidak Aktif]'
            ], [
                'IdGrupMateriUjian' => [
                    'required' => 'Grup Materi Ujian harus dipilih'
                ],
                'UsernameJuri' => [
                    'required' => 'Username Juri harus diisi',
                    'max_length' => 'Username Juri maksimal 100 karakter',
                    'is_unique' => 'Username Juri sudah digunakan'
                ],
                'Status' => [
                    'required' => 'Status harus dipilih',
                    'in_list' => 'Status harus Aktif atau Tidak Aktif'
                ]
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $errors = $validation->getErrors();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $errors
                ]);
            }

            // Generate IdJuri
            $idJuri = $this->munaqosahJuriModel->generateNextIdJuri();

            // Tentukan TypeUjian berdasarkan IdTpq (jika ada nilai dan bukan '0' maka pra-munaqosah, else munaqosah)
            $idTpqPost = $this->request->getPost('IdTpq');
            $computedTypeUjian = (!empty($idTpqPost) && $idTpqPost !== '0') ? 'pra-munaqosah' : 'munaqosah';

            $sessionIdTpq = session()->get('IdTpq');
            $roomConfigReferenceId = ($idTpqPost !== null && $idTpqPost !== '') ? $idTpqPost : $sessionIdTpq;
            $roomConfig = $this->getRoomIdRange($roomConfigReferenceId);
            $allowedRooms = $roomConfig['rooms'];
            $roomIdMinLabel = sprintf('ROOM-%02d', $roomConfig['min']);
            $roomIdMaxLabel = sprintf('ROOM-%02d', $roomConfig['max']);

            // Validasi dan siapkan RoomId
            $roomIdPost = $this->request->getPost('RoomId');
            $roomId = null;
            if ($roomIdPost !== null && $roomIdPost !== '') {
                $roomIdPost = strtoupper(trim($roomIdPost));

                if (empty($allowedRooms) || !in_array($roomIdPost, $allowedRooms, true)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => [
                            'RoomId' => sprintf('Room ID tidak valid. Pilih %s sampai %s.', $roomIdMinLabel, $roomIdMaxLabel)
                        ]
                    ]);
                }

                $roomId = $roomIdPost;
            }

            // Prepare data
            $data = [
                'IdJuri' => $idJuri,
                'IdTpq' => $this->request->getPost('IdTpq') ?: null,
                'UsernameJuri' => $this->request->getPost('UsernameJuri'),
                'IdGrupMateriUjian' => $this->request->getPost('IdGrupMateriUjian'),
                'RoomId' => $roomId,
                'TypeUjian' => $computedTypeUjian,
                'Status' => $this->request->getPost('Status')
            ];

            // Start database transaction
            $this->db->transStart();

            // Save to tbl_munaqosah_juri
            if (!$this->munaqosahJuriModel->save($data)) {
                throw new \Exception('Gagal menyimpan data juri: ' . implode(', ', $this->munaqosahJuriModel->errors()));
            }

            // Create user in MyAuth
            $email = $data['UsernameJuri'] . '@smartpq.simpedis.com';
            $password = $this->request->getPost('PasswordJuri') ?: 'JuriTpqSmart';

            // Insert to users table
            $userData = [
                'username' => $data['UsernameJuri'],
                'email' => $email,
                'password_hash' => Password::hash($password),
                'active' => 1
            ];

            $this->db->table('users')->insert($userData);
            $userId = $this->db->insertID();

            // Insert to auth_groups_users table
            $groupData = [
                'group_id' => 5, // Group ID untuk juri
                'user_id' => $userId,
            ];

            $this->db->table('auth_groups_users')->insert($groupData);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data juri berhasil disimpan',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data juri: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Update room juri
     */
    public function updateRoomJuri($id)
    {
        try {
            $roomIdPost = $this->request->getPost('RoomId');
            $roomIdPost = $roomIdPost !== null ? trim($roomIdPost) : '';

            $existingJuri = $this->munaqosahJuriModel->find($id);
            if (!$existingJuri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data juri tidak ditemukan'
                ]);
            }

            $sessionIdTpq = session()->get('IdTpq');
            $roomConfigReferenceId = (!empty($existingJuri['IdTpq']) || $existingJuri['IdTpq'] === '0')
                ? $existingJuri['IdTpq']
                : $sessionIdTpq;

            $roomConfig = $this->getRoomIdRange($roomConfigReferenceId);
            $allowedRooms = $roomConfig['rooms'];
            $roomIdMinLabel = sprintf('ROOM-%02d', $roomConfig['min']);
            $roomIdMaxLabel = sprintf('ROOM-%02d', $roomConfig['max']);

            $roomId = null;
            if ($roomIdPost !== '') {
                $roomIdPost = strtoupper($roomIdPost);
                if (empty($allowedRooms) || !in_array($roomIdPost, $allowedRooms, true)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Room ID tidak valid. Pilih ' . $roomIdMinLabel . ' sampai ' . $roomIdMaxLabel . '.'
                    ]);
                }
                $roomId = $roomIdPost;
            }

            $this->munaqosahJuriModel->update($id, [
                'RoomId' => $roomId
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Room juri berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update password juri
     */
    public function updatePasswordJuri($id)
    {
        try {
            $password = $this->request->getPost('password');

            if (!$password) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password harus diisi'
                ]);
            }

            // Get existing juri data
            $existingJuri = $this->munaqosahJuriModel->find($id);
            if (!$existingJuri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data juri tidak ditemukan'
                ]);
            }

            // Update password in users table
            $this->db->table('users')
                ->where('username', $existingJuri['UsernameJuri'])
                ->update([
                    'password_hash' => Password::hash($password),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password juri berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate password: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete juri
     */
    public function deleteJuri($id)
    {
        try {
            // Get existing data
            $existingJuri = $this->munaqosahJuriModel->find($id);
            if (!$existingJuri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data juri tidak ditemukan'
                ]);
            }

            // Start database transaction
            $this->db->transStart();

            // Delete from auth_groups_users
            $this->db->table('auth_groups_users')
                ->where('user_id', function ($builder) use ($existingJuri) {
                    $builder->select('id')
                        ->from('users')
                        ->where('username', $existingJuri['UsernameJuri']);
                })
                ->delete();

            // Delete from users
            $this->db->table('users')
                ->where('username', $existingJuri['UsernameJuri'])
                ->delete();

            // Delete from tbl_munaqosah_juri
            if (!$this->munaqosahJuriModel->delete($id)) {
                throw new \Exception('Gagal menghapus data juri');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data juri berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data juri: ' . $e->getMessage()
            ]);
        }
    }

    public function updateSantri()
    {
        $validation = \Config\Services::validation();
        
        // Set validation rules
        $validation->setRules([
            'IdSantri' => 'required',
            'NamaSantri' => 'required|min_length[3]|max_length[100]',
            'TempatLahirSantri' => 'required|min_length[2]|max_length[100]',
            'TanggalLahirSantri' => 'required|valid_date',
            'JenisKelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'NamaAyah' => 'required|min_length[3]|max_length[100]'
        ], [
            'IdSantri' => [
                'required' => 'ID Santri harus diisi'
            ],
            'NamaSantri' => [
                'required' => 'Nama Santri harus diisi',
                'min_length' => 'Nama Santri minimal 3 karakter',
                'max_length' => 'Nama Santri maksimal 100 karakter'
            ],
            'TempatLahirSantri' => [
                'required' => 'Tempat Lahir harus diisi',
                'min_length' => 'Tempat Lahir minimal 2 karakter',
                'max_length' => 'Tempat Lahir maksimal 100 karakter'
            ],
            'TanggalLahirSantri' => [
                'required' => 'Tanggal Lahir harus diisi',
                'valid_date' => 'Format tanggal tidak valid'
            ],
            'JenisKelamin' => [
                'required' => 'Jenis Kelamin harus diisi',
                'in_list' => 'Jenis Kelamin harus Laki-laki atau Perempuan'
            ],
            'NamaAyah' => [
                'required' => 'Nama Ayah harus diisi',
                'min_length' => 'Nama Ayah minimal 3 karakter',
                'max_length' => 'Nama Ayah maksimal 100 karakter'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $detailedErrors = [];
            
            foreach ($errors as $field => $error) {
                $detailedErrors[] = $error;
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi input gagal',
                'detailed_errors' => $detailedErrors
            ]);
        }

        try {
            $idSantri = $this->request->getPost('IdSantri');
            
            // Cek apakah santri ada
            $existingSantri = $this->santriBaruModel->getDetailSantri($idSantri);
            if (!$existingSantri) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan'
                ]);
            }

            // Format data sebelum disimpan
            $namaSantri = $this->formatTitleCase($this->request->getPost('NamaSantri'));
            $tempatLahirSantri = $this->formatTitleCase($this->request->getPost('TempatLahirSantri'));
            $tanggalLahirSantri = $this->request->getPost('TanggalLahirSantri');
            $tanggalLahirSantri = date('Y-m-d', strtotime($tanggalLahirSantri));
            $jenisKelamin = $this->request->getPost('JenisKelamin');
            $namaAyah = $this->formatTitleCase($this->request->getPost('NamaAyah'));

            // Bandingkan data lama dan baru
            $changes = [];
            $changeMessages = [];
            
            // Compare NamaSantri
            if ($existingSantri['NamaSantri'] !== $namaSantri) {
                $changes['NamaSantri'] = $namaSantri;
                $changeMessages[] = "Nama Santri: '{$existingSantri['NamaSantri']}' → '{$namaSantri}'";
            }
            
            // Compare TempatLahirSantri
            if ($existingSantri['TempatLahirSantri'] !== $tempatLahirSantri) {
                $changes['TempatLahirSantri'] = $tempatLahirSantri;
                $changeMessages[] = "Tempat Lahir: '{$existingSantri['TempatLahirSantri']}' → '{$tempatLahirSantri}'";
            }
            
            // Compare TanggalLahirSantri
            $existingTanggal = date('Y-m-d', strtotime($existingSantri['TanggalLahirSantri']));
            if ($existingTanggal !== $tanggalLahirSantri) {
                $changes['TanggalLahirSantri'] = $tanggalLahirSantri;
                $changeMessages[] = "Tanggal Lahir: '{$existingTanggal}' → '{$tanggalLahirSantri}'";
            }
            
            // Compare JenisKelamin
            if ($existingSantri['JenisKelamin'] !== $jenisKelamin) {
                $changes['JenisKelamin'] = $jenisKelamin;
                $changeMessages[] = "Jenis Kelamin: '{$existingSantri['JenisKelamin']}' → '{$jenisKelamin}'";
            }
            
            // Compare NamaAyah
            if ($existingSantri['NamaAyah'] !== $namaAyah) {
                $changes['NamaAyah'] = $namaAyah;
                $changeMessages[] = "Nama Ayah: '{$existingSantri['NamaAyah']}' → '{$namaAyah}'";
            }
            
            // Cek apakah ada perubahan
            if (empty($changes)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tidak ada perubahan data. Semua data sudah sesuai dengan yang tersimpan.',
                    'no_changes' => true,
                    'data' => $existingSantri
                ]);
            }
            
            // Tambahkan updated_at ke changes
            $changes['updated_at'] = date('Y-m-d H:i:s');
            
            // Update data santri hanya field yang berubah
            $result = $this->santriBaruModel->update($existingSantri['id'], $changes);
            
            if ($result) {
                // Ambil data terbaru untuk response
                $updatedData = $this->santriBaruModel->getDetailSantri($idSantri);
                
                log_message('info', 'Santri updated successfully: ' . $idSantri . ' - Changes: ' . implode(', ', array_keys($changes)));
                
                $changeSummary = implode('<br>', $changeMessages);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data santri berhasil diperbarui',
                    'changes' => $changeSummary,
                    'change_count' => count($changes) - 1, // -1 untuk updated_at
                    'data' => $updatedData
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui data santri'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error in updateSantri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data santri: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current tahun ajaran
     */
    public function getCurrentTahunAjaran()
    {
        try {
            $helpFunctionModel = new \App\Models\HelpFunctionModel();
            $tahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();

            return $this->response->setJSON([
                'success' => true,
                'status' => 'SUCCESS',
                'code' => 'TAHUN_AJARAN_FOUND',
                'message' => 'Tahun ajaran berhasil diambil',
                'data' => [
                    'IdTahunAjaran' => $tahunAjaran
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getCurrentTahunAjaran: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'SYSTEM_ERROR',
                'code' => 'INTERNAL_ERROR',
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }


    // ==================== MONITORING ====================

    public function monitoringMunaqosah()
    {
        helper('munaqosah');

        $helpFunctionModel = new \App\Models\HelpFunctionModel();
        $currentTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();

        $idTpq = session()->get('IdTpq');
        $dataTpq = $this->helpFunction->getDataTpq($idTpq);

        $statistik = getStatistikMunaqosah();

        $data = [
            'page_title' => 'Monitoring Munaqosah',
            'current_tahun_ajaran' => $currentTahunAjaran,
            'tpqDropdown' => $dataTpq,
            'statistik' => $statistik,
        ];

        return view('backend/Munaqosah/monitoringMunaqosah', $data);
    }

    public function dashboardMonitoring()
    {
        helper('munaqosah');

        $helpFunctionModel = new \App\Models\HelpFunctionModel();
        $currentTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();

        $idTpq = session()->get('IdTpq');
        $sessionIdTpq = session()->get('IdTpq');
        $selectedTpq = $this->request->getGet('tpq');
        $selectedType = $this->request->getGet('type') ?? 'pra-munaqosah'; // Default untuk dashboard

        // Jika user login sebagai Juri, ambil IdTpq dari data juri
        $isJuri = in_groups('Juri');
        if ($isJuri) {
            $usernameJuri = user()->username;
            $juriData = $this->munaqosahJuriModel->getJuriByUsernameJuri($usernameJuri);
            if ($juriData) {
                // Set IdTpq jika ada
                if (!empty($juriData->IdTpq)) {
                    $selectedTpq = $juriData->IdTpq;
                    $sessionIdTpq = $juriData->IdTpq;
                }

                // Set TypeUjian berdasarkan IdTpq: jika ada IdTpq -> pra-munaqosah, jika tidak -> munaqosah
                if (!empty($juriData->IdTpq)) {
                    $selectedType = 'pra-munaqosah';
                } else {
                    $selectedType = 'munaqosah';
                }
            }
        }
        // Jika user login sebagai admin TPQ/Operator
        elseif (!empty($sessionIdTpq)) {
            $selectedTpq = $sessionIdTpq;
        }

        $typeOptions = [
            'pra-munaqosah' => 'Pra Munaqosah',
            'munaqosah' => 'Munaqosah'
        ];

        // Ambil data TPQ dari query grouped tabel tbl_munaqosah_registrasi_uji
        $builder = $this->db->table('tbl_munaqosah_registrasi_uji r');
        $builder->select('r.IdTpq, t.NamaTpq');
        $builder->join('tbl_tpq t', 't.IdTpq = r.IdTpq', 'left');
        $builder->where('r.IdTahunAjaran', $currentTahunAjaran);
        $builder->where('r.TypeUjian', $selectedType);

        // Jika user login sebagai Juri, operator TPQ, atau admin TPQ, filter berdasarkan IdTpq
        if (!empty($sessionIdTpq)) {
            $builder->where('r.IdTpq', $sessionIdTpq);
        }

        $builder->groupBy('r.IdTpq');
        $builder->orderBy('t.NamaTpq', 'ASC');
        $dataTpq = $builder->get()->getResultArray();

        $statistik = getStatistikMunaqosah();

        // Ambil semua grup materi aktif
        $grupList = $this->grupMateriUjiMunaqosahModel->getGrupMateriAktif();

        // Data antrian untuk semua grup
        $antrianData = [];

        // Ambil statistik antrian untuk setiap grup
        foreach ($grupList as $grup) {
            $filters = [
                'IdTahunAjaran' => $currentTahunAjaran,
                'IdGrupMateriUjian' => $grup['IdGrupMateriUjian'],
                'TypeUjian' => $selectedType,
            ];

            if (!empty($selectedTpq)) {
                $filters['IdTpq'] = $selectedTpq;
            }

            $statusCounts = $this->antrianMunaqosahModel->getStatusCounts($filters);
            $queue = $this->antrianMunaqosahModel->getQueueWithDetails($filters);

            $totalPeserta = array_sum($statusCounts);
            $totalSelesai = $statusCounts[2] ?? 0;
            $totalProses = $statusCounts[1] ?? 0;
            $totalMenunggu = $statusCounts[0] ?? 0;
            $progressPersentase = $totalPeserta > 0 ? round(($totalSelesai / $totalPeserta) * 100) : 0;

            // Ambil status ruangan untuk grup ini
            $rooms = [];
            if ($grup['IdGrupMateriUjian']) {
                // Ambil kapasitas maksimal ruangan dari konfigurasi berdasarkan grup materi
                $configIdTpq = $selectedTpq ?? '0';
                $settingKey = 'KapasitasRuanganMaksimal_' . $grup['IdGrupMateriUjian'];
                $kapasitasMaksimal = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, $settingKey, 1);
                if ($kapasitasMaksimal <= 0) {
                    $kapasitasMaksimal = 1;
                }

                $roomRows = $this->munaqosahJuriModel->getRoomsByGrupAndType($grup['IdGrupMateriUjian'], $selectedType, $selectedTpq ?? null);
                $roomStatuses = [];

                foreach ($roomRows as $roomRow) {
                    $roomId = $roomRow['RoomId'];
                    $roomStatuses[$roomId] = [
                        'RoomId' => $roomId,
                        'occupied' => false,
                        'participant_count' => 0,
                        'participants' => [],
                        'max_capacity' => $kapasitasMaksimal,
                        'is_full' => false,
                    ];
                }

                // Hitung jumlah peserta per ruangan
                foreach ($queue as $row) {
                    if ((int) ($row['Status'] ?? 0) === 1 && !empty($row['RoomId'])) {
                        $roomId = $row['RoomId'];
                        if (!isset($roomStatuses[$roomId])) {
                            $roomStatuses[$roomId] = [
                                'RoomId' => $roomId,
                                'occupied' => false,
                                'participant_count' => 0,
                                'participants' => [],
                                'max_capacity' => $kapasitasMaksimal,
                                'is_full' => false,
                            ];
                        }

                        $roomStatuses[$roomId]['participant_count']++;
                        $roomStatuses[$roomId]['participants'][] = $row;
                        $roomStatuses[$roomId]['occupied'] = true;

                        // Tandai ruangan penuh jika mencapai kapasitas maksimal
                        if ($roomStatuses[$roomId]['participant_count'] >= $kapasitasMaksimal) {
                            $roomStatuses[$roomId]['is_full'] = true;
                        }
                    }
                }

                $rooms = array_values($roomStatuses);
            }

            $antrianData[] = [
                'grup' => $grup,
                'statistics' => [
                    'total' => $totalPeserta,
                    'completed' => $totalSelesai,
                    'waiting' => $totalMenunggu,
                    'in_progress' => $totalProses,
                    'progress' => $progressPersentase,
                ],
                'rooms' => $rooms,
                'queue' => array_slice($queue, 0, 10), // Ambil 10 teratas saja untuk preview
            ];
        }

        // Ambil statistik per Group Peserta
        $statistikGroupPeserta = $this->nilaiMunaqosahModel->getStatistikPesertaDinilai(
            $currentTahunAjaran,
            $selectedType,
            $selectedTpq ?? null
        );

        $data = [
            'page_title' => 'Dashboard Monitoring Munaqosah',
            'current_tahun_ajaran' => $currentTahunAjaran,
            'tpqDropdown' => $dataTpq,
            'statistik' => $statistik,
            'antrianData' => $antrianData,
            'grupList' => $grupList,
            'types' => $typeOptions,
            'selected_tpq' => $selectedTpq ?? '',
            'selected_type' => $selectedType,
            'session_id_tpq' => $sessionIdTpq ?? '',
            'statistikGroupPeserta' => $statistikGroupPeserta,
            'is_juri' => $isJuri,
            'juri_id_tpq' => $isJuri && isset($juriData) ? ($juriData->IdTpq ?? null) : null,
        ];

        return view('backend/Munaqosah/dashboardMonitoring', $data);
    }

    public function kelulusanUjian()
    {
        helper('munaqosah');

        $helpFunctionModel = new \App\Models\HelpFunctionModel();
        $currentTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();

        $idTpq = session()->get('IdTpq');
        $dataTpq = $this->helpFunction->getDataTpq($idTpq);

        $statistik = getStatistikMunaqosah();

        // Cek setting AktiveTombolKelulusan dengan IdTpq == 0 (setting global/admin)
        // Operator/TPQ hanya bisa memilih munaqosah jika setting ini aktif
        $aktiveTombolKelulusan = false;
        $isAdmin = empty($idTpq) || $idTpq == 0;

        if (!$isAdmin) {
            // Cek setting dengan IdTpq == 0 terlebih dahulu (setting global)
            $aktiveTombolKelulusan = $this->munaqosahKonfigurasiModel->getSettingAsBool('0', 'AktiveTombolKelulusan', false);
        } else {
            // Admin selalu bisa melihat semua Type Ujian
            $aktiveTombolKelulusan = true;
        }

        $data = [
            'page_title' => 'Kelulusan Ujian',
            'current_tahun_ajaran' => $currentTahunAjaran,
            'tpqDropdown' => $dataTpq,
            'statistik' => $statistik,
            'aktiveTombolKelulusan' => $aktiveTombolKelulusan,
            'isAdmin' => $isAdmin,
        ];

        return view('backend/Munaqosah/kelulusanUjian', $data);
    }

    private function buildMonitoringDataset(string $idTahunAjaran, ?int $idTpq = 0, ?string $typeParam = null, bool $includeBobot = false, ?string $targetNoPeserta = null): array
    {
        try {
            $idTahunAjaran = trim($idTahunAjaran);
            if ($idTahunAjaran === '') {
                return [
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ];
            }

            $idTpq = (int)($idTpq ?? 0);
            $allowedTypes = ['munaqosah', 'pra-munaqosah'];
            $typeParam = $typeParam !== null ? strtolower($typeParam) : null;
            $typeUjian = in_array($typeParam, $allowedTypes, true)
                ? $typeParam
                : (($idTpq !== 0) ? 'pra-munaqosah' : 'munaqosah');

            $builder = $this->db->table('tbl_munaqosah_registrasi_uji r');
            $builder->select('r.NoPeserta,r.IdSantri,r.IdTpq,r.IdTahunAjaran,r.IdKategoriMateri,r.IdGrupMateriUjian,r.TypeUjian, s.NamaSantri, t.NamaTpq, km.NamaKategoriMateri');
            $builder->join('tbl_santri_baru s', 's.IdSantri = r.IdSantri', 'left');
            $builder->join('tbl_tpq t', 't.IdTpq = r.IdTpq', 'left');
            $builder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = r.IdKategoriMateri', 'left');
            $builder->where('r.IdTahunAjaran', $idTahunAjaran);
            $builder->where('r.TypeUjian', $typeUjian);
            if (!empty($idTpq)) {
                $builder->where('r.IdTpq', $idTpq);
            }
            if (!empty($targetNoPeserta)) {
                $builder->where('r.NoPeserta', $targetNoPeserta);
            }

            $registrasiRows = $builder->get()->getResultArray();

            if (empty($registrasiRows)) {
                return [
                    'success' => $targetNoPeserta ? false : true,
                    'message' => $targetNoPeserta ? 'Peserta tidak ditemukan untuk parameter yang diberikan' : null,
                    'data' => [
                        'categories' => [],
                        'rows' => [],
                        'meta' => [
                            'IdTahunAjaran' => $idTahunAjaran,
                            'TypeUjian' => $typeUjian,
                            'IdTpq' => $idTpq,
                            'bobot_source' => $includeBobot ? $idTahunAjaran : null,
                            'has_bobot' => false,
                            'requested_no_peserta' => $targetNoPeserta,
                        ]
                    ]
                ];
            }

            $bobotData = ['map' => [], 'source' => $idTahunAjaran];
            if ($includeBobot) {
                $bobotData = $this->getBobotWeightData($idTahunAjaran);
            }
            $bobotMap = $bobotData['map'];
            $bobotSource = $bobotData['source'];

            $categoriesMap = [];
            $grupMateriMap = []; // Map untuk IdKategoriMateri -> IdGrupMateriUjian

            foreach ($registrasiRows as $row) {
                $catId = $row['IdKategoriMateri'];
                if (empty($catId)) {
                    continue;
                }
                if (!isset($categoriesMap[$catId])) {
                    $idGrupMateriUjian = $row['IdGrupMateriUjian'] ?? null;
                    $grupMateriMap[$catId] = $idGrupMateriUjian;

                    $categoriesMap[$catId] = [
                        'id' => $catId,
                        'name' => $row['NamaKategoriMateri'] ?? $catId,
                        'weight' => isset($bobotMap[$catId]) ? (float)$bobotMap[$catId] : 0.0,
                        'IdGrupMateriUjian' => $idGrupMateriUjian,
                    ];
                }
            }

            // Ambil konfigurasi MaxJuriPerRoom untuk setiap kategori
            foreach ($categoriesMap as $catId => $catData) {
                $idGrupMateriUjian = $catData['IdGrupMateriUjian'] ?? null;
                $maxJuri = 2; // Default 2 juri

                if (!empty($idGrupMateriUjian)) {
                    // Ambil konfigurasi MaxJuriPerRoom untuk grup materi ini
                    // Jika IdTpq tidak ada (0 atau kosong), ambil default setting
                    $configIdTpq = ($idTpq != 0 && !empty($idTpq)) ? (string)$idTpq : 'default';
                    $settingKey = 'MaxJuriPerRoom_' . $idGrupMateriUjian;

                    // Method getSetting sudah otomatis fallback ke default jika tidak ditemukan
                    $maxJuriSetting = $this->munaqosahKonfigurasiModel->getSetting($configIdTpq, $settingKey);

                    if ($maxJuriSetting !== null && is_numeric($maxJuriSetting)) {
                        $maxJuri = (int)$maxJuriSetting;
                    }
                }

                $categoriesMap[$catId]['maxJuri'] = $maxJuri;
            }

            ksort($categoriesMap);
            $categories = array_values($categoriesMap);

            $pesertaInfo = [];
            foreach ($registrasiRows as $row) {
                $np = $row['NoPeserta'];
                if (!isset($pesertaInfo[$np])) {
                    $pesertaInfo[$np] = [
                        'NoPeserta' => $np,
                        'IdSantri' => $row['IdSantri'],
                        'IdTpq' => $row['IdTpq'],
                        'NamaSantri' => $row['NamaSantri'] ?? '-',
                        'NamaTpq' => $row['NamaTpq'] ?? '-',
                        'TypeUjian' => $row['TypeUjian'],
                        'IdTahunAjaran' => $row['IdTahunAjaran'],
                    ];
                }
            }

            $noPesertaList = array_keys($pesertaInfo);

            $nilaiBuilder = $this->db->table('tbl_munaqosah_nilai n');
            $nilaiBuilder->select('n.NoPeserta,n.IdKategoriMateri,n.Nilai,j.UsernameJuri');
            $nilaiBuilder->join('tbl_munaqosah_juri j', 'j.IdJuri = n.IdJuri', 'left');
            $nilaiBuilder->where('n.IdTahunAjaran', $idTahunAjaran);
            $nilaiBuilder->where('n.TypeUjian', $typeUjian);
            $nilaiBuilder->whereIn('n.NoPeserta', $noPesertaList);
            $nilaiRows = $nilaiBuilder->get()->getResultArray();

            // Helper function untuk ekstrak nomor juri dari username
            // Format: juri.baca.al-quran.218.1 -> return 1
            $extractJuriNumber = function ($username) {
                if (empty($username)) return 0;
                // Ambil angka terakhir setelah titik terakhir
                $parts = explode('.', $username);
                $lastPart = end($parts);
                return is_numeric($lastPart) ? (int)$lastPart : 0;
            };

            $nilaiIndex = [];
            foreach ($nilaiRows as $row) {
                $np = $row['NoPeserta'];
                $catId = $row['IdKategoriMateri'];
                if ($catId === null) {
                    continue;
                }

                // Ekstrak nomor juri dari username
                $juriNumber = $extractJuriNumber($row['UsernameJuri'] ?? '');

                // Cek maxJuri untuk kategori ini
                $maxJuri = isset($categoriesMap[$catId]['maxJuri']) ? (int)$categoriesMap[$catId]['maxJuri'] : 2;

                // Hanya ambil nilai dari juri yang sesuai dengan maxJuri (juri 1 sampai maxJuri)
                if ($juriNumber < 1 || $juriNumber > $maxJuri) {
                    continue;
                }

                if (!isset($nilaiIndex[$np])) {
                    $nilaiIndex[$np] = [];
                }
                if (!isset($nilaiIndex[$np][$catId])) {
                    $nilaiIndex[$np][$catId] = [];
                }

                // Simpan nilai dengan index sesuai nomor juri (index 0 = juri 1, index 1 = juri 2, dll)
                $index = $juriNumber - 1;
                $nilaiIndex[$np][$catId][$index] = (float)$row['Nilai'];
            }

            $rows = [];
            foreach ($pesertaInfo as $np => $info) {
                $row = $info;
                $row['nilai'] = [];
                if ($includeBobot) {
                    $row['averages'] = [];
                    $row['weighted'] = [];
                }

                foreach ($categories as $cat) {
                    $catId = $cat['id'];
                    $rawScoresUnordered = $nilaiIndex[$np][$catId] ?? [];
                    $maxJuri = $cat['maxJuri'] ?? 2;

                    // Urutkan array berdasarkan index (juri 1, juri 2, dll)
                    ksort($rawScoresUnordered);
                    $rawScores = array_values($rawScoresUnordered);

                    // Buat array nilai dengan panjang sesuai maxJuri, isi dengan 0.0 jika tidak ada
                    $scores = [];
                    for ($i = 0; $i < $maxJuri; $i++) {
                        $scores[$i] = isset($rawScores[$i]) ? (float)$rawScores[$i] : 0.0;
                    }

                    $row['nilai'][$catId] = $scores;

                    if ($includeBobot) {
                        // Untuk average, hanya gunakan nilai yang > 0 dan sesuai dengan maxJuri
                        $validScores = array_filter($rawScores, function ($score) {
                            return $score > 0;
                        });
                        $validScores = array_values($validScores);

                        // Jika hanya ada satu nilai valid, gunakan nilai tersebut
                        if (count($validScores) === 1) {
                            $average = (float)$validScores[0];
                        } else {
                            $average = $this->computeAverageScore($validScores);
                        }

                        $weight = isset($bobotMap[$catId]) ? (float)$bobotMap[$catId] : 0.0;
                        $weightedScore = round(($average * $weight) / 100, 2);

                        $row['averages'][$catId] = $average;
                        $row['weighted'][$catId] = $weightedScore;
                    }
                }

                if ($includeBobot) {
                    $totalWeighted = array_sum($row['weighted']);
                    $totalWeighted = round($totalWeighted, 2);
                    $threshold = $this->getKelulusanThresholdForTpq($row['IdTpq'] ?? null);

                    $row['total_weighted'] = $totalWeighted;
                    $row['kelulusan_threshold'] = $threshold;
                    $row['kelulusan_met'] = $totalWeighted >= $threshold;
                    $row['kelulusan_status'] = $row['kelulusan_met'] ? 'Lulus' : 'Belum Lulus';
                    $row['kelulusan_difference'] = round($totalWeighted - $threshold, 2);
                }

                $rows[] = $row;
            }

            return [
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'rows' => $rows,
                    'meta' => [
                        'IdTahunAjaran' => $idTahunAjaran,
                        'TypeUjian' => $typeUjian,
                        'IdTpq' => $idTpq,
                        'bobot_source' => $includeBobot ? $bobotSource : null,
                        'has_bobot' => $includeBobot && !empty($bobotMap),
                        'requested_no_peserta' => $targetNoPeserta,
                    ]
                ]
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Error in buildMonitoringDataset: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ];
        }
    }

    private function getBobotWeightData(string $idTahunAjaran): array
    {
        $key = strtolower($idTahunAjaran);
        if (isset($this->bobotWeightCache[$key])) {
            return $this->bobotWeightCache[$key];
        }

        $attempts = [$idTahunAjaran];
        if ($key !== 'default') {
            $attempts[] = 'default';
            $attempts[] = 'Default';
            $attempts[] = 'DEFAULT';
        }
        $attempts = array_unique($attempts);

        foreach ($attempts as $attempt) {
            $attemptKey = strtolower($attempt);
            if (!isset($this->bobotWeightCache[$attemptKey])) {
                $rows = $this->bobotNilaiMunaqosahModel->getBobotWithKategori($attempt);
                $map = [];
                foreach ($rows as $row) {
                    $map[$row['IdKategoriMateri']] = (float)$row['NilaiBobot'];
                }
                $this->bobotWeightCache[$attemptKey] = [
                    'map' => $map,
                    'source' => $attempt,
                ];
            }
            if (!empty($this->bobotWeightCache[$attemptKey]['map'])) {
                $result = $this->bobotWeightCache[$attemptKey];
                if ($attemptKey !== $key) {
                    $this->bobotWeightCache[$key] = $result;
                }
                return $result;
            }
        }

        $empty = ['map' => [], 'source' => $idTahunAjaran];
        $this->bobotWeightCache[$key] = $empty;
        return $empty;
    }

    private function getKelulusanThresholdForTpq($idTpq): int
    {
        $configId = ($idTpq === null || $idTpq === '' || $idTpq === 0) ? 'default' : (string)$idTpq;
        if (!isset($this->kelulusanThresholdCache[$configId])) {
            $rawThreshold = $this->munaqosahKonfigurasiModel->getSetting($configId, 'NilaiKelulusanMinimal');

            if ($rawThreshold === null) {
                $rawThreshold = $this->munaqosahKonfigurasiModel->getSetting($configId, 'KelulusanMinimal');
            }

            if ($rawThreshold === null && $configId !== 'default') {
                $rawThreshold = $this->munaqosahKonfigurasiModel->getSetting('default', 'NilaiKelulusanMinimal');
                if ($rawThreshold === null) {
                    $rawThreshold = $this->munaqosahKonfigurasiModel->getSetting('default', 'KelulusanMinimal');
                }
            }

            $threshold = is_numeric($rawThreshold) ? (int)$rawThreshold : 65;

            $this->kelulusanThresholdCache[$configId] = $threshold;
        }

        return $this->kelulusanThresholdCache[$configId];
    }

    private function computeAverageScore(array $scores): float
    {
        $valid = [];
        foreach ($scores as $score) {
            if ($score === null || $score === '') {
                continue;
            }
            if (!is_numeric($score)) {
                continue;
            }
            $scoreValue = (float)$score;
            // Hanya hitung nilai yang > 0 (bukan nilai 0 yang di-padding)
            if ($scoreValue > 0) {
                $valid[] = $scoreValue;
            }
        }

        if (empty($valid)) {
            return 0.0;
        }

        // Jika hanya ada satu nilai valid, kembalikan nilai tersebut langsung
        if (count($valid) === 1) {
            return round($valid[0], 2);
        }

        return round(array_sum($valid) / count($valid), 2);
    }

    public function getMonitoringData()
    {
        try {
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            if (empty($idTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ]);
            }

            $idTpqParam = $this->request->getGet('IdTpq');
            $idTpq = ($idTpqParam === null || $idTpqParam === '') ? 0 : (int)$idTpqParam;
            $typeParam = $this->request->getGet('TypeUjian');

            $result = $this->buildMonitoringDataset($idTahunAjaran, $idTpq, $typeParam, false);

            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getMonitoringData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    public function getStatistikGroupPeserta()
    {
        try {
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            if (empty($idTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ]);
            }

            $sessionIdTpq = session()->get('IdTpq');

            // Jika user login sebagai Juri, ambil IdTpq dari data juri
            if (in_groups('Juri')) {
                $usernameJuri = user()->username;
                $juriData = $this->munaqosahJuriModel->getJuriByUsernameJuri($usernameJuri);
                if ($juriData && !empty($juriData->IdTpq)) {
                    $idTpq = (int)$juriData->IdTpq;
                } else {
                    $idTpq = null;
                }
            }
            // Jika user login sebagai operator TPQ, gunakan IdTpq dari session
            elseif (!empty($sessionIdTpq)) {
                $idTpq = (int)$sessionIdTpq;
            } else {
                $idTpqParam = $this->request->getGet('IdTpq');
                $idTpq = ($idTpqParam === null || $idTpqParam === '' || $idTpqParam === '0') ? null : (int)$idTpqParam;
            }

            $typeParam = $this->request->getGet('TypeUjian');

            $statistikGroupPeserta = $this->nilaiMunaqosahModel->getStatistikPesertaDinilai(
                $idTahunAjaran,
                $typeParam,
                $idTpq
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $statistikGroupPeserta
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getStatistikGroupPeserta: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    public function getStatistikPerGroupMateri()
    {
        try {
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            if (empty($idTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ]);
            }

            $sessionIdTpq = session()->get('IdTpq');

            // Jika user login sebagai Juri, ambil IdTpq dari data juri
            if (in_groups('Juri')) {
                $usernameJuri = user()->username;
                $juriData = $this->munaqosahJuriModel->getJuriByUsernameJuri($usernameJuri);
                if ($juriData && !empty($juriData->IdTpq)) {
                    $idTpq = (int)$juriData->IdTpq;
                } else {
                    $idTpq = null;
                }
            }
            // Jika user login sebagai operator TPQ, gunakan IdTpq dari session
            elseif (!empty($sessionIdTpq)) {
                $idTpq = (int)$sessionIdTpq;
            } else {
                $idTpqParam = $this->request->getGet('IdTpq');
                $idTpq = ($idTpqParam === null || $idTpqParam === '' || $idTpqParam === '0') ? null : (int)$idTpqParam;
            }

            $typeParam = $this->request->getGet('TypeUjian');

            $statistikGroupMateri = $this->nilaiMunaqosahModel->getStatistikPerGroupMateri(
                $idTahunAjaran,
                $typeParam,
                $idTpq
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $statistikGroupMateri
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getStatistikPerGroupMateri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    public function getStatistikPenilaianPerJuri()
    {
        try {
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            if (empty($idTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ]);
            }

            $sessionIdTpq = session()->get('IdTpq');

            // Jika user login sebagai Juri, ambil IdTpq dari data juri
            if (in_groups('Juri')) {
                $usernameJuri = user()->username;
                $juriData = $this->munaqosahJuriModel->getJuriByUsernameJuri($usernameJuri);
                if ($juriData && !empty($juriData->IdTpq)) {
                    $idTpq = (int)$juriData->IdTpq;
                } else {
                    $idTpq = null;
                }
            }
            // Jika user login sebagai operator TPQ, gunakan IdTpq dari session
            elseif (!empty($sessionIdTpq)) {
                $idTpq = (int)$sessionIdTpq;
            } else {
                $idTpqParam = $this->request->getGet('IdTpq');
                $idTpq = ($idTpqParam === null || $idTpqParam === '' || $idTpqParam === '0') ? null : (int)$idTpqParam;
            }

            $typeParam = $this->request->getGet('TypeUjian');

            $statistikJuri = $this->nilaiMunaqosahModel->getStatistikPenilaianPerJuri(
                $idTahunAjaran,
                $typeParam,
                $idTpq
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $statistikJuri
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getStatistikPenilaianPerJuri: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    public function getStatistikPenilaianPerGrupMateriRuangan()
    {
        try {
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            if (empty($idTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ]);
            }

            $sessionIdTpq = session()->get('IdTpq');

            // Jika user login sebagai Juri, ambil IdTpq dari data juri
            if (in_groups('Juri')) {
                $usernameJuri = user()->username;
                $juriData = $this->munaqosahJuriModel->getJuriByUsernameJuri($usernameJuri);
                if ($juriData && !empty($juriData->IdTpq)) {
                    $idTpq = (int)$juriData->IdTpq;
                } else {
                    $idTpq = null;
                }
            }
            // Jika user login sebagai operator TPQ, gunakan IdTpq dari session
            elseif (!empty($sessionIdTpq)) {
                $idTpq = (int)$sessionIdTpq;
            } else {
                $idTpqParam = $this->request->getGet('IdTpq');
                $idTpq = ($idTpqParam === null || $idTpqParam === '' || $idTpqParam === '0') ? null : (int)$idTpqParam;
            }

            $typeParam = $this->request->getGet('TypeUjian');

            $statistikGrupMateri = $this->nilaiMunaqosahModel->getStatistikPenilaianPerGrupMateriRuangan(
                $idTahunAjaran,
                $typeParam,
                $idTpq
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $statistikGrupMateri
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getStatistikPenilaianPerGrupMateriRuangan: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }

    public function getKelulusanData()
    {
        try {
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            if (empty($idTahunAjaran)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'IdTahunAjaran harus diisi'
                ]);
            }

            $idTpqParam = $this->request->getGet('IdTpq');
            $idTpq = ($idTpqParam === null || $idTpqParam === '') ? 0 : (int)$idTpqParam;
            $typeParam = $this->request->getGet('TypeUjian');

            $result = $this->buildMonitoringDataset($idTahunAjaran, $idTpq, $typeParam, true);

            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            log_message('error', 'Error in getKelulusanData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'details' => $e->getMessage()
            ]);
        }
    }


    // ==================== KELULUSAN DETAIL ====================

    private function prepareKelulusanPesertaData(string $noPeserta, ?string $idTahunAjaran = null, ?string $typeUjian = null, ?int $idTpqFilter = null): array
    {
        $noPeserta = strtoupper(trim($noPeserta));
        if ($noPeserta === '') {
            return [
                'success' => false,
                'message' => 'NoPeserta harus diisi'
            ];
        }

        $registrasiBuilder = $this->db->table('tbl_munaqosah_registrasi_uji r');
        $registrasiBuilder->select('r.*, s.NamaSantri, t.NamaTpq');
        $registrasiBuilder->join('tbl_santri_baru s', 's.IdSantri = r.IdSantri', 'left');
        $registrasiBuilder->join('tbl_tpq t', 't.IdTpq = r.IdTpq', 'left');
        $registrasiBuilder->where('r.NoPeserta', $noPeserta);

        if (!empty($idTahunAjaran)) {
            $registrasiBuilder->where('r.IdTahunAjaran', $idTahunAjaran);
        }

        if (!empty($typeUjian)) {
            $registrasiBuilder->where('r.TypeUjian', $typeUjian);
        }

        if (!empty($idTpqFilter)) {
            $registrasiBuilder->where('r.IdTpq', $idTpqFilter);
        }

        $registrasiBuilder->orderBy('r.IdTahunAjaran', 'DESC');
        $registrasiBuilder->orderBy('r.TypeUjian', 'ASC');

        $registrasiRows = $registrasiBuilder->get()->getResultArray();

        if (empty($registrasiRows)) {
            return [
                'success' => false,
                'message' => 'Data peserta tidak ditemukan'
            ];
        }

        $primaryRow = $registrasiRows[0];
        $resolvedTahun = $idTahunAjaran ?: $primaryRow['IdTahunAjaran'];
        $resolvedType = $typeUjian ?: $primaryRow['TypeUjian'];
        $resolvedType = strtolower($resolvedType);
        $resolvedTpq = $idTpqFilter ?? ($primaryRow['IdTpq'] ?? 0);

        $filteredRegistrasi = array_filter($registrasiRows, static function ($row) use ($resolvedTahun, $resolvedType) {
            return $row['IdTahunAjaran'] === $resolvedTahun && strtolower($row['TypeUjian']) === $resolvedType;
        });

        if (!empty($filteredRegistrasi)) {
            $registrasiRows = array_values($filteredRegistrasi);
        }

        $dataset = $this->buildMonitoringDataset($resolvedTahun, (int)$resolvedTpq, $resolvedType, true, $noPeserta);

        if (!$dataset['success'] || empty($dataset['data']['rows'])) {
            return [
                'success' => false,
                'message' => $dataset['message'] ?? 'Data kelulusan tidak ditemukan'
            ];
        }

        $rowData = $dataset['data']['rows'][0];
        $categories = $dataset['data']['categories'];
        $meta = $dataset['data']['meta'];

        $materiBuilder = $this->db->table('tbl_munaqosah_registrasi_uji r');
        $materiBuilder->select(
            'r.IdKategoriMateri,' .
                ' r.IdMateri,' .
                ' km.NamaKategoriMateri,' .
                ' mp.Id AS IdMa,' .
                ' mp.NamaMateri AS MateriPelajaran,' .
                ' mp.Kategori AS MateriKategori,' .
                ' mp.NamaMateri AS MateriMunaqosah,' .
                ' km.NamaKategoriMateri,' .
                ' al.NamaSurah,' .
                ' al.WebLinkAyat AS AlquranLink'
        );
        $materiBuilder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = r.IdKategoriMateri', 'left');
        $materiBuilder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = r.IdMateri', 'left');
        $materiBuilder->join('tbl_munaqosah_materi mm', 'mm.IdMateri = r.IdMateri', 'left');
        $materiBuilder->join('tbl_munaqosah_alquran al', 'al.IdMateri = r.IdMateri', 'left');
        $materiBuilder->where('r.NoPeserta', $noPeserta);
        $materiBuilder->where('r.IdTahunAjaran', $resolvedTahun);
        $materiBuilder->where('r.TypeUjian', $resolvedType);
        $materiRows = $materiBuilder->get()->getResultArray();

        $materiMap = [];
        foreach ($materiRows as $materi) {
            $catId = $materi['IdKategoriMateri'];
            if (empty($catId)) {
                continue;
            }
            if (!isset($materiMap[$catId])) {
                $materiMap[$catId] = [];
            }
            $materiNama = $materi['MateriPelajaran'] ?? $materi['MateriMunaqosah'] ?? $materi['NamaSurah'] ?? '-';
            $materiLink = $materi['AlquranLink'] ?? null;
            $materiMap[$catId][] = [
                'IdMateri' => $materi['IdMateri'],
                'IdMa' => $materi['IdMa'] ?? null,
                'NamaMateri' => $materiNama,
                'KategoriMateriUjian' => $materi['KategoriMateriUjian'] ?? $materi['NamaKategoriMateri'] ?? ($materi['MateriKategori'] ?? '-'),
                'WebLinkAyat' => $materiLink,
            ];
        }

        $nilaiDetails = $this->nilaiMunaqosahModel
            ->select(
                'tbl_munaqosah_nilai.*,' .
                    ' j.UsernameJuri,' .
                    ' j.RoomId,' .
                    ' km.NamaKategoriMateri,' .
                    ' mp.Id AS IdMa,' .
                    ' mp.NamaMateri AS MateriPelajaran,' .
                    ' mp.Kategori AS MateriKategori,' .
                    ' mp.NamaMateri AS MateriMunaqosah,' .
                    ' al.NamaSurah,' .
                    ' al.WebLinkAyat AS AlquranLink'
            )
            ->join('tbl_munaqosah_juri j', 'j.IdJuri = tbl_munaqosah_nilai.IdJuri', 'left')
            ->join('tbl_kategori_materi km', 'km.IdKategoriMateri = tbl_munaqosah_nilai.IdKategoriMateri', 'left')
            ->join('tbl_materi_pelajaran mp', 'mp.IdMateri = tbl_munaqosah_nilai.IdMateri', 'left')
            ->join('tbl_munaqosah_materi mm', 'mm.IdMateri = tbl_munaqosah_nilai.IdMateri', 'left')
            ->join('tbl_munaqosah_alquran al', 'al.IdMateri = tbl_munaqosah_nilai.IdMateri', 'left')
            ->where('tbl_munaqosah_nilai.NoPeserta', $noPeserta)
            ->where('tbl_munaqosah_nilai.IdTahunAjaran', $resolvedTahun)
            ->where('tbl_munaqosah_nilai.TypeUjian', $resolvedType)
            ->orderBy('km.IdKategoriMateri', 'ASC')
            ->orderBy('tbl_munaqosah_nilai.IdJuri', 'ASC')
            ->orderBy('tbl_munaqosah_nilai.created_at', 'ASC')
            ->findAll();

        // Helper function untuk ekstrak nomor juri dari username
        // Format: juri.baca.al-quran.218.1 -> return 1
        $extractJuriNumber = function ($username) {
            if (empty($username)) return 0;
            // Ambil angka terakhir setelah titik terakhir
            $parts = explode('.', $username);
            $lastPart = end($parts);
            return is_numeric($lastPart) ? (int)$lastPart : 0;
        };

        $nilaiMap = [];
        foreach ($nilaiDetails as $detail) {
            $catId = $detail['IdKategoriMateri'];
            if (empty($catId)) {
                continue;
            }

            // Cek maxJuri untuk kategori ini
            $maxJuri = null;
            foreach ($categories as $cat) {
                if ($cat['id'] === $catId) {
                    $maxJuri = $cat['maxJuri'] ?? null;
                    break;
                }
            }
            if ($maxJuri === null) {
                $maxJuri = 2; // Default
            } else {
                $maxJuri = (int)$maxJuri;
            }

            // Ekstrak nomor juri dari username
            $juriNumber = $extractJuriNumber($detail['UsernameJuri'] ?? '');

            // Hanya ambil nilai dari juri yang sesuai dengan maxJuri (juri 1 sampai maxJuri)
            if ($juriNumber < 1 || $juriNumber > $maxJuri) {
                continue;
            }

            if (!isset($nilaiMap[$catId])) {
                $nilaiMap[$catId] = [];
            }
            $nilaiMap[$catId][] = [
                'IdJuri' => $detail['IdJuri'],
                'UsernameJuri' => $detail['UsernameJuri'] ?? '-',
                'RoomId' => $detail['RoomId'] ?? null,
                'Nilai' => (float)$detail['Nilai'],
                'Catatan' => $detail['Catatan'] ?? '',
                'IdMa' => $detail['IdMa'] ?? null,
                'MateriNama' => $detail['MateriPelajaran'] ?? $detail['MateriMunaqosah'] ?? $detail['NamaSurah'] ?? null,
                'MateriLink' => $detail['AlquranLink'] ?? null,
                'UpdatedAt' => $detail['updated_at'] ?? $detail['created_at'] ?? null,
                'JuriNumber' => $juriNumber, // Simpan nomor juri untuk sorting
            ];
        }

        // Sort nilai berdasarkan nomor juri untuk setiap kategori
        foreach ($nilaiMap as $catId => $scores) {
            usort($nilaiMap[$catId], function ($a, $b) {
                return ($a['JuriNumber'] ?? 0) <=> ($b['JuriNumber'] ?? 0);
            });
        }

        $categoryDetails = [];
        foreach ($categories as $cat) {
            $catId = $cat['id'];
            $scores = $nilaiMap[$catId] ?? [];
            $juriScores = [];
            foreach ($scores as $score) {
                // Gunakan nomor juri dari username, bukan index array
                $juriNumber = $score['JuriNumber'] ?? 1;
                $score['label'] = 'Juri ' . $juriNumber;
                $juriScores[] = $score;
            }

            $categoryDetails[$catId] = [
                'category' => $cat,
                'average' => $rowData['averages'][$catId] ?? 0.0,
                'weighted' => $rowData['weighted'][$catId] ?? 0.0,
                'juri_scores' => $juriScores,
                'materi' => $materiMap[$catId] ?? [],
            ];
        }

        $threshold = $rowData['kelulusan_threshold'] ?? $this->getKelulusanThresholdForTpq($rowData['IdTpq'] ?? null);
        $totalWeighted = $rowData['total_weighted'] ?? 0.0;
        $difference = round($totalWeighted - $threshold, 2);
        $kelulusanMet = $rowData['kelulusan_met'] ?? ($totalWeighted >= $threshold);

        $peserta = [
            'NoPeserta' => $rowData['NoPeserta'],
            'NamaSantri' => $rowData['NamaSantri'],
            'NamaTpq' => $rowData['NamaTpq'],
            'IdTpq' => $rowData['IdTpq'],
            'IdSantri' => $rowData['IdSantri'] ?? ($primaryRow['IdSantri'] ?? null),
            'TypeUjian' => $rowData['TypeUjian'],
            'IdTahunAjaran' => $rowData['IdTahunAjaran'],
            'TotalWeighted' => $totalWeighted,
            'KelulusanThreshold' => $threshold,
            'KelulusanStatus' => $kelulusanMet ? 'Lulus' : 'Belum Lulus',
            'KelulusanMet' => $kelulusanMet,
            'KelulusanDifference' => $difference,
        ];

        $meta['bobot_source'] = $meta['bobot_source'] ?? $resolvedTahun;

        return [
            'success' => true,
            'data' => [
                'peserta' => $peserta,
                'categories' => $categories,
                'categoryDetails' => $categoryDetails,
                'meta' => $meta,
                'registrasi' => $registrasiRows,
                'nilai_details' => $nilaiDetails,
                'materi_details' => $materiRows,
            ]
        ];
    }

    public function kelulusanPesertaUjian()
    {
        $noPeserta = $this->request->getGet('NoPeserta');
        $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
        $typeUjian = $this->request->getGet('TypeUjian');
        $idTpqParam = $this->request->getGet('IdTpq');
        $idTpq = ($idTpqParam === null || $idTpqParam === '') ? null : (int)$idTpqParam;

        $result = $this->prepareKelulusanPesertaData($noPeserta ?? '', $idTahunAjaran, $typeUjian, $idTpq);

        if (!$result['success']) {
            return redirect()->to(base_url('backend/munaqosah/kelulusan'))
                ->with('error', $result['message'] ?? 'Data peserta tidak ditemukan');
        }

        $detail = $result['data'];

        $data = array_merge($detail, [
            'page_title' => 'Detail Kelulusan Peserta ' . ($detail['peserta']['NoPeserta'] ?? ''),
        ]);

        return view('backend/Munaqosah/kelulusanPesertaUjian', $data);
    }

    public function printKelulusanPesertaUjian()
    {
        try {
            $noPeserta = $this->request->getGet('NoPeserta');
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            $typeUjian = $this->request->getGet('TypeUjian');
            $idTpqParam = $this->request->getGet('IdTpq');
            $idTpq = ($idTpqParam === null || $idTpqParam === '') ? null : (int)$idTpqParam;

            $result = $this->prepareKelulusanPesertaData($noPeserta ?? '', $idTahunAjaran, $typeUjian, $idTpq);

            if (!$result['success']) {
                return redirect()->to(base_url('backend/munaqosah/kelulusan'))
                    ->with('error', $result['message'] ?? 'Data peserta tidak ditemukan');
            }

            $detail = $result['data'];
            $detail['generated_at'] = date('Y-m-d H:i:s');

            $html = view('backend/Munaqosah/printKelulusanPesertaUjian', $detail);

            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'Arial');
            $options->set('isFontSubsettingEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'kelulusan_' . strtoupper($noPeserta ?? 'peserta') . '_' . date('Ymd_His') . '.pdf';

            if (ob_get_length()) {
                ob_end_clean();
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $dompdf->output();
            exit();
        } catch (\Throwable $e) {
            log_message('error', 'Error in printKelulusanPesertaUjian: ' . $e->getMessage());
            return redirect()->to(base_url('backend/munaqosah/kelulusan'))
                ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Print surat keterangan kelulusan menggunakan template suratKelulusanUjianMunaqosah
     */
    public function printSuratKelulusanPesertaUjian()
    {
        try {
            $noPeserta = $this->request->getGet('NoPeserta');
            $idTahunAjaran = $this->request->getGet('IdTahunAjaran');
            $typeUjian = $this->request->getGet('TypeUjian');
            $idTpqParam = $this->request->getGet('IdTpq');
            $idTpq = ($idTpqParam === null || $idTpqParam === '') ? null : (int)$idTpqParam;

            $result = $this->prepareKelulusanPesertaData($noPeserta ?? '', $idTahunAjaran, $typeUjian, $idTpq);

            if (!$result['success']) {
                return redirect()->to(base_url('backend/munaqosah/kelulusan'))
                    ->with('error', $result['message'] ?? 'Data peserta tidak ditemukan');
            }

            $detail = $result['data'];
            $pesertaData = $detail['peserta'];
            $categoryDetails = $detail['categoryDetails'] ?? [];
            $meta = $detail['meta'] ?? [];

            // Ambil data TPQ untuk logo/kop
            $tpqRaw = $this->helpFunction->getNamaTpqById($pesertaData['IdTpq'] ?? null);

            // Map data TPQ sesuai dengan format yang digunakan template
            $tpqData = [];
            if ($tpqRaw) {
                $tpqData = [
                    'NamaTpq' => $tpqRaw['NamaTpq'] ?? '',
                    'AlamatTpq' => $tpqRaw['Alamat'] ?? '',
                    'NoTelp' => $tpqRaw['NoHp'] ?? '',
                    'Logo' => $tpqRaw['LogoLembaga'] ?? null,
                ];

                // Ambil nama kepala TPQ dari struktur lembaga
                $kepalaTpq = $this->helpFunction->getDataKepalaTpqStrukturLembaga(null, $pesertaData['IdTpq'] ?? null, null);
                if (!empty($kepalaTpq) && isset($kepalaTpq[0]->Nama)) {
                    $tpqData['NamaKepalaTpq'] = $kepalaTpq[0]->Nama;
                }
            }

            $data = [
                'peserta' => $pesertaData,
                'categoryDetails' => $categoryDetails,
                'meta' => $meta,
                'tpqData' => $tpqData,
                'generated_at' => date('Y-m-d')
            ];

            // Generate PDF
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $html = view('frontend/munaqosah/suratKelulusanUjianMunaqosah', $data);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'Surat_Kelulusan_' . str_replace(' ', '_', $pesertaData['NamaSantri'] ?? 'peserta') . '_' . date('Ymd_His') . '.pdf';

            if (ob_get_length()) {
                ob_end_clean();
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $dompdf->output();
            exit();
        } catch (\Throwable $e) {
            log_message('error', 'Error in printSuratKelulusanPesertaUjian: ' . $e->getMessage());
            return redirect()->to(base_url('backend/munaqosah/kelulusan'))
                ->with('error', 'Gagal membuat surat keterangan kelulusan: ' . $e->getMessage());
        }
    }

    // ==================== KATEGORI KESALAHAN ====================

    /**
     * Display list kategori kesalahan
     */
    public function listKategoriKesalahan()
    {
        $data = [
            'page_title' => 'Data Kategori Kesalahan Munaqosah'
        ];
        return view('backend/Munaqosah/listKategoriKesalahan', $data);
    }

    /**
     * Get all kategori kesalahan
     */
    public function getKategoriKesalahan()
    {
        try {
            $kesalahan = $this->munaqosahKategoriKesalahanModel
                ->select('tbl_munaqosah_kategori_kesalahan.*, tbl_kategori_materi.NamaKategoriMateri')
                ->join('tbl_kategori_materi', 'tbl_kategori_materi.IdKategoriMateri = tbl_munaqosah_kategori_kesalahan.IdKategoriMateri', 'left')
                ->orderBy('tbl_munaqosah_kategori_kesalahan.IdKategoriKesalahan', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $kesalahan
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save kategori kesalahan
     */
    public function saveKategoriKesalahan()
    {
        try {
            $rules = [
                'IdKategoriKesalahan' => 'required|max_length[50]|is_unique[tbl_munaqosah_kategori_kesalahan.IdKategoriKesalahan]',
                'IdKategoriMateri' => 'required',
                'NamaKategoriKesalahan' => 'required|max_length[255]',
                'Status' => 'required|in_list[Aktif,Tidak Aktif]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'IdKategoriKesalahan' => strtoupper($this->request->getPost('IdKategoriKesalahan')),
                'IdKategoriMateri' => $this->request->getPost('IdKategoriMateri'),
                'NamaKategoriKesalahan' => $this->request->getPost('NamaKategoriKesalahan'),
                'Status' => $this->request->getPost('Status')
            ];

            if ($this->munaqosahKategoriKesalahanModel->save($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data kategori kesalahan berhasil disimpan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data',
                    'errors' => $this->munaqosahKategoriKesalahanModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update kategori kesalahan
     */
    public function updateKategoriKesalahan($id)
    {
        try {
            $rules = [
                'IdKategoriMateri' => 'required',
                'NamaKategoriKesalahan' => 'required|max_length[255]',
                'Status' => 'required|in_list[Aktif,Tidak Aktif]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'IdKategoriMateri' => $this->request->getPost('IdKategoriMateri'),
                'NamaKategoriKesalahan' => $this->request->getPost('NamaKategoriKesalahan'),
                'Status' => $this->request->getPost('Status')
            ];

            if ($this->munaqosahKategoriKesalahanModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data kategori kesalahan berhasil diupdate'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupdate data',
                    'errors' => $this->munaqosahKategoriKesalahanModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete kategori kesalahan
     */
    public function deleteKategoriKesalahan($id)
    {
        try {
            if ($this->munaqosahKategoriKesalahanModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data kategori kesalahan berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get error categories by kategori materi for input nilai juri
     */
    public function getErrorCategoriesByKategori()
    {
        try {
            $kategori = $this->request->getGet('kategori');

            if (!$kategori) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parameter kategori harus diisi'
                ]);
            }

            $kesalahan = $this->munaqosahKategoriKesalahanModel
                ->select('tbl_munaqosah_kategori_kesalahan.*')
                ->join('tbl_kategori_materi', 'tbl_kategori_materi.IdKategoriMateri = tbl_munaqosah_kategori_kesalahan.IdKategoriMateri', 'left')
                ->where('tbl_kategori_materi.NamaKategoriMateri', $kategori)
                ->where('tbl_munaqosah_kategori_kesalahan.Status', 'Aktif')
                ->orderBy('tbl_munaqosah_kategori_kesalahan.IdKategoriKesalahan', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $kesalahan
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== KONFIGURASI MUNAQOSAH ====================

    /**
     * Display list konfigurasi munaqosah
     */
    public function listKonfigurasiMunaqosah()
    {
        // Ambil IdTpq dari session
        $idTpq = session()->get('IdTpq');

        // Get configuration data based on IdTpq
        // If IdTpq exists and not 0, show default template + IdTpq data
        // If IdTpq = 0 or null (admin), show all
        $konfigurasi = $this->munaqosahKonfigurasiModel->getByTpq($idTpq);

        // Get list TPQ untuk dropdown
        $listTpq = $this->helpFunction->getDataTpq(false); // false = ambil semua TPQ

        $data = [
            'page_title' => 'Konfigurasi Munaqosah',
            'konfigurasi' => $konfigurasi,
            'idTpq' => $idTpq,
            'listTpq' => $listTpq
        ];

        return view('backend/Munaqosah/listKonfigurasiMunaqosah', $data);
    }

    /**
     * Save konfigurasi munaqosah
     */
    public function saveKonfigurasi()
    {
        try {
            $rules = [
                'IdTpq' => 'required',
                'SettingKey' => 'required|min_length[3]',
                'SettingValue' => 'required',
                'SettingType' => 'required|in_list[number,text,boolean,json]',
                'Description' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if combination IdTpq + SettingKey already exists
            $existing = $this->munaqosahKonfigurasiModel
                ->where('IdTpq', $this->request->getPost('IdTpq'))
                ->where('SettingKey', $this->request->getPost('SettingKey'))
                ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Konfigurasi dengan IdTpq dan SettingKey tersebut sudah ada',
                    'duplicate' => true
                ]);
            }

            $data = [
                'IdTpq' => $this->request->getPost('IdTpq'),
                'SettingKey' => $this->request->getPost('SettingKey'),
                'SettingValue' => $this->request->getPost('SettingValue'),
                'SettingType' => $this->request->getPost('SettingType'),
                'Description' => $this->request->getPost('Description') ?? ''
            ];

            if ($this->munaqosahKonfigurasiModel->save($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data konfigurasi berhasil disimpan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data',
                    'errors' => $this->munaqosahKonfigurasiModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in saveKonfigurasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update konfigurasi munaqosah
     */
    public function updateKonfigurasi($id)
    {
        try {
            // Check if record exists
            $existing = $this->munaqosahKonfigurasiModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data konfigurasi tidak ditemukan'
                ]);
            }

            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));

            $rules = [
                'SettingKey' => $isAdmin ? 'required|min_length[3]' : 'required',
                'SettingValue' => 'required',
                'SettingType' => 'required|in_list[number,text,boolean,json]',
                'Description' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $newSettingKey = $this->request->getPost('SettingKey');
            $settingValue = $this->request->getPost('SettingValue');
            $settingType = $this->request->getPost('SettingType');
            $description = $this->request->getPost('Description') ?? '';

            if (!$isAdmin && $newSettingKey !== $existing['SettingKey']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah Setting Key'
                ]);
            }

            if ($isAdmin && $newSettingKey !== $existing['SettingKey']) {
                $duplicate = $this->munaqosahKonfigurasiModel
                    ->where('IdTpq', $existing['IdTpq'])
                    ->where('SettingKey', $newSettingKey)
                    ->where('id !=', $id)
                    ->first();

                if ($duplicate) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Setting Key tersebut sudah digunakan untuk ID TPQ yang sama'
                    ]);
                }
            }

            if (!$isAdmin && $settingType !== $existing['SettingType']) {
                $settingType = $existing['SettingType'];
            }

            $data = [
                'SettingValue' => $settingValue,
                'SettingType' => $settingType,
                'Description' => $description
            ];

            if ($isAdmin) {
                $data['SettingKey'] = $newSettingKey;
            }

            if ($this->munaqosahKonfigurasiModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data konfigurasi berhasil diupdate'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate data',
                'errors' => $this->munaqosahKonfigurasiModel->errors()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateKonfigurasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Duplicate konfigurasi munaqosah
     */
    public function duplicateKonfigurasi()
    {
        try {
            $sourceId = $this->request->getPost('source_id');
            $targetIdTpq = $this->request->getPost('IdTpq');
            $requestedSettingKey = $this->request->getPost('SettingKey');
            $settingValue = $this->request->getPost('SettingValue');
            $description = $this->request->getPost('Description') ?? '';

            $sessionIdTpq = session()->get('IdTpq');
            $isAdmin = ($sessionIdTpq === '0' || $sessionIdTpq === 0 || empty($sessionIdTpq));

            // Validate input
            if (empty($sourceId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Source ID tidak boleh kosong'
                ]);
            }

            if (empty($targetIdTpq)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID TPQ tujuan harus diisi'
                ]);
            }

            // Prevent duplicating to 'default'
            if ($targetIdTpq === 'default') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat menduplikasi ke "default". Gunakan ID TPQ lain atau "0" untuk admin.'
                ]);
            }

            // Get source configuration
            $source = $this->munaqosahKonfigurasiModel->find($sourceId);
            if (!$source) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data konfigurasi sumber tidak ditemukan'
                ]);
            }

            // Verify source is from 'default'
            if ($source['IdTpq'] !== 'default') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya konfigurasi dengan IdTpq = "default" yang dapat diduplikasi'
                ]);
            }

            if (!$isAdmin && $requestedSettingKey !== $source['SettingKey']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah Setting Key'
                ]);
            }

            $requestedSettingKey = $requestedSettingKey ?: $source['SettingKey'];

            if ($isAdmin) {
                $requestedSettingKey = trim($requestedSettingKey);
                if ($requestedSettingKey === '') {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Setting Key tidak boleh kosong'
                    ]);
                }
            } else {
                $requestedSettingKey = $source['SettingKey'];
            }

            // Check if configuration already exists for target IdTpq + SettingKey
            $existing = $this->munaqosahKonfigurasiModel
                ->where('IdTpq', $targetIdTpq)
                ->where('SettingKey', $requestedSettingKey)
                ->first();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Konfigurasi dengan IdTpq "' . $targetIdTpq . '" dan SettingKey "' . $requestedSettingKey . '" sudah ada. Silakan edit konfigurasi yang sudah ada.',
                    'duplicate' => true,
                    'existing_id' => $existing['id']
                ]);
            }

            // Create new configuration
            // Use SettingType from source (cannot be changed)
            // Use SettingValue from form if provided, otherwise use source value
            $data = [
                'IdTpq' => $targetIdTpq,
                'SettingKey' => $requestedSettingKey,
                'SettingValue' => !empty($settingValue) ? $settingValue : $source['SettingValue'],
                'SettingType' => $source['SettingType'],
                'Description' => !empty($description) ? $description : $source['Description']
            ];

            if ($this->munaqosahKonfigurasiModel->save($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Konfigurasi berhasil diduplikasi ke IdTpq "' . $targetIdTpq . '"'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menduplikasi data',
                    'errors' => $this->munaqosahKonfigurasiModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in duplicateKonfigurasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete konfigurasi munaqosah
     */
    public function deleteKonfigurasi($id)
    {
        try {
            // Check if record exists
            $existing = $this->munaqosahKonfigurasiModel->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data konfigurasi tidak ditemukan'
                ]);
            }

            // Prevent deletion of default template (optional - can be removed if needed)
            // if ($existing['IdTpq'] === 'default') {
            //     return $this->response->setJSON([
            //         'success' => false,
            //         'message' => 'Tidak dapat menghapus konfigurasi default template'
            //     ]);
            // }

            if ($this->munaqosahKonfigurasiModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data konfigurasi berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteKonfigurasi: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    private function getRoomIdRange($idTpq)
    {
        $configId = $idTpq;

        if ($configId === null || $configId === '') {
            $configId = 'default';
        }

        $configId = (string)$configId;

        $roomIdMin = $this->munaqosahKonfigurasiModel->getSetting($configId, 'RoomIdMin');
        $roomIdMax = $this->munaqosahKonfigurasiModel->getSetting($configId, 'RoomIdMax');

        $roomIdMin = $roomIdMin !== null ? (int)$roomIdMin : 1;
        $roomIdMax = $roomIdMax !== null ? (int)$roomIdMax : 10;

        if ($roomIdMin < 0) {
            $roomIdMin = 0;
        }

        if ($roomIdMax < $roomIdMin) {
            $roomIdMax = $roomIdMin;
        }

        $rooms = [];
        for ($i = $roomIdMin; $i <= $roomIdMax; $i++) {
            $rooms[] = sprintf('ROOM-%02d', $i);
        }

        return [
            'rooms' => $rooms,
            'min' => $roomIdMin,
            'max' => $roomIdMax,
        ];
    }

    // ==================== JADWAL PESERTA UJIAN ====================

    public function jadwalPesertaUjian()
    {
        helper('munaqosah');

        $helpFunctionModel = new \App\Models\HelpFunctionModel();
        $currentTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();

        $idTpq = session()->get('IdTpq');
        $dataTpq = $this->helpFunction->getDataTpq($idTpq);
        $isAdmin = empty($idTpq) || $idTpq == 0;

        // Get TPQ data dari peserta yang terdaftar, grouped by IdTpq
        $tpqFromPeserta = $this->pesertaMunaqosahModel->getTpqFromPeserta(
            $currentTahunAjaran,
            $isAdmin ? null : $idTpq
        );

        // Get konfigurasi grup jadwal peserta (default: start=1, end=8)
        $configIdTpq = $isAdmin ? '0' : $idTpq;
        $groupStart = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NoGroupJadwalPesertaStart', 1);
        $groupEnd = $this->munaqosahKonfigurasiModel->getSettingAsInt($configIdTpq, 'NoGroupJadwalPesertaEnd', 8);

        $data = [
            'page_title' => 'Jadwal Peserta Ujian',
            'current_tahun_ajaran' => $currentTahunAjaran,
            'tpqDropdown' => $dataTpq,
            'tpqFromPeserta' => $tpqFromPeserta,
            'isAdmin' => $isAdmin,
            'groupStart' => $groupStart,
            'groupEnd' => $groupEnd,
        ];

        return view('backend/Munaqosah/jadwalPesertaUjian', $data);
    }

    public function getJadwalPesertaUjian()
    {
        $idTahunAjaran = $this->request->getGet('tahunAjaran');
        $typeUjian = $this->request->getGet('typeUjian');
        $idTpq = session()->get('IdTpq');
        $isAdmin = empty($idTpq) || $idTpq == 0;

        if (empty($idTahunAjaran)) {
            $helpFunctionModel = new \App\Models\HelpFunctionModel();
            $idTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();
        }

        $jadwal = $this->munaqosahJadwalUjianModel->getJadwalGrouped($idTahunAjaran, $typeUjian);

        if (!$isAdmin) {
            $jadwal = array_filter($jadwal, function ($item) use ($idTpq) {
                return $item['IdTpq'] == $idTpq;
            });
            $jadwal = array_values($jadwal);
        }

        // Format data untuk tabel dengan grouping berdasarkan Group, Tanggal, Jam
        $formattedData = [];
        $groupedData = [];

        // Group data berdasarkan GroupPeserta, Tanggal, dan Jam
        foreach ($jadwal as $row) {
            $jumlahPeserta = $this->munaqosahJadwalUjianModel->getCountPesertaByTpq(
                $row['IdTpq'],
                $idTahunAjaran,
                $row['TypeUjian']
            );

            $key = $row['GroupPeserta'] . '_' . $row['Tanggal'] . '_' . $row['Jam'];

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'GroupPeserta' => $row['GroupPeserta'],
                    'Tanggal' => $row['Tanggal'],
                    'Jam' => $row['Jam'],
                    'rows' => []
                ];
            }

            $groupedData[$key]['rows'][] = [
                'id' => $row['id'],
                'GroupPeserta' => $row['GroupPeserta'],
                'Tanggal' => $row['Tanggal'],
                'Jam' => $row['Jam'],
                'IdTpq' => $row['IdTpq'],
                'NamaTpq' => $row['NamaTpq'] ?? '-',
                'KelurahanDesa' => $row['KelurahanDesa'] ?? ($row['DesaKelurahan'] ?? '-'),
                'Jumlah' => $jumlahPeserta,
                'TypeUjian' => $row['TypeUjian'],
            ];
        }

        // Convert ke array dan sort
        $formattedData = array_values($groupedData);
        usort($formattedData, function ($a, $b) {
            // Sort by Tanggal, then Jam, then GroupPeserta
            if ($a['Tanggal'] != $b['Tanggal']) {
                return strcmp($a['Tanggal'], $b['Tanggal']);
            }
            if ($a['Jam'] != $b['Jam']) {
                return strcmp($a['Jam'], $b['Jam']);
            }
            return strcmp($a['GroupPeserta'], $b['GroupPeserta']);
        });

        // Hitung grand total
        $grandTotal = 0;
        foreach ($formattedData as $group) {
            if (isset($group['rows']) && is_array($group['rows'])) {
                foreach ($group['rows'] as $row) {
                    $grandTotal += isset($row['Jumlah']) ? (int)$row['Jumlah'] : 0;
                }
            }
        }

        // Pastikan data selalu berupa array, meskipun kosong
        if (empty($formattedData)) {
            $formattedData = [];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $formattedData,
            'grandTotal' => $grandTotal,
            'message' => empty($formattedData) ? 'Tidak ada data jadwal' : 'Data berhasil dimuat'
        ]);
    }

    public function saveJadwalPesertaUjian()
    {
        $rules = [
            'GroupPeserta' => 'required',
            'Tanggal' => 'required',
            'Jam' => 'required',
            'IdTpq' => 'required',
            'IdTahunAjaran' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $idTpq = session()->get('IdTpq');
        $isAdmin = empty($idTpq) || $idTpq == 0;
        $idTpqInput = $this->request->getPost('IdTpq');
        $idTahunAjaran = $this->request->getPost('IdTahunAjaran');
        $typeUjian = $isAdmin ? $this->request->getPost('TypeUjian') : 'pra-munaqosah';

        // Validasi: Cek apakah IdTpq sudah ada di tabel untuk IdTahunAjaran dan TypeUjian yang sama
        // Satu TPQ bisa memiliki jadwal untuk "Munaqosah" dan "Pra-Munaqosah" (type ujian berbeda)
        // Tapi satu TPQ tidak bisa memiliki dua jadwal untuk type ujian yang sama dalam tahun ajaran yang sama
        $existingJadwal = $this->munaqosahJadwalUjianModel->where('IdTpq', $idTpqInput)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->where('Status', 'aktif')
            ->first();

        if ($existingJadwal) {
            $typeUjianLabel = ($typeUjian === 'munaqosah') ? 'Munaqosah' : 'Pra-Munaqosah';
            return $this->response->setJSON([
                'success' => false,
                'message' => 'TPQ ini sudah memiliki jadwal untuk tahun ajaran ' . $idTahunAjaran . ' dengan type ujian ' . $typeUjianLabel . '. Setiap TPQ hanya bisa memiliki satu jadwal per tahun ajaran per type ujian.'
            ]);
        }

        $data = [
            'GroupPeserta' => $this->request->getPost('GroupPeserta'),
            'Tanggal' => $this->request->getPost('Tanggal'),
            'Jam' => $this->request->getPost('Jam'),
            'IdTpq' => $idTpqInput,
            'IdTahunAjaran' => $idTahunAjaran,
            'TypeUjian' => $typeUjian,
            'Status' => 'aktif',
        ];

        if ($this->munaqosahJadwalUjianModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jadwal berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan jadwal',
                'errors' => $this->munaqosahJadwalUjianModel->errors()
            ]);
        }
    }

    public function updateJadwalPesertaUjian($id)
    {
        $rules = [
            'GroupPeserta' => 'required',
            'Tanggal' => 'required',
            'Jam' => 'required',
            'IdTpq' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $idTpq = session()->get('IdTpq');
        $isAdmin = empty($idTpq) || $idTpq == 0;
        $idTpqInput = $this->request->getPost('IdTpq');

        // Ambil data jadwal yang sedang di-update untuk mendapatkan IdTahunAjaran
        $currentJadwal = $this->munaqosahJadwalUjianModel->find($id);
        if (!$currentJadwal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ]);
        }

        $idTahunAjaran = $currentJadwal['IdTahunAjaran'];
        $typeUjian = $isAdmin ? $this->request->getPost('TypeUjian') : ($currentJadwal['TypeUjian'] ?? 'pra-munaqosah');

        // Validasi: Cek apakah IdTpq sudah ada di tabel untuk IdTahunAjaran dan TypeUjian yang sama
        // Exclude ID yang sedang di-update
        $existingJadwal = $this->munaqosahJadwalUjianModel->where('IdTpq', $idTpqInput)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->where('Status', 'aktif')
            ->where('id !=', $id)
            ->first();

        if ($existingJadwal) {
            $typeUjianLabel = ($typeUjian === 'munaqosah') ? 'Munaqosah' : 'Pra-Munaqosah';
            return $this->response->setJSON([
                'success' => false,
                'message' => 'TPQ ini sudah memiliki jadwal untuk tahun ajaran ' . $idTahunAjaran . ' dengan type ujian ' . $typeUjianLabel . '. Setiap TPQ hanya bisa memiliki satu jadwal per tahun ajaran per type ujian.'
            ]);
        }

        $data = [
            'GroupPeserta' => $this->request->getPost('GroupPeserta'),
            'Tanggal' => $this->request->getPost('Tanggal'),
            'Jam' => $this->request->getPost('Jam'),
            'IdTpq' => $idTpqInput,
        ];

        if ($isAdmin) {
            $data['TypeUjian'] = $typeUjian;
        }

        if ($this->munaqosahJadwalUjianModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jadwal berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate jadwal',
                'errors' => $this->munaqosahJadwalUjianModel->errors()
            ]);
        }
    }

    public function deleteJadwalPesertaUjian($id)
    {
        if ($this->munaqosahJadwalUjianModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus jadwal'
            ]);
        }
    }

    public function getTpqFromPeserta()
    {
        $idTahunAjaran = $this->request->getGet('tahunAjaran');
        $typeUjian = $this->request->getGet('typeUjian');
        $idTpq = session()->get('IdTpq');
        $isAdmin = empty($idTpq) || $idTpq == 0;

        if (empty($idTahunAjaran)) {
            $helpFunctionModel = new \App\Models\HelpFunctionModel();
            $idTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();
        }

        // Get TPQ dari peserta menggunakan model
        $tpqFromPeserta = $this->pesertaMunaqosahModel->getTpqFromPeserta(
            $idTahunAjaran,
            $isAdmin ? null : $idTpq
        );

        // Get IdTpq yang sudah ada di jadwal untuk tahun ajaran ini dan type ujian yang dipilih
        // Logika: TPQ yang sudah digunakan untuk type ujian tertentu tidak akan muncul lagi untuk type ujian yang sama
        // Tapi TPQ yang sudah digunakan untuk "Munaqosah" bisa muncul lagi untuk "Pra-Munaqosah" dan sebaliknya
        $existingJadwalBuilder = $this->munaqosahJadwalUjianModel->where('IdTahunAjaran', $idTahunAjaran)
            ->where('Status', 'aktif');

        // Filter berdasarkan typeUjian jika ada - hanya exclude TPQ yang sudah digunakan untuk type ujian yang sama
        if (!empty($typeUjian)) {
            $existingJadwalBuilder->where('TypeUjian', $typeUjian);
        }

        $existingJadwal = $existingJadwalBuilder->findAll();

        $existingIdTpq = [];
        foreach ($existingJadwal as $jadwal) {
            $existingIdTpq[] = $jadwal['IdTpq'];
        }

        // Filter TPQ: exclude TPQ yang sudah ada di jadwal untuk type ujian yang sama
        // TPQ yang sudah digunakan untuk type ujian lain tetap muncul
        $filteredTpq = array_filter($tpqFromPeserta, function ($tpq) use ($existingIdTpq) {
            return !in_array($tpq['IdTpq'], $existingIdTpq);
        });

        return $this->response->setJSON([
            'success' => true,
            'data' => array_values($filteredTpq)
        ]);
    }

    public function printJadwalPeserta()
    {
        try {
            $idTahunAjaran = $this->request->getGet('tahunAjaran');
            $typeUjian = $this->request->getGet('typeUjian');
            $idTpq = session()->get('IdTpq');
            $isAdmin = empty($idTpq) || $idTpq == 0;

            if (empty($idTahunAjaran)) {
                $helpFunctionModel = new \App\Models\HelpFunctionModel();
                $idTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();
            }

            // Get jadwal data
            $jadwal = $this->munaqosahJadwalUjianModel->getJadwalGrouped($idTahunAjaran, $typeUjian);

            if (!$isAdmin) {
                $jadwal = array_filter($jadwal, function ($item) use ($idTpq) {
                    return $item['IdTpq'] == $idTpq;
                });
                $jadwal = array_values($jadwal);
            }

            // Format data untuk tabel dengan grouping
            $formattedData = [];
            $groupedData = [];

            foreach ($jadwal as $row) {
                $jumlahPeserta = $this->munaqosahJadwalUjianModel->getCountPesertaByTpq(
                    $row['IdTpq'],
                    $idTahunAjaran,
                    $row['TypeUjian']
                );

                $key = $row['GroupPeserta'] . '_' . $row['Tanggal'] . '_' . $row['Jam'];

                if (!isset($groupedData[$key])) {
                    $groupedData[$key] = [
                        'GroupPeserta' => $row['GroupPeserta'],
                        'Tanggal' => $row['Tanggal'],
                        'Jam' => $row['Jam'],
                        'rows' => []
                    ];
                }

                $groupedData[$key]['rows'][] = [
                    'id' => $row['id'],
                    'GroupPeserta' => $row['GroupPeserta'],
                    'Tanggal' => $row['Tanggal'],
                    'Jam' => $row['Jam'],
                    'IdTpq' => $row['IdTpq'],
                    'NamaTpq' => $row['NamaTpq'] ?? '-',
                    'KelurahanDesa' => $row['KelurahanDesa'] ?? '-',
                    'Jumlah' => $jumlahPeserta,
                    'TypeUjian' => $row['TypeUjian'],
                ];
            }

            // Convert ke array dan sort
            $formattedData = array_values($groupedData);
            usort($formattedData, function ($a, $b) {
                if ($a['Tanggal'] != $b['Tanggal']) {
                    return strcmp($a['Tanggal'], $b['Tanggal']);
                }
                if ($a['Jam'] != $b['Jam']) {
                    return strcmp($a['Jam'], $b['Jam']);
                }
                return strcmp($a['GroupPeserta'], $b['GroupPeserta']);
            });

            // Hitung grand total
            $grandTotal = 0;
            foreach ($formattedData as $group) {
                foreach ($group['rows'] as $row) {
                    $grandTotal += $row['Jumlah'];
                }
            }

            // Prepare data for view
            $data = [
                'jadwal' => $formattedData,
                'grandTotal' => $grandTotal,
                'tahunAjaran' => $idTahunAjaran,
                'typeUjian' => $typeUjian ?? 'munaqosah',
                'generated_at' => date('Y-m-d H:i:s')
            ];

            // Load view untuk PDF
            $html = view('backend/Munaqosah/printJadwalPeserta', $data);

            // Setup Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'Arial');
            $options->set('isFontSubsettingEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            $filename = 'Jadwal_Peserta_Ujian_' . str_replace('/', '_', $idTahunAjaran) . '_' . ($typeUjian ?? 'munaqosah') . '.pdf';

            if (ob_get_length()) {
                ob_end_clean();
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $dompdf->output();
            exit();
        } catch (\Throwable $e) {
            log_message('error', 'Error in printJadwalPeserta: ' . $e->getMessage());
            return redirect()->to(base_url('backend/munaqosah/jadwal-peserta-ujian'))
                ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}

