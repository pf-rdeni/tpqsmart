<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-poll"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Survey</span>
                        <span class="info-box-number"><?= $total_survey ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Survey Aktif</span>
                        <span class="info-box-number"><?= $total_active ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Respon</span>
                        <span class="info-box-number"><?= $total_response ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon fas fa-check"></i> <?= session()->getFlashdata('message') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="icon fas fa-ban"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Main Card -->
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title">Daftar Survey</h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('backend/survey/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Buat Survey Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="surveyTable">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Survey</th>
                                <th>Target</th>
                                <th>Respon</th>
                                <th>Status</th>
                                <th>Tgl Dibuat</th>
                                <th style="width: 25%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($surveys as $index => $survey): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="font-weight-bold"><?= esc($survey['title']) ?></div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 300px;">
                                            <?= esc(strip_tags($survey['description'])) ?: 'Tidak ada deskripsi' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeColor = 'badge-secondary';
                                        $targetText = strtoupper($survey['target_type']);
                                        if ($survey['target_type'] === 'public') $badgeColor = 'badge-info';
                                        elseif ($survey['target_type'] === 'guru') $badgeColor = 'badge-primary';
                                        elseif ($survey['target_type'] === 'santri') $badgeColor = 'badge-success';
                                        elseif ($survey['target_type'] === 'tpq') $badgeColor = 'badge-warning';
                                        ?>
                                        <span class="badge <?= $badgeColor ?>"><?= $targetText ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-light shadow-sm px-3 border">
                                            <?= $survey['response_count'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status-switch" 
                                                   id="switch-<?= $survey['id'] ?>" 
                                                   data-id="<?= $survey['id'] ?>" 
                                                   <?= $survey['status'] === 'active' ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-normal status-label-<?= $survey['id'] ?>" 
                                                   for="switch-<?= $survey['id'] ?>">
                                                <?= $survey['status'] === 'active' ? '<span class="text-success font-weight-bold">Aktif</span>' : '<span class="text-muted">Draft</span>' ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($survey['created_at'])) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('backend/survey/edit/' . $survey['id']) ?>" 
                                               class="btn btn-warning" title="Edit Form Builder">
                                                <i class="fas fa-edit"></i> Builder
                                            </a>
                                            <a href="<?= base_url('backend/survey/settings/' . $survey['id']) ?>" 
                                               class="btn btn-info" title="Pengaturan">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            <a href="<?= base_url('backend/survey/results/' . $survey['id']) ?>" 
                                               class="btn btn-success" title="Hasil Analitik">
                                                <i class="fas fa-chart-bar"></i> Hasil
                                            </a>
                                            <button type="button" class="btn btn-secondary dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                <a class="dropdown-item" href="<?= base_url('backend/survey/preview/' . $survey['id']) ?>" target="_blank">
                                                    <i class="fas fa-eye text-primary mr-2"></i> Preview Form
                                                </a>
                                                <form action="<?= base_url('backend/survey/duplicate/' . $survey['id']) ?>" method="POST" class="d-inline">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-copy text-info mr-2"></i> Duplikasi Survey
                                                    </button>
                                                </form>
                                                <div class="dropdown-divider"></div>
                                                <form action="<?= base_url('backend/survey/delete/' . $survey['id']) ?>" method="POST" 
                                                      class="delete-survey-form d-inline" data-title="<?= esc($survey['title']) ?>">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash mr-2"></i> Hapus Survey
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#surveyTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Toggle Status
    $('.toggle-status-switch').on('change', function() {
        const surveyId = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const label = $(`.status-label-${surveyId}`);
        
        $.ajax({
            url: '<?= base_url('backend/survey/toggle-status') ?>',
            method: 'POST',
            data: { id: surveyId },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.status === 'active') {
                        label.html('<span class="text-success font-weight-bold">Aktif</span>');
                    } else {
                        label.html('<span class="text-muted">Draft</span>');
                    }
                } else {
                    toastr.error(response.message || 'Gagal mengubah status');
                    // Revert switch state
                    $(`#switch-${surveyId}`).prop('checked', !isChecked);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan koneksi.');
                $(`#switch-${surveyId}`).prop('checked', !isChecked);
            }
        });
    });

    // Confirm Delete via AJAX
    $('.delete-survey-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const actionUrl = form.attr('action');
        const surveyTitle = form.data('title');

        Swal.fire({
            title: 'Hapus Survey?',
            text: `Apakah Anda yakin ingin menghapus survey "${surveyTitle}"? Seluruh respon, pertanyaan, dan target akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus Survey...',
                    html: 'Sedang menghapus seluruh data survey terkait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            toastr.error(response.message || 'Gagal menghapus survey.');
                        }
                    },
                    error: function() {
                        Swal.close();
                        toastr.error('Terjadi kesalahan koneksi.');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
