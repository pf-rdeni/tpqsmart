<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center mb-2 mb-md-0">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-table mr-2 text-info"></i> Tabel Analisis Tanggapan</h5>
    </div>
    
    <!-- Inline Filter Form -->
    <form class="form-inline mb-2 mb-md-0" id="filter-dynamic-form" method="GET" action="javascript:void(0)">
        <div class="form-group form-group-sm mr-2">
            <select class="form-control form-control-sm" name="tpq_id" id="filter-dynamic-tpq">
                <option value="">-- Semua Lembaga TPQ --</option>
                <?php foreach ($tpqs as $tpq): ?>
                    <option value="<?= $tpq['IdTpq'] ?>" <?= ($filters['tpq_id'] ?? '') == $tpq['IdTpq'] ? 'selected' : '' ?>><?= esc($tpq['NamaTpq']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="button" class="btn btn-sm btn-primary" id="btn-apply-dynamic-filter"><i class="fas fa-filter mr-1"></i> Filter</button>
    </form>
</div>

<div class="table-responsive" style="border: 1px solid #dee2e6; border-radius: 4px;">
    <table class="table table-hover table-striped table-sm text-sm mb-0" id="dynamicTable" style="width: 100%; white-space: nowrap;">
        <thead>
            <tr class="bg-light">
                <th style="width: 40px; text-align: center;">No</th>
                <th>Nama Responden</th>
                <th>TPQ / Lembaga</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Waktu Pengisian</th>
                <?php foreach ($questions as $q): ?>
                    <th class="text-wrap" style="min-width: 180px; max-width: 300px;" title="<?= esc(strip_tags($q['question_text'])) ?>">
                        <?= esc(mb_strimwidth(strip_tags($q['question_text']), 0, 40, '...')) ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responses as $index => $resp): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td>
                        <span class="font-weight-bold text-dark"><?= esc($resp['respondent_name'] ?? 'Anonim') ?></span>
                    </td>
                    <td><?= esc($resp['NamaTpq'] ?? 'Lembaga Lain / Publik') ?></td>
                    <td><?= esc($resp['respondent_email'] ?? '-') ?></td>
                    <td><?= esc($resp['respondent_phone'] ?? '-') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($resp['submitted_at'])) ?></td>
                    <?php foreach ($questions as $q): ?>
                        <td>
                            <?php
                            $ans = $resp['answers_decoded']['q_' . $q['id']] ?? null;
                            $ansText = '-';
                            if ($ans !== null && $ans !== '' && $ans !== []) {
                                if (is_array($ans)) {
                                    if ($q['question_type'] === 'grid_multiple' || $q['question_type'] === 'grid_checkbox') {
                                        $gridText = [];
                                        if (isset($ans['rows'])) {
                                            foreach ($ans['rows'] as $r => $c) {
                                                $colVal = is_array($c) ? implode(', ', $c) : $c;
                                                $gridText[] = esc($r) . ': ' . esc($colVal);
                                            }
                                        }
                                        $ansText = implode('; ', $gridText);
                                    } else {
                                        $ansText = esc(implode(', ', $ans));
                                    }
                                } else {
                                    $ansVal = (string)$ans;
                                    if ($q['question_type'] === 'master_tpq' && isset($tpqNames[$ansVal])) {
                                        $ansText = esc($tpqNames[$ansVal]);
                                    } elseif ($q['question_type'] === 'master_guru' && isset($guruNames[$ansVal])) {
                                        $ansText = esc($guruNames[$ansVal]);
                                    } elseif ($q['question_type'] === 'master_santri' && isset($santriNames[$ansVal])) {
                                        $ansText = esc($santriNames[$ansVal]);
                                    } elseif ($q['question_type'] === 'file_upload') {
                                        $fileData = is_string($ans) ? json_decode($ans, true) : $ans;
                                        if (is_array($fileData) && isset($fileData['file_path'])) {
                                            $downloadUrl = base_url($fileData['file_path']);
                                            $ansText = '<a href="' . $downloadUrl . '" target="_blank" class="badge badge-info py-1 px-2" data-export-text="' . esc($fileData['file_name']) . '"><i class="fas fa-download mr-1"></i> ' . esc($fileData['file_name']) . '</a>';
                                        } else {
                                            $ansText = esc((string)$ans);
                                        }
                                    } else {
                                        $ansText = esc($ansVal);
                                    }
                                }
                            }
                            echo $ansText;
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#dynamicTable')) {
        $('#dynamicTable').DataTable().destroy();
    }

    const table = $('#dynamicTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "scrollX": true,
        "dom": "<'row'<'col-sm-12 col-md-6 d-flex align-items-center'lB><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "buttons": [
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns mr-1"></i> Pilih Kolom',
                className: 'btn-sm btn-info mr-2 shadow-xs'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="far fa-file-excel mr-1"></i> Ekspor Excel',
                className: 'btn-sm btn-success shadow-xs',
                title: 'Hasil Analisis Survei - <?= esc($survey['title']) ?>',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            const expText = $(node).find('[data-export-text]').attr('data-export-text');
                            if (expText !== undefined) {
                                return expText;
                            }
                            // Strip HTML tags for clean export
                            const temp = document.createElement("div");
                            temp.innerHTML = data;
                            return temp.textContent || temp.innerText || data;
                        }
                    }
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Move search box styling to match AdminLTE standards
    $('#dynamicTable_filter input').addClass('form-control-sm');
    $('#dynamicTable_length select').addClass('form-control-sm mr-2');

    // Custom filtering
    $('#btn-apply-dynamic-filter').on('click', function() {
        const tpqId = $('#filter-dynamic-tpq').val();
        const url = `<?= base_url("backend/survey/results/dynamic-table/{$survey['id']}") ?>?tpq_id=${tpqId}`;
        $('#tab-dynamic').html(`
            <div class="text-center py-5 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Menerapkan filter...</div>
            </div>
        `).load(url);
    });
});
</script>
