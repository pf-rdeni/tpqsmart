<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Berkas Lampiran Guru</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php if (in_groups('Admin')): ?>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filterTpq" class="form-label">Filter TPQ</label>
                        <select id="filterTpq" class="form-control form-control-sm">
                            <option value="">Semua TPQ</option>
                            <?php if (!empty($tpq)) : foreach ($tpq as $dataTpq): ?>
                                    <option value="<?= esc($dataTpq['IdTpq']) ?>">
                                        <?= esc($dataTpq['NamaTpq']) ?> - <?= esc($dataTpq['KelurahanDesa'] ?? '-') ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
            <table id="tabelBerkasLampiran" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NIK / Nama / TPQ</th>
                        <th>KTP</th>
                        <th>KK</th>
                        <th>Buku Rekening</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($guruWithBerkas as $item) :
                        $guru = $item['guru'];
                        $berkas = $item['berkas'];
                        $namaTpq = $item['namaTpq'];
                    ?>
                        <tr data-idtpq="<?= esc($guru['IdTpq'] ?? '') ?>" data-idguru="<?= esc($guru['IdGuru']) ?>">
                            <td>
                                <?= esc($guru['IdGuru']) ?><br>
                                <strong><?= ucwords(strtolower($guru['Nama'])) ?></strong><br>
                                <small style="color: #666;">TPQ: <?= esc($namaTpq) ?></small>
                            </td>
                            <td>
                                <?php if (isset($berkas['KTP'])): ?>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <a href="<?= base_url('uploads/berkas/' . $berkas['KTP']['NamaFile']) ?>" target="_blank" class="btn btn-sm btn-info p-1" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($berkas['KTP']['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'KTP')" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($berkas['KTP']['id']) ?>)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'KTP')" title="Upload KTP">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($berkas['KK'])): ?>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <a href="<?= base_url('uploads/berkas/' . $berkas['KK']['NamaFile']) ?>" target="_blank" class="btn btn-sm btn-info p-1" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($berkas['KK']['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'KK')" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($berkas['KK']['id']) ?>)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'KK')" title="Upload KK">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($berkas['Buku Rekening']) && is_array($berkas['Buku Rekening'])): ?>
                                    <?php
                                    // Cek apakah sudah ada BPR dan BRK
                                    $hasBpr = false;
                                    $hasBrk = false;
                                    foreach ($berkas['Buku Rekening'] as $rekening) {
                                        $dataBerkas = $rekening['DataBerkas'] ?? '';
                                        if ($dataBerkas === 'BPR') {
                                            $hasBpr = true;
                                        } elseif ($dataBerkas === 'BRK') {
                                            $hasBrk = true;
                                        }
                                    }
                                    $showUploadButton = !($hasBpr && $hasBrk);
                                    ?>
                                    <?php foreach ($berkas['Buku Rekening'] as $rekening): ?>
                                        <div class="mb-1 d-flex align-items-center gap-2 flex-wrap">
                                            <strong><?= esc($rekening['DataBerkas'] ?? '-') ?>:</strong>
                                            <a href="<?= base_url('uploads/berkas/' . $rekening['NamaFile']) ?>" target="_blank" class="btn btn-sm btn-info p-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($rekening['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', '<?= esc($rekening['DataBerkas'] ?? '') ?>')" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($rekening['id']) ?>)" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if ($showUploadButton): ?>
                                        <div class="mt-1">
                                            <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening')" title="Upload Buku Rekening">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif (isset($berkas['Buku Rekening'])): ?>
                                    <!-- Backward compatibility: jika masih single file -->
                                    <?php
                                    // Cek apakah sudah ada BPR atau BRK
                                    $dataBerkas = $berkas['Buku Rekening']['DataBerkas'] ?? '';
                                    $hasBpr = ($dataBerkas === 'BPR');
                                    $hasBrk = ($dataBerkas === 'BRK');
                                    $showUploadButton = !($hasBpr && $hasBrk); // Akan false jika sudah ada keduanya, tapi untuk single file ini tidak mungkin
                                    // Untuk single file, selalu tampilkan button upload karena masih bisa upload yang lain
                                    $showUploadButton = true;
                                    ?>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <a href="<?= base_url('uploads/berkas/' . $berkas['Buku Rekening']['NamaFile']) ?>" target="_blank" class="btn btn-sm btn-info p-1" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($berkas['Buku Rekening']['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening')" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($berkas['Buku Rekening']['id']) ?>)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <?php if ($showUploadButton): ?>
                                        <div class="mt-1">
                                            <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening')" title="Upload Buku Rekening">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening')" title="Upload Buku Rekening">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>NIK / Nama / TPQ</th>
                        <th>KTP</th>
                        <th>KK</th>
                        <th>Buku Rekening</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Modal Upload Berkas -->
<div class="modal fade" id="modalUploadBerkas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Berkas Lampiran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formUploadBerkas">
                    <input type="hidden" id="uploadIdGuru" name="IdGuru">
                    <div class="form-group">
                        <label>Nama Guru</label>
                        <input type="text" id="uploadNamaGuru" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="uploadNamaBerkas">Tipe Berkas <span class="text-danger">*</span></label>
                        <select class="form-control" id="uploadNamaBerkas" name="NamaBerkas" required>
                            <option value="">Pilih Tipe Berkas</option>
                            <option value="KTP">KTP</option>
                            <option value="KK">KK (Kartu Keluarga)</option>
                            <option value="Buku Rekening">Buku Rekening</option>
                        </select>
                    </div>
                    <div class="form-group" id="dataBerkasGroup" style="display: none;">
                        <label for="uploadDataBerkas">Nama Bank <span class="text-danger">*</span></label>
                        <select class="form-control" id="uploadDataBerkas" name="DataBerkas">
                            <option value="">Pilih Nama Bank</option>
                            <option value="BPR">BPR</option>
                            <option value="BRK">BRK</option>
                        </select>
                        <small class="form-text text-muted">Pilih bank untuk buku rekening yang akan diupload</small>
                    </div>
                    <div class="form-group">
                        <label for="fileBerkas">File Berkas <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="fileBerkas" accept="image/jpeg,image/jpg,image/png">
                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 15MB (akan di-compress setelah crop)</small>
                        <small class="form-text text-info" id="editModeInfo" style="display: none;">
                            <i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengganti gambar
                        </small>
                    </div>
                    <div id="existingImageContainer" style="display: none;">
                        <label>Gambar Saat Ini</label>
                        <div class="text-center mb-2">
                            <img id="existingImage" src="" alt="Gambar Saat Ini" style="max-width: 100%; max-height: 400px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-primary" id="btnCropExisting" onclick="cropExistingImage()">
                                <i class="fas fa-crop"></i> Crop
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="btnRemoveExisting" onclick="removeExistingImage()">
                                <i class="fas fa-times"></i> Hapus dan Upload Baru
                            </button>
                        </div>
                    </div>
                    <div id="previewContainer" style="display: none;">
                        <label>Preview Hasil Crop</label>
                        <div class="text-center mb-2">
                            <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 400px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                        <button type="button" class="btn btn-sm btn-warning" onclick="removePreviewImage()">
                            <i class="fas fa-redo"></i> Pilih File Lain
                        </button>
                    </div>
                    <input type="hidden" id="croppedImageData" name="croppedImageData">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" id="btnUseExisting" style="display: none;" onclick="useExistingImage()">Gunakan Gambar Saat Ini</button>
                <button type="button" class="btn btn-primary" id="btnUploadBerkasFromForm" style="display: none;" onclick="uploadBerkasFromForm()">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crop Berkas -->
<div class="modal fade" id="modalCropBerkas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%; margin: 10px auto;">
        <div class="modal-content" style="height: calc(100vh - 20px); display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Crop Berkas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="flex: 1; overflow: hidden; padding: 15px; display: flex; align-items: center; justify-content: center;">
                <div id="cropContainerBerkas" style="width: 100%; height: 100%; max-height: calc(100vh - 200px); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <img id="imageToCropBerkas" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; border-top: 1px solid #dee2e6;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUploadBerkas">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Berkas (Khusus untuk Edit) -->
<div class="modal fade" id="modalEditBerkas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Berkas Lampiran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditBerkas">
                    <input type="hidden" id="editBerkasId" name="editBerkasId">
                    <input type="hidden" id="editIdGuru" name="IdGuru">
                    <div class="form-group">
                        <label>Nama Guru</label>
                        <input type="text" id="editNamaGuru" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tipe Berkas</label>
                        <input type="text" id="editNamaBerkas" class="form-control" readonly>
                    </div>
                    <div class="form-group" id="editDataBerkasGroup" style="display: none;">
                        <label>Nama Bank</label>
                        <input type="text" id="editDataBerkas" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editFileBerkas">Ganti dengan File Baru</label>
                        <input type="file" class="form-control-file" id="editFileBerkas" accept="image/jpeg,image/jpg,image/png">
                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 15MB (akan di-compress setelah crop). Kosongkan jika tidak ingin mengganti.</small>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Gambar Saat Ini</label>
                                <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                                    <img id="editExistingImage" src="" alt="Gambar Saat Ini" style="max-width: 100%; max-height: 400px; object-fit: contain;">
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-sm btn-warning" id="btnCropExistingImage" onclick="cropExistingImageInEdit()">
                                        <i class="fas fa-crop"></i> Crop Gambar Saat Ini
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Preview Hasil Edit</label>
                                <div id="editPreviewContainer" style="display: none;">
                                    <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                                        <img id="editPreviewImage" src="" alt="Preview" style="max-width: 100%; max-height: 400px; object-fit: contain;">
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning" onclick="removeEditPreviewImage()">
                                            <i class="fas fa-redo"></i> Pilih File Lain
                                        </button>
                                    </div>
                                </div>
                                <div id="editPreviewPlaceholder" class="text-center" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 300px; display: flex; align-items: center; justify-content: center; color: #999;">
                                    <div>
                                        <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px;"></i>
                                        <p class="mb-0">Belum ada preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="editCroppedImageData" name="editCroppedImageData">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUpdateBerkas" style="display: none;" onclick="updateBerkasFromForm()">Update</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style untuk modal crop berkas */
    #modalCropBerkas .modal-dialog {
        max-width: 95%;
        margin: 10px auto;
    }

    #modalCropBerkas .modal-content {
        height: calc(100vh - 20px);
        display: flex;
        flex-direction: column;
    }

    #modalCropBerkas .modal-body {
        flex: 1;
        overflow: hidden;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 0;
    }

    #cropContainerBerkas {
        width: 100%;
        height: 100%;
        max-height: calc(100vh - 200px);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    #imageToCropBerkas {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    #modalCropBerkas .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        padding: 10px 15px;
    }

    /* Pastikan cropper container tidak overflow */
    #modalCropBerkas .cropper-container {
        max-width: 100%;
        max-height: 100%;
    }

    /* Responsive untuk layar kecil */
    @media (max-height: 600px) {
        #modalCropBerkas .modal-content {
            height: calc(100vh - 10px);
        }

        #cropContainerBerkas {
            max-height: calc(100vh - 150px);
        }
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Image Upload Helper -->
<script src="<?= base_url('helpers/js/image-upload-helper.js') ?>"></script>
<script>
    let cropperBerkas = null;
    let selectedFileBerkas = null;
    let currentIdGuru = null;

    // Inisialisasi DataTable
    document.addEventListener('DOMContentLoaded', function() {
        const table = initializeDataTableScrollX("#tabelBerkasLampiran", [], {
            "pageLength": 25,
            "lengthChange": true
        });

        <?php if (in_groups('Admin')): ?>
            // Filter TPQ untuk Admin
            const filterTpq = $('#filterTpq');
            let customFilterFunction = null;
            const filterStorageKey = 'berkasLampiran_filter_tpq';

            // Fungsi untuk menyimpan filter TPQ ke localStorage
            function saveFilterTpq() {
                const selectedTpq = filterTpq.val() || '';
                localStorage.setItem(filterStorageKey, selectedTpq);
            }

            // Fungsi untuk memuat filter TPQ dari localStorage
            function loadFilterTpq() {
                try {
                    const savedTpq = localStorage.getItem(filterStorageKey);
                    if (savedTpq !== null && savedTpq !== '') {
                        // Cek apakah option dengan value tersebut ada
                        const optionExists = filterTpq.find('option[value="' + savedTpq + '"]').length > 0;
                        if (optionExists) {
                            filterTpq.val(savedTpq);
                            // Trigger change event untuk menerapkan filter
                            filterTpq.trigger('change');
                        }
                    }
                } catch (e) {
                    console.error('Error loading filter TPQ from localStorage:', e);
                }
            }

            // Load filter saat halaman dimuat
            loadFilterTpq();

            filterTpq.on('change', function() {
                const selectedTpq = $(this).val();

                // Simpan filter ke localStorage
                saveFilterTpq();

                if (customFilterFunction !== null) {
                    $.fn.dataTable.ext.search.pop();
                    customFilterFunction = null;
                }

                if (selectedTpq !== '') {
                    customFilterFunction = function(settings, data, dataIndex) {
                        if (!settings || !settings.nTable || settings.nTable.id !== 'tabelBerkasLampiran') {
                            return true;
                        }

                        try {
                            const row = table.row(dataIndex).node();
                            if (!row) {
                                return true;
                            }

                            const rowIdTpq = $(row).attr('data-idtpq');
                            return rowIdTpq === selectedTpq;
                        } catch (e) {
                            console.error('Error in custom filter:', e);
                            return true;
                        }
                    };

                    $.fn.dataTable.ext.search.push(customFilterFunction);
                }

                table.draw();
            });
        <?php endif; ?>

        // Handle perubahan tipe berkas untuk menampilkan/sembunyikan dropdown bank
        $('#uploadNamaBerkas').on('change', function() {
            const selectedBerkas = $(this).val();
            if (selectedBerkas === 'Buku Rekening') {
                $('#dataBerkasGroup').show();
                $('#uploadDataBerkas').prop('required', true);
            } else {
                $('#dataBerkasGroup').hide();
                $('#uploadDataBerkas').val('');
                $('#uploadDataBerkas').prop('required', false);
            }
        });
    });

    // Function untuk membuka modal upload
    // Function untuk membuka modal upload dengan tipe berkas yang sudah dipilih
    function openUploadModalWithType(idGuru, namaGuru, namaBerkas) {
        currentIdGuru = idGuru;
        $('#uploadIdGuru').val(idGuru);
        $('#uploadNamaGuru').val(namaGuru);
        $('#fileBerkas').val('');
        $('#previewContainer').hide();
        $('#btnUploadBerkasFromForm').hide();
        $('#existingImageContainer').hide();
        $('#btnUseExisting').hide();
        $('#editModeInfo').hide();

        // Reset cropper jika ada
        if (cropperBerkas) {
            cropperBerkas.destroy();
            cropperBerkas = null;
        }

        // Simpan nilai ke window SEBELUM set ke form
        window.savedCropNamaBerkas = namaBerkas;
        if (namaBerkas === 'Buku Rekening') {
            // Jika Buku Rekening, simpan nilai bank jika sudah ada
            const existingDataBerkas = $('#uploadDataBerkas').val();
            if (existingDataBerkas) {
                window.savedCropDataBerkas = existingDataBerkas;
            } else {
                window.savedCropDataBerkas = null;
            }
        } else {
            window.savedCropDataBerkas = null;
        }

        // Set tipe berkas yang dipilih
        $('#uploadNamaBerkas').val(namaBerkas);
        $('#uploadNamaBerkas').prop('disabled', true); // Disable karena sudah dipilih dari kolom

        // Jika Buku Rekening, tampilkan dropdown bank
        if (namaBerkas === 'Buku Rekening') {
            $('#dataBerkasGroup').show();
            $('#uploadDataBerkas').prop('required', true);
            $('#uploadDataBerkas').prop('disabled', false);
            // Set nilai bank jika sudah ada di window
            if (window.savedCropDataBerkas) {
                $('#uploadDataBerkas').val(window.savedCropDataBerkas);
            }
        } else {
            $('#dataBerkasGroup').hide();
            $('#uploadDataBerkas').prop('required', false);
            $('#uploadDataBerkas').val('');
        }

        window.currentEditBerkasData = null;

        // Buka modal setelah semua set
        $('#modalUploadBerkas').modal('show');
    }

    // Event handler untuk menyimpan nilai bank saat user memilih
    $(document).on('change', '#uploadDataBerkas', function() {
        const dataBerkas = $(this).val();
        if (dataBerkas) {
            window.savedCropDataBerkas = dataBerkas;
        }
    });

    // Event handler untuk menyimpan nilai tipe berkas saat user memilih
    $(document).on('change', '#uploadNamaBerkas', function() {
        const namaBerkas = $(this).val();
        if (namaBerkas) {
            window.savedCropNamaBerkas = namaBerkas;
            // Jika bukan Buku Rekening, reset nilai bank
            if (namaBerkas !== 'Buku Rekening') {
                window.savedCropDataBerkas = null;
            }
        }
    });

    function openUploadModal(idGuru, namaGuru, isEditMode = false, existingBerkasData = null) {
        currentIdGuru = idGuru;
        $('#uploadIdGuru').val(idGuru);
        $('#uploadNamaGuru').val(namaGuru);
        $('#fileBerkas').val('');
        $('#previewContainer').hide();
        $('#btnUploadBerkasFromForm').hide();
        $('#existingImageContainer').hide();
        $('#btnUseExisting').hide();
        $('#editModeInfo').hide();

        // Reset cropper jika ada
        if (cropperBerkas) {
            cropperBerkas.destroy();
            cropperBerkas = null;
        }

        // Jika mode edit dan ada data existing
        if (isEditMode && existingBerkasData) {
            $('#uploadNamaBerkas').val(existingBerkasData.NamaBerkas);
            $('#uploadNamaBerkas').prop('disabled', true); // Disable dropdown karena sudah ada

            // Jika Buku Rekening, tampilkan DataBerkas
            if (existingBerkasData.NamaBerkas === 'Buku Rekening') {
                $('#uploadDataBerkas').val(existingBerkasData.DataBerkas || '');
                $('#uploadDataBerkas').prop('disabled', true);
                $('#dataBerkasGroup').show();
            }

            // Tampilkan gambar existing
            const imageUrl = '<?= base_url('uploads/berkas/') ?>' + existingBerkasData.NamaFile;
            $('#existingImage').attr('src', imageUrl);
            $('#existingImageContainer').show();
            $('#btnUseExisting').show();
            $('#editModeInfo').show();

            // Simpan data existing untuk digunakan nanti
            window.currentEditBerkasData = existingBerkasData;
        } else {
            // Mode upload baru
            $('#uploadNamaBerkas').val('');
            $('#uploadNamaBerkas').prop('disabled', false);
            $('#uploadDataBerkas').val('');
            $('#uploadDataBerkas').prop('disabled', false);
            $('#dataBerkasGroup').hide();
            window.currentEditBerkasData = null;
        }

        $('#modalUploadBerkas').modal('show');
    }

    // Function untuk menggunakan gambar existing (tanpa upload baru)
    function useExistingImage() {
        if (!window.currentEditBerkasData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Data berkas tidak ditemukan'
            });
            return;
        }

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan perubahan...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim request untuk update (hanya update metadata jika perlu, atau tidak perlu update karena file sama)
        // Karena file sama, kita bisa langsung close modal dan reload
        Swal.close();
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: 'Gambar tetap menggunakan yang saat ini. Tidak ada perubahan yang disimpan.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            $('#modalUploadBerkas').modal('hide');
            location.reload();
        });
    }

    // Function untuk crop gambar existing
    function cropExistingImage() {
        const existingImageSrc = $('#existingImage').attr('src');
        if (!existingImageSrc) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gambar tidak ditemukan'
            });
            return;
        }

        // Simpan nilai tipe berkas dan bank sebelum buka modal crop
        const namaBerkas = $('#uploadNamaBerkas').val();
        const dataBerkas = $('#uploadDataBerkas').val();

        // Buka modal crop dengan gambar existing dan tipe berkas
        showCropModalBerkas(existingImageSrc, namaBerkas);
    }

    // Function untuk menghapus gambar existing dan upload baru
    function removeExistingImage() {
        $('#existingImageContainer').hide();
        $('#btnUseExisting').hide();
        $('#fileBerkas').prop('required', true);
        $('#editModeInfo').hide();
    }

    // Handle file input change
    $('#fileBerkas').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            return;
        }

        // Validasi ukuran file (max 15MB - file akan di-compress setelah crop)
        if (file.size > 15728640) { // 15MB
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Ukuran file terlalu besar. Maksimal 15MB. File akan otomatis di-compress setelah crop.'
            });
            $(this).val('');
            return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tipe file tidak diizinkan. Hanya JPG, JPEG, atau PNG'
            });
            $(this).val('');
            return;
        }

        selectedFileBerkas = file;

        // Sembunyikan existing image container jika ada
        $('#existingImageContainer').hide();
        $('#btnUseExisting').hide();
        $('#previewContainer').hide();
        $('#btnUploadBerkasFromForm').hide();
        $('#croppedImageData').val('');

        // Validasi tipe berkas dan data berkas sudah dipilih
        const namaBerkas = $('#uploadNamaBerkas').val();
        if (!namaBerkas) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih Tipe Berkas terlebih dahulu'
            });
            $(this).val('');
            return;
        }

        // Simpan nilai tipe berkas dan bank ke window untuk digunakan saat crop
        // SELALU simpan nilai terbaru dari form sebelum membuka modal crop
        // Ambil nilai langsung dari form untuk memastikan nilai terbaru
        const currentNamaBerkas = $('#uploadNamaBerkas').val();
        const currentDataBerkas = $('#uploadDataBerkas').val();

        window.savedCropNamaBerkas = currentNamaBerkas || namaBerkas;

        if (currentNamaBerkas === 'Buku Rekening' || namaBerkas === 'Buku Rekening') {
            if (!currentDataBerkas) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih Nama Bank terlebih dahulu'
                });
                $(this).val('');
                return;
            }
            // Simpan nilai bank ke window
            window.savedCropDataBerkas = currentDataBerkas;
        } else {
            window.savedCropDataBerkas = null;
        }

        // Tunggu ImageUploadHelper tersedia
        waitForImageUploadHelper(function() {
            if (!window.ImageUploadHelper || !window.ImageUploadHelper.resizeImageBeforeCrop) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ImageUploadHelper tidak ditemukan. Pastikan image-upload-helper.js sudah di-include.'
                });
                return;
            }

            // Resize sebelum crop
            const maxDimension = 2000;
            const resizeQuality = 0.85;

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses gambar...',
                text: 'Sedang resize dan compress gambar...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            window.ImageUploadHelper.resizeImageBeforeCrop(file, maxDimension, maxDimension, resizeQuality, function(processedFile) {
                selectedFileBerkas = processedFile;

                // Hitung ukuran file setelah resize
                const originalSize = (file.size / 1024 / 1024).toFixed(2); // MB
                const resizedSize = (processedFile.size / 1024 / 1024).toFixed(2); // MB
                const sizeReduction = ((1 - processedFile.size / file.size) * 100).toFixed(1); // Persentase

                // Tutup loading dan tampilkan info ukuran
                Swal.close();
                Swal.fire({
                    icon: 'info',
                    title: 'Gambar berhasil di-resize',
                    html: `Ukuran file:<br>
                           <strong>Sebelum:</strong> ${originalSize} MB<br>
                           <strong>Sesudah:</strong> ${resizedSize} MB<br>
                           <strong>Pengurangan:</strong> ${sizeReduction}%`,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Langsung buka modal crop tanpa preview di form
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Simpan tipe berkas untuk digunakan saat crop
                        const namaBerkas = $('#uploadNamaBerkas').val();
                        showCropModalBerkas(e.target.result, namaBerkas);
                    };
                    reader.readAsDataURL(processedFile);
                });
            });
        });
    });

    // Function untuk menampilkan modal crop
    function showCropModalBerkas(imageUrl, namaBerkas = null) {
        const imageElement = document.getElementById('imageToCropBerkas');

        if (cropperBerkas) {
            cropperBerkas.destroy();
            cropperBerkas = null;
        }

        // Tentukan apakah ini dari modal edit atau modal upload
        const isFromEditModal = $('#modalEditBerkas').is(':visible') || window.currentEditBerkasData;

        // Jika namaBerkas tidak diberikan, ambil dari form yang sesuai
        if (!namaBerkas) {
            if (isFromEditModal) {
                namaBerkas = window.savedCropNamaBerkas || $('#editNamaBerkas').val();
            } else {
                namaBerkas = window.savedCropNamaBerkas || $('#uploadNamaBerkas').val();
            }
        }

        // Simpan namaBerkas untuk digunakan saat inisialisasi cropper
        window.currentCropNamaBerkas = namaBerkas;

        // Pastikan nilai bank juga tersimpan
        if (!window.savedCropDataBerkas && namaBerkas === 'Buku Rekening') {
            if (isFromEditModal) {
                window.savedCropDataBerkas = $('#editDataBerkas').val();
            } else {
                window.savedCropDataBerkas = $('#uploadDataBerkas').val();
            }
        }

        imageElement.src = imageUrl;

        // Tutup modal yang sesuai (edit atau upload) sementara, akan dibuka kembali setelah crop selesai
        if (isFromEditModal) {
            $('#modalEditBerkas').modal('hide');
            $('#modalCropBerkas').off('shown.bs.modal');

            // Tunggu modal edit tertutup, baru buka modal crop
            $('#modalEditBerkas').one('hidden.bs.modal', function() {
                $('#modalCropBerkas').modal('show');
            });
        } else {
            // Jika dari modal upload, simpan nilai SEBELUM menutup modal
            // Ambil nilai langsung dari form untuk memastikan nilai terbaru
            const namaBerkas = $('#uploadNamaBerkas').val();
            const dataBerkas = $('#uploadDataBerkas').val();

            // Simpan ke window untuk digunakan setelah crop
            if (namaBerkas) {
                window.savedCropNamaBerkas = namaBerkas;
            }
            if (dataBerkas) {
                window.savedCropDataBerkas = dataBerkas;
            } else if (namaBerkas !== 'Buku Rekening') {
                // Jika bukan Buku Rekening, pastikan dataBerkas null
                window.savedCropDataBerkas = null;
            }

            // Set flag bahwa kita sedang membuka modal crop
            window.isOpeningCropModal = true;

            $('#modalUploadBerkas').modal('hide');
            $('#modalCropBerkas').off('shown.bs.modal');

            // Tunggu modal upload tertutup, baru buka modal crop
            $('#modalUploadBerkas').one('hidden.bs.modal', function() {
                $('#modalCropBerkas').modal('show');
            });
        }

        $('#modalCropBerkas').on('shown.bs.modal', function() {
            if (cropperBerkas) {
                cropperBerkas.destroy();
                cropperBerkas = null;
            }

            const currentSrc = imageElement.src;
            imageElement.src = '';
            imageElement.src = currentSrc;

            imageElement.onload = function() {
                setTimeout(function() {
                    if (typeof Cropper === 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Library Cropper.js belum dimuat. Silakan refresh halaman.'
                        });
                        return;
                    }

                    if (!imageElement.src || imageElement.offsetWidth === 0) return;

                    if (cropperBerkas) {
                        cropperBerkas.destroy();
                        cropperBerkas = null;
                    }

                    try {
                        // Pastikan container memiliki ukuran yang tepat
                        const cropContainer = document.getElementById('cropContainerBerkas');
                        if (cropContainer) {
                            // Set container height berdasarkan viewport
                            const maxHeight = window.innerHeight - 200; // Kurangi untuk header dan footer
                            cropContainer.style.maxHeight = maxHeight + 'px';
                            cropContainer.style.height = maxHeight + 'px';
                        }

                        // Tentukan aspect ratio berdasarkan tipe berkas
                        let aspectRatio = NaN; // Default: free aspect ratio
                        let aspectRatioFixed = false; // Apakah aspect ratio fixed atau bisa di-adjust
                        const namaBerkas = window.currentCropNamaBerkas || $('#uploadNamaBerkas').val();

                        if (namaBerkas === 'KTP') {
                            // KTP Indonesia: 85.6mm x 53.98mm = 1.585 (FIXED)
                            aspectRatio = 85.6 / 53.98; // = 1.585
                            aspectRatioFixed = true;
                        } else if (namaBerkas === 'KK') {
                            // KK: A4 Landscape (297mm x 210mm = 1.414) - Initial, bisa di-adjust
                            aspectRatio = 297 / 210; // = 1.414 (A4 Landscape)
                            aspectRatioFixed = false; // Bisa di-adjust
                        } else if (namaBerkas === 'Buku Rekening') {
                            // Buku Rekening: Proporsi dokumen rekening bank (mirip A4 Landscape)
                            // Biasanya dokumen rekening bank memiliki proporsi sekitar 1.4-1.5
                            aspectRatio = 297 / 210; // = 1.414 (A4 Landscape) - Initial, bisa di-adjust
                            aspectRatioFixed = false; // Bisa di-adjust
                        }
                        // Untuk lainnya, tetap NaN (free aspect ratio)

                        // Simpan namaBerkas dan aspectRatioFixed untuk digunakan di ready callback
                        const cropNamaBerkas = namaBerkas;
                        const cropAspectRatioFixed = aspectRatioFixed;

                        cropperBerkas = new Cropper(imageElement, {
                            aspectRatio: aspectRatio, // Fixed untuk KTP, initial untuk KK, free untuk lainnya
                            viewMode: 1, // Restrict the crop box within the canvas
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            responsive: true,
                            minContainerWidth: 200,
                            minContainerHeight: 200,
                            ready: function() {
                                console.log('Cropper Berkas initialized successfully');

                                // Untuk KK dan Buku Rekening, set aspect ratio menjadi free setelah initial crop box dibuat
                                // Ini memungkinkan user untuk adjust crop box
                                if ((cropNamaBerkas === 'KK' || cropNamaBerkas === 'Buku Rekening') && !cropAspectRatioFixed && this.cropper) {
                                    // Set aspect ratio menjadi NaN setelah crop box dibuat
                                    // User bisa adjust dengan mengubah ukuran crop box
                                    setTimeout(() => {
                                        if (this.cropper && typeof this.cropper.setAspectRatio === 'function') {
                                            // Set ke NaN untuk memungkinkan free aspect ratio
                                            this.cropper.setAspectRatio(NaN);
                                        }
                                    }, 100);
                                }

                                // Pastikan gambar terlihat utuh setelah cropper siap
                                try {
                                    if (this.cropper && typeof this.cropper.scaleToFit === 'function') {
                                        this.cropper.scaleToFit();
                                    } else if (this.cropper && typeof this.cropper.reset === 'function') {
                                        // Fallback: reset untuk memastikan gambar terlihat utuh
                                        this.cropper.reset();
                                    }
                                } catch (e) {
                                    console.log('Error in cropper ready callback:', e);
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing cropper:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menginisialisasi cropper: ' + error.message
                        });
                    }
                }, 500);
            };

            if (imageElement.complete) {
                imageElement.onload();
            } else {
                imageElement.addEventListener('load', imageElement.onload, {
                    once: true
                });
            }
        });
    }

    // Function untuk menyimpan hasil crop dan kembali ke form
    $('#btnUploadBerkas').on('click', function() {
        if (!cropperBerkas) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Cropper belum diinisialisasi'
            });
            return;
        }

        // Get cropped canvas
        const canvas = cropperBerkas.getCroppedCanvas({
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal membuat canvas'
            });
            return;
        }

        // Convert canvas to base64
        const base64Image = canvas.toDataURL('image/jpeg', 0.85);

        // Simpan hasil crop ke hidden input dan preview
        // Tentukan apakah ini dari modal edit atau modal upload
        // Gunakan window.currentEditBerkasData atau cek apakah editNamaBerkas ada nilainya
        const isFromEditModal = window.currentEditBerkasData || ($('#editNamaBerkas').val() && $('#editNamaBerkas').val() !== '');

        if (isFromEditModal) {
            // Jika dari modal edit
            $('#editCroppedImageData').val(base64Image);
            $('#editPreviewImage').attr('src', base64Image);
            $('#editPreviewContainer').show();
            $('#editPreviewPlaceholder').hide();

            // Simpan nilai tipe berkas dan bank
            const namaBerkas = $('#editNamaBerkas').val();
            const dataBerkas = $('#editDataBerkas').val();
            window.savedCropNamaBerkas = namaBerkas;
            window.savedCropDataBerkas = dataBerkas;
        } else {
            // Jika dari modal upload
            $('#croppedImageData').val(base64Image);
            $('#previewImage').attr('src', base64Image);

            // Jangan ambil nilai dari form karena form mungkin sudah di-reset saat modal ditutup
            // Gunakan nilai yang sudah disimpan di window (disimpan sebelum modal ditutup)
            // window.savedCropNamaBerkas dan window.savedCropDataBerkas sudah tersimpan
            // Tidak perlu set ulang di sini, nilai sudah ada di window
        }

        // Tutup modal crop
        $('#modalCropBerkas').modal('hide');

        // Tunggu modal crop tertutup, lalu buka kembali modal yang sesuai
        $('#modalCropBerkas').one('hidden.bs.modal', function() {
            // Tentukan apakah ini dari modal edit atau modal upload
            // Gunakan window.currentEditBerkasData atau cek apakah editNamaBerkas ada nilainya
            const isFromEditModal = window.currentEditBerkasData || ($('#editNamaBerkas').val() && $('#editNamaBerkas').val() !== '');

            if (isFromEditModal) {
                // Jika dari modal edit, kembali ke modal edit
                const savedNamaBerkas = window.savedCropNamaBerkas || $('#editNamaBerkas').val();
                const savedDataBerkas = window.savedCropDataBerkas || $('#editDataBerkas').val();

                // Tampilkan preview dan button update
                $('#editPreviewContainer').show();
                $('#editPreviewPlaceholder').hide();
                $('#btnUpdateBerkas').show();

                // Sembunyikan button crop existing karena sudah ada preview hasil crop
                $('#btnCropExistingImage').hide();

                // Pastikan nilai bank tetap tersimpan untuk edit
                if (savedNamaBerkas === 'Buku Rekening' && savedDataBerkas) {
                    $('#editDataBerkas').val(savedDataBerkas);
                }

                setTimeout(function() {
                    $('#modalEditBerkas').modal('show');
                }, 100);
            } else {
                // Jika dari modal upload, kembali ke modal upload
                // Gunakan nilai yang sudah disimpan SEBELUM crop, jangan ambil dari form
                const savedNamaBerkas = window.savedCropNamaBerkas;
                const savedDataBerkas = window.savedCropDataBerkas;

                console.log('Restore setelah crop - savedNamaBerkas:', savedNamaBerkas, 'savedDataBerkas:', savedDataBerkas);

                // Sembunyikan existing image container dan tampilkan preview hasil crop
                if ($('#existingImageContainer').is(':visible')) {
                    $('#existingImageContainer').hide();
                    $('#btnUseExisting').hide();
                }
                $('#previewContainer').show();
                $('#btnUploadBerkasFromForm').show();

                // Buka kembali modal upload
                setTimeout(function() {
                    // Pastikan nilai bank sudah di-set sebelum modal dibuka
                    // Gunakan nilai yang sudah disimpan
                    const finalNamaBerkas = window.savedCropNamaBerkas;
                    const finalDataBerkas = window.savedCropDataBerkas;

                    console.log('Sebelum buka modal - finalNamaBerkas:', finalNamaBerkas, 'finalDataBerkas:', finalDataBerkas);

                    // Set nilai tipe berkas TERLEBIH DAHULU
                    if (finalNamaBerkas) {
                        $('#uploadNamaBerkas').val(finalNamaBerkas);
                        $('#uploadNamaBerkas').prop('disabled', true);
                    }

                    // Set nilai bank jika Buku Rekening
                    if (finalNamaBerkas === 'Buku Rekening') {
                        $('#dataBerkasGroup').show();
                        $('#uploadDataBerkas').prop('required', true);
                        $('#uploadDataBerkas').prop('disabled', false);
                        if (finalDataBerkas) {
                            $('#uploadDataBerkas').val(finalDataBerkas);
                        }
                    } else {
                        $('#dataBerkasGroup').hide();
                        $('#uploadDataBerkas').prop('required', false);
                        $('#uploadDataBerkas').val('');
                    }

                    $('#modalUploadBerkas').modal('show');

                    // Setelah modal dibuka, pastikan nilai bank tetap tersimpan (triple check)
                    $('#modalUploadBerkas').one('shown.bs.modal', function() {
                        const checkNamaBerkas = window.savedCropNamaBerkas;
                        const checkDataBerkas = window.savedCropDataBerkas;

                        console.log('Setelah modal shown - checkNamaBerkas:', checkNamaBerkas, 'checkDataBerkas:', checkDataBerkas);

                        // Set nilai tipe berkas
                        if (checkNamaBerkas) {
                            $('#uploadNamaBerkas').val(checkNamaBerkas);
                            $('#uploadNamaBerkas').prop('disabled', true);
                        }

                        if (checkNamaBerkas === 'Buku Rekening') {
                            $('#dataBerkasGroup').show();
                            $('#uploadDataBerkas').prop('required', true);
                            $('#uploadDataBerkas').prop('disabled', false);
                            if (checkDataBerkas) {
                                $('#uploadDataBerkas').val(checkDataBerkas);
                            }
                        } else {
                            $('#dataBerkasGroup').hide();
                            $('#uploadDataBerkas').prop('required', false);
                        }
                    });
                }, 100);
            }
        });
    });

    // Function untuk upload berkas dari form
    function uploadBerkasFromForm() {
        const croppedImageData = $('#croppedImageData').val();

        if (!croppedImageData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tidak ada gambar yang akan diupload. Silakan pilih file dan crop terlebih dahulu.'
            });
            return;
        }

        // Validasi form
        const namaBerkas = $('#uploadNamaBerkas').val();
        if (!namaBerkas) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih Tipe Berkas'
            });
            return;
        }

        if (namaBerkas === 'Buku Rekening') {
            const dataBerkas = $('#uploadDataBerkas').val();
            if (!dataBerkas) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih Nama Bank'
                });
                return;
            }
        }

        Swal.fire({
            title: 'Mengupload berkas...',
            text: 'Sedang memproses dan mengupload berkas...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Prepare form data
        const formData = {
            IdGuru: $('#uploadIdGuru').val(),
            NamaBerkas: $('#uploadNamaBerkas').val(),
            DataBerkas: $('#uploadDataBerkas').val() || null,
            berkas_cropped: croppedImageData
        };

        // Jika mode edit, kirim ID berkas yang sedang di-edit
        if (window.currentEditBerkasData && window.currentEditBerkasData.id) {
            formData.editBerkasId = window.currentEditBerkasData.id;
            console.log('Edit mode: editBerkasId =', window.currentEditBerkasData.id);
        } else {
            console.log('New upload mode: no editBerkasId');
        }

        // Upload via AJAX
        $.ajax({
            url: '<?= base_url('backend/guru/uploadBerkas') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengupload berkas'
                });
            }
        });
    }

    // Function untuk menghapus preview dan reset form
    function removePreviewImage() {
        $('#previewContainer').hide();
        $('#btnUploadBerkasFromForm').hide();
        $('#croppedImageData').val('');
        $('#fileBerkas').val('');
        selectedFileBerkas = null;
    }

    // Function untuk melihat daftar berkas
    // Function untuk edit berkas langsung (dari kolom)
    function editBerkasDirect(berkasId, idGuru, namaGuru, namaBerkas, dataBerkas = null) {
        // Ambil detail berkas via AJAX
        $.ajax({
            url: '<?= base_url('backend/guru/getBerkasById') ?>/' + berkasId,
            type: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const berkasData = response.data;

                    // Set data ke modal edit
                    $('#editBerkasId').val(berkasId);
                    $('#editIdGuru').val(idGuru);
                    $('#editNamaGuru').val(namaGuru);
                    $('#editNamaBerkas').val(namaBerkas);

                    // Jika Buku Rekening, tampilkan DataBerkas
                    if (namaBerkas === 'Buku Rekening') {
                        const bankValue = dataBerkas || berkasData.DataBerkas || '';
                        $('#editDataBerkas').val(bankValue);
                        $('#editDataBerkasGroup').show();
                    } else {
                        $('#editDataBerkasGroup').hide();
                    }

                    // Tampilkan gambar existing
                    const imageUrl = '<?= base_url('uploads/berkas/') ?>' + berkasData.NamaFile;
                    $('#editExistingImage').attr('src', imageUrl);

                    // Reset form
                    $('#editFileBerkas').val('');
                    $('#editPreviewContainer').hide();
                    $('#editPreviewPlaceholder').show();
                    $('#btnUpdateBerkas').hide();
                    $('#editCroppedImageData').val('');

                    // Tampilkan button crop existing image
                    $('#btnCropExistingImage').show();

                    // Simpan data untuk digunakan saat update
                    window.currentEditBerkasData = berkasData;

                    // Buka modal edit
                    $('#modalEditBerkas').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat data berkas'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data berkas'
                });
            }
        });
    }

    // Function untuk delete berkas secara langsung
    function deleteBerkasDirect(berkasId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Berkas yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/guru/deleteBerkas') ?>/' + berkasId,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghapus berkas'
                        });
                    }
                });
            }
        });
    }

    // Handle file input change untuk modal edit
    $('#editFileBerkas').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            return;
        }

        // Validasi ukuran file (max 15MB - file akan di-compress setelah crop)
        if (file.size > 15728640) { // 15MB
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Ukuran file terlalu besar. Maksimal 15MB. File akan otomatis di-compress setelah crop.'
            });
            $(this).val('');
            return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tipe file tidak diizinkan. Hanya JPG, JPEG, atau PNG'
            });
            $(this).val('');
            return;
        }

        // Simpan nilai tipe berkas untuk crop
        const namaBerkas = $('#editNamaBerkas').val();
        window.savedCropNamaBerkas = namaBerkas;

        if (namaBerkas === 'Buku Rekening') {
            const dataBerkas = $('#editDataBerkas').val();
            window.savedCropDataBerkas = dataBerkas;
        }

        // Tunggu ImageUploadHelper tersedia
        waitForImageUploadHelper(function() {
            if (!window.ImageUploadHelper || !window.ImageUploadHelper.resizeImageBeforeCrop) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ImageUploadHelper tidak ditemukan. Pastikan image-upload-helper.js sudah di-include.'
                });
                return;
            }

            // Resize sebelum crop
            const maxDimension = 2000;
            const resizeQuality = 0.85;

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses gambar...',
                text: 'Sedang resize dan compress gambar...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            window.ImageUploadHelper.resizeImageBeforeCrop(file, maxDimension, maxDimension, resizeQuality, function(processedFile) {
                // Hitung ukuran file setelah resize
                const originalSize = (file.size / 1024 / 1024).toFixed(2); // MB
                const resizedSize = (processedFile.size / 1024 / 1024).toFixed(2); // MB
                const sizeReduction = ((1 - processedFile.size / file.size) * 100).toFixed(1); // Persentase

                // Tutup loading dan tampilkan info ukuran
                Swal.close();
                Swal.fire({
                    icon: 'info',
                    title: 'Gambar berhasil di-resize',
                    html: `Ukuran file:<br>
                           <strong>Sebelum:</strong> ${originalSize} MB<br>
                           <strong>Sesudah:</strong> ${resizedSize} MB<br>
                           <strong>Pengurangan:</strong> ${sizeReduction}%`,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Langsung buka modal crop tanpa preview di form
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        showCropModalBerkas(e.target.result, namaBerkas);
                    };
                    reader.readAsDataURL(processedFile);
                });
            });
        });
    });

    // Function untuk crop gambar existing di modal edit
    function cropExistingImageInEdit() {
        const existingImageSrc = $('#editExistingImage').attr('src');
        if (!existingImageSrc) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gambar tidak ditemukan'
            });
            return;
        }

        // Simpan nilai tipe berkas dan bank untuk digunakan saat crop
        const namaBerkas = $('#editNamaBerkas').val();
        window.savedCropNamaBerkas = namaBerkas;

        if (namaBerkas === 'Buku Rekening') {
            const dataBerkas = $('#editDataBerkas').val();
            window.savedCropDataBerkas = dataBerkas;
        }

        // Buka modal crop dengan gambar existing
        showCropModalBerkas(existingImageSrc, namaBerkas);
    }

    // Function untuk menghapus preview di modal edit
    function removeEditPreviewImage() {
        $('#editPreviewContainer').hide();
        $('#editPreviewPlaceholder').show();
        $('#btnUpdateBerkas').hide();
        $('#editCroppedImageData').val('');
        $('#editFileBerkas').val('');
        $('#btnCropExistingImage').show();
    }

    // Function untuk update berkas dari form edit
    function updateBerkasFromForm() {
        const croppedImageData = $('#editCroppedImageData').val();
        const editBerkasId = $('#editBerkasId').val();

        // Jika tidak ada gambar baru yang di-crop, gunakan gambar existing
        if (!croppedImageData) {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: 'Tidak ada perubahan. Gambar tetap menggunakan yang saat ini.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                $('#modalEditBerkas').modal('hide');
            });
            return;
        }

        Swal.fire({
            title: 'Mengupdate berkas...',
            text: 'Sedang memproses dan mengupdate berkas...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Prepare form data
        const formData = {
            IdGuru: $('#editIdGuru').val(),
            NamaBerkas: $('#editNamaBerkas').val(),
            DataBerkas: $('#editDataBerkas').val() || null,
            berkas_cropped: croppedImageData,
            editBerkasId: editBerkasId
        };

        // Upload via AJAX
        $.ajax({
            url: '<?= base_url('backend/guru/uploadBerkas') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengupdate berkas'
                });
            }
        });
    }

    // Function untuk delete berkas (legacy, redirect ke deleteBerkasDirect)
    function deleteBerkas(berkasId) {
        deleteBerkasDirect(berkasId);
    }

    // Helper function untuk menunggu ImageUploadHelper
    function waitForImageUploadHelper(callback, maxAttempts = 50) {
        let attempts = 0;
        const checkInterval = setInterval(function() {
            if (window.ImageUploadHelper && window.ImageUploadHelper.resizeImageBeforeCrop) {
                clearInterval(checkInterval);
                callback();
            } else {
                attempts++;
                if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    console.error('ImageUploadHelper tidak ditemukan setelah ' + maxAttempts + ' attempts');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: 'ImageUploadHelper tidak ditemukan. Pastikan:<br>' +
                            '1. File helpers/js/image-upload-helper.js dapat diakses<br>' +
                            '2. Route helpers/js/(:segment) sudah terdaftar di Routes.php<br>' +
                            '3. Controller Helpers.php sudah ter-deploy<br>' +
                            '4. Refresh halaman dan coba lagi'
                    });
                }
            }
        }, 100);
    }

    // Cleanup cropper saat modal ditutup
    $('#modalCropBerkas').on('hidden.bs.modal', function() {
        if (cropperBerkas) {
            cropperBerkas.destroy();
            cropperBerkas = null;
        }
    });

    $('#modalUploadBerkas').on('hidden.bs.modal', function() {
        // Jangan reset nilai jika sedang membuka modal crop
        // Cek apakah modal crop sedang dibuka atau akan dibuka
        const isOpeningCrop = $('#modalCropBerkas').is(':visible') || window.isOpeningCropModal;

        if (cropperBerkas) {
            cropperBerkas.destroy();
            cropperBerkas = null;
        }
        selectedFileBerkas = null;
        $('#fileBerkas').val('');
        $('#fileBerkas').prop('required', false);
        $('#previewContainer').hide();
        $('#btnUploadBerkasFromForm').hide();
        $('#existingImageContainer').hide();
        $('#btnUseExisting').hide();
        $('#editModeInfo').hide();

        // Jangan reset nilai tipe berkas dan bank jika sedang membuka modal crop
        if (!isOpeningCrop) {
            $('#dataBerkasGroup').hide();
            $('#uploadNamaBerkas').prop('disabled', false);
            $('#uploadNamaBerkas').val('');
            $('#uploadDataBerkas').val('');
            $('#uploadDataBerkas').prop('disabled', false);
            $('#uploadDataBerkas').prop('required', false);
            window.currentEditBerkasData = null;
            // Clear saved crop data hanya jika tidak sedang membuka modal crop
            window.savedCropNamaBerkas = null;
            window.savedCropDataBerkas = null;
        }

        // Reset flag
        window.isOpeningCropModal = false;
    });

    // Reset modal edit saat ditutup
    $('#modalEditBerkas').on('hidden.bs.modal', function() {
        if (cropperBerkas) {
            cropperBerkas.destroy();
            cropperBerkas = null;
        }
        $('#editFileBerkas').val('');
        $('#editPreviewContainer').hide();
        $('#editPreviewPlaceholder').show();
        $('#btnUpdateBerkas').hide();
        $('#editCroppedImageData').val('');
        $('#btnCropExistingImage').show();
        window.currentEditBerkasData = null;
        // Clear saved crop data
        window.savedCropNamaBerkas = null;
        window.savedCropDataBerkas = null;
    });
</script>
<?= $this->endSection(); ?>