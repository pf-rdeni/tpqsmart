<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Data Guru Baru</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="<?= base_url('backend/guru/store') ?>" method="POST">
                <div class="form-group">
                    <label for="IdTpq">TPQ</label>
                    <select class="form-control" id="IdTpq" name="IdTpq" required <?= session()->get('IdTpq') ? 'disabled' : '' ?>>
                        <option value="">Pilih TPQ</option>
                        <?php foreach ($tpq as $dataTpq): ?>
                            <?php if (session()->get('IdTpq') == $dataTpq['IdTpq']): ?>
                                <option value="<?= $dataTpq['IdTpq'] ?>" selected data-nama="<?= $dataTpq['NamaTpq'] ?>">
                                    <?= $dataTpq['NamaTpq'] ?> - <?= $dataTpq['KelurahanDesa'] ?>
                                </option>
                            <?php else: ?>
                                <option value="<?= $dataTpq['IdTpq'] ?>" data-nama="<?= $dataTpq['NamaTpq'] ?>">
                                    <?= $dataTpq['NamaTpq'] ?> - <?= $dataTpq['KelurahanDesa'] ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <div id="IdTpqError" class="invalid-feedback"></div>
                </div>

                <input type="hidden" id="TempatTugas" name="TempatTugas" required>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="IdGuru">NIK</label>
                            <input type="text" class="form-control" id="IdGuru" name="IdGuru" required pattern="^[1-9]\d{15}$">
                            <div id="IdGuruError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="NoHp">No Handphone</label>
                            <input type="text" class="form-control" id="NoHp" name="NoHp" required pattern="^[0-9]{10,13}$" placeholder="Contoh: 081234567890">
                            <div id="NoHpError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="TanggalMulaiTugas">Tanggal Mulai Tugas</label>
                            <input type="date" class="form-control" id="TanggalMulaiTugas" name="TanggalMulaiTugas" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="GelarDepan">Gelar Depan</label>
                            <input type="text" class="form-control" id="GelarDepan" name="GelarDepan" placeholder="Contoh: dr., Dr., Prof.">
                            <div id="GelarDepanError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Nama">Nama Lengkap</label>
                            <input type="text" class="form-control" id="Nama" name="Nama" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="GelarBelakang">Gelar Belakang</label>
                            <input type="text" class="form-control" id="GelarBelakang" name="GelarBelakang" placeholder="Contoh: S.Pd, S.Kom, M.Pd">
                            <div id="GelarBelakangError" class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="JenisKelamin">Jenis Kelamin</label>
                            <select class="form-control" id="JenisKelamin" name="JenisKelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="TempatLahir">Tempat Lahir</label>
                            <input type="text" class="form-control" id="TempatLahir" name="TempatLahir" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="TanggalLahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="TanggalLahir" name="TanggalLahir" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="PendidikanTerakhir">Pendidikan Terakhir</label>
                            <select class="form-control" id="PendidikanTerakhir" name="PendidikanTerakhir" required>
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="D1">D1</option>
                                <option value="D2">D2</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat</label>
                    <textarea class="form-control" id="Alamat" name="Alamat" rows="3" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Rt">RT</label>
                            <input type="text" class="form-control" id="Rt" name="Rt" required>
                            <div id="RtError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Rw">RW</label>
                            <input type="text" class="form-control" id="Rw" name="Rw" required>
                            <div id="RwError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="KelurahanDesa">Kelurahan/Desa</label>
                            <select class="form-control" id="KelurahanDesa" name="KelurahanDesa" required>
                                <option value="">Pilih Kelurahan/Desa</option>
                                <option value="Teluk Sasah">Teluk Sasah</option>
                                <option value="Busung">Busung</option>
                                <option value="Kuala Sempang">Kuala Sempang</option>
                                <option value="Tanjung Permai">Tanjung Permai</option>
                                <option value="Teluk Lobam">Teluk Lobam</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="IdTpq" value="<?= session()->get('IdTpq') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('backend/guru/show') ?>" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Menggunakan ID input untuk menentukan apakah itu NIK atau KK    
    function validasiNomorKkNik(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const errorElement = document.getElementById(inputId + 'Error');
        if (!errorElement) {
            console.error(`Error element for ${inputId} not found`);
            return;
        }

        // Fungsi untuk validasi nomor (NIK/KK)
        function validasiNomor(input) {
            const nilai = input.value.replace(/\D/g, ''); // Hapus karakter non-digit
            const pola = /^[1-9]\d{15}$/;
            const docTypeLabel = 'NIK';

            // Validasi dasar
            if (nilai === '') {
                tampilkanError(`${docTypeLabel} harus diisi.`);
                return false;
            }

            // Validasi format
            if (!pola.test(nilai)) {
                tampilkanError(`${docTypeLabel} harus 16 digit dan tidak boleh diawali dengan angka 0.`);
                return false;
            }

            // Validasi angka 0
            if (nilai === '0000000000000000') {
                tampilkanError(`${docTypeLabel} tidak boleh semua angka 0.`);
                return false;
            }

            sembunyikanError();
            return true;
        }

        // Fungsi untuk menampilkan error
        function tampilkanError(pesan) {
            errorElement.textContent = pesan;
            errorElement.style.display = 'block';
            input.classList.add('is-invalid');
        }

        // Fungsi untuk menyembunyikan error
        function sembunyikanError() {
            errorElement.style.display = 'none';
            input.classList.remove('is-invalid');
        }

        // Event listener untuk input
        input.addEventListener('input', function(e) {
            const nilai = e.target.value.replace(/\D/g, '');
            e.target.value = nilai;
            validasiNomor(this);
        });

        // Event listener untuk blur
        input.addEventListener('blur', function() {
            if (validasiNomor(this)) {
                // Cek ke database jika validasi format berhasil
                $.ajax({
                    url: '<?= base_url('backend/guru/validateNik') ?>',
                    type: 'POST',
                    data: {
                        IdGuru: this.value
                    },
                    success: function(response) {
                        if (response.exists) {
                            tampilkanError(`NIK ${response.data.IdGuru} sudah terdaftar atas nama ${response.data.Nama} di ${response.data.TempatTugas}!`);
                        }
                    }
                });
            }
        });
    }

    // Fungsi validasi RT/RW
    function validasiRtRw() {
        const rtInput = document.getElementById('Rt');
        const rwInput = document.getElementById('Rw');
        const rtError = document.getElementById('RtError');
        const rwError = document.getElementById('RwError');

        function validasiNomor(input, errorElement, label) {
            const nilai = input.value.replace(/\D/g, '');
            input.value = nilai;

            if (nilai === '') {
                tampilkanError(`${label} harus diisi.`);
                return false;
            }

            if (nilai === '0') {
                tampilkanError(`${label} tidak boleh 0.`);
                return false;
            }

            if (parseInt(nilai) > 999) {
                tampilkanError(`${label} tidak boleh lebih dari 999.`);
                input.value = nilai.slice(0, 3);
                return false;
            }

            function tampilkanError(pesan) {
                errorElement.textContent = pesan;
                errorElement.style.display = 'block';
                input.classList.add('is-invalid');
            }

            function sembunyikanError() {
                errorElement.style.display = 'none';
                input.classList.remove('is-invalid');
            }

            sembunyikanError();
            return true;
        }

        // Event listener untuk RT
        rtInput.addEventListener('input', function(e) {
            validasiNomor(this, rtError, 'RT');
        });

        rtInput.addEventListener('blur', function() {
            validasiNomor(this, rtError, 'RT');
        });

        // Event listener untuk RW
        rwInput.addEventListener('input', function(e) {
            validasiNomor(this, rwError, 'RW');
        });

        rwInput.addEventListener('blur', function() {
            validasiNomor(this, rwError, 'RW');
        });
    }

    // Fungsi validasi TPQ
    function validasiTpq() {
        const tpqSelect = document.getElementById('IdTpq');
        const tpqError = document.getElementById('IdTpqError');

        function validasiTpq() {
            const nilai = tpqSelect.value;

            if (nilai === '') {
                tampilkanError('TPQ harus dipilih.');
                return false;
            }

            function tampilkanError(pesan) {
                tpqError.textContent = pesan;
                tpqError.style.display = 'block';
                tpqSelect.classList.add('is-invalid');
            }

            function sembunyikanError() {
                tpqError.style.display = 'none';
                tpqSelect.classList.remove('is-invalid');
            }

            sembunyikanError();
            return true;
        }

        // Event listener untuk change
        tpqSelect.addEventListener('change', function() {
            validasiTpq();
        });

        // Event listener untuk blur
        tpqSelect.addEventListener('blur', function() {
            validasiTpq();
        });
    }

    // Fungsi validasi gelar depan
    function validasiGelarDepan() {
        const gelarDepanInput = document.getElementById('GelarDepan');
        const gelarDepanError = document.getElementById('GelarDepanError');

        function validasiGelarDepan() {
            const nilai = gelarDepanInput.value.trim();

            // Jika kosong, tidak perlu validasi
            if (nilai === '') {
                sembunyikanError();
                return true;
            }

            // Pola untuk gelar depan (dr., Dr., Prof.)
            const pola = /^(dr\.|Dr\.|Prof\.)$/;

            if (!pola.test(nilai)) {
                tampilkanError('Format gelar depan tidak valid. Contoh: dr., Dr., Prof.');
                return false;
            }

            function tampilkanError(pesan) {
                gelarDepanError.textContent = pesan;
                gelarDepanError.style.display = 'block';
                gelarDepanInput.classList.add('is-invalid');
            }

            function sembunyikanError() {
                gelarDepanError.style.display = 'none';
                gelarDepanInput.classList.remove('is-invalid');
            }

            sembunyikanError();
            return true;
        }

        // Event listener untuk input
        gelarDepanInput.addEventListener('input', function() {
            validasiGelarDepan();
        });

        // Event listener untuk blur
        gelarDepanInput.addEventListener('blur', function() {
            validasiGelarDepan();
        });
    }

    // Fungsi validasi gelar belakang
    function validasiGelarBelakang() {
        const gelarBelakangInput = document.getElementById('GelarBelakang');
        const gelarBelakangError = document.getElementById('GelarBelakangError');

        function validasiGelarBelakang() {
            const nilai = gelarBelakangInput.value.trim();

            // Jika kosong, tidak perlu validasi
            if (nilai === '') {
                sembunyikanError();
                return true;
            }

            // Pola untuk gelar belakang (S.Pd, S.Kom, M.Pd)
            const pola = /^[A-Z]\.([A-Za-z]+\.?)+$/;

            if (!pola.test(nilai)) {
                tampilkanError('Format gelar belakang tidak valid. Contoh: S.Pd, S.Kom, M.Pd');
                return false;
            }

            function tampilkanError(pesan) {
                gelarBelakangError.textContent = pesan;
                gelarBelakangError.style.display = 'block';
                gelarBelakangInput.classList.add('is-invalid');
            }

            function sembunyikanError() {
                gelarBelakangError.style.display = 'none';
                gelarBelakangInput.classList.remove('is-invalid');
            }

            sembunyikanError();
            return true;
        }

        // Event listener untuk input
        gelarBelakangInput.addEventListener('input', function() {
            validasiGelarBelakang();
        });

        // Event listener untuk blur
        gelarBelakangInput.addEventListener('blur', function() {
            validasiGelarBelakang();
        });
    }

    // Fungsi untuk mengatur Kelurahan/Desa berdasarkan TPQ
    function setKelurahanDesa() {
        const tpqSelect = document.getElementById('IdTpq');
        const kelurahanSelect = document.getElementById('KelurahanDesa');

        // Event listener untuk perubahan TPQ
        tpqSelect.addEventListener('change', function() {
            const selectedTpq = this.options[this.selectedIndex].text;

            // Pisahkan nama TPQ dan kelurahan berdasarkan tanda "-"
            const parts = selectedTpq.split('-');
            if (parts.length > 1) {
                const kelurahan = parts[1].trim();

                // Cari kelurahan yang cocok (case insensitive)
                let found = false;
                for (let i = 0; i < kelurahanSelect.options.length; i++) {
                    const option = kelurahanSelect.options[i];
                    if (option.value.toLowerCase() === kelurahan.toLowerCase()) {
                        kelurahanSelect.value = option.value;
                        found = true;
                        break;
                    }
                }
            }
        });

        // Jika TPQ sudah terpilih saat halaman dimuat
        if (tpqSelect.value) {
            const selectedTpq = tpqSelect.options[tpqSelect.selectedIndex].text;

            // Pisahkan nama TPQ dan kelurahan berdasarkan tanda "-"
            const parts = selectedTpq.split('-');
            if (parts.length > 1) {
                const kelurahan = parts[1].trim();

                // Cari kelurahan yang cocok (case insensitive)
                let found = false;
                for (let i = 0; i < kelurahanSelect.options.length; i++) {
                    const option = kelurahanSelect.options[i];
                    if (option.value.toLowerCase() === kelurahan.toLowerCase()) {
                        kelurahanSelect.value = option.value;
                        found = true;
                        break;
                    }
                }
            }
        }
    }

    // Fungsi untuk memformat huruf kapital di awal kata
    function formatKapital(input) {
        const words = input.value.split(' ');
        const formattedWords = words.map(word => {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        });
        input.value = formattedWords.join(' ');
    }

    // Fungsi untuk validasi input dengan huruf kapital
    function validasiKapital() {
        const inputs = ['Nama', 'TempatLahir', 'KelurahanDesa'];

        inputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                // Event listener untuk input
                input.addEventListener('input', function() {
                    formatKapital(this);
                });

                // Event listener untuk blur
                input.addEventListener('blur', function() {
                    formatKapital(this);
                });
            }
        });
    }

    // Fungsi validasi nomor handphone
    function validasiNoHp() {
        const noHpInput = document.getElementById('NoHp');
        const noHpError = document.getElementById('NoHpError');

        function validasiNoHp() {
            const nilai = noHpInput.value.replace(/\D/g, '');
            noHpInput.value = nilai;

            if (nilai === '') {
                tampilkanError('Nomor handphone harus diisi.');
                return false;
            }

            if (nilai.length < 10 || nilai.length > 13) {
                tampilkanError('Nomor handphone harus 10-13 digit.');
                return false;
            }

            if (!nilai.startsWith('0')) {
                tampilkanError('Nomor handphone harus diawali dengan angka 0.');
                return false;
            }

            // Validasi operator seluler
            const operator = nilai.substring(0, 4);
            const validOperators = ['0811', '0812', '0813', '0814', '0815', '0816', '0817', '0818', '0819', '0821', '0822', '0823', '0828', '0831', '0832', '0833', '0838', '0852', '0853', '0855', '0856', '0857', '0858', '0859', '0877', '0878', '0879', '0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889', '0895', '0896', '0897', '0898', '0899', '0907', '0908', '0909', '0911', '0912', '0913', '0914', '0915', '0916', '0917', '0918', '0919', '0921', '0922', '0923', '0924', '0925', '0926', '0927', '0928', '0929', '0931', '0932', '0933', '0934', '0935', '0936', '0937', '0938', '0939', '0941', '0942', '0943', '0944', '0945', '0946', '0947', '0948', '0949', '0951', '0952', '0953', '0954', '0955', '0956', '0957', '0958', '0959', '0961', '0962', '0963', '0964', '0965', '0966', '0967', '0968', '0969', '0971', '0972', '0973', '0974', '0975', '0976', '0977', '0978', '0979', '0981', '0982', '0983', '0984', '0985', '0986', '0987', '0988', '0989', '0991', '0992', '0993', '0994', '0995', '0996', '0997', '0998', '0999'];

            if (!validOperators.includes(operator)) {
                tampilkanError('Nomor handphone tidak valid. Pastikan menggunakan operator yang benar.');
                return false;
            }

            function tampilkanError(pesan) {
                noHpError.textContent = pesan;
                noHpError.style.display = 'block';
                noHpInput.classList.add('is-invalid');
            }

            function sembunyikanError() {
                noHpError.style.display = 'none';
                noHpInput.classList.remove('is-invalid');
            }

            sembunyikanError();
            return true;
        }

        // Event listener untuk input
        noHpInput.addEventListener('input', function() {
            validasiNoHp();
        });

        // Event listener untuk blur
        noHpInput.addEventListener('blur', function() {
            validasiNoHp();
        });
    }

    // Fungsi untuk mengatur TempatTugas berdasarkan TPQ yang dipilih
    function setTempatTugas() {
        const tpqSelect = document.getElementById('IdTpq');
        const tempatTugasInput = document.getElementById('TempatTugas');

        // Event listener untuk perubahan TPQ
        tpqSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                tempatTugasInput.value = selectedOption.getAttribute('data-nama');
            } else {
                tempatTugasInput.value = '';
            }
        });

        // Set nilai awal jika TPQ sudah terpilih
        if (tpqSelect.value) {
            const selectedOption = tpqSelect.options[tpqSelect.selectedIndex];
            tempatTugasInput.value = selectedOption.getAttribute('data-nama');
        }
    }

    // Inisialisasi validasi saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        validasiNomorKkNik('IdGuru');
        validasiRtRw();
        validasiTpq();
        validasiGelarDepan();
        validasiGelarBelakang();
        setKelurahanDesa();
        validasiKapital();
        validasiNoHp();
        setTempatTugas();
    });
</script>
<?= $this->endSection(); ?>