<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Data Juri Sertifikasi
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/sertifikasi/createJuriSertifikasi') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Juri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tblJuriSertifikasi" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Juri</th>
                                <th>Username Juri</th>
                                <th>Group Materi</th>
                                <th>Nama Materi</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($juri_list as $juri): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($juri['IdJuri']) ?></td>
                                    <td><?= esc($juri['usernameJuri']) ?></td>
                                    <td><?= esc($juri['IdGroupMateri']) ?></td>
                                    <td><?= esc($juri['NamaMateri'] ?? '-') ?></td>
                                    <td><?= $juri['created_at'] ? date('d/m/Y H:i', strtotime($juri['created_at'])) : '-' ?></td>
                                    <td><?= $juri['updated_at'] ? date('d/m/Y H:i', strtotime($juri['updated_at'])) : '-' ?></td>
                                    <td>
                                        <a href="<?= base_url('backend/sertifikasi/editJuriSertifikasi/' . $juri['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $juri['id'] ?>)" title="Hapus">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function() {
        $('#tblJuriSertifikasi').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "order": [[0, "asc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data juri akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteJuri(id);
            }
        });
    }

    function deleteJuri(id) {
        $.ajax({
            url: '<?= base_url('backend/sertifikasi/deleteJuriSertifikasi') ?>/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menghapus data'
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>

