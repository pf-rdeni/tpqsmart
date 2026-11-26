<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<?php
$activeLinks = $activeLinks ?? [];
$archiveLinks = $archiveLinks ?? [];
$tahunAjaranLabel = function_exists('convertTahunAjaran') && !empty($tahunAjaranSaatIni)
    ? convertTahunAjaran($tahunAjaranSaatIni)
    : ($tahunAjaranSaatIni ?? '-');

$renderLinkRows = static function (array $rows, bool $isAdmin, bool $isArchive) {
    if (empty($rows)) {
        return;
    }
    $no = 1;
    foreach ($rows as $row) :
        $taDisplay = function_exists('convertTahunAjaran') && !empty($row['IdTahunAjaran'])
            ? convertTahunAjaran($row['IdTahunAjaran'])
            : ($row['IdTahunAjaran'] ?? '-');
        $publicUrl = base_url('absensi/haskey/' . $row['HashKey']);
        $waText = urlencode('Link Absensi Santri ' . ($row['NamaTpq'] ?? '') . ' (' . $taDisplay . '): ' . $publicUrl);
        $createdAt = !empty($row['CreatedAt']) ? date('d M Y, H:i', strtotime($row['CreatedAt'])) : '-';
        ?>
        <tr>
            <td class="text-center align-middle"><?= $no++; ?></td>
            <?php if ($isAdmin): ?>
                <td class="align-middle">
                    <div class="font-weight-bold text-dark"><?= esc($row['NamaTpq'] ?? '-'); ?></div>
                    <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= esc($row['KelurahanDesa'] ?? '-'); ?></small>
                </td>
            <?php endif; ?>
            <td class="align-middle text-center">
                <span class="badge <?= $isArchive ? 'badge-secondary' : 'badge-success' ?> px-2 py-1" style="font-size: 0.82rem;">
                    <?= esc($taDisplay); ?>
                </span>
                <div class="mt-1">
                    <small class="<?= $isArchive ? 'text-muted' : 'text-success font-weight-bold' ?>">
                        <?= $isArchive ? '<i class="fas fa-archive mr-1"></i>Arsip' : '<i class="fas fa-check-circle mr-1"></i>Aktif' ?>
                    </small>
                </div>
            </td>
            <td class="align-middle" style="min-width: 280px;">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control bg-light border-right-0 font-monospace" style="font-size: 0.8rem;" value="<?= esc($publicUrl) ?>" readonly id="link-<?= esc($row['Id']) ?>">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-info" onclick="copyToClipboard('link-<?= esc($row['Id']) ?>')" title="Salin Link">
                            <i class="fas fa-copy"></i>
                        </button>
                        <a href="<?= esc($publicUrl) ?>" target="_blank" class="btn btn-outline-primary" title="Buka Link">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= $waText ?>" target="_blank" class="btn btn-success" title="Kirim via WhatsApp" style="background-color: #25D366; border-color: #25D366;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </td>
            <td class="align-middle text-muted" style="white-space: nowrap;">
                <small><i class="far fa-clock mr-1"></i><?= esc($createdAt) ?></small>
            </td>
            <td class="align-middle text-center" style="white-space: nowrap;">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="<?= base_url('backend/absensi/link/edit/' . $row['Id']) ?>" class="btn btn-outline-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="<?= base_url('backend/absensi/link/regenerate/' . $row['Id']) ?>"
                        class="btn btn-outline-secondary btn-confirm-action"
                        data-title="Reset Key Link?"
                        data-text="Link lama tidak akan bisa digunakan lagi setelah key di-reset."
                        data-confirm="Ya, Reset Key"
                        title="Reset Key">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                    <a href="<?= base_url('backend/absensi/link/delete/' . $row['Id']) ?>"
                        class="btn btn-outline-danger btn-confirm-action"
                        data-title="Hapus Link Absensi?"
                        data-text="Data link yang dihapus tidak dapat dikembalikan."
                        data-confirm="Ya, Hapus"
                        title="Hapus">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>
        </tr>
        <?php
    endforeach;
};
?>

<div class="col-12">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Ringkasan Statistik Modern -->
    <div class="row mb-3">
        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
            <div class="info-box shadow-sm mb-0" style="border-radius: 0.6rem;">
                <span class="info-box-icon bg-gradient-success elevation-1" style="border-radius: 0.5rem;">
                    <i class="fas fa-link"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Link Aktif (TA <?= esc($tahunAjaranLabel) ?>)</span>
                    <span class="info-box-number" style="font-size: 1.35rem;"><?= count($activeLinks) ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
            <div class="info-box shadow-sm mb-0" style="border-radius: 0.6rem;">
                <span class="info-box-icon bg-gradient-secondary elevation-1" style="border-radius: 0.5rem;">
                    <i class="fas fa-archive"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Arsip Tahun Ajaran Lama</span>
                    <span class="info-box-number" style="font-size: 1.35rem;"><?= count($archiveLinks) ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 d-flex align-items-stretch">
            <div class="card shadow-sm mb-0 w-100 d-flex justify-content-center" style="border-radius: 0.6rem; border-left: 4px solid #007bff;">
                <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs text-muted">Tambah Akses Presensi</div>
                        <div class="font-weight-bold text-dark">Link Absensi Public</div>
                    </div>
                    <a href="<?= base_url('backend/absensi/link/new') ?>" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Buat Link Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tab Aktif & Archive -->
    <div class="card card-primary card-outline card-tabs shadow-sm" style="border-radius: 0.6rem;">
        <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="linkAbsensiTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="tab-link-aktif" data-toggle="tab" href="#pane-link-aktif" role="tab" aria-controls="pane-link-aktif" aria-selected="true">
                        <i class="fas fa-broadcast-tower text-success mr-1"></i> Link Aktif
                        <span class="badge badge-success ml-1"><?= count($activeLinks) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab-link-archive" data-toggle="tab" href="#pane-link-archive" role="tab" aria-controls="pane-link-archive" aria-selected="false">
                        <i class="fas fa-archive text-secondary mr-1"></i> Archive (TA Lama)
                        <span class="badge badge-secondary ml-1"><?= count($archiveLinks) ?></span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="linkAbsensiTabsContent">
                <!-- Tab Link Aktif -->
                <div class="tab-pane fade show active" id="pane-link-aktif" role="tabpanel" aria-labelledby="tab-link-aktif">
                    <?php if (!empty($activeLinks)) : ?>
                        <div class="table-responsive">
                            <table id="tableLinkAktif" class="table table-hover table-bordered w-100">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 45px;">No</th>
                                        <?php if ($isAdmin): ?><th>Lembaga (TPQ)</th><?php endif; ?>
                                        <th class="text-center" style="width: 130px;">Tahun Ajaran</th>
                                        <th>Link Absensi Public</th>
                                        <th style="width: 150px;">Dibuat Pada</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $renderLinkRows($activeLinks, (bool) $isAdmin, false); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-link fa-2x mb-2 text-secondary"></i>
                            <p class="mb-2">Belum ada link absensi public untuk tahun ajaran aktif (<strong><?= esc($tahunAjaranLabel) ?></strong>).</p>
                            <a href="<?= base_url('backend/absensi/link/new') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Buat Link Baru
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Archive -->
                <div class="tab-pane fade" id="pane-link-archive" role="tabpanel" aria-labelledby="tab-link-archive">
                    <div class="alert alert-light border text-xs mb-3 py-2">
                        <i class="fas fa-info-circle text-secondary mr-1"></i>
                        Menampilkan daftar link absensi public dari tahun ajaran sebelum <strong><?= esc($tahunAjaranLabel) ?></strong> sebagai arsip.
                    </div>
                    <?php if (!empty($archiveLinks)) : ?>
                        <div class="table-responsive">
                            <table id="tableLinkArchive" class="table table-hover table-bordered w-100">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 45px;">No</th>
                                        <?php if ($isAdmin): ?><th>Lembaga (TPQ)</th><?php endif; ?>
                                        <th class="text-center" style="width: 130px;">Tahun Ajaran</th>
                                        <th>Link Absensi Public</th>
                                        <th style="width: 150px;">Dibuat Pada</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $renderLinkRows($archiveLinks, (bool) $isAdmin, true); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-archive fa-2x mb-2 text-secondary"></i>
                            <p class="mb-0">Belum ada arsip link absensi dari tahun ajaran sebelumnya.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(function() {
        if (typeof initializeDataTableUmum === 'function') {
            if ($('#tableLinkAktif').length) initializeDataTableUmum('#tableLinkAktif', true, true);
            if ($('#tableLinkArchive').length) initializeDataTableUmum('#tableLinkArchive', true, true);
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            if ($.fn.dataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });

        $(document).on('click', '.btn-confirm-action', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var title = $(this).data('title') || 'Konfirmasi';
            var text = $(this).data('text') || 'Apakah Anda yakin?';
            var confirmText = $(this).data('confirm') || 'Ya, Lanjutkan';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: text,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            } else if (confirm(title + '\n' + text)) {
                window.location.href = href;
            }
        });
    });

    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        if (!copyText) return;
        var val = copyText.value;

        var notifySuccess = function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Link berhasil disalin',
                    showConfirmButton: false,
                    timer: 1800
                });
            } else {
                alert('Link berhasil disalin: ' + val);
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(val).then(notifySuccess).catch(function() {
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand('copy');
                notifySuccess();
            });
        } else {
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand('copy');
            notifySuccess();
        }
    }
</script>
<?= $this->endSection(); ?>
