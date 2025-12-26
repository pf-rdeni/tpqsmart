<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Berkas Lampiran Guru</h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/guru/pengajuanInsentif') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-file-invoice"></i> Pengajuan Insentif
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php if (in_groups('Admin')): ?>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filterTpq" class="form-label">Filter TPQ</label>
                        <select id="filterTpq" class="form-control form-control-sm" multiple="multiple">
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
                                <small style="color: #666;">
                                    BPR:
                                    <?php if (!empty($guru['NoRekBpr'])): ?>
                                        <span style="color: #007bff;"><?= esc($guru['NoRekBpr']) ?></span>
                                    <?php else: ?>
                                        <span style="color: #dc3545;">xxxxxxxxxx</span>
                                    <?php endif; ?>
                                    <br>
                                    BRK:
                                    <?php if (!empty($guru['NoRekRiauKepri'])): ?>
                                        <span style="color: #007bff;"><?= esc($guru['NoRekRiauKepri']) ?></span>
                                    <?php else: ?>
                                        <span style="color: #dc3545;">xxxxxxxxxx</span>
                                    <?php endif; ?>
                                </small><br>
                                <small style="color: #666;">TPQ: <?= esc($namaTpq) ?></small>
                            </td>
                            <td>
                                <?php if (isset($berkas['KTP'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= base_url('uploads/berkas/' . $berkas['KTP']['NamaFile']) ?>"
                                            alt="KTP Preview"
                                            class="preview-image"
                                            data-image-url="<?= base_url('uploads/berkas/' . $berkas['KTP']['NamaFile']) ?>"
                                            style="max-width: 150px; max-height: 100px; width: auto; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px; cursor: pointer;"
                                            title="Klik sekali untuk memperbesar, double click untuk membuka di tab baru">
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
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
                                    <div class="mb-2">
                                        <img src="<?= base_url('uploads/berkas/' . $berkas['KK']['NamaFile']) ?>"
                                            alt="KK Preview"
                                            class="preview-image"
                                            data-image-url="<?= base_url('uploads/berkas/' . $berkas['KK']['NamaFile']) ?>"
                                            style="max-width: 150px; max-height: 100px; width: auto; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px; cursor: pointer;"
                                            title="Klik sekali untuk memperbesar, double click untuk membuka di tab baru">
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
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
                                    // Pisahkan data BPR dan BRK
                                    $bprData = null;
                                    $brkData = null;
                                    foreach ($berkas['Buku Rekening'] as $rekening) {
                                        $dataBerkas = $rekening['DataBerkas'] ?? '';
                                        if ($dataBerkas === 'BPR') {
                                            $bprData = $rekening;
                                        } elseif ($dataBerkas === 'BRK') {
                                            $brkData = $rekening;
                                        }
                                    }
                                    ?>
                                    <div class="row g-2">
                                        <!-- Kolom BPR -->
                                        <div class="col-6">
                                            <?php if ($bprData): ?>
                                                <div class="mb-1">
                                                    <img src="<?= base_url('uploads/berkas/' . $bprData['NamaFile']) ?>"
                                                        alt="BPR Preview"
                                                        class="preview-image"
                                                        data-image-url="<?= base_url('uploads/berkas/' . $bprData['NamaFile']) ?>"
                                                        data-id-guru="<?= esc($guru['IdGuru']) ?>"
                                                        data-bank-type="BPR"
                                                        data-no-rek="<?= esc($guru['NoRekBpr'] ?? '') ?>"
                                                        style="max-width: 100%; max-height: 100px; width: auto; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px; cursor: pointer;"
                                                        title="Klik sekali untuk memperbesar, double click untuk membuka di tab baru">
                                                </div>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BPR:</strong>
                                                    <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($bprData['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BPR')" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($bprData['id']) ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BPR:</strong>
                                                    <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BPR')" title="Upload BPR">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Kolom BRK -->
                                        <div class="col-6">
                                            <?php if ($brkData): ?>
                                                <div class="mb-1">
                                                    <img src="<?= base_url('uploads/berkas/' . $brkData['NamaFile']) ?>"
                                                        alt="BRK Preview"
                                                        class="preview-image"
                                                        data-image-url="<?= base_url('uploads/berkas/' . $brkData['NamaFile']) ?>"
                                                        data-id-guru="<?= esc($guru['IdGuru']) ?>"
                                                        data-bank-type="BRK"
                                                        data-no-rek="<?= esc($guru['NoRekRiauKepri'] ?? '') ?>"
                                                        style="max-width: 100%; max-height: 100px; width: auto; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px; cursor: pointer;"
                                                        title="Klik sekali untuk memperbesar, double click untuk membuka di tab baru">
                                                </div>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BRK:</strong>
                                                    <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($brkData['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BRK')" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($brkData['id']) ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BRK:</strong>
                                                    <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BRK')" title="Upload BRK">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif (isset($berkas['Buku Rekening'])): ?>
                                    <!-- Backward compatibility: jika masih single file -->
                                    <?php
                                    $dataBerkas = $berkas['Buku Rekening']['DataBerkas'] ?? '';
                                    $isBpr = ($dataBerkas === 'BPR');
                                    $isBrk = ($dataBerkas === 'BRK');
                                    ?>
                                    <div class="row g-2">
                                        <!-- Kolom BPR -->
                                        <div class="col-6">
                                            <?php if ($isBpr): ?>
                                                <div class="mb-1">
                                                    <img src="<?= base_url('uploads/berkas/' . $berkas['Buku Rekening']['NamaFile']) ?>"
                                                        alt="BPR Preview"
                                                        class="preview-image"
                                                        data-image-url="<?= base_url('uploads/berkas/' . $berkas['Buku Rekening']['NamaFile']) ?>"
                                                        data-id-guru="<?= esc($guru['IdGuru']) ?>"
                                                        data-bank-type="BPR"
                                                        data-no-rek="<?= esc($guru['NoRekBpr'] ?? '') ?>"
                                                        style="max-width: 100%; max-height: 100px; width: auto; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px; cursor: pointer;"
                                                        title="Klik sekali untuk memperbesar, double click untuk membuka di tab baru">
                                                </div>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BPR:</strong>
                                                    <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($berkas['Buku Rekening']['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BPR')" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($berkas['Buku Rekening']['id']) ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BPR:</strong>
                                                    <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BPR')" title="Upload BPR">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Kolom BRK -->
                                        <div class="col-6">
                                            <?php if ($isBrk): ?>
                                                <div class="mb-1">
                                                    <img src="<?= base_url('uploads/berkas/' . $berkas['Buku Rekening']['NamaFile']) ?>"
                                                        alt="BRK Preview"
                                                        class="preview-image"
                                                        data-image-url="<?= base_url('uploads/berkas/' . $berkas['Buku Rekening']['NamaFile']) ?>"
                                                        data-id-guru="<?= esc($guru['IdGuru']) ?>"
                                                        data-bank-type="BRK"
                                                        data-no-rek="<?= esc($guru['NoRekRiauKepri'] ?? '') ?>"
                                                        style="max-width: 100%; max-height: 100px; width: auto; height: auto; border: 1px solid #ddd; padding: 2px; border-radius: 4px; cursor: pointer;"
                                                        title="Klik sekali untuk memperbesar, double click untuk membuka di tab baru">
                                                </div>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BRK:</strong>
                                                    <button class="btn btn-sm btn-warning p-1" onclick="editBerkasDirect(<?= esc($berkas['Buku Rekening']['id']) ?>, '<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BRK')" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger p-1" onclick="deleteBerkasDirect(<?= esc($berkas['Buku Rekening']['id']) ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <strong>BRK:</strong>
                                                    <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BRK')" title="Upload BRK">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="row g-2">
                                        <!-- Kolom BPR -->
                                        <div class="col-6">
                                            <strong>BPR:</strong>
                                            <div class="mt-1">
                                                <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BPR')" title="Upload BPR">
                                                    <i class="fas fa-upload"></i> Upload
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Kolom BRK -->
                                        <div class="col-6">
                                            <strong>BRK:</strong>
                                            <div class="mt-1">
                                                <button class="btn btn-sm btn-primary" onclick="openUploadModalWithType('<?= esc($guru['IdGuru']) ?>', '<?= esc($guru['Nama']) ?>', 'Buku Rekening', 'BRK')" title="Upload BRK">
                                                    <i class="fas fa-upload"></i> Upload
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content" style="height: calc(100vh - 40px); max-height: 900px; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Upload Berkas Lampiran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="flex: 1; overflow-y: auto; min-height: 0;">
                <form id="formUploadBerkas">
                    <input type="hidden" id="uploadIdGuru" name="IdGuru">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Nama Guru</label>
                                <input type="text" id="uploadNamaGuru" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="uploadNamaBerkas">Tipe Berkas <span class="text-danger">*</span></label>
                                <select class="form-control" id="uploadNamaBerkas" name="NamaBerkas" required>
                                    <option value="">Pilih Tipe Berkas</option>
                                    <option value="KTP">KTP</option>
                                    <option value="KK">KK (Kartu Keluarga)</option>
                                    <option value="Buku Rekening">Buku Rekening</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="dataBerkasGroup" style="display: none;">
                                <label for="uploadDataBerkas">Nama Bank <span class="text-danger">*</span></label>
                                <select class="form-control" id="uploadDataBerkas" name="DataBerkas">
                                    <option value="">Pilih Nama Bank</option>
                                    <option value="BPR">BPR</option>
                                    <option value="BRK">BRK</option>
                                </select>
                                <small class="form-text text-muted">Pilih bank untuk buku rekening yang akan diupload</small>
                            </div>
                        </div>
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
                        <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="existingImage" src="" alt="Gambar Saat Ini" style="max-width: 100%; max-height: 230px; object-fit: contain;">
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
                        <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 230px; object-fit: contain;">
                        </div>
                        <button type="button" class="btn btn-sm btn-warning" onclick="removePreviewImage()">
                            <i class="fas fa-redo"></i> Pilih File Lain
                        </button>
                    </div>
                    <input type="hidden" id="croppedImageData" name="croppedImageData">
                </form>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; border-top: 1px solid #dee2e6;">
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
            <div class="alert alert-warning m-0" style="flex-shrink: 0; border-radius: 0; padding: 10px 15px; margin-bottom: 0 !important;">
                <small>
                    <i class="fas fa-info-circle"></i> <strong>Panduan:</strong>
                    Geser (drag) untuk memindahkan area crop • Resize untuk mengubah ukuran •
                    Gunakan tombol Putar Kiri/Kanan untuk memutar gambar •
                    <span id="perspectiveModeInfo">Aspect ratio sudah fixed sesuai jenis berkas</span> •
                    <span id="perspectiveModeActive" style="display: none; color: #dc3545; font-weight: bold;">Mode Perspektif Aktif: Drag titik sudut untuk memperbaiki perspektif miring</span> •
                    Klik <strong>Selesai</strong> untuk menyimpan
                </small>
            </div>
            <div class="modal-body" style="flex: 1; overflow: hidden; padding: 15px; display: flex; align-items: center; justify-content: center;">
                <div id="cropContainerBerkas" style="width: 100%; height: 100%; max-height: calc(100vh - 200px); overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                    <img id="imageToCropBerkas" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    <!-- Perspective Overlay -->
                    <div id="perspectiveOverlay">
                        <canvas id="perspectiveCanvas"></canvas>
                        <div class="perspective-line" id="perspectiveLine1"></div>
                        <div class="perspective-line" id="perspectiveLine2"></div>
                        <div class="perspective-line" id="perspectiveLine3"></div>
                        <div class="perspective-line" id="perspectiveLine4"></div>
                        <div class="perspective-handle" id="handleTL" data-handle="tl" style="top: 20%; left: 20%;"></div>
                        <div class="perspective-handle" id="handleTR" data-handle="tr" style="top: 20%; right: 20%;"></div>
                        <div class="perspective-handle" id="handleBL" data-handle="bl" style="bottom: 20%; left: 20%;"></div>
                        <div class="perspective-handle" id="handleBR" data-handle="br" style="bottom: 20%; right: 20%;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; border-top: 1px solid #dee2e6;">
                <div class="mr-auto">
                    <button type="button" class="btn btn-info btn-sm" id="btnRotateLeft" title="Putar 90° ke kiri">
                        <i class="fas fa-undo"></i> Putar Kiri
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="btnRotateRight" title="Putar 90° ke kanan">
                        <i class="fas fa-redo"></i> Putar Kanan
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnTogglePerspective" title="Aktifkan Mode Perspektif untuk memperbaiki gambar miring">
                        <i class="fas fa-project-diagram"></i> Mode Perspektif
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUploadBerkas">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Berkas (Khusus untuk Edit) -->
<div class="modal fade" id="modalEditBerkas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content" style="height: calc(100vh - 40px); max-height: 900px; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Edit Berkas Lampiran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="flex: 1; overflow-y: auto; min-height: 0;">
                <form id="formEditBerkas">
                    <input type="hidden" id="editBerkasId" name="editBerkasId">
                    <input type="hidden" id="editIdGuru" name="IdGuru">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Nama Guru</label>
                                <input type="text" id="editNamaGuru" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Tipe Berkas</label>
                                <input type="text" id="editNamaBerkas" class="form-control" readonly>
                            </div>
                            <div class="col-md-4" id="editDataBerkasGroup" style="display: none;">
                                <label>Nama Bank</label>
                                <input type="text" id="editDataBerkas" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editFileBerkas">Ganti dengan File Baru</label>
                        <div class="custom-file-wrapper" style="position: relative;">
                            <input type="file" class="form-control-file" id="editFileBerkas" accept="image/jpeg,image/jpg,image/png" style="position: absolute; opacity: 0; width: 0; height: 0; overflow: hidden;">
                            <button type="button" class="btn btn-primary btn-sm" id="btnBrowseEditFile" onclick="document.getElementById('editFileBerkas').click()">
                                <i class="fas fa-upload"></i> Pilih File
                            </button>
                            <span id="editFileNameDisplay" class="ml-2" style="font-size: 14px; color: #666;"></span>
                        </div>
                        <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 15MB (akan di-compress setelah crop). Kosongkan jika tidak ingin mengganti.</small>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Gambar Saat Ini</label>
                                <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img id="editExistingImage" src="" alt="Gambar Saat Ini" style="max-width: 100%; max-height: 230px; object-fit: contain;">
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-sm btn-warning" id="btnCropExistingImage" onclick="cropExistingImageInEdit()">
                                        <i class="fas fa-edit"></i> Edit Gambar saat ini
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Preview Hasil Edit</label>
                                <div id="editPreviewContainer" style="display: none;">
                                    <div class="text-center mb-2" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <img id="editPreviewImage" src="" alt="Preview" style="max-width: 100%; max-height: 230px; object-fit: contain;">
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning" onclick="removeEditPreviewImage()">
                                            <i class="fas fa-redo"></i> Pilih File Lain
                                        </button>
                                    </div>
                                </div>
                                <div id="editPreviewPlaceholder" class="text-center" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; min-height: 200px; max-height: 250px; display: flex; align-items: center; justify-content: center; color: #999;">
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
            <div class="modal-footer" style="flex-shrink: 0; border-top: 1px solid #dee2e6;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUpdateBerkas" style="display: none;" onclick="updateBerkasFromForm()">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk memperbesar gambar -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" role="dialog" aria-labelledby="modalImagePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagePreviewLabel">Preview Gambar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <!-- Form untuk update no rekening (hanya muncul untuk BPR/BRK) -->
                <div id="noRekFormContainer" style="display: none;">
                    <form id="formUpdateNoRek">
                        <input type="hidden" id="updateNoRekIdGuru" name="IdGuru">
                        <input type="hidden" id="updateNoRekBankType" name="BankType">
                        <div class="form-group">
                            <label for="updateNoRekInput" id="updateNoRekLabel">No. Rekening</label>
                            <input type="text" class="form-control" id="updateNoRekInput" name="NoRek" placeholder="Masukkan nomor rekening" maxlength="11" inputmode="numeric" pattern="[0-9]*">
                            <small class="form-text text-muted" id="updateNoRekHelp">Masukkan nomor rekening sesuai dengan buku rekening di atas</small>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-save"></i> Simpan No. Rekening
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center mb-3">
                    <img id="previewEnlargedImage" src="" alt="Preview" style="max-width: 100%; max-height: 70vh; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style untuk modal edit berkas */
    #modalEditBerkas .modal-dialog {
        max-width: 900px;
        margin: 20px auto;
    }

    #modalEditBerkas .modal-content {
        height: calc(100vh - 40px);
        max-height: 900px;
        display: flex;
        flex-direction: column;
    }

    #modalEditBerkas .modal-body {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
        padding: 15px;
    }

    #modalEditBerkas .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        padding: 10px 15px;
    }

    /* Responsive untuk layar kecil */
    @media (max-height: 700px) {
        #modalEditBerkas .modal-content {
            height: calc(100vh - 20px);
            max-height: 680px;
        }
    }

    /* Style untuk modal upload berkas */
    #modalUploadBerkas .modal-dialog {
        max-width: 900px;
        margin: 20px auto;
    }

    #modalUploadBerkas .modal-content {
        height: calc(100vh - 40px);
        max-height: 900px;
        display: flex;
        flex-direction: column;
    }

    #modalUploadBerkas .modal-body {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
        padding: 15px;
    }

    #modalUploadBerkas .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid #dee2e6;
        padding: 10px 15px;
    }

    /* Responsive untuk layar kecil */
    @media (max-height: 700px) {
        #modalUploadBerkas .modal-content {
            height: calc(100vh - 20px);
            max-height: 680px;
        }
    }

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

    /* Style untuk perspective overlay */
    #perspectiveOverlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1000;
        display: none;
    }

    #perspectiveOverlay.active {
        display: block;
        pointer-events: all;
    }

    .perspective-handle {
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: #007bff;
        border: 2px solid #fff;
        border-radius: 50%;
        cursor: move;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        pointer-events: all;
        z-index: 1001;
    }

    .perspective-handle:hover {
        background-color: #0056b3;
        transform: scale(1.2);
    }

    .perspective-line {
        position: absolute;
        border: 2px dashed #007bff;
        pointer-events: none;
        z-index: 999;
    }

    #perspectiveCanvas {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: 998;
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
    let perspectiveMode = false;
    let perspectiveHandles = {
        tl: {
            x: 20,
            y: 20
        },
        tr: {
            x: 80,
            y: 20
        },
        bl: {
            x: 20,
            y: 80
        },
        br: {
            x: 80,
            y: 80
        }
    };
    let perspectiveCanvas = null;
    let perspectiveCtx = null;
    let perspectiveImage = null;
    let currentDraggedHandle = null;
    let cropBoxBounds = null;

    // Inisialisasi DataTable
    document.addEventListener('DOMContentLoaded', function() {
        const table = initializeDataTableScrollX("#tabelBerkasLampiran", [], {
            "pageLength": 25,
            "lengthChange": true
        });

        <?php if (in_groups('Admin')): ?>
            // Filter TPQ untuk Admin dengan Select2 Multiple
            const filterTpq = $('#filterTpq');
            let customFilterFunction = null;
            const filterStorageKey = 'berkasLampiran_filter_tpq';

            // Inisialisasi Select2 untuk filter TPQ (multiple select)
            filterTpq.select2({
                placeholder: 'Pilih TPQ (bisa pilih beberapa)...',
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                language: 'id',
                closeOnSelect: false // Tidak menutup dropdown saat memilih (untuk multiple selection)
            });

            // Fungsi untuk menyimpan filter TPQ ke localStorage
            function saveFilterTpq() {
                const selectedTpq = filterTpq.val() || [];
                // Simpan sebagai JSON array
                localStorage.setItem(filterStorageKey, JSON.stringify(selectedTpq));
            }

            // Fungsi untuk memuat filter TPQ dari localStorage
            function loadFilterTpq() {
                try {
                    const savedTpqStr = localStorage.getItem(filterStorageKey);
                    if (savedTpqStr !== null && savedTpqStr !== '') {
                        let savedTpq = [];
                        try {
                            // Coba parse sebagai JSON array
                            savedTpq = JSON.parse(savedTpqStr);
                            if (!Array.isArray(savedTpq)) {
                                // Jika bukan array, coba sebagai single value (backward compatibility)
                                savedTpq = savedTpqStr ? [savedTpqStr] : [];
                            }
                        } catch (e) {
                            // Jika parse gagal, anggap sebagai single value (backward compatibility)
                            savedTpq = savedTpqStr ? [savedTpqStr] : [];
                        }

                        if (savedTpq.length > 0) {
                            // Validasi bahwa semua option ada
                            const validValues = [];
                            savedTpq.forEach(function(value) {
                                const optionExists = filterTpq.find('option[value="' + value + '"]').length > 0;
                                if (optionExists) {
                                    validValues.push(value);
                                }
                            });

                            if (validValues.length > 0) {
                                filterTpq.val(validValues).trigger('change.select2');
                                // Trigger change event untuk menerapkan filter setelah Select2 siap
                                setTimeout(function() {
                                    filterTpq.trigger('change');
                                }, 100);
                            }
                        }
                    }
                } catch (e) {
                    console.error('Error loading filter TPQ from localStorage:', e);
                }
            }

            // Load filter saat halaman dimuat (setelah Select2 siap)
            setTimeout(function() {
                loadFilterTpq();
            }, 200);

            filterTpq.on('change', function() {
                const selectedTpq = $(this).val() || [];

                // Simpan filter ke localStorage
                saveFilterTpq();

                // Hapus custom filter function yang lama jika ada
                if (customFilterFunction !== null) {
                    const searchFunctions = $.fn.dataTable.ext.search;
                    for (let i = searchFunctions.length - 1; i >= 0; i--) {
                        if (searchFunctions[i] === customFilterFunction) {
                            searchFunctions.splice(i, 1);
                            break;
                        }
                    }
                    customFilterFunction = null;
                }

                // Jika ada filter yang dipilih
                if (selectedTpq.length > 0) {
                    // Pastikan selectedTpq adalah array
                    const selectedTpqArray = Array.isArray(selectedTpq) ? selectedTpq : [selectedTpq];

                    customFilterFunction = function(settings, data, dataIndex) {
                        if (!settings || !settings.nTable || settings.nTable.id !== 'tabelBerkasLampiran') {
                            return true;
                        }

                        try {
                            const row = table.row(dataIndex).node();
                            if (!row) {
                                return true;
                            }

                            const rowIdTpq = $(row).attr('data-idtpq') || '';
                            // Cek apakah rowIdTpq ada dalam array selectedTpqArray
                            return selectedTpqArray.indexOf(rowIdTpq) !== -1;
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

        // Handle preview image: single click untuk memperbesar, double click untuk buka di tab baru
        $(document).on('click', '.preview-image', function(e) {
            const imageUrl = $(this).data('image-url');
            if (!imageUrl) return;

            // Delay untuk membedakan single click dan double click
            const $img = $(this);
            if ($img.data('clickTimer')) {
                clearTimeout($img.data('clickTimer'));
                $img.removeData('clickTimer');

                // Ini adalah double click
                window.open(imageUrl, '_blank');
            } else {
                // Set timer untuk single click
                const timer = setTimeout(function() {
                    // Single click: buka modal dengan gambar diperbesar
                    $('#previewEnlargedImage').attr('src', imageUrl);

                    // Cek apakah ini gambar BPR atau BRK
                    const idGuru = $img.data('id-guru');
                    const bankType = $img.data('bank-type');
                    const noRek = $img.data('no-rek') || '';

                    if (bankType && (bankType === 'BPR' || bankType === 'BRK')) {
                        // Tampilkan form no rekening
                        $('#noRekFormContainer').show();
                        $('#updateNoRekIdGuru').val(idGuru);
                        $('#updateNoRekBankType').val(bankType);
                        $('#updateNoRekInput').val(noRek);

                        // Update label dan help text berdasarkan bank type
                        if (bankType === 'BPR') {
                            $('#updateNoRekLabel').text('No. Rekening BPR (11 digit)');
                            $('#updateNoRekHelp').text('Masukkan 11 digit nomor rekening BPR (hanya angka)');
                            $('#updateNoRekInput').attr('maxlength', '11');
                        } else {
                            $('#updateNoRekLabel').text('No. Rekening BRK (10 digit)');
                            $('#updateNoRekHelp').text('Masukkan 10 digit nomor rekening BRK (hanya angka)');
                            $('#updateNoRekInput').attr('maxlength', '10');
                        }
                    } else {
                        // Sembunyikan form untuk KTP/KK
                        $('#noRekFormContainer').hide();
                    }

                    $('#modalImagePreview').modal('show');
                    $img.removeData('clickTimer');
                }, 250); // Delay 250ms untuk membedakan dengan double click

                $img.data('clickTimer', timer);
            }
        });
    });

    // Function untuk membuka modal upload
    // Function untuk membuka modal upload dengan tipe berkas yang sudah dipilih
    function openUploadModalWithType(idGuru, namaGuru, namaBerkas, dataBerkas = null) {
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
            // Jika Buku Rekening, gunakan parameter dataBerkas jika ada, atau ambil dari existing
            if (dataBerkas) {
                window.savedCropDataBerkas = dataBerkas;
            } else {
                const existingDataBerkas = $('#uploadDataBerkas').val();
                if (existingDataBerkas) {
                    window.savedCropDataBerkas = existingDataBerkas;
                } else {
                    window.savedCropDataBerkas = null;
                }
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
            // Jika dataBerkas diberikan sebagai parameter, set dan disable dropdown
            if (dataBerkas) {
                $('#uploadDataBerkas').val(dataBerkas);
                $('#uploadDataBerkas').prop('disabled', true);
            } else {
                $('#uploadDataBerkas').prop('disabled', false);
                // Set nilai bank jika sudah ada di window
                if (window.savedCropDataBerkas) {
                    $('#uploadDataBerkas').val(window.savedCropDataBerkas);
                }
            }
        } else {
            $('#dataBerkasGroup').hide();
            $('#uploadDataBerkas').prop('required', false);
            $('#uploadDataBerkas').val('');
            $('#uploadDataBerkas').prop('disabled', false);
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

                // Tutup loading dan langsung buka modal crop tanpa menampilkan info resize
                Swal.close();
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
            // Disable tombol rotate sampai cropper siap
            $('#btnRotateLeft').prop('disabled', true);
            $('#btnRotateRight').prop('disabled', true);

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
                            // KK: A4 Landscape (297mm x 210mm = 1.414) (FIXED)
                            aspectRatio = 297 / 210; // = 1.414 (A4 Landscape)
                            aspectRatioFixed = true; // Fixed ratio
                        } else if (namaBerkas === 'Buku Rekening') {
                            // Buku Rekening: A4 Portrait (210mm x 297mm = 0.707) (FIXED)
                            aspectRatio = 210 / 297; // = 0.707 (A4 Portrait)
                            aspectRatioFixed = true; // Fixed ratio
                        }
                        // Untuk lainnya, tetap NaN (free aspect ratio)

                        // Simpan namaBerkas, aspectRatio, dan aspectRatioFixed untuk digunakan di ready callback dan rotate
                        const cropNamaBerkas = namaBerkas;
                        const cropAspectRatio = aspectRatio;
                        const cropAspectRatioFixed = aspectRatioFixed;

                        // Simpan aspect ratio ke window untuk digunakan saat rotate
                        window.currentCropAspectRatio = aspectRatio;
                        window.currentCropAspectRatioFixed = aspectRatioFixed;

                        cropperBerkas = new Cropper(imageElement, {
                            aspectRatio: aspectRatio, // Fixed untuk KTP, KK, dan Buku Rekening
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

                                // Enable tombol rotate setelah cropper siap
                                $('#btnRotateLeft').prop('disabled', false);
                                $('#btnRotateRight').prop('disabled', false);
                                $('#btnTogglePerspective').prop('disabled', false);

                                // Initialize perspective canvas
                                initPerspectiveCanvas();

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

    // Function untuk rotate gambar ke kiri (90 derajat counterclockwise)
    $('#btnRotateLeft').on('click', function() {
        if (cropperBerkas) {
            cropperBerkas.rotate(-90);
            // Setelah rotate, sesuaikan crop box dengan ukuran gambar yang baru
            setTimeout(function() {
                if (cropperBerkas) {
                    try {
                        const canvasData = cropperBerkas.getCanvasData();
                        const containerData = cropperBerkas.getContainerData();

                        if (canvasData && containerData) {
                            // Jika aspect ratio fixed, pastikan crop box mengikuti aspect ratio
                            const aspectRatio = window.currentCropAspectRatio;
                            const isFixedRatio = window.currentCropAspectRatioFixed && !isNaN(aspectRatio);

                            let cropBoxWidth, cropBoxHeight;

                            if (isFixedRatio) {
                                // Untuk fixed ratio, hitung berdasarkan aspect ratio
                                const maxSize = Math.min(canvasData.width * 0.8, canvasData.height * 0.8, containerData.width, containerData.height);

                                if (canvasData.width > canvasData.height) {
                                    // Landscape: width lebih besar
                                    cropBoxWidth = Math.min(maxSize, containerData.width);
                                    cropBoxHeight = cropBoxWidth / aspectRatio;

                                    // Jika height terlalu besar, sesuaikan
                                    if (cropBoxHeight > containerData.height) {
                                        cropBoxHeight = Math.min(containerData.height, canvasData.height * 0.8);
                                        cropBoxWidth = cropBoxHeight * aspectRatio;
                                    }
                                } else {
                                    // Portrait: height lebih besar
                                    cropBoxHeight = Math.min(maxSize, containerData.height);
                                    cropBoxWidth = cropBoxHeight * aspectRatio;

                                    // Jika width terlalu besar, sesuaikan
                                    if (cropBoxWidth > containerData.width) {
                                        cropBoxWidth = Math.min(containerData.width, canvasData.width * 0.8);
                                        cropBoxHeight = cropBoxWidth / aspectRatio;
                                    }
                                }
                            } else {
                                // Free aspect ratio: hitung berdasarkan 80% dari canvas
                                cropBoxWidth = Math.min(canvasData.width * 0.8, containerData.width);
                                cropBoxHeight = Math.min(canvasData.height * 0.8, containerData.height);
                            }

                            // Pastikan aspect ratio tetap terjaga untuk fixed ratio
                            if (isFixedRatio && !isNaN(aspectRatio)) {
                                cropperBerkas.setAspectRatio(aspectRatio);
                            }

                            // Set posisi crop box di tengah container
                            const cropBoxLeft = (containerData.width - cropBoxWidth) / 2;
                            const cropBoxTop = (containerData.height - cropBoxHeight) / 2;

                            cropperBerkas.setCropBoxData({
                                left: cropBoxLeft,
                                top: cropBoxTop,
                                width: cropBoxWidth,
                                height: cropBoxHeight
                            });
                        }
                    } catch (e) {
                        console.log('Error adjusting crop box after rotate:', e);
                    }
                }
            }, 100);
        }
    });

    // Function untuk rotate gambar ke kanan (90 derajat clockwise)
    $('#btnRotateRight').on('click', function() {
        if (cropperBerkas) {
            cropperBerkas.rotate(90);
            // Setelah rotate, sesuaikan crop box dengan ukuran gambar yang baru
            setTimeout(function() {
                if (cropperBerkas) {
                    try {
                        const canvasData = cropperBerkas.getCanvasData();
                        const containerData = cropperBerkas.getContainerData();

                        if (canvasData && containerData) {
                            // Jika aspect ratio fixed, pastikan crop box mengikuti aspect ratio
                            const aspectRatio = window.currentCropAspectRatio;
                            const isFixedRatio = window.currentCropAspectRatioFixed && !isNaN(aspectRatio);

                            let cropBoxWidth, cropBoxHeight;

                            if (isFixedRatio) {
                                // Untuk fixed ratio, hitung berdasarkan aspect ratio
                                const maxSize = Math.min(canvasData.width * 0.8, canvasData.height * 0.8, containerData.width, containerData.height);

                                if (canvasData.width > canvasData.height) {
                                    // Landscape: width lebih besar
                                    cropBoxWidth = Math.min(maxSize, containerData.width);
                                    cropBoxHeight = cropBoxWidth / aspectRatio;

                                    // Jika height terlalu besar, sesuaikan
                                    if (cropBoxHeight > containerData.height) {
                                        cropBoxHeight = Math.min(containerData.height, canvasData.height * 0.8);
                                        cropBoxWidth = cropBoxHeight * aspectRatio;
                                    }
                                } else {
                                    // Portrait: height lebih besar
                                    cropBoxHeight = Math.min(maxSize, containerData.height);
                                    cropBoxWidth = cropBoxHeight * aspectRatio;

                                    // Jika width terlalu besar, sesuaikan
                                    if (cropBoxWidth > containerData.width) {
                                        cropBoxWidth = Math.min(containerData.width, canvasData.width * 0.8);
                                        cropBoxHeight = cropBoxWidth / aspectRatio;
                                    }
                                }
                            } else {
                                // Free aspect ratio: hitung berdasarkan 80% dari canvas
                                cropBoxWidth = Math.min(canvasData.width * 0.8, containerData.width);
                                cropBoxHeight = Math.min(canvasData.height * 0.8, containerData.height);
                            }

                            // Pastikan aspect ratio tetap terjaga untuk fixed ratio
                            if (isFixedRatio && !isNaN(aspectRatio)) {
                                cropperBerkas.setAspectRatio(aspectRatio);
                            }

                            // Set posisi crop box di tengah container
                            const cropBoxLeft = (containerData.width - cropBoxWidth) / 2;
                            const cropBoxTop = (containerData.height - cropBoxHeight) / 2;

                            cropperBerkas.setCropBoxData({
                                left: cropBoxLeft,
                                top: cropBoxTop,
                                width: cropBoxWidth,
                                height: cropBoxHeight
                            });
                        }
                    } catch (e) {
                        console.log('Error adjusting crop box after rotate:', e);
                    }
                }
            }, 100);
        }
    });

    // Function untuk initialize perspective canvas
    function initPerspectiveCanvas() {
        perspectiveCanvas = document.getElementById('perspectiveCanvas');
        if (perspectiveCanvas) {
            const container = document.getElementById('cropContainerBerkas');
            if (container) {
                const updateCanvasSize = () => {
                    const rect = container.getBoundingClientRect();
                    perspectiveCanvas.width = rect.width;
                    perspectiveCanvas.height = rect.height;
                    perspectiveCanvas.style.width = rect.width + 'px';
                    perspectiveCanvas.style.height = rect.height + 'px';
                    if (perspectiveCanvas.width && perspectiveCanvas.height) {
                        perspectiveCtx = perspectiveCanvas.getContext('2d');
                    }
                };
                updateCanvasSize();
            }
        }
    }

    // Function untuk toggle perspective mode
    $('#btnTogglePerspective').on('click', function() {
        console.log('Toggle perspective clicked');
        if (!cropperBerkas) {
            console.warn('Cropper not initialized');
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Cropper belum diinisialisasi'
            });
            return;
        }

        perspectiveMode = !perspectiveMode;
        console.log('Perspective mode:', perspectiveMode);

        if (perspectiveMode) {
            // Aktifkan mode perspektif
            const overlay = $('#perspectiveOverlay');
            overlay.addClass('active');
            $('#perspectiveModeInfo').hide();
            $('#perspectiveModeActive').show();
            $(this).removeClass('btn-warning').addClass('btn-success');
            $(this).html('<i class="fas fa-check"></i> Mode Perspektif Aktif');

            // Disable cropper interaction saat perspective mode aktif
            if (cropperBerkas) {
                cropperBerkas.setDragMode('none');
                cropperBerkas.disable();
                // Hide crop box dengan CSS
                $('.cropper-crop-box').css('display', 'none');
            }

            // Update canvas size
            initPerspectiveCanvas();

            // Ambil crop box data dan update handles
            if (cropperBerkas) {
                const cropBoxData = cropperBerkas.getCropBoxData();
                const container = document.getElementById('cropContainerBerkas');
                if (container) {
                    const rect = container.getBoundingClientRect();

                    // Update handles berdasarkan crop box
                    perspectiveHandles.tl = {
                        x: (cropBoxData.left / rect.width) * 100,
                        y: (cropBoxData.top / rect.height) * 100
                    };
                    perspectiveHandles.tr = {
                        x: ((cropBoxData.left + cropBoxData.width) / rect.width) * 100,
                        y: (cropBoxData.top / rect.height) * 100
                    };
                    perspectiveHandles.bl = {
                        x: (cropBoxData.left / rect.width) * 100,
                        y: ((cropBoxData.top + cropBoxData.height) / rect.height) * 100
                    };
                    perspectiveHandles.br = {
                        x: ((cropBoxData.left + cropBoxData.width) / rect.width) * 100,
                        y: ((cropBoxData.top + cropBoxData.height) / rect.height) * 100
                    };

                    updatePerspectiveHandlesPosition();

                    // Load image untuk preview
                    const originalCanvas = cropperBerkas.getCroppedCanvas({
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    });

                    if (originalCanvas) {
                        const img = new Image();
                        img.onload = function() {
                            perspectiveImage = img;
                            drawPerspectivePreview();
                        };
                        img.src = originalCanvas.toDataURL();
                    }
                }
            }
        } else {
            // Nonaktifkan mode perspektif
            $('#perspectiveOverlay').removeClass('active');
            $('#perspectiveModeInfo').show();
            $('#perspectiveModeActive').hide();
            $(this).removeClass('btn-success').addClass('btn-warning');
            $(this).html('<i class="fas fa-project-diagram"></i> Mode Perspektif');

            // Enable cropper interaction kembali
            if (cropperBerkas) {
                cropperBerkas.enable();
                cropperBerkas.setDragMode('move');
                // Show crop box kembali
                $('.cropper-crop-box').css('display', '');
            }
        }
    });

    // Function untuk update position handles
    function updatePerspectiveHandlesPosition() {
        $('#handleTL').css({
            left: perspectiveHandles.tl.x + '%',
            top: perspectiveHandles.tl.y + '%'
        });
        $('#handleTR').css({
            right: (100 - perspectiveHandles.tr.x) + '%',
            top: perspectiveHandles.tr.y + '%'
        });
        $('#handleBL').css({
            left: perspectiveHandles.bl.x + '%',
            bottom: (100 - perspectiveHandles.bl.y) + '%'
        });
        $('#handleBR').css({
            right: (100 - perspectiveHandles.br.x) + '%',
            bottom: (100 - perspectiveHandles.br.y) + '%'
        });

        updatePerspectiveLines();
        if (perspectiveMode && perspectiveImage) {
            drawPerspectivePreview();
        }
    }

    // Function untuk update perspective lines
    function updatePerspectiveLines() {
        const container = document.getElementById('cropContainerBerkas');
        if (!container) return;
        const rect = container.getBoundingClientRect();

        const tl = {
            x: (perspectiveHandles.tl.x / 100) * rect.width,
            y: (perspectiveHandles.tl.y / 100) * rect.height
        };
        const tr = {
            x: (perspectiveHandles.tr.x / 100) * rect.width,
            y: (perspectiveHandles.tr.y / 100) * rect.height
        };
        const bl = {
            x: (perspectiveHandles.bl.x / 100) * rect.width,
            y: (perspectiveHandles.bl.y / 100) * rect.height
        };
        const br = {
            x: (perspectiveHandles.br.x / 100) * rect.width,
            y: (perspectiveHandles.br.y / 100) * rect.height
        };

        const dist1 = Math.sqrt(Math.pow(tr.x - tl.x, 2) + Math.pow(tr.y - tl.y, 2));
        const angle1 = Math.atan2(tr.y - tl.y, tr.x - tl.x) * 180 / Math.PI;
        $('#perspectiveLine1').css({
            left: tl.x + 'px',
            top: tl.y + 'px',
            width: dist1 + 'px',
            transform: 'rotate(' + angle1 + 'deg)',
            transformOrigin: '0 0'
        });

        const dist2 = Math.sqrt(Math.pow(br.x - tr.x, 2) + Math.pow(br.y - tr.y, 2));
        const angle2 = Math.atan2(br.y - tr.y, br.x - tr.x) * 180 / Math.PI;
        $('#perspectiveLine2').css({
            left: tr.x + 'px',
            top: tr.y + 'px',
            width: dist2 + 'px',
            transform: 'rotate(' + angle2 + 'deg)',
            transformOrigin: '0 0'
        });

        const dist3 = Math.sqrt(Math.pow(bl.x - br.x, 2) + Math.pow(bl.y - br.y, 2));
        const angle3 = Math.atan2(bl.y - br.y, bl.x - br.x) * 180 / Math.PI;
        $('#perspectiveLine3').css({
            left: br.x + 'px',
            top: br.y + 'px',
            width: dist3 + 'px',
            transform: 'rotate(' + angle3 + 'deg)',
            transformOrigin: '0 0'
        });

        const dist4 = Math.sqrt(Math.pow(tl.x - bl.x, 2) + Math.pow(tl.y - bl.y, 2));
        const angle4 = Math.atan2(tl.y - bl.y, tl.x - bl.x) * 180 / Math.PI;
        $('#perspectiveLine4').css({
            left: bl.x + 'px',
            top: bl.y + 'px',
            width: dist4 + 'px',
            transform: 'rotate(' + angle4 + 'deg)',
            transformOrigin: '0 0'
        });
    }

    // Function untuk draw perspective preview
    function drawPerspectivePreview() {
        if (!perspectiveCanvas || !perspectiveCtx || !perspectiveImage || !cropperBerkas) return;
        const container = document.getElementById('cropContainerBerkas');
        if (!container) return;
        const rect = container.getBoundingClientRect();

        if (perspectiveCanvas.width !== rect.width || perspectiveCanvas.height !== rect.height) {
            perspectiveCanvas.width = rect.width;
            perspectiveCanvas.height = rect.height;
        }

        perspectiveCtx.clearRect(0, 0, perspectiveCanvas.width, perspectiveCanvas.height);
        const cropBoxData = cropperBerkas.getCropBoxData();

        // Simplified preview - hanya draw outline, transform akan dilakukan saat save
        perspectiveCtx.strokeStyle = 'rgba(0, 123, 255, 0.5)';
        perspectiveCtx.lineWidth = 2;
        perspectiveCtx.beginPath();
        const tl = {
            x: (perspectiveHandles.tl.x / 100) * rect.width,
            y: (perspectiveHandles.tl.y / 100) * rect.height
        };
        const tr = {
            x: (perspectiveHandles.tr.x / 100) * rect.width,
            y: (perspectiveHandles.tr.y / 100) * rect.height
        };
        const bl = {
            x: (perspectiveHandles.bl.x / 100) * rect.width,
            y: (perspectiveHandles.bl.y / 100) * rect.height
        };
        const br = {
            x: (perspectiveHandles.br.x / 100) * rect.width,
            y: (perspectiveHandles.br.y / 100) * rect.height
        };
        perspectiveCtx.moveTo(tl.x, tl.y);
        perspectiveCtx.lineTo(tr.x, tr.y);
        perspectiveCtx.lineTo(br.x, br.y);
        perspectiveCtx.lineTo(bl.x, bl.y);
        perspectiveCtx.closePath();
        perspectiveCtx.stroke();
    }

    // Drag handlers untuk perspective handles
    $(document).on('mousedown touchstart', '.perspective-handle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!perspectiveMode) return;

        currentDraggedHandle = $(this).data('handle');
        const container = document.getElementById('cropContainerBerkas');
        const rect = container.getBoundingClientRect();

        function moveHandle(e) {
            const clientX = e.clientX || (e.touches && e.touches[0].clientX);
            const clientY = e.clientY || (e.touches && e.touches[0].clientY);
            if (!clientX || !clientY) return;

            const x = ((clientX - rect.left) / rect.width) * 100;
            const y = ((clientY - rect.top) / rect.height) * 100;
            const clampedX = Math.max(0, Math.min(100, x));
            const clampedY = Math.max(0, Math.min(100, y));

            perspectiveHandles[currentDraggedHandle] = {
                x: clampedX,
                y: clampedY
            };
            updatePerspectiveHandlesPosition();
        }

        function stopDrag() {
            currentDraggedHandle = null;
            $(document).off('mousemove touchmove', moveHandle);
            $(document).off('mouseup touchend', stopDrag);
        }

        $(document).on('mousemove touchmove', moveHandle);
        $(document).on('mouseup touchend', stopDrag);
        return false;
    });

    // Function untuk apply perspective crop saat save
    function applyPerspectiveCrop() {
        if (!perspectiveMode || !cropperBerkas) return null;

        // Get original cropped image
        const originalCanvas = cropperBerkas.getCroppedCanvas({
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!originalCanvas) return null;

        const cropBoxData = cropperBerkas.getCropBoxData();

        // Create output canvas dengan ukuran crop box
        const outputCanvas = document.createElement('canvas');
        outputCanvas.width = cropBoxData.width;
        outputCanvas.height = cropBoxData.height;

        const outputCtx = outputCanvas.getContext('2d');
        outputCtx.imageSmoothingEnabled = true;
        outputCtx.imageSmoothingQuality = 'high';

        // Source quad dari perspective handles (relatif ke container)
        const container = document.getElementById('cropContainerBerkas');
        if (!container) return originalCanvas;

        const rect = container.getBoundingClientRect();

        // Convert perspective handles ke koordinat relatif crop box
        const srcTL = {
            x: ((perspectiveHandles.tl.x / 100) * rect.width - cropBoxData.left) / cropBoxData.width,
            y: ((perspectiveHandles.tl.y / 100) * rect.height - cropBoxData.top) / cropBoxData.height
        };
        const srcTR = {
            x: ((perspectiveHandles.tr.x / 100) * rect.width - cropBoxData.left) / cropBoxData.width,
            y: ((perspectiveHandles.tr.y / 100) * rect.height - cropBoxData.top) / cropBoxData.height
        };
        const srcBL = {
            x: ((perspectiveHandles.bl.x / 100) * rect.width - cropBoxData.left) / cropBoxData.width,
            y: ((perspectiveHandles.bl.y / 100) * rect.height - cropBoxData.top) / cropBoxData.height
        };
        const srcBR = {
            x: ((perspectiveHandles.br.x / 100) * rect.width - cropBoxData.left) / cropBoxData.width,
            y: ((perspectiveHandles.br.y / 100) * rect.height - cropBoxData.top) / cropBoxData.height
        };

        // Apply perspective transform menggunakan bilinear interpolation
        const sourceImageData = originalCanvas.getContext('2d').getImageData(0, 0, originalCanvas.width, originalCanvas.height);
        const outputImageData = outputCtx.createImageData(outputCanvas.width, outputCanvas.height);

        // Isi semua pixel dengan warna putih terlebih dahulu
        for (let i = 0; i < outputImageData.data.length; i += 4) {
            outputImageData.data[i] = 255; // R
            outputImageData.data[i + 1] = 255; // G
            outputImageData.data[i + 2] = 255; // B
            outputImageData.data[i + 3] = 255; // A
        }

        for (let y = 0; y < outputCanvas.height; y++) {
            for (let x = 0; x < outputCanvas.width; x++) {
                const u = x / outputCanvas.width;
                const v = y / outputCanvas.height;

                // Bilinear interpolation pada source quad
                const topX = srcTL.x + (srcTR.x - srcTL.x) * u;
                const topY = srcTL.y + (srcTR.y - srcTL.y) * u;
                const bottomX = srcBL.x + (srcBR.x - srcBL.x) * u;
                const bottomY = srcBL.y + (srcBR.y - srcBL.y) * u;

                const srcX = topX + (bottomX - topX) * v;
                const srcY = topY + (bottomY - topY) * v;

                const mappedX = Math.floor(srcX * originalCanvas.width);
                const mappedY = Math.floor(srcY * originalCanvas.height);

                if (mappedX >= 0 && mappedX < originalCanvas.width && mappedY >= 0 && mappedY < originalCanvas.height) {
                    const srcIdx = (mappedY * originalCanvas.width + mappedX) * 4;
                    const dstIdx = (y * outputCanvas.width + x) * 4;

                    outputImageData.data[dstIdx] = sourceImageData.data[srcIdx];
                    outputImageData.data[dstIdx + 1] = sourceImageData.data[srcIdx + 1];
                    outputImageData.data[dstIdx + 2] = sourceImageData.data[srcIdx + 2];
                    outputImageData.data[dstIdx + 3] = sourceImageData.data[srcIdx + 3];
                }
            }
        }

        outputCtx.putImageData(outputImageData, 0, 0);
        return outputCanvas;
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

        let canvas;

        // Jika perspective mode aktif, gunakan perspective transform
        if (perspectiveMode && $('#perspectiveOverlay').hasClass('active')) {
            canvas = applyPerspectiveCrop();
            if (!canvas) {
                // Fallback ke normal crop jika perspective gagal
                canvas = cropperBerkas.getCroppedCanvas({
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
            }
        } else {
            // Get cropped canvas normal
            canvas = cropperBerkas.getCroppedCanvas({
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
        }

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
                    $('#editFileNameDisplay').text('');
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

        // Tampilkan nama file yang dipilih
        if (file) {
            $('#editFileNameDisplay').text(file.name).css('color', '#28a745');
        } else {
            $('#editFileNameDisplay').text('').css('color', '#666');
        }

        if (!file) {
            return;
        }

        // Validasi ukuran file (max 15MB - file akan di-compress setelah crop)
        if (file.size > 15728640) { //15MB
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Ukuran file terlalu besar. Maksimal 15MB. File akan otomatis di-compress setelah crop.'
            });
            $(this).val('');
            $('#editFileNameDisplay').text('');
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
            $('#editFileNameDisplay').text('');
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
                // Tutup loading dan langsung buka modal crop tanpa menampilkan info resize
                Swal.close();
                // Langsung buka modal crop tanpa preview di form
                const reader = new FileReader();
                reader.onload = function(e) {
                    showCropModalBerkas(e.target.result, namaBerkas);
                };
                reader.readAsDataURL(processedFile);
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
        $('#editFileNameDisplay').text('');
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

    // Validasi nomor rekening saat input
    $('#updateNoRekInput').on('input', function() {
        const bankType = $('#updateNoRekBankType').val();
        const noRek = $(this).val().trim();

        // Hapus karakter non-digit
        const cleanNoRek = noRek.replace(/\D/g, '');
        if (noRek !== cleanNoRek) {
            $(this).val(cleanNoRek);
        }

        // Validasi panjang berdasarkan bank type
        if (bankType === 'BPR' && cleanNoRek.length > 11) {
            $(this).val(cleanNoRek.substring(0, 11));
        } else if (bankType === 'BRK' && cleanNoRek.length > 10) {
            $(this).val(cleanNoRek.substring(0, 10));
        }
    });

    // Handle form update no rekening
    $('#formUpdateNoRek').on('submit', function(e) {
        e.preventDefault();

        const idGuru = $('#updateNoRekIdGuru').val();
        const bankType = $('#updateNoRekBankType').val();
        const noRek = $('#updateNoRekInput').val().trim();

        if (!idGuru || !bankType) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Data tidak lengkap'
            });
            return;
        }

        // Validasi nomor rekening
        if (noRek) {
            // Harus berupa angka
            if (!/^\d+$/.test(noRek)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Nomor rekening harus berupa angka'
                });
                return;
            }

            // Validasi panjang berdasarkan bank type
            if (bankType === 'BPR') {
                if (noRek.length !== 11) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Nomor rekening BPR harus 11 digit'
                    });
                    return;
                }
            } else if (bankType === 'BRK') {
                if (noRek.length !== 10) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Nomor rekening BRK harus 10 digit'
                    });
                    return;
                }
            }
        }

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/guru/updateNoRekening') ?>',
            type: 'POST',
            data: {
                IdGuru: idGuru,
                BankType: bankType,
                NoRek: noRek
            },
            dataType: 'json',
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
                        // Update data attribute pada gambar preview
                        const $img = $('.preview-image[data-id-guru="' + idGuru + '"][data-bank-type="' + bankType + '"]');
                        if ($img.length) {
                            $img.data('no-rek', noRek);
                        }
                        // Reload halaman untuk update tampilan no rekening
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan saat menyimpan nomor rekening'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                let errorMessage = 'Terjadi kesalahan saat menyimpan nomor rekening';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

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

    // Reset form no rekening saat modal preview ditutup
    $('#modalImagePreview').on('hidden.bs.modal', function() {
        $('#noRekFormContainer').hide();
        $('#formUpdateNoRek')[0].reset();
        $('#updateNoRekIdGuru').val('');
        $('#updateNoRekBankType').val('');
    });

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
        $('#editFileNameDisplay').text('');
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