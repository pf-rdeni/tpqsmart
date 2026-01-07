<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page_title ?? 'TPQ Smart' ?></title>

    <?php
    // Logo logic similar to backend but simplified
    $faviconUrl = base_url('favicon.ico');
    $faviconType = 'image/x-icon';
    ?>
    <link rel="icon" type="<?= $faviconType ?>" href="<?= $faviconUrl ?>" />

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS for Frontend -->
    <style>
        .content-wrapper {
            margin-left: 0 !important;
        }
        .main-footer {
            margin-left: 0 !important;
        }
        .navbar-light {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
