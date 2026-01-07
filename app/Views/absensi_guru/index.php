<?= $this->extend('frontend/template/publicTemplate'); ?>

<?= $this->section('content'); ?>
    <style>
        .header-title {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .card-teacher {
            transition: transform 0.2s;
        }
        .card-teacher:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .section-header {
            background-color: #f4f6f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 5px solid;
        }
        .section-belum { border-color: #dc3545; }
        .section-sudah { border-color: #28a745; }
    </style>

            <?php if (!empty($hasAction) && $hasAction): ?>
                
                <div class="header-title">
                    <h2 class="display-5"><?= esc($kegiatan['NamaKegiatan']) ?></h2>
                    <p class="lead">
                        <?= date('d F Y', strtotime($kegiatan['Tanggal'])) ?> <br>
                        <span class="badge badge-info"><?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> - <?= date('H:i', strtotime($kegiatan['JamSelesai'])) ?></span>
                    </p>
                </div>

                <!-- Statistics Widgets -->
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $stats['total'] ?></h3>
                                <p>Total Guru</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $stats['hadir'] ?></h3>
                                <p>Hadir</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $stats['izin'] ?></h3>
                                <p>Izin</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= $stats['sakit'] ?></h3>
                                <p>Sakit</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-procedures"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= $stats['belum'] ?></h3>
                                <p>Belum Hadir</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Cari Nama Guru, TPQ, atau Desa...">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Belum Hadir Section -->
                <h4 class="section-header section-belum text-danger"><i class="fas fa-user-times"></i> Belum Hadir (<span id="count-belum"><?= count($belumHadir) ?></span>)</h4>
                <div class="row" id="list-belum">
                    <?php foreach ($belumHadir as $guru): ?>
                        <div class="col-md-3 col-sm-6 teacher-col" id="card-<?= $guru->Id ?>">
                            <div class="card card-outline card-danger card-teacher h-100">
                                <div class="card-body box-profile text-center d-flex flex-column">
                                    <div class="text-center mb-2">
                                         <?php 
                                            // Handling Photo profile
                                            $imgUrl = base_url('template/backend/dist/img/user1-128x128.jpg'); // Default
                                         ?>
                                        <img class="profile-user-img img-fluid img-circle"
                                             src="<?= $imgUrl ?>"
                                             alt="User profile picture" style="height: 100px; width: 100px; object-fit: cover;">
                                    </div>
                                    <h5 class="profile-username text-center font-weight-bold"><?= esc($guru->NamaGuru) ?></h5>
                                    
                                    <div class="mt-auto">
                                        <?php if(!empty($guru->NamaTpq)): ?>
                                            <p class="text-muted text-center mb-0 small"><i class="fas fa-school mr-1"></i> <?= esc($guru->NamaTpq) ?></p>
                                        <?php endif; ?>
                                        <?php if(!empty($guru->KelurahanDesa)): ?>
                                            <p class="text-muted text-center mb-2 small"><i class="fas fa-map-marker-alt mr-1"></i> <?= esc($guru->KelurahanDesa) ?></p>
                                        <?php endif; ?>

                                        <?php if(!empty($guru->NoHp)): ?>
                                            <p class="text-muted text-center mb-1 small"><i class="fas fa-phone mr-1"></i> <?= esc($guru->NoHp) ?></p>
                                        <?php endif; ?>
                                        
                                        <button onclick="tandaiHadir('<?= $guru->Id ?>', '<?= esc($guru->NamaGuru) ?>')" class="btn btn-primary btn-block mt-3">
                                            <b><i class="fas fa-edit"></i> ABSEN</b>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-12 text-center" id="no-data-belum" style="display:none;"><p class="text-muted">Tidak ada data ditemukan</p></div>
                </div>
                <!-- Pagination for Belum Hadir -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center" id="pagination-belum"></ul>
                </nav>


                <hr class="my-4">

                <!-- Sudah Hadir Section -->
                <h4 class="section-header section-sudah text-success"><i class="fas fa-user-check"></i> Sudah Hadir (<span id="count-sudah"><?= count($sudahHadir) ?></span>)</h4>
                <div class="row" id="list-sudah">
                    <?php foreach ($sudahHadir as $guru): ?>
                        <?php
                            $status = $guru->StatusKehadiran;
                            $cardClass = 'card-success'; // Default Hadir
                            $badgeClass = 'badge-success';

                            if ($status == 'Izin') {
                                $cardClass = 'card-warning';
                                $badgeClass = 'badge-warning';
                            } elseif ($status == 'Sakit') {
                                $cardClass = 'card-primary'; // Blue
                                $badgeClass = 'badge-primary';
                            }
                        ?>
                        <div class="col-md-3 col-sm-6 teacher-col">
                            <div class="card card-outline <?= $cardClass ?> h-100">
                                <div class="card-body box-profile d-flex flex-column">
                                    <div class="text-center mb-2">
                                        <?php 
                                            $imgUrl = base_url('template/backend/dist/img/user1-128x128.jpg');
                                        ?>
                                        <img class="img-circle" src="<?= $imgUrl ?>" alt="User" style="width: 50px; height: 50px; object-fit: cover;">
                                    </div>
                                    <h5 class="text-center font-weight-bold mb-1" style="font-size: 1rem;"><?= esc($guru->NamaGuru) ?></h5>
                                    
                                    <div class="mt-auto">
                                        <?php if(!empty($guru->NamaTpq)): ?>
                                            <p class="text-muted text-center mb-0 small"><i class="fas fa-school mr-1"></i> <?= esc($guru->NamaTpq) ?></p>
                                        <?php endif; ?>
                                        
                                        <p class="text-muted text-center small mb-0 mt-2">
                                            <i class="far fa-clock"></i> <?= date('H:i', strtotime($record->WaktuAbsen ?? 'now')) ?>
                                            <span class="badge <?= $badgeClass ?> ml-1"><?= $guru->StatusKehadiran ?></span>
                                        </p>
                                        <?php if(!empty($guru->Keterangan)): ?>
                                            <p class="text-muted text-center small mb-0 font-italic">"<?= esc($guru->Keterangan) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-12 text-center" id="no-data-sudah" style="display:none;"><p class="text-muted">Tidak ada data ditemukan</p></div>
                </div>
                <!-- Pagination for Sudah Hadir -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center" id="pagination-sudah"></ul>
                </nav>

            <?php else: ?>
                <div class="alert alert-warning mt-5 text-center">
                    <h4><i class="icon fas fa-exclamation-triangle"></i> Info</h4>
                    <?= $message ?? 'Tidak ada data.' ?>
                </div>
            <?php endif; ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    const ITEMS_PER_PAGE = 8;

    $(document).ready(function() {
        // Initialize pagination
        initPagination('list-belum', 'pagination-belum');
        initPagination('list-sudah', 'pagination-sudah');

        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            
            // Filter both lists
            filterList('list-belum', value);
            filterList('list-sudah', value);

            // Re-initialize pagination after filtering
            initPagination('list-belum', 'pagination-belum');
            initPagination('list-sudah', 'pagination-sudah');
        });
    });

    function filterList(listId, value) {
        const list = document.getElementById(listId);
        const items = list.getElementsByClassName('teacher-col');
        let visibleCount = 0;

        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const text = item.textContent || item.innerText;
            if (text.toLowerCase().indexOf(value) > -1) {
                item.classList.remove('d-none');
                item.classList.add('d-block'); // Ensure it's marked as visible for pagination
                // We actually don't toggle display here directly if we want pagination to handle it.
                // But for search + pagination:
                // 1. Mark items as 'matched' or 'unmatched'
                item.dataset.matched = "true";
                visibleCount++;
            } else {
                item.classList.add('d-none');
                item.classList.remove('d-block');
                item.dataset.matched = "false";
            }
        }
        
        // Show/Hide "No Data" message
        if (listId === 'list-belum') {
             $('#no-data-belum').toggle(visibleCount === 0);
        } else {
             $('#no-data-sudah').toggle(visibleCount === 0);
        }
    }

    function initPagination(listId, paginationId) {
        const list = document.getElementById(listId);
        // Only select items that match the search (or all if no search)
        // using selector that checks if style display is not none is tricky with classes.
        // Let's use the data-matched attribute we set, or default to all.
        let items = Array.from(list.getElementsByClassName('teacher-col'));
        
        // Filter by matched status if search is active (checked via dataset)
        // If dataset.matched is undefined, assume true (initial load)
        let visibleItems = items.filter(item => item.dataset.matched !== "false");
        
        const pageCount = Math.ceil(visibleItems.length / ITEMS_PER_PAGE);
        const pagination = document.getElementById(paginationId);
        pagination.innerHTML = '';

        if (pageCount <= 1) {
            // Show all visible items, hide pagination if only 1 page
            visibleItems.forEach(item => item.classList.remove('d-none'));
            return;
        }

        // Show first page
        showPage(visibleItems, 1);

        // Generate Buttons
        for (let i = 1; i <= pageCount; i++) {
            const li = document.createElement('li');
            li.className = 'page-item';
            if (i === 1) li.classList.add('active');
            
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerText = i;
            a.onclick = (e) => {
                e.preventDefault();
                showPage(visibleItems, i);
                
                // Update active class
                const currentActive = pagination.querySelector('.active');
                if (currentActive) currentActive.classList.remove('active');
                li.classList.add('active');
            };

            li.appendChild(a);
            pagination.appendChild(li);
        }
    }

    function showPage(items, page) {
        const start = (page - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;

        // First, hide all items involved in this pagination set
        // (We only touch the items that were candidates for this pagination)
        items.forEach(item => item.classList.add('d-none'));

        // Then show the specific slice
        for (let i = start; i < end && i < items.length; i++) {
            items[i].classList.remove('d-none');
        }
    }


    async function tandaiHadir(id, nama) {
        // Custom HTML for Bootstrap 4 Toggle Buttons
        const { value: status } = await Swal.fire({
            title: 'Konfirmasi Kehadiran',
            html: `
                <p>Pilih status kehadiran untuk <b>${nama}</b></p>
                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                    <label class="btn btn-outline-success active" style="width: 33%">
                        <input type="radio" name="status_pilihan" value="Hadir" checked> Hadir
                    </label>
                    <label class="btn btn-outline-warning" style="width: 33%">
                        <input type="radio" name="status_pilihan" value="Izin"> Izin
                    </label>
                    <label class="btn btn-outline-danger" style="width: 34%">
                        <input type="radio" name="status_pilihan" value="Sakit"> Sakit
                    </label>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Lanjut',
            didOpen: () => {
                // Manually handle Bootstrap 4 toggle active state for dynamic content
                const labels = Swal.getHtmlContainer().querySelectorAll('.btn');
                labels.forEach(label => {
                    label.addEventListener('click', function() {
                        labels.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            },
            preConfirm: () => {
                const checked = Swal.getHtmlContainer().querySelector('input[name="status_pilihan"]:checked');
                return checked ? checked.value : null;
            }
        });

        if (status) {
            let keterangan = '';
            
            if (status === 'Izin') {
                const { value: text } = await Swal.fire({
                    title: 'Keterangan Izin',
                    input: 'textarea',
                    inputLabel: 'Alasan (Opsional)',
                    inputPlaceholder: 'Tulis alasan izin disini...',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal'
                });
                
                if (text === undefined) {
                    return; 
                }
                keterangan = text;
            }

            // AJAX Request
            $.ajax({
                url: '<?= base_url('presensi/hadir') ?>',
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    keterangan: keterangan,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status ' + status + ' berhasil dicatat.',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            willClose: () => {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message || 'Terjadi kesalahan.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan koneksi.',
                        'error'
                    );
                }
            });
        }
    }
</script>
<?= $this->endSection(); ?>
