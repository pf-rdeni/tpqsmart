<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Row 1: Form Input Nilai (Full Width) -->
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit"></i> Input Nilai Lomba</h3>
                        <div class="card-tools">
                            <span class="badge badge-light mr-2">
                                <i class="fas fa-trophy"></i> <?= esc($juri_data['NamaLomba']) ?> - <?= esc($juri_data['NamaCabang']) ?>
                            </span>
                            <span class="badge badge-primary">
                                <i class="fas fa-user-tie"></i> <?= esc($juri_data['IdJuri']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Stepper 2 Langkah -->
                        <div id="stepper" class="bs-stepper">
                            <div class="bs-stepper-header" role="tablist">
                                <div class="step" data-target="#step1">
                                    <button type="button" class="step-trigger" role="tab" aria-controls="step1" id="trigger1">
                                        <span class="bs-stepper-circle">1</span>
                                        <span class="bs-stepper-label">Cek Peserta</span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" data-target="#step2">
                                    <button type="button" class="step-trigger" role="tab" aria-controls="step2" id="trigger2">
                                        <span class="bs-stepper-circle">2</span>
                                        <span class="bs-stepper-label">Input Nilai</span>
                                    </button>
                                </div>
                            </div>
                            <div class="bs-stepper-content">
                                <!-- Step 1: Cek Peserta -->
                                <div id="step1" class="content" role="tabpanel" aria-labelledby="trigger1">
                                    <div class="form-group">
                                        <label for="noPeserta">Masukkan Nomor Peserta (3 digit)</label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary text-white"><i class="fas fa-hashtag"></i></span>
                                            </div>
                                            <input type="text" class="form-control form-control-lg text-center font-weight-bold" 
                                                   id="noPeserta" placeholder="100" maxlength="3" 
                                                   style="font-size: 2rem; letter-spacing: 0.5rem;" autofocus>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" id="btnCekPeserta">
                                                    <i class="fas fa-search"></i> Cek
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1 d-block" id="statusCek">Ketik 3 digit nomor peserta</small>
                                    </div>
                                </div>

                                <!-- Step 2: Input Nilai -->
                                <div id="step2" class="content" role="tabpanel" aria-labelledby="trigger2">
                                    <form id="formNilai">
                                        <input type="hidden" name="registrasi_id" id="registrasiId">
                                        <input type="hidden" name="cabang_id" id="cabangId">
                                        <input type="hidden" name="IdJuri" value="<?= esc($juri_data['IdJuri']) ?>">
                                        <input type="hidden" name="isEdit" id="isEdit" value="false">
                                        
                                        <div id="formKriteria">
                                            <!-- Form kriteria akan di-generate secara dinamis -->
                                        </div>

                                        <div class="mt-4">
                                            <button type="button" class="btn btn-secondary" id="btnBack">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </button>
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-save"></i> Simpan Nilai
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Riwayat Terkini -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history"></i> Riwayat Penilaian Terkini</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="tableRiwayat">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Waktu</th>
                                        <th width="80">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="listRiwayat">
                                    <?php if (empty($recent_scored)): ?>
                                        <tr class="empty-row"><td colspan="4" class="text-muted text-center">Belum ada penilaian</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_scored as $r): ?>
                                            <tr>
                                                <td>
                                                    <code class="bg-success text-white px-2 py-1 rounded"><?= esc($r['NoPeserta']) ?></code>
                                                </td>
                                                <td>
                                                    <?php if (($r['TipePeserta'] ?? 'Individu') === 'Kelompok'): ?>
                                                        <span class="badge badge-info">Tim</span>
                                                        <?= esc($r['NamaKelompok'] ?? 'Tim') ?>
                                                    <?php else: ?>
                                                        <?= esc($r['NamaSantri'] ?? 'Peserta') ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?= date('d/m H:i', strtotime($r['updated_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-xs btn-warning btn-edit-riwayat" 
                                                            data-nopeserta="<?= esc($r['NoPeserta']) ?>" title="Edit Nilai">
                                                        <i class="fas fa-edit"></i>
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
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
var stepper; // Define stepper globally

$(document).ready(function() {
    stepper = new Stepper(document.querySelector('#stepper'));

    var typingTimer;
    var doneTypingInterval = 3000; // 3 detik delay

    // Cek peserta saat klik tombol
    $('#btnCekPeserta').click(function() {
        cekPeserta();
    });

    // Cek peserta saat tekan Enter
    $('#noPeserta').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            clearTimeout(typingTimer);
            cekPeserta();
        }
    });

    // Auto-trigger setelah 3 digit dengan delay 3 detik
    $('#noPeserta').on('input', function() {
        // Hanya izinkan angka
        this.value = this.value.replace(/[^0-9]/g, '');
        
        var val = $(this).val();
        clearTimeout(typingTimer);
        
        if (val.length === 3) {
            $('#statusCek').html('<i class="fas fa-clock text-warning"></i> Mencek dalam 3 detik... <span class="text-info">(tekan Enter untuk cek langsung)</span>');
            typingTimer = setTimeout(function() {
                cekPeserta();
            }, doneTypingInterval);
        } else if (val.length < 3) {
            $('#statusCek').html('Ketik ' + (3 - val.length) + ' digit lagi');
            $('#pesertaInfo').hide();
        }
    });

}); // End of document.ready

// Functions defined outside to be accessible globally

function cekPeserta() {
        var noPeserta = $('#noPeserta').val().trim();
        if (!noPeserta) {
            Swal.fire('Perhatian', 'Masukkan nomor peserta', 'warning');
            return;
        }

        $.ajax({
            url: '<?= base_url('backend/perlombaan/cekPeserta') ?>',
            type: 'POST',
            data: {
                noPeserta: noPeserta,
                IdJuri: '<?= esc($juri_data['IdJuri']) ?>',
                cabang_id: '<?= esc($juri_data['cabang_id']) ?>'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnCekPeserta').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function(response) {
                $('#btnCekPeserta').prop('disabled', false).html('<i class="fas fa-search"></i> Cek');
                
                if (response.success) {
                    // Cek apakah peserta sudah dinilai
                    if (response.data.already_scored) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sudah Dinilai',
                            html: '<div class="text-left">' +
                                  '<p>Peserta dengan nomor <strong>' + response.data.registrasi.NoPeserta + '</strong> sudah Anda nilai.</p>' +
                                  '<hr>' +
                                  '<p class="mb-0">Apa yang ingin Anda lakukan?</p>' +
                                  '</div>',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-edit"></i> Edit Nilai',
                            cancelButtonText: '<i class="fas fa-times"></i> Keluar',
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            width: 450,
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Lanjut ke edit nilai
                                tampilkanInfoPeserta(response.data);
                            } else {
                                // Keluar - reset form
                                $('#noPeserta').val('').focus();
                                $('#statusCek').html('Ketik 3 digit nomor peserta');
                                $('#pesertaInfo').hide();
                            }
                        });
                        return;
                    }
                    
                    tampilkanInfoPeserta(response.data);
                } else {
                    // Cek apakah message berisi HTML
                    if (response.html) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            html: response.message,
                            width: 500
                        });
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                    $('#pesertaInfo').hide();
                }
            },
            error: function() {
                $('#btnCekPeserta').prop('disabled', false).html('<i class="fas fa-search"></i> Cek');
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            }
        });
    }

    function tampilkanInfoPeserta(data) {
        var registrasi = data.registrasi;
        var kriteria = data.kriteria;
        var alreadyScored = data.already_scored;
        var existingNilai = data.existing_nilai;

        // Simpan data untuk step berikutnya
        $('#registrasiId').val(registrasi.id);
        $('#cabangId').val(registrasi.cabang_id);
        $('#isEdit').val(alreadyScored ? 'true' : 'false');

        // Generate form kriteria dengan info peserta di header
        generateFormKriteria(kriteria, existingNilai, registrasi, alreadyScored);

        // Update status dan langsung ke step 2
        $('#statusCek').html('<i class="fas fa-check-circle text-success"></i> Peserta <strong>' + registrasi.NoPeserta + '</strong> ditemukan!');
        stepper.next();
    }

    function generateFormKriteria(kriteria, existingNilai, registrasi, alreadyScored) {
        // Badge color: hijau jika baru, kuning jika edit
        var badgeClass = alreadyScored ? 'badge-warning' : 'badge-success';
        var statusText = alreadyScored ? '<small class="ml-2">(Edit)</small>' : '';
        
        // Info peserta singkat di atas
        var html = '<div class="alert alert-' + (alreadyScored ? 'warning' : 'success') + ' mb-3">';
        html += '<div class="row align-items-center">';
        html += '<div class="col-md-4"><strong>No Peserta:</strong> <span class="badge ' + badgeClass + ' px-3 py-2" style="font-size: 1.1rem;">' + registrasi.NoPeserta + '</span>' + statusText + '</div>';
        html += '<div class="col-md-4"><strong>Nama:</strong> ' + registrasi.NamaSantri + '</div>';
        html += '<div class="col-md-4"><strong>Cabang:</strong> ' + registrasi.NamaCabang + '</div>';
        html += '</div>';
        html += '</div>';
        
        // Tabel kriteria
        html += '<div class="table-responsive"><table class="table table-bordered">';
        html += '<thead class="thead-light"><tr><th width="50">No</th><th>Kriteria</th><th width="200">Nilai</th></tr></thead>';
        html += '<tbody>';

        kriteria.forEach(function(k, i) {
            var existingVal = existingNilai[k.id] ? existingNilai[k.id].Nilai : '';
            html += '<tr>';
            html += '<td class="text-center">' + (i + 1) + '</td>';
            html += '<td><strong>' + k.NamaKriteria + '</strong></td>';
            html += '<td>';
            html += '<input type="number" class="form-control input-nilai" name="nilai[' + k.id + ']" ';
            html += 'min="' + k.NilaiMin + '" max="' + k.NilaiMax + '" step="0.01" ';
            html += 'value="' + existingVal + '" required ';
            html += 'placeholder="' + k.NilaiMin + '-' + k.NilaiMax + '">';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        $('#formKriteria').html(html);
    }

    // Tombol kembali ke step 1
    $('#btnBack').click(function() {
        stepper.previous();
    });

    // Submit form nilai
    $('#formNilai').submit(function(e) {
        e.preventDefault();

        // Validasi nilai
        var isValid = true;
        $('.input-nilai').each(function() {
            var val = parseFloat($(this).val());
            var min = parseFloat($(this).attr('min'));
            var max = parseFloat($(this).attr('max'));
            if (isNaN(val) || val < min || val > max) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire('Perhatian', 'Pastikan semua nilai sudah diisi dengan benar', 'warning');
            return;
        }

        $.ajax({
            url: '<?= base_url('backend/perlombaan/simpanNilai') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        willClose: () => {
                            // Reset form dan kembali ke step 1
                            stepper.to(1);
                            $('#noPeserta').val('').focus();
                            $('#formKriteria').html('');
                            $('#statusCek').html('Ketik 3 digit nomor peserta');
                            
                            // Update riwayat dengan entry baru
                            if (response.last_scored) {
                                var ls = response.last_scored;
                                var nama = ls.TipePeserta === 'Kelompok' 
                                    ? '<span class="badge badge-info">Tim</span> ' + (ls.NamaKelompok || 'Tim')
                                    : ls.NamaSantri;
                                
                                var newRow = '<tr>';
                                newRow += '<td><code class="bg-success text-white px-2 py-1 rounded">' + ls.NoPeserta + '</code></td>';
                                newRow += '<td>' + nama + '</td>';
                                newRow += '<td><small class="text-muted">' + ls.waktu + '</small></td>';
                                newRow += '<td><button type="button" class="btn btn-xs btn-warning btn-edit-riwayat" data-nopeserta="' + ls.NoPeserta + '" title="Edit Nilai"><i class="fas fa-edit"></i></button></td>';
                                newRow += '</tr>';
                                
                                // Hapus pesan "belum ada penilaian" jika ada
                                $('#listRiwayat').find('.empty-row').remove();
                                // Tambah di awal list
                                $('#listRiwayat').prepend(newRow);
                                // Batasi maksimal 5 item
                                if ($('#listRiwayat tr').length > 5) {
                                    $('#listRiwayat tr:last').remove();
                                }
                            }
                        }
                    });
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
                $('button[type="submit"]').prop('disabled', false);
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                $('button[type="submit"]').prop('disabled', false);
            }
        });
    });
// Handler untuk tombol edit dari riwayat
$(document).on('click', '.btn-edit-riwayat', function() {
    var noPeserta = $(this).data('nopeserta');
    $('#noPeserta').val(noPeserta);
    cekPeserta();
});
</script>
<?= $this->endSection(); ?>
