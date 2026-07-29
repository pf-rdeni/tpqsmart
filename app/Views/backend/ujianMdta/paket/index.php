<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    .table-responsive-custom {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
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
    @media (max-width: 767.98px) {
        #tabelPaketSoal th, #tabelPaketSoal td {
            white-space: nowrap;
            vertical-align: middle;
        }
        #tabelPaketSoal td.col-nama-paket {
            white-space: normal !important;
            min-width: 200px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="fas fa-layer-group text-success me-2"></i> Paket Soal Ujian MDTA</h4>
                    <small class="text-muted">Kelola bank soal dalam bentuk Paket Soal</small>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= base_url('backend/ujian-mdta/paket/arsip') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-archive me-1"></i> Lihat Arsip
                    </a>
                    <a href="<?= base_url('backend/ujian-mdta/paket/create') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages Handled via SweetAlert2 -->

    <!-- Alert Flash -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan pengisian:</h6>
            <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Bar AdminLTE Style -->
    <div class="card card-default card-outline card-success shadow-sm mb-3">
        <div class="card-body p-2">
            <form id="formFilterPaket" method="get" action="<?= base_url('backend/ujian-mdta/paket') ?>">
                <div class="row g-2 align-items-center">
                    <!-- Search Input AdminLTE style -->
                    <div class="col-md-5 col-12">
                        <div class="input-group input-group-sm">
                            <input type="text" name="keyword" class="form-control form-control-sm" id="inputKeywordFilter" placeholder="Cari nama paket soal..." value="<?= esc($keywordFilter ?? '') ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default btn-sm">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Kelas -->
                    <div class="col-md-3 col-6">
                        <select name="id_kelas" class="form-control form-control-sm filter-auto-submit">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelasList as $kls): ?>
                                <option value="<?= $kls['IdKelas'] ?>" <?= ($idKelasFilter ?? '') == $kls['IdKelas'] ? 'selected' : '' ?>>
                                    <?= esc($kls['NamaKelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Materi -->
                    <div class="col-md-3 col-6">
                        <select name="id_materi" class="form-control form-control-sm filter-auto-submit">
                            <option value="">-- Semua Materi --</option>
                            <?php foreach ($materiList as $mat): ?>
                                <option value="<?= $mat['IdMateri'] ?>" <?= ($idMateriFilter ?? '') == $mat['IdMateri'] ? 'selected' : '' ?>>
                                    <?= esc($mat['NamaMateri']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Reset Filter Button -->
                    <?php if (!empty($keywordFilter) || !empty($idKelasFilter) || !empty($idMateriFilter)): ?>
                        <div class="col-md-1 col-12 text-end">
                            <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="btn btn-outline-secondary btn-sm w-100" title="Reset Filter">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card card-outline card-success shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($paketList)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Belum ada paket soal</h5>
                    <a href="<?= base_url('backend/ujian-mdta/paket/create') ?>" class="btn btn-success btn-sm mt-2">
                        <i class="fas fa-plus me-1"></i> Buat Paket Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive table-responsive-custom">
                    <table class="table table-hover table-striped align-middle mb-0" id="tabelPaketSoal">
                        <thead class="table-success align-middle">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th width="185">Aksi</th>
                                <th class="col-nama-paket">Nama Paket Soal</th>
                                <th>Kelas</th>
                                <th>Materi</th>
                                <th class="text-center" width="160">Soal & Mode</th>
                                <th width="130">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paketList as $i => $paket): ?>
                                <tr class="align-middle">
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm text-nowrap align-items-center" style="white-space: nowrap !important;">
                                            <!-- Dropdown [Pilihan ▼] -->
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-primary btn-sm dropdown-toggle fw-semibold d-inline-flex align-items-center" type="button"
                                                        data-toggle="dropdown" data-bs-toggle="dropdown" data-boundary="window" data-bs-boundary="window" aria-expanded="false"
                                                        style="padding-top: 5px; padding-bottom: 5px; line-height: 1.2;">
                                                    Pilihan
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <!-- Cetak MS Word -->
                                                    <li><h6 class="dropdown-header text-uppercase fw-bold text-primary"><i class="fas fa-file-word me-1"></i> Cetak MS Word</h6></li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/paket/export-word/{$paket['id']}?mode=soal") ?>">
                                                            <i class="fas fa-file-word me-2 text-primary"></i>Cetak Soal Ms Word
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/paket/export-word/{$paket['id']}?mode=jawaban") ?>">
                                                            <i class="fas fa-key me-2 text-warning"></i>Cetak Jawaban Ms Word
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/paket/export-word/{$paket['id']}?mode=semua") ?>">
                                                            <i class="fas fa-file-invoice me-2 text-info"></i>Cetak Soal dan jawaban Ms Word
                                                        </a>
                                                    </li>

                                                    <li><hr class="dropdown-divider"></li>

                                                    <!-- Pengelolaan Soal -->
                                                    <li><h6 class="dropdown-header text-uppercase fw-bold text-secondary"><i class="fas fa-tasks me-1"></i> Pengelolaan Soal</h6></li>
                                                    <?php if (!empty($paket['isGlobalReadOnly'])): ?>
                                                        <li>
                                                            <a class="dropdown-item fw-bold text-warning btn-duplikasi" href="#" data-id="<?= $paket['id'] ?>" data-nama="<?= esc($paket['NamaPaket']) ?>">
                                                                <i class="fas fa-copy me-2 text-warning"></i>Duplikasi Ke TPQ Saya
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal") ?>">
                                                                <i class="fas fa-eye me-2 text-info"></i>Lihat Soal (Read-Only)
                                                            </a>
                                                        </li>
                                                    <?php else: ?>
                                                        <li>
                                                            <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal") ?>">
                                                                <i class="fas fa-edit me-2 text-success"></i>Kelola Soal
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item btn-duplikasi" href="#" data-id="<?= $paket['id'] ?>" data-nama="<?= esc($paket['NamaPaket']) ?>">
                                                                <i class="fas fa-copy me-2 text-warning"></i>Duplikasi Paket
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/paket/preview/{$paket['id']}") ?>" target="_blank">
                                                            <i class="fas fa-eye me-2 text-info"></i>Preview Soal
                                                        </a>
                                                    </li>

                                                    <li><hr class="dropdown-divider"></li>

                                                    <!-- Pengaturan Paket -->
                                                    <?php if (empty($paket['isGlobalReadOnly'])): ?>
                                                        <li>
                                                            <a class="dropdown-item" href="<?= base_url("backend/ujian-mdta/paket/edit/{$paket['id']}") ?>">
                                                                <i class="fas fa-pen me-2 text-secondary"></i>Edit Data Paket
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item btn-arsipkan" href="#" data-id="<?= $paket['id'] ?>" data-nama="<?= esc($paket['NamaPaket']) ?>">
                                                                <i class="fas fa-archive me-2 text-secondary"></i>Arsipkan Paket
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger btn-hapus" href="#" data-id="<?= $paket['id'] ?>" data-nama="<?= esc($paket['NamaPaket']) ?>">
                                                                <i class="fas fa-trash me-2"></i>Hapus Paket
                                                            </a>
                                                        </li>
                                                    <?php else: ?>
                                                        <li>
                                                            <span class="dropdown-item text-muted disabled"><i class="fas fa-lock me-2"></i>Edit/Hapus Terkunci (Global)</span>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                            <!-- Tombol Soal -->
                                            <a href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal") ?>"
                                               class="btn btn-outline-success btn-sm fw-semibold d-inline-flex align-items-center"
                                               style="padding-top: 5px; padding-bottom: 5px; line-height: 1.2; white-space: nowrap !important;"
                                               title="Lihat daftar soal dalam paket ini">
                                                <i class="fas fa-list-ol me-1"></i> Soal
                                                <?php if ($paket['JumlahSoal'] > 0): ?>
                                                    <span class="badge bg-success ms-1"><?= $paket['JumlahSoal'] ?></span>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    </td>
                                     <td>
                                         <div class="fw-bold text-dark mb-1"><?= esc($paket['NamaPaket']) ?></div>
                                         <div>
                                             <?php
                                             $isGlobal   = (!empty($paket['IsGlobal']) && (int)$paket['IsGlobal'] === 1);
                                             $idTpqPaket = (string)($paket['IdTpq'] ?? '0');
                                             ?>
                                             <?php if ($idTpqPaket === '0'): ?>
                                                 <?php if ($isGlobal): ?>
                                                     <span class="badge bg-primary text-white"><i class="fas fa-globe me-1"></i>GLOBAL (PUSAT)</span>
                                                 <?php else: ?>
                                                     <span class="badge bg-info text-white"><i class="fas fa-user-shield me-1"></i>PRIVAT ADMIN</span>
                                                 <?php endif; ?>
                                             <?php else: ?>
                                                 <?php if ($isGlobal): ?>
                                                     <span class="badge bg-primary text-white"><i class="fas fa-globe me-1"></i>GLOBAL (SHARED)</span>
                                                 <?php else: ?>
                                                     <span class="badge bg-success text-white"><i class="fas fa-building me-1"></i>PRIVAT TPQ</span>
                                                 <?php endif; ?>
                                             <?php endif; ?>
                                         </div>
                                     </td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($paket['NamaKelas'] ?? '-') ?></span></td>
                                    <td><small><?= esc($paket['NamaMateri'] ?? '-') ?></small></td>
                                    <td class="text-center">
                                         <?php
                                         $jmlPG   = (int)($paket['JumlahPG'] ?? 0);
                                         $jmlEsai = (int)($paket['JumlahEsai'] ?? 0);
                                         $totalS  = (int)($paket['JumlahSoal'] ?? 0);

                                         $modeRaw = strtoupper(trim((string)($paket['ModeJawaban'] ?? 'ABCD')));
                                         if (is_numeric($modeRaw)) {
                                             $numOpsi = (int)$modeRaw;
                                             $endChar = chr(64 + min(26, max(1, $numOpsi)));
                                             $opsiText = "A-{$endChar}";
                                         } else if (strlen($modeRaw) > 0 && preg_match('/^[A-Z]+$/', $modeRaw)) {
                                             $firstChar = substr($modeRaw, 0, 1);
                                             $lastChar  = substr($modeRaw, -1);
                                             $opsiText  = ($firstChar === $lastChar) ? $firstChar : "{$firstChar}-{$lastChar}";
                                         } else {
                                             $opsiText = 'A-D';
                                         }
                                         ?>

                                         <?php if ($totalS == 0): ?>
                                             <span class="badge bg-secondary mb-1">0 Soal</span>
                                         <?php else: ?>
                                             <div class="mb-1">
                                                 <span class="badge bg-success px-2 py-1"><?= $totalS ?> Soal</span>
                                             </div>
                                             <div class="small text-dark">
                                                 <?php if ($jmlPG > 0): ?>
                                                     <div class="text-nowrap"><?= $jmlPG ?> PG (Opsi <?= $opsiText ?>)</div>
                                                 <?php endif; ?>
                                                 <?php if ($jmlEsai > 0): ?>
                                                     <div class="text-nowrap"><?= $jmlEsai ?> Esai</div>
                                                 <?php endif; ?>
                                             </div>
                                         <?php endif; ?>
                                     </td>
                                    <td>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($paket['created_at'])) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Form Tersembunyi untuk POST Actions -->
<form id="formAction" method="post" action="" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method_action" value="">
</form>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-trigger Filter pada Perubahan Dropdown & Ketikan Keyword
    const formFilter = document.getElementById('formFilterPaket');
    if (formFilter) {
        // Dropdown Kelas & Materi -> submit langsung saat berubah
        formFilter.querySelectorAll('.filter-auto-submit').forEach(select => {
            select.addEventListener('change', function () {
                formFilter.submit();
            });
        });

        // Input Keyword -> submit otomatis setelah 500ms selesai mengetik
        const keywordInput = document.getElementById('inputKeywordFilter');
        if (keywordInput) {
            let debounceTimer;
            keywordInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    formFilter.submit();
                }, 500);
            });

            // Jika sedang ada kata kunci pencarian, tempatkan kursor di akhir kata
            if (keywordInput.value.length > 0) {
                keywordInput.focus();
                const len = keywordInput.value.length;
                keywordInput.setSelectionRange(len, len);
            }
        }
    }

    // Duplikasi Paket (Global ke Lokal TPQ)
    document.querySelectorAll('.btn-duplikasi').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id   = this.dataset.id;
            const nama = this.dataset.nama;
            Swal.fire({
                title: `Duplikasi Paket Soal?`,
                html: `Paket <strong>"${nama}"</strong> akan diduplikasi menjadi <strong>Paket Lokal TPQ Anda</strong>.<br><small class="text-muted">Seluruh butir soal dan kunci jawaban akan disalin secara utuh. Anda dapat mengedit dan mengkustomisasi soal setelah diduplikasi.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-copy me-1"></i> Ya, Duplikasi Ke TPQ Saya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formAction');
                    form.action = `<?= base_url('backend/ujian-mdta/paket/duplikasi/') ?>${id}`;
                    form.submit();
                }
            });
        });
    });

    // Arsipkan
    document.querySelectorAll('.btn-arsipkan').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id   = this.dataset.id;
            const nama = this.dataset.nama;
            Swal.fire({
                title: `Arsipkan Paket Soal?`,
                text: `Paket "${nama}" akan dipindahkan ke folder Arsip.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Arsipkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formAction');
                    form.action = `<?= base_url('backend/ujian-mdta/paket/arsipkan/') ?>${id}`;
                    form.submit();
                }
            });
        });
    });

    // Hapus
    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id   = this.dataset.id;
            const nama = this.dataset.nama;
            Swal.fire({
                title: `Hapus Paket Soal?`,
                text: `Paket "${nama}" akan dihapus. Paket tidak bisa dihapus jika sedang digunakan di jadwal ujian.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus Paket',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formAction');
                    form.action = `<?= base_url('backend/ujian-mdta/paket/delete/') ?>${id}`;
                    form.submit();
                }
            });
        });
    });

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

    // SweetAlert2 Flash Notifications
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

    // Initialize DataTables for Paket Soal
    if (typeof $.fn.DataTable !== 'undefined') {
        if ($.fn.DataTable.isDataTable('#tabelPaketSoal')) {
            $('#tabelPaketSoal').DataTable().destroy();
        }
        $('#tabelPaketSoal').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "language": {
                "sSearch": "Cari Paket:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ paket",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 paket",
                "infoFiltered": "(disaring dari _MAX_ total paket)",
                "zeroRecords": "Tidak ada paket soal yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Lanjut",
                    "previous": "Sebelumnya"
                }
            }
        });
    }
});
</script>

<?= $this->endSection(); ?>
