<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Generate QR Code</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('backend/qr/generate') ?>" method="GET">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="content">Konten QR Code</label>
                            <input type="text" class="form-control" id="content" name="content" required>
                        </div>
                        <div class="form-group">
                            <label for="size">Ukuran QR Code</label>
                            <select class="form-control" id="size" name="size">
                                <option value="100">100x100</option>
                                <option value="200">200x200</option>
                                <option value="300" selected>300x300</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_type">Tipe Pengguna</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="">Pilih Tipe Pengguna</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="guru">Guru</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_id">ID Pengguna</label>
                            <input type="text" class="form-control" id="user_id" name="user_id" required>
                        </div>
                        <div class="form-group">
                            <label for="user_name">Nama Pengguna</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" required>
                        </div>
                        <div class="form-group">
                            <label for="user_position">Jabatan</label>
                            <input type="text" class="form-control" id="user_position" name="user_position" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generate QR Code</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fungsi untuk mengisi form berdasarkan tipe pengguna
        $('#user_type').on('change', function() {
            var userType = $(this).val();
            var userId = $('#user_id');
            var userName = $('#user_name');
            var userPosition = $('#user_position');

            // Reset form
            userId.val('');
            userName.val('');
            userPosition.val('');

            // Tampilkan loading jika perlu
            if (userType) {
                // Contoh: Ambil data guru jika tipe pengguna adalah guru
                if (userType === 'guru') {
                    $.get('<?= base_url('backend/guru/getData') ?>', function(data) {
                        // Isi dropdown dengan data guru
                        // Implementasi sesuai kebutuhan
                    });
                }
                // Tambahkan kondisi lain sesuai kebutuhan
            }
        });
    });
</script>
<?= $this->endSection() ?>