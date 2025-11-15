<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-undo"></i> Reset Nilai
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Peringatan!</strong> Tindakan ini akan mereset kolom <strong>IdGuru</strong> dan <strong>Nilai</strong> pada tabel nilai berdasarkan filter yang dipilih. Pastikan Anda telah memilih filter dengan benar sebelum melakukan reset.
                    </div>

                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="IdTpq">TPQ <small class="text-muted">(Opsional)</small></label>
                                    <select name="IdTpq" id="IdTpq" class="form-control">
                                        <option value="">-- Pilih TPQ --</option>
                                        <?php foreach ($dataTpq as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?> - <?= $tpq['KelurahanDesa'] ?? '' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="IdTahunAjaran">Tahun Ajaran <small class="text-muted">(Opsional)</small></label>
                                    <select name="IdTahunAjaran" id="IdTahunAjaran" class="form-control">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        <?php foreach ($tahunAjaranList as $ta): ?>
                                            <option value="<?= $ta ?>"><?= convertTahunAjaran($ta) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Semester">Semester <small class="text-muted">(Opsional)</small></label>
                                    <select name="Semester" id="Semester" class="form-control">
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" id="btnPreview" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Preview Data yang Akan Direset
                                </button>
                                <button type="button" id="btnReset" class="btn btn-danger" disabled>
                                    <i class="fas fa-undo"></i> Reset Nilai
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Preview Section -->
                    <div id="previewSection" class="mt-4" style="display: none;">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-list"></i> Preview Data yang Akan Direset
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <strong>Total Data yang Akan Direset: <span id="totalCount">0</span></strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm" id="previewTable">
                                        <thead>
                                            <tr>
                                                <th width="40">
                                                    <input type="checkbox" id="selectAll" title="Pilih Semua">
                                                </th>
                                                <th>No</th>
                                                <th>TPQ</th>
                                                <th>Kelurahan/Desa</th>
                                                <th>Tahun Ajaran</th>
                                                <th>Semester</th>
                                                <th>Kelas</th>
                                                <th>Jumlah Nilai</th>
                                                <th>Status Pengisian</th>
                                            </tr>
                                        </thead>
                                        <tbody id="previewTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    let previewData = null;

    // Load saved filters from localStorage
    function loadSavedFilters() {
        const savedFilters = localStorage.getItem('resetNilaiFilters');
        if (savedFilters) {
            try {
                const filters = JSON.parse(savedFilters);
                if (filters.IdTpq) {
                    $('#IdTpq').val(filters.IdTpq);
                }
                if (filters.IdTahunAjaran) {
                    $('#IdTahunAjaran').val(filters.IdTahunAjaran);
                }
                if (filters.Semester) {
                    $('#Semester').val(filters.Semester);
                }
            } catch (e) {
                console.error('Error loading saved filters:', e);
            }
        }
    }

    // Save filters to localStorage
    function saveFilters() {
        const filters = {
            IdTpq: $('#IdTpq').val() || '',
            IdTahunAjaran: $('#IdTahunAjaran').val() || '',
            Semester: $('#Semester').val() || ''
        };
        localStorage.setItem('resetNilaiFilters', JSON.stringify(filters));
    }

    // Load saved filters on page load
    loadSavedFilters();

    // Save filters when changed
    $('#IdTpq, #IdTahunAjaran, #Semester').on('change', function() {
        saveFilters();
    });

    // Preview button click
    $('#btnPreview').on('click', function() {
        const IdTpq = $('#IdTpq').val();
        const IdTahunAjaran = $('#IdTahunAjaran').val();
        const Semester = $('#Semester').val();

        // Validasi minimal satu filter harus diisi
        if (!IdTpq && !IdTahunAjaran && !Semester) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Minimal satu filter harus diisi!'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang menghitung data yang akan direset',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/resetNilai/getCount') ?>',
            type: 'POST',
            data: {
                IdTpq: IdTpq,
                IdTahunAjaran: IdTahunAjaran,
                Semester: Semester,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    previewData = response.data;
                    displayPreview(previewData);
                    // Tombol reset tetap disabled sampai ada checkbox yang dipilih
                    $('#btnReset').prop('disabled', true);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Terjadi kesalahan saat mengambil data'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMessage = 'Terjadi kesalahan pada server';
                if (xhr.status === 404) {
                    errorMessage = 'Endpoint tidak ditemukan. Pastikan routes sudah dikonfigurasi dengan benar.';
                } else if (xhr.status === 403) {
                    errorMessage = 'Akses ditolak. Pastikan Anda memiliki hak akses Admin.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan pada server. Silakan cek log untuk detail.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else {
                    errorMessage = 'Error: ' + error + ' (Status: ' + xhr.status + ')';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Display preview data
    function displayPreview(data) {
        $('#totalCount').text(data.total);
        
        let html = '';
        if (data.detail && data.detail.length > 0) {
            let no = 1;
            data.detail.forEach(function(row) {
                // Format status pengisian
                // Hijau = aman (belum ada yang diisi/0%), Merah = 100% terisi, Kuning = sebagian terisi
                let statusHtml = '';
                if (row.TotalNilai > 0) {
                    // Konversi ke number untuk memastikan perbandingan yang benar
                    const totalSudahDiisiNum = parseInt(row.TotalSudahDiisi) || 0;
                    
                    // Hitung persentase dengan lebih akurat, gunakan toFixed untuk menampilkan minimal 1 desimal jika < 1%
                    const persentaseRaw = (totalSudahDiisiNum / row.TotalNilai) * 100;
                    let persentase = Math.round(persentaseRaw);
                    let persentaseDisplay = persentase;
                    
                    // Jika persentase < 1% tapi > 0, tampilkan dengan 1 desimal
                    if (persentase === 0 && totalSudahDiisiNum > 0) {
                        persentaseDisplay = persentaseRaw.toFixed(1);
                    }
                    
                    let badgeClass = 'badge-secondary';
                    
                    // Gunakan TotalSudahDiisi untuk menentukan warna badge, bukan persentase
                    if (totalSudahDiisiNum === 0) {
                        // Hijau = aman, belum ada yang diisi
                        badgeClass = 'badge-success';
                        persentaseDisplay = 0;
                    } else if (persentase >= 100) {
                        // Merah = 100% sudah terisi
                        badgeClass = 'badge-danger';
                    } else {
                        // Kuning = sebagian sudah terisi
                        badgeClass = 'badge-warning';
                    }
                    
                    statusHtml = '<span class="badge ' + badgeClass + '">' + 
                                 row.TotalSudahDiisi + ' / ' + row.TotalNilai + 
                                 ' (' + persentaseDisplay + '%)</span>';
                } else {
                    statusHtml = '<span class="badge badge-secondary">0 / 0 (0%)</span>';
                }
                
                // Buat unique key untuk checkbox
                const rowKey = row.IdTpq + '_' + row.IdTahunAjaran + '_' + row.Semester + '_' + row.IdKelas;
                
                // Cek apakah checkbox harus disabled (TotalSudahDiisi = 0 = belum ada yang diisi)
                // Gunakan TotalSudahDiisi langsung, bukan persentase karena persentase bisa 0% meskipun ada nilai (karena pembulatan)
                // Konversi ke number untuk memastikan perbandingan yang benar
                const totalSudahDiisiNum = parseInt(row.TotalSudahDiisi) || 0;
                const isDisabled = totalSudahDiisiNum === 0;
                const disabledAttr = isDisabled ? 'disabled' : '';
                const disabledClass = isDisabled ? 'text-muted' : '';
                const disabledTitle = isDisabled ? 'title="Data sudah kosong, tidak perlu direset"' : '';
                
                html += '<tr class="' + disabledClass + '">';
                html += '<td><input type="checkbox" class="row-checkbox" data-key="' + rowKey + '" data-idtpq="' + (row.IdTpq || '') + '" data-idtahunajaran="' + (row.IdTahunAjaran || '') + '" data-semester="' + (row.Semester || '') + '" data-idkelas="' + (row.IdKelas || '') + '" data-totalnilai="' + row.TotalNilai + '" ' + disabledAttr + ' ' + disabledTitle + '></td>';
                html += '<td>' + no++ + '</td>';
                html += '<td>' + (row.NamaTpq || row.IdTpq || '-') + '</td>';
                html += '<td>' + (row.KelurahanDesa || '-') + '</td>';
                html += '<td>' + (row.IdTahunAjaran || '-') + '</td>';
                html += '<td>' + (row.Semester || '-') + '</td>';
                html += '<td>' + (row.NamaKelas || '-') + '</td>';
                html += '<td><strong>' + row.TotalNilai + '</strong></td>';
                html += '<td>' + statusHtml + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>';
        }
        
        $('#previewTableBody').html(html);
        $('#previewSection').show();
        
        // Reset select all checkbox
        $('#selectAll').prop('checked', false);
    }

    // Select All checkbox - hanya pilih yang tidak disabled
    $(document).on('change', '#selectAll', function() {
        $('.row-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
        updateSelectedCount();
    });

    // Individual checkbox change - hanya hitung yang tidak disabled
    $(document).on('change', '.row-checkbox', function() {
        const totalCheckboxes = $('.row-checkbox:not(:disabled)').length;
        const checkedCheckboxes = $('.row-checkbox:not(:disabled):checked').length;
        $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        const selectedRows = $('.row-checkbox:checked');
        let totalSelected = 0;
        selectedRows.each(function() {
            totalSelected += parseInt($(this).data('totalnilai') || 0);
        });
        
        if (selectedRows.length > 0) {
            $('#btnReset').prop('disabled', false);
            $('#btnReset').html('<i class="fas fa-undo"></i> Reset Nilai (' + selectedRows.length + ' kelas, ' + totalSelected + ' data)');
        } else {
            $('#btnReset').prop('disabled', true);
            $('#btnReset').html('<i class="fas fa-undo"></i> Reset Nilai');
        }
    }

    // Reset button click
    $('#btnReset').on('click', function() {
        const selectedRows = $('.row-checkbox:checked');
        
        if (selectedRows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu kelas untuk direset!'
            });
            return;
        }

        // Kumpulkan data kelas yang dipilih dan buat detail informasi
        const selectedClasses = [];
        let totalSelectedNilai = 0;
        let totalSudahDiisi = 0;
        const classDetails = [];
        
        selectedRows.each(function() {
            const row = $(this).closest('tr');
            const rowData = {
                IdTpq: $(this).data('idtpq'),
                IdTahunAjaran: $(this).data('idtahunajaran'),
                Semester: $(this).data('semester'),
                IdKelas: $(this).data('idkelas')
            };
            selectedClasses.push(rowData);
            
            const totalNilai = parseInt($(this).data('totalnilai') || 0);
            totalSelectedNilai += totalNilai;
            
            // Ambil informasi dari badge untuk menghitung yang sudah diisi
            const badgeText = row.find('.badge').text();
            const match = badgeText.match(/(\d+)\s*\/\s*(\d+)/);
            if (match) {
                totalSudahDiisi += parseInt(match[1] || 0);
            }
            
            // Kumpulkan detail kelas untuk ditampilkan
            const kelasInfo = {
                NamaTpq: row.find('td').eq(2).text(),
                KelurahanDesa: row.find('td').eq(3).text(),
                TahunAjaran: row.find('td').eq(4).text(),
                Semester: row.find('td').eq(5).text(),
                Kelas: row.find('td').eq(6).text(),
                TotalNilai: totalNilai,
                Status: badgeText
            };
            classDetails.push(kelasInfo);
        });

        // Buat detail informasi untuk konfirmasi
        let detailHtml = '<div style="text-align: left; max-height: 300px; overflow-y: auto;">';
        detailHtml += '<table class="table table-sm table-bordered" style="margin-bottom: 10px;">';
        detailHtml += '<thead><tr><th>TPQ</th><th>Kelurahan/Desa</th><th>Tahun Ajaran</th><th>Semester</th><th>Kelas</th><th>Jumlah Nilai</th><th>Status</th></tr></thead>';
        detailHtml += '<tbody>';
        
        classDetails.forEach(function(detail) {
            detailHtml += '<tr>';
            detailHtml += '<td>' + detail.NamaTpq + '</td>';
            detailHtml += '<td>' + detail.KelurahanDesa + '</td>';
            detailHtml += '<td>' + detail.TahunAjaran + '</td>';
            detailHtml += '<td>' + detail.Semester + '</td>';
            detailHtml += '<td>' + detail.Kelas + '</td>';
            detailHtml += '<td><strong>' + detail.TotalNilai + '</strong></td>';
            detailHtml += '<td>' + detail.Status + '</td>';
            detailHtml += '</tr>';
        });
        
        detailHtml += '</tbody></table>';
        detailHtml += '</div>';

        // Konfirmasi dengan detail lengkap
        Swal.fire({
            title: 'Konfirmasi Reset Nilai',
            html: '<div style="text-align: left;">' +
                  '<p><strong>Ringkasan:</strong></p>' +
                  '<ul>' +
                  '<li>Total kelas yang dipilih: <strong>' + selectedRows.length + '</strong></li>' +
                  '<li>Total data nilai: <strong>' + totalSelectedNilai + '</strong></li>' +
                  '<li>Nilai yang sudah diisi: <strong>' + totalSudahDiisi + '</strong></li>' +
                  '<li>Nilai yang belum diisi: <strong>' + (totalSelectedNilai - totalSudahDiisi) + '</strong></li>' +
                  '</ul>' +
                  '<p><strong>Detail kelas yang akan direset:</strong></p>' +
                  detailHtml +
                  '<p class="text-danger mt-3"><strong><i class="fas fa-exclamation-triangle"></i> Peringatan:</strong> Tindakan ini akan mereset kolom <strong>IdGuru</strong> dan <strong>Nilai</strong> untuk semua data di kelas yang dipilih. Tindakan ini tidak dapat dibatalkan!</p>' +
                  '</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset Sekarang!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '800px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show progress
                Swal.fire({
                    title: 'Memproses Reset...',
                    html: 'Sedang mereset data nilai, mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/resetNilai/reset') ?>',
                    type: 'POST',
                    data: {
                        selectedClasses: selectedClasses,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: '<p>' + response.message + '</p>' +
                                      '<p>Total data yang direset: <strong>' + response.data.total_affected + '</strong></p>',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reset form but keep filters
                                $('#previewSection').hide();
                                $('#btnReset').prop('disabled', true);
                                $('#btnReset').html('<i class="fas fa-undo"></i> Reset Nilai');
                                $('.row-checkbox').prop('checked', false);
                                $('#selectAll').prop('checked', false);
                                previewData = null;
                                // Filters tetap tersimpan di localStorage
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal melakukan reset nilai'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                        
                        let errorMessage = 'Terjadi kesalahan pada server';
                        if (xhr.status === 404) {
                            errorMessage = 'Endpoint tidak ditemukan. Pastikan routes sudah dikonfigurasi dengan benar.';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Akses ditolak. Pastikan Anda memiliki hak akses Admin.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan pada server. Silakan cek log untuk detail.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else {
                            errorMessage = 'Error: ' + error + ' (Status: ' + xhr.status + ')';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>

