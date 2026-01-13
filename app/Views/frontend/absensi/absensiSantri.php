<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($page_title) ? $page_title : 'Absensi Santri' ?></title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    
    <style>
        .absensi-btn-mobile {
            min-height: 48px;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            margin-right: 5px;
            cursor: pointer;
        }
        .absensi-btn-mobile .icheck-primary {
             margin: 0;
        }
        
        /* Mimic backend card styles */
        .card-primary.card-outline-tabs > .card-header a.active {
            border-top: 3px solid #007bff;
        }
        
        .photo-profil-thumbnail {
            width: 33px;
            height: 35px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 15px;
            border: 2px solid #ddd;
        }
        .santri-name {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        body {
            background-color: #f4f6f9;
        }
        
        /* Mobile specific adjustments */
        @media(max-width: 576px) {
            .nav-tabs .nav-link {
                padding: 0.5rem 0.5rem;
                font-size: 0.9rem;
            }
            .btn-group-toggle label {
                padding: 5px;
                font-size: 12px;
                flex-direction: column;
            }
            .btn-group-toggle i {
                margin-bottom: 2px;
            }
        }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="#" class="navbar-brand">
        <span class="brand-text font-weight-light">Absensi<b>Public</b></span>
      </a>
      
      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
         <ul class="navbar-nav ml-auto">
           <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link">Guru: <strong><?= isset($guru_nama) ? esc($guru_nama) : 'Guru' ?></strong></span>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('absensi/haskey/logout/' . $hash_key) ?>" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"> Absensi Santri</h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container">
        <!-- Card Tabs for Classes -->
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <?php foreach($kelas_list_all as $k): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($k['IdKelas'] == $selected_kelas) ? 'active' : '' ?>" 
                           href="?tanggal=<?= $tanggal_dipilih ?>&IdKelas=<?= $k['IdKelas'] ?>" 
                           role="tab">
                           <?= esc($k['NamaKelas']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="card-body">
                <!-- Date Picker & Actions -->
                 <div class="row mb-4">
                     <div class="col-md-6">
                         <div class="form-group">
                             <label><i class="fas fa-calendar-alt"></i> Tanggal Absensi:</label>
                             <input type="date" class="form-control form-control-lg" value="<?= $tanggal_dipilih ?>" onchange="updateTanggal(this.value)">
                         </div>
                     </div>
                     <div class="col-md-6 d-flex align-items-end">
                         <div class="form-group w-100">
                             <button type="button" class="btn btn-success btn-lg btn-block" onclick="setAllHadir()">
                                 <i class="fas fa-check-double"></i> Set Semua Hadir
                             </button>
                             <small class="text-muted"><i class="fas fa-info-circle"></i> Default: Semua santri di-set sebagai Hadir</small>
                         </div>
                     </div>
                 </div>

                <!-- Absensi Form -->
                <form action="<?= base_url('absensi/haskey/simpan') ?>" method="post" id="formAbsensi">
                    <input type="hidden" name="IdTpq" value="<?= $IdTpq ?>">
                    <input type="hidden" name="IdTahunAjaran" value="<?= $IdTahunAjaran ?>">
                    <input type="hidden" name="IdKelas" value="<?= $selected_kelas ?>">
                    <input type="hidden" name="tanggal" value="<?= $tanggal_dipilih ?>">

                    <?php if (empty($santri)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Semua santri di kelas ini sudah diabsen pada tanggal <?= date('d-m-Y', strtotime($tanggal_dipilih)) ?>.
                            <?php if (!empty($absensi_recorder)): ?>
                                <br><small><i class="fas fa-user-check text-success"></i> Diabsensi oleh: <strong><?= esc($absensi_recorder) ?></strong></small>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($santri_sudah_absen)): ?>
                        <button type="button" class="btn btn-warning btn-block mb-3" id="btnToggleEdit" onclick="toggleEditSection()">
                            <i class="fas fa-edit"></i> Ubah Absensi
                        </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <div id="santri-list">
                            <?php foreach ($santri as $row): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <?php 
                                            $photoProfil = $row->PhotoProfil ?? '';
                                            $thumbnailPath = base_url('uploads/santri/thumbnails/thumb_' . $photoProfil);
                                            
                                            // Generate Initials (Acronym)
                                            $namaWords = explode(' ', trim($row->NamaSantri));
                                            $acronym = '';
                                            foreach ($namaWords as $w) {
                                                $acronym .= mb_substr($w, 0, 1);
                                            }
                                            $acronym = strtoupper(substr($acronym, 0, 2));

                                            // Deterministic Color based on Name
                                            $bgColors = ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14', '#20c997'];
                                            $colorIndex = crc32($row->NamaSantri) % count($bgColors);
                                            $bgColor = $bgColors[abs($colorIndex)];
                                            ?>

                                            <div class="mr-3 position-relative">
                                                <?php if($photoProfil): ?>
                                                    <!-- Image with fallback handling -->
                                                    <img src="<?= $thumbnailPath ?>" 
                                                         class="photo-profil-thumbnail" 
                                                         onerror="this.style.display='none'; document.getElementById('avatar-fallback-<?= $row->IdSantri ?>').style.display='flex';">
                                                    
                                                    <!-- Hidden fallback for onerror key -->
                                                    <div id="avatar-fallback-<?= $row->IdSantri ?>"
                                                         class="photo-profil-thumbnail align-items-center justify-content-center text-white" 
                                                         style="display: none; background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Default Avatar (Initials) -->
                                                    <div class="photo-profil-thumbnail d-flex align-items-center justify-content-center text-white" 
                                                         style="background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h5 class="m-0 santri-name text-dark"><?= esc($row->NamaSantri) ?></h5>
                                        </div>

                                        <!-- Option Buttons Styles like Backend -->
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                          <label class="btn btn-outline-success absensi-btn-mobile active" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Hadir')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Hadir" checked autocomplete="off"> 
                                            <i class="fas fa-check-circle"></i> Hadir
                                          </label>
                                          <label class="btn btn-outline-warning absensi-btn-mobile" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Izin')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Izin" autocomplete="off"> 
                                            <i class="fas fa-envelope"></i> Izin
                                          </label>
                                          <label class="btn btn-outline-info absensi-btn-mobile" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Sakit')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Sakit" autocomplete="off"> 
                                            <i class="fas fa-clinic-medical"></i> Sakit
                                          </label>
                                          <label class="btn btn-outline-danger absensi-btn-mobile" onclick="toggleKeterangan(<?= $row->IdSantri ?>, 'Alfa')">
                                            <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Alfa" autocomplete="off"> 
                                            <i class="fas fa-times-circle"></i> Alfa
                                          </label>
                                        </div>
                                        
                                        <div class="form-group mt-2" id="keterangan-box-<?= $row->IdSantri ?>" style="display: none;">
                                            <input type="text" name="keterangan[<?= $row->IdSantri ?>]" class="form-control form-control-sm" placeholder="Keterangan (Opsional)">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4 mb-4">
                             <button type="submit" class="btn btn-primary btn-lg btn-block" id="btnSimpan">
                                 <i class="fas fa-save"></i> SIMPAN ABSENSI
                             </button>
                        </div>
                        
                        <?php if (!empty($santri_sudah_absen)): ?>
                        <hr>
                        <button type="button" class="btn btn-warning btn-block mb-3" id="btnToggleEdit" onclick="toggleEditSection()">
                            <i class="fas fa-edit"></i> Ubah Absensi yang Sudah Tercatat
                        </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </form>
                
                <!-- Section Edit Absensi -->
                <?php if (!empty($santri_sudah_absen)): ?>
                <div id="editAbsensiSection" style="display: none;">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit"></i> Ubah Absensi
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" onclick="toggleEditSection()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning alert-sm mb-3">
                                <i class="fas fa-info-circle"></i> Klik pada status kehadiran untuk mengubah absensi santri.
                            </div>
                            
                            <?php foreach ($santri_sudah_absen as $row): 
                                $photoProfil = $row->PhotoProfil ?? '';
                                $thumbnailPath = base_url('uploads/santri/thumbnails/thumb_' . $photoProfil);
                                
                                // Generate Initials
                                $namaWords = explode(' ', trim($row->NamaSantri));
                                $acronym = '';
                                foreach ($namaWords as $w) {
                                    $acronym .= mb_substr($w, 0, 1);
                                }
                                $acronym = strtoupper(substr($acronym, 0, 2));

                                // Deterministic Color
                                $bgColors = ['#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14', '#20c997'];
                                $colorIndex = crc32($row->NamaSantri) % count($bgColors);
                                $bgColor = $bgColors[abs($colorIndex)];
                                
                                // Status badge class
                                $statusClass = '';
                                $statusIcon = '';
                                switch(strtolower($row->Kehadiran ?? '')) {
                                    case 'hadir':
                                        $statusClass = 'badge-success';
                                        $statusIcon = 'fa-check-circle';
                                        break;
                                    case 'izin':
                                        $statusClass = 'badge-warning';
                                        $statusIcon = 'fa-envelope';
                                        break;
                                    case 'sakit':
                                        $statusClass = 'badge-info';
                                        $statusIcon = 'fa-clinic-medical';
                                        break;
                                    case 'alfa':
                                        $statusClass = 'badge-danger';
                                        $statusIcon = 'fa-times-circle';
                                        break;
                                }
                            ?>
                            <div class="card mb-2 shadow-sm santri-edit-card" id="edit-card-<?= $row->IdSantri ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3 position-relative">
                                                <?php if($photoProfil): ?>
                                                    <img src="<?= $thumbnailPath ?>" 
                                                         class="photo-profil-thumbnail" 
                                                         onerror="this.style.display='none'; document.getElementById('edit-avatar-fallback-<?= $row->IdSantri ?>').style.display='flex';">
                                                    <div id="edit-avatar-fallback-<?= $row->IdSantri ?>"
                                                         class="photo-profil-thumbnail align-items-center justify-content-center text-white" 
                                                         style="display: none; background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="photo-profil-thumbnail d-flex align-items-center justify-content-center text-white" 
                                                         style="background-color: <?= $bgColor ?>; font-weight: bold; font-size: 1.1rem; margin-right: 0;">
                                                        <?= $acronym ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="m-0 santri-name text-dark"><?= esc($row->NamaSantri) ?></h6>
                                                <small class="text-muted">
                                                    Status: <span class="badge <?= $statusClass ?>" id="status-badge-<?= $row->IdSantri ?>">
                                                        <i class="fas <?= $statusIcon ?>"></i> <?= ucfirst($row->Kehadiran ?? '-') ?>
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Edit Buttons -->
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons" id="edit-buttons-<?= $row->IdSantri ?>">
                                        <label class="btn btn-outline-success absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'hadir' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Hadir'); updateSingleAbsensi(<?= $row->IdSantri ?>, 'Hadir')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Hadir" <?= strtolower($row->Kehadiran) == 'hadir' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-check-circle"></i> Hadir
                                        </label>
                                        <label class="btn btn-outline-warning absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'izin' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Izin')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Izin" <?= strtolower($row->Kehadiran) == 'izin' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-envelope"></i> Izin
                                        </label>
                                        <label class="btn btn-outline-info absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'sakit' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Sakit')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Sakit" <?= strtolower($row->Kehadiran) == 'sakit' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-clinic-medical"></i> Sakit
                                        </label>
                                        <label class="btn btn-outline-danger absensi-btn-mobile <?= strtolower($row->Kehadiran) == 'alfa' ? 'active' : '' ?>" onclick="toggleEditKeterangan(<?= $row->IdSantri ?>, 'Alfa'); updateSingleAbsensi(<?= $row->IdSantri ?>, 'Alfa')">
                                            <input type="radio" name="edit_kehadiran_<?= $row->IdSantri ?>" value="Alfa" <?= strtolower($row->Kehadiran) == 'alfa' ? 'checked' : '' ?> autocomplete="off"> 
                                            <i class="fas fa-times-circle"></i> Alfa
                                        </label>
                                    </div>
                                    
                                    <!-- Keterangan Input (Opsional) -->
                                    <?php 
                                    $showKeterangan = in_array(strtolower($row->Kehadiran ?? ''), ['izin', 'sakit']);
                                    ?>
                                    <div class="form-group mt-2" id="edit-keterangan-box-<?= $row->IdSantri ?>" style="display: <?= $showKeterangan ? 'block' : 'none' ?>;">
                                        <div class="input-group">
                                            <input type="text" id="edit-keterangan-<?= $row->IdSantri ?>" class="form-control form-control-sm" placeholder="Keterangan (Opsional)" value="<?= esc($row->Keterangan ?? '') ?>" onblur="submitEditKeterangan(<?= $row->IdSantri ?>)">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-sm btn-primary" onclick="submitEditKeterangan(<?= $row->IdSantri ?>)">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <!-- /.card -->
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="container">
        <div class="float-right d-none d-sm-inline">
          <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="#">TPQ Smart</a>.</strong>
    </div>
  </footer>
</div>

<!-- jQuery -->
<script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('template/backend/dist/js/adminlte.min.js') ?>"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function updateTanggal(val) {
        var url = new URL(window.location.href);
        url.searchParams.set('tanggal', val);
        window.location.href = url.toString();
    }

    function setAllHadir() {
        $('input[value="Hadir"]').prop('checked', true).trigger('change');
        $('.btn-group-toggle label').removeClass('active');
        $('input[value="Hadir"]').parent('label').addClass('active');

        // Hide all keterangan boxes
        $('div[id^="keterangan-box-"]').hide();
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Semua santri di-set Hadir',
            timer: 1000,
            showConfirmButton: false
        });
    }

    function toggleKeterangan(idSantri, status) {
        var box = $('#keterangan-box-' + idSantri);
        if (status === 'Hadir') {
            box.slideUp();
        } else {
            box.slideDown();
        }
    }

    $(document).ready(function() {
        $('#formAbsensi').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var btn = $('#btnSimpan');
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: $(form).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> SIMPAN ABSENSI');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> SIMPAN ABSENSI');
                }
            });
        });
    });

    // Toggle edit absensi section
    function toggleEditSection() {
        var editSection = $('#editAbsensiSection');
        var toggleBtn = $('#btnToggleEdit');
        
        if (editSection.is(':visible')) {
            editSection.slideUp();
            toggleBtn.html('<i class="fas fa-edit"></i> Ubah Absensi');
        } else {
            editSection.slideDown();
            toggleBtn.html('<i class="fas fa-times"></i> Tutup');
            
            // Scroll to edit section
            $('html, body').animate({
                scrollTop: editSection.offset().top - 100
            }, 500);
        }
    }

    // Update single absensi via AJAX
    function updateSingleAbsensi(idSantri, kehadiran) {
        var formData = {
            IdSantri: idSantri,
            tanggal: '<?= $tanggal_dipilih ?>',
            kehadiran: kehadiran,
            IdKelas: '<?= $selected_kelas ?>',
            IdTahunAjaran: '<?= $IdTahunAjaran ?>',
            IdTpq: '<?= $IdTpq ?>'
        };

        // Update button states immediately
        var buttonGroup = $('#edit-buttons-' + idSantri);
        buttonGroup.find('label').removeClass('active');
        buttonGroup.find('input[value="' + kehadiran + '"]').prop('checked', true).closest('label').addClass('active');

        $.ajax({
            url: '<?= base_url("absensi/haskey/update") ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update status badge
                    var badgeClass = '';
                    var badgeIcon = '';
                    switch(kehadiran.toLowerCase()) {
                        case 'hadir':
                            badgeClass = 'badge-success';
                            badgeIcon = 'fa-check-circle';
                            break;
                        case 'izin':
                            badgeClass = 'badge-warning';
                            badgeIcon = 'fa-envelope';
                            break;
                        case 'sakit':
                            badgeClass = 'badge-info';
                            badgeIcon = 'fa-clinic-medical';
                            break;
                        case 'alfa':
                            badgeClass = 'badge-danger';
                            badgeIcon = 'fa-times-circle';
                            break;
                    }
                    
                    var badge = $('#status-badge-' + idSantri);
                    badge.removeClass('badge-success badge-warning badge-info badge-danger');
                    badge.addClass(badgeClass);
                    badge.html('<i class="fas ' + badgeIcon + '"></i> ' + kehadiran);
                    
                    // Flash effect to indicate success
                    var card = $('#edit-card-' + idSantri);
                    card.css('background-color', '#d4edda');
                    setTimeout(function() {
                        card.css('background-color', '');
                    }, 500);
                    
                    // Toast notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Absensi berhasil diperbarui',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
            }
        });
    }

    // Toggle keterangan field visibility for edit mode
    function toggleEditKeterangan(idSantri, status) {
        var box = $('#edit-keterangan-box-' + idSantri);
        
        // Update button states immediately
        var buttonGroup = $('#edit-buttons-' + idSantri);
        buttonGroup.find('label').removeClass('active');
        buttonGroup.find('input[value="' + status + '"]').prop('checked', true).closest('label').addClass('active');
        
        if (status === 'Izin' || status === 'Sakit') {
            box.slideDown();
        } else {
            box.slideUp();
        }
    }

    // Submit keterangan with status update for Izin/Sakit
    function submitEditKeterangan(idSantri) {
        var kehadiran = $('input[name="edit_kehadiran_' + idSantri + '"]:checked').val();
        var keterangan = $('#edit-keterangan-' + idSantri).val();
        
        var formData = {
            IdSantri: idSantri,
            tanggal: '<?= $tanggal_dipilih ?>',
            kehadiran: kehadiran,
            keterangan: keterangan,
            IdKelas: '<?= $selected_kelas ?>',
            IdTahunAjaran: '<?= $IdTahunAjaran ?>',
            IdTpq: '<?= $IdTpq ?>'
        };

        $.ajax({
            url: '<?= base_url("absensi/haskey/update") ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update status badge
                    var badgeClass = '';
                    var badgeIcon = '';
                    switch(kehadiran.toLowerCase()) {
                        case 'hadir':
                            badgeClass = 'badge-success';
                            badgeIcon = 'fa-check-circle';
                            break;
                        case 'izin':
                            badgeClass = 'badge-warning';
                            badgeIcon = 'fa-envelope';
                            break;
                        case 'sakit':
                            badgeClass = 'badge-info';
                            badgeIcon = 'fa-clinic-medical';
                            break;
                        case 'alfa':
                            badgeClass = 'badge-danger';
                            badgeIcon = 'fa-times-circle';
                            break;
                    }
                    
                    var badge = $('#status-badge-' + idSantri);
                    badge.removeClass('badge-success badge-warning badge-info badge-danger');
                    badge.addClass(badgeClass);
                    badge.html('<i class="fas ' + badgeIcon + '"></i> ' + kehadiran);
                    
                    // Flash effect
                    var card = $('#edit-card-' + idSantri);
                    card.css('background-color', '#d4edda');
                    setTimeout(function() {
                        card.css('background-color', '');
                    }, 500);
                    
                    // Toast notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Absensi berhasil diperbarui',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
            }
        });
    }
</script>
</body>
</html>
