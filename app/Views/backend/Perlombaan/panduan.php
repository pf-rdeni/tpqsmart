<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<style>
    /* Custom Flow Chart Styling */
    .flow-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 40px;
        position: relative;
        padding: 20px 0;
    }
    .flow-wrapper::before {
        content: '';
        position: absolute;
        top: 55px;
        left: 5%;
        right: 5%;
        height: 4px;
        background: #dee2e6;
        z-index: 1;
    }
    .flow-step {
        width: 16.66%;
        text-align: center;
        position: relative;
        z-index: 2;
    }
    .flow-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #fff;
        border: 4px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 24px;
        color: #adb5bd;
        transition: all 0.3s ease;
    }
    .flow-step.active .flow-icon {
        border-color: #007bff;
        color: #007bff;
        box-shadow: 0 0 15px rgba(0,123,255,0.3);
    }
    .flow-step.success .flow-icon {
        border-color: #28a745;
        color: #28a745;
    }
    .flow-step.warning .flow-icon {
        border-color: #ffc107;
        color: #ffc107;
    }
    .flow-step.danger .flow-icon {
        border-color: #dc3545;
        color: #dc3545;
    }
    .flow-title {
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 5px;
    }
    .flow-desc {
        font-size: 0.85rem;
        color: #6c757d;
        line-height: 1.2;
    }

    /* Explanation Card Styling */
    .help-card {
        transition: transform 0.2s;
        height: 100%;
    }
    .help-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .phase-badge {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: inline-block;
    }
</style>

<section class="content">
    <div class="container-fluid">
        <!-- Flow Chart Card -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-project-diagram"></i> Alur Kerja Perlombaan (Flow Chart)</h3>
            </div>
            <div class="card-body">
                <div class="flow-wrapper">
                    <!-- Phase 1 -->
                    <div class="flow-step active">
                        <div class="flow-icon"><i class="fas fa-cog"></i></div>
                        <div class="flow-title">1. Persiapan</div>
                        <div class="flow-desc">Setup Lomba, Cabang, & Kriteria</div>
                    </div>
                    <!-- Phase 2 -->
                    <div class="flow-step success">
                        <div class="flow-icon"><i class="fas fa-user-edit"></i></div>
                        <div class="flow-title">2. Registrasi</div>
                        <div class="flow-desc">Pendaftaran & Validasi Peserta</div>
                    </div>
                    <!-- Phase 3 -->
                    <div class="flow-step warning">
                        <div class="flow-icon"><i class="fas fa-play-circle"></i></div>
                        <div class="flow-title">3. Pelaksanaan</div>
                        <div class="flow-desc">Undian & Penugasan Juri</div>
                    </div>
                    <!-- Phase 4 -->
                    <div class="flow-step danger">
                        <div class="flow-icon"><i class="fas fa-poll"></i></div>
                        <div class="flow-title">4. Penilaian</div>
                        <div class="flow-desc">Input Nilai & Monitoring</div>
                    </div>
                    <!-- Phase 5 -->
                    <div class="flow-step success">
                        <div class="flow-icon"><i class="fas fa-trophy"></i></div>
                        <div class="flow-title">5. Hasil</div>
                        <div class="flow-desc">Peringkat & Pengumuman</div>
                    </div>
                    <!-- Phase 6 -->
                    <div class="flow-step" style="color: #6f42c1;">
                        <div class="flow-icon" style="color: #6f42c1; border-color: #6f42c1;"><i class="fas fa-certificate"></i></div>
                        <div class="flow-title">6. Sertifikat</div>
                        <div class="flow-desc">Cetak & Distribusi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Guide Sections -->
        <h4 class="mb-3 mt-4"><i class="fas fa-info-circle text-primary"></i> Penjelasan Detail Modul</h4>
        
        <div class="row">
            <!-- Modul 1 -->
            <div class="col-md-4 mb-4">
                <div class="card help-card border-left border-primary">
                    <div class="card-body">
                        <span class="phase-badge badge badge-primary">Fase 1: Persiapan</span>
                        <h5><i class="fas fa-list-alt text-primary"></i> Master Lomba & Cabang</h5>
                        <p class="small text-muted">
                            Admin/Operator membuat event perlombaan utama, menentukan cabang lomba (Individu/Kelompok), serta mengatur batasan usia atau kelas peserta.
                        </p>
                        <hr>
                        <ul class="small mb-0 pl-3">
                            <li>Atur Kriteria Penilaian per cabang.</li>
                            <li>Tentukan maksimal peserta per TPQ.</li>
                            <li>Status lomba Harus <strong>Aktif</strong> agar bisa digunakan.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modul 2 -->
            <div class="col-md-4 mb-4">
                <div class="card help-card border-left border-success">
                    <div class="card-body">
                        <span class="phase-badge badge badge-success">Fase 2: Registrasi</span>
                        <h5><i class="fas fa-id-card text-success"></i> Pendaftaran Peserta</h5>
                        <p class="small text-muted">
                            Pilih santri yang akan diikutkan. Sistem secara otomatis memvalidasi apakah santri sesuai dengan syarat (Usia/Kelas) di cabang tersebut.
                        </p>
                        <hr>
                        <ul class="small mb-0 pl-3">
                            <li>Status pendaftaran otomatis <strong>Valid</strong> jika memenuhi syarat.</li>
                            <li>Satu santri hanya bisa didaftarkan satu kali per cabang.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modul 3 -->
            <div class="col-md-4 mb-4">
                <div class="card help-card border-left border-warning">
                    <div class="card-body">
                        <span class="phase-badge badge badge-warning">Fase 3: Pelaksanaan (1)</span>
                        <h5><i class="fas fa-random text-warning"></i> Pengundian (Draw)</h5>
                        <p class="small text-muted">
                            Lakukan pengocokan nomor urut tampil untuk peserta yang sudah terdaftar secara transparan dan otomatis oleh sistem.
                        </p>
                        <hr>
                        <ul class="small mb-0 pl-3">
                            <li>Nomor peserta otomatis tersimpan.</li>
                            <li>Daftar dapat diekspor ke <strong>Excel/PDF</strong> untuk publikasi.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modul 4 -->
            <div class="col-md-4 mb-4">
                <div class="card help-card border-left border-info">
                    <div class="card-body">
                        <span class="phase-badge badge badge-info">Fase 3: Pelaksanaan (2)</span>
                        <h5><i class="fas fa-user-tie text-info"></i> Pengaturan Juri</h5>
                        <p class="small text-muted">
                            Menunjuk user juri untuk bertugas di cabang tertentu. Akun juri otomatis dibuatkan oleh sistem jika belum ada.
                        </p>
                        <hr>
                        <ul class="small mb-0 pl-3">
                            <li>Bisa membagi kriteria khusus untuk juri tertentu.</li>
                            <li>Sistem juri rata-rata atau juri split kriteria.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modul 5 -->
            <div class="col-md-4 mb-4">
                <div class="card help-card border-left border-danger">
                    <div class="card-body">
                        <span class="phase-badge badge badge-danger">Fase 4: Penilaian</span>
                        <h5><i class="fas fa-chart-line text-danger"></i> Monitor Nilai</h5>
                        <p class="small text-muted">
                            Fitur khusus Admin & Operator untuk memantau progres penilaian juri secara live agar proses perlombaan berjalan lancar.
                        </p>
                        <hr>
                        <ul class="small mb-0 pl-3">
                            <li>Lihat progres per juri vs total peserta.</li>
                            <li>Deteksi dini jika ada juri yang belum input nilai.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modul 6 -->
            <div class="col-md-4 mb-4">
                <div class="card help-card border-left border-success">
                    <div class="card-body">
                        <span class="phase-badge badge badge-success">Fase 5: Hasil</span>
                        <h5><i class="fas fa-award text-success"></i> Juara & Peringkat</h5>
                        <p class="small text-muted">
                            Sistem menghitung total bobot nilai dari semua juri secara otomatis untuk menentukan peringkat pemenang berdasarkan cabang.
                        </p>
                        <hr>
                        <ul class="small mb-0 pl-3">
                            <li>Perhitungan otomatis Nilai Bobot.</li>
                            <li>Normalisasi nilai jika menggunakan juri jamak.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modul 7 -->
            <div class="col-md-4 mb-4">
                <div class="card help-card border-left border-purple" style="border-left-color: #6f42c1 !important;">
                    <div class="card-body">
                        <span class="phase-badge badge badge-purple" style="background-color: #6f42c1; color: white;">Fase 6: Sertifikat</span>
                        <h5><i class="fas fa-certificate" style="color: #6f42c1;"></i> E-Sertifikat</h5>
                        <p class="small text-muted">
                            Fitur canggih untuk mencetak sertifikat pemenang secara otomatis menggunakan template dinamis yang bisa dikustomisasi.
                        </p>
                        <hr>
                        <ul class="small mb-0 pl-3">
                            <li><strong>Upload:</strong> Template JPG/PNG.</li>
                            <li><strong>Config:</strong> Drag & Drop posisi.</li>
                            <li><strong>Unduh:</strong> Batch ZIP / PDF Satuan.</li>
                            <li><strong>Font:</strong> Arial, Times, Courier.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Note -->
        <div class="alert alert-info border-left shadow-sm">
            <h5><i class="fas fa-lightbulb"></i> Tips untuk Pengguna Baru:</h5>
            <ol class="mb-0">
                <li>Pastikan <strong>Tahun Ajaran</strong> aktif sebelum membuat Master Lomba.</li>
                <li>Gunakan Dashboard Perlombaan untuk navigasi cepat antar modul.</li>
                <li>Jika ada kendala akses juri, cek status akun juri di menu <strong>Setting Juri</strong>.</li>
            </ol>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
