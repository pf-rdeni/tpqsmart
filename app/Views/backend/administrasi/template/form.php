<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle mr-1"></i>
            <?= implode('<br>', session()->getFlashdata('errors')) ?>
        </div>
    <?php endif; ?>

    <?php 
    $isEdit = !empty($template);
    $actionUrl = $isEdit ? base_url('backend/administrasi/template/update/' . $template['Id']) : base_url('backend/administrasi/template/store');
    ?>

    <form action="<?= $actionUrl ?>" method="POST" id="formTemplate">
        <?= csrf_field() ?>
        <div class="row">
            <!-- Kolom Kiri: Input Konfigurasi & Konten -->
            <div class="col-lg-8">
                <!-- 1. Informasi Dasar -->
                <div class="card card-primary card-outline shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-info-circle mr-2"></i>1. Informasi Template</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7 form-group">
                                <label>Nama Template <span class="text-danger">*</span></label>
                                <input type="text" name="NamaTemplate" class="form-control font-weight-bold" 
                                       value="<?= esc(old('NamaTemplate', $template['NamaTemplate'] ?? '')) ?>" 
                                       placeholder="Contoh: Template Undangan Rapat Wali Santri" required>
                            </div>
                            <div class="col-md-5 form-group">
                                <label>Kategori Surat <span class="text-danger">*</span></label>
                                <select name="IdKategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($kategori as $k): ?>
                                        <option value="<?= $k['Id'] ?>" <?= (old('IdKategori', $template['IdKategori'] ?? '') == $k['Id']) ? 'selected' : '' ?>>
                                            <?= esc($k['NamaKategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi Singkat (Opsional)</label>
                            <textarea name="Deskripsi" class="form-control" rows="2" placeholder="Jelaskan kegunaan template surat ini..."><?= esc(old('Deskripsi', $template['Deskripsi'] ?? '')) ?></textarea>
                        </div>

                        <?php if ($isAdmin): ?>
                            <div class="form-group">
                                <label>Lembaga Terkait <span class="text-danger">*</span></label>
                                <?php 
                                    $selectedTpq = (string)old('IdTpq', $template['IdTpq'] ?? '0');
                                ?>
                                <select name="IdTpq" class="form-control font-weight-bold" id="selectIdTpq">
                                    <option value="0" <?= $selectedTpq === '0' ? 'selected' : '' ?>>
                                        🏛️ FKPQ (Admin / Forum Komunikasi / Global)
                                    </option>
                                    <optgroup label="-- Pilih Lembaga TPQ Tertentu --">
                                        <?php foreach ($listTpq as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>" <?= $selectedTpq === (string)$tpq['IdTpq'] ? 'selected' : '' ?>>
                                                <?= esc($tpq['NamaTpq']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle text-info"></i> Pilih <strong>FKPQ (IdTpq = 0)</strong> untuk template surat resmi admin/forum atau template global yang berlaku bagi seluruh TPQ.
                                </small>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="IdTpq" value="<?= esc($idTpq) ?>">
                        <?php endif; ?>

                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" name="IsDefault" class="custom-control-input" id="checkDefault" value="1"
                                   <?= (!empty($template['IsDefault']) || old('IsDefault')) ? 'checked' : '' ?>>
                            <label class="custom-control-label font-weight-normal" for="checkDefault">
                                Jadikan Template Default untuk kategori ini
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 2. Format Kertas & Margin -->
                <div class="card card-outline card-secondary shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-ruler-combined mr-2"></i>2. Pengaturan Layout Kertas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Ukuran Kertas</label>
                                <select name="UkuranKertas" class="form-control">
                                    <option value="A4" <?= (old('UkuranKertas', $template['UkuranKertas'] ?? 'A4') == 'A4') ? 'selected' : '' ?>>A4 (210 x 297 mm)</option>
                                    <option value="F4" <?= (old('UkuranKertas', $template['UkuranKertas'] ?? '') == 'F4') ? 'selected' : '' ?>>F4 / Folio (215 x 330 mm)</option>
                                    <option value="A5" <?= (old('UkuranKertas', $template['UkuranKertas'] ?? '') == 'A5') ? 'selected' : '' ?>>A5 (148 x 210 mm)</option>
                                    <option value="Legal" <?= (old('UkuranKertas', $template['UkuranKertas'] ?? '') == 'Legal') ? 'selected' : '' ?>>Legal (216 x 356 mm)</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Orientasi</label>
                                <select name="Orientasi" class="form-control">
                                    <option value="Portrait" <?= (old('Orientasi', $template['Orientasi'] ?? 'Portrait') == 'Portrait') ? 'selected' : '' ?>>Portrait (Tegak)</option>
                                    <option value="Landscape" <?= (old('Orientasi', $template['Orientasi'] ?? '') == 'Landscape') ? 'selected' : '' ?>>Landscape (Mendatar)</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Font & Ukuran</label>
                                <div class="input-group">
                                    <select name="FontFamily" class="form-control">
                                        <option value="Times New Roman" <?= (old('FontFamily', $template['FontFamily'] ?? 'Times New Roman') == 'Times New Roman') ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Arial" <?= (old('FontFamily', $template['FontFamily'] ?? '') == 'Arial') ? 'selected' : '' ?>>Arial</option>
                                        <option value="Calibri" <?= (old('FontFamily', $template['FontFamily'] ?? '') == 'Calibri') ? 'selected' : '' ?>>Calibri</option>
                                    </select>
                                    <select name="FontSize" class="form-control" style="max-width: 80px;">
                                        <option value="10" <?= (old('FontSize', $template['FontSize'] ?? '12') == '10') ? 'selected' : '' ?>>10 pt</option>
                                        <option value="11" <?= (old('FontSize', $template['FontSize'] ?? '12') == '11') ? 'selected' : '' ?>>11 pt</option>
                                        <option value="12" <?= (old('FontSize', $template['FontSize'] ?? '12') == '12') ? 'selected' : '' ?>>12 pt</option>
                                        <option value="14" <?= (old('FontSize', $template['FontSize'] ?? '12') == '14') ? 'selected' : '' ?>>14 pt</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <label class="d-block text-muted small text-uppercase font-weight-bold mb-2">Batas Tepi / Margin (mm):</label>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="small">Atas (Top)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="MarginAtas" class="form-control" value="<?= esc(old('MarginAtas', $template['MarginAtas'] ?? 20)) ?>">
                                    <div class="input-group-append"><span class="input-group-text">mm</span></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small">Bawah (Bottom)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="MarginBawah" class="form-control" value="<?= esc(old('MarginBawah', $template['MarginBawah'] ?? 20)) ?>">
                                    <div class="input-group-append"><span class="input-group-text">mm</span></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small">Kiri (Left)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="MarginKiri" class="form-control" value="<?= esc(old('MarginKiri', $template['MarginKiri'] ?? 25)) ?>">
                                    <div class="input-group-append"><span class="input-group-text">mm</span></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="small">Kanan (Right)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="MarginKanan" class="form-control" value="<?= esc(old('MarginKanan', $template['MarginKanan'] ?? 15)) ?>">
                                    <div class="input-group-append"><span class="input-group-text">mm</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Sumber Kop Surat -->
                <div class="card card-outline card-info shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-stamp mr-2"></i>3. Sumber Kop Surat</h3>
                    </div>
                    <div class="card-body">
                        <?php 
                            $defaultKop = ($isAdmin && (!isset($template['IdTpq']) || $template['IdTpq'] == 0)) ? 'FKPQ' : 'TPQ';
                            $currentKop = old('SumberKop', $template['SumberKop'] ?? $defaultKop); 
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="kopTPQ" name="SumberKop" value="TPQ" class="custom-control-input radio-kop" <?= $currentKop === 'TPQ' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="kopTPQ">
                                        <strong>Kop TPQ</strong> <small class="text-muted">(Menggunakan kop profil lembaga TPQ)</small>
                                    </label>
                                </div>
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="kopMDA" name="SumberKop" value="MDA" class="custom-control-input radio-kop" <?= $currentKop === 'MDA' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="kopMDA">
                                        <strong>Kop MDTA / MDA</strong> <small class="text-muted">(Profil lembaga MDA)</small>
                                    </label>
                                </div>
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="kopFKPQ" name="SumberKop" value="FKPQ" class="custom-control-input radio-kop" <?= $currentKop === 'FKPQ' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="kopFKPQ">
                                        <strong>Kop FKPQ</strong> <small class="text-muted">(Kop Forum Komunikasi)</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="kopKepanitiaan" name="SumberKop" value="Kepanitiaan" class="custom-control-input radio-kop" <?= $currentKop === 'Kepanitiaan' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="kopKepanitiaan">
                                        <strong>Kop Kepanitiaan Khusus</strong> <small class="text-muted">(Pilih dari upload)</small>
                                    </label>
                                </div>
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="kopTanpa" name="SumberKop" value="TanpaKop" class="custom-control-input radio-kop" <?= $currentKop === 'TanpaKop' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="kopTanpa">
                                        <strong>Tanpa Kop Surat</strong> <small class="text-muted">(Surat polos / kertas kop cetak)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Pilihan Kop Kepanitiaan -->
                        <div class="form-group mt-3" id="wrapperKopCustom" style="<?= $currentKop === 'Kepanitiaan' ? '' : 'display: none;' ?>">
                            <label>Pilih Kop Kepanitiaan Terdaftar:</label>
                            <select name="IdKopCustom" class="form-control">
                                <option value="">-- Pilih Kop Kepanitiaan --</option>
                                <?php foreach ($kopList as $kop): ?>
                                    <option value="<?= $kop['Id'] ?>" <?= (old('IdKopCustom', $template['IdKopCustom'] ?? '') == $kop['Id']) ? 'selected' : '' ?>>
                                        <?= esc($kop['NamaKop']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 4. Komponen Surat (Toggles) -->
                <div class="card card-outline card-warning shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-toggle-on mr-2"></i>4. Komponen Bagian Surat</h3>
                        <small class="text-muted ml-2">(Centang komponen yang ditampilkan)</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php 
                            $components = [
                                'ShowKopLembaga'    => ['Kop Surat Lembaga', 'Tampilkan kop di bagian atas', true],
                                'ShowGarisPemisah'  => ['Garis Pemisah Kop', 'Garis tebal di bawah kop', true],
                                'ShowNomorSurat'    => ['Nomor Surat', 'Nomor, Sifat, Lampiran, Hal', true],
                                'ShowLampiran'      => ['Baris Lampiran', 'Menampilkan jumlah lampiran', false],
                                'ShowHal'           => ['Perihal / Hal', 'Menampilkan perihal surat', true],
                                'ShowTujuan'        => ['Bagian Tujuan', 'Kepada Yth. & Alamat', true],
                                'ShowSalam'         => ['Salam Pembuka & Penutup', 'Assalamu\'alaikum dll', true],
                                'ShowIsi'           => ['Isi Surat', 'Teks mukadimah & konten utama', true],
                                'ShowPenutup'       => ['Kalimat Penutup', 'Demikian surat ini kami sampaikan...', true],
                                'ShowTempatTanggal' => ['Tempat & Tanggal', 'Tempat dan tanggal surat', true],
                                'ShowMengetahui'    => ['Bagian Tanda Tangan', 'Mengetahui / Yang Membuat', true],
                                'ShowQrCode'        => ['QR Code Tanda Tangan', 'Dukungan QR validasi digital', true],
                            ];
                            ?>

                            <?php foreach ($components as $fieldName => $cfg): ?>
                                <?php 
                                $checked = isset($template[$fieldName]) 
                                    ? $template[$fieldName] == 1 
                                    : (old($fieldName, $cfg[2]) ? true : false);
                                ?>
                                <div class="col-md-6 mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="<?= $fieldName ?>" value="1" class="custom-control-input switch-component" id="switch_<?= $fieldName ?>" <?= $checked ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="switch_<?= $fieldName ?>">
                                            <strong><?= $cfg[0] ?></strong>
                                            <small class="d-block text-muted"><?= $cfg[1] ?></small>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 5. Konten Template Default -->
                <div class="card card-outline card-success shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i>5. Teks & Konten Default</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Salam Pembuka</label>
                            <input type="text" name="SalamPembuka" class="form-control" 
                                   value="<?= esc(old('SalamPembuka', $template['SalamPembuka'] ?? 'Assalamu\'alaikum Warahmatullahi Wabarakatuh')) ?>">
                        </div>

                        <div class="form-group">
                            <label>Mukadimah / Paragraf Pembuka</label>
                            <textarea name="MukadimahIsi" class="form-control" rows="2" 
                                      placeholder="Puji syukur kehadirat Allah SWT..."><?= esc(old('MukadimahIsi', $template['MukadimahIsi'] ?? 'Dengan hormat, sehubungan dengan...')) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Isi Pokok Surat</label>
                            <textarea name="IsiSurat" id="editorIsiSurat" class="form-control" rows="8" 
                                      placeholder="Tuliskan isi pokok surat atau gunakan format tabel..."><?= esc(old('IsiSurat', $template['IsiSurat'] ?? '')) ?></textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> Placeholder yang dapat digunakan: <code>{{NOMOR_SURAT}}</code>, <code>{{TANGGAL_SURAT}}</code>, <code>{{NAMA_TUJUAN}}</code>, <code>{{JABATAN_TUJUAN}}</code>, <code>{{NAMA_GURU}}</code>, <code>{{NAMA_SANTRI}}</code>, <code>{{NAMA_TPQ}}</code>.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Kalimat Penutup</label>
                            <textarea name="Penutup" class="form-control" rows="2"><?= esc(old('Penutup', $template['Penutup'] ?? 'Demikian surat ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.')) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Salam Penutup</label>
                            <input type="text" name="SalamPenutup" class="form-control" 
                                   value="<?= esc(old('SalamPenutup', $template['SalamPenutup'] ?? 'Wassalamu\'alaikum Warahmatullahi Wabarakatuh')) ?>">
                        </div>

                        <div class="form-group">
                            <label>Format Penomoran Surat</label>
                            <input type="text" name="FormatNomor" class="form-control" 
                                   value="<?= esc(old('FormatNomor', $template['FormatNomor'] ?? '[NO:3]/[KODE-TPQ]/[KODE-KAT]/[BLN-ROMAWI]/[TAHUN]')) ?>">
                            <small class="text-muted">Token: <code>[NO:3]</code>, <code>[KODE-TPQ]</code>, <code>[KODE-KAT]</code>, <code>[BLN-ROMAWI]</code>, <code>[TAHUN]</code></small>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="mb-5 d-flex justify-content-between">
                    <a href="<?= base_url('backend/administrasi/template') ?>" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Buat Template Sekarang' ?>
                    </button>
                </div>
            </div>

            <!-- Kolom Kanan: Live Document Structure Preview -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header bg-dark text-white">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-eye mr-2"></i>Pratinjau Struktur Surat</h3>
                    </div>
                    <div class="card-body p-3 bg-light">
                        <div class="p-3 bg-white shadow-sm border rounded" id="liveDocOutline" style="min-height: 480px; font-size: 11px; line-height: 1.4;">
                            <!-- Outline Kop -->
                            <div class="p-2 mb-2 text-center border-bottom border-dark outline-section" id="sec-ShowKopLembaga">
                                <span class="badge badge-secondary mb-1">KOP SURAT</span>
                                <div class="font-weight-bold text-uppercase">Nama Lembaga / Panitia</div>
                                <div class="text-muted" style="font-size: 9px;">Alamat lengkap dan kontak sekretariat</div>
                            </div>

                            <!-- Garis Pemisah -->
                            <div class="border-top border-dark my-1 outline-section" id="sec-ShowGarisPemisah" style="border-width: 2px !important;"></div>

                            <!-- Header Info -->
                            <div class="my-2 outline-section" id="sec-ShowNomorSurat">
                                <table style="width: 100%; font-size: 10px;">
                                    <tr><td style="width: 50px;">Nomor</td><td>: 001/TPQ/UND/XI/<?= date('Y') ?></td></tr>
                                    <tr id="sec-ShowLampiran"><td>Lampiran</td><td>: -</td></tr>
                                    <tr id="sec-ShowHal"><td>Hal</td><td>: Undangan Resmi</td></tr>
                                </table>
                            </div>

                            <!-- Tujuan -->
                            <div class="my-2 p-1 outline-section" id="sec-ShowTujuan" style="font-size: 10px;">
                                <div>Kepada Yth.</div>
                                <div class="font-weight-bold">[Nama Tujuan / Pejabat]</div>
                                <div class="text-muted">di - Tempat</div>
                            </div>

                            <!-- Salam Pembuka -->
                            <div class="my-2 outline-section font-italic" id="sec-ShowSalam" style="font-size: 10px;">
                                Assalamu'alaikum Wr. Wb.
                            </div>

                            <!-- Isi Surat -->
                            <div class="my-2 p-1 border border-dashed rounded outline-section" id="sec-ShowIsi" style="background: #fafafa; font-size: 10px;">
                                <div class="text-muted mb-1">[Mukadimah dan paragraf pembuka...]</div>
                                <div class="text-dark">[Isi pokok surat, waktu, tempat, atau agenda kegiatan...]</div>
                            </div>

                            <!-- Penutup -->
                            <div class="my-2 outline-section text-muted" id="sec-ShowPenutup" style="font-size: 10px;">
                                [Kalimat penutup dan ucapan terima kasih...]
                            </div>

                            <!-- Tanggal & TTD -->
                            <div class="my-3 outline-section" id="sec-ShowTempatTanggal" style="text-align: right; font-size: 10px;">
                                <div>Bintan, <?= date('d M Y') ?></div>
                            </div>

                            <div class="row outline-section" id="sec-ShowMengetahui">
                                <div class="col-6 text-center" style="font-size: 9px;">
                                    <div>Mengetahui,</div>
                                    <div class="font-weight-bold">Kepala TPQ</div>
                                    <div class="my-1 outline-section" id="sec-ShowQrCode">
                                        <span class="badge badge-light border p-1"><i class="fas fa-qrcode"></i> [QR TTD]</span>
                                    </div>
                                    <div class="mt-2 text-muted">(Nama Kepala)</div>
                                </div>
                                <div class="col-6 text-center" style="font-size: 9px;">
                                    <div>Hormat kami,</div>
                                    <div class="font-weight-bold">Sekretaris / Pembuat</div>
                                    <div class="my-1">
                                        <span class="badge badge-light border p-1"><i class="fas fa-signature"></i> [Manual/QR]</span>
                                    </div>
                                    <div class="mt-2 text-muted">(Nama Pembuat)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE for Isi Surat
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#editorIsiSurat',
            height: 380,
            menubar: false,
            plugins: 'table lists link code searchreplace',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | insertPlaceholder | code',
            content_style: 'body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; }',
            setup: function(editor) {
                editor.ui.registry.addMenuButton('insertPlaceholder', {
                    text: '{ Variabel Dinamis }',
                    icon: 'code',
                    fetch: function(callback) {
                        var items = [
                            { type: 'menuitem', text: '{{NOMOR_SURAT}} - Nomor Surat', onAction: function() { editor.insertContent('{{NOMOR_SURAT}}'); } },
                            { type: 'menuitem', text: '{{TANGGAL_SURAT}} - Tanggal Surat', onAction: function() { editor.insertContent('{{TANGGAL_SURAT}}'); } },
                            { type: 'menuitem', text: '{{HAL}} - Perihal Surat', onAction: function() { editor.insertContent('{{HAL}}'); } },
                            { type: 'menuitem', text: '{{NAMA_TUJUAN}} - Nama Penerima', onAction: function() { editor.insertContent('{{NAMA_TUJUAN}}'); } },
                            { type: 'menuitem', text: '{{JABATAN_TUJUAN}} - Jabatan Penerima', onAction: function() { editor.insertContent('{{JABATAN_TUJUAN}}'); } },
                            { type: 'menuitem', text: '{{NAMA_SANTRI}} - Nama Santri', onAction: function() { editor.insertContent('{{NAMA_SANTRI}}'); } },
                            { type: 'menuitem', text: '{{NAMA_WALI}} - Nama Wali Santri', onAction: function() { editor.insertContent('{{NAMA_WALI}}'); } },
                            { type: 'menuitem', text: '{{NAMA_KELAS}} - Nama Kelas', onAction: function() { editor.insertContent('{{NAMA_KELAS}}'); } },
                            { type: 'menuitem', text: '{{NAMA_GURU}} - Nama Guru', onAction: function() { editor.insertContent('{{NAMA_GURU}}'); } },
                            { type: 'menuitem', text: '{{NAMA_TPQ}} - Nama Lembaga', onAction: function() { editor.insertContent('{{NAMA_TPQ}}'); } },
                            { type: 'menuitem', text: '{{ALAMAT_TPQ}} - Alamat Lembaga', onAction: function() { editor.insertContent('{{ALAMAT_TPQ}}'); } }
                        ];
                        callback(items);
                    }
                });
            }
        });
    }

    // Auto-select Kop FKPQ when Lembaga FKPQ is selected
    $('#selectIdTpq').on('change', function() {
        let val = $(this).val();
        if (val === '0') {
            $('#kopFKPQ').prop('checked', true).trigger('change');
        } else if ($('#kopFKPQ').is(':checked')) {
            $('#kopTPQ').prop('checked', true).trigger('change');
        }
    });

    // Show/hide Kop Kepanitiaan dropdown
    $('.radio-kop').on('change', function() {
        if ($(this).val() === 'Kepanitiaan') {
            $('#wrapperKopCustom').slideDown();
        } else {
            $('#wrapperKopCustom').slideUp();
        }
    });

    // Sync outline preview with component switch
    function syncOutline() {
        $('.switch-component').each(function() {
            let field = $(this).attr('name');
            let isChecked = $(this).is(':checked');
            let target = $('#sec-' + field);
            if (target.length) {
                if (isChecked) {
                    target.show();
                } else {
                    target.hide();
                }
            }
        });
    }

    $('.switch-component').on('change', syncOutline);
    syncOutline();
});
</script>

<?= $this->endSection(); ?>
