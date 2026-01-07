<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?= isset($kegiatan) ? 'Edit' : 'Tambah' ?> Kegiatan</h3>
                </div>
                <!-- Form start -->
                <?php 
                    $actionUrl = isset($kegiatan) ? base_url('backend/kegiatan-absensi/' . $kegiatan['Id']) : base_url('backend/kegiatan-absensi');
                ?>
                <form action="<?= $actionUrl ?>" method="POST">
                    <?= csrf_field() ?>
                    <?php if (isset($kegiatan)) : ?>
                        <input type="hidden" name="_method" value="PUT">
                    <?php endif; ?>
                    
                    <div class="card-body">
                         <?php if (session()->getFlashdata('errors')) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Nama Kegiatan</label>
                            <input type="text" class="form-control" name="NamaKegiatan" value="<?= isset($kegiatan) ? $kegiatan['NamaKegiatan'] : old('NamaKegiatan') ?>" placeholder="Contoh: Rapat Bulanan, Halal Bihalal" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" class="form-control" name="Tanggal" value="<?= isset($kegiatan) ? $kegiatan['Tanggal'] : (old('Tanggal') ? old('Tanggal') : date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam Mulai</label>
                                    <input type="time" class="form-control" name="JamMulai" value="<?= isset($kegiatan) ? date('H:i', strtotime($kegiatan['JamMulai'])) : old('JamMulai') ?>" required>
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jam Selesai</label>
                                    <input type="time" class="form-control" name="JamSelesai" value="<?= isset($kegiatan) ? date('H:i', strtotime($kegiatan['JamSelesai'])) : old('JamSelesai') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Lingkup</label>
                            <select class="form-control" name="Lingkup" id="selectLingkup" <?= isset($kegiatan) ? 'disabled' : '' ?>>
                                <option value="Umum" <?= (isset($kegiatan) && $kegiatan['Lingkup'] == 'Umum') || old('Lingkup') == 'Umum' ? 'selected' : '' ?>>Umum (Semua Guru)</option>
                                <option value="TPQ" <?= (isset($kegiatan) && $kegiatan['Lingkup'] == 'TPQ') || old('Lingkup') == 'TPQ' ? 'selected' : '' ?>>TPQ Tertentu</option>
                            </select>
                            <?php if (isset($kegiatan)) : ?>
                                <input type="hidden" name="Lingkup" value="<?= $kegiatan['Lingkup'] ?>">
                            <?php endif; ?>
                        </div>

                        <!-- TPQ Selection only for Admin. Operator auto-assigned. -->
                        <?php if (in_groups('Admin')): ?>
                        <div class="form-group" id="groupTpq" style="display: none;">
                            <label>Pilih TPQ</label>
                            <select class="form-control select2" name="IdTpq" <?= isset($kegiatan) ? 'disabled' : '' ?>>
                                <option value="">-- Pilih TPQ --</option>
                                <?php foreach($tpq_list as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>" <?= (isset($kegiatan) && $kegiatan['IdTpq'] == $tpq['IdTpq']) || old('IdTpq') == $tpq['IdTpq'] ? 'selected' : '' ?>>
                                        <?= $tpq['NamaTpq'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('backend/kegiatan-absensi') ?>" class="btn btn-default">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

    </section>

<script>
    $(document).ready(function() {
        function toggleTpq() {
            var scope = $('#selectLingkup').val();
            if (scope === 'TPQ') {
                $('#groupTpq').show();
            } else {
                $('#groupTpq').hide();
            }
        }
        
        $('#selectLingkup').change(toggleTpq);
        toggleTpq(); // Run on init
        
        // Initialize Select2 if available
        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        }
    });
</script>
<?= $this->endSection(); ?>
