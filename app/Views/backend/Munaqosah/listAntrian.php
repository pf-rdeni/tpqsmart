<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Antrian Grup Materi Ujian</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('backend/munaqosah/input-antrian') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Registrasi Antrian
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form method="get" action="<?= base_url('backend/munaqosah/antrian') ?>" class="mb-4">
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-4">
                                    <label for="group">Grup Materi Ujian</label>
                                    <select name="group" id="group" class="form-control">
                                        <?php foreach ($groups as $group): ?>
                                            <option value="<?= $group['IdGrupMateriUjian'] ?>"
                                                <?= ($selected_group === $group['IdGrupMateriUjian']) ? 'selected' : '' ?>>
                                                <?= $group['NamaMateriGrup'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="type">Type Ujian</label>
                                    <select name="type" id="type" class="form-control">
                                        <?php foreach ($types as $typeValue => $typeLabel): ?>
                                            <option value="<?= $typeValue ?>" <?= ($selected_type === $typeValue) ? 'selected' : '' ?>>
                                                <?= $typeLabel ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="tahun">Tahun Ajaran</label>
                                    <input type="text" readonly class="form-control" id="tahun" name="tahun"
                                        value="<?= $selected_tahun ?>">
                                </div>
                                <div class="form-group col-md-2 text-md-right">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="row text-white mb-3">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="p-3 rounded" style="background-color: #00a0e9;">
                                    <h5 class="mb-1">Total Peserta</h5>
                                    <h2 class="mb-0">
                                        <?= $statistics['total'] ?>
                                    </h2>
                                    <small>Teregister</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="p-3 rounded" style="background-color: #28a745;">
                                    <h5 class="mb-1">Sudah diuji</h5>
                                    <h2 class="mb-0">
                                        <?= $statistics['completed'] ?>
                                    </h2>
                                    <small><?= $statistics['total'] > 0 ? round(($statistics['completed'] / max($statistics['total'], 1)) * 100) : 0 ?>% selesai</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="p-3 rounded" style="background-color: #f6c23e; color: #1f2d3d;">
                                    <h5 class="mb-1">Antrian ujian</h5>
                                    <h2 class="mb-0">
                                        <?= $statistics['queueing'] ?>
                                    </h2>
                                    <?php $waitingPercentage = $statistics['total'] > 0 ? round(($statistics['queueing'] / max($statistics['total'], 1)) * 100) : 0; ?>
                                    <small><?= $waitingPercentage ?>% menunggu</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="p-3 rounded" style="background-color: #3b5998;">
                                    <h5 class="mb-1">Progress</h5>
                                    <h2 class="mb-0">
                                        <?= $statistics['progress'] ?>%
                                    </h2>
                                    <small>Tingkat penyelesaian</small>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Status Ruangan</h5>
                        <?php if (!empty($rooms)): ?>
                            <div class="row">
                                <?php foreach ($rooms as $room): ?>
                                    <?php $isOccupied = $room['occupied']; ?>
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="p-3 rounded shadow-sm room-card <?= $isOccupied ? 'bg-danger text-white' : 'bg-success text-white' ?>">
                                            <h5 class="mb-2">Ruangan <?= $room['RoomId'] ?></h5>
                                            <?php if ($isOccupied && $room['participant']): ?>
                                                <div class="room-participant">
                                                    <strong>No Peserta:</strong> <?= $room['participant']['NoPeserta'] ?><br>
                                                    <span><?= $room['participant']['NamaSantri'] ?? '-' ?></span>
                                                </div>
                                            <?php else: ?>
                                                <p class="mb-0">Kosong</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Belum ada ruangan terdaftar untuk grup materi dan tipe ujian ini. Tambahkan RoomId pada data juri.
                            </div>
                        <?php endif; ?>

                        <div class="input-group my-4">
                            <input type="text" id="queueSearch" class="form-control" placeholder="Ketik atau scan QR no peserta">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="btnQueueSearch">Cari</button>
                                <button class="btn btn-secondary" type="button" id="btnQueueReset">Reset</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tableAntrian" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Peserta</th>
                                        <th>Nama Peserta</th>
                                        <th>Room</th>
                                        <th>Status</th>
                                        <th>Type Ujian</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($queue as $row): ?>
                                        <?php
                                        $status = (int) ($row['Status'] ?? 0);
                                        $statusLabel = 'Menunggu';
                                        $badgeClass = 'badge-warning';
                                        if ($status === 1) {
                                            $statusLabel = 'Sedang Ujian';
                                            $badgeClass = 'badge-danger';
                                        } elseif ($status === 2) {
                                            $statusLabel = 'Selesai';
                                            $badgeClass = 'badge-success';
                                        }
                                        $typeResolved = $row['TypeUjian'] ?? ($row['TypeUjianResolved'] ?? '-');
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['NoPeserta'] ?></td>
                                            <td><?= $row['NamaSantri'] ?? '-' ?></td>
                                            <td>
                                                <?php if (!empty($row['RoomId'])): ?>
                                                    <span class="badge badge-info"><?= $row['RoomId'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                            </td>
                                            <td><?= $typeResolved ?></td>
                                            <td><?= !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($status === 0): ?>
                                                        <button type="button" class="btn btn-sm btn-danger btn-open-room"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            In
                                                        </button>
                                                    <?php elseif ($status === 1): ?>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>" method="post" class="mr-1">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="2">
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Tandai peserta selesai?')">Keluar</button>
                                                        </form>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>" method="post">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="0">
                                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Kembalikan peserta ke antrian menunggu?')">Tunggu</button>
                                                        </form>
                                                    <?php elseif ($status === 2): ?>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>" method="post">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="0">
                                                            <button type="submit" class="btn btn-sm btn-secondary">Tunggu</button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('backend/munaqosah/delete-antrian/' . $row['id']) ?>"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        Del
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalPilihRoom" tabindex="-1" role="dialog" aria-labelledby="modalPilihRoomLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formPilihRoom" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPilihRoomLabel">Pilih Ruangan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="status" value="1">
                    <div class="form-group">
                        <label>No Peserta</label>
                        <input type="text" id="modalNoPeserta" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Peserta</label>
                        <input type="text" id="modalNamaPeserta" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modalRoomId">Pilih Room</label>
                        <select name="room_id" id="modalRoomId" class="form-control" required>
                        </select>
                        <small class="form-text text-muted">Hanya menampilkan ruangan yang kosong.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Masukkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const roomStatuses = <?= json_encode($rooms ?? []) ?>;

    $(function() {
        const table = $('#tableAntrian').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            order: [
                [0, 'asc']
            ]
        });

        $('#btnQueueSearch').on('click', function() {
            const value = $('#queueSearch').val();
            table.search(value).draw();
        });

        $('#queueSearch').on('keyup', function(e) {
            if (e.key === 'Enter') {
                table.search(this.value).draw();
            }
        });

        $('#btnQueueReset').on('click', function() {
            $('#queueSearch').val('');
            table.search('').draw();
        });

        $('.btn-open-room').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            const availableRooms = roomStatuses.filter(room => !room.occupied);

            if (availableRooms.length === 0) {
                alert('Tidak ada ruangan kosong saat ini.');
                return;
            }

            const formAction = `<?= base_url('backend/munaqosah/update-status-antrian') ?>/` + id;
            $('#formPilihRoom').attr('action', formAction);
            $('#modalNoPeserta').val(noPeserta);
            $('#modalNamaPeserta').val(nama);

            const select = $('#modalRoomId');
            select.empty();

            availableRooms.forEach(room => {
                select.append(new Option(room.RoomId, room.RoomId));
            });

            $('#modalPilihRoom').modal('show');
        });

        $('#modalPilihRoom').on('hidden.bs.modal', function() {
            $('#formPilihRoom').trigger('reset');
        });
    });
</script>
<?= $this->endSection() ?>