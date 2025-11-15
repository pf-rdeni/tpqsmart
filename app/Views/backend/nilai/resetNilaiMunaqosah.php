<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trash"></i> Hapus Nilai Munaqosah
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Peringatan!</strong> Tindakan ini akan <strong>MENGHAPUS</strong> data nilai munaqosah berdasarkan <strong>NoPeserta</strong> yang dipilih. Data yang dihapus tidak dapat dikembalikan. Pastikan Anda telah memilih dengan benar sebelum melakukan penghapusan.
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
                                    <label for="TypeUjian">Type Ujian <small class="text-muted">(Opsional)</small></label>
                                    <select name="TypeUjian" id="TypeUjian" class="form-control">
                                        <option value="">-- Pilih Type Ujian --</option>
                                        <option value="munaqosah">Munaqosah</option>
                                        <option value="pra-munaqosah">Pra-Munaqosah</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" id="btnPreview" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Preview Data yang Akan Dihapus
                                </button>
                                <button type="button" id="btnReset" class="btn btn-danger" disabled>
                                    <i class="fas fa-trash"></i> Hapus Nilai
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Preview Section -->
                    <div id="previewSection" class="mt-4" style="display: none;">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-list"></i> Preview Data yang Akan Dihapus
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <strong>Total Data yang Akan Dihapus: <span id="totalCount">0</span></strong>
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
                                                <th>Type Ujian</th>
                                                <th>No Peserta</th>
                                                <th>Jumlah Nilai</th>
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
        const savedFilters = localStorage.getItem('resetNilaiMunaqosahFilters');
        if (savedFilters) {
            try {
                const filters = JSON.parse(savedFilters);
                if (filters.IdTpq) {
                    $('#IdTpq').val(filters.IdTpq);
                }
                if (filters.IdTahunAjaran) {
                    $('#IdTahunAjaran').val(filters.IdTahunAjaran);
                }
                if (filters.TypeUjian) {
                    $('#TypeUjian').val(filters.TypeUjian);
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
            TypeUjian: $('#TypeUjian').val() || ''
        };
        localStorage.setItem('resetNilaiMunaqosahFilters', JSON.stringify(filters));
    }

    // Load saved filters on page load
    loadSavedFilters();

    // Save filters when changed
    $('#IdTpq, #IdTahunAjaran, #TypeUjian').on('change', function() {
        saveFilters();
    });

    // Preview button click
    $('#btnPreview').on('click', function() {
        const IdTpq = $('#IdTpq').val();
        const IdTahunAjaran = $('#IdTahunAjaran').val();
        const TypeUjian = $('#TypeUjian').val();

        // Validasi minimal satu filter harus diisi
        if (!IdTpq && !IdTahunAjaran && !TypeUjian) {
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
            url: '<?= base_url('backend/nilai/resetNilaiMunaqosah/getCount') ?>',
            type: 'POST',
            data: {
                IdTpq: IdTpq,
                IdTahunAjaran: IdTahunAjaran,
                TypeUjian: TypeUjian,
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
                // Buat unique key untuk checkbox
                const rowKey = row.NoPeserta || '';
                
                // Cek apakah checkbox harus disabled (TotalNilai = 0 = belum ada data)
                const totalNilaiNum = parseInt(row.TotalNilai) || 0;
                const isDisabled = totalNilaiNum === 0;
                const disabledAttr = isDisabled ? 'disabled' : '';
                const disabledClass = isDisabled ? 'text-muted' : '';
                const disabledTitle = isDisabled ? 'title="Data sudah kosong, tidak perlu dihapus"' : '';
                
                html += '<tr class="' + disabledClass + '">';
                html += '<td><input type="checkbox" class="row-checkbox" data-key="' + rowKey + '" data-nopeserta="' + (row.NoPeserta || '') + '" data-totalnilai="' + row.TotalNilai + '" ' + disabledAttr + ' ' + disabledTitle + '></td>';
                html += '<td>' + no++ + '</td>';
                html += '<td>' + (row.NamaTpq || row.IdTpq || '-') + '</td>';
                html += '<td>' + (row.KelurahanDesa || '-') + '</td>';
                html += '<td>' + (row.IdTahunAjaran || '-') + '</td>';
                html += '<td>' + (row.TypeUjian || '-') + '</td>';
                html += '<td>' + (row.NoPeserta || '-') + '</td>';
                html += '<td><strong>' + row.TotalNilai + '</strong></td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>';
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
            $('#btnReset').html('<i class="fas fa-trash"></i> Hapus Nilai (' + selectedRows.length + ' peserta, ' + totalSelected + ' data)');
        } else {
            $('#btnReset').prop('disabled', true);
            $('#btnReset').html('<i class="fas fa-trash"></i> Hapus Nilai');
        }
    }

    // Reset button click
    $('#btnReset').on('click', function() {
        const selectedRows = $('.row-checkbox:checked');
        
        if (selectedRows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu peserta untuk dihapus!'
            });
            return;
        }

        // Kumpulkan data peserta yang dipilih dan buat detail informasi
        const selectedPeserta = [];
        let totalSelectedNilai = 0;
        const pesertaDetails = [];
        
        selectedRows.each(function() {
            const row = $(this).closest('tr');
            const rowData = {
                NoPeserta: $(this).data('nopeserta')
            };
            selectedPeserta.push(rowData);
            
            const totalNilai = parseInt($(this).data('totalnilai') || 0);
            totalSelectedNilai += totalNilai;
            
            // Kumpulkan detail peserta untuk ditampilkan
            const pesertaInfo = {
                NamaTpq: row.find('td').eq(2).text(),
                KelurahanDesa: row.find('td').eq(3).text(),
                TahunAjaran: row.find('td').eq(4).text(),
                TypeUjian: row.find('td').eq(5).text(),
                NoPeserta: row.find('td').eq(6).text(),
                TotalNilai: totalNilai
            };
            pesertaDetails.push(pesertaInfo);
        });

        // Buat detail informasi untuk konfirmasi
        let detailHtml = '<div style="text-align: left; max-height: 300px; overflow-y: auto;">';
        detailHtml += '<table class="table table-sm table-bordered" style="margin-bottom: 10px;">';
        detailHtml += '<thead><tr><th>TPQ</th><th>Kelurahan/Desa</th><th>Tahun Ajaran</th><th>Type Ujian</th><th>No Peserta</th><th>Jumlah Nilai</th></tr></thead>';
        detailHtml += '<tbody>';
        
        pesertaDetails.forEach(function(detail) {
            detailHtml += '<tr>';
            detailHtml += '<td>' + detail.NamaTpq + '</td>';
            detailHtml += '<td>' + detail.KelurahanDesa + '</td>';
            detailHtml += '<td>' + detail.TahunAjaran + '</td>';
            detailHtml += '<td>' + detail.TypeUjian + '</td>';
            detailHtml += '<td>' + detail.NoPeserta + '</td>';
            detailHtml += '<td><strong>' + detail.TotalNilai + '</strong></td>';
            detailHtml += '</tr>';
        });
        
        detailHtml += '</tbody></table>';
        detailHtml += '</div>';

        // Konfirmasi dengan detail lengkap
        Swal.fire({
            title: 'Konfirmasi Hapus Nilai',
            html: '<div style="text-align: left;">' +
                  '<p><strong>Ringkasan:</strong></p>' +
                  '<ul>' +
                  '<li>Total peserta yang dipilih: <strong>' + selectedRows.length + '</strong></li>' +
                  '<li>Total data nilai: <strong>' + totalSelectedNilai + '</strong></li>' +
                  '</ul>' +
                  '<p><strong>Detail peserta yang akan dihapus:</strong></p>' +
                  detailHtml +
                  '<p class="text-danger mt-3"><strong><i class="fas fa-exclamation-triangle"></i> Peringatan:</strong> Tindakan ini akan <strong>MENGHAPUS</strong> semua data nilai untuk peserta yang dipilih berdasarkan <strong>NoPeserta</strong>. Data yang dihapus tidak dapat dikembalikan!</p>' +
                  '</div>',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Sekarang!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '800px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show progress
                Swal.fire({
                    title: 'Memproses Hapus...',
                    html: 'Sedang menghapus data nilai, mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/nilai/resetNilaiMunaqosah/delete') ?>',
                    type: 'POST',
                    data: {
                        selectedPeserta: selectedPeserta,
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
                                $('#btnReset').html('<i class="fas fa-trash"></i> Hapus Nilai');
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

