<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url("Pages") ?>" class="brand-link">
        <img src="<?php echo base_url('/template/backend/dist') ?>/img/AdminLTELogo.png" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">TPQ</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo base_url('/template/backend/dist') ?>/img/user.svg"
                    class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= user()->fullname; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href=<?php echo base_url('backend/pages/index') ?> class="nav-link">
                        <i class="nav-icon 	fas fa-tachometer-alt"></i>
                        <p> Dashboard</p>
                    </a>
                </li>
                <?php if (in_groups('admin')): ?>
                    <li class="nav-item">
                        <a href=<?php echo base_url('backend/tpq/show') ?> class="nav-link">
                            <i class="nav-icon 	fas fa-mosque"></i>
                            <p> TPQ</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href=<?php echo base_url('backend/guru/show') ?> class="nav-link">
                            <i class="nav-icon 	fas fa-user"></i>
                            <p> Guru</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon 	fas fa-users"></i>
                            <p>
                                Santri
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/createEmisStep') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Santri</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showSantriBaru') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Santri</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview" style="display: none;"> <!-- none; or block -->
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showAturSantriBaru') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Atur Santri</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Setting
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="display: block;"> <!-- none; or block -->
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/kelas/showSantriKelasBaru') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kelas Baru</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/kelas/showListSantriPerKelas') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kenaikan Kelas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/guruKelas/show') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Guru Kelas</p>
                                </a>
                            </li>
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/materiPelajaran/showMateriPelajaran') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Materi</p>
                                </a>
                            </li>
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/materiPelajaran/showMateriPelajaran') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kurikulum</p>
                                </a>
                            </li>
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/kelasMateriPelajaran/showMateriKelas') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Materi Kelas</p>
                                </a>
                            </li>
                        </ul>
                    </li>

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
                                <a href=<?php echo base_url('backend/santri/showSantriPerKelas') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Santri Per Kelas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/nilai/showSumaryPersemester') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Nilai Per Semester</p>
                                </a>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/nilai/showNilaiProfilDetail' . '/' . '20150001') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Profil Detail</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/santri/showKontakSantri') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kontak Santri</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
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
                <!-- Kesantrian -->
                <li class="nav-item no-hover">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Kesantrian
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href=<?php echo base_url('backend/santri/showKontakSantri') ?> class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kontak Santri</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href=<?php echo base_url('backend/santri/showSantriPerKelas') ?> class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Santri</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Penilaian -->
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
                                <p>Prestasi Santri</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href=<?php

                                    echo base_url('backend/santri/showSantriPerKelas/' . $encryptedIdGuru) ?> class="nav-link no-hover">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nilai Per Semester</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>