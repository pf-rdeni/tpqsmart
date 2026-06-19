<div class="row justify-content-center py-3">
    <div class="col-md-8">
        <div class="card card-outline card-teal shadow-xs">
            <div class="card-header py-2">
                <h6 class="card-title font-weight-bold text-teal mb-0"><i class="fas fa-globe mr-1"></i> Pengaturan Publikasi Hasil Survey</h6>
            </div>
            <form id="public-settings-form" action="<?= base_url("backend/survey/results/save-public-settings/{$survey['id']}") ?>" method="POST">
                <div class="card-body">
                    <p class="text-muted small">Anda dapat membagikan hasil ringkasan survey ini secara transparan kepada publik (misal: untuk saling memantau siapa saja yang sudah atau belum mengisi survey).</p>
                    
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="bk_public_result_enabled" name="public_result_enabled" <?= $survey['public_result_enabled'] == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold cursor-pointer" for="bk_public_result_enabled">Aktifkan Halaman Hasil Publik</label>
                    </div>

                    <div id="bk-public-result-mode-group" style="display: <?= $survey['public_result_enabled'] == 1 ? 'block' : 'none' ?>;">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Link Akses Hasil Publik:</label>
                            <div class="input-group input-group-sm w-75">
                                <input type="text" class="form-control bg-light" id="bk-result-link-val" value="<?= $result_url ?>" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" id="bk-copy-result-btn" title="Salin Link">
                                        <i class="far fa-copy"></i> Salin Link
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Mode Tampilan Hasil:</label>
                            <div class="custom-control custom-radio mb-2">
                                <input class="custom-control-input" type="radio" id="bk-mode-summary" name="public_result_mode" value="summary" <?= $survey['public_result_mode'] === 'summary' ? 'checked' : '' ?>>
                                <label for="bk-mode-summary" class="custom-control-label font-weight-normal cursor-pointer">
                                    <strong>Ringkasan Pengisian (Summary)</strong><br>
                                    <span class="text-muted small">Pengunjung hanya bisa melihat nama/TPQ yang **Sudah** dan **Belum** mengisi. Isi pilihan jawaban disembunyikan. Cocok untuk memantau kedisiplinan pengisian.</span>
                                </label>
                            </div>
                            <div class="custom-control custom-radio mt-2">
                                <input class="custom-control-input" type="radio" id="bk-mode-detail" name="public_result_mode" value="detail" <?= $survey['public_result_mode'] === 'detail' ? 'checked' : '' ?>>
                                <label for="bk-mode-detail" class="custom-control-label font-weight-normal cursor-pointer">
                                    <strong>Detail Grafik & Jawaban (Detail)</strong><br>
                                    <span class="text-muted small">Menampilkan chart ringkasan pilihan jawaban beserta daftar pengisian (Sudah/Belum).</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-end py-2">
                    <button type="submit" class="btn btn-teal btn-sm text-white font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Publikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle details settings container
    $('#bk_public_result_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#bk-public-result-mode-group').slideDown();
        } else {
            $('#bk-public-result-mode-group').slideUp();
        }
    });

    // Copy link
    $('#bk-copy-result-btn').on('click', function() {
        const input = document.getElementById('bk-result-link-val');
        input.select();
        document.execCommand('copy');
        toastr.success('Link hasil publik disalin ke clipboard.');
    });

    // Save Settings Submit Handler
    $('#public-settings-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Publikasi');
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Gagal menyimpan.');
                }
            },
            error: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Publikasi');
                toastr.error('Terjadi kesalahan koneksi.');
            }
        });
    });
});
</script>
