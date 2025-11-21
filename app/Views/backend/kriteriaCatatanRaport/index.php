<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kriteria Catatan Raport</h3>
                    <div class="card-tools">
                        <?php
                        // Cek apakah user adalah admin (IdTpq = 0 atau empty, atau dari session)
                        $isAdminUser = ($isAdmin ?? false) || ($isOperator ?? false) || ($currentIdTpq === '0' || $currentIdTpq === 0 || empty($currentIdTpq));
                        ?>
                        <?php if ($isAdminUser) : ?>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddKriteria">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </button>
                        <?php else : ?>
                            <span class="badge badge-info">
                                <i class="fas fa-info-circle"></i> Gunakan tombol duplikasi untuk menerapkan konfigurasi ke TPQ/Kelas Anda
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Filter TPQ:</label>
                            <select class="form-control" id="filterIdTpq" <?= ($isRestrictedUser ?? false) ? 'disabled' : '' ?>>
                                <option value="">-- Semua TPQ --</option>
                                <?php if ($isAdminUser) : ?>
                                    <option value="default" <?= $currentIdTpq === 'default' ? 'selected' : '' ?>>default (Template Default)</option>
                                    <option value="0" <?= $currentIdTpq === '0' ? 'selected' : '' ?>>0 (Admin)</option>
                                <?php endif; ?>
                                <?php if (!empty($listTpq)) : ?>
                                    <?php foreach ($listTpq as $tpq) : ?>
                                        <option value="<?= $tpq['IdTpq'] ?>" <?= $currentIdTpq == $tpq['IdTpq'] ? 'selected' : '' ?>>
                                            <?= $tpq['IdTpq'] ?> - <?= $tpq['NamaTpq'] ?? $tpq['IdTpq'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if ($isRestrictedUser ?? false) : ?>
                                <input type="hidden" id="filterIdTpqHidden" value="<?= $currentIdTpq ?>">
                                <small class="form-text text-muted">
                                    <i class="fas fa-lock"></i> TPQ Anda: <strong><?= $currentIdTpq ?></strong> (tidak dapat diubah)
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label>Filter Tahun Ajaran:</label>
                            <select class="form-control" id="filterIdTahunAjaran">
                                <option value="">-- Semua Tahun Ajaran --</option>
                                <?php if (!empty($listTahunAjaran)) : ?>
                                    <?php foreach ($listTahunAjaran as $ta) : ?>
                                        <option value="<?= $ta['IdTahunAjaran'] ?>" <?= $currentTahunAjaran == $ta['IdTahunAjaran'] ? 'selected' : '' ?>>
                                            <?= convertTahunAjaran($ta['IdTahunAjaran']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Filter Kelas:</label>
                            <select class="form-control" id="filterIdKelas">
                                <option value="">-- Semua Kelas --</option>
                                <?php if (!empty($listKelas)) : ?>
                                    <?php foreach ($listKelas as $kelas) : ?>
                                        <option value="<?= $kelas['IdKelas'] ?>" <?= $currentIdKelas == $kelas['IdKelas'] ? 'selected' : '' ?>>
                                            <?= $kelas['NamaKelas'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-info btn-block" id="btnFilter">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="kriteriaTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nilai Huruf</th>
                                <th>Nilai Min</th>
                                <th>Nilai Max</th>
                                <th>ID TPQ</th>
                                <th>Tahun Ajaran</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php if (!empty($catatanList)) : ?>
                                <?php foreach ($catatanList as $catatan) : ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><span class="badge badge-primary"><?= $catatan['NilaiHuruf'] ?></span></td>
                                        <td><?= $catatan['NilaiMin'] ?? '-' ?></td>
                                        <td><?= $catatan['NilaiMax'] ?? '-' ?></td>
                                        <td><?= $catatan['IdTpq'] ?></td>
                                        <td><?= $catatan['IdTahunAjaran'] ? convertTahunAjaran($catatan['IdTahunAjaran']) : 'Semua' ?></td>
                                        <td><?= $catatan['NamaKelas'] ?? ($catatan['IdKelas'] ? $catatan['IdKelas'] : 'Semua') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $catatan['Status'] === 'Aktif' ? 'success' : 'secondary' ?>">
                                                <?= $catatan['Status'] ?>
                                            </span>
                                        </td>
                                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($catatan['Catatan']) ?>">
                                            <?= htmlspecialchars(mb_substr($catatan['Catatan'], 0, 100)) ?><?= mb_strlen($catatan['Catatan']) > 100 ? '...' : '' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php
                                                // Cek apakah user adalah admin
                                                $isAdmin = ($currentIdTpq === '0' || $currentIdTpq === 0 || empty($currentIdTpq));

                                                // Untuk row dengan IdTpq = 'default', hanya admin yang bisa edit/delete
                                                $canEditDelete = ($catatan['IdTpq'] !== 'default') || $isAdmin;
                                                ?>

                                                <?php if ($canEditDelete) : ?>
                                                    <button type="button" class="btn btn-warning btn-sm edit-kriteria-btn"
                                                        data-id="<?= $catatan['id'] ?>"
                                                        data-nilai-huruf="<?= $catatan['NilaiHuruf'] ?>"
                                                        data-nilai-min="<?= $catatan['NilaiMin'] ?>"
                                                        data-nilai-max="<?= $catatan['NilaiMax'] ?>"
                                                        data-catatan="<?= htmlspecialchars($catatan['Catatan']) ?>"
                                                        data-status="<?= $catatan['Status'] ?>"
                                                        data-id-tahun-ajaran="<?= $catatan['IdTahunAjaran'] ?>"
                                                        data-id-tpq="<?= $catatan['IdTpq'] ?>"
                                                        data-id-kelas="<?= $catatan['IdKelas'] ?>"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($catatan['IdTpq'] === 'default') : ?>
                                                    <button type="button" class="btn btn-info btn-sm duplicate-kriteria-btn"
                                                        data-id="<?= $catatan['id'] ?>"
                                                        data-nilai-huruf="<?= $catatan['NilaiHuruf'] ?>"
                                                        data-nilai-min="<?= $catatan['NilaiMin'] ?>"
                                                        data-nilai-max="<?= $catatan['NilaiMax'] ?>"
                                                        data-catatan="<?= htmlspecialchars($catatan['Catatan']) ?>"
                                                        data-status="<?= $catatan['Status'] ?>"
                                                        title="Duplikasi">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($canEditDelete) : ?>
                                                    <button type="button" class="btn btn-danger btn-sm delete-kriteria-btn"
                                                        data-id="<?= $catatan['id'] ?>"
                                                        data-id-tpq="<?= $catatan['IdTpq'] ?>"
                                                        data-nilai-huruf="<?= $catatan['NilaiHuruf'] ?>"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data kriteria catatan raport</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kriteria Catatan Raport -->
<div class="modal fade" id="modalAddKriteria" tabindex="-1" role="dialog" aria-labelledby="modalAddKriteriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddKriteriaLabel">Tambah Kriteria Catatan Raport Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddKriteria">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addNilaiHuruf">Nilai Huruf <span class="text-danger">*</span></label>
                        <select class="form-control" id="addNilaiHuruf" name="NilaiHuruf" required>
                            <option value="">-- Pilih Nilai Huruf --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addNilaiMin">Nilai Minimum</label>
                                <input type="number" step="0.01" class="form-control" id="addNilaiMin" name="NilaiMin" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="addNilaiMax">Nilai Maksimum</label>
                                <input type="number" step="0.01" class="form-control" id="addNilaiMax" name="NilaiMax" placeholder="100.00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="addCatatan">Catatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="addCatatan" name="Catatan" rows="5" required placeholder="Masukkan catatan untuk raport..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="addStatus">Status</label>
                        <select class="form-control" id="addStatus" name="Status">
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addIdTpq">ID TPQ <span class="text-danger">*</span></label>
                        <select class="form-control" id="addIdTpq" name="IdTpq" required>
                            <option value="">-- Pilih ID TPQ --</option>
                            <?php if ($isAdminUser) : ?>
                                <option value="default">default (Template Default)</option>
                                <option value="0">0 (Admin)</option>
                            <?php endif; ?>
                            <?php if (!empty($listTpq)) : ?>
                                <?php foreach ($listTpq as $tpq) : ?>
                                    <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['IdTpq'] ?> - <?= $tpq['NamaTpq'] ?? $tpq['IdTpq'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addIdTahunAjaran">Tahun Ajaran</label>
                        <select class="form-control" id="addIdTahunAjaran" name="IdTahunAjaran">
                            <option value="">-- Semua Tahun Ajaran (NULL) --</option>
                            <?php if (!empty($listTahunAjaran)) : ?>
                                <?php foreach ($listTahunAjaran as $ta) : ?>
                                    <option value="<?= $ta['IdTahunAjaran'] ?>"><?= convertTahunAjaran($ta['IdTahunAjaran']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Kosongkan untuk berlaku semua tahun ajaran</small>
                    </div>
                    <div class="form-group">
                        <label for="addIdKelas">Kelas</label>
                        <select class="form-control" id="addIdKelas" name="IdKelas">
                            <option value="">-- Semua Kelas (NULL) --</option>
                            <?php if (!empty($listKelas)) : ?>
                                <?php foreach ($listKelas as $kelas) : ?>
                                    <option value="<?= $kelas['IdKelas'] ?>"><?= $kelas['NamaKelas'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Kosongkan untuk berlaku semua kelas</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kriteria Catatan Raport -->
<div class="modal fade" id="modalEditKriteria" tabindex="-1" role="dialog" aria-labelledby="modalEditKriteriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditKriteriaLabel">Edit Kriteria Catatan Raport</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditKriteria">
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editNilaiHuruf">Nilai Huruf <span class="text-danger">*</span></label>
                        <select class="form-control" id="editNilaiHuruf" name="NilaiHuruf" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editNilaiMin">Nilai Minimum</label>
                                <input type="number" step="0.01" class="form-control" id="editNilaiMin" name="NilaiMin">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editNilaiMax">Nilai Maksimum</label>
                                <input type="number" step="0.01" class="form-control" id="editNilaiMax" name="NilaiMax">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editCatatan">Catatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editCatatan" name="Catatan" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="Status">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editIdTpq">ID TPQ</label>
                        <input type="text" class="form-control" id="editIdTpq" name="IdTpq" readonly>
                        <small class="form-text text-muted">ID TPQ tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="editIdTahunAjaran">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="editIdTahunAjaran" name="IdTahunAjaran" readonly>
                        <small class="form-text text-muted">Tahun Ajaran tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="editIdKelas">Kelas</label>
                        <input type="text" class="form-control" id="editIdKelas" name="IdKelas" readonly>
                        <small class="form-text text-muted">Kelas tidak dapat diubah</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Duplikasi Kriteria Catatan Raport -->
<div class="modal fade" id="modalDuplicateKriteria" tabindex="-1" role="dialog" aria-labelledby="modalDuplicateKriteriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="modalDuplicateKriteriaLabel">
                    <i class="fas fa-copy"></i> Duplikasi Kriteria Catatan Raport
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formDuplicateKriteria">
                <input type="hidden" id="duplicateId" name="source_id">
                <?php if (!$isAdminUser) : ?>
                    <input type="hidden" id="duplicateIdTpqHidden" name="IdTpq" value="<?= $currentIdTpq ?>">
                <?php endif; ?>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Anda akan menduplikasi konfigurasi dari template default ke ID TPQ/Kelas yang dipilih.
                    </div>
                    <div class="form-group">
                        <label for="duplicateIdTpq">ID TPQ Tujuan <span class="text-danger">*</span></label>
                        <?php if ($isAdminUser) : ?>
                            <select class="form-control" id="duplicateIdTpq" name="IdTpq" required>
                                <option value="">-- Pilih ID TPQ Tujuan --</option>
                                <option value="0">0 (Admin)</option>
                                <?php if (!empty($listTpq)) : ?>
                                    <?php foreach ($listTpq as $tpq) : ?>
                                        <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['IdTpq'] ?> - <?= $tpq['NamaTpq'] ?? $tpq['IdTpq'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">
                                Pilih ID TPQ tujuan untuk menerapkan konfigurasi ini. Gunakan '0' untuk admin. Tidak dapat menduplikasi ke 'default'.
                            </small>
                        <?php else : ?>
                            <input type="text" class="form-control" id="duplicateIdTpq" value="<?= $currentIdTpq ?>" readonly>
                            <small class="form-text text-muted">
                                <i class="fas fa-lock"></i> Konfigurasi akan diterapkan ke TPQ Anda: <strong><?= $currentIdTpq ?></strong> (tidak dapat diubah)
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="duplicateIdTahunAjaran">Tahun Ajaran</label>
                        <select class="form-control" id="duplicateIdTahunAjaran" name="IdTahunAjaran">
                            <option value="">-- Semua Tahun Ajaran (NULL) --</option>
                            <?php if (!empty($listTahunAjaran)) : ?>
                                <?php foreach ($listTahunAjaran as $ta) : ?>
                                    <option value="<?= $ta['IdTahunAjaran'] ?>"><?= convertTahunAjaran($ta['IdTahunAjaran']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Kosongkan untuk berlaku semua tahun ajaran</small>
                    </div>
                    <div class="form-group">
                        <label for="duplicateIdKelas">Kelas</label>
                        <select class="form-control" id="duplicateIdKelas" name="IdKelas">
                            <option value="">-- Semua Kelas (NULL) --</option>
                            <?php if (!empty($listKelas)) : ?>
                                <?php foreach ($listKelas as $kelas) : ?>
                                    <option value="<?= $kelas['IdKelas'] ?>"><?= $kelas['NamaKelas'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Kosongkan untuk berlaku semua kelas</small>
                    </div>
                    <div class="form-group">
                        <label for="duplicateNilaiHuruf">Nilai Huruf <span class="text-danger">*</span></label>
                        <select class="form-control" id="duplicateNilaiHuruf" name="NilaiHuruf" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duplicateNilaiMin">Nilai Minimum</label>
                                <input type="number" step="0.01" class="form-control" id="duplicateNilaiMin" name="NilaiMin">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duplicateNilaiMax">Nilai Maksimum</label>
                                <input type="number" step="0.01" class="form-control" id="duplicateNilaiMax" name="NilaiMax">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="duplicateCatatan">Catatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="duplicateCatatan" name="Catatan" rows="5" required></textarea>
                        <small class="form-text text-muted">Anda dapat mengubah catatan sebelum menduplikasi</small>
                    </div>
                    <div class="form-group">
                        <label for="duplicateStatus">Status</label>
                        <select class="form-control" id="duplicateStatus" name="Status">
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-copy"></i> Duplikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Get current user's IdTpq from session
        const currentIdTpq = '<?= $currentIdTpq ?? "" ?>';
        const isAdmin = currentIdTpq === '' || currentIdTpq === '0' || currentIdTpq === 0;

        // Filter button click
        $('#btnFilter').on('click', function() {
            // Jika filter TPQ disabled (untuk GuruKelas), gunakan hidden input
            const idTpq = $('#filterIdTpq').prop('disabled') ? $('#filterIdTpqHidden').val() : $('#filterIdTpq').val();
            const idTahunAjaran = $('#filterIdTahunAjaran').val();
            const idKelas = $('#filterIdKelas').val();
            
            let url = '<?= base_url('backend/rapor/kriteriaCatatanRapor') ?>';
            const params = [];
            
            if (idTpq) params.push('IdTpq=' + idTpq);
            if (idTahunAjaran) params.push('IdTahunAjaran=' + idTahunAjaran);
            if (idKelas) params.push('IdKelas=' + idKelas);
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            window.location.href = url;
        });

        // Auto-select IdTpq when opening modal Tambah
        $('#modalAddKriteria').on('show.bs.modal', function() {
            if (!isAdmin && currentIdTpq) {
                $('#addIdTpq').val(currentIdTpq);
            }
        });

        // Reset form when modal is closed
        $('#modalAddKriteria').on('hidden.bs.modal', function() {
            $('#formAddKriteria')[0].reset();
        });

        // Form Tambah Kriteria
        $('#formAddKriteria').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url('backend/rapor/saveKriteriaCatatanRapor') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#formAddKriteria button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal menyimpan data',
                            html: response.errors ? '<ul>' + Object.values(response.errors).map(err => '<li>' + err + '</li>').join('') + '</ul>' : null
                        });
                        $('#formAddKriteria button[type="submit"]').prop('disabled', false).html('Simpan');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                    $('#formAddKriteria button[type="submit"]').prop('disabled', false).html('Simpan');
                }
            });
        });

        // Button Edit Click
        $(document).on('click', '.edit-kriteria-btn', function() {
            var id = $(this).data('id');
            var nilaiHuruf = $(this).data('nilai-huruf');
            var nilaiMin = $(this).data('nilai-min');
            var nilaiMax = $(this).data('nilai-max');
            var catatan = $(this).data('catatan');
            var status = $(this).data('status');
            var idTahunAjaran = $(this).data('id-tahun-ajaran');
            var idTpq = $(this).data('id-tpq');
            var idKelas = $(this).data('id-kelas');

            $('#editId').val(id);
            $('#editNilaiHuruf').val(nilaiHuruf);
            $('#editNilaiMin').val(nilaiMin);
            $('#editNilaiMax').val(nilaiMax);
            $('#editCatatan').val(catatan);
            $('#editStatus').val(status);
            $('#editIdTpq').val(idTpq);
            $('#editIdTahunAjaran').val(idTahunAjaran || 'Semua');
            $('#editIdKelas').val(idKelas || 'Semua');

            $('#modalEditKriteria').modal('show');
        });

        // Form Edit Kriteria
        $('#formEditKriteria').on('submit', function(e) {
            e.preventDefault();

            var id = $('#editId').val();

            $.ajax({
                url: '<?= base_url('backend/rapor/updateKriteriaCatatanRapor/') ?>' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#formEditKriteria button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengupdate...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal mengupdate data',
                            html: response.errors ? '<ul>' + Object.values(response.errors).map(err => '<li>' + err + '</li>').join('') + '</ul>' : null
                        });
                        $('#formEditKriteria button[type="submit"]').prop('disabled', false).html('Update');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                    $('#formEditKriteria button[type="submit"]').prop('disabled', false).html('Update');
                }
            });
        });

        // Button Duplicate Click
        $(document).on('click', '.duplicate-kriteria-btn', function() {
            var id = $(this).data('id');
            var nilaiHuruf = $(this).data('nilai-huruf');
            var nilaiMin = $(this).data('nilai-min');
            var nilaiMax = $(this).data('nilai-max');
            var catatan = $(this).data('catatan');
            var status = $(this).data('status');

            $('#duplicateId').val(id);
            $('#duplicateNilaiHuruf').val(nilaiHuruf);
            $('#duplicateNilaiMin').val(nilaiMin);
            $('#duplicateNilaiMax').val(nilaiMax);
            $('#duplicateCatatan').val(catatan);
            $('#duplicateStatus').val(status);

            // For admin, clear selection; for non-admin, it's already locked
            if (isAdmin) {
                $('#duplicateIdTpq').val('').trigger('change');
            } else {
                $('#duplicateIdTpqHidden').val(currentIdTpq);
                $('#duplicateIdTpq').val(currentIdTpq);
            }

            $('#modalDuplicateKriteria').modal('show');
        });

        // Form Duplicate Kriteria
        $('#formDuplicateKriteria').on('submit', function(e) {
            e.preventDefault();

            // Validate target IdTpq
            var targetIdTpq = $('#duplicateIdTpq').val();
            if (!targetIdTpq || targetIdTpq === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID TPQ Tujuan harus dipilih'
                });
                $('#duplicateIdTpq').focus();
                return;
            }

            // Prevent duplicating to 'default'
            if (targetIdTpq === 'default') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak dapat menduplikasi ke "default". Gunakan ID TPQ lain atau "0" untuk admin.'
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('backend/rapor/duplicateKriteriaCatatanRapor') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#formDuplicateKriteria button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menduplikasi...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal menduplikasi data',
                            html: response.errors ? '<ul>' + Object.values(response.errors).map(err => '<li>' + err + '</li>').join('') + '</ul>' : null
                        });
                        $('#formDuplicateKriteria button[type="submit"]').prop('disabled', false).html('<i class="fas fa-copy"></i> Duplikasi');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                    $('#formDuplicateKriteria button[type="submit"]').prop('disabled', false).html('<i class="fas fa-copy"></i> Duplikasi');
                }
            });
        });

        // Button Delete Click
        $(document).on('click', '.delete-kriteria-btn', function() {
            var id = $(this).data('id');
            var idTpq = $(this).data('id-tpq');
            var nilaiHuruf = $(this).data('nilai-huruf');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data kriteria catatan raport dengan Nilai Huruf "' + nilaiHuruf + '" untuk TPQ "' + idTpq + '" akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('backend/rapor/deleteKriteriaCatatanRapor/') ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Gagal menghapus data'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan: ' + error
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>

