<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Input Registrasi Antrian</h3>
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

                        <!-- Hidden inputs untuk filter yang dibawa dari halaman sebelumnya -->
                        <?php if (empty($session_id_tpq)): ?>
                            <input type="hidden" id="tpq" value="<?= $selected_tpq ?>">
                            <input type="hidden" id="type" value="<?= $selected_type ?>">
                        <?php else: ?>
                            <input type="hidden" id="tpq" value="<?= $session_id_tpq ?>">
                            <input type="hidden" id="type" value="pra-munaqosah">
                        <?php endif; ?>
                        <input type="hidden" id="group" value="<?= $selected_group ?>">
                        <input type="hidden" id="tahun" value="<?= $selected_tahun ?>">

                        <!-- Info Filter Aktif (untuk konfirmasi) -->
                        <div class="alert alert-info mb-3 py-2">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                <strong>Grup:</strong> <?php
                                $selectedGroupName = '-';
                                foreach ($groups as $group) {
                                    if ($group['IdGrupMateriUjian'] === $selected_group) {
                                        $selectedGroupName = $group['NamaMateriGrup'];
                                        break;
                                    }
                                }
                                echo $selectedGroupName;
                                ?>
                                <?php if (empty($session_id_tpq)): ?>
                                    | <strong>Type:</strong> <?= $types[$selected_type] ?? $selected_type ?>
                                <?php endif; ?>
                            </small>
                        </div>

                        <!-- Input Registrasi -->
                        <div class="card card-warning mb-4">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user-plus"></i> Registrasi Peserta</h3>
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <input type="text" id="queueSearch" class="form-control form-control-lg" placeholder="Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)" inputmode="numeric" maxlength="3">
                                    <div class="input-group-append">
                                        <button class="btn btn-warning" type="button" id="btnScanQR">
                                            <i class="fas fa-qrcode"></i> Scan QR
                                        </button>
                                        <button class="btn btn-danger" type="button" id="btnQueueReset">Reset</button>
                                    </div>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <span class="text-info"><i class="fas fa-info-circle"></i> Auto registrasi akan aktif setelah 3 digit, atau tekan Enter</span>
                                </small>
                            </div>
                        </div>

                        <!-- Status Peserta (Compact untuk Mobile) -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 p-2 bg-light rounded">
                            <div class="d-flex align-items-center mr-2 mb-1 mb-sm-0">
                                <span class="badge badge-info badge-lg mr-1">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div>
                                    <small class="d-block text-muted">Total</small>
                                    <strong class="text-info"><?= $statistics['total'] ?></strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mr-2 mb-1 mb-sm-0">
                                <span class="badge badge-warning badge-lg mr-1">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div>
                                    <small class="d-block text-muted">Antrian</small>
                                    <strong class="text-warning"><?= $statistics['queueing'] ?></strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-1 mb-sm-0">
                                <span class="badge badge-success badge-lg mr-1">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <div>
                                    <small class="d-block text-muted">Progress</small>
                                    <strong class="text-success"><?= $statistics['progress'] ?>%</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Status Ruangan -->
                        <h5 class="mt-3 mb-2">Status Ruangan</h5>
                        <?php if (!empty($rooms)): ?>
                            <?php 
                            $totalRooms = count($rooms);
                            // Jika hanya 1 ruangan, gunakan full width, jika lebih gunakan grid responsif
                            // Desktop: 1 ruangan = full, >1 = 3 kolom | Tablet: 2 kolom | Mobile: 1 kolom
                            $colClass = $totalRooms === 1 
                                ? 'col-12' 
                                : 'col-12 col-sm-6 col-lg-4';
                            ?>
                            <div class="row">
                                <?php foreach ($rooms as $room): ?>
                                    <?php $isOccupied = $room['occupied']; ?>
                                    <div class="<?= $colClass ?> mb-3">
                                        <div class="p-3 rounded shadow-sm room-card <?= $isOccupied ? 'bg-danger text-white' : 'bg-success text-white' ?>">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="mb-0">Ruangan <?= $room['RoomId'] ?></h5>
                                                <?php if ($isOccupied): ?>
                                                    <span class="badge badge-light badge-pill">
                                                        <i class="fas fa-user"></i> Digunakan
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-light badge-pill">
                                                        <i class="fas fa-door-open"></i> Kosong
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($isOccupied && $room['participant']): ?>
                                                <div class="room-participant mb-3">
                                                    <div class="mb-1">
                                                        <strong>No Peserta:</strong> <?= $room['participant']['NoPeserta'] ?>
                                                    </div>
                                                    <div>
                                                        <small><?= $room['participant']['NamaSantri'] ?? '-' ?></small>
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                    <button type="button"
                                                        class="btn btn-light btn-finish-room text-dark"
                                                        data-id="<?= $room['participant']['id'] ?? '' ?>"
                                                        data-nopeserta="<?= $room['participant']['NoPeserta'] ?? '' ?>"
                                                        data-nama="<?= $room['participant']['NamaSantri'] ?? '-' ?>">
                                                        <i class="fas fa-check"></i> Selesai
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-warning btn-exit-room"
                                                        data-id="<?= $room['participant']['id'] ?? '' ?>"
                                                        data-nopeserta="<?= $room['participant']['NoPeserta'] ?? '' ?>"
                                                        data-nama="<?= $room['participant']['NamaSantri'] ?? '-' ?>">
                                                        <i class="fas fa-sign-out-alt"></i> Keluar
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <p class="mb-0">
                                                    <i class="fas fa-door-open mr-1"></i>Ruangan tersedia
                                                </p>
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

                        <!-- Table Antrian -->
                        <h5 class="mt-3 mb-2">Daftar Antrian</h5>
                        <div class="table-responsive">
                            <table id="tableAntrian" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Peserta - Nama Santri</th>
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
                                            <td><?= $row['NoPeserta'] ?> - <?= $row['NamaSantri'] ?? '-' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($status === 0): ?>
                                                        <button type="button" class="btn btn-sm btn-warning btn-open-room"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Masuk
                                                        </button>
                                                    <?php elseif ($status === 1): ?>
                                                        <button type="button" class="btn btn-sm btn-success btn-finish-row"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Selesai
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-warning btn-exit-row"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Keluar
                                                        </button>
                                                    <?php elseif ($status === 2): ?>
                                                        <button type="button" class="btn btn-sm btn-danger btn-wait-row"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-nopeserta="<?= $row['NoPeserta'] ?>"
                                                            data-nama="<?= $row['NamaSantri'] ?? '-' ?>">
                                                            Selesai
                                                        </button>
                                                    <?php endif; ?>
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

<!-- QR Scanner Modal -->
<div class="modal fade" id="modalQRScanner" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width: 100%"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    .badge-lg {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }
    .room-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }
    @media (max-width: 576px) {
        .badge-lg {
            font-size: 0.875rem;
            padding: 0.4rem 0.6rem;
        }
        .bg-light {
            font-size: 0.875rem;
        }
        .room-card {
            margin-bottom: 0.75rem;
        }
        .room-card h5 {
            font-size: 1rem;
        }
        .btn-group-sm {
            font-size: 0.75rem;
        }
    }
</style>
<script>
        $(function() {
        const table = $('#tableAntrian').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            order: [
                [0, 'asc']
            ]
        });

        // Validasi input hanya angka 3 digit
        let autoRegistrasiHandler = null;
        
        $('#queueSearch').on('input', function(e) {
            let value = $(this).val();
            // Hanya terima angka
            value = value.replace(/[^0-9]/g, '');
            // Batasi maksimal 3 digit
            if (value.length > 3) {
                value = value.substring(0, 3);
            }
            $(this).val(value);
            
            // Auto registrasi logic (mengganti handler lama)
            const noPeserta = value.trim();
            
            // Clear any existing timeout and countdown
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            
            // Show auto search indicator - hanya jika sudah 3 digit angka
            if (/^\d{3}$/.test(noPeserta)) {
                // Add visual indicator
                $(this).addClass('border-info');
                
                // Show countdown indicator in placeholder
                const originalPlaceholder = $(this).attr('placeholder');
                let countdown = 1;
                
                const updatePlaceholder = () => {
                    $(this).attr('placeholder', `Auto registrasi dalam ${countdown} detik...`);
                };
                
                updatePlaceholder();
                
                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        $(this).attr('placeholder', originalPlaceholder);
                        $(this).removeClass('border-info');
                        registerAntrian();
                    } else {
                        updatePlaceholder();
                    }
                }, 1000);
                
                // Store interval ID for cleanup
                window.autoSearchCountdown = countdownInterval;
                
                const $input = $(this);
                window.autoSearchTimeout = setTimeout(function() {
                    clearInterval(window.autoSearchCountdown);
                    $input.attr('placeholder', originalPlaceholder);
                    $input.removeClass('border-info');
                    registerAntrian();
                }, 1000); // 1 second delay after user stops typing
            } else {
                // Remove visual indicator if less than 3 digits
                $(this).removeClass('border-info');
                $(this).attr('placeholder', 'Ketikkan atau scan QR No Peserta untuk registrasi (3 digit)');
            }
        });

        // Prevent paste huruf
        $('#queueSearch').on('paste', function(e) {
            const paste = (e.originalEvent || e).clipboardData.getData('text');
            const numbersOnly = paste.replace(/[^0-9]/g, '').substring(0, 3);
            e.preventDefault();
            $(this).val(numbersOnly);
            // Trigger input event untuk auto registrasi
            $(this).trigger('input');
        });

        // Validasi keypress - hanya terima angka
        $('#queueSearch').on('keypress', function(e) {
            // Hanya terima angka (0-9), backspace (8), delete (46), arrow keys
            const charCode = e.which || e.keyCode;
            // Allow: backspace (8), delete (46), arrow keys (37-40), tab (9)
            if ([8, 9, 37, 38, 39, 40, 46].includes(charCode)) {
                return true;
            }
            // Hanya terima angka (48-57)
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
                return false;
            }
            // Batasi 3 digit (cek panjang saat ini)
            if ($(this).val().length >= 3) {
                e.preventDefault();
                return false;
            }
        });

        // Fungsi registrasi antrian
        function registerAntrian() {
            // Clear any auto search timeouts when manually clicking
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $('#queueSearch').removeClass('border-info');
            $('#queueSearch').attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');

            const noPeserta = $('#queueSearch').val().trim();
            const idGrupMateri = $('#group').val() || $('input#group').val();
            const typeUjian = $('#type').val() || $('input#type').val();
            const tahunAjaran = $('#tahun').val() || $('input#tahun').val();

            // Validasi input
            if (!noPeserta) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Masukkan nomor peserta terlebih dahulu'
                });
                return;
            }

            // Validasi harus 3 digit angka
            if (!/^\d{3}$/.test(noPeserta)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Nomor peserta harus 3 digit angka'
                });
                $('#queueSearch').focus();
                return;
            }

            if (!idGrupMateri) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih grup materi ujian terlebih dahulu'
                });
                return;
            }

            if (!typeUjian) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih type ujian terlebih dahulu'
                });
                return;
            }

            if (!tahunAjaran) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Tahun ajaran tidak boleh kosong'
                });
                return;
            }

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mendaftarkan peserta ke antrian',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim request AJAX
            $.ajax({
                url: '<?= base_url('backend/munaqosah/register-antrian-ajax') ?>',
                type: 'POST',
                data: {
                    NoPeserta: noPeserta,
                    IdGrupMateriUjian: idGrupMateri,
                    TypeUjian: typeUjian,
                    IdTahunAjaran: tahunAjaran,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                            didOpen: () => {
                                // Reset input
                                $('#queueSearch').val('');
                            }
                        }).then(() => {
                            // Set flag untuk auto focus setelah reload
                            sessionStorage.setItem('autoFocusQueueSearch', 'true');
                            // Reload halaman untuk memperbarui tabel setelah auto close
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat registrasi'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan saat registrasi';
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
        }

        // Event handler untuk tombol registrasi
        $('#btnQueueRegister').on('click', function() {
            registerAntrian();
        });

        // Event handler untuk Enter key
        $('#queueSearch').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                registerAntrian();
            }
        });

        // Event handler untuk reset
        $('#btnQueueReset').on('click', function() {
            // Clear any auto search timeouts
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $('#queueSearch').val('');
            $('#queueSearch').removeClass('border-info');
            $('#queueSearch').attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');
        });

        // QR Scanner
        let html5QrcodeScanner = null;

        $('#btnScanQR').click(function() {
            $('#modalQRScanner').modal('show');

            // Initialize QR scanner only once
            if (typeof Html5QrcodeScanner !== 'undefined') {
                // Clear existing scanner if any
                if (html5QrcodeScanner) {
                    try {
                        html5QrcodeScanner.clear();
                    } catch (e) {
                        console.log('Error clearing scanner:', e);
                    }
                }

                // Initialize new scanner
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    }
                );

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'QR Scanner library tidak tersedia'
                });
            }
        });

        // Clear scanner when modal is closed
        $('#modalQRScanner').on('hidden.bs.modal', function() {
            if (html5QrcodeScanner) {
                try {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                } catch (e) {
                    console.log('Error clearing scanner:', e);
                }
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            $('#queueSearch').val(decodedText);
            $('#modalQRScanner').modal('hide');

            // Auto register antrian after QR scan
            registerAntrian();
        }

        function onScanFailure(error) {
            // Handle scan failure silently
        }


        // Clear auto search timeout and indicators when user focuses input
        $('#queueSearch').on('focus', function() {
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $(this).removeClass('border-info');
            $(this).attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');
        });

        // Clear indicators when user clicks away
        $('#queueSearch').on('blur', function() {
            if (window.autoSearchTimeout) {
                clearTimeout(window.autoSearchTimeout);
            }
            if (window.autoSearchCountdown) {
                clearInterval(window.autoSearchCountdown);
            }
            $(this).removeClass('border-info');
            $(this).attr('placeholder', 'Ketik atau scan QR no peserta untuk registrasi');
        });

        // Event handler untuk delete antrian dengan SweetAlert2
        $('.btn-delete-antrian').on('click', function(e) {
            e.preventDefault();
            const link = $(this);
            const url = link.attr('href');
            const confirmMessage = link.data('confirm') || 'Apakah Anda yakin ingin menghapus data ini?';

            Swal.fire({
                title: 'Konfirmasi',
                text: confirmMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        // Event handler untuk tombol In (auto assign room)
        $('.btn-open-room').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mencari ruangan kosong',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim request AJAX untuk auto assign room
            $.ajax({
                url: `<?= base_url('backend/munaqosah/auto-assign-room-ajax') ?>/${id}`,
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: response.message || 'Tidak ada ruangan kosong saat ini'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan saat mengassign ruangan';
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

        // Event handler untuk button Selesai dari room card
        $('.btn-finish-room').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            if (!id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID antrian tidak ditemukan'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Tandai peserta ${noPeserta} - ${nama} selesai ujian?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 2,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta ditandai selesai',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
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
                }
            });
        });

        // Event handler untuk button Keluar dari room card
        $('.btn-exit-room').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            if (!id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID antrian tidak ditemukan'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Keluarkan peserta ${noPeserta} - ${nama} dari ruangan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Keluarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 0,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta dikembalikan ke antrian menunggu',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
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
                }
            });
        });

        // Event handler untuk button Selesai dari tabel
        $('.btn-finish-row').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi',
                text: `Tandai peserta ${noPeserta} - ${nama} selesai ujian?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 2,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta ditandai selesai',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
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
                }
            });
        });

        // Event handler untuk button Keluar dari tabel
        $('.btn-exit-row').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi',
                text: `Keluarkan peserta ${noPeserta} - ${nama} dari ruangan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Keluarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 0,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta dikembalikan ke antrian menunggu',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
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
                }
            });
        });

        // Event handler untuk button Tunggu dari tabel
        $('.btn-wait-row').on('click', function() {
            const id = $(this).data('id');
            const noPeserta = $(this).data('nopeserta');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi',
                text: `Kembalikan peserta ${noPeserta} - ${nama} ke antrian menunggu?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Kembalikan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengupdate status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `<?= base_url('backend/munaqosah/update-status-antrian-ajax') ?>/${id}`,
                        type: 'POST',
                        data: {
                            status: 0,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Peserta dikembalikan ke antrian menunggu',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Gagal mengupdate status'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan saat mengupdate status';
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
                }
            });
        });

        // Auto focus ke input registrasi setelah reload (jika ada flag)
        if (sessionStorage.getItem('autoFocusQueueSearch') === 'true') {
            sessionStorage.removeItem('autoFocusQueueSearch');
            setTimeout(function() {
                $('#queueSearch').focus();
            }, 500);
        }
    });
</script>
<?= $this->endSection() ?>

