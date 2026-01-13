<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-check-circle"></i> <?= $page_title ?></h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Poto Profil</th>
                    <th width="15%">Aksi</th>
                    <th>Nama Santri</th>
                    <th>JK</th>
                    <th>TTL</th>
                    <th>Nama Ayah</th>
                    <th>Kelurahan</th>
                    <th>No HP Ayah/Ibu</th>
                    <?php if (in_groups('admin')) : ?>
                        <th>TPQ</th>
                    <?php endif; ?>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($dataSantri as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-center">
                        <?php if (!empty($row['PhotoProfil']) && file_exists(FCPATH . 'uploads/profil/user/' . $row['PhotoProfil'])): ?>
                            <img src="<?= base_url('uploads/profil/user/' . $row['PhotoProfil']) ?>" alt="Foto" class="img-thumbnail" style="width: 45px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                            <img src="<?= base_url('images/no-photo.jpg') ?>" alt="No Foto" class="img-thumbnail" style="width: 45px; height: 60px; object-fit: cover;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('backend/santri/perbandinganDataSantri/' . $row['id']) ?>" class="btn btn-primary btn-sm btn-block mb-1">
                            <i class="fas fa-edit"></i> Verifikasi
                        </a>
                    </td>
                    <td><?= esc($row['NamaSantri']) ?></td>
                    <td><?= esc($row['JenisKelamin']) ?></td>
                    <td><?= esc($row['TempatLahirSantri']) ?>, <?= !empty($row['TanggalLahirSantri']) ? formatTanggalIndonesia($row['TanggalLahirSantri'], 'd F Y') : '-' ?></td>
                    <td><?= esc($row['NamaAyah']) ?></td>
                    <td><?= esc($row['KelurahanDesaSantri']) ?></td>
                    <td>
                        <?php 
                        $hp = [];
                        if (!empty($row['NoHpAyah'])) {
                            $hp[] = '<button type="button" class="btn btn-xs btn-success mb-1" onclick="sendWhatsapp(\'' . esc($row['NoHpAyah']) . '\', \'Ayah ' . esc($row['NamaSantri']) . '\', \'' . esc($row['NamaSantri']) . '\')"><i class="fab fa-whatsapp"></i> A: ' . esc($row['NoHpAyah']) . '</button>';
                        }
                        if (!empty($row['NoHpIbu'])) {
                            $hp[] = '<button type="button" class="btn btn-xs btn-success" onclick="sendWhatsapp(\'' . esc($row['NoHpIbu']) . '\', \'Ibu ' . esc($row['NamaSantri']) . '\', \'' . esc($row['NamaSantri']) . '\')"><i class="fab fa-whatsapp"></i> I: ' . esc($row['NoHpIbu']) . '</button>';
                        }
                        echo implode('<br>', $hp);
                        ?>
                    </td>
                    <?php if (in_groups('admin')) : ?>
                        <td><?= esc($row['NamaTpq']) ?></td>
                    <?php endif; ?>
                    <td>
                        <?php 
                        switch ($row['Status']) {
                            case '1': // Valid / Aktif
                            case 'Sudah Diverifikasi':
                                echo '<span class="badge badge-success">Sudah Diverifikasi</span>';
                                break;
                            case '0': // Pending / Baru
                            case null:
                            case 'Belum Diverifikasi':
                                echo '<span class="badge badge-warning">Belum Diverifikasi</span>';
                                break;
                            case '2': // Revisi 
                            case 'Perlu Revisi':
                            case 'Revisi':
                            case 'Perlu Perbaikan':
                                echo '<span class="badge badge-danger">Perlu Perbaikan</span>';
                                break;
                            default:
                                echo '<span class="badge badge-secondary">' . esc($row['Status']) . '</span>';
                                break;
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    const CURRENT_OPERATOR = '<?= esc($operatorName) ?>';

    function sendWhatsapp(number, contactName, santriName) {
        // Remove non-numeric characters
        let cleanNumber = number.replace(/\D/g, '');
        
        // Ensure format 62xxx
        if (cleanNumber.startsWith('0')) {
            cleanNumber = '62' + cleanNumber.substring(1);
        }

        Swal.fire({
            title: 'Kirim Pesan WhatsApp',
            html: `
                <div class="text-left mb-2">
                    <label>Pilih Template Pesan:</label>
                    <select id="waTemplate" class="form-control mb-2">
                        <option value="">-- Tulis Manual --</option>
                        <option value="valid">Info: Data Valid</option>
                        <option value="revisi">Info: Perlu Perbaikan</option>
                    </select>
                    
                    <div id="revisiOptions" style="display: none;" class="mb-3 pl-1">
                        <label class="d-block text-danger small mb-1">Pilih Bagian yang Perlu Diperbaiki:</label>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input revisi-check" id="checkKk" value="Lampiran Kartu Keluarga">
                            <label class="custom-control-label font-weight-normal" for="checkKk">Lampiran Kartu Keluarga</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input revisi-check" id="checkAkte" value="Akte Kelahiran">
                            <label class="custom-control-label font-weight-normal" for="checkAkte">Akte Kelahiran</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input revisi-check" id="checkFoto" value="Foto Profil">
                            <label class="custom-control-label font-weight-normal" for="checkFoto">Foto Profil</label>
                        </div>
                         <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input revisi-check" id="checkData" value="Data Diri (Nama, TTL, dll)">
                            <label class="custom-control-label font-weight-normal" for="checkData">Data Diri (Nama, TTL, dll)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input revisi-check" id="checkLainnya" value="Lainnya">
                            <label class="custom-control-label font-weight-normal" for="checkLainnya">Lainnya</label>
                        </div>
                    </div>

                    <label>Isi Pesan:</label>
                    <textarea id="waMessage" class="form-control" rows="6" placeholder="Tulis pesan Anda di sini..."></textarea>
                    <div class="text-muted mt-1"><small>Kirim ke: ${contactName} (${number})</small></div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fab fa-whatsapp"></i> Kirim',
            cancelButtonText: 'Batal',
            didOpen: () => {
                const templateSelect = Swal.getPopup().querySelector('#waTemplate');
                const messageInput = Swal.getPopup().querySelector('#waMessage');
                const revisiOptions = Swal.getPopup().querySelector('#revisiOptions');
                const checkboxes = Swal.getPopup().querySelectorAll('.revisi-check');

                // Function to generate revisi message based on checked items
                const updateRevisiMessage = () => {
                    const checkedItems = Array.from(checkboxes)
                        .filter(cb => cb.checked)
                        .map(cb => `- ${cb.value}`);
                    
                    if (checkedItems.length > 0) {
                        messageInput.value = `Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nKami menginformasikan bahwa data santri atas nama *${santriName}* statusnya *PERLU PERBAIKAN* pada bagian:\n${checkedItems.join('\n')}\n\nKirim dengan membalas pesan ini agar kami bantu.\nTerima kasih.\n\nOperator Lembaga : ${CURRENT_OPERATOR}`;
                    } else {
                        messageInput.value = `Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nKami menginformasikan bahwa data santri atas nama *${santriName}* statusnya *PERLU PERBAIKAN*.\nKirim dengan membalas pesan ini agar kami bantu.\nTerima kasih.\n\nOperator Lembaga : ${CURRENT_OPERATOR}`;
                    }
                };

                templateSelect.addEventListener('change', () => {
                    const type = templateSelect.value;
                    let text = '';
                    
                    if (type === 'valid') {
                        revisiOptions.style.display = 'none';
                        text = `Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nKami menginformasikan bahwa data santri atas nama *${santriName}* telah kami verifikasi dan dinyatakan *VALID*.\nTerima kasih.\n\nOperator Lembaga : ${CURRENT_OPERATOR}`;
                        messageInput.value = text;
                    } else if (type === 'revisi') {
                        revisiOptions.style.display = 'block';
                        // Reset checkboxes when switching to revisi
                        checkboxes.forEach(cb => cb.checked = false);
                        updateRevisiMessage();
                    } else {
                        revisiOptions.style.display = 'none';
                        messageInput.value = '';
                    }
                });

                // Listen for checkbox changes
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateRevisiMessage);
                });
            },
            preConfirm: () => {
                const message = Swal.getPopup().querySelector('#waMessage').value;
                if (!message) {
                    Swal.showValidationMessage('Anda perlu menulis pesan!');
                    return false;
                }
                return message;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const message = encodeURIComponent(result.value);
                const url = `https://wa.me/${cleanNumber}?text=${message}`;
                window.open(url, '_blank');
            }
        });
    }

    $(function () {
        // Define custom initComplete to exclude first 3 columns (No, Photo, Aksi)
        var customInitComplete = function() {
            var api = this.api();
            var savedFilters = JSON.parse(localStorage.getItem('verifikasiDataSantri_filters') || '{}');

            api.columns().every(function() {
                var column = this;
                var colIdx = column.index();

                // Skip columns 0 (No), 1 (Poto Profil), 2 (Aksi)
                if (colIdx < 3) return;

                var select = $('<select class="form-control form-control-sm mt-1"><option value="">Semua</option></select>')
                    .appendTo($(column.header()))
                    .on('change', function() {
                        var rawVal = $(this).val();
                        var val = $.fn.dataTable.util.escapeRegex(rawVal);
                        
                        // Save to localStorage
                        var currentFilters = JSON.parse(localStorage.getItem('verifikasiDataSantri_filters') || '{}');
                        if (rawVal === "") {
                            delete currentFilters[colIdx];
                        } else {
                            currentFilters[colIdx] = rawVal;
                        }
                        localStorage.setItem('verifikasiDataSantri_filters', JSON.stringify(currentFilters));

                        // Use partial match search instead of exact match to handle HTML tags around text
                        // Or rely on smart search if we are searching for text content
                        column
                            .search(val ? val : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function(d, j) {
                    var val = d;
                    var text = d;
                    
                    // Check if value contains HTML tag
                    if (d.includes('<')) {
                        // Extract text content from HTML
                        text = $('<div>').html(d).text().trim();
                        // Use the clean text as the value for the dropdown
                        val = $.fn.dataTable.util.escapeRegex(text);
                    } else {
                        val = $.fn.dataTable.util.escapeRegex(d);
                    }
                    
                    // Add option if it doesn't already exist in the select (simple check)
                    // Note: This unique check is basic, strict DataTables implementation normally iterates unique data.
                    // Since we iterate unique() data, just guarding against empty text.
                    if (text && select.find('option[value="' + val + '"]').length === 0) {
                        select.append('<option value="' + val + '">' + text + '</option>');
                    }
                });

                // Restore saved filter
                if (savedFilters[colIdx]) {
                    var savedVal = savedFilters[colIdx];
                    // Check if value actually exists in options to avoid setting invalid state
                    if (select.find('option[value="' + savedVal + '"]').length > 0) {
                        select.val(savedVal);
                        var searchVal = $.fn.dataTable.util.escapeRegex(savedVal);
                        column.search(searchVal ? searchVal : '', true, false);
                    }
                }
            });

            // Draw table once after restoring filters if any
            if (Object.keys(savedFilters).length > 0) {
                api.draw();
            }
        };

        if (typeof initializeDataTableWithFilter === 'function') {
            initializeDataTableWithFilter("#example1", true, ["excel", "pdf", "print", "colvis"], {
                "initComplete": customInitComplete
            });
        } else {
            $("#example1").DataTable({
                "responsive": true, 
                "lengthChange": true, 
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "initComplete": customInitComplete
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        }
    });
</script>
<?= $this->endSection(); ?>
