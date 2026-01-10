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
                <form action="<?= $actionUrl ?>" method="post" onsubmit="syncState(); return true;">
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

                        <!-- Jenis Pelaksanaan (Toggle Utama) -->
                        <div class="form-group bg-light p-3 rounded border">
                            <label class="d-block mb-3">Jenis Pelaksanaan:</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="polaSekali" name="PolaJadwalUI" class="custom-control-input" value="sekali" checked>
                                <label class="custom-control-label" for="polaSekali">Sekali Saja</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="polaRutin" name="PolaJadwalUI" class="custom-control-input" value="rutin">
                                <label class="custom-control-label" for="polaRutin">Rutin (Berulang)</label>
                            </div>
                        </div>

                        <!-- Recurrence Container (Hidden by default) -->
                        <div id="recurrenceContainer" style="display:none;" class="mb-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-sync-alt mr-2"></i> Pola Jadwal Rutin</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Left Tabs -->
                                        <div class="col-md-3 border-right">
                                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                                                <a class="nav-link active" id="tab-harian" href="#p-harian">Harian</a>
                                                <a class="nav-link" id="tab-mingguan" href="#p-mingguan">Mingguan</a>
                                                <a class="nav-link" id="tab-bulanan" href="#p-bulanan">Bulanan</a>
                                                <a class="nav-link" id="tab-tahunan" href="#p-tahunan">Tahunan</a>
                                            </div>
                                        </div>
                                        
                                        <!-- Right Content -->
                                        <div class="col-md-9 pt-2">
                                            <div class="tab-content" id="v-pills-tabContent">
                                                <?php 
                                                    $days = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 7=>'Minggu']; 
                                                    $positions = [1=>'Pertama', 2=>'Kedua', 3=>'Ketiga', 4=>'Keempat', 5=>'Terakhir'];
                                                ?>

                                                <!-- HARIAN -->
                                                <div class="tab-pane active" id="p-harian">
                                                    <div class="form-group row">
                                                        <div class="col-12">
                                                            <div class="custom-control custom-radio mb-2">
                                                                <input class="custom-control-input" type="radio" id="harianOpt1" name="harianOption" value="Interval" <?= ($kegiatan['OpsiPola'] ?? 'Interval') == 'Interval' ? 'checked' : '' ?>>
                                                                <label class="custom-control-label d-inline-flex" for="harianOpt1">
                                                                    Setiap &nbsp; <input type="number" class="form-control form-control-sm mx-1" name="Interval_Harian" value="<?= $kegiatan['Interval'] ?? 1 ?>" min="1" style="width:60px;"> &nbsp; hari
                                                                </label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input class="custom-control-input" type="radio" id="harianOpt2" name="harianOption" value="Weekday" <?= ($kegiatan['OpsiPola'] ?? '') == 'Weekday' ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" for="harianOpt2">Setiap hari kerja (Senin-Jumat)</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- MINGGUAN -->
                                                <div class="tab-pane" id="p-mingguan">
                                                    <div class="form-group row align-items-center mb-3">
                                                        <label class="col-auto">Ulangi setiap</label>
                                                        <div class="col-auto">
                                                            <input type="number" class="form-control form-control-sm" name="Interval_Mingguan" value="<?= $kegiatan['Interval'] ?? 1 ?>" min="1" style="width:60px;">
                                                        </div>
                                                        <label class="col-auto">minggu pada:</label>
                                                    </div>
                                                        <div class="row">
                                                            <?php 
                                                                $rawDays = $kegiatan['HariDalamMinggu'] ?? '';
                                                            $savedDays = is_array($rawDays) ? $rawDays : explode(',', $rawDays);
                                                            foreach($days as $idx => $dayName): 
                                                                $isChecked = in_array((string)$idx, $savedDays) ? 'checked' : '';
                                                        ?>
                                                        <div class="col-md-3 col-6 mb-2">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="checkDay<?= $idx ?>" name="HariDalamMinggu[]" value="<?= $idx ?>" <?= $isChecked ?>>
                                                                <label class="custom-control-label" for="checkDay<?= $idx ?>"><?= $dayName ?></label>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- BULANAN -->
                                                <div class="tab-pane" id="p-bulanan">
                                                        <!-- Option 1: On Date -->
                                                        <div class="col-12 mb-2">
                                                            <div class="custom-control custom-radio">
                                                                <input class="custom-control-input" type="radio" id="bulananOpt1" name="bulananOption" value="Tanggal" <?= ($kegiatan['OpsiPola'] ?? 'Tanggal') == 'Tanggal' ? 'checked' : '' ?>>
                                                                <label class="custom-control-label d-inline-flex align-items-center" for="bulananOpt1">
                                                                    Tanggal &nbsp; <input type="number" class="form-control form-control-sm mx-1" name="Tangal_Bulanan" value="<?= $kegiatan['TanggalDalamBulan'] ?? 1 ?>" min="1" max="31" style="width:60px;"> &nbsp; setiap &nbsp; <input type="number" class="form-control form-control-sm mx-1" name="Interval_Bulanan1" value="<?= $kegiatan['Interval'] ?? 1 ?>" min="1" style="width:60px;"> &nbsp; bulan
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <!-- Option 2: Nth Day -->
                                                        <div class="col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input class="custom-control-input" type="radio" id="bulananOpt2" name="bulananOption" value="HariKe" <?= ($kegiatan['OpsiPola'] ?? '') == 'HariKe' ? 'checked' : '' ?>>
                                                                <label class="custom-control-label d-inline-flex align-items-center" for="bulananOpt2">
                                                                    Hari &nbsp;
                                                                    <select class="form-control form-control-sm mx-1" name="Posisi_Bulanan" style="width:100px;">
                                                                        <?php 
                                                                            $pos = $kegiatan['PosisiMinggu'] ?? 1;
                                                                            foreach($positions as $val => $label) {
                                                                                echo '<option value="'.$val.'" '.($pos == $val ? 'selected' : '').'>'.$label.'</option>';
                                                                            }
                                                                        ?>
                                                                    </select>
                                                                    &nbsp;
                                                                    <select class="form-control form-control-sm mx-1" name="HariTarget_Bulanan" style="width:110px;">
                                                                        <?php 
                                                                            $targetDay = $kegiatan['HariDalamMinggu'] ?? 1;
                                                                            // Handle if it's an array (from explode)
                                                                            if (is_array($targetDay)) {
                                                                                $targetDay = reset($targetDay); // Get first element
                                                                            }
                                                                            foreach($days as $idx => $dayName): 
                                                                        ?>
                                                                        <option value="<?= $idx ?>" <?= ((int)$targetDay === (int)$idx) ? 'selected' : '' ?>><?= $dayName ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    &nbsp; setiap &nbsp; <input type="number" class="form-control form-control-sm mx-1" name="Interval_Bulanan2" value="<?= $kegiatan['Interval'] ?? 1 ?>" min="1" style="width:60px;"> &nbsp; bulan
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- TAHUNAN -->
                                                <div class="tab-pane" id="p-tahunan">
                                                    <div class="form-group row align-items-center mb-3">
                                                        <label class="col-auto">Ulangi setiap</label>
                                                        <div class="col-auto">
                                                            <input type="number" class="form-control form-control-sm" name="Interval_Tahunan" value="<?= $kegiatan['Interval'] ?? 1 ?>" min="1" style="width:60px;">
                                                        </div>
                                                        <label class="col-auto">tahun</label>
                                                    </div>
                                                    <!-- Option 1: On Date -->
                                                    <div class="form-group row">
                                                        <div class="col-12 mb-2">
                                                            <div class="custom-control custom-radio">
                                                                <input class="custom-control-input" type="radio" id="tahunanOpt1" name="tahunanOption" value="Tanggal" <?= ($kegiatan['OpsiPola'] ?? 'Tanggal') == 'Tanggal' ? 'checked' : '' ?>>
                                                                <label class="custom-control-label d-inline-flex align-items-center" for="tahunanOpt1">
                                                                    Pada: &nbsp; 
                                                                    <select class="form-control form-control-sm mx-1" name="Bulan_Tahunan1" style="width:120px;">
                                                                         <?php 
                                                                             $mth = $kegiatan['BulanTahun'] ?? 1;
                                                                             for($m=1; $m<=12; $m++): 
                                                                         ?>
                                                                         <option value="<?= $m ?>" <?= ($m == $mth) ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                                                                         <?php endfor; ?>
                                                                    </select>
                                                                    &nbsp; Tanggal: &nbsp; <input type="number" class="form-control form-control-sm mx-1" name="Tanggal_Tahunan" value="<?= $kegiatan['TanggalDalamBulan'] ?? 1 ?>" min="1" max="31" style="width:60px;">
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <!-- Option 2: Nth Day -->
                                                        <div class="col-12">
                                                            <div class="custom-control custom-radio">
                                                                <input class="custom-control-input" type="radio" id="tahunanOpt2" name="tahunanOption" value="HariKe" <?= ($kegiatan['OpsiPola'] ?? '') == 'HariKe' ? 'checked' : '' ?>>
                                                                <label class="custom-control-label d-inline-flex align-items-center" for="tahunanOpt2">
                                                                    Pada: &nbsp;
                                                                    <select class="form-control form-control-sm mx-1" name="Posisi_Tahunan" style="width:100px;">
                                                                        <?php 
                                                                            $pos = $kegiatan['PosisiMinggu'] ?? 1;
                                                                            foreach($positions as $val => $label) { // reuse positions from Monthly
                                                                                echo '<option value="'.$val.'" '.($pos == $val ? 'selected' : '').'>'.$label.'</option>';
                                                                            }
                                                                        ?>
                                                                    </select>
                                                                    &nbsp;
                                                                    <select class="form-control form-control-sm mx-1" name="HariTarget_Tahunan" style="width:110px;">
                                                                        <?php 
                                                                            $targetDay = $kegiatan['HariDalamMinggu'] ?? 1;
                                                                            if (is_array($targetDay)) {
                                                                                $targetDay = reset($targetDay);
                                                                            }
                                                                            foreach($days as $idx => $dayName): 
                                                                        ?>
                                                                        <option value="<?= $idx ?>" <?= ((int)$targetDay === (int)$idx) ? 'selected' : '' ?>><?= $dayName ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    &nbsp; dari &nbsp;
                                                                    <select class="form-control form-control-sm mx-1" name="Bulan_Tahunan2" style="width:120px;">
                                                                         <?php 
                                                                             $mth = $kegiatan['BulanTahun'] ?? 1;
                                                                             for($m=1; $m<=12; $m++): 
                                                                         ?>
                                                                         <option value="<?= $m ?>" <?= ($m == $mth) ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                                                                         <?php endfor; ?>
                                                                    </select>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <!-- Range Section -->
                                    <h6 class="font-weight-bold">Rentang Waktu</h6>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Mulai:</label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control form-control-sm" name="TanggalMulaiRutin" value="<?= $kegiatan['TanggalMulaiRutin'] ?? '' ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7 border-left pl-4">
                                            <div class="custom-control custom-radio mb-1">
                                                <input class="custom-control-input" type="radio" id="end-none" name="JenisBatasAkhir" value="Selamanya" <?= ($kegiatan['JenisBatasAkhir']??'Tanggal') == 'Selamanya' ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="end-none">Tanpa batas akhir</label>
                                            </div>
                                            <div class="custom-control custom-radio mb-1 d-flex align-items-center">
                                                <input class="custom-control-input" type="radio" id="end-occurrence" name="JenisBatasAkhir" value="Kejadian" <?= ($kegiatan['JenisBatasAkhir']??'') == 'Kejadian' ? 'checked' : '' ?>>
                                                <label class="custom-control-label mr-2" for="end-occurrence">Berakhir setelah:</label>
                                                <input type="number" class="form-control form-control-sm mr-2" id="jumlahKejadian" name="JumlahKejadian" value="<?= $kegiatan['JumlahKejadian'] ?? '' ?>" style="width:70px;">
                                                <span>kejadian</span>
                                            </div>
                                            <div class="custom-control custom-radio d-flex align-items-center">
                                                <input class="custom-control-input" type="radio" id="end-date" name="JenisBatasAkhir" value="Tanggal" <?= ($kegiatan['JenisBatasAkhir']??'Tanggal') == 'Tanggal' ? 'checked' : '' ?>>
                                                <label class="custom-control-label mr-2" for="end-date">Berakhir pada:</label>
                                                <input type="date" class="form-control form-control-sm" id="tanggalAkhirRutin" name="TanggalAkhirRutin" value="<?= $kegiatan['TanggalAkhirRutin'] ?? '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- HIDDEN INPUTS (Master Source of Truth for Controller) -->
                        <input type="hidden" name="JenisJadwal" id="h_JenisJadwal" value="<?= $kegiatan['JenisJadwal'] ?? 'sekali' ?>">
                        <input type="hidden" name="Interval" id="h_Interval" value="<?= $kegiatan['Interval'] ?? 1 ?>">
                        <input type="hidden" name="TanggalDalamBulan" id="h_TanggalDalamBulan" value="<?= $kegiatan['TanggalDalamBulan'] ?? '' ?>">
                        <input type="hidden" name="OpsiPola" id="h_OpsiPola" value="<?= $kegiatan['OpsiPola'] ?? 'Tanggal' ?>">
                        <input type="hidden" name="PosisiMinggu" id="h_PosisiMinggu" value="<?= $kegiatan['PosisiMinggu'] ?? '' ?>">
                        <input type="hidden" name="BulanTahun" id="h_BulanTahun" value="<?= $kegiatan['BulanTahun'] ?? '' ?>">
                        <!-- Note: HariDalamMinggu[] is handled directly by checkbox name -->

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
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 if available

        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        }

        // --- Outlook-style Recurrence Logic ---
        
        // Hidden Inputs (Target)
        const h_JenisJadwal = $('#h_JenisJadwal');
        const h_Interval = $('#h_Interval');
        const h_TanggalDalamBulan = $('#h_TanggalDalamBulan');
        const h_OpsiPola = $('#h_OpsiPola');
        const h_PosisiMinggu = $('#h_PosisiMinggu');
        const h_BulanTahun = $('#h_BulanTahun');
        // Note: HariDalamMinggu[] is checked directly from checkboxes by Controller if name matches, 
        // BUT for complex mappings (like 'Weekday' or 'First Sunday'), we might need to handle it.
        // Actually, Controller expects 'HariDalamMinggu' checkboxes for 'mingguan'. 
        // For 'bulanan'/'tahunan' (HariKe), we might need to send a single value or handle it in backend.
        // Let's check Controller: It implode(',') POST['HariDalamMinggu'].
        // For Nth Weekday, we probably want to reuse that column or add a specific hidden input for 'HariTarget'.
        // Backend migration didn't rename 'HariDalamMinggu'. So we will use 'HariTarget_*' inputs and 
        // inject them into a hidden input or let controller handle 'HariTarget_Bulanan'.
        // Wait, Controller needs update to read 'HariTarget_*'. 
        // Let's add hidden input 'HariTarget_Hidden' and sync to it.
        
        // Let's create a dynamic hidden input for HariTarget if needed, or misuse 'HariDalamMinggu'
        // Strategy: We will add a hidden input named 'HariDalamMinggu[]' dynamically if needed for non-weekly modes?
        // Better: Use Controller modifying logic. For now, let's just ensure UI sends correct named inputs 
        // and later update Controller to read specifics if complex.
        
        // Actually, looking at HTML above:
        // Bulanan Opt2 uses name="HariTarget_Bulanan".
        // Tahunan Opt2 uses name="HariTarget_Tahunan".
        // Mingguan uses name="HariDalamMinggu[]".
        // Controller currently reads 'HariDalamMinggu'.
        // We should Sync these custom selects into 'HariDalamMinggu[]' hidden list or similar so Controller picks it up easily?
        // Or update Controller to look for 'HariTarget_Bulanan'.
        // I will update Form to Sync to a hidden field named 'HariTarget_Sync' and change Controller to look at that if empty?
        // No, let's keep it simple. Let's make sure the UI inputs are distinct and Controller logic maps them.
        
        const recurrenceContainer = $('#recurrenceContainer');
        const rangeSection = $('#range-section'); // Recurrence Range
        const polaRadios = $('input[name="PolaJadwalUI"]');
        
        // Function to set active tab
        // window.setJenisJadwal removed as it is handled by events


        // --- SYNC STATE FUNCTION ---
        // Reads all UI inputs and populates Hidden Inputs
        function syncState() {
            // 1. Pola Jadwal (Sekali / Rutin)
            const pola = $('input[name="PolaJadwalUI"]:checked').val();
            
            if (pola === 'sekali') {
                h_JenisJadwal.val('sekali');
                recurrenceContainer.hide();
                // Reset/Default values for safety
                h_Interval.val(1);
            } else {
                recurrenceContainer.show();
                // Determine Active Tab
                // Bootstrap 4 active tab class
                let activeTabId = $('#v-pills-tab a.active').attr('id');
                // Map ID to JenisJadwal
                let jenis = 'harian';
                if (activeTabId === 'tab-mingguan') jenis = 'mingguan';
                if (activeTabId === 'tab-bulanan') jenis = 'bulanan';
                if (activeTabId === 'tab-tahunan') jenis = 'tahunan';
                
                h_JenisJadwal.val(jenis);

                // --- Sync Logic per Type ---
                if (jenis === 'harian') {
                    const harianOpt = $('input[name="harianOption"]:checked').val();
                    if (harianOpt === 'Interval') {
                        h_Interval.val($('input[name="Interval_Harian"]').val());
                        h_OpsiPola.val('Tanggal'); // Default
                    } else { // Weekday
                        h_Interval.val(1);
                        h_OpsiPola.val('Weekday');
                    }
                }
                else if (jenis === 'mingguan') {
                    h_Interval.val($('input[name="Interval_Mingguan"]').val());
                    h_OpsiPola.val('Tanggal');
                }
                else if (jenis === 'bulanan') {
                    const bulananOpt = $('input[name="bulananOption"]:checked').val();
                    h_OpsiPola.val(bulananOpt); // Tanggal / HariKe
                    
                    if (bulananOpt === 'Tanggal') {
                        h_TanggalDalamBulan.val($('input[name="Tangal_Bulanan"]').val());
                        h_Interval.val($('input[name="Interval_Bulanan1"]').val());
                        // Clear others
                        h_PosisiMinggu.val(null);
                    } else {
                        // HariKe
                        h_PosisiMinggu.val($('select[name="Posisi_Bulanan"]').val());
                        // Helper: We need to send 'HariDalamMinggu' as single value (1-7)
                        // But existing field is 'HariDalamMinggu'. We should Inject it?
                        // Let's let Controller read 'HariTarget_Bulanan' from POST directly.
                        h_Interval.val($('input[name="Interval_Bulanan2"]').val());
                    }
                }
                else if (jenis === 'tahunan') {
                    h_Interval.val($('input[name="Interval_Tahunan"]').val());
                    const tahunanOpt = $('input[name="tahunanOption"]:checked').val();
                    h_OpsiPola.val(tahunanOpt);
                    
                    if (tahunanOpt === 'Tanggal') {
                        h_BulanTahun.val($('select[name="Bulan_Tahunan1"]').val());
                        h_TanggalDalamBulan.val($('input[name="Tanggal_Tahunan"]').val());
                    } else {
                        // HariKe
                        h_PosisiMinggu.val($('select[name="Posisi_Tahunan"]').val());
                        h_BulanTahun.val($('select[name="Bulan_Tahunan2"]').val());
                    }
                }
            }
            window.syncState = syncState;
        }
        
        // Listeners
        // Tab click (Manual Implementation to prevent sticky tabs)
        $('#v-pills-tab .nav-link').on('click', function (e) {
            e.preventDefault();
            
            // 1. Update Nav Active
            $('#v-pills-tab .nav-link').removeClass('active');
            $(this).addClass('active');

            // 2. Hide all panes explicitly
            $('.tab-pane').removeClass('active show').hide();

            // 3. Show target
            var target = $(this).attr('href');
            $(target).addClass('active show').show();
            
            // 4. Sync State
            syncState();
        });
        
        // All inputs change (Delegated)
        $(document).on('change input', '#recurrenceContainer input, #recurrenceContainer select', function() {
            syncState();
        });
        
        // Main Toggle (Delegated)
        $(document).on('change', 'input[name="PolaJadwalUI"]', syncState);
        
        // Initial Sync
        // Check if DB had data to Set Initial UI state
        // Case: Edit mode. 
        // If hidden 'JenisJadwal' is 'sekali', set radio sekali. 
        // If 'harian'/'mingguan' etc, set radio rutin, show container, click tab.
        const initialJenis = "<?= $kegiatan['JenisJadwal'] ?? 'sekali' ?>";
        if (initialJenis === 'sekali') {
            $('#polaSekali').prop('checked', true);
            recurrenceContainer.hide();
        } else {
            $('#polaRutin').prop('checked', true);
            recurrenceContainer.show();
            // Click the tab manually
            $(`#tab-${initialJenis}`).trigger('click');
            
            // Populate/Select sub-options based on OpsiPola?
            // This part is tricky to reverse-engineer from DB to UI cleanly without many IFs.
            // For MVP: We assume values are in inputs (populated by PHP value="" above).
            // We just need to check the right Radio buttons inside tabs.
            const initialPola = "<?= $kegiatan['OpsiPola'] ?? 'Tanggal' ?>";
            
            if (initialPola === 'HariKe') {
                // Select Opt2 in Bulanan/Tahunan
                $('#bulananOpt2').prop('checked', true);
                $('#tahunanOpt2').prop('checked', true);
            }
        }
        
        // Run once
        syncState();
        
        // End Date Logic
        const endRadios = $('input[name="JenisBatasAkhir"]');
        const endDateInput = $('#tanggalAkhirRutin');
        const occurrencesInput = $('#jumlahKejadian');
        
        function toggleEndCondition() {
            const val = $('input[name="JenisBatasAkhir"]:checked').val();
            if (val === 'Tanggal') {
                endDateInput.prop('disabled', false);
                occurrencesInput.prop('disabled', true);
            } else if (val === 'Kejadian') {
                endDateInput.prop('disabled', true);
                occurrencesInput.prop('disabled', false);
            } else { // Selamanya Input hidden logic if needed
                endDateInput.prop('disabled', true);
                occurrencesInput.prop('disabled', true);
            }
        }
        endRadios.on('change', toggleEndCondition);
        toggleEndCondition(); // Init
        
    });
</script>
<?= $this->endSection(); ?>
