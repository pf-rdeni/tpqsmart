<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Nilai Peserta Sertifikasi
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-info">
                                Juri: <?= esc($juri_data['usernameJuri']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="tableNilaiPeserta">
                                <thead>
                                    <tr>
                                        <th>No Peserta</th>
                                        <th>Nama Guru</th>
                                        <th>Nama TPQ</th>
                                        <?php if (!empty($all_materi)): ?>
                                            <?php foreach ($all_materi as $materi): ?>
                                                <th>Nilai <?= esc($materi['NamaMateri']) ?></th>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($nilai_data)): ?>
                                        <?php foreach ($nilai_data as $peserta): ?>
                                            <tr>
                                                <td data-export="<?= esc($peserta['noTest']) ?>"><strong><?= esc($peserta['noTest']) ?></strong></td>
                                                <td data-export="<?= esc($peserta['NamaGuru']) ?>"><?= esc($peserta['NamaGuru']) ?></td>
                                                <td data-export="<?= esc($peserta['NamaTpq']) ?>"><?= esc($peserta['NamaTpq']) ?></td>
                                                <?php if (!empty($all_materi)): ?>
                                                    <?php foreach ($all_materi as $materi): ?>
                                                        <?php 
                                                        $nilaiMateri = $peserta['nilaiByMateri'][$materi['IdMateri']] ?? null;
                                                        $badgeClass = '';
                                                        $displayNilai = 0;
                                                        
                                                        if ($nilaiMateri !== null && $nilaiMateri !== '') {
                                                            $displayNilai = floatval($nilaiMateri);
                                                            $badgeClass = $displayNilai >= 70 ? 'success' : ($displayNilai >= 60 ? 'warning' : 'danger');
                                                        } else {
                                                            // Jika belum ada nilai, tampilkan 0 dengan badge merah
                                                            $displayNilai = 0;
                                                            $badgeClass = 'danger';
                                                        }
                                                        ?>
                                                        <td class="text-center" data-export="<?= number_format($displayNilai, 2) ?>">
                                                            <span class="badge badge-<?= $badgeClass ?>">
                                                                <?= number_format($displayNilai, 2) ?>
                                                            </span>
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?= 3 + (count($all_materi ?? [])) ?>" class="text-center">
                                                <p class="text-muted">Tidak ada data nilai sertifikasi</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#tableNilaiPeserta').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'pageLength': 25,
            'order': [[0, 'asc']],
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Nilai Peserta Sertifikasi',
                    filename: 'Nilai_Peserta_Sertifikasi_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                // Gunakan data-export jika ada, jika tidak ambil text dari node
                                if (node && $(node).attr('data-export')) {
                                    return $(node).attr('data-export');
                                }
                                // Hapus tag HTML dari data (seperti badge, strong, dll)
                                var text = $(data).text();
                                return text || data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Nilai Peserta Sertifikasi',
                    filename: 'Nilai_Peserta_Sertifikasi_' + new Date().toISOString().split('T')[0],
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(data, row, column, node) {
                                // Gunakan data-export jika ada, jika tidak ambil text dari node
                                if (node && $(node).attr('data-export')) {
                                    return $(node).attr('data-export');
                                }
                                // Hapus tag HTML dari data (seperti badge, strong, dll)
                                var text = $(data).text();
                                return text || data;
                            }
                        }
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.pageMargins = [10, 10, 10, 10];
                    }
                }
            ],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json',
                'buttons': {
                    excel: 'Export ke Excel',
                    pdf: 'Export ke PDF'
                }
            }
        });
    });
</script>
<style>
    .dt-buttons {
        margin-bottom: 10px;
    }
    .dt-buttons .btn {
        margin-right: 5px;
    }
</style>
<?= $this->endSection(); ?>

