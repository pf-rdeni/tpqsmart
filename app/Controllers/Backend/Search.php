<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;

class Search extends BaseController
{
    protected $helpFunctionModel;

    public function __construct()
    {
        $this->helpFunctionModel = new HelpFunctionModel();
    }

    /**
     * Menampilkan halaman hasil pencarian
     */
    public function index()
    {
        $query = $this->request->getGet('q') ?? '';
        $query = trim($query);

        // Ambil semua menu yang bisa diakses user
        $availableMenus = $this->getAvailableMenus();

        // Filter menu berdasarkan query
        $results = [];
        if (!empty($query)) {
            $queryLower = strtolower($query);
            foreach ($availableMenus as $menu) {
                $menuTitleLower = strtolower($menu['title']);
                $menuCategoryLower = strtolower($menu['category'] ?? '');
                $menuDescriptionLower = strtolower($menu['description'] ?? '');

                // Cek apakah query cocok dengan title, category, atau description
                if (
                    strpos($menuTitleLower, $queryLower) !== false ||
                    strpos($menuCategoryLower, $queryLower) !== false ||
                    strpos($menuDescriptionLower, $queryLower) !== false
                ) {
                    // Hitung relevansi (prioritas: title > category > description)
                    $relevance = 0;
                    if (strpos($menuTitleLower, $queryLower) !== false) {
                        $relevance += 10;
                        if ($menuTitleLower === $queryLower) {
                            $relevance += 5; // Exact match
                        }
                    }
                    if (strpos($menuCategoryLower, $queryLower) !== false) {
                        $relevance += 5;
                    }
                    if (strpos($menuDescriptionLower, $queryLower) !== false) {
                        $relevance += 2;
                    }

                    $menu['relevance'] = $relevance;
                    $results[] = $menu;
                }
            }

            // Sort berdasarkan relevansi
            usort($results, function ($a, $b) {
                return $b['relevance'] - $a['relevance'];
            });
        } else {
            // Jika tidak ada query, tampilkan semua menu yang tersedia (rekomendasi)
            $results = $availableMenus;
        }

        // Group hasil berdasarkan kategori
        $groupedResults = [];
        foreach ($results as $result) {
            $category = $result['category'] ?? 'Lainnya';
            if (!isset($groupedResults[$category])) {
                $groupedResults[$category] = [];
            }
            $groupedResults[$category][] = $result;
        }

        $data = [
            'page_title' => 'Hasil Pencarian',
            'query' => $query,
            'results' => $results,
            'groupedResults' => $groupedResults,
            'totalResults' => count($results),
        ];

        return view('backend/search/index', $data);
    }

    /**
     * Mendapatkan semua menu yang bisa diakses user berdasarkan permission
     */
    private function getAvailableMenus()
    {
        $menus = [];
        $activeRole = session()->get('active_role');
        $availableRoles = session()->get('available_roles') ?? [];
        
        // Cek apakah sedang di halaman Munaqosah (untuk menyembunyikan menu operator)
        $currentUri = current_url(true);
        $uriString = uri_string();
        $request = \Config\Services::request();
        $dashboardParam = $request->getGet('dashboard');
        $isMunaqosahPage = (
            strpos($uriString, 'munaqosah') !== false ||
            strpos($currentUri->getPath(), 'munaqosah') !== false ||
            $dashboardParam === 'munaqosah'
        );
        
        // Cek apakah sedang di halaman MyAuth
        $isMyAuthPage = (
            strpos($uriString, 'backend/auth') !== false ||
            strpos($currentUri->getPath(), 'backend/auth') !== false ||
            $dashboardParam === 'myauth'
        );
        
        // Cek apakah user memiliki peran operator
        $hasOperatorRole = ($activeRole === 'operator' || (empty($activeRole) && in_groups('Operator')));
        
        // Menu operator (Kelembagaan, Guru, Santri, dll) tidak ditampilkan jika sedang di halaman Munaqosah atau MyAuth
        // Tapi menu Munaqosah tetap muncul untuk operator
        $isActiveOperator = $hasOperatorRole && !$isMunaqosahPage && !$isMyAuthPage;
        
        // Cek apakah user memiliki peran guru
        $hasGuruRole = in_array('guru', $availableRoles) || in_groups('Guru');
        $isActiveGuru = (
            $activeRole === 'guru' ||
            $activeRole === 'wali_kelas' ||
            $activeRole === 'kepala_tpq' ||
            (empty($activeRole) && $hasGuruRole)
        );

        // Dashboard - Semua user
        $menus[] = [
            'title' => 'Dashboard',
            'url' => base_url('/'),
            'icon' => 'fas fa-tachometer-alt',
            'category' => 'Utama',
            'description' => 'Halaman utama dashboard',
        ];

        // Menu untuk Admin
        if (in_groups('Admin')) {
            // Sholat & Al-Qur'an
            $menus[] = [
                'title' => 'Jadwal Sholat',
                'url' => base_url('backend/jadwal-sholat'),
                'icon' => 'fas fa-mosque',
                'category' => 'Sholat & Al-Qur\'an',
                'description' => 'Kelola jadwal sholat',
            ];
            $menus[] = [
                'title' => 'Surah Al-Qur\'an',
                'url' => base_url('backend/surah'),
                'icon' => 'fas fa-book-quran',
                'category' => 'Sholat & Al-Qur\'an',
                'description' => 'Daftar surah Al-Qur\'an',
            ];
            $menus[] = [
                'title' => 'Cari Ayat',
                'url' => base_url('backend/ayah'),
                'icon' => 'fas fa-search',
                'category' => 'Sholat & Al-Qur\'an',
                'description' => 'Pencarian ayat Al-Qur\'an',
            ];
            $menus[] = [
                'title' => 'Pencarian Al-Qur\'an',
                'url' => base_url('backend/quran/search'),
                'icon' => 'fas fa-search',
                'category' => 'Sholat & Al-Qur\'an',
                'description' => 'Pencarian dalam Al-Qur\'an',
            ];

            // Kelembagaan
            $menus[] = [
                'title' => 'List TPQ',
                'url' => base_url('backend/tpq/show'),
                'icon' => 'fas fa-building',
                'category' => 'Kelembagaan',
                'description' => 'Daftar TPQ',
            ];
            $menus[] = [
                'title' => 'List MDA',
                'url' => base_url('backend/mda/show'),
                'icon' => 'fas fa-building',
                'category' => 'Kelembagaan',
                'description' => 'Daftar MDA',
            ];

            // Sertifikasi
            $menus[] = [
                'title' => 'List Peserta Sertifikasi',
                'url' => base_url('backend/sertifikasi/listPesertaSertifikasi'),
                'icon' => 'fas fa-certificate',
                'category' => 'Sertifikasi',
                'description' => 'Daftar peserta sertifikasi',
            ];
            $menus[] = [
                'title' => 'List Nilai Sertifikasi',
                'url' => base_url('backend/sertifikasi/listNilaiSertifikasi'),
                'icon' => 'fas fa-certificate',
                'category' => 'Sertifikasi',
                'description' => 'Daftar nilai sertifikasi',
            ];
            $menus[] = [
                'title' => 'Data Juri Sertifikasi',
                'url' => base_url('backend/sertifikasi/listJuriSertifikasi'),
                'icon' => 'fas fa-user-tie',
                'category' => 'Sertifikasi',
                'description' => 'Data juri sertifikasi',
            ];

            // Munaqosah
            $menus[] = [
                'title' => 'Dashboard Munaqosah',
                'url' => base_url('backend/munaqosah/dashboard-munaqosah'),
                'icon' => 'fas fa-graduation-cap',
                'category' => 'Munaqosah',
                'description' => 'Dashboard munaqosah',
            ];
            $menus[] = [
                'title' => 'Kategori Materi',
                'url' => base_url('backend/kategori-materi'),
                'icon' => 'fas fa-list',
                'category' => 'Munaqosah',
                'description' => 'Kelola kategori materi',
            ];
            $menus[] = [
                'title' => 'Kategori Kesalahan',
                'url' => base_url('backend/munaqosah/list-kategori-kesalahan'),
                'icon' => 'fas fa-exclamation-triangle',
                'category' => 'Munaqosah',
                'description' => 'Kelola kategori kesalahan',
            ];
            $menus[] = [
                'title' => 'Grup Materi Ujian',
                'url' => base_url('backend/munaqosah/grup-materi-ujian'),
                'icon' => 'fas fa-layer-group',
                'category' => 'Munaqosah',
                'description' => 'Kelola grup materi ujian',
            ];
            $menus[] = [
                'title' => 'Materi Ujian',
                'url' => base_url('backend/munaqosah/materi'),
                'icon' => 'fas fa-book',
                'category' => 'Munaqosah',
                'description' => 'Kelola materi ujian',
            ];
            $menus[] = [
                'title' => 'Bobot Nilai',
                'url' => base_url('backend/munaqosah/bobot'),
                'icon' => 'fas fa-balance-scale',
                'category' => 'Munaqosah',
                'description' => 'Kelola bobot nilai',
            ];
            $menus[] = [
                'title' => 'Jadwal Peserta Ujian',
                'url' => base_url('backend/munaqosah/jadwal-peserta-ujian'),
                'icon' => 'fas fa-calendar-alt',
                'category' => 'Munaqosah',
                'description' => 'Kelola jadwal peserta ujian',
            ];
            $menus[] = [
                'title' => 'Nilai Kelulusan',
                'url' => base_url('backend/munaqosah/export-hasil-munaqosah'),
                'icon' => 'fas fa-check-circle',
                'category' => 'Munaqosah',
                'description' => 'Nilai kelulusan munaqosah',
            ];
            $menus[] = [
                'title' => 'Monitoring Munaqosah',
                'url' => base_url('backend/munaqosah/monitoring'),
                'icon' => 'fas fa-chart-line',
                'category' => 'Munaqosah',
                'description' => 'Monitoring munaqosah',
            ];
            $menus[] = [
                'title' => 'Kelulusan Ujian',
                'url' => base_url('backend/munaqosah/kelulusan'),
                'icon' => 'fas fa-check-circle',
                'category' => 'Munaqosah',
                'description' => 'Kelola kelulusan ujian',
            ];
            $menus[] = [
                'title' => 'Konfigurasi Munaqosah',
                'url' => base_url('backend/munaqosah/list-konfigurasi-munaqosah'),
                'icon' => 'fas fa-cog',
                'category' => 'Munaqosah',
                'description' => 'Konfigurasi munaqosah',
            ];
            $menus[] = [
                'title' => 'Data Juri dan Panitia',
                'url' => base_url('backend/munaqosah/juri'),
                'icon' => 'fas fa-users',
                'category' => 'Munaqosah',
                'description' => 'Data juri dan panitia',
            ];
            // Daftar Peserta tidak boleh diakses oleh Juri
            if (!in_groups('Juri')) {
                $menus[] = [
                    'title' => 'Daftar Peserta',
                    'url' => base_url('backend/munaqosah/peserta'),
                    'icon' => 'fas fa-user-graduate',
                    'category' => 'Munaqosah',
                    'description' => 'Daftar peserta munaqosah',
                ];
            }

            // Setting
            $menus[] = [
                'title' => 'Auth Group',
                'url' => base_url('backend/user/authGroup'),
                'icon' => 'fas fa-users-cog',
                'category' => 'Setting',
                'description' => 'Kelola auth group',
            ];
            $menus[] = [
                'title' => 'Pengaturan Umum',
                'url' => base_url('backend/tools/index'),
                'icon' => 'fas fa-cog',
                'category' => 'Setting',
                'description' => 'Pengaturan umum sistem',
            ];
            $menus[] = [
                'title' => 'Reset Nilai',
                'url' => base_url('backend/nilai/resetNilaiIndex'),
                'icon' => 'fas fa-redo',
                'category' => 'Setting',
                'description' => 'Reset nilai',
            ];
            $menus[] = [
                'title' => 'Log Viewer',
                'url' => base_url('backend/logviewer'),
                'icon' => 'fas fa-file-alt',
                'category' => 'Setting',
                'description' => 'View log sistem',
            ];
            $menus[] = [
                'title' => 'Normalisasi Data',
                'url' => base_url('backend/kelas/showCheckDuplikasiKelasSantri'),
                'icon' => 'fas fa-database',
                'category' => 'Setting',
                'description' => 'Normalisasi data kelas santri',
            ];

            // MyAuth Management
            $menus[] = [
                'title' => 'Dashboard MyAuth',
                'url' => base_url('backend/auth'),
                'icon' => 'fas fa-shield-alt',
                'category' => 'MyAuth',
                'description' => 'Dashboard pengaturan MyAuth',
            ];
            $menus[] = [
                'title' => 'Manajemen User',
                'url' => base_url('backend/auth/users'),
                'icon' => 'fas fa-users',
                'category' => 'MyAuth',
                'description' => 'Manajemen user MyAuth',
            ];
            $menus[] = [
                'title' => 'Manajemen Group',
                'url' => base_url('backend/auth/groups'),
                'icon' => 'fas fa-users-cog',
                'category' => 'MyAuth',
                'description' => 'Manajemen group MyAuth',
            ];
            $menus[] = [
                'title' => 'Manajemen Permission',
                'url' => base_url('backend/auth/permissions'),
                'icon' => 'fas fa-key',
                'category' => 'MyAuth',
                'description' => 'Manajemen permission MyAuth',
            ];
            $menus[] = [
                'title' => 'Riwayat Login',
                'url' => base_url('backend/auth/loginAttempts'),
                'icon' => 'fas fa-history',
                'category' => 'MyAuth',
                'description' => 'Riwayat login attempt',
            ];
            $menus[] = [
                'title' => 'Token Reset Password',
                'url' => base_url('backend/auth/passwordResets'),
                'icon' => 'fas fa-key',
                'category' => 'MyAuth',
                'description' => 'Token reset password',
            ];
            $menus[] = [
                'title' => 'User Online',
                'url' => base_url('backend/auth/onlineUsers'),
                'icon' => 'fas fa-user-check',
                'category' => 'MyAuth',
                'description' => 'Daftar user yang sedang online',
            ];
        }

        // Menu untuk Admin dan Operator
        if (in_groups('Admin') || $isActiveOperator) {
            // Guru
            $menus[] = [
                'title' => 'Daftar Guru',
                'url' => base_url('backend/guru/show'),
                'icon' => 'fas fa-user',
                'category' => 'Guru',
                'description' => 'Daftar guru',
            ];
            $menus[] = [
                'title' => 'Sertifikasi Guru',
                'url' => base_url('backend/guru/showSertifikasi'),
                'icon' => 'fas fa-certificate',
                'category' => 'Guru',
                'description' => 'Sertifikasi guru',
            ];
            $menus[] = [
                'title' => 'Rekening Bank Guru',
                'url' => base_url('backend/guru/showAkunBank'),
                'icon' => 'fas fa-university',
                'category' => 'Guru',
                'description' => 'Rekening bank guru',
            ];
            $menus[] = [
                'title' => 'Setting Guru Kelas',
                'url' => base_url('backend/guruKelas/show'),
                'icon' => 'fas fa-chalkboard-teacher',
                'category' => 'Guru',
                'description' => 'Kelola guru kelas',
            ];

            // Santri
            $menus[] = [
                'title' => 'Santri Baru',
                'url' => base_url('backend/santri/createEmisStep'),
                'icon' => 'fas fa-user-plus',
                'category' => 'Santri',
                'description' => 'Input data santri baru',
            ];
            if (in_groups('Admin') || $isActiveOperator) {
                $menus[] = [
                    'title' => 'Registrasi Santri Baru',
                    'url' => base_url('backend/kelas/showSantriKelasBaru'),
                    'icon' => 'fas fa-user-plus',
                    'category' => 'Santri',
                    'description' => 'Registrasi santri baru ke kelas',
                ];
            }
            $menus[] = [
                'title' => 'Profil Santri',
                'url' => base_url('backend/santri/showProfilSantri'),
                'icon' => 'fas fa-user',
                'category' => 'Santri',
                'description' => 'Lihat profil santri',
            ];
            $menus[] = [
                'title' => 'Detail Profil Santri',
                'url' => base_url('backend/santri/detailProfilSantri'),
                'icon' => 'fas fa-user-circle',
                'category' => 'Santri',
                'description' => 'Detail profil santri lengkap',
            ];
            $menus[] = [
                'title' => 'Ubah Santri',
                'url' => base_url('backend/santri/showAturSantriBaru'),
                'icon' => 'fas fa-user-edit',
                'category' => 'Santri',
                'description' => 'Ubah data santri',
            ];
            $menus[] = [
                'title' => 'Data Emis',
                'url' => base_url('backend/santri/showSantriEmis'),
                'icon' => 'fas fa-database',
                'category' => 'Santri',
                'description' => 'Data EMIS santri',
            ];
            $menus[] = [
                'title' => 'Santri Per Kelas',
                'url' => base_url('backend/santri/showSantriBaruPerkelasTpq'),
                'icon' => 'fas fa-users',
                'category' => 'Santri',
                'description' => 'Daftar santri per kelas',
            ];
            if (in_groups('Admin') || $isActiveOperator) {
                $menus[] = [
                    'title' => 'Kenaikan Kelas',
                    'url' => base_url('backend/kelas/showListSantriPerKelas'),
                    'icon' => 'fas fa-arrow-up',
                    'category' => 'Santri',
                    'description' => 'Kenaikan kelas santri',
                ];
            }

            // Kelembagaan untuk Operator
            if ($isActiveOperator) {
                $menus[] = [
                    'title' => 'Profil Lembaga',
                    'url' => base_url('backend/tpq/profilLembaga'),
                    'icon' => 'fas fa-building',
                    'category' => 'Kelembagaan',
                    'description' => 'Profil lembaga',
                ];
                $menus[] = [
                    'title' => 'Struktur Lembaga',
                    'url' => base_url('backend/strukturlembaga'),
                    'icon' => 'fas fa-sitemap',
                    'category' => 'Kelembagaan',
                    'description' => 'Struktur organisasi lembaga',
                ];
                $menus[] = [
                    'title' => 'Sarana Lembaga',
                    'url' => base_url('backend/tpq/showSaranaLembaga'),
                    'icon' => 'fas fa-building',
                    'category' => 'Kelembagaan',
                    'description' => 'Sarana dan prasarana lembaga',
                ];
            }

            // Raport
            $menus[] = [
                'title' => 'Preview Nilai',
                'url' => base_url('backend/raport/showPreviewNilai'),
                'icon' => 'fas fa-eye',
                'category' => 'Raport',
                'description' => 'Preview nilai santri',
            ];
            $menus[] = [
                'title' => 'Prestasi Santri',
                'url' => base_url('backend/raport/showPrestasiSantri'),
                'icon' => 'fas fa-trophy',
                'category' => 'Raport',
                'description' => 'Prestasi santri',
            ];
            $menus[] = [
                'title' => 'Rapor Semester Ganjil',
                'url' => base_url('backend/rapor/index/Ganjil'),
                'icon' => 'fas fa-file-alt',
                'category' => 'Raport',
                'description' => 'Rapor semester ganjil',
            ];
            $menus[] = [
                'title' => 'Rapor Semester Genap',
                'url' => base_url('backend/rapor/index/Genap'),
                'icon' => 'fas fa-file-alt',
                'category' => 'Raport',
                'description' => 'Rapor semester genap',
            ];
            $menus[] = [
                'title' => 'Serah Terima Semester Ganjil',
                'url' => base_url('backend/rapor/serah-terima/Ganjil'),
                'icon' => 'fas fa-exchange-alt',
                'category' => 'Raport',
                'description' => 'Serah terima rapor semester ganjil',
            ];
            $menus[] = [
                'title' => 'Serah Terima Semester Genap',
                'url' => base_url('backend/rapor/serah-terima/Genap'),
                'icon' => 'fas fa-exchange-alt',
                'category' => 'Raport',
                'description' => 'Serah terima rapor semester genap',
            ];
            $menus[] = [
                'title' => 'Kriteria Catatan Rapor',
                'url' => base_url('backend/rapor/kriteriaCatatanRapor'),
                'icon' => 'fas fa-clipboard-list',
                'category' => 'Raport',
                'description' => 'Kriteria catatan rapor',
            ];
            
            // Mapping Wali Kelas (conditional - hanya jika setting aktif)
            $toolsModel = new \App\Models\ToolsModel();
            $idTpq = session()->get('IdTpq');
            $idGuru = session()->get('IdGuru');
            $mappingEnabled = false;
            $isWaliKelas = false;
            $showMappingMenu = false;

            $isAdmin = in_groups('Admin');
            $isOperator = in_groups('Operator');

            if (!empty($idTpq)) {
                $mappingEnabled = $toolsModel->getSetting($idTpq, 'MappingWaliKelas');

                if (($isAdmin || $isOperator) && $mappingEnabled) {
                    $showMappingMenu = true;
                } elseif (!empty($idGuru) && $mappingEnabled) {
                    $helpFunctionModel = new \App\Models\HelpFunctionModel();
                    $idTahunAjaran = session()->get('IdTahunAjaran');
                    if (empty($idTahunAjaran)) {
                        $idTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();
                    }

                    $guruKelasData = $helpFunctionModel->getDataGuruKelas(
                        IdGuru: $idGuru,
                        IdTpq: $idTpq,
                        IdTahunAjaran: $idTahunAjaran
                    );

                    foreach ($guruKelasData as $gk) {
                        $gkArray = is_object($gk) ? (array)$gk : $gk;
                        if (($gkArray['NamaJabatan'] ?? '') === 'Wali Kelas') {
                            $isWaliKelas = true;
                            break;
                        }
                    }

                    if ($isWaliKelas) {
                        $showMappingMenu = true;
                    }
                }
            }

            if ($showMappingMenu) {
                $menus[] = [
                    'title' => 'Mapping Wali Kelas',
                    'url' => base_url('backend/rapor/settingMappingWaliKelas'),
                    'icon' => 'fas fa-map',
                    'category' => 'Raport',
                    'description' => 'Mapping wali kelas untuk rapor',
                ];
            }

            // Extra
            $menus[] = [
                'title' => 'Download',
                'url' => base_url('backend/extra/showDownload'),
                'icon' => 'fas fa-download',
                'category' => 'Extra',
                'description' => 'Download file',
            ];
            $menus[] = [
                'title' => 'Artikel',
                'url' => base_url('backend/extra/showArtikel'),
                'icon' => 'fas fa-newspaper',
                'category' => 'Extra',
                'description' => 'Artikel',
            ];
            $menus[] = [
                'title' => 'QR Code',
                'url' => base_url('backend/qr/index'),
                'icon' => 'fas fa-qrcode',
                'category' => 'Extra',
                'description' => 'Generate QR Code',
            ];

            // Setting
            $menus[] = [
                'title' => 'Daftar Materi',
                'url' => base_url('backend/materiPelajaran/showMateriPelajaran'),
                'icon' => 'fas fa-book',
                'category' => 'Setting',
                'description' => 'Daftar materi pelajaran',
            ];
            $menus[] = [
                'title' => 'Materi Kelas',
                'url' => base_url('backend/kelasMateriPelajaran/showMateriKelas'),
                'icon' => 'fas fa-book-reader',
                'category' => 'Setting',
                'description' => 'Materi per kelas',
            ];
            $menus[] = [
                'title' => 'Daftar Akun',
                'url' => base_url('backend/user/index'),
                'icon' => 'fas fa-users',
                'category' => 'Setting',
                'description' => 'Daftar akun pengguna',
            ];

            // Munaqosah untuk Operator (hanya jika bukan Admin, karena Admin sudah punya semua)
            if ($isActiveOperator && !in_groups('Admin')) {
                $menus[] = [
                    'title' => 'Dashboard Munaqosah',
                    'url' => base_url('backend/munaqosah/dashboard-munaqosah'),
                    'icon' => 'fas fa-graduation-cap',
                    'category' => 'Munaqosah',
                    'description' => 'Dashboard munaqosah',
                ];
                $menus[] = [
                    'title' => 'Jadwal Peserta Ujian (Pra-Munaqosah)',
                    'url' => base_url('backend/munaqosah/jadwal-peserta-ujian?type=pra-munaqosah'),
                    'icon' => 'fas fa-calendar-alt',
                    'category' => 'Munaqosah',
                    'description' => 'Jadwal peserta ujian pra-munaqosah',
                ];
                $menus[] = [
                    'title' => 'Monitoring Munaqosah',
                    'url' => base_url('backend/munaqosah/monitoring'),
                    'icon' => 'fas fa-chart-line',
                    'category' => 'Munaqosah',
                    'description' => 'Monitoring munaqosah',
                ];
                $menus[] = [
                    'title' => 'Kelulusan Ujian',
                    'url' => base_url('backend/munaqosah/kelulusan'),
                    'icon' => 'fas fa-check-circle',
                    'category' => 'Munaqosah',
                    'description' => 'Kelola kelulusan ujian',
                ];
                $menus[] = [
                    'title' => 'Konfigurasi Munaqosah',
                    'url' => base_url('backend/munaqosah/list-konfigurasi-munaqosah'),
                    'icon' => 'fas fa-cog',
                    'category' => 'Munaqosah',
                    'description' => 'Konfigurasi munaqosah',
                ];
                $menus[] = [
                    'title' => 'Data Juri dan Panitia',
                    'url' => base_url('backend/munaqosah/juri'),
                    'icon' => 'fas fa-users',
                    'category' => 'Munaqosah',
                    'description' => 'Data juri dan panitia',
                ];
                // Daftar Peserta tidak boleh diakses oleh Juri
                if (!in_groups('Juri')) {
                    $menus[] = [
                        'title' => 'Daftar Peserta',
                        'url' => base_url('backend/munaqosah/peserta'),
                        'icon' => 'fas fa-user-graduate',
                        'category' => 'Munaqosah',
                        'description' => 'Daftar peserta munaqosah',
                    ];
                }
            }

            // Munaqosah untuk Panitia, Admin, atau Operator (Registrasi dan Antrian)
            // Hanya tambahkan jika belum ada (untuk menghindari duplikasi)
            // Registrasi Peserta tidak boleh diakses oleh Juri
            if ((in_groups('Panitia') || in_groups('Admin') || $isActiveOperator) && !in_groups('Juri')) {
                $menus[] = [
                    'title' => 'Registrasi Peserta',
                    'url' => base_url('backend/munaqosah/registrasi-peserta'),
                    'icon' => 'fas fa-user-plus',
                    'category' => 'Munaqosah',
                    'description' => 'Registrasi peserta munaqosah',
                ];
                $menus[] = [
                    'title' => 'Antrian Ujian',
                    'url' => base_url('backend/munaqosah/antrian'),
                    'icon' => 'fas fa-list-ol',
                    'category' => 'Munaqosah',
                    'description' => 'Antrian ujian',
                ];
            }
        }

        // Menu untuk Guru
        if ($isActiveGuru) {
            // Kesiswaan
            $menus[] = [
                'title' => 'Input Data Santri',
                'url' => base_url('backend/santri/createEmisStep'),
                'icon' => 'fas fa-user-plus',
                'category' => 'Kesiswaan',
                'description' => 'Input data santri baru',
            ];
            $menus[] = [
                'title' => 'Ubah Data Santri',
                'url' => base_url('backend/santri/showAturSantriBaru'),
                'icon' => 'fas fa-user-edit',
                'category' => 'Kesiswaan',
                'description' => 'Ubah data santri',
            ];
            $menus[] = [
                'title' => 'Profil Data Santri',
                'url' => base_url('backend/santri/showProfilSantri'),
                'icon' => 'fas fa-user',
                'category' => 'Kesiswaan',
                'description' => 'Lihat profil santri',
            ];

            // Absensi
            $menus[] = [
                'title' => 'Kehadiran',
                'url' => base_url('backend/absensi/index'),
                'icon' => 'fas fa-check-circle',
                'category' => 'Absensi',
                'description' => 'Input kehadiran santri',
            ];
            $menus[] = [
                'title' => 'Statistik Kehadiran',
                'url' => base_url('backend/absensi/statistikKehadiran'),
                'icon' => 'fas fa-chart-bar',
                'category' => 'Absensi',
                'description' => 'Statistik kehadiran santri',
            ];

            // Keuangan
            $menus[] = [
                'title' => 'Tabungan',
                'url' => base_url('backend/tabungan/showPerkelas'),
                'icon' => 'fas fa-piggy-bank',
                'category' => 'Keuangan',
                'description' => 'Kelola tabungan santri',
            ];
            $menus[] = [
                'title' => 'Iuran Bulanan',
                'url' => base_url('backend/iuranBulanan/showPerKelas'),
                'icon' => 'fas fa-money-bill-wave',
                'category' => 'Keuangan',
                'description' => 'Kelola iuran bulanan',
            ];

            // Penilaian
            $menus[] = [
                'title' => 'Prestasi',
                'url' => base_url('backend/prestasi/showPerKelas'),
                'icon' => 'fas fa-trophy',
                'category' => 'Penilaian',
                'description' => 'Kelola prestasi santri',
            ];
            $menus[] = [
                'title' => 'Input Nilai Semester Ganjil',
                'url' => base_url('backend/nilai/showSantriPerKelas/Ganjil'),
                'icon' => 'fas fa-edit',
                'category' => 'Penilaian',
                'description' => 'Input nilai semester ganjil',
            ];
            $menus[] = [
                'title' => 'Input Nilai Semester Genap',
                'url' => base_url('backend/nilai/showSantriPerKelas/Genap'),
                'icon' => 'fas fa-edit',
                'category' => 'Penilaian',
                'description' => 'Input nilai semester genap',
            ];
            $menus[] = [
                'title' => 'Detail Nilai Semester Ganjil',
                'url' => base_url('backend/nilai/showDetailNilaiSantriPerKelas/Ganjil'),
                'icon' => 'fas fa-list',
                'category' => 'Penilaian',
                'description' => 'Detail nilai semester ganjil',
            ];
            $menus[] = [
                'title' => 'Detail Nilai Semester Genap',
                'url' => base_url('backend/nilai/showDetailNilaiSantriPerKelas/Genap'),
                'icon' => 'fas fa-list',
                'category' => 'Penilaian',
                'description' => 'Detail nilai semester genap',
            ];
            $menus[] = [
                'title' => 'Rapor Nilai Semester Ganjil',
                'url' => base_url('backend/rapor/index/Ganjil'),
                'icon' => 'fas fa-file-alt',
                'category' => 'Penilaian',
                'description' => 'Rapor nilai semester ganjil',
            ];
            $menus[] = [
                'title' => 'Rapor Nilai Semester Genap',
                'url' => base_url('backend/rapor/index/Genap'),
                'icon' => 'fas fa-file-alt',
                'category' => 'Penilaian',
                'description' => 'Rapor nilai semester genap',
            ];
            
            // Mapping Wali Kelas untuk Guru (conditional - hanya jika setting aktif dan user adalah Wali Kelas)
            $toolsModelGuru = new \App\Models\ToolsModel();
            $idTpqGuru = session()->get('IdTpq');
            $idGuruGuru = session()->get('IdGuru');
            $mappingEnabledGuru = false;
            $isWaliKelasGuru = false;
            $showMappingMenuGuru = false;

            if (!empty($idTpqGuru) && !empty($idGuruGuru)) {
                $mappingEnabledGuru = $toolsModelGuru->getSetting($idTpqGuru, 'MappingWaliKelas');

                if ($mappingEnabledGuru) {
                    $helpFunctionModelGuru = new \App\Models\HelpFunctionModel();
                    $idTahunAjaranGuru = session()->get('IdTahunAjaran');
                    if (empty($idTahunAjaranGuru)) {
                        $idTahunAjaranGuru = $helpFunctionModelGuru->getTahunAjaranSaatIni();
                    }

                    $guruKelasDataGuru = $helpFunctionModelGuru->getDataGuruKelas(
                        IdGuru: $idGuruGuru,
                        IdTpq: $idTpqGuru,
                        IdTahunAjaran: $idTahunAjaranGuru
                    );

                    foreach ($guruKelasDataGuru as $gk) {
                        $gkArray = is_object($gk) ? (array)$gk : $gk;
                        if (($gkArray['NamaJabatan'] ?? '') === 'Wali Kelas') {
                            $isWaliKelasGuru = true;
                            break;
                        }
                    }

                    if ($isWaliKelasGuru) {
                        $showMappingMenuGuru = true;
                    }
                }
            }

            if ($showMappingMenuGuru) {
                $menus[] = [
                    'title' => 'Mapping Wali Kelas',
                    'url' => base_url('backend/rapor/settingMappingWaliKelas'),
                    'icon' => 'fas fa-map',
                    'category' => 'Penilaian',
                    'description' => 'Mapping wali kelas untuk rapor',
                ];
            }
        }

        // Menu untuk Juri
        if (in_groups('Juri')) {
            $menus[] = [
                'title' => 'Dashboard Munaqosah',
                'url' => base_url('backend/munaqosah/dashboard-munaqosah'),
                'icon' => 'fas fa-graduation-cap',
                'category' => 'Munaqosah',
                'description' => 'Dashboard munaqosah',
            ];
            $menus[] = [
                'title' => 'Input Nilai Juri',
                'url' => base_url('backend/munaqosah/input-nilai-juri'),
                'icon' => 'fas fa-edit',
                'category' => 'Munaqosah',
                'description' => 'Input nilai sebagai juri',
            ];
            $menus[] = [
                'title' => 'Data Nilai Juri',
                'url' => base_url('backend/munaqosah/data-nilai-juri'),
                'icon' => 'fas fa-list',
                'category' => 'Munaqosah',
                'description' => 'Data nilai yang sudah diinput',
            ];
            $menus[] = [
                'title' => 'Antrian Peserta',
                'url' => base_url('backend/munaqosah/monitoring-antrian-peserta-ruangan-juri'),
                'icon' => 'fas fa-list-ol',
                'category' => 'Munaqosah',
                'description' => 'Antrian peserta ujian',
            ];
            $menus[] = [
                'title' => 'Dashboard Monitoring',
                'url' => base_url('backend/munaqosah/dashboard-monitoring'),
                'icon' => 'fas fa-chart-line',
                'category' => 'Munaqosah',
                'description' => 'Dashboard monitoring',
            ];
        }

        // Menu untuk Panitia (tidak termasuk Juri)
        if (in_groups('Panitia') && !in_groups('Juri')) {
            $menus[] = [
                'title' => 'Dashboard Munaqosah',
                'url' => base_url('backend/munaqosah/dashboard-munaqosah'),
                'icon' => 'fas fa-graduation-cap',
                'category' => 'Munaqosah',
                'description' => 'Dashboard munaqosah',
            ];
            $menus[] = [
                'title' => 'Daftar Peserta',
                'url' => base_url('backend/munaqosah/peserta?type=munaqosah&tpq=0'),
                'icon' => 'fas fa-user-graduate',
                'category' => 'Munaqosah',
                'description' => 'Daftar peserta munaqosah',
            ];
            $menus[] = [
                'title' => 'Registrasi Peserta',
                'url' => base_url('backend/munaqosah/registrasi-peserta?type=munaqosah&tpq=0'),
                'icon' => 'fas fa-user-plus',
                'category' => 'Munaqosah',
                'description' => 'Registrasi peserta munaqosah',
            ];
            $menus[] = [
                'title' => 'Jadwal Peserta Ujian',
                'url' => base_url('backend/munaqosah/jadwal-peserta-ujian?type=munaqosah&tpq=0'),
                'icon' => 'fas fa-calendar-alt',
                'category' => 'Munaqosah',
                'description' => 'Jadwal peserta ujian',
            ];
            $menus[] = [
                'title' => 'Antrian Ujian',
                'url' => base_url('backend/munaqosah/antrian?type=munaqosah&tpq=0'),
                'icon' => 'fas fa-list-ol',
                'category' => 'Munaqosah',
                'description' => 'Antrian ujian',
            ];
            $menus[] = [
                'title' => 'Dashboard Monitoring',
                'url' => base_url('backend/munaqosah/dashboard-monitoring?type=munaqosah&tpq=0'),
                'icon' => 'fas fa-chart-line',
                'category' => 'Munaqosah',
                'description' => 'Dashboard monitoring',
            ];
            $menus[] = [
                'title' => 'Monitoring Munaqosah',
                'url' => base_url('backend/munaqosah/monitoring?type=munaqosah&tpq=0'),
                'icon' => 'fas fa-chart-line',
                'category' => 'Munaqosah',
                'description' => 'Monitoring munaqosah',
            ];
        }

        // Menu untuk JuriSertifikasi
        if (in_groups('JuriSertifikasi')) {
            $menus[] = [
                'title' => 'Dashboard Sertifikasi',
                'url' => base_url('backend/sertifikasi/dashboard'),
                'icon' => 'fas fa-certificate',
                'category' => 'Sertifikasi',
                'description' => 'Dashboard sertifikasi',
            ];
            $menus[] = [
                'title' => 'Input Nilai Sertifikasi',
                'url' => base_url('backend/sertifikasi/inputNilaiSertifikasi'),
                'icon' => 'fas fa-edit',
                'category' => 'Sertifikasi',
                'description' => 'Input nilai sertifikasi',
            ];
            $menus[] = [
                'title' => 'Nilai Peserta Sertifikasi',
                'url' => base_url('backend/sertifikasi/nilaiPesertaSertifikasi'),
                'icon' => 'fas fa-list',
                'category' => 'Sertifikasi',
                'description' => 'Nilai peserta sertifikasi',
            ];
        }

        // Menu untuk PanitiaSertifikasi
        if (in_groups('PanitiaSertifikasi')) {
            $menus[] = [
                'title' => 'Dashboard Panitia Sertifikasi',
                'url' => base_url('backend/sertifikasi/dashboardPanitiaSertifikasi'),
                'icon' => 'fas fa-certificate',
                'category' => 'Sertifikasi',
                'description' => 'Dashboard panitia sertifikasi',
            ];
            $menus[] = [
                'title' => 'List Peserta Sertifikasi',
                'url' => base_url('backend/sertifikasi/listPesertaSertifikasi'),
                'icon' => 'fas fa-users',
                'category' => 'Sertifikasi',
                'description' => 'List peserta sertifikasi',
            ];
            $menus[] = [
                'title' => 'List Nilai Sertifikasi',
                'url' => base_url('backend/sertifikasi/listNilaiSertifikasi'),
                'icon' => 'fas fa-list',
                'category' => 'Sertifikasi',
                'description' => 'List nilai sertifikasi',
            ];
        }

        // Menu untuk grup Santri
        if (in_groups('Santri')) {
            $menus[] = [
                'title' => 'Detail Profil',
                'url' => base_url('backend/santri/detailProfilSantri'),
                'icon' => 'fas fa-user-circle',
                'category' => 'Kesantrian',
                'description' => 'Detail profil santri lengkap',
            ];
            $menus[] = [
                'title' => 'Detail Absensi',
                'url' => base_url('backend/absensi/showAbsensiSantri'),
                'icon' => 'fas fa-calendar-check',
                'category' => 'Kesantrian',
                'description' => 'Detail absensi santri',
            ];
            $menus[] = [
                'title' => 'Detail Prestasi',
                'url' => base_url('backend/prestasi/showPrestasiSantri'),
                'icon' => 'fas fa-trophy',
                'category' => 'Kesantrian',
                'description' => 'Detail prestasi santri',
            ];
            $menus[] = [
                'title' => 'Detail Nilai',
                'url' => base_url('backend/nilai/showNilaiProfilDetail'),
                'icon' => 'fas fa-chart-line',
                'category' => 'Kesantrian',
                'description' => 'Detail nilai santri',
            ];
            $menus[] = [
                'title' => 'Detail Tabungan',
                'url' => base_url('backend/tabungan/showTabunganSantri'),
                'icon' => 'fas fa-piggy-bank',
                'category' => 'Kesantrian',
                'description' => 'Detail tabungan santri',
            ];
            $menus[] = [
                'title' => 'Kontak Santri',
                'url' => base_url('backend/santri/showKontakSantri'),
                'icon' => 'fas fa-address-book',
                'category' => 'Kesantrian',
                'description' => 'Kontak santri',
            ];
        }

        // Menu Penilaian untuk Operator (jika bukan Guru, karena Guru sudah punya di bagian atas)
        // Di sidebar: if ($isActiveGuru || $isActiveOperator || in_groups('Operator'))
        // Karena Guru sudah punya menu Penilaian, kita hanya tambahkan untuk Operator yang bukan Guru
        if (($isActiveOperator || in_groups('Operator')) && !$isActiveGuru) {
            $menus[] = [
                'title' => 'Prestasi',
                'url' => base_url('backend/prestasi/showPerKelas'),
                'icon' => 'fas fa-trophy',
                'category' => 'Penilaian',
                'description' => 'Kelola prestasi santri',
            ];
            $menus[] = [
                'title' => 'Input Nilai Semester Ganjil',
                'url' => base_url('backend/nilai/showSantriPerKelas/Ganjil'),
                'icon' => 'fas fa-edit',
                'category' => 'Penilaian',
                'description' => 'Input nilai semester ganjil',
            ];
            $menus[] = [
                'title' => 'Input Nilai Semester Genap',
                'url' => base_url('backend/nilai/showSantriPerKelas/Genap'),
                'icon' => 'fas fa-edit',
                'category' => 'Penilaian',
                'description' => 'Input nilai semester genap',
            ];
            $menus[] = [
                'title' => 'Detail Nilai Semester Ganjil',
                'url' => base_url('backend/nilai/showDetailNilaiSantriPerKelas/Ganjil'),
                'icon' => 'fas fa-list',
                'category' => 'Penilaian',
                'description' => 'Detail nilai semester ganjil',
            ];
            $menus[] = [
                'title' => 'Detail Nilai Semester Genap',
                'url' => base_url('backend/nilai/showDetailNilaiSantriPerKelas/Genap'),
                'icon' => 'fas fa-list',
                'category' => 'Penilaian',
                'description' => 'Detail nilai semester genap',
            ];
            $menus[] = [
                'title' => 'Rapor Nilai Semester Ganjil',
                'url' => base_url('backend/rapor/index/Ganjil'),
                'icon' => 'fas fa-file-alt',
                'category' => 'Penilaian',
                'description' => 'Rapor nilai semester ganjil',
            ];
            $menus[] = [
                'title' => 'Rapor Nilai Semester Genap',
                'url' => base_url('backend/rapor/index/Genap'),
                'icon' => 'fas fa-file-alt',
                'category' => 'Penilaian',
                'description' => 'Rapor nilai semester genap',
            ];
            $menus[] = [
                'title' => 'Serah Terima Semester Ganjil',
                'url' => base_url('backend/rapor/serah-terima/Ganjil'),
                'icon' => 'fas fa-exchange-alt',
                'category' => 'Penilaian',
                'description' => 'Serah terima rapor semester ganjil',
            ];
            $menus[] = [
                'title' => 'Serah Terima Semester Genap',
                'url' => base_url('backend/rapor/serah-terima/Genap'),
                'icon' => 'fas fa-exchange-alt',
                'category' => 'Penilaian',
                'description' => 'Serah terima rapor semester genap',
            ];
            $menus[] = [
                'title' => 'Kriteria Catatan Rapor',
                'url' => base_url('backend/rapor/kriteriaCatatanRapor'),
                'icon' => 'fas fa-clipboard-list',
                'category' => 'Penilaian',
                'description' => 'Kriteria catatan rapor',
            ];
            
            // Mapping Wali Kelas untuk Operator (conditional - hanya jika setting aktif)
            if (!isset($toolsModel)) {
                $toolsModel = new \App\Models\ToolsModel();
            }
            $idTpqOp = session()->get('IdTpq');
            $idGuruOp = session()->get('IdGuru');
            $mappingEnabledOp = false;
            $isWaliKelasOp = false;
            $showMappingMenuOp = false;

            $isAdminOp = in_groups('Admin');
            $isOperatorOp = in_groups('Operator');

            if (!empty($idTpqOp)) {
                $mappingEnabledOp = $toolsModel->getSetting($idTpqOp, 'MappingWaliKelas');

                if (($isAdminOp || $isOperatorOp) && $mappingEnabledOp) {
                    $showMappingMenuOp = true;
                } elseif (!empty($idGuruOp) && $mappingEnabledOp) {
                    if (!isset($helpFunctionModel)) {
                        $helpFunctionModel = new \App\Models\HelpFunctionModel();
                    }
                    $idTahunAjaranOp = session()->get('IdTahunAjaran');
                    if (empty($idTahunAjaranOp)) {
                        $idTahunAjaranOp = $helpFunctionModel->getTahunAjaranSaatIni();
                    }

                    $guruKelasDataOp = $helpFunctionModel->getDataGuruKelas(
                        IdGuru: $idGuruOp,
                        IdTpq: $idTpqOp,
                        IdTahunAjaran: $idTahunAjaranOp
                    );

                    foreach ($guruKelasDataOp as $gk) {
                        $gkArray = is_object($gk) ? (array)$gk : $gk;
                        if (($gkArray['NamaJabatan'] ?? '') === 'Wali Kelas') {
                            $isWaliKelasOp = true;
                            break;
                        }
                    }

                    if ($isWaliKelasOp) {
                        $showMappingMenuOp = true;
                    }
                }
            }

            if ($showMappingMenuOp) {
                $menus[] = [
                    'title' => 'Mapping Wali Kelas',
                    'url' => base_url('backend/rapor/settingMappingWaliKelas'),
                    'icon' => 'fas fa-map',
                    'category' => 'Penilaian',
                    'description' => 'Mapping wali kelas untuk rapor',
                ];
            }
        }

        // Menu umum untuk semua user
        $menus[] = [
            'title' => 'Profil',
            'url' => base_url('backend/pages/profil'),
            'icon' => 'fas fa-user',
            'category' => 'Utama',
            'description' => 'Profil pengguna',
        ];
        $menus[] = [
            'title' => 'Help',
            'url' => base_url('backend/pages/help'),
            'icon' => 'fas fa-question-circle',
            'category' => 'Utama',
            'description' => 'Bantuan dan panduan',
        ];
        $menus[] = [
            'title' => 'Contact',
            'url' => base_url('backend/pages/contact'),
            'icon' => 'fas fa-envelope',
            'category' => 'Utama',
            'description' => 'Kontak dan informasi',
        ];

        return $menus;
    }
}

