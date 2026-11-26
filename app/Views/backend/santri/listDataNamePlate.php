<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Cetak Name Plate Raport Santri
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Setiap label Name Plate berukuran <strong>10 cm &times; 4.8 cm</strong> dengan foto profil ukuran <strong>3 &times; 4 cm</strong>.</li>
                <li>Komposisi label: <strong>Foto (3&times;4) | Profil Informasi</strong> (ID Santri, Nama, Alamat, Nama Orang Tua, dan No. HP).</li>
                <li>Jika santri belum memiliki foto profil, akan ditampilkan kotak kosong bertuliskan <strong>Foto 3x4</strong> untuk tempel foto fisik.</li>
                <li>Gunakan pilihan ukuran kertas <strong>A4</strong> (10 label/halaman) atau <strong>F4 / Folio</strong> (12 label/halaman) saat mencetak massal atau pilihan santri.</li>
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h3 class="card-title mb-2 mb-md-0">Cetak Name Plate Santri</h3>
            <div class="d-flex align-items-center ml-auto">
                <label for="paperSizeSelect" class="mb-0 mr-2 font-weight-normal text-sm">Ukuran Kertas:</label>
                <select id="paperSizeSelect" class="form-control form-control-sm" style="width: auto;">
                    <option value="a4" selected>A4 (10 label / lembar)</option>
                    <option value="folio">F4 / Folio (12 label / lembar)</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($dataKelasObject)) : ?>
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTabNamePlate" role="tablist">
                            <?php foreach ($dataKelasObject as $index => $kelas) : ?>
                                <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                    <a class="nav-link border-white text-center <?= $index === 0 ? 'active' : '' ?>"
                                        id="tab-np-<?= $kelas->IdKelas ?>"
                                        data-toggle="tab"
                                        href="#kelas-np-<?= $kelas->IdKelas ?>"
                                        role="tab"
                                        aria-controls="kelas-np-<?= $kelas->IdKelas ?>"
                                        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                        <?= esc($kelas->NamaKelas) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="kelasTabNamePlateContent">
                            <?php foreach ($dataKelasObject as $index => $kelas) : ?>
                                <?php $kelasSantri = $santriPerKelas[$kelas->IdKelas] ?? []; ?>
                                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                                    id="kelas-np-<?= $kelas->IdKelas ?>"
                                    role="tabpanel"
                                    aria-labelledby="tab-np-<?= $kelas->IdKelas ?>">
                                    <div class="mb-3 d-flex flex-wrap gap-2">
                                        <button type="button"
                                            class="btn btn-warning btn-sm mr-2 mb-2 btn-print-all-nameplate"
                                            data-tpq="<?= esc($currentIdTpq) ?>"
                                            data-kelas="<?= esc($kelas->IdKelas) ?>">
                                            <i class="fas fa-print"></i> Cetak Semua Name Plate (Kelas <?= esc($kelas->NamaKelas) ?>)
                                        </button>
                                        <button type="button"
                                            class="btn btn-success btn-sm mb-2 btn-print-selected-nameplate"
                                            data-kelas="<?= esc($kelas->IdKelas) ?>">
                                            <i class="fas fa-check-square"></i> Cetak Terpilih
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tableNamePlate-<?= $kelas->IdKelas ?>">
                                            <thead>
                                                <tr>
                                                    <th style="width: 35px;" class="text-center">
                                                        <input type="checkbox" class="check-all-np" data-kelas="<?= esc($kelas->IdKelas) ?>" title="Pilih Semua">
                                                    </th>
                                                    <th>No</th>
                                                    <th>Aksi</th>
                                                    <th>Foto</th>
                                                    <th>ID Santri</th>
                                                    <th>Nama Santri</th>
                                                    <th>Alamat</th>
                                                    <th>Nama Orang Tua</th>
                                                    <th>No. HP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($kelasSantri)) : ?>
                                                    <?php $no = 1; ?>
                                                    <?php foreach ($kelasSantri as $santri) : ?>
                                                        <?php
                                                        $hasPhoto = false;
                                                        $photoUrl = '';
                                                        if (!empty($santri['PhotoProfil'])) {
                                                            $photoFile = FCPATH . 'uploads/santri/' . $santri['PhotoProfil'];
                                                            if (file_exists($photoFile)) {
                                                                $hasPhoto = true;
                                                                $photoUrl = base_url('uploads/santri/' . $santri['PhotoProfil']);
                                                            }
                                                        }
                                                        $ortu = implode(' | ', array_filter([
                                                            trim((string) ($santri['NamaAyah'] ?? '')),
                                                            trim((string) ($santri['NamaIbu'] ?? '')),
                                                        ], static fn($v) => $v !== '' && $v !== '-'));
                                                        $hp = implode(' | ', array_unique(array_filter([
                                                            trim((string) ($santri['NoHpSantri'] ?? '')),
                                                            trim((string) ($santri['NoHpAyah'] ?? '')),
                                                            trim((string) ($santri['NoHpIbu'] ?? '')),
                                                        ], static fn($v) => $v !== '' && $v !== '-')));
                                                        ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="check-item-np check-item-np-<?= esc($kelas->IdKelas) ?>" value="<?= esc($santri['IdSantri']) ?>">
                                                            </td>
                                                            <td><?= $no++ ?></td>
                                                            <td>
                                                                <a href="<?= base_url('backend/santri/generatePDFNamePlate/' . $santri['IdSantri']) ?>"
                                                                    class="btn btn-primary btn-xs btn-print-single-np"
                                                                    data-id="<?= esc($santri['IdSantri']) ?>"
                                                                    target="_blank">
                                                                    <i class="fas fa-print"></i> Cetak
                                                                </a>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if ($hasPhoto) : ?>
                                                                    <img src="<?= esc($photoUrl) ?>" alt="Foto" style="width: 36px; height: 48px; object-fit: cover; border: 1px solid #ccc;">
                                                                <?php else : ?>
                                                                    <span class="badge badge-secondary">Foto 3x4 Kosong</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= esc($santri['IdSantri']) ?></td>
                                                            <td><?= esc($santri['NamaSantri']) ?></td>
                                                            <td><?= esc($santri['AlamatSantri'] ?? '-') ?></td>
                                                            <td><?= esc($ortu ?: '-') ?></td>
                                                            <td><?= esc($hp ?: '-') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Tidak ada data santri yang ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<form id="formSelectedNamePlate" action="<?= base_url('backend/santri/generatePDFSelectedNamePlate') ?>" method="POST" target="_blank" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="santri_ids" id="selectedSantriIds">
    <input type="hidden" name="paper" id="selectedPaperSize" value="a4">
</form>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    <?php if (!empty($dataKelasObject)) : ?>
        <?php foreach ($dataKelasObject as $kelas) : ?>
            initializeDataTableUmum("#tableNamePlate-<?= $kelas->IdKelas ?>", true, true);
        <?php endforeach; ?>
    <?php endif; ?>

    const npStorageKey = 'nameplate_active_tab_<?= $currentIdTpq ?>';
    $('#kelasTabNamePlate a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        localStorage.setItem(npStorageKey, $(e.target).attr('href'));
    });

    const savedNpTab = localStorage.getItem(npStorageKey);
    if (savedNpTab && $(savedNpTab).length > 0) {
        $('#kelasTabNamePlate a[href="' + savedNpTab + '"]').tab('show');
    }

    $(document).on('change', '.check-all-np', function() {
        const kelasId = $(this).data('kelas');
        $('.check-item-np-' + kelasId).prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-print-single-np', function(e) {
        e.preventDefault();
        const idSantri = $(this).data('id');
        const paper = $('#paperSizeSelect').val() || 'a4';
        const url = '<?= base_url('backend/santri/generatePDFNamePlate') ?>/' + encodeURIComponent(idSantri) + '?paper=' + encodeURIComponent(paper);
        window.open(url, '_blank');
    });

    $(document).on('click', '.btn-print-all-nameplate', function() {
        const idTpq = $(this).data('tpq') || '';
        const idKelas = $(this).data('kelas') || '';
        const paper = $('#paperSizeSelect').val() || 'a4';
        const url = '<?= base_url('backend/santri/generatePDFAllNamePlate') ?>?filterIdTpq=' + encodeURIComponent(idTpq) + '&filterIdKelas=' + encodeURIComponent(idKelas) + '&paper=' + encodeURIComponent(paper);
        window.open(url, '_blank');
    });

    $(document).on('click', '.btn-print-selected-nameplate', function() {
        const kelasId = $(this).data('kelas');
        const ids = [];
        $('.check-item-np-' + kelasId + ':checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum ada santri dipilih',
                text: 'Centang minimal satu santri terlebih dahulu untuk dicetak.'
            });
            return;
        }

        $('#selectedSantriIds').val(ids.join(','));
        $('#selectedPaperSize').val($('#paperSizeSelect').val() || 'a4');
        $('#formSelectedNamePlate').submit();
    });
</script>
<?= $this->endSection(); ?>
