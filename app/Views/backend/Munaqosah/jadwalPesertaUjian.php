<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Jadwal Peserta Ujian Munaqosah</h3>
                        <div class="d-flex">
                            <div class="mr-2">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <input type="text" id="filterTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>">
                            </div>
                            <div class="mr-2">
                                <label class="mb-0 small">Type Ujian</label>
                                <select id="filterTypeUjian" class="form-control form-control-sm">
                                    <?php if ($isAdmin): ?>
                                        <option value="munaqosah">Munaqosah</option>
                                    <?php endif; ?>
                                    <option value="pra-munaqosah">Pra-Munaqosah</option>
                                </select>
                            </div>
                            <div class="align-self-end">
                                <button id="btnReload" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
                                <button id="btnPrintPdf" class="btn btn-sm btn-danger ml-2"><i class="fas fa-file-pdf"></i> Print PDF</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Form Input Jadwal -->
                        <div class="row mb-3" style="background-color: #ffc107; padding: 10px; border-radius: 5px;">
                            <div class="col-md-1">
                                <label class="mb-0 small">TahunAjaran</label>
                                <input type="text" id="inputTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="mb-0 small">TypeUjian</label>
                                <select id="inputTypeUjian" class="form-control form-control-sm" <?= !$isAdmin ? 'disabled' : '' ?>>
                                    <?php if ($isAdmin): ?>
                                        <option value="munaqosah" selected>Munaqosah</option>
                                        <option value="pra-munaqosah">Pra-Munaqosah</option>
                                    <?php else: ?>
                                        <option value="pra-munaqosah" selected>Pra-Munaqosah</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="mb-0 small">Tanggal</label>
                                <input type="date" id="inputTanggal" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-1">
                                <label class="mb-0 small">Jam</label>
                                <input type="time" id="inputJam" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="mb-0 small">IdTpq-NamaTpq</label>
                                <select id="inputIdTpq" class="form-control form-control-sm">
                                    <option value="">Pilih TPQ</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="mb-0 small">Group</label>
                                <select id="inputGroupPeserta" class="form-control form-control-sm">
                                    <?php
                                    $groupStart = isset($groupStart) ? $groupStart : 1;
                                    $groupEnd = isset($groupEnd) ? $groupEnd : 8;
                                    for ($i = $groupStart; $i <= $groupEnd; $i++): ?>
                                        <option value="Group <?= $i ?>">Group <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="align-self-end ml-2">
                                <button id="btnAddJadwal" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add</button>
                            </div>
                        </div>

                        <!-- Tabel Jadwal -->
                        <div class="table-responsive">
                            <table id="tblJadwal" class="table table-bordered" style="width:100%">
                                <thead style="background-color: #28a745; color: white;">
                                    <tr>
                                        <th style="text-align: center;">Group</th>
                                        <th style="text-align: center;">Tanggal</th>
                                        <th style="text-align: center;">Waktu</th>
                                        <th style="text-align: left;">Nama TPQ</th>
                                        <th style="text-align: left;">Desa/Kelurahan</th>
                                        <th style="text-align: center;">Jumlah</th>
                                        <th style="text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyJadwal">
                                    <!-- Data akan diisi via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="modalEditJadwal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jadwal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditJadwal">
                    <input type="hidden" id="editId">
                    <div class="form-group">
                        <label>Group</label>
                        <select id="editGroupPeserta" class="form-control">
                            <?php
                            $groupStart = isset($groupStart) ? $groupStart : 1;
                            $groupEnd = isset($groupEnd) ? $groupEnd : 8;
                            for ($i = $groupStart; $i <= $groupEnd; $i++): ?>
                                <option value="Group <?= $i ?>">Group <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" id="editTanggal" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Jam</label>
                        <input type="time" id="editJam" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>TPQ</label>
                        <select id="editIdTpq" class="form-control">
                            <option value="">Pilih TPQ</option>
                            <?php if (!empty($tpqDropdown)) : foreach ($tpqDropdown as $tpq): ?>
                                    <option value="<?= esc($tpq['IdTpq']) ?>"><?= esc($tpq['NamaTpq']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <?php if ($isAdmin): ?>
                        <div class="form-group">
                            <label>Type Ujian</label>
                            <select id="editTypeUjian" class="form-control">
                                <option value="pra-munaqosah">Pra-Munaqosah</option>
                                <option value="munaqosah">Munaqosah</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUpdateJadwal">Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        const currentTahunAjaran = '<?= esc($current_tahun_ajaran) ?>';
        const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;

        // Konfigurasi grup dari database (default: start=1, end=8)
        const groupStart = <?= isset($groupStart) ? (int)$groupStart : 1 ?>;
        const groupEnd = <?= isset($groupEnd) ? (int)$groupEnd : 8 ?>;

        // Generate array grup berdasarkan konfigurasi
        const allGroups = [];
        for (let i = groupStart; i <= groupEnd; i++) {
            allGroups.push(`Group ${i}`);
        }

        // Variabel global untuk menyimpan data jadwal (untuk optimasi, tidak perlu request ke server setiap kali)
        let jadwalDataCache = [];

        // Load tanggal terakhir dari localStorage
        const lastTanggal = localStorage.getItem('jadwalLastTanggal');
        if (lastTanggal) {
            $('#inputTanggal').val(lastTanggal);
        }

        // Load jam terakhir dari localStorage
        const lastJam = localStorage.getItem('jadwalLastJam');
        if (lastJam) {
            $('#inputJam').val(lastJam);
        }

        // Load TPQ dari peserta saat filter berubah
        function loadTpqFromPeserta() {
            const tahunAjaran = $('#filterTahunAjaran').val() || currentTahunAjaran;

            $.get('<?= base_url("backend/munaqosah/get-tpq-from-peserta") ?>', {
                tahunAjaran: tahunAjaran
            }, function(response) {
                if (response.success) {
                    const select = $('#inputIdTpq');
                    select.empty().append('<option value="">Pilih TPQ</option>');
                    response.data.forEach(function(item) {
                        select.append(`<option value="${item.IdTpq}" data-nama="${item.NamaTpq}">${item.IdTpq}-${item.NamaTpq}</option>`);
                    });
                }
            }, 'json');
        }

        // Fungsi helper untuk menghitung grup tersedia berdasarkan data lokal
        function calculateAvailableGroups(jadwalData, tanggal, typeUjian) {
            // Menggunakan allGroups yang sudah didefinisikan di scope global

            // Filter data berdasarkan typeUjian
            let filteredData = jadwalData;
            if (typeUjian) {
                filteredData = jadwalData.filter(function(group) {
                    return group.rows && group.rows.length > 0 && group.rows[0].TypeUjian === typeUjian;
                });
            }

            // Ambil semua grup yang sudah digunakan di semua tanggal
            const usedGroupsAll = [];
            filteredData.forEach(function(group) {
                if (group.rows && group.rows.length > 0) {
                    group.rows.forEach(function(row) {
                        if (row.GroupPeserta && !usedGroupsAll.includes(row.GroupPeserta)) {
                            usedGroupsAll.push(row.GroupPeserta);
                        }
                    });
                }
            });

            // Grup yang belum digunakan di semua tanggal
            const unusedGroupsAll = allGroups.filter(function(group) {
                return !usedGroupsAll.includes(group);
            });

            // Jika tanggal tidak dipilih, return semua grup
            if (!tanggal) {
                return {
                    availableGroups: allGroups,
                    groupsInTanggal: [],
                    usedGroupsAll: usedGroupsAll
                };
            }

            // Cari grup yang ada di tanggal tersebut
            const groupsInTanggal = [];
            filteredData.forEach(function(group) {
                if (group.Tanggal === tanggal && group.rows && group.rows.length > 0) {
                    group.rows.forEach(function(row) {
                        if (row.GroupPeserta && !groupsInTanggal.includes(row.GroupPeserta)) {
                            groupsInTanggal.push(row.GroupPeserta);
                        }
                    });
                }
            });

            // Jika tanggal sudah ada di tabel
            if (groupsInTanggal.length > 0) {
                // Tampilkan grup yang ada di tanggal tersebut + grup yang belum digunakan di semua tanggal
                const availableGroups = [...groupsInTanggal, ...unusedGroupsAll];
                // Sort untuk konsistensi
                availableGroups.sort();

                return {
                    availableGroups: availableGroups,
                    groupsInTanggal: groupsInTanggal,
                    usedGroupsAll: usedGroupsAll,
                    tanggalExists: true
                };
            } else {
                // Tanggal belum ada, hanya tampilkan grup yang belum digunakan di semua tanggal
                return {
                    availableGroups: unusedGroupsAll,
                    groupsInTanggal: [],
                    usedGroupsAll: usedGroupsAll,
                    tanggalExists: false
                };
            }
        }

        // Load grup yang tersedia berdasarkan tanggal (menggunakan data lokal, tidak request ke server)
        function loadAvailableGroups() {
            const tanggal = $('#inputTanggal').val();
            const typeUjian = $('#inputTypeUjian').val();

            const select = $('#inputGroupPeserta');
            const currentValue = select.val();
            select.empty();

            // Jika data jadwal belum ada di cache, tampilkan semua grup (fallback)
            if (!jadwalDataCache || jadwalDataCache.length === 0) {
                allGroups.forEach(function(group) {
                    select.append(`<option value="${group}">${group}</option>`);
                });
                // Coba load jadwal jika belum ada
                if ($('#filterTahunAjaran').val() || currentTahunAjaran) {
                    loadJadwal();
                }
                return;
            }

            // Hitung grup tersedia berdasarkan data lokal
            const result = calculateAvailableGroups(jadwalDataCache, tanggal, typeUjian);

            // Tampilkan grup yang tersedia
            if (result.availableGroups && result.availableGroups.length > 0) {
                result.availableGroups.forEach(function(group) {
                    select.append(`<option value="${group}">${group}</option>`);
                });
            } else {
                // Jika tidak ada grup tersedia, tampilkan semua grup (fallback)
                allGroups.forEach(function(group) {
                    select.append(`<option value="${group}">${group}</option>`);
                });
            }

            // Jika nilai sebelumnya masih ada di dropdown, set kembali
            if (currentValue && select.find(`option[value="${currentValue}"]`).length > 0) {
                select.val(currentValue);
            } else {
                // Set ke grup pertama yang tersedia
                const firstOption = select.find('option:first').val();
                if (firstOption) {
                    select.val(firstOption);
                }
            }
        }

        // Load jadwal
        function loadJadwal() {
            const tahunAjaran = $('#filterTahunAjaran').val() || currentTahunAjaran;
            const typeUjian = $('#filterTypeUjian').val();

            Swal.fire({
                title: 'Memuat...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.get('<?= base_url("backend/munaqosah/get-jadwal-peserta-ujian") ?>', {
                tahunAjaran: tahunAjaran,
                typeUjian: typeUjian
            }, function(response) {
                Swal.close();
                if (response.success) {
                    // Simpan data jadwal ke cache untuk optimasi
                    jadwalDataCache = response.data || [];

                    // Render tabel
                    renderTable(response.data, response.grandTotal || 0);

                    // Update grup yang tersedia setelah data di-load
                    loadAvailableGroups();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat jadwal'
                    });
                }
            }, 'json').fail(function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat jadwal'
                });
            });
        }

        // Render tabel dengan grouping, Sub, Sub Total, dan Grand Total
        function renderTable(data, grandTotal) {
            const tbody = $('#tbodyJadwal');
            tbody.empty();

            if (!data || data.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
                return;
            }

            let prevTanggal = null;
            let prevJam = null;
            let prevJamTime = null;
            let subtotalPagi = 0; // Subtotal untuk jam < 13:00
            let subtotalSiang = 0; // Subtotal untuk jam >= 13:00
            let subtotalPerTanggal = 0; // Subtotal untuk satu tanggal
            let allSubTotal = []; // Array untuk menyimpan Sub Total per tanggal
            let grandTotalCalc = 0;

            data.forEach(function(group, groupIndex) {
                if (!group.rows || group.rows.length === 0) return;

                const currentTanggal = group.Tanggal;
                const currentJam = group.Jam;
                const currentJamTime = currentJam ? parseInt(currentJam.split(':')[0]) : 0;

                // Jika tanggal berubah, hitung Sub Total untuk tanggal sebelumnya
                if (prevTanggal !== null && prevTanggal !== currentTanggal) {
                    // Tambahkan Sub untuk jam < 13:00 jika ada
                    if (subtotalPagi > 0) {
                        tbody.append(`<tr style="background-color: #ffc107;">
                        <td colspan="5" style="text-align: right; font-weight: bold;">Sub</td>
                        <td style="text-align: center; font-weight: bold;">${subtotalPagi}</td>
                        <td></td>
                    </tr>`);
                        subtotalPerTanggal += subtotalPagi;
                    }
                    // Tambahkan Sub untuk jam >= 13:00 jika ada
                    if (subtotalSiang > 0) {
                        tbody.append(`<tr style="background-color: #ffc107;">
                        <td colspan="5" style="text-align: right; font-weight: bold;">Sub</td>
                        <td style="text-align: center; font-weight: bold;">${subtotalSiang}</td>
                        <td></td>
                    </tr>`);
                        subtotalPerTanggal += subtotalSiang;
                    }
                    // Tambahkan Sub Total untuk tanggal sebelumnya
                    if (subtotalPerTanggal > 0) {
                        tbody.append(`<tr style="background-color: #90EE90;">
                        <td colspan="5" style="text-align: right; font-weight: bold;">Sub Total</td>
                        <td style="text-align: center; font-weight: bold;">${subtotalPerTanggal}</td>
                        <td></td>
                    </tr>`);
                        allSubTotal.push(subtotalPerTanggal);
                        grandTotalCalc += subtotalPerTanggal;
                    }
                    // Reset untuk tanggal baru
                    subtotalPagi = 0;
                    subtotalSiang = 0;
                    subtotalPerTanggal = 0;
                }

                // Jika jam berubah dalam tanggal yang sama
                if (prevTanggal === currentTanggal && prevJam !== currentJam) {
                    // Jika jam sebelumnya < 13:00 dan sekarang >= 13:00, atau sebaliknya
                    if (prevJamTime !== null) {
                        if (prevJamTime < 13 && currentJamTime >= 13) {
                            // Akhiri Sub untuk pagi
                            if (subtotalPagi > 0) {
                                tbody.append(`<tr style="background-color: #ffc107;">
                                <td colspan="5" style="text-align: right; font-weight: bold;">Sub</td>
                                <td style="text-align: center; font-weight: bold;">${subtotalPagi}</td>
                                <td></td>
                            </tr>`);
                                subtotalPerTanggal += subtotalPagi;
                                subtotalPagi = 0;
                            }
                        } else if (prevJamTime >= 13 && currentJamTime < 13) {
                            // Akhiri Sub untuk siang
                            if (subtotalSiang > 0) {
                                tbody.append(`<tr style="background-color: #ffc107;">
                                <td colspan="5" style="text-align: right; font-weight: bold;">Sub</td>
                                <td style="text-align: center; font-weight: bold;">${subtotalSiang}</td>
                                <td></td>
                            </tr>`);
                                subtotalPerTanggal += subtotalSiang;
                                subtotalSiang = 0;
                            }
                        }
                    }
                }

                // Format tanggal
                const tanggalObj = new Date(currentTanggal + 'T00:00:00');
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                const tanggalFormatted = tanggalObj.toLocaleDateString('id-ID', options);

                // Format jam
                const jamFormatted = currentJam ? `Jam ${currentJam} s/d Selesai` : '-';

                // Render rows untuk group ini
                group.rows.forEach(function(row, rowIndex) {
                    let html = '';

                    if (rowIndex === 0) {
                        // Row pertama dengan rowspan
                        html = `<tr>
                        <td rowspan="${group.rows.length}" style="text-align: center; vertical-align: middle;">${row.GroupPeserta}</td>
                        <td rowspan="${group.rows.length}" style="text-align: center; vertical-align: middle;">${tanggalFormatted}</td>
                        <td rowspan="${group.rows.length}" style="text-align: center; vertical-align: middle;">${jamFormatted}</td>
                        <td style="text-align: left;">${row.NamaTpq || '-'}</td>
                        <td style="text-align: left;">${row.KelurahanDesa || '-'}</td>
                        <td style="text-align: center;">${row.Jumlah || 0}</td>
                        <td style="text-align: center;">
                            <button class="btn btn-sm btn-warning btn-edit" data-id="${row.id}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-namatpq="${row.NamaTpq || ''}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>`;
                    } else {
                        // Row berikutnya tanpa kolom group, tanggal, jam
                        html = `<tr>
                        <td style="text-align: left;">${row.NamaTpq || '-'}</td>
                        <td style="text-align: left;">${row.KelurahanDesa || '-'}</td>
                        <td style="text-align: center;">${row.Jumlah || 0}</td>
                        <td style="text-align: center;">
                            <button class="btn btn-sm btn-warning btn-edit" data-id="${row.id}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" data-namatpq="${row.NamaTpq || ''}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>`;
                    }

                    tbody.append(html);

                    // Tambahkan ke subtotal berdasarkan jam
                    const jumlah = parseInt(row.Jumlah || 0);
                    if (currentJamTime < 13) {
                        subtotalPagi += jumlah;
                    } else {
                        subtotalSiang += jumlah;
                    }
                });

                prevTanggal = currentTanggal;
                prevJam = currentJam;
                prevJamTime = currentJamTime;
            });

            // Tambahkan Sub untuk tanggal terakhir
            if (data.length > 0) {
                // Tambahkan Sub untuk jam < 13:00 jika ada
                if (subtotalPagi > 0) {
                    tbody.append(`<tr style="background-color: #ffc107;">
                    <td colspan="5" style="text-align: right; font-weight: bold;">Sub</td>
                    <td style="text-align: center; font-weight: bold;">${subtotalPagi}</td>
                    <td></td>
                </tr>`);
                    subtotalPerTanggal += subtotalPagi;
                }
                // Tambahkan Sub untuk jam >= 13:00 jika ada
                if (subtotalSiang > 0) {
                    tbody.append(`<tr style="background-color: #ffc107;">
                    <td colspan="5" style="text-align: right; font-weight: bold;">Sub</td>
                    <td style="text-align: center; font-weight: bold;">${subtotalSiang}</td>
                    <td></td>
                </tr>`);
                    subtotalPerTanggal += subtotalSiang;
                }
                // Tambahkan Sub Total untuk tanggal terakhir
                if (subtotalPerTanggal > 0) {
                    tbody.append(`<tr style="background-color: #90EE90;">
                    <td colspan="5" style="text-align: right; font-weight: bold;">Sub Total</td>
                    <td style="text-align: center; font-weight: bold;">${subtotalPerTanggal}</td>
                    <td></td>
                </tr>`);
                    allSubTotal.push(subtotalPerTanggal);
                    grandTotalCalc += subtotalPerTanggal;
                }
            }

            // Tambahkan Grand Total
            tbody.append(`<tr style="background-color:rgb(244, 247, 244);">
            <td colspan="5" style="text-align: right; font-weight: bold;">Grand Total</td>
            <td style="text-align: center; font-weight: bold;">${grandTotalCalc}</td>
            <td></td>
        </tr>`);
        }

        // Add jadwal
        $('#btnAddJadwal').click(function() {
            const data = {
                GroupPeserta: $('#inputGroupPeserta').val(),
                Tanggal: $('#inputTanggal').val(),
                Jam: $('#inputJam').val(),
                IdTpq: $('#inputIdTpq').val(),
                IdTahunAjaran: $('#inputTahunAjaran').val(),
                TypeUjian: $('#inputTypeUjian').val()
            };

            if (!data.Tanggal || !data.Jam || !data.IdTpq) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Mohon lengkapi semua field yang wajib diisi'
                });
                return;
            }

            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.post('<?= base_url("backend/munaqosah/save-jadwal-peserta-ujian") ?>', data, function(response) {
                Swal.close();
                if (response.success) {
                    // Simpan tanggal dan jam terakhir ke localStorage
                    localStorage.setItem('jadwalLastTanggal', data.Tanggal);
                    localStorage.setItem('jadwalLastJam', data.Jam);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Jadwal berhasil disimpan'
                    }).then(() => {
                        // Reset form kecuali tanggal dan jam
                        $('#inputIdTpq').val('');
                        // Reload TPQ dropdown untuk update list
                        loadTpqFromPeserta();
                        // Reload jadwal (akan update cache dan grup secara otomatis)
                        loadJadwal();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal menyimpan jadwal'
                    });
                }
            }, 'json').fail(function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menyimpan jadwal'
                });
            });
        });

        // Edit jadwal
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');

            // Ambil data jadwal
            $.get('<?= base_url("backend/munaqosah/get-jadwal-peserta-ujian") ?>', {
                tahunAjaran: $('#filterTahunAjaran').val() || currentTahunAjaran,
                typeUjian: $('#filterTypeUjian').val()
            }, function(response) {
                if (response.success) {
                    let jadwalData = null;
                    response.data.forEach(function(group) {
                        if (group.rows) {
                            group.rows.forEach(function(row) {
                                if (row.id == id) {
                                    jadwalData = row;
                                }
                            });
                        }
                    });

                    if (jadwalData) {
                        $('#editId').val(jadwalData.id);
                        $('#editGroupPeserta').val(jadwalData.GroupPeserta);
                        $('#editTanggal').val(jadwalData.Tanggal);
                        $('#editJam').val(jadwalData.Jam);
                        $('#editIdTpq').val(jadwalData.IdTpq);
                        if (isAdmin) {
                            $('#editTypeUjian').val(jadwalData.TypeUjian);
                        }
                        $('#modalEditJadwal').modal('show');
                    }
                }
            }, 'json');
        });

        // Update jadwal
        $('#btnUpdateJadwal').click(function() {
            const id = $('#editId').val();
            const data = {
                GroupPeserta: $('#editGroupPeserta').val(),
                Tanggal: $('#editTanggal').val(),
                Jam: $('#editJam').val(),
                IdTpq: $('#editIdTpq').val()
            };

            if (isAdmin) {
                data.TypeUjian = $('#editTypeUjian').val();
            }

            Swal.fire({
                title: 'Mengupdate...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.post(`<?= base_url("backend/munaqosah/update-jadwal-peserta-ujian") ?>/${id}`, data, function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Jadwal berhasil diupdate'
                    }).then(() => {
                        $('#modalEditJadwal').modal('hide');
                        // Reload jadwal (akan update cache dan grup secara otomatis)
                        loadJadwal();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal mengupdate jadwal'
                    });
                }
            }, 'json').fail(function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengupdate jadwal'
                });
            });
        });

        // Delete jadwal
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const namaTpq = $(this).data('namatpq') || 'jadwal ini';

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Hapus jadwal untuk TPQ ${namaTpq}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.get(`<?= base_url("backend/munaqosah/delete-jadwal-peserta-ujian") ?>/${id}`, function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Jadwal berhasil dihapus'
                            }).then(() => {
                                // Reload TPQ dropdown untuk update list (TPQ yang dihapus bisa muncul lagi)
                                loadTpqFromPeserta();
                                // Reload jadwal (akan update cache dan grup secara otomatis)
                                loadJadwal();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal menghapus jadwal'
                            });
                        }
                    }, 'json').fail(function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus jadwal'
                        });
                    });
                }
            });
        });

        // Print PDF
        $('#btnPrintPdf').click(function() {
            const tahunAjaran = $('#filterTahunAjaran').val() || currentTahunAjaran;
            const typeUjian = $('#filterTypeUjian').val();

            const url = '<?= base_url("backend/munaqosah/print-jadwal-peserta") ?>' +
                '?tahunAjaran=' + encodeURIComponent(tahunAjaran) +
                '&typeUjian=' + encodeURIComponent(typeUjian);

            // Buka di tab baru untuk print PDF
            window.open(url, '_blank');
        });

        // Event handlers
        $('#btnReload').click(function() {
            loadTpqFromPeserta();
            loadJadwal();
        });

        $('#filterTypeUjian').change(function() {
            loadTpqFromPeserta();
            loadJadwal();
        });

        // Update TPQ dropdown saat tahun ajaran berubah
        $('#filterTahunAjaran').change(function() {
            loadTpqFromPeserta();
            loadJadwal();
        });

        // Event handler untuk dropdown Group - cek tanggal saat diklik
        $('#inputGroupPeserta').on('focus click', function() {
            loadAvailableGroups();
        });

        // Event handler untuk input Tanggal - update grup saat tanggal berubah
        $('#inputTanggal').change(function() {
            loadAvailableGroups();
        });

        // Event handler untuk TypeUjian - update grup saat type ujian berubah
        $('#inputTypeUjian').change(function() {
            loadAvailableGroups();
        });

        // Load initial data
        loadTpqFromPeserta();
        loadJadwal();
        loadAvailableGroups(); // Load grup saat pertama kali
    });
</script>
<?= $this->endSection(); ?>