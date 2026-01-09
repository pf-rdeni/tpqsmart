<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kegiatan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/kegiatan-absensi/new') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Kegiatan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped" id="table-kegiatan">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kegiatan</th>
                                <th>Jenis Jadwal</th>
                                <th>Lingkup</th>
                                <th>Link Absensi</th>
                                <th>Status Active</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kegiatan as $key => $item) : ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= esc($item['NamaKegiatan']) ?></td>
                                    <td>
                                        <?php
                                        $jenisLabel = [
                                            'sekali' => '<span class="badge badge-secondary">Sekali</span>',
                                            'harian' => '<span class="badge badge-success">Harian</span>',
                                            'mingguan' => '<span class="badge badge-primary">Mingguan</span>',
                                            'bulanan' => '<span class="badge badge-warning">Bulanan</span>',
                                            'tahunan' => '<span class="badge badge-info">Tahunan</span>'
                                        ];
                                        echo $jenisLabel[$item['JenisJadwal'] ?? 'sekali'] ?? '<span class="badge badge-secondary">Sekali</span>';
                                        
                                        // Detail Pola
                                        $type = $item['JenisJadwal'] ?? 'sekali';
                                        if ($type !== 'sekali') {
                                            echo '<div class="small text-muted mt-1">';
                                            
                                            // Interval Info
                                            $interval = $item['Interval'] ?? 1;
                                            $unit = match($type) {
                                                'harian' => 'hari',
                                                'mingguan' => 'minggu',
                                                'bulanan' => 'bulan',
                                                'tahunan' => 'tahun',
                                                default => ''
                                            };
                                            
                                            if ($interval > 1) {
                                                echo "Setiap $interval $unit<br>";
                                            } else {
                                                // Optional: "Setiap hari/minggu/bulan" implied
                                            }

                                            // Pattern Details
                                            if ($type == 'harian') {
                                                 if (($item['OpsiPola'] ?? 'Interval') == 'Weekday') {
                                                     echo "Setiap Hari Kerja (Senin-Jumat)";
                                                 } elseif ($interval == 1) {
                                                     echo "Setiap Hari";
                                                 }
                                            } elseif ($type == 'mingguan') {
                                                $days = explode(',', $item['HariDalamMinggu'] ?? '');
                                                $names = array_map(function($d) {
                                                    return ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][(int)$d] ?? '-';
                                                }, $days);
                                                echo 'Setiap Hari ' . implode(', ', array_filter($names));
                                            } elseif ($type == 'bulanan') {
                                                if (($item['OpsiPola'] ?? 'Tanggal') == 'Tanggal') {
                                                    echo 'Setiap Tanggal ' . ($item['TanggalDalamBulan'] ?? '-');
                                                } else {
                                                    $pos = ['', 'Ke-1', 'Ke-2', 'Ke-3', 'Ke-4', 'Terakhir'][$item['PosisiMinggu'] ?? 1] ?? '';
                                                    $dIdx = $item['HariDalamMinggu'] ?? 1;
                                                    $dName = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][(int)$dIdx] ?? '';
                                                    echo "Setiap Hari $dName Minggu $pos setiap Bulannya";
                                                }
                                            } elseif ($type == 'tahunan') {
                                                // Format English Month to Indonesian if needed, or just use English for now
                                                // Assuming setlocale is irrelevant here, stick to English or mapping
                                                $monthNum = $item['BulanTahun'] ?? 1;
                                                $monthName = date('F', mktime(0,0,0, $monthNum, 1));
                                                
                                                if (($item['OpsiPola'] ?? 'Tanggal') == 'Tanggal') {
                                                    echo 'Setiap Tanggal ' . ($item['TanggalDalamBulan'] ?? '-') . " Bulan $monthName";
                                                } else {
                                                    $pos = ['', 'Pertama', 'Kedua', 'Ketiga', 'Keempat', 'Terakhir'][$item['PosisiMinggu'] ?? 1] ?? '';
                                                    $dIdx = $item['HariDalamMinggu'] ?? 1;
                                                    // Handle array case for HariDalamMinggu
                                                    if(is_array($dIdx)) $dIdx = reset($dIdx);
                                                    
                                                    $dName = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][(int)$dIdx] ?? '';
                                                    echo "Setiap Hari $dName Minggu $pos Bulan $monthName";
                                                }
                                            }
                                            
                                            // Rentang Waktu
                                            if (!empty($item['TanggalMulaiRutin'])) {
                                                echo '<br><span class="text-muted border-top pt-1 mt-1 d-block">';
                                                echo '<i class="far fa-clock mr-1"></i>';
                                                echo date('d M Y', strtotime($item['TanggalMulaiRutin']));
                                                
                                                $endType = $item['JenisBatasAkhir'] ?? 'Tanggal';
                                                if ($endType == 'Tanggal' && !empty($item['TanggalAkhirRutin'])) {
                                                    echo ' - ' . date('d M Y', strtotime($item['TanggalAkhirRutin']));
                                                } elseif ($endType == 'Kejadian') {
                                                    echo ' (' . ($item['JumlahKejadian'] ?? 0) . 'x)';
                                                } elseif ($endType == 'Selamanya') {
                                                    echo ' (Tanpa Batas)';
                                                }
                                                echo '</span>';
                                            }
                                            echo '</div>';
                                        } else {
                                            // Untuk 'Sekali', tampilkan Tanggal & Waktu di sini
                                            echo '<div class="small text-muted mt-1">';
                                            echo '<i class="far fa-calendar-alt mr-1"></i> ' . date('d M Y', strtotime($item['Tanggal']));
                                            echo '<br>';
                                            echo '<i class="far fa-clock mr-1"></i> ' . date('H:i', strtotime($item['JamMulai'])) . ' - ' . date('H:i', strtotime($item['JamSelesai']));
                                            echo '</div>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($item['Lingkup'] == 'Umum'): ?>
                                            <span class="badge badge-info">Umum</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">TPQ <?= !empty($item['NamaTpq']) ? esc($item['NamaTpq']) : '(' . $item['IdTpq'] . ')' ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" value="<?= base_url('presensi/' . $item['Token']) ?>" id="link-<?= $item['Id'] ?>" readonly>
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-info btn-flat" onclick="copyLink('link-<?= $item['Id'] ?>')" title="Copy Link"><i class="fas fa-copy"></i></button>
                                                <a href="<?= base_url('presensi/' . $item['Token']) ?>" target="_blank" class="btn btn-primary btn-flat" title="Buka Halaman Absensi"><i class="fas fa-external-link-alt"></i></a>
                                                <button type="button" class="btn btn-success btn-flat btn-wa-modal" 
                                                    data-link="<?= base_url('presensi/' . $item['Token']) ?>" 
                                                    data-nama="<?= esc($item['NamaKegiatan']) ?>" 
                                                    title="Kirim WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input switch-active" id="activeSwitch<?= $item['Id'] ?>" data-id="<?= $item['Id'] ?>" <?= ($item['IsActive'] == 1) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="activeSwitch<?= $item['Id'] ?>"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('backend/kegiatan-absensi/' . $item['Id'] . '/edit') ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Form Delete using helper or standard form -->
                                        <form action="<?= base_url('backend/kegiatan-absensi/' . $item['Id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Modal WhatsApp -->
    <div class="modal fade" id="waModal" tabindex="-1" role="dialog" aria-labelledby="waModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="waModalLabel">Kirim Link via WhatsApp</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Link Presensi</label>
                        <input type="text" class="form-control" id="waLink" readonly>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="checkGroup">
                            <label class="custom-control-label" for="checkGroup">Kirim ke Grup / Pilih Kontak Lain (Manual)</label>
                        </div>
                    </div>

                    <div class="form-group" id="group-guru-select">
                        <label>Pilih Guru</label>
                        <select class="form-control select2" id="waGuru" style="width: 100%;">
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach($guruList as $guru): ?>
                                <option value="<?= $guru['NoHp'] ?>" data-nama="<?= $guru['Nama'] ?>"><?= $guru['Nama'] ?> - <?= $guru['NoHp'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pesan</label>
                        <textarea class="form-control" id="waMessage" rows="6"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="sendWa()"><i class="fab fa-whatsapp"></i> Kirim</button>
                </div>
            </div>
        </div>
    </div>

    </section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(function() {
        // Initialize Select2
        // dropdownParent is required for search to work within a Bootstrap modal
        $('.select2').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#waModal')
        });

        // Toggle Active
        $('.switch-active').change(function() {
            var id = $(this).data('id');
            // ... (rest of the code)
            var isChecked = $(this).is(':checked');
            
            // If checking (turning ON), others might turn OFF, so we might reload or handle UI.
            // AJAX call
            $.ajax({
                url: '<?= base_url('backend/kegiatan-absensi/active') ?>/' + id,
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Status kegiatan berhasil diubah.');
                        // Reload to reflect that others might have been deactivated
                        setTimeout(function(){ location.reload(); }, 500);
                    } else {
                        toastr.error('Gagal mengubah status.');
                    }
                }
            });
        });

        // Initialize Message on Guru Change
        $('#waGuru').change(function(){
            updateWaMessage();
        });

        // Toggle Group Checkbox
        $('#checkGroup').change(function() {
            if($(this).is(':checked')) {
                $('#waGuru').prop('disabled', true);
            } else {
                $('#waGuru').prop('disabled', false);
            }
            updateWaMessage();
        });

        // Open WA Modal via class listener (Using event delegation for DataTables compatibility)
        $(document).on('click', '.btn-wa-modal', function() {
            var link = $(this).data('link');
            var nama = $(this).data('nama');
            openWaModal(link, nama);
        });
    });

    function copyLink(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */
        document.execCommand("copy");
        toastr.success('Link berhasil disalin ke clipboard');
    }

    var currentKegiatanName = '';

    function openWaModal(link, kegiatanName) {
        $('#waLink').val(link);
        currentKegiatanName = kegiatanName;
        
        // Reset state
        $('#checkGroup').prop('checked', false);
        $('#waGuru').prop('disabled', false).val('').trigger('change');
        
        // Default Message handled by updateWaMessage trigger or manual init
        updateWaMessage();
        
        $('#waModal').modal('show');
    }
    
    function updateWaMessage() {
         var link = $('#waLink').val();
         var isGroup = $('#checkGroup').is(':checked');
         var greeting = "Assalamualaikum";
         
         if (isGroup) {
             greeting += " Bapak/Ibu Guru";
         } else {
             var guruNama = $('#waGuru option:selected').data('nama');
             if(guruNama) {
                 greeting += " Ustadz/Ustadzah *" + guruNama + "*";
             }
         }
         
         var msg = greeting + ",\nBerikut adalah link presensi untuk kegiatan *" + currentKegiatanName + "*:\n" + link + "\n\nMohon segera melakukan absensi.\nTerima kasih.";
         $('#waMessage').val(msg);
    }

    function sendWa() {
        var isGroup = $('#checkGroup').is(':checked');
        var message = $('#waMessage').val();
        var url = "";

        if (isGroup) {
            // No phone number, just text. User picks contact/group in WA.
            url = "https://wa.me/?text=" + encodeURIComponent(message);
        } else {
            var noHp = $('#waGuru').val();
            if (!noHp) {
                toastr.error('Silakan pilih guru terlebih dahulu atau centang opsi Kirim ke Grup.');
                return;
            }
            
            // Format NoHP
            var formattedHp = noHp.replace(/\D/g, '');
            if (formattedHp.startsWith('0')) {
                formattedHp = '62' + formattedHp.substring(1);
            }
             url = "https://wa.me/" + formattedHp + "?text=" + encodeURIComponent(message);
        }
        
        window.open(url, '_blank');
        $('#waModal').modal('hide');
    }
</script>
<?= $this->endSection(); ?>
