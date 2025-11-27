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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Petunjuk:</strong> Pilih wali kelas untuk setiap santri. Jika tidak dipilih, akan menggunakan wali kelas asli.
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
                                            <select class="form-control select-mapping" 
                                                    data-santri="<?= $santriArray['IdSantri'] ?>"
                                                    data-kelas="<?= $IdKelas ?>">
                                                <option value="">-- Pilih Wali Kelas --</option>
                                                <?php foreach ($guruPendampingList as $guru): ?>
                                                    <?php if (!empty($guru['IdGuru'])): ?>
                                                        <option value="<?= $guru['IdGuru'] ?>" 
                                                                <?= $currentMapping == $guru['IdGuru'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($guru['Nama'] ?? '') ?>
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

