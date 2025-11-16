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
            // Jika tidak ada di session, cek manual (simplified version)
            $roleCount = 0;
            if (in_groups('Admin')) {
                $roleCount++;
            }
            if (in_groups('Operator')) {
                $roleCount++;
                $allRoles[] = 'operator';
            }
            if (in_groups('Guru') && !empty($idGuru) && !empty($idTpq)) {
                $roleCount++;
                $allRoles[] = 'guru';
                
                // Cek Kepala TPQ
                try {
                    $helpFunctionModel = new \App\Models\HelpFunctionModel();
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

                // Cek Wali Kelas
                if (!empty($idKelas) && !empty($idTahunAjaran)) {
                    try {
                        $helpFunctionModel = new \App\Models\HelpFunctionModel();
                        $guruKelasRows = $helpFunctionModel->getDataGuruKelas(
                            IdGuru: $idGuru,
                            IdTpq: $idTpq,
                            IdKelas: $idKelas,
                            IdTahunAjaran: $idTahunAjaran
                        );
                        if (!empty($guruKelasRows)) {
                            foreach ($guruKelasRows as $row) {
                                if (isset($row->IdJabatan) && (int)$row->IdJabatan === 3) {
                                    $roleCount++;
                                    $allRoles[] = 'wali_kelas';
                                    break;
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        // Ignore error
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
        ?>
        <?php if ($hasMultipleRoles && !in_groups('Admin')): ?>
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
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('logout') ?>" role="button">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</nav>