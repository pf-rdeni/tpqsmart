<!DOCTYPE html>
<html lang="id">
<?= $this->include('/backend/template/meta'); ?>
<?php
// Theme akan di-handle oleh JavaScript menggunakan localStorage
// Default class tanpa dark-mode, JavaScript akan apply theme dari localStorage
$bodyClass = 'hold-transition sidebar-mini layout-fixed';
?>
<body class="<?= $bodyClass ?>">
    <div class="wrapper">
        <?= $this->include('/backend/template/navbar'); ?>
        <?= $this->include('/backend/template/sidebar'); ?>
        <div class="content-wrapper">
            <?= $this->include('/backend/template/header'); ?>
            <?= $this->renderSection('content'); ?>
        </div>
        <?= $this->include('/backend/template/footer'); ?>
    </div>
    <?= $this->include('/backend/template/js'); ?>
    <?= $this->include('/backend/template/scripts'); ?>
    <?= $this->include('/backend/template/dashboardSelector'); ?>
    <?= $this->renderSection('scripts'); ?>
</body>

</html>