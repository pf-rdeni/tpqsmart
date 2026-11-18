<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?= $this->include('backend/administrasi/_partials/page_header'); ?>

    <div class="row">
        <!-- Kolom Kiri: Form & Daftar Counter -->
        <div class="col-lg-7">
            <!-- Card Set Counter -->
            <div class="card card-primary card-outline shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-cog mr-2"></i>Atur Counter / Nomor Terakhir</h3>
                </div>
                <form id="formSavePenomoran">
                    <div class="card-body">
                        <div class="row">
                            <?php if ($isAdmin): ?>
                                <div class="col-md-6 form-group">
                                    <label>Pilih Lembaga / TPQ <span class="text-danger">*</span></label>
                                    <select name="IdTpq" id="inputTpq" class="form-control" required>
                                        <?php foreach ($listTpq as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>" <?= $tpq['IdTpq'] == $idTpq ? 'selected' : '' ?>>
                                                <?= esc($tpq['NamaTpq']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="IdTpq" value="<?= esc($idTpq) ?>">
                            <?php endif; ?>

                            <div class="col-md-<?= $isAdmin ? '6' : '12' ?> form-group">
                                <label>Kategori Surat <span class="text-danger">*</span></label>
                                <select name="IdKategori" id="inputKategori" class="form-control" required>
                                    <?php foreach ($kategori as $k): ?>
                                        <option value="<?= $k['Id'] ?>" data-kode="<?= esc($k['Kode']) ?>">
                                            <?= esc($k['NamaKategori']) ?> (<?= esc($k['Kode']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="Tahun" id="inputTahun" class="form-control" value="<?= date('Y') ?>" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Bulan (Opsional)</label>
                                <select name="Bulan" id="inputBulan" class="form-control">
                                    <option value="">Semua / Tahunan</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= $m == (int)date('n') ? 'selected' : '' ?>>
                                            Bulan <?= $m ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Urutan Terakhir <span class="text-danger">*</span></label>
                                <input type="number" name="UrutanTerakhir" id="inputUrutan" class="form-control" min="0" value="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-primary" id="btnSaveNomor">
                            <i class="fas fa-save mr-1"></i> Simpan Counter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card History Counter -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-list-ol mr-2"></i>Daftar Counter Aktif</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <?php if ($isAdmin): ?><th>Lembaga</th><?php endif; ?>
                                <th>Kategori</th>
                                <th>Tahun/Bln</th>
                                <th>Urutan Terakhir</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($penomoran)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">Belum ada counter penomoran yang tercatat.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($penomoran as $p): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <?php if ($isAdmin): ?><td><?= esc($p['NamaTpq'] ?: '-') ?></td><?php endif; ?>
                                        <td><strong><?= esc($p['NamaKategori']) ?></strong></td>
                                        <td><?= esc($p['Tahun']) ?><?= !empty($p['Bulan']) ? ' / Bln ' . esc($p['Bulan']) : ' (Tahunan)' ?></td>
                                        <td><span class="badge badge-primary font-weight-bold px-2 py-1"><?= esc($p['UrutanTerakhir']) ?></span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-xs btn-reset-counter" data-id="<?= $p['Id'] ?>" title="Reset ke 0">
                                                <i class="fas fa-undo"></i> Reset
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Simulator & Token Reference -->
        <div class="col-lg-5">
            <!-- Simulator Card -->
            <div class="card card-warning card-outline shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-magic mr-2"></i>Simulator Format Nomor</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Format String</label>
                        <input type="text" id="simFormat" class="form-control" value="[NO:3]/[KODE-TPQ]/[KODE-KAT]/[BLN-ROMAWI]/[TAHUN]">
                    </div>

                    <div class="row">
                        <div class="col-6 form-group">
                            <label>Kode TPQ Contoh</label>
                            <input type="text" id="simKodeTpq" class="form-control" value="TPQ-01">
                        </div>
                        <div class="col-6 form-group">
                            <label>Kode Kategori</label>
                            <input type="text" id="simKodeKat" class="form-control" value="UND">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 form-group">
                            <label>Nomor Urut</label>
                            <input type="number" id="simUrutan" class="form-control" value="1">
                        </div>
                        <div class="col-6 form-group">
                            <label>Bulan / Tahun</label>
                            <input type="text" id="simBulanTahun" class="form-control" value="<?= date('n') ?> / <?= date('Y') ?>" readonly>
                        </div>
                    </div>

                    <div class="alert alert-info py-3 text-center my-3">
                        <small class="text-uppercase font-weight-bold d-block text-muted mb-1">Hasil Generate Nomor:</small>
                        <span id="previewOutput" class="h4 font-weight-bold text-dark mb-0 d-block">---</span>
                    </div>

                    <button type="button" class="btn btn-warning btn-block font-weight-bold" id="btnTestPreview">
                        <i class="fas fa-play mr-1"></i> Uji Format Sekarang
                    </button>
                </div>
            </div>

            <!-- Token Reference Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-book mr-2"></i>Token yang Tersedia</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Token</th>
                                <th>Keterangan</th>
                                <th>Contoh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>[NO:3]</code></td>
                                <td>Nomor urut (3 digit)</td>
                                <td><code>001</code></td>
                            </tr>
                            <tr>
                                <td><code>[NO:4]</code></td>
                                <td>Nomor urut (4 digit)</td>
                                <td><code>0001</code></td>
                            </tr>
                            <tr>
                                <td><code>[KODE-TPQ]</code></td>
                                <td>Kode singkatan TPQ</td>
                                <td><code>TPQ-01</code></td>
                            </tr>
                            <tr>
                                <td><code>[KODE-KAT]</code></td>
                                <td>Kode kategori surat</td>
                                <td><code>UND</code></td>
                            </tr>
                            <tr>
                                <td><code>[BLN-ROMAWI]</code></td>
                                <td>Bulan dalam Romawi</td>
                                <td><code>XI</code></td>
                            </tr>
                            <tr>
                                <td><code>[BLN]</code></td>
                                <td>Bulan angka 2 digit</td>
                                <td><code>11</code></td>
                            </tr>
                            <tr>
                                <td><code>[TAHUN]</code></td>
                                <td>Tahun 4 digit</td>
                                <td><code><?= date('Y') ?></code></td>
                            </tr>
                            <tr>
                                <td><code>[TAHUN-PENDEK]</code></td>
                                <td>Tahun 2 digit</td>
                                <td><code><?= date('y') ?></code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function runPreview() {
        $.ajax({
            url: '<?= base_url('backend/administrasi/penomoran/preview') ?>',
            type: 'POST',
            data: {
                format: $('#simFormat').val(),
                kode_tpq: $('#simKodeTpq').val(),
                kode_kat: $('#simKodeKat').val(),
                urutan: $('#simUrutan').val(),
                bulan: '<?= date('n') ?>',
                tahun: '<?= date('Y') ?>'
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#previewOutput').text(res.preview);
                }
            }
        });
    }

    // Run preview at start
    runPreview();

    $('#btnTestPreview').on('click', runPreview);
    $('#simFormat, #simKodeTpq, #simKodeKat, #simUrutan').on('input', runPreview);

    // Form save counter
    $('#formSavePenomoran').on('submit', function(e) {
        e.preventDefault();
        $('#btnSaveNomor').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/administrasi/penomoran/save') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            },
            complete: function() {
                $('#btnSaveNomor').prop('disabled', false).text('Simpan Counter');
            }
        });
    });

    // Reset counter
    $('.btn-reset-counter').on('click', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Reset Counter Nomor?',
            text: 'Urutan terakhir akan dikembalikan menjadi 0.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e0a800',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('backend/administrasi/penomoran/reset') ?>/' + id, function(res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
