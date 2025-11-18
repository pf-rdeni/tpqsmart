<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pencarian Al-Qur'an</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pencarian Al-Qur'an</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cari Kata dalam Al-Qur'an</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= base_url('backend/quran/search') ?>" class="mb-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Kata Kunci</label>
                                        <input type="text" name="keyword" class="form-control" 
                                               value="<?= esc($keyword ?? '') ?>" 
                                               placeholder="Masukkan kata yang ingin dicari dalam Al-Qur'an">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php if (isset($result) && $result['success']): ?>
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-check"></i> Hasil Pencarian</h5>
                                <p><strong>Kata Kunci:</strong> "<?= esc($keyword) ?>"</p>
                                <p><strong>Total Hasil:</strong> <?= esc($result['total_results'] ?? 0) ?> ayat ditemukan</p>
                                <?php if (isset($result['note'])): ?>
                                    <p class="mb-0"><small><i class="fas fa-info-circle"></i> <?= esc($result['note']) ?></small></p>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($result['matches'])): ?>
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h3 class="card-title text-white">Hasil Pencarian</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width: 100px;">Surah</th>
                                                        <th style="width: 80px;">Ayat</th>
                                                        <th>Ayat (Arab)</th>
                                                        <th style="width: 150px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($result['matches'] as $match): ?>
                                                        <tr>
                                                            <td>
                                                                <?= esc($match['surah']['name'] ?? '-') ?><br>
                                                                <small class="text-muted">
                                                                    (<?= esc($match['surah']['englishName'] ?? '-') ?>)
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <strong><?= esc($match['numberInSurah'] ?? '-') ?></strong>
                                                            </td>
                                                            <td style="font-size: 16px; text-align: right; direction: rtl; font-family: 'Amiri', 'Traditional Arabic', serif;">
                                                                <?= esc($match['text'] ?? '-') ?>
                                                            </td>
                                                            <td>
                                                                <a href="<?= base_url('backend/surah/' . ($match['surah']['number'] ?? '') . '/' . ($match['numberInSurah'] ?? '')) ?>" 
                                                                   class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> Tidak Ada Hasil</h5>
                                    <p>Tidak ada ayat yang ditemukan untuk kata kunci "<?= esc($keyword) ?>".</p>
                                </div>
                            <?php endif; ?>
                        <?php elseif (isset($result) && !$result['success']): ?>
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <p><?= esc($result['error'] ?? 'Terjadi kesalahan saat melakukan pencarian') ?></p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Informasi</h5>
                                <p>Silakan masukkan kata kunci untuk mencari dalam Al-Qur'an.</p>
                                <p><strong>Catatan:</strong> Pencarian dilakukan dalam teks Arab Al-Qur'an.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

