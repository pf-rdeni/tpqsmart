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
                            <strong>Pilih Kelas:</strong> Gunakan tab di atas untuk memilih kelas yang ingin dikelola. Setiap tab menampilkan santri dari kelas yang berbeda. Tab aktif akan tersimpan otomatis, sehingga saat refresh halaman, tab yang terakhir dipilih akan tetap aktif.
                        </li>
                        <li class="mb-2">
                            <strong>Lihat Daftar Santri:</strong> Tabel menampilkan daftar santri per kelas dengan informasi lengkap:
                            <ul class="mt-2">
                                <li><strong>No:</strong> Nomor urut santri</li>
                                <li><strong>Aksi:</strong> Tombol untuk mencetak profil individual</li>
                                <li><strong>ID Santri:</strong> Identitas unik santri</li>
                                <li><strong>Nama:</strong> Nama lengkap santri</li>
                                <li><strong>NIK:</strong> Nomor Induk Kependudukan</li>
                                <li><strong>Jenis Kelamin:</strong> Jenis kelamin santri</li>
                                <li><strong>Tempat, Tgl Lahir:</strong> Tempat dan tanggal lahir</li>
                                <li><strong>Alamat:</strong> Alamat lengkap santri</li>
                                <li><strong>No HP / Email:</strong> Kontak santri</li>
                                <li><strong>Nama Ayah/Ibu:</strong> Nama orang tua</li>
                                <li><strong>Pekerjaan Ayah/Ibu:</strong> Pekerjaan orang tua</li>
                                <li><strong>No HP Ayah/Ibu:</strong> Kontak orang tua</li>
                                <li><strong>TPQ:</strong> Nama TPQ</li>
                                <li><strong>Kelas:</strong> Nama kelas</li>
                            </ul>
                            Tabel dapat di-scroll horizontal jika kolom banyak.
                        </li>
                        <li class="mb-2">
                            <strong>Search & Sort:</strong> Gunakan search box DataTable untuk mencari santri berdasarkan nama atau Id Santri.
                            Data dapat diurutkan dengan mengklik header kolom.
                        </li>
                        <li class="mb-2">
                            <strong>Card Tanda Tangan QR:</strong>
                            <ul class="mt-2">
                                <li>Card ini menampilkan statistik tanda tangan untuk setiap kelas (Sudah TTD / Belum TTD)</li>
                                <li><strong>Untuk Kepala Sekolah:</strong> Dapat mengklik tombol <span class="badge badge-warning"><i class="fas fa-signature"></i> Tanda Tangan Semua</span> untuk menandatangani semua profil santri dalam kelas tersebut secara bulk.</li>
                                <li>Tombol akan berubah menjadi <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Batalkan TTD QR</span> jika semua profil sudah ditandatangani.</li>
                                <li><strong>Untuk User Lain:</strong> Card tetap terlihat untuk melihat statistik, namun tombol akan terkunci dengan informasi bahwa hanya Kepala Sekolah yang dapat melakukan tanda tangan.</li>
                            </ul>
                            <small class="text-muted">Catatan: Tanda tangan dilakukan secara bulk untuk semua santri dalam kelas. Setiap profil yang ditandatangani akan memiliki QR code yang dapat divalidasi.</small>
                        </li>
                        <li class="mb-2">
                            <strong>Card Cetak Semua Profil:</strong>
                            <ul class="mt-2">
                                <li>Card ini menampilkan informasi tentang pencetakan profil untuk kelas yang dipilih</li>
                                <li>Menampilkan status apakah profil akan dicetak <strong>dengan tandatangan QR</strong> (jika sudah ditandatangani) atau <strong>tanpa tandatangan QR</strong> (jika belum ditandatangani)</li>
                                <li>Klik tombol <span class="badge badge-warning"><i class="fas fa-print"></i> Cetak Semua Profil</span> untuk mencetak semua profil santri dalam kelas tersebut dalam satu file PDF</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Print Profil:</strong>
                            <ul class="mt-2">
                                <li><strong>Print Individual:</strong> Klik tombol <span class="badge badge-primary"><i class="fas fa-print"></i> Print</span> pada kolom "Aksi" untuk mencetak profil santri individual dalam format PDF. Profil akan dicetak dengan QR code jika sudah ditandatangani.</li>
                                <li><strong>Print All Profil:</strong> Klik tombol <span class="badge badge-warning"><i class="fas fa-print"></i> Cetak Semua Profil</span> di card untuk mencetak semua profil santri dalam kelas tersebut dalam satu file PDF dengan page break otomatis.</li>
                            </ul>
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Halaman ini menampilkan <strong>profil lengkap</strong> santri yang dikelompokkan per kelas menggunakan tab. Setiap kelas memiliki tab sendiri.</li>
                            <li>Data ditampilkan berdasarkan <strong>TPQ</strong> dan <strong>Kelas</strong> yang sesuai dengan akses user Anda.</li>
                            <li>Tabel menampilkan informasi lengkap: <strong>No, Aksi, ID Santri, Nama, NIK, Jenis Kelamin, Tempat/Tgl Lahir, Alamat, No HP/Email, Data Orang Tua, TPQ, dan Kelas</strong>.</li>
                            <li>Gunakan fitur <strong>pagination</strong> di DataTable untuk navigasi halaman jika data banyak (default: 25 data per halaman).</li>
                            <li>Tab aktif akan tersimpan di localStorage, sehingga saat refresh halaman, tab yang terakhir dipilih akan tetap aktif.</li>
                            <li>Card <strong>Tanda Tangan QR</strong> dan <strong>Cetak Semua Profil</strong> menggunakan layout setengah lebar (col-md-6) dan berdampingan.</li>
                            <li>Card statistik menampilkan informasi real-time tentang status tanda tangan untuk setiap kelas.</li>
                            <li>QR code di PDF dapat diklik dan akan mengarah ke halaman validasi tanda tangan digital.</li>
                            <li>Profil yang sudah ditandatangani akan memiliki QR code di PDF, sedangkan yang belum ditandatangani tidak akan memiliki QR code.</li>
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
            <!-- Tab Navigation -->
            <?php if (!empty($dataKelasObject)): ?>
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTab" role="tablist">
                            <?php foreach ($dataKelasObject as $index => $kelas) : ?>
                                <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                    <a class="nav-link border-white text-center <?= $index === 0 ? 'active' : '' ?>"
                                        id="tab-<?= $kelas->IdKelas ?>"
                                        data-toggle="tab"
                                        href="#kelas-<?= $kelas->IdKelas ?>"
                                        role="tab"
                                        aria-controls="kelas-<?= $kelas->IdKelas ?>"
                                        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                        <?= esc($kelas->NamaKelas) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <br>
                    <div class="card-body">
                        <div class="tab-content" id="kelasTabContent">
                            <?php foreach ($dataKelasObject as $index => $kelas) : ?>
                                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                                    id="kelas-<?= $kelas->IdKelas ?>"
                                    role="tabpanel"
                                    aria-labelledby="tab-<?= $kelas->IdKelas ?>">
                                    <div class="table-responsive">
                                        <div class="mb-3">
                                            <div class="row">
                                                <?php
                                                // Cek status tanda tangan bulk untuk kelas ini
                                                $bulkStatus = $bulkSignatureStatus[$kelas->IdKelas] ?? null;
                                                $allSignedKepsek = $bulkStatus && $bulkStatus['all_signed_kepsek'];
                                                $totalSantri = $bulkStatus['total'] ?? 0;
                                                $ttdKepsek = $bulkStatus['ttd_kepsek'] ?? 0;
                                                $belumTtdKepsek = $bulkStatus['belum_ttd_kepsek'] ?? 0;
                                                ?>

                                                <div class="col-md-6 mb-3">
                                                    <div class="info-box <?= $allSignedKepsek ? 'bg-gradient-success' : 'bg-gradient-info' ?> shadow-sm">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-qrcode"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">
                                                                <?= $allSignedKepsek ? 'Batalkan TTD QR' : 'Tanda Tangan QR' ?>
                                                                <br><small>Kepala Sekolah - Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                            </span>
                                                            <div class="row mt-2">
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <span class="info-box-number" style="font-size: 1.5rem;"><?= $ttdKepsek ?></span>
                                                                        <small>Sudah TTD</small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <span class="info-box-number" style="font-size: 1.5rem;"><?= $belumTtdKepsek ?></span>
                                                                        <small>Belum TTD</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <?php if (!$isKepalaSekolah): ?>
                                                                    <button type="button" class="btn btn-secondary btn-sm btn-block" disabled>
                                                                        <i class="fas fa-lock"></i> Tanda Tangan Terkunci
                                                                    </button>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-info-circle"></i> Hanya Kepala Sekolah yang dapat melakukan tanda tangan QR
                                                                        </small>
                                                                    </div>
                                                                <?php elseif ($allSignedKepsek): ?>
                                                                    <button type="button" class="btn btn-danger btn-sm btn-block btn-cancel-ttd-kepsek-profil"
                                                                        data-tpq="<?= $currentIdTpq ?>"
                                                                        data-kelas="<?= $kelas->IdKelas ?>">
                                                                        <i class="fas fa-times-circle"></i> Batalkan TTD QR
                                                                    </button>
                                                                    <div class="mt-2">
                                                                        <small>Info: Untuk membatalkan tanda tangan semua profil, silahkan klik tombol <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Batalkan TTD</span> di card</small>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-warning btn-sm btn-block btn-ttd-bulk-kepsek-profil"
                                                                        data-tpq="<?= $currentIdTpq ?>"
                                                                        data-kelas="<?= $kelas->IdKelas ?>">
                                                                        <i class="fas fa-signature"></i> Tanda Tangan Semua
                                                                    </button>
                                                                    <div class="mt-2">
                                                                        <small>Info: Untuk menandatangani semua profil, silahkan klik tombol <span class="badge badge-warning"><i class="fas fa-signature"></i> Tanda Tangan Semua</span> di card</small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div class="info-box bg-gradient-primary shadow-sm">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-print"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">
                                                                Cetak Semua Profil
                                                                <br><small>Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                            </span>
                                                            <div class="mt-2">
                                                                <?php if ($allSignedKepsek): ?>
                                                                    <div class="alert alert-success py-2 mb-2" style="font-size: 0.85rem;">
                                                                        <i class="fas fa-check-circle"></i> <strong>Akan dicetak dengan tandatangan QR</strong>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="alert alert-warning py-2 mb-2" style="font-size: 0.85rem;">
                                                                        <i class="fas fa-exclamation-triangle"></i> <strong>Akan dicetak tanpa tandatangan QR</strong>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <button type="button"
                                                                    class="btn btn-warning btn-sm btn-block btn-print-all-profil"
                                                                    data-tpq="<?= $currentIdTpq ?>"
                                                                    data-kelas="<?= $kelas->IdKelas ?>">
                                                                    <i class="fas fa-print"></i> Cetak Semua Profil
                                                                </button>
                                                            </div>
                                                            <div class="mt-2">
                                                                <small>Info: Untuk mencetak semua profil santri dalam kelas ini, silahkan klik tombol <span class="badge badge-warning"><i class="fas fa-print"></i> Cetak Semua Profil</span> di card. atau klik tombol <span class="badge badge-primary"><i class="fas fa-print"></i> Print</span> pada kolom Aksi untuk mencetak profil individual.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-striped" id="tableSantri-<?= $kelas->IdKelas ?>">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Aksi</th>
                                                    <th>ID Santri</th>
                                                    <th>Nama</th>
                                                    <th>NIK</th>
                                                    <th>Jenis Kelamin</th>
                                                    <th>Tempat, Tgl Lahir</th>
                                                    <th>Alamat</th>
                                                    <th>No HP / Email</th>
                                                    <th>Nama Ayah</th>
                                                    <th>Nama Ibu</th>
                                                    <th>Pekerjaan Ayah</th>
                                                    <th>Pekerjaan Ibu</th>
                                                    <th>No HP Ayah</th>
                                                    <th>No HP Ibu</th>
                                                    <th>TPQ</th>
                                                    <th>Kelas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                $kelasSantri = $santriPerKelas[$kelas->IdKelas] ?? [];
                                                foreach ($kelasSantri as $santri) :
                                                    // Format tanggal lahir
                                                    $tanggalLahir = '-';
                                                    if (!empty($santri['TanggalLahirSantri'])) {
                                                        try {
                                                            $dateObj = new \DateTime($santri['TanggalLahirSantri']);
                                                            $tanggalLahir = $dateObj->format('d/m/Y');
                                                        } catch (\Exception $e) {
                                                            $tanggalLahir = $santri['TanggalLahirSantri'];
                                                        }
                                                    }

                                                    $tempatTglLahir = '';
                                                    if (!empty($santri['TempatLahirSantri'])) {
                                                        $tempatTglLahir = $santri['TempatLahirSantri'];
                                                    }
                                                    if (!empty($tempatTglLahir) && $tanggalLahir !== '-') {
                                                        $tempatTglLahir .= ', ' . $tanggalLahir;
                                                    } elseif ($tanggalLahir !== '-') {
                                                        $tempatTglLahir = $tanggalLahir;
                                                    }
                                                    if (empty($tempatTglLahir)) {
                                                        $tempatTglLahir = '-';
                                                    }

                                                    // Format alamat
                                                    $alamatParts = [];
                                                    if (!empty($santri['AlamatSantri'])) {
                                                        $alamatParts[] = $santri['AlamatSantri'];
                                                    }
                                                    if (!empty($santri['RtSantri'])) {
                                                        $alamatParts[] = 'RT ' . $santri['RtSantri'];
                                                    }
                                                    if (!empty($santri['RwSantri'])) {
                                                        $alamatParts[] = 'RW ' . $santri['RwSantri'];
                                                    }
                                                    if (!empty($santri['KelurahanDesaSantri'])) {
                                                        $alamatParts[] = $santri['KelurahanDesaSantri'];
                                                    }
                                                    if (!empty($santri['KecamatanSantri'])) {
                                                        $alamatParts[] = $santri['KecamatanSantri'];
                                                    }
                                                    if (!empty($santri['KabupatenKotaSantri'])) {
                                                        $alamatParts[] = $santri['KabupatenKotaSantri'];
                                                    }
                                                    if (!empty($santri['ProvinsiSantri'])) {
                                                        $alamatParts[] = $santri['ProvinsiSantri'];
                                                    }
                                                    $alamatLengkap = !empty($alamatParts) ? implode(', ', $alamatParts) : '-';

                                                    // Format No HP / Email
                                                    $noHpEmail = [];
                                                    if (!empty($santri['NoHpSantri'])) {
                                                        $noHpEmail[] = $santri['NoHpSantri'];
                                                    }
                                                    if (!empty($santri['EmailSantri'])) {
                                                        $noHpEmail[] = $santri['EmailSantri'];
                                                    }
                                                    $noHpEmailStr = !empty($noHpEmail) ? implode(' / ', $noHpEmail) : '-';
                                                ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td>
                                                            <a href="<?= base_url('backend/santri/generatePDFprofilSantriRaport/' . $santri['IdSantri']); ?>" target="_blank" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-print"></i><span class="d-none d-md-inline">&nbsp;Print</span>
                                                            </a>
                                                        </td>
                                                        <td style="white-space: nowrap;"><?= esc($santri['IdSantri'] ?? '-') ?></td>
                                                        <td style="min-width: 150px;">
                                                            <strong><?= ucwords(strtolower($santri['NamaSantri'] ?? '-')); ?></strong>
                                                        </td>
                                                        <td style="white-space: nowrap;"><?= esc($santri['NikSantri'] ?? '-') ?></td>
                                                        <td style="white-space: nowrap;"><?= esc($santri['JenisKelamin'] ?? '-') ?></td>
                                                        <td style="min-width: 120px;"><?= esc($tempatTglLahir ?: '-') ?></td>
                                                        <td style="min-width: 200px; max-width: 250px; word-wrap: break-word;"><?= esc($alamatLengkap) ?></td>
                                                        <td style="min-width: 120px;"><?= esc($noHpEmailStr) ?></td>
                                                        <td style="min-width: 120px;"><?= esc($santri['NamaAyah'] ?? '-') ?></td>
                                                        <td style="min-width: 120px;"><?= esc($santri['NamaIbu'] ?? '-') ?></td>
                                                        <td style="min-width: 100px;"><?= esc($santri['PekerjaanUtamaAyah'] ?? '-') ?></td>
                                                        <td style="min-width: 100px;"><?= esc($santri['PekerjaanUtamaIbu'] ?? '-') ?></td>
                                                        <td style="white-space: nowrap;"><?= esc($santri['NoHpAyah'] ?? '-') ?></td>
                                                        <td style="white-space: nowrap;"><?= esc($santri['NoHpIbu'] ?? '-') ?></td>
                                                        <td style="min-width: 120px;"><?= esc($santri['NamaTpq'] ?? '-') ?></td>
                                                        <td style="white-space: nowrap;"><?= esc($santri['NamaKelas'] ?? '-') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tidak ada data santri untuk ditampilkan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // Initialize DataTable untuk setiap kelas
    <?php if (!empty($dataKelasObject)): ?>
        <?php foreach ($dataKelasObject as $kelas): ?>
            initializeDataTableUmum("#tableSantri-<?= $kelas->IdKelas ?>", true, true);
        <?php endforeach; ?>
    <?php endif; ?>

    // Simpan tab aktif ke localStorage saat tab diklik
    $('#kelasTab a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const targetTab = $(e.target).attr('href'); // e.g., #kelas-123
        const storageKey = 'profil_santri_active_tab_<?= $currentIdTpq ?>';
        localStorage.setItem(storageKey, targetTab);
    });

    // Pulihkan tab aktif dari localStorage saat halaman dimuat
    const storageKey = 'profil_santri_active_tab_<?= $currentIdTpq ?>';
    const savedTab = localStorage.getItem(storageKey);
    if (savedTab) {
        // Cek apakah tab yang disimpan masih ada di halaman
        const tabExists = $(savedTab).length > 0;
        if (tabExists) {
            // Aktifkan tab yang disimpan
            const tabLink = $('#kelasTab a[href="' + savedTab + '"]');
            if (tabLink.length > 0) {
                tabLink.tab('show');
            }
        }
    }

    // Handle tanda tangan bulk kepala sekolah untuk profil santri
    $(document).on('click', '.btn-ttd-bulk-kepsek-profil', function() {
        const IdTpq = $(this).data('tpq');
        const IdKelas = $(this).data('kelas');
        const btn = $(this);

        // Hitung jumlah santri dari tabel kelas yang sesuai
        const tableId = '#tableSantri-' + IdKelas;
        const jumlahSantri = $(tableId + ' tbody tr').length;

        Swal.fire({
            title: 'Konfirmasi Tanda Tangan Kepala Sekolah',
            html: '<div class="text-left">' +
                '<p><strong>Jenis Dokumen:</strong> Profil Santri</p>' +
                '<p><strong>Jumlah Profil:</strong> ' + jumlahSantri + ' profil</p>' +
                '<p class="mt-3">Apakah Anda yakin ingin menandatangani semua profil santri dalam filter ini sebagai Kepala Sekolah?</p>' +
                '</div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Tandatangani',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('<?= base_url('backend/santri/ttdBulkKepsekProfil') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            filterIdTpq: IdTpq,
                            filterIdKelas: IdKelas
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses tanda tangan',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    });

    // Handle cancel tanda tangan bulk kepala sekolah untuk profil santri
    $(document).on('click', '.btn-cancel-ttd-kepsek-profil', function() {
        const IdTpq = $(this).data('tpq');
        const IdKelas = $(this).data('kelas');
        const btn = $(this);

        // Hitung jumlah santri dari tabel kelas yang sesuai
        const tableId = '#tableSantri-' + IdKelas;
        const jumlahSantri = $(tableId + ' tbody tr').length;

        Swal.fire({
            title: 'Konfirmasi Batalkan Tanda Tangan Kepala Sekolah',
            html: '<div class="text-left">' +
                '<p><strong>Jenis Dokumen:</strong> Profil Santri</p>' +
                '<p><strong>Jumlah Profil:</strong> ' + jumlahSantri + ' profil</p>' +
                '<p class="mt-3 text-danger"><strong>Peringatan:</strong> Tanda tangan yang sudah dibuat akan dihapus dari database dan tidak dapat dikembalikan.</p>' +
                '<p class="mt-2">Apakah Anda yakin ingin membatalkan semua tanda tangan kepala sekolah untuk profil santri ini?</p>' +
                '</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('<?= base_url('backend/santri/cancelBulkKepsekProfil') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            filterIdTpq: IdTpq,
                            filterIdKelas: IdKelas
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses pembatalan tanda tangan',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    });

    // Handle print all profil per kelas
    $(document).on('click', '.btn-print-all-profil', function() {
        const IdTpq = $(this).data('tpq');
        const IdKelas = $(this).data('kelas');

        // Hitung jumlah santri dari tabel kelas yang sesuai
        const tableId = '#tableSantri-' + IdKelas;
        const jumlahSantri = $(tableId + ' tbody tr').length;

        if (jumlahSantri === 0) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Tidak ada profil santri untuk dicetak',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Build URL dengan parameter filter
        let url = '<?= base_url('backend/santri/generatePDFAllProfilSantri') ?>?';
        url += 'filterIdTpq=' + encodeURIComponent(IdTpq) + '&';
        url += 'filterIdKelas=' + encodeURIComponent(IdKelas);

        // Show loading indicator
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang membuat PDF untuk semua profil santri kelas ini',
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
    });
</script>
<?= $this->endSection(); ?>