<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <!-- Card Informasi Alur Proses -->
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Alur Proses Profil Data Santri
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-3"><i class="fas fa-list-ol text-primary"></i> Alur Proses:</h5>
                    <ol class="mb-4">
                        <li class="mb-2">
                            <strong>Lihat Daftar Santri:</strong> Tabel menampilkan daftar semua santri dengan informasi dasar 
                            (IdSantri, Nama, Kelurahan/Desa, TPQ, Kelas, Status).
                        </li>
                        <li class="mb-2">
                            <strong>Filter & Search:</strong> Gunakan search box DataTable untuk mencari santri berdasarkan nama, IdSantri, TPQ, atau kolom lainnya. 
                            Data dapat diurutkan dengan mengklik header kolom.
                        </li>
                        <li class="mb-2">
                            <strong>Lihat Detail Profil:</strong> Klik tombol <span class="badge badge-info"><i class="fas fa-eye"></i> Lihat Profil</span> 
                            pada kolom "Aksi" untuk melihat detail lengkap profil santri, termasuk:
                            <ul class="mt-2">
                                <li>Data pribadi santri lengkap</li>
                                <li>Data orang tua/wali</li>
                                <li>Data alamat lengkap</li>
                                <li>Foto profil</li>
                                <li>Riwayat pendidikan</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Export Data:</strong> Gunakan tombol export di DataTable (Copy, Excel, PDF, dll) untuk menyalin atau mengunduh data profil santri.
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Halaman ini menampilkan <strong>ringkasan profil</strong> santri. Untuk melihat detail lengkap, klik tombol "Lihat Profil".</li>
                            <li>Data ditampilkan berdasarkan <strong>TPQ</strong> dan <strong>Kelas</strong> yang sesuai dengan akses user Anda.</li>
                            <li>Kolom "Status" menunjukkan status keaktifan santri (Aktif, Non-Aktif, Alumni).</li>
                            <li>Gunakan fitur <strong>pagination</strong> di DataTable untuk navigasi halaman jika data banyak.</li>
                            <li>Data dapat di-<strong>export</strong> ke berbagai format (Excel, PDF, dll) untuk keperluan laporan atau backup.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profil Data Santri</h3>
        </div>
        <div class="card-body">
            <table id="tblProfilSantri" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('backend/santri/profilDetailSantri/' . $santri['IdSantri']); ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i><span class="d-none d-md-inline">&nbsp;Lihat Profil</span>
                                </a>
                            </td>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= ucwords(strtolower($santri['NamaSantri'])); ?></td>
                            <td><?= ucwords(strtolower($santri['KelurahanDesa'])); ?></td>
                            <td><?= preg_replace_callback('/\b(al|el|ad|ar|at|an)-(\w+)/i', function ($matches) { return ucfirst(strtolower($matches[1])) . '-' . ucfirst($matches[2]); }, ucwords(strtolower($santri['NamaTpq']))); ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <td>
                                <?php if ($santri['Status'] == "Belum Diverifikasi"): ?>
                                    <span class="badge bg-warning"><?= $santri['Status']; ?></span>
                                <?php elseif ($santri['Status'] == "Perlu Perbaikan"): ?>
                                    <span class="badge bg-danger"><?= $santri['Status']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $santri['Status']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableWithFilter("#tblProfilSantri", true);
</script>
<?= $this->endSection(); ?>


