<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">

    <!-- Filter Tahun Ajaran Asal -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="fas fa-filter text-purple"></i> Pilih Tahun Ajaran Asal untuk Proses Kenaikan</h5>
                </div>
                <div class="col-md-6">
                    <select class="form-control" id="selectSourceTahunAjaran">
                        <?php foreach ($tahunAjaranList as $ta): ?>
                            <?php 
                            // Hitung tahun ajaran berikutnya untuk label
                            $nextTa = (int)substr($ta, 0, 4) + 1 . ((int)substr($ta, 4, 4) + 1);
                            ?>
                            <option value="<?= $ta; ?>" <?= ($ta == $previous_tahun_ajaran) ? 'selected' : ''; ?>>
                                <?= convertTahunAjaran($ta); ?> &rarr; <?= convertTahunAjaran($nextTa); ?> (Target Kenaikan)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Proses Flow -->
    <div class="card card-info card-outline collapsed-card mb-3">
        <div class="card-header bg-info">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Informasi Proses Kenaikan Kelas
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <div class="row">
                <div class="col-md-12">
                    <h5><i class="fas fa-list-ol"></i> Cara Menggunakan Halaman Kenaikan Kelas:</h5>
                    <ol class="mb-3">
                        <li class="mb-2">
                            <strong>Memahami Tampilan Halaman</strong>
                            <ul class="mt-1">
                                <li>Halaman ini menampilkan <strong>dua tabel</strong>: tabel atas untuk <strong>Tahun Ajaran Asal</strong> (yang dipilih pada filter) dan tabel bawah untuk <strong>Tahun Ajaran Target Kenaikan</strong></li>
                                <li>Tabel atas menampilkan kelas-kelas dari tahun ajaran asal yang <strong>siap untuk dinaikkan</strong> ke tahun ajaran target</li>
                                <li>Tabel bawah menampilkan kelas-kelas yang <strong>sudah terdaftar di tahun ajaran target</strong></li>
                                <li>Setiap baris menampilkan: Tahun Ajaran, Nama Kelas, dan Jumlah Santri</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Memilih Tahun Ajaran Asal</strong>
                            <ul class="mt-1">
                                <li>Gunakan filter <strong>"Pilih Tahun Ajaran Asal untuk Proses Kenaikan"</strong> di bagian atas halaman untuk menentukan tahun ajaran yang ingin Anda naikkan kelasnya secara fleksibel</li>
                                <li>Sistem akan otomatis menyesuaikan daftar kelas asal (tabel atas) dan target kenaikan (tabel bawah)</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Memproses Kenaikan Kelas</strong>
                            <ul class="mt-1">
                                <li>Lihat tabel <strong>"Tahun Ajaran Asal"</strong> untuk menemukan kelas yang akan dinaikkan</li>
                                <li>Pastikan jumlah santri sudah sesuai dan benar</li>
                                <li>Klik tombol <strong>"Proses Naik Kelas"</strong> (ikon pensil/edit) pada kolom "Proses Naik Kelas"</li>
                                <li>Sistem akan otomatis memproses semua santri di kelas tersebut untuk naik ke kelas berikutnya</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Apa yang Terjadi Setelah Proses?</strong>
                            <ul class="mt-1">
                                <li>Semua santri di kelas tersebut akan <strong>otomatis naik ke kelas berikutnya</strong></li>
                                <li>Santri akan muncul di <strong>tahun ajaran target</strong> dengan kelas yang lebih tinggi</li>
                                <li>Data nilai dan absensi di tahun ajaran asal <strong>tetap tersimpan</strong> dan tidak hilang</li>
                                <li>Data nilai untuk tahun ajaran target akan <strong>otomatis dibuat</strong> sesuai materi kelas baru</li>
                                <li>Setelah selesai, kelas akan muncul di tabel <strong>"Tahun Ajaran Target"</strong> di bagian bawah</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Memverifikasi Hasil</strong>
                            <ul class="mt-1">
                                <li>Setelah proses selesai, halaman akan memuat kembali data terbaru</li>
                                <li>Cek tabel <strong>"Tahun Ajaran Target"</strong> di bagian bawah</li>
                                <li>Pastikan kelas yang baru diproses sudah muncul dengan jumlah santri yang benar</li>
                                <li>Jika ada masalah, hubungi administrator untuk bantuan</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Tips dan Saran</strong>
                            <ul class="mt-1">
                                <li>Proses kenaikan kelas kini **bisa dilakukan secara fleksibel kapan saja** tanpa harus menunggu tahun ajaran baru berjalan secara sistem</li>
                                <li>Pastikan semua santri yang akan naik kelas <strong>sudah memiliki data lengkap</strong></li>
                                <li>Jika ada santri yang <strong>tidak naik kelas</strong> (tinggal kelas), pastikan sudah ditangani terlebih dahulu</li>
                                <li>Proses ini bisa dilakukan <strong>per kelas</strong>, tidak harus semua kelas sekaligus</li>
                                <li>Disarankan untuk <strong>mencatat</strong> kelas yang sudah diproses agar tidak terlewat</li>
                            </ul>
                        </li>
                    </ol>

                    <div class="alert alert-warning mb-0">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan Penting:</h5>
                        <ul class="mb-0">
                            <li>Proses kenaikan kelas <strong>tidak dapat dibatalkan</strong> setelah dilakukan</li>
                            <li>Pastikan Anda sudah <strong>yakin</strong> sebelum mengklik tombol "Proses Naik Kelas"</li>
                            <li>Pastikan <strong>tahun ajaran target/baru</strong> sudah dikonfigurasi dengan benar</li>
                            <li>Jika ada santri yang <strong>pindah TPQ</strong> atau <strong>keluar</strong>, pastikan sudah ditangani sebelum proses kenaikan</li>
                            <li>Halaman ini hanya bisa diakses oleh <strong>Admin</strong> dan <strong>Operator</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card for Previous Academic Year -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Santri TPQ Per Kelas Tahun Ajaran <?= convertTahunAjaran($previous_tahun_ajaran) ?> untuk dinaikan</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="previousKelas" class="table table-bordered table-striped">
                <?php
                $tableHeadersFooter = '
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Nama Kelas</th>
                        <th>Jumlah Santri</th>
                        <th>Proses Naik Kelas</th>
                    </tr>
                ';
                ?>
                <thead>
                    <?= $tableHeadersFooter ?>
                </thead>
                <tbody>
                    <?php if (!empty($kelas_previous)): ?>
                        <?php foreach ($kelas_previous as $dataKelas) : ?>
                            <tr>
                                <td><?= $dataKelas['IdTahunAjaran']; ?></td>
                                <td><?= $dataKelas['NamaKelas']; ?></td>
                                <td><?= $dataKelas['SumIdKelas']; ?></td>
                                <td>
                                    <a href="<?php echo base_url('backend/kelas/updateNaikKelas/' . $dataKelas['IdTahunAjaran'] . '/' . $dataKelas['IdKelas']) ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No data available for the previous academic year.</td>
                        </tr>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <?= $tableHeadersFooter ?>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <!-- Card for Current Academic Year -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Santri TPQ Per Kelas Tahun Ajaran <?= convertTahunAjaran($current_tahun_ajaran) ?></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="currentKelas" class="table table-bordered table-striped">
                <?php
                $tableHeadersFooter = '
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Nama Kelas</th>
                        <th>Jumlah Santri</th>
                    </tr>
                ';
                ?>
                <thead>
                    <?= $tableHeadersFooter ?>
                </thead>
                <tbody>
                    <?php if (!empty($kelas_current)): ?>
                        <?php foreach ($kelas_current as $dataKelas) : ?>
                            <tr>
                                <td><?= $dataKelas['IdTahunAjaran']; ?></td>
                                <td><?= $dataKelas['NamaKelas']; ?></td>
                                <td><?= $dataKelas['SumIdKelas']; ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No data available for the current academic year.</td>
                        </tr>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <?= $tableHeadersFooter ?>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectSourceTahunAjaran = document.getElementById('selectSourceTahunAjaran');
        if (selectSourceTahunAjaran) {
            selectSourceTahunAjaran.addEventListener('change', function() {
                const selectedVal = this.value;
                window.location.href = '<?= base_url('backend/kelas/showListSantriPerKelas') ?>/' + selectedVal;
            });
        }
    });
</script>
<?= $this->endSection(); ?>