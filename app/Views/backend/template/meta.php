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

</head>