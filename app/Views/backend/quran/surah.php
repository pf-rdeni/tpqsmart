<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Surah Al-Qur'an</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Surah Al-Qur'an</li>
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
                        <h3 class="card-title">Cari Surah</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?= base_url('backend/surah') ?>" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nomor Surah (1-114)</label>
                                        <input type="number" name="id" class="form-control" 
                                               value="<?= esc($surah_id ?? '') ?>" 
                                               min="1" max="114" 
                                               placeholder="Contoh: 1 (Al-Fatihah), 2 (Al-Baqarah)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Cari Surah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php if (isset($result) && $result['success']): ?>
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-check"></i> Surah Ditemukan</h5>
                                <p><strong>Nama Surah (Arab):</strong> <?= esc($result['surah_name_arabic'] ?? '-') ?></p>
                                <p><strong>Nama Surah (English):</strong> <?= esc($result['surah_name_english'] ?? '-') ?></p>
                                <p><strong>Terjemahan:</strong> <?= esc($result['surah_name_english_translation'] ?? '-') ?></p>
                                <p><strong>Jumlah Ayat:</strong> <?= esc($result['number_of_ayahs'] ?? 0) ?></p>
                                <p><strong>Tipe Wahyu:</strong> <?= esc($result['revelation_type'] ?? '-') ?></p>
                            </div>

                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h3 class="card-title text-white">
                                        <?= esc($result['surah_name_arabic'] ?? '') ?> 
                                        (<?= esc($result['surah_name_english'] ?? '') ?>)
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($result['ayahs'])): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width: 80px;">No. Ayat</th>
                                                        <th>Ayat (Arab)</th>
                                                        <th>No. dalam Surah</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($result['ayahs'] as $ayah): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?= base_url('backend/surah/' . $result['surah_number'] . '/' . $ayah['numberInSurah']) ?>" 
                                                                   class="btn btn-sm btn-info">
                                                                    <?= esc($ayah['numberInSurah'] ?? '-') ?>
                                                                </a>
                                                            </td>
                                                            <td style="font-size: 18px; text-align: right; direction: rtl; font-family: 'Amiri', 'Traditional Arabic', serif;">
                                                                <?= esc($ayah['text'] ?? '-') ?>
                                                            </td>
                                                            <td><?= esc($ayah['number'] ?? '-') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Tidak ada ayat yang ditemukan.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif (isset($result) && !$result['success']): ?>
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <p><?= esc($result['error'] ?? 'Terjadi kesalahan saat mengambil data surah') ?></p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Informasi</h5>
                                <p>Silakan masukkan nomor surah (1-114) untuk melihat isi surah.</p>
                                <p><strong>Contoh:</strong></p>
                                <ul>
                                    <li>1 = Al-Fatihah</li>
                                    <li>2 = Al-Baqarah</li>
                                    <li>3 = Ali 'Imran</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

