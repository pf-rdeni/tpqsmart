<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>

<?php
$roomIdMin = isset($roomIdMin) ? (int)$roomIdMin : 1;
$roomIdMax = isset($roomIdMax) ? (int)$roomIdMax : 10;

if ($roomIdMax < $roomIdMin) {
    $roomIdMax = $roomIdMin;
}

$roomOptions = $roomOptions ?? [];
if (empty($roomOptions)) {
    for ($number = $roomIdMin; $number <= $roomIdMax; $number++) {
        $roomOptions[] = sprintf('ROOM-%02d', $number);
    }
}

$roomIdMinLabel = sprintf('ROOM-%02d', $roomIdMin);
$roomIdMaxLabel = sprintf('ROOM-%02d', $roomIdMax);
?>

<section class="content">
    <div class="container-fluid">
        <!-- Card Panitia -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-list"></i> Data Panitia Munaqosah
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalPanitia">
                        <i class="fas fa-plus"></i> Tambah Panitia
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablePanitia" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Username</th>
                                <th width="15%">Type Ujian</th>
                                <th width="15%">TPQ</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($panitia)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($panitia as $p): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $p['username'] ?></td>
                                        <td>
                                            <?php
                                            // Tentukan TypeUjian dari IdTpq
                                            $typeUjian = (!empty($p['IdTpq']) && $p['IdTpq'] != 0) ? 'pra-munaqosah' : 'munaqosah';
                                            ?>
                                            <span class="badge badge-info"><?= $typeUjian ?></span>
                                        </td>
                                        <td><?= $p['NamaTpq'] ?? '-' ?></td>
                                        <td>
                                            <?php
                                            $statusBadgeClass = $p['Status'] === 'Aktif' ? 'badge-success' : 'badge-danger';
                                            ?>
                                            <span class="badge <?= $statusBadgeClass ?>"><?= $p['Status'] ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-edit-password-panitia" data-id="<?= $p['id'] ?>" data-username="<?= htmlspecialchars($p['username'], ENT_QUOTES, 'UTF-8') ?>" title="Ubah Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-delete-panitia" data-id="<?= $p['id'] ?>" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="no-data-row">
                                    <td colspan="6" class="text-center" data-order="0">Tidak ada data panitia</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Juri -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-gavel"></i> Data Juri Pra-munaqosah/Munaqosah
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalJuri">
                        <i class="fas fa-plus"></i> Tambah Juri
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableJuri" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">ID Juri</th>
                                <th width="18%">Username</th>
                                <th width="12%">Grup Materi</th>
                                <th width="10%">Room ID</th>
                                <th width="10%">Type Ujian</th>
                                <th width="12%">TPQ</th>
                                <th width="8%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($juri)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($juri as $j): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $j['IdJuri'] ?></td>
                                        <td><?= $j['UsernameJuri'] ?></td>
                                        <td><?= $j['NamaMateriGrup'] ?></td>
                                        <td>
                                            <?php if (!empty($j['RoomId'])): ?>
                                                <span class="badge badge-info"><?= $j['RoomId'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $j['TypeUjian'] ?></td>
                                        <td><?= $j['NamaTpq'] ?? '-' ?></td>
                                        <td>
                                            <?php
                                            $statusBadgeClass = $j['Status'] === 'Aktif' ? 'badge-success' : 'badge-danger';
                                            ?>
                                            <span class="badge <?= $statusBadgeClass ?>"><?= $j['Status'] ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-edit-password" data-id="<?= $j['id'] ?>" data-username="<?= htmlspecialchars($j['UsernameJuri'], ENT_QUOTES, 'UTF-8') ?>" title="Ubah Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning btn-edit-room" data-id="<?= $j['id'] ?>" data-room="<?= $j['RoomId'] ?>" title="Ubah Room">
                                                <i class="fas fa-door-open"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $j['id'] ?>" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="no-data-row">
                                    <td colspan="9" class="text-center" data-order="0">Tidak ada data juri</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah/Edit Juri -->
<div class="modal fade" id="modalJuri" tabindex="-1" role="dialog" aria-labelledby="modalJuriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalJuriLabel">Tambah Juri Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formJuri">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="IdGrupMateriUjian">Grup Materi Ujian <span class="text-danger">*</span></label>
                                <select class="form-control" id="IdGrupMateriUjian" name="IdGrupMateriUjian" required>
                                    <option value="">Pilih Grup Materi Ujian</option>
                                    <?php foreach ($grupMateriUjian as $grup): ?>
                                        <option value="<?= $grup['IdGrupMateriUjian'] ?>"><?= $grup['NamaMateriGrup'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="IdTpq">TPQ (Opsional)</label>
                                <select class="form-control" id="IdTpq" name="IdTpq">
                                    <?php if (count($tpqDropdown) == 1): ?>
                                        <!-- Jika hanya ada satu TPQ, set sebagai default -->
                                        <option value="<?= $tpqDropdown[0]['IdTpq'] ?>" selected><?= $tpqDropdown[0]['NamaTpq'] ?></option>
                                    <?php else: ?>
                                        <!-- Jika ada banyak TPQ, tampilkan pilihan -->
                                        <option value="">Pilih TPQ</option>
                                        <?php foreach ($tpqDropdown as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="UsernameJuri">Username Juri <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="UsernameJuri" name="UsernameJuri" autocomplete="username" required readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="btnGenerateUsername">
                                            <i class="fas fa-sync"></i> Generate
                                        </button>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Username akan digenerate otomatis berdasarkan grup materi dan TPQ</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="Status" name="Status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="RoomId">Room ID (Opsional)</label>
                                <select class="form-control" id="RoomId" name="RoomId">
                                    <option value="">Tanpa Room</option>
                                    <?php foreach ($roomOptions as $roomOption): ?>
                                        <option value="<?= $roomOption ?>"><?= $roomOption ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Pilih room untuk menempatkan juri (<?= $roomIdMinLabel ?> - <?= $roomIdMaxLabel ?>).
                                    <strong>Biarkan Tanpa Room jika tidak menggunakan sistem room.</strong>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-sm alert-warning mt-4 mb-0">
                                <small>
                                    <i class="fas fa-lightbulb"></i> <strong>Tips:</strong> Pilihan Room ID tersedia dari <?= $roomIdMinLabel ?> hingga <?= $roomIdMaxLabel ?>.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="PasswordJuri">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="PasswordJuri" name="PasswordJuri" autocomplete="new-password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="useDefaultPassword" name="useDefaultPassword">
                                    <label class="form-check-label" for="useDefaultPassword">
                                        Gunakan password default: <strong>JuriTpqSmart</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ConfirmPasswordJuri">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="ConfirmPasswordJuri" name="ConfirmPasswordJuri" autocomplete="new-password" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="icon fas fa-info"></i> Informasi Akun:</h6>
                                <ul class="mb-0">
                                    <li><strong>Email:</strong> <span id="emailPreview">username@smartpq.simpedis.com</span></li>
                                    <li><strong>Password:</strong> JuriTpqSmart</li>
                                    <li><strong>Group:</strong> Juri (ID: 5)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveJuri">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Password -->
<div class="modal fade" id="modalEditPassword" tabindex="-1" role="dialog" aria-labelledby="modalEditPasswordLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPasswordLabel">Ubah Password Juri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditPassword">
                <div class="modal-body">
                    <!-- Hidden username field for accessibility -->
                    <input type="text" id="editUsernameJuri" name="editUsernameJuri" autocomplete="username" style="position: absolute; left: -9999px; width: 1px; height: 1px; opacity: 0;" tabindex="-1" aria-hidden="true">
                    <div class="form-group">
                        <label for="editPasswordJuri">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editPasswordJuri" name="editPasswordJuri" autocomplete="new-password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditPasswordBtn">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="useDefaultPasswordEdit" name="useDefaultPasswordEdit">
                            <label class="form-check-label" for="useDefaultPasswordEdit">
                                Gunakan password default: <strong>JuriTpqSmart</strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editConfirmPasswordJuri">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="editConfirmPasswordJuri" name="editConfirmPasswordJuri" autocomplete="new-password" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdatePassword">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Room -->
<div class="modal fade" id="modalEditRoom" tabindex="-1" role="dialog" aria-labelledby="modalEditRoomLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditRoomLabel">Ubah Room Juri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditRoom">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editRoomId">Room ID</label>
                        <select class="form-control" id="editRoomId" name="RoomId">
                            <option value="">Tanpa Room</option>
                            <?php foreach ($roomOptions as $roomOption): ?>
                                <option value="<?= $roomOption ?>"><?= $roomOption ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Pilih room untuk juri ini (<?= $roomIdMinLabel ?> - <?= $roomIdMaxLabel ?>) atau pilih Tanpa Room jika tidak menggunakan sistem room.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdateRoom">
                        <i class="fas fa-save"></i> Update Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Panitia -->
<div class="modal fade" id="modalPanitia" tabindex="-1" role="dialog" aria-labelledby="modalPanitiaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPanitiaLabel">Tambah Panitia Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPanitia">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="IdTpqPanitia">TPQ <span class="text-danger">*</span></label>
                                <select class="form-control" id="IdTpqPanitia" name="IdTpq" required>
                                    <?php if (count($tpqDropdown) == 1): ?>
                                        <option value="<?= $tpqDropdown[0]['IdTpq'] ?>" selected><?= $tpqDropdown[0]['NamaTpq'] ?></option>
                                    <?php else: ?>
                                        <option value="0" selected>Umum (Munaqosah)</option>
                                        <?php foreach ($tpqDropdown as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Pilih TPQ untuk panitia TPQ tertentu, atau "Umum" untuk panitia munaqosah umum</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="StatusPanitia">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="StatusPanitia" name="Status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="UsernamePanitia">Username Panitia <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="UsernamePanitia" name="UsernamePanitia" autocomplete="username" required readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="btnGenerateUsernamePanitia">
                                            <i class="fas fa-sync"></i> Generate
                                        </button>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Username akan digenerate otomatis berdasarkan TPQ</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="PasswordPanitia">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="PasswordPanitia" name="PasswordPanitia" autocomplete="new-password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordPanitiaBtn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="useDefaultPasswordPanitia" name="useDefaultPasswordPanitia">
                                    <label class="form-check-label" for="useDefaultPasswordPanitia">
                                        Gunakan password default: <strong>PanitiaTpqSmart</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="icon fas fa-info"></i> Informasi Akun:</h6>
                                <ul class="mb-0">
                                    <li><strong>Email:</strong> <span id="emailPreviewPanitia">username@smartpq.simpedis.com</span></li>
                                    <li><strong>Password:</strong> PanitiaTpqSmart</li>
                                    <li><strong>Group:</strong> Panitia (ID: 6)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSavePanitia">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Password Panitia -->
<div class="modal fade" id="modalEditPasswordPanitia" tabindex="-1" role="dialog" aria-labelledby="modalEditPasswordPanitiaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPasswordPanitiaLabel">Ubah Password Panitia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditPasswordPanitia">
                <div class="modal-body">
                    <input type="text" id="editUsernamePanitia" name="editUsernamePanitia" autocomplete="username" style="position: absolute; left: -9999px; width: 1px; height: 1px; opacity: 0;" tabindex="-1" aria-hidden="true">
                    <div class="form-group">
                        <label for="editPasswordPanitia">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editPasswordPanitia" name="editPasswordPanitia" autocomplete="new-password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditPasswordPanitiaBtn">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="useDefaultPasswordEditPanitia" name="useDefaultPasswordEditPanitia">
                            <label class="form-check-label" for="useDefaultPasswordEditPanitia">
                                Gunakan password default: <strong>PanitiaTpqSmart</strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editConfirmPasswordPanitia">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="editConfirmPasswordPanitia" name="editConfirmPasswordPanitia" autocomplete="new-password" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnUpdatePasswordPanitia">
                        <i class="fas fa-save"></i> Update Password
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
        let tableJuri;
        let currentJuriId = null;

        // Inisialisasi DataTables menggunakan fungsi initializeDataTableUmum yang sudah ada
        try {
            var $table = $('#tableJuri');

            // Pastikan tabel ada di DOM
            if ($table.length) {
                // Cek apakah tabel sudah diinisialisasi
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableJuri')) {
                    // Destroy existing DataTable
                    try {
                        $table.DataTable().destroy();
                    } catch (e) {
                        console.warn('Error destroying existing DataTable:', e);
                    }
                }

                // Tunggu sebentar untuk memastikan destroy selesai
                setTimeout(function() {
                    // Double check - pastikan tidak ada instance DataTable
                    if (!$.fn.DataTable.isDataTable('#tableJuri')) {
                        // Gunakan fungsi initializeDataTableUmum yang sudah ada
                        if (typeof initializeDataTableUmum === 'function') {
                            try {
                                // Inisialisasi dengan options tambahan untuk kolom Aksi
                                initializeDataTableUmum('#tableJuri', true, true, ['excel', 'pdf'], {
                                    "order": [],
                                    "columnDefs": [{
                                        "targets": -1, // Last column (Aksi)
                                        "orderable": false,
                                        "searchable": false
                                    }]
                                });

                                // Simpan instance DataTable
                                tableJuri = $('#tableJuri').DataTable();
                            } catch (e) {
                                console.error('Error calling initializeDataTableUmum:', e);
                            }
                        } else {
                            console.error('Function initializeDataTableUmum not found');
                        }
                    } else {
                        console.warn('DataTable still exists, skipping initialization');
                    }
                }, 100);
            }
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }

        // Pendekatan baru: Generate Username menggunakan vanilla JavaScript untuk kompatibilitas maksimal
        function generateUsernameJuri() {
            // Ambil nilai dari form
            var idGrupMateriUjian = document.getElementById('IdGrupMateriUjian');
            var idTpq = document.getElementById('IdTpq');
            var usernameField = document.getElementById('UsernameJuri');
            var emailPreview = document.getElementById('emailPreview');
            var btnGenerate = document.getElementById('btnGenerateUsername');

            // Validasi
            if (!idGrupMateriUjian || !idGrupMateriUjian.value) {
                showAlert('Pilih grup materi ujian terlebih dahulu', 'warning');
                return;
            }

            // Simpan state button
            var originalBtnHTML = btnGenerate.innerHTML;
            var originalBtnDisabled = btnGenerate.disabled;

            // Update button state
            btnGenerate.disabled = true;
            btnGenerate.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generate...';

            // Siapkan data
            var formData = new FormData();
            formData.append('IdGrupMateriUjian', idGrupMateriUjian.value);
            formData.append('IdTpq', (idTpq && idTpq.value) ? idTpq.value : '');

            // CSRF token
            var csrfName = '<?= csrf_token() ?>';
            var csrfHash = '<?= csrf_hash() ?>';
            formData.append(csrfName, csrfHash);

            // URL endpoint
            var url = '<?= base_url('backend/munaqosah/generate-username-juri') ?>';

            // Gunakan XMLHttpRequest untuk kompatibilitas maksimal
            var xhr = new XMLHttpRequest();

            xhr.open('POST', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    // Restore button
                    btnGenerate.disabled = originalBtnDisabled;
                    btnGenerate.innerHTML = originalBtnHTML;

                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);

                            if (response && response.success === true && response.username) {
                                // Set username
                                if (usernameField) {
                                    usernameField.value = response.username;
                                }
                                // Update email preview
                                if (emailPreview) {
                                    emailPreview.textContent = response.username + '@smartpq.simpedis.com';
                                }
                            } else {
                                var errorMsg = (response && response.message) ? response.message : 'Gagal generate username';
                                showAlert(errorMsg, 'error');
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                            showAlert('Gagal memproses response dari server', 'error');
                        }
                    } else {
                        var errorMsg = 'Gagal generate username';
                        if (xhr.status === 404) {
                            errorMsg = 'Endpoint tidak ditemukan. Silakan refresh halaman.';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Terjadi kesalahan di server. Silakan coba lagi.';
                        } else if (xhr.responseText) {
                            try {
                                var errorResponse = JSON.parse(xhr.responseText);
                                if (errorResponse && errorResponse.message) {
                                    errorMsg = errorResponse.message;
                                }
                            } catch (e) {
                                // Use default message
                            }
                        }
                        showAlert(errorMsg, 'error');
                    }
                }
            };

            xhr.onerror = function() {
                // Restore button
                btnGenerate.disabled = originalBtnDisabled;
                btnGenerate.innerHTML = originalBtnHTML;
                showAlert('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.', 'error');
            };

            // Send request
            xhr.send(formData);
        }

        // Expose function globally untuk kompatibilitas
        window.generateUsername = generateUsernameJuri;
        window.generateUsernameJuri = generateUsernameJuri;

        // Show Alert using SweetAlert2
        function showAlert(message, type) {
            let icon = 'info';
            let title = 'Informasi';

            switch (type) {
                case 'success':
                    icon = 'success';
                    title = 'Berhasil';
                    break;
                case 'error':
                    icon = 'error';
                    title = 'Error';
                    break;
                case 'warning':
                    icon = 'warning';
                    title = 'Peringatan';
                    break;
                default:
                    icon = 'info';
                    title = 'Informasi';
            }

            Swal.fire({
                icon: icon,
                title: title,
                text: message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                timer: type === 'success' ? 3000 : null,
                timerProgressBar: type === 'success' ? true : false
            });
        }

        // Reset Form
        function resetForm() {
            $('#formJuri')[0].reset();
            $('#formJuri .invalid-feedback').text('');
            $('#formJuri .form-control').removeClass('is-invalid');
            $('#modalJuriLabel').text('Tambah Juri Baru');
            $('#btnSaveJuri').html('<i class="fas fa-save"></i> Simpan');
            currentJuriId = null;
            $('#emailPreview').text('username@smartpq.simpedis.com');
            // Set default status to Aktif
            $('#Status').val('Aktif');
            // Reset RoomId selection
            $('#RoomId').val('');
            // Set default TypeUjian
            $('#TypeUjian').val('munaqosah');
            // Set default password
            $('#useDefaultPassword').prop('checked', true);
            $('#PasswordJuri').val('JuriTpqSmart').prop('readonly', true);
            $('#ConfirmPasswordJuri').val('JuriTpqSmart').prop('readonly', true);

            // Reset TPQ dropdown jika hanya ada satu TPQ
            <?php if (count($tpqDropdown) == 1): ?>
                $('#IdTpq').val('<?= $tpqDropdown[0]['IdTpq'] ?>');
            <?php endif; ?>
            // Set TypeUjian berdasar TPQ yang terpilih
            const hasTpq = $('#IdTpq').val() && $('#IdTpq').val() !== '' && $('#IdTpq').val() !== '0';
            $('#TypeUjian').val(hasTpq ? 'pra-munaqosah' : 'munaqosah');
        }


        // Fungsi untuk setup event handlers (dipanggil setelah modal dibuka)
        function setupGenerateUsernameHandlers() {
            // Button Generate Username
            var btnGenerateEl = document.getElementById('btnGenerateUsername');
            if (btnGenerateEl && !btnGenerateEl.hasAttribute('data-handler-attached')) {
                btnGenerateEl.setAttribute('data-handler-attached', 'true');
                btnGenerateEl.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof window.generateUsernameJuri === 'function') {
                        window.generateUsernameJuri();
                    }
                });
            }

            // Change event untuk IdGrupMateriUjian
            var grupMateriEl = document.getElementById('IdGrupMateriUjian');
            if (grupMateriEl && !grupMateriEl.hasAttribute('data-handler-attached')) {
                grupMateriEl.setAttribute('data-handler-attached', 'true');
                grupMateriEl.addEventListener('change', function() {
                    // Atur TypeUjian otomatis berdasarkan pilihan TPQ
                    var idTpqEl = document.getElementById('IdTpq');
                    var typeUjianEl = document.getElementById('TypeUjian');
                    var hasTpq = idTpqEl && idTpqEl.value && idTpqEl.value !== '' && idTpqEl.value !== '0';

                    if (typeUjianEl) {
                        typeUjianEl.value = hasTpq ? 'pra-munaqosah' : 'munaqosah';
                    }

                    // Auto generate jika grup materi sudah dipilih
                    if (this.value) {
                        setTimeout(function() {
                            if (typeof window.generateUsernameJuri === 'function') {
                                window.generateUsernameJuri();
                            }
                        }, 200);
                    }
                });
            }

            // Change event untuk IdTpq
            var idTpqEl = document.getElementById('IdTpq');
            if (idTpqEl && !idTpqEl.hasAttribute('data-handler-attached')) {
                idTpqEl.setAttribute('data-handler-attached', 'true');
                idTpqEl.addEventListener('change', function() {
                    // Atur TypeUjian otomatis berdasarkan pilihan TPQ
                    var typeUjianEl = document.getElementById('TypeUjian');
                    var hasTpq = this.value && this.value !== '' && this.value !== '0';

                    if (typeUjianEl) {
                        typeUjianEl.value = hasTpq ? 'pra-munaqosah' : 'munaqosah';
                    }

                    // Auto generate jika grup materi sudah dipilih
                    var grupMateriEl = document.getElementById('IdGrupMateriUjian');
                    if (grupMateriEl && grupMateriEl.value) {
                        setTimeout(function() {
                            if (typeof window.generateUsernameJuri === 'function') {
                                window.generateUsernameJuri();
                            }
                        }, 200);
                    }
                });
            }
        }

        // Setup handlers saat document ready (jika elemen sudah ada)
        setupGenerateUsernameHandlers();

        // Toggle Password Visibility
        $(document).on('click', '#togglePasswordBtn', function() {
            const passwordField = $('#PasswordJuri');
            const icon = $(this).find('i');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Toggle Edit Password Visibility
        $(document).on('click', '#toggleEditPasswordBtn', function() {
            const passwordField = $('#editPasswordJuri');
            const icon = $(this).find('i');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Use Default Password Checkbox
        $(document).on('change', '#useDefaultPassword', function() {
            const passwordField = $('#PasswordJuri');
            const confirmField = $('#ConfirmPasswordJuri');

            if ($(this).is(':checked')) {
                passwordField.val('JuriTpqSmart').prop('readonly', true);
                confirmField.val('JuriTpqSmart').prop('readonly', true);
            } else {
                passwordField.val('').prop('readonly', false);
                confirmField.val('').prop('readonly', false);
            }
        });

        // Use Default Password Edit Checkbox
        $(document).on('change', '#useDefaultPasswordEdit', function() {
            const passwordField = $('#editPasswordJuri');
            const confirmField = $('#editConfirmPasswordJuri');

            if ($(this).is(':checked')) {
                passwordField.val('JuriTpqSmart').prop('readonly', true);
                confirmField.val('JuriTpqSmart').prop('readonly', false);
            } else {
                passwordField.val('').prop('readonly', false);
                confirmField.val('').prop('readonly', false);
            }
        });

        // Form Submit
        $('#formJuri').submit(function(e) {
            e.preventDefault();

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formData = new FormData(this);
            let url = currentJuriId ?
                '<?= base_url('backend/munaqosah/updateJuri') ?>/' + currentJuriId :
                '<?= base_url('backend/munaqosah/saveJuri') ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalJuri').modal('hide');
                        // Reload halaman untuk menampilkan data terbaru
                        location.reload();
                    } else {
                        // Show validation errors
                        if (response.errors) {
                            let errorMessages = [];
                            Object.keys(response.errors).forEach(function(key) {
                                let field = $('[name="' + key + '"]');
                                field.addClass('is-invalid');
                                field.siblings('.invalid-feedback').text(response.errors[key]);
                                errorMessages.push(response.errors[key]);
                            });

                            // Show detailed error with SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: '<ul class="text-left"><li>' + errorMessages.join('</li><li>') + '</li></ul>',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        } else {
                            showAlert(response.message, 'error');
                        }
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat menyimpan data', 'error');
                }
            });
        });

        // Edit Password Button
        $(document).on('click', '.btn-edit-password', function() {
            currentJuriId = $(this).data('id');
            const username = $(this).data('username') || '';
            // Set username for accessibility (hidden field)
            $('#editUsernameJuri').val(username);
            $('#modalEditPassword').modal('show');
            // Reset form
            $('#formEditPassword')[0].reset();
            // Restore username after reset
            $('#editUsernameJuri').val(username);
            $('#useDefaultPasswordEdit').prop('checked', false);
        });

        // Edit Room Button
        $(document).on('click', '.btn-edit-room', function() {
            currentJuriId = $(this).data('id');
            const roomId = $(this).data('room') || '';
            $('#editRoomId').val(roomId);
            $('#modalEditRoom').modal('show');
        });

        // Form Edit Room Submit
        $('#formEditRoom').submit(function(e) {
            e.preventDefault();

            const roomId = $('#editRoomId').val();

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/updateRoomJuri') ?>/' + currentJuriId,
                type: 'POST',
                data: {
                    RoomId: roomId
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalEditRoom').modal('hide');
                        // Reload halaman untuk menampilkan data terbaru
                        location.reload();
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat mengupdate room', 'error');
                }
            });
        });

        // Delete Button
        $(document).on('click', '.btn-delete', function() {
            currentJuriId = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data juri ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with delete
                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/deleteJuri') ?>/' + currentJuriId,
                        type: 'POST',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                // Reload halaman untuk menampilkan data terbaru
                                location.reload();
                            } else {
                                showAlert(response.message, 'error');
                            }
                        },
                        error: function() {
                            showAlert('Terjadi kesalahan saat menghapus data', 'error');
                        }
                    });
                }
            });
        });

        // Form Edit Password Submit
        $('#formEditPassword').submit(function(e) {
            e.preventDefault();

            // Validate password match
            const password = $('#editPasswordJuri').val();
            const confirmPassword = $('#editConfirmPasswordJuri').val();

            if (password !== confirmPassword) {
                showAlert('Password dan konfirmasi password tidak sama', 'error');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/updatePasswordJuri') ?>/' + currentJuriId,
                type: 'POST',
                data: {
                    password: password
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalEditPassword').modal('hide');
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat mengupdate password', 'error');
                }
            });
        });

        // Modal Events
        $('#modalJuri').on('hidden.bs.modal', function() {
            resetForm();
        });

        $('#modalEditRoom').on('hidden.bs.modal', function() {
            $('#formEditRoom')[0].reset();
        });

        // Auto generate username when modal is shown and TPQ is pre-selected
        var modalJuriEl = document.getElementById('modalJuri');
        if (modalJuriEl) {
            // Event saat modal ditampilkan
            modalJuriEl.addEventListener('shown.bs.modal', function() {
                // Setup handlers setelah modal dibuka
                setupGenerateUsernameHandlers();

                setTimeout(function() {
                    <?php if (count($tpqDropdown) == 1): ?>
                        // Auto generate username if TPQ is pre-selected
                        var idGrupMateriUjian = document.getElementById('IdGrupMateriUjian');
                        if (idGrupMateriUjian && idGrupMateriUjian.value) {
                            if (typeof window.generateUsernameJuri === 'function') {
                                window.generateUsernameJuri();
                            }
                        }
                    <?php endif; ?>
                }, 300);
            });

            // Event saat modal akan ditampilkan
            modalJuriEl.addEventListener('show.bs.modal', function() {
                // Reset form state
                var usernameField = document.getElementById('UsernameJuri');
                var emailPreview = document.getElementById('emailPreview');
                if (usernameField) {
                    usernameField.value = '';
                }
                if (emailPreview) {
                    emailPreview.textContent = 'username@smartpq.simpedis.com';
                }
            });
        }

        // ==================== PANITIA MUNAQOSAH ====================
        let tablePanitia;
        let currentPanitiaId = null;

        // Inisialisasi DataTable untuk Panitia
        try {
            var $tablePanitia = $('#tablePanitia');
            if ($tablePanitia.length) {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablePanitia')) {
                    try {
                        $tablePanitia.DataTable().destroy();
                    } catch (e) {
                        console.warn('Error destroying existing DataTable:', e);
                    }
                }
                setTimeout(function() {
                    if (!$.fn.DataTable.isDataTable('#tablePanitia')) {
                        if (typeof initializeDataTableUmum === 'function') {
                            try {
                                initializeDataTableUmum('#tablePanitia', true, true, ['excel', 'pdf'], {
                                    "order": [],
                                    "columnDefs": [{
                                        "targets": -1,
                                        "orderable": false,
                                        "searchable": false
                                    }]
                                });
                                tablePanitia = $('#tablePanitia').DataTable();
                            } catch (e) {
                                console.error('Error calling initializeDataTableUmum:', e);
                            }
                        }
                    }
                }, 100);
            }
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }

        // Generate Username Panitia
        function generateUsernamePanitia() {
            var idTpq = document.getElementById('IdTpqPanitia');
            var usernameField = document.getElementById('UsernamePanitia');
            var emailPreview = document.getElementById('emailPreviewPanitia');
            var btnGenerate = document.getElementById('btnGenerateUsernamePanitia');

            if (!idTpq || !idTpq.value) {
                showAlert('Pilih TPQ terlebih dahulu', 'warning');
                return;
            }

            var originalBtnHTML = btnGenerate.innerHTML;
            var originalBtnDisabled = btnGenerate.disabled;

            btnGenerate.disabled = true;
            btnGenerate.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generate...';

            var formData = new FormData();
            formData.append('IdTpq', idTpq.value);

            var csrfName = '<?= csrf_token() ?>';
            var csrfHash = '<?= csrf_hash() ?>';
            formData.append(csrfName, csrfHash);

            var url = '<?= base_url('backend/munaqosah/generate-username-panitia') ?>';
            var xhr = new XMLHttpRequest();

            xhr.open('POST', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    btnGenerate.disabled = originalBtnDisabled;
                    btnGenerate.innerHTML = originalBtnHTML;

                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response && response.success === true && response.username) {
                                if (usernameField) {
                                    usernameField.value = response.username;
                                }
                                if (emailPreview) {
                                    emailPreview.textContent = response.username + '@smartpq.simpedis.com';
                                }
                            } else {
                                var errorMsg = (response && response.message) ? response.message : 'Gagal generate username';
                                showAlert(errorMsg, 'error');
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                            showAlert('Gagal memproses response dari server', 'error');
                        }
                    } else {
                        showAlert('Gagal generate username', 'error');
                    }
                }
            };

            xhr.onerror = function() {
                btnGenerate.disabled = originalBtnDisabled;
                btnGenerate.innerHTML = originalBtnHTML;
                showAlert('Tidak dapat terhubung ke server', 'error');
            };

            xhr.send(formData);
        }

        // Setup handlers untuk Panitia
        $('#IdTpqPanitia').on('change', function() {
            if (this.value) {
                setTimeout(function() {
                    generateUsernamePanitia();
                }, 200);
            }
        });

        $('#btnGenerateUsernamePanitia').on('click', function(e) {
            e.preventDefault();
            generateUsernamePanitia();
        });

        // Toggle Password Panitia
        $(document).on('click', '#togglePasswordPanitiaBtn', function() {
            const passwordField = $('#PasswordPanitia');
            const icon = $(this).find('i');
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Use Default Password Panitia
        $(document).on('change', '#useDefaultPasswordPanitia', function() {
            const passwordField = $('#PasswordPanitia');
            if ($(this).is(':checked')) {
                passwordField.val('PanitiaTpqSmart').prop('readonly', true);
            } else {
                passwordField.val('').prop('readonly', false);
            }
        });

        // Form Submit Panitia
        $('#formPanitia').submit(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formData = new FormData(this);
            let url = '<?= base_url('backend/munaqosah/save-panitia') ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalPanitia').modal('hide');
                        location.reload();
                    } else {
                        if (response.errors) {
                            let errorMessages = [];
                            Object.keys(response.errors).forEach(function(key) {
                                let field = $('[name="' + key + '"]');
                                field.addClass('is-invalid');
                                field.siblings('.invalid-feedback').text(response.errors[key]);
                                errorMessages.push(response.errors[key]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: '<ul class="text-left"><li>' + errorMessages.join('</li><li>') + '</li></ul>',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            showAlert(response.message, 'error');
                        }
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat menyimpan data', 'error');
                }
            });
        });

        // Edit Password Panitia Button
        $(document).on('click', '.btn-edit-password-panitia', function() {
            currentPanitiaId = $(this).data('id');
            const username = $(this).data('username') || '';
            $('#editUsernamePanitia').val(username);
            $('#modalEditPasswordPanitia').modal('show');
            $('#formEditPasswordPanitia')[0].reset();
            $('#editUsernamePanitia').val(username);
            $('#useDefaultPasswordEditPanitia').prop('checked', false);
        });

        // Toggle Edit Password Panitia
        $(document).on('click', '#toggleEditPasswordPanitiaBtn', function() {
            const passwordField = $('#editPasswordPanitia');
            const icon = $(this).find('i');
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Use Default Password Edit Panitia
        $(document).on('change', '#useDefaultPasswordEditPanitia', function() {
            const passwordField = $('#editPasswordPanitia');
            const confirmField = $('#editConfirmPasswordPanitia');
            if ($(this).is(':checked')) {
                passwordField.val('PanitiaTpqSmart').prop('readonly', true);
                confirmField.val('PanitiaTpqSmart').prop('readonly', false);
            } else {
                passwordField.val('').prop('readonly', false);
                confirmField.val('').prop('readonly', false);
            }
        });

        // Form Edit Password Panitia Submit
        $('#formEditPasswordPanitia').submit(function(e) {
            e.preventDefault();
            const password = $('#editPasswordPanitia').val();
            const confirmPassword = $('#editConfirmPasswordPanitia').val();

            if (password !== confirmPassword) {
                showAlert('Password dan konfirmasi password tidak sama', 'error');
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/update-password-panitia') ?>/' + currentPanitiaId,
                type: 'POST',
                data: {
                    password: password
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalEditPasswordPanitia').modal('hide');
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat mengupdate password', 'error');
                }
            });
        });

        // Delete Panitia Button
        $(document).on('click', '.btn-delete-panitia', function() {
            currentPanitiaId = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data panitia ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/delete-panitia') ?>/' + currentPanitiaId,
                        type: 'POST',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                location.reload();
                            } else {
                                showAlert(response.message, 'error');
                            }
                        },
                        error: function() {
                            showAlert('Terjadi kesalahan saat menghapus data', 'error');
                        }
                    });
                }
            });
        });

        // Reset Form Panitia
        $('#modalPanitia').on('hidden.bs.modal', function() {
            $('#formPanitia')[0].reset();
            $('#formPanitia .invalid-feedback').text('');
            $('#formPanitia .form-control').removeClass('is-invalid');
            $('#emailPreviewPanitia').text('username@smartpq.simpedis.com');
            $('#StatusPanitia').val('Aktif');
            $('#useDefaultPasswordPanitia').prop('checked', true);
            $('#PasswordPanitia').val('PanitiaTpqSmart').prop('readonly', true);
            <?php if (count($tpqDropdown) == 1): ?>
                $('#IdTpqPanitia').val('<?= $tpqDropdown[0]['IdTpq'] ?>');
            <?php else: ?>
                $('#IdTpqPanitia').val('0');
            <?php endif; ?>
        });

        // Auto generate username when modal is shown
        $('#modalPanitia').on('shown.bs.modal', function() {
            setTimeout(function() {
                <?php if (count($tpqDropdown) == 1): ?>
                    var idTpq = document.getElementById('IdTpqPanitia');
                    if (idTpq && idTpq.value) {
                        generateUsernamePanitia();
                    }
                <?php endif; ?>
            }, 300);
        });

    });
</script>
<?= $this->endSection() ?>