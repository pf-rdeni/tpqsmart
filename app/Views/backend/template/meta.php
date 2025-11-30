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

    <!-- Custom CSS untuk memastikan navbar selalu terlihat di mobile -->
    <style>
        /* Memastikan navbar selalu terlihat di semua ukuran layar */
        @media (max-width: 991.98px) {
            .main-header.navbar {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch;
            }
            
            .main-header.navbar .navbar-nav {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                flex-wrap: nowrap !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Memastikan tombol hamburger menu selalu terlihat dan tidak terpotong */
            .main-header.navbar .navbar-nav > li:first-child {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                flex-shrink: 0 !important;
                min-width: 50px !important;
            }
            
            .main-header.navbar .navbar-nav > li:first-child .nav-link {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-width: 50px !important;
                padding: 0.5rem 1rem !important;
            }
            
            .main-header.navbar .navbar-nav > li:first-child .nav-link i {
                font-size: 1.2rem !important;
                display: inline-block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Pastikan icon Font Awesome ter-load dengan baik */
            .main-header.navbar .navbar-nav .nav-link i.fas,
            .main-header.navbar .navbar-nav .nav-link i.far,
            .main-header.navbar .navbar-nav .nav-link i.fab {
                font-family: "Font Awesome 5 Free", "Font Awesome 5 Brands" !important;
                font-weight: 900 !important;
                display: inline-block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Right navbar links - pastikan tidak terpotong */
            .main-header.navbar .navbar-nav.ml-auto {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                flex-wrap: nowrap !important;
                flex-shrink: 0 !important;
                margin-left: auto !important;
            }
            
            .main-header.navbar .navbar-nav.ml-auto .nav-item {
                flex-shrink: 0 !important;
                display: block !important;
            }
            
            .main-header.navbar .navbar-nav.ml-auto .nav-link {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-width: 44px !important;
                padding: 0.5rem 0.75rem !important;
                white-space: nowrap !important;
            }
            
            .main-header.navbar .navbar-nav.ml-auto .nav-link i {
                font-size: 1.1rem !important;
                display: inline-block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Styling untuk dropdown menu mobile */
            .main-header.navbar .navbar-nav .dropdown-menu {
                border-radius: 0.25rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                margin-top: 0.5rem;
                min-width: 200px;
                position: absolute !important;
                z-index: 1000 !important;
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
            
            /* Pastikan navbar tidak overflow */
            .main-header.navbar {
                max-width: 100vw !important;
                width: 100% !important;
            }
            
            /* Fix untuk icon yang tidak ter-load */
            .main-header.navbar .nav-link i:before {
                content: attr(data-icon);
            }
        }
        
        /* Fix khusus untuk layar sangat kecil */
        @media (max-width: 576px) {
            .main-header.navbar .navbar-nav > li:first-child .nav-link {
                min-width: 45px !important;
                padding: 0.5rem 0.75rem !important;
            }
            
            .main-header.navbar .navbar-nav.ml-auto .nav-link {
                min-width: 40px !important;
                padding: 0.5rem 0.5rem !important;
            }
            
            .main-header.navbar .navbar-nav.ml-auto .nav-link span {
                display: none !important;
            }
            
            .main-header.navbar .navbar-nav.ml-auto .nav-link i.fa-angle-down {
                display: none !important;
            }
        }
        
        /* Fallback untuk Font Awesome jika tidak ter-load */
        .main-header.navbar .nav-link[data-widget="pushmenu"] i.fa-bars:before {
            content: "\f0c9" !important;
        }
        
        .main-header.navbar .nav-link[data-widget="navbar-search"] i.fa-search:before {
            content: "\f002" !important;
        }
        
        .main-header.navbar .nav-link[data-widget="fullscreen"] i.fa-expand-arrows-alt:before {
            content: "\f31e" !important;
        }
        
        /* Pastikan Font Awesome font family ter-load */
        @font-face {
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-style: normal;
        }
        
        /* Fallback text untuk icon jika Font Awesome tidak ter-load */
        .main-header.navbar .nav-link[data-widget="pushmenu"]:after {
            content: "☰";
            display: none;
            font-size: 1.2rem;
        }
        
        .main-header.navbar .nav-link[data-widget="pushmenu"] i.fa-bars:empty:after {
            content: "☰";
            display: inline-block;
        }
    </style>
    
    <!-- Script untuk memastikan icon ter-load dengan baik -->
    <script>
        // Pastikan Font Awesome ter-load sebelum halaman selesai loading
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah Font Awesome sudah ter-load
            function checkFontAwesome() {
                var testIcon = document.createElement('i');
                testIcon.className = 'fas fa-bars';
                testIcon.style.position = 'absolute';
                testIcon.style.visibility = 'hidden';
                document.body.appendChild(testIcon);
                
                var computedStyle = window.getComputedStyle(testIcon, ':before');
                var content = computedStyle.getPropertyValue('content');
                
                document.body.removeChild(testIcon);
                
                // Jika Font Awesome tidak ter-load, tambahkan fallback
                if (!content || content === 'none' || content === '""') {
                    console.warn('Font Awesome tidak ter-load, menggunakan fallback');
                    var style = document.createElement('style');
                    style.textContent = `
                        .main-header.navbar .nav-link i.fas:before,
                        .main-header.navbar .nav-link i.far:before,
                        .main-header.navbar .nav-link i.fab:before {
                            font-family: Arial, sans-serif !important;
                        }
                        .main-header.navbar .nav-link[data-widget="pushmenu"] i:before {
                            content: "☰" !important;
                        }
                        .main-header.navbar .nav-link[data-widget="navbar-search"] i:before {
                            content: "🔍" !important;
                        }
                        .main-header.navbar .nav-link[data-widget="fullscreen"] i:before {
                            content: "⛶" !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            }
            
            // Tunggu sedikit untuk memastikan Font Awesome CSS sudah ter-load
            setTimeout(checkFontAwesome, 100);
            
            // Pastikan hamburger menu selalu terlihat
            var hamburgerMenu = document.querySelector('.main-header.navbar .nav-link[data-widget="pushmenu"]');
            if (hamburgerMenu) {
                hamburgerMenu.style.display = 'flex';
                hamburgerMenu.style.visibility = 'visible';
                hamburgerMenu.style.opacity = '1';
                
                var hamburgerIcon = hamburgerMenu.querySelector('i');
                if (hamburgerIcon) {
                    hamburgerIcon.style.display = 'inline-block';
                    hamburgerIcon.style.visibility = 'visible';
                    hamburgerIcon.style.opacity = '1';
                }
            }
            
            // Pastikan semua icon di navbar terlihat
            var navbarIcons = document.querySelectorAll('.main-header.navbar .nav-link i');
            navbarIcons.forEach(function(icon) {
                icon.style.display = 'inline-block';
                icon.style.visibility = 'visible';
                icon.style.opacity = '1';
            });
        });
    </script>

</head>