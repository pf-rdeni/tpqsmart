<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Tahap 2: Registrasi & Antrian
                </h3>
            </div>
            <div class="card-body">
                <p>Tahap ini melibatkan pendaftaran santri dan pengelolaan antrian saat hari pelaksanaan ujian.</p>
                
                <hr>

                <h4>1. Pendaftaran Peserta</h4>
                <ul>
                    <li>Operator memilih santri yang kompeten dari data induk.</li>
                    <li>Sistem memberikan <strong>Nomor Peserta</strong> unik.</li>
                    <li>Untuk Munaqosah Umum, data harus divalidasi oleh Admin Pusat.</li>
                </ul>

                <h4>2. Kartu Ujian & QR Code</h4>
                <ul>
                    <li>Cetak Kartu Ujian yang berisi Nama, Nomor, dan QR Code.</li>
                    <li>QR Code digunakan untuk <strong>Check-in</strong> saat hadir di lokasi ujian.</li>
                </ul>

                <h4>3. Sistem Antrian Otomatis</h4>
                <p>Sistem menggunakan logika <em>First-Come First-Served</em> dengan distribusi ruangan cerdas:</p>
                <ol>
                    <li>Peserta melakukan scan QR Code (Check-in).</li>
                    <li>Sistem mengecek Ruangan/Juri yang sedang kosong.</li>
                    <li>Peserta langsung diarahkan ke Ruangan/Juri tersebut.</li>
                    <li>Jika semua penuh, peserta masuk daftar tunggu.</li>
                </ol>

                <hr>
                <h5>Diagram Alur Registrasi & Antrian</h5>
                <div class="mermaid">
sequenceDiagram
    participant O as Operator TPQ
    participant S as Santri
    participant Sys as Sistem
    participant J as Juri/Ruangan

    O->>Sys: Input Data Santri
    Sys-->>O: Generate No Peserta & QR
    O->>S: Berikan Kartu Ujian

    Note over S, J: Hari Pelaksanaan

    S->>Sys: Scan QR Check-in
    activate Sys
    Sys->>Sys: Cek Ruangan Kosong
    alt Ada Ruangan Kosong
        Sys-->>S: Assign ke Ruangan X
        Sys->>J: Masukkan Data ke Dashboard Juri
    else Semua Penuh
        Sys-->>S: Masuk Waiting List
    end
    deactivate Sys
                </div>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/munaqosah/setup') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Setup</a>
                <a href="<?= base_url('backend/dokumentasi/munaqosah/penilaian') ?>" class="btn btn-primary float-right">Lanjut: Penilaian <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>
<?= $this->endSection(); ?>
