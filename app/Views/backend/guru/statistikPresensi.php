<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-map-marked-alt mr-2"></i>Statistik Presensi Guru</h3>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="<?= base_url('backend/guru/statistik-presensi') ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kegiatan">Kegiatan Absensi <span class="text-danger">*</span></label>
                            <select name="kegiatan" id="kegiatan" class="form-control" required>
                                <option value="">-- Pilih Kegiatan --</option>
                                <?php foreach ($kegiatanList as $kegiatan): ?>
                                    <option value="<?= $kegiatan['Id'] ?>" <?= ($filterKegiatan == $kegiatan['Id']) ? 'selected' : '' ?>>
                                        <?= esc($kegiatan['NamaKegiatan']) ?> - <?= date('d/m/Y', strtotime($kegiatan['Tanggal'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <?php if ($isAdmin && !empty($tpqList)): ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tpq">TPQ</label>
                            <select name="tpq" id="tpq" class="form-control">
                                <option value="">-- Semua TPQ --</option>
                                <?php foreach ($tpqList as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>" <?= ($filterTpq == $tpq['IdTpq']) ? 'selected' : '' ?>>
                                        <?= esc($tpq['NamaTpq']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_dari">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" value="<?= $filterTanggalDari ?>">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tanggal_sampai">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" value="<?= $filterTanggalSampai ?>">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <?php if ($selectedKegiatan): ?>
                <hr>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $stats['total'] ?></h3>
                                <p>Total Guru</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $stats['hadir'] ?></h3>
                                <p>Hadir</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $stats['izin'] ?></h3>
                                <p>Izin</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= $stats['sakit'] ?></h3>
                                <p>Sakit</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-procedures"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($locationData)): ?>
                    <!-- Map Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-map-marked-alt mr-2"></i>Peta Sebaran Lokasi Absensi</h3>
                                </div>
                                <div class="card-body">
                                    <div id="attendance-map" style="height: 500px; border-radius: 8px;"></div>
                                    <p class="text-muted text-center mt-2 mb-0 small">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Menampilkan <?= count($locationData) ?> lokasi absensi guru
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-table mr-2"></i>Detail Lokasi Absensi</h3>
                                </div>
                                <div class="card-body">
                                    <table id="locationTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Guru</th>
                                                <th>TPQ</th>
                                                <th>Status</th>
                                                <th>Waktu Absensi</th>
                                                <th>Koordinat</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($locationData as $loc): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= esc($loc['nama']) ?></td>
                                                    <td><?= esc($loc['tpq']) ?></td>
                                                    <td>
                                                        <span class="badge badge-<?= $loc['status'] == 'Hadir' ? 'success' : ($loc['status'] == 'Izin' ? 'warning' : 'primary') ?>">
                                                            <?= esc($loc['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= esc($loc['waktu']) ?></td>
                                                    <td>
                                                        <small><?= $loc['lat'] ?>, <?= $loc['lng'] ?></small>
                                                    </td>
                                                    <td><?= esc($loc['keterangan']) ?: '-' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="icon fas fa-info-circle"></i>
                        Tidak ada data lokasi absensi untuk kegiatan ini.
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="icon fas fa-exclamation-triangle"></i>
                    Silakan pilih kegiatan untuk melihat statistik dan peta lokasi absensi.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    <?php if (!empty($locationData)): ?>
    $('#locationTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 10,
        "order": [[1, 'asc']]
    });

    // Initialize Map
    const locationData = <?= json_encode($locationData) ?>;
    
    if (locationData && locationData.length > 0) {
        // Calculate center point
        let sumLat = 0, sumLng = 0;
        locationData.forEach(loc => {
            sumLat += loc.lat;
            sumLng += loc.lng;
        });
        const centerLat = sumLat / locationData.length;
        const centerLng = sumLng / locationData.length;

        // Initialize map
        const map = L.map('attendance-map').setView([centerLat, centerLng], 13);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Create marker cluster group
        const markers = L.markerClusterGroup({
            chunkedLoading: true,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        // Define icon colors
        const iconColors = {
            'Hadir': '#28a745',
            'Izin': '#ffc107',
            'Sakit': '#007bff'
        };

        // Add markers
        locationData.forEach(loc => {
            const color = iconColors[loc.status] || '#6c757d';
            
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [25, 25],
                iconAnchor: [12, 12]
            });

            const marker = L.marker([loc.lat, loc.lng], { icon: customIcon });

            const popupContent = `
                <div style="min-width: 200px;">
                    <h6 class="mb-2 font-weight-bold">${loc.nama}</h6>
                    <p class="mb-1 small"><i class="fas fa-school mr-1"></i> ${loc.tpq}</p>
                    <p class="mb-1 small"><i class="far fa-clock mr-1"></i> ${loc.waktu}</p>
                    <p class="mb-1 small">
                        <span class="badge badge-${loc.status === 'Hadir' ? 'success' : (loc.status === 'Izin' ? 'warning' : 'primary')}">${loc.status}</span>
                    </p>
                    ${loc.keterangan ? `<p class="mb-0 small text-muted font-italic">"${loc.keterangan}"</p>` : ''}
                </div>
            `;

            marker.bindPopup(popupContent);
            markers.addLayer(marker);
        });

        map.addLayer(markers);

        // Fit bounds
        if (locationData.length > 1) {
            const bounds = markers.getBounds();
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
    <?php endif; ?>
});
</script>
<?= $this->endSection(); ?>
