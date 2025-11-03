<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Data Nilai Juri</h3>
                        <div class="d-flex">
                            <div class="mr-2">
                                <label class="mb-0 small">Juri</label>
                                <?php if ($isJuri && $currentJuriId): ?>
                                    <select id="filterJuri" class="form-control form-control-sm" disabled>
                                        <option value="<?= esc($currentJuriId) ?>"><?= esc($currentJuriData['UsernameJuri'] ?? $currentJuriId) ?></option>
                                    </select>
                                    <input type="hidden" id="hiddenJuriId" value="<?= esc($currentJuriId) ?>">
                                <?php else: ?>
                                    <select id="filterJuri" class="form-control form-control-sm">
                                        <option value="">Pilih Juri</option>
                                        <?php if (!empty($juriList)) : foreach ($juriList as $juri): ?>
                                                <option value="<?= esc($juri['IdJuri']) ?>"><?= esc($juri['UsernameJuri'] ?? $juri['IdJuri']) ?></option>
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <div class="mr-2">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <input type="text" id="filterTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>" <?= $isJuri ? 'readonly' : '' ?>>
                            </div>
                            <div class="mr-2">
                                <label class="mb-0 small">Type Ujian</label>
                                <select id="filterTypeUjian" class="form-control form-control-sm" <?= $isJuri ? 'disabled' : '' ?>>
                                    <option value="munaqosah" <?= ($currentTypeUjian ?? 'munaqosah') === 'munaqosah' ? 'selected' : '' ?>>Munaqosah</option>
                                    <option value="pra-munaqosah" <?= ($currentTypeUjian ?? 'munaqosah') === 'pra-munaqosah' ? 'selected' : '' ?>>Pra-Munaqosah</option>
                                </select>
                                <?php if ($isJuri && $currentTypeUjian): ?>
                                    <input type="hidden" id="hiddenTypeUjian" value="<?= esc($currentTypeUjian) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="align-self-end">
                                <button id="btnReload" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Info Juri -->
                        <div class="alert alert-info" id="juriInfo" style="display: none;">
                            <strong>Juri:</strong> <span id="juriName">-</span><br>
                            <strong>Grup Materi:</strong> <span id="grupMateri">-</span>
                        </div>

                        <!-- Tabel Data Nilai -->
                        <div class="table-responsive" id="tblContainer">
                            <table id="tblDataNilaiJuri" class="table table-bordered table-striped" style="width:100%">
                                <thead id="theadDataNilaiJuri"></thead>
                                <tbody id="tbodyDataNilaiJuri"></tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Klik tombol Edit pada kolom Aksi untuk mengubah nilai.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Verifikasi Admin/Operator untuk Edit Nilai -->
<div class="modal fade" id="modalVerifyEditNilai" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi untuk Edit Nilai</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="mb-2"><i class="fas fa-info-circle"></i> Informasi Peserta yang Akan Diedit</h6>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Nama Santri:</strong> <span id="verifyNamaSantri" class="badge badge-success">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong>No Peserta:</strong> <span id="verifyNoPesertaDisplay" class="badge badge-info">-</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Type Ujian:</strong> <span id="verifyTypeUjianDisplay" class="badge badge-primary">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong id="verifyTypeUjianLabel">Munaqosah:</strong> Masukkan kredensial <strong id="verifyRequiredRole">Admin</strong>
                        </div>
                    </div>
                    <small class="d-block mt-2">Edit nilai memerlukan persetujuan sesuai type ujian</small>
                </div>
                <form id="formVerifyEdit">
                    <input type="hidden" id="verifyNilaiId">
                    <input type="hidden" id="verifyTypeUjian">
                    <input type="hidden" id="verifyNoPeserta">
                    
                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" id="verifyUsername" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" id="verifyPassword" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Alasan Edit <span class="text-danger">*</span></label>
                        <textarea id="verifyAlasanEdit" class="form-control" rows="3" placeholder="Masukkan alasan mengapa nilai perlu diedit..." required></textarea>
                        <small class="form-text text-muted">Alasan edit akan disimpan sebagai tracking untuk audit</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnVerifyEdit">Verifikasi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Edit Nilai (mirip inputNilaiJuri step 2) -->
<div class="modal fade" id="modalFormEditNilai" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Edit Nilai 
                    <span id="modalTypeUjianBadge" class="badge badge-primary ml-2">-</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <!-- Info Peserta -->
                <div id="infoPesertaEdit" class="alert alert-info mb-3" style="display: none;">
                    <h6><i class="icon fas fa-info"></i> Informasi Peserta</h6>
                    <div id="pesertaInfoEdit"></div>
                </div>

                <!-- Form Nilai (akan di-generate secara dinamis) -->
                <div id="formNilaiEditContainer">
                    <!-- Form akan di-generate di sini -->
                </div>

                <!-- Section untuk menampilkan Ayat (di dalam modal) -->
                <div id="ayatSectionEdit" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0" id="ayatTitleEdit">Lihat Ayat</h5>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openInNewTabEdit()">
                                        <i class="fas fa-external-link-alt"></i> Buka di Tab Baru
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideAyatSectionEdit()">
                                        <i class="fas fa-times"></i> Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="iframeContainerEdit">
                                <iframe id="iframeAyatEdit" src="" style="width: 100%; height: 500px; border: none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSaveEditNilai">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .nilai-0 {
        background-color: #f8d7da !important;
        color: #dc3545;
        font-weight: 600;
    }

    .nowrap {
        white-space: nowrap;
    }

    .dt-center {
        text-align: center;
    }

    .dt-left {
        text-align: left;
    }

    /* Styling untuk section ayat di dalam modal */
    #ayatSectionEdit {
        margin-top: 1rem;
        z-index: 1;
    }

    #ayatSectionEdit .card {
        border: 1px solid #dee2e6;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    #ayatSectionEdit .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    #iframeContainerEdit {
        overflow: hidden;
        position: relative;
        width: 100%;
        height: 500px;
        border-radius: 0.375rem;
    }

    #iframeAyatEdit {
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.3s ease;
        transform-origin: top left;
        overflow: hidden;
    }
</style>
<script>
    let dtInstance = null;
    let currentData = null;

    function fmt(val) {
        return (val === 0 || val === '0') ? '<span class="nilai-0">0</span>' : val;
    }

    function buildHeader(categories) {
        const headerCategories = categories || [];
        let th = '<tr>' +
            '<th class="dt-left">No Peserta</th>' +
            '<th class="dt-left">Nama Santri</th>' +
            '<th class="dt-left">TPQ</th>' +
            '<th class="dt-center">Type</th>';
        
        headerCategories.forEach(k => {
            const label = (k && (k.name || k.NamaKategoriMateri)) ? (k.name || k.NamaKategoriMateri) : (k.id || k.IdKategoriMateri || '-');
            th += `<th class="dt-center nowrap">${label}</th>`;
        });
        
        th += '<th class="dt-center">Aksi</th></tr>';

        $('#theadDataNilaiJuri').html(th);
    }

    function buildRows(categories, rows) {
        const headerCategories = categories || [];
        const tbody = [];
        
        rows.forEach(r => {
            let tds = `<td class="dt-left">${r.NoPeserta}</td>` +
                `<td class="dt-left">${r.NamaSantri}</td>` +
                `<td class="dt-left">${r.NamaTpq}</td>` +
                `<td class="dt-center">${r.TypeUjian}</td>`;
            
            // Buat array untuk menyimpan ID nilai per kategori untuk aksi edit
            const nilaiIds = [];
            
            headerCategories.forEach(cat => {
                const key = cat.id || cat.IdKategoriMateri || cat;
                let sc = r.nilai[key] || [];
                let nilaiId = r.nilaiIds && r.nilaiIds[key] ? r.nilaiIds[key][0] : null;
                
                const nilai = (sc[0] !== undefined && sc[0] !== null) ? sc[0] : 0;
                tds += `<td class="dt-center">${fmt(nilai)}</td>`;
                
                nilaiIds.push({
                    kategoriId: key,
                    kategoriName: cat.name || key,
                    nilaiId: nilaiId,
                    nilai: nilai
                });
            });
            
            // Kolom Aksi - tombol Edit untuk setiap kategori
            let aksiButtons = '';
            nilaiIds.forEach((item, idx) => {
                if (item.nilaiId) {
                    aksiButtons += `<button class="btn btn-sm btn-warning btn-edit-nilai" 
                        data-nilai-id="${item.nilaiId}" 
                        data-no-peserta="${r.NoPeserta}" 
                        data-nama-santri="${r.NamaSantri}" 
                        data-kategori-id="${item.kategoriId}" 
                        data-kategori-name="${item.kategoriName}" 
                        data-nilai="${item.nilai}" 
                        title="Edit ${item.kategoriName}">
                        <i class="fas fa-edit"></i> Edit ${item.kategoriName}
                    </button> `;
                }
            });
            
            tds += `<td class="dt-center">${aksiButtons || '-'}</td>`;
            tbody.push(`<tr>${tds}</tr>`);
        });
        
        $('#tbodyDataNilaiJuri').html(tbody.join(''));
    }

    function loadDataNilaiJuri() {
        // Ambil IdJuri - jika juri login, gunakan hidden field, jika admin gunakan select
        const idJuri = $('#hiddenJuriId').length > 0 ? $('#hiddenJuriId').val() : $('#filterJuri').val();
        const idTahunAjaran = $('#filterTahunAjaran').val().trim();
        // Ambil TypeUjian - jika juri login, gunakan hidden field, jika admin gunakan select
        const typeUjian = $('#hiddenTypeUjian').length > 0 ? $('#hiddenTypeUjian').val() : $('#filterTypeUjian').val();

        if (!idJuri) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih juri terlebih dahulu'
            });
            return;
        }

        if (!idTahunAjaran) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Tahun ajaran harus diisi'
            });
            return;
        }

        Swal.fire({
            title: 'Memuat...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= base_url("backend/munaqosah/get-data-nilai-juri") ?>',
            method: 'POST',
            data: {
                IdJuri: idJuri,
                IdTahunAjaran: idTahunAjaran,
                TypeUjian: typeUjian
            },
            success: function(resp) {
                Swal.close();
                if (!resp.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: resp.message || 'Gagal memuat data'
                    });
                    return;
                }

                currentData = resp.data;
                const headerCategories = currentData.categories || [];

                // Tampilkan info juri
                if (currentData.meta) {
                    $('#juriName').text(currentData.meta.UsernameJuri || '-');
                    $('#grupMateri').text(currentData.meta.IdGrupMateriUjian || '-');
                    $('#juriInfo').show();
                }

                // Hancurkan instance lama dan rebuild table
                if (dtInstance) {
                    dtInstance.destroy();
                    dtInstance = null;
                }

                $('#tblContainer').html(
                    '<table id="tblDataNilaiJuri" class="table table-bordered table-striped" style="width:100%">' +
                    '<thead id="theadDataNilaiJuri"></thead>' +
                    '<tbody id="tbodyDataNilaiJuri"></tbody>' +
                    '</table>'
                );

                buildHeader(headerCategories);
                buildRows(headerCategories, currentData.rows);

                // Inisialisasi DataTables
                dtInstance = $('#tblDataNilaiJuri').DataTable({
                    scrollX: true,
                    order: [[0, 'asc']],
                    pageLength: 25,
                    dom: 'Bfrtip',
                    buttons: ['excel', 'print']
                });

                // Attach event handler untuk tombol edit
                $('.btn-edit-nilai').off('click').on('click', function() {
                    const nilaiId = $(this).data('nilai-id');
                    const noPeserta = $(this).data('no-peserta');
                    const namaSantri = $(this).data('nama-santri');
                    
                    // Ambil data lengkap untuk mengetahui TypeUjian
                    let typeUjian = currentData.meta ? currentData.meta.TypeUjian : 'munaqosah';
                    const typeUjianDisplay = typeUjian === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';
                    
                    // Set nilai untuk modal verifikasi
                    $('#verifyNilaiId').val(nilaiId);
                    $('#verifyNoPeserta').val(noPeserta);
                    $('#verifyTypeUjian').val(typeUjian);
                    
                    // Set display Nama Santri dan No Peserta di modal verifikasi
                    $('#verifyNamaSantri').text(namaSantri || '-');
                    $('#verifyNoPesertaDisplay').text(noPeserta || '-');
                    
                    // Set display Type Ujian di modal verifikasi
                    $('#verifyTypeUjianDisplay').text(typeUjianDisplay);
                    if (typeUjian === 'pra-munaqosah') {
                        $('#verifyTypeUjianDisplay').removeClass('badge-primary').addClass('badge-warning');
                    } else {
                        $('#verifyTypeUjianDisplay').removeClass('badge-warning').addClass('badge-primary');
                    }
                    
                    // Set label berdasarkan typeUjian
                    if (typeUjian === 'pra-munaqosah') {
                        $('#verifyTypeUjianLabel').text('Pra-Munaqosah:');
                        $('#verifyRequiredRole').text('Operator');
                    } else {
                        $('#verifyTypeUjianLabel').text('Munaqosah:');
                        $('#verifyRequiredRole').text('Admin');
                    }
                    
                    // Reset form verifikasi
                    $('#verifyUsername').val('');
                    $('#verifyPassword').val('');
                    $('#verifyAlasanEdit').val('');
                    
                    // Tampilkan modal verifikasi
                    $('#modalVerifyEditNilai').modal('show');
                });

            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data'
                });
            }
        });
    }

    // Global variables untuk edit nilai
    let currentEditNilaiId = null;
    let currentEditAlasan = null;
    let currentEditData = null;
    let currentEditErrorCategories = {};

    const ERROR_AUTO_SHOW_THRESHOLD = 67;

    // Verifikasi Admin/Operator untuk edit nilai
    function verifyEditNilai() {
        const nilaiId = $('#verifyNilaiId').val();
        const username = $('#verifyUsername').val();
        const password = $('#verifyPassword').val();
        const alasanEdit = $('#verifyAlasanEdit').val();
        const typeUjian = $('#verifyTypeUjian').val();

        if (!username || !password) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Username dan Password harus diisi'
            });
            return;
        }

        if (!alasanEdit) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Alasan edit harus diisi'
            });
            return;
        }

        if (!nilaiId) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'ID nilai tidak ditemukan'
            });
            return;
        }

        Swal.fire({
            title: 'Memverifikasi...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= base_url("backend/munaqosah/verify-edit-nilai-credentials") ?>',
            method: 'POST',
            data: {
                username: username,
                password: password,
                typeUjian: typeUjian,
                idNilai: nilaiId // Kirim ID nilai untuk validasi IdTpq
            },
            success: function(resp) {
                Swal.close();
                if (resp.success) {
                    // Simpan alasan edit
                    currentEditNilaiId = nilaiId;
                    currentEditAlasan = alasanEdit;
                    
                    // Tutup modal verifikasi
                    $('#modalVerifyEditNilai').modal('hide');
                    
                    // Ambil data peserta untuk edit
                    loadPesertaForEditNilai(nilaiId);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: resp.status === 'AUTHORIZATION_ERROR' ? 'Otorisasi Error' : 'Autentikasi Error',
                        text: resp.message || 'Verifikasi gagal'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat verifikasi'
                });
            }
        });
    }

    // Load data peserta untuk edit nilai
    function loadPesertaForEditNilai(idNilai) {
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= base_url("backend/munaqosah/get-peserta-for-edit-nilai") ?>',
            method: 'POST',
            data: { idNilai: idNilai },
            success: function(resp) {
                Swal.close();
                if (resp.success) {
                    currentEditData = resp.data;
                    currentEditErrorCategories = resp.data.error_categories || {};
                    
                    // Tampilkan info peserta
                    showPesertaInfoEdit(resp.data.peserta, resp.data.juri);
                    
                    // Generate form edit nilai
                    generateFormEditNilai(resp.data.materi, resp.data.nilai_yang_ada);
                    
                    // Tampilkan modal form edit
                    $('#modalFormEditNilai').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.message || 'Gagal memuat data peserta'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data'
                });
            }
        });
    }

    // Tampilkan info peserta untuk edit
    function showPesertaInfoEdit(peserta, juri) {
        // Ambil Type Ujian dari data yang sudah dimuat
        const typeUjian = currentEditData ? (currentEditData.typeUjian || 'munaqosah') : 'munaqosah';
        const typeUjianDisplay = typeUjian === 'pra-munaqosah' ? 'Pra-Munaqosah' : 'Munaqosah';
        const typeUjianBadge = typeUjian === 'pra-munaqosah' ? 'badge-warning' : 'badge-primary';
        
        // Set Type Ujian di header modal
        $('#modalTypeUjianBadge').text(typeUjianDisplay).removeClass('badge-primary badge-warning').addClass(typeUjianBadge);
        
        const infoHtml = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Nama Santri:</strong> ${peserta.NamaSantri}<br>
                    <strong>No Peserta:</strong> ${peserta.NoPeserta}<br>
                    <strong>Type Ujian:</strong> <span class="badge ${typeUjianBadge}">${typeUjianDisplay}</span>
                </div>
                <div class="col-md-6">
                    <strong>Grup Materi:</strong> ${juri.NamaMateriGrup}<br>
                    ${juri.RoomId ? `<strong>Room:</strong> <span class="badge badge-info">${juri.RoomId}</span><br>` : ''}
                    <strong>Juri:</strong> ${juri.UsernameJuri}
                </div>
            </div>
        `;
        $('#pesertaInfoEdit').html(infoHtml);
        $('#infoPesertaEdit').show();
    }

    // Generate form edit nilai (mirip inputNilaiJuri step 2)
    function generateFormEditNilai(materiData, nilaiYangAda) {
        if (!materiData || materiData.length === 0) {
            $('#formNilaiEditContainer').html('<div class="alert alert-warning">Tidak ada materi yang tersedia</div>');
            return;
        }

        let formHtml = '<div class="row">';

        // Group materi by kategori
        const groupedMateri = {};
        materiData.forEach(materi => {
            const kategori = materi.KategoriMateriUjian || materi.NamaKategoriMateri || 'Umum';
            if (!groupedMateri[kategori]) {
                groupedMateri[kategori] = [];
            }
            groupedMateri[kategori].push(materi);
        });

        // Generate form for each kategori
        for (const kategori of Object.keys(groupedMateri)) {
            const errorCategories = getErrorCategoriesForKategoriEdit(kategori);

            formHtml += `
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">${kategori}</h5>
                        </div>
                        <div class="card-body">
            `;

            groupedMateri[kategori].forEach(materi => {
                // Ambil nilai yang sudah ada
                const nilaiAda = nilaiYangAda[materi.IdMateri] || {};
                const nilaiCurrent = nilaiAda.Nilai || '';
                const catatanCurrent = nilaiAda.Catatan || '';

                // Parse catatan (format: 1-3-4-8-10)
                const catatanArray = catatanCurrent ? catatanCurrent.split('-') : [];

                // Tambahkan info tambahan untuk materi Quran
                let additionalInfo = '';
                if (materi.WebLinkAyat) {
                    additionalInfo = `
                        <br>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="showAyatModalEdit('${materi.WebLinkAyat}', '${materi.NamaMateri}')">
                                <i class="fas fa-eye"></i> Lihat Ayat
                            </button>
                        </div>
                    `;
                }

                formHtml += `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nilai_${materi.IdMateri}">${materi.NamaMateri}</label>
                                <input type="number" 
                                    class="form-control nilai-input-edit" 
                                    id="edit_nilai_${materi.IdMateri}" 
                                    name="nilai[${materi.IdMateri}]"
                                    data-materi-id="${materi.IdMateri}"
                                    min="<?= $nilai_minimal ?? 40 ?>" 
                                    max="<?= $nilai_maximal ?? 99 ?>" 
                                    step="1"
                                    value="${nilaiCurrent}"
                                    oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);"
                                    required>
                                <small class="form-text text-muted">Range nilai: <?= $nilai_minimal ?? 40 ?> - <?= $nilai_maximal ?? 99 ?></small>
                                ${additionalInfo}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="mb-0">Kategori Kesalahan (Opsional)</label>
                                    <button type="button" 
                                        class="btn btn-sm btn-outline-secondary toggle-error-categories-edit" 
                                        data-materi-id="${materi.IdMateri}"
                                        aria-label="Tampilkan atau sembunyikan kategori kesalahan">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="error-categories-container d-none" data-materi-id="${materi.IdMateri}" data-visible="false" data-manual-hidden="false">
                                    ${errorCategories.length > 0 ? errorCategories.map((category, index) => {
                                        const isChecked = catatanArray.includes(category);
                                        return `
                                            <div class="form-check">
                                                <input class="form-check-input error-checkbox-edit" 
                                                    type="checkbox" 
                                                    value="${category}" 
                                                    id="edit_error_${materi.IdMateri}_${index}"
                                                    name="catatan[${materi.IdMateri}][]"
                                                    ${isChecked ? 'checked' : ''}>
                                                <label class="form-check-label" for="edit_error_${materi.IdMateri}_${index}">
                                                    ${category}
                                                </label>
                                            </div>
                                        `;
                                    }).join('') : '<p class="text-muted">Tidak ada kategori kesalahan tersedia</p>'}
                                </div>
                                <small class="form-text text-muted">Tampilkan kategori kesalahan (klik ikon mata) atau otomatis jika nilai < 67.</small>
                            </div>
                        </div>
                    </div>
                `;
            });

            formHtml += `
                        </div>
                    </div>
                </div>
            `;
        }

        formHtml += '</div>';
        $('#formNilaiEditContainer').html(formHtml);

        // Setup event listeners
        setupNilaiInputListenersEdit();
        setupErrorCategoryTogglesEdit();
        
        // Auto show error categories for low scores
        $('.nilai-input-edit').each(function() {
            handleAutoShowErrorCategoriesEdit($(this));
        });
    }

    // Function to get error categories for a kategori
    function getErrorCategoriesForKategoriEdit(kategori) {
        if (!kategori) return [];
        const categories = currentEditErrorCategories[kategori];
        return Array.isArray(categories) ? categories : [];
    }

    // Setup event listeners for nilai inputs (edit)
    function setupNilaiInputListenersEdit() {
        $('.nilai-input-edit').on('input', function() {
            handleAutoShowErrorCategoriesEdit($(this));
        });

        $('.nilai-input-edit').on('blur', function() {
            handleAutoShowErrorCategoriesEdit($(this));
        });
    }

    function setupErrorCategoryTogglesEdit() {
        $('.toggle-error-categories-edit').off('click').on('click', function() {
            const materiId = $(this).data('materi-id');
            toggleErrorCategoriesEdit(materiId, true);
        });
    }

    function handleAutoShowErrorCategoriesEdit($input) {
        const materiId = $input.data('materi-id');
        if (!materiId) return;

        const container = getErrorCategoryContainerEdit(materiId);
        if (!container.length) return;

        const rawValue = ($input.val() || '').toString().trim();
        const numericValue = parseFloat(rawValue);
        const hasTwoDigits = rawValue.length >= 2;
        const isValidNumber = !Number.isNaN(numericValue);
        const isLowScore = hasTwoDigits && isValidNumber && numericValue < ERROR_AUTO_SHOW_THRESHOLD;
        const shouldHide = hasTwoDigits && isValidNumber && numericValue >= ERROR_AUTO_SHOW_THRESHOLD;

        if (isLowScore) {
            showErrorCategoriesEdit(materiId, false);
            container.attr('data-manual-hidden', 'false');
        } else if (shouldHide || rawValue === '') {
            hideErrorCategoriesEdit(materiId, false);
            container.attr('data-manual-hidden', 'false');
        }
    }

    function toggleErrorCategoriesEdit(materiId, triggeredByManual = false) {
        const container = getErrorCategoryContainerEdit(materiId);
        if (!container.length) return;

        const isVisible = container.attr('data-visible') === 'true';
        if (isVisible) {
            hideErrorCategoriesEdit(materiId, triggeredByManual);
        } else {
            showErrorCategoriesEdit(materiId, triggeredByManual);
        }
    }

    function showErrorCategoriesEdit(materiId, triggeredByManual = false) {
        const container = getErrorCategoryContainerEdit(materiId);
        if (!container.length) return;

        container.removeClass('d-none').attr('data-visible', 'true');
        container.attr('data-manual-hidden', 'false');
        updateErrorToggleStateEdit(materiId, true);
    }

    function hideErrorCategoriesEdit(materiId, triggeredByManual = false) {
        const container = getErrorCategoryContainerEdit(materiId);
        if (!container.length) return;

        container.addClass('d-none').attr('data-visible', 'false');
        container.attr('data-manual-hidden', triggeredByManual ? 'true' : 'false');
        updateErrorToggleStateEdit(materiId, false);
    }

    function updateErrorToggleStateEdit(materiId, isVisible) {
        const toggleBtn = $(`.toggle-error-categories-edit[data-materi-id="${materiId}"]`);
        const icon = toggleBtn.find('i');

        if (isVisible) {
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
            toggleBtn.attr('aria-pressed', 'true');
        } else {
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
            toggleBtn.attr('aria-pressed', 'false');
        }
    }

    function getErrorCategoryContainerEdit(materiId) {
        return $(`.error-categories-container[data-materi-id="${materiId}"]`);
    }

    // Save edit nilai dengan alasan
    function saveEditNilaiWithReason() {
        if (!currentEditNilaiId || !currentEditAlasan) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Data verifikasi tidak lengkap'
            });
            return;
        }

        // Get nilai range from PHP
        const nilaiMinimal = <?= $nilai_minimal ?? 40 ?>;
        const nilaiMaximal = <?= $nilai_maximal ?? 99 ?>;

        // Validate all inputs
        const nilaiInputs = $('.nilai-input-edit');
        let isValid = true;
        let errorMessage = '';

        nilaiInputs.each(function() {
            const value = parseFloat($(this).val());
            if ($(this).val().trim() !== '') {
                if (isNaN(value) || value < nilaiMinimal || value > nilaiMaximal) {
                    isValid = false;
                    errorMessage = `Semua nilai harus dalam range ${nilaiMinimal}-${nilaiMaximal}`;
                    return false;
                }
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Error',
                text: errorMessage
            });
            return;
        }

        // Collect form data
        const formData = {
            idNilai: currentEditNilaiId,
            alasanEdit: currentEditAlasan,
            nilai: {},
            catatan: {}
        };

        nilaiInputs.each(function() {
            const materiId = $(this).attr('name').replace('nilai[', '').replace(']', '');
            const nilai = $(this).val().trim();
            if (nilai !== '') {
                formData.nilai[materiId] = parseFloat(nilai);
            }
        });

        // Collect error categories data
        $('.error-categories-container').each(function() {
            const materiId = $(this).data('materi-id');
            const selectedErrors = [];

            $(this).find('.error-checkbox-edit:checked').each(function() {
                selectedErrors.push($(this).val());
            });

            if (selectedErrors.length > 0) {
                formData.catatan[materiId] = selectedErrors.join('-');
            }
        });

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= base_url("backend/munaqosah/update-nilai-with-reason") ?>',
            method: 'POST',
            data: formData,
            success: function(resp) {
                Swal.close();
                if (resp.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: resp.message || 'Nilai berhasil diperbarui'
                    }).then(() => {
                        $('#modalFormEditNilai').modal('hide');
                        loadDataNilaiJuri(); // Reload data
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: resp.message || 'Gagal memperbarui nilai'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan'
                });
            }
        });
    }

    // Fungsi untuk menampilkan ayat (reuse dari inputNilaiJuri)
    let currentAyatUrlEdit = '';

    function showAyatModalEdit(url, title) {
        currentAyatUrlEdit = url;
        $('#ayatTitleEdit').text(title);
        $('#iframeAyatEdit').attr('src', '');
        
        // Tampilkan section ayat di dalam modal
        $('#ayatSectionEdit').slideDown(300);
        
        setTimeout(function() {
            $('#iframeAyatEdit').attr('src', url);
            
            // Scroll ke section ayat dalam modal setelah section muncul
            const modalBody = $('#modalFormEditNilai .modal-body');
            const ayatSection = $('#ayatSectionEdit');
            if (modalBody.length && ayatSection.length) {
                const ayatOffset = ayatSection.offset().top;
                const modalBodyOffset = modalBody.offset().top;
                const modalBodyScrollTop = modalBody.scrollTop();
                const targetScroll = modalBodyScrollTop + (ayatOffset - modalBodyOffset) - 20;
                
                modalBody.animate({
                    scrollTop: targetScroll
                }, 500);
            }
        }, 350);
    }

    function hideAyatSectionEdit() {
        $('#ayatSectionEdit').slideUp(300, function() {
            $('#iframeAyatEdit').attr('src', '');
        });
    }

    function openInNewTabEdit() {
        if (currentAyatUrlEdit) {
            window.open(currentAyatUrlEdit, '_blank');
        }
    }

    $(function() {
        // Jika juri login dan data sudah tersedia, auto load
        <?php if ($isJuri && $currentJuriId && $currentTypeUjian): ?>
            // Auto load data untuk juri
            loadDataNilaiJuri();
        <?php endif; ?>
        
        $('#btnReload').on('click', loadDataNilaiJuri);
        $('#filterTypeUjian').on('change', loadDataNilaiJuri);
        
        // Handler untuk verifikasi edit nilai
        $('#btnVerifyEdit').on('click', verifyEditNilai);
        
        // Handler untuk save edit nilai
        $('#btnSaveEditNilai').on('click', saveEditNilaiWithReason);
        
        // Jika admin, enable filter juri change
        <?php if (!$isJuri): ?>
            $('#filterJuri').on('change', function() {
                if ($(this).val()) {
                    loadDataNilaiJuri();
                }
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection(); ?>

