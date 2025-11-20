<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href=<?php echo base_url('/') ?> class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href=<?php echo base_url('backend/pages/profil') ?> class="nav-link">Profil</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href=<?php echo base_url('backend/pages/help') ?> class="nav-link">Help</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href=<?php echo base_url('backend/pages/contact') ?> class="nav-link">Contact</a>
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
        <!-- Dashboard Selector Toggle (hanya untuk Admin dan Operator) -->
        <?php if (in_groups('Admin') || in_groups('Operator')): ?>
            <li class="nav-item">
                <a class="nav-link" href="#" id="btnPilihDashboard" role="button" title="Ganti Dashboard" style="cursor: pointer;">
                    <i class="fas fa-exchange-alt"></i>
                    <span class="d-none d-md-inline ml-1" id="currentDashboardLabel">Dashboard</span>
                </a>
            </li>
        <?php endif; ?>
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search"
                            aria-label="Search">
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
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-Light navbar-badge">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-Light navbar-badge">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">0 Notifications</span>
                <div class="dropdown-divider"></div>

            </div>
        </li>
        <!-- Theme Switcher -->
        <li class="nav-item">
            <a class="nav-link" href="#" id="themeToggle" role="button" title="Ubah Tema">
                <i class="fas fa-moon" id="themeIcon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <!-- Customize AdminLTE -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('backend/customize') ?>" role="button" title="Customize AdminLTE">
                <i class="fab fa-windows"></i>
            </a>
        </li>
    </ul>
</nav>