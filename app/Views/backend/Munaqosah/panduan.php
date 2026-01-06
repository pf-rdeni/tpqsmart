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
        width: 16%;
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
    .flow-step.active .flow-icon { border-color: #007bff; color: #007bff; box-shadow: 0 0 15px rgba(0,123,255,0.3); }
    .flow-step.success .flow-icon { border-color: #28a745; color: #28a745; }
    .flow-step.warning .flow-icon { border-color: #ffc107; color: #ffc107; }
    .flow-step.info .flow-icon { border-color: #17a2b8; color: #17a2b8; }
    .flow-step.danger .flow-icon { border-color: #dc3545; color: #dc3545; }
    
    .flow-title { font-weight: bold; font-size: 1rem; margin-bottom: 5px; }
    .flow-desc { font-size: 0.8rem; color: #6c757d; line-height: 1.2; }

    /* Explanation Card Styling */
    .help-card { transition: transform 0.2s; height: 100%; border-top: 3px solid #dee2e6; }
    .help-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .phase-badge { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: inline-block; }
    
    .nav-pills .nav-link.active { background-color: #007bff; box-shadow: 0 4px 10px rgba(0,123,255,0.2); }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="munaqosah-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pra-munaqosah-tab" data-toggle="pill" href="#pra-munaqosah" role="tab">
                                    <i class="fas fa-home"></i> Panduan Pra-Munaqosah (Internal TPQ)
                                </a>
                            </li>
                            <?php if ($isAdmin): ?>
                            <li class="nav-item">
                                <a class="nav-link" id="munaqosah-umum-tab" data-toggle="pill" href="#munaqosah-umum" role="tab">
                                    <i class="fas fa-globe"></i> Panduan Munaqosah Umum (FKPQ)
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="munaqosah-tabContent">
                            
                            <!-- TAB 1: PRA-MUNAQOSAH -->
                            <div class="tab-pane fade show active" id="pra-munaqosah" role="tabpanel">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Apa itu Pra-Munaqosah?</h5>
                                    Pra-Munaqosah adalah simulasi atau ujian internal yang dikelola mandiri oleh TPQ untuk mempersiapkan santri sebelum mengikuti Munaqosah Umum FKPQ.
                                </div>

                                <!-- Flow Chart Pra-Munaqosah -->
                                <div class="flow-wrapper">
                                    <div class="flow-step active">
                                        <div class="flow-icon"><i class="fas fa-tools"></i></div>
                                        <div class="flow-title">1. Setup</div>
                                        <div class="flow-desc">Atur Bobot & Materi Internal</div>
                                    </div>
                                    <div class="flow-step success">
                                        <div class="flow-icon"><i class="fas fa-user-plus"></i></div>
                                        <div class="flow-title">2. Registrasi</div>
                                        <div class="flow-desc">Input Santri & No Peserta</div>
                                    </div>
                                    <div class="flow-step info">
                                        <div class="flow-icon"><i class="fas fa-clipboard-check"></i></div>
                                        <div class="flow-title">3. Verifikasi</div>
                                        <div class="flow-desc">Cek Kelengkapan Data</div>
                                    </div>
                                    <div class="flow-step warning">
                                        <div class="flow-icon"><i class="fas fa-door-open"></i></div>
                                        <div class="flow-title">4. Ruangan</div>
                                        <div class="flow-desc">Tentukan Ustad Penilai</div>
                                    </div>
                                    <div class="flow-step danger">
                                        <div class="flow-icon"><i class="fas fa-pen-nib"></i></div>
                                        <div class="flow-title">5. Penilaian</div>
                                        <div class="flow-desc">Input Nilai Form Internal</div>
                                    </div>
                                    <div class="flow-step success">
                                        <div class="flow-icon"><i class="fas fa-clipboard-list"></i></div>
                                        <div class="flow-title">6. Hasil</div>
                                        <div class="flow-desc">Rekap Lulus/Tidak Lulus</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #007bff;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-primary">Fase 1: Setup</span>
                                                <h6><i class="fas fa-tools"></i> Konfigurasi Bobot & Materi</h6>
                                                <p class="small text-muted mb-2">Operator TPQ mengatur bobot penilaian untuk setiap materi ujian (Tahsin, Tahfidz, Tajwid, dll) melalui menu <strong>Bobot Nilai</strong>.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Tentukan persentase tiap materi (total harus 100%)</li>
                                                    <li>Sesuaikan dengan kurikulum internal TPQ</li>
                                                    <li>Simpan konfigurasi untuk digunakan saat penilaian</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #28a745;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-success">Fase 2: Registrasi</span>
                                                <h6><i class="fas fa-user-plus"></i> Pendaftaran & Penomoran</h6>
                                                <p class="small text-muted mb-2">Daftarkan santri yang akan mengikuti ujian internal dan berikan nomor peserta unik melalui menu <strong>Data Peserta</strong>.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Pilih santri dari database TPQ</li>
                                                    <li>Generate nomor peserta otomatis atau manual</li>
                                                    <li>Nomor peserta digunakan untuk identifikasi saat ujian</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #17a2b8;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-info">Fase 3: Verifikasi</span>
                                                <h6><i class="fas fa-clipboard-check"></i> Cek Kelengkapan Data</h6>
                                                <p class="small text-muted mb-2">Pastikan semua data peserta sudah lengkap dan valid sebelum pelaksanaan ujian.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Verifikasi identitas santri (nama, kelas, usia)</li>
                                                    <li>Cek kelengkapan nomor peserta</li>
                                                    <li>Konfirmasi kesiapan peserta untuk ujian</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #ffc107;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-warning">Fase 4: Ruangan</span>
                                                <h6><i class="fas fa-door-open"></i> Penugasan Ustadz Penilai</h6>
                                                <p class="small text-muted mb-2">Tentukan ustadz/ustadzah yang akan menjadi penguji untuk ujian internal.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Pilih ustadz yang kompeten di bidangnya</li>
                                                    <li>Atur pembagian ruangan jika diperlukan</li>
                                                    <li>Berikan akses login untuk input nilai</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #dc3545;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-danger">Fase 5: Penilaian</span>
                                                <h6><i class="fas fa-pen-nib"></i> Input Nilai Ujian</h6>
                                                <p class="small text-muted mb-2">Ustadz penguji memasukkan nilai untuk setiap peserta melalui menu <strong>Input Nilai Juri</strong>.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Login sebagai Juri dengan akun yang diberikan</li>
                                                    <li>Input nilai per materi sesuai kriteria penilaian</li>
                                                    <li>Sistem otomatis menghitung nilai akhir berdasarkan bobot</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #28a745;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-success">Fase 6: Hasil</span>
                                                <h6><i class="fas fa-clipboard-list"></i> Rekap & Kelulusan</h6>
                                                <p class="small text-muted mb-2">Lihat hasil akhir ujian dan tentukan status kelulusan santri.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Sistem menampilkan nilai akhir otomatis</li>
                                                    <li>Tentukan batas nilai kelulusan (passing grade)</li>
                                                    <li>Export hasil untuk dokumentasi TPQ</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($isAdmin): ?>
                            <!-- TAB 2: MUNAQOSAH UMUM -->
                            <div class="tab-pane fade" id="munaqosah-umum" role="tabpanel">
                                <div class="alert alert-success">
                                    <h5><i class="icon fas fa-check"></i> Apa itu Munaqosah Umum?</h5>
                                    Munaqosah Umum adalah ujian resmi yang dikoordinasikan oleh FKPQ. Melibatkan sistem antrian real-time, pembagian ruangan otomatis, dan pencetakan sertifikat resmi.
                                </div>

                                <!-- Flow Chart Munaqosah Umum -->
                                <div class="flow-wrapper">
                                    <div class="flow-step active">
                                        <div class="flow-icon"><i class="fas fa-cog"></i></div>
                                        <div class="flow-title">1. Setup</div>
                                        <div class="flow-desc">Bobot & Materi Pusat</div>
                                    </div>
                                    <div class="flow-step success">
                                        <div class="flow-icon"><i class="fas fa-calendar-alt"></i></div>
                                        <div class="flow-title">2. Jadwal</div>
                                        <div class="flow-desc">Setting FKPQ & Lokasi</div>
                                    </div>
                                    <div class="flow-step info">
                                        <div class="flow-icon"><i class="fas fa-id-badge"></i></div>
                                        <div class="flow-title">3. Registrasi</div>
                                        <div class="flow-desc">Generate No & Kartu</div>
                                    </div>
                                    <div class="flow-step warning">
                                        <div class="flow-icon"><i class="fas fa-university"></i></div>
                                        <div class="flow-title">4. Pelaksanaan</div>
                                        <div class="flow-desc">Antrian & Check-in Ruangan</div>
                                    </div>
                                    <div class="flow-step danger">
                                        <div class="flow-icon"><i class="fas fa-user-check"></i></div>
                                        <div class="flow-title">5. Penilaian</div>
                                        <div class="flow-desc">Juri & Monitoring Live</div>
                                    </div>
                                    <div class="flow-step success">
                                        <div class="flow-icon"><i class="fas fa-certificate"></i></div>
                                        <div class="flow-title">6. Hasil</div>
                                        <div class="flow-desc">Cetak Sertifikat & Syahadah</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #007bff;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-primary">Fase 1: Setup</span>
                                                <h6><i class="fas fa-cog"></i> Konfigurasi Pusat FKPQ</h6>
                                                <p class="small text-muted mb-2">Admin FKPQ mengatur master bobot nilai dan materi ujian yang berlaku untuk semua TPQ di tingkat daerah.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Tentukan standar bobot penilaian pusat</li>
                                                    <li>Atur materi ujian yang wajib diikuti semua TPQ</li>
                                                    <li>Konfigurasi berlaku untuk seluruh peserta Munaqosah Umum</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #28a745;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-success">Fase 2: Jadwal</span>
                                                <h6><i class="fas fa-calendar-alt"></i> Penjadwalan Ujian</h6>
                                                <p class="small text-muted mb-2">Admin FKPQ menentukan tanggal, waktu, dan lokasi pelaksanaan Munaqosah Umum melalui menu <strong>Jadwal Ujian</strong>.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Tentukan tanggal dan waktu pelaksanaan</li>
                                                    <li>Atur lokasi ujian (gedung, aula, masjid)</li>
                                                    <li>Informasi jadwal otomatis tersedia untuk semua TPQ</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #17a2b8;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-info">Fase 3: Registrasi</span>
                                                <h6><i class="fas fa-id-badge"></i> Generate Nomor & Kartu</h6>
                                                <p class="small text-muted mb-2">Operator TPQ mendaftarkan santri dan sistem otomatis generate nomor peserta serta kartu ujian.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Daftarkan santri dari TPQ masing-masing</li>
                                                    <li>Sistem generate nomor peserta unik otomatis</li>
                                                    <li>Cetak kartu peserta dengan QR Code untuk check-in</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #ffc107;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-warning">Fase 4: Pelaksanaan</span>
                                                <h6><i class="fas fa-university"></i> Antrian & Ruangan</h6>
                                                <p class="small text-muted mb-2">Panitia mengelola antrian peserta dan sistem otomatis assign peserta ke ruangan yang tersedia.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Peserta scan QR Code kartu untuk check-in antrian</li>
                                                    <li>Sistem auto-assign peserta ke ruangan juri yang available</li>
                                                    <li>Monitor real-time status antrian melalui dashboard</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #dc3545;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-danger">Fase 5: Penilaian</span>
                                                <h6><i class="fas fa-user-check"></i> Juri & Monitoring</h6>
                                                <p class="small text-muted mb-2">Juri FKPQ melakukan penilaian dan admin memantau progres secara live.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Juri login dan input nilai peserta yang masuk ruangannya</li>
                                                    <li>Admin monitoring progres penilaian semua juri secara real-time</li>
                                                    <li>Sistem otomatis kalkulasi nilai akhir berdasarkan bobot pusat</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card help-card" style="border-top-color: #28a745;">
                                            <div class="card-body">
                                                <span class="phase-badge badge badge-success">Fase 6: Hasil</span>
                                                <h6><i class="fas fa-certificate"></i> Sertifikat Resmi</h6>
                                                <p class="small text-muted mb-2">Admin FKPQ validasi hasil akhir dan cetak sertifikat/syahadah resmi untuk peserta yang lulus.</p>
                                                <ul class="small mb-0 pl-3">
                                                    <li>Verifikasi dan validasi nilai akhir semua peserta</li>
                                                    <li>Cetak sertifikat resmi FKPQ secara massal</li>
                                                    <li>Export rekap hasil untuk dokumentasi dan arsip</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Access Info -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-gray">
                        <h3 class="card-title"><i class="fas fa-user-cog"></i> Peran Operator (TPQ)</h3>
                    </div>
                    <div class="card-body small">
                        <ul>
                            <li>Menginput data santri untuk Pra-Munaqosah dan Munaqosah Umum.</li>
                            <li>Melakukan <strong>Registrasi (Penomoran)</strong> santri agar mendapatkan nomor peserta.</li>
                            <li>Mengunggah berkas persyaratan santri jika diperlukan.</li>
                            <li>Melihat rekap nilai dan status kelulusan santri di TPQ-nya sendiri.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title"><i class="fas fa-shield-alt"></i> Peran Admin (FKPQ)</h3>
                    </div>
                    <div class="card-body small">
                        <ul>
                            <li>Menentukan jadwal ujian nasional/umum dan master materi.</li>
                            <li>Melakukan <strong>Verifikasi & Konfirmasi</strong> final data peserta dari semua TPQ.</li>
                            <li>Mengelola antrian ruangan dan penugasan juri tingkat daerah.</li>
                            <li>Mengesahkan nilai akhir dan menerbitkan Sertifikat Munaqosah resmi.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
