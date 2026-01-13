<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-gavel mr-1"></i>
                    Tahap 3: Penilaian & Hasil
                </h3>
            </div>
            <div class="card-body">
                <p>Panduan bagi Dewan Juri dalam memberikan penilaian dan penentuan kelulusan.</p>
                
                <hr>

                <h4>1. Dashboard Juri</h4>
                <ul>
                    <li>Juri hanya perlu login dan <strong>Standby</strong>.</li>
                    <li>Sistem otomatis menampilkan nama peserta yang di-assign ke ruangan juri tersebut (Push Notification/Auto-refresh).</li>
                </ul>

                <h4>2. Proses Input Nilai</h4>
                <ul>
                    <li>Klik nama peserta yang aktif.</li>
                    <li>Akan muncul form penilaian sesuai materi yang diujikan.</li>
                    <li>Pilih <strong>Kategori Kesalahan</strong> jika ada pengurangan poin, atau input nilai manual (tergantung konfigurasi).</li>
                    <li>Nilai total dihitung otomatis real-time.</li>
                </ul>

                <h4>3. Kunci Nilai & Selesai</h4>
                <ul>
                    <li>Setelah selesai menguji satu peserta, klik <strong>Simpan & Selesai</strong>.</li>
                    <li>Status peserta berubah menjadi "Sudah Dinilai".</li>
                    <li>Juri kembali ke mode standby menunggu peserta berikutnya.</li>
                </ul>

                <h4>4. Kelulusan (Final)</h4>
                <ul>
                    <li>Admin memantau rekap nilai seluruh peserta.</li>
                    <li>Tentukan <em>Passing Grade</em> (Nilai Minimal Lulus).</li>
                    <li>Generate status Lulus/Tidak Lulus massal.</li>
                    <li>Cetak Sertifikat/Syahadah.</li>
                </ul>

                <hr>
                <h5>Diagram Alur Penilaian</h5>
                <div class="mermaid">
stateDiagram-v2
    [*] --> Standby: Juri Login
    Standby --> Menguji: Peserta Masuk
    
    state Menguji {
        [*] --> Cek_Identitas
        Cek_Identitas --> Uji_Materi
        Uji_Materi --> Input_Pengurangan_Poin
        Input_Pengurangan_Poin --> Hitung_Total
    }

    Menguji --> Simpan: Klik Selesai
    Simpan --> Standby: Tunggu Next Peserta
    
    Standby --> [*]: Logout
                </div>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/munaqosah/registrasi') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Registrasi</a>
                <a href="<?= base_url('backend/dokumentasi/munaqosah') ?>" class="btn btn-default float-right">Ke Menu Utama <i class="fas fa-home"></i></a>
            </div>
        </div>
    </div>
</section>


<?= $this->endSection(); ?>
