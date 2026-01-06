<!-- MODE 1: Single Juri, All Kriteria -->
<table class="table table-bordered table-striped table-sm" id="tableNilai">
    <thead>
        <tr class="bg-secondary">
            <th rowspan="2" class="align-middle text-center">No</th>
            <th rowspan="2" class="align-middle text-center">No Peserta</th>
            <th rowspan="2" class="align-middle">Nama</th>
            <th rowspan="2" class="align-middle">TPQ</th>
            <th rowspan="2" class="align-middle">Juri</th>
            <?php foreach ($kriteria_list as $k): ?>
                <th colspan="2" class="text-center"><?= esc($k['NamaKriteria']) ?><br><small>(<?= $k['Bobot'] ?>%)</small></th>
            <?php endforeach; ?>
            <th rowspan="2" class="align-middle text-center bg-info text-white">Total<br>Nilai</th>
            <th rowspan="2" class="align-middle text-center bg-success text-white">Total<br>Bobot</th>
        </tr>
        <tr class="bg-light">
            <?php foreach ($kriteria_list as $k): ?>
                <th class="text-center">Nilai</th>
                <th class="text-center">Bobot</th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nilai_data as $i => $row): 
            $totalNilai = 0;
            $totalBobot = 0;
        ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><code><?= esc($row['NoPeserta']) ?></code></td>
                <td><?= esc($row['NamaSantri']) ?></td>
                <td><?= esc($row['NamaTpq']) ?></td>
                <td><span class="badge badge-info"><?= esc($row['NamaJuri']) ?></span></td>
                <?php foreach ($kriteria_list as $k): 
                    $hasNilai = isset($row['nilai'][$k['id']]);
                    $nilai = $hasNilai ? $row['nilai'][$k['id']] : 0;
                    $bobot = $nilai * ($k['Bobot'] / 100);
                    $totalNilai += $nilai;
                    $totalBobot += $bobot;
                ?>
                    <td class="text-center">
                        <?php if ($hasNilai): ?>
                            <?= number_format($nilai, 2) ?>
                        <?php else: ?>
                            <span class="badge badge-danger" title="Sedang dinilai..."><i class="fas fa-spinner fa-spin"></i></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-muted"><em><?= number_format($bobot, 2) ?></em></td>
                <?php endforeach; ?>
                <td class="text-center bg-info text-white"><strong><?= number_format($row['total_raw'] ?? $totalNilai, 2) ?></strong></td>
                <td class="text-center bg-success text-white"><strong><?= number_format($row['total'], 2) ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
