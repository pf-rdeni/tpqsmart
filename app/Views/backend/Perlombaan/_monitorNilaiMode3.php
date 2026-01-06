<!-- MODE 3: Multiple Juris, Different Kriteria per Juri -->
<?php 
// Build mapping: kriteria_id => juri info
$kriteriaJuriMap = [];
foreach ($juri_list as $juri) {
    $juriKriterias = $kriteria_by_juri[$juri['IdJuri']] ?? [];
    foreach ($juriKriterias as $kritId) {
        $kriteriaJuriMap[$kritId] = $juri['UsernameJuri'] ?? $juri['NamaJuri'] ?? $juri['IdJuri'];
    }
}
?>
<table class="table table-bordered table-striped table-sm" id="tableNilai">
    <thead>
        <tr class="bg-secondary">
            <th rowspan="2" class="align-middle text-center">No</th>
            <th rowspan="2" class="align-middle text-center">Status</th>
            <th rowspan="2" class="align-middle text-center">No Peserta</th>
            <th rowspan="2" class="align-middle">Nama</th>
            <th rowspan="2" class="align-middle">TPQ</th>
            <?php foreach ($kriteria_list as $k): ?>
                <th colspan="2" class="text-center">
                    <?= esc($k['NamaKriteria']) ?><br>
                    <small>(<?= $k['Bobot'] ?>%)</small>
                </th>
            <?php endforeach; ?>
            <th rowspan="2" class="align-middle text-center bg-info text-white">Total<br>Nilai</th>
            <th rowspan="2" class="align-middle text-center bg-success text-white">Total<br>Bobot</th>
        </tr>
        <tr class="bg-light">
            <?php foreach ($kriteria_list as $k): 
                $juriName = $kriteriaJuriMap[$k['id']] ?? '-';
            ?>
                <th class="text-center">Nilai<br><small class="badge badge-info"><?= esc($juriName) ?></small></th>
                <th class="text-center">Bobot</th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; // Initialize $no for the new incrementing behavior
        foreach ($nilai_data as $i => $row): 
            $totalNilai = 0;
            $totalBobot = 0;
        ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= $row['status_label'] ?></td>
                <td><code><?= esc($row['NoPeserta']) ?></code></td>
                <td><?= esc($row['NamaSantri']) ?></td>
                <td><?= esc($row['NamaTpq']) ?></td>
                <?php foreach ($kriteria_list as $k): 
                    $kriteriaData = isset($row['nilai'][$k['id']]) ? $row['nilai'][$k['id']] : null;
                    $nilai = $kriteriaData ? $kriteriaData['nilai'] : 0;
                    $bobot = $nilai * ($k['Bobot'] / 100);
                    $totalNilai += $nilai;
                    $totalBobot += $bobot;
                ?>
                    <td class="text-center">
                        <?php if ($kriteriaData): ?>
                            <strong><?= number_format($nilai, 2) ?></strong>
                        <?php else: ?>
                            <span class="badge badge-danger" title="Sedang dinilai..."><i class="fas fa-spinner fa-spin"></i></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-muted">
                        <em><?= number_format($bobot, 2) ?></em>
                    </td>
                <?php endforeach; ?>
                <td class="text-center bg-info text-white"><strong><?= number_format($row['total_raw'], 2) ?></strong></td>
                <td class="text-center bg-success text-white"><strong><?= number_format($row['total'], 2) ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
