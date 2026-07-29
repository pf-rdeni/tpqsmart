<!-- Navbar Ujian Minimalis (tpqsmart.simpedis.com style dalam AdminLTE) -->
<nav class="main-header navbar navbar-expand navbar-dark border-bottom-0 shadow-sm" style="margin-left: 0 !important; background-color: #0088cc !important; padding: 8px 16px;">
    <div class="container-fluid px-2 d-flex justify-content-between align-items-center">
        <!-- Brand / Sub-brand -->
        <a href="#" class="navbar-brand text-white font-weight-bold m-0 p-0 d-flex align-items-center">
            <span class="brand-text font-weight-bold" style="font-size: 1.1rem;">tpqsmart<span style="color:#facc15;">.</span>simpedis.com</span>
            <span class="text-white-50 mx-2 d-none d-md-inline" style="font-weight: 300;">|</span>
            <span class="text-white small fw-normal d-none d-lg-inline opacity-75">Forum Komunikasi Diniyah Takmiliyah (FKDT)</span>
        </a>

        <!-- Right Navbar Items -->
        <div class="d-flex align-items-center" style="gap: 14px;">
            <!-- Live Clock Top (Desktop Only) -->
            <div class="d-none d-lg-block">
                <span class="badge badge-dark font-monospace px-3 py-2" id="topLiveClock" style="font-size: 0.95rem; background-color: rgba(0,0,0,0.3) !important; border-radius: 6px; letter-spacing: 1px;">
                    00 : 00 : 00
                </span>
            </div>

            <!-- Dark Mode Toggle Button -->
            <div>
                <button type="button" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" id="btnToggleDarkUjian" title="Toggle Dark Mode" style="width: 34px; height: 34px; padding: 0;">
                    <i class="fas fa-moon"></i>
                </button>
            </div>

            <!-- User Info Dinamis (Clean & Minimal) -->
            <div class="d-none d-sm-flex align-items-center text-white small font-weight-bold">
                <?php
                // Ambil nama login dari berbagai sumber secara dinamis
                $displayName = '';
                if (function_exists('user') && user()) {
                    $currentUser = user();
                    if (in_groups('Santri')) {
                        $displayName = session()->get('NamaSantri') ?? '';
                        if (empty($displayName)) {
                            $db = \Config\Database::connect();
                            $s = $db->table('tbl_santri_baru')
                                ->select('NamaSantri')
                                ->where('IdSantri', session()->get('IdSantri'))
                                ->get()->getRowArray();
                            $displayName = $s['NamaSantri'] ?? ($currentUser->username ?? 'Santri');
                        }
                    } elseif (in_groups('Guru')) {
                        $db = \Config\Database::connect();
                        $g = $db->table('tbl_guru')
                            ->select('NamaGuru')
                            ->where('IdGuru', session()->get('IdGuru'))
                            ->get()->getRowArray();
                        $displayName = $g['NamaGuru'] ?? ($currentUser->username ?? 'Guru');
                    } else {
                        $displayName = $currentUser->username ?? 'Pengguna';
                    }
                }
                ?>
                <i class="fas fa-user-circle mr-2 opacity-75" style="font-size: 1.15rem;"></i>
                <span class="text-truncate" style="max-width: 180px;"><?= esc($displayName) ?></span>
            </div>

            <!-- Exit / Keluar Ujian -->
            <div>
                <?php
                if (in_groups('Santri')) {
                    $exitUrl = base_url('backend/ujian-mdta/santri');
                    $exitLabel = 'Keluar Ujian';
                } elseif (in_groups(['Guru', 'KepalaTpq', 'Operator', 'Admin'])) {
                    $exitUrl = base_url('backend/ujian-mdta/paket');
                    $exitLabel = 'Exit Preview';
                } else {
                    $exitUrl = base_url('backend/dashboard');
                    $exitLabel = 'Kembali';
                }
                ?>
                <a href="<?= $exitUrl ?>" class="btn btn-sm btn-light text-primary font-weight-bold px-3 shadow-sm rounded-lg d-inline-flex align-items-center">
                    <i class="fas fa-sign-out-alt mr-1"></i> <span><?= $exitLabel ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>
