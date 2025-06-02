<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header Rapor -->
                    <div class="text-center mb-4">
                        <h4>RAPOR SANTRI</h4>
                        <h5><?= $tpq['NamaTpq'] ?></h5>
                        <p>Tahun Ajaran <?= $tahunAjaran ?></p>
                    </div>

                    <!-- Data Santri -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150">Nama Santri</td>
                                    <td>: <?= $santri['NamaSantri'] ?></td>
                                </tr>
                                <tr>
                                    <td>NIS</td>
                                    <td>: <?= $santri['IdSantri'] ?></td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td>: <?= $santri['IdKelas'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Nilai Semester -->
                    <h5 class="mb-3">Nilai Semester <?= $semester ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Materi</th>
                                    <th>Kategori</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($nilai as $n) :
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $n->NamaMateri ?></td>
                                        <td><?= $n->Kategori ?></td>
                                        <td><?= $n->Nilai ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>