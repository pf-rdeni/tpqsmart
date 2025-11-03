<!DOCTYPE html>
<html lang="id">
<?= $this->include('/backend/template/meta'); ?>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php if (isset($isPublic) && $isPublic): ?>
            <!-- Public Header - Tanpa Sidebar/Navbar Admin -->
            <div class="content-wrapper" style="margin-left: 0;">
                <!-- <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0"><?= $page_title ?? 'Form Pendaftaran Santri' ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                                    <li class="breadcrumb-item active">Pendaftaran</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div> -->
            <?php else: ?>
                <!-- Admin Layout - Dengan Sidebar/Navbar -->
                <?= $this->include('/backend/template/navbar'); ?>
                <?= $this->include('/backend/template/sidebar'); ?>
                <div class="content-wrapper">
                    <?= $this->include('/backend/template/header'); ?>
                <?php endif; ?>

                <?= $this->renderSection('content'); ?>

                </div>
                <?= $this->include('/backend/template/footer'); ?>
            </div>
            <?= $this->include('/backend/template/js'); ?>
            <?= $this->include('/backend/template/scripts'); ?>
            <?= $this->renderSection('scripts'); ?>
</body>

</html>