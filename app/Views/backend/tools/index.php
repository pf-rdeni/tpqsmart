<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tools Setting</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/tools/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped" id="toolsTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID TPQ</th>
                                <th>Setting Key</th>
                                <th>Setting Value</th>
                                <th>Setting Type</th>
                                <th>Description</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($tools as $tool) : ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $tool['IdTpq'] ?></td>
                                    <td><?= $tool['SettingKey'] ?></td>
                                    <td><?= $tool['SettingValue'] ?></td>
                                    <td><?= $tool['SettingType'] ?></td>
                                    <td><?= $tool['Description'] ?></td>
                                    <td>
                                        <a href="<?= base_url('backend/tools/edit/' . $tool['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('backend/tools/delete/' . $tool['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>