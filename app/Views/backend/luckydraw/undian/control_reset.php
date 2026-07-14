<?= $this->extend('/backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.ld-reset-container {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    padding: 28px 24px;
}

.ld-card {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 32px;
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
    color: #fff;
    max-width: 800px;
    margin: 0 auto;
}

.ld-header-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 8px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
}

.ld-header-desc {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
    margin-bottom: 24px;
}

.form-group label {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.form-control {
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #fff !important;
    border-radius: 10px;
    height: 46px;
    transition: all 0.3s ease;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.12) !important;
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.25) !important;
}

.form-control option {
    background: #24243e;
    color: #fff;
}

/* Custom Checkbox */
.reset-option-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 16px;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.reset-option-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(239, 68, 68, 0.4);
}

.reset-option-card.checked {
    background: rgba(239, 68, 68, 0.08);
    border-color: #ef4444;
}

.custom-checkbox {
    width: 22px;
    height: 22px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
    transition: all 0.2s ease;
}

.reset-option-card.checked .custom-checkbox {
    border-color: #ef4444;
    background: #ef4444;
}

.custom-checkbox i {
    color: #fff;
    font-size: 0.8rem;
    display: none;
}

.reset-option-card.checked .custom-checkbox i {
    display: block;
}

.option-details {
    flex-grow: 1;
}

.option-title {
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 2px;
}

.option-desc {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.55);
    line-height: 1.4;
}

.alert-warning-custom {
    background: rgba(217, 119, 6, 0.15);
    border: 1px solid rgba(217, 119, 6, 0.3);
    color: #f59e0b;
    border-radius: 12px;
    padding: 16px 20px;
    margin-top: 24px;
    display: flex;
    gap: 14px;
    font-size: 0.9rem;
    line-height: 1.5;
}

.alert-warning-custom i {
    font-size: 1.3rem;
    margin-top: 2px;
}

.ld-btn-reset {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: none;
    color: #fff;
    font-weight: 700;
    font-size: 1.05rem;
    padding: 14px 28px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.35);
    width: 100%;
    margin-top: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.ld-btn-reset:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(220, 38, 38, 0.5);
}

.ld-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,0.6);
    text-decoration: none !important;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 24px;
    transition: color 0.3s ease;
}

.ld-btn-back:hover {
    color: #fff;
}
</style>

<div class="ld-reset-container">
    <div class="container-fluid">
        <a href="<?= base_url('backend/luckydraw/dashboard/admin') ?>" class="ld-btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>

        <div class="ld-card">
            <div class="ld-header-title">
                <i class="fas fa-sliders-h text-danger"></i> Control Reset Lucky Draw
            </div>
            <div class="ld-header-desc">
                Pilih opsi di bawah untuk mengosongkan atau merestart data undian secara selektif dan fleksibel.
            </div>

            <form id="form-reset-control">
                <?= csrf_field(); ?>

                <!-- Baris Pilihan Kegiatan -->
                <div class="form-group mb-4">
                    <label for="id_kegiatan"><i class="fas fa-star text-warning mr-1"></i> Pilih Kegiatan *</label>
                    <select class="form-control" name="id_kegiatan" id="id_kegiatan" required>
                        <option value="">-- Pilih Kegiatan --</option>
                        <?php foreach($kegiatan as $k): ?>
                            <option value="<?= $k->id ?>" <?= ($k->id == $active_id_kegiatan) ? 'selected' : '' ?>>
                                <?= esc($k->nama_kegiatan) ?> (<?= date('d M Y', strtotime($k->tanggal_kegiatan)) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Baris Pilihan Barang (Dinamis) -->
                <div class="form-group mb-4">
                    <label for="id_barang"><i class="fas fa-gift text-warning mr-1"></i> Pilih Barang Hadiah (Opsional)</label>
                    <select class="form-control" name="id_barang" id="id_barang">
                        <option value="">-- Semua Barang Hadiah --</option>
                    </select>
                    <small class="text-muted mt-1 d-block">
                        Biarkan kosong jika ingin mereset seluruh barang/pemenang pada kegiatan terpilih.
                    </small>
                </div>

                <div class="form-group mb-3">
                    <label><i class="fas fa-cogs text-danger mr-1"></i> Opsi Reset Yang Dijalankan</label>
                </div>

                <!-- Checkbox 1: Reset Pemenang -->
                <div class="reset-option-card" id="card_reset_pemenang">
                    <input type="checkbox" name="reset_pemenang" id="reset_pemenang" class="d-none" value="1">
                    <div class="custom-checkbox">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="option-details">
                        <div class="option-title text-danger">Reset Pemenang Undian</div>
                        <div class="option-desc">
                            Menghapus seluruh daftar pemenang undian. Stok barang hadiah akan otomatis kembali penuh.
                        </div>
                    </div>
                </div>

                <!-- Checkbox 2: Reset Status Pengambilan -->
                <div class="reset-option-card" id="card_reset_status_diambil">
                    <input type="checkbox" name="reset_status_diambil" id="reset_status_diambil" class="d-none" value="1">
                    <div class="custom-checkbox">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="option-details">
                        <div class="option-title text-warning">Reset Status Pengambilan Hadiah</div>
                        <div class="option-desc">
                            Mengubah kembali status serah terima pemenang menjadi "Belum Diambil" dan mengosongkan waktu serah terima. Daftar pemenang tidak dihapus.
                        </div>
                    </div>
                </div>

                <!-- Checkbox 3: Reset Barang Hadiah -->
                <div class="reset-option-card" id="card_reset_barang">
                    <input type="checkbox" name="reset_barang" id="reset_barang" class="d-none" value="1">
                    <div class="custom-checkbox">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="option-details">
                        <div class="option-title text-danger">Reset Daftar Barang Hadiah</div>
                        <div class="option-desc">
                            Menghapus daftar barang hadiah dari kegiatan. Aksi ini juga akan menghapus data pemenang karena dependensi data.
                        </div>
                    </div>
                </div>

                <!-- Checkbox 4: Reset Panitia -->
                <div class="reset-option-card" id="card_reset_panitia">
                    <input type="checkbox" name="reset_panitia" id="reset_panitia" class="d-none" value="1">
                    <div class="custom-checkbox">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="option-details">
                        <div class="option-title text-danger">Reset Panitia Kegiatan</div>
                        <div class="option-desc">
                            Menghapus penugasan panitia pada kegiatan yang dipilih. Panitia terpilih tidak akan dapat mengakses modul undian kegiatan ini lagi.
                        </div>
                    </div>
                </div>

                <!-- Alert Warning Box -->
                <div class="alert-warning-custom">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Peringatan Keamanan!</strong> Tindakan reset tidak dapat dibatalkan (undo) setelah diproses. Pastikan Anda telah memilih opsi dengan benar sebelum menekan tombol di bawah.
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="ld-btn-reset">
                    <i class="fas fa-trash-alt"></i> Jalankan Reset Data
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectKegiatan = document.getElementById('id_kegiatan');
    const selectBarang = document.getElementById('id_barang');
    const formReset = document.getElementById('form-reset-control');

    // Opsi Checkboxes
    const checkboxes = {
        pemenang: document.getElementById('reset_pemenang'),
        status: document.getElementById('reset_status_diambil'),
        barang: document.getElementById('reset_barang'),
        panitia: document.getElementById('reset_panitia')
    };

    const cards = {
        pemenang: document.getElementById('card_reset_pemenang'),
        status: document.getElementById('card_reset_status_diambil'),
        barang: document.getElementById('card_reset_barang'),
        panitia: document.getElementById('card_reset_panitia')
    };

    // Fungsi update status card checked class
    function updateCardState(key) {
        if (checkboxes[key].checked) {
            cards[key].classList.add('checked');
        } else {
            cards[key].classList.remove('checked');
        }
    }

    // Event listener untuk klik card
    Object.keys(cards).forEach(key => {
        cards[key].addEventListener('click', function(e) {
            // Hindari trigger ganda jika user mengklik inputnya langsung (walaupun input tersembunyi)
            if (e.target.tagName === 'INPUT') return;

            // Jika "Reset Daftar Barang" di-check, paksa "Reset Pemenang" ikut di-check dan disable
            if (key === 'barang') {
                checkboxes.barang.checked = !checkboxes.barang.checked;
                if (checkboxes.barang.checked) {
                    checkboxes.pemenang.checked = true;
                    checkboxes.pemenang.disabled = true;
                    cards.pemenang.style.opacity = '0.6';
                    cards.pemenang.style.pointerEvents = 'none';
                    updateCardState('pemenang');
                } else {
                    checkboxes.pemenang.disabled = false;
                    cards.pemenang.style.opacity = '1';
                    cards.pemenang.style.pointerEvents = 'auto';
                }
                updateCardState('barang');
            } else {
                checkboxes[key].checked = !checkboxes[key].checked;
                updateCardState(key);
            }

            // Jika Reset Pemenang dicentang, nonaktifkan Reset Status (karena pemenang dihapus, status pengambilan tidak relevan)
            if (key === 'pemenang' && checkboxes.pemenang.checked) {
                checkboxes.status.checked = false;
                updateCardState('status');
            }
            if (key === 'status' && checkboxes.status.checked) {
                checkboxes.pemenang.checked = false;
                updateCardState('pemenang');
            }
        });
    });

    // Panggil barang saat kegiatan terpilih berubah
    function loadBarang(idKegiatan) {
        if (!idKegiatan) {
            selectBarang.innerHTML = '<option value="">-- Semua Barang Hadiah --</option>';
            return;
        }

        // Tampilkan loading
        selectBarang.innerHTML = '<option value="">Memuat barang...</option>';

        fetch('<?= base_url('backend/luckydraw/undian/get-barang-by-kegiatan') ?>/' + idKegiatan)
            .then(response => response.json())
            .then(data => {
                let html = '<option value="">-- Semua Barang Hadiah --</option>';
                data.forEach(item => {
                    html += `<option value="${item.id}">No. ${item.no_barang} - ${item.nama_barang} (${item.kategori})</option>`;
                });
                selectBarang.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                selectBarang.innerHTML = '<option value="">Gagal memuat data barang</option>';
            });
    }

    // Trigger load barang pertama kali saat halaman dimuat jika kegiatan sudah terpilih
    if (selectKegiatan.value) {
        loadBarang(selectKegiatan.value);
    }

    selectKegiatan.addEventListener('change', function() {
        loadBarang(this.value);
    });

    // Form Submit dengan double verification Swal
    formReset.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validasi minimal 1 checked
        const anyChecked = Object.keys(checkboxes).some(key => checkboxes[key].checked);
        if (!anyChecked) {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Silakan pilih minimal satu opsi reset yang ingin dijalankan.',
                icon: 'warning',
                background: '#1f1a3a',
                color: '#fff'
            });
            return;
        }

        // Dapatkan rincian apa yang akan dihapus
        let listReset = [];
        if (checkboxes.pemenang.checked) listReset.push('• Data pemenang undian');
        if (checkboxes.status.checked) listReset.push('• Status serah terima pemenang');
        if (checkboxes.barang.checked) listReset.push('• Daftar barang hadiah & data pemenangnya');
        if (checkboxes.panitia.checked) listReset.push('• Penugasan panitia kegiatan');

        const scopeText = selectBarang.value ? 'pada barang hadiah terpilih' : 'pada seluruh kegiatan';

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            html: `<div style="text-align:left;">Anda akan mereset data berikut ${scopeText}:<br><br>${listReset.join('<br>')}</div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            background: '#1f1a3a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                // Konfirmasi kedua: ketik RESET
                Swal.fire({
                    title: 'Verifikasi Administrator',
                    text: "Tindakan ini tidak dapat dibatalkan. Ketik 'RESET' untuk mengonfirmasi:",
                    input: 'text',
                    inputPlaceholder: 'Ketik RESET di sini',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Proses Sekarang!',
                    cancelButtonText: 'Batal',
                    background: '#1f1a3a',
                    color: '#fff',
                    preConfirm: (value) => {
                        if (value !== 'RESET') {
                            Swal.showValidationMessage("Anda harus mengetik 'RESET' untuk melanjutkan.");
                        }
                        return value;
                    }
                }).then((finalResult) => {
                    if (finalResult.isConfirmed) {
                        // Jalankan AJAX
                        Swal.showLoading();

                        // Kumpulkan data form secara aman (termasuk CSRF token dan input disabled)
                        const formData = new FormData(formReset);
                        // Jika input disabled, tambahkan manual ke form data karena FormData mengabaikannya
                        if (checkboxes.pemenang.disabled) {
                            formData.append('reset_pemenang', '1');
                        }

                        fetch('<?= base_url('backend/luckydraw/undian/proses-reset') ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(res => {
                            if (res.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: 'success',
                                    background: '#1f1a3a',
                                    color: '#fff'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: res.message,
                                    icon: 'error',
                                    background: '#1f1a3a',
                                    color: '#fff'
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan jaringan atau server error.',
                                icon: 'error',
                                background: '#1f1a3a',
                                color: '#fff'
                            });
                        });
                    }
                });
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
