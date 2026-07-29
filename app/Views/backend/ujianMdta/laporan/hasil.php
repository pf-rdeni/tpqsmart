<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    .filter-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .filter-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    .btn-action-group .btn {
        font-weight: 500;
        font-size: 0.875rem;
        border-radius: 5px;
    }
    .table-laporan th {
        vertical-align: middle;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        background-color: #f8f9fa;
        color: #343a40;
    }
    .table-laporan td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .peserta-subheader th {
        background-color: #f1f3f5 !important;
        font-size: 0.8rem;
    }
    .card-body, .table-responsive {
        overflow: visible !important;
    }
    .dropdown-menu-laporan {
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        border: 1px solid #dee2e6 !important;
        min-width: 210px;
        z-index: 1050 !important;
    }
    .dropdown-menu-laporan .dropdown-item {
        padding: 0.4rem 1rem;
        color: #333333;
    }
    .dropdown-menu-laporan .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
    }

    .dropdown-menu-laporan .dropdown-divider {
        margin: 0.3rem 0;
    }
</style>

<div class="container-fluid">
    <!-- Header Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">
                LAPORAN <small class="text-muted fw-normal fs-6">HASIL UJIAN</small>
            </h3>
        </div>
    </div>

    <!-- Filter Card (Referensi Gambar 2) -->
    <div class="filter-card border">
        <form method="get" action="<?= base_url('backend/ujian-mdta/laporan-hasil') ?>" id="formFilterLaporan">
            <div class="row g-3 align-items-center">
                <!-- Lembaga -->
                <div class="col-md-6 col-lg-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-right text-sm-end filter-label">Lembaga</label>
                        <div class="col-sm-8">
                            <?php if (!empty($isLembagaUser)): ?>
                                <select class="form-control form-control-sm custom-select custom-select-sm select2bs4" disabled="disabled">
                                    <?php foreach ($tpqList as $t): ?>
                                        <option value="<?= $t['IdTpq'] ?>" selected><?= esc($t['NamaTpq']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="IdTpq" value="<?= esc($filter['IdTpq']) ?>">
                            <?php else: ?>
                                <select name="IdTpq" class="form-control form-control-sm custom-select custom-select-sm select2bs4">
                                    <option value="all">Pilih / Semua Lembaga</option>
                                    <?php foreach ($tpqList as $t): ?>
                                        <option value="<?= $t['IdTpq'] ?>" <?= ($filter['IdTpq'] == $t['IdTpq']) ? 'selected' : '' ?>>
                                            <?= esc($t['NamaTpq']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

                <!-- Kelompok -->
                <div class="col-md-6 col-lg-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-right text-sm-end filter-label">Kelompok</label>
                        <div class="col-sm-8">
                            <select name="IdKelas" class="form-control form-control-sm custom-select custom-select-sm select2bs4">
                                <option value="all">Semua Kelompok / Kelas</option>
                                <?php foreach ($kelasList as $k): ?>
                                    <option value="<?= $k['IdKelas'] ?>" <?= ($filter['IdKelas'] == $k['IdKelas']) ? 'selected' : '' ?>>
                                        <?= esc($k['NamaKelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Materi -->
                <div class="col-md-6 col-lg-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-right text-sm-end filter-label">Materi</label>
                        <div class="col-sm-8">
                            <select name="IdMateri" class="form-control form-control-sm custom-select custom-select-sm select2bs4">
                                <option value="all">Semua Materi</option>
                                <?php foreach ($materiList as $m): ?>
                                    <option value="<?= $m['IdMateri'] ?>" <?= (($filter['IdMateri'] ?? '') == $m['IdMateri']) ? 'selected' : '' ?>>
                                        <?= esc($m['NamaMateri']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Ruang -->
                <div class="col-md-6 col-lg-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-right text-sm-end filter-label">Ruang</label>
                        <div class="col-sm-8">
                            <select name="Ruang" class="form-control form-control-sm custom-select custom-select-sm">
                                <option value="">Semua</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Rentang Waktu -->
                <div class="col-md-6 col-lg-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-right text-sm-end filter-label">Rentang Waktu</label>
                        <div class="col-sm-8">
                            <div class="input-group input-group-sm">
                                <input type="date" name="startDate" class="form-control form-control-sm" value="<?= esc($filter['startDate']) ?>">
                                <div class="input-group-append input-group-prepend">
                                    <span class="input-group-text bg-white">-</span>
                                </div>
                                <input type="date" name="endDate" class="form-control form-control-sm" value="<?= esc($filter['endDate']) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kata Kunci -->
                <div class="col-md-6 col-lg-8">
                    <div class="row align-items-center">
                        <label class="col-sm-2 col-form-label text-sm-right text-sm-end filter-label">Kata Kunci</label>
                        <div class="col-sm-10">
                            <input type="text" name="keyword" class="form-control form-control-sm" 
                                   placeholder="Masukkan kata kunci pencarian" value="<?= esc($filter['keyword']) ?>">
                        </div>
                    </div>
                </div>

            </div>

            <?php 
                $rekapQuery = http_build_query([
                    'IdTpq'     => $filter['IdTpq'] ?? '',
                    'IdKelas'   => $filter['IdKelas'] ?? '',
                    'IdMateri'  => $filter['IdMateri'] ?? '',
                    'Ruang'     => $filter['Ruang'] ?? '',
                    'startDate' => $filter['startDate'] ?? '',
                    'endDate'   => $filter['endDate'] ?? '',
                    'keyword'   => $filter['keyword'] ?? '',
                ]);
            ?>
            <!-- Filter & Action Buttons -->
            <div class="d-flex justify-content-center gap-2 mt-4 btn-action-group">
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-search me-1"></i> Tampilkan
                </button>

                <button type="button" onclick="exportRekap('sesi')" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="fas fa-print me-1"></i> Rekap Per Sesi
                </button>

                <button type="button" onclick="exportRekap('materi')" class="btn btn-outline-secondary btn-sm px-3" title="Export Excel Rekap Per Materi berdasarkan Jadwal Terpilih">
                    <i class="fas fa-print me-1"></i> Rekap Per Materi
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card (Referensi Gambar 2) -->
    <div class="card shadow-sm border">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-laporan" id="tabelLaporanHasil">
                    <thead>
                        <tr>
                            <th width="80" class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <input type="checkbox" id="checkAllJadwal" class="form-check-input me-1" title="Pilih Semua">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-0 px-1 text-xs" 
                                                type="button" 
                                                data-toggle="dropdown" 
                                                data-bs-toggle="dropdown" 
                                                aria-haspopup="true" 
                                                aria-expanded="false">
                                            Bulk
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-sm shadow-sm">
                                            <a class="dropdown-item" href="#" id="btnPilihSemua">Pilih Semua</a>
                                            <a class="dropdown-item" href="#" id="btnBatalPilih">Batal Pilih</a>
                                        </div>
                                    </div>
                                </div>
                            </th>

                            <th width="40" class="text-center">No.</th>
                            <th width="100" class="text-center">Aksi</th>
                            <th>Deskripsi</th>
                            <th width="140">Materi</th>
                            <th width="180">Kelompok</th>
                            <th width="90" class="text-center">Tertinggi</th>
                            <th width="90" class="text-center">Rerata</th>
                            <th colspan="4" class="text-center border-bottom-0">Peserta</th>
                        </tr>
                        <tr class="peserta-subheader">
                            <th colspan="8"></th>
                            <th width="70" class="text-center border-top-0">Total</th>
                            <th width="90" class="text-center border-top-0">Mengerjakan</th>
                            <th width="80" class="text-center border-top-0">Atas KKM</th>
                            <th width="90" class="text-center border-top-0">Bawah KKM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jadwalList)): ?>
                            <tr>
                                <td colspan="12" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Tidak ada data laporan hasil ujian yang sesuai dengan filter.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($jadwalList as $idx => $j): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input check-row" value="<?= $j['id'] ?>">
                                    </td>
                                    <td class="text-center fw-semibold"><?= $idx + 1 ?></td>
                                    
                                    <!-- Aksi Dropdown Button (Referensi Gambar 1) -->
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm border dropdown-toggle py-1 px-2 text-dark fw-bold" 
                                                    type="button" 
                                                    data-toggle="dropdown" 
                                                    data-bs-toggle="dropdown" 
                                                    data-boundary="window" 
                                                    data-bs-boundary="window" 
                                                    aria-haspopup="true" 
                                                    aria-expanded="false">
                                                laporan
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-laporan shadow-sm border">
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/{$j['id']}") ?>">
                                                    lihat daftar nilai
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/export-pdf/{$j['id']}") ?>" target="_blank">
                                                    cetak daftar nilai
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/export-pdf/{$j['id']}?simple=1") ?>" target="_blank">
                                                    cetak daftar nilai (simple)
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="alert('Fitur lembar jawab siap dicetak dari detail santri.'); return false;">
                                                    cetak lembar jawab
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/presensi/{$j['id']}") ?>" target="_blank">
                                                    cetak presensi
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/presensi-excel/{$j['id']}") ?>">
                                                    cetak presensi (Excel)
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/presensi/{$j['id']}?offline=1") ?>" target="_blank">
                                                    cetak daftar hadir (offline)
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/berita-acara/{$j['id']}") ?>" target="_blank">
                                                    cetak berita acara
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/berita-acara/{$j['id']}?v=2") ?>" target="_blank">
                                                    cetak berita acara (v2)
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/analisis-soal/{$j['id']}") ?>">
                                                    analisis butir soal
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="alert('Fitur capaian indikator siap diakses.'); return false;">
                                                    capaian indikator
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/rekap-jawaban/{$j['id']}") ?>">
                                                    rekap pengerjaan
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/rekap-jawaban/{$j['id']}") ?>">
                                                    rekap jawaban
                                                </a>
                                                <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/laporan/analisis-soal/{$j['id']}") ?>">
                                                    rekap benar/salah
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Deskripsi -->
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($j['NamaUjian']) ?></div>
                                        <div class="small text-muted">
                                            <?= date('d M Y H:i', strtotime($j['TanggalMulai'])) ?>
                                        </div>
                                    </td>

                                    <!-- Materi -->
                                    <td>
                                        <span class="badge bg-light text-dark border font-weight-normal py-1 px-2">
                                            <?= esc($j['NamaMateri'] ?? '-') ?>
                                        </span>
                                    </td>

                                    <!-- Kelompok -->
                                    <td>
                                        <span class="text-dark fw-medium">
                                            <?= esc($j['NamaKelas'] ?? 'KELAS VI') ?> 
                                            <?= !empty($j['NamaTpq']) ? ' — ' . esc($j['NamaTpq']) : '' ?>
                                        </span>
                                    </td>

                                    <!-- Tertinggi & Rerata -->
                                    <td class="text-center fw-semibold"><?= $j['stats']['Tertinggi'] ?></td>
                                    <td class="text-center fw-semibold text-primary"><?= number_format($j['stats']['Rerata'], 2) ?></td>

                                    <!-- Peserta Metrics -->
                                    <td class="text-center fw-bold"><?= $j['stats']['Total'] ?></td>
                                    <td class="text-center"><?= $j['stats']['Mengerjakan'] ?></td>
                                    <td class="text-center text-success fw-bold"><?= $j['stats']['AtasKKM'] ?></td>
                                    <td class="text-center text-danger fw-bold"><?= $j['stats']['BawahKKM'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
function exportRekap(type) {
    let selectedIds = [];
    $('.check-row:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Jadwal Ujian',
                text: 'Silakan pilih/checklist minimal satu jadwal ujian pada tabel terlebih dahulu!',
                confirmButtonText: 'OK'
            });
        } else {
            alert('Silakan pilih/checklist minimal satu jadwal ujian pada tabel terlebih dahulu!');
        }
        return false;
    }

    let baseUrl = type === 'materi' 
        ? '<?= base_url('backend/ujian-mdta/rekap-materi') ?>' 
        : '<?= base_url('backend/ujian-mdta/rekap-sesi') ?>';

    let filterParams = '<?= $rekapQuery ?>';
    let targetUrl = baseUrl + '?' + filterParams + '&jadwal_ids=' + selectedIds.join(',');

    window.location.href = targetUrl;
}

$(document).ready(function() {
    // Checkbox Master Handlers
    $('#checkAllJadwal').on('change', function() {
        $('.check-row').prop('checked', this.checked);
    });

    $('#btnPilihSemua').on('click', function(e) {
        e.preventDefault();
        $('.check-row').prop('checked', true);
        $('#checkAllJadwal').prop('checked', true);
    });

    $('#btnBatalPilih').on('click', function(e) {
        e.preventDefault();
        $('.check-row').prop('checked', false);
        $('#checkAllJadwal').prop('checked', false);
    });

    // Select2 Initialization AdminLTE Style
    if ($.fn.select2) {
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    // DataTables Initialization
    if ($('#tabelLaporanHasil').length) {
        $('#tabelLaporanHasil').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 data",
                "paginate": {
                    "first": "Pertama", "last": "Terakhir", "next": "▶", "previous": "◀"
                }
            }
        });
    }
});
</script>

<?= $this->endSection(); ?>
