<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search"></i> Hasil Pencarian
            </h3>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <form method="GET" action="<?= base_url('backend/search') ?>" class="mb-4">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" class="form-control" placeholder="Cari menu atau halaman..." 
                           value="<?= esc($query) ?>" autofocus>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>

            <?php if (!empty($query)): ?>
                <!-- Search Results -->
                <?php if ($totalResults > 0): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Ditemukan <strong><?= $totalResults ?></strong> hasil untuk "<strong><?= esc($query) ?></strong>"
                    </div>

                    <!-- Grouped Results -->
                    <?php foreach ($groupedResults as $category => $categoryMenus): ?>
                        <div class="card card-primary card-outline mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-folder"></i> <?= esc($category) ?>
                                    <span class="badge badge-light ml-2"><?= count($categoryMenus) ?></span>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($categoryMenus as $menu): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <i class="<?= esc($menu['icon']) ?> fa-2x text-primary"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">
                                                        <a href="<?= esc($menu['url']) ?>" class="text-decoration-none">
                                                            <?= esc($menu['title']) ?>
                                                        </a>
                                                    </h5>
                                                    <p class="mb-0 text-muted">
                                                        <small><?= esc($menu['description']) ?></small>
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-link"></i> 
                                                        <?= esc($menu['url']) ?>
                                                    </small>
                                                </div>
                                                <div class="ml-3">
                                                    <a href="<?= esc($menu['url']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-external-link-alt"></i> Buka
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4>Tidak ada hasil ditemukan</h4>
                        <p>Kami tidak menemukan menu atau halaman yang sesuai dengan "<strong><?= esc($query) ?></strong>"</p>
                        <p class="mb-0">
                            <a href="<?= base_url('backend/search') ?>" class="btn btn-primary">
                                <i class="fas fa-list"></i> Lihat Semua Menu
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Recommended Menus (All Available Menus) -->
                <div class="alert alert-success">
                    <i class="fas fa-lightbulb"></i> 
                    <strong>Rekomendasi Menu</strong> - Berikut adalah menu-menu yang dapat Anda akses:
                </div>

                <?php if (!empty($groupedResults)): ?>
                    <?php foreach ($groupedResults as $category => $categoryMenus): ?>
                        <div class="card card-success card-outline mb-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-folder"></i> <?= esc($category) ?>
                                    <span class="badge badge-light ml-2"><?= count($categoryMenus) ?></span>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($categoryMenus as $menu): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <i class="<?= esc($menu['icon']) ?> fa-2x text-success"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">
                                                        <a href="<?= esc($menu['url']) ?>" class="text-decoration-none">
                                                            <?= esc($menu['title']) ?>
                                                        </a>
                                                    </h5>
                                                    <p class="mb-0 text-muted">
                                                        <small><?= esc($menu['description']) ?></small>
                                                    </p>
                                                </div>
                                                <div class="ml-3">
                                                    <a href="<?= esc($menu['url']) ?>" class="btn btn-sm btn-success">
                                                        <i class="fas fa-external-link-alt"></i> Buka
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>Tidak ada menu tersedia</h4>
                        <p>Anda tidak memiliki akses ke menu apapun saat ini.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Auto focus pada search input
        $('input[name="q"]').focus();
        
        // Highlight search term in results
        <?php if (!empty($query)): ?>
            var searchTerm = <?= json_encode($query) ?>;
            $('.list-group-item h5, .list-group-item p').each(function() {
                var text = $(this).html();
                var regex = new RegExp('(' + searchTerm + ')', 'gi');
                text = text.replace(regex, '<mark>$1</mark>');
                $(this).html(text);
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>

