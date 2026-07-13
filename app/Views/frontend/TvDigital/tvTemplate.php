<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'TV Digital Display' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>" />
    
    <!-- Google Font: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom TV Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('css/infografis-tv.css') ?>">
    
    <!-- Custom script/CSS injects if any -->
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Main Content Rendering -->
    <?= $this->renderSection('content') ?>

    <!-- jQuery (existing local) -->
    <script src="<?= base_url('plugins/jquery/jquery.min.js') ?>"></script>
    
    <!-- Chart.js -->
    <script src="<?= base_url('plugins/chart.js/Chart.min.js') ?>"></script>
    
    <!-- Custom TV JS Script -->
    <script src="<?= base_url('js/infografis-tv.js') ?>"></script>
    
    <!-- Injected Page Scripts -->
    <?= $this->renderSection('scripts') ?>

</body>
</html>
