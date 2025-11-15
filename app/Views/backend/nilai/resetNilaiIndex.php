<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-undo"></i> Reset Nilai
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Informasi!</strong> Pilih jenis reset nilai yang ingin Anda lakukan. Pastikan Anda telah memilih dengan benar sebelum melakukan reset.
                    </div>

                    <div class="row">
                        <!-- Reset Nilai Semester -->
                        <div class="col-md-4">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-book"></i> Reset Nilai Semester
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Reset nilai semester untuk tabel <strong>tbl_nilai</strong>. 
                                        Akan mereset kolom <strong>IdGuru</strong> dan <strong>Nilai</strong> berdasarkan filter TPQ, Tahun Ajaran, dan Semester.
                                    </p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> Filter berdasarkan TPQ</li>
                                        <li><i class="fas fa-check text-success"></i> Filter berdasarkan Tahun Ajaran</li>
                                        <li><i class="fas fa-check text-success"></i> Filter berdasarkan Semester</li>
                                        <li><i class="fas fa-check text-success"></i> Preview sebelum reset</li>
                                        <li><i class="fas fa-check text-success"></i> Pilih kelas secara multiple</li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('backend/nilai/resetNilai') ?>" class="btn btn-primary btn-block">
                                        <i class="fas fa-arrow-right"></i> Buka Reset Nilai Semester
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reset Nilai Sertifikasi -->
                        <div class="col-md-4">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-trash"></i> Hapus Nilai Sertifikasi
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Hapus nilai sertifikasi untuk tabel <strong>tbl_sertifikasi_nilai</strong>. 
                                        Akan <strong>MENGHAPUS</strong> data nilai berdasarkan <strong>NoPeserta</strong> yang dipilih.
                                    </p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> Filter berdasarkan No Peserta</li>
                                        <li><i class="fas fa-check text-success"></i> Preview sebelum hapus</li>
                                        <li><i class="fas fa-check text-success"></i> Pilih peserta secara multiple</li>
                                        <li><i class="fas fa-exclamation-triangle text-danger"></i> Data yang dihapus tidak dapat dikembalikan</li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('backend/nilai/resetNilaiSertifikasi') ?>" class="btn btn-warning btn-block">
                                        <i class="fas fa-arrow-right"></i> Buka Hapus Nilai Sertifikasi
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reset Nilai Munaqosah -->
                        <div class="col-md-4">
                            <div class="card card-danger card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-trash"></i> Hapus Nilai Munaqosah
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Hapus nilai munaqosah untuk tabel <strong>tbl_munaqosah_nilai</strong>. 
                                        Akan <strong>MENGHAPUS</strong> data nilai berdasarkan <strong>NoPeserta</strong> yang dipilih.
                                    </p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success"></i> Filter berdasarkan TPQ</li>
                                        <li><i class="fas fa-check text-success"></i> Filter berdasarkan Tahun Ajaran</li>
                                        <li><i class="fas fa-check text-success"></i> Filter berdasarkan Type Ujian</li>
                                        <li><i class="fas fa-check text-success"></i> Preview sebelum hapus</li>
                                        <li><i class="fas fa-check text-success"></i> Pilih peserta secara multiple</li>
                                        <li><i class="fas fa-exclamation-triangle text-danger"></i> Data yang dihapus tidak dapat dikembalikan</li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('backend/nilai/resetNilaiMunaqosah') ?>" class="btn btn-danger btn-block">
                                        <i class="fas fa-arrow-right"></i> Buka Hapus Nilai Munaqosah
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

