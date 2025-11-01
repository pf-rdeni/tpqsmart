<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row mb-2">
                <div class="col-sm-12 float-sm-left">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalDuplicateBobot">
                        <i class="fas fa-copy"></i> Duplikasi Nilai Bobot
                    </button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <?php
                $kategoriList = $kategoriList ?? [];
                if (empty($kategoriList) && !empty($bobot)) {
                    foreach ($bobot as $row) {
                        if (!empty($row['IdKategoriMateri'])) {
                            $kategoriList[$row['IdKategoriMateri']] = $row['NamaKategoriMateri'] ?? $row['IdKategoriMateri'];
                        }
                    }
                }
                ?>
                <table id="tableBobot" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun Ajaran</th>
                            <?php foreach ($kategoriList as $kategoriName): ?>
                                <th><?= esc($kategoriName) ?></th>
                            <?php endforeach; ?>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $groupedBobot = [];
                        foreach ($bobot as $row) {
                            $tahunAjaran = $row['IdTahunAjaran'];
                            if (!isset($groupedBobot[$tahunAjaran])) {
                                $groupedBobot[$tahunAjaran] = [
                                    'tahun' => $tahunAjaran,
                                    'created_at' => $row['created_at'] ?? $row['updated_at'] ?? null,
                                    'data' => []
                                ];
                            }

                            if (empty($groupedBobot[$tahunAjaran]['created_at']) && (!empty($row['created_at']) || !empty($row['updated_at']))) {
                                $groupedBobot[$tahunAjaran]['created_at'] = $row['created_at'] ?? $row['updated_at'];
                            }

                            $kategoriId = $row['IdKategoriMateri'];
                            $groupedBobot[$tahunAjaran]['data'][$kategoriId] = [
                                'nilai' => $row['NilaiBobot'],
                                'nama' => $row['NamaKategoriMateri'] ?? ($kategoriList[$kategoriId] ?? $kategoriId)
                            ];

                            if (!isset($kategoriList[$kategoriId])) {
                                $kategoriList[$kategoriId] = $groupedBobot[$tahunAjaran]['data'][$kategoriId]['nama'];
                            }
                        }

                        foreach ($groupedBobot as $tahun => $group):
                            $createdAt = $group['created_at'] ?? null;
                            $createdAtDisplay = $createdAt ? date('d/m/Y H:i', strtotime($createdAt)) : '-';
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $group['tahun'] ?></strong></td>
                                <?php foreach ($kategoriList as $kategoriId => $kategoriName): ?>
                                    <td class="text-center">
                                        <?= isset($group['data'][$kategoriId]) ? esc($group['data'][$kategoriId]['nilai']) : '-' ?>
                                    </td>
                                <?php endforeach; ?>
                                <td><?= esc($createdAtDisplay) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-warning btn-sm"
                                            onclick="editBobotTahun('<?= $group['tahun'] ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteBobotTahun('<?= $group['tahun'] ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /.card-body -->
</div><!-- /.card -->

<!-- Modal Duplicate Bobot -->
<div class="modal fade" id="modalDuplicateBobot" tabindex="-1" role="dialog" aria-labelledby="modalDuplicateBobotLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDuplicateBobotLabel">Duplikasi Nilai Bobot</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formDuplicateBobot">
                    <div class="form-group">
                        <label for="sourceTahunAjaran">Tahun Ajaran Sumber <span class="text-danger">*</span></label>
                        <select class="form-control" id="sourceTahunAjaran" name="sourceTahunAjaran" required>
                            <option value="">Pilih Tahun Ajaran Sumber</option>
                            <!-- Data akan diisi oleh JavaScript -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="targetTahunAjaran">Tahun Ajaran Baru <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="targetTahunAjaran" name="targetTahunAjaran"
                            placeholder="Masukkan 8 digit angka, contoh: 20252026"
                            maxlength="8" pattern="[0-9]{8}" required>
                        <small class="form-text text-muted">
                            Format: 8 digit angka (contoh: 20252026 untuk tahun ajaran 2025/2026)
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" id="btnSimpanDuplicate">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Bobot -->
<div class="modal fade" id="modalEditBobot" tabindex="-1" role="dialog" aria-labelledby="modalEditBobotLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditBobotLabel">Edit Bobot Nilai Munaqosah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="editIdTahunAjaran">Tahun Ajaran <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editIdTahunAjaran" name="editIdTahunAjaran" readonly>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tableEditBobotForm">
                        <thead class="thead-light">
                            <tr>
                                <th>Kategori Materi Ujian</th>
                                <th>Nilai Bobot (%)</th>
                            </tr>
                        </thead>
                        <tbody id="editBobotFormBody">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success" id="btnUpdateBobot">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Bobot -->
<div class="modal fade" id="modalEditBobot" tabindex="-1" role="dialog" aria-labelledby="modalEditBobotLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditBobotLabel">Edit Bobot Nilai Munaqosah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="editIdTahunAjaran">Tahun Ajaran <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editIdTahunAjaran" name="editIdTahunAjaran" readonly>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tableEditBobotForm">
                        <thead class="thead-light">
                            <tr>
                                <th>Tahun Ajaran</th>
                                <th>Kategori Materi Ujian</th>
                                <th>Nilai Bobot (%)</th>
                            </tr>
                        </thead>
                        <tbody id="editBobotFormBody">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success" id="btnUpdateBobot">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php
$kategoriMaster = [];
foreach ($kategoriList as $kategoriId => $kategoriName) {
    $kategoriMaster[] = [
        'id' => $kategoriId,
        'name' => $kategoriName
    ];
}
?>
<style>
    /* Styling untuk form bobot */
    .bobot-input {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .bobot-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .bobot-input.changed {
        background-color: #ffebee;
        border-color: #f44336;
        color: #d32f2f;
    }

    .bobot-input.changed:focus {
        border-color: #f44336;
        box-shadow: 0 0 0 0.2rem rgba(244, 67, 54, 0.25);
    }

    /* Styling untuk edit form bobot */
    .edit-bobot-input {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .edit-bobot-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .edit-bobot-input.changed {
        background-color: #ffebee;
        border-color: #f44336;
        color: #d32f2f;
    }

    .edit-bobot-input.changed:focus {
        border-color: #f44336;
        box-shadow: 0 0 0 0.2rem rgba(244, 67, 54, 0.25);
    }

    .table td {
        vertical-align: middle;
    }

    /* Badge styling untuk total bobot */
    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .badge-success {
        color: #fff;
        background-color: #28a745;
    }

    .badge-danger {
        color: #fff;
        background-color: #dc3545;
    }

    /* Validasi tahun ajaran */
    .is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-valid:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .btn-group-custom {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-group-custom .btn {
        flex: 1;
        min-width: 120px;
    }
</style>
<script>
    // Data default akan diambil dari database
    let defaultBobotData = [];
    let currentBobotData = [];
    let originalValues = {};
    let isEditMode = false;
    const kategoriMaster = <?= json_encode($kategoriMaster, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    let kategoriData = kategoriMaster.slice();

    const kategoriMap = new Map(kategoriData.map(item => [item.id, item.name]));

    function getKategoriName(id) {
        return kategoriMap.get(id) || id;
    }

    function escapeHtml(text) {
        if (typeof text !== 'string') {
            return text;
        }
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function ensureKategoriDataFromDefault() {
        if (kategoriData.length === 0 && defaultBobotData.length > 0) {
            kategoriData = defaultBobotData.map(item => ({
                id: item.IdKategoriMateri,
                name: item.NamaKategoriMateri || item.IdKategoriMateri
            }));
            kategoriMap.clear();
            kategoriData.forEach(item => kategoriMap.set(item.id, item.name));
        }
    }

    $(document).ready(function() {
        // DataTable initialization
        $('#tableBobot').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "order": [
                [0, "asc"]
            ]
        });

        loadDefaultDataFromDatabase();

        // Load tahun ajaran options saat modal dibuka
        $('#modalDuplicateBobot').on('show.bs.modal', function() {
            loadTahunAjaranOptions();
            $('#targetTahunAjaran').val('');
        });

        // Validasi real-time format tahun ajaran
        $('#targetTahunAjaran').on('input', function() {
            const value = $(this).val();
            const tahunAjaranRegex = /^\d{8}$/;

            // Remove non-numeric characters
            const numericValue = value.replace(/\D/g, '');
            $(this).val(numericValue);

            // Limit to 8 digits
            if (numericValue.length > 8) {
                $(this).val(numericValue.substring(0, 8));
            }

            // Visual feedback
            if (numericValue.length === 8 && tahunAjaranRegex.test(numericValue)) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else if (numericValue.length > 0) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // Button Duplicate
        $('#btnDuplicate').on('click', function() {
            const tahunAjaran = $('#IdTahunAjaran').val();
            if (!tahunAjaran) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Tahun Ajaran harus diisi terlebih dahulu',
                    icon: 'warning'
                });
                return;
            }

            duplicateDefaultData(tahunAjaran);
        });

        // Button Delete
        $('#btnDelete').on('click', function() {
            const tahunAjaran = $('#IdTahunAjaran').val();
            if (!tahunAjaran) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Tahun Ajaran harus diisi terlebih dahulu',
                    icon: 'warning'
                });
                return;
            }

            deleteBobotByTahunAjaran(tahunAjaran);
        });

        // Button Simpan Duplicate
        $('#btnSimpanDuplicate').on('click', function() {
            duplicateBobotData();
        });

        // Button Update Bobot
        $('#btnUpdateBobot').on('click', function() {
            updateBobotData();
        });

        // Form Edit Bobot
        $('#formEditBobot').on('submit', function(e) {
            e.preventDefault();

            var id = $('#editId').val();

            $.ajax({
                url: '<?= base_url('backend/munaqosah/update-bobot/') ?>' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server',
                        icon: 'error'
                    });
                }
            });
        });
    });

    // Fungsi untuk load data default dari database
    function loadDefaultDataFromDatabase() {
        $.ajax({
            url: '<?= base_url('backend/munaqosah/get-default-bobot') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    defaultBobotData = (response.data || []).map(item => ({
                        IdTahunAjaran: item.IdTahunAjaran || 'Default',
                        IdKategoriMateri: item.IdKategoriMateri,
                        NamaKategoriMateri: item.NamaKategoriMateri || getKategoriName(item.IdKategoriMateri),
                        NilaiBobot: parseFloat(item.NilaiBobot) || 0
                    }));

                    ensureKategoriDataFromDefault();
                    loadDefaultData();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Gagal mengambil data default',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server',
                    icon: 'error'
                });
            }
        });
    }

    // Fungsi untuk load tahun ajaran options
    function loadTahunAjaranOptions() {
        $.ajax({
            url: '<?= base_url('backend/munaqosah/get-tahun-ajaran-options') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const select = $('#sourceTahunAjaran');
                    select.empty();
                    select.append('<option value="">Pilih Tahun Ajaran Sumber</option>');

                    response.data.forEach(function(item) {
                        select.append(`<option value="${item.IdTahunAjaran}">${item.IdTahunAjaran}</option>`);
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server',
                    icon: 'error'
                });
            }
        });
    }

    // Fungsi untuk duplicate bobot data
    function duplicateBobotData() {
        const sourceTahunAjaran = $('#sourceTahunAjaran').val();
        const targetTahunAjaran = $('#targetTahunAjaran').val();

        if (!sourceTahunAjaran) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Tahun Ajaran Sumber harus dipilih',
                icon: 'warning'
            });
            return;
        }

        if (!targetTahunAjaran) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Tahun Ajaran Baru harus diisi',
                icon: 'warning'
            });
            return;
        }

        // Validasi format tahun ajaran (harus 8 digit angka)
        const tahunAjaranRegex = /^\d{8}$/;
        if (!tahunAjaranRegex.test(targetTahunAjaran)) {
            Swal.fire({
                title: 'Format Salah!',
                text: 'Tahun Ajaran harus berupa 8 digit angka (contoh: 20252026)',
                icon: 'error'
            });
            return;
        }

        // Validasi tahun ajaran yang masuk akal
        const tahun1 = parseInt(targetTahunAjaran.substring(0, 4));
        const tahun2 = parseInt(targetTahunAjaran.substring(4, 8));
        const currentYear = new Date().getFullYear();

        if (tahun2 !== tahun1 + 1) {
            Swal.fire({
                title: 'Format Salah!',
                text: 'Tahun kedua harus tahun pertama + 1 (contoh: 20252026)',
                icon: 'error'
            });
            return;
        }

        if (tahun1 < 2000 || tahun1 > currentYear + 10) {
            Swal.fire({
                title: 'Tahun Tidak Valid!',
                text: `Tahun ajaran harus antara 2000 dan ${currentYear + 10}`,
                icon: 'error'
            });
            return;
        }

        if (sourceTahunAjaran === targetTahunAjaran) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Tahun Ajaran Baru tidak boleh sama dengan Tahun Ajaran Sumber',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: 'Duplikasi Data?',
            text: `Apakah Anda yakin ingin menduplikasi data dari ${sourceTahunAjaran} ke ${targetTahunAjaran}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Duplikasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/duplicate-bobot-data') ?>',
                    type: 'POST',
                    data: {
                        sourceTahunAjaran: sourceTahunAjaran,
                        targetTahunAjaran: targetTahunAjaran,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                $('#modalDuplicateBobot').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk load data default ke tabel
    function loadDefaultData() {
        const tbody = $('#bobotFormBody');
        tbody.empty();
        currentBobotData = [];
        originalValues = {};
        isEditMode = false;

        ensureKategoriDataFromDefault();

        const defaultMap = {};
        defaultBobotData.forEach(item => {
            if (item && item.IdKategoriMateri) {
                defaultMap[item.IdKategoriMateri] = item;
                if (!kategoriMap.has(item.IdKategoriMateri)) {
                    kategoriMap.set(item.IdKategoriMateri, item.NamaKategoriMateri || item.IdKategoriMateri);
                    kategoriData.push({
                        id: item.IdKategoriMateri,
                        name: item.NamaKategoriMateri || item.IdKategoriMateri
                    });
                }
            }
        });

        const currentTahunAjaran = $('#IdTahunAjaran').val() || (defaultBobotData[0]?.IdTahunAjaran || 'Default');

        kategoriData.forEach((kategori, index) => {
            const defaultItem = defaultMap[kategori.id] || {
                NilaiBobot: 0
            };
            const nilai = parseFloat(defaultItem.NilaiBobot) || 0;
            const namaKategori = defaultItem.NamaKategoriMateri || kategori.name;

            currentBobotData.push({
                IdTahunAjaran: currentTahunAjaran,
                IdKategoriMateri: kategori.id,
                NamaKategoriMateri: namaKategori,
                NilaiBobot: nilai
            });

            const row = `
            <tr data-kategori-id="${kategori.id}">
                <td>
                    <input type="text" class="form-control bobot-input" 
                           value="${escapeHtml(currentTahunAjaran)}" readonly 
                           data-index="${index}" data-field="tahun">
                </td>
                <td>
                    <input type="text" class="form-control bobot-input" 
                           value="${escapeHtml(namaKategori)}" readonly 
                           data-index="${index}" data-field="kategori"
                           data-id="${escapeHtml(kategori.id)}">
                </td>
                <td>
                    <input type="number" class="form-control bobot-input" 
                           value="${nilai}" 
                           data-index="${index}" data-field="nilai"
                           data-id="${escapeHtml(kategori.id)}"
                           step="0.01" min="0" max="100">
                </td>
            </tr>
        `;
            tbody.append(row);
            originalValues[index] = nilai;
        });

        $('.bobot-input[data-field="nilai"]').off('input').on('input', function() {
            const index = $(this).data('index');
            const currentValue = parseFloat($(this).val()) || 0;
            const originalValue = originalValues[index];

            if (currentValue !== originalValue) {
                $(this).addClass('changed');
            } else {
                $(this).removeClass('changed');
            }

            updateTotalBobot();
        });

        updateTotalBobot();
    }

    // Fungsi untuk load data edit ke tabel
    function loadEditData(editData, tahunAjaran) {
        const tbody = $('#bobotFormBody');
        tbody.empty();
        originalValues = {};
        isEditMode = true;

        ensureKategoriDataFromDefault();

        const dataMap = {};
        (editData || []).forEach(item => {
            if (item && item.IdKategoriMateri) {
                dataMap[item.IdKategoriMateri] = parseFloat(item.NilaiBobot) || 0;
                if (item.NamaKategoriMateri && !kategoriMap.has(item.IdKategoriMateri)) {
                    kategoriMap.set(item.IdKategoriMateri, item.NamaKategoriMateri);
                    kategoriData.push({
                        id: item.IdKategoriMateri,
                        name: item.NamaKategoriMateri
                    });
                }
            }
        });

        kategoriData.forEach((kategori, index) => {
            const existingValue = dataMap[kategori.id] ?? 0;
            const row = `
            <tr data-kategori-id="${kategori.id}">
                <td>
                    <input type="text" class="form-control bobot-input" 
                           value="${escapeHtml(tahunAjaran)}" readonly 
                           data-index="${index}" data-field="tahun">
                </td>
                <td>
                    <input type="text" class="form-control bobot-input" 
                           value="${escapeHtml(getKategoriName(kategori.id))}" readonly 
                           data-index="${index}" data-field="kategori"
                           data-id="${escapeHtml(kategori.id)}">
                </td>
                <td>
                    <input type="number" class="form-control bobot-input" 
                           value="${existingValue}" 
                           data-index="${index}" data-field="nilai"
                           data-id="${escapeHtml(kategori.id)}"
                           step="0.01" min="0" max="100">
                </td>
            </tr>
        `;
            tbody.append(row);
            originalValues[index] = parseFloat(existingValue);
        });

        $('.bobot-input[data-field="nilai"]').off('input').on('input', function() {
            const index = $(this).data('index');
            const currentValue = parseFloat($(this).val()) || 0;
            const originalValue = originalValues[index];

            if (currentValue !== originalValue) {
                $(this).addClass('changed');
            } else {
                $(this).removeClass('changed');
            }

            updateTotalBobot();
        });
    }

    // Fungsi untuk duplicate data default
    function duplicateDefaultData(tahunAjaran) {
        Swal.fire({
            title: 'Duplicate Data?',
            text: `Apakah Anda yakin ingin menduplikasi data default untuk tahun ajaran ${tahunAjaran}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Duplicate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request untuk duplicate data ke database
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/duplicate-default-bobot') ?>',
                    type: 'POST',
                    data: {
                        IdTahunAjaran: tahunAjaran,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Update tahun ajaran di tabel
                            $('.bobot-input[data-field="tahun"]').val(tahunAjaran);

                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk delete berdasarkan tahun ajaran
    function deleteBobotByTahunAjaran(tahunAjaran) {
        Swal.fire({
            title: 'Hapus Data?',
            text: `Apakah Anda yakin ingin menghapus semua data untuk tahun ajaran ${tahunAjaran}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/delete-bobot-by-tahun') ?>',
                    type: 'POST',
                    data: {
                        IdTahunAjaran: tahunAjaran,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk save data
    function saveBobotData() {
        const tahunAjaran = $('#IdTahunAjaran').val();
        if (!tahunAjaran) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Tahun Ajaran harus diisi',
                icon: 'warning'
            });
            return;
        }

        const dataToSave = [];
        let hasChanges = false;
        let totalBobot = 0;

        $('.bobot-input[data-field="nilai"]').each(function() {
            const index = $(this).data('index');
            const nilai = parseFloat($(this).val()) || 0;
            const kategoriInput = $(this).closest('tr').find('.bobot-input[data-field="kategori"]');
            const kategoriId = (kategoriInput.data('id') || kategoriInput.attr('data-id') || '').toString().trim();

            if (!kategoriId) {
                Swal.fire({
                    title: 'Validasi Error!',
                    text: 'ID kategori materi tidak ditemukan untuk salah satu baris.',
                    icon: 'error'
                });
                hasChanges = false;
                return false;
            }

            dataToSave.push({
                IdTahunAjaran: tahunAjaran,
                IdKategoriMateri: kategoriId,
                NilaiBobot: nilai
            });

            totalBobot += nilai;

            if ($(this).hasClass('changed')) {
                hasChanges = true;
            }
        });

        if (dataToSave.length !== $('.bobot-input[data-field="nilai"]').length) {
            return;
        }

        // Validasi total bobot harus 100%
        if (Math.abs(totalBobot - 100) > 0.01) {
            Swal.fire({
                title: 'Validasi Error!',
                text: `Total bobot nilai harus berjumlah 100%. Saat ini berjumlah ${totalBobot.toFixed(2)}%`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!hasChanges) {
            Swal.fire({
                title: 'Info',
                text: 'Tidak ada perubahan yang perlu disimpan',
                icon: 'info'
            });
            return;
        }

        Swal.fire({
            title: 'Simpan Data?',
            text: `Apakah Anda yakin ingin menyimpan data untuk tahun ajaran ${tahunAjaran}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/save-bobot-batch') ?>',
                    type: 'POST',
                    data: {
                        data: dataToSave,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk update data
    function updateBobotData() {
        const tahunAjaran = $('#editIdTahunAjaran').val();
        if (!tahunAjaran) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Tahun Ajaran harus diisi',
                icon: 'warning'
            });
            return;
        }

        const dataToUpdate = [];
        let hasChanges = false;
        let totalBobot = 0;

        $('.edit-bobot-input[data-field="nilai"]').each(function() {
            const index = $(this).data('index');
            const nilai = parseFloat($(this).val()) || 0;
            const kategoriInput = $(this).closest('tr').find('.edit-bobot-input[data-field="kategori"]');
            const kategoriId = (kategoriInput.data('id') || kategoriInput.attr('data-id') || '').toString().trim();

            if (!kategoriId) {
                Swal.fire({
                    title: 'Validasi Error!',
                    text: 'ID kategori materi tidak ditemukan untuk salah satu baris.',
                    icon: 'error'
                });
                hasChanges = false;
                return false;
            }

            dataToUpdate.push({
                IdTahunAjaran: tahunAjaran,
                IdKategoriMateri: kategoriId,
                NilaiBobot: nilai
            });

            totalBobot += nilai;

            if ($(this).hasClass('changed')) {
                hasChanges = true;
            }
        });

        if (dataToUpdate.length !== $('.edit-bobot-input[data-field="nilai"]').length) {
            return;
        }

        // Validasi total bobot harus 100%
        if (Math.abs(totalBobot - 100) > 0.01) {
            Swal.fire({
                title: 'Validasi Error!',
                text: `Total bobot nilai harus berjumlah 100%. Saat ini berjumlah ${totalBobot.toFixed(2)}%`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!hasChanges) {
            Swal.fire({
                title: 'Info',
                text: 'Tidak ada perubahan yang perlu disimpan',
                icon: 'info'
            });
            return;
        }

        Swal.fire({
            title: 'Update Data?',
            text: `Apakah Anda yakin ingin mengupdate data untuk tahun ajaran ${tahunAjaran}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/save-bobot-batch') ?>',
                    type: 'POST',
                    data: {
                        data: dataToUpdate,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                $('#modalEditBobot').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk update total bobot real-time (Add Mode)
    function updateTotalBobot() {
        let total = 0;
        $('.bobot-input[data-field="nilai"]').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        // Update display total
        let totalDisplay = $('#totalBobotDisplay');
        if (totalDisplay.length === 0) {
            // Create total display if not exists
            $('#bobotFormBody').after(`
            <tr id="totalBobotRow">
                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                <td>
                    <span id="totalBobotDisplay" class="badge ${Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger'}">
                        ${total.toFixed(2)}%
                    </span>
                </td>
            </tr>
        `);
        } else {
            // Update existing total display
            totalDisplay.text(`${total.toFixed(2)}%`);
            totalDisplay.removeClass('badge-success badge-danger');
            totalDisplay.addClass(Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger');
        }
    }

    // Fungsi untuk update total bobot real-time (Edit Mode)
    function updateTotalBobotEdit() {
        let total = 0;
        $('.edit-bobot-input[data-field="nilai"]').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        // Update display total
        let totalDisplay = $('#editTotalBobotDisplay');
        if (totalDisplay.length === 0) {
            // Create total display if not exists
            $('#editBobotFormBody').after(`
            <tr id="editTotalBobotRow">
                <td class="text-right"><strong>Total:</strong></td>
                <td>
                    <span id="editTotalBobotDisplay" class="badge ${Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger'}">
                        ${total.toFixed(2)}%
                    </span>
                </td>
            </tr>
        `);
        } else {
            // Update existing total display
            totalDisplay.text(`${total.toFixed(2)}%`);
            totalDisplay.removeClass('badge-success badge-danger');
            totalDisplay.addClass(Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger');
        }
    }

    function editBobotTahun(tahunAjaran) {
        // Load data untuk tahun ajaran yang dipilih
        $.ajax({
            url: '<?= base_url('backend/munaqosah/get-bobot-by-tahun/') ?>' + encodeURIComponent(tahunAjaran),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Populate edit form dengan data yang ada
                    $('#editIdTahunAjaran').val(tahunAjaran);

                    // Clear existing data first
                    $('#editBobotFormBody').empty();

                    ensureKategoriDataFromDefault();

                    const dataMap = {};
                    (response.data || []).forEach(item => {
                        if (item && item.IdKategoriMateri) {
                            dataMap[item.IdKategoriMateri] = {
                                nilai: parseFloat(item.NilaiBobot) || 0,
                                nama: item.NamaKategoriMateri || getKategoriName(item.IdKategoriMateri)
                            };

                            if (item.NamaKategoriMateri && !kategoriMap.has(item.IdKategoriMateri)) {
                                kategoriMap.set(item.IdKategoriMateri, item.NamaKategoriMateri);
                                kategoriData.push({
                                    id: item.IdKategoriMateri,
                                    name: item.NamaKategoriMateri
                                });
                            }
                        }
                    });

                    const defaultMap = {};
                    defaultBobotData.forEach(item => {
                        if (item && item.IdKategoriMateri) {
                            defaultMap[item.IdKategoriMateri] = parseFloat(item.NilaiBobot) || 0;
                        }
                    });

                    kategoriData.forEach((kategori, index) => {
                        const existing = dataMap[kategori.id];
                        const nilai = existing ? existing.nilai : (defaultMap[kategori.id] ?? 0);
                        const namaKategori = existing ? existing.nama : getKategoriName(kategori.id);

                        const row = `
                        <tr data-kategori-id="${kategori.id}">
                            <td>
                                <input type="text" class="form-control edit-bobot-input" 
                                       value="${escapeHtml(namaKategori)}" readonly 
                                       data-index="${index}" data-field="kategori"
                                       data-id="${escapeHtml(kategori.id)}">
                            </td>
                            <td>
                                <input type="number" class="form-control edit-bobot-input" 
                                       value="${nilai}" 
                                       data-index="${index}" data-field="nilai"
                                       data-id="${escapeHtml(kategori.id)}"
                                       step="0.01" min="0" max="100">
                            </td>
                        </tr>
                    `;
                        $('#editBobotFormBody').append(row);
                    });

                    $('.edit-bobot-input[data-field="nilai"]').each(function() {
                        $(this).attr('data-original', $(this).val());
                    });

                    // Attach event listeners for changes
                    $('.edit-bobot-input[data-field="nilai"]').off('input').on('input', function() {
                        const index = $(this).data('index');
                        const currentValue = parseFloat($(this).val()) || 0;
                        const originalValue = parseFloat($(this).attr('data-original')) || 0;

                        if (currentValue !== originalValue) {
                            $(this).addClass('changed');
                        } else {
                            $(this).removeClass('changed');
                        }

                        updateTotalBobotEdit();
                    });

                    // Update total bobot saat pertama kali load
                    updateTotalBobotEdit();

                    // Update modal title untuk edit
                    $('#modalEditBobotLabel').text('Edit Bobot Nilai Munaqosah - ' + tahunAjaran);

                    $('#modalEditBobot').modal('show');
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server',
                    icon: 'error'
                });
            }
        });
    }

    function deleteBobotTahun(tahunAjaran) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Data untuk tahun ajaran ${tahunAjaran} akan dihapus dan tidak dapat dikembalikan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/delete-bobot-by-tahun') ?>',
                    type: 'POST',
                    data: {
                        IdTahunAjaran: tahunAjaran,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>