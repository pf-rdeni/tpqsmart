<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div>
    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Proses Peserta Munaqosah ke Tabel Nilai</h3>
                        </div>
                        <div class="card-body">
                            <form id="registrasiForm" method="POST" action="<?= base_url('backend/munaqosah/processRegistrasiPeserta') ?>">
                                <div class="row">
                                    <!-- Filter TPQ -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="filterTpq">Filter TPQ:</label>
                                            <select class="form-control" id="filterTpq" name="filterTpq">
                                                <option value="0">Semua TPQ</option>
                                                <?php foreach ($tpq as $t): ?>
                                                    <option value="<?= $t['IdTpq'] ?>" <?= (session()->get('IdTpq') == $t['IdTpq']) ? 'selected' : '' ?>>
                                                        <?= $t['NamaTpq'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Filter Kelas -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="filterKelas">Filter Kelas:</label>
                                            <select class="form-control" id="filterKelas" name="filterKelas">
                                                <option value="0">Semua Kelas</option>
                                                <?php foreach ($kelas as $k): ?>
                                                    <option value="<?= $k['IdKelas'] ?>"><?= $k['NamaKelas'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Tahun Ajaran -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tahunAjaran">Tahun Ajaran:</label>
                                            <input type="text" class="form-control" id="tahunAjaran" name="tahunAjaran" 
                                                   value="<?= $tahunAjaran ?>" readonly>
                                        </div>
                                    </div>
                                </div>


                                <!-- Daftar Santri -->
                                <div class="row" id="daftarSantriContainer">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Daftar Peserta Munaqosah:</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="santriTable">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%" class="text-center">
                                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                                            </th>
                                                            <th width="10%">ID Santri</th>
                                                            <th width="25%">Nama Santri</th>
                                                            <th width="15%">Kelas</th>
                                                            <th width="20%">TPQ</th>
                                                            <th width="15%">Status Data Nilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="santriTableBody">
                                                        <!-- Data akan diisi via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success" id="processBtn" disabled>
                                            <i class="fas fa-save"></i> Proses ke Tabel Nilai
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>

/* Checkbox alignment fixes */
#santriTable th:first-child,
#santriTable td:first-child {
    text-align: center;
    vertical-align: middle;
    padding: 0.5rem 0.25rem;
}

#santriTable th:first-child {
    background-color: transparent !important;
    color: inherit !important;
}

#selectAll {
    margin: 0;
    transform: scale(1.1);
}

.santri-checkbox {
    margin: 0;
    transform: scale(1.1);
}

/* Ensure consistent row height */
#santriTable tbody tr {
    height: 40px;
}

#santriTable tbody td {
    vertical-align: middle;
    padding: 0.5rem 0.25rem;
}
</style>
<script>
$(document).ready(function() {
    let selectedSantri = [];

    // Load santri data when page loads
    loadSantriData();

    // Filter change events
    $('#filterTpq, #filterKelas').on('change', function() {
        loadSantriData();
    });


    // Select all checkbox
    $('#selectAll').on('change', function() {
        if ($(this).is(':checked')) {
            selectAllSantri();
        } else {
            clearSelection();
        }
    });

    // Individual santri selection
    $(document).on('change', '.santri-checkbox', function() {
        const santriId = $(this).val();
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            if (!selectedSantri.includes(santriId)) {
                selectedSantri.push(santriId);
            }
        } else {
            selectedSantri = selectedSantri.filter(id => id !== santriId);
            $('#selectAll').prop('checked', false);
        }
        
        updateProcessButton();
    });


    function loadSantriData() {
        const filterTpq = $('#filterTpq').val();
        const filterKelas = $('#filterKelas').val();
        
        $.ajax({
            url: '<?= base_url('backend/munaqosah/getSantriForRegistrasi') ?>',
            type: 'GET',
            data: {
                filterTpq: filterTpq,
                filterKelas: filterKelas
            },
            success: function(response) {
                populateSantriTable(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading santri data:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data!',
                    text: 'Tidak dapat memuat data santri. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function populateSantriTable(santriData) {
        const tbody = $('#santriTableBody');
        tbody.empty();
        
        santriData.forEach(function(santri) {
            const isPeserta = santri.isPeserta ? 'Sudah Ada Data Nilai' : 'Belum Ada Data Nilai';
            const statusClass = santri.isPeserta ? 'badge-danger' : 'badge-success';
            const disabledAttr = santri.isPeserta ? 'disabled' : '';
            
            const row = `
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input santri-checkbox" 
                               value="${santri.IdSantri}" ${disabledAttr}>
                    </td>
                    <td>${santri.IdSantri}</td>
                    <td>${santri.NamaSantri}</td>
                    <td>${santri.NamaKelas}</td>
                    <td>${santri.NamaTpq}</td>
                    <td><span class="badge ${statusClass}">${isPeserta}</span></td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function selectAllSantri() {
        $('.santri-checkbox:not(:disabled)').each(function() {
            if (!$(this).is(':checked')) {
                $(this).prop('checked', true);
                const santriId = $(this).val();
                if (!selectedSantri.includes(santriId)) {
                    selectedSantri.push(santriId);
                }
            }
        });
        updateProcessButton();
    }

    function clearSelection() {
        $('.santri-checkbox').prop('checked', false);
        selectedSantri = [];
        previewData = [];
        updateProcessButton();
    }

    function updateProcessButton() {
        const hasSelection = selectedSantri.length > 0;
        $('#processBtn').prop('disabled', !hasSelection);
    }

    // Process button click
    $('#processBtn').click(function() {
        if (selectedSantri.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Pilih minimal satu santri untuk diproses',
                confirmButtonText: 'OK'
            });
            return;
        }
        processRegistrasi();
    });




    function processRegistrasi() {
        // Show loading with SweetAlert2
        Swal.fire({
            title: 'Memproses Data...',
            text: 'Sedang menyimpan data ke tabel nilai munaqosah',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/munaqosah/processRegistrasiPeserta') ?>',
            type: 'POST',
            data: {
                santri_ids: JSON.stringify(selectedSantri),
                tahunAjaran: $('#tahunAjaran').val()
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Gagal memproses data. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            }
        });
    }




    function resetForm() {
        selectedSantri = [];
        previewData = [];
        $('#selectAll').prop('checked', false);
        $('.santri-checkbox').prop('checked', false);
        updateProcessButton();
    }
});
</script>
<?= $this->endSection() ?>
