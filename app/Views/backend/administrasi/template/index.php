<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?= $this->include('backend/administrasi/_partials/page_header'); ?>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-file-invoice text-primary mr-2"></i>Daftar Master Template Surat
                    </h3>
                    <a href="<?= base_url('backend/administrasi/template/create') ?>" class="btn btn-primary btn-sm font-weight-bold ml-auto">
                        <i class="fas fa-plus mr-1"></i> Buat Template Baru
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="tableTemplates">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="width: 90px;" class="text-center">Aksi</th>
                                <th>Nama Template</th>
                                <th>Kategori</th>
                                <th style="width: 100px;">Format Kertas</th>
                                <th style="width: 110px;">Sumber Kop</th>
                                <th style="width: 80px;" class="text-center">Default</th>
                                <th style="width: 80px;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($templates as $t): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-cog mr-1"></i> Aksi
                                            </button>
                                            <div class="dropdown-menu shadow">
                                                <!-- Edit Template -->
                                                <a class="dropdown-item text-warning" href="<?= base_url('backend/administrasi/template/edit/' . $t['Id']) ?>">
                                                    <i class="fas fa-edit mr-2"></i> Edit Template
                                                </a>

                                                <!-- Set Default -->
                                                <?php if (!$t['IsDefault']): ?>
                                                    <a class="dropdown-item text-primary btn-set-default" href="javascript:void(0)" data-id="<?= $t['Id'] ?>">
                                                        <i class="fas fa-star mr-2"></i> Jadikan Default
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Toggle Aktif/Nonaktif -->
                                                <a class="dropdown-item text-<?= $t['IsActive'] ? 'secondary' : 'success' ?> btn-toggle-tpl" href="javascript:void(0)" data-id="<?= $t['Id'] ?>">
                                                    <i class="fas fa-power-off mr-2"></i> <?= $t['IsActive'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                </a>

                                                <div class="dropdown-divider"></div>

                                                <!-- Hapus Template -->
                                                <a class="dropdown-item text-danger btn-delete-tpl" href="javascript:void(0)" data-id="<?= $t['Id'] ?>" data-nama="<?= esc($t['NamaTemplate']) ?>">
                                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Template
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= esc($t['NamaTemplate']) ?></strong>
                                        <div class="mt-1">
                                            <?php if ($t['IdTpq'] == 0): ?>
                                                <span class="badge badge-dark"><i class="fas fa-university mr-1"></i> FKPQ (Admin / Global)</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><i class="fas fa-school mr-1"></i> <?= esc($t['NamaTpq'] ?? 'TPQ #' . $t['IdTpq']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($t['Deskripsi'])): ?>
                                            <small class="text-muted d-block mt-1"><?= esc($t['Deskripsi']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= esc($t['WarnaKategori'] ?: 'secondary') ?>">
                                            <?= esc($t['NamaKategori'] ?: '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">
                                            <?= esc($t['UkuranKertas']) ?> (<?= esc($t['Orientasi']) ?>)
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= esc($t['SumberKop']) ?>
                                            <?= !empty($t['NamaKopCustom']) ? ': ' . esc($t['NamaKopCustom']) : '' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($t['IsDefault']): ?>
                                            <span class="badge badge-primary"><i class="fas fa-star mr-1"></i> Default</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($t['IsActive']): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Nonaktif</span>
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
        $('#tableTemplates').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' },
            columnDefs: [
                { targets: [0, 1], orderable: false }
            ]
        });
    }

    // Set Default
    $(document).on('click', '.btn-set-default', function() {
        let id = $(this).data('id');
        $.post('<?= base_url('backend/administrasi/template/set-default') ?>/' + id, function(res) {
            if (res.success) {
                location.reload();
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }, 'json');
    });

    // Toggle Active
    $(document).on('click', '.btn-toggle-tpl', function() {
        let id = $(this).data('id');
        $.post('<?= base_url('backend/administrasi/template/toggle') ?>/' + id, function(res) {
            if (res.success) {
                location.reload();
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        }, 'json');
    });

    // Delete Template
    $(document).on('click', '.btn-delete-tpl', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus Template?',
            text: 'Template "' + nama + '" akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('backend/administrasi/template/delete') ?>/' + id, function(res) {
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
