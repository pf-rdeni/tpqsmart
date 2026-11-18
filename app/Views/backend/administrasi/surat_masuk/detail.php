<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Kolom Kiri: Informasi Surat Masuk & Berkas -->
        <div class="col-lg-5">
            <div class="card card-primary card-outline shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-envelope-open text-primary mr-2"></i>Informasi Surat Masuk
                    </h3>
                    <a href="<?= base_url('backend/administrasi/surat-masuk/cetak-disposisi/' . $surat['Id']) ?>" 
                       target="_blank" 
                       class="btn btn-outline-danger btn-sm font-weight-bold">
                        <i class="fas fa-print mr-1"></i> Cetak Lembar Disposisi
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <tr>
                            <th style="width: 140px;" class="pl-3 py-2">No. Agenda</th>
                            <td class="py-2"><span class="badge badge-secondary"><?= esc($surat['NoAgenda'] ?: '-') ?></span></td>
                        </tr>
                        <tr>
                            <th class="pl-3 py-2">Nomor Surat</th>
                            <td class="py-2"><strong><?= esc($surat['NomorSurat'] ?: '-') ?></strong></td>
                        </tr>
                        <tr>
                            <th class="pl-3 py-2">Asal Pengirim</th>
                            <td class="py-2"><strong><?= esc($surat['AsalSurat'] ?: '-') ?></strong></td>
                        </tr>
                        <tr>
                            <th class="pl-3 py-2">Perihal / Hal</th>
                            <td class="py-2"><?= esc($surat['Hal'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th class="pl-3 py-2">Tanggal Surat</th>
                            <td class="py-2"><?= !empty($surat['Tanggal']) ? date('d-m-Y', strtotime($surat['Tanggal'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <th class="pl-3 py-2">Tanggal Diterima</th>
                            <td class="py-2"><?= !empty($surat['TanggalTerima']) ? date('d-m-Y', strtotime($surat['TanggalTerima'])) : '-' ?></td>
                        </tr>
                        <tr>
                            <th class="pl-3 py-2">Lampiran</th>
                            <td class="py-2"><?= esc($surat['Lampiran'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <th class="pl-3 py-2">Ringkasan / Catatan</th>
                            <td class="py-2"><?= nl2br(esc($surat['Catatan'] ?: '-')) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Card Berkas Scan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-paperclip mr-2"></i>Berkas Lampiran Scan</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($lampiranList)): ?>
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-file fa-2x mb-2 d-block text-secondary"></i>
                            Tidak ada berkas scan yang dilampirkan saat pencatatan.
                        </div>
                    <?php else: ?>
                        <?php foreach ($lampiranList as $l): ?>
                            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                                <div class="text-truncate mr-2">
                                    <i class="fas fa-file-pdf text-danger fa-lg mr-2"></i>
                                    <strong><?= esc($l['NamaFile']) ?></strong>
                                    <?php if (!empty($l['Ukuran'])): ?>
                                        <small class="text-muted d-block"><?= round($l['Ukuran'] / 1024, 1) ?> KB</small>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= base_url('uploads/surat_masuk/' . $l['PathFile']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> Buka
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?= base_url('backend/administrasi/surat-masuk') ?>" class="btn btn-secondary btn-block mb-4">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Surat Masuk
            </a>
        </div>

        <!-- Kolom Kanan: Disposisi Staf -->
        <div class="col-lg-7">
            <!-- Form Buat Disposisi -->
            <div class="card card-warning card-outline shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-share-square mr-2"></i>Beri Instruksi / Lembar Disposisi Baru</h3>
                </div>
                <form action="<?= base_url('backend/administrasi/surat-masuk/disposisi/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="IdSurat" value="<?= $surat['Id'] ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tujukan Disposisi Ke Staf / User <span class="text-danger">*</span></label>
                                <select name="KeUser" class="form-control font-weight-bold" required>
                                    <option value="">-- Pilih Staf Penerima --</option>
                                    <?php foreach ($userList as $u): ?>
                                        <option value="<?= $u['id'] ?>"><?= esc($u['username']) ?> (<?= esc($u['email']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Derajat Prioritas</label>
                                <select name="Prioritas" class="form-control">
                                    <option value="Biasa" selected>Biasa</option>
                                    <option value="Segera">Segera</option>
                                    <option value="Sangat Segera">Sangat Segera</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Batas Waktu (Deadline)</label>
                                <input type="date" name="Deadline" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label>Instruksi / Catatan Pimpinan <span class="text-danger">*</span></label>
                            <textarea name="Instruksi" class="form-control" rows="3" placeholder="Contoh: Mohon dipelajari dan ditindaklanjuti, siapkan draft balasan sebelum rapat..." required></textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-warning font-weight-bold px-4">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim Disposisi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Riwayat Timeline Disposisi -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-2"></i>Riwayat & Tindak Lanjut Disposisi</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($disposisiList)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                            Belum ada disposisi yang dikirimkan untuk surat masuk ini.
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($disposisiList as $d): ?>
                                <div>
                                    <i class="fas fa-envelope-open bg-<?= $d['StatusBaca'] ? 'success' : 'warning' ?>"></i>
                                    <div class="timeline-item shadow-sm">
                                        <span class="time">
                                            <i class="fas fa-clock mr-1"></i> <?= date('d M Y H:i', strtotime($d['CreatedAt'])) ?>
                                        </span>
                                        <h3 class="timeline-header font-weight-bold">
                                            Dari: <span class="text-primary"><?= esc($d['DariUsername'] ?? 'Pimpinan') ?></span>
                                            <i class="fas fa-long-arrow-alt-right mx-1"></i>
                                            Ke: <span class="text-success"><?= esc($d['KeUsername'] ?? 'Staf') ?></span>
                                            <span class="badge badge-<?= $d['Prioritas'] === 'Sangat Segera' ? 'danger' : ($d['Prioritas'] === 'Segera' ? 'warning' : 'secondary') ?> ml-2">
                                                <?= esc($d['Prioritas']) ?>
                                            </span>
                                        </h3>
                                        <div class="timeline-body">
                                            <strong>Instruksi:</strong>
                                            <p class="mb-2"><?= nl2br(esc($d['Instruksi'])) ?></p>

                                            <?php if (!empty($d['Deadline'])): ?>
                                                <small class="text-danger font-weight-bold d-block mb-1">
                                                    <i class="fas fa-hourglass-half mr-1"></i> Deadline: <?= date('d-m-Y', strtotime($d['Deadline'])) ?>
                                                </small>
                                            <?php endif; ?>

                                            <?php if (!empty($d['CatatanBalas'])): ?>
                                                <div class="alert alert-light border mt-2 py-2 mb-0">
                                                    <strong><i class="fas fa-reply mr-1"></i> Tindak Lanjut Staf:</strong>
                                                    <p class="mb-0 small"><?= nl2br(esc($d['CatatanBalas'])) ?></p>
                                                    <small class="text-muted d-block mt-1">Dibaca: <?= date('d-m-Y H:i', strtotime($d['TanggalBaca'])) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="timeline-footer">
                                            <?php if (!$d['StatusBaca']): ?>
                                                <button type="button" class="btn btn-outline-success btn-xs btn-tindak-lanjut" data-id="<?= $d['Id'] ?>">
                                                    <i class="fas fa-check-circle mr-1"></i> Tandai Telah Ditindaklanjuti
                                                </button>
                                            <?php else: ?>
                                                <span class="badge badge-success"><i class="fas fa-check"></i> Selesai Ditindaklanjuti</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.btn-tindak-lanjut').on('click', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Tindak Lanjut Disposisi',
            input: 'textarea',
            inputLabel: 'Tuliskan catatan tindak lanjut / respon dari instruksi pimpinan:',
            inputPlaceholder: 'Contoh: Sudah dikonfirmasi ke panitia terkait...',
            showCancelButton: true,
            confirmButtonText: 'Simpan Respon',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: (catatan) => {
                return $.post('<?= base_url('backend/administrasi/surat-masuk/disposisi/update-status') ?>/' + id, {
                    CatatanBalas: catatan
                }, 'json').then(res => {
                    if (!res.success) {
                        throw new Error(res.message);
                    }
                    return res;
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Berhasil', 'Disposisi berhasil ditandai telah ditindaklanjuti.', 'success').then(() => location.reload());
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
