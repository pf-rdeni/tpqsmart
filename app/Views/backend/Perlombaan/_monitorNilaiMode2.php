<!-- MODE 2: Multiple Juris, Same Kriteria - With Rata-rata -->
<table class="table table-bordered table-striped table-sm" id="tableNilai">
    <thead>
        <tr class="bg-secondary">
            <th rowspan="2" class="align-middle text-center">No</th>
            <th rowspan="2" class="align-middle text-center">Status</th>
            <th rowspan="2" class="align-middle text-center">No Peserta</th>
            <th rowspan="2" class="align-middle">Nama</th>
            <th rowspan="2" class="align-middle">TPQ</th>
            <th rowspan="2" class="align-middle">Juri</th>
            <?php foreach ($kriteria_list as $k): ?>
                <th colspan="2" class="text-center"><?= esc($k['NamaKriteria']) ?><br><small>(<?= $k['Bobot'] ?>%)</small></th>
            <?php endforeach; ?>
            <th rowspan="2" class="align-middle text-center bg-info text-white">Total<br>Nilai</th>
            <th rowspan="2" class="align-middle text-center bg-success text-white">Total<br>Bobot</th>
            <th rowspan="2" class="align-middle text-center bg-primary text-white">Rata-rata<br>Nilai</th>
            <th rowspan="2" class="align-middle text-center bg-dark text-white">Nilai<br>Bobot</th>
        </tr>
        <tr class="bg-light">
            <?php foreach ($kriteria_list as $k): ?>
                <th class="text-center">Nilai</th>
                <th class="text-center">Bobot</th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach ($nilai_data as $i => $row): 
            $isFirstRow = isset($row['rowspan']) && $row['rowspan'] > 0;
            $totalNilai = 0;
            $totalBobot = 0;
        ?>
            <tr>
                <?php if ($isFirstRow): ?>
                    <td class="text-center align-middle" rowspan="<?= $row['rowspan'] ?>"><?= $no++ ?></td>
                    <td class="text-center align-middle" rowspan="<?= $row['rowspan'] ?>"><?= $row['status_label'] ?></td>
                    <td class="align-middle" rowspan="<?= $row['rowspan'] ?>"><code><?= esc($row['NoPeserta']) ?></code></td>
                    <td class="align-middle" rowspan="<?= $row['rowspan'] ?>"><?= esc($row['NamaSantri']) ?></td>
                    <td class="align-middle" rowspan="<?= $row['rowspan'] ?>"><?= esc($row['NamaTpq']) ?></td>
                <?php endif; ?>
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
                <td class="text-center bg-info text-white"><strong><?= number_format($totalNilai, 2) ?></strong></td>
                <td class="text-center bg-success text-white"><strong><?= number_format($totalBobot, 2) ?></strong></td>
                <?php if ($isFirstRow): ?>
                    <td class="text-center align-middle bg-primary text-white" rowspan="<?= $row['rowspan'] ?>">
                        <strong><?= number_format($row['total_nilai_rata'], 2) ?></strong>
                    </td>
                    <td class="text-center align-middle bg-dark text-white" rowspan="<?= $row['rowspan'] ?>">
                        <strong style="font-size: 1.2em;"><?= number_format($row['rata_rata'], 2) ?></strong>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
