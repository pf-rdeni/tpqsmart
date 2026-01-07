<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Kartu Info Juri -->
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie"></i> Info Juri
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>ID Juri</td>
                                <td>: <code><?= esc($juri_data['IdJuri']) ?></code></td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td>: <?= esc($juri_data['NamaJuri'] ?: $juri_data['UsernameJuri']) ?></td>
                            </tr>
                            <tr>
                                <td>Kegiatan</td>
                                <td>: <?= esc($juri_data['NamaLomba']) ?></td>
                            </tr>
                            <tr>
                                <td>Perlombaan</td>
                                <td>: <?= esc($juri_data['NamaCabang']) ?></td>
                            </tr>
                            <tr>
                                <td>Tipe/Kategori</td>
                                <td>: 
                                    <span class="badge badge-<?= (strcasecmp($juri_data['Tipe'] ?? '', 'Kelompok') === 0) ? 'info' : 'primary' ?>">
                                        <?= esc($juri_data['Tipe'] ?? '-') ?>
                                    </span> 
                                    / <?= esc($juri_data['Kategori'] ?? '-') ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Statistik Penilaian -->
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $total_peserta ?></h3>
                                <p>Total Peserta</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $peserta_sudah_dinilai ?></h3>
                                <p>Sudah Dinilai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $peserta_belum_dinilai ?></h3>
                                <p>Belum Dinilai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Cepat -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Menu Cepat</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?= base_url('backend/perlombaan/inputNilaiJuri') ?>" class="btn btn-lg btn-primary btn-block mb-3">
                                    <i class="fas fa-edit fa-2x"></i><br>
                                    Input Nilai
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= base_url('backend/perlombaan/dataNilaiJuri') ?>" class="btn btn-lg btn-info btn-block mb-3">
                                    <i class="fas fa-list fa-2x"></i><br>
                                    Lihat Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Penilaian Terkini -->
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Penilaian Terkini</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No Peserta</th>
                                    <th>Nama</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Ambil 5 registrasi terakhir yang dinilai
                                $recentScored = \Config\Database::connect()
                                    ->table('tbl_lomba_nilai n')
                                    ->select('n.registrasi_id, r.NoPeserta, r.TipePeserta, r.NamaKelompok, MAX(n.updated_at) as updated_at')
                                    ->join('tbl_lomba_registrasi r', 'r.id = n.registrasi_id')
                                    ->where('n.IdJuri', $juri_data['IdJuri'])
                                    ->groupBy('n.registrasi_id, r.NoPeserta, r.TipePeserta, r.NamaKelompok')
                                    ->orderBy('updated_at', 'DESC')
                                    ->limit(5)
                                    ->get()->getResultArray();
                                ?>
                                <?php if (empty($recentScored)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada penilaian</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentScored as $r): ?>
                                        <tr>
                                            <td><code><?= esc($r['NoPeserta']) ?></code></td>
                                            <td>
                                                <?php if ($r['TipePeserta'] === 'Kelompok'): ?>
                                                    <span class="badge badge-info">Tim</span>
                                                    <?= esc($r['NamaKelompok']) ?>
                                                <?php else: ?>
                                                    Peserta #<?= esc($r['NoPeserta']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><small><?= date('H:i', strtotime($r['updated_at'])) ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
