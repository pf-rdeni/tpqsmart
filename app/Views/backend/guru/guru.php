<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row mb-2">
                <div class="col-sm-12 float-sm-left">
                    <a href="<?= base_url('backend/guru/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Guru</a>
                </div>
            </div>
            <h3 class="card-title">Pengajar di TPQ yang ada di Kecamatan Seri Kuala Lobam</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="tabelGuru" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>TTL</th>
                        <th>Mulai Bertugas</th>
                        <th>Alamat Lengkap</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($guru as $dataGuru) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $dataGuru['IdGuru'] ?></td>
                            <td><?= ucwords(strtolower($dataGuru['Nama'])) ?></td>
                            <td><?= ucwords(strtolower($dataGuru['JenisKelamin'])) ?></td>
                            <td><?= ucwords(strtolower($dataGuru['TempatLahir'])) . ", " . $dataGuru['TanggalLahir'] ?></td>
                            <td><?= $dataGuru['TanggalMulaiTugas'] ?></td>
                            <td><?= ucwords(strtolower($dataGuru['Alamat'])) . ", RT " . $dataGuru['Rt'] . " / RW " . $dataGuru['Rw'] . ", " . ucwords(strtolower($dataGuru['KelurahanDesa'])) ?></td>
                            <td><?= $dataGuru['Status'] == 1 ? 'Aktif' : 'Tidak Aktif' ?></td>
                            <td>
                                <a href="javascript:void(0)" onclick="editGuru('<?= $dataGuru['IdGuru'] ?>')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="javascript:void(0)" onclick="deleteGuru('<?= $dataGuru['IdGuru'] ?>', '<?= ucwords(strtolower($dataGuru['Nama'])) ?>')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>TTL</th>
                        <th>Mulai Bertugas</th>
                        <th>Alamat Lengkap</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Modal Edit Guru -->
<div class="modal fade" id="editGuruModal" tabindex="-1" role="dialog" aria-labelledby="editGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGuruModalLabel">Edit Data Guru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editGuruForm" action="<?= base_url('backend/guru/update') ?>" method="POST">
                    <input type="hidden" id="edit_IdGuru" name="IdGuru">
                    <input type="hidden" id="edit_TempatTugas" name="TempatTugas" required>

                    <div class="form-group">
                        <label for="edit_IdTpq">TPQ</label>
                        <select class="form-control" id="edit_IdTpq" name="IdTpq" required <?= session()->get('IdTpq') ? 'disabled' : '' ?>>
                            <option value="">Pilih TPQ</option>
                            <?php foreach ($tpq as $dataTpq): ?>
                                <option value="<?= $dataTpq['IdTpq'] ?>" data-nama="<?= $dataTpq['NamaTpq'] ?>">
                                    <?= $dataTpq['NamaTpq'] ?> - <?= $dataTpq['KelurahanDesa'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="edit_IdTpqError" class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_NoHp">No Handphone</label>
                                <input type="text" class="form-control" id="edit_NoHp" name="NoHp" required pattern="^[0-9]{10,13}$" placeholder="Contoh: 081234567890">
                                <div id="edit_NoHpError" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_TanggalMulaiTugas">Tanggal Mulai Tugas</label>
                                <input type="date" class="form-control" id="edit_TanggalMulaiTugas" name="TanggalMulaiTugas" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_GelarDepan">Gelar Depan</label>
                                <input type="text" class="form-control" id="edit_GelarDepan" name="GelarDepan" placeholder="Contoh: dr., Dr., Prof.">
                                <div id="edit_GelarDepanError" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_Nama">Nama Lengkap</label>
                                <input type="text" class="form-control" id="edit_Nama" name="Nama" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_GelarBelakang">Gelar Belakang</label>
                                <input type="text" class="form-control" id="edit_GelarBelakang" name="GelarBelakang" placeholder="Contoh: S.Pd, S.Kom, M.Pd">
                                <div id="edit_GelarBelakangError" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_JenisKelamin">Jenis Kelamin</label>
                                <select class="form-control" id="edit_JenisKelamin" name="JenisKelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_TempatLahir">Tempat Lahir</label>
                                <input type="text" class="form-control" id="edit_TempatLahir" name="TempatLahir" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_TanggalLahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="edit_TanggalLahir" name="TanggalLahir" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_PendidikanTerakhir">Pendidikan Terakhir</label>
                                <select class="form-control" id="edit_PendidikanTerakhir" name="PendidikanTerakhir" required>
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
                        <label for="edit_Alamat">Alamat</label>
                        <textarea class="form-control" id="edit_Alamat" name="Alamat" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_Rt">RT</label>
                                <input type="text" class="form-control" id="edit_Rt" name="Rt" required>
                                <div id="edit_RtError" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_Rw">RW</label>
                                <input type="text" class="form-control" id="edit_Rw" name="Rw" required>
                                <div id="edit_RwError" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_KelurahanDesa">Kelurahan/Desa</label>
                                <select class="form-control" id="edit_KelurahanDesa" name="KelurahanDesa" required>
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="updateGuru()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    function deleteGuru(IdGuru, namaGuru) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Data guru ID: <strong>${IdGuru}</strong> Nama: <strong>${namaGuru}</strong> akan dihapus permanen!`,
            icon: 'question',
            iconColor: '#d33',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('<?= base_url('backend/guru/delete/') ?>' + IdGuru, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message || 'Data guru berhasil dihapus.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Gagal menghapus data guru.');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: error.message || 'Terjadi kesalahan saat menghapus data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }

    function editGuru(id) {
        // Ambil data guru dari server
        fetch('<?= base_url('backend/guru/getData/') ?>' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const guru = data.data;

                    // Reset form terlebih dahulu
                    document.getElementById('editGuruForm').reset();

                    // Isi form dengan data guru
                    document.getElementById('edit_IdGuru').value = guru.IdGuru;

                    // Set TPQ
                    const tpqSelect = document.getElementById('edit_IdTpq');
                    for (let i = 0; i < tpqSelect.options.length; i++) {
                        if (tpqSelect.options[i].value === guru.IdTpq) {
                            tpqSelect.selectedIndex = i;
                            break;
                        }
                    }

                    document.getElementById('edit_NoHp').value = guru.NoHp;
                    document.getElementById('edit_TanggalMulaiTugas').value = guru.TanggalMulaiTugas;
                    document.getElementById('edit_GelarDepan').value = guru.GelarDepan || '';

                    // Format nama dengan kapital
                    const namaInput = document.getElementById('edit_Nama');
                    namaInput.value = guru.Nama;
                    formatKapital(namaInput);

                    document.getElementById('edit_GelarBelakang').value = guru.GelarBelakang || '';

                    // Set Jenis Kelamin
                    const jenisKelaminSelect = document.getElementById('edit_JenisKelamin');
                    for (let i = 0; i < jenisKelaminSelect.options.length; i++) {
                        if (jenisKelaminSelect.options[i].value.toUpperCase() === guru.JenisKelamin.toUpperCase()) {
                            jenisKelaminSelect.selectedIndex = i;
                            break;
                        }
                    }

                    // Format tempat lahir dengan kapital
                    const tempatLahirInput = document.getElementById('edit_TempatLahir');
                    tempatLahirInput.value = guru.TempatLahir;
                    formatKapital(tempatLahirInput);

                    document.getElementById('edit_TanggalLahir').value = guru.TanggalLahir;

                    // Set Pendidikan Terakhir
                    const pendidikanSelect = document.getElementById('edit_PendidikanTerakhir');
                    let pendidikanValue = guru.PendidikanTerakhir;

                    // Konversi nilai pendidikan
                    if (pendidikanValue.includes('/')) {
                        // Hapus "/SEDERAJAT" dan spasi di sekitar "/"
                        pendidikanValue = pendidikanValue.split('/')[0].trim().toUpperCase();
                    } else {
                        pendidikanValue = pendidikanValue.toUpperCase();
                    }

                    // Konversi nilai khusus
                    if (pendidikanValue === 'SETARATA') {
                        pendidikanValue = 'S1';
                    } else if (pendidikanValue === 'DIPLOMA') {
                        pendidikanValue = 'D3';
                    }

                    for (let i = 0; i < pendidikanSelect.options.length; i++) {
                        if (pendidikanSelect.options[i].value.toUpperCase() === pendidikanValue) {
                            pendidikanSelect.selectedIndex = i;
                            break;
                        }
                    }

                    // Format alamat dengan kapital
                    const alamatInput = document.getElementById('edit_Alamat');
                    alamatInput.value = guru.Alamat;
                    formatKapital(alamatInput);

                    document.getElementById('edit_Rt').value = guru.Rt;
                    document.getElementById('edit_Rw').value = guru.Rw;

                    // Set Kelurahan/Desa
                    const kelurahanSelect = document.getElementById('edit_KelurahanDesa');
                    for (let i = 0; i < kelurahanSelect.options.length; i++) {
                        if (kelurahanSelect.options[i].value.toUpperCase() === guru.KelurahanDesa.toUpperCase()) {
                            kelurahanSelect.selectedIndex = i;
                            break;
                        }
                    }

                    document.getElementById('edit_TempatTugas').value = guru.TempatTugas;

                    // Tampilkan modal
                    $('#editGuruModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Gagal mengambil data guru',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengambil data guru',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }

    function updateGuru() {
        const form = document.getElementById('editGuruForm');
        const formData = new FormData(form);

        // Tambahkan IdTpq dari session jika select disabled
        const tpqSelect = document.getElementById('edit_IdTpq');
        if (tpqSelect.disabled) {
            formData.set('IdTpq', '<?= session()->get('IdTpq') ?>');
        }

        fetch('<?= base_url('backend/guru/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Data guru berhasil diperbarui',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Gagal memperbarui data guru');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Gagal!',
                    text: error.message || 'Terjadi kesalahan saat memperbarui data',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }

    // Fungsi validasi nomor (NIK/KK)
    function validasiNomorKkNik(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const errorElement = document.getElementById(inputId + 'Error');
        if (!errorElement) {
            console.error(`Error element for ${inputId} not found`);
            return;
        }

        function validasiNomor(input) {
            const nilai = input.value.replace(/\D/g, '');
            const pola = /^[1-9]\d{15}$/;
            const docTypeLabel = 'NIK';

            if (nilai === '') {
                tampilkanError(`${docTypeLabel} harus diisi.`);
                return false;
            }

            if (!pola.test(nilai)) {
                tampilkanError(`${docTypeLabel} harus 16 digit dan tidak boleh diawali dengan angka 0.`);
                return false;
            }

            if (nilai === '0000000000000000') {
                tampilkanError(`${docTypeLabel} tidak boleh semua angka 0.`);
                return false;
            }

            sembunyikanError();
            return true;
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

        input.addEventListener('input', function(e) {
            const nilai = e.target.value.replace(/\D/g, '');
            e.target.value = nilai;
            validasiNomor(this);
        });

        input.addEventListener('blur', function() {
            if (validasiNomor(this)) {
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
        const rtInput = document.getElementById('edit_Rt');
        const rwInput = document.getElementById('edit_Rw');
        const rtError = document.getElementById('edit_RtError');
        const rwError = document.getElementById('edit_RwError');

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

        rtInput.addEventListener('input', function(e) {
            validasiNomor(this, rtError, 'RT');
        });

        rtInput.addEventListener('blur', function() {
            validasiNomor(this, rtError, 'RT');
        });

        rwInput.addEventListener('input', function(e) {
            validasiNomor(this, rwError, 'RW');
        });

        rwInput.addEventListener('blur', function() {
            validasiNomor(this, rwError, 'RW');
        });
    }

    // Fungsi validasi TPQ
    function validasiTpq() {
        const tpqSelect = document.getElementById('edit_IdTpq');
        const tpqError = document.getElementById('edit_IdTpqError');

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

        tpqSelect.addEventListener('change', function() {
            validasiTpq();
        });

        tpqSelect.addEventListener('blur', function() {
            validasiTpq();
        });
    }

    // Fungsi validasi gelar depan
    function validasiGelarDepan() {
        const gelarDepanInput = document.getElementById('edit_GelarDepan');
        const gelarDepanError = document.getElementById('edit_GelarDepanError');

        function validasiGelarDepan() {
            const nilai = gelarDepanInput.value.trim();

            if (nilai === '') {
                sembunyikanError();
                return true;
            }

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

        gelarDepanInput.addEventListener('input', function() {
            validasiGelarDepan();
        });

        gelarDepanInput.addEventListener('blur', function() {
            validasiGelarDepan();
        });
    }

    // Fungsi validasi gelar belakang
    function validasiGelarBelakang() {
        const gelarBelakangInput = document.getElementById('edit_GelarBelakang');
        const gelarBelakangError = document.getElementById('edit_GelarBelakangError');

        function validasiGelarBelakang() {
            const nilai = gelarBelakangInput.value.trim();

            if (nilai === '') {
                sembunyikanError();
                return true;
            }

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

        gelarBelakangInput.addEventListener('input', function() {
            validasiGelarBelakang();
        });

        gelarBelakangInput.addEventListener('blur', function() {
            validasiGelarBelakang();
        });
    }

    // Fungsi untuk mengatur Kelurahan/Desa berdasarkan TPQ
    function setKelurahanDesa() {
        const tpqSelect = document.getElementById('edit_IdTpq');
        const kelurahanSelect = document.getElementById('edit_KelurahanDesa');

        tpqSelect.addEventListener('change', function() {
            const selectedTpq = this.options[this.selectedIndex].text;
            const parts = selectedTpq.split('-');
            if (parts.length > 1) {
                const kelurahan = parts[1].trim();
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

        if (tpqSelect.value) {
            const selectedTpq = tpqSelect.options[tpqSelect.selectedIndex];
            const parts = selectedTpq.text.split('-');
            if (parts.length > 1) {
                const kelurahan = parts[1].trim();
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
        // Pisahkan input menjadi kata-kata
        const words = input.value.split(' ');

        // Format setiap kata
        const formattedWords = words.map(word => {
            // Jika kata kosong, kembalikan kosong
            if (!word.trim()) return word;

            // Ubah huruf pertama menjadi kapital dan sisanya kecil
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        });

        // Gabungkan kembali dengan spasi
        input.value = formattedWords.join(' ');
    }

    // Fungsi untuk validasi input dengan huruf kapital
    function validasiKapital() {
        const inputs = ['edit_Nama', 'edit_TempatLahir', 'edit_KelurahanDesa', 'edit_Alamat'];

        inputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                // Format saat input berubah
                input.addEventListener('input', function() {
                    formatKapital(this);
                });

                // Format saat input kehilangan fokus
                input.addEventListener('blur', function() {
                    formatKapital(this);
                });

                // Format saat nilai diisi secara programatik
                const originalValue = input.value;
                if (originalValue) {
                    input.value = originalValue;
                    formatKapital(input);
                }
            }
        });
    }

    // Fungsi validasi nomor handphone
    function validasiNoHp() {
        const noHpInput = document.getElementById('edit_NoHp');
        const noHpError = document.getElementById('edit_NoHpError');

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

        noHpInput.addEventListener('input', function() {
            validasiNoHp();
        });

        noHpInput.addEventListener('blur', function() {
            validasiNoHp();
        });
    }

    // Fungsi untuk mengatur TempatTugas berdasarkan TPQ yang dipilih
    function setTempatTugas() {
        const tpqSelect = document.getElementById('edit_IdTpq');
        const tempatTugasInput = document.getElementById('edit_TempatTugas');

        tpqSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                tempatTugasInput.value = selectedOption.getAttribute('data-nama');
            } else {
                tempatTugasInput.value = '';
            }
        });

        if (tpqSelect.value) {
            const selectedOption = tpqSelect.options[tpqSelect.selectedIndex];
            tempatTugasInput.value = selectedOption.getAttribute('data-nama');
        }
    }

    // Inisialisasi validasi saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        validasiNomorKkNik('edit_NoHp');
        validasiRtRw();
        validasiTpq();
        validasiGelarDepan();
        validasiGelarBelakang();
        setKelurahanDesa();
        validasiKapital();
        validasiNoHp();
        setTempatTugas();
    });

    initializeDataTableUmum("#tabelGuru", true, true);
</script>
<?= $this->endSection(); ?>