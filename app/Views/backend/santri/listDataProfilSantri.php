<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <!-- Card Informasi Alur Proses -->
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Alur Proses Profil Data Santri
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-3"><i class="fas fa-list-ol text-primary"></i> Alur Proses:</h5>
                    <ol class="mb-4">
                        <li class="mb-2">
                            <strong>Filter Data:</strong> Gunakan filter di atas tabel untuk menyaring data santri:
                            <ul class="mt-2">
                                <li><strong>Filter TPQ:</strong> Pilih TPQ untuk menampilkan santri dari TPQ tertentu. Format tampilan: "Nama TPQ - Kelurahan/Desa" untuk memudahkan pemilihan TPQ dengan nama yang sama.</li>
                                <li><strong>Filter Kelas:</strong> Pilih kelas untuk menampilkan santri dari kelas tertentu. Pilih "Semua Kelas" untuk menampilkan semua kelas.</li>
                                <li>Klik tombol <span class="badge badge-primary"><i class="fas fa-filter"></i> Filter</span> untuk menerapkan filter yang dipilih.</li>
                                <li>Klik tombol <span class="badge badge-secondary"><i class="fas fa-redo"></i> Reset</span> untuk menghapus semua filter dan menampilkan semua data.</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Lihat Daftar Santri:</strong> Tabel menampilkan daftar santri berdasarkan filter yang dipilih dengan informasi dasar
                            (IdSantri, Nama, Kelurahan/Desa, TPQ, Kelas, Status). Tabel dapat di-scroll horizontal jika kolom banyak.
                        </li>
                        <li class="mb-2">
                            <strong>Search & Sort:</strong> Gunakan search box DataTable untuk mencari santri berdasarkan nama, IdSantri, TPQ, atau kolom lainnya.
                            Data dapat diurutkan dengan mengklik header kolom.
                        </li>
                        <li class="mb-2">
                            <strong>Lihat Detail Profil:</strong> Klik tombol <span class="badge badge-info"><i class="fas fa-eye"></i> Profil</span>
                            pada kolom "Aksi" untuk melihat detail lengkap profil santri, termasuk:
                            <ul class="mt-2">
                                <li>Data pribadi santri lengkap</li>
                                <li>Data orang tua/wali</li>
                                <li>Data alamat lengkap</li>
                                <li>Foto profil</li>
                                <li>Riwayat pendidikan</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Print Profil:</strong>
                            <ul class="mt-2">
                                <li><strong>Print Individual:</strong> Klik tombol <span class="badge badge-primary"><i class="fas fa-print"></i> Print</span> pada kolom "Aksi" untuk mencetak profil santri individual dalam format PDF.</li>
                                <li><strong>Print All Profil:</strong> Klik tombol <span class="badge badge-success"><i class="fas fa-print"></i> Print All Profil</span> di form filter untuk mencetak semua profil santri berdasarkan filter yang dipilih dalam satu file PDF.</li>
                            </ul>
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Halaman ini menampilkan <strong>ringkasan profil</strong> santri. Untuk melihat detail lengkap, klik tombol <span class="badge badge-info"><i class="fas fa-eye"></i> Profil</span>.</li>
                            <li>Data ditampilkan berdasarkan <strong>TPQ</strong> dan <strong>Kelas</strong> yang sesuai dengan akses user Anda. Admin dapat memilih semua TPQ, sedangkan user lain hanya melihat TPQ mereka sendiri.</li>
                            <li>Kolom "Status" menunjukkan status verifikasi santri (<span class="badge bg-warning">Belum Diverifikasi</span>, <span class="badge bg-danger">Perlu Perbaikan</span>, <span class="badge bg-success">Terverifikasi</span>).</li>
                            <li>Gunakan fitur <strong>pagination</strong> di DataTable untuk navigasi halaman jika data banyak (default: 25 data per halaman).</li>
                            <li>Filter TPQ menampilkan format "Nama TPQ - Kelurahan/Desa" untuk memudahkan pemilihan TPQ dengan nama yang sama.</li>
                            <li>Tombol <strong>Print All Profil</strong> akan mencetak semua profil santri yang sesuai dengan filter yang dipilih dalam satu file PDF dengan page break otomatis.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profil Data Santri</h3>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form id="filterForm" method="GET" action="<?= base_url('backend/santri/showProfilSantri') ?>" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filterIdTpq">Filter TPQ</label>
                            <select class="form-control select2" id="filterIdTpq" name="filterIdTpq" style="width: 100%;" <?= !$isAdmin ? 'disabled' : '' ?>>
                                <?php if ($isAdmin): ?>
                                    <option value="">Semua TPQ</option>
                                    <?php foreach ($dataTpq as $tpq): ?>
                                        <?php
                                        $displayText = esc($tpq['NamaTpq']);
                                        if (!empty($tpq['KelurahanDesa'])) {
                                            $displayText .= ' - ' . esc($tpq['KelurahanDesa']);
                                        }
                                        ?>
                                        <option value="<?= $tpq['IdTpq'] ?>" <?= ($currentIdTpq == $tpq['IdTpq']) ? 'selected' : '' ?>>
                                            <?= $displayText ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php
                                    // Untuk non-Admin, tampilkan hanya TPQ mereka sendiri
                                    $userTpq = null;
                                    foreach ($dataTpq as $tpq) {
                                        if ($tpq['IdTpq'] == $currentIdTpq) {
                                            $userTpq = $tpq;
                                            break;
                                        }
                                    }
                                    if ($userTpq):
                                        $displayText = esc($userTpq['NamaTpq']);
                                        if (!empty($userTpq['KelurahanDesa'])) {
                                            $displayText .= ' - ' . esc($userTpq['KelurahanDesa']);
                                        }
                                    ?>
                                        <option value="<?= $userTpq['IdTpq'] ?>" selected>
                                            <?= $displayText ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (!$isAdmin): ?>
                                <input type="hidden" name="filterIdTpq" value="<?= $currentIdTpq ?>">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filterIdKelas">Filter Kelas</label>
                            <select class="form-control select2" id="filterIdKelas" name="filterIdKelas" style="width: 100%;">
                                <option value="">Semua Kelas</option>
                                <?php foreach ($dataKelas as $kelas): ?>
                                    <?php
                                    $selected = false;
                                    if (is_array($currentIdKelas)) {
                                        // Jika array, ambil yang pertama atau cek apakah ada di array
                                        $selected = in_array($kelas['IdKelas'], $currentIdKelas);
                                    } else if ($currentIdKelas !== null) {
                                        $selected = ($currentIdKelas == $kelas['IdKelas']);
                                    }
                                    ?>
                                    <option value="<?= $kelas['IdKelas'] ?>" <?= $selected ? 'selected' : '' ?>>
                                        <?= esc($kelas['NamaKelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="<?= base_url('backend/santri/showProfilSantri') ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                                <button type="button" class="btn btn-success" id="btnPrintAll" onclick="printAllProfil()">
                                    <i class="fas fa-print"></i> Print All Profil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="tblProfilSantri" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Aksi</th>
                            <th>IdSantri</th>
                            <th>Nama</th>
                            <th>Kelurahan/Desa</th>
                            <th>TPQ</th>
                            <th>Kelas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataSantri as $santri) : ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('backend/santri/profilDetailSantri/' . $santri['IdSantri']); ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i><span class="d-none d-md-inline">&nbsp;Profil</span>
                                    </a>
                                    <a href="<?= base_url('backend/santri/generatePDFprofilSantriRaport/' . $santri['IdSantri']); ?>" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-print"></i><span class="d-none d-md-inline">&nbsp;Print</span>
                                    </a>
                                </td>
                                <td><?= $santri['IdSantri']; ?></td>
                                <td><?= ucwords(strtolower($santri['NamaSantri'])); ?></td>
                                <td><?= ucwords(strtolower($santri['KelurahanDesa'])); ?></td>
                                <td><?= preg_replace_callback('/\b(al|el|ad|ar|at|an)-(\w+)/i', function ($matches) {
                                        return ucfirst(strtolower($matches[1])) . '-' . ucfirst($matches[2]);
                                    }, ucwords(strtolower($santri['NamaTpq']))); ?></td>
                                <td><?= $santri['NamaKelas']; ?></td>
                                <td>
                                    <?php if ($santri['Status'] == "Belum Diverifikasi"): ?>
                                        <span class="badge bg-warning"><?= $santri['Status']; ?></span>
                                    <?php elseif ($santri['Status'] == "Perlu Perbaikan"): ?>
                                        <span class="badge bg-danger"><?= $santri['Status']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $santri['Status']; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Aksi</th>
                            <th>IdSantri</th>
                            <th>Nama</th>
                            <th>Kelurahan/Desa</th>
                            <th>TPQ</th>
                            <th>Kelas</th>
                            <th>Status</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // Initialize Select2 untuk filter dropdown
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih...',
            allowClear: true
        });

        // Handle disabled select untuk Select2
        const tpqSelect = $('#filterIdTpq');
        if (tpqSelect.prop('disabled')) {
            tpqSelect.next('.select2-container').css('opacity', '0.6');
        }
    });

    // Function untuk print all profil berdasarkan filter
    function printAllProfil() {
        // Ambil nilai filter dari form
        // Jika select disabled, ambil dari hidden input atau select value
        let filterIdTpq = '';
        const tpqSelect = $('#filterIdTpq');
        if (tpqSelect.prop('disabled')) {
            // Jika disabled, ambil dari hidden input atau value select
            const hiddenInput = $('input[name="filterIdTpq"]');
            filterIdTpq = hiddenInput.length ? hiddenInput.val() : tpqSelect.val();
        } else {
            filterIdTpq = tpqSelect.val() || '';
        }

        const filterIdKelas = $('#filterIdKelas').val() || '';

        // Build URL dengan parameter filter
        let url = '<?= base_url('backend/santri/generatePDFAllProfilSantri') ?>?';

        if (filterIdTpq) {
            url += 'filterIdTpq=' + encodeURIComponent(filterIdTpq) + '&';
        }

        // Handle single select (bukan array)
        if (filterIdKelas && filterIdKelas !== '') {
            url += 'filterIdKelas=' + encodeURIComponent(filterIdKelas) + '&';
        }

        // Hapus trailing & jika ada
        url = url.replace(/[&?]$/, '');

        // Show loading indicator
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang membuat PDF untuk semua profil santri',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Open in new window
        const printWindow = window.open(url, '_blank');

        // Check if window was blocked
        if (!printWindow || printWindow.closed || typeof printWindow.closed == 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Popup Diblokir',
                text: 'Silakan izinkan popup untuk membuka PDF. Atau klik tombol Print All Profil lagi setelah mengizinkan popup.',
                confirmButtonText: 'OK'
            });
        } else {
            // Close loading after a delay
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    }

    // Initialize DataTable dengan scroll horizontal dan export buttons
    initializeDataTableScrollX("#tblProfilSantri", {
        "pageLength": 25,
        "lengthChange": true,
        "order": [
            [1, 'asc']
        ], // Sort by IdSantri
        "columnDefs": [{
            "targets": [0], // Kolom Aksi
            "orderable": false,
            "searchable": false
        }]
    });
</script>
<?= $this->endSection(); ?>