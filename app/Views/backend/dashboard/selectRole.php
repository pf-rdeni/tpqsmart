<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title">
                            <i class="fas fa-user-check"></i> Pilih Peran Aktif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i> Informasi</h5>
                            <p>Anda memiliki beberapa peran dalam sistem. Silakan pilih peran yang ingin Anda gunakan saat ini.</p>
                            <p class="mb-0">Anda dapat mengubah peran aktif kapan saja melalui menu profil atau dashboard.</p>
                        </div>

                        <div class="row mt-4">
                            <?php foreach ($available_roles as $roleKey => $roleData): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card card-outline card-<?= $roleData['color'] ?> role-card" 
                                         data-role="<?= $roleKey ?>"
                                         style="cursor: pointer; transition: transform 0.2s;">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-<?= $roleData['icon'] ?>"></i> 
                                                <?= esc($roleData['label']) ?>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text"><?= esc($roleData['description']) ?></p>
                                            <button class="btn btn-<?= $roleData['color'] ?> btn-block btn-lg select-role-btn" 
                                                    data-role="<?= $roleKey ?>">
                                                <i class="fas fa-check-circle"></i> Pilih Peran Ini
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Hover effect
    $('.role-card').hover(
        function() {
            $(this).css('transform', 'translateY(-5px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );

    // Handle klik card atau button
    $('.select-role-btn, .role-card').on('click', function() {
        const role = $(this).data('role');
        const $btn = $(this).closest('.role-card').find('.select-role-btn');
        
        // Disable semua button
        $('.select-role-btn').prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        // Kirim request untuk switch role
        $.ajax({
            url: '<?= base_url('backend/dashboard/switch-role') ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                role: role
            }),
            success: function(response) {
                if (response.success) {
                    // Redirect ke dashboard sesuai peran
                    window.location.href = response.redirect;
                } else {
                    alert('Gagal mengubah peran: ' + (response.message || 'Terjadi kesalahan'));
                    // Enable button kembali
                    $('.select-role-btn').prop('disabled', false);
                    $('.select-role-btn').each(function() {
                        const roleKey = $(this).data('role');
                        $(this).html('<i class="fas fa-check-circle"></i> Pilih Peran Ini');
                    });
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat mengubah peran. Silakan coba lagi.');
                // Enable button kembali
                $('.select-role-btn').prop('disabled', false);
                $('.select-role-btn').each(function() {
                    const roleKey = $(this).data('role');
                    $(this).html('<i class="fas fa-check-circle"></i> Pilih Peran Ini');
                });
            }
        });
    });
});
</script>

<style>
.role-card {
    min-height: 250px;
    border-width: 2px;
}

.role-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card-purple {
    border-color: #6f42c1;
}

.card-purple .card-header {
    background-color: #6f42c1;
    color: white;
}
</style>

<?= $this->endSection(); ?>
<?= $this->endSection(); ?>

