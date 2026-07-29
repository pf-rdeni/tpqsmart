<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    .table-rekap th, .table-rekap td {
        vertical-align: middle;
        text-align: center;
        font-size: 0.85rem;
    }
    .table-rekap th.text-left, .table-rekap td.text-left,
    .table-rekap th.text-start, .table-rekap td.text-start {
        text-align: left !important;
    }
    .badge-jawaban-benar {
        background-color: #28a745;
        color: #fff;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .badge-jawaban-salah {
        background-color: #dc3545;
        color: #fff;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .badge-jawaban-kosong {
        background-color: #e9ecef;
        color: #6c757d;
        padding: 4px 8px;
        border-radius: 4px;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold"><i class="fas fa-list-ol text-info me-2"></i> Rekap Jawaban Santri</h4>
            <small class="text-muted"><?= esc($jadwal['NamaUjian']) ?> — <?= esc($jadwal['NamaKelas'] ?? 'Kelas') ?></small>
        </div>
        <div>
            <a href="<?= base_url('backend/ujian-mdta/laporan-hasil') ?>" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button onclick="window.print();" class="btn btn-primary btn-sm">
                <i class="fas fa-print me-1"></i> Cetak Halaman
            </button>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card card-outline card-info shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="row text-sm">
                <div class="col-md-3"><strong>Nama Ujian:</strong> <?= esc($jadwal['NamaUjian']) ?></div>
                <div class="col-md-3"><strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($jadwal['TanggalMulai'])) ?></div>
                <div class="col-md-3"><strong>KKM:</strong> <span class="badge bg-warning text-dark"><?= esc($jadwal['NilaiMinimum']) ?></span></div>
                <div class="col-md-3"><strong>Total Soal:</strong> <?= count($soalList) ?> Butir</div>
            </div>
        </div>
    </div>

    <!-- Table Rekap Jawaban -->
    <div class="card card-default shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 table-rekap" id="tabelRekapJawaban">
                    <thead class="bg-light">
                        <tr>
                            <th width="40" rowspan="2">No</th>
                            <th width="120" rowspan="2" class="text-left text-start">No. Peserta</th>
                            <th width="200" rowspan="2" class="text-left text-start">Nama Santri</th>
                            <?php if (!empty($isUserAdmin)): ?>
                                <th width="160" rowspan="2" class="text-left text-start">Lembaga / TPQ</th>
                            <?php endif; ?>
                            <th colspan="<?= count($soalList) ?>" class="text-center">Nomor Soal</th>
                            <th width="60" rowspan="2">Benar</th>
                            <th width="60" rowspan="2">Salah</th>
                            <th width="70" rowspan="2">Nilai</th>
                        </tr>
                        <tr>
                            <?php foreach ($soalList as $s): ?>
                                <th width="35" title="Soal No. <?= $s['NomorSoal'] ?>"><?= $s['NomorSoal'] ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sesiList)): ?>
                            <tr>
                                <td colspan="<?= (!empty($isUserAdmin) ? 7 : 6) + count($soalList) ?>" class="text-center py-4 text-muted">
                                    Belum ada santri yang mengikuti atau menyelesaikan ujian ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sesiList as $idx => $sesi): ?>
                                <?php 
                                $idSesi = $sesi['idSesi'] ?? $sesi['id'] ?? $sesi['IdSesi'] ?? null;
                                $noPeserta = $sesi['IdSantri'] ?? '-';
                                $totalBenar = 0;
                                $totalSalah = 0;


                                ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-semibold text-secondary text-left text-start"><?= esc($noPeserta) ?></td>
                                    <td class="text-left text-start fw-bold"><?= esc($sesi['NamaSantri']) ?></td>
                                    <?php if (!empty($isUserAdmin)): ?>
                                        <td class="text-left text-start text-muted text-nowrap"><?= esc($sesi['NamaTpq'] ?? '-') ?></td>
                                    <?php endif; ?>

                                    
                                    <?php foreach ($soalList as $s): ?>
                                        <?php 
                                        $idSoal = $s['id'];
                                        $jInfo = ($idSesi && isset($jawabanMap[$idSesi][$idSoal])) ? $jawabanMap[$idSesi][$idSoal] : null;
                                        ?>

                                        <td>
                                            <?php if ($jInfo !== null): ?>
                                                <?php if ($jInfo['isBenar'] == 1): ?>
                                                    <?php $totalBenar++; ?>
                                                    <span class="badge-jawaban-benar"><?= esc($jInfo['jawaban'] ?? '✓') ?></span>
                                                <?php else: ?>
                                                    <?php $totalSalah++; ?>
                                                    <span class="badge-jawaban-salah"><?= esc($jInfo['jawaban'] ?? '✗') ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge-jawaban-kosong">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <td class="text-success fw-bold"><?= $totalBenar ?></td>
                                    <td class="text-danger fw-bold"><?= $totalSalah ?></td>
                                    <td class="fw-bold text-primary"><?= number_format((float)$sesi['NilaiAkhir'], 2) ?></td>
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
$(document).ready(function() {
    if ($('#tabelRekapJawaban').length) {
        $('#tabelRekapJawaban').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 data",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "▶",
                    "previous": "◀"
                }
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>


