<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <?php if (!$cabang): ?>
            <!-- Pilih Perlombaan -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-random"></i> Pilih Perlombaan untuk Pengundian</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="history.back()" title="Kembali ke halaman sebelumnya">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kegiatan</th>
                                            <th>Perlombaan</th>
                                            <th>Kategori</th>
                                            <th>Tipe</th>
                                            <th>Batasan</th>
                                            <th>Jml Peserta/Grup</th>
                                            <th class="text-center">Status Pengundian</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cabang_list as $i => $c): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= esc($c['NamaLomba']) ?></td>
                                                <td><?= esc($c['NamaCabang']) ?></td>
                                                <td><?= esc($c['Kategori'] ?? '-') ?></td>
                                                <td>
                                                    <span class="badge badge-<?= ($c['Tipe'] ?? 'Individu') === 'Kelompok' ? 'info' : 'secondary' ?>">
                                                        <?= esc($c['Tipe'] ?? 'Individu') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (($c['UsiaMin'] ?? 0) > 0 || ($c['UsiaMax'] ?? 0) > 0): ?>
                                                        <small class="d-block text-nowrap"><i class="fas fa-calendar-alt text-info"></i> Usia: <?= $c['UsiaMin'] ?>-<?= $c['UsiaMax'] ?></small>
                                                    <?php endif; ?>
                                                    <?php if (($c['KelasMin'] ?? 0) > 0 || ($c['KelasMax'] ?? 0) > 0): ?>
                                                        <small class="d-block text-nowrap"><i class="fas fa-graduation-cap text-warning"></i> Kelas: <?= esc($c['NamaKelasMin'] ?: 'Semua') ?>-<?= esc($c['NamaKelasMax'] ?: 'Semua') ?></small>
                                                    <?php endif; ?>
                                                    <?php if (!($c['UsiaMin'] ?? 0) && !($c['UsiaMax'] ?? 0) && !($c['KelasMin'] ?? 0) && !($c['KelasMax'] ?? 0)): ?>
                                                        <small class="text-muted">-</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success"><?= esc($c['total_peserta'] ?? 0) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                        $total = (int) ($c['total_peserta'] ?? 0);
                                                        $terundi = (int) ($c['total_teregistrasi'] ?? 0);
                                                        
                                                        if ($total == 0) {
                                                            echo '<span class="badge badge-secondary">Tidak ada peserta</span>';
                                                        } elseif ($terundi == 0) {
                                                            echo '<span class="badge badge-danger">Belum diundi</span>';
                                                        } elseif ($terundi < $total) {
                                                            echo '<span class="badge badge-warning">Proses (' . $terundi . '/' . $total . ')</span>';
                                                        } else {
                                                            echo '<span class="badge badge-success">Selesai diundi</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('backend/perlombaan/pengundian/' . $c['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-arrow-right"></i> Pilih
                                                    </a>
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
        <?php else: ?>
            <!-- Header Info -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="callout callout-info py-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-trophy"></i> <?= esc($cabang['NamaLomba']) ?> - <?= esc($cabang['NamaCabang']) ?></h5>
                                <p class="mb-0">
                                    Tipe: <strong><?= esc($cabang['Tipe'] ?? 'Individu') ?></strong> |
                                    Calon: <strong><?= $total_calon ?></strong> |
                                    Teregistrasi: <strong><?= $total_teregistrasi ?></strong>
                                </p>
                            </div>
                            <div class="mt-2 mt-md-0">
                                <a href="<?= base_url('backend/perlombaan/pengundian') ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Pilih Perlombaan Lain
                                </a>
                                <a href="<?= base_url('backend/perlombaan/setCabang/' . $cabang['lomba_id']) ?>" class="btn btn-sm btn-info ml-1">
                                    <i class="fas fa-cog"></i> Kelola Cabang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Panel: Calon Peserta -->
                <div class="col-md-12 mb-4">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-users"></i> Calon Peserta (Belum Diundi)</h3>
                            <div class="card-tools">
                                <span class="badge badge-warning"><?= $total_calon ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($calon_peserta)): ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-2"></i>
                                    <p>Semua peserta sudah diundi</p>
                                </div>
                            <?php else: ?>
                                <div class="p-2">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="checkAll">
                                        <label class="custom-control-label" for="checkAll"><strong>Pilih Semua</strong></label>
                                    </div>
                                </div>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <thead class="sticky-top bg-light">
                                            <tr>
                                                <th width="40"></th>
                                                <th>Nama</th>
                                                <th>TPQ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($calon_peserta as $calon): ?>
                                                <tr>
                                                    <td>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input check-peserta" 
                                                                   id="peserta<?= $calon['id'] ?>" value="<?= $calon['id'] ?>">
                                                            <label class="custom-control-label" for="peserta<?= $calon['id'] ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($calon['NamaGrup'])): ?>
                                                            <strong><?= esc($calon['NamaGrup']) ?></strong><br>
                                                            <small class="text-muted"><?= esc($calon['NamaSantri']) ?></small>
                                                        <?php else: ?>
                                                            <?= esc($calon['NamaSantri']) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><small><?= esc($calon['NamaTpq']) ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-success btn-block" id="btnUndian" disabled>
                                        <i class="fas fa-random"></i> Mulai Pengundian (<span id="countSelected">0</span> peserta)
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Panel: Hasil Registrasi -->
                <div class="col-md-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-check-circle"></i> Peserta Teregistrasi (Nomor Urut)</h3>
                            <div class="card-tools">
                                <span class="badge badge-success"><?= $total_teregistrasi ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($peserta_teregistrasi)): ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-hourglass-half fa-3x mb-2"></i>
                                    <p>Belum ada peserta yang diundi</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive p-2">
                                    <table class="table table-sm table-striped" id="tableRegistrasi" style="width: 100%;">
                                        <thead class="bg-success text-white">
                                            <tr>
                                                <th>No Peserta</th>
                                                <th>Nama</th>
                                                <th>Kelas</th>
                                                <th>Usia</th>
                                                <th>TPQ</th>
                                                <th>Alamat TPQ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="hasilRegistrasi">
                                            <?php foreach ($peserta_teregistrasi as $reg): ?>
                                                <tr>
                                                    <td><code class="bg-success text-white p-1"><?= esc($reg['NoPeserta']) ?></code></td>
                                                    <td>
                                                        <?php if ($reg['TipePeserta'] === 'Kelompok'): ?>
                                                            <strong><?= esc($reg['NamaKelompok']) ?></strong>
                                                            <br><small class="text-muted">
                                                                <?php 
                                                                $namaAnggota = array_map(function($a) { return $a['NamaSantri']; }, $reg['anggota']);
                                                                echo implode(', ', $namaAnggota);
                                                                ?>
                                                            </small>
                                                        <?php else: ?>
                                                            <?php 
                                                            $anggota = $reg['anggota'][0] ?? [];
                                                            echo esc($anggota['NamaSantri'] ?? '-');
                                                            ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $kelasList = array_unique(array_filter(array_map(function($a) { return $a['NamaKelas'] ?? null; }, $reg['anggota'])));
                                                        echo !empty($kelasList) ? implode(', ', $kelasList) : '-';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $usiaList = array_map(function($a) {
                                                            if (empty($a['TanggalLahirSantri'])) return null;
                                                            try {
                                                                $bday = new DateTime($a['TanggalLahirSantri']);
                                                                $today = new DateTime('today');
                                                                return $bday->diff($today)->y;
                                                            } catch (Exception $e) { return null; }
                                                        }, $reg['anggota']);
                                                        $usiaList = array_unique(array_filter($usiaList));
                                                        echo !empty($usiaList) ? implode(', ', $usiaList) . ' th' : '-';
                                                        ?>
                                                    </td>
                                                    <td><small><?= esc($reg['anggota'][0]['NamaTpq'] ?? '-') ?></small></td>
                                                    <td><small><?= esc($reg['anggota'][0]['KelurahanDesa'] ?? '-') ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Animasi Undian -->
<div class="modal fade" id="modalUndian" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <h3 class="mb-4">Mengundi Nomor Peserta...</h3>
                <div id="animasiNomor" class="display-1 text-success font-weight-bold">
                    ---
                </div>
                <div id="progressUndian" class="mt-4" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                             style="width: 0%"></div>
                    </div>
                    <p class="mt-2 mb-0"><span id="currentProgress">0</span> / <span id="totalProgress">0</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable untuk Export
    if ($('#tableRegistrasi').length > 0) {
        $("#tableRegistrasi").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "buttons": [
                {
                    extend: 'excel',
                    className: 'btn-success btn-sm',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    title: 'Daftar Nomor Peserta' + (typeof '<?= esc($cabang['NamaCabang'] ?? '') ?>' !== 'undefined' ? ' - <?= esc($cabang['NamaCabang'] ?? '') ?>' : '')
                },
                {
                    extend: 'pdf',
                    className: 'btn-danger btn-sm',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    title: 'Daftar Nomor Peserta' + (typeof '<?= esc($cabang['NamaCabang'] ?? '') ?>' !== 'undefined' ? ' - <?= esc($cabang['NamaCabang'] ?? '') ?>' : '')
                },
                {
                    extend: 'print',
                    className: 'btn-info btn-sm',
                    text: '<i class="fas fa-print"></i> Print'
                }
            ]
        }).buttons().container().appendTo('#tableRegistrasi_wrapper .col-md-6:eq(0)');
        
        // Sesuaikan style tombol agar tidak berhimpitan
        $('.dt-buttons').addClass('mb-2');
    }

    // Check all
    $('#checkAll').change(function() {
        $('.check-peserta').prop('checked', $(this).is(':checked'));
        updateCount();
    });

    // Update count saat checkbox berubah
    $('.check-peserta').change(function() {
        updateCount();
    });

    function updateCount() {
        var count = $('.check-peserta:checked').length;
        $('#countSelected').text(count);
        $('#btnUndian').prop('disabled', count === 0);
    }

    // Proses Undian
    $('#btnUndian').click(function() {
        var selectedIds = [];
        $('.check-peserta:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire('Perhatian', 'Pilih peserta yang akan diundi', 'warning');
            return;
        }

        Swal.fire({
            title: 'Mulai Pengundian?',
            text: 'Akan mengundi ' + selectedIds.length + ' peserta',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Mulai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                prosesUndian(selectedIds);
            }
        });
    });

    function prosesUndian(pesertaIds) {
        $('#modalUndian').modal('show');
        $('#progressUndian').show();
        $('#totalProgress').text(pesertaIds.length);
        
        // Animasi nomor random
        var animasiInterval = setInterval(function() {
            var randomNum = Math.floor(Math.random() * 900) + 100;
            $('#animasiNomor').text(randomNum);
        }, 50);

        $.ajax({
            url: '<?= base_url('backend/perlombaan/prosesUndian') ?>',
            type: 'POST',
            data: {
                cabang_id: '<?= $cabang['id'] ?? '' ?>',
                peserta_ids: pesertaIds
            },
            dataType: 'json',
            success: function(response) {
                clearInterval(animasiInterval);
                
                if (response.success) {
                    // Animate through the results
                    var results = response.data;
                    var currentIndex = 0;
                    
                    function showNextResult() {
                        if (currentIndex < results.length) {
                            var res = results[currentIndex];
                            $('#animasiNomor').text(res.NoPeserta);
                            $('#currentProgress').text(currentIndex + 1);
                            $('.progress-bar').css('width', ((currentIndex + 1) / results.length * 100) + '%');
                            currentIndex++;
                            setTimeout(showNextResult, 300);
                        } else {
                            // Selesai
                            setTimeout(function() {
                                $('#modalUndian').modal('hide');
                                Swal.fire({
                                    title: 'Pengundian Selesai!',
                                    text: results.length + ' peserta berhasil diundi',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            }, 500);
                        }
                    }
                    
                    showNextResult();
                } else {
                    clearInterval(animasiInterval);
                    $('#modalUndian').modal('hide');
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function() {
                clearInterval(animasiInterval);
                $('#modalUndian').modal('hide');
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>
