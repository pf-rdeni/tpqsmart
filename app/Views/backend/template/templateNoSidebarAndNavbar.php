<!DOCTYPE html>
<html lang="id">
<?= $this->include('/backend/template/meta'); ?>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar dan Sidebar tidak di-include untuk halaman monitoring full view -->
        <div class="content-wrapper" style="margin-left: 0 !important; margin-top: 0 !important;">
            <!-- Header tidak diperlukan, langsung ke content -->
            <?= $this->renderSection('content'); ?>
        </div>
        <?= $this->include('/backend/template/footer'); ?>
    </div>
    <?= $this->include('/backend/template/js'); ?>
    <?= $this->include('/backend/template/scripts'); ?>
    <?= $this->renderSection('scripts'); ?>
</body>

</html>

