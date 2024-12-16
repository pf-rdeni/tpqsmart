<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kelas Materi Pelajaran</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('kelasMateriPelajaran/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="kelasTab" role="tablist">
                        <?php foreach ($materi_per_kelas as $kelasId => $kelas): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $kelasId === array_key_first($materi_per_kelas) ? 'active' : '' ?>"
                                    id="tab-<?= $kelasId ?>"
                                    data-toggle="tab"
                                    href="#kelas-<?= $kelasId ?>"
                                    role="tab"
                                    aria-controls="kelas-<?= $kelasId ?>"
                                    aria-selected="<?= $kelasId === array_key_first($materi_per_kelas) ? 'true' : 'false' ?>">
                                    <?= $kelas['nama_kelas'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="tab-content" id="kelasTabContent">
                        <?php foreach ($materi_per_kelas as $kelasId => $kelas): ?>
                            <div class="tab-pane fade <?= $kelasId === array_key_first($materi_per_kelas) ? 'show active' : '' ?>"
                                id="kelas-<?= $kelasId ?>"
                                role="tabpanel"
                                aria-labelledby="tab-<?= $kelasId ?>">
                                <div class="d-flex align-items-center">
                                    <h3 class="mr-2">Kelas: <?= $kelas['nama_kelas'] ?></h3>
                                    <?php if (!empty($kelas['nama_tpq'])): ?>
                                        <h4>TPQ <?= $kelas['nama_tpq'] ?></h4>
                                    <?php endif; ?>
                                </div>
                                <table class="table table-bordered table-striped" id="table-kelas-<?= $kelasId ?> ">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>IdMateri</th>
                                            <th>Kategori</th>
                                            <th>Materi</th>
                                            <th>Kurikulum</th>
                                            <th>S.Ganjil</th>
                                            <th>S.Genap</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $grouped_materi = [];

                                        // Mengelompokkan materi berdasarkan IdMateri
                                        foreach ($kelas['materi'] as $item) {
                                            $id = $item['IdMateri'];
                                            if (!isset($grouped_materi[$id])) {
                                                $grouped_materi[$id] = [
                                                    'IdMateri' => $item['IdMateri'],
                                                    'Kategori' => $item['Kategori'],
                                                    'NamaMateri' => $item['NamaMateri'],
                                                    'NamaTahunAjaran' => $item['NamaTahunAjaran'],
                                                    'Semester1' => false,
                                                    'Semester2' => false
                                                ];
                                            }
                                            if ($item['Semester'] == 1) {
                                                $grouped_materi[$id]['Semester1'] = true;
                                            }
                                            if ($item['Semester'] == 2) {
                                                $grouped_materi[$id]['Semester2'] = true;
                                            }
                                        }

                                        foreach ($grouped_materi as $materi): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $materi['IdMateri'] ?></td>
                                                <td><?= $materi['Kategori'] ?></td>
                                                <td><?= $materi['NamaMateri'] ?></td>
                                                <td><?= $materi['NamaTahunAjaran'] ?></td>
                                                <td>
                                                    <input type="checkbox"
                                                        <?= $materi['Semester1'] ? 'checked' : '' ?>>
                                                </td>
                                                <td>
                                                    <input type="checkbox"
                                                        <?= $materi['Semester2'] ? 'checked' : '' ?>>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('kelasMateriPelajaran/delete/' . $materi['IdMateri']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>IdMateri</th>
                                            <th>Kategori</th>
                                            <th>Materi</th>
                                            <th>Kurikulum</th>
                                            <th>S.Ganjil</th>
                                            <th>S.Genap</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    <?php foreach ($materi_per_kelas as $kelasId => $kelas): ?>
        initializeDataTableUmum("#table-kelas-<?= $kelasId ?>", true);
    <?php endforeach; ?>
</script>
<?= $this->endSection() ?>