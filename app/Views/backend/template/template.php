<!DOCTYPE html>
<html lang="id">
<?= $this->include('/backend/template/meta'); ?>

<body class="hold-transition sidebar-mini layout-fixed">
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
    <?= $this->renderSection('scripts'); ?>
</body>

</html>