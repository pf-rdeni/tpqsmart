<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title ?></title>

    <?php
    // Ambil logo lembaga untuk favicon
    $faviconUrl = base_url('favicon.ico'); // Default favicon
    $faviconType = 'image/x-icon'; // Default type

    $idTpq = session()->get('IdTpq');
    if (!empty($idTpq)) {
        $tpqModel = new \App\Models\TpqModel();
        $tpqData = $tpqModel->GetData($idTpq);

        if (!empty($tpqData) && !empty($tpqData[0]['LogoLembaga'])) {
            // Gunakan logo lembaga sebagai favicon jika ada
            $logoFile = $tpqData[0]['LogoLembaga'];
            $faviconUrl = base_url('uploads/logo/' . $logoFile);

            // Tentukan tipe berdasarkan ekstensi file
            $extension = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
            switch ($extension) {
                case 'png':
                    $faviconType = 'image/png';
                    break;
                case 'jpg':
                case 'jpeg':
                    $faviconType = 'image/jpeg';
                    break;
                case 'gif':
                    $faviconType = 'image/gif';
                    break;
                case 'svg':
                    $faviconType = 'image/svg+xml';
                    break;
                default:
                    $faviconType = 'image/png';
                    break;
            }
        }
    }
    ?>
    <!-- Favicon -->
    <link rel="icon" type="<?= $faviconType ?>" href="<?= $faviconUrl ?>" />
    <link rel="shortcut icon" type="<?= $faviconType ?>" href="<?= $faviconUrl ?>" />
    <link rel="apple-touch-icon" href="<?= $faviconUrl ?>" />

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/fontawesome-free/css/all.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="<?php echo base_url('/plugins') ?>/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="<?php echo base_url('/plugins') ?>/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('template/backend/dist') ?>/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/select2/css/select2.min.css">
    <link rel="stylesheet"
        href="<?php echo base_url('/plugins') ?>/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet"
        href="<?php echo base_url('/plugins') ?>/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/bs-stepper/css/bs-stepper.min.css">

    <!-- dropzonejs -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/dropzone/min/dropzone.min.css">

    <!-- fullCalendar -->
    <link rel="stylesheet" href="<?php echo base_url('/plugins') ?>/fullcalendar/main.css">

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS untuk optimasi navbar di mobile sesuai AdminLTE -->
    <style>
        /* Memastikan navbar selalu terlihat dan berfungsi dengan baik di semua ukuran layar */
        @media (max-width: 991.98px) {
            .main-header.navbar {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Navbar nav menggunakan flex row untuk layout horizontal */
            .main-header.navbar > .navbar-nav {
                display: flex !important;
                flex-direction: row;
                align-items: center;
                flex-wrap: nowrap;
                white-space: nowrap;
            }
            
            /* Memastikan tombol hamburger menu selalu terlihat */
            .main-header.navbar .navbar-nav > li:first-child {
                display: block !important;
                flex-shrink: 0;
            }
            
            /* Optimasi spacing untuk item navbar di mobile */
            .main-header.navbar .navbar-nav .nav-item {
                flex-shrink: 0;
                margin: 0 0.125rem;
            }
            
            /* Sembunyikan text label di mobile untuk menghemat ruang */
            .main-header.navbar .navbar-nav .nav-link span.d-none.d-md-inline,
            .main-header.navbar .navbar-nav .nav-link span.d-md-inline {
                display: none !important;
            }
            
            /* Pastikan icon tetap terlihat */
            .main-header.navbar .navbar-nav .nav-link i {
                display: inline-block !important;
            }
            
            /* Styling untuk dropdown menu mobile */
            .main-header.navbar .navbar-nav .dropdown-menu {
                border-radius: 0.25rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                margin-top: 0.5rem;
                min-width: 200px;
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                top: 100% !important;
                z-index: 1050 !important;
                display: none;
            }
            
            /* Pastikan dropdown menu muncul saat show */
            .main-header.navbar .navbar-nav .dropdown.show .dropdown-menu,
            .main-header.navbar .navbar-nav .dropdown-menu.show {
                display: block !important;
            }
            
            /* Pastikan nav-item dropdown memiliki position relative */
            .main-header.navbar .navbar-nav .nav-item.dropdown {
                position: relative !important;
            }
            
            /* Pastikan dropdown toggle bisa diklik di mobile */
            .main-header.navbar .navbar-nav .nav-item.dropdown > .nav-link {
                cursor: pointer;
                -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
                touch-action: manipulation;
            }
            
            .main-header.navbar .navbar-nav .dropdown-item {
                padding: 0.75rem 1rem;
                transition: background-color 0.2s ease;
            }
            
            .main-header.navbar .navbar-nav .dropdown-item:hover {
                background-color: #f8f9fa;
            }
            
            .main-header.navbar .navbar-nav .dropdown-item i {
                width: 20px;
                text-align: center;
            }
            
            /* Pastikan navbar search block tidak overflow */
            .main-header.navbar .navbar-search-block {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 1000;
                background: white;
                padding: 0.5rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            /* Pastikan navbar tidak terlalu tinggi */
            .main-header.navbar {
                min-height: 57px;
                max-height: 57px;
            }
        }
        
        /* Untuk layar sangat kecil, optimasi tambahan */
        @media (max-width: 576px) {
            /* Kurangi padding untuk menghemat ruang */
            .main-header.navbar .navbar-nav .nav-link {
                padding: 0.25rem 0.5rem;
            }
            
            /* Pastikan icon tidak terlalu besar */
            .main-header.navbar .navbar-nav .nav-link i {
                font-size: 0.9rem;
            }
        }
        
        /* Pastikan dropdown tidak terpotong */
        @media (max-width: 991.98px) {
            .main-header.navbar .navbar-nav .dropdown.show .dropdown-menu {
                display: block !important;
            }
            
            /* Pastikan dropdown menu tidak terpotong oleh overflow */
            .main-header.navbar {
                overflow: visible !important;
            }
            
            .main-header.navbar > .navbar-nav {
                overflow: visible !important;
            }
            
            /* Pastikan dropdown menu bisa keluar dari container */
            .main-header.navbar .navbar-nav .nav-item.dropdown {
                overflow: visible !important;
            }
        }
    </style>
    
    <!-- Script untuk memastikan dropdown Bootstrap berfungsi di mobile -->
    <script>
        // Fix untuk dropdown menu di mobile
        (function() {
            function initNavbarDropdowns() {
                if (typeof $ === 'undefined' || !$.fn.dropdown) {
                    // Retry jika jQuery belum ready
                    setTimeout(initNavbarDropdowns, 100);
                    return;
                }
                
                // Pastikan semua dropdown di navbar ter-initialize
                $('.main-header.navbar [data-toggle="dropdown"]').each(function() {
                    var $toggle = $(this);
                    // Bootstrap 4 menggunakan data('bs.dropdown'), Bootstrap 5 menggunakan data('bs.dropdown')
                    if (!$toggle.data('bs.dropdown')) {
                        try {
                            $toggle.dropdown();
                        } catch(e) {
                            console.warn('Error initializing dropdown:', e);
                        }
                    }
                });
                
                // Fix khusus untuk mobile: pastikan dropdown bisa diklik
                $('.main-header.navbar [data-toggle="dropdown"]').on('click', function(e) {
                    var $toggle = $(this);
                    var $dropdown = $toggle.closest('.dropdown');
                    
                    // Pastikan event tidak di-prevent oleh elemen lain
                    e.stopPropagation();
                    
                    // Jika dropdown sudah terbuka, tutup
                    if ($dropdown.hasClass('show')) {
                        $dropdown.removeClass('show');
                        $dropdown.find('.dropdown-menu').removeClass('show');
                        $toggle.attr('aria-expanded', 'false');
                    } else {
                        // Tutup dropdown lain yang terbuka
                        $('.main-header.navbar .dropdown.show').not($dropdown).each(function() {
                            $(this).removeClass('show');
                            $(this).find('.dropdown-menu').removeClass('show');
                            $(this).find('[data-toggle="dropdown"]').attr('aria-expanded', 'false');
                        });
                        
                        // Buka dropdown ini
                        $dropdown.addClass('show');
                        $dropdown.find('.dropdown-menu').addClass('show');
                        $toggle.attr('aria-expanded', 'true');
                    }
                });
                
                // Tutup dropdown saat klik di luar
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.main-header.navbar .dropdown').length) {
                        $('.main-header.navbar .dropdown.show').removeClass('show');
                        $('.main-header.navbar .dropdown-menu.show').removeClass('show');
                        $('.main-header.navbar [data-toggle="dropdown"]').attr('aria-expanded', 'false');
                    }
                });
            }
            
            // Initialize saat DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initNavbarDropdowns);
            } else {
                initNavbarDropdowns();
            }
            
            // Re-initialize setelah jQuery ready (jika jQuery dimuat setelah DOM)
            if (typeof $ !== 'undefined') {
                $(document).ready(function() {
                    setTimeout(initNavbarDropdowns, 100);
                });
            }
        })();
    </script>

</head>