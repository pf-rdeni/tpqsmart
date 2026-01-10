<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-1"></i>
                    Tahap 1: Setup & Konfigurasi
                </h3>
            </div>
            <div class="card-body">
                <p>Sebelum pelaksanaan ujian, Admin/Operator harus mengatur parameter penilaian agar sesuai dengan kurikulum yang berlaku.</p>
                
                <hr>

                <h4>1. Kategori & Materi Ujian</h4>
                <ul>
                    <li><strong>Kategori Materi</strong>: Kelompok besar materi (misal: Al-Quran, Hafalan, Doa).</li>
                    <li><strong>Materi Ujian</strong>: Item spesifik yang dinilai (misal: Tajwid, Fashohah, Surat An-Nas).</li>
                    <li>Atur di menu <strong>Master Data -> Materi Munaqosah</strong>.</li>
                </ul>

                <h4>2. Bobot Nilai</h4>
                <div class="callout callout-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Penting!</h5>
                    <p>Total bobot nilai untuk satu paket ujian harus berjumlah <strong>100%</strong>.</p>
                </div>
                <ul>
                    <li>Masuk ke menu <strong>Bobot Nilai</strong>.</li>
                    <li>Tentukan persentase untuk setiap materi.</li>
                    <li>Contoh: Tajwid (40%), Kelancaran (30%), Adab (30%).</li>
                </ul>

                <h4>3. Kategori Kesalahan</h4>
                <ul>
                    <li>Digunakan untuk standarisasi pengurangan nilai.</li>
                    <li>Misal: "Salah Harakat" (-2 poin), "Lupa Ayat" (-5 poin).</li>
                    <li>Membantu juri memberikan penilaian yang objektif.</li>
                </ul>

                <hr>
                <h5>Diagram Alur Setup</h5>
                <div class="mermaid">
graph LR
    A[Start] --> B[Master Materi]
    B --> C[Master Kategori Kesalahan]
    C --> D{Jenis Ujian?}
    D -- Pra-Munaqosah --> E[Setup Bobot Internal]
    D -- Munaqosah Umum --> F[Setup Bobot Pusat]
    E --> G[Siap Registrasi]
    F --> G
                </div>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/munaqosah') ?>" class="btn btn-default">Kembali</a>
                <a href="<?= base_url('backend/dokumentasi/munaqosah/registrasi') ?>" class="btn btn-primary float-right">Lanjut: Registrasi <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>
<?= $this->endSection(); ?>
