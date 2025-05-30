<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Tools Setting</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('backend/tools/update/' . $tool['id']) ?>" method="post">
                        <div class="form-group">
                            <label for="IdTpq">ID TPQ</label>
                            <input type="number" class="form-control" id="IdTpq" name="IdTpq" value="<?= old('IdTpq', $tool['IdTpq']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="SettingKey">Setting Key</label>
                            <input type="text" class="form-control" id="SettingKey" name="SettingKey" value="<?= old('SettingKey', $tool['SettingKey']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="SettingValue">Setting Value</label>
                            <input type="text" class="form-control" id="SettingValue" name="SettingValue" value="<?= old('SettingValue', $tool['SettingValue']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="SettingType">Setting Type</label>
                            <select class="form-control" id="SettingType" name="SettingType" required>
                                <option value="">Pilih Tipe</option>
                                <option value="text" <?= old('SettingType', $tool['SettingType']) == 'text' ? 'selected' : '' ?>>Text</option>
                                <option value="number" <?= old('SettingType', $tool['SettingType']) == 'number' ? 'selected' : '' ?>>Number</option>
                                <option value="boolean" <?= old('SettingType', $tool['SettingType']) == 'boolean' ? 'selected' : '' ?>>Boolean</option>
                                <option value="json" <?= old('SettingType', $tool['SettingType']) == 'json' ? 'selected' : '' ?>>JSON</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="Description">Description</label>
                            <textarea class="form-control" id="Description" name="Description" rows="3"><?= old('Description', $tool['Description']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="<?= base_url('backend/tools') ?>" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>