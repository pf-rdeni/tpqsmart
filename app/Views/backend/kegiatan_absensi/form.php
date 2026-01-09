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

                        <div class="form-group">
                            <label>Tempat</label>
                            <input type="text" class="form-control" name="Tempat" value="<?= isset($kegiatan) ? $kegiatan['Tempat'] : old('Tempat') ?>" placeholder="Contoh: Aula Utama, TPQ Al-Hidayah" required>
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
                            <label>Detail / Keterangan</label>
                            <textarea class="form-control" name="Detail" rows="3" placeholder="Masukkan detail kegiatan..."><?= isset($kegiatan) ? $kegiatan['Detail'] : old('Detail') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Jenis Jadwal</label>
                            <select class="form-control" name="JenisJadwal" id="jenisJadwal">
                                <option value="sekali" <?= (isset($kegiatan) && $kegiatan['JenisJadwal'] == 'sekali') || old('JenisJadwal') == 'sekali' || !isset($kegiatan) ? 'selected' : '' ?>>Sekali</option>
                                <option value="harian" <?= (isset($kegiatan) && $kegiatan['JenisJadwal'] == 'harian') || old('JenisJadwal') == 'harian' ? 'selected' : '' ?>>Harian</option>
                                <option value="mingguan" <?= (isset($kegiatan) && $kegiatan['JenisJadwal'] == 'mingguan') || old('JenisJadwal') == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
                                <option value="bulanan" <?= (isset($kegiatan) && $kegiatan['JenisJadwal'] == 'bulanan') || old('JenisJadwal') == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                            </select>
                            <small class="text-muted">Pilih "Sekali" untuk kegiatan satu kali, atau pilih pola rutin.</small>
                        </div>

                        <!-- Conditional fields for recurring schedules -->
                        <div id="rutinFields" style="display: none;">
                            <div class="form-group">
                                <label>Periode Rutin</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="small">Tanggal Mulai</label>
                                        <input type="date" name="TanggalMulaiRutin" id="tanggalMulaiRutin" class="form-control" value="<?= isset($kegiatan) ? $kegiatan['TanggalMulaiRutin'] : old('TanggalMulaiRutin') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small">Tanggal Akhir <span class="text-muted">(Opsional)</span></label>
                                        <input type="date" name="TanggalAkhirRutin" id="tanggalAkhirRutin" class="form-control" value="<?= isset($kegiatan) ? $kegiatan['TanggalAkhirRutin'] : old('TanggalAkhirRutin') ?>">
                                    </div>
                                </div>
                                <small class="text-muted">Tentukan periode jadwal rutin. Kosongkan tanggal akhir jika tidak ada batas waktu.</small>
                            </div>
                            
                            <!-- Weekly: Day selector -->
                            <div id="weeklyField" style="display: none;">
                                <div class="form-group">
                                    <label>Hari dalam Minggu</label>
                                    <select class="form-control" name="HariDalamMinggu" id="hariDalamMinggu">
                                        <option value="1" <?= (isset($kegiatan) && $kegiatan['HariDalamMinggu'] == 1) || old('HariDalamMinggu') == 1 ? 'selected' : '' ?>>Senin</option>
                                        <option value="2" <?= (isset($kegiatan) && $kegiatan['HariDalamMinggu'] == 2) || old('HariDalamMinggu') == 2 ? 'selected' : '' ?>>Selasa</option>
                                        <option value="3" <?= (isset($kegiatan) && $kegiatan['HariDalamMinggu'] == 3) || old('HariDalamMinggu') == 3 ? 'selected' : '' ?>>Rabu</option>
                                        <option value="4" <?= (isset($kegiatan) && $kegiatan['HariDalamMinggu'] == 4) || old('HariDalamMinggu') == 4 ? 'selected' : '' ?>>Kamis</option>
                                        <option value="5" <?= (isset($kegiatan) && $kegiatan['HariDalamMinggu'] == 5) || old('HariDalamMinggu') == 5 ? 'selected' : '' ?>>Jumat</option>
                                        <option value="6" <?= (isset($kegiatan) && $kegiatan['HariDalamMinggu'] == 6) || old('HariDalamMinggu') == 6 ? 'selected' : '' ?>>Sabtu</option>
                                        <option value="7" <?= (isset($kegiatan) && $kegiatan['HariDalamMinggu'] == 7) || old('HariDalamMinggu') == 7 ? 'selected' : '' ?>>Minggu</option>
                                    </select>
                                    <small class="text-muted">Pilih hari dalam minggu untuk jadwal mingguan.</small>
                                </div>
                            </div>
                            
                            <!-- Monthly: Date selector -->
                            <div id="monthlyField" style="display: none;">
                                <div class="form-group">
                                    <label>Tanggal dalam Bulan</label>
                                    <input type="number" name="TanggalDalamBulan" id="tanggalDalamBulan" class="form-control" min="1" max="31" value="<?= isset($kegiatan) ? $kegiatan['TanggalDalamBulan'] : old('TanggalDalamBulan') ?>">
                                    <small class="text-muted">Pilih tanggal (1-31) untuk jadwal bulanan.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Lingkup (Peserta)</label>
                            <?php 
                                $activeRole = session()->get('active_role');
                                $idTpqSession = session()->get('IdTpq');
                                $isOperator = ($activeRole == 'operator' && !in_groups('Admin'));
                                $isGuruOnly = ($isGuru ?? false); // From controller
                                
                                if (($isOperator || $isGuruOnly) && !empty($idTpqSession)) {
                                    // Find TPQ Name from the list
                                    $tpqName = 'TPQ Anda';
                                    if (!empty($tpq_list)) {
                                        foreach ($tpq_list as $t) {
                                            if ($t['IdTpq'] == $idTpqSession) {
                                                $tpqName = $t['NamaTpq'] . (!empty($t['NamaKelDesa']) ? ' - ' . $t['NamaKelDesa'] : '');
                                                break;
                                            }
                                        }
                                    }
                            ?>
                                <!-- Operator/Guru View: Fixed to their TPQ -->
                                <input type="text" class="form-control" value="<?= $tpqName ?>" readonly>
                                <input type="hidden" name="LingkupSelect" value="<?= $idTpqSession ?>">
                                <small class="text-muted">Kegiatan ini otomatis dikhususkan untuk TPQ Anda.</small>
                            
                            <?php } else { ?>
                                <!-- Admin View: Full Selection -->
                                <select class="form-control select2" name="LingkupSelect" <?= isset($kegiatan) ? 'disabled' : '' ?>>
                                    <option value="Umum" <?= (isset($kegiatan) && $kegiatan['Lingkup'] == 'Umum') || old('LingkupSelect') == 'Umum' ? 'selected' : '' ?>>Umum (Semua Guru)</option>
                                    <?php if (!empty($tpq_list)): ?>
                                        <optgroup label="TPQ">
                                            <?php foreach ($tpq_list as $tpq): ?>
                                                <?php 
                                                    $isSelected = (isset($kegiatan) && $kegiatan['Lingkup'] == 'TPQ' && $kegiatan['IdTpq'] == $tpq['IdTpq']) || old('LingkupSelect') == $tpq['IdTpq'];
                                                    $label = $tpq['NamaTpq'];
                                                    if (!empty($tpq['NamaKelDesa'])) {
                                                        $label .= ' - ' . $tpq['NamaKelDesa'];
                                                    } elseif (isset($tpq['KelurahanDesa'])) { 
                                                        $label .= ' - ' . $tpq['KelurahanDesa'];
                                                    }
                                                ?>
                                                <option value="<?= $tpq['IdTpq'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                                <?php if (isset($kegiatan)) : ?>
                                    <input type="hidden" name="LingkupSelect" value="<?= $kegiatan['Lingkup'] == 'Umum' ? 'Umum' : $kegiatan['IdTpq'] ?>">
                                <?php endif; ?>
                                <small class="text-muted">Pilih "Umum" untuk semua guru, atau pilih TPQ spesifik.</small>
                            <?php } ?>
                        </div>

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
        // Initialize Select2 if available
        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        }

        // Conditional display for recurring schedule fields
        function toggleRecurringFields() {
            const jenisJadwal = $('#jenisJadwal').val();
            
            if (jenisJadwal === 'sekali') {
                $('#rutinFields').hide();
                $('#weeklyField').hide();
                $('#monthlyField').hide();
            } else {
                $('#rutinFields').show();
                
                // Show/hide specific fields based on type
                if (jenisJadwal === 'mingguan') {
                    $('#weeklyField').show();
                    $('#monthlyField').hide();
                } else if (jenisJadwal === 'bulanan') {
                    $('#weeklyField').hide();
                    $('#monthlyField').show();
                } else if (jenisJadwal === 'harian') {
                    $('#weeklyField').hide();
                    $('#monthlyField').hide();
                }
            }
        }

        // Initialize on page load
        toggleRecurringFields();

        // Toggle on change
        $('#jenisJadwal').on('change', toggleRecurringFields);
    });
</script>
<?= $this->endSection(); ?>
