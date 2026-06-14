<?php $this->extend('/backend/template/template'); ?>
<?php $this->section('content'); ?>

<style>
/* ===== Lucky Draw Dashboard – Pemenang ===== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.ld-dashboard-pemenang {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    padding: 28px 24px;
}

/* Hero banner */
.ld-hero {
    background: linear-gradient(120deg, #a18cd1 0%, #fbc2eb 100%);
    border-radius: 20px;
    padding: 32px 36px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 12px 40px rgba(161, 140, 209, 0.4);
    position: relative;
    overflow: hidden;
}
.ld-hero::before {
    content: '🎁';
    position: absolute;
    right: 32px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 96px;
    opacity: 0.18;
}
.ld-hero-text h1 {
    font-size: 1.9rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 6px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.ld-hero-text p {
    color: rgba(255,255,255,0.85);
    margin: 0;
    font-size: 1rem;
}
.ld-hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Quick action buttons */
.ld-btn-quick {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 24px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}
.ld-btn-quick:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    text-decoration: none;
}
.ld-btn-input {
    background: #fff;
    color: #7c3aed;
}
.ld-btn-barang {
    background: rgba(255,255,255,0.22);
    color: #fff;
    border: 2px solid rgba(255,255,255,0.5);
    backdrop-filter: blur(6px);
}

/* Stat cards */
.ld-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}
.ld-stat-card {
    border-radius: 16px;
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 24px rgba(0,0,0,0.28);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.ld-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0,0,0,0.36);
}
.ld-stat-card .stat-icon {
    font-size: 2rem;
    opacity: 0.85;
}
.ld-stat-card .stat-value {
    font-size: 2.6rem;
    font-weight: 800;
    line-height: 1;
    color: #fff;
}
.ld-stat-card .stat-label {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.8);
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.ld-stat-card .stat-bg-icon {
    position: absolute;
    right: -12px;
    bottom: -12px;
    font-size: 5rem;
    opacity: 0.1;
}
/* Card colors */
.card-purple  { background: linear-gradient(135deg, #7c3aed, #a855f7); }
.card-gold    { background: linear-gradient(135deg, #d97706, #f59e0b); }
.card-green   { background: linear-gradient(135deg, #059669, #10b981); }
.card-red     { background: linear-gradient(135deg, #dc2626, #ef4444); }
.card-blue    { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }

/* Section title */
.ld-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: rgba(255,255,255,0.9);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ld-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.15);
}

/* Content panels */
.ld-panel {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 24px;
    backdrop-filter: blur(10px);
    margin-bottom: 24px;
}

/* Barang progress list */
.ld-barang-list { list-style: none; padding: 0; margin: 0; }
.ld-barang-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.ld-barang-item:last-child { border-bottom: none; }
.ld-barang-info { flex: 1; min-width: 0; }
.ld-barang-name {
    font-weight: 600;
    color: #fff;
    font-size: 0.92rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ld-barang-kategori {
    font-size: 0.77rem;
    color: rgba(255,255,255,0.5);
    margin-top: 2px;
}
.ld-barang-counter {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    min-width: 90px;
}
.ld-counter-badge {
    font-size: 0.82rem;
    font-weight: 700;
    padding: 3px 12px;
    border-radius: 20px;
}
.badge-full  { background: #dc2626; color: #fff; }
.badge-avail { background: #059669; color: #fff; }
.ld-progress {
    width: 90px;
    height: 6px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    overflow: hidden;
}
.ld-progress-bar {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, #a855f7, #7c3aed);
    transition: width 0.6s ease;
}

/* Recent pemenang table */
.ld-table {
    width: 100%;
    border-collapse: collapse;
    color: rgba(255,255,255,0.9);
    font-size: 0.88rem;
}
.ld-table th {
    padding: 10px 14px;
    text-align: left;
    font-weight: 600;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.06em;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.ld-table td {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    vertical-align: middle;
}
.ld-table tr:last-child td { border-bottom: none; }
.ld-table tr:hover td { background: rgba(255,255,255,0.04); }

.no-undian-badge {
    display: inline-block;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #fff;
    border-radius: 8px;
    padding: 3px 12px;
    font-weight: 700;
    font-size: 0.88rem;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
}
.status-taken  { background: rgba(5, 150, 105, 0.2); color: #10b981; }
.status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: rgba(255,255,255,0.4);
}
.empty-state i { font-size: 3rem; margin-bottom: 12px; opacity: 0.5; }

@media (max-width: 576px) {
    .ld-hero { padding: 22px 20px; }
    .ld-hero-text h1 { font-size: 1.4rem; }
    .ld-stats-grid { grid-template-columns: 1fr 1fr; }
    .ld-btn-quick { padding: 10px 18px; font-size: 0.85rem; }
}
</style>

<div class="ld-dashboard-pemenang">

    <!-- Hero Banner -->
    <div class="ld-hero">
        <div class="ld-hero-text">
            <h1>🎁 Lucky Draw – Input Pemenang</h1>
            <p>Selamat datang! Kelola data pemenang dan barang hadiah dari sini.</p>
        </div>
        <div class="ld-hero-actions">
            <a href="<?= base_url('backend/luckydraw/undian') ?>" class="ld-btn-quick ld-btn-input">
                <i class="fas fa-trophy"></i> Input Pemenang
            </a>
            <a href="<?= base_url('backend/luckydraw/barang') ?>" class="ld-btn-quick ld-btn-barang">
                <i class="fas fa-boxes"></i> Data Barang Hadiah
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="ld-stats-grid">
        <div class="ld-stat-card card-purple">
            <span class="stat-icon">📦</span>
            <span class="stat-value"><?= $totalBarang ?></span>
            <span class="stat-label">Total Jenis Barang</span>
            <i class="fas fa-boxes stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-blue">
            <span class="stat-icon">🎫</span>
            <span class="stat-value"><?= $totalSlotBarang ?></span>
            <span class="stat-label">Total Slot Hadiah</span>
            <i class="fas fa-ticket-alt stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-gold">
            <span class="stat-icon">🏆</span>
            <span class="stat-value"><?= $totalPemenang ?></span>
            <span class="stat-label">Total Pemenang</span>
            <i class="fas fa-trophy stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-green">
            <span class="stat-icon">✅</span>
            <span class="stat-value"><?= $totalSudahDiambil ?></span>
            <span class="stat-label">Hadiah Diambil</span>
            <i class="fas fa-check-circle stat-bg-icon"></i>
        </div>
        <div class="ld-stat-card card-red">
            <span class="stat-icon">⏳</span>
            <span class="stat-value"><?= $totalSisaSlot ?></span>
            <span class="stat-label">Sisa Slot Kosong</span>
            <i class="fas fa-hourglass-half stat-bg-icon"></i>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap;" class="ld-content-grid">

        <!-- Stok Barang Hadiah -->
        <div class="ld-panel">
            <div class="ld-section-title">
                <i class="fas fa-boxes" style="color:#a855f7;"></i>
                Stok Barang Hadiah
            </div>
            <?php if (empty($barang)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open d-block"></i>
                    <p>Belum ada barang terdaftar</p>
                </div>
            <?php else: ?>
                <ul class="ld-barang-list">
                    <?php foreach ($barang as $b): ?>
                        <?php
                            $terisi  = $b->jumlah - max(0, $b->sisa);
                            $persen  = $b->jumlah > 0 ? round(($terisi / $b->jumlah) * 100) : 0;
                            $isFull  = $b->sisa <= 0;
                        ?>
                        <li class="ld-barang-item">
                            <div class="ld-barang-info">
                                <div class="ld-barang-name"><?= esc($b->nama_barang) ?></div>
                                <div class="ld-barang-kategori">📂 <?= esc($b->kategori) ?></div>
                            </div>
                            <div class="ld-barang-counter">
                                <span class="ld-counter-badge <?= $isFull ? 'badge-full' : 'badge-avail' ?>">
                                    <?= $terisi ?>/<?= $b->jumlah ?>
                                    <?= $isFull ? '🔴' : '🟢' ?>
                                </span>
                                <div class="ld-progress">
                                    <div class="ld-progress-bar" style="width:<?= $persen ?>%;"></div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- 5 Pemenang Terbaru -->
        <div class="ld-panel">
            <div class="ld-section-title">
                <i class="fas fa-history" style="color:#f59e0b;"></i>
                5 Pemenang Terbaru
            </div>
            <?php if (empty($recentPemenang)): ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash d-block"></i>
                    <p>Belum ada pemenang tercatat</p>
                </div>
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
                                    <div style="font-size:0.76rem;color:rgba(255,255,255,0.45);"><?= esc($p->kategori) ?></div>
                                </td>
                                <td>
                                    <?php if ($p->status_diambil): ?>
                                        <span class="status-badge status-taken"><i class="fas fa-check-circle"></i> Diambil</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending"><i class="fas fa-clock"></i> Belum</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:16px;text-align:center;">
                    <a href="<?= base_url('backend/luckydraw/undian') ?>" 
                       style="color:#a855f7;font-size:0.85rem;font-weight:600;text-decoration:none;">
                        Lihat semua pemenang →
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- end grid -->

</div>

<script>
// Animate stat values on load
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
