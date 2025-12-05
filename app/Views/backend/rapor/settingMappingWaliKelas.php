<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $page_title ?></h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/rapor/Ganjil') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Rapor
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (!$mappingEnabled): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Fitur mapping wali kelas belum diaktifkan. Silakan hubungi admin/operator untuk mengaktifkan setting <strong>MappingWaliKelas</strong> di Tools.
                </div>
            <?php else: ?>
                <!-- Filter Kelas -->
                <?php if (!empty($listKelas) && count($listKelas) > 1): ?>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Pilih Kelas:</label>
                            <select class="form-control" id="filterKelas" onchange="window.location.href='<?= base_url('backend/rapor/settingMappingWaliKelas') ?>/' + this.value">
                                <?php foreach ($listKelas as $kelas): ?>
                                    <option value="<?= $kelas['IdKelas'] ?>" <?= $IdKelas == $kelas['IdKelas'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kelas['NamaKelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($santriList)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Tidak ada santri di kelas ini.
                    </div>
                <?php else: ?>
                    <!-- Informasi Proses Flow -->
                    <div class="card card-info card-outline collapsed-card mb-3">
                        <div class="card-header bg-info">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Informasi Proses Mapping Wali Kelas
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><i class="fas fa-list-ol"></i> Alur Proses:</h5>
                                    <ol class="mb-3">
                                        <li class="mb-2">
                                            <strong>Pilih Wali Kelas untuk Setiap Santri</strong>
                                            <ul class="mt-1">
                                                <li>Setiap santri dapat dipilihkan wali kelas dari daftar guru yang mengajar di kelas yang sama</li>
                                                <li>Dropdown menampilkan semua guru (Wali Kelas, Guru Pendamping, dan guru lainnya) yang mengajar di kelas ini</li>
                                                <li>Nama guru ditampilkan dengan format yang konsisten (ucwords)</li>
                                            </ul>
                                        </li>
                                        <li class="mb-2">
                                            <strong>Default Mapping</strong>
                                            <ul class="mt-1">
                                                <li>Jika santri belum memiliki mapping di database, secara otomatis akan menggunakan <strong>Wali Kelas asli</strong> sebagai default</li>
                                                <li>Default ini akan terlihat di dropdown saat halaman pertama kali dimuat</li>
                                                <li>Anda dapat mengubah default ini dengan memilih guru lain dari dropdown</li>
                                            </ul>
                                        </li>
                                        <li class="mb-2">
                                            <strong>Menyimpan Mapping</strong>
                                            <ul class="mt-1">
                                                <li>Klik tombol <strong>"Simpan Semua Mapping"</strong> untuk menyimpan semua perubahan sekaligus</li>
                                                <li>Sistem akan melakukan <strong>mass update</strong> (bukan loop satu per satu) untuk efisiensi</li>
                                                <li>Mapping yang dipilih akan di-<strong>insert</strong> (jika baru) atau di-<strong>update</strong> (jika sudah ada)</li>
                                                <li>Jika dropdown dikosongkan (tidak dipilih), mapping akan di-<strong>delete</strong> dari database</li>
                                            </ul>
                                        </li>
                                        <li class="mb-2">
                                            <strong>Reset Mapping</strong>
                                            <ul class="mt-1">
                                                <li>Tombol <strong>"Reset"</strong> akan mengosongkan semua dropdown</li>
                                                <li>Setelah reset, jangan lupa klik <strong>"Simpan"</strong> untuk menyimpan perubahan</li>
                                                <li>Jika tidak disimpan, perubahan akan hilang saat halaman di-refresh</li>
                                            </ul>
                                        </li>
                                        <li class="mb-2">
                                            <strong>Penggunaan di Rapor</strong>
                                            <ul class="mt-1">
                                                <li>Nama wali kelas yang muncul di rapor akan sesuai dengan mapping yang telah disimpan</li>
                                                <li>Jika tidak ada mapping, sistem akan menggunakan wali kelas asli</li>
                                                <li>Fitur ini hanya mempengaruhi <strong>tampilan nama</strong> di rapor, tidak mempengaruhi fungsi tanda tangan QR code</li>
                                                <li>Tanda tangan QR code tetap hanya bisa dilakukan oleh <strong>Wali Kelas asli</strong></li>
                                            </ul>
                                        </li>
                                    </ol>

                                    <div class="alert alert-warning mb-0">
                                        <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Penting:</h5>
                                        <ul class="mb-0">
                                            <li>Mapping ini bersifat <strong>per tahun ajaran</strong>, jadi perlu dikonfigurasi ulang setiap tahun ajaran baru</li>
                                            <li>Hanya <strong>Wali Kelas asli</strong> yang dapat melakukan mapping untuk kelasnya</li>
                                            <li>Fitur ini hanya aktif jika setting <strong>MappingWaliKelas</strong> diaktifkan oleh Admin/Operator di Tools</li>
                                            <li>Semua perubahan disimpan dalam <strong>satu transaksi database</strong> untuk memastikan konsistensi data</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 30%;">Nama Santri</th>
                                    <th style="width: 65%;">Wali Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($santriList as $index => $santri): ?>
                                    <?php
                                    $santriArray = is_object($santri) ? (array)$santri : $santri;
                                    $currentMapping = $mappingData[$santriArray['IdSantri']] ?? null;
                                    // Jika tidak ada mapping di database, default ke Wali Kelas asli
                                    if (empty($currentMapping) && !empty($waliKelasIdGuru)) {
                                        $currentMapping = $waliKelasIdGuru;
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($santriArray['NamaSantri'] ?? '') ?></strong>
                                        </td>
                                        <td>
                                            <select class="form-control select-mapping select2-mapping"
                                                data-santri="<?= $santriArray['IdSantri'] ?>"
                                                data-kelas="<?= $IdKelas ?>"
                                                style="width: 100%;">
                                                <option value="">-- Pilih Wali Kelas --</option>
                                                <?php
                                                // Array warna untuk guru pendamping (dinamis)
                                                $guruColors = [
                                                    ['bg' => '#fff3cd', 'text' => '#856404', 'emoji' => '🟡'], // Kuning
                                                    ['bg' => '#d1ecf1', 'text' => '#0c5460', 'emoji' => '🔵'], // Biru muda
                                                    ['bg' => '#f8d7da', 'text' => '#721c24', 'emoji' => '🔴'], // Merah muda
                                                    ['bg' => '#e2e3e5', 'text' => '#383d41', 'emoji' => '⚪'], // Abu-abu
                                                    ['bg' => '#d4edda', 'text' => '#155724', 'emoji' => '🟢'], // Hijau muda (bukan wali kelas)
                                                    ['bg' => '#fff4e6', 'text' => '#cc6600', 'emoji' => '🟠'], // Oranye
                                                    ['bg' => '#e7d4f8', 'text' => '#6f42c1', 'emoji' => '🟣'], // Ungu
                                                    ['bg' => '#ffe6f0', 'text' => '#c2185b', 'emoji' => '🌸'], // Pink
                                                ];

                                                $guruIndex = 0; // Index untuk guru pendamping (bukan wali kelas)
                                                foreach ($guruPendampingList as $guru): ?>
                                                    <?php if (!empty($guru['IdGuru'])): ?>
                                                        <?php
                                                        $isWaliKelas = $guru['IsWaliKelas'] ?? false;
                                                        $namaJabatan = $guru['NamaJabatan'] ?? '';
                                                        $namaGuru = $guru['Nama'] ?? '';
                                                        $idGuru = $guru['IdGuru'] ?? null;

                                                        // Cek apakah ini adalah Wali Kelas Utama (asli)
                                                        $isWaliKelasUtama = (!empty($waliKelasIdGuru) && $idGuru == $waliKelasIdGuru);

                                                        // Tambahkan badge/icon langsung di text untuk visual yang jelas
                                                        // Untuk Wali Kelas Utama: tampilkan nama dengan emoji hijau dan label "Wali Kelas Utama"
                                                        // Untuk Wali Kelas lainnya: tampilkan nama dengan emoji hijau
                                                        // Untuk Guru Pendamping: tampilkan nama dengan emoji dan warna dinamis
                                                        if ($isWaliKelas) {
                                                            if ($isWaliKelasUtama) {
                                                                $displayText = '🟢 ' . $namaGuru . ' (Wali Kelas Utama)';
                                                            } else {
                                                                $displayText = '🟢 ' . $namaGuru;
                                                            }
                                                            $optionClass = 'wali-kelas-option' . ($isWaliKelasUtama ? ' wali-kelas-utama' : '');
                                                            $bgColor = '#d4edda';
                                                            $textColor = '#155724';
                                                            $emoji = '🟢';
                                                        } else if ($namaJabatan) {
                                                            // Ambil warna berdasarkan index guru pendamping
                                                            $colorIndex = $guruIndex % count($guruColors);
                                                            $color = $guruColors[$colorIndex];
                                                            // Hilangkan keterangan jabatan untuk guru pendamping (hanya tampilkan nama dengan emoji)
                                                            $displayText = $color['emoji'] . ' ' . $namaGuru;
                                                            $optionClass = 'guru-pendamping-option guru-index-' . $guruIndex;
                                                            $bgColor = $color['bg'];
                                                            $textColor = $color['text'];
                                                            $emoji = $color['emoji'];
                                                            $guruIndex++; // Increment index untuk guru berikutnya
                                                        } else {
                                                            $displayText = $namaGuru;
                                                            $optionClass = '';
                                                            $bgColor = '';
                                                            $textColor = '';
                                                            $emoji = '';
                                                        }
                                                        ?>
                                                        <option value="<?= $guru['IdGuru'] ?>"
                                                            class="<?= $optionClass ?>"
                                                            data-is-wali-kelas="<?= $isWaliKelas ? '1' : '0' ?>"
                                                            data-is-wali-kelas-utama="<?= $isWaliKelasUtama ? '1' : '0' ?>"
                                                            data-jabatan="<?= htmlspecialchars($namaJabatan) ?>"
                                                            data-guru-index="<?= $isWaliKelas ? -1 : ($guruIndex - 1) ?>"
                                                            style="<?= !empty($bgColor) ? 'background-color: ' . $bgColor . ' !important; color: ' . $textColor . ' !important;' . ($isWaliKelas ? ' font-weight: 600;' : '') : '' ?>"
                                                            <?= $currentMapping == $guru['IdGuru'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($displayText) ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="btnSaveMapping">
                            <i class="fas fa-save"></i> Simpan Semua Mapping
                        </button>
                        <button type="button" class="btn btn-secondary" id="btnResetMapping">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Styling untuk dropdown option berdasarkan jabatan - dengan inline style untuk memastikan ter-apply */
    .select-mapping option.wali-kelas-option {
        background-color: #d4edda !important;
        color: #155724 !important;
        font-weight: 600 !important;
        padding: 5px !important;
    }

    /* Styling khusus untuk Wali Kelas Utama */
    .select-mapping option.wali-kelas-utama {
        background-color: #28a745 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    .select-mapping option.guru-pendamping-option {
        padding: 5px !important;
    }

    /* Warna dinamis untuk setiap guru pendamping */
    .select-mapping option.guru-index-0 {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .select-mapping option.guru-index-1 {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
    }

    .select-mapping option.guru-index-2 {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    .select-mapping option.guru-index-3 {
        background-color: #e2e3e5 !important;
        color: #383d41 !important;
    }

    .select-mapping option.guru-index-4 {
        background-color: #d4edda !important;
        color: #155724 !important;
    }

    .select-mapping option.guru-index-5 {
        background-color: #fff4e6 !important;
        color: #cc6600 !important;
    }

    .select-mapping option.guru-index-6 {
        background-color: #e7d4f8 !important;
        color: #6f42c1 !important;
    }

    .select-mapping option.guru-index-7 {
        background-color: #ffe6f0 !important;
        color: #c2185b !important;
    }

    /* Styling untuk Select2 jika digunakan */
    .select2-results__option[data-is-wali-kelas="1"],
    .select2-results__option.wali-kelas-option {
        background-color: #d4edda !important;
        color: #155724 !important;
        font-weight: 600 !important;
    }

    /* Styling khusus untuk Wali Kelas Utama di Select2 */
    .select2-results__option[data-is-wali-kelas-utama="1"],
    .select2-results__option.wali-kelas-utama {
        background-color: #28a745 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    /* Warna dinamis untuk Select2 guru pendamping */
    .select2-results__option[data-guru-index="0"] {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .select2-results__option[data-guru-index="1"] {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
    }

    .select2-results__option[data-guru-index="2"] {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    .select2-results__option[data-guru-index="3"] {
        background-color: #e2e3e5 !important;
        color: #383d41 !important;
    }

    .select2-results__option[data-guru-index="4"] {
        background-color: #d4edda !important;
        color: #155724 !important;
    }

    .select2-results__option[data-guru-index="5"] {
        background-color: #fff4e6 !important;
        color: #cc6600 !important;
    }

    .select2-results__option[data-guru-index="6"] {
        background-color: #e7d4f8 !important;
        color: #6f42c1 !important;
    }

    .select2-results__option[data-guru-index="7"] {
        background-color: #ffe6f0 !important;
        color: #c2185b !important;
    }

    /* Styling untuk select yang dipilih - highlight Wali Kelas */
    .select-mapping option.wali-kelas-option:checked {
        background: linear-gradient(#d4edda, #c3e6cb) !important;
        color: #155724 !important;
    }

    .select-mapping option.wali-kelas-utama:checked {
        background: linear-gradient(#28a745, #218838) !important;
        color: #ffffff !important;
    }

    /* Gradient untuk guru pendamping yang dipilih (akan di-override oleh inline style) */
    .select-mapping option.guru-pendamping-option:checked {
        font-weight: 600 !important;
    }

    /* Styling untuk select yang dipilih */
    .select-mapping:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Styling untuk Select2 container */
    .select2-container--bootstrap4 .select2-results__option[data-is-wali-kelas="1"] {
        background-color: #d4edda !important;
        color: #155724 !important;
        font-weight: 600 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-is-wali-kelas-utama="1"] {
        background-color: #28a745 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    /* Warna dinamis untuk Select2 container guru pendamping */
    .select2-container--bootstrap4 .select2-results__option[data-guru-index="0"] {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-guru-index="1"] {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-guru-index="2"] {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-guru-index="3"] {
        background-color: #e2e3e5 !important;
        color: #383d41 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-guru-index="4"] {
        background-color: #d4edda !important;
        color: #155724 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-guru-index="5"] {
        background-color: #fff4e6 !important;
        color: #cc6600 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-guru-index="6"] {
        background-color: #e7d4f8 !important;
        color: #6f42c1 !important;
    }

    .select2-container--bootstrap4 .select2-results__option[data-guru-index="7"] {
        background-color: #ffe6f0 !important;
        color: #c2185b !important;
    }

    /* Highlight saat hover */
    .select2-results__option[data-is-wali-kelas="1"]:hover {
        background-color: #c3e6cb !important;
    }

    /* Hover effect untuk guru pendamping berdasarkan index */
    .select2-results__option[data-guru-index="0"]:hover {
        background-color: #ffeaa7 !important;
    }

    .select2-results__option[data-guru-index="1"]:hover {
        background-color: #bee5eb !important;
    }

    .select2-results__option[data-guru-index="2"]:hover {
        background-color: #f5c6cb !important;
    }

    .select2-results__option[data-guru-index="3"]:hover {
        background-color: #d6d8db !important;
    }

    .select2-results__option[data-guru-index="4"]:hover {
        background-color: #c3e6cb !important;
    }

    .select2-results__option[data-guru-index="5"]:hover {
        background-color: #ffe0b3 !important;
    }

    .select2-results__option[data-guru-index="6"]:hover {
        background-color: #d4b3f0 !important;
    }

    .select2-results__option[data-guru-index="7"]:hover {
        background-color: #ffb3d9 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function() {
        // Pastikan jQuery sudah ter-load
        function initMappingScript() {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded! Retrying...');
                setTimeout(initMappingScript, 100);
                return;
            }

            console.log('jQuery version:', jQuery.fn.jquery);

            // Gunakan jQuery dengan aman
            jQuery(document).ready(function($) {
                console.log('Initializing mapping script...');
                console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');
                console.log('Found select elements:', $('.select-mapping').length);

                // Array warna untuk guru pendamping (harus sama dengan PHP)
                const guruColors = [{
                        bg: '#fff3cd',
                        text: '#856404',
                        emoji: '🟡'
                    }, // Kuning
                    {
                        bg: '#d1ecf1',
                        text: '#0c5460',
                        emoji: '🔵'
                    }, // Biru muda
                    {
                        bg: '#f8d7da',
                        text: '#721c24',
                        emoji: '🔴'
                    }, // Merah muda
                    {
                        bg: '#e2e3e5',
                        text: '#383d41',
                        emoji: '⚪'
                    }, // Abu-abu
                    {
                        bg: '#d4edda',
                        text: '#155724',
                        emoji: '🟢'
                    }, // Hijau muda
                    {
                        bg: '#fff4e6',
                        text: '#cc6600',
                        emoji: '🟠'
                    }, // Oranye
                    {
                        bg: '#e7d4f8',
                        text: '#6f42c1',
                        emoji: '🟣'
                    }, // Ungu
                    {
                        bg: '#ffe6f0',
                        text: '#c2185b',
                        emoji: '🌸'
                    }, // Pink
                ];

                // Fungsi untuk format option dengan styling dinamis
                function formatOption(option) {
                    if (!option.id) {
                        return option.text;
                    }

                    const $option = $(option.element);
                    const isWaliKelas = $option.data('is-wali-kelas') === '1';
                    const isWaliKelasUtama = $option.data('is-wali-kelas-utama') === '1';
                    const jabatan = $option.data('jabatan') || '';
                    const guruIndex = parseInt($option.data('guru-index')) || -1;
                    const text = option.text;

                    // Text sudah mengandung emoji dan label, langsung return dengan styling
                    let $result = $('<span>' + text + '</span>');

                    if (isWaliKelas) {
                        if (isWaliKelasUtama) {
                            $result.css({
                                'color': '#ffffff',
                                'font-weight': '700'
                            });
                        } else {
                            $result.css({
                                'color': '#155724',
                                'font-weight': '600'
                            });
                        }
                    } else if (jabatan && guruIndex >= 0) {
                        // Gunakan warna dinamis berdasarkan index
                        const colorIndex = guruIndex % guruColors.length;
                        const color = guruColors[colorIndex];
                        $result.css({
                            'color': color.text
                        });
                    }

                    return $result;
                }

                // Inisialisasi Select2 jika tersedia
                if ($.fn.select2) {
                    $('.select2-mapping').select2({
                        theme: 'bootstrap4',
                        templateResult: formatOption,
                        templateSelection: formatOption,
                        width: '100%',
                        escapeMarkup: function(markup) {
                            return markup; // Allow HTML/emoji
                        }
                    });

                    // Tambahkan class dan style ke Select2 results untuk styling dinamis
                    $('.select2-mapping').on('select2:open', function() {
                        setTimeout(function() {
                            $('.select2-results__option').each(function() {
                                const $option = $(this);
                                const isWaliKelas = $option.data('is-wali-kelas') === '1';
                                const isWaliKelasUtama = $option.data('is-wali-kelas-utama') === '1';
                                const guruIndex = parseInt($option.data('guru-index')) || -1;

                                if (isWaliKelas) {
                                    $option.addClass('wali-kelas-option');
                                    if (isWaliKelasUtama) {
                                        $option.addClass('wali-kelas-utama');
                                        $option.css({
                                            'background-color': '#28a745 !important',
                                            'color': '#ffffff !important',
                                            'font-weight': '700 !important'
                                        });
                                    }
                                } else if ($option.data('jabatan') && guruIndex >= 0) {
                                    $option.addClass('guru-pendamping-option');
                                    // Apply warna dinamis berdasarkan index
                                    const colorIndex = guruIndex % guruColors.length;
                                    const color = guruColors[colorIndex];
                                    $option.css({
                                        'background-color': color.bg + ' !important',
                                        'color': color.text + ' !important'
                                    });
                                }
                            });
                        }, 10);
                    });
                }

                // Fallback: Pastikan inline style ter-apply untuk dropdown standar
                $('.select-mapping option').each(function() {
                    const $option = $(this);
                    const isWaliKelas = $option.data('is-wali-kelas') === '1';
                    const jabatan = $option.data('jabatan') || '';
                    const guruIndex = parseInt($option.data('guru-index')) || -1;

                    // Style sudah di-set inline di HTML, tapi pastikan class juga ada
                    if (isWaliKelas) {
                        const isWaliKelasUtama = $option.data('is-wali-kelas-utama') === '1';
                        $option.addClass('wali-kelas-option');
                        if (isWaliKelasUtama) {
                            $option.addClass('wali-kelas-utama');
                            // Force apply style untuk Wali Kelas Utama
                            $option.attr('style', 'background-color: #28a745 !important; color: #ffffff !important; font-weight: 700;');
                            console.log('Applied Wali Kelas Utama style to:', $option.text());
                        } else {
                            // Force apply style untuk Wali Kelas biasa
                            $option.attr('style', 'background-color: #d4edda !important; color: #155724 !important; font-weight: 600;');
                            console.log('Applied Wali Kelas style to:', $option.text());
                        }
                    } else if (jabatan && guruIndex >= 0) {
                        $option.addClass('guru-pendamping-option');
                        // Ambil warna berdasarkan index
                        const colorIndex = guruIndex % guruColors.length;
                        const color = guruColors[colorIndex];
                        // Force apply style untuk memastikan terlihat
                        $option.attr('style', 'background-color: ' + color.bg + ' !important; color: ' + color.text + ' !important;');
                        console.log('Applied Guru Pendamping style (index ' + guruIndex + ') to:', $option.text());
                    }
                });

                console.log('Mapping script initialized successfully');
                // Simpan mapping
                $('#btnSaveMapping').on('click', function() {
                    const mappings = [];
                    const IdKelas = $('.select-mapping').first().data('kelas');

                    // Kumpulkan semua mapping dari semua select
                    $('.select-mapping').each(function() {
                        const IdSantri = $(this).data('santri');
                        const IdGuru = $(this).val();

                        // Masukkan semua mapping, termasuk yang kosong (untuk reset)
                        mappings.push({
                            IdSantri: IdSantri,
                            IdGuru: IdGuru || null // null jika tidak dipilih
                        });
                    });

                    // Simpan semua mapping dalam satu request
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Siapkan CSRF token
                    const csrfName = '<?= csrf_token() ?>';
                    const csrfHash = '<?= csrf_hash() ?>';

                    // Kirim semua mapping dalam satu request
                    $.ajax({
                        url: '<?= base_url('backend/rapor/saveMappingWaliKelas') ?>',
                        type: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: {
                            mappings: mappings,
                            IdKelas: IdKelas,
                            [csrfName]: csrfHash
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Semua mapping berhasil disimpan.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Terjadi kesalahan saat menyimpan mapping.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            console.error('AJAX Error:', {
                                status: status,
                                error: error,
                                responseText: xhr.responseText,
                                statusCode: xhr.status
                            });

                            let errorMessage = 'Terjadi kesalahan saat menyimpan mapping.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 403) {
                                errorMessage = 'Akses ditolak. Pastikan Anda memiliki permission untuk melakukan mapping.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Terjadi kesalahan server. Silakan coba lagi atau hubungi administrator.';
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                // Reset mapping
                $('#btnResetMapping').on('click', function() {
                    Swal.fire({
                        title: 'Reset Mapping?',
                        text: 'Apakah Anda yakin ingin mereset semua mapping?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Reset',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reset semua select ke default
                            $('.select-mapping').val('');
                            // Jika menggunakan Select2, trigger change
                            if ($.fn.select2) {
                                $('.select2-mapping').trigger('change');
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Mapping telah direset. Jangan lupa klik Simpan untuk menyimpan perubahan.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
            }); // End jQuery(document).ready
        } // End initMappingScript

        // Jalankan setelah DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMappingScript);
        } else {
            initMappingScript();
        }
    })(); // End IIFE
</script>
<?= $this->endSection() ?>