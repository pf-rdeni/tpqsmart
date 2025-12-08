<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="card card-solid">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-address-book"></i> Kontak Santri
    </h3>
    <?php if (!$isSantri): ?>
      <div class="card-tools">
        <!-- Filter TPQ dan Kelas untuk Admin/Operator/Guru -->
        <form method="get" action="<?= base_url('backend/santri/showKontakSantri') ?>" class="d-inline-flex align-items-center">
          <?php if ($isAdmin): ?>
            <select name="filterIdTpq" class="form-control form-control-sm mr-2" style="width: 200px;" onchange="this.form.submit()">
              <option value="">Semua TPQ</option>
              <?php foreach ($dataTpq as $tpq): ?>
                <option value="<?= $tpq['IdTpq'] ?>" <?= ($currentIdTpq == $tpq['IdTpq']) ? 'selected' : '' ?>>
                  <?= esc($tpq['NamaTpq']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
          <?php if (!empty($dataKelas)): ?>
            <select name="filterIdKelas" class="form-control form-control-sm mr-2" style="width: 200px;" onchange="this.form.submit()">
              <option value="">Semua Kelas</option>
              <?php foreach ($dataKelas as $kelas): ?>
                <option value="<?= $kelas['IdKelas'] ?>" <?= ($currentIdKelas == $kelas['IdKelas']) ? 'selected' : '' ?>>
                  <?= esc($kelas['NamaKelas']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </form>
      </div>
    <?php endif; ?>
  </div>
  <div class="card-body pb-0">
    <?php if (empty($santri)): ?>
      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Tidak ada data santri untuk ditampilkan.
      </div>
    <?php else: ?>
      <div class="row">
        <?php foreach ($santri as $s): ?>
          <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column mb-3">
            <div class="card bg-light d-flex flex-fill">
              <div class="card-body pt-3">
                <div class="text-center mb-3">
                  <?php
                  $photoUrl = base_url('images/no-photo.jpg');
                  if (!empty($s['PhotoProfil'])) {
                    $photoPath = FCPATH . 'uploads/santri/' . $s['PhotoProfil'];
                    if (file_exists($photoPath)) {
                      $photoUrl = base_url('uploads/santri/' . $s['PhotoProfil']);
                    }
                  }
                  ?>
                  <img src="<?= $photoUrl ?>"
                    alt="Foto <?= esc($s['NamaSantri']) ?>"
                    class="img-fluid"
                    style="width: 150px; height: 200px; object-fit: cover; border: 3px solid #dee2e6; border-radius: 4px;"
                    onerror="this.src='<?= base_url('images/no-photo.jpg') ?>'">
                </div>
                <div>
                  <h2 class="lead text-center"><b><?= esc($s['NamaSantri']) ?></b></h2>
                  <p class="text-muted text-sm text-center">
                    <b>Id Santri: </b><?= esc($s['IdSantri']) ?><br>
                    <b>Jenis Kelamin: </b><?= esc($s['JenisKelamin'] ?? '-') ?>
                  </p>
                  <ul class="ml-4 mb-0 fa-ul text-muted">
                    <?php if (!empty($s['Alamat'])): ?>
                      <li class="small">
                        <span class="fa-li"><i class="fas fa-lg fa-building"></i></span>
                        <strong>Alamat:</strong><br>
                        <?= esc($s['Alamat']) ?>
                        <?php if (!empty($s['TitikKoordinatSantri']) || !empty($s['AlamatSantri'])): ?>
                          <?php
                          // Buat URL Google Maps
                          $mapQuery = '';
                          if (!empty($s['TitikKoordinatSantri'])) {
                            // Jika ada koordinat, gunakan koordinat
                            $mapQuery = urlencode($s['TitikKoordinatSantri']);
                            $mapUrl = "https://www.google.com/maps?q={$mapQuery}";
                          } else {
                            // Jika tidak ada koordinat, gunakan alamat lengkap
                            $mapQuery = urlencode($s['Alamat']);
                            $mapUrl = "https://www.google.com/maps/search/?api=1&query={$mapQuery}";
                          }
                          ?>
                          <br>
                          <a href="<?= $mapUrl ?>" target="_blank" class="btn btn-xs btn-info mt-1" title="Lihat di Google Maps">
                            <i class="fas fa-map-marker-alt"></i> Lihat di Peta
                          </a>
                        <?php endif; ?>
                      </li>
                    <?php endif; ?>
                    <?php if (!empty($s['NamaAyah'])): ?>
                      <li class="small">
                        <span class="fa-li"><i class="fas fa-lg fa-user"></i></span>
                        Ayah: <?= esc($s['NamaAyah']) ?>
                      </li>
                    <?php endif; ?>
                    <?php if (!empty($s['NamaIbu'])): ?>
                      <li class="small">
                        <span class="fa-li"><i class="fas fa-lg fa-user"></i></span>
                        Ibu: <?= esc($s['NamaIbu']) ?>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </div>
              <?php if (!empty($s['NoHpSantri'])): ?>
                <div class="card-footer">
                  <div class="text-right">
                    <a href="tel:<?= esc($s['NoHpSantri']) ?>" class="btn btn-sm btn-success">
                      <i class="fas fa-phone"></i> Call No HP Santri
                    </a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($santri) && count($santri) > 12): ?>
    <div class="card-footer">
      <nav aria-label="Contacts Page Navigation">
        <ul class="pagination justify-content-center m-0">
          <li class="page-item active"><a class="page-link" href="#">1</a></li>
          <!-- Pagination bisa ditambahkan jika diperlukan -->
        </ul>
      </nav>
    </div>
  <?php endif; ?>
</div>

<?= $this->endSection(); ?>