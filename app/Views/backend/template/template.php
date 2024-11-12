<!DOCTYPE html>
<html lang="id">

<?= $this->include('/backend/template/meta'); ?>
<?= $this->include('/backend/template/navbar'); ?>
<?= $this->include('/backend/template/sidebar'); ?>
<?= $this->include('/backend/template/header'); ?>
<?= $this->renderSection('content'); ?>
<?= $this->include('/backend/template/footer'); ?>
<?= $this->include('/backend/template/js'); ?>
<?= $this->renderSection('scripts'); ?>

</body>

</html>