<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Header Title & Filter Bar -->
    <div class="card card-primary card-outline shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="card-title font-weight-bold text-primary mb-1">
                        <i class="fas fa-print mr-2"></i> Administrasi & Kelengkapan Cetak Ujian
                    </h4>
                    <p class="text-muted small mb-0">Cetak label ruangan juri, lembar ujian tulis imla, pedoman klasifikasi range nilai, dan rekapitulasi penempatan.</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge badge-info p-2 font-weight-normal">
                        <i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran: <strong><?= esc($idTahunAjaran); ?></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body bg-light border-bottom">
            <form id="filterForm" method="GET" action="<?= base_url('backend/munaqosah/administrasi'); ?>" class="row align-items-end">
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold text-secondary small mb-1"><i class="fas fa-calendar-check mr-1"></i> Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-control form-control-sm select2bs4" onchange="this.form.submit()">
                        <?php if (!empty($tahunAjaranList)): ?>
                            <?php foreach ($tahunAjaranList as $ta): ?>
                                <option value="<?= esc($ta['IdTahunAjaran']); ?>" <?= ($idTahunAjaran == $ta['IdTahunAjaran']) ? 'selected' : ''; ?>>
                                    <?= esc($ta['NamaTahunAjaran'] ?? $ta['IdTahunAjaran']); ?> <?= ($ta['StatusAktif'] ?? '') == '1' ? '(Aktif)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?= esc($idTahunAjaran); ?>"><?= esc($idTahunAjaran); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                    <label class="font-weight-bold text-secondary small mb-1"><i class="fas fa-tasks mr-1"></i> Agenda Pelaksanaan</label>
                    <?php if (!empty($activeRole) && $activeRole == 'operator'): ?>
                        <input type="hidden" name="type" value="pra-munaqosah">
                        <select class="form-control form-control-sm" disabled>
                            <option value="pra-munaqosah" selected>Pra-Munaqosah (Lembaga / TPQ)</option>
                        </select>
                    <?php else: ?>
                        <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="munaqosah" <?= ($typeUjian == 'munaqosah') ? 'selected' : ''; ?>>Ujian Munaqosah (Kecamatan / FKPQ)</option>
                            <option value="pra-munaqosah" <?= ($typeUjian == 'pra-munaqosah') ? 'selected' : ''; ?>>Pra-Munaqosah (Lembaga / TPQ)</option>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 col-sm-8 mb-2 mb-md-0">
                    <label class="font-weight-bold text-secondary small mb-1"><i class="fas fa-mosque mr-1"></i> Lembaga / TPQ</label>
                    <?php if (!empty($activeRole) && $activeRole == 'operator'): ?>
                        <input type="hidden" name="tpq" value="<?= esc($idTpq); ?>">
                        <select class="form-control form-control-sm select2bs4" disabled>
                            <?php foreach ($tpqList as $tpq): ?>
                                <option value="<?= esc($tpq['IdTpq']); ?>" <?= ($idTpq == $tpq['IdTpq']) ? 'selected' : ''; ?>>
                                    <?= esc($tpq['NamaTpq']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <select name="tpq" class="form-control form-control-sm select2bs4" onchange="this.form.submit()">
                            <option value="0" <?= ($idTpq == 0 || $idTpq === '0') ? 'selected' : ''; ?>>Semua Lembaga / Seluruh TPQ</option>
                            <?php foreach ($tpqList as $tpq): ?>
                                <option value="<?= esc($tpq['IdTpq']); ?>" <?= ($idTpq == $tpq['IdTpq']) ? 'selected' : ''; ?>>
                                    <?= esc($tpq['NamaTpq']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-2 col-sm-4 text-right">
                    <button type="submit" class="btn btn-sm btn-primary btn-block shadow-sm">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4 Cards Dokumen Administrasi -->
    <div class="row">
        <!-- CARD 1: LABEL RUANGAN JURI -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-door-open mr-2"></i> 1. Label / Plang Pintu Ruangan Juri
                    </h5>
                    <span class="badge badge-light text-primary font-weight-bold">A4 Landscape</span>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        Mencetak label identitas pintu ruangan ujian (Room Sign) berukuran <strong>A4 Landscape</strong>. Anda dapat mencetak <strong>satu per satu ruangan</strong> maupun <strong>sekaligus seluruh ruangan</strong> (otomatis 1 lembar per ruangan).
                    </p>

                    <form action="<?= base_url('backend/munaqosah/print-label-ruangan'); ?>" method="POST" target="_blank" id="formPrintLabelRuangan">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="tahun_ajaran" value="<?= esc($idTahunAjaran); ?>">
                        <input type="hidden" name="type" value="<?= esc($typeUjian); ?>">
                        <input type="hidden" name="tpq" value="<?= esc($idTpq); ?>">

                        <div class="form-group mb-3 bg-light p-2 rounded border">
                            <label class="font-weight-bold text-dark small mb-1">
                                <i class="fas fa-list-ol mr-1 text-primary"></i> Pilih Opsi Target Cetak:
                            </label>
                            <select name="room_number" id="selectRoomNumber" class="form-control form-control-sm">
                                <option value="all">🖨️ Cetak Semua Ruangan Sekaligus (<?= count($ruanganList); ?> Ruangan)</option>
                                <optgroup label="Cetak Perorangan / Satuan Ruangan:">
                                    <?php foreach ($ruanganList as $roomNo => $rData): ?>
                                        <option value="<?= esc($roomNo); ?>">
                                            📍 <?= esc($roomNo); ?> - <?= esc($rData['NamaMateriGrup']); ?> (<?= count($rData['JuriList']); ?> Juri)
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Daftar Akses Cepat Cetak Satuan -->
                        <?php if (!empty($ruanganList)): ?>
                            <div class="mb-3">
                                <label class="font-weight-bold text-dark small mb-1 d-block">
                                    <i class="fas fa-bolt text-warning mr-1"></i> Cetak Cepat Tiap Ruangan (1 Lembar):
                                </label>
                                <div class="table-responsive border rounded" style="max-height: 140px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0 bg-white small">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="py-1">Ruangan</th>
                                                <th class="py-1">Materi Ujian</th>
                                                <th class="py-1 text-center" style="width: 80px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ruanganList as $roomNo => $rData): ?>
                                                <tr>
                                                    <td class="font-weight-bold text-primary align-middle py-1">
                                                        <?= esc($roomNo); ?>
                                                    </td>
                                                    <td class="align-middle py-1 text-truncate" style="max-width: 150px;">
                                                        <?= esc($rData['NamaMateriGrup']); ?>
                                                    </td>
                                                    <td class="text-center py-1">
                                                        <a href="<?= base_url('backend/munaqosah/print-label-ruangan?room_number=' . urlencode($roomNo) . '&tahun_ajaran=' . urlencode($idTahunAjaran) . '&type=' . urlencode($typeUjian) . '&tpq=' . urlencode($idTpq)); ?>" 
                                                           target="_blank" 
                                                           class="btn btn-xs btn-outline-primary py-0 px-2 shadow-sm"
                                                           title="Cetak <?= esc($roomNo); ?>">
                                                            <i class="fas fa-print"></i> Cetak
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group mb-3 px-1">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="showUsernameSwitch" name="show_username" value="1">
                                <label class="custom-control-label font-weight-normal small text-dark" for="showUsernameSwitch">
                                    Tampilkan Username Akun Juri di bawah Nama Juri
                                </label>
                            </div>
                        </div>

                        <div class="mt-auto pt-2">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-print mr-1"></i> Cetak Dokumen Label Ruangan (PDF)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CARD 2: LEMBAR IMLA MASTER -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-pen-nib mr-2"></i> 2. Formulir Penilaian Praktek Tulis Ayat Al-Qur'an
                    </h5>
                    <span class="badge badge-light text-success font-weight-bold">A4 Portrait</span>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        Mencetak <strong>Master Formulir Penilaian Praktek Tulis Ayat Al-Qur'an (1 Lembar A4 Portrait)</strong> yang dilengkapi KOP Lembaga, kolom No. Uji & Nama, 5 strip bidang tulis ayat, kolom Catatan, dan Paraf Juri.
                    </p>

                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fas fa-lightbulb mr-1"></i> <strong>Tips Penggunaan:</strong> Anda cukup mencetak 1 lembar master ini dari sistem, lalu melakukan penggandaan / fotokopi manual sesuai jumlah santri yang mengikuti ujian imla / tulis Al-Qur'an.
                    </div>

                    <form action="<?= base_url('backend/munaqosah/print-lembar-imla'); ?>" method="POST" target="_blank" class="mt-auto">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="tahun_ajaran" value="<?= esc($idTahunAjaran); ?>">
                        <input type="hidden" name="type" value="<?= esc($typeUjian); ?>">
                        <input type="hidden" name="tpq" value="<?= esc($idTpq); ?>">

                        <button type="submit" class="btn btn-success btn-block shadow-sm font-weight-bold">
                            <i class="fas fa-print mr-1"></i> Cetak Formulir Penilaian Imla (PDF)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CARD 3: KLASIFIKASI & RANGE NILAI -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-clipboard-check mr-2"></i> 3. Standar Klasifikasi & Range Nilai
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        Mencetak pedoman fisik resmi <strong>Range Nilai Kesalahan</strong> untuk para Dewan Juri dan Panitia Pengawas. Berisi batas pengurangan nilai (Nilai Min - Max) per kategori kesalahan materi munaqosah.
                    </p>

                    <div class="table-responsive small bg-light p-2 rounded border mb-3" style="max-height: 180px; overflow-y: auto;">
                        <?php if (!empty($kategoriKesalahan)): ?>
                            <?php 
                            $groupedKategori = [];
                            foreach ($kategoriKesalahan as $kk) {
                                $materi = !empty($kk['NamaKategoriMateri']) ? $kk['NamaKategoriMateri'] : 'Materi Umum';
                                $groupedKategori[$materi][] = $kk;
                            }
                            ?>
                            <?php foreach ($groupedKategori as $namaMateri => $items): ?>
                                <div class="badge badge-primary py-1 px-2 mb-1 w-100 text-left font-weight-bold">
                                    <i class="fas fa-book-reader mr-1"></i> Materi: <?= esc($namaMateri); ?>
                                </div>
                                <table class="table table-sm table-bordered table-striped mb-2 bg-white">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%;" class="text-center">No</th>
                                            <th style="width: 30%;" class="text-center">Range Nilai</th>
                                            <th style="width: 60%;">Kriteria Poin Penilaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach ($items as $item): 
                                        ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $no++; ?></td>
                                                <td class="text-center font-weight-bold text-danger">
                                                    <?= esc($item['NilaiMin']); ?> - <?= esc($item['NilaiMax']); ?>
                                                </td>
                                                <td><?= esc($item['NamaKategoriKesalahan']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted p-2">Belum ada data kategori kesalahan.</div>
                        <?php endif; ?>
                    </div>

                    <form action="<?= base_url('backend/munaqosah/print-klasifikasi-nilai'); ?>" method="POST" target="_blank" class="mt-auto">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="tahun_ajaran" value="<?= esc($idTahunAjaran); ?>">
                        <input type="hidden" name="type" value="<?= esc($typeUjian); ?>">
                        <input type="hidden" name="tpq" value="<?= esc($idTpq); ?>">

                        <button type="submit" class="btn btn-info btn-block shadow-sm">
                            <i class="fas fa-print mr-1"></i> Cetak Pedoman Klasifikasi Nilai (PDF)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CARD 4: REKAPITULASI PENEMPATAN RUANGAN & JURI -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-users-cog mr-2"></i> 4. Rekap Penempatan Ruangan & Juri
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        Mencetak <strong>Matriks Rekapitulasi Pembagian Ruangan & Juri Penguji</strong> secara menyeluruh untuk arsip panitia pelaksana dan pengawas ujian.
                    </p>

                    <div class="bg-light p-3 rounded border mb-3">
                        <div class="row text-center">
                            <div class="col-4 border-right">
                                <h4 class="font-weight-bold text-primary mb-0"><?= count($ruanganList); ?></h4>
                                <small class="text-muted">Total Ruangan</small>
                            </div>
                            <div class="col-4 border-right">
                                <?php 
                                $totalJuriTerdata = 0;
                                foreach ($ruanganList as $r) {
                                    $totalJuriTerdata += count($r['JuriList']);
                                }
                                ?>
                                <h4 class="font-weight-bold text-success mb-0"><?= $totalJuriTerdata; ?></h4>
                                <small class="text-muted">Total Juri Aktif</small>
                            </div>
                            <div class="col-4">
                                <h4 class="font-weight-bold text-info mb-0"><?= count($materiList); ?></h4>
                                <small class="text-muted">Materi Ujian</small>
                            </div>
                        </div>
                    </div>

                    <form action="<?= base_url('backend/munaqosah/print-rekap-ruangan-juri'); ?>" method="POST" target="_blank" class="mt-auto">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="tahun_ajaran" value="<?= esc($idTahunAjaran); ?>">
                        <input type="hidden" name="type" value="<?= esc($typeUjian); ?>">
                        <input type="hidden" name="tpq" value="<?= esc($idTpq); ?>">

                        <button type="submit" class="btn btn-secondary btn-block shadow-sm font-weight-bold">
                            <i class="fas fa-file-invoice mr-1"></i> Cetak Rekapitulasi Juri & Ruangan (PDF)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CARD 5: ABSENSI SANTRI DAN WALI SANTRI (BAPAK/IBU) -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-gradient-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-user-check mr-2"></i> 5. Absensi Santri & Wali Santri (Bapak/Ibu)
                    </h5>
                    <span class="badge badge-light text-dark font-weight-bold">A4 Portrait</span>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        Mencetak <strong>Daftar Hadir / Form Absensi Resmi (A4 Portrait)</strong> untuk santri peserta ujian beserta wali santri (Bapak/Ibu) dengan format kolom paraf berselang-seling (*zig-zag signature*).
                    </p>

                    <form action="<?= base_url('backend/munaqosah/print-absensi-peserta'); ?>" method="POST" target="_blank" class="mt-auto">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="tahun_ajaran" value="<?= esc($idTahunAjaran); ?>">
                        <input type="hidden" name="type" value="<?= esc($typeUjian); ?>">
                        <input type="hidden" name="tpq" value="<?= esc($idTpq); ?>">

                        <div class="form-group mb-3 bg-light p-2 rounded border">
                            <label class="font-weight-bold text-dark small mb-1">
                                <i class="fas fa-filter mr-1 text-primary"></i> Pilihan Format Absensi:
                            </label>
                            <select name="mode" class="form-control form-control-sm">
                                <option value="data">✅ Isi Otomatis Data Santri Peserta Terdaftar</option>
                                <option value="blank">📝 Master Form Kosong (Untuk Diisi Manual)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-dark btn-block shadow-sm font-weight-bold">
                            <i class="fas fa-print mr-1"></i> Cetak Form Absensi Santri & Wali Santri (PDF)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
