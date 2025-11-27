<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">

    <!-- Informasi Proses Flow -->
    <div class="card card-info card-outline collapsed-card mb-3">
        <div class="card-header bg-info">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Informasi Proses Registrasi Santri Baru
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
                    <h5><i class="fas fa-list-ol"></i> Cara Menggunakan Halaman Registrasi Santri Baru:</h5>
                    <ol class="mb-3">
                        <li class="mb-2">
                            <strong>Memahami Tampilan Halaman</strong>
                            <ul class="mt-1">
                                <li>Halaman ini menampilkan daftar santri yang sudah <strong>mendaftar</strong> tetapi <strong>belum dimasukkan ke kelas</strong></li>
                                <li>Setiap baris menampilkan informasi santri: Nama, Jenis Kelamin, Nama Ayah, Kelas Diajukan, dll</li>
                                <li>Kolom <strong>"Kelas Diajukan"</strong> menampilkan kelas yang sudah ditentukan saat pendaftaran</li>
                                <li>Kolom <strong>"Kelas Koreksi"</strong> adalah dropdown untuk memilih kelas yang benar</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Memilih Kelas untuk Setiap Santri</strong>
                            <ul class="mt-1">
                                <li>Untuk setiap santri, lihat kolom <strong>"Kelas Diajukan"</strong> terlebih dahulu</li>
                                <li>Jika kelas diajukan sudah <strong>benar</strong>, tidak perlu mengubah dropdown "Kelas Koreksi" (sudah terisi otomatis)</li>
                                <li>Jika kelas diajukan <strong>perlu dikoreksi</strong>, klik dropdown "Kelas Koreksi" dan pilih kelas yang sesuai</li>
                                <li>Pastikan <strong>semua santri</strong> sudah dipilih kelasnya sebelum menyimpan</li>
                                <li>Anda bisa menggunakan fitur <strong>search/filter</strong> pada tabel untuk memudahkan pencarian santri</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Menyimpan Data</strong>
                            <ul class="mt-1">
                                <li>Setelah semua santri sudah dipilih kelasnya, klik tombol <strong>"Simpan"</strong> di bagian bawah halaman</li>
                                <li>Sistem akan memproses <strong>semua santri sekaligus</strong> untuk efisiensi</li>
                                <li>Proses ini akan:
                                    <ul>
                                        <li>Memasukkan santri ke kelas yang dipilih</li>
                                        <li>Mengaktifkan santri di sistem</li>
                                        <li>Membuat data nilai kosong untuk semua materi pelajaran di kelas tersebut</li>
                                        <li>Menggunakan tahun ajaran saat ini secara otomatis</li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Setelah Proses Selesai</strong>
                            <ul class="mt-1">
                                <li>Sistem akan menampilkan <strong>pesan sukses</strong> jika proses berhasil</li>
                                <li>Santri yang berhasil diproses akan <strong>hilang dari halaman ini</strong> (karena sudah masuk kelas)</li>
                                <li>Santri yang sudah diproses akan muncul di halaman <strong>"Santri Per Kelas"</strong></li>
                                <li>Data nilai untuk semester Ganjil dan Genap akan <strong>otomatis dibuat</strong> (kosong, siap diisi)</li>
                                <li>Jika masih ada santri yang belum diproses, mereka akan tetap muncul di halaman ini</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Tips dan Saran</strong>
                            <ul class="mt-1">
                                <li>Lakukan proses registrasi <strong>secara berkala</strong> (misalnya setiap minggu atau setiap ada santri baru)</li>
                                <li>Pastikan <strong>tahun ajaran saat ini</strong> sudah benar sebelum memproses</li>
                                <li>Jika ada santri yang <strong>pindah TPQ</strong>, pastikan data TPQ-nya sudah benar</li>
                                <li>Gunakan fitur <strong>search</strong> pada tabel untuk mencari santri tertentu dengan cepat</li>
                                <li>Jika ada banyak santri, Anda bisa memproses <strong>beberapa kali</strong> (tidak harus semua sekaligus)</li>
                            </ul>
                        </li>
                    </ol>

                    <div class="alert alert-warning mb-0">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Penting:</h5>
                        <ul class="mb-0">
                            <li>Halaman ini hanya menampilkan santri yang <strong>belum pernah dimasukkan ke kelas</strong></li>
                            <li>Setelah disimpan, santri akan <strong>langsung aktif</strong> dan bisa digunakan di sistem</li>
                            <li>Pastikan <strong>materi pelajaran</strong> untuk setiap kelas sudah dikonfigurasi dengan benar</li>
                            <li>Halaman ini hanya bisa diakses oleh <strong>Admin</strong> dan <strong>Operator</strong></li>
                            <li>Jika ada kesalahan setelah disimpan, hubungi administrator untuk memperbaiki</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Set Santri Baru TPQ <?= $dataTpq[0]['NamaTpq'] ?></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="<?= base_url('backend/kelas/setKelasSantriBaru') ?>" method="POST">
                <table id="kenaikanKelas" class="table table-bordered table-striped">
                    <?php
                    $tableHeaders = '
                    <tr>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Nama Ayah</th>
                        <th>Kelas Diajukan</th>
                        <th>Kelas Koreksi</th>
                        <th>Nama TPQ</th>
                        <th>Nama Kel/Desa</th>
                    </tr>
                ';
                    ?>
                    <thead>
                        <?= $tableHeaders ?>
                    </thead>
                    <tbody>
                        <?php foreach ($dataSantri as $santri) : ?>
                            <tr>
                                <td><?= $santri['NamaSantri']; ?></td>
                                <td><?= $santri['JenisKelamin']; ?></td>
                                <td><?= $santri['NamaAyah']; ?></td>
                                <td><?= $santri['NamaKelas']; ?></td>
                                <td>
                                    <input type="hidden" name="IdTpq[<?= $santri['IdSantri']; ?>]" value="<?= $santri['IdTpq']; ?>">
                                    <select name="IdKelas[<?= $santri['IdSantri']; ?>]" class="form-control select2" id="FormProfilTpq" required>
                                        <option value="" disabled selected>Pilih kelas</option>
                                        <?php
                                        foreach ($dataKelas as $kelas): ?>
                                            <option value="<?= $kelas['IdKelas'] ?>"
                                                <?= ($kelas['NamaKelas'] == $santri['NamaKelas']) ? 'selected' : '' ?>>
                                                <?= $kelas['NamaKelas'] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td><?= $santri['NamaTpq']; ?></td>
                                <td><?= $santri['NamaKelDesa']; ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <?= $tableHeaders ?>
                    </tfoot>
                </table>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum("#kenaikanKelas", true, true);
</script>
<?= $this->endSection(); ?>