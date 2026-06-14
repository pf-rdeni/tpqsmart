<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-2 col-6">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahBarang">
                        Tambah Barang
                    </button>
                </div>
                <div class="col-lg-6 col-6">
                    <h3 class="card-title">Data Barang Lucky Draw</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No Barang</th>
                        <th>Kategori / Grup</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $b) : ?>
                        <tr>
                            <td><?= $b->no_barang ?></td>
                            <td><?= $b->kategori ?></td>
                            <td><?= $b->nama_barang ?></td>
                            <td><?= $b->jumlah ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ModalEditBarang<?= $b->id ?>"><i class="fas fa-edit"></i></button>
                                <button type="button" class="btn btn-danger btn-sm btn-delete-barang" data-url="<?= base_url('/backend/luckydraw/barang/delete/' . $b->id) ?>" data-nama="<?= esc($b->nama_barang) ?>" data-pemenang="<?= $b->jumlah - $b->sisa ?>"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        
                        <!-- Modal Edit -->
                        <div class="modal fade" id="ModalEditBarang<?= $b->id ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Barang</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <form action="<?= base_url('/backend/luckydraw/barang/update/' . $b->id) ?>" method="POST" class="form-edit-barang">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>No Barang (Otomatis)</label>
                                                <input type="text" name="no_barang" class="form-control" value="<?= $b->no_barang ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Kategori / Grup</label>
                                                <input type="text" name="kategori" class="form-control" value="<?= $b->kategori ?>" required placeholder="Contoh: Grup Utama, Grup A, Grup Utama Santri, dll.">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Barang</label>
                                                <input type="text" name="nama_barang" class="form-control" value="<?= $b->nama_barang ?>" required placeholder="Contoh: TV LED 32 Inch, Dispenser, Kipas Angin, dll.">
                                            </div>
                                            <div class="form-group">
                                                <label>Jumlah</label>
                                                <input type="number" name="jumlah" class="form-control" value="<?= $b->jumlah ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="ModalTambahBarang" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Barang Lucky Draw</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?= base_url('/backend/luckydraw/barang/store') ?>" method="POST" id="form-tambah-barang">
                <div class="modal-body">
                    <div class="form-group">
                        <label>No Barang (Otomatis)</label>
                        <input type="text" name="no_barang" class="form-control" value="<?= $next_no_barang ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Kategori / Grup</label>
                        <input type="text" name="kategori" class="form-control" required placeholder="Contoh: Grup Utama, Grup A, Grup Utama Santri, dll.">
                    </div>
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required placeholder="Contoh: TV LED 32 Inch, Dispenser, Kipas Angin, dll.">
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. AJAX Add Barang
    const formTambah = document.getElementById('form-tambah-barang');
    if (formTambah) {
        formTambah.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = formTambah.querySelector('button[type="submit"]');
            const originalHtml = btnSubmit.innerHTML;
            
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Menyimpan...';
            btnSubmit.disabled = true;

            const formData = new FormData(formTambah);
            fetch(formTambah.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $('#ModalTambahBarang').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    btnSubmit.innerHTML = originalHtml;
                    btnSubmit.disabled = false;
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error(error);
                btnSubmit.innerHTML = originalHtml;
                btnSubmit.disabled = false;
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // 2. AJAX Edit Barang
    const formsEdit = document.querySelectorAll('.form-edit-barang');
    formsEdit.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = form.querySelector('button[type="submit"]');
            const originalHtml = btnSubmit.innerHTML;
            
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Memperbarui...';
            btnSubmit.disabled = true;

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $(form).closest('.modal').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    btnSubmit.innerHTML = originalHtml;
                    btnSubmit.disabled = false;
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error(error);
                btnSubmit.innerHTML = originalHtml;
                btnSubmit.disabled = false;
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    });

    // 3. SweetAlert Delete Confirmation
    const deleteButtons = document.querySelectorAll('.btn-delete-barang');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const nama = this.getAttribute('data-nama');
            const pemenangCount = parseInt(this.getAttribute('data-pemenang')) || 0;

            let text = `Barang "${nama}" akan dihapus permanen!`;
            let confirmText = 'Ya, Hapus!';
            let titleText = 'Apakah Anda yakin?';
            
            if (pemenangCount > 0) {
                titleText = 'Peringatan Penting!';
                text = `Barang "${nama}" sudah memiliki ${pemenangCount} pemenang undian terkait. Menghapus barang ini akan turut MENGHAPUS secara permanen semua data pemenang tersebut dari daftar pemenang!`;
                confirmText = 'Ya, Hapus Semua!';
            }

            Swal.fire({
                title: titleText,
                text: text,
                icon: pemenangCount > 0 ? 'error' : 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem saat menghapus data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    });
});
</script>

<?= $this->endSection(); ?>
