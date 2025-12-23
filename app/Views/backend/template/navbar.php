<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <!-- Menu items untuk desktop (tersembunyi di mobile) -->
        <li class="nav-item d-none d-md-inline-block">
            <a href=<?php echo base_url('/') ?> class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-md-inline-block">
            <a href=<?php echo base_url('backend/pages/profil') ?> class="nav-link">Profil</a>
        </li>
        <li class="nav-item d-none d-md-inline-block">
            <a href=<?php echo base_url('backend/pages/contact') ?> class="nav-link">Contact</a>
        </li>
        <!-- Dropdown menu untuk mobile (hanya muncul di mobile) -->
        <li class="nav-item dropdown d-md-none">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-left">
                <a href=<?php echo base_url('/') ?> class="dropdown-item">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                <a href=<?php echo base_url('backend/pages/profil') ?> class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profil
                </a>
                <a href=<?php echo base_url('backend/pages/contact') ?> class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> Contact
                </a>
            </div>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Role Switcher (hanya untuk user dengan multiple peran) -->
        <?php
        // Cek apakah user memiliki multiple peran
        $idGuru = session()->get('IdGuru');
        $idTpq = session()->get('IdTpq');
        $idTahunAjaran = session()->get('IdTahunAjaran');
        $idKelas = session()->get('IdKelas');
        $hasMultipleRoles = false;
        $allRoles = [];
        $activeRole = session()->get('active_role');
        $availableRoles = session()->get('available_roles', []);

        // Jika available_roles ada di session, gunakan itu
        if (!empty($availableRoles)) {
            $allRoles = $availableRoles;
            $hasMultipleRoles = count($allRoles) > 1;
        } else {
            // Jika tidak ada di session, cek manual berdasarkan IdGuru
            $roleCount = 0;
            $helpFunctionModel = new \App\Models\HelpFunctionModel();

            if (in_groups('Admin')) {
                $roleCount++;
            }

            // Cek Operator - jika IdGuru memiliki peran sebagai Operator
            if (in_groups('Operator') && !empty($idGuru)) {
                // Cek apakah IdGuru ini adalah Operator
                // Jika user adalah Operator dan memiliki IdGuru, tambahkan role operator
                $roleCount++;
                $allRoles[] = 'operator';
            }

            // Cek Kepala TPQ - jika IdGuru memiliki peran sebagai Kepala TPQ
            if (!empty($idGuru) && !empty($idTpq)) {
                try {
                    $strukturLembaga = $helpFunctionModel->getStrukturLembagaJabatan($idGuru, $idTpq);
                    foreach ($strukturLembaga as $jabatan) {
                        if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                            $roleCount++;
                            $allRoles[] = 'kepala_tpq';
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore error
                }
            }

            // Cek Guru Kelas/Wali Kelas - jika IdGuru memiliki peran sebagai Guru Kelas atau Wali Kelas
            // Jika IdGuru memiliki data di tbl_guru_kelas (apapun IdJabatannya), berarti memiliki peran Guru
            if (!empty($idGuru) && !empty($idTpq)) {
                $hasGuruKelas = false;

                // Cek 1: Apakah user memiliki group 'Guru'
                if (in_groups('Guru')) {
                    $hasGuruKelas = true;
                } else {
                    // Cek 2: Apakah IdGuru memiliki data di tbl_guru_kelas (apapun IdJabatannya)
                    // Jika ada data di tbl_guru_kelas, berarti memiliki peran Guru
                    try {
                        $guruKelasRows = $helpFunctionModel->getDataGuruKelas(
                            IdGuru: $idGuru,
                            IdTpq: $idTpq
                        );
                        // Jika ada data di tbl_guru_kelas, berarti memiliki peran Guru
                        // Tidak perlu mengecek IdJabatan spesifik, karena semua IdJabatan di tbl_guru_kelas adalah peran Guru
                        if (!empty($guruKelasRows) && count($guruKelasRows) > 0) {
                            $hasGuruKelas = true;
                        }
                    } catch (\Throwable $e) {
                        // Ignore error, tapi log untuk debugging
                        // log_message('error', 'Error checking guru kelas: ' . $e->getMessage());
                    }
                }

                if ($hasGuruKelas) {
                    // Pastikan 'guru' belum ada di array
                    if (!in_array('guru', $allRoles)) {
                        $roleCount++;
                        $allRoles[] = 'guru';
                    }
                }
            }

            $hasMultipleRoles = $roleCount > 1;
        }

        $roleLabels = [
            'operator' => 'Operator',
            'kepala_tpq' => 'Kepala TPQ',
            'wali_kelas' => 'Wali Kelas',
            'guru' => 'Guru Kelas'
        ];
        $activeRoleLabel = isset($roleLabels[$activeRole]) ? $roleLabels[$activeRole] : 'Peran';

        // Cek apakah sedang di halaman Munaqosah (tidak relevan untuk menampilkan menu peran)
        $currentUri = current_url(true);
        $uriString = uri_string();
        $request = \Config\Services::request();
        $dashboardParam = $request->getGet('dashboard');
        $isMunaqosahPage = (
            strpos($uriString, 'munaqosah') !== false ||
            strpos($currentUri->getPath(), 'munaqosah') !== false ||
            $dashboardParam === 'munaqosah'
        );
        ?>
        <?php if ($hasMultipleRoles && !in_groups('Admin') && !$isMunaqosahPage): ?>
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" data-toggle="dropdown" title="Ganti Peran">
                    <i class="fas fa-user-cog"></i>
                    <span class="d-none d-md-inline ml-1"><?= esc($activeRoleLabel) ?></span>
                    <i class="fas fa-angle-down ml-1"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-header">Pilih Peran</span>
                    <div class="dropdown-divider"></div>
                    <?php foreach ($allRoles as $role): ?>
                        <?php if (isset($roleLabels[$role])): ?>
                            <a href="#" class="dropdown-item switch-role-btn <?= $role === $activeRole ? 'active' : '' ?>" data-role="<?= esc($role) ?>">
                                <?php if ($role === $activeRole): ?>
                                    <i class="fas fa-check text-success"></i>
                                <?php else: ?>
                                    <i class="far fa-circle"></i>
                                <?php endif; ?>
                                <span class="ml-2"><?= esc($roleLabels[$role]) ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('backend/dashboard/select-role') ?>" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span class="ml-2">Kelola Peran</span>
                    </a>
                </div>
            </li>
        <?php endif; ?>
        <!-- Dashboard Selector Dropdown (hanya untuk Admin) -->
        <?php if (in_groups('Admin')): ?>
            <?php
            // Daftar dashboard yang tersedia
            $availableDashboards = [
                'semester' => [
                    'label' => 'Default',
                    'icon' => 'fas fa-book',
                    'url' => base_url('backend/dashboard/admin')
                ],
                'munaqosah' => [
                    'label' => 'Munaqosah',
                    'icon' => 'fas fa-graduation-cap',
                    'url' => base_url('backend/munaqosah/dashboard-munaqosah')
                ]
            ];

            // Tambahkan Sertifikasi dan MyAuth jika Admin
            if (in_groups('Admin')) {
                $availableDashboards['sertifikasi'] = [
                    'label' => 'Sertifikasi',
                    'icon' => 'fas fa-certificate',
                    'url' => base_url('backend/sertifikasi/dashboard-admin')
                ];
                $availableDashboards['myauth'] = [
                    'label' => 'MyAuth',
                    'icon' => 'fas fa-shield-alt',
                    'url' => base_url('backend/auth')
                ];
            }

            // Tentukan dashboard aktif berdasarkan URL atau localStorage (akan diupdate via JS)
            $currentUrl = current_url(true);
            $uriString = uri_string();
            $activeDashboard = 'semester'; // default

            if (strpos($uriString, 'munaqosah') !== false) {
                $activeDashboard = 'munaqosah';
            } elseif (strpos($uriString, 'sertifikasi') !== false) {
                $activeDashboard = 'sertifikasi';
            } elseif (strpos($uriString, 'auth') !== false && strpos($uriString, 'backend/auth') !== false) {
                $activeDashboard = 'myauth';
            }
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" data-toggle="dropdown" title="Ganti Dashboard" id="dashboardDropdownToggle">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="d-none d-md-inline ml-1" id="currentDashboardLabel">Dashboard</span>
                    <i class="fas fa-angle-down ml-1"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <span class="dropdown-header">Pilih Dashboard</span>
                    <div class="dropdown-divider"></div>
                    <?php foreach ($availableDashboards as $dashboardKey => $dashboard): ?>
                        <a href="#" class="dropdown-item switch-dashboard-btn <?= $dashboardKey === $activeDashboard ? 'active' : '' ?>"
                            data-dashboard="<?= esc($dashboardKey) ?>"
                            data-url="<?= esc($dashboard['url']) ?>">
                            <?php if ($dashboardKey === $activeDashboard): ?>
                                <i class="fas fa-check text-success"></i>
                            <?php else: ?>
                                <i class="far fa-circle"></i>
                            <?php endif; ?>
                            <i class="<?= esc($dashboard['icon']) ?> ml-2"></i>
                            <span class="ml-2"><?= esc($dashboard['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" id="btnPilihDashboard">
                        <i class="fas fa-tachometer-alt ml-2"></i>
                        <span class="ml-2">Buka Pilihan Dashboard</span>
                    </a>
                </div>
            </li>
        <?php endif; ?>
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline" action="<?= base_url('backend/search') ?>" method="GET">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" name="q" placeholder="Cari menu atau halaman..."
                            aria-label="Search" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>
        <!-- Theme Switcher -->
        <li class="nav-item">
            <a class="nav-link" href="#" id="themeToggle" role="button" title="Ubah Tema">
                <i class="fas fa-moon" id="themeIcon"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <!-- Customize AdminLTE -->
        <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" href="<?= base_url('backend/customize') ?>" role="button" title="Customize AdminLTE">
                <i class="fab fa-windows"></i>
            </a>
        </li>
    </ul>
</nav>

<!-- Custom CSS untuk optimasi navbar di mobile sesuai AdminLTE -->
<style>
    /* Dark mode styles untuk navbar - memastikan teks dan icon terlihat */
    .dark-mode .main-header.navbar.navbar-white.navbar-light {
        background-color: #343a40 !important;
        border-color: #495057 !important;
    }

    .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link {
        color: #ffffff !important;
    }

    .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link i {
        color: #ffffff !important;
    }

    .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link:hover {
        color: #f8f9fa !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link:hover i {
        color: #f8f9fa !important;
    }

    .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link:focus {
        color: #ffffff !important;
    }

    /* Dark mode untuk dropdown menu */
    .dark-mode .main-header.navbar .navbar-nav .dropdown-menu {
        background-color: #343a40 !important;
        border-color: #495057 !important;
    }

    .dark-mode .main-header.navbar .navbar-nav .dropdown-item {
        color: #ffffff !important;
    }

    .dark-mode .main-header.navbar .navbar-nav .dropdown-item:hover {
        background-color: #495057 !important;
        color: #ffffff !important;
    }

    .dark-mode .main-header.navbar .navbar-nav .dropdown-item i {
        color: #ffffff !important;
    }

    .dark-mode .main-header.navbar .navbar-nav .dropdown-header {
        color: #adb5bd !important;
    }

    /* Dark mode untuk navbar search block */
    .dark-mode .main-header.navbar .navbar-search-block {
        background-color: #343a40 !important;
        border-color: #495057 !important;
    }

    .dark-mode .main-header.navbar .navbar-search-block .form-control-navbar {
        background-color: #495057 !important;
        color: #ffffff !important;
        border-color: #6c757d !important;
    }

    .dark-mode .main-header.navbar .navbar-search-block .form-control-navbar::placeholder {
        color: #adb5bd !important;
    }

    .dark-mode .main-header.navbar .navbar-search-block .btn-navbar {
        background-color: #495057 !important;
        color: #ffffff !important;
        border-color: #6c757d !important;
    }

    /* Memastikan navbar selalu terlihat dan berfungsi dengan baik di semua ukuran layar */
    @media (max-width: 991.98px) {
        .main-header.navbar {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        /* Navbar nav menggunakan flex row untuk layout horizontal */
        .main-header.navbar>.navbar-nav {
            display: flex !important;
            flex-direction: row;
            align-items: center;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        /* Memastikan tombol hamburger menu selalu terlihat */
        .main-header.navbar .navbar-nav>li:first-child {
            display: block !important;
            flex-shrink: 0;
        }

        /* Optimasi spacing untuk item navbar di mobile */
        .main-header.navbar .navbar-nav .nav-item {
            flex-shrink: 0;
            margin: 0 0.125rem;
        }

        /* Sembunyikan text label di mobile untuk menghemat ruang */
        .main-header.navbar .navbar-nav .nav-link span.d-none.d-md-inline,
        .main-header.navbar .navbar-nav .nav-link span.d-md-inline {
            display: none !important;
        }

        /* Pastikan icon tetap terlihat */
        .main-header.navbar .navbar-nav .nav-link i {
            display: inline-block !important;
        }

        /* Dark mode di mobile - memastikan teks dan icon terlihat */
        .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link {
            color: #ffffff !important;
        }

        .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link i {
            color: #ffffff !important;
        }

        /* Styling untuk dropdown menu mobile */
        .main-header.navbar .navbar-nav .dropdown-menu {
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-top: 0.5rem;
            min-width: 200px;
            position: absolute !important;
            top: 100% !important;
            z-index: 9999 !important;
            display: none !important;
            background-color: #fff !important;
            border: 1px solid rgba(0, 0, 0, .15) !important;
            max-width: calc(100vw - 20px);
            white-space: nowrap;
        }

        /* Dark mode untuk dropdown menu di mobile */
        .dark-mode .main-header.navbar .navbar-nav .dropdown-menu {
            background-color: #343a40 !important;
            border-color: #495057 !important;
        }

        /* Dropdown menu di sebelah kanan (default untuk right navbar) */
        .main-header.navbar .navbar-nav.ml-auto .dropdown-menu,
        .main-header.navbar .navbar-nav .dropdown-menu-right {
            right: 0 !important;
            left: auto !important;
        }

        /* Dropdown menu di sebelah kiri (untuk left navbar seperti Home, Profil) */
        .main-header.navbar .navbar-nav:not(.ml-auto) .dropdown-menu-left,
        .main-header.navbar .navbar-nav .dropdown-menu-left {
            left: 0 !important;
            right: auto !important;
            margin-left: 0 !important;
            transform: none !important;
        }

        /* Pastikan dropdown menu muncul saat show */
        .main-header.navbar .navbar-nav .dropdown.show .dropdown-menu,
        .main-header.navbar .navbar-nav .dropdown-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Pastikan nav-item dropdown memiliki position relative */
        .main-header.navbar .navbar-nav .nav-item.dropdown {
            position: relative !important;
        }

        /* Pastikan dropdown toggle bisa diklik di mobile */
        .main-header.navbar .navbar-nav .nav-item.dropdown>.nav-link {
            cursor: pointer !important;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
            touch-action: manipulation;
            pointer-events: auto !important;
            user-select: none;
        }

        /* Pastikan dropdown menu tidak terpotong */
        .main-header.navbar .navbar-nav .nav-item.dropdown {
            overflow: visible !important;
        }

        .main-header.navbar .navbar-nav .dropdown-item {
            padding: 0.75rem 1rem;
            transition: background-color 0.2s ease;
        }

        .main-header.navbar .navbar-nav .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .main-header.navbar .navbar-nav .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Dark mode untuk dropdown item di mobile */
        .dark-mode .main-header.navbar .navbar-nav .dropdown-item {
            color: #ffffff !important;
        }

        .dark-mode .main-header.navbar .navbar-nav .dropdown-item:hover {
            background-color: #495057 !important;
            color: #ffffff !important;
        }

        .dark-mode .main-header.navbar .navbar-nav .dropdown-item i {
            color: #ffffff !important;
        }

        /* Pastikan navbar search block tidak overflow */
        .main-header.navbar .navbar-search-block {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            padding: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Dark mode untuk navbar search block di mobile */
        .dark-mode .main-header.navbar .navbar-search-block {
            background-color: #343a40 !important;
        }

        /* Pastikan navbar tidak terlalu tinggi */
        .main-header.navbar {
            min-height: 57px;
            max-height: 57px;
        }
    }

    /* Untuk layar sangat kecil, optimasi tambahan */
    @media (max-width: 576px) {

        /* Kurangi padding untuk menghemat ruang */
        .main-header.navbar .navbar-nav .nav-link {
            padding: 0.25rem 0.5rem;
        }

        /* Pastikan icon tidak terlalu besar */
        .main-header.navbar .navbar-nav .nav-link i {
            font-size: 0.9rem;
        }

        /* Dark mode di layar kecil - memastikan teks dan icon terlihat */
        .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link {
            color: #ffffff !important;
        }

        .dark-mode .main-header.navbar.navbar-white.navbar-light .nav-link i {
            color: #ffffff !important;
        }
    }

    /* Pastikan dropdown tidak terpotong */
    @media (max-width: 991.98px) {
        .main-header.navbar .navbar-nav .dropdown.show .dropdown-menu {
            display: block !important;
        }

        /* Pastikan dropdown menu tidak terpotong oleh overflow */
        .main-header.navbar {
            overflow: visible !important;
        }

        .main-header.navbar>.navbar-nav {
            overflow: visible !important;
        }

        /* Pastikan dropdown menu bisa keluar dari container */
        .main-header.navbar .navbar-nav .nav-item.dropdown {
            overflow: visible !important;
        }

        /* Pastikan dropdown menu kiri terlihat dengan baik */
        .main-header.navbar .navbar-nav:not(.ml-auto) .dropdown-menu-left {
            left: 0 !important;
            right: auto !important;
            margin-left: 0 !important;
            transform: none !important;
            min-width: 180px;
        }

        /* Pastikan dropdown menu tidak terpotong oleh viewport */
        .main-header.navbar .navbar-nav .dropdown-menu {
            max-width: calc(100vw - 20px);
        }
    }
</style>