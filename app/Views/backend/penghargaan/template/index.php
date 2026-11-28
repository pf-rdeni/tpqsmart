<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="alert alert-info text-sm mb-0 mr-3 flex-grow-1 py-2">
            <i class="fas fa-info-circle mr-1"></i>
            Setiap kelompok (<strong>Semua Jenis</strong> maupun <strong>Khusus Jenis Tertentu</strong>) hanya memiliki <strong>1 template yang Dipilih</strong>.
            Saat mencetak, sistem otomatis memakai template yang <strong>Dipilih</strong> di kelompok khusus jenis tersebut; jika belum ada, otomatis memakai template yang <strong>Dipilih</strong> di kelompok <strong>Semua Jenis</strong>.
        </div>
        <button type="button" class="btn btn-primary btn-sm flex-shrink-0" data-toggle="modal" data-target="#modalAddTemplate">
            <i class="fas fa-plus mr-1"></i> Buat Template Baru
        </button>
    </div>

    <!-- BAGIAN ATAS (COMPACT): Template Referensi / Starter / Dari Admin -->
    <?php if (!empty($referenceTemplates)): ?>
        <div class="card card-outline card-secondary shadow-sm mb-4">
            <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold text-sm text-secondary mb-0">
                    <i class="fas fa-layer-group mr-1"></i>
                    <?= !empty($isAdmin) ? 'Template Starter Sistem (Referensi Bawaan)' : 'Template Referensi dari Admin (Klik Duplikat untuk Kustomisasi TPQ Anda)' ?>
                    <span class="badge badge-secondary ml-1"><?= count($referenceTemplates) ?></span>
                </h3>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <?php foreach ($referenceTemplates as $ref): ?>
                        <?php $isSelectedRef = !empty($ref['IsDefault']) && !empty($isAdmin); ?>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-2">
                            <div class="card h-100 <?= $isSelectedRef ? 'border-success' : 'border' ?> shadow-none mb-0">
                                <div class="position-relative bg-light text-center p-1" style="height: 85px; overflow: hidden;">
                                    <?php if (!empty($ref['BackgroundFile'])): ?>
                                        <img src="<?= base_url($ref['BackgroundFile']) ?>" alt="Ref" style="max-height: 75px; max-width: 100%; object-fit: contain;">
                                    <?php endif; ?>
                                    <?php if ($isSelectedRef): ?>
                                        <span class="badge badge-success position-absolute" style="top: 4px; right: 4px; font-size: 9px;">DIPILIH</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body p-2">
                                    <div class="font-weight-bold text-xs text-truncate" title="<?= esc($ref['NamaTemplate']) ?>">
                                        <?= esc($ref['NamaTemplate']) ?>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 10px;">
                                        <?= esc($ref['UkuranKertas']) ?> · <?= esc(ucfirst($ref['Orientasi'])) ?>
                                    </small>
                                </div>
                                <div class="card-footer bg-white p-1 d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (!empty($isAdmin) && !$isSelectedRef): ?>
                                            <button type="button"
                                                class="btn btn-success btn-xs py-0 px-1 btn-select-template"
                                                data-id="<?= $ref['Id'] ?>"
                                                data-nama="<?= esc($ref['NamaTemplate']) ?>"
                                                data-grup="Template Starter / Umum"
                                                title="Pilih sebagai default Admin">
                                                <i class="fas fa-check" style="font-size: 10px;"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?= base_url('backend/penghargaan/template/preview/' . $ref['Id']) ?>" class="btn btn-info btn-xs py-0 px-1" title="Preview">
                                            <i class="fas fa-eye" style="font-size: 10px;"></i>
                                        </a>
                                        <?php if (!empty($isAdmin)): ?>
                                            <a href="<?= base_url('backend/penghargaan/template/editor/' . $ref['Id']) ?>" class="btn btn-primary btn-xs py-0 px-1" title="Editor">
                                                <i class="fas fa-edit" style="font-size: 10px;"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <form action="<?= base_url('backend/penghargaan/template/duplicate/' . $ref['Id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-xs py-0 px-2" title="Duplikat ke Template Saya">
                                            <i class="fas fa-copy mr-1" style="font-size: 10px;"></i><span style="font-size: 10px;">Duplikat</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- BAGIAN BAWAH: Template Milik Lembaga / Custom (Dikelompokkan per Jenis) -->
    <?php
    $hasOwnTemplates = false;
    foreach ($groupedTemplates as $g) {
        if (!empty($g['items'])) {
            $hasOwnTemplates = true;
            break;
        }
    }
    ?>
    <?php if (!$hasOwnTemplates): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-4 text-muted text-sm">
                <i class="fas fa-palette fa-2x mb-2 d-block"></i>
                Belum ada template milik TPQ Anda sendiri. Klik <strong>Duplikat</strong> pada template referensi di atas atau klik <strong>Buat Template Baru</strong>.
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($groupedTemplates as $groupKey => $group): ?>
        <?php if (empty($group['items'])) continue; ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-2">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas <?= $groupKey === 'umum' ? 'fa-globe text-primary' : 'fa-tag text-info' ?> mr-2"></i>
                    <?= esc($group['title']) ?>
                    <span class="badge badge-light border ml-2"><?= count($group['items']) ?> template</span>
                </h3>
            </div>
            <div class="card-body pb-1">
                <div class="row">
                    <?php foreach ($group['items'] as $tpl): ?>
                        <?php
                        $isSelected    = !empty($tpl['IsDefault']);
                        $isAdminTpl    = empty($tpl['IdTpq']) || (string) $tpl['IdTpq'] === '0';
                        $canEditThis   = !empty($isAdmin) ? $isAdminTpl : (!$isAdminTpl && (string) ($tpl['IdTpq'] ?? '') === (string) $idTpq);
                        ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 <?= $isSelected ? 'border-success shadow' : 'border shadow-none' ?>" style="<?= $isSelected ? 'border-width: 2px;' : '' ?>">
                                <div class="position-relative bg-light text-center p-2" style="height: 185px; overflow: hidden;">
                                    <?php if (!empty($tpl['BackgroundFile'])): ?>
                                        <img src="<?= base_url($tpl['BackgroundFile']) ?>" alt="Preview" style="max-height: 165px; max-width: 100%; object-fit: contain; border: 1px solid #dee2e6;">
                                    <?php else: ?>
                                        <div class="text-muted pt-5"><i class="fas fa-image fa-3x"></i></div>
                                    <?php endif; ?>

                                    <?php if ($isSelected): ?>
                                        <span class="badge badge-success position-absolute px-2 py-1" style="top: 10px; right: 10px;">
                                            <i class="fas fa-check-circle mr-1"></i> DIPILIH
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-<?= $tpl['Status'] === 'aktif' ? 'info' : 'warning' ?> position-absolute" style="top: 10px; right: 10px;">
                                            <?= esc(ucfirst($tpl['Status'])) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($isAdminTpl): ?>
                                        <span class="badge badge-dark position-absolute" style="top: 10px; left: 10px;">
                                            Template Admin<?= !empty($tpl['AllowDuplicate']) ? ' · Boleh Duplikat' : ' · Privat Admin' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body py-2">
                                    <h6 class="font-weight-bold mb-1"><?= esc($tpl['NamaTemplate']) ?></h6>
                                    <small class="text-muted d-block">
                                        <?= esc($tpl['UkuranKertas']) ?> · <?= esc(ucfirst($tpl['Orientasi'])) ?>
                                    </small>
                                </div>
                                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap py-2">
                                    <div class="mb-1">
                                        <?php if ($canEditThis): ?>
                                            <?php if (!$isSelected): ?>
                                                <button type="button"
                                                    class="btn btn-success btn-xs btn-select-template"
                                                    data-id="<?= $tpl['Id'] ?>"
                                                    data-nama="<?= esc($tpl['NamaTemplate']) ?>"
                                                    data-grup="<?= esc($group['title']) ?>"
                                                    title="Jadikan template aktif terpilih untuk grup ini">
                                                    <i class="fas fa-check mr-1"></i> Pilih
                                                </button>
                                            <?php else: ?>
                                                <span class="text-success font-weight-bold text-xs mr-1"><i class="fas fa-check-double"></i> Aktif Dipilih</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted text-xs"><i class="fas fa-lock mr-1"></i> Read-only</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-1">
                                        <a href="<?= base_url('backend/penghargaan/template/preview/' . $tpl['Id']) ?>" class="btn btn-info btn-xs" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($canEditThis): ?>
                                            <a href="<?= base_url('backend/penghargaan/template/editor/' . $tpl['Id']) ?>" class="btn btn-primary btn-xs" title="Editor">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <form action="<?= base_url('backend/penghargaan/template/duplicate/' . $tpl['Id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-secondary btn-xs" title="Duplikasi &amp; Customisasi">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </form>
                                        <?php if ($canEditThis): ?>
                                            <form action="<?= base_url('backend/penghargaan/template/delete/' . $tpl['Id']) ?>" method="post" class="d-inline form-delete-template">
                                                <?= csrf_field() ?>
                                                <button type="button" class="btn btn-danger btn-xs btn-delete-template" data-nama="<?= esc($tpl['NamaTemplate']) ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Tambah Template -->
<div class="modal fade" id="modalAddTemplate" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('backend/penghargaan/template/store') ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Buat Template Sertifikat Baru</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Template</label>
                    <input type="text" name="NamaTemplate" class="form-control" placeholder="Misal: Sertifikat Juara TPQ 2026" required>
                </div>
                <div class="form-group">
                    <label>Khusus Jenis Penghargaan (Opsional)</label>
                    <select name="IdJenisPenghargaan" class="form-control">
                        <option value="">Semua Jenis Penghargaan (Umum)</option>
                        <?php foreach ($listJenis as $j): ?>
                            <option value="<?= $j['Id'] ?>">[<?= esc($j['Kode']) ?>] <?= esc($j['NamaPenghargaan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Ukuran Kertas</label>
                        <select name="UkuranKertas" class="form-control">
                            <option value="A4">A4</option>
                            <option value="A5">A5</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Orientasi</label>
                        <select name="Orientasi" class="form-control">
                            <option value="landscape">Landscape (Mendatar)</option>
                            <option value="portrait">Portrait (Tegak)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Upload Background Image (JPG/PNG/SVG)</label>
                    <input type="file" name="BackgroundFile" class="form-control-file" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                    <small class="text-muted">Kosongkan untuk menggunakan background starter default.</small>
                </div>
                <?php if (!empty($isAdmin)): ?>
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="addAllowDuplicate" name="AllowDuplicate" value="1" checked>
                            <label class="custom-control-label" for="addAllowDuplicate">Izinkan Operator TPQ melihat &amp; menduplikasi template ini</label>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Buat &amp; Buka Editor</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';

    <?php if (session()->getFlashdata('success')): ?>
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: <?= json_encode(session()->getFlashdata('success')) ?>,
            timer: 2500,
            showConfirmButton: false
        });
    }
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: <?= json_encode(session()->getFlashdata('error')) ?>
        });
    }
    <?php endif; ?>

    document.querySelectorAll('.btn-select-template').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const nama = this.dataset.nama;
            const grup = this.dataset.grup;

            Swal.fire({
                title: 'Pilih Template Ini?',
                html: 'Template <strong>' + nama + '</strong> akan diaktifkan dan ditandai sebagai <strong>DIPILIH</strong> untuk kelompok:<br><span class="badge badge-info mt-1 p-1">' + grup + '</span><br><br><small class="text-muted">Template lain yang sebelumnya dipilih di kelompok yang sama akan otomatis dilepas agar tidak terjadi duplikasi.</small>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Pilih & Aktifkan',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const formData = new FormData();
                formData.append(csrfName, csrfHash);

                fetch('<?= base_url('backend/penghargaan/template/select') ?>/' + id, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Template Dipilih!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal memilih template.' });
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan koneksi.' });
                });
            });
        });
    });

    document.querySelectorAll('.btn-delete-template').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = this.closest('form');
            const nama = this.dataset.nama;
            Swal.fire({
                title: 'Hapus Template?',
                html: 'Template <strong>' + nama + '</strong> akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<?= $this->endSection(); ?>
