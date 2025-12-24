<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href=<?php echo base_url('/') ?> class="brand-link">
        <?php
        // Ambil logo lembaga dari database
        $logoUrl = base_url('/template/backend/dist/img/AdminLTELogo.png'); // Default logo
        $namaTpq = 'TPQ'; // Default nama

        $idTpq = session()->get('IdTpq');
        if (!empty($idTpq)) {
            $tpqModel = new \App\Models\TpqModel();
            $tpqData = $tpqModel->GetData($idTpq);

            if (!empty($tpqData) && !empty($tpqData[0]['LogoLembaga'])) {
                // Gunakan logo lembaga jika ada
                $logoUrl = base_url('uploads/logo/' . $tpqData[0]['LogoLembaga']);
            }

            // Ambil nama TPQ jika ada
            if (!empty($tpqData) && !empty($tpqData[0]['NamaTpq'])) {
                $namaTpq = $tpqData[0]['NamaTpq'];
            }
        }
        ?>
        <img src="<?= $logoUrl ?>" alt="Logo Lembaga"
            class="brand-image img-circle elevation-3"
            style="opacity: .8; object-fit: cover;"
            onerror="this.src='<?= base_url('/template/backend/dist/img/AdminLTELogo.png') ?>'">
        <span class="brand-text font-weight-light"><?= esc($namaTpq) ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php
                // Ambil foto profil user yang sedang login
                $userImage = null;
                $hasValidImage = false;

                if (function_exists('user') && user()) {
                    $userModel = new \App\Models\UserModel();
                    $userId = null;
                    if (function_exists('user_id')) {
                        $userId = user_id();
                    } else {
                        $userId = user()->id ?? null;
                    }
                    if ($userId) {
                        $userData = $userModel->getUser($userId);
                        $userImage = $userData['user_image'] ?? null;

                        // Cek apakah file benar-benar ada dan bukan default.svg
                        if (!empty($userImage) && $userImage !== 'default.svg') {
                            $imagePath = FCPATH . 'uploads/profil/user/' . $userImage;
                            if (file_exists($imagePath)) {
                                $hasValidImage = true;
                            }
                        }
                    }
                }
                ?>
                <?php if ($hasValidImage): ?>
                    <img src="<?= base_url('uploads/profil/user/' . $userImage) ?>"
                        class="img-circle elevation-2" alt="User Image"
                        style="width: 40px; height: 40px; object-fit: cover;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <i class="fas fa-user-circle elevation-2"
                        style="display: none; font-size: 40px; color: #6c757d; width: 40px; height: 40px; line-height: 40px; text-align: center;"></i>
                <?php else: ?>
                    <i class="fas fa-user-circle elevation-2"
                        style="font-size: 40px; color: #6c757d; width: 40px; height: 40px; line-height: 40px; text-align: center;"></i>
                <?php endif; ?>
            </div>
            <div class="info">
                <?php if (in_groups('Juri')): ?>
                    <a href=<?php echo base_url('backend/munaqosah/input-nilai-juri') ?> class="d-block"><?= user()->username; ?></a>
                <?php elseif (in_groups('Panitia')): ?>
                    <a href=<?php echo base_url('backend/munaqosah/dashboard-munaqosah') ?> class="d-block"><?= user()->username; ?></a>
                <?php else: ?>
                    <a href=<?php echo base_url('backend/pages/profil') ?> class="d-block"><?= user()->fullname; ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        // Ambil active_role dari session untuk menentukan menu yang ditampilkan
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

        // Cek apakah sedang di halaman MyAuth (untuk menyembunyikan menu lain)
        $isMyAuthPage = (
            strpos($uriString, 'backend/auth') !== false ||
            strpos($currentUri->getPath(), 'backend/auth') !== false ||
            $dashboardParam === 'myauth'
        );

        // Cek apakah sedang di halaman Sertifikasi
        $isSertifikasiPage = (
            strpos($uriString, 'sertifikasi') !== false ||
            strpos($currentUri->getPath(), 'sertifikasi') !== false ||
            $dashboardParam === 'sertifikasi'
        );

        // Cek apakah user memiliki peran operator
        $hasOperatorRole = ($activeRole === 'operator' || (empty($activeRole) && in_groups('Operator')));

        // Untuk Operator: menu default (Kelembagaan, Guru, Santri, dll) dan menu Munaqosah selalu muncul bersamaan
        // Dashboard utama adalah Default, tapi menu Munaqosah juga tersedia di sidebar
        $isActiveOperator = $hasOperatorRole && !$isMyAuthPage && !$isSertifikasiPage;

        // Cek apakah user memiliki peran guru (dari active_role atau available_roles)
        // Peran guru bisa dari: 'guru', 'wali_kelas', atau 'kepala_tpq' (kepala TPQ juga memiliki akses menu guru)
        $hasGuruRole = in_array('guru', $availableRoles) || in_groups('Guru');
        $isActiveGuru = (
            $activeRole === 'guru' ||
            $activeRole === 'wali_kelas' ||
            $activeRole === 'kepala_tpq' ||
            (empty($activeRole) && $hasGuruRole)
        );

        $isActiveKepalaTpq = ($activeRole === 'kepala_tpq');
        ?>
        <?php if ($isActiveGuru || $isActiveOperator): ?>
            <?php
            // Cek dan set tahun ajaran jika belum ada di session
            $helpFunctionModel = new \App\Models\HelpFunctionModel();
            $idTahunAjaranList = session()->get('IdTahunAjaranList');
            $idTahunAjaran = session()->get('IdTahunAjaran');
            $tahunAjaranSaatIni = $helpFunctionModel->getTahunAjaranSaatIni();

            // Jika IdTahunAjaranList null atau kosong, set ke tahun ajaran saat ini
            if (empty($idTahunAjaranList) || !is_array($idTahunAjaranList)) {
                $idTahunAjaranList = [$tahunAjaranSaatIni];
                session()->set('IdTahunAjaranList', $idTahunAjaranList);
            }

            // Jika IdTahunAjaran null, set ke tahun ajaran saat ini
            if ($idTahunAjaran == null) {
                $idTahunAjaran = $tahunAjaranSaatIni;
                session()->set('IdTahunAjaran', $idTahunAjaran);
            }

            // Pastikan tahun ajaran saat ini ada di list
            if (!in_array($tahunAjaranSaatIni, $idTahunAjaranList)) {
                $idTahunAjaranList[] = $tahunAjaranSaatIni;
                session()->set('IdTahunAjaranList', $idTahunAjaranList);
            }
            ?>
            <div class="info">
                <select class="form-control" id="tahunAjaranSelect">
                    <?php foreach ($idTahunAjaranList as $ta): ?>
                        <option value="<?= $ta; ?>" <?= ($ta == $idTahunAjaran) ? 'selected' : ''; ?>>
                            <?= convertTahunAjaran($ta); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href=<?php echo base_url('/') ?> class="nav-link">
                        <i class="nav-icon 	fas fa-tachometer-alt"></i>
                        <p> Dashboard</p>
                    </a>
                </li>
                <!-- Jadwal Sholat & Al-Qur'an -->
                <?php if (in_groups('Admin') && !$isMyAuthPage && !$isSertifikasiPage && !$isMunaqosahPage): ?>
                    <li class="nav-item no-hover">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-mosque"></i>
                            <p>
                                Sholat & Al-Qur'an
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/jadwal-sholat') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Jadwal Sholat</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/surah') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Surah Al-Qur'an</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/ayah') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cari Ayat</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/quran/search') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pencarian Al-Qur'an</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ((in_groups('JuriSertifikasi') || in_groups('Admin') || in_groups('PanitiaSertifikasi')) && $isSertifikasiPage): ?>
                    <!-- Sertifikasi -->
                    <li class="nav-item no-hover">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>
                                Sertifikasi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <?php if (in_groups('JuriSertifikasi')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/dashboard') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/inputNilaiSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input Nilai Sertifikasi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/nilaiPesertaSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Nilai Peserta</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_groups('PanitiaSertifikasi')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/dashboardPanitiaSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/listPesertaSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Peserta Sertifikasi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/listNilaiSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Nilai Sertifikasi</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_groups('Admin')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/listPesertaSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Peserta Sertifikasi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/listNilaiSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Nilai Sertifikasi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/sertifikasi/listJuriSertifikasi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Data Juri Sertifikasi</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ((in_groups('Admin') || in_groups('Juri') || in_groups('Panitia') || $hasOperatorRole) && ($isMunaqosahPage || $hasOperatorRole)): ?>
                    <!-- Munaqosah -->
                    <li class="nav-item no-hover">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>
                                Munaqosah
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <?php if (in_groups('Juri')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/dashboard-munaqosah') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard Munaqosah</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/input-nilai-juri') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input Nilai Juri</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/data-nilai-juri') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Data Nilai Juri</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/monitoring-antrian-peserta-ruangan-juri') ?> class="nav-link" target="_blank">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Antrian Peserta</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/dashboard-monitoring') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard Monitoring</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_groups('Admin')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/dashboard-munaqosah') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard Munaqosah</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/kategori-materi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Kategori Materi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/list-kategori-kesalahan') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Kategori Kesalahan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/grup-materi-ujian') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Grup Materi Ujian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/materi') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Materi Ujian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/bobot') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Bobot Nilai</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/jadwal-peserta-ujian') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Jadwal Peserta Ujian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/export-hasil-munaqosah') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Export Hasil Munaqosah</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasOperatorRole): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/dashboard-munaqosah') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard Munaqosah</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/jadwal-peserta-ujian?type=pra-munaqosah') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Jadwal Peserta Ujian</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_groups('Admin') || $hasOperatorRole): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/monitoring') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monitoring Munaqosah</p>
                                    </a>
                                </li>
                                <?php if (in_groups('Admin')): ?>
                                    <li class="nav-item">
                                        <a href=<?php echo base_url('backend/munaqosah/monitoring-penilaian-juri-pasangan') ?> class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Cek Nilai Pasangan Juri</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/kelulusan') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Nilai Munaqosah</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/kelulusan-simple') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Kelulusan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/list-konfigurasi-munaqosah') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Konfigurasi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/juri') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Data Juri dan Panitia</p>
                                    </a>
                                </li>
                                <?php if (!in_groups('Juri')): ?>
                                    <li class="nav-item">
                                        <a href=<?php echo base_url('backend/munaqosah/peserta') ?> class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Daftar Peserta</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (in_groups('Panitia') && !in_groups('Juri')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/dashboard-munaqosah') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard Munaqosah</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/peserta?type=munaqosah&tpq=0') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Daftar Peserta</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/registrasi-peserta?type=munaqosah&tpq=0') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Registrasi Peserta</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/jadwal-peserta-ujian?type=munaqosah&tpq=0') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Jadwal Peserta Ujian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/antrian?type=munaqosah&tpq=0') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Antrian Ujian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/dashboard-monitoring?type=munaqosah&tpq=0') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard Monitoring</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/monitoring?type=munaqosah&tpq=0') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Monitoring Munaqosah</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ((in_groups('Panitia') || in_groups('Admin') || $isActiveOperator) && !in_groups('Juri')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/registrasi-peserta') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Registrasi Peserta</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/munaqosah/antrian') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Antrian Ujian</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ((in_groups('Admin') && !$isMyAuthPage && !$isSertifikasiPage && !$isMunaqosahPage) || ($isActiveOperator)): ?>
                    <!--  Kelembagaan -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Kelembagaan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <?php if (in_groups('Admin')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/tpq/show') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> List TPQ</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/mda/show') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> List MDA</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/fkpq/show') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> List FKPQ</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/fkdt/show') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p> List FKDT</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($isActiveOperator): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/tpq/profilLembaga') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Profil Lembaga</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/strukturlembaga') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Struktur Lembaga</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/tpq/showSaranaLembaga') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sarana Lembaga</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <!--  Data Guru -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon 	fas fa-user"></i>
                            <p> Guru
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/guru/show') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Guru</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/guru/showSertifikasi') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sertifikasi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/guru/showAkunBank') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rekening Bank</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/guru/pengajuanInsentif') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pengajuan Insentif</p>
                                </a>
                            </li>
                            <?php if (in_groups('Admin') || $isActiveOperator): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/guruKelas/show') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Setting Guru Kelas</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <!--  Data Santri -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon 	fas fa-user-gear"></i>
                            <p>
                                Santri
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/createEmisStep') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Santri Baru</p>
                                </a>
                            </li>
                            <?php if (in_groups('Admin') || $isActiveOperator): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/kelas/showSantriKelasBaru') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Registrasi Santri Baru</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showProfilSantri') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Profil Santri</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/detailProfilSantri') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Detail Profil Santri</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showAturSantriBaru') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ubah Santri</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showSantriEmis') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Emis</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showSantriBaruPerkelasTpq') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Santri Per Kelas</p>
                                </a>
                            </li>
                            <?php if (in_groups('Admin') || $isActiveOperator): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/kelas/showListSantriPerKelas') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Kenaikan Kelas</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <!--  Raport-->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Raport
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="nav-item">
                                <a href="#" class="nav-link no-hover">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rapor Nilai</p>
                                    <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" style="display: none;">
                                    <li class="nav-item">
                                        <a href=<?php
                                                echo base_url('backend/rapor/index' . '/' . 'Ganjil') ?> class="nav-link">
                                            <i class="far fa-dot-circle nav-icon text-warning"></i>
                                            <p>Semester Ganjil</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href=<?php
                                                echo base_url('backend/rapor/index' . '/' . 'Genap') ?> class="nav-link">
                                            <i class="far fa-dot-circle nav-icon text-info"></i>
                                            <p>Semester Genap</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link no-hover">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Serah Terima</p>
                                    <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" style="display: none;">
                                    <li class="nav-item">
                                        <a href=<?php echo base_url('backend/rapor/serah-terima/Ganjil') ?> class="nav-link">
                                            <i class="far fa-dot-circle nav-icon text-warning"></i>
                                            <p>Semester Ganjil</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href=<?php echo base_url('backend/rapor/serah-terima/Genap') ?> class="nav-link">
                                            <i class="far fa-dot-circle nav-icon text-info"></i>
                                            <p>Semester Genap</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/rapor/kriteriaCatatanRapor') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kriteria Catatan</p>
                                </a>
                            </li>
                            <?php
                            // Cek apakah setting MappingWaliKelas aktif
                            // Menu ini muncul untuk:
                            // 1. Admin atau Operator (muncul jika setting aktif)
                            // 2. Guru/Wali Kelas (hanya muncul jika setting aktif DAN user adalah Wali Kelas)
                            $toolsModel = new \App\Models\ToolsModel();
                            $idTpq = session()->get('IdTpq');
                            $idGuru = session()->get('IdGuru');
                            $mappingEnabled = false;
                            $isWaliKelas = false;
                            $showMappingMenu = false;

                            // Cek apakah user adalah Admin atau Operator
                            $isAdmin = in_groups('Admin');
                            $isOperator = in_groups('Operator');

                            // Untuk Admin atau Operator, cek setting dan tampilkan menu jika aktif
                            if (($isAdmin || $isOperator) && !empty($idTpq)) {
                                $mappingEnabled = $toolsModel->getSetting($idTpq, 'MappingWaliKelas');
                                if ($mappingEnabled) {
                                    $showMappingMenu = true;
                                }
                            }
                            // Jika bukan Admin/Operator, cek apakah user adalah Wali Kelas
                            elseif (!empty($idGuru) && !empty($idTpq)) {
                                $mappingEnabled = $toolsModel->getSetting($idTpq, 'MappingWaliKelas');

                                if ($mappingEnabled) {
                                    $helpFunctionModel = new \App\Models\HelpFunctionModel();
                                    $idTahunAjaran = session()->get('IdTahunAjaran');
                                    if (empty($idTahunAjaran)) {
                                        $idTahunAjaran = $helpFunctionModel->getTahunAjaranSaatIni();
                                    }

                                    // Cek apakah user adalah Wali Kelas
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
                            ?>
                            <?php if ($showMappingMenu): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/rapor/settingMappingWaliKelas') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Mapping Wali Kelas</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>

                    </li>
                    <!-- Extra -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Extra
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/extra/showDownload') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Download</p>
                                </a>
                            </li>
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/extra/showArtikel') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Artikel</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/qr/index') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>QR</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (in_groups('Admin') && $isMyAuthPage): ?>
                    <!--  MyAuth Management -->
                    <li class="nav-item no-hover">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>
                                MyAuth
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/auth') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/auth/users') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen User</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/auth/groups') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Group</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/auth/permissions') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Permission</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/auth/loginAttempts') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Riwayat Login</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/auth/passwordResets') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Token Reset Password</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/auth/onlineUsers') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>User Online</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ((in_groups('Admin') && !$isMyAuthPage && !$isSertifikasiPage && !$isMunaqosahPage) || ($isActiveOperator)): ?>
                    <!--  General Setting -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>
                                Setting
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/materiPelajaran/showMateriPelajaran') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Materi</p>
                                </a>
                            </li>
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/kelasMateriPelajaran/showMateriKelas') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Materi Kelas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/user/index') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Akun</p>
                                </a>
                            </li>
                            <?php if (in_groups('Admin')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/user/authGroup') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Auth Group</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_groups('Admin') || $isActiveOperator): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/tools/index') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pengaturan Umum</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_groups('Admin') || $isActiveOperator): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/raporGroupKategori/index') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Nama Group</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_groups('Admin')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/nilai/resetNilaiIndex') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reset Nilai</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/logviewer') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Log Viewer</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/kelas/showCheckDuplikasiKelasSantri') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Normalisasi Data</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($isActiveGuru && !$isMyAuthPage && !$isSertifikasiPage && !$isMunaqosahPage): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Kesiswaan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/createEmisStep') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Input Data Santri</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showAturSantriBaru') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ubah Data Santri</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showProfilSantri') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Profil Data Santri</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/detailProfilSantri') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Detail Profil Santri</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Detail Profil -->
                    <li class="nav-item">
                        <a href=<?php echo base_url('backend/santri/detailProfilSantri') ?> class="nav-link">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p>Detail Profil</p>
                        </a>
                    </li>
                    <!-- Absensi -->
                    <li class="nav-item no-hover">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-signature"></i>
                            <p>
                                Absensi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/absensi/index') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kehadiran</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/absensi/ubahAbsensi') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ubah Absensi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/absensi/statistikKehadiran') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Statistik</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Keuangan -->
                    <li class="nav-item no-hover">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Keuangan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;">
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/tabungan/showPerkelas') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tabungan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/iuranBulanan/showPerKelas') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Iuran Bulanan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Penilaian -->
                    <?php if ($isActiveGuru || $isActiveOperator || in_groups('Operator')): ?>
                        <li class="nav-item no-hover">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fas fa-award"></i>
                                <p>
                                    Penilaian
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                <?php
                                $IdGuru = session()->get('IdGuru');
                                $encrypter = \Config\Services::encrypter();
                                $encryptedIdGuru = null;
                                if ($IdGuru)
                                    $encryptedIdGuru = bin2hex($encrypter->encrypt($IdGuru));; ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/prestasi/showPerKelas') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Prestasi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link no-hover">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Input Nilai
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href=<?php

                                                    echo base_url('backend/nilai/showSantriPerKelas' . '/' . 'Ganjil') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-warning"></i>
                                                <p>Semester Ganjil</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href=<?php

                                                    echo base_url('backend/nilai/showSantriPerKelas' . '/' . 'Genap') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-info"></i>
                                                <p>Semester Genap</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link no-hover">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Detail Nilai
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href=<?php

                                                    echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Ganjil') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-warning"></i>
                                                <p>Semester Ganjil</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href=<?php

                                                    echo base_url('backend/nilai/showDetailNilaiSantriPerKelas' . '/' . 'Genap') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-info"></i>
                                                <p>Semester Genap</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link no-hover">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Rangking Kelas
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href=<?php
                                                    echo base_url('backend/nilai/showRanking' . '/' . 'Ganjil') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-warning"></i>
                                                <p>Semester Ganjil</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href=<?php
                                                    echo base_url('backend/nilai/showRanking' . '/' . 'Genap') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-info"></i>
                                                <p>Semester Genap</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link no-hover">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Rapor Nilai</p>
                                        <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href=<?php
                                                    echo base_url('backend/rapor/index' . '/' . 'Ganjil') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-warning"></i>
                                                <p>Semester Ganjil</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href=<?php
                                                    echo base_url('backend/rapor/index' . '/' . 'Genap') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-info"></i>
                                                <p>Semester Genap</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link no-hover">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Serah Terima</p>
                                        <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: none;">
                                        <li class="nav-item">
                                            <a href=<?php echo base_url('backend/rapor/serah-terima/Ganjil') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-warning"></i>
                                                <p>Semester Ganjil</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href=<?php echo base_url('backend/rapor/serah-terima/Genap') ?> class="nav-link">
                                                <i class="far fa-dot-circle nav-icon text-info"></i>
                                                <p>Semester Genap</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/rapor/kriteriaCatatanRapor') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Kriteria Catatan</p>
                                    </a>
                                </li>
                                <?php
                                // Cek apakah setting MappingWaliKelas aktif untuk Guru
                                // Menu ini muncul hanya jika setting aktif DAN user adalah Wali Kelas
                                if (!isset($toolsModel)) {
                                    $toolsModel = new \App\Models\ToolsModel();
                                }
                                $idTpqGuru = session()->get('IdTpq');
                                $idGuruGuru = session()->get('IdGuru');
                                $mappingEnabledGuru = false;
                                $isWaliKelasGuru = false;
                                $showMappingMenuGuru = false;

                                if (!empty($idTpqGuru) && !empty($idGuruGuru)) {
                                    $mappingEnabledGuru = $toolsModel->getSetting($idTpqGuru, 'MappingWaliKelas');

                                    if ($mappingEnabledGuru) {
                                        if (!isset($helpFunctionModel)) {
                                            $helpFunctionModel = new \App\Models\HelpFunctionModel();
                                        }
                                        $idTahunAjaranGuru = session()->get('IdTahunAjaran');
                                        if (empty($idTahunAjaranGuru)) {
                                            $idTahunAjaranGuru = $helpFunctionModel->getTahunAjaranSaatIni();
                                        }

                                        // Cek apakah user adalah Wali Kelas
                                        $guruKelasDataGuru = $helpFunctionModel->getDataGuruKelas(
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
                                ?>
                                <?php if ($showMappingMenuGuru): ?>
                                    <li class="nav-item">
                                        <a href=<?php echo base_url('backend/rapor/settingMappingWaliKelas') ?> class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Mapping Wali Kelas</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (in_groups('Santri') && !$isSertifikasiPage && !$isMunaqosahPage): ?>
                    <!-- Kesantrian -->
                    <li class="nav-item no-hover">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Kesantrian
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: block;">
                            <li class="nav-item">
                                <a href="<?= base_url('backend/santri/detailProfilSantri') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Detail Profil</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('backend/absensi/showAbsensiSantri') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Detail Absensi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('backend/prestasi/showPrestasiSantri') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Detail Prestasi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('backend/nilai/showNilaiProfilDetail') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Detail Nilai</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('backend/tabungan/showTabunganSantri') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Detail Tabungan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('backend/santri/showKontakSantri') ?>" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kontak Santri</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- Logout -->
                <li class="nav-item">
                    <a href=<?php echo base_url('logout') ?> class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p> Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tahunAjaranSelect = document.getElementById('tahunAjaranSelect');

        if (tahunAjaranSelect) {
            tahunAjaranSelect.addEventListener('change', function() {
                const selectedTahunAjaran = this.value;

                // Tampilkan loading
                this.disabled = true;
                this.style.opacity = '0.6';

                // Kirim request ke server untuk update session
                fetch('<?= base_url('dashboard/updateTahunAjaranDanKelas') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            tahunAjaran: selectedTahunAjaran
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Tampilkan informasi kelas yang tersedia
                            if (data.kelasCount > 0) {
                                console.log('Tahun ajaran berhasil diubah. Tersedia ' + data.kelasCount + ' kelas.');
                                // Refresh halaman setelah session berhasil diupdate
                                window.location.reload();
                            } else {
                                alert('Tahun ajaran berhasil diubah, namun tidak ada kelas yang tersedia untuk tahun ajaran ini.');
                                window.location.reload();
                            }
                        } else {
                            // Jika gagal, kembalikan ke nilai sebelumnya
                            this.value = '<?= session()->get('IdTahunAjaran') ?? '' ?>';
                            alert('Gagal mengubah tahun ajaran: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Jika error, kembalikan ke nilai sebelumnya
                        this.value = '<?= session()->get('IdTahunAjaran') ?? '' ?>';
                        alert('Terjadi kesalahan saat mengubah tahun ajaran');
                    })
                    .finally(() => {
                        // Enable select kembali
                        this.disabled = false;
                        this.style.opacity = '1';
                    });
            });
        }
    });
</script>