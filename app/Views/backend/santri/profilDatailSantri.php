<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Profil Detail Santri</h3>
            <div>
                <a href="<?= base_url('backend/santri/editSantri/' . $dataSantri['IdSantri']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i><span class="d-none d-md-inline">&nbsp;Edit</span></a>
                <a href="<?= base_url('backend/santri/generatePDFprofilSantriRaport/' . $dataSantri['IdSantri']); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-print"></i><span class="d-none d-md-inline">&nbsp;Print</span></a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-3">
                    <?php
                    $thumb = !empty($dataSantri['PhotoProfil']) ? base_url('uploads/santri/' . $dataSantri['PhotoProfil']) : base_url('images/no-photo.jpg');
                    ?>
                    <img src="<?= $thumb; ?>" alt="Foto" class="img-fluid img-thumbnail" style="max-height:180px;">
                </div>
                <div class="col-md-9">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th style="width:220px;">Nama</th>
                                <td><?= ucwords(strtolower($dataSantri['NamaSantri'])); ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td><?= $dataSantri['JenisKelamin']; ?></td>
                            </tr>
                            <tr>
                                <th>Tempat & Tgl Lahir</th>
                                <td><?= $dataSantri['TempatLahirSantri']; ?>, <?= $dataSantri['TanggalLahirSantri']; ?></td>
                            </tr>
                            <tr>
                                <th>Alamat Santri</th>
                                <td><?= $dataSantri['AlamatSantri']; ?></td>
                            </tr>
                            <tr>
                                <th>Diterima di TPA</th>
                                <td><?= $dataSantri['NamaTpq']; ?></td>
                            </tr>
                            <tr>
                                <th>&nbsp;&nbsp;• Kelas</th>
                                <td><?= $dataSantri['NamaKelas']; ?></td>
                            </tr>
                            <tr>
                                <th>&nbsp;&nbsp;• Tanggal</th>
                                <td><?= date('d-m-Y', strtotime($dataSantri['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <th>Nama Orang Tua - Ayah</th>
                                <td><?= $dataSantri['NamaAyah']; ?></td>
                            </tr>
                            <tr>
                                <th>Nama Orang Tua - Ibu</th>
                                <td><?= $dataSantri['NamaIbu']; ?></td>
                            </tr>
                            <tr>
                                <th>Alamat Orang Tua</th>
                                <td><?= $dataSantri['AlamatAyah'] ?: $dataSantri['AlamatIbu']; ?></td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td><?= $dataSantri['NoHpSantri'] ?: ($dataSantri['NoHpAyah'] ?: $dataSantri['NoHpIbu']); ?></td>
                            </tr>
                            <tr>
                                <th>Pekerjaan Ayah</th>
                                <td><?= $dataSantri['PekerjaanUtamaAyah']; ?></td>
                            </tr>
                            <tr>
                                <th>Pekerjaan Ibu</th>
                                <td><?= $dataSantri['PekerjaanUtamaIbu']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>