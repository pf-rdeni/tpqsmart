<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?= $this->include('backend/administrasi/_partials/page_header'); ?>

    <!-- Kartu Statistik -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3><?= $stats['total'] ?></h3>
                    <p>Total Surat Teregister</p>
                </div>
                <div class="icon">
                    <i class="fas fa-mail-bulk"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-primary shadow-sm">
                <div class="inner">
                    <h3><?= $stats['keluar'] ?></h3>
                    <p>Surat Keluar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3><?= $stats['masuk'] ?></h3>
                    <p>Surat Masuk</p>
                </div>
                <div class="icon">
                    <i class="fas fa-inbox"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Filter Laporan -->
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title font-weight-bold"><i class="fas fa-filter mr-1"></i> Parameter Filter Rekapitulasi</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= base_url('backend/administrasi/laporan') ?>" id="formFilterLaporan">
                <div class="row">
                    <!-- Jenis Surat -->
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Jenis Surat</label>
                        <select name="jenis" class="form-control form-control-sm">
                            <option value="Semua" <?= ($filters['jenis'] === 'Semua') ? 'selected' : '' ?>>-- Semua Jenis --</option>
                            <option value="Keluar" <?= ($filters['jenis'] === 'Keluar') ? 'selected' : '' ?>>Surat Keluar</option>
                            <option value="Masuk" <?= ($filters['jenis'] === 'Masuk') ? 'selected' : '' ?>>Surat Masuk</option>
                        </select>
                    </div>

                    <!-- Kategori Surat -->
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Kategori Surat</label>
                        <select name="id_kategori" class="form-control form-control-sm">
                            <option value="">-- Semua Kategori --</option>
                            <?php foreach ($kategoriList as $kat): ?>
                                <option value="<?= $kat['Id'] ?>" <?= ($filters['id_kategori'] == $kat['Id']) ? 'selected' : '' ?>>
                                    <?= esc($kat['NamaKategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="<?= esc($filters['tanggal_mulai']) ?>">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm" value="<?= esc($filters['tanggal_selesai']) ?>">
                    </div>
                </div>

                <div class="row">
                    <!-- Status Surat -->
                    <div class="col-md-3 form-group">
                        <label class="small font-weight-bold">Status Surat</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="Semua" <?= ($filters['status'] === 'Semua') ? 'selected' : '' ?>>-- Semua Status --</option>
                            <option value="Final" <?= ($filters['status'] === 'Final') ? 'selected' : '' ?>>Final</option>
                            <option value="Draft" <?= ($filters['status'] === 'Draft') ? 'selected' : '' ?>>Draft</option>
                            <option value="Diarsipkan" <?= ($filters['status'] === 'Diarsipkan') ? 'selected' : '' ?>>Diarsipkan</option>
                        </select>
                    </div>

                    <!-- Filter Lembaga (Khusus Admin) -->
                    <?php if (!empty($isAdmin)): ?>
                        <div class="col-md-5 form-group">
                            <label class="small font-weight-bold">Lembaga Terkait</label>
                            <select name="id_tpq" class="form-control form-control-sm select2">
                                <option value="Semua" <?= ($filters['id_tpq'] === 'Semua') ? 'selected' : '' ?>>-- Semua Lembaga --</option>
                                <option value="0" <?= ($filters['id_tpq'] === '0' || $filters['id_tpq'] === 0) ? 'selected' : '' ?>>🏛️ FKPQ (Admin / Global)</option>
                                <?php foreach ($listTpq as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>" <?= ($filters['id_tpq'] == $tpq['IdTpq']) ? 'selected' : '' ?>>
                                        <?= esc($tpq['NamaTpq']) ?> (<?= esc($tpq['Kecamatan'] ?? '') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="col-md-4 ml-auto d-flex align-items-end justify-content-end form-group">
                        <button type="submit" class="btn btn-primary btn-sm mr-2 shadow-sm">
                            <i class="fas fa-search mr-1"></i> Terapkan Filter
                        </button>
                        <a href="<?= base_url('backend/administrasi/laporan') ?>" class="btn btn-outline-secondary btn-sm mr-2">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </a>
                        <button type="button" class="btn btn-success btn-sm shadow-sm font-weight-bold" id="btnExportExcel">
                            <i class="fas fa-file-excel mr-1"></i> Ekspor Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Tabel Hasil Rekapitulasi -->
    <div class="card card-outline card-secondary shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-table mr-1 text-primary"></i> Data Rekapitulasi Surat 
                <span class="badge badge-info ml-1"><?= count($suratList) ?> Arsip Ditemukan</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm" id="tableRekapitulasi">
                    <thead class="thead-light text-center">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th style="width: 80px;">Jenis</th>
                            <th>Nomor Surat / Agenda</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th>Kategori</th>
                            <th>Asal / Tujuan</th>
                            <th>Perihal</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($suratList)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                    Tidak ada data surat yang sesuai dengan parameter filter di atas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($suratList as $s): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center">
                                        <?php if ($s['JenisSurat'] === 'Keluar'): ?>
                                            <span class="badge badge-primary"><i class="fas fa-paper-plane mr-1"></i> Keluar</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><i class="fas fa-inbox mr-1"></i> Masuk</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($s['NomorSurat'] ?: '-') ?></strong>
                                        <?php if (!empty($s['NoAgenda'])): ?>
                                            <div class="small text-muted">Agenda: <?= esc($s['NoAgenda']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= !empty($s['Tanggal']) ? date('d/m/Y', strtotime($s['Tanggal'])) : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">
                                            <?= esc($s['NamaKategori'] ?? 'Umum') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($s['JenisSurat'] === 'Keluar'): ?>
                                            <i class="fas fa-arrow-right text-primary mr-1"></i>
                                            <?= esc($s['TujuanNama'] ?: ($s['TujuanInstansi'] ?? '-')) ?>
                                            <?php if (!empty($s['TujuanJabatan'])): ?>
                                                <div class="small text-muted"><?= esc($s['TujuanJabatan']) ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <i class="fas fa-arrow-left text-success mr-1"></i>
                                            <?= esc($s['AsalSurat'] ?: '-') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($s['Hal']) ?></td>
                                    <td class="text-center">
                                        <?php if ($s['Status'] === 'Final'): ?>
                                            <span class="badge badge-success">Final</span>
                                        <?php elseif ($s['Status'] === 'Draft'): ?>
                                            <span class="badge badge-warning">Draft</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= esc($s['Status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($s['JenisSurat'] === 'Keluar'): ?>
                                            <a href="<?= base_url('backend/administrasi/surat-keluar/cetak/' . $s['Id']) ?>" target="_blank" class="btn btn-outline-danger btn-xs" title="Cetak PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="<?= base_url('backend/administrasi/surat-keluar/edit/' . $s['Id']) ?>" class="btn btn-outline-primary btn-xs" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('backend/administrasi/surat-masuk/detail/' . $s['Id']) ?>" class="btn btn-outline-info btn-xs" title="Detail Surat Masuk">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi DataTables jika data ada
    if ($('#tableRekapitulasi tbody tr').length > 1 || !$('#tableRekapitulasi tbody tr td').hasClass('text-muted')) {
        $('#tableRekapitulasi').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            },
            pageLength: 25,
            order: [[3, 'desc']]
        });
    }

    // Tombol Ekspor Excel
    $('#btnExportExcel').on('click', function(e) {
        e.preventDefault();
        let form = $('#formFilterLaporan');
        let originalAction = form.attr('action');
        let originalTarget = form.attr('target');

        form.attr('action', '<?= base_url('backend/administrasi/laporan/export-excel') ?>');
        form.attr('target', '_blank');
        form.submit();

        // Kembalikan form action semula
        form.attr('action', originalAction);
        form.removeAttr('target');
    });
});
</script>

<?= $this->endSection(); ?>
