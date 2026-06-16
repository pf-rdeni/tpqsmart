<?php $this->extend('/backend/template/template'); ?>
<?php $this->section('content'); ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.ld-admin-dashboard {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    padding: 28px 24px;
}

/* Hero */
.ld-hero {
    background: linear-gradient(120deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 32px 36px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.45);
    position: relative;
    overflow: hidden;
}
.ld-hero::before {
    content: '🎰';
    position: absolute;
    right: 32px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 96px;
    opacity: 0.15;
}
.ld-hero-text h1 {
    font-size: 1.9rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 4px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.ld-hero-text .meta {
    color: rgba(255,255,255,0.8);
    font-size: 0.95rem;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 8px;
}
.ld-hero-text .meta span { display: flex; align-items: center; gap: 6px; }
.ld-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }

/* Quick Buttons */
.ld-btn-quick {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px; border-radius: 50px;
    font-weight: 600; font-size: 0.9rem;
    text-decoration: none; transition: all 0.3s ease;
    border: none; cursor: pointer; white-space: nowrap;
}
.ld-btn-quick:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.25); text-decoration: none; }
.btn-white   { background: #fff; color: #764ba2; }
.btn-glass   { background: rgba(255,255,255,0.2); color: #fff; border: 2px solid rgba(255,255,255,0.45); backdrop-filter: blur(6px); }
.btn-glass2  { background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.3); }

/* Stats */
.ld-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 18px;
    margin-bottom: 30px;
}
.ld-stat-card {
    border-radius: 16px; padding: 22px 18px;
    display: flex; flex-direction: column; gap: 6px;
    position: relative; overflow: hidden;
    box-shadow: 0 6px 24px rgba(0,0,0,0.28);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.ld-stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,0.36); }
.ld-stat-card .stat-value { font-size: 2.4rem; font-weight: 800; line-height: 1; color: #fff; }
.ld-stat-card .stat-label { font-size: 0.82rem; color: rgba(255,255,255,0.8); font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em; }
.ld-stat-card .stat-bg-icon { position: absolute; right: -10px; bottom: -12px; font-size: 4.5rem; opacity: 0.1; }
.card-purple  { background: linear-gradient(135deg, #7c3aed, #a855f7); }
.card-gold    { background: linear-gradient(135deg, #d97706, #f59e0b); }
.card-green   { background: linear-gradient(135deg, #059669, #10b981); }
.card-red     { background: linear-gradient(135deg, #dc2626, #ef4444); }
.card-blue    { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }
.card-cyan    { background: linear-gradient(135deg, #0891b2, #22d3ee); }

/* Admin action grid */
.admin-menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}
.admin-menu-card {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 22px 16px;
    text-align: center;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s ease;
    display: block;
    backdrop-filter: blur(10px);
}
.admin-menu-card:hover {
    background: rgba(255,255,255,0.16);
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.35);
    color: #fff;
    text-decoration: none;
}
.admin-menu-card .menu-icon { font-size: 2.2rem; margin-bottom: 10px; }
.admin-menu-card .menu-label { font-size: 0.88rem; font-weight: 600; }

/* Section */
.ld-section-title {
    font-size: 1.05rem; font-weight: 700; color: rgba(255,255,255,0.9);
    margin-bottom: 14px; display: flex; align-items: center; gap: 10px;
}
.ld-section-title::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.12); }

/* Panel */
.ld-panel {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px; padding: 22px;
    backdrop-filter: blur(10px);
    margin-bottom: 24px;
}

/* Barang list */
.ld-barang-item { display: flex; align-items: center; gap: 14px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.06); }
.ld-barang-item:last-child { border-bottom: none; }
.ld-barang-info { flex: 1; min-width: 0; }
.ld-barang-name { font-weight: 600; color: #fff; font-size: 0.9rem; }
.ld-barang-kategori { font-size: 0.75rem; color: rgba(255,255,255,0.45); margin-top: 2px; }
.ld-counter-badge { font-size: 0.8rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
.badge-full  { background: #dc2626; color: #fff; }
.badge-avail { background: #059669; color: #fff; }
.ld-progress { width: 80px; height: 6px; background: rgba(255,255,255,0.12); border-radius: 10px; overflow: hidden; }
.ld-progress-bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #a855f7, #7c3aed); transition: width 0.6s ease; }

/* Table */
.ld-table { width: 100%; border-collapse: collapse; color: rgba(255,255,255,0.9); font-size: 0.87rem; }
.ld-table th { padding: 10px 12px; text-align: left; font-weight: 600; color: rgba(255,255,255,0.5); text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.06em; border-bottom: 1px solid rgba(255,255,255,0.1); }
.ld-table td { padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
.ld-table tr:last-child td { border-bottom: none; }
.no-undian-badge { display: inline-block; background: linear-gradient(135deg, #7c3aed, #a855f7); color: #fff; border-radius: 8px; padding: 2px 10px; font-weight: 700; }
.status-taken  { background: rgba(5, 150, 105, 0.2); color: #10b981; display:inline-flex; align-items:center; gap:4px; padding: 3px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
.status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; display:inline-flex; align-items:center; gap:4px; padding: 3px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }

.empty-state { text-align:center; padding:30px 20px; color:rgba(255,255,255,0.4); }
.empty-state i { font-size:2.5rem; margin-bottom:10px; opacity:0.5; }

@media (max-width: 576px) {
    .ld-hero { padding: 20px; }
    .ld-hero-text h1 { font-size: 1.4rem; }
    .ld-stats-grid { grid-template-columns: 1fr 1fr; }
    .admin-menu-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="ld-admin-dashboard">

    <!-- Hero Banner -->
    <div class="ld-hero">
        <div class="ld-hero-text">
            <h1>🎰 Lucky Draw — Admin</h1>
            <?php if ($kegiatan): ?>
            <div class="meta">
                <span><i class="fas fa-star"></i> <strong><?= esc($kegiatan->nama_kegiatan) ?></strong></span>
                <span><i class="fas fa-calendar-alt"></i> <?= date('d F Y', strtotime($kegiatan->tanggal_kegiatan)) ?></span>
                <span><i class="fas fa-map-marker-alt"></i> <?= esc($kegiatan->tempat_pelaksanaan) ?></span>
            </div>
            <?php endif; ?>
        </div>
        <div class="ld-hero-actions">
            <a href="<?= base_url('backend/luckydraw/pilih') ?>" class="ld-btn-quick btn-glass2">
                <i class="fas fa-exchange-alt"></i> Ganti Kegiatan
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="ld-stats-grid">
        <div class="ld-stat-card card-purple">
            <span class="stat-value"><?= $totalBarang ?></span>
            <span class="stat-label">Total Jenis Barang</span>
            <i class="fas fa-boxes stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-blue">
            <span class="stat-value"><?= $totalSlotBarang ?></span>
            <span class="stat-label">Total Slot Hadiah</span>
            <i class="fas fa-ticket-alt stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-gold">
            <span class="stat-value"><?= $totalPemenang ?></span>
            <span class="stat-label">Total Pemenang</span>
            <i class="fas fa-trophy stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-green">
            <span class="stat-value"><?= $totalSudahDiambil ?></span>
            <span class="stat-label">Hadiah Diambil</span>
            <i class="fas fa-check-circle stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-red">
            <span class="stat-value"><?= $totalBelumDiambil ?></span>
            <span class="stat-label">Belum Diambil</span>
            <i class="fas fa-clock stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-cyan">
            <span class="stat-value"><?= $totalSisaSlot ?></span>
            <span class="stat-label">Sisa Slot Kosong</span>
            <i class="fas fa-hourglass-half stat-bg-icon"></i>
        </div>
    </div>

    <!-- Admin Quick Access Menu -->
    <div class="ld-section-title"><i class="fas fa-th-large" style="color:#a855f7;"></i> Akses Cepat</div>
    <div class="admin-menu-grid">
        <a href="<?= base_url('backend/luckydraw/barang') ?>" class="admin-menu-card">
            <div class="menu-icon">🎁</div>
            <div class="menu-label">Data Barang Hadiah</div>
        </a>
        <a href="<?= base_url('backend/luckydraw/undian') ?>" class="admin-menu-card">
            <div class="menu-icon">🏆</div>
            <div class="menu-label">Input Pemenang</div>
        </a>
        <a href="<?= base_url('backend/luckydraw/undian/verifikasi') ?>" class="admin-menu-card">
            <div class="menu-icon">✅</div>
            <div class="menu-label">Verifikasi Serah Terima</div>
        </a>
        <a href="<?= base_url('backend/luckydraw/undian/semua') ?>" class="admin-menu-card">
            <div class="menu-icon">📋</div>
            <div class="menu-label">Semua Pemenang</div>
        </a>
        <a href="<?= base_url('backend/luckydraw/kegiatan') ?>" class="admin-menu-card">
            <div class="menu-icon">📅</div>
            <div class="menu-label">Manajemen Kegiatan</div>
        </a>
        <a href="<?= base_url('backend/luckydraw/panitia') ?>" class="admin-menu-card">
            <div class="menu-icon">👥</div>
            <div class="menu-label">Manajemen Panitia</div>
        </a>
    </div>

    <?php
    $summary = [];
    foreach ((array) $pemenang as $p) {
        $key = $p->kategori . ' - ' . $p->nama_barang;
        if (!isset($summary[$key])) {
            $summary[$key] = ['diambil' => 0, 'belum' => 0];
        }
        if ($p->status_diambil == 1) {
            $summary[$key]['diambil']++;
        } else {
            $summary[$key]['belum']++;
        }
    }
    ?>

    <?php if(!empty($summary)): ?>
    <div class="ld-panel">
        <div class="ld-section-title">
            <i class="fas fa-chart-pie" style="color:#10b981;"></i> Ringkasan Pemenang per Hadiah
        </div>
        <div style="overflow-x:auto;">
            <table class="ld-table" style="text-align:center;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Kategori - Nama Barang</th>
                        <th style="text-align:center;"><i class="fas fa-check-circle" style="color:#10b981;"></i> Sudah Diambil</th>
                        <th style="text-align:center;"><i class="fas fa-clock" style="color:#f59e0b;"></i> Belum Diambil</th>
                        <th style="text-align:center;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalDiambil = 0; $totalBelum = 0; $totalSemua = 0;
                    foreach($summary as $kategori => $stat): 
                        $totalDiambil += $stat['diambil'];
                        $totalBelum += $stat['belum'];
                        $totalRow = $stat['diambil'] + $stat['belum'];
                        $totalSemua += $totalRow;
                    ?>
                    <tr>
                        <td style="text-align:left; font-weight:600; color:#fff;"><?= esc($kategori) ?></td>
                        <td><span class="status-taken" style="font-size:0.85rem;"><?= $stat['diambil'] ?></span></td>
                        <td><span class="status-pending" style="font-size:0.85rem;"><?= $stat['belum'] ?></span></td>
                        <td><span class="no-undian-badge" style="background:rgba(255,255,255,0.1); color:#fff;"><?= $totalRow ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot style="border-top:1px solid rgba(255,255,255,0.1); font-weight:700;">
                    <tr>
                        <td style="text-align:right; padding:15px 12px;">TOTAL</td>
                        <td style="color:#10b981; font-size:1.1rem;"><?= $totalDiambil ?></td>
                        <td style="color:#f59e0b; font-size:1.1rem;"><?= $totalBelum ?></td>
                        <td style="color:#fff; font-size:1.1rem;"><?= $totalSemua ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Content Grid: Barang & Pemenang Terbaru -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="ld-content-grid">

        <!-- Stok Barang -->
        <div class="ld-panel">
            <div class="ld-section-title">
                <i class="fas fa-boxes" style="color:#a855f7;"></i> Stok Barang Hadiah
            </div>
            <?php if (empty($barang)): ?>
                <div class="empty-state"><i class="fas fa-box-open d-block"></i><p>Belum ada barang terdaftar</p></div>
            <?php else: ?>
                <?php foreach ($barang as $b):
                    $terisi = $b->jumlah - max(0, $b->sisa);
                    $persen = $b->jumlah > 0 ? round(($terisi / $b->jumlah) * 100) : 0;
                    $isFull = $b->sisa <= 0;
                ?>
                <div class="ld-barang-item">
                    <div class="ld-barang-info">
                        <div class="ld-barang-name"><?= esc($b->nama_barang) ?></div>
                        <div class="ld-barang-kategori">📂 <?= esc($b->kategori) ?></div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                        <span class="ld-counter-badge <?= $isFull ? 'badge-full' : 'badge-avail' ?>">
                            <?= $terisi ?>/<?= $b->jumlah ?> <?= $isFull ? '🔴' : '🟢' ?>
                        </span>
                        <div class="ld-progress">
                            <div class="ld-progress-bar" style="width:<?= $persen ?>%;"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 5 Pemenang Terbaru -->
        <div class="ld-panel">
            <div class="ld-section-title">
                <i class="fas fa-history" style="color:#f59e0b;"></i> 5 Pemenang Terbaru
            </div>
            <?php if (empty($recentPemenang)): ?>
                <div class="empty-state"><i class="fas fa-user-slash d-block"></i><p>Belum ada pemenang tercatat</p></div>
            <?php else: ?>
                <table class="ld-table">
                    <thead>
                        <tr>
                            <th>No. Undian</th>
                            <th>Barang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPemenang as $p): ?>
                        <tr>
                            <td><span class="no-undian-badge"><?= esc($p->no_undian) ?></span></td>
                            <td>
                                <div style="font-weight:600;color:#fff;"><?= esc($p->nama_barang) ?></div>
                                <div style="font-size:0.75rem;color:rgba(255,255,255,0.45);"><?= esc($p->kategori) ?></div>
                            </td>
                            <td>
                                <?php if ($p->status_diambil): ?>
                                    <span class="status-taken"><i class="fas fa-check-circle"></i> Diambil</span>
                                <?php else: ?>
                                    <span class="status-pending"><i class="fas fa-clock"></i> Belum</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:14px;text-align:center;">
                    <a href="<?= base_url('backend/luckydraw/undian/semua') ?>"
                       style="color:#a855f7;font-size:0.85rem;font-weight:600;text-decoration:none;">
                        Lihat semua pemenang →
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- end grid -->

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(el => {
        const target = parseInt(el.textContent);
        if (isNaN(target) || target === 0) return;
        let current = 0;
        const step = Math.ceil(target / 30);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 30);
    });
});
</script>

<?php $this->endSection(); ?>
