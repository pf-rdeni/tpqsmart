<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-envelope-open-text text-primary mr-2"></i><?= esc($page_title ?? 'Administrasi Surat') ?>
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('backend/administrasi') ?>">Administrasi Surat</a></li>
                    <?php if (!empty($breadcrumb)): ?>
                        <?php foreach ($breadcrumb as $b): ?>
                            <?php if (!empty($b['active'])): ?>
                                <li class="breadcrumb-item active"><?= esc($b['label']) ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item"><a href="<?= esc($b['url']) ?>"><?= esc($b['label']) ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="breadcrumb-item active"><?= esc($page_title ?? '') ?></li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>
