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
                                            
                                            // Info Interval
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
                                                // Opsional: "Setiap hari/minggu/bulan" tersirat
                                            }

                                            // Detail Pola
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
                                                // Format Bulan Inggris ke Indonesia jika diperlukan, atau gunakan Inggris untuk saat ini
                                                // Asumsikan setlocale tidak relevan di sini, tetap gunakan Inggris atau pemetaan
                                                $monthNum = $item['BulanTahun'] ?? 1;
                                                $monthName = date('F', mktime(0,0,0, $monthNum, 1));
                                                
                                                if (($item['OpsiPola'] ?? 'Tanggal') == 'Tanggal') {
                                                    echo 'Setiap Tanggal ' . ($item['TanggalDalamBulan'] ?? '-') . " Bulan $monthName";
                                                } else {
                                                    $pos = ['', 'Pertama', 'Kedua', 'Ketiga', 'Keempat', 'Terakhir'][$item['PosisiMinggu'] ?? 1] ?? '';
                                                    $dIdx = $item['HariDalamMinggu'] ?? 1;
                                                    // Menangani kasus array untuk HariDalamMinggu
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
                                        <!-- Form Hapus menggunakan helper atau form standar -->
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
        // Penjelasan Proses:
        // Saat halaman dimuat (document ready), inisialisasi semua library dan event listener.
        // - Select2 untuk dropdown pencarian.
        // - Event 'change' untuk switch aktif/nonaktif.
        // - Event 'change' untuk pilihan Guru di modal WA.
        // - Event 'change' untuk checkbox Group/Manual di modal WA.
        // - Event 'click' untuk tombol buka modal WA.
        // Inisialisasi Select2
        // dropdownParent diperlukan agar pencarian berfungsi di dalam modal Bootstrap
        $('.select2').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#waModal')
        });

        // Toggle Aktif
        // Penjelasan Proses:
        // Mengirim AJAX request untuk mengubah status aktif/nonaktif kegiatan.
        // Jika sukses, reload halaman.
        $('.switch-active').change(function() {
            var id = $(this).data('id');
            // ... (rest of the code)
            var isChecked = $(this).is(':checked');
            
            // Jika dicentang (mengaktifkan), yang lain mungkin nonaktif, jadi kita mungkin perlu memuat ulang atau menangani UI.
            // Panggilan AJAX
            $.ajax({
                url: '<?= base_url('backend/kegiatan-absensi/active') ?>/' + id,
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Status kegiatan berhasil diubah.');
                        // Muat ulang untuk mencerminkan bahwa yang lain mungkin telah dinonaktifkan
                        setTimeout(function(){ location.reload(); }, 500);
                    } else {
                        toastr.error('Gagal mengubah status.');
                    }
                }
            });
        });

        // Inisialisasi Pesan saat Guru Berubah
        $('#waGuru').change(function(){
            updateWaMessage();
        });

        // Toggle Checkbox Grup
        $('#checkGroup').change(function() {
            if($(this).is(':checked')) {
                $('#waGuru').prop('disabled', true);
            } else {
                $('#waGuru').prop('disabled', false);
            }
            updateWaMessage();
        });

        // Buka Modal WA melalui event listener kelas (Menggunakan delegasi event untuk kompatibilitas DataTables)
        $(document).on('click', '.btn-wa-modal', function() {
            var link = $(this).data('link');
            var nama = $(this).data('nama');
            openWaModal(link, nama);
        });
    });

    function copyLink(elementId) {
        // Penjelasan Proses:
        // Menyalin URL absensi dari input text ke clipboard pengguna.
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* Untuk perangkat seluler */
        document.execCommand("copy");
        toastr.success('Link berhasil disalin ke clipboard');
    }

    var currentKegiatanName = '';

    function openWaModal(link, kegiatanName) {
        // Penjelasan Proses:
        // Membuka modal pop-up untuk kirim WA.
        // Mereset form input di dalam modal ke kondisi awal.
        $('#waLink').val(link);
        currentKegiatanName = kegiatanName;
        
        // Reset status
        $('#checkGroup').prop('checked', false);
        $('#waGuru').prop('disabled', false).val('').trigger('change');
        
        // Pesan Default ditangani oleh trigger updateWaMessage atau inisialisasi manual
        updateWaMessage();
        
        $('#waModal').modal('show');
    }
    
    function updateWaMessage() {
        // Penjelasan Proses:
        // Membuat template pesan WA secara otomatis berdasarkan pilihan (Grup/Personal).
        // Format: Salam + Nama Guru (jika personal) + Link + Pesan Penutup.
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
        // Penjelasan Proses:
        // Membuka URL WhatsApp API (wa.me) di tab baru.
        // Jika Grup: Buka wa.me/?text=... (User pilih kontak sendiri).
        // Jika Personal: Buka wa.me/62xxx?text=... (Langsung ke chat guru).
        var isGroup = $('#checkGroup').is(':checked');
        var message = $('#waMessage').val();
        var url = "";

        if (isGroup) {
            // Tanpa nomor telepon, hanya teks. Pengguna memilih kontak/grup di WA.
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
