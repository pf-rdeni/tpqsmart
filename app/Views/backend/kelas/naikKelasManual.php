<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">

    <?= session()->getFlashdata('pesan') ?>

    <!-- ══════════════════════════════════════════════════════════
         LANGKAH 1 — Filter TPQ, TA Sumber, Kelas & Target
    ══════════════════════════════════════════════════════════ -->
    <div class="card card-primary card-outline mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exchange-alt text-primary mr-1"></i>
                Kenaikan / Perubahan Kelas Manual
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-info mr-2" id="btnSinkronisasiOtomatis" title="Periksa dan sinkronkan IdKelas di tbl_santri_baru mengikuti tbl_kelas_santri">
                    <i class="fas fa-sync-alt mr-1"></i> Cek & Sinkronkan IdKelas
                </button>
                <a href="<?= base_url('backend/kelas/showListSantriPerKelas') ?>"
                   class="btn btn-sm btn-default">
                    <i class="fas fa-arrow-left mr-1"></i> Naik Kelas Otomatis
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Fitur Penyesuaian / Kenaikan Kelas Manual</strong><br>
                Mendukung 2 mode utama:
                <ul class="mb-0 pl-3 mt-1">
                    <li><strong>Tahun Ajaran Sama:</strong> Untuk memindahkan santri ke kelas lain pada tahun ajaran yang sedang berjalan (koreksi data kelas santri).</li>
                    <li><strong>Tahun Ajaran Berikutnya:</strong> Untuk mempromosikan/menaikkan santri ke tahun ajaran baru.</li>
                </ul>
            </div>

            <!-- Step 1: Filter -->
            <div class="row align-items-end">
                <!-- 1. FILTER TPQ (SEBELUM TAHUN AJARAN SUMBER) -->
                <div class="col-lg-3 col-md-6">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold">
                            <i class="fas fa-mosque mr-1 text-purple"></i>
                            TPQ / Lembaga
                        </label>
                        <?php if ($isAdmin): ?>
                            <select class="form-control select2" id="selectTpq" multiple="multiple" data-placeholder="Semua TPQ (atau pilih beberapa)" style="width: 100%;">
                                <?php foreach ($dataTpq as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>">
                                        <?= esc($tpq['NamaTpq']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1">Pilih satu / beberapa, atau kosongkan untuk semua TPQ</small>
                        <?php else: ?>
                            <select class="form-control" id="selectTpq" disabled style="width: 100%;">
                                <?php foreach ($dataTpq as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>" selected>
                                        <?= esc($tpq['NamaTpq']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1"><i class="fas fa-lock mr-1 text-warning"></i>Terkunci pada TPQ Anda</small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. TAHUN AJARAN SUMBER -->
                <div class="col-lg-2 col-md-6">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold">
                            <i class="fas fa-calendar-alt mr-1 text-primary"></i>
                            TA Sumber
                        </label>
                        <select class="form-control" id="selectTahunAjaran">
                            <option value="">-- Pilih TA --</option>
                            <?php foreach ($tahunAjaranList as $ta): ?>
                                <option value="<?= $ta ?>"
                                    <?= ($ta == $selectedTahunAjaran) ? 'selected' : '' ?>>
                                    <?= convertTahunAjaran($ta) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted d-block mt-1">TA saat ini</small>
                    </div>
                </div>

                <!-- 3. KELAS SUMBER -->
                <div class="col-lg-2 col-md-6">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold">
                            <i class="fas fa-chalkboard-teacher mr-1 text-primary"></i>
                            Kelas Sumber
                        </label>
                        <select class="form-control" id="selectKelas">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($allClasses as $kelas): ?>
                                <?php if ($kelas['IdKelas'] == 10) continue; // Exclude Alumni ?>
                                <option value="<?= $kelas['IdKelas'] ?>"
                                    <?= ($kelas['IdKelas'] == $selectedKelas) ? 'selected' : '' ?>>
                                    <?= esc($kelas['NamaKelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted d-block mt-1">Kelas asal santri</small>
                    </div>
                </div>

                <!-- 4. TAHUN AJARAN TARGET -->
                <div class="col-lg-3 col-md-6">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold">
                            <i class="fas fa-calendar-check mr-1 text-success"></i>
                            TA Target
                        </label>
                        <select class="form-control" id="selectTahunAjaranTarget" disabled>
                            <option value="">-- Pilih TA Sumber --</option>
                        </select>
                        <small class="text-muted d-block mt-1" id="taTargetHint">Pilih TA sama / baru</small>
                    </div>
                </div>

                <!-- 5. TOMBOL TAMPILKAN -->
                <div class="col-lg-2 col-md-12">
                    <div class="form-group mb-2">
                        <button type="button" id="btnLoadSantri"
                                class="btn btn-primary btn-block" disabled>
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         LANGKAH 2 — Pilih Santri & Tentukan Kelas Tujuan
    ══════════════════════════════════════════════════════════ -->
    <div class="card" id="cardPilihSantri" style="display:none;">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">
                <i class="fas fa-users mr-1"></i>
                Langkah 2 — Pilih Santri & Tentukan Kelas Tujuan
            </h3>
            <div class="card-tools">
                <span class="badge badge-light badge-pill px-3 py-1" id="badgeTotal">0 santri</span>
            </div>
        </div>
        <div class="card-body">

            <!-- Info box ringkasan -->
            <div class="row mb-3" id="infoRow">
                <div class="col-md-3 col-sm-6">
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-purple elevation-1">
                            <i class="fas fa-mosque"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Filter TPQ</span>
                            <span class="info-box-number" id="infoTpqFilter" style="font-size:0.95rem;">Semua TPQ</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-chalkboard"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Kelas Asal</span>
                            <span class="info-box-number" id="infoKelasAsal" style="font-size:0.95rem;">—</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">TA Sumber</span>
                            <span class="info-box-number" id="infoTASumber" style="font-size:0.95rem;">—</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">TA Target</span>
                            <span class="info-box-number" id="infoTATarget" style="font-size:0.95rem;">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toolbar pilih & Set Kelas Sekaligus -->
            <div class="d-flex align-items-center mb-3 flex-wrap">
                <div class="btn-group mr-3 mb-2">
                    <button type="button" class="btn btn-success btn-sm" id="btnPilihSemua">
                        <i class="fas fa-check-double mr-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnBatalSemua">
                        <i class="fas fa-times mr-1"></i> Batal Semua
                    </button>
                </div>
                <span class="text-muted mr-3 mb-2" id="labelTerpilih">0 santri dipilih</span>

                <!-- Shortcut Set Kelas Tujuan Sekaligus -->
                <div class="ml-auto d-flex align-items-center mb-2">
                    <label class="mr-2 mb-0 font-weight-bold small text-muted">
                        <i class="fas fa-magic text-purple mr-1"></i> Set Kelas Tujuan Terpilih:
                    </label>
                    <select class="form-control form-control-sm mr-2" id="bulkKelasTujuan" style="width: 180px;">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($allClasses as $kelas): ?>
                            <option value="<?= $kelas['IdKelas'] ?>">
                                <?= esc($kelas['NamaKelas']) ?><?= ($kelas['IdKelas'] == 10) ? ' (Lulus)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnApplyBulkKelas" title="Terapkan kelas ke semua santri yang tercentang">
                        <i class="fas fa-check mr-1"></i> Terapkan
                    </button>
                </div>
            </div>

            <!-- Notice khusus Kelulusan Kelas 6 -->
            <div id="alumniNotice" class="alert alert-success py-2 mb-3" style="display:none; font-size:0.9rem;">
                <i class="fas fa-graduation-cap mr-1"></i>
                <strong>Kelulusan Kelas 6:</strong> Santri yang diarahkan ke <strong>ALUMNI (Lulus)</strong> akan otomatis diset statusnya menjadi Alumni (nonaktif / <code>Active = 2</code>) dan <strong>tidak akan dibuatkan kelas baru</strong> di tahun berikutnya.
            </div>

            <!-- Loading -->
            <div id="santriLoading" class="text-center py-5" style="display:none;">
                <div class="spinner-border text-info" role="status" style="width:2.5rem;height:2.5rem;"></div>
                <p class="mt-3 text-muted">Memuat data santri...</p>
            </div>

            <!-- Tabel santri -->
            <div id="santriTableWrap" style="display:none;">
                <div style="max-height:450px; overflow-y:auto; border:1px solid #dee2e6; border-radius:4px;">
                    <table class="table table-sm table-bordered table-hover mb-0" id="tableSantri">
                        <thead class="thead-dark" style="position:sticky;top:0;z-index:1;">
                            <tr>
                                <th style="width:38px;text-align:center;">
                                    <input type="checkbox" id="checkAll" title="Pilih / Batal semua">
                                </th>
                                <th style="width:40px;text-align:center;">No</th>
                                <th style="width:100px;text-align:center;">Id Santri</th>
                                <th>Nama Santri</th>
                                <?php if ($isAdmin): ?>
                                    <th>TPQ / Lembaga</th>
                                <?php endif; ?>
                                <th style="width:220px;">Kelas Tujuan</th>
                            </tr>
                        </thead>
                        <tbody id="santriBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Kosong -->
            <div id="santriKosong" class="text-center py-5" style="display:none;">
                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted mb-0">Tidak ada santri aktif di kelas dan filter yang dipilih.</p>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         LANGKAH 3 — Proses
    ══════════════════════════════════════════════════════════ -->
    <div class="card card-warning card-outline" id="cardProses" style="display:none;">
        <div class="card-body text-center py-4">
            <p class="text-muted mb-3" id="warningProsesText">
                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                Proses ini akan memperbarui kelas santri yang dipilih.
                Pastikan santri dan kelas tujuan sudah benar.
            </p>
            <button type="button" class="btn btn-lg btn-warning px-5" id="btnProses">
                <i class="fas fa-check-circle mr-2"></i>
                <span id="btnProsesLabel">Proses Perubahan Kelas</span>
                (<span id="labelJumlahProses">0</span> Santri)
            </button>
        </div>
    </div>

</div><!-- /col-12 -->

<!-- ── Hidden data untuk JS ─────────────────────────────────── -->
<span id="jsData"
      data-url-ajax="<?= base_url('backend/kelas/getSantriListAjax') ?>"
      data-url-proses="<?= base_url('backend/kelas/prosesNaikKelasManual') ?>"
      data-url-list="<?= base_url('backend/kelas/showListSantriPerKelas') ?>"
      data-url-manual="<?= base_url('backend/kelas/showNaikKelasManual') ?>"
      data-url-check-sync="<?= base_url('backend/kelas/checkSinkronisasiIdKelas') ?>"
      data-url-proses-sync="<?= base_url('backend/kelas/prosesSinkronisasiIdKelas') ?>"
      data-is-admin="<?= $isAdmin ? '1' : '0' ?>"
      data-id-tpq-session="<?= esc($idTpqSession ?? '') ?>"
      data-csrf-name="<?= csrf_token() ?>"
      data-csrf-hash="<?= csrf_hash() ?>"
      data-all-ta='<?= json_encode($tahunAjaranList) ?>'
      data-classes='<?= json_encode(array_values($allClasses)) ?>'>
</span>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Konfigurasi dari PHP ───────────────────────────────── */
    const jd            = document.getElementById('jsData');
    const urlAjax       = jd.dataset.urlAjax;
    const urlProses     = jd.dataset.urlProses;
    const urlList       = jd.dataset.urlList;
    const urlManual     = jd.dataset.urlManual;
    const isAdmin       = jd.dataset.isAdmin === '1';
    const idTpqSession  = jd.dataset.idTpqSession;
    const classes       = JSON.parse(jd.dataset.classes || '[]');
    const allTaList     = JSON.parse(jd.dataset.allTa || '[]');

    /* ── Inisialisasi Select2 untuk Admin Filter TPQ ───────── */
    if (isAdmin && typeof $.fn.select2 !== 'undefined') {
        $('#selectTpq').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Semua TPQ (atau pilih beberapa)'
        });
    }

    /* ── Mapping kelas ─────────────────────────────────────── */
    const nextMap  = {1:2, 2:3, 3:4, 4:5, 5:6, 6:7, 7:8, 8:9, 9:10};
    const nameMap  = {};
    classes.forEach(c => { nameMap[c.IdKelas] = c.NamaKelas; });
    nameMap[10] = 'ALUMNI';

    function getNextId(id)   { return nextMap[parseInt(id)] ?? 10; }
    function getName(id)     { return nameMap[parseInt(id)] ?? 'ALUMNI'; }
    function fmtTA(ta)       { return (ta && ta.length === 8) ? ta.substring(0,4)+'/'+ta.substring(4) : (ta || '—'); }
    function getNextTA(ta) {
        if (!ta || ta.length !== 8) return '';
        return String(parseInt(ta.substring(0,4))+1) + String(parseInt(ta.substring(4))+1);
    }
    function esc(s) {
        return String(s??'')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Elemen DOM ────────────────────────────────────────── */
    const selTA          = document.getElementById('selectTahunAjaran');
    const selTATarget    = document.getElementById('selectTahunAjaranTarget');
    const selKelas       = document.getElementById('selectKelas');
    const btnLoad        = document.getElementById('btnLoadSantri');
    const taTargetHint   = document.getElementById('taTargetHint');

    const cardPilih      = document.getElementById('cardPilihSantri');
    const cardProses     = document.getElementById('cardProses');
    const loading        = document.getElementById('santriLoading');
    const tblWrap        = document.getElementById('santriTableWrap');
    const tblBody        = document.getElementById('santriBody');
    const kosong         = document.getElementById('santriKosong');
    const checkAll       = document.getElementById('checkAll');

    const badgeTotal      = document.getElementById('badgeTotal');
    const labelTerpilih   = document.getElementById('labelTerpilih');
    const labelJumlah     = document.getElementById('labelJumlahProses');
    const infoTpqFilter   = document.getElementById('infoTpqFilter');
    const infoKelasAsal   = document.getElementById('infoKelasAsal');
    const infoTASumber    = document.getElementById('infoTASumber');
    const infoTATarget    = document.getElementById('infoTATarget');
    const btnProsesLabel  = document.getElementById('btnProsesLabel');

    const bulkKelasTujuan = document.getElementById('bulkKelasTujuan');
    const btnApplyBulk    = document.getElementById('btnApplyBulkKelas');

    /* ── Helper Ambil Nilai Filter TPQ ──────────────────────── */
    function getSelectedTpqList() {
        if (!isAdmin) {
            return idTpqSession ? [idTpqSession] : [];
        }
        const val = $('#selectTpq').val();
        if (!val || val.length === 0) {
            return []; // Kosong = Semua TPQ
        }
        return Array.isArray(val) ? val : [val];
    }

    /* ── Populate Dropdown Target Tahun Ajaran ─────────────── */
    function populateTATarget(selectedSourceTA, preselectedTarget) {
        if (!selectedSourceTA) {
            selTATarget.innerHTML = '<option value="">-- Pilih TA Sumber Dahulu --</option>';
            selTATarget.disabled = true;
            return;
        }

        const nextTaVal = getNextTA(selectedSourceTA);
        let opts = '';

        // Opsi 1: Tahun Ajaran SAMA (Koreksi / Pindah Kelas) -> DEFAULT!
        const isSelSame = (!preselectedTarget || preselectedTarget === selectedSourceTA);
        opts += `<option value="${selectedSourceTA}" ${isSelSame ? 'selected' : ''}>
                    ${fmtTA(selectedSourceTA)} — Tahun Ajaran Sama (Koreksi / Pindah Kelas)
                 </option>`;

        // Opsi 2: Tahun Ajaran BERIKUTNYA (Kenaikan Kelas Baru)
        if (nextTaVal) {
            const isSelNext = (preselectedTarget === nextTaVal);
            opts += `<option value="${nextTaVal}" ${isSelNext ? 'selected' : ''}>
                        ${fmtTA(nextTaVal)} — Tahun Ajaran Berikutnya (Kenaikan Kelas Baru)
                     </option>`;
        }

        // Opsi lainnya dari list tahun ajaran jika ada
        allTaList.forEach(ta => {
            if (ta !== selectedSourceTA && ta !== nextTaVal) {
                const isSel = (preselectedTarget === ta);
                opts += `<option value="${ta}" ${isSel ? 'selected' : ''}>${fmtTA(ta)}</option>`;
            }
        });

        selTATarget.innerHTML = opts;
        selTATarget.disabled = false;
        updateTargetHint();
    }

    function updateTargetHint() {
        const src = selTA.value;
        const tgt = selTATarget.value;
        if (!src || !tgt) return;

        if (src === tgt) {
            taTargetHint.innerHTML = '<span class="text-info font-weight-bold"><i class="fas fa-info-circle mr-1"></i>Mode: TA Sama</span> (pindah/koreksi kelas santri di TA ini)';
            btnProsesLabel.textContent = 'Proses Perubahan Kelas (TA Sama)';
        } else {
            taTargetHint.innerHTML = '<span class="text-success font-weight-bold"><i class="fas fa-level-up-alt mr-1"></i>Mode: Kenaikan Kelas</span> (santri naik ke TA target)';
            btnProsesLabel.textContent = 'Proses Kenaikan Kelas (TA Baru)';
        }
    }

    /* ── Enable tombol load ─────────────────────────────────── */
    function checkLoad() {
        btnLoad.disabled = !(selTA.value && selKelas.value && selTATarget.value);
    }

    selTA.addEventListener('change', function () {
        populateTATarget(this.value, null);
        checkLoad();
    });
    selTATarget.addEventListener('change', function () {
        updateTargetHint();
        checkLoad();
    });
    selKelas.addEventListener('change', checkLoad);

    /* ── Build options kelas tujuan ─────────────────────────── */
    function buildOpts(selectedId) {
        return classes.map(c => {
            const sel = c.IdKelas == selectedId ? 'selected' : '';
            const label = (c.IdKelas == 10) ? 'ALUMNI (Lulus)' : esc(c.NamaKelas);
            return `<option value="${c.IdKelas}" ${sel}>${label}</option>`;
        }).join('');
    }

    /* ── Update counter terpilih ────────────────────────────── */
    function updateCount() {
        const total   = tblBody.querySelectorAll('.cb').length;
        const checked = tblBody.querySelectorAll('.cb:checked').length;
        labelTerpilih.textContent = `${checked} santri dipilih`;
        labelJumlah.textContent   = checked;
        checkAll.checked          = total > 0 && checked === total;
        checkAll.indeterminate    = checked > 0 && checked < total;
    }

    /* ── Render tabel santri ────────────────────────────────── */
    function renderTable(list, defaultTargetId) {
        let html = '';
        list.forEach((s, i) => {
            const opts = buildOpts(defaultTargetId);
            const tpqCol = isAdmin
                ? `<td class="align-middle">${esc(s.NamaTpq || '—')}</td>`
                : '';
            html += `
            <tr>
                <td class="text-center align-middle">
                    <input type="checkbox" class="cb" value="${s.IdSantri}" checked>
                </td>
                <td class="text-center align-middle">${i+1}</td>
                <td class="text-center align-middle font-weight-bold text-muted">${esc(s.IdSantri)}</td>
                <td class="align-middle font-weight-bold">${esc(s.NamaSantri || 'Tanpa Nama')}</td>
                ${tpqCol}
                <td>
                    <select class="form-control form-control-sm sel-tujuan"
                            data-id="${s.IdSantri}">${opts}</select>
                </td>
            </tr>`;
        });
        tblBody.innerHTML = html;
        tblBody.querySelectorAll('.cb').forEach(cb => cb.addEventListener('change', updateCount));
        updateCount();
    }

    /* ── Load santri via AJAX ───────────────────────────────── */
    btnLoad.addEventListener('click', function () {
        const ta       = selTA.value;
        const taTarget = selTATarget.value;
        const kelas    = selKelas.value;
        if (!ta || !kelas || !taTarget) return;

        const isSameTA = (ta === taTarget);
        const isKelas6 = (parseInt(kelas) === 9);
        const defaultTargetId = isKelas6 ? 10 : getNextId(kelas);

        if (isKelas6) {
            $('#alumniNotice').slideDown();
            btnProsesLabel.textContent = 'Proses Kelulusan Santri (Alumni)';
        } else {
            $('#alumniNotice').slideUp();
        }

        // Ambil filter TPQ
        const tpqList = getSelectedTpqList();
        let tpqParam = '';
        if (isAdmin) {
            if (tpqList.length > 0) {
                tpqParam = '?tpq=' + encodeURIComponent(tpqList.join(','));
                infoTpqFilter.textContent = `${tpqList.length} TPQ Terpilih`;
            } else {
                infoTpqFilter.textContent = 'Semua TPQ';
            }
        } else {
            const tpqName = $('#selectTpq option:selected').text().trim();
            infoTpqFilter.textContent = tpqName || 'TPQ Anda';
        }

        infoKelasAsal.textContent = getName(parseInt(kelas));
        infoTASumber.textContent  = fmtTA(ta);
        infoTATarget.innerHTML    = fmtTA(taTarget) + (isSameTA ? ' <span class="badge badge-info ml-1">TA Sama</span>' : ' <span class="badge badge-success ml-1">TA Baru</span>');

        updateTargetHint();

        // Tampilkan card, sembunyikan card proses
        cardPilih.style.display  = '';
        cardProses.style.display = 'none';
        loading.style.display    = '';
        tblWrap.style.display    = 'none';
        kosong.style.display     = 'none';
        tblBody.innerHTML        = '';
        updateCount();

        const fetchUrl = `${urlAjax}/${ta}/${kelas}${tpqParam}`;

        fetch(fetchUrl)
            .then(r => r.json())
            .then(res => {
                loading.style.display = 'none';
                if (res.status !== 'success' || !res.data || res.data.length === 0) {
                    kosong.style.display = '';
                    badgeTotal.textContent = '0 santri';
                    return;
                }
                badgeTotal.textContent = `${res.data.length} santri`;
                renderTable(res.data, defaultTargetId);
                tblWrap.style.display    = '';
                cardProses.style.display = '';
            })
            .catch(() => {
                loading.style.display = 'none';
                Swal.fire('Error', 'Gagal memuat data santri. Periksa koneksi Anda.', 'error');
            });
    });

    /* ── Tombol Terapkan Kelas Sekaligus ─────────────────────── */
    btnApplyBulk.addEventListener('click', function () {
        const targetClassVal = bulkKelasTujuan.value;
        if (!targetClassVal) {
            Swal.fire('Pilih Kelas', 'Silakan pilih kelas tujuan terlebih dahulu pada dropdown di samping.', 'info');
            return;
        }

        const checkedBoxes = tblBody.querySelectorAll('.cb:checked');
        if (checkedBoxes.length === 0) {
            Swal.fire('Perhatian', 'Pilih minimal 1 santri dengan mencentang checkbox untuk menerapkan kelas.', 'warning');
            return;
        }

        let appliedCount = 0;
        checkedBoxes.forEach(cb => {
            const idSantri = cb.value;
            const selRow = tblBody.querySelector(`.sel-tujuan[data-id="${idSantri}"]`);
            if (selRow) {
                selRow.value = targetClassVal;
                appliedCount++;
            }
        });

        const targetClassName = getName(parseInt(targetClassVal));
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Kelas tujuan ${appliedCount} santri diubah ke ${targetClassName}`,
            showConfirmButton: false,
            timer: 2000
        });
    });

    /* ── Pilih semua / batal semua ──────────────────────────── */
    document.getElementById('btnPilihSemua').addEventListener('click', () => {
        tblBody.querySelectorAll('.cb').forEach(cb => cb.checked = true);
        updateCount();
    });
    document.getElementById('btnBatalSemua').addEventListener('click', () => {
        tblBody.querySelectorAll('.cb').forEach(cb => cb.checked = false);
        updateCount();
    });
    checkAll.addEventListener('change', () => {
        tblBody.querySelectorAll('.cb').forEach(cb => cb.checked = checkAll.checked);
        updateCount();
    });

    /* ── Proses kenaikan / perubahan kelas ──────────────────── */
    document.getElementById('btnProses').addEventListener('click', function () {
        const ta       = selTA.value;
        const taTarget = selTATarget.value;
        const kelas    = selKelas.value;

        // Kumpulkan santri yang dicentang beserta kelas tujuan masing-masing
        const payload = [];
        tblBody.querySelectorAll('.cb:checked').forEach(cb => {
            const idSantri = cb.value;
            const selEl    = tblBody.querySelector(`.sel-tujuan[data-id="${idSantri}"]`);
            payload.push({
                IdSantri:      idSantri,
                IdKelasTujuan: selEl ? selEl.value : String(getNextId(kelas))
            });
        });

        if (payload.length === 0) {
            Swal.fire('Perhatian', 'Pilih minimal 1 santri untuk diproses.', 'warning');
            return;
        }

        const isSameTA = (ta === taTarget);
        const totalAlumni = payload.filter(p => p.IdKelasTujuan == 10).length;
        const isAllAlumni = (totalAlumni === payload.length && totalAlumni > 0);

        let modalTitle = isSameTA
            ? 'Konfirmasi Perubahan Kelas (Tahun Ajaran Sama)'
            : 'Konfirmasi Kenaikan Kelas (Tahun Ajaran Baru)';

        if (isAllAlumni) {
            modalTitle = 'Konfirmasi Kelulusan Santri (Alumni)';
        }

        let modeBadge = isSameTA
            ? '<span class="badge badge-info">Tahun Ajaran Sama</span>'
            : '<span class="badge badge-success">Tahun Ajaran Baru</span>';

        if (isAllAlumni) {
            modeBadge = '<span class="badge badge-primary"><i class="fas fa-graduation-cap mr-1"></i>Kelulusan (Alumni)</span>';
        }

        let warningMsg = isSameTA
            ? 'Data kelas dan nilai materi santri pada tahun ajaran ini akan disesuaikan dengan kelas baru.'
            : 'Santri akan dipromosikan ke tahun ajaran target. Data lama di tahun ajaran target akan ditimpa jika sudah ada.';

        let alumniAlertHtml = '';
        if (totalAlumni > 0) {
            alumniAlertHtml = `
                <div class="alert alert-success py-2 mb-2" style="font-size:.85rem;">
                    <i class="fas fa-graduation-cap mr-1"></i>
                    Sebanyak <strong>${totalAlumni} santri</strong> akan dinyatakan <strong>Lulus / Alumni</strong> (Active = 2). Santri alumni <strong>tidak dibuatkan kelas baru</strong> di tahun berikutnya.
                </div>`;
        }

        Swal.fire({
            title: modalTitle,
            html: `
                <div class="text-left" style="font-size:.9rem;">
                    ${alumniAlertHtml}
                    <p class="mb-2">Anda akan memproses perubahan kelas dengan detail berikut:</p>
                    <table class="table table-sm table-bordered mb-3">
                        <tr>
                            <th class="bg-light" style="width:40%">Mode</th>
                            <td>${modeBadge}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Kelas Sumber</th>
                            <td><strong>${esc(getName(parseInt(kelas)))}</strong></td>
                        </tr>
                        <tr>
                            <th class="bg-light">TA Sumber</th>
                            <td>${fmtTA(ta)}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">TA Target</th>
                            <td><strong>${fmtTA(taTarget)}</strong></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Jumlah Santri</th>
                            <td><strong class="text-primary" style="font-size:1.1rem;">${payload.length} santri</strong> ${totalAlumni > 0 ? `(${totalAlumni} Alumni)` : ''}</td>
                        </tr>
                    </table>
                    <div class="alert alert-warning mb-0 py-2" style="font-size:.82rem;">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        ${warningMsg}
                    </div>
                </div>`,
            icon: isAllAlumni ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isAllAlumni ? '#28a745' : '#f39c12',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  isAllAlumni ? '<i class="fas fa-graduation-cap mr-1"></i> Ya, Luluskan Santri!' : '<i class="fas fa-check mr-1"></i> Ya, Proses Sekarang!',
            cancelButtonText:   'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;

            // Loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Harap tunggu, sedang memproses penyesuaian kelas.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const body = {
                idTahunAjaran:       ta,
                idTahunAjaranTarget: taTarget,
                idKelas:             kelas,
                santriList:          payload,
            };
            body[jd.dataset.csrfName] = jd.dataset.csrfHash;

            fetch(urlProses, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(body)
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    let extraHtml = '';
                    if (res.data && res.data.total_lulus > 0) {
                        extraHtml += `<div class="alert alert-success py-2 mt-2 mb-1" style="font-size:.85rem;">
                            <i class="fas fa-graduation-cap mr-1"></i>
                            <strong>${res.data.total_lulus} santri</strong> dinyatakan <strong>Lulus / Alumni</strong> (Active = 2).
                        </div>`;
                    }
                    if (res.data && res.data.total_ditimpa > 0) {
                        extraHtml += `<p class="text-muted mt-2 mb-0" style="font-size:.85rem;">
                            <i class="fas fa-sync-alt mr-1"></i>
                            ${res.data.total_ditimpa} data ditimpa karena sudah ada di TA target.
                        </p>`;
                    }
                    if (res.data && res.data.errors && res.data.errors.length > 0) {
                        extraHtml += `<p class="text-danger mt-1 mb-0" style="font-size:.82rem;">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Gagal: ${res.data.errors.slice(0,3).join('; ')}
                        </p>`;
                    }

                    Swal.fire({
                        title: 'Berhasil!',
                        html:  `<p>${esc(res.message)}</p>${extraHtml}`,
                        icon:  'success',
                        showCancelButton:   true,
                        confirmButtonText:  '<i class="fas fa-list mr-1"></i> Lihat List Per Kelas',
                        cancelButtonText:   '<i class="fas fa-redo mr-1"></i> Tetap di Halaman',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor:  '#6c757d',
                        reverseButtons: true,
                    }).then(r2 => {
                        if (r2.isConfirmed) {
                            window.location.href = `${urlList}/${res.data.id_ta_target || taTarget}`;
                        } else {
                            // Pertahankan parameter TPQ jika ada
                            let tpqParamStr = '';
                            if (isAdmin) {
                                const selectedTpq = getSelectedTpqList();
                                if (selectedTpq.length > 0) {
                                    tpqParamStr = '&tpq=' + encodeURIComponent(selectedTpq.join(','));
                                }
                            }
                            window.location.href = `${urlManual}?ta=${ta}&ta_target=${taTarget}&kelas=${kelas}${tpqParamStr}`;
                        }
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan saat memproses.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem. Silakan coba lagi.', 'error');
            });
        });
    });

    /* ── Fitur Cek & Sinkronisasi IdKelas Otomatis ─────────── */
    const urlCheckSync  = jd.dataset.urlCheckSync;
    const urlProsesSync = jd.dataset.urlProsesSync;
    const btnSync       = document.getElementById('btnSinkronisasiOtomatis');

    if (btnSync) {
        btnSync.addEventListener('click', function () {
            const ta = selTA.value || null;
            const selectedTpq = getSelectedTpqList();

            Swal.fire({
                title: 'Memeriksa Keselarasan Data...',
                text: 'Harap tunggu, sedang mendeteksi santri yang kelasnya belum sinkron.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const checkBody = {
                idTahunAjaran: ta,
                tpq: selectedTpq
            };
            checkBody[jd.dataset.csrfName] = jd.dataset.csrfHash;

            fetch(urlCheckSync, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(checkBody)
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Gagal memeriksa data.', 'error');
                    return;
                }

                if (res.total === 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Semua Data Sudah Sinkron!',
                        html: `<p class="mb-0">Tidak ditemukan santri yang IdKelas di data induk (<code>tbl_santri_baru</code>) berbeda dengan kelas aktif di riwayat akademik (<code>tbl_kelas_santri</code>) pada TA <strong>${fmtTA(res.id_tahun_ajaran)}</strong>.</p>`,
                        confirmButtonText: 'Bagus'
                    });
                    return;
                }

                // Render tabel santri yang belum sinkron
                let rowsHtml = '';
                res.data.forEach((s, idx) => {
                    const tpqCell = isAdmin ? `<td>${esc(s.NamaTpq || '—')}</td>` : '';
                    rowsHtml += `
                    <tr>
                        <td class="text-center align-middle">
                            <input type="checkbox" class="cb-sync" value="${s.IdSantri}" data-target-class="${s.IdKelasAktif}" checked>
                        </td>
                        <td class="text-center align-middle">${idx + 1}</td>
                        <td class="text-center align-middle font-weight-bold text-muted">${esc(s.IdSantri)}</td>
                        <td class="align-middle font-weight-bold">${esc(s.NamaSantri || 'Tanpa Nama')}</td>
                        ${tpqCell}
                        <td class="text-center align-middle">
                            <span class="badge badge-secondary py-1 px-2">${esc(s.NamaKelasInduk || 'Belum Ada')}</span>
                        </td>
                        <td class="text-center align-middle text-primary font-weight-bold">➔</td>
                        <td class="text-center align-middle">
                            <span class="badge badge-success py-1 px-2">${esc(s.NamaKelasAktif || 'Belum Ada')}</span>
                        </td>
                    </tr>`;
                });

                const syncModalHtml = `
                    <div class="text-left" style="font-size:0.88rem;">
                        <p class="mb-2">Ditemukan <strong class="text-danger">${res.total} santri</strong> yang <code>IdKelas</code> di data induk (<strong>tbl_santri_baru</strong>) belum sama dengan kelas aktif di <strong>tbl_kelas_santri</strong> pada TA <strong>${fmtTA(res.id_tahun_ajaran)}</strong>.</p>
                        
                        <div class="d-flex align-items-center mb-2">
                            <small class="text-muted"><span id="syncSelectedCount">${res.total}</span> santri dipilih untuk disinkronkan</small>
                        </div>

                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                            <table class="table table-sm table-bordered table-hover mb-0" style="font-size:0.82rem;">
                                <thead class="thead-light" style="position:sticky; top:0; z-index:1;">
                                    <tr>
                                        <th style="width:30px; text-align:center;">
                                            <input type="checkbox" id="checkAllSync" checked>
                                        </th>
                                        <th style="width:35px; text-align:center;">No</th>
                                        <th style="width:85px; text-align:center;">Id Santri</th>
                                        <th>Nama Santri</th>
                                        ${isAdmin ? '<th>TPQ</th>' : ''}
                                        <th class="text-center">Data Induk (Lama)</th>
                                        <th class="text-center" style="width:25px;">➔</th>
                                        <th class="text-center">Kelas Aktif (Baru)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rowsHtml}
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info py-2 mt-2 mb-0" style="font-size:0.83rem;">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Persetujuan Sinkronisasi:</strong> Menyetujui akan memperbarui <code>tbl_santri_baru.IdKelas</code> agar sama persis mengikuti <code>tbl_kelas_santri.IdKelas</code> yang aktif.
                        </div>
                    </div>`;

                Swal.fire({
                    title: 'Persetujuan Sinkronisasi IdKelas',
                    html: syncModalHtml,
                    width: '820px',
                    showCancelButton: true,
                    cancelButtonColor: '#6c757d',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: '<i class="fas fa-check-double mr-1"></i> Setuju & Sinkronkan Sekarang!',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        const modal = Swal.getHtmlContainer();
                        const checkAllSyncEl = modal.querySelector('#checkAllSync');
                        const countEl = modal.querySelector('#syncSelectedCount');
                        const itemBoxes = modal.querySelectorAll('.cb-sync');

                        function updateSyncCount() {
                            const checked = modal.querySelectorAll('.cb-sync:checked').length;
                            countEl.textContent = checked;
                            checkAllSyncEl.checked = checked === itemBoxes.length && checked > 0;
                            checkAllSyncEl.indeterminate = checked > 0 && checked < itemBoxes.length;
                        }

                        checkAllSyncEl.addEventListener('change', function () {
                            itemBoxes.forEach(cb => cb.checked = checkAllSyncEl.checked);
                            updateSyncCount();
                        });

                        itemBoxes.forEach(cb => cb.addEventListener('change', updateSyncCount));
                    }
                }).then(confirmRes => {
                    if (!confirmRes.isConfirmed) return;

                    const checkedItems = [];
                    document.querySelectorAll('.cb-sync:checked').forEach(cb => {
                        checkedItems.push({
                            IdSantri: cb.value,
                            IdKelasAktif: cb.getAttribute('data-target-class')
                        });
                    });

                    if (checkedItems.length === 0) {
                        Swal.fire('Perhatian', 'Tidak ada santri yang dicentang untuk disinkronkan.', 'warning');
                        return;
                    }

                    Swal.fire({
                        title: 'Menyinkronkan Data...',
                        text: 'Sedang memperbarui IdKelas santri...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const prosesBody = {
                        santri: checkedItems,
                        idTahunAjaran: res.id_tahun_ajaran
                    };
                    prosesBody[jd.dataset.csrfName] = jd.dataset.csrfHash;

                    fetch(urlProsesSync, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(prosesBody)
                    })
                    .then(r => r.json())
                    .then(syncRes => {
                        if (syncRes.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sinkronisasi Berhasil!',
                                html: `<p>${esc(syncRes.message)}</p><p class="text-muted small mb-0">Sekarang IdKelas di data induk santri telah selaras dengan kelas santri aktif.</p>`,
                                confirmButtonText: 'Selesai'
                            }).then(() => {
                                // Jika sedang menampilkan santri, reload data
                                if (!btnLoad.disabled && tblWrap.style.display !== 'none') {
                                    btnLoad.click();
                                }
                            });
                        } else {
                            Swal.fire('Gagal', syncRes.message || 'Gagal memproses sinkronisasi.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan sistem saat sinkronisasi.', 'error');
                    });
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal menghubungi server untuk pemeriksaan.', 'error');
            });
        });
    }

    /* ── Auto-load jika ada query param ─────────────────────── */
    const params = new URLSearchParams(window.location.search);
    const paramTA       = params.get('ta');
    const paramTATarget = params.get('ta_target');
    const paramKelas    = params.get('kelas');
    const paramTpq      = params.get('tpq');

    if (paramTpq && isAdmin) {
        const tpqArr = paramTpq.split(',');
        $('#selectTpq').val(tpqArr).trigger('change');
    }

    if (paramTA) {
        selTA.value = paramTA;
        populateTATarget(paramTA, paramTATarget);
        if (paramKelas) {
            selKelas.value = paramKelas;
        }
        checkLoad();
        if (!btnLoad.disabled) {
            setTimeout(() => btnLoad.click(), 400);
        }
    }
});
</script>

<?= $this->endSection(); ?>
