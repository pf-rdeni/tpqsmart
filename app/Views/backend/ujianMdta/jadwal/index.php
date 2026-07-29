<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
.table-responsive-custom {
    min-height: 380px !important;
    padding-bottom: 160px !important;
    -webkit-overflow-scrolling: touch;
}
.table-responsive-custom .dropdown-menu {
    z-index: 1070 !important;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2) !important;
}
div.dataTables_wrapper {
    padding: 1rem 1.25rem !important;
}
div.dataTables_wrapper .dataTables_length,
div.dataTables_wrapper .dataTables_filter {
    margin-bottom: 0.75rem !important;
}
div.dataTables_wrapper .dataTables_info,
div.dataTables_wrapper .dataTables_paginate {
    margin-top: 0.75rem !important;
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold"><i class="fas fa-calendar-alt text-success me-2"></i> Jadwal Ujian MDTA</h4>
                <small class="text-muted">Pengaturan jadwal pelaksanaan ujian online per kelas dan remedial</small>
            </div>
            <a href="<?= base_url('backend/ujian-mdta/jadwal/create') ?>" class="btn btn-success btn-sm">
                <i class="fas fa-calendar-plus me-1"></i> Buat Jadwal Ujian Baru
            </a>
        </div>
    </div>

    <!-- Nav Tabs Status Arsip -->
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item me-2">
            <a class="nav-link <?= ($filter['arsip'] ?? '0') === '0' ? 'active bg-success text-white fw-bold' : 'btn btn-outline-secondary' ?>" 
               href="<?= base_url('backend/ujian-mdta/jadwal') ?>?arsip=0<?= !empty($filter['ta']) && $filter['ta'] !== 'all' ? '&ta=' . urlencode($filter['ta']) : '' ?>">
                <i class="fas fa-calendar-check me-1"></i> Jadwal Aktif / Belum Diarsip
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($filter['arsip'] ?? '0') === '1' ? 'active bg-secondary text-white fw-bold' : 'btn btn-outline-secondary' ?>" 
               href="<?= base_url('backend/ujian-mdta/jadwal') ?>?arsip=1<?= !empty($filter['ta']) && $filter['ta'] !== 'all' ? '&ta=' . urlencode($filter['ta']) : '' ?>">
                <i class="fas fa-archive me-1"></i> Arsip Jadwal
            </a>
        </li>
    </ul>

    <!-- Filter Bar Card AdminLTE Style -->
    <div class="card card-default card-outline card-success shadow-sm mb-3">
        <div class="card-body p-2">
            <div class="row g-2 align-items-center">
                <!-- Search Input -->
                <div class="col-md-3 col-12">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm" id="filterSearch" placeholder="Cari nama ujian / paket..." onkeyup="applyJadwalFilters()">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default btn-sm" onclick="applyJadwalFilters()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Tahun Ajaran -->
                <div class="col-md-3 col-6">
                    <select class="form-control form-control-sm" id="filterTa" onchange="changeTaFilter(this.value)">
                        <?php if ($isAdmin): ?>
                            <option value="all" <?= ($filter['ta'] ?? 'all') === 'all' ? 'selected' : '' ?>>-- Semua Tahun Ajaran --</option>
                        <?php endif; ?>
                        <?php foreach ($taList as $ta): ?>
                            <option value="<?= esc($ta) ?>" <?= ($filter['ta'] ?? '') === $ta ? 'selected' : '' ?>>
                                TA <?= esc($ta) ?> <?= (!$isAdmin && $ta === $sessionTa) ? '(Aktif Sidebar)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Kelas -->
                <div class="col-md-2 col-6">
                    <select class="form-control form-control-sm" id="filterKelas" onchange="applyJadwalFilters()">
                        <option value="">-- Semua Kelas --</option>
                        <?php
                        $kelasList = [];
                        foreach ($jadwalList as $item) {
                            if (!empty($item['NamaKelas']) && !in_array($item['NamaKelas'], $kelasList)) {
                                $kelasList[] = $item['NamaKelas'];
                            }
                        }
                        sort($kelasList);
                        foreach ($kelasList as $kls):
                        ?>
                            <option value="<?= esc($kls) ?>"><?= esc($kls) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-md-2 col-6">
                    <select class="form-control form-control-sm" id="filterStatus" onchange="applyJadwalFilters()">
                        <option value="">-- Status --</option>
                        <option value="draft">Draft</option>
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <!-- Reset Filter Button -->
                <div class="col-md-2 col-12 text-end">
                    <button type="button" class="btn btn-default btn-sm w-100" onclick="resetJadwalFilters()">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>


<style>
    .table-responsive-custom {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    .table-responsive-custom .dropdown-menu {
        z-index: 1060 !important;
    }
    @media (max-width: 767.98px) {
        .table th, .table td {
            white-space: nowrap;
            vertical-align: middle;
        }
    }
</style>

    <!-- Tabel Jadwal Ujian -->
    <div class="card card-outline card-success shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($jadwalList)): ?>
                <div class="text-center py-5">
                    <i class="fas <?= ($filter['arsip'] ?? '0') === '1' ? 'fa-archive' : 'fa-calendar-times' ?> fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted"><?= ($filter['arsip'] ?? '0') === '1' ? 'Belum ada Jadwal Ujian yang Diarsip' : 'Belum ada Jadwal Ujian Aktif' ?></h5>
                    <p class="text-muted small">
                        <?= ($filter['arsip'] ?? '0') === '1' ? 'Jadwal yang diarsip dari Tahun Ajaran sebelumnya akan tampil di sini.' : 'Klik tombol di bawah untuk membuat jadwal ujian baru.' ?>
                    </p>
                    <?php if (($filter['arsip'] ?? '0') !== '1'): ?>
                        <a href="<?= base_url('backend/ujian-mdta/jadwal/create') ?>" class="btn btn-success">
                            <i class="fas fa-calendar-plus me-1"></i> Buat Jadwal Ujian
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>

                <div class="table-responsive table-responsive-custom">
                    <table class="table table-hover table-striped align-middle mb-0" id="tabelJadwalUjian">
                        <thead class="table-success align-middle">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Ujian & Paket Soal</th>
                                <th>Kelas / Semester</th>
                                <th class="text-center">Soal & Opsi</th>
                                <th>Waktu Pelaksanaan</th>
                                <th class="text-center">Nilai KKM</th>
                                <th class="text-center">Tipe & Attempt</th>
                                <th class="text-center">Status</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalList as $i => $j): ?>
                                <tr class="jadwal-main-row"
                                    data-id="<?= $j['id'] ?>"
                                    data-nama="<?= strtolower(esc($j['NamaUjian'])) ?>"
                                    data-paket="<?= strtolower(esc($j['NamaPaket'] ?? '')) ?>"
                                    data-kelas="<?= strtolower(esc($j['NamaKelas'] ?? '')) ?>"
                                    data-status="<?= strtolower(esc($j['Status'] ?? '')) ?>">
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-bold"><?= esc($j['NamaUjian']) ?></div>
                                        <small class="text-muted"><i class="fas fa-box me-1"></i>Paket: <?= esc($j['NamaPaket'] ?? '-') ?></small>
                                    </td>
                                     <td>
                                         <span class="badge bg-light text-dark border"><?= esc($j['NamaKelas'] ?? '-') ?></span>
                                         <?php
                                         $semVal = (string)($j['Semester'] ?? '1');
                                         $semMap = [
                                             '1'        => 'Sem 1 (Ganjil)',
                                             '2'        => 'Sem 2 (Genap)',
                                             'mingguan' => 'Harian / Mingguan',
                                             'uts'      => 'UTS',
                                             'uas'      => 'UAS',
                                         ];
                                         $semText = $semMap[$semVal] ?? ("Sem: " . $semVal);
                                         ?>
                                         <small class="d-block text-muted"><i class="fas fa-bookmark me-1"></i><?= $semText ?></small>
                                     </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary mb-1"><?= $j['JumlahSoal'] ?> Soal</span>
                                        <br>
                                        <small class="text-muted d-block"><i class="fas fa-clock me-1"></i><?= $j['DurasiMenit'] ?> Menit</small>
                                        <?php
                                        $jmlPilihan = (int)($j['JumlahPilihan'] ?? 4);
                                        $hurufAkhir = chr(64 + $jmlPilihan);
                                        ?>
                                        <span class="badge bg-info text-dark border border-info mt-1">
                                            <?= $jmlPilihan ?> Opsi (A-<?= $hurufAkhir ?>)
                                        </span>
                                    </td>
                                    <td>
                                        <small class="d-block"><strong>Mulai:</strong> <?= date('d/m/Y H:i', strtotime($j['TanggalMulai'])) ?></small>
                                        <small class="d-block text-muted"><strong>Selesai:</strong> <?= date('d/m/Y H:i', strtotime($j['TanggalSelesai'])) ?></small>
                                    </td>
                                    <td class="text-center"><span class="badge bg-warning text-dark"><?= $j['NilaiMinimum'] ?></span></td>
                                    <td class="text-center">
                                        <?php if ($j['TipeJadwal'] == 'utama'): ?>
                                            <span class="badge bg-primary">Utama</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Remedial ke-<?= $j['AttemptKe'] - 1 ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $statusBadge = [
                                            'draft'    => 'bg-secondary',
                                            'aktif'    => 'bg-success',
                                            'selesai'  => 'bg-info',
                                            'nonaktif' => 'bg-danger'
                                        ];
                                        ?>
                                        <span class="badge <?= $statusBadge[$j['Status']] ?? 'bg-secondary' ?>">
                                            <?= ucfirst($j['Status']) ?>
                                        </span>
                                        <?php if ((int)($j['IsArchived'] ?? 0) === 1): ?>
                                            <span class="badge bg-secondary d-block mt-1"><i class="fas fa-archive me-1"></i> Diarsip</span>
                                        <?php endif; ?>
                                    </td>
                                     <td class="text-center align-middle">
                                         <div class="btn-group">
                                             <button class="btn btn-outline-primary btn-sm dropdown-toggle fw-bold" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" data-boundary="window" data-bs-boundary="window" aria-haspopup="true" aria-expanded="false">
                                                 <i class="fas fa-cog me-1"></i> Aksi
                                             </button>
                                             <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                 <?php if ($j['Status'] == 'draft'): ?>
                                                     <form method="post" action="<?= base_url("backend/ujian-mdta/jadwal/aktivasi/{$j['id']}") ?>">
                                                         <?= csrf_field() ?>
                                                         <button type="submit" class="dropdown-item text-success fw-semibold">
                                                             <i class="fas fa-play me-2"></i> Aktifkan Ujian
                                                         </button>
                                                     </form>
                                                 <?php elseif ($j['Status'] == 'aktif'): ?>
                                                     <a href="<?= base_url("backend/ujian-mdta/jadwal/monitor/{$j['id']}/1") ?>" class="dropdown-item text-primary fw-semibold">
                                                         <i class="fas fa-desktop me-2"></i> Live Monitor
                                                     </a>
                                                 <?php endif; ?>

                                                 <a href="javascript:void(0)" class="dropdown-item text-warning fw-semibold" onclick="handleEditJadwal(<?= $j['id'] ?>)">
                                                     <i class="fas fa-edit me-2"></i> Edit Jadwal
                                                 </a>

                                                 <?php if (!empty($j['is_remedial_ready'])): ?>
                                                     <?php
                                                     $tglMulaiRemed   = !empty($j['TanggalMulaiRemedial']) ? date('Y-m-d\TH:i', strtotime($j['TanggalMulaiRemedial'])) : '';
                                                     $tglSelesaiRemed = !empty($j['TanggalSelesaiRemedial']) ? date('Y-m-d\TH:i', strtotime($j['TanggalSelesaiRemedial'])) : '';
                                                     ?>
                                                     <a href="javascript:void(0)" class="dropdown-item fw-semibold" style="color: #6f42c1;" onclick="openModalAturRemedial(<?= $j['id'] ?>, '<?= esc($j['NamaUjian'], 'js') ?>', <?= $j['target_remedial_ke'] ?>, <?= $j['jml_perlu_remedial'] ?>, '<?= $tglMulaiRemed ?>', '<?= $tglSelesaiRemed ?>')">
                                                         <i class="fas fa-redo me-2"></i> Remedial (<?= $j['jml_perlu_remedial'] ?> Santri)
                                                     </a>
                                                 <?php endif; ?>

                                                 <a href="<?= base_url("backend/ujian-mdta/laporan/{$j['id']}") ?>" class="dropdown-item text-info fw-semibold">
                                                     <i class="fas fa-chart-bar me-2"></i> Laporan Nilai
                                                 </a>

                                                 <div class="dropdown-divider"></div>

                                                 <?php if ((int)($j['IsArchived'] ?? 0) === 1): ?>
                                                     <a href="javascript:void(0)" class="dropdown-item text-success fw-semibold" onclick="handleArchiveJadwal(<?= $j['id'] ?>, 0, '<?= esc($j['NamaUjian'], 'js') ?>')">
                                                         <i class="fas fa-box-open me-2"></i> Buka Arsip
                                                     </a>
                                                 <?php else: ?>
                                                     <a href="javascript:void(0)" class="dropdown-item text-secondary fw-semibold" onclick="handleArchiveJadwal(<?= $j['id'] ?>, 1, '<?= esc($j['NamaUjian'], 'js') ?>')">
                                                         <i class="fas fa-archive me-2"></i> Arsipkan Jadwal
                                                     </a>
                                                 <?php endif; ?>

                                                 <a href="javascript:void(0)" class="dropdown-item text-danger fw-semibold" onclick="handleDeleteJadwal(<?= $j['id'] ?>, '<?= esc($j['NamaUjian'], 'js') ?>', <?= (int)($j['total_sesi'] ?? 0) ?>)">
                                                     <i class="fas fa-trash me-2"></i> Hapus Jadwal
                                                 </a>
                                             </div>
                                         </div>
                                     </td>

                                </tr>

                                <!-- SUB-ROW REMEDIAL JIKA ADA -->
                                <?php if (!empty($j['remedial_subrows'])): ?>
                                    <?php foreach ($j['remedial_subrows'] as $subIdx => $sub): ?>
                                        <tr class="jadwal-subrow-<?= $j['id'] ?> table-light border-bottom border-warning-subtle" style="background-color: #fcfaff;">
                                            <td class="text-center align-middle">
                                                <span class="fw-bold" style="color: #6f42c1;">└─ <?= $i + 1 ?>.<?= $subIdx + 1 ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="fw-bold" style="color: #6f42c1;">
                                                    <i class="fas fa-redo me-1"></i> <?= esc($sub['nama_remedial']) ?>
                                                </div>
                                                <small class="text-muted"><i class="fas fa-users me-1 text-primary"></i><?= $sub['jml_santri'] ?> Santri Terdaftar</small>
                                            </td>
                                             <td class="align-middle">
                                                 <span class="badge bg-light text-dark border"><?= esc($j['NamaKelas'] ?? '-') ?></span>
                                                 <small class="d-block text-muted"><i class="fas fa-bookmark me-1"></i><?= $semText ?></small>
                                             </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-secondary mb-1"><?= $j['JumlahSoal'] ?> Soal</span>
                                                <small class="text-muted d-block"><i class="fas fa-clock me-1"></i><?= $j['DurasiMenit'] ?> Menit</small>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (!empty($sub['tgl_mulai'])): ?>
                                                    <small class="d-block"><strong>Mulai:</strong> <?= date('d/m/Y H:i', strtotime($sub['tgl_mulai'])) ?></small>
                                                    <small class="d-block text-muted"><strong>Selesai:</strong> <?= !empty($sub['tgl_selesai']) ? date('d/m/Y H:i', strtotime($sub['tgl_selesai'])) : '-' ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted small fst-italic"><i class="fas fa-clock me-1 text-warning"></i>Bisa Langsung Dimulai</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-warning text-dark"><?= $j['NilaiMinimum'] ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge text-white fw-bold px-2 py-1" style="background-color: #6f42c1;">
                                                    <i class="fas fa-redo me-1"></i> Remedial Ke-<?= $sub['attempt_target'] ?>
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge <?= $sub['status_remedial'] == 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                                    <?= ucfirst($sub['status_remedial'] ?: 'aktif') ?>
                                                </span>
                                            </td>
                                             <td class="text-center align-middle">
                                                 <div class="btn-group">
                                                     <button class="btn btn-outline-purple btn-sm dropdown-toggle fw-bold" style="color: #6f42c1; border-color: #6f42c1;" type="button" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                         <i class="fas fa-cog me-1"></i> Aksi
                                                     </button>
                                                     <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                         <a href="<?= base_url("backend/ujian-mdta/jadwal/monitor/{$j['id']}/" . ($sub['attempt_target'] + 1)) ?>" class="dropdown-item text-primary fw-semibold">
                                                             <i class="fas fa-desktop me-2"></i> Live Monitor
                                                         </a>
                                                         <?php
                                                         $subTglMulaiRemed   = !empty($sub['tgl_mulai']) ? date('Y-m-d\TH:i', strtotime($sub['tgl_mulai'])) : '';
                                                         $subTglSelesaiRemed = !empty($sub['tgl_selesai']) ? date('Y-m-d\TH:i', strtotime($sub['tgl_selesai'])) : '';
                                                         ?>
                                                         <a href="javascript:void(0)" class="dropdown-item text-warning fw-semibold" onclick="openModalAturRemedial(<?= $j['id'] ?>, '<?= esc($j['NamaUjian'], 'js') ?>', <?= $sub['attempt_target'] ?>, <?= $sub['jml_santri'] ?>, '<?= $subTglMulaiRemed ?>', '<?= $subTglSelesaiRemed ?>')">
                                                             <i class="fas fa-clock me-2"></i> Edit Waktu Remedial
                                                         </a>

                                                         <?php if (!empty($sub['is_remedial_ready'])): ?>
                                                             <a href="javascript:void(0)" class="dropdown-item fw-semibold" style="color: #6f42c1;" onclick="openModalAturRemedial(<?= $j['id'] ?>, '<?= esc($j['NamaUjian'], 'js') ?>', <?= $sub['next_attempt_target'] ?>, <?= $sub['jml_gagal'] ?>, '<?= $subTglMulaiRemed ?>', '<?= $subTglSelesaiRemed ?>')">
                                                                 <i class="fas fa-redo me-2"></i> Aktifkan Remedial Ke-<?= $sub['next_attempt_target'] ?> (<?= $sub['jml_gagal'] ?> Santri)
                                                             </a>
                                                         <?php endif; ?>

                                                         <div class="dropdown-divider"></div>
                                                         <a href="javascript:void(0)" class="dropdown-item text-danger fw-semibold" onclick="handleDeleteRemedial(<?= $j['id'] ?>, '<?= esc($j['NamaUjian'], 'js') ?>', <?= $sub['attempt_target'] ?>, <?= (int)($sub['jml_santri'] ?? 0) ?>)">
                                                             <i class="fas fa-trash me-2"></i> Hapus Remedial
                                                         </a>
                                                     </div>
                                                 </div>
                                             </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Pengaturan Waktu Ujian Remedial -->
<div class="modal fade" id="modalAturRemedial" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white py-3" style="background-color: #6f42c1;">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="fas fa-redo me-2"></i> Pengaturan & Aktivasi Ujian Remedial
                </h5>
                <button type="button" class="btn-close close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAturRemedial" method="post" action="">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="alert p-3 rounded-3 mb-3 border" style="background-color: #f3ebff; border-color: #d1b3ff;">
                        <div class="fw-bold text-dark mb-1" id="remed_namaUjian">-</div>
                        <div class="small text-muted">
                            Target: <span class="badge text-white fw-bold" id="remed_targetKe" style="background-color: #6f42c1;">Remedial Ke-1</span>
                            | Jumlah Santri Belum Lulus: <span class="badge bg-danger fw-bold fs-6" id="remed_jmlSantri">0 Santri</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Tanggal & Jam Mulai Remedial</label>
                        <input type="datetime-local" class="form-control" name="TanggalMulaiRemedial" id="remed_tglMulai">
                        <small class="text-muted">Kosongkan jika remedial dapat langsung dimulai kapan saja.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Tanggal & Jam Selesai Remedial</label>
                        <input type="datetime-local" class="form-control" name="TanggalSelesaiRemedial" id="remed_tglSelesai">
                        <small class="text-muted">Kosongkan jika tidak ada batas waktu penutupan remedial.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-semibold" data-dismiss="modal" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn text-white btn-sm px-4 fw-bold" style="background-color: #6f42c1; border-color: #6f42c1;">
                        <i class="fas fa-paper-plane me-1"></i> Aktifkan & Simpan Remedial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= esc(session()->getFlashdata('success'), 'js') ?>',
            timer: 2500,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= esc(session()->getFlashdata('error'), 'js') ?>',
            timer: 4000,
            showConfirmButton: true
        });
    <?php endif; ?>
});
function openModalAturRemedial(idJadwal, namaUjian, targetKe, jmlSantri, tglMulai, tglSelesai) {
    const form = document.getElementById('formAturRemedial');
    form.action = `<?= base_url("backend/ujian-mdta/jadwal/aktifkanRemedialJadwal/") ?>/${idJadwal}`;

    document.getElementById('remed_namaUjian').textContent = namaUjian;
    document.getElementById('remed_targetKe').textContent = `Remedial Ke-${targetKe}`;
    document.getElementById('remed_jmlSantri').textContent = `${jmlSantri} Santri`;
    document.getElementById('remed_tglMulai').value = tglMulai || '';
    document.getElementById('remed_tglSelesai').value = tglSelesai || '';

    if (window.jQuery && typeof $.fn.modal === 'function') {
        $('#modalAturRemedial').modal('show');
    } else if (window.bootstrap && typeof bootstrap.Modal === 'function') {
        const el = document.getElementById('modalAturRemedial');
        const modal = (typeof bootstrap.Modal.getInstance === 'function' ? bootstrap.Modal.getInstance(el) : null) || new bootstrap.Modal(el);
        modal.show();
    } else if (window.jQuery) {
        $('#modalAturRemedial').modal('show');
    }
}

function handleEditJadwal(idJadwal) {
    window.location.href = `<?= base_url("backend/ujian-mdta/jadwal/edit/") ?>/${idJadwal}`;
}

function handleDeleteJadwal(idJadwal, namaUjian, totalSesi) {
    const deleteUrl = `<?= base_url("backend/ujian-mdta/jadwal/delete/") ?>/${idJadwal}`;

    if (totalSesi === 0) {
        Swal.fire({
            title: 'Hapus Jadwal Ujian?',
            html: `Apakah Anda yakin ingin menghapus jadwal <strong>"${namaUjian}"</strong>?<br><small class="text-muted">Data jadwal ini belum memiliki sesi pengerjaan santri.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitDeleteForm(deleteUrl, '');
            }
        });
    } else {
        Swal.fire({
            title: '🚨 PERINGATAN KETAT!',
            html: `Jadwal <strong>"${namaUjian}"</strong> sudah diikuti oleh <strong>${totalSesi} Sesi Santri</strong>.<br><br><span class="text-danger fw-bold">Penghapusan akan MENGHAPUS PERMANEN seluruh nilai, jawaban, dan riwayat ujian santri pada jadwal ini.</span><br><br>Ketik kata <strong class="text-danger">HAPUS</strong> di bawah ini untuk mengonfirmasi:`,
            icon: 'error',
            input: 'text',
            inputPlaceholder: 'Ketik HAPUS di sini',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-exclamation-triangle me-1"></i> HAPUS PERMANEN',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (value !== 'HAPUS') {
                    return 'Anda harus mengetik kata HAPUS secara tepat!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value === 'HAPUS') {
                submitDeleteForm(deleteUrl, 'HAPUS');
            }
        });
    }
}

function handleDeleteRemedial(idJadwal, namaUjian, attemptTarget, totalSesi) {
    const deleteUrl = `<?= base_url("backend/ujian-mdta/jadwal/delete-remedial/") ?>/${idJadwal}/${attemptTarget}`;

    if (totalSesi === 0) {
        Swal.fire({
            title: `Hapus Remedial Ke-${attemptTarget}?`,
            html: `Apakah Anda yakin ingin menghapus <strong>Ujian Remedial Ke-${attemptTarget}</strong> pada jadwal <strong>"${namaUjian}"</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitDeleteForm(deleteUrl, '');
            }
        });
    } else {
        Swal.fire({
            title: '🚨 PERINGATAN KETAT!',
            html: `Ujian Remedial Ke-${attemptTarget} pada <strong>"${namaUjian}"</strong> memiliki <strong>${totalSesi} Sesi Santri</strong>.<br><br><span class="text-danger fw-bold">Penghapusan akan MENGHAPUS PERMANEN seluruh nilai dan jawaban santri pada sesi remedial ini.</span><br><br>Ketik kata <strong class="text-danger">HAPUS</strong> di bawah ini untuk mengonfirmasi:`,
            icon: 'error',
            input: 'text',
            inputPlaceholder: 'Ketik HAPUS di sini',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-exclamation-triangle me-1"></i> HAPUS REMEDIAL PERMANEN',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (value !== 'HAPUS') {
                    return 'Anda harus mengetik kata HAPUS secara tepat!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value === 'HAPUS') {
                submitDeleteForm(deleteUrl, 'HAPUS');
            }
        });
    }
}

function submitDeleteForm(url, confirmDeleteText) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= csrf_token() ?>';
    csrfInput.value = '<?= csrf_hash() ?>';
    form.appendChild(csrfInput);

    if (confirmDeleteText) {
        const confirmInput = document.createElement('input');
        confirmInput.type = 'hidden';
        confirmInput.name = 'confirm_delete';
        confirmInput.value = confirmDeleteText;
        form.appendChild(confirmInput);
    }

    document.body.appendChild(form);
    form.submit();
}

function changeTaFilter(taVal) {
    let url = new URL(window.location.href);
    if (taVal && taVal !== 'all') {
        url.searchParams.set('ta', taVal);
    } else {
        url.searchParams.delete('ta');
    }
    window.location.href = url.toString();
}

function handleArchiveJadwal(id, toArchived, nama) {
    let actionText = toArchived ? 'mengarsipkan' : 'mengeluarkan dari arsip';
    let confirmBtn = toArchived ? 'Ya, Arsipkan!' : 'Ya, Buka Arsip!';

    let executeArchive = function() {
        $.post('<?= base_url("backend/ujian-mdta/jadwal/archive/") ?>' + id, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    alert(res.message);
                    location.reload();
                }
            } else {
                alert(res.message || 'Gagal mengubah status arsip.');
            }
        }, 'json').fail(function() {
            alert('Terjadi kesalahan server.');
        });
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Konfirmasi Arsip',
            text: 'Apakah Anda yakin ingin ' + actionText + ' jadwal "' + nama + '"?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmBtn,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                executeArchive();
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin ' + actionText + ' jadwal "' + nama + '"?')) {
            executeArchive();
        }
    }
}


function applyJadwalFilters() {
    const searchVal = (document.getElementById('filterSearch') ? document.getElementById('filterSearch').value : '').toLowerCase().trim();
    const kelasVal  = (document.getElementById('filterKelas') ? document.getElementById('filterKelas').value : '').toLowerCase().trim();
    const statusVal = (document.getElementById('filterStatus') ? document.getElementById('filterStatus').value : '').toLowerCase().trim();

    const mainRows = document.querySelectorAll('tr.jadwal-main-row');
    
    mainRows.forEach(row => {
        const namaUjian = (row.getAttribute('data-nama') || '').toLowerCase();
        const paketSoal = (row.getAttribute('data-paket') || '').toLowerCase();
        const namaKelas = (row.getAttribute('data-kelas') || '').toLowerCase();
        const status    = (row.getAttribute('data-status') || '').toLowerCase();
        const idJadwal  = row.getAttribute('data-id') || '';

        const matchSearch = !searchVal || namaUjian.includes(searchVal) || paketSoal.includes(searchVal);
        const matchKelas  = !kelasVal || namaKelas === kelasVal;
        const matchStatus = !statusVal || status === statusVal;

        const isVisible = matchSearch && matchKelas && matchStatus;
        
        row.style.display = isVisible ? '' : 'none';

        // Toggle subrows
        const subRows = document.querySelectorAll(`tr.jadwal-subrow-${idJadwal}`);
        subRows.forEach(sub => {
            sub.style.display = isVisible ? '' : 'none';
        });
    });
}

function resetJadwalFilters() {
    if (document.getElementById('filterSearch')) document.getElementById('filterSearch').value = '';
    if (document.getElementById('filterKelas')) document.getElementById('filterKelas').value = '';
    if (document.getElementById('filterStatus')) document.getElementById('filterStatus').value = '';
    applyJadwalFilters();
}

$(document).ready(function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        if ($.fn.DataTable.isDataTable('#tabelJadwalUjian')) {
            $('#tabelJadwalUjian').DataTable().destroy();
        }
        $('#tabelJadwalUjian').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "language": {
                "sSearch": "Cari Ujian:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ jadwal",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 jadwal",
                "infoFiltered": "(disaring dari _MAX_ total jadwal)",
                "zeroRecords": "Tidak ada jadwal ujian yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Lanjut",
                    "previous": "Sebelumnya"
                }
            }
        });
    }

    // Floating Body Attachment untuk Dropdown Tabel (Bebas terpotong atas/bawah 100%)
    $(document).on('show.bs.dropdown', '.table-responsive-custom, .table-responsive', function (e) {
        var $dropdown = $(e.target).hasClass('dropdown') ? $(e.target) : $(e.target).closest('.dropdown');
        var $button   = $dropdown.find('.dropdown-toggle');
        var $menu     = $dropdown.find('.dropdown-menu');

        if (!$button.length || !$menu.length) return;

        $menu.data('parent-container', $dropdown);
        $('body').append($menu.detach());

        var offset     = $button.offset();
        var btnHeight  = $button.outerHeight();
        var menuHeight = $menu.outerHeight();
        var menuWidth  = $menu.outerWidth();
        var btnWidth   = $button.outerWidth();
        var windowH    = $(window).height();
        var scrollT    = $(window).scrollTop();

        var topPos  = offset.top + btnHeight;
        var leftPos = offset.left;

        if (leftPos + menuWidth > $(window).width() - 10) {
            leftPos = offset.left + btnWidth - menuWidth;
        }

        if (topPos + menuHeight > scrollT + windowH && offset.top - menuHeight > scrollT) {
            topPos = offset.top - menuHeight;
        }

        $menu.css({
            'display': 'block',
            'position': 'absolute',
            'top': topPos + 'px',
            'left': Math.max(10, leftPos) + 'px',
            'z-index': 1090,
            'box-shadow': '0 0.5rem 1.5rem rgba(0, 0, 0, 0.2)'
        });
    });

    $(document).on('hide.bs.dropdown', '.table-responsive-custom, .table-responsive', function (e) {
        var $menu = $('body > .dropdown-menu');
        $menu.each(function() {
            var $parent = $(this).data('parent-container');
            if ($parent && $parent.length) {
                $parent.append($(this).detach());
                $(this).css({'display': '', 'position': '', 'top': '', 'left': '', 'z-index': '', 'box-shadow': ''});
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
