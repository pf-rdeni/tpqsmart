<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-paper-plane text-primary mr-2"></i>Daftar Arsip Surat Keluar
                    </h3>
                    <div class="card-tools ml-auto">
                        <a href="<?= base_url('backend/administrasi/surat-keluar/buat') ?>" class="btn btn-primary btn-sm font-weight-bold">
                            <i class="fas fa-plus mr-1"></i> Buat Surat Baru
                        </a>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="card-body border-bottom bg-light py-2">
                    <form method="GET" action="<?= base_url('backend/administrasi/surat-keluar') ?>" class="form-inline">
                        <label class="mr-2 small font-weight-bold">Filter Kategori:</label>
                        <select name="kategori" class="form-control form-control-sm mr-3">
                            <option value="">-- Semua Kategori --</option>
                            <?php foreach ($kategoriList as $k): ?>
                                <option value="<?= $k['Id'] ?>" <?= (request()->getGet('kategori') == $k['Id']) ? 'selected' : '' ?>>
                                    <?= esc($k['NamaKategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label class="mr-2 small font-weight-bold">Status:</label>
                        <select name="status" class="form-control form-control-sm mr-3">
                            <option value="">-- Semua Status --</option>
                            <option value="Final" <?= (request()->getGet('status') == 'Final') ? 'selected' : '' ?>>Final</option>
                            <option value="Draft" <?= (request()->getGet('status') == 'Draft') ? 'selected' : '' ?>>Draft</option>
                        </select>

                        <button type="submit" class="btn btn-outline-secondary btn-sm mr-2">
                            <i class="fas fa-filter mr-1"></i> Terapkan
                        </button>
                        <a href="<?= base_url('backend/administrasi/surat-keluar') ?>" class="btn btn-link btn-sm text-muted">
                            Reset
                        </a>
                    </form>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="tableSuratKeluar">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="width: 90px;" class="text-center">Aksi</th>
                                <th>Nomor Surat</th>
                                <th style="width: 90px;">Tanggal</th>
                                <th>Perihal / Hal</th>
                                <th>Tujuan / Penerima</th>
                                <th>Kategori</th>
                                <th style="width: 80px;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($suratList as $s): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog mr-1"></i> Aksi
                                            </button>
                                            <div class="dropdown-menu shadow">
                                                <!-- Cetak PDF Gabungan -->
                                                <a class="dropdown-item text-danger" href="<?= base_url('backend/administrasi/surat-keluar/cetak/' . $s['Id']) ?>" target="_blank">
                                                    <i class="fas fa-file-pdf mr-2"></i> Buka / Cetak PDF
                                                </a>

                                                <!-- Unduh ZIP jika banyak penerima -->
                                                <?php if (!empty($s['TotalPenerima']) && $s['TotalPenerima'] > 1): ?>
                                                    <a class="dropdown-item text-primary" href="<?= base_url('backend/administrasi/surat-keluar/cetak-zip/' . $s['Id']) ?>">
                                                        <i class="fas fa-file-archive mr-2"></i> Unduh File ZIP (<?= $s['TotalPenerima'] ?>)
                                                    </a>
                                                <?php endif; ?>

                                                <div class="dropdown-divider"></div>

                                                <!-- Edit Surat -->
                                                <a class="dropdown-item text-warning" href="<?= base_url('backend/administrasi/surat-keluar/edit/' . $s['Id']) ?>">
                                                    <i class="fas fa-edit mr-2"></i> Edit Surat
                                                </a>

                                                <!-- Duplikat Surat -->
                                                <a class="dropdown-item text-info" href="<?= base_url('backend/administrasi/surat-keluar/duplikat/' . $s['Id']) ?>">
                                                    <i class="fas fa-copy mr-2"></i> Duplikat Surat
                                                </a>

                                                <div class="dropdown-divider"></div>

                                                <!-- Hapus Surat -->
                                                <a class="dropdown-item text-danger btn-delete-surat" href="javascript:void(0)" data-id="<?= $s['Id'] ?>" data-nomor="<?= esc($s['NomorSurat']) ?>">
                                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Surat
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= esc($s['NomorSurat'] ?: '-') ?></strong>
                                        <?php if ($isAdmin && !empty($s['NamaTpq'])): ?>
                                            <small class="d-block text-muted"><?= esc($s['NamaTpq']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($s['Tanggal']) ? date('d-m-Y', strtotime($s['Tanggal'])) : '-' ?></td>
                                    <td>
                                        <strong><?= esc($s['Hal'] ?: '-') ?></strong>
                                    </td>
                                    <td>
                                        <?= esc($s['TujuanNama'] ?: '-') ?>
                                        <?php if (!empty($s['TotalPenerima']) && $s['TotalPenerima'] > 1): ?>
                                            <span class="badge badge-info ml-1" title="Surat ditujukan ke <?= $s['TotalPenerima'] ?> penerima">
                                                <i class="fas fa-users"></i> <?= $s['TotalPenerima'] ?> Penerima
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= esc($s['WarnaKategori'] ?: 'secondary') ?>">
                                            <?= esc($s['NamaKategori'] ?: '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($s['Status'] === 'Final'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Final</span>
                                        <?php elseif ($s['Status'] === 'Draft'): ?>
                                            <span class="badge badge-warning"><i class="fas fa-edit"></i> Draft</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= esc($s['Status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.DataTable) {
        $('#tableSuratKeluar').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' },
            columnDefs: [
                { targets: [0, 1], orderable: false }
            ]
        });
    }

    // Hapus Surat via delegated event
    $(document).on('click', '.btn-delete-surat', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let nomor = $(this).data('nomor');
        Swal.fire({
            title: 'Hapus Surat Keluar?',
            text: 'Surat nomor "' + nomor + '" akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('backend/administrasi/surat-keluar/delete') ?>/' + id, function(res) {
                    if (res.success) {
                        Swal.fire('Terhapus', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
