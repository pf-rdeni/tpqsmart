<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">

                <h2>Hubungi Kami!</h2>
                <?php foreach ($alamat as $as) : ?>
                    <ul>
                        <li> <?= $as['tipe']; ?> </li>
                        <li> <?= $as['alamat']; ?></li>
                        <li><?= $as['kota']; ?></li>

                    </ul>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>