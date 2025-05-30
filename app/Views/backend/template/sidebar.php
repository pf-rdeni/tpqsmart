<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href=<?php echo base_url('backend/pages/index') ?> class="brand-link">
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
                <a href=<?php echo base_url('backend/pages/index') ?> class="d-block"><?= user()->fullname; ?></a>
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
                <?php if (in_groups('Admin') || in_groups('Operator')): ?>
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
                            <?php endif; ?>
                            <?php if (in_groups('Operator')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/tpq/showProfilTpq') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Profil TPQ</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/tpq/showStrukturTpq') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Struktur TPQ</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/tpq/showSaranaPrasarana') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sarana Prasarana</p>
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
                            <?php if (in_groups('Operator')): ?>
                                <li class="nav-item">
                                    <a href=<?php echo base_url('backend/santri/showSantriBaruPerkelasTpq') ?> class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Santri Per Kelas</p>
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
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/raport/showPreviewNilai') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Preview Nilai</p>
                                </a>
                            </li>
                            <li class="=nav-item">
                                <a href=<?php echo base_url('backend/raport/showPrestasiSantri') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Prestasi Santri</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/raport/showCetakRaport') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cetak Raport</p>
                                </a>
                            </li>
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
                                <a href=<?php echo base_url('backend/extra/showTutorial') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tutorial</p>
                                </a>
                            </li>
                        </ul>
                    </li>
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
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/user/index') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Daftar Akun</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href=<?php echo base_url('backend/tools/index') ?> class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tools</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (in_groups('Guru')): ?>
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
                        </ul>
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
                                    <p>Rangking</p>
                                    <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" style="display: none;">
                                    <li class="nav-item">
                                        <a href=<?php

                                                echo base_url('backend/nilai/showSumaryPersemester' . '/' . 'Ganjil') ?> class="nav-link">
                                            <i class="far fa-dot-circle nav-icon text-warning"></i>
                                            <p>Semester Ganjil</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href=<?php

                                                echo base_url('backend/nilai/showSumaryPersemester' . '/' . 'Genap') ?> class="nav-link">
                                            <i class="far fa-dot-circle nav-icon text-info"></i>
                                            <p>Semester Genap</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (in_groups('Santri')): ?>
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