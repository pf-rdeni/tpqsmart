<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center mb-2 mb-md-0">
        <div class="btn-group btn-group-sm">
            <a href="<?= base_url("backend/survey/results/export-excel/{$survey['id']}") ?>" class="btn btn-success">
                <i class="far fa-file-excel mr-1"></i> Export Excel (CSV)
            </a>
            <a href="<?= base_url("backend/survey/results/export-pdf/{$survey['id']}") ?>" target="_blank" class="btn btn-danger">
                <i class="far fa-file-pdf mr-1"></i> Cetak Ringkasan (PDF)
            </a>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger btn-reset-responses ml-2" title="Hapus semua tanggapan masuk">
            <i class="fas fa-trash-restore mr-1"></i> Reset Tanggapan
        </button>
    </div>
    
    <!-- Inline Filter Form -->
    <form class="form-inline mb-2 mb-md-0" id="filter-responses-form" method="GET" action="javascript:void(0)">
        <div class="form-group form-group-sm mr-2">
            <select class="form-control form-control-sm" name="tpq_id" id="filter-tpq">
                <option value="">-- Semua Lembaga TPQ --</option>
                <?php foreach ($tpqs as $tpq): ?>
                    <option value="<?= $tpq['IdTpq'] ?>" <?= ($filters['tpq_id'] ?? '') == $tpq['IdTpq'] ? 'selected' : '' ?>><?= esc($tpq['NamaTpq']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="button" class="btn btn-sm btn-primary" id="btn-apply-filter"><i class="fas fa-filter mr-1"></i> Filter</button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped table-sm" id="responsesTable">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Nama Responden</th>
                <th>TPQ</th>
                <th>Email / No HP</th>
                <th>Waktu Pengiriman</th>
                <th style="width: 15%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responses as $index => $resp): ?>
                <tr id="response-row-<?= $resp['id'] ?>">
                    <td><?= $index + 1 ?></td>
                    <td>
                        <span class="font-weight-bold text-dark"><?= esc($resp['respondent_name'] ?? 'Anonim') ?></span>
                        <small class="text-muted d-block font-xs">IP: <?= esc($resp['ip_address'] ?? '-') ?></small>
                    </td>
                    <td><?= esc($resp['NamaTpq'] ?? 'Lembaga Lain / Publik') ?></td>
                    <td>
                        <?php if ($resp['respondent_email']): ?>
                            <div class="small"><i class="far fa-envelope text-muted mr-1"></i> <?= esc($resp['respondent_email']) ?></div>
                        <?php endif; ?>
                        <?php if ($resp['respondent_phone']): ?>
                            <div class="small"><i class="fas fa-phone-alt text-muted mr-1"></i> <?= esc($resp['respondent_phone']) ?></div>
                        <?php endif; ?>
                        <?php if (!$resp['respondent_email'] && !$resp['respondent_phone']): ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($resp['submitted_at'])) ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-primary btn-view-response" data-id="<?= $resp['id'] ?>" title="Detail Jawaban">
                            <i class="far fa-eye"></i> Detail
                        </button>
                        <button type="button" class="btn btn-xs btn-warning btn-edit-response" data-id="<?= $resp['id'] ?>" title="Edit Jawaban">
                            <i class="far fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-xs btn-danger btn-delete-response" data-id="<?= $resp['id'] ?>" title="Hapus Jawaban">
                            <i class="far fa-trash-alt"></i> Hapus
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal container for view-response loaded via AJAX -->
<div class="modal fade" id="viewResponseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal-response-detail-content">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Modal container for edit-response loaded via AJAX -->
<div class="modal fade" id="editResponseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal-response-edit-content">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Destroy existing table if loaded multiple times via load()
    if ($.fn.DataTable.isDataTable('#responsesTable')) {
        $('#responsesTable').DataTable().destroy();
    }

    const respTable = $('#responsesTable').DataTable({
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

    // Custom filtering
    $('#btn-apply-filter').on('click', function() {
        const tpqId = $('#filter-tpq').val();
        
        // Reload tab content with filter query
        const url = `<?= base_url("backend/survey/results/responses/{$survey['id']}") ?>?tpq_id=${tpqId}`;
        $('#tab-responses').html(`
            <div class="text-center py-5 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Menerapkan filter...</div>
            </div>
        `).load(url);
    });

    // View Response Detail Modal
    $(document).off('click', '.btn-view-response').on('click', '.btn-view-response', function() {
        const respId = $(this).data('id');
        const modal = $('#viewResponseModal');
        
        $('#modal-response-detail-content').html(`
            <div class="text-center py-5 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Memuat jawaban responden...</div>
            </div>
        `).load(`<?= base_url('backend/survey/results/view-response') ?>/${respId}`);

        modal.modal('show');
    });

    // Edit Response Modal
    $(document).off('click', '.btn-edit-response').on('click', '.btn-edit-response', function() {
        const respId = $(this).data('id');
        const modal = $('#editResponseModal');
        
        $('#modal-response-edit-content').html(`
            <div class="text-center py-5 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Memuat form edit tanggapan...</div>
            </div>
        `).load(`<?= base_url('backend/survey/results/edit-response') ?>/${respId}`);

        modal.modal('show');
    });

    // Delete single response
    $(document).off('click', '.btn-delete-response').on('click', '.btn-delete-response', function() {
        const respId = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Tanggapan?',
            text: 'Apakah Anda yakin ingin menghapus tanggapan responden ini? Tindakan ini permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('backend/survey/results/delete-response') ?>/${respId}`,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            
                            // Remove row from DataTable
                            respTable.row($(`#response-row-${respId}`)).remove().draw(false);
                        } else {
                            toastr.error(response.message || 'Gagal menghapus tanggapan.');
                        }
                    }
                });
            }
        });
    });

    // Reset all responses
    $(document).off('click', '.btn-reset-responses').on('click', '.btn-reset-responses', function() {
        Swal.fire({
            title: 'Reset Semua Tanggapan?',
            text: 'Apakah Anda yakin ingin menghapus SELURUH tanggapan masuk untuk survey ini? Struktur pertanyaan tidak akan dihapus. Tindakan ini tidak dapat dibatalkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses Reset...',
                    html: 'Sedang menghapus seluruh data respon...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: `<?= base_url("backend/survey/results/reset-responses/{$survey['id']}") ?>`,
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
                            toastr.error(response.message || 'Gagal mereset data.');
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
