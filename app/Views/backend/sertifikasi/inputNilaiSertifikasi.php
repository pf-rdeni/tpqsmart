<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Form Input Nilai Sertifikasi
                            <span id="noTestHeader" class="ml-2 badge badge-info" style="display: none;"></span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary" id="btnKembaliStep1" style="display: none;">
                                <i class="fas fa-arrow-left"></i> Kembali ke Step 1
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Stepper -->
                        <div class="bs-stepper">
                            <div class="bs-stepper-header" role="tablist">
                                <div class="step" data-target="#step1">
                                    <button type="button" class="step-trigger" role="tab" aria-controls="step1" id="stepper1-trigger" aria-selected="true">
                                        <span class="bs-stepper-circle">1</span>
                                        <span class="bs-stepper-label">Input No Peserta</span>
                                    </button>
                                </div>
                                <div class="bs-stepper-line"></div>
                                <div class="step" data-target="#step2">
                                    <button type="button" class="step-trigger" role="tab" aria-controls="step2" id="stepper2-trigger" aria-selected="false" disabled="disabled">
                                        <span class="bs-stepper-circle">2</span>
                                        <span class="bs-stepper-label">Input Nilai</span>
                                    </button>
                                </div>
                            </div>
                            <div class="bs-stepper-content">
                                <form id="formInputNilai">
                                    <!-- Step 1: Input No Peserta -->
                                    <div id="step1" class="content" role="tabpanel" aria-labelledby="stepper1-trigger">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="noTest">No Peserta <span class="text-danger">*</span></label>
                                                    <input type="number"
                                                        class="form-control"
                                                        id="noTest"
                                                        name="noTest"
                                                        placeholder="Masukkan Nomor Peserta (100-300)"
                                                        min="100"
                                                        max="300"
                                                        pattern="[0-9]{3}"
                                                        required>
                                                    <small class="form-text text-muted">
                                                        Masukkan nomor peserta guru yang akan dinilai (100-300, 3 digit angka)
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <div>
                                                        <button type="button" class="btn btn-primary" id="btnCekPeserta">
                                                            <i class="fas fa-search"></i> Cek Peserta
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Info Peserta -->
                                        <div id="infoPeserta" class="alert" style="display: none;">
                                            <h5 id="infoPesertaTitle"><i class="icon fas fa-info-circle"></i> Informasi Peserta</h5>
                                            <div id="pesertaInfo"></div>
                                        </div>
                                        <!-- Tabel 5 Peserta Terakhir -->
                                        <?php if (!empty($peserta_terakhir)): ?>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header bg-gradient-secondary">
                                                            <h5 class="card-title mb-0 text-white">
                                                                <i class="fas fa-history"></i> 5 Peserta Terakhir yang Sudah Dinilai
                                                            </h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-striped table-bordered table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No Peserta</th>
                                                                            <th>Nama Guru</th>
                                                                            <th>Tanggal</th>
                                                                            <th>Waktu</th>
                                                                            <th>Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($peserta_terakhir as $peserta): ?>
                                                                            <tr>
                                                                                <td><strong><?= esc($peserta['NoPeserta'] ?? $peserta['noTest'] ?? '-') ?></strong></td>
                                                                                <td><?= esc($peserta['NamaGuru'] ?? '-') ?></td>
                                                                                <td><?= date('d/m/Y', strtotime($peserta['updated_at'])) ?></td>
                                                                                <td><?= date('H:i:s', strtotime($peserta['updated_at'])) ?></td>
                                                                                <td class="text-center">
                                                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setNoTest('<?= esc($peserta['NoPeserta'] ?? $peserta['noTest'] ?? '') ?>')">
                                                                                        <i class="fas fa-edit"></i> Input Lagi
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Step 2: Input Nilai -->
                                    <div id="step2" class="content" role="tabpanel" aria-labelledby="stepper2-trigger">
                                        <div id="formNilaiContainer">
                                            <!-- Form nilai akan di-generate secara dinamis -->
                                        </div>
                                        <!-- Button untuk mengirim nilai -->
                                        <div id="btnKirimNilaiContainer" class="text-center mt-4" style="display: none;">
                                            <button type="button" class="btn btn-success btn-lg" id="btnKirimNilai">
                                                <i class="fas fa-paper-plane"></i> Simpan Nilai
                                            </button>
                                            <p class="text-muted mt-2">
                                                <i class="fas fa-info-circle"></i> Pastikan nilai sudah diisi dengan benar sebelum menyimpan
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    var stepper;
    var currentGuruData = null;
    var currentJuriData = <?= json_encode($juri_data) ?>;
    var isEditMode = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize stepper
        stepper = new Stepper(document.querySelector('.bs-stepper'));

        // Cek peserta button
        document.getElementById('btnCekPeserta').addEventListener('click', function() {
            cekPeserta();
        });

        // Validasi input no peserta: hanya angka, min 100, max 300 dengan validasi real-time
        var noTestInput = document.getElementById('noTest');

        // Fungsi untuk validasi dan tampilkan error
        function validateNoPesertaRealTime(input) {
            var value = input.value.trim();
            var errorMsg = input.parentElement.querySelector('.error-message');

            // Hapus error message lama jika ada
            if (errorMsg) {
                errorMsg.remove();
            }

            // Jika kosong, tidak tampilkan error
            if (!value) {
                input.classList.remove('is-invalid');
                return true;
            }

            var isValid = true;
            var errorText = '';

            // Cek panjang digit
            if (value.length > 3) {
                isValid = false;
                errorText = 'Nomor peserta maksimal 3 digit (100-300)';
            }
            // Cek digit pertama jika sudah ada input
            else if (value.length >= 1) {
                var firstDigit = parseInt(value[0]);
                // Jika digit pertama > 3, tidak mungkin valid (max 300)
                if (firstDigit > 3) {
                    isValid = false;
                    errorText = 'Angka pertama tidak boleh lebih dari 3 (maksimal 300)';
                }
                // Jika digit pertama = 3, cek digit kedua
                else if (firstDigit === 3 && value.length >= 2) {
                    var secondDigit = parseInt(value[1]);
                    // Jika digit kedua > 0, tidak valid (max 300, jadi 3xx dengan x > 0 tidak valid)
                    if (secondDigit > 0) {
                        isValid = false;
                        errorText = 'Nomor peserta maksimal 300';
                    }
                    // Jika digit kedua = 0, cek digit ketiga (hanya 30x yang valid, dan x harus 0)
                    else if (value.length === 3) {
                        var thirdDigit = parseInt(value[2]);
                        // Jika digit ketiga > 0, tidak valid (max 300, jadi hanya 300 yang valid)
                        if (thirdDigit > 0) {
                            isValid = false;
                            errorText = 'Nomor peserta maksimal 300';
                        }
                    }
                }
                // Jika sudah 3 digit, cek range
                else if (value.length === 3) {
                    var valueInt = parseInt(value);
                    if (valueInt < 100) {
                        isValid = false;
                        errorText = 'Nomor peserta minimal 100';
                    } else if (valueInt > 300) {
                        isValid = false;
                        errorText = 'Nomor peserta maksimal 300';
                    }
                }
            }

            // Tampilkan atau sembunyikan error
            if (!isValid) {
                input.classList.add('is-invalid');
                // Buat error message span merah
                errorMsg = document.createElement('span');
                errorMsg.className = 'error-message text-danger';
                errorMsg.style.display = 'block';
                errorMsg.style.fontSize = '0.875rem';
                errorMsg.style.marginTop = '0.25rem';
                errorMsg.textContent = errorText;
                input.parentElement.appendChild(errorMsg);
            } else {
                input.classList.remove('is-invalid');
            }

            return isValid;
        }

        // Event listener untuk input real-time
        noTestInput.addEventListener('input', function() {
            var value = this.value;
            // Hapus karakter non-angka
            value = value.replace(/[^0-9]/g, '');
            this.value = value;

            // Validasi real-time
            validateNoPesertaRealTime(this);
        });

        noTestInput.addEventListener('keypress', function(e) {
            var currentValue = this.value;
            var key = e.key;

            // Hanya izinkan angka
            if (!/[0-9]/.test(key) && !['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(key)) {
                e.preventDefault();
                return;
            }

            // Validasi digit sebelum input
            if (/[0-9]/.test(key)) {
                var newValue = currentValue + key;
                // Jika digit pertama > 3, prevent input
                if (newValue.length === 1 && parseInt(key) > 3) {
                    e.preventDefault();
                    // Tampilkan error
                    this.value = newValue;
                    validateNoPesertaRealTime(this);
                    this.value = currentValue; // Kembalikan nilai
                    return;
                }
                // Jika digit pertama = 3, cek digit kedua
                if (currentValue.length === 1 && currentValue[0] === '3') {
                    // Jika digit kedua > 0, prevent input (max 300, jadi hanya 30x yang valid)
                    if (parseInt(key) > 0) {
                        e.preventDefault();
                        // Tampilkan error
                        this.value = newValue;
                        validateNoPesertaRealTime(this);
                        this.value = currentValue; // Kembalikan nilai
                        return;
                    }
                }
                // Jika sudah mengetik "30", cek digit ketiga
                if (currentValue.length === 2 && currentValue === '30') {
                    // Jika digit ketiga > 0, prevent input (max 300, jadi hanya 300 yang valid)
                    if (parseInt(key) > 0) {
                        e.preventDefault();
                        // Tampilkan error
                        this.value = newValue;
                        validateNoPesertaRealTime(this);
                        this.value = currentValue; // Kembalikan nilai
                        return;
                    }
                }
                // Jika sudah 3 digit, prevent input lebih
                if (currentValue.length >= 3) {
                    e.preventDefault();
                    return;
                }
            }

            // Enter key untuk cek peserta
            if (e.key === 'Enter') {
                e.preventDefault();
                if (validateNoPesertaRealTime(this)) {
                    cekPeserta();
                }
            }
        });

        // Validasi saat blur
        noTestInput.addEventListener('blur', function() {
            validateNoPesertaRealTime(this);
        });

        // Kembali ke step 1
        document.getElementById('btnKembaliStep1').addEventListener('click', function() {
            stepper.to(1);
            document.getElementById('btnKembaliStep1').style.display = 'none';
            document.getElementById('formNilaiContainer').innerHTML = '';
            document.getElementById('btnKirimNilaiContainer').style.display = 'none';
        });

        // Simpan nilai
        document.getElementById('btnKirimNilai').addEventListener('click', function() {
            simpanNilai();
        });
    });

    function setNoTest(noTest) {
        document.getElementById('noTest').value = noTest;
        cekPeserta();
    }

    function cekPeserta() {
        var noTest = document.getElementById('noTest').value.trim();

        if (!noTest) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nomor peserta tidak boleh kosong'
            });
            return;
        }

        // Validasi range
        var noTestInt = parseInt(noTest);
        if (isNaN(noTestInt) || noTestInt < 100 || noTestInt > 300) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nomor peserta harus antara 100-300'
            });
            document.getElementById('noTest').focus();
            return;
        }

        // Validasi panjang (3 digit)
        if (noTest.length !== 3) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nomor peserta harus 3 digit (100-300)'
            });
            document.getElementById('noTest').focus();
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // AJAX request
        fetch('<?= base_url('backend/sertifikasi/cekPeserta') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    noTest: noTest,
                    IdJuri: currentJuriData.IdJuri
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    currentGuruData = data.data.guru;
                    isEditMode = data.data.allMateriSudahDinilai || false;

                    // Tentukan status sudah dinilai atau belum (hanya badge)
                    var statusBadge = '';
                    if (data.data.allMateriSudahDinilai) {
                        statusBadge = '<span class="badge badge-success">Sudah Dinilai</span>';
                    } else {
                        statusBadge = '<span class="badge badge-warning">Belum Dinilai</span>';
                    }

                    // Tampilkan info peserta (hanya No Peserta, Nama, dan Status)
                    var infoHtml = `
                    <table class="table table-sm">
                        <tr>
                            <th width="150">No Peserta:</th>
                            <td><strong>${data.data.guru.NoPeserta || 'N/A'}</strong></td>
                        </tr>
                        <tr>
                            <th>Nama:</th>
                            <td>${data.data.guru.Nama}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>${statusBadge}</td>
                        </tr>
                    </table>
                `;

                    document.getElementById('pesertaInfo').innerHTML = infoHtml;
                    document.getElementById('infoPeserta').className = 'alert alert-info';
                    document.getElementById('infoPeserta').style.display = 'block';
                    document.getElementById('noTestHeader').textContent = 'No Peserta : ' + noTest;
                    document.getElementById('noTestHeader').style.display = 'inline-block';

                    // Load form nilai dengan materi
                    loadFormNilai(data.data);
                } else {
                    // Tampilkan pesan error yang lebih informatif
                    var icon = 'error';
                    var title = 'Validasi Gagal';

                    if (data.code === 'NILAI_SUDAH_ADA_DARI_JURI_LAIN') {
                        icon = 'warning';
                        title = 'Nilai Sudah Ada';
                    }

                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error.message
                });
            });
    }

    function loadFormNilai(data) {
        var materiList = data.materiList || [];
        var existingNilaiByMateri = data.existingNilaiByMateri || {};

        if (materiList.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Materi',
                text: 'Tidak ada materi yang tersedia untuk grup materi ini'
            });
            return;
        }

        var formHtml = `
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Input Nilai per Materi</h5>
                </div>
                <div class="card-body">
        `;

        // Generate form untuk setiap materi
        materiList.forEach(function(materi) {
            var existingNilai = existingNilaiByMateri[materi.IdMateri] || null;
            var nilaiValue = existingNilai ? existingNilai.Nilai : '';
            var statusBadge = existingNilai ? '<span class="badge badge-warning ml-2">Sudah Ada</span>' : '';

            // Khusus untuk Materi Pilihan Ganda (SM001), gunakan 2 field
            if (materi.IdMateri === 'SM001') {
                // Hitung jumlah benar dari nilai yang ada (jika ada)
                var jumlahBenarValue = '';
                if (nilaiValue && nilaiValue !== '') {
                    // Konversi balik: nilai = (jumlah benar / 25) * 100
                    // jumlah benar = (nilai / 100) * 25
                    var jumlahBenar = Math.round((parseFloat(nilaiValue) / 100) * 25);
                    jumlahBenarValue = jumlahBenar;
                }

                formHtml += `
                    <div class="form-group">
                        <label for="materi_${materi.IdMateri}">
                            ${materi.NamaMateri} <span class="text-danger">*</span> ${statusBadge}
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="jumlah_benar_${materi.IdMateri}" class="small">Jumlah Soal Benar</label>
                                <input type="number" 
                                       class="form-control jumlah-benar-input" 
                                       id="jumlah_benar_${materi.IdMateri}" 
                                       data-id-materi="${materi.IdMateri}"
                                       min="0" max="25" step="1" 
                                       value="${jumlahBenarValue}" 
                                       placeholder="Masukkan jumlah benar (0-25)">
                                <small class="form-text text-muted">
                                    Total soal: 25
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label for="nilai_${materi.IdMateri}" class="small">Nilai (0-100)</label>
                                <input type="number" 
                                       class="form-control nilai-input" 
                                       id="nilai_${materi.IdMateri}" 
                                       name="nilai[${materi.IdMateri}]" 
                                       data-id-materi="${materi.IdMateri}"
                                       min="0" max="100" step="0.01" 
                                       value="${nilaiValue}" 
                                       placeholder="Masukkan nilai (0-100)" 
                                       required>
                                <small class="form-text text-muted">
                                    Nilai akan otomatis terisi dari jumlah benar, atau isi manual
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // Materi lain tetap menggunakan 1 field
                formHtml += `
                    <div class="form-group">
                        <label for="nilai_${materi.IdMateri}">
                            ${materi.NamaMateri} <span class="text-danger">*</span> ${statusBadge}
                        </label>
                        <input type="number" 
                               class="form-control nilai-input" 
                               id="nilai_${materi.IdMateri}" 
                               name="nilai[${materi.IdMateri}]" 
                               data-id-materi="${materi.IdMateri}"
                               min="0" max="100" step="0.01" 
                               value="${nilaiValue}" 
                               placeholder="Masukkan nilai (0-100)" 
                               required>
                        <small class="form-text text-muted">
                            Masukkan nilai untuk ${materi.NamaMateri} dalam range 0-100
                        </small>
                    </div>
                `;
            }
        });

        formHtml += `
                </div>
            </div>
        `;

        document.getElementById('formNilaiContainer').innerHTML = formHtml;
        document.getElementById('btnKirimNilaiContainer').style.display = 'block';

        // Setup event listener untuk konversi otomatis jumlah benar ke nilai (khusus SM001)
        var jumlahBenarInputs = document.querySelectorAll('.jumlah-benar-input');
        jumlahBenarInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                var idMateri = this.getAttribute('data-id-materi');
                if (idMateri === 'SM001') {
                    var jumlahBenar = parseFloat(this.value) || 0;
                    if (jumlahBenar >= 0 && jumlahBenar <= 25) {
                        // Konversi: nilai = (jumlah benar / 25) * 100
                        var nilai = (jumlahBenar / 25) * 100;
                        var nilaiInput = document.getElementById('nilai_' + idMateri);
                        if (nilaiInput) {
                            nilaiInput.value = nilai.toFixed(2);
                        }
                    }
                }
            });
        });

        // Setup event listener untuk nilai input (jika user langsung isi nilai, tidak perlu update jumlah benar)
        var nilaiInputs = document.querySelectorAll('.nilai-input');
        nilaiInputs.forEach(function(input) {
            var idMateri = input.getAttribute('data-id-materi');
            if (idMateri === 'SM001') {
                input.addEventListener('input', function() {
                    // Jika user langsung isi nilai, tidak perlu update jumlah benar
                    // Biarkan user bebas memilih cara input
                });
            }
        });

        // Move to step 2
        stepper.to(2);
        document.getElementById('btnKembaliStep1').style.display = 'block';
    }

    function simpanNilai() {
        // Ambil semua input nilai (hanya field nilai yang akan disimpan, bukan jumlah benar)
        var nilaiInputs = document.querySelectorAll('.nilai-input');
        var nilaiData = {};
        var hasError = false;
        var errorMessage = '';

        nilaiInputs.forEach(function(input) {
            var idMateri = input.getAttribute('data-id-materi');
            var nilai = input.value.trim();

            if (!nilai || nilai === '') {
                hasError = true;
                errorMessage = 'Semua nilai harus diisi';
                input.classList.add('is-invalid');
            } else {
                var nilaiFloat = parseFloat(nilai);
                if (isNaN(nilaiFloat) || nilaiFloat < 0 || nilaiFloat > 100) {
                    hasError = true;
                    errorMessage = 'Nilai harus dalam range 0-100';
                    input.classList.add('is-invalid');
                } else {
                    nilaiData[idMateri] = nilaiFloat;
                    input.classList.remove('is-invalid');
                }
            }
        });

        // Validasi khusus untuk SM001: pastikan jumlah benar valid jika diisi
        var jumlahBenarInput = document.getElementById('jumlah_benar_SM001');
        if (jumlahBenarInput) {
            var jumlahBenar = jumlahBenarInput.value.trim();
            if (jumlahBenar !== '') {
                var jumlahBenarFloat = parseFloat(jumlahBenar);
                if (isNaN(jumlahBenarFloat) || jumlahBenarFloat < 0 || jumlahBenarFloat > 25) {
                    hasError = true;
                    errorMessage = 'Jumlah soal benar harus dalam range 0-25';
                    jumlahBenarInput.classList.add('is-invalid');
                } else {
                    jumlahBenarInput.classList.remove('is-invalid');
                }
            }
        }

        if (hasError) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
            return;
        }

        if (Object.keys(nilaiData).length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tidak ada nilai yang akan disimpan'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Prepare form data
        var formData = new URLSearchParams();
        formData.append('noTest', currentGuruData.NoPeserta || currentGuruData.noTest);
        formData.append('IdJuri', currentJuriData.IdJuri);

        // Append nilai data as JSON string
        Object.keys(nilaiData).forEach(function(idMateri) {
            formData.append('nilai[' + idMateri + ']', nilaiData[idMateri]);
        });

        // AJAX request
        fetch('<?= base_url('backend/sertifikasi/simpanNilai') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Nilai berhasil disimpan',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reset form
                        document.getElementById('noTest').value = '';
                        document.getElementById('infoPeserta').style.display = 'none';
                        document.getElementById('noTestHeader').style.display = 'none';
                        document.getElementById('formNilaiContainer').innerHTML = '';
                        document.getElementById('btnKirimNilaiContainer').style.display = 'none';
                        stepper.to(1);
                        document.getElementById('btnKembaliStep1').style.display = 'none';

                        // Reload page to refresh peserta terakhir
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Gagal menyimpan nilai'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error.message
                });
            });
    }
</script>
<?= $this->endSection(); ?>