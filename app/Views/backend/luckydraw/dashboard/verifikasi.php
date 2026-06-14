<?php $this->extend('/backend/template/template'); ?>
<?php $this->section('content'); ?>

<style>
/* ===== Lucky Draw Dashboard – Verifikasi ===== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.ld-dashboard-verifikasi {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
    padding: 28px 24px;
}

/* Hero banner */
.ldv-hero {
    background: linear-gradient(120deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 32px 36px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 12px 40px rgba(17, 153, 142, 0.4);
    position: relative;
    overflow: hidden;
}
.ldv-hero::before {
    content: '✅';
    position: absolute;
    right: 32px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 96px;
    opacity: 0.18;
}
.ldv-hero-text h1 {
    font-size: 1.9rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 6px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.ldv-hero-text p {
    color: rgba(255,255,255,0.85);
    margin: 0;
    font-size: 1rem;
}

.ldv-btn-verif {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    background: #fff;
    color: #11998e;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    white-space: nowrap;
}
.ldv-btn-verif:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.3);
    color: #0d7a72;
    text-decoration: none;
}

/* Stat cards */
.ldv-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}
.ldv-stat-card {
    border-radius: 16px;
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 24px rgba(0,0,0,0.28);
    transition: transform 0.3s ease;
}
.ldv-stat-card:hover { transform: translateY(-4px); }
.ldv-stat-card .stat-icon { font-size: 2rem; }
.ldv-stat-card .stat-value {
    font-size: 2.8rem;
    font-weight: 800;
    line-height: 1;
    color: #fff;
}
.ldv-stat-card .stat-label {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.75);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.ldv-stat-card .stat-bg-icon {
    position: absolute;
    right: -12px;
    bottom: -12px;
    font-size: 5rem;
    opacity: 0.1;
}
.card-teal    { background: linear-gradient(135deg, #11998e, #38ef7d); color: #003d38; }
.card-teal .stat-value, .card-teal .stat-label { color: rgba(255,255,255,0.95); }
.card-green   { background: linear-gradient(135deg, #059669, #10b981); }
.card-orange  { background: linear-gradient(135deg, #d97706, #f59e0b); }
.card-slate   { background: linear-gradient(135deg, #334155, #475569); }

/* Progress ring */
.ldv-progress-section {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
}
.ldv-ring-wrap {
    position: relative;
    width: 160px;
    height: 160px;
    flex-shrink: 0;
}
.ldv-ring-wrap svg { transform: rotate(-90deg); }
.ldv-ring-bg  { fill: none; stroke: rgba(255,255,255,0.1); stroke-width: 12; }
.ldv-ring-fg  {
    fill: none;
    stroke: #38ef7d;
    stroke-width: 12;
    stroke-linecap: round;
    transition: stroke-dashoffset 1s ease;
}
.ldv-ring-label {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.ldv-ring-pct  { font-size: 2.2rem; font-weight: 800; color: #fff; line-height: 1; }
.ldv-ring-sub  { font-size: 0.78rem; color: rgba(255,255,255,0.55); font-weight: 500; }
.ldv-legend    { display: flex; flex-direction: column; gap: 10px; }
.ldv-legend-item { display: flex; align-items: center; gap: 12px; }
.ldv-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.ldv-dot-green  { background: #38ef7d; }
.ldv-dot-orange { background: #f59e0b; }
.ldv-dot-gray   { background: rgba(255,255,255,0.2); }
.ldv-legend-text { color: rgba(255,255,255,0.8); font-size: 0.88rem; }
.ldv-legend-val  { font-weight: 700; color: #fff; }

/* Panel */
.ldv-panel {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 24px;
    backdrop-filter: blur(10px);
    margin-bottom: 24px;
}
.ldv-section-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: rgba(255,255,255,0.9);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ldv-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.1);
}

/* Antrean table */
.ldv-table {
    width: 100%;
    border-collapse: collapse;
    color: rgba(255,255,255,0.9);
    font-size: 0.88rem;
}
.ldv-table th {
    padding: 10px 14px;
    text-align: left;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase;
    font-size: 0.74rem;
    letter-spacing: 0.06em;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.ldv-table td {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    vertical-align: middle;
}
.ldv-table tr:last-child td { border-bottom: none; }
.ldv-table tr:hover td { background: rgba(255,255,255,0.04); }

.no-undian-badge {
    display: inline-block;
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: #fff;
    border-radius: 8px;
    padding: 3px 12px;
    font-weight: 700;
    font-size: 0.88rem;
}
.antrean-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
    font-size: 0.78rem;
    font-weight: 600;
}
.empty-state {
    text-align: center;
    padding: 48px 20px;
    color: rgba(255,255,255,0.4);
}
.empty-state i { font-size: 3.5rem; margin-bottom: 14px; opacity: 0.4; }
.empty-state p { font-size: 1rem; margin: 0; }

@media (max-width: 768px) {
    .ldv-hero { padding: 22px 18px; }
    .ldv-hero-text h1 { font-size: 1.35rem; }
    .ldv-stats-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<div class="ld-dashboard-verifikasi">

    <!-- Hero Banner -->
    <div class="ldv-hero">
        <div class="ldv-hero-text">
            <h1>✅ Lucky Draw – Verifikasi</h1>
            <p>Pantau progress serah terima hadiah dan verifikasi pemenang di sini.</p>
        </div>
        <a href="<?= base_url('backend/luckydraw/undian/verifikasi') ?>" class="ldv-btn-verif">
            <i class="fas fa-check-double"></i> Buka Verifikasi
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="ldv-stats-grid">
        <div class="ldv-stat-card card-teal">
            <span class="stat-icon">🏆</span>
            <span class="stat-value"><?= $totalPemenang ?></span>
            <span class="stat-label">Total Pemenang</span>
            <i class="fas fa-trophy stat-bg-icon"></i>
        </div>
        <div class="ldv-stat-card card-green">
            <span class="stat-icon">✅</span>
            <span class="stat-value"><?= $totalSudahDiambil ?></span>
            <span class="stat-label">Sudah Diambil</span>
            <i class="fas fa-check-double stat-bg-icon"></i>
        </div>
        <div class="ldv-stat-card card-orange">
            <span class="stat-icon">⏳</span>
            <span class="stat-value"><?= $totalBelumDiambil ?></span>
            <span class="stat-label">Belum Diambil</span>
            <i class="fas fa-hourglass-half stat-bg-icon"></i>
        </div>
        <div class="ldv-stat-card card-slate">
            <span class="stat-icon">📊</span>
            <span class="stat-value"><?= $persenSelesai ?>%</span>
            <span class="stat-label">Progress Selesai</span>
            <i class="fas fa-chart-pie stat-bg-icon"></i>
        </div>
    </div>

    <!-- Progress Ring Panel -->
    <div class="ldv-panel" style="margin-bottom:24px;">
        <div class="ldv-section-title">
            <i class="fas fa-chart-pie" style="color:#38ef7d;"></i>
            Progress Serah Terima Hadiah
        </div>
        <div class="ldv-progress-section">
            <div class="ldv-ring-wrap">
                <?php
                    $circumference = 2 * M_PI * 60;
                    $offset = $circumference * (1 - ($persenSelesai / 100));
                ?>
                <svg width="160" height="160" viewBox="0 0 160 160">
                    <circle class="ldv-ring-bg" cx="80" cy="80" r="60"/>
                    <circle class="ldv-ring-fg" cx="80" cy="80" r="60"
                        stroke-dasharray="<?= $circumference ?>"
                        stroke-dashoffset="<?= $offset ?>"
                        id="progressRing"/>
                </svg>
                <div class="ldv-ring-label">
                    <span class="ldv-ring-pct" id="ringPct">0%</span>
                    <span class="ldv-ring-sub">Selesai</span>
                </div>
            </div>
            <div class="ldv-legend">
                <div class="ldv-legend-item">
                    <span class="ldv-dot ldv-dot-green"></span>
                    <span class="ldv-legend-text">Sudah Diambil: <span class="ldv-legend-val"><?= $totalSudahDiambil ?></span></span>
                </div>
                <div class="ldv-legend-item">
                    <span class="ldv-dot ldv-dot-orange"></span>
                    <span class="ldv-legend-text">Belum Diambil: <span class="ldv-legend-val"><?= $totalBelumDiambil ?></span></span>
                </div>
                <div class="ldv-legend-item">
                    <span class="ldv-dot ldv-dot-gray"></span>
                    <span class="ldv-legend-text">Total Pemenang: <span class="ldv-legend-val"><?= $totalPemenang ?></span></span>
                </div>
                <div style="margin-top:12px;">
                    <a href="<?= base_url('backend/luckydraw/undian/verifikasi') ?>"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff;border-radius:50px;font-weight:700;text-decoration:none;font-size:0.9rem;transition:all .3s ease;"
                       onmouseover="this.style.transform='translateY(-2px)'" 
                       onmouseout="this.style.transform='none'">
                        <i class="fas fa-check-double"></i> Mulai Verifikasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Antrean Belum Diambil -->
    <div class="ldv-panel">
        <div class="ldv-section-title">
            <i class="fas fa-list-ul" style="color:#f59e0b;"></i>
            Antrean Belum Diambil (<?= count($antrean) ?>)
        </div>
        <?php if (empty($antrean)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle d-block" style="color:#38ef7d;"></i>
                <p>🎉 Semua hadiah sudah diambil!</p>
            </div>
        <?php else: ?>
            <table class="ldv-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Undian</th>
                        <th>Barang Hadiah</th>
                        <th>Kategori</th>
                        <th>Dicatat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($antrean as $idx => $p): ?>
                        <tr>
                            <td><span class="antrean-number"><?= $idx + 1 ?></span></td>
                            <td><span class="no-undian-badge"><?= esc($p->no_undian) ?></span></td>
                            <td style="font-weight:600;color:#fff;"><?= esc($p->nama_barang) ?></td>
                            <td style="color:rgba(255,255,255,0.55);font-size:0.82rem;"><?= esc($p->kategori) ?></td>
                            <td style="color:rgba(255,255,255,0.45);font-size:0.8rem;">
                                <?= !empty($p->created_at) ? date('d/m/Y H:i', strtotime($p->created_at)) : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:18px;text-align:center;">
                <a href="<?= base_url('backend/luckydraw/undian/verifikasi') ?>"
                   style="color:#38ef7d;font-weight:600;font-size:0.88rem;text-decoration:none;">
                    Buka halaman verifikasi lengkap →
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat values
    document.querySelectorAll('.stat-value').forEach(el => {
        const raw = el.textContent.trim();
        const isPct = raw.includes('%');
        const target = parseInt(raw);
        if (isNaN(target) || target === 0) return;
        let current = 0;
        const step = Math.max(1, Math.ceil(target / 30));
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current + (isPct ? '%' : '');
            if (current >= target) clearInterval(timer);
        }, 30);
    });

    // Animate ring label
    const pct = <?= $persenSelesai ?>;
    const ringEl = document.getElementById('ringPct');
    if (ringEl) {
        let cur = 0;
        const step = Math.max(1, Math.ceil(pct / 40));
        const t = setInterval(() => {
            cur = Math.min(cur + step, pct);
            ringEl.textContent = cur + '%';
            if (cur >= pct) clearInterval(t);
        }, 25);
    }
});
</script>

<?php $this->endSection(); ?>
