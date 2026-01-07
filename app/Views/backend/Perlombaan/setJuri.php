<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Row 1: Pilih Kegiatan & Perlombaan -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Pilih Kegiatan & Perlombaan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="history.back()" title="Kembali ke halaman sebelumnya">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kegiatan</label>
                                    <select class="form-control" id="selectLomba">
                                        <option value="">-- Pilih Kegiatan --</option>
                                        <?php foreach ($lomba_list as $l): ?>
                                            <?php 
                                                $tpqLabel = !empty($l['NamaTpq']) 
                                                    ? ' - TPQ ' . $l['NamaTpq'] . ' - ' . ($l['KelurahanDesa'] ?? '')
                                                    : ' - Umum';
                                            ?>
                                            <option value="<?= $l['id'] ?>" 
                                                data-idtpq="<?= $l['IdTpq'] ?? '' ?>"
                                                data-namatpq="<?= esc($l['NamaTpq'] ?? '') ?>"
                                                <?= ($lomba && $lomba['id'] == $l['id']) ? 'selected' : '' ?>>
                                                <?= esc($l['NamaLomba']) ?><?= $tpqLabel ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Perlombaan</label>
                                    <select class="form-control" id="selectCabang" <?= !$cabang ? 'disabled' : '' ?>>
                                        <option value="">-- Pilih Perlombaan --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Tambah Juri -->
        <?php if ($cabang): ?>
        <?php 
            // Auto-detect tipe juri dari lomba
            $lombaIdTpq = $lomba['IdTpq'] ?? null;
            $isLombaFromTpq = !empty($lombaIdTpq);
        ?>
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus"></i> Tambah Juri
                            <?php if ($isLombaFromTpq): ?>
                                <span class="badge badge-primary ml-2">TPQ <?= esc($lomba['NamaTpq'] ?? '') ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary ml-2">Juri Umum</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <form id="formAddJuri">
                        <div class="card-body">
                            <input type="hidden" name="cabang_id" value="<?= $cabang['id'] ?>">
                            <!-- Auto-set tipe juri berdasarkan lomba -->
                            <input type="hidden" name="TipeJuri" id="tipeJuri" value="<?= $isLombaFromTpq ? 'tpq' : 'umum' ?>">
                            <?php if ($isLombaFromTpq): ?>
                                <input type="hidden" name="IdTpq" value="<?= $lombaIdTpq ?>">
                            <?php endif; ?>
                            
                            <?php $isOperatorOnly = in_groups('Operator') && !in_groups('Admin'); ?>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="UsernameJuri" id="inputUsername" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info" id="btnGenerateUsername">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nama Juri</label>
                                        <input type="text" class="form-control" name="NamaJuri" id="inputNamaJuri">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="PasswordJuri" id="inputPassword" placeholder="Kosongkan untuk default">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-secondary" id="btnShowPassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Default: <code>JuriLombaTpqSmart</code></small>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Row 3: Daftar Juri -->
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie"></i> Daftar Juri
                            <?php if ($cabang): ?> - <?= esc($cabang['NamaCabang']) ?><?php endif; ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Juri</th>
                                        <th>Username</th>
                                        <th>Nama</th>
                                        <th>Tipe/TPQ</th>
                                        <th>Kriteria</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($juri_list)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <?= $cabang ? 'Belum ada juri ditugaskan' : 'Pilih cabang terlebih dahulu' ?>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($juri_list as $i => $juri): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><code><?= esc($juri['IdJuri']) ?></code></td>
                                                <td><?= esc($juri['UsernameJuri']) ?></td>
                                                <td><?= esc($juri['NamaJuri'] ?: '-') ?></td>
                                                <td>
                                                    <?php if (!empty($juri['NamaTpq'])): ?>
                                                        <span class="badge badge-primary"><?= esc($juri['NamaTpq']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Umum</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($juri['kriteria_custom'])): ?>
                                                        <span class="badge badge-warning"><?= $juri['kriteria_count'] ?>/<?= $juri['kriteria_total'] ?></span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Semua</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $juri['Status'] === 'Aktif' ? 'success' : 'secondary' ?>">
                                                        <?= $juri['Status'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning btn-edit-juri" 
                                                        data-id="<?= $juri['id'] ?>"
                                                        data-username="<?= esc($juri['UsernameJuri']) ?>"
                                                        data-nama="<?= esc($juri['NamaJuri'] ?? '') ?>"
                                                        data-status="<?= $juri['Status'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-info btn-set-kriteria" 
                                                        data-id="<?= $juri['id'] ?>"
                                                        data-nama="<?= esc($juri['NamaJuri'] ?: $juri['UsernameJuri']) ?>"
                                                        title="Setting Kriteria">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-juri" data-id="<?= $juri['id'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Edit Juri -->
<div class="modal fade" id="modalEditJuri" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Juri</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formEditJuri">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editJuriId">
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="editUsername" readonly>
                        <small class="text-muted">Username tidak dapat diubah</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Juri</label>
                        <input type="text" class="form-control" name="NamaJuri" id="editNamaJuri">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="Status" id="editStatus">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <hr>
                    <h6><i class="fas fa-key"></i> Reset Password</h6>
                    
                    <div class="form-group">
                        <label>Password Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="NewPassword" id="editNewPassword" placeholder="Kosongkan jika tidak ingin reset">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary btn-toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="useDefaultPassword" name="UseDefaultPassword">
                            <label class="custom-control-label" for="useDefaultPassword">
                                Gunakan password default (JuriLombaTpqSmart)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Setting Kriteria Juri -->
<div class="modal fade" id="modalSetKriteria" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title"><i class="fas fa-cog"></i> Setting Kriteria Juri</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formSetKriteria">
                <div class="modal-body">
                    <input type="hidden" name="juri_id" id="kriteriaJuriId">
                    
                    <div class="alert alert-info">
                        <strong>Juri:</strong> <span id="kriteriaJuriNama"></span>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="useDefaultKriteria" name="use_default" value="1">
                            <label class="custom-control-label" for="useDefaultKriteria">
                                <strong>Semua Kriteria (Default)</strong>
                            </label>
                            <small class="form-text text-muted">Juri akan menilai semua kriteria cabang lomba</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div id="kriteriaListContainer">
                        <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat kriteria...</p>
                    </div>
                    
                    <div id="kriteriaInfo" class="mt-3" style="display: none;">
                        <span class="badge badge-info">Dipilih: <span id="selectedCount">0</span> / <span id="totalCount">0</span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk TPQ
    $('#selectTpq').select2({
        placeholder: 'Pilih TPQ...',
        allowClear: true,
        width: '100%'
    });

    // Load daftar TPQ
    loadTpqList();

    <?php if ($cabang): ?>
    // Load cabang for current lomba
    loadCabangByLomba(<?= $cabang['lomba_id'] ?>, <?= $cabang['id'] ?>);
    // Save to localStorage
    localStorage.setItem('setJuri_lombaId', '<?= $cabang['lomba_id'] ?>');
    localStorage.setItem('setJuri_cabangId', '<?= $cabang['id'] ?>');
    
    // Auto-generate username saat halaman dimuat
    setTimeout(function() {
        generateUsername();
    }, 500);
    <?php elseif ($lomba): ?>
    // Load cabang for default lomba    
    loadCabangByLomba(<?= $lomba['id'] ?>);
    <?php else: ?>
    // Restore from localStorage if no selection
    var savedLombaId = localStorage.getItem('setJuri_lombaId');
    if (savedLombaId && $('#selectLomba option[value="' + savedLombaId + '"]').length) {
        $('#selectLomba').val(savedLombaId).trigger('change');
    }
    <?php endif; ?>

    // Toggle TPQ field dan auto-generate username
    $('#tipeJuri').change(function() {
        if ($(this).val() === 'tpq') {
            $('#tpqGroup').slideDown();
        } else {
            $('#tpqGroup').slideUp();
            $('#selectTpq').val('').trigger('change');
        }
        // Generate username saat tipe berubah
        setTimeout(function() {
            generateUsername();
        }, 300);
    });

    // Auto-generate username saat TPQ dipilih
    $('#selectTpq').change(function() {
        generateUsername();
    });

    $('#selectLomba').change(function() {
        var lombaId = $(this).val();
        if (lombaId) {
            localStorage.setItem('setJuri_lombaId', lombaId);
            loadCabangByLomba(lombaId);
        } else {
            localStorage.removeItem('setJuri_lombaId');
            localStorage.removeItem('setJuri_cabangId');
            $('#selectCabang').html('<option value="">-- Pilih Perlombaan --</option>').prop('disabled', true);
        }
    });

    $('#selectCabang').change(function() {
        var cabangId = $(this).val();
        if (cabangId) {
            window.location.href = '<?= base_url('backend/perlombaan/setJuri') ?>/' + cabangId;
        }
    });

    function loadCabangByLomba(lombaId, selectedId = null) {
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getCabangByLomba') ?>/' + lombaId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '<option value="">-- Pilih Perlombaan --</option>';
                    response.data.forEach(function(item) {
                        var selected = (selectedId && item.id == selectedId) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + selected + '>' + (item.DisplayLabel || item.NamaCabang) + '</option>';
                    });
                    $('#selectCabang').html(html).prop('disabled', false);
                }
            }
        });
    }

    function loadTpqList() {
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getTpqList') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var options = '<option value="">-- Pilih TPQ --</option>';
                    
                    var isOperator = <?= (in_groups('Operator') && !in_groups('Admin')) ? 'true' : 'false' ?>;
                    var myIdTpq = '<?= session()->get('IdTpq') ?>';

                    response.data.forEach(function(item) {
                        options += '<option value="' + item.IdTpq + '">' + item.NamaTpq + '</option>';
                    });
                    $('#selectTpq').html(options);

                    if (isOperator && myIdTpq) {
                        // Auto-select and disable for operator
                        $('#selectTpq').val(myIdTpq).trigger('change');
                        $('#selectTpq').prop('disabled', true);
                        
                        // Add hidden input for submission
                        if ($('#hiddenIdTpq').length === 0) {
                            $('#formAddJuri').append('<input type="hidden" id="hiddenIdTpq" name="IdTpq" value="' + myIdTpq + '">');
                        }
                    }
                }
            }
        });
    }

    // Fungsi Generate Username
    function generateUsername() {
        var cabangId = $('input[name="cabang_id"]').val();
        var idTpq = $('#selectTpq').val() || '';

        if (!cabangId) return;

        $.ajax({
            url: '<?= base_url('backend/perlombaan/generateUsernameJuriLomba') ?>',
            type: 'POST',
            data: { cabang_id: cabangId, IdTpq: idTpq },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#inputUsername').val(response.username);
                }
            }
        });
    }

    // Tombol Generate Username
    $('#btnGenerateUsername').click(function() {
        generateUsername();
    });

    // Toggle Password Visibility
    $('#btnShowPassword').click(function() {
        var input = $('#inputPassword');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#formAddJuri').submit(function(e) {
        e.preventDefault();
        
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/perlombaan/storeJuri') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Tambah Juri');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Tambah Juri');
            }
        });
    });

    // Edit Juri - Open Modal
    $('.btn-edit-juri').click(function() {
        var id = $(this).data('id');
        var username = $(this).data('username');
        var nama = $(this).data('nama');
        var status = $(this).data('status');

        $('#editJuriId').val(id);
        $('#editUsername').val(username);
        $('#editNamaJuri').val(nama);
        $('#editStatus').val(status);
        $('#editNewPassword').val('');
        $('#useDefaultPassword').prop('checked', false);

        $('#modalEditJuri').modal('show');
    });

    // Toggle Password in Edit Modal
    $('.btn-toggle-password').click(function() {
        var input = $(this).closest('.input-group').find('input');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Checkbox default password
    $('#useDefaultPassword').change(function() {
        if ($(this).is(':checked')) {
            $('#editNewPassword').val('JuriLombaTpqSmart').prop('readonly', true);
        } else {
            $('#editNewPassword').val('').prop('readonly', false);
        }
    });

    // Submit Edit Form
    $('#formEditJuri').submit(function(e) {
        e.preventDefault();
        
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/perlombaan/updateJuri') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalEditJuri').modal('hide');
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
            }
        });
    });

    $('.btn-delete-juri').click(function() {
        var id = $(this).data('id');
        
        // First, check impact
        $.ajax({
            url: '<?= base_url('backend/perlombaan/checkJuriImpact') ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    Swal.fire('Error', response.message, 'error');
                    return;
                }
                
                var data = response.data;
                var impactHtml = '';
                
                if (data.has_impact) {
                    impactHtml = '<div class="text-left mt-3"><strong>Data yang akan dihapus:</strong><ul class="mt-2">' + data.impact.join('') + '</ul></div>';
                } else {
                    impactHtml = '<p class="text-muted">Tidak ada data terkait yang akan dihapus.</p>';
                }
                
                Swal.fire({
                    title: 'Yakin hapus juri ini?',
                    html: '<p><strong>' + (data.juri.NamaJuri || data.juri.UsernameJuri) + '</strong></p>' + impactHtml,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url('backend/perlombaan/deleteJuri') ?>/' + id,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            },
            error: function() {
                Swal.fire('Error', 'Gagal mengecek data', 'error');
            }
        });
    });

    // ==================== KRITERIA SETTINGS ====================
    
    // Open Kriteria Modal
    $(document).on('click', '.btn-set-kriteria', function() {
        var juriId = $(this).data('id');
        var juriNama = $(this).data('nama');
        
        $('#kriteriaJuriId').val(juriId);
        $('#kriteriaJuriNama').text(juriNama);
        $('#kriteriaListContainer').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat kriteria...</p>');
        $('#kriteriaInfo').hide();
        $('#useDefaultKriteria').prop('checked', false);
        
        // Load kriteria
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getJuriKriteria') ?>/' + juriId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderKriteriaList(response.data);
                } else {
                    $('#kriteriaListContainer').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#kriteriaListContainer').html('<div class="alert alert-danger">Gagal memuat data</div>');
            }
        });
        
        $('#modalSetKriteria').modal('show');
    });
    
    function renderKriteriaList(data) {
        var html = '<div class="list-group">';
        var hasCustom = data.has_custom_setting;
        
        data.kriteria.forEach(function(k) {
            var checked = hasCustom ? (k.assigned ? 'checked' : '') : '';
            var disabled = k.used_by_others && !k.assigned ? 'disabled' : '';
            var labelClass = k.used_by_others && !k.assigned ? 'list-group-item bg-light text-muted' : 'list-group-item';
            
            html += '<label class="' + labelClass + '">';
            html += '<input type="checkbox" class="kriteria-check" name="kriteria_ids[]" value="' + k.id + '" ' + checked + ' ' + disabled + '> ';
            html += '<strong>' + k.NamaKriteria + '</strong> ';
            html += '<span class="badge badge-secondary">' + k.Bobot + '%</span>';
            
            // Tampilkan info juri yang menggunakan kriteria ini
            if (k.used_by_others && !k.assigned) {
                html += ' <span class="badge badge-warning ml-2"><i class="fas fa-user"></i> ' + k.used_by + '</span>';
            }
            
            html += '</label>';
        });
        html += '</div>';
        
        $('#kriteriaListContainer').html(html);
        $('#totalCount').text(data.total_count);
        $('#kriteriaInfo').show();
        
        // Set default checkbox jika tidak ada custom setting
        if (!hasCustom) {
            $('#useDefaultKriteria').prop('checked', true);
            $('.kriteria-check:not(:disabled)').prop('disabled', true);
        }
        
        updateSelectedCount();
    }
    
    function updateSelectedCount() {
        var count = $('.kriteria-check:checked').length;
        $('#selectedCount').text(count);
    }
    
    // Toggle kriteria checkboxes when default is checked
    $('#useDefaultKriteria').change(function() {
        if ($(this).is(':checked')) {
            $('.kriteria-check').prop('disabled', true).prop('checked', false);
        } else {
            $('.kriteria-check').prop('disabled', false);
        }
        updateSelectedCount();
    });
    
    // Update count on checkbox change
    $(document).on('change', '.kriteria-check', function() {
        updateSelectedCount();
        // Uncheck default if any kriteria is manually checked
        if ($('.kriteria-check:checked').length > 0) {
            $('#useDefaultKriteria').prop('checked', false);
        }
    });
    
    // Submit Kriteria Form
    $('#formSetKriteria').submit(function(e) {
        e.preventDefault();
        
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: '<?= base_url('backend/perlombaan/saveJuriKriteria') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalSetKriteria').modal('hide');
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
