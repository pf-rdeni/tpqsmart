<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Santri TPQ Di Kecamatan Seri Kuala Lobam</h3>
                <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i><span class="d-none d-md-inline">&nbsp;Daftar Santri Baru</span>
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <h5>Data Pendaftaran Santri Terbaru</h5>
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Profil</th>
                        <th>Aksi</th>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Tanggal Reg</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td>
                                <?php
                                $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                                ?>
                                <img src="<?= $santri['PhotoProfil'] ? $uploadPath . $santri['PhotoProfil'] : base_url('images/no-photo.jpg'); ?>"
                                    alt="PhotoProfil"
                                    class="img-fluid popup-image"
                                    width="30"
                                    height="40"
                                    onmouseover="showPopup(this)"
                                    onmouseout="hidePopup(this)"
                                    onclick="showPopup(this)"
                                    style="cursor: pointer;">
                                <div class="image-popup" style="display: none; position: absolute; z-index: 1000;">
                                    <img src="<?= $santri['PhotoProfil'] ? $uploadPath . $santri['PhotoProfil'] : base_url('images/no-photo.jpg'); ?>"
                                        alt="PhotoProfil"
                                        width="200"
                                        height="250">
                                </div>
                                <script>
                                    function showPopup(img) {
                                        const popup = img.nextElementSibling;
                                        popup.style.display = 'block';
                                    }

                                    function hidePopup(img) {
                                        const popup = img.nextElementSibling;
                                        popup.style.display = 'none';
                                    }
                                </script>
                            </td>
                            <td class="d-flex justify-content-between gap-1">
                                <a href="javascript:void(0)" onclick="showDetailSantri('<?= $santri['IdSantri']; ?>')" class="btn btn-info btn-sm flex-fill">
                                    <i class="fas fa-eye"></i><span class="d-none d-md-inline">&nbsp;Detail</span>
                                </a>
                                <a href="<?= base_url('backend/santri/editSantri/' . $santri['IdSantri']); ?>" class="btn btn-warning btn-sm flex-fill">
                                    <i class="fas fa-edit"></i><span class="d-none d-md-inline">&nbsp;Edit</span>
                                </a>
                                <a href="javascript:void(0)" onclick="deleteSantri('<?= $santri['IdSantri']; ?>')" class="btn btn-danger btn-sm flex-fill">
                                    <i class="fas fa-trash"></i><span class="d-none d-md-inline">&nbsp;Hapus</span>
                                </a>
                            </td>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= ucwords(strtolower($santri['NamaSantri'])); ?></td>
                            <td><?= ucwords(strtolower($santri['KelurahanDesa'])); ?></td>
                            <!-- Mengubah format nama TPQ -->
                            <!-- detail keterangan:
                            \b(al|el|ad)-(\w+) = kata yang diawali dengan al- atau el- atau ad- dan diikuti dengan kata lain
                            \b = kata awal
                            (al|el|ad) = al atau el atau ad
                            - = tanda hubung
                            (\w+) = kata setelah tanda hubung -->
                            <td><?= preg_replace_callback('/\b(al|el|ad)-(\w+)/i', function ($matches) {
                                    return ucfirst(strtolower($matches[1])) . '-' . ucfirst($matches[2]);
                                }, ucwords(strtolower($santri['NamaTpq']))); ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <td>
                                <?php if ($santri['status'] == "Belum Diverifikasi"): ?>
                                    <span class="badge bg-warning"><?= $santri['status']; ?></span>
                                <?php else: ?>
                                    <?php if ($santri['status'] == "Perlu Perbaikan"): ?>
                                        <span class="badge bg-danger"><?= $santri['status']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $santri['status']; ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y H:i:s', strtotime($santri['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Profil</th>
                        <th>Aksi</th>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Tanggal Reg</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
        </div>
        <!-- /.card-body -->
        <div class="card-header">
            <h5>Data Santri Baru berdasarkan TPQ</h5>
        </div>
        <div class="card-body">
            <table id="example3" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama TPQ</th>
                        <th>Alamat TPQ</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($dataTpq as $tpq): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $tpq['NamaTpq']; ?></td>
                            <td><?= $tpq['Alamat']; ?></td>
                            <td>
                                <a href="<?= base_url('backend/santri/showSantriBaruPerKelasTpq/' . $tpq['IdTpq']); ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Nama TPQ</th>
                        <th>Alamat TPQ</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.card -->
</div>

<!-- buatkan modal untuk view detail santri-->

<!-- Modal Detail Santri -->
<div class="modal fade" id="detailSantriModal" tabindex="-1" role="dialog" aria-labelledby="detailSantriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailSantriModalLabel">Detail Santri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="detailSantriTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="data-santri-tab" data-toggle="tab" href="#data-santri" role="tab">
                            <i class="fas fa-user"></i> Data Santri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-ortu-tab" data-toggle="tab" href="#data-ortu" role="tab">
                            <i class="fas fa-users"></i> Data Orang Tua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-alamat-tab" data-toggle="tab" href="#data-alamat" role="tab">
                            <i class="fas fa-map-marker-alt"></i> Data Alamat
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="detailSantriTabContent">
                    <!-- Tab Data Santri -->
                    <div class="tab-pane fade show active" id="data-santri" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <img id="modalPhotoProfil" src="" alt="Foto Profil" class="img-fluid rounded" style="max-width: 200px;">
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%">ID Santri</td>
                                        <td width="5%">:</td>
                                        <td id="modalIdSantri"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Santri</td>
                                        <td>:</td>
                                        <td id="modalNamaSantri"></td>
                                    </tr>
                                    <tr>
                                        <td>TPQ</td>
                                        <td>:</td>
                                        <td id="modalNamaTpq"></td>
                                    </tr>
                                    <tr>
                                        <td>Kelas</td>
                                        <td>:</td>
                                        <td id="modalKelas"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatus"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Orang Tua -->
                    <div class="tab-pane fade" id="data-ortu" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">Data Ayah</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%">Nama Ayah</td>
                                        <td width="5%">:</td>
                                        <td id="modalNamaAyah"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatusAyah"></td>
                                    </tr>
                                    <tr>
                                        <td>Pekerjaan</td>
                                        <td>:</td>
                                        <td id="modalPekerjaanAyah"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">Data Ibu</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%">Nama Ibu</td>
                                        <td width="5%">:</td>
                                        <td id="modalNamaIbu"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatusIbu"></td>
                                    </tr>
                                    <tr>
                                        <td>Pekerjaan</td>
                                        <td>:</td>
                                        <td id="modalPekerjaanIbu"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Alamat -->
                    <div class="tab-pane fade" id="data-alamat" role="tabpanel">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%">Alamat</td>
                                <td width="5%">:</td>
                                <td id="modalAlamat"></td>
                            </tr>
                            <tr>
                                <td>RT/RW</td>
                                <td>:</td>
                                <td><span id="modalRT"></span>/<span id="modalRW"></span></td>
                            </tr>
                            <tr>
                                <td>Kelurahan/Desa</td>
                                <td>:</td>
                                <td id="modalKelurahanDesa"></td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td>:</td>
                                <td id="modalKecamatan"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    /*=== Modal Delete santri===*/
    function deleteSantri(IdSantri) {
        // Dapatkan nama santri dari baris tabel
        const row = event.target.closest('tr');
        const namaSantri = row.querySelector('td:nth-child(4)').textContent;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Data santri ID: <strong>${IdSantri}</strong> Nama: <strong>${namaSantri}</strong> akan dihapus permanen!`,
            icon: 'question',
            iconColor: '#d33',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('<?= base_url('backend/santri/deleteSantriBaru/') ?>' + IdSantri, {
                        method: 'DELETE'
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data santri berhasil dihapus.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error('Gagal menghapus data santri.');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }

    /*=== Modal View detail santri===*/
    function showDetailSantri(IdSantri) {
        const dataSantri = <?= json_encode($dataSantri) ?>;
        const santri = dataSantri.find(s => s.IdSantri === IdSantri);

        if (santri) {
            // Set nilai untuk tab Data Santri
            const uploadPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/') ?>';
            document.getElementById('modalPhotoProfil').src = santri.PhotoProfil ? uploadPath + santri.PhotoProfil : '<?= base_url('images/no-photo.jpg') ?>';
            document.getElementById('modalIdSantri').textContent = santri.IdSantri;
            document.getElementById('modalNamaSantri').textContent = santri.NamaSantri;
            document.getElementById('modalNamaTpq').textContent = santri.NamaTpq;
            document.getElementById('modalKelas').textContent = santri.NamaKelas;

            // Set status dengan badge
            const statusElement = document.getElementById('modalStatus');
            let badgeClass = 'bg-success';
            if (santri.status === 'Belum Diverifikasi') {
                badgeClass = 'bg-warning';
            } else if (santri.status === 'Perlu Perbaikan') {
                badgeClass = 'bg-danger';
            }
            statusElement.innerHTML = `<span class="badge ${badgeClass}">${santri.status}</span>`;

            // Set nilai untuk tab Data Orang Tua
            document.getElementById('modalNamaAyah').textContent = santri.NamaAyah || '-';
            document.getElementById('modalStatusAyah').textContent = santri.StatusAyah || '-';
            document.getElementById('modalPekerjaanAyah').textContent = santri.PekerjaanUtamaAyah || '-';
            document.getElementById('modalNamaIbu').textContent = santri.NamaIbu || '-';
            document.getElementById('modalStatusIbu').textContent = santri.StatusIbu || '-';
            document.getElementById('modalPekerjaanIbu').textContent = santri.PekerjaanUtamaIbu || '-';

            // Set nilai untuk tab Data Alamat
            document.getElementById('modalAlamat').textContent = santri.Alamat || '-';
            document.getElementById('modalRT').textContent = santri.RT || '-';
            document.getElementById('modalRW').textContent = santri.RW || '-';
            document.getElementById('modalKelurahanDesa').textContent = santri.KelurahanDesa || '-';
            document.getElementById('modalKecamatan').textContent = 'Seri Kuala Lobam';

            // Tampilkan modal
            $('#detailSantriModal').modal('show');
        }
    }
</script>
<?= $this->endSection(); ?>