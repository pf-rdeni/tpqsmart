<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-graduate"></i> Detail Profil Santri
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/dashboard/santri') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Foto Profil dan Info Dasar -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <img src="<?= esc($photoUrl) ?>" 
                                 alt="Foto Profil" 
                                 class="img-fluid rounded-circle mb-3"
                                 style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #dee2e6;"
                                 onerror="this.src='<?= base_url('images/no-photo.jpg') ?>'">
                            <h4 class="mb-1"><?= esc($santri['NamaSantri'] ?? '-') ?></h4>
                            <p class="text-muted mb-0">
                                <small>ID: <?= esc($santri['IdSantri'] ?? '-') ?></small>
                            </p>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>TPQ:</strong> <?= esc($santri['NamaTpq'] ?? '-') ?></p>
                                    <p><strong>Kelas:</strong> <?= esc($santri['NamaKelas'] ?? '-') ?></p>
                                    <p><strong>NIS:</strong> <?= esc($santri['NISN'] ?? '-') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>NIK:</strong> <?= esc($santri['NikSantri'] ?? '-') ?></p>
                                    <p><strong>No. KK:</strong> <?= esc($santri['IdKartuKeluarga'] ?? '-') ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge <?= (!empty($santri['Status']) && $santri['Status'] == 1) ? 'badge-success' : 'badge-secondary' ?>">
                                            <?= (!empty($santri['Status']) && $santri['Status'] == 1) ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="profilTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="info-santri-tab" data-toggle="tab" href="#info-santri" role="tab" aria-controls="info-santri" aria-selected="true">
                                <i class="fas fa-user"></i> Informasi Santri
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="orang-tua-tab" data-toggle="tab" href="#orang-tua" role="tab" aria-controls="orang-tua" aria-selected="false">
                                <i class="fas fa-users"></i> Data Orang Tua
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="lampiran-tab" data-toggle="tab" href="#lampiran" role="tab" aria-controls="lampiran" aria-selected="false">
                                <i class="fas fa-paperclip"></i> Lampiran
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="profilTabContent">
                        <!-- Tab: Informasi Santri -->
                        <div class="tab-pane fade show active" id="info-santri" role="tabpanel" aria-labelledby="info-santri-tab">
                            <div class="card-body">
                                <!-- Data Pribadi -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-user text-primary"></i> Data Pribadi</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-id-card"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Nama Lengkap</span>
                                                                <span class="info-box-number"><?= esc($santri['NamaSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-venus-mars"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Jenis Kelamin</span>
                                                                <span class="info-box-number"><?= esc($santri['JenisKelamin'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-birthday-cake"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Tempat, Tanggal Lahir</span>
                                                                <span class="info-box-number">
                                                                    <?= esc($santri['TempatLahirSantri'] ?? '-') ?>, 
                                                                    <?= !empty($santri['TanggalLahirSantri']) ? date('d F Y', strtotime($santri['TanggalLahirSantri'])) : '-' ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Anak Ke</span>
                                                                <span class="info-box-number"><?= esc($santri['AnakKe'] ?? '-') ?> dari <?= esc($santri['JumlahSaudara'] ?? '-') ?> bersaudara</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-phone"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">No. HP</span>
                                                                <span class="info-box-number"><?= esc($santri['NoHpSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-secondary"><i class="fas fa-envelope"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Email</span>
                                                                <span class="info-box-number"><?= esc($santri['EmailSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Akademik & Lainnya -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-success">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-graduation-cap text-success"></i> Data Akademik & Lainnya</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-star"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Cita-cita</span>
                                                                <span class="info-box-number"><?= esc($santri['CitaCita'] ?? ($santri['CitaCitaLainya'] ?? '-')) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-heart"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Hobi</span>
                                                                <span class="info-box-number"><?= esc($santri['Hobi'] ?? ($santri['HobiLainya'] ?? '-')) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-wheelchair"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kebutuhan Khusus</span>
                                                                <span class="info-box-number"><?= esc($santri['KebutuhanKhusus'] ?? ($santri['KebutuhanKhususLainya'] ?? '-')) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-accessible-icon"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kebutuhan Disabilitas</span>
                                                                <span class="info-box-number"><?= esc($santri['KebutuhanDisabilitas'] ?? ($santri['KebutuhanDisabilitasLainya'] ?? '-')) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Yang Membiayai Sekolah</span>
                                                                <span class="info-box-number"><?= esc($santri['YangBiayaSekolah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-secondary"><i class="fas fa-user-tie"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Nama Kepala Keluarga</span>
                                                                <span class="info-box-number"><?= esc($santri['NamaKepalaKeluarga'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alamat Santri -->
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-home text-info"></i> Alamat Santri</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-map-marked-alt"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Alamat Lengkap</span>
                                                                <span class="info-box-number"><?= esc($santri['AlamatSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-secondary"><i class="fas fa-building"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Status Mukim</span>
                                                                <span class="info-box-number"><?= esc($santri['StatusMukim'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-home"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Status Tempat Tinggal</span>
                                                                <span class="info-box-number"><?= esc($santri['StatusTempatTinggalSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-map"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Provinsi</span>
                                                                <span class="info-box-number"><?= esc($santri['ProvinsiSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-city"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kabupaten/Kota</span>
                                                                <span class="info-box-number"><?= esc($santri['KabupatenKotaSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-secondary"><i class="fas fa-map-pin"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kecamatan</span>
                                                                <span class="info-box-number"><?= esc($santri['KecamatanSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-location-dot"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kelurahan/Desa</span>
                                                                <span class="info-box-number"><?= esc($santri['KelurahanDesaSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-hashtag"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">RT/RW</span>
                                                                <span class="info-box-number"><?= esc($santri['RtSantri'] ?? '-') ?>/<?= esc($santri['RwSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-mail-bulk"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kode Pos</span>
                                                                <span class="info-box-number"><?= esc($santri['KodePosSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-route"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Jarak Tempuh</span>
                                                                <span class="info-box-number"><?= esc($santri['JarakTempuhSantri'] ?? '-') ?> km</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-bus"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Transportasi</span>
                                                                <span class="info-box-number"><?= esc($santri['TransportasiSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-secondary"><i class="fas fa-clock"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Waktu Tempuh</span>
                                                                <span class="info-box-number"><?= esc($santri['WaktuTempuhSantri'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Data Orang Tua -->
                        <div class="tab-pane fade" id="orang-tua" role="tabpanel" aria-labelledby="orang-tua-tab">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Data Ayah -->
                                    <div class="col-md-12 mb-4">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-male text-info"></i> Data Ayah</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-user"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Nama Lengkap</span>
                                                                <span class="info-box-number"><?= esc($santri['NamaAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-secondary"><i class="fas fa-info-circle"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Status</span>
                                                                <span class="info-box-number"><?= esc($santri['StatusAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-id-card"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">NIK</span>
                                                                <span class="info-box-number"><?= esc($santri['NikAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-flag"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kewarganegaraan</span>
                                                                <span class="info-box-number"><?= esc($santri['KewarganegaraanAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-birthday-cake"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Tempat, Tanggal Lahir</span>
                                                                <span class="info-box-number">
                                                                    <?= esc($santri['TempatLahirAyah'] ?? '-') ?>, 
                                                                    <?= !empty($santri['TanggalLahirAyah']) ? date('d F Y', strtotime($santri['TanggalLahirAyah'])) : '-' ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Pendidikan</span>
                                                                <span class="info-box-number"><?= esc($santri['PendidikanAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-briefcase"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Pekerjaan</span>
                                                                <span class="info-box-number"><?= esc($santri['PekerjaanUtamaAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Penghasilan</span>
                                                                <span class="info-box-number"><?= esc($santri['PenghasilanUtamaAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-phone"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">No. HP</span>
                                                                <span class="info-box-number"><?= esc($santri['NoHpAyah'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Ibu -->
                                    <div class="col-md-12 mb-4">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-female text-danger"></i> Data Ibu</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-user"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Nama Lengkap</span>
                                                                <span class="info-box-number"><?= esc($santri['NamaIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-secondary"><i class="fas fa-info-circle"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Status</span>
                                                                <span class="info-box-number"><?= esc($santri['StatusIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-id-card"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">NIK</span>
                                                                <span class="info-box-number"><?= esc($santri['NikIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-flag"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kewarganegaraan</span>
                                                                <span class="info-box-number"><?= esc($santri['KewarganegaraanIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-birthday-cake"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Tempat, Tanggal Lahir</span>
                                                                <span class="info-box-number">
                                                                    <?= esc($santri['TempatLahirIbu'] ?? '-') ?>, 
                                                                    <?= !empty($santri['TanggalLahirIbu']) ? date('d F Y', strtotime($santri['TanggalLahirIbu'])) : '-' ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Pendidikan</span>
                                                                <span class="info-box-number"><?= esc($santri['PendidikanIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-briefcase"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Pekerjaan</span>
                                                                <span class="info-box-number"><?= esc($santri['PekerjaanUtamaIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Penghasilan</span>
                                                                <span class="info-box-number"><?= esc($santri['PenghasilanUtamaIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-phone"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">No. HP</span>
                                                                <span class="info-box-number"><?= esc($santri['NoHpIbu'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Wali (jika ada) -->
                                <?php if (!empty($santri['StatusWali']) && $santri['StatusWali'] == 'Ya'): ?>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-user-shield text-warning"></i> Data Wali</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-user"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Nama Lengkap</span>
                                                                <span class="info-box-number"><?= esc($santri['NamaWali'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-id-card"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">NIK</span>
                                                                <span class="info-box-number"><?= esc($santri['NikWali'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-flag"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Kewarganegaraan</span>
                                                                <span class="info-box-number"><?= esc($santri['KewarganegaraanWali'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-warning"><i class="fas fa-birthday-cake"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Tempat, Tanggal Lahir</span>
                                                                <span class="info-box-number">
                                                                    <?= esc($santri['TempatLahirWali'] ?? '-') ?>, 
                                                                    <?= !empty($santri['TanggalLahirWali']) ? date('d F Y', strtotime($santri['TanggalLahirWali'])) : '-' ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Pendidikan</span>
                                                                <span class="info-box-number"><?= esc($santri['PendidikanWali'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-primary"><i class="fas fa-briefcase"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Pekerjaan</span>
                                                                <span class="info-box-number"><?= esc($santri['PekerjaanUtamaWali'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Penghasilan</span>
                                                                <span class="info-box-number"><?= esc($santri['PenghasilanUtamaWali'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-danger"><i class="fas fa-phone"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">No. HP</span>
                                                                <span class="info-box-number"><?= esc($santri['NoHpWali'] ?? '-') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Alamat Orang Tua -->
                                <div class="row mt-4">
                                    <?php 
                                    // Cek apakah alamat ibu sama dengan ayah
                                    // Cek dari field AlamatIbuSamaDenganAyah
                                    $alamatIbuSamaField = (!empty($santri['AlamatIbuSamaDenganAyah']) && $santri['AlamatIbuSamaDenganAyah'] == 'Ya');
                                    
                                    // Cek juga dengan membandingkan isi alamat secara langsung
                                    $alamatAyah = trim($santri['AlamatAyah'] ?? '');
                                    $alamatIbu = trim($santri['AlamatIbu'] ?? '');
                                    $provinsiAyah = trim($santri['ProvinsiAyah'] ?? '');
                                    $provinsiIbu = trim($santri['ProvinsiIbu'] ?? '');
                                    $kabupatenAyah = trim($santri['KabupatenKotaAyah'] ?? '');
                                    $kabupatenIbu = trim($santri['KabupatenKotaIbu'] ?? '');
                                    $kecamatanAyah = trim($santri['KecamatanAyah'] ?? '');
                                    $kecamatanIbu = trim($santri['KecamatanIbu'] ?? '');
                                    $kelurahanAyah = trim($santri['KelurahanDesaAyah'] ?? '');
                                    $kelurahanIbu = trim($santri['KelurahanDesaIbu'] ?? '');
                                    $rtAyah = trim($santri['RtAyah'] ?? '');
                                    $rtIbu = trim($santri['RtIbu'] ?? '');
                                    $rwAyah = trim($santri['RwAyah'] ?? '');
                                    $rwIbu = trim($santri['RwIbu'] ?? '');
                                    $kodePosAyah = trim($santri['KodePosAyah'] ?? '');
                                    $kodePosIbu = trim($santri['KodePosIbu'] ?? '');
                                    
                                    // Bandingkan semua field alamat
                                    $alamatIbuSamaIsi = (
                                        $alamatAyah == $alamatIbu &&
                                        $provinsiAyah == $provinsiIbu &&
                                        $kabupatenAyah == $kabupatenIbu &&
                                        $kecamatanAyah == $kecamatanIbu &&
                                        $kelurahanAyah == $kelurahanIbu &&
                                        $rtAyah == $rtIbu &&
                                        $rwAyah == $rwIbu &&
                                        $kodePosAyah == $kodePosIbu &&
                                        !empty($alamatAyah) && !empty($alamatIbu)
                                    );
                                    
                                    // Gunakan salah satu kondisi yang true
                                    $alamatIbuSama = $alamatIbuSamaField || $alamatIbuSamaIsi;
                                    ?>
                                    
                                    <?php if ($alamatIbuSama): ?>
                                        <!-- Alamat Ayah & Ibu (Sama) -->
                                        <div class="col-md-12 mb-4">
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                    <h3 class="card-title"><i class="fas fa-map-marker-alt text-success"></i> Alamat Ayah & Ibu</h3>
                                                </div>
                                                <div class="card-body">
                                                    <?php if (!empty($santri['TinggalDiluarNegeriAyah']) && $santri['TinggalDiluarNegeriAyah'] == 'Ya'): ?>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Ayah tinggal di luar negeri
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="row">
                                                            <div class="col-md-12 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-map-marked-alt"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Alamat Lengkap</span>
                                                                        <span class="info-box-number"><?= esc($santri['AlamatAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-home"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Status Kepemilikan</span>
                                                                        <span class="info-box-number"><?= esc($santri['StatusKepemilikanRumahAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-warning"><i class="fas fa-map"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Provinsi</span>
                                                                        <span class="info-box-number"><?= esc($santri['ProvinsiAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-danger"><i class="fas fa-city"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kabupaten/Kota</span>
                                                                        <span class="info-box-number"><?= esc($santri['KabupatenKotaAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-secondary"><i class="fas fa-map-pin"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kecamatan</span>
                                                                        <span class="info-box-number"><?= esc($santri['KecamatanAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-primary"><i class="fas fa-location-dot"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kelurahan/Desa</span>
                                                                        <span class="info-box-number"><?= esc($santri['KelurahanDesaAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-info"><i class="fas fa-hashtag"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">RT/RW</span>
                                                                        <span class="info-box-number"><?= esc($santri['RtAyah'] ?? '-') ?>/<?= esc($santri['RwAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-mail-bulk"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kode Pos</span>
                                                                        <span class="info-box-number"><?= esc($santri['KodePosAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Alamat Ayah -->
                                        <div class="col-md-12 mb-4">
                                            <div class="card card-outline card-info">
                                                <div class="card-header">
                                                    <h3 class="card-title"><i class="fas fa-map-marker-alt text-info"></i> Alamat Ayah</h3>
                                                </div>
                                                <div class="card-body">
                                                    <?php if (!empty($santri['TinggalDiluarNegeriAyah']) && $santri['TinggalDiluarNegeriAyah'] == 'Ya'): ?>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Ayah tinggal di luar negeri
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="row">
                                                            <div class="col-md-12 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-info"><i class="fas fa-map-marked-alt"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Alamat Lengkap</span>
                                                                        <span class="info-box-number"><?= esc($santri['AlamatAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-home"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Status Kepemilikan</span>
                                                                        <span class="info-box-number"><?= esc($santri['StatusKepemilikanRumahAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-warning"><i class="fas fa-map"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Provinsi</span>
                                                                        <span class="info-box-number"><?= esc($santri['ProvinsiAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-danger"><i class="fas fa-city"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kabupaten/Kota</span>
                                                                        <span class="info-box-number"><?= esc($santri['KabupatenKotaAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-secondary"><i class="fas fa-map-pin"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kecamatan</span>
                                                                        <span class="info-box-number"><?= esc($santri['KecamatanAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-primary"><i class="fas fa-location-dot"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kelurahan/Desa</span>
                                                                        <span class="info-box-number"><?= esc($santri['KelurahanDesaAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-info"><i class="fas fa-hashtag"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">RT/RW</span>
                                                                        <span class="info-box-number"><?= esc($santri['RtAyah'] ?? '-') ?>/<?= esc($santri['RwAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-mail-bulk"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kode Pos</span>
                                                                        <span class="info-box-number"><?= esc($santri['KodePosAyah'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Alamat Ibu -->
                                        <div class="col-md-12 mb-4">
                                            <div class="card card-outline card-danger">
                                                <div class="card-header">
                                                    <h3 class="card-title"><i class="fas fa-map-marker-alt text-danger"></i> Alamat Ibu</h3>
                                                </div>
                                                <div class="card-body">
                                                    <?php if (!empty($santri['TinggalDiluarNegeriIbu']) && $santri['TinggalDiluarNegeriIbu'] == 'Ya'): ?>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Ibu tinggal di luar negeri
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="row">
                                                            <div class="col-md-12 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-danger"><i class="fas fa-map-marked-alt"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Alamat Lengkap</span>
                                                                        <span class="info-box-number"><?= esc($santri['AlamatIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-home"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Status Kepemilikan</span>
                                                                        <span class="info-box-number"><?= esc($santri['StatusKepemilikanRumahIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-warning"><i class="fas fa-map"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Provinsi</span>
                                                                        <span class="info-box-number"><?= esc($santri['ProvinsiIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-danger"><i class="fas fa-city"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kabupaten/Kota</span>
                                                                        <span class="info-box-number"><?= esc($santri['KabupatenKotaIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-secondary"><i class="fas fa-map-pin"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kecamatan</span>
                                                                        <span class="info-box-number"><?= esc($santri['KecamatanIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-primary"><i class="fas fa-location-dot"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kelurahan/Desa</span>
                                                                        <span class="info-box-number"><?= esc($santri['KelurahanDesaIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-info"><i class="fas fa-hashtag"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">RT/RW</span>
                                                                        <span class="info-box-number"><?= esc($santri['RtIbu'] ?? '-') ?>/<?= esc($santri['RwIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="info-box bg-light">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-mail-bulk"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Kode Pos</span>
                                                                        <span class="info-box-number"><?= esc($santri['KodePosIbu'] ?? '-') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Lampiran -->
                        <div class="tab-pane fade" id="lampiran" role="tabpanel" aria-labelledby="lampiran-tab">
                            <div class="card-body">
                                <?php
                                // Fungsi helper untuk mendapatkan icon dan warna berdasarkan ekstensi
                                function getFileIcon($fileName) {
                                    if (empty($fileName)) {
                                        return ['icon' => 'fa-file', 'color' => 'secondary', 'bgColor' => 'bg-secondary'];
                                    }
                                    
                                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                    
                                    switch ($extension) {
                                        case 'pdf':
                                            return ['icon' => 'fa-file-pdf', 'color' => 'danger', 'bgColor' => 'bg-danger'];
                                        case 'jpg':
                                        case 'jpeg':
                                        case 'png':
                                        case 'gif':
                                        case 'webp':
                                            return ['icon' => 'fa-file-image', 'color' => 'info', 'bgColor' => 'bg-info'];
                                        case 'doc':
                                        case 'docx':
                                            return ['icon' => 'fa-file-word', 'color' => 'primary', 'bgColor' => 'bg-primary'];
                                        case 'xls':
                                        case 'xlsx':
                                            return ['icon' => 'fa-file-excel', 'color' => 'success', 'bgColor' => 'bg-success'];
                                        default:
                                            return ['icon' => 'fa-file', 'color' => 'secondary', 'bgColor' => 'bg-secondary'];
                                    }
                                }
                                
                                // Kumpulkan semua dokumen dalam satu array
                                $documents = [];
                                
                                // Dokumen Santri
                                if (!empty($santri['FileKIP'])) {
                                    $documents[] = [
                                        'label' => 'File KIP',
                                        'fileName' => $santri['FileKIP'],
                                        'type' => 'santri'
                                    ];
                                }
                                
                                if (!empty($santri['FileKKS'])) {
                                    $documents[] = [
                                        'label' => 'Kartu Keluarga Sejahtera (KKS)',
                                        'fileName' => $santri['FileKKS'],
                                        'type' => 'santri'
                                    ];
                                }
                                
                                if (!empty($santri['FilePKH'])) {
                                    $documents[] = [
                                        'label' => 'Program Keluarga Harapan (PKH)',
                                        'fileName' => $santri['FilePKH'],
                                        'type' => 'santri'
                                    ];
                                }
                                
                                // Kartu Keluarga - gabungkan jika sama
                                $kkFiles = [];
                                $kkLabels = [];
                                
                                // Kartu Keluarga Santri
                                if (!empty($santri['FileKkSantri'])) {
                                    $kkFiles[] = $santri['FileKkSantri'];
                                    $kkLabels[] = 'Kartu Keluarga Santri';
                                }
                                
                                // Kartu Keluarga Ayah
                                $fileKkAyah = $santri['FileKkAyah'] ?? ($santri['FileKKAyah'] ?? null);
                                if (!empty($fileKkAyah)) {
                                    $kkFiles[] = $fileKkAyah;
                                    $kkLabels[] = 'Kartu Keluarga Ayah';
                                }
                                
                                // Kartu Keluarga Ibu
                                $fileKkIbu = $santri['FileKkIbu'] ?? ($santri['FileKKIbu'] ?? null);
                                if (!empty($fileKkIbu)) {
                                    $kkFiles[] = $fileKkIbu;
                                    $kkLabels[] = 'Kartu Keluarga Ibu';
                                }
                                
                                // Kartu Keluarga Wali
                                $fileKkWali = $santri['FileKkWali'] ?? ($santri['FileKKWali'] ?? null);
                                if (!empty($fileKkWali)) {
                                    $kkFiles[] = $fileKkWali;
                                    $kkLabels[] = 'Kartu Keluarga Wali';
                                }
                                
                                // Jika ada Kartu Keluarga, gabungkan yang sama
                                if (!empty($kkFiles)) {
                                    $uniqueKkFiles = array_unique($kkFiles);
                                    foreach ($uniqueKkFiles as $kkFile) {
                                        // Cari semua label yang memiliki file ini
                                        $matchingLabels = [];
                                        foreach ($kkFiles as $index => $file) {
                                            if ($file === $kkFile) {
                                                $matchingLabels[] = $kkLabels[$index];
                                            }
                                        }
                                        
                                        // Jika hanya satu, gunakan label asli, jika lebih dari satu gabungkan
                                        if (count($matchingLabels) > 1) {
                                            $label = 'Kartu Keluarga';
                                        } else {
                                            $label = $matchingLabels[0];
                                        }
                                        
                                        $documents[] = [
                                            'label' => $label,
                                            'fileName' => $kkFile,
                                            'type' => 'kk'
                                        ];
                                    }
                                }
                                
                                $basePath = FCPATH . 'uploads/santri/';
                                ?>
                                
                                <?php if (empty($documents)): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <strong>Belum ada dokumen yang diunggah.</strong>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($documents as $doc): 
                                            $fileExists = file_exists($basePath . $doc['fileName']);
                                            $fileIcon = getFileIcon($doc['fileName']);
                                            $fileUrl = base_url('uploads/santri/' . $doc['fileName']);
                                            $fileSize = $fileExists ? filesize($basePath . $doc['fileName']) : 0;
                                            $fileSizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024, 2) . ' KB' : '-';
                                        ?>
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card card-outline card-<?= $fileIcon['color'] ?> h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="info-box-icon <?= $fileIcon['bgColor'] ?> mr-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                                                <i class="fas <?= $fileIcon['icon'] ?> fa-2x text-white"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0 font-weight-bold"><?= esc($doc['label']) ?></h6>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-file"></i> <?= esc($doc['fileName']) ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                        
                                                        <?php if ($fileExists): ?>
                                                            <div class="mb-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-hdd"></i> Ukuran: <?= $fileSizeFormatted ?>
                                                                </small>
                                                            </div>
                                                            <div class="d-flex gap-2">
                                                                <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-<?= $fileIcon['color'] ?> flex-fill">
                                                                    <i class="fas fa-eye"></i> Lihat
                                                                </a>
                                                                <a href="<?= $fileUrl ?>" download class="btn btn-sm btn-outline-<?= $fileIcon['color'] ?>">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="alert alert-warning mb-0 py-2">
                                                                <small>
                                                                    <i class="fas fa-exclamation-triangle"></i> File tidak ditemukan
                                                                </small>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Info Tambahan -->
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> 
                                                <strong>Informasi:</strong> Klik tombol "Lihat" untuk melihat preview file atau tombol download untuk mengunduh file.
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

