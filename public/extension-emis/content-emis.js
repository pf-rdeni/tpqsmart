/**
 * TPQ Smart - EMIS 4.0 Bridge
 * Content Script for emis.kemenag.go.id
 * 
 * Alur Kerja Semi-Otomatis:
 * Step 1:
 *  - Memilih radio "Nama, Tempat, dan tanggal lahir" secara spesifik
 *  - Mengisi Nama Lengkap & Tempat Lahir santri
 *  - Menyalin Tanggal Lahir ke clipboard
 *  - Popup Step 1 menyediakan tombol salin cepat untuk NIK, Nama Santri, Tempat Lahir, dan Tanggal Lahir
 *  - Popup Step 1 otomatis tertutup saat klik SELANJUTNYA dan dapat digeser (draggable)
 * 
 * Step 2:
 *  - Fokus mengisi otomatis:
 *    1. NIK Calon Santri (atau otomatis centang [✓] Belum Punya NIK)
 *    2. No Handphone Calon Santri (atau otomatis centang [✓] Tidak memiliki nomor handphone)
 *    3. Nama Lengkap Ayah Kandung
 *    4. Nama Lengkap Ibu Kandung
 *  - Proteksi mutlak agar tidak pernah menimpa field Calon Santri
 *  - Popup Step 2 dapat digeser (draggable) dan memandu checklist isian manual
 */

console.log('[TPQ-EMIS Bridge] Content script aktif di EMIS Kemenag.');

// Inisialisasi pengawas transisi otomatis dari Step 1 ke Step 2
initStepTransitionWatcher();

// Listener global: jika tombol SELANJUTNYA/LANJUT diklik di Step 1, langsung tutup popup Step 1
document.addEventListener('click', (e) => {
    const target = e.target;
    if (!target) return;
    const btn = target.closest('button, [role="button"], a, input[type="button"], input[type="submit"]') || target;
    const txt = (btn.innerText || btn.textContent || btn.value || '').trim().toUpperCase();
    if (txt.includes('SELANJUT') || txt.includes('LANJUT') || txt.includes('NEXT')) {
        // PERHATIAN: JANGAN tutup popup Step 0 di sini!
        // Di Step 0, user mencoba cari NIK dan mengklik tombol Lanjut/Cari di EMIS.
        // Jika hasil cek NIK tidak ada data, popup Step 0 HARUS tetap terbuka
        // agar user bisa langsung berpindah ke Opsi 2 (Step 1) tanpa perlu refresh halaman.
        console.log('[TPQ-EMIS Bridge] Tombol navigasi formulir diklik di EMIS...');
        const step1Alert = document.getElementById('tpq-step1-alert');
        if (step1Alert) step1Alert.remove();
    }
}, true);

// Cek antrean data santri saat halaman pertama kali dibuka / direload
chrome.storage.local.get(['pendingSantriData', 'waitingForStep2', 'pendingUpdateAlamatData', 'currentTask', 'waitingForUpdateStep2', 'waitingForUpdateStep3'], (result) => {
    if (result && result.currentTask === 'UPDATE_ALAMAT' && result.pendingUpdateAlamatData) {
        console.log('[TPQ-EMIS Bridge] Antrean UPDATE ALAMAT terdeteksi:', result.pendingUpdateAlamatData.nama_santri);
        initUpdateAlamatProcess(result.pendingUpdateAlamatData);
    } else if (result && result.pendingSantriData) {
        const santri = result.pendingSantriData;
        console.log('[TPQ-EMIS Bridge] Ditemukan data santri dalam antrean:', santri.nama_santri);
        
        const currentStep = detectCurrentEmisStep();
        console.log('[TPQ-EMIS Bridge] Posisi halaman saat inisialisasi: Step', currentStep);

        if (currentStep === 2) {
            console.log('[TPQ-EMIS Bridge] Form utama (Step 2) terdeteksi, mengisi NIK, HP & Nama Ortu...');
            fillEmisStep2(santri);
            chrome.storage.local.remove(['waitingForStep2']);
        } else {
            console.log('[TPQ-EMIS Bridge] Menampilkan popup start pengisian (Step 0 / Step 1)...');
            showStep0StartPopup(santri);
        }
    }
});

// Listener pesan langsung dari background worker ekstensi
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request && request.action === 'EXECUTE_UPDATE_ALAMAT') {
        const santriData = request.data;
        console.log('[TPQ-EMIS Bridge] Menerima instruksi UPDATE ALAMAT untuk:', santriData.nama_santri);
        initUpdateAlamatProcess(santriData);
        sendResponse({ success: true, action: 'init_update_alamat' });
        return true;
    }

    if (request && request.action === 'EXECUTE_FILL_FORM') {
        const santriData = request.data;
        console.log('[TPQ-EMIS Bridge] Menerima instruksi pengisian untuk:', santriData.nama_santri);
        
        const currentStep = detectCurrentEmisStep();
        console.log('[TPQ-EMIS Bridge] Langkah halaman EMIS saat ini: Step', currentStep);

        if (currentStep === 1) {
            // Tampilkan popup start (Step 0 / Step 1) di bagian atas agar user dapat memilih metode dengan akurat
            showStep0StartPopup(santriData);
            sendResponse({ success: true, step: 'step0_popup' });
        } else if (currentStep === 2) {
            // Jika form masih di posisi Step 2 (santri sebelumnya), muat ulang ke link PPDB baru
            chrome.storage.local.set({ pendingSantriData: santriData, waitingForStep2: false }, () => {
                window.location.href = "https://emis.kemenag.go.id/search-data-ppdb/baru";
            });
            sendResponse({ success: true, action: 'reloading_for_new_santri' });
        } else {
            chrome.storage.local.set({ pendingSantriData: santriData, waitingForStep2: false }, () => {
                window.location.href = "https://emis.kemenag.go.id/search-data-ppdb/baru";
            });
            sendResponse({ success: false, message: 'Mengarahkan ke halaman PPDB Baru' });
        }
        return true;
    }
});

/**
 * Deteksi apakah halaman saat ini adalah Step 1, Step 2, atau halaman lain
 */
function detectCurrentEmisStep() {
    const textContent = (document.body.innerText || document.body.textContent || '').toUpperCase();

    // Ciri Step 2 (Formulir Utama):
    // Ada "TANGGAL MASUK", "KETERANGAN ORANG TUA", "AYAH KANDUNG", atau tombol "SIMPAN"
    const hasTanggalMasuk = textContent.includes('TANGGAL MASUK');
    const hasOrangTua = textContent.includes('KETERANGAN ORANG TUA') || textContent.includes('AYAH KANDUNG');
    const hasSimpanBtn = Array.from(document.querySelectorAll('button')).some(btn => 
        (btn.innerText || btn.textContent || '').trim().toUpperCase().includes('SIMPAN')
    );

    if (hasTanggalMasuk || (hasOrangTua && hasSimpanBtn)) {
        return 2;
    }

    // Ciri Step 1 (Pencarian / Validasi Awal):
    // Ada URL search-data-ppdb, pilihan "BERDASARKAN", "NAMA, TEMPAT, DAN TANGGAL LAHIR", atau tombol "SELANJUTNYA"
    const isPpdbUrl = window.location.href.includes('search-data-ppdb');
    const hasBerdasarkan = textContent.includes('BERDASARKAN');
    const hasOpsiTtl = textContent.includes('NAMA, TEMPAT, DAN TANGGAL LAHIR');
    const hasNextBtn = Array.from(document.querySelectorAll('button')).some(btn => 
        (btn.innerText || btn.textContent || '').trim().toUpperCase().includes('SELANJUTNYA')
    );

    if (isPpdbUrl || hasBerdasarkan || hasOpsiTtl || hasNextBtn) {
        return 1;
    }

    return 0; // Halaman dashboard / daftar
}

/**
 * Pengawas transisi: memantau kapan Step 1 selesai dan Step 2 muncul di layar
 */
function initStepTransitionWatcher() {
    setInterval(() => {
        chrome.storage.local.get(['pendingSantriData', 'waitingForStep2', 'pendingUpdateAlamatData', 'currentTask', 'waitingForUpdateStep2', 'waitingForUpdateStep3'], (res) => {
            if (res && res.currentTask === 'UPDATE_ALAMAT' && res.pendingUpdateAlamatData) {
                const stage = detectUpdateAlamatStage();
                // Transisi dari Step 1 ke Step 2 (Tab DATA SANTRI)
                if (stage === 2 && res.waitingForUpdateStep2) {
                    console.log('[TPQ-EMIS Bridge] Transisi otomatis ke Update Data Step 2 (DATA SANTRI) terdeteksi!');
                    const oldP1 = document.getElementById('tpq-update-alamat-step1-popup');
                    if (oldP1) oldP1.remove();
                    executeStep2DataSantri(res.pendingUpdateAlamatData);
                    chrome.storage.local.set({ waitingForUpdateStep2: false, waitingForUpdateStep3: true });
                }
                // Transisi dari Step 2 ke Step 3 (Tab DATA ALAMAT)
                else if (stage === 3 && res.waitingForUpdateStep3) {
                    console.log('[TPQ-EMIS Bridge] Transisi otomatis ke Update Data Step 3 (DATA ALAMAT) terdeteksi!');
                    const oldP2 = document.getElementById('tpq-update-santri-step2-popup');
                    if (oldP2) oldP2.remove();
                    executeStep3DataAlamat(res.pendingUpdateAlamatData);
                    chrome.storage.local.remove(['waitingForUpdateStep3']);
                }
            } else if (res && res.pendingSantriData) {
                const step = detectCurrentEmisStep();
                if (step === 2 && res.waitingForStep2) {
                    console.log('[TPQ-EMIS Bridge] Transisi otomatis ke Step 2 terdeteksi! Mengisi NIK, HP & Ortu...');
                    const oldStep1 = document.getElementById('tpq-step1-alert');
                    if (oldStep1) oldStep1.remove();
                    const oldStep0 = document.getElementById('tpq-step0-alert');
                    if (oldStep0) oldStep0.remove();
                    const oldPending = document.getElementById('tpq-pending-banner');
                    if (oldPending) oldPending.remove();
                    const oldPill = document.getElementById('tpq-reopen-step0-pill');
                    if (oldPill) oldPill.remove();

                    fillEmisStep2(res.pendingSantriData);
                    chrome.storage.local.remove(['waitingForStep2']);
                }
            }
        });
    }, 500);
}

/**
 * ============================================================================
 * STEP 1: Pencarian & Validasi Awal Calon Santri
 * ============================================================================
 */

/**
 * Memilih radio button 'Nama, Tempat, dan tanggal lahir' secara spesifik
 */
function selectRadioStep1() {
    const radios = Array.from(document.querySelectorAll('input[type="radio"]'));
    for (const r of radios) {
        let text = '';
        if (r.id) {
            const lbl = document.querySelector(`label[for="${r.id}"]`);
            if (lbl) text += ' ' + (lbl.innerText || lbl.textContent || '');
        }
        const parent = r.closest('label') || r.parentElement;
        if (parent) {
            text += ' ' + (parent.innerText || parent.textContent || '');
        }

        const upper = text.toUpperCase();
        if ((upper.includes('TEMPAT') || upper.includes('TANGGAL')) && !upper.includes('NISN')) {
            if (!r.checked) {
                console.log('[TPQ-EMIS Bridge] Memilih radio Nama/TTL:', r);
                r.checked = true;
                r.dispatchEvent(new Event('change', { bubbles: true }));
                r.dispatchEvent(new Event('click', { bubbles: true }));
                if (parent) {
                    try { parent.click(); } catch (_) {}
                }
            }
            return true;
        }
    }

    // Fallback: cari label yang memuat teks "Nama, Tempat, dan tanggal lahir"
    const allLabels = Array.from(document.querySelectorAll('label, span'));
    for (const el of allLabels) {
        const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
        if (txt === 'NAMA, TEMPAT, DAN TANGGAL LAHIR' || (txt.includes('TEMPAT') && txt.includes('TANGGAL LAHIR') && txt.length <= 40)) {
            const r = el.querySelector('input[type="radio"]') || 
                      el.parentElement?.querySelector('input[type="radio"]') ||
                      (el.htmlFor ? document.getElementById(el.htmlFor) : null);
            if (r) {
                r.checked = true;
                r.dispatchEvent(new Event('change', { bubbles: true }));
                r.dispatchEvent(new Event('click', { bubbles: true }));
            }
            try { el.click(); } catch (_) {}
            return true;
        }
    }
    return false;
}

/**
 * Mencari input Nama Lengkap dan Tempat Lahir di Step 1
 */
function findStep1Fields() {
    let inputNama = null;
    let inputTempat = null;

    const allInputs = Array.from(document.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])'))
        .filter(inp => {
            const rect = inp.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0 && !inp.disabled;
        });

    // 1. Identifikasi berdasarkan label atau atribut
    for (const inp of allInputs) {
        const ph = (inp.getAttribute('placeholder') || '').toUpperCase();
        const name = (inp.getAttribute('name') || '').toUpperCase();
        const id = (inp.getAttribute('id') || '').toUpperCase();
        const aria = (inp.getAttribute('aria-label') || '').toUpperCase();

        let labelText = '';
        let parent = inp.parentElement;
        for (let i = 0; i < 4 && parent; i++) {
            const raw = (parent.innerText || parent.textContent || '').trim().toUpperCase();
            if (!raw.includes('KARTU KELUARGA') && !raw.includes('PASTIKAN') && raw.length <= 50) {
                labelText += ' ' + raw;
            }
            parent = parent.parentElement;
        }

        const combined = ph + ' ' + name + ' ' + id + ' ' + aria + ' ' + labelText;

        if (combined.includes('TEMPAT') && !inputTempat) {
            inputTempat = inp;
        } else if (combined.includes('TANGGAL') || combined.includes('DATE')) {
            // Lewati field tanggal lahir
        } else if (combined.includes('NAMA') && !combined.includes('TEMPAT') && !inputNama) {
            inputNama = inp;
        }
    }

    // 2. Fallback posisi geometris: input pertama adalah Nama, kedua Tempat Lahir
    if (!inputNama || !inputTempat) {
        const sorted = [...allInputs].sort((a, b) => {
            const rectA = a.getBoundingClientRect();
            const rectB = b.getBoundingClientRect();
            if (Math.abs(rectA.top - rectB.top) > 20) {
                return rectA.top - rectB.top;
            }
            return rectA.left - rectB.left;
        });

        if (sorted.length >= 2) {
            if (!inputNama) inputNama = sorted[0];
            if (!inputTempat) inputTempat = sorted[1];
        } else if (sorted.length === 1) {
            if (!inputNama) inputNama = sorted[0];
        }
    }

    return { inputNama, inputTempat };
}

function fillEmisStep1(data) {
    console.log('[TPQ-EMIS Bridge] Memulai proses Step 1 untuk:', data.nama_santri);

    let attempts = 0;
    const maxAttempts = 20; // 4 detik

    const timer = setInterval(() => {
        attempts++;
        selectRadioStep1();

        const fields = findStep1Fields();

        if (fields.inputNama && fields.inputTempat) {
            clearInterval(timer);
            console.log('[TPQ-EMIS Bridge] Field Step 1 ditemukan pada iterasi:', attempts);
            eksekusiIsiStep1(fields, data);
        } else if (attempts >= maxAttempts) {
            clearInterval(timer);
            console.warn('[TPQ-EMIS Bridge] Batas waktu Step 1 tercapai, mengeksekusi field yang ada...');
            eksekusiIsiStep1(fields, data);
        }
    }, 200);
}

function eksekusiIsiStep1(fields, data) {
    const { inputNama, inputTempat } = fields;

    // 1. Isi NAMA LENGKAP
    if (inputNama) {
        const valNama = data.nama_santri_clean || data.nama_santri;
        console.log('[TPQ-EMIS Bridge] Mengisi NAMA LENGKAP Step 1:', valNama);
        setNativeValue(inputNama, valNama);
    }

    // 2. Isi TEMPAT LAHIR
    if (inputTempat) {
        const valTempat = data.tempat_lahir_clean || data.tempat_lahir;
        console.log('[TPQ-EMIS Bridge] Mengisi TEMPAT LAHIR Step 1:', valTempat);
        setNativeValue(inputTempat, valTempat);
    }

    // 3. Salin Tanggal Lahir ke clipboard
    if (data.tanggal_lahir) {
        try {
            navigator.clipboard.writeText(data.tanggal_lahir);
            console.log('[TPQ-EMIS Bridge] Tanggal lahir disalin ke clipboard:', data.tanggal_lahir);
        } catch (_) {}
    }

    // 4. Tampilkan floating notification Step 1 (dengan fitur salin NIK, Nama, TTL & draggable)
    showStep1Assistant(data);

    // 5. Simpan state antrean untuk Step 2
    chrome.storage.local.set({
        pendingSantriData: data,
        waitingForStep2: true
    });
}

/**
 * ============================================================================
 * STEP 2: Formulir Utama Santri Baru
 * Otomatis mengisi:
 * 1. NIK Calon Santri (atau mencentang [✓] Belum Punya NIK)
 * 2. No Handphone Calon Santri (atau mencentang [✓] Tidak memiliki nomor handphone)
 * 3. Nama Lengkap Ayah Kandung
 * 4. Nama Lengkap Ibu Kandung
 * ============================================================================
 */

// Helper mencari elemen header / judul seksi terdalam (leaf element)
function findLeafHeading(targetText) {
    const target = targetText.toUpperCase().trim();
    const all = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6, strong, b, div, p, span, label, legend'));
    const matches = all.filter(el => {
        const t = (el.innerText || el.textContent || '').trim().toUpperCase();
        return t === target || (t.startsWith(target) && t.length <= target.length + 10);
    });

    if (matches.length === 0) {
        return all.find(el => {
            const t = (el.innerText || el.textContent || '').trim().toUpperCase();
            return t.includes(target) && t.length <= 30;
        }) || null;
    }

    matches.sort((a, b) => a.querySelectorAll('*').length - b.querySelectorAll('*').length);
    return matches[0];
}

// Helper mencari input pertama setelah elemen referensi dan sebelum elemen pembatas
function findFirstInputAfter(el, stopEl = null) {
    if (!el) return null;
    const allInputs = Array.from(document.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])'))
        .filter(inp => {
            const rect = inp.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0 && !inp.disabled;
        });

    for (const inp of allInputs) {
        if ((el.compareDocumentPosition(inp) & Node.DOCUMENT_POSITION_FOLLOWING) !== 0) {
            if (stopEl && (stopEl.compareDocumentPosition(inp) & Node.DOCUMENT_POSITION_FOLLOWING) !== 0) {
                continue;
            }
            const role = (inp.getAttribute('role') || '').toLowerCase();
            if (role === 'combobox') continue;

            return inp;
        }
    }
    return null;
}

/**
 * Mencari input NIK dan Checkbox 'Belum Punya NIK' di Step 2 (Seksi Keterangan Calon Santri)
 */
function findStep2NikElements() {
    let inputNik = null;
    let checkboxNik = null;

    // 1. Cari checkbox 'Belum Punya NIK' (Mendukung [role="checkbox"] RNW & input[type="checkbox"])
    const checkboxes = Array.from(document.querySelectorAll('[role="checkbox"], input[type="checkbox"]'));
    for (const cb of checkboxes) {
        let labelText = '';
        if (cb.id) {
            const lbl = document.querySelector(`label[for="${cb.id}"]`);
            if (lbl) labelText += ' ' + (lbl.innerText || lbl.textContent || '');
        }
        const parent = cb.closest('.r-1awozwy') || cb.closest('.r-18u37iz') || cb.closest('label') || cb.parentElement;
        if (parent) {
            labelText += ' ' + (parent.innerText || parent.textContent || '');
        }

        const t = labelText.toUpperCase();
        if (t.includes('BELUM') && t.includes('NIK') && !t.includes('NISN')) {
            checkboxNik = cb;
            break;
        }
    }

    // Fallback: Cari langsung elemen label teks "Belum Punya NIK"
    if (!checkboxNik) {
        const allTextEls = Array.from(document.querySelectorAll('.css-901oao, label, span, div')).filter(el => {
            const t = (el.innerText || el.textContent || '').trim().toUpperCase();
            return (t.includes('BELUM') && t.includes('NIK') && !t.includes('NISN') && t.length <= 30);
        });

        for (const tEl of allTextEls) {
            const container = tEl.closest('.r-1awozwy') || tEl.parentElement;
            if (container) {
                const cb = container.querySelector('[role="checkbox"], input[type="checkbox"]');
                checkboxNik = cb || container;
                break;
            }
        }
    }

    // 2. Cari input teks untuk NIK di seksi KETERANGAN CALON SANTRI
    const headingOrtu = findLeafHeading('KETERANGAN ORANG TUA SANTRI') || findLeafHeading('KETERANGAN ORANG TUA') || findLeafHeading('AYAH KANDUNG');

    const allInputs = Array.from(document.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])'))
        .filter(inp => {
            const rect = inp.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0 && !inp.disabled;
        });

    for (const inp of allInputs) {
        // Harus sebelum seksi Orang Tua
        if (headingOrtu && (headingOrtu.compareDocumentPosition(inp) & Node.DOCUMENT_POSITION_FOLLOWING) !== 0) {
            continue;
        }

        const ph = (inp.getAttribute('placeholder') || '').toUpperCase();
        const name = (inp.getAttribute('name') || '').toUpperCase();
        const id = (inp.getAttribute('id') || '').toUpperCase();
        const aria = (inp.getAttribute('aria-label') || '').toUpperCase();

        let labelText = '';
        let p = inp.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            const raw = (p.innerText || p.textContent || '').trim().toUpperCase();
            if (raw.length <= 40) {
                labelText += ' ' + raw;
            }
            p = p.parentElement;
        }

        const combined = ph + ' ' + name + ' ' + id + ' ' + aria + ' ' + labelText;
        if (combined.includes('NIK') && !combined.includes('NISN')) {
            inputNik = inp;
            break;
        }
    }

    // Fallback: jika checkboxNik ditemukan, input NIK biasanya ada di baris container yang sama
    if (!inputNik && checkboxNik) {
        const row = checkboxNik.closest('.row, .ant-row, .form-group, div') || checkboxNik.parentElement?.parentElement;
        if (row) {
            const textInps = Array.from(row.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"])'));
            if (textInps.length > 0) {
                inputNik = textInps[0];
            }
        }
    }

    return { inputNik, checkboxNik };
}

/**
 * Mencari input NISN dan Checkbox 'Belum Punya NISN' di Step 2 (Seksi Keterangan Calon Santri)
 * Mendukung [role="checkbox"] (React Native for Web) dan input[type="checkbox"]
 */
function findStep2NisnElements() {
    let inputNisn = null;
    let checkboxNisn = null;

    // 1. Cari checkbox 'Belum Punya NISN'
    const checkboxes = Array.from(document.querySelectorAll('[role="checkbox"], input[type="checkbox"]'));
    for (const cb of checkboxes) {
        let labelText = '';
        if (cb.id) {
            const lbl = document.querySelector(`label[for="${cb.id}"]`);
            if (lbl) labelText += ' ' + (lbl.innerText || lbl.textContent || '');
        }
        const parent = cb.closest('.r-1awozwy') || cb.closest('.r-18u37iz') || cb.closest('label') || cb.parentElement;
        if (parent) {
            labelText += ' ' + (parent.innerText || parent.textContent || '');
        }

        const t = labelText.toUpperCase();
        if (t.includes('BELUM') && t.includes('NISN')) {
            checkboxNisn = cb;
            break;
        }
    }

    // Fallback: Cari langsung label teks "Belum Punya NISN"
    if (!checkboxNisn) {
        const allTextEls = Array.from(document.querySelectorAll('.css-901oao, label, span, div')).filter(el => {
            const t = (el.innerText || el.textContent || '').trim().toUpperCase();
            return t === 'BELUM PUNYA NISN' || (t.includes('BELUM') && t.includes('NISN') && t.length <= 30);
        });

        for (const tEl of allTextEls) {
            const container = tEl.closest('.r-1awozwy') || tEl.parentElement;
            if (container) {
                const cb = container.querySelector('[role="checkbox"], input[type="checkbox"]');
                checkboxNisn = cb || container;
                break;
            }
        }
    }

    // 2. Cari input teks untuk NISN di seksi KETERANGAN CALON SANTRI
    const headingOrtu = findLeafHeading('KETERANGAN ORANG TUA SANTRI') || findLeafHeading('KETERANGAN ORANG TUA') || findLeafHeading('AYAH KANDUNG');

    const allInputs = Array.from(document.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])'))
        .filter(inp => {
            const rect = inp.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0 && !inp.disabled;
        });

    for (const inp of allInputs) {
        if (headingOrtu && (headingOrtu.compareDocumentPosition(inp) & Node.DOCUMENT_POSITION_FOLLOWING) !== 0) {
            continue;
        }

        const ph = (inp.getAttribute('placeholder') || '').toUpperCase();
        const name = (inp.getAttribute('name') || '').toUpperCase();
        const id = (inp.getAttribute('id') || '').toUpperCase();
        const aria = (inp.getAttribute('aria-label') || '').toUpperCase();

        let labelText = '';
        let p = inp.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            const raw = (p.innerText || p.textContent || '').trim().toUpperCase();
            if (raw.length <= 40) {
                labelText += ' ' + raw;
            }
            p = p.parentElement;
        }

        const combined = ph + ' ' + name + ' ' + id + ' ' + aria + ' ' + labelText;
        if (combined.includes('NISN')) {
            inputNisn = inp;
            break;
        }
    }

    // Fallback: jika checkboxNisn ditemukan, input NISN biasanya ada di baris container yang sama
    if (!inputNisn && checkboxNisn) {
        const row = checkboxNisn.closest('.row, .ant-row, .form-group, .r-18u37iz, div') || checkboxNisn.parentElement?.parentElement;
        if (row) {
            const textInps = Array.from(row.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"])'));
            if (textInps.length > 0) {
                inputNisn = textInps[0];
            }
        }
    }

    return { inputNisn, checkboxNisn };
}

/**
 * Format nomor telepon ke standar internasional EMIS 4.0 (+62...)
 */
function formatEmisPhoneNumber(rawNoHp) {
    if (!rawNoHp) return '';
    let digits = String(rawNoHp).trim().replace(/[^0-9]/g, '');
    if (digits.length < 8) return '';

    if (digits.startsWith('62')) {
        return '+' + digits;
    } else if (digits.startsWith('0')) {
        return '+62' + digits.substring(1);
    } else {
        return '+62' + digits;
    }
}

/**
 * Format tanggal YYYY-MM-DD ke format YYYY-NamaBulan-DD (contoh: 2024-Agustus-18)
 * Memudahkan pemilihan kalender picker di EMIS yang menampilkan nama bulan
 */
function formatTanggalNamaBulan(tanggalStr) {
    if (!tanggalStr) return '-';
    const parts = String(tanggalStr).trim().split('-');
    if (parts.length === 3) {
        const tahun = parts[0];
        const bulanIndex = parseInt(parts[1], 10);
        const hari = parts[2];
        const namaBulan = [
            '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        if (bulanIndex >= 1 && bulanIndex <= 12) {
            return `${tahun}-${namaBulan[bulanIndex]}-${hari}`;
        }
    }
    return tanggalStr;
}

/**
 * Mencari input No Handphone dan Checkbox 'Tidak memiliki nomor handphone' di Step 2
 */
function findStep2HpElements() {
    let inputHp = null;
    let checkboxHp = null;

    // 1. Cari checkbox 'Tidak memiliki nomor handphone' (Mendukung [role="checkbox"] RNW & input[type="checkbox"])
    const checkboxes = Array.from(document.querySelectorAll('[role="checkbox"], input[type="checkbox"]'));
    for (const cb of checkboxes) {
        let labelText = '';
        if (cb.id) {
            const lbl = document.querySelector(`label[for="${cb.id}"]`);
            if (lbl) labelText += ' ' + (lbl.innerText || lbl.textContent || '');
        }
        const parent = cb.closest('.r-1awozwy') || cb.closest('.r-18u37iz') || cb.closest('label') || cb.parentElement;
        if (parent) {
            labelText += ' ' + (parent.innerText || parent.textContent || '');
        }

        const t = labelText.toUpperCase();
        if ((t.includes('TIDAK') || t.includes('BELUM')) && (t.includes('HANDPHONE') || t.includes('HP') || t.includes('NOMOR'))) {
            checkboxHp = cb;
            break;
        }
    }

    // Fallback: Cari langsung elemen label teks jika checkbox belum terhubung
    if (!checkboxHp) {
        const allTextEls = Array.from(document.querySelectorAll('.css-901oao, label, span, div')).filter(el => {
            const t = (el.innerText || el.textContent || '').trim().toUpperCase();
            return (t.includes('TIDAK') || t.includes('BELUM')) && (t.includes('HANDPHONE') || t.includes('HP')) && t.length <= 40;
        });

        for (const tEl of allTextEls) {
            const container = tEl.closest('.r-1awozwy') || tEl.parentElement;
            if (container) {
                const cb = container.querySelector('[role="checkbox"], input[type="checkbox"]');
                checkboxHp = cb || container;
                break;
            }
        }
    }

    // 2. Cari input teks untuk NO HANDPHONE di seksi KETERANGAN CALON SANTRI
    const headingOrtu = findLeafHeading('KETERANGAN ORANG TUA SANTRI') || findLeafHeading('KETERANGAN ORANG TUA') || findLeafHeading('AYAH KANDUNG');

    const allInputs = Array.from(document.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])'))
        .filter(inp => {
            const rect = inp.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0 && !inp.disabled;
        });

    for (const inp of allInputs) {
        // Harus sebelum seksi Orang Tua
        if (headingOrtu && (headingOrtu.compareDocumentPosition(inp) & Node.DOCUMENT_POSITION_FOLLOWING) !== 0) {
            continue;
        }

        const ph = (inp.getAttribute('placeholder') || '').toUpperCase();
        const name = (inp.getAttribute('name') || '').toUpperCase();
        const id = (inp.getAttribute('id') || '').toUpperCase();
        const aria = (inp.getAttribute('aria-label') || '').toUpperCase();

        let labelText = '';
        let p = inp.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            const raw = (p.innerText || p.textContent || '').trim().toUpperCase();
            if (raw.length <= 40) {
                labelText += ' ' + raw;
            }
            p = p.parentElement;
        }

        const combined = ph + ' ' + name + ' ' + id + ' ' + aria + ' ' + labelText;
        if (combined.includes('HANDPHONE') || combined.includes('NO HP') || combined.includes('NOMOR HP') || combined.includes('TELEPON') || ph.includes('+62') || ph.includes('08') || ph.includes('CONTOH: 02') || ph.includes('CONTOH: 08') || ph.includes('CONTOH: +62')) {
            inputHp = inp;
            break;
        }
    }

    // Fallback: jika checkboxHp ditemukan, input HP biasanya tepat setelah checkboxHp dan sebelum headingOrtu
    if (!inputHp && checkboxHp) {
        inputHp = findFirstInputAfter(checkboxHp, headingOrtu);
    }

    return { inputHp, checkboxHp };
}

/**
 * Mencari input untuk Nama Lengkap Ayah Kandung & Ibu Kandung secara akurat
 */
function findOrangTuaInputs(data) {
    let inputAyah = null;
    let inputIbu = null;

    // 1. Cari heading seksi: Ayah Kandung, Ibu Kandung, Wali
    const headingAyah = findLeafHeading('AYAH KANDUNG');
    const headingIbu = findLeafHeading('IBU KANDUNG');
    const headingWali = findLeafHeading('WALI');
    const btnSimpan = Array.from(document.querySelectorAll('button')).find(btn => 
        (btn.innerText || btn.textContent || '').trim().toUpperCase().includes('SIMPAN')
    ) || null;

    // METODE 1: Input pertama setelah heading Ayah (sebelum Ibu), dan setelah heading Ibu (sebelum Wali)
    if (headingAyah) {
        inputAyah = findFirstInputAfter(headingAyah, headingIbu);
    }
    if (headingIbu) {
        inputIbu = findFirstInputAfter(headingIbu, headingWali || btnSimpan);
    }

    // METODE 2: Fallback container 'KETERANGAN ORANG TUA SANTRI'
    if (!inputAyah || !inputIbu) {
        const headingOrtu = findLeafHeading('KETERANGAN ORANG TUA SANTRI') || findLeafHeading('KETERANGAN ORANG TUA');
        if (headingOrtu) {
            let card = headingOrtu.parentElement;
            while (card && card !== document.body) {
                const txt = (card.innerText || '').toUpperCase();
                if (txt.includes('AYAH KANDUNG') && !txt.includes('KETERANGAN CALON SANTRI')) {
                    break;
                }
                card = card.parentElement;
            }

            if (card && card !== document.body) {
                const inputsInCard = Array.from(card.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])'))
                    .filter(inp => inp.getBoundingClientRect().width > 0 && !inp.disabled);

                if (!inputAyah && inputsInCard.length >= 1) inputAyah = inputsInCard[0];
                if (!inputIbu && inputsInCard.length >= 2) inputIbu = inputsInCard[1];
            }
        }
    }

    // =========================================================================
    // VERIFIKASI KEAMANAN (Absolute Protection agar tidak menimpa Nama Santri)
    // =========================================================================
    if (inputAyah && data.nama_santri) {
        const val = (inputAyah.value || '').trim().toUpperCase();
        const namaSantri = data.nama_santri.trim().toUpperCase();
        const namaSantriClean = (data.nama_santri_clean || '').trim().toUpperCase();
        if ((val && val === namaSantri) || (val && val === namaSantriClean)) {
            console.warn('[TPQ-EMIS Bridge] Proteksi aktif: inputAyah menunjuk ke field nama santri! Dibatalkan.');
            inputAyah = null;
        }
    }

    if (inputIbu && data.nama_santri) {
        const val = (inputIbu.value || '').trim().toUpperCase();
        const namaSantri = data.nama_santri.trim().toUpperCase();
        const namaSantriClean = (data.nama_santri_clean || '').trim().toUpperCase();
        if ((val && val === namaSantri) || (val && val === namaSantriClean)) {
            console.warn('[TPQ-EMIS Bridge] Proteksi aktif: inputIbu menunjuk ke field nama santri! Dibatalkan.');
            inputIbu = null;
        }
    }

    if (inputAyah && inputIbu && inputAyah === inputIbu) {
        console.warn('[TPQ-EMIS Bridge] inputAyah dan inputIbu terdeteksi sama! Membatalkan inputIbu.');
        inputIbu = null;
    }

    console.log('[TPQ-EMIS Bridge] Hasil pencarian input orang tua:', {
        inputAyah: inputAyah ? (inputAyah.name || inputAyah.placeholder || 'Ditemukan') : 'Tidak ditemukan',
        inputIbu: inputIbu ? (inputIbu.name || inputIbu.placeholder || 'Ditemukan') : 'Tidak ditemukan'
    });

    return { inputAyah, inputIbu };
}

/**
 * Memilih radio button Jenis Kelamin (Laki-laki / Perempuan) di Formulir EMIS Step 2
 * Kompatibel dengan React Native for Web (role="radio") dan input radio standar
 */
function selectJenisKelaminEmis(targetJk) {
    if (!targetJk) return false;
    const jkStr = String(targetJk).trim().toUpperCase();
    const isPerempuan = jkStr.includes('PEREMPUAN') || jkStr === 'P';
    const labelDisplayName = isPerempuan ? 'Perempuan' : 'Laki-laki';

    console.log('[TPQ-EMIS Bridge] Mencoba memilih Jenis Kelamin otomatis:', labelDisplayName);

    // 1. Cari elemen dengan role="radio" (React Native for Web) atau input[type="radio"]
    const radioElements = Array.from(document.querySelectorAll('[role="radio"], input[type="radio"]'));
    for (const r of radioElements) {
        // Ambil container opsi terdekat (biasanya .r-1awozwy atau parent element)
        const container = r.closest('.r-1awozwy') || r.closest('.r-18u37iz') || r.parentElement;
        const txt = (container ? (container.innerText || container.textContent || '') : '').trim().toUpperCase();

        const match = isPerempuan 
            ? txt.includes('PEREMPUAN') 
            : (txt.includes('LAKI') && !txt.includes('PEREMPUAN'));

        if (match) {
            const isChecked = r.getAttribute('aria-checked') === 'true' || r.checked === true;
            if (!isChecked) {
                console.log('[TPQ-EMIS Bridge] Menemukan radio Jenis Kelamin (' + labelDisplayName + '), melakukan klik...');
                try { r.focus(); } catch (_) {}
                try { r.click(); } catch (_) {}
                try {
                    r.dispatchEvent(new PointerEvent('pointerdown', { bubbles: true, cancelable: true }));
                    r.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
                    r.dispatchEvent(new PointerEvent('pointerup', { bubbles: true, cancelable: true }));
                    r.dispatchEvent(new MouseEvent('mouseup', { bubbles: true, cancelable: true }));
                    r.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
                } catch (_) {}

                // Klik juga container dan text element di sampingnya jika ada
                if (container) {
                    try { container.click(); } catch (_) {}
                    const textEl = container.querySelector('.css-901oao, span, div:not([role="radio"])');
                    if (textEl) {
                        try { textEl.click(); } catch (_) {}
                    }
                }
            }

            // Visual feedback: beri highlight hijau lembut
            if (container) {
                try {
                    container.style.transition = 'all 0.3s ease';
                    container.style.backgroundColor = '#d1e7dd';
                    container.style.borderRadius = '6px';
                    container.style.padding = '2px 8px';
                } catch (_) {}
            }
            return true;
        }
    }

    // 2. Fallback: Cari langsung label teks "Laki-laki" atau "Perempuan"
    const allLabels = Array.from(document.querySelectorAll('.css-901oao, label, span, div')).filter(el => {
        const t = (el.innerText || el.textContent || '').trim().toUpperCase();
        return isPerempuan ? (t === 'PEREMPUAN') : (t === 'LAKI-LAKI' || t === 'LAKI - LAKI');
    });

    for (const textEl of allLabels) {
        const container = textEl.closest('.r-1awozwy') || textEl.parentElement;
        if (container) {
            const radio = container.querySelector('[role="radio"], input[type="radio"]');
            if (radio) {
                const isChecked = radio.getAttribute('aria-checked') === 'true' || radio.checked === true;
                if (!isChecked) {
                    try { radio.focus(); } catch (_) {}
                    try { radio.click(); } catch (_) {}
                    try {
                        radio.dispatchEvent(new PointerEvent('pointerdown', { bubbles: true, cancelable: true }));
                        radio.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
                        radio.dispatchEvent(new PointerEvent('pointerup', { bubbles: true, cancelable: true }));
                        radio.dispatchEvent(new MouseEvent('mouseup', { bubbles: true, cancelable: true }));
                        radio.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
                    } catch (_) {}
                }
            }
            try { textEl.click(); } catch (_) {}
            try { container.click(); } catch (_) {}
            try {
                container.style.transition = 'all 0.3s ease';
                container.style.backgroundColor = '#d1e7dd';
                container.style.borderRadius = '6px';
                container.style.padding = '2px 8px';
            } catch (_) {}
            return true;
        }
    }

    return false;
}

/**
 * Normalisasi nama kelas TPQ Smart ke teks pilihan dropdown Tingkat Kelas EMIS
 * Contoh: TPQ1/SD1 -> Kelas 1, TKQA -> Kelompok A
 */
function normalizeTingkatKelasEmis(rawKelas) {
    if (!rawKelas) return '';
    const str = String(rawKelas).trim();
    const upper = str.toUpperCase();

    if (upper.includes('TKQB') || upper.includes('KELOMPOK B')) {
        return 'Kelompok B';
    }
    if (upper.includes('TKQA') || upper.includes('TKQ') || upper.includes('KELOMPOK A')) {
        return 'Kelompok A';
    }

    // Ambil angka dari TPQ1/SD1, Kelas 1, SD 2, dll
    const numMatch = str.match(/\d+/);
    if (numMatch) {
        return 'Kelas ' + numMatch[0];
    }

    return str;
}

/**
 * Mencari dan memilih dropdown Tingkat Kelas di Formulir EMIS Step 2
 * Kompatibel dengan format opsi EMIS: Kelas 1 s.d. 12 (value 1-12), Kelompok A (value 20), Kelompok B (value 21)
 */
function selectTingkatKelasEmis(rawKelas) {
    const target = normalizeTingkatKelasEmis(rawKelas);
    if (!target) return false;

    console.log('[TPQ-EMIS Bridge] Mencoba memilih Tingkat Kelas:', target, '(dari asal:', rawKelas, ')');

    const selects = Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0) continue;

        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const optionValues = options.map(o => String(o.value ?? '').trim());

        // Ciri unik dropdown Tingkat Kelas di EMIS:
        // Memiliki opsi 'kelas 1' dan 'kelompok a', atau memiliki value '20' dan '1'
        const hasKelas1 = optionTexts.some(t => t.includes('kelas 1')) || optionValues.includes('1');
        const hasKelompokA = optionTexts.some(t => t.includes('kelompok a')) || optionValues.includes('20');

        let contextText = (sel.name || '') + ' ' + (sel.id || '');
        let p = sel.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            contextText += ' ' + (p.innerText || p.textContent || '');
            p = p.parentElement;
        }
        const contextUpper = contextText.toUpperCase();
        const hasTingkatLabel = contextUpper.includes('TINGKAT') || contextUpper.includes('KELAS');

        if ((hasKelas1 && hasKelompokA) || (hasKelas1 && hasTingkatLabel)) {
            console.log('[TPQ-EMIS Bridge] Dropdown Tingkat Kelas ditemukan! Menyetel ke:', target);
            const success = setNativeSelectValue(sel, target);
            if (success) {
                const container = sel.closest('.r-16y2uox') || sel.closest('.r-crgep1') || sel.parentElement;
                if (container) {
                    try {
                        container.style.transition = 'background-color 0.3s ease, border-radius 0.3s ease';
                        container.style.backgroundColor = '#d1e7dd';
                        container.style.borderRadius = '6px';
                    } catch (_) {}
                }
                return true;
            }
        }
    }

    return false;
}

function fillEmisStep2(data) {
    console.log('[TPQ-EMIS Bridge] Mengisi Step 2 (NIK, NISN, No HP, Tingkat Kelas, Nama Ayah & Ibu, Jenis Kelamin)...', data.nama_santri);

    // Pastikan popup Step 1 tertutup
    const step1Alert = document.getElementById('tpq-step1-alert');
    if (step1Alert) step1Alert.remove();

    let filledAyah = false;
    let filledIbu = false;
    let filledNik = false;
    let filledNisn = false;
    let filledHp = false;
    let filledJk = false;
    let filledKelas = false;
    let attempts = 0;
    const maxAttempts = 15; // 3 detik

    const timer = setInterval(() => {
        attempts++;

        // 1. Isi NIK Calon Santri / Centang Belum Punya NIK secara otomatis
        if (!filledNik) {
            const { inputNik, checkboxNik } = findStep2NikElements();
            if (data.belum_punya_nik || !data.nik) {
                if (checkboxNik) {
                    console.log('[TPQ-EMIS Bridge] Mencentang [✓] Belum Punya NIK');
                    setCheckboxChecked(checkboxNik, true);
                    filledNik = true;
                }
            } else if (data.nik && inputNik) {
                console.log('[TPQ-EMIS Bridge] Mengisi NIK Calon Santri:', data.nik);
                setNativeValue(inputNik, data.nik);
                filledNik = true;
            }
        }

        // 2. Isi NISN Calon Santri / Centang Belum Punya NISN secara otomatis
        if (!filledNisn) {
            const { inputNisn, checkboxNisn } = findStep2NisnElements();
            if (data.belum_punya_nisn || !data.nisn) {
                if (checkboxNisn) {
                    console.log('[TPQ-EMIS Bridge] Mencentang [✓] Belum Punya NISN');
                    setCheckboxChecked(checkboxNisn, true);
                    filledNisn = true;
                }
            } else if (data.nisn && inputNisn) {
                console.log('[TPQ-EMIS Bridge] Mengisi NISN Calon Santri:', data.nisn);
                setNativeValue(inputNisn, data.nisn);
                filledNisn = true;
            }
        }

        // 3. Isi No Handphone Calon Santri / Centang Tidak Memiliki HP secara otomatis
        if (!filledHp) {
            const { inputHp, checkboxHp } = findStep2HpElements();
            const formattedHp = formatEmisPhoneNumber(data.no_hp);

            if (formattedHp && inputHp && !data.tidak_punya_hp) {
                console.log('[TPQ-EMIS Bridge] Mengisi No Handphone Calon Santri (+62):', formattedHp);
                setNativeValue(inputHp, formattedHp);
                filledHp = true;
            } else if (data.tidak_punya_hp || !formattedHp) {
                if (checkboxHp) {
                    console.log('[TPQ-EMIS Bridge] Mencentang [✓] Tidak Memiliki No Handphone');
                    setCheckboxChecked(checkboxHp, true);
                    filledHp = true;
                }
            }
        }

        // 4. Isi Nama Ayah & Ibu Kandung
        const { inputAyah, inputIbu } = findOrangTuaInputs(data);

        if (inputAyah && data.nama_ayah && !filledAyah) {
            console.log('[TPQ-EMIS Bridge] Mengisi Nama Ayah Kandung:', data.nama_ayah);
            setNativeValue(inputAyah, data.nama_ayah);
            filledAyah = true;
        }

        if (inputIbu && data.nama_ibu && !filledIbu) {
            console.log('[TPQ-EMIS Bridge] Mengisi Nama Ibu Kandung:', data.nama_ibu);
            setNativeValue(inputIbu, data.nama_ibu);
            filledIbu = true;
        }

        // 5. Pilih Jenis Kelamin (Laki-laki / Perempuan) secara otomatis
        if (!filledJk && data.jenis_kelamin) {
            filledJk = selectJenisKelaminEmis(data.jenis_kelamin);
        }

        // 6. Pilih Tingkat Kelas secara otomatis
        if (!filledKelas && (data.tingkat_kelas || data.nama_kelas_asal)) {
            filledKelas = selectTingkatKelasEmis(data.tingkat_kelas || data.nama_kelas_asal);
        }

        if ((filledAyah && filledIbu && filledNik && filledNisn && filledHp && filledJk && filledKelas) || attempts >= maxAttempts) {
            clearInterval(timer);
            // Tampilkan Floating Checklist Assistant untuk isian manual Step 2
            showStep2Assistant(data, filledAyah, filledIbu, filledNik, filledHp, filledJk, filledNisn, filledKelas);
        }
    }, 200);

    return { success: true, step: 2 };
}

/**
 * ============================================================================
 * HELPER FORM INTERACTION (REACT / VUE / ANT-DESIGN COMPATIBLE)
 * ============================================================================
 */

/**
 * Mencentang checkbox secara universal (kompatibel React Native for Web role="checkbox" dan input[type="checkbox"])
 */
function setCheckboxChecked(element, check = true) {
    if (!element) return false;

    // 1. Standar HTML <input type="checkbox">
    if (element.tagName === 'INPUT' && element.type === 'checkbox') {
        if (element.checked !== check) {
            element.checked = check;
            element.dispatchEvent(new Event('change', { bubbles: true }));
            element.dispatchEvent(new Event('click', { bubbles: true }));
        }
        return true;
    }

    // 2. React Native for Web [role="checkbox"]
    const roleCb = (element.getAttribute && element.getAttribute('role') === 'checkbox') 
        ? element 
        : (element.querySelector ? element.querySelector('[role="checkbox"]') : null);

    const targetEl = roleCb || element;
    const isChecked = targetEl.getAttribute && targetEl.getAttribute('aria-checked') === 'true';

    if (isChecked !== check) {
        console.log('[TPQ-EMIS Bridge] Mengubah status checkbox ke:', check ? 'Checked' : 'Unchecked', targetEl);
        try { targetEl.focus(); } catch (_) {}
        try { targetEl.click(); } catch (_) {}
        try {
            targetEl.dispatchEvent(new PointerEvent('pointerdown', { bubbles: true, cancelable: true }));
            targetEl.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
            targetEl.dispatchEvent(new PointerEvent('pointerup', { bubbles: true, cancelable: true }));
            targetEl.dispatchEvent(new MouseEvent('mouseup', { bubbles: true, cancelable: true }));
            targetEl.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
        } catch (_) {}

        // Klik juga parent container atau teks label di sampingnya jika ada
        const container = (targetEl.closest ? targetEl.closest('.r-1awozwy') : null) || targetEl.parentElement;
        if (container) {
            try { container.click(); } catch (_) {}
            const labelTextEl = container.querySelector ? container.querySelector('.css-901oao, span, label') : null;
            if (labelTextEl && labelTextEl !== targetEl) {
                try { labelTextEl.click(); } catch (_) {}
            }
        }
    }

    // Efek highlight visual hijau lembut
    const visualContainer = (targetEl.closest ? targetEl.closest('.r-1awozwy') : null) || targetEl.parentElement;
    if (visualContainer && visualContainer.style) {
        try {
            visualContainer.style.transition = 'background-color 0.3s ease, border-radius 0.3s ease';
            visualContainer.style.backgroundColor = '#d1e7dd';
            visualContainer.style.borderRadius = '4px';
        } catch (_) {}
    }

    return true;
}

function setNativeValue(element, value) {
    if (!element) return false;

    // Jika elemen adalah <select>, alihkan ke setNativeSelectValue
    if (element.tagName === 'SELECT') {
        return setNativeSelectValue(element, value);
    }

    try {
        const val = String(value ?? '').trim();
        if (!val) return false;

        const wasReadOnly = element.readOnly;
        if (wasReadOnly) {
            element.readOnly = false;
        }

        const lastValue = element.value;

        // Bypass React overridden setter dengan prototype HTMLInputElement / HTMLTextAreaElement asli
        const isTextArea = element.tagName === 'TEXTAREA';
        const proto = isTextArea 
            ? (window.HTMLTextAreaElement ? window.HTMLTextAreaElement.prototype : Object.getPrototypeOf(element))
            : (window.HTMLInputElement ? window.HTMLInputElement.prototype : Object.getPrototypeOf(element));
        const nativeSetter = Object.getOwnPropertyDescriptor(proto, 'value')?.set;
        if (nativeSetter) {
            nativeSetter.call(element, val);
        } else {
            element.value = val;
        }

        // Kunci sinkronisasi state React: update _valueTracker
        if (element._valueTracker) {
            element._valueTracker.setValue(lastValue);
        }

        // Dispatch standard bubbling events
        element.dispatchEvent(new Event('input', { bubbles: true }));
        element.dispatchEvent(new Event('change', { bubbles: true }));

        if (wasReadOnly) {
            element.readOnly = true;
        }

        // Highlight visual hijau
        element.style.transition = 'background-color 0.3s ease, border-color 0.3s ease';
        element.style.backgroundColor = '#d1e7dd';
        element.style.borderColor = '#198754';
        setTimeout(() => { 
            element.style.backgroundColor = ''; 
            element.style.borderColor = '';
        }, 3000);

        return true;
    } catch (e) {
        console.error('[TPQ-EMIS Bridge] Gagal set value:', e);
        try { element.value = value; } catch (_) {}
        return false;
    }
}

/**
 * Helper khusus memilih opsi pada elemen <select> (kompatibel React Native for Web / React Synthetic Events)
 */
function setNativeSelectValue(selectElement, targetValueOrText) {
    if (!selectElement || targetValueOrText === undefined || targetValueOrText === null) return false;

    try {
        const target = String(targetValueOrText).trim().toLowerCase();
        if (!target) return false;

        const options = Array.from(selectElement.options || selectElement.querySelectorAll('option'));
        if (options.length === 0) return false;

        // 1. Cari opsi exact match berdasarkan value atau label/text
        let matchedOption = options.find(opt => {
            const val = String(opt.value ?? '').trim().toLowerCase();
            const lbl = String(opt.label || opt.text || opt.innerText || '').trim().toLowerCase();
            return (val && val === target) || (lbl && lbl === target);
        });

        // 2. Jika tidak ada exact match, coba partial / substring match
        if (!matchedOption) {
            matchedOption = options.find(opt => {
                const lbl = String(opt.label || opt.text || opt.innerText || '').trim().toLowerCase();
                return lbl && (lbl.includes(target) || target.includes(lbl));
            });
        }

        if (!matchedOption) {
            console.warn('[TPQ-EMIS Bridge] Opsi tidak ditemukan pada <select> untuk nilai:', targetValueOrText);
            return false;
        }

        const valToSet = matchedOption.value;
        const lastValue = selectElement.value;

        // Tandai option yang dipilih
        options.forEach(opt => {
            opt.selected = (opt === matchedOption);
        });

        // Bypass React overridden setter dengan prototype HTMLSelectElement asli
        const proto = window.HTMLSelectElement ? window.HTMLSelectElement.prototype : Object.getPrototypeOf(selectElement);
        const nativeSetter = Object.getOwnPropertyDescriptor(proto, 'value')?.set;
        if (nativeSetter) {
            nativeSetter.call(selectElement, valToSet);
        } else {
            selectElement.value = valToSet;
        }

        // Kunci sinkronisasi state React: update _valueTracker jika ada
        if (selectElement._valueTracker) {
            selectElement._valueTracker.setValue(lastValue);
        }

        // Trigger input & change events dengan bubbles: true
        selectElement.dispatchEvent(new Event('input', { bubbles: true }));
        selectElement.dispatchEvent(new Event('change', { bubbles: true }));

        // Highlight visual hijau
        try {
            selectElement.style.transition = 'background-color 0.3s ease, border-color 0.3s ease';
            selectElement.style.backgroundColor = '#d1e7dd';
            selectElement.style.borderColor = '#198754';
            if (selectElement.parentElement) {
                selectElement.parentElement.style.borderColor = '#198754';
            }
            setTimeout(() => {
                try {
                    selectElement.style.backgroundColor = '';
                    selectElement.style.borderColor = '';
                    if (selectElement.parentElement) {
                        selectElement.parentElement.style.borderColor = '';
                    }
                } catch (_) {}
            }, 3000);
        } catch (_) {}

        console.log(`[TPQ-EMIS Bridge] Berhasil set select: "${matchedOption.label || matchedOption.text}" (value: ${valToSet})`);
        return true;
    } catch (e) {
        console.error('[TPQ-EMIS Bridge] Gagal set select value:', e);
        return false;
    }
}

/**
 * Membuat elemen floating card dapat digeser (draggable) dengan mouse
 */
function makeDraggable(element, handle = null) {
    if (!element) return;
    const dragHandle = handle || element;
    dragHandle.style.cursor = 'move';
    dragHandle.style.userSelect = 'none';

    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let initialLeft = 0;
    let initialTop = 0;

    dragHandle.addEventListener('mousedown', (e) => {
        // Jangan aktifkan drag jika user mengklik tombol, input, link, atau tombol copy
        if (e.target.closest('button, input, a, select, textarea, .tpq-copy-badge, .tpq-copy-btn')) return;

        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;

        const rect = element.getBoundingClientRect();
        initialLeft = rect.left;
        initialTop = rect.top;

        // Beralih ke fixed coordinates left & top agar perpindahan mulus
        element.style.bottom = 'auto';
        element.style.right = 'auto';
        element.style.left = initialLeft + 'px';
        element.style.top = initialTop + 'px';
        element.style.transition = 'none';

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        e.preventDefault();
    });

    function onMouseMove(e) {
        if (!isDragging) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;

        let newLeft = initialLeft + dx;
        let newTop = initialTop + dy;

        // Jaga agar tetap berada di area viewport layar
        const maxLeft = window.innerWidth - element.offsetWidth - 8;
        const maxTop = window.innerHeight - element.offsetHeight - 8;

        newLeft = Math.max(8, Math.min(newLeft, maxLeft));
        newTop = Math.max(8, Math.min(newTop, maxTop));

        element.style.left = newLeft + 'px';
        element.style.top = newTop + 'px';
    }

    function onMouseUp() {
        isDragging = false;
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    }
}

/**
 * ============================================================================
 * FLOATING ASSISTANT BANNERS
 * ============================================================================
 */

/**
 * Mencoba mengisi input NIK jika sedang berada di tab pencarian NIK EMIS
 */
function tryFillNikInput(nik) {
    if (!nik) return false;
    const inputs = Array.from(document.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"])'))
        .filter(inp => {
            const rect = inp.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0 && !inp.disabled;
        });

    for (const inp of inputs) {
        const ph = (inp.getAttribute('placeholder') || '').toUpperCase();
        const nm = (inp.getAttribute('name') || '').toUpperCase();
        const id = (inp.getAttribute('id') || '').toUpperCase();
        if (ph.includes('NIK') || nm.includes('NIK') || id.includes('NIK') || inputs.length === 1) {
            setNativeValue(inp, nik);
            return true;
        }
    }
    return false;
}

/**
 * ============================================================================
 * STEP 0 / START POPUP: Asisten Pendaftaran & Pemilihan Metode Pencarian
 * Tampil di bagian atas saat halaman pendaftaran EMIS terbuka.
 * Memberikan 2 opsi agar pengisian lebih akurat:
 * - Step 0: Cari via NIK (tombol Salin NIK, berguna jika ingin mencoba cari via NIK)
 * - Step 1: Cari via Nama & TTL (tombol Mulai Isi Step 1, mengisi otomatis Nama & TTL)
 * ============================================================================
 */
function showStep0StartPopup(data) {
    if (!data) return;

    const popupId = 'tpq-step0-alert';
    const old = document.getElementById(popupId);
    if (old) old.remove();

    const oldPending = document.getElementById('tpq-pending-banner');
    if (oldPending) oldPending.remove();

    const oldPill = document.getElementById('tpq-reopen-step0-pill');
    if (oldPill) oldPill.remove();

    // Format tanggal lahir berlabel nama bulan (misal: 2024-Agustus-18)
    const tglLahirLabel = data.tanggal_lahir_label || formatTanggalNamaBulan(data.tanggal_lahir);

    // Pastikan status antrean tersimpan dan aktif memantau transisi ke Step 2
    chrome.storage.local.set({
        pendingSantriData: data,
        waitingForStep2: true
    });

    const isPpdbUrl = window.location.href.includes('search-data-ppdb');

    const div = document.createElement('div');
    div.id = popupId;
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999999;
        background: #0d6efd;
        color: white;
        padding: 16px 18px;
        border-radius: 8px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.35);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        max-width: 420px;
        width: 380px;
        line-height: 1.4;
    `;

    div.innerHTML = `
        <style>
            .tpq-btn-copy-gold {
                background: #ffc107;
                color: #000;
                border: none;
                padding: 4px 10px;
                border-radius: 4px;
                font-size: 11.5px;
                font-weight: bold;
                cursor: pointer;
                transition: background 0.2s, transform 0.1s;
                white-space: nowrap;
            }
            .tpq-btn-copy-gold:hover {
                background: #ffca2c;
                transform: translateY(-1px);
            }
            @keyframes tpq-pulse {
                0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
                70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
                100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
            }
            .tpq-pulse-btn {
                animation: tpq-pulse 1.8s infinite;
            }
        </style>

        <div id="tpq-step0-header" style="cursor: move; user-select: none; font-weight: bold; font-size: 14px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
            <span>⚡ Mulai Isi Formulir EMIS</span>
            <span style="font-size: 11px; background: rgba(255,255,255,0.25); padding: 2px 7px; border-radius: 4px; display: flex; align-items: center; gap: 4px;">
                <span>⠿</span> <span>Geser</span>
            </span>
        </div>

        <!-- Info Singkat Santri -->
        <div style="background: rgba(0,0,0,0.22); padding: 8px 12px; border-radius: 6px; margin-bottom: 10px;">
            <div style="font-size: 11px; opacity: 0.85;">CALON SANTRI:</div>
            <div style="font-size: 14.5px; font-weight: bold; color: #fff;">${data.nama_santri}</div>
            <div style="font-size: 12px; color: #e2e8f0; margin-top: 1px;">
                Kelas: <strong style="color: #ffc107;">${data.tingkat_kelas || 'Santri Baru'}</strong>
            </div>
        </div>

        <!-- Opsi 1: Step 0 (Cari via NIK) -->
        <div style="background: rgba(0,0,0,0.3); padding: 10px 12px; border-radius: 6px; margin-bottom: 10px; border-left: 3px solid #ffc107;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <strong style="color: #ffc107; font-size: 12.5px;">🔍 Opsi 1: Cari via NIK (Step 0)</strong>
            </div>
            <div style="font-size: 11.5px; opacity: 0.9; margin-bottom: 6px;">
                Jika ingin mencoba pencarian pada tab NIK di EMIS:
            </div>
            ${data.belum_punya_nik ? `
                <div style="font-size: 11.5px; color: #ffc107; font-style: italic; background: rgba(0,0,0,0.2); padding: 5px 8px; border-radius: 4px;">
                    ⚠️ Santri belum punya NIK (Silakan gunakan Opsi 2 / Step 1)
                </div>
            ` : `
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.15); padding: 6px 10px; border-radius: 4px;">
                    <span style="font-family: monospace; font-size: 13.5px; font-weight: bold; color: #fff;">${data.nik || '-'}</span>
                    <button id="tpq-btn-copy-nik-step0" class="tpq-btn-copy-gold" data-val="${data.nik || ''}">📋 Salin NIK</button>
                </div>
            `}
        </div>

        <!-- Opsi 2: Step 1 (Cari via Nama & TTL) -->
        <div id="tpq-opsi2-box" style="background: rgba(0,0,0,0.3); padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; border-left: 3px solid #10b981;">
            <div style="margin-bottom: 4px;">
                <strong style="color: #6ee7b7; font-size: 12.5px;">📝 Opsi 2: Cari via Nama & TTL (Step 1)</strong>
            </div>
            <div style="font-size: 11.5px; opacity: 0.9; margin-bottom: 7px;">
                Pilih tab <em>"Nama, Tempat, dan tanggal lahir"</em> di EMIS, lalu klik tombol isi:
            </div>
            <div style="font-size: 11.5px; background: rgba(0,0,0,0.2); padding: 5px 8px; border-radius: 4px; margin-bottom: 8px; line-height: 1.4;">
                • Tempat Lahir: <strong>${data.tempat_lahir || '-'}</strong><br>
                • Tanggal Lahir: <strong>${tglLahirLabel}</strong>
            </div>
            <button id="tpq-btn-start-step1" style="
                width: 100%; background: #198754; color: #ffffff; border: none;
                padding: 8px 12px; border-radius: 4px; font-size: 13px; font-weight: bold;
                cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.25);
                display: flex; align-items: center; justify-content: center; gap: 6px;
                transition: background 0.2s;
            ">
                <span>⚡ Mulai Isi Step 1 (Nama & TTL)</span>
            </button>
        </div>

        <!-- Footer -->
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11.5px;">
            ${!isPpdbUrl ? `
                <button id="tpq-btn-nav-ppdb" style="
                    background: #ffc107; color: #000; border: none; padding: 5px 9px;
                    border-radius: 4px; font-size: 11.5px; font-weight: bold; cursor: pointer;
                ">🔗 Buka PPDB Baru</button>
            ` : '<span></span>'}
            <div style="display: flex; gap: 6px;">
                <button id="tpq-btn-cancel-antrean" style="
                    background: rgba(255,255,255,0.18); color: white; border: none;
                    padding: 5px 10px; border-radius: 4px; cursor: pointer;
                ">Batal</button>
                <button id="tpq-close-step0-alert" style="
                    background: rgba(255,255,255,0.28); color: white; border: none;
                    padding: 5px 12px; border-radius: 4px; cursor: pointer; font-weight: 500;
                ">Tutup</button>
            </div>
        </div>
    `;

    document.body.appendChild(div);

    // Draggable
    const headerEl = document.getElementById('tpq-step0-header');
    makeDraggable(div, headerEl);

    // Listener Tombol Salin NIK (Step 0)
    const btnCopyNik = document.getElementById('tpq-btn-copy-nik-step0');
    if (btnCopyNik) {
        btnCopyNik.addEventListener('click', () => {
            const val = btnCopyNik.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                tryFillNikInput(val);

                const origText = btnCopyNik.innerText;
                const origBg = btnCopyNik.style.background;
                btnCopyNik.innerText = '✓ NIK Tersalin!';
                btnCopyNik.style.background = '#198754';
                btnCopyNik.style.color = '#fff';
                setTimeout(() => {
                    btnCopyNik.innerText = origText;
                    btnCopyNik.style.background = origBg;
                    btnCopyNik.style.color = '#000';
                }, 1500);
            }
        });
    }

    // Listener Tombol Mulai Isi Step 1
    const btnStart1 = document.getElementById('tpq-btn-start-step1');
    if (btnStart1) {
        btnStart1.addEventListener('click', () => {
            const pill = document.getElementById('tpq-reopen-step0-pill');
            if (pill) pill.remove();
            div.remove();
            fillEmisStep1(data);
        });
    }

    // Listener Buka PPDB jika di luar halaman
    const btnNav = document.getElementById('tpq-btn-nav-ppdb');
    if (btnNav) {
        btnNav.addEventListener('click', () => {
            window.location.href = "https://emis.kemenag.go.id/search-data-ppdb/baru";
        });
    }

    // Tutup popup -> Munculkan mini pill mengambang agar mudah dibuka kembali tanpa refresh
    document.getElementById('tpq-close-step0-alert').addEventListener('click', () => {
        div.remove();
        showStep0MiniPill(data);
    });

    // Batal (hapus antrean santri dari storage)
    document.getElementById('tpq-btn-cancel-antrean').addEventListener('click', () => {
        chrome.storage.local.remove(['pendingSantriData', 'waitingForStep2']);
        const pill = document.getElementById('tpq-reopen-step0-pill');
        if (pill) pill.remove();
        div.remove();
    });

    // Observer untuk mendeteksi jika hasil cek NIK di EMIS menghasilkan "Data tidak ditemukan"
    const notFoundObserver = new MutationObserver(() => {
        const step0 = document.getElementById(popupId);
        if (!step0) {
            notFoundObserver.disconnect();
            return;
        }
        if (document.getElementById('tpq-nik-not-found-hint')) return;

        const bodyTxt = (document.body.innerText || document.body.textContent || '').toUpperCase();
        if (bodyTxt.includes('TIDAK DITEMUKAN') || bodyTxt.includes('BELUM DITEMUKAN') || bodyTxt.includes('TIDAK TERDAFTAR') || bodyTxt.includes('DATA TIDAK ADA')) {
            const opsi2Box = step0.querySelector('#tpq-opsi2-box');
            if (opsi2Box && !document.getElementById('tpq-nik-not-found-hint')) {
                const hint = document.createElement('div');
                hint.id = 'tpq-nik-not-found-hint';
                hint.style.cssText = `
                    background: #dc3545; color: #fff; padding: 7px 10px; border-radius: 5px;
                    font-size: 11.5px; margin-bottom: 10px; font-weight: 500; line-height: 1.4;
                    border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 2px 6px rgba(0,0,0,0.25);
                `;
                hint.innerHTML = `⚠️ <strong>Data NIK tidak ditemukan di EMIS.</strong><br>Silakan pilih tab <em>"Nama, Tempat, dan tanggal lahir"</em> di EMIS, lalu klik tombol di bawah:`;
                opsi2Box.parentElement.insertBefore(hint, opsi2Box);

                // Tambahkan efek sorot pada tombol Mulai Isi Step 1
                const btn1 = step0.querySelector('#tpq-btn-start-step1');
                if (btn1) {
                    btn1.classList.add('tpq-pulse-btn');
                    btn1.style.outline = '2px solid #ffc107';
                }
            }
        }
    });

    try {
        notFoundObserver.observe(document.body, { childList: true, subtree: true, characterData: true });
    } catch (_) {}
}

/**
 * Tombol mini (pill) mengambang jika popup ditutup secara manual,
 * agar pengguna dapat membukanya kembali kapan saja tanpa refresh halaman.
 */
function showStep0MiniPill(data) {
    if (!data) return;
    const oldPill = document.getElementById('tpq-reopen-step0-pill');
    if (oldPill) oldPill.remove();

    const pill = document.createElement('div');
    pill.id = 'tpq-reopen-step0-pill';
    pill.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999999;
        background: #0d6efd;
        color: white;
        padding: 7px 14px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(255,255,255,0.4);
        transition: transform 0.15s, background 0.2s;
    `;
    pill.innerHTML = `<span>⚡ Buka TPQ Smart (${data.nama_santri})</span>`;
    pill.addEventListener('mouseenter', () => { pill.style.transform = 'scale(1.03)'; pill.style.background = '#0b5ed7'; });
    pill.addEventListener('mouseleave', () => { pill.style.transform = 'scale(1)'; pill.style.background = '#0d6efd'; });
    pill.addEventListener('click', () => {
        pill.remove();
        showStep0StartPopup(data);
    });
    document.body.appendChild(pill);
}

/**
 * Banner Asisten Step 1: Memberikan tanggal lahir, NIK, Nama & TTL santri + tombol salin + draggable
 */
function showStep1Assistant(data) {
    const alertId = 'tpq-step1-alert';
    const old = document.getElementById(alertId);
    if (old) old.remove();

    const div = document.createElement('div');
    div.id = alertId;
    div.style.cssText = `
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9999999;
        background: #0d6efd;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.35);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        max-width: 420px;
    `;

    // Format Tampilan Tanggal Lahir (YYYY-NamaBulan-DD agar mudah di kalender picker EMIS)
    const tglLahirLabel = data.tanggal_lahir_label || formatTanggalNamaBulan(data.tanggal_lahir);

    // Tampilan NIK di Step 1
    const nikDisplay = data.belum_punya_nik
        ? '<span style="color: #ffc107; font-weight: 600;">(Belum Punya NIK)</span>'
        : `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.nik || '-'}</span>
           <button class="tpq-copy-badge" data-val="${data.nik || ''}">Salin</button>`;

    div.innerHTML = `
        <style>
            .tpq-copy-badge {
                background: rgba(255, 255, 255, 0.25);
                color: #fff;
                border: 1px solid rgba(255, 255, 255, 0.4);
                padding: 1px 6px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: bold;
                cursor: pointer;
                margin-left: 6px;
                transition: background 0.2s;
            }
            .tpq-copy-badge:hover {
                background: rgba(255, 255, 255, 0.45);
            }
            .tpq-data-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 5px;
                font-size: 12.5px;
            }
        </style>

        <div id="tpq-step1-header" style="cursor: move; user-select: none; font-weight: bold; font-size: 14px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
            <span>✅ Step 1: Validasi Awal Santri</span>
            <span style="font-size: 11px; background: rgba(255,255,255,0.25); padding: 2px 7px; border-radius: 4px; display: flex; align-items: center; gap: 4px;">
                <span>⠿</span> <span>Geser</span>
            </span>
        </div>

        <div style="background: rgba(0,0,0,0.2); padding: 9px 12px; border-radius: 6px; margin-bottom: 10px;">
            <!-- Nama Santri -->
            <div class="tpq-data-row">
                <span>• <strong>Nama:</strong></span>
                <div>
                    <span style="font-weight: 600; color: #fff;">${data.nama_santri}</span>
                    <button class="tpq-copy-badge" data-val="${data.nama_santri}">Salin</button>
                </div>
            </div>

            <!-- Tempat Lahir -->
            <div class="tpq-data-row">
                <span>• <strong>Tempat Lahir:</strong></span>
                <div>
                    <span style="color: #fff;">${data.tempat_lahir || '-'}</span>
                    <button class="tpq-copy-badge" data-val="${data.tempat_lahir || ''}">Salin</button>
                </div>
            </div>

            <!-- NIK -->
            <div class="tpq-data-row" style="margin-bottom: 0;">
                <span>• <strong>NIK:</strong></span>
                <div>${nikDisplay}</div>
            </div>
        </div>
        
        <div style="background: rgba(0,0,0,0.3); padding: 10px 12px; border-radius: 6px; margin-bottom: 10px;">
            <div style="font-weight: bold; color: #ffc107; margin-bottom: 4px;">
                👉 Tugas Anda:
            </div>
            <div>Pilih <strong>TANGGAL LAHIR</strong> di kalender EMIS:</div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 6px;">
                <span style="font-size: 16px; font-weight: bold; color: #fff;">📅 ${tglLahirLabel}</span>
                <button id="tpq-btn-copy-tgl-step1" style="
                    background: #ffc107; color: #000; border: none; padding: 4px 10px;
                    border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;
                ">Salin Tanggal</button>
            </div>
            <div style="font-size: 11px; opacity: 0.85; margin-top: 4px;">(Sudah otomatis disalin ke clipboard)</div>
        </div>

        <div style="font-size: 11.5px; opacity: 0.95; line-height: 1.4; margin-bottom: 10px;">
            Setelah memilih tanggal lahir, klik tombol <strong style="background: rgba(255,255,255,0.25); padding: 1px 6px; border-radius: 3px;">SELANJUTNYA</strong> di formulir EMIS.<br>
            <span style="font-size: 11px; opacity: 0.85;">(Popup ini otomatis tertutup saat tombol ditekan & dapat digeser jika menutupi form)</span>
        </div>

        <button id="tpq-close-step1-alert" style="
            background: rgba(255,255,255,0.25); color: white; border: none;
            padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;
        ">Tutup</button>
    `;

    document.body.appendChild(div);
    document.getElementById('tpq-close-step1-alert').addEventListener('click', () => div.remove());

    // Jadikan popup Step 1 dapat digeser (draggable)
    const headerEl = document.getElementById('tpq-step1-header');
    makeDraggable(div, headerEl);

    // Pasang listener tombol salin tanggal
    const btnCopyTgl = document.getElementById('tpq-btn-copy-tgl-step1');
    if (btnCopyTgl && data.tanggal_lahir) {
        btnCopyTgl.addEventListener('click', () => {
            navigator.clipboard.writeText(data.tanggal_lahir);
            btnCopyTgl.innerText = 'Tersalin!';
            setTimeout(() => { btnCopyTgl.innerText = 'Salin Tanggal'; }, 1500);
        });
    }

    // Pasang listener ke semua tombol copy badge di dalam Step 1
    const copyBadges = div.querySelectorAll('.tpq-copy-badge');
    copyBadges.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const originalText = btn.innerText;
                btn.innerText = 'Tersalin!';
                btn.style.background = '#198754';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.background = 'rgba(255, 255, 255, 0.25)';
                }, 1200);
            }
        });
    });

    // Pasang listener langsung ke semua tombol SELANJUTNYA di halaman
    const allBtns = Array.from(document.querySelectorAll('button, a, div[role="button"]'));
    allBtns.forEach(b => {
        const t = (b.innerText || b.textContent || '').trim().toUpperCase();
        if (t.includes('SELANJUT') || t.includes('LANJUT')) {
            b.addEventListener('click', () => {
                const el = document.getElementById('tpq-step1-alert');
                if (el) el.remove();
            });
        }
    });
}

/**
 * Banner Asisten Step 2: Aligned dengan Step 1
 * - Bagian Atas: Data yang terisi otomatis / bisa disalin (NIK, HP, Ortu, JK)
 * - Bagian Bawah: 👉 Tugas Anda (Pilihan manual di EMIS: Tanggal Masuk, Tingkat Kelas, Status Ortu)
 */
function showStep2Assistant(data, filledAyah = false, filledIbu = false, filledNik = false, filledHp = false, filledJk = false, filledNisn = false, filledKelas = false) {
    const alertId = 'tpq-step2-alert';
    const old = document.getElementById(alertId);
    if (old) old.remove();

    // Pastikan jenis kelamin terpilih otomatis saat popup tampil
    if (!filledJk && data.jenis_kelamin) {
        filledJk = selectJenisKelaminEmis(data.jenis_kelamin);
    }

    // Pastikan tingkat kelas terpilih otomatis saat popup tampil jika belum
    const normalizedKelas = normalizeTingkatKelasEmis(data.tingkat_kelas || data.nama_kelas_asal);
    if (!filledKelas && normalizedKelas) {
        filledKelas = selectTingkatKelasEmis(normalizedKelas);
    }

    // Pastikan NISN dicentang / diisi saat popup tampil
    if (!filledNisn) {
        const { inputNisn, checkboxNisn } = findStep2NisnElements();
        if (data.belum_punya_nisn || !data.nisn) {
            if (checkboxNisn) {
                setCheckboxChecked(checkboxNisn, true);
                filledNisn = true;
            }
        } else if (data.nisn && inputNisn) {
            setNativeValue(inputNisn, data.nisn);
            filledNisn = true;
        }
    }

    const div = document.createElement('div');
    div.id = alertId;
    div.style.cssText = `
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9999999;
        background: #0d6efd;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.35);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        max-width: 440px;
    `;

    // Format Tampilan Tanggal Masuk & Tanggal Lahir (YYYY-NamaBulan-DD)
    const tglMasukLabel = data.tanggal_masuk_label || formatTanggalNamaBulan(data.tanggal_masuk);
    const tglLahirLabel = data.tanggal_lahir_label || formatTanggalNamaBulan(data.tanggal_lahir);

    // Tampilan NIK di Step 2 (Otomatis terisi)
    let nikDisplay = '';
    if (data.belum_punya_nik || !data.nik) {
        nikDisplay = '<span style="color: #ffc107; font-weight: 600;">[✓] Belum Punya NIK</span>';
    } else {
        nikDisplay = `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.nik}</span>
           <button class="tpq-copy-badge" data-val="${data.nik}">Salin</button>`;
    }

    // Tampilan NISN di Step 2 (Otomatis terisi / centang belum punya NISN)
    let nisnDisplay = '';
    if (data.belum_punya_nisn || !data.nisn) {
        nisnDisplay = '<span style="color: #ffc107; font-weight: 600;">[✓] Belum Punya NISN</span>';
    } else {
        nisnDisplay = `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.nisn}</span>
           <button class="tpq-copy-badge" data-val="${data.nisn}">Salin</button>`;
    }

    // Tampilan No Handphone di Step 2 (Otomatis terisi format +62)
    const formattedHp = formatEmisPhoneNumber(data.no_hp);
    let hpDisplay = '';
    if (data.tidak_punya_hp || !formattedHp) {
        hpDisplay = '<span style="color: #ffc107; font-weight: 600;">[✓] Tidak Punya HP</span>';
    } else {
        hpDisplay = `<span style="font-family: monospace; font-size: 13px; color: #fff;">${formattedHp}</span>
           <button class="tpq-copy-badge" data-val="${formattedHp}">Salin</button>`;
    }

    div.innerHTML = `
        <style>
            .tpq-copy-badge {
                background: rgba(255, 255, 255, 0.25);
                color: #fff;
                border: 1px solid rgba(255, 255, 255, 0.4);
                padding: 1px 6px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: bold;
                cursor: pointer;
                margin-left: 6px;
                transition: background 0.2s;
            }
            .tpq-copy-badge:hover {
                background: rgba(255, 255, 255, 0.45);
            }
            .tpq-data-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 5px;
                font-size: 12.5px;
            }
            .tpq-manual-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 6px;
                font-size: 12.5px;
                border-bottom: 1px dashed rgba(255, 255, 255, 0.2);
                padding-bottom: 4px;
            }
        </style>

        <div id="tpq-step2-header" style="cursor: move; user-select: none; font-weight: bold; font-size: 14px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
            <span>📋 Step 2: Formulir Utama Santri</span>
            <span style="font-size: 11px; background: rgba(255,255,255,0.25); padding: 2px 7px; border-radius: 4px; display: flex; align-items: center; gap: 4px;">
                <span>⠿</span> <span>Geser</span>
            </span>
        </div>

        <!-- BAGIAN ATAS: DATA YANG TERISI OTOMATIS / BISA DISALIN -->
        <div style="background: rgba(0,0,0,0.2); padding: 9px 12px; border-radius: 6px; margin-bottom: 10px;">
            <div style="font-size: 11px; font-weight: bold; color: rgba(255,255,255,0.85); margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                <span>✨ TERISI OTOMATIS (Bisa Disalin):</span>
                <span style="font-size: 10.5px; background: #198754; color: white; padding: 1px 6px; border-radius: 3px;">Auto-Filled ✓</span>
            </div>

            <!-- NIK -->
            <div class="tpq-data-row">
                <span>• <strong>NIK:</strong></span>
                <div>${nikDisplay}</div>
            </div>

            <!-- NISN -->
            <div class="tpq-data-row">
                <span>• <strong>NISN:</strong></span>
                <div>${nisnDisplay}</div>
            </div>

            <!-- No HP -->
            <div class="tpq-data-row">
                <span>• <strong>No HP:</strong></span>
                <div>${hpDisplay}</div>
            </div>

            <!-- Tingkat Kelas (Otomatis Dipilih) -->
            <div class="tpq-data-row">
                <span>• <strong>Tingkat Kelas:</strong></span>
                <div>
                    <span style="font-weight: 600; color: #fff;">${normalizedKelas || data.tingkat_kelas || '-'}</span>
                    <span style="font-size: 10.5px; background: #198754; color: white; padding: 1px 6px; border-radius: 3px; margin-left: 6px;">Dipilih ✓</span>
                </div>
            </div>

            <!-- Ayah Kandung -->
            <div class="tpq-data-row">
                <span>• <strong>Ayah:</strong></span>
                <div>
                    <span style="font-weight: 600; color: #fff;">${data.nama_ayah || '-'}</span>
                    ${data.nama_ayah ? `<button class="tpq-copy-badge" data-val="${data.nama_ayah}">Salin</button>` : ''}
                </div>
            </div>

            <!-- Ibu Kandung -->
            <div class="tpq-data-row">
                <span>• <strong>Ibu:</strong></span>
                <div>
                    <span style="font-weight: 600; color: #fff;">${data.nama_ibu || '-'}</span>
                    ${data.nama_ibu ? `<button class="tpq-copy-badge" data-val="${data.nama_ibu}">Salin</button>` : ''}
                </div>
            </div>

            <!-- Jenis Kelamin (Otomatis Dipilih) -->
            <div class="tpq-data-row" style="margin-bottom: 0;">
                <span>• <strong>Jenis Kelamin:</strong></span>
                <div>
                    <span style="font-weight: 600; color: #fff;">${data.jenis_kelamin || '-'}</span>
                    <span style="font-size: 10.5px; background: #198754; color: white; padding: 1px 6px; border-radius: 3px; margin-left: 6px;">Dipilih ✓</span>
                </div>
            </div>
        </div>

        <!-- BAGIAN BAWAH: TUGAS ANDA (YANG PERLU DIPILIH MANUAL DI EMIS) -->
        <div style="background: rgba(0,0,0,0.3); padding: 10px 12px; border-radius: 6px; margin-bottom: 10px;">
            <div style="font-weight: bold; color: #ffc107; margin-bottom: 6px;">
                👉 Tugas Anda (Pilih Manual di EMIS):
            </div>

            <!-- 1. Tanggal Masuk -->
            <div class="tpq-manual-row">
                <span>1. <strong>Tgl Masuk:</strong></span>
                <div style="display: flex; align-items: center; gap: 4px;">
                    <span style="font-size: 14px; font-weight: bold; color: #fff;">📅 ${tglMasukLabel}</span>
                    <button class="tpq-copy-badge" data-val="${data.tanggal_masuk || ''}" style="background: #ffc107; color: #000; border: none;">Salin</button>
                </div>
            </div>

            <!-- 2. Status Ortu & Wali -->
            <div class="tpq-manual-row" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
                <span>2. <strong>Status Ortu:</strong></span>
                <span style="color: #fff; font-size: 12px;">Masih Hidup | Wali: Ayah</span>
            </div>
        </div>

        <div style="font-size: 11.5px; opacity: 0.95; line-height: 1.4; margin-bottom: 10px;">
            Setelah semua opsi dipilih, tekan tombol <strong style="background: rgba(255,255,255,0.25); padding: 1px 6px; border-radius: 3px;">SIMPAN</strong> di pojok kanan bawah EMIS.<br>
            <span style="font-size: 11px; opacity: 0.85;">(Popup ini dapat digeser jika menutupi formulir)</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <button id="tpq-btn-lanjut-update-alamat" style="
                background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #ffffff; border: 1px solid rgba(255,255,255,0.4); padding: 9px 12px;
                border-radius: 5px; font-size: 12.5px; font-weight: bold; cursor: pointer; width: 100%;
                box-shadow: 0 2px 8px rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; gap: 6px;
            ">
                <span>🚀 Simpan & Lanjut Update Alamat di Kesiswaan</span>
            </button>
            <div style="display: flex; gap: 6px;">
                <button id="tpq-btn-tandai-selesai-step2" style="
                    background: #198754; color: #ffffff; border: none; padding: 7px 10px;
                    border-radius: 4px; font-size: 11.5px; font-weight: bold; cursor: pointer; flex: 1;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                ">✓ Selesai Saja</button>
                <button id="tpq-close-step2-alert" style="
                    background: rgba(255,255,255,0.25); color: white; border: none;
                    padding: 7px 10px; border-radius: 4px; font-size: 11.5px; cursor: pointer;
                ">Tutup</button>
            </div>
        </div>
    `;

    document.body.appendChild(div);
    document.getElementById('tpq-close-step2-alert').addEventListener('click', () => {
        chrome.storage.local.remove(['pendingSantriData', 'waitingForStep2']);
        div.remove();
        showTemporaryFloatingToast('Antrean pendaftaran santri ditutup.', 'danger');
    });

    // Jadikan popup Step 2 dapat digeser (draggable)
    const headerEl = document.getElementById('tpq-step2-header');
    makeDraggable(div, headerEl);

    // Pasang listener copy badge
    const copyBtns = div.querySelectorAll('.tpq-copy-badge');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const originalText = btn.innerText;
                const originalBg = btn.style.background;
                const originalColor = btn.style.color;
                btn.innerText = 'Tersalin!';
                btn.style.background = '#198754';
                btn.style.color = '#fff';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.background = originalBg;
                    btn.style.color = originalColor;
                }, 1200);
            }
        });
    });

    // Tombol Lanjut Update Alamat (Satu Paket Pendaftaran + Update Alamat)
    const btnLanjutUpdate = document.getElementById('tpq-btn-lanjut-update-alamat');
    if (btnLanjutUpdate && data.id_santri) {
        btnLanjutUpdate.addEventListener('click', () => {
            // 1. Beritahu TPQ Smart status pendaftaran PPDB selesai di database
            try {
                chrome.runtime.sendMessage({
                    action: 'FORM_FILLED_NOTIFY',
                    id_santri: data.id_santri
                });
            } catch (_) {}

            // 2. Siapkan antrean untuk update alamat di Kesiswaan EMIS
            chrome.storage.local.set({
                pendingUpdateAlamatData: data,
                currentTask: 'UPDATE_ALAMAT',
                waitingForUpdateStep2: false
            }, () => {
                // Bersihkan antrean pendaftaran PPDB
                chrome.storage.local.remove(['pendingSantriData', 'waitingForStep2']);

                btnLanjutUpdate.innerHTML = '🔄 Mengalihkan ke Kesiswaan...';
                btnLanjutUpdate.style.background = '#0b5ed7';
                btnLanjutUpdate.disabled = true;

                showTemporaryFloatingToast('Pendaftaran selesai! Mengalihkan ke menu Kesiswaan untuk update alamat...', 'success');

                setTimeout(() => {
                    div.remove();
                    window.location.href = "https://emis.kemenag.go.id/kesiswaan";
                }, 600);
            });
        });
    }

    // Tombol tandai selesai saja (tanpa lanjut alamat)
    const btnSelesai = document.getElementById('tpq-btn-tandai-selesai-step2');
    if (btnSelesai && data.id_santri) {
        btnSelesai.addEventListener('click', () => {
            try {
                chrome.runtime.sendMessage({
                    action: 'FORM_FILLED_NOTIFY',
                    id_santri: data.id_santri
                });
                btnSelesai.innerText = '✅ Ditandai Selesai!';
                btnSelesai.style.background = '#059669';
                btnSelesai.disabled = true;
                setTimeout(() => { div.remove(); }, 1800);
            } catch (_) {
                btnSelesai.innerText = '✅ Selesai!';
            }
        });
    }
}

/**
 * Banner mengambang jika halaman EMIS belum di posisi formulir
 */
function showPendingBanner(santri, currentStep) {
    showStep0StartPopup(santri);
}

/**
 * ============================================================================
 * MODUL: UPDATE ALAMAT SANTRI KE EMIS 4.0 (Kesiswaan -> Tab DATA ALAMAT)
 * ============================================================================
 */

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Deteksi apakah halaman berada pada Step 1 (Pencarian Kesiswaan), Step 2 (Tab DATA SANTRI), atau Step 3 (Tab DATA ALAMAT)
 */
function detectUpdateAlamatStage() {
    const textContent = (document.body.innerText || document.body.textContent || '').toUpperCase();
    const currentUrl = window.location.href.toLowerCase();

    // Ciri Step 3: Tab DATA ALAMAT aktif (ada teks TEMPAT TINGGAL DOMISILI atau STATUS KEPEMILIKAN RUMAH)
    const hasDataAlamatText = textContent.includes('TEMPAT TINGGAL DOMISILI') || textContent.includes('STATUS KEPEMILIKAN RUMAH');
    if (hasDataAlamatText) {
        return 3;
    }

    // Ciri Step 2: Halaman Detail Santri (Tab DATA SANTRI aktif / pertama kali masuk detail)
    const hasTabs = textContent.includes('DATA SANTRI') && (textContent.includes('DATA ORANG TUA') || textContent.includes('DATA ALAMAT'));
    const hasDetailUrl = currentUrl.includes('/detail') || currentUrl.includes('/edit') || currentUrl.includes('/kesiswaan/');
    const hasDetailHeader = textContent.includes('DETAIL SANTRI') || textContent.includes('YANG MEMBIAYAI SEKOLAH');

    if (hasTabs || hasDetailHeader || (hasDetailUrl && textContent.includes('DATA SANTRI'))) {
        return 2;
    }

    // Ciri Step 1 (Daftar Santri Kesiswaan)
    return 1;
}

/**
 * Inisialisasi alur kerja Update Data Santri (3 Step)
 */
function initUpdateAlamatProcess(santriData) {
    if (!santriData) return;
    const stage = detectUpdateAlamatStage();
    console.log('[TPQ-EMIS Bridge] Menjalankan Update Data Santri: Tahap', stage, 'untuk:', santriData.nama_santri);

    if (stage === 1) {
        executeStep1UpdateAlamat(santriData);
    } else if (stage === 2) {
        executeStep2DataSantri(santriData);
    } else if (stage === 3) {
        executeStep3DataAlamat(santriData);
    }
}

/**
 * Mencari input pencarian nama / NISN pada halaman Kesiswaan EMIS
 */
function findKesiswaanSearchInput() {
    const inputs = Array.from(document.querySelectorAll('input[type="text"], input[type="search"], input:not([type])'));
    for (const inp of inputs) {
        const ph = (inp.placeholder || '').toUpperCase();
        const aria = (inp.getAttribute('aria-label') || '').toUpperCase();
        const idName = ((inp.id || '') + ' ' + (inp.name || '')).toUpperCase();
        if (ph.includes('CARI') || ph.includes('NAMA') || ph.includes('NISN') || aria.includes('CARI') || idName.includes('SEARCH')) {
            return inp;
        }
    }
    return null;
}

/**
 * Eksekusi Step 1 Update Alamat: Pencarian Nama Santri
 */
function executeStep1UpdateAlamat(santriData) {
    console.log('[TPQ-EMIS Bridge] Eksekusi Step 1 Update Alamat: Mencari nama santri...');

    let attempts = 0;
    const maxAttempts = 20;

    const timer = setInterval(() => {
        attempts++;
        const searchInput = findKesiswaanSearchInput();

        if (searchInput) {
            clearInterval(timer);
            console.log('[TPQ-EMIS Bridge] Kolom pencarian Kesiswaan ditemukan.');
            setNativeValue(searchInput, santriData.nama_santri);

            // Trigger Enter event pada input pencarian
            searchInput.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', code: 'Enter', keyCode: 13, bubbles: true }));
            searchInput.dispatchEvent(new KeyboardEvent('keyup', { key: 'Enter', code: 'Enter', keyCode: 13, bubbles: true }));

            showUpdateAlamatStep1Popup(santriData);
        } else if (attempts >= maxAttempts) {
            clearInterval(timer);
            console.warn('[TPQ-EMIS Bridge] Kolom pencarian belum terdeteksi, menampilkan popup panduan.');
            showUpdateAlamatStep1Popup(santriData);
        }
    }, 250);

    // Listener global: jika tombol Ubah Data diklik, bersiap untuk Step 2
    document.addEventListener('click', (e) => {
        const target = e.target;
        if (!target) return;
        const btn = target.closest('button, [role="button"], a') || target;
        const txt = (btn.innerText || btn.textContent || '').trim().toUpperCase();
        if (txt.includes('UBAH DATA') || txt.includes('EDIT')) {
            console.log('[TPQ-EMIS Bridge] Tombol Ubah Data diklik! Bersiap untuk Step 2...');
            const p1 = document.getElementById('tpq-update-alamat-step1-popup');
            if (p1) p1.remove();
            chrome.storage.local.set({ waitingForUpdateStep2: true });
        }
    }, true);
}

/**
 * Notifikasi popup mengambang sementara (toast)
 */
function showTemporaryFloatingToast(message, type = 'danger') {
    const oldToast = document.getElementById('tpq-temp-toast');
    if (oldToast) oldToast.remove();

    const toast = document.createElement('div');
    toast.id = 'tpq-temp-toast';
    const bg = type === 'danger' ? '#dc3545' : (type === 'success' ? '#198754' : '#0d6efd');
    toast.style.cssText = `
        position: fixed;
        top: 25px;
        right: 25px;
        z-index: 9999999;
        background: ${bg};
        color: #ffffff;
        padding: 12px 18px;
        border-radius: 8px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    toast.innerHTML = `<span style="font-size: 16px;">⏹</span> <span>${escapeHtml(message)}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(() => toast.remove(), 400);
    }, 2800);
}

/**
 * Batalkan / Stop proses Update Data dan bersihkan antrean storage
 */
function cancelUpdateAlamatProcess(customMsg = null) {
    isStep2Executing = false;
    isStep2Done = false;
    isStep3Executing = false;
    isStep3Done = false;
    chrome.storage.local.remove(['pendingUpdateAlamatData', 'currentTask', 'waitingForUpdateStep2', 'waitingForUpdateStep3'], () => {
        console.log('[TPQ-EMIS Bridge] Antrean Update Data dibersihkan dari storage.');
    });
    const p1 = document.getElementById('tpq-update-alamat-step1-popup');
    if (p1) p1.remove();
    const p2 = document.getElementById('tpq-update-santri-step2-popup');
    if (p2) p2.remove();
    const p3 = document.getElementById('tpq-update-alamat-step3-popup') || document.getElementById('tpq-update-alamat-step2-popup');
    if (p3) p3.remove();

    const msg = customMsg || 'Proses update data santri dibatalkan. Antrean telah dibersihkan.';
    showTemporaryFloatingToast(msg, 'danger');
}

/**
 * Popup Bantuan Step 1 Update Alamat
 */
function showUpdateAlamatStep1Popup(data) {
    const existing = document.getElementById('tpq-update-alamat-step1-popup');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.id = 'tpq-update-alamat-step1-popup';
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 25px;
        z-index: 999999;
        width: 380px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
    `;

    div.innerHTML = `
        <div id="tpq-update-step1-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 12px 16px; font-weight: bold; font-size: 14px; display: flex; justify-content: space-between; align-items: center; cursor: move;">
            <span><i class="fas fa-map-marker-alt"></i> ⚡ TPQ Smart: Cari Santri di EMIS (Step 1/3)</span>
            <span id="tpq-close-update-step1" style="cursor: pointer; font-size: 18px; line-height: 1; padding: 2px 6px;" title="Tutup dan bersihkan antrean">&times;</span>
        </div>
        <div style="padding: 14px 16px; max-height: 75vh; overflow-y: auto;">
            <div style="background: #e7f1ff; border: 1px solid #b6d4fe; border-radius: 6px; padding: 10px 12px; margin-bottom: 12px;">
                <div style="font-size: 11px; text-transform: uppercase; color: #084298; font-weight: bold; letter-spacing: 0.5px;">Santri yang Dicari:</div>
                <div style="font-size: 15px; font-weight: bold; color: #0d6efd; margin-top: 2px;">${escapeHtml(data.nama_santri)}</div>
                <div style="font-size: 12px; color: #495057; margin-top: 4px;">
                    NIK: <strong>${escapeHtml(data.nik || '-')}</strong> 
                    ${data.nik ? `<button class="tpq-copy-badge" data-val="${escapeHtml(data.nik)}" style="margin-left: 6px; padding: 1px 6px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>` : ''}
                </div>
                ${data.nisn ? `<div style="font-size: 12px; color: #495057; margin-top: 2px;">NISN: <strong>${escapeHtml(data.nisn)}</strong> <button class="tpq-copy-badge" data-val="${escapeHtml(data.nisn)}" style="margin-left: 6px; padding: 1px 6px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button></div>` : ''}
            </div>

            <div style="background: #fff3cd; border: 1px solid #ffe69c; border-radius: 6px; padding: 10px 12px; margin-bottom: 14px; color: #664d03; font-size: 12px; line-height: 1.5;">
                <strong><i class="fas fa-info-circle"></i> Petunjuk Langkah 1:</strong><br>
                1. Nama lengkap santri telah diisikan otomatis ke kolom <strong>Cari Nama / NISN</strong>.<br>
                2. Tekan <strong>Enter</strong> pada kolom cari jika daftar belum memuat.<br>
                3. Klik tombol hijau <strong>[Ubah Data]</strong> pada baris santri untuk masuk ke Tab DATA SANTRI.
            </div>

            <div style="display: flex; gap: 8px;">
                <button id="tpq-btn-cancel-update-step1" style="background: #fff; color: #dc3545; border: 1px solid #dc3545; padding: 9px 12px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px;" title="Batalkan proses antrean santri ini agar dapat memilih proses lain">
                    ⏹ <span>Stop / Batal</span>
                </button>
                <button id="tpq-btn-force-step2" style="flex: 1; background: #198754; color: #fff; border: none; padding: 9px 12px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    ⚡ Masuk ke Step 2 (DATA SANTRI)
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(div);
    makeDraggable(div, document.getElementById('tpq-update-step1-header'));

    document.getElementById('tpq-close-update-step1').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data ditutup dan antrean telah dibersihkan.');
    });

    document.getElementById('tpq-btn-cancel-update-step1').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data santri dibatalkan. Anda dapat memilih santri lain di TPQ Smart.');
    });

    div.querySelectorAll('.tpq-copy-badge').forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const oldTxt = btn.innerText;
                btn.innerText = 'Tersalin!';
                setTimeout(() => { btn.innerText = oldTxt; }, 1200);
            }
        });
    });

    document.getElementById('tpq-btn-force-step2').addEventListener('click', () => {
        div.remove();
        executeStep2DataSantri(data);
    });
}

/**
 * Memastikan tab DATA ALAMAT aktif (hanya mengklik jika tab belum aktif)
 */
function ensureDataAlamatTabActive() {
    // Jika tab DATA ALAMAT sudah aktif (form alamat sudah tampil di layar), JANGAN klik lagi agar tidak me-reset form
    if (detectUpdateAlamatStage() === 3) {
        return true;
    }

    const tabs = Array.from(document.querySelectorAll('a, button, [role="tab"], li, span, div'));
    for (const t of tabs) {
        const txt = (t.innerText || t.textContent || '').trim().toUpperCase();
        if (txt === 'DATA ALAMAT') {
            console.log('[TPQ-EMIS Bridge] Menemukan tab DATA ALAMAT, mengaktifkan...');
            try { t.click(); } catch (_) {}
            return true;
        }
    }
    return false;
}

/**
 * Auto-fill field teks domisili Ayah dan Centang Checkbox Ibu di tab DATA ALAMAT
 */
function fillUpdateAlamatTextFields(santri) {
    console.log('[TPQ-EMIS Bridge] Mengisi field teks domisili Ayah di tab DATA ALAMAT...');
    const inputs = Array.from(document.querySelectorAll('input[type="text"], input[type="number"], input:not([type]), textarea'));

    let filledRt = false, filledRw = false, filledAlamat = false, filledKodePos = false;

    inputs.forEach(inp => {
        if (inp.disabled || inp.readOnly) return;

        let labelText = (inp.placeholder || '') + ' ' + (inp.name || '') + ' ' + (inp.id || '');
        let p = inp.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            const txt = (p.innerText || p.textContent || '').trim();
            if (txt.length < 50) labelText += ' ' + txt;
            p = p.parentElement;
        }
        labelText = labelText.toUpperCase();

        // Jangan isi otomatis field Koordinat (diisi manual oleh user jika diperlukan)
        if (labelText.includes('KOORDINAT')) return;

        if (!filledRt && labelText.includes('RT') && !labelText.includes('ALAMAT') && !labelText.includes('RUKUN') && !labelText.includes('KOTA')) {
            if (santri.ayah_rt) {
                setNativeValue(inp, santri.ayah_rt);
                filledRt = true;
            }
        } else if (!filledRw && labelText.includes('RW') && !labelText.includes('ALAMAT') && !labelText.includes('RUKUN')) {
            if (santri.ayah_rw) {
                setNativeValue(inp, santri.ayah_rw);
                filledRw = true;
            }
        } else if (!filledAlamat && (labelText.includes('ALAMAT') || inp.tagName === 'TEXTAREA') && !labelText.includes('EMAIL')) {
            if (santri.ayah_alamat) {
                setNativeValue(inp, santri.ayah_alamat);
                filledAlamat = true;
            }
        } else if (!filledKodePos && (labelText.includes('KODEPOS') || labelText.includes('KODE POS'))) {
            if (santri.ayah_kode_pos) {
                setNativeValue(inp, santri.ayah_kode_pos);
                filledKodePos = true;
            }
        }
    });

    // Auto-check checkbox "Sama dengan ayah kandung" untuk Ibu & Wali
    const checkboxes = Array.from(document.querySelectorAll('input[type="checkbox"]'));
    checkboxes.forEach(cb => {
        let cbText = (cb.name || '') + ' ' + (cb.id || '');
        let p = cb.parentElement;
        for (let i = 0; i < 3 && p; i++) {
            cbText += ' ' + (p.innerText || p.textContent || '');
            p = p.parentElement;
        }
        cbText = cbText.toUpperCase();
        if (cbText.includes('SAMA DENGAN AYAH') || cbText.includes('SAMA DENGAN AYAH KANDUNG')) {
            if (!cb.checked) {
                cb.checked = true;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
                cb.dispatchEvent(new Event('click', { bubbles: true }));
            }
        }
    });

    // Auto-select dropdown khusus di tab DATA ALAMAT (Hanya 3 dropdown yang diizinkan otomatis: Status Kepemilikan Rumah, Status Mukim, Status Tempat Tinggal)
    const selects = Array.from(document.querySelectorAll('select'));
    if (selects.length > 0) {
        console.log(`[TPQ-EMIS Bridge] Menemukan ${selects.length} elemen <select> di tab DATA ALAMAT, menyetel pilihan otomatis (Status Rumah, Mukim, Tempat Tinggal)...`);

        selects.forEach(sel => {
            const options = Array.from(sel.options || sel.querySelectorAll('option'));
            const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase());
            const optionValues = options.map(o => String(o.value || '').trim());

            let contextText = (sel.name || '') + ' ' + (sel.id || '');
            let p = sel.parentElement;
            for (let i = 0; i < 5 && p; i++) {
                contextText += ' ' + (p.innerText || p.textContent || '');
                p = p.parentElement;
            }
            contextText = contextText.toUpperCase();

            // 1. Status Kepemilikan Rumah (Ayah)
            // Ciri: memiliki opsi value '9' atau 'Milik Sendiri', atau teks label KEPEMILIKAN RUMAH
            if (optionValues.includes('9') || optionTexts.some(t => t.includes('milik sendiri')) || contextText.includes('KEPEMILIKAN RUMAH')) {
                const target = santri.ayah_status_kepemilikan_rumah || 'Milik Sendiri';
                console.log('[TPQ-EMIS Bridge] Menyetel Status Kepemilikan Rumah:', target);
                setNativeSelectValue(sel, target);
            }

            // 2. Status Mukim (Santri)
            // Ciri: memiliki opsi 'tidak mukim' dan 'mukim', atau teks label STATUS MUKIM
            else if ((optionTexts.includes('tidak mukim') && optionTexts.includes('mukim')) || (contextText.includes('MUKIM') && !contextText.includes('TEMPAT TINGGAL'))) {
                const target = santri.santri_status_mukim || 'Tidak Mukim';
                console.log('[TPQ-EMIS Bridge] Menyetel Status Mukim:', target);
                setNativeSelectValue(sel, target);
            }

            // 3. Status Tempat Tinggal (Santri)
            // Ciri: memiliki opsi value '18' atau 'tinggal dengan ayah', atau teks label STATUS TEMPAT TINGGAL
            else if (optionValues.includes('18') || optionTexts.some(t => t.includes('tinggal dengan ayah')) || (contextText.includes('STATUS TEMPAT TINGGAL') && !contextText.includes('WALI'))) {
                const target = santri.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung';
                console.log('[TPQ-EMIS Bridge] Menyetel Status Tempat Tinggal Santri:', target);
                setNativeSelectValue(sel, target);
            }

            // Catatan: Jarak Tempuh, Transportasi, dan Waktu Tempuh sengaja TIDAK diotomatiskan (manual via panduan popup)
        });
    }
}

/**
 * Mencari dan mengisi dropdown "Yang Membiayai Sekolah" di tab DATA SANTRI
 */
function fillDataSantriYangMembiayaiSekolah(santriData) {
    console.log('[TPQ-EMIS Bridge] Mencari dan menyetel dropdown "Yang Membiayai Sekolah" di tab DATA SANTRI...');
    const selects = Array.from(document.querySelectorAll('select'));
    const targetValue = santriData.yang_biaya_sekolah || 'Orang Tua';

    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase());

        let contextText = (sel.name || '') + ' ' + (sel.id || '');
        let p = sel.parentElement;
        for (let i = 0; i < 5 && p; i++) {
            contextText += ' ' + (p.innerText || p.textContent || '');
            p = p.parentElement;
        }
        contextText = contextText.toUpperCase();

        const isContextMatch = contextText.includes('MEMBIAYAI') || contextText.includes('BIAYA');
        const hasOrangTuaOption = optionTexts.some(t => t.includes('orang tua'));

        if (isContextMatch || (hasOrangTuaOption && options.length <= 6)) {
            console.log('[TPQ-EMIS Bridge] Menemukan dropdown Yang Membiayai Sekolah, menyetel ke:', targetValue);
            setNativeSelectValue(sel, targetValue);
            return true;
        }
    }
    console.warn('[TPQ-EMIS Bridge] Dropdown "Yang Membiayai Sekolah" belum terdeteksi pada layar.');
    return false;
}

let isStep2Executing = false;
let isStep2Done = false;

/**
 * Eksekusi Step 2: Tab DATA SANTRI (Dropdown Yang Membiayai Sekolah)
 */
function executeStep2DataSantri(santriData) {
    if (!santriData) return;
    if (isStep2Executing || isStep2Done) return;
    isStep2Executing = true;

    console.log('[TPQ-EMIS Bridge] Menjalankan Step 2 Update Data (DATA SANTRI) untuk:', santriData.nama_santri);

    const p1 = document.getElementById('tpq-update-alamat-step1-popup');
    if (p1) p1.remove();

    let attempts = 0;
    const maxAttempts = 10;

    const timer = setInterval(() => {
        attempts++;
        const filled = fillDataSantriYangMembiayaiSekolah(santriData);

        if (filled || attempts >= 4) {
            clearInterval(timer);
            isStep2Executing = false;
            isStep2Done = true;
            showUpdateSantriStep2Popup(santriData);
        }
    }, 350);
}

/**
 * Popup Panduan Tab DATA SANTRI (Step 2/3)
 */
function showUpdateSantriStep2Popup(data) {
    const existing = document.getElementById('tpq-update-santri-step2-popup');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.id = 'tpq-update-santri-step2-popup';
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 25px;
        z-index: 999999;
        width: 430px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 10px;
        box-shadow: 0 10px 35px rgba(0,0,0,0.3);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
    `;

    const targetBiaya = data.yang_biaya_sekolah || 'Orang Tua';

    div.innerHTML = `
        <div id="tpq-update-santri-step2-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 12px 16px; font-weight: bold; font-size: 14px; display: flex; justify-content: space-between; align-items: center; cursor: move;">
            <span><i class="fas fa-user-edit"></i> ⚡ TPQ Smart: Tab DATA SANTRI (Step 2/3)</span>
            <span id="tpq-close-update-santri-step2" style="cursor: pointer; font-size: 18px; line-height: 1; padding: 2px 6px;">&times;</span>
        </div>
        <div style="padding: 14px 16px;">
            <div style="background: #e7f1ff; border: 1px solid #b6d4fe; border-radius: 6px; padding: 8px 12px; margin-bottom: 12px;">
                <div style="font-size: 11px; text-transform: uppercase; color: #084298; font-weight: bold;">Santri:</div>
                <div style="font-size: 15px; font-weight: bold; color: #0d6efd;">${escapeHtml(data.nama_santri)}</div>
                <div style="font-size: 11px; color: #6c757d;">NIK: ${escapeHtml(data.nik || '-')}</div>
            </div>

            <div style="background: #d1e7dd; border: 1px solid #badbcc; border-radius: 6px; padding: 10px 12px; margin-bottom: 12px; font-size: 12px; color: #0f5132;">
                <div style="font-weight: bold; margin-bottom: 3px;">✓ Dropdown "Yang Membiayai Sekolah":</div>
                <div>Otomatis disetel ke: <strong>${escapeHtml(targetBiaya)}</strong></div>
            </div>

            <div style="font-size: 12px; line-height: 1.5; color: #495057; margin-bottom: 14px; background: #fff3cd; border: 1px solid #ffe69c; border-radius: 6px; padding: 10px 12px;">
                <div style="font-weight: bold; color: #664d03; margin-bottom: 4px;">📌 Petunjuk Langkah:</div>
                <ol style="margin: 0; padding-left: 18px;">
                    <li>Periksa pilihan <em>Yang Membiayai Sekolah</em> pada form EMIS.</li>
                    <li>Klik tombol <strong>Simpan</strong> di bagian bawah EMIS jika Anda ingin menyimpan perubahan data santri.</li>
                    <li>Setelah itu, klik tombol <strong>Lanjut ke Tab DATA ALAMAT</strong> di bawah ini.</li>
                </ol>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button id="tpq-btn-lanjut-step3" style="width: 100%; background: #0d6efd; color: #fff; border: none; padding: 10px 12px; border-radius: 6px; font-weight: bold; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <span>➡️ Lanjut ke Tab DATA ALAMAT (Step 3)</span>
                </button>
                <button id="tpq-btn-refill-biaya" style="width: 100%; background: #f8f9fa; color: #0d6efd; border: 1px solid #0d6efd; padding: 7px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    ⚡ Setel Ulang Dropdown "Yang Membiayai Sekolah"
                </button>
                <button id="tpq-btn-cancel-update-santri-step2" style="width: 100%; background: #fff5f5; color: #dc3545; border: 1px solid #f5c2c7; padding: 7px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    ⏹ Stop / Batalkan Proses Santri Ini
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(div);
    makeDraggable(div, document.getElementById('tpq-update-santri-step2-header'));

    document.getElementById('tpq-close-update-santri-step2').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data santri ditutup dan antrean dibersihkan.');
    });

    document.getElementById('tpq-btn-cancel-update-santri-step2').addEventListener('click', () => {
        cancelUpdateAlamatProcess(`Proses update data untuk ${data.nama_santri || 'santri'} telah dibatalkan.`);
    });

    document.getElementById('tpq-btn-refill-biaya').addEventListener('click', () => {
        fillDataSantriYangMembiayaiSekolah(data);
    });

    document.getElementById('tpq-btn-lanjut-step3').addEventListener('click', () => {
        div.remove();
        isStep3Done = false;
        ensureDataAlamatTabActive();
        setTimeout(() => {
            executeStep3DataAlamat(data);
        }, 400);
    });
}

let isStep3Executing = false;
let isStep3Done = false;

/**
 * Eksekusi Step 3: Tab DATA ALAMAT (Dijalankan SEKALI SAJA tanpa looping)
 */
function executeStep3DataAlamat(santriData) {
    if (!santriData) return;
    if (isStep3Executing || isStep3Done) {
        console.log('[TPQ-EMIS Bridge] Step 3 sudah dijalankan sebelumnya, melewati panggilan ganda.');
        return;
    }
    isStep3Executing = true;

    console.log('[TPQ-EMIS Bridge] Menjalankan Step 3 Update Alamat untuk:', santriData.nama_santri);

    // Pastikan popup Step 1 & Step 2 tertutup
    const p1 = document.getElementById('tpq-update-alamat-step1-popup');
    if (p1) p1.remove();
    const p2 = document.getElementById('tpq-update-santri-step2-popup');
    if (p2) p2.remove();

    // 1. Pastikan tab DATA ALAMAT aktif hanya jika belum aktif
    ensureDataAlamatTabActive();

    // 2. Beri jeda 400ms agar React selesai render tab alamat, lalu isi SEKALI SAJA
    setTimeout(() => {
        fillUpdateAlamatTextFields(santriData);
        isStep3Executing = false;
        isStep3Done = true;
        showUpdateAlamatStep3Popup(santriData);
    }, 400);
}

/**
 * Popup Panduan Interaktif Tab DATA ALAMAT (Step 3/3)
 */
function showUpdateAlamatStep3Popup(data) {
    const existing = document.getElementById('tpq-update-alamat-step3-popup') || document.getElementById('tpq-update-alamat-step2-popup');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.id = 'tpq-update-alamat-step3-popup';
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 25px;
        z-index: 999999;
        width: 440px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 10px;
        box-shadow: 0 10px 35px rgba(0,0,0,0.3);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
    `;

    div.innerHTML = `
        <div id="tpq-update-step3-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 12px 16px; font-weight: bold; font-size: 14px; display: flex; justify-content: space-between; align-items: center; cursor: move;">
            <span><i class="fas fa-map-marked-alt"></i> ⚡ TPQ Smart: Tab DATA ALAMAT (Step 3/3)</span>
            <span id="tpq-close-update-step3" style="cursor: pointer; font-size: 18px; line-height: 1; padding: 2px 6px;">&times;</span>
        </div>
        <div style="padding: 14px 16px; max-height: 75vh; overflow-y: auto;">
            <div style="background: #e7f1ff; border: 1px solid #b6d4fe; border-radius: 6px; padding: 8px 12px; margin-bottom: 12px;">
                <div style="font-size: 11px; text-transform: uppercase; color: #084298; font-weight: bold;">Santri:</div>
                <div style="font-size: 15px; font-weight: bold; color: #0d6efd;">${escapeHtml(data.nama_santri)}</div>
                <div style="font-size: 11px; color: #6c757d;">NIK: ${escapeHtml(data.nik || '-')}</div>
            </div>

            <div style="font-weight: bold; color: #0d6efd; font-size: 13px; margin-bottom: 6px; border-bottom: 1px solid #dee2e6; padding-bottom: 3px;">
                1. Domisili Ayah Kandung:
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 12px;">
                <tr style="background: #f8f9fa;">
                    <td style="padding: 4px; color: #198754; font-weight: bold; width: 45%;">Status Kepemilikan (Auto):</td>
                    <td style="padding: 4px; font-weight: bold; color: #198754;">
                        ${escapeHtml(data.ayah_status_kepemilikan_rumah || 'Milik Sendiri')}
                        <button class="tpq-copy-badge" data-val="${escapeHtml(data.ayah_status_kepemilikan_rumah || 'Milik Sendiri')}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #198754; background: #fff; color: #198754; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; color: #495057;">Provinsi:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.ayah_provinsi || '-')}
                        ${data.ayah_provinsi ? `<button class="tpq-copy-badge" data-val="${escapeHtml(data.ayah_provinsi)}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>` : ''}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; color: #495057;">Kabupaten / Kota:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.ayah_kabupaten_kota || '-')}
                        ${data.ayah_kabupaten_kota ? `<button class="tpq-copy-badge" data-val="${escapeHtml(data.ayah_kabupaten_kota)}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>` : ''}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; color: #495057;">Kecamatan:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.ayah_kecamatan || '-')}
                        ${data.ayah_kecamatan ? `<button class="tpq-copy-badge" data-val="${escapeHtml(data.ayah_kecamatan)}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>` : ''}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; color: #495057;">Kelurahan / Desa:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.ayah_kelurahan_desa || '-')}
                        ${data.ayah_kelurahan_desa ? `<button class="tpq-copy-badge" data-val="${escapeHtml(data.ayah_kelurahan_desa)}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>` : ''}
                    </td>
                </tr>
                <tr style="background: #f8f9fa;">
                    <td style="padding: 4px; color: #198754; font-weight: bold;">RT / RW (Auto-fill):</td>
                    <td style="padding: 4px; font-weight: bold; color: #198754;">
                        ${escapeHtml(data.ayah_rt || '-')} / ${escapeHtml(data.ayah_rw || '-')}
                    </td>
                </tr>
                <tr style="background: #f8f9fa;">
                    <td style="padding: 4px; color: #198754; font-weight: bold;">Alamat (Auto-fill):</td>
                    <td style="padding: 4px; font-weight: bold; color: #198754;">
                        ${escapeHtml(data.ayah_alamat || '-')}
                    </td>
                </tr>
                <tr style="background: #f8f9fa;">
                    <td style="padding: 4px; color: #198754; font-weight: bold;">Kode Pos (Auto-fill):</td>
                    <td style="padding: 4px; font-weight: bold; color: #198754;">
                        ${escapeHtml(data.ayah_kode_pos || '-')}
                    </td>
                </tr>
            </table>

            <div style="font-weight: bold; color: #0d6efd; font-size: 13px; margin-top: 10px; margin-bottom: 6px; border-bottom: 1px solid #dee2e6; padding-bottom: 3px;">
                2. Ibu Kandung & Wali:
            </div>
            <div style="background: #f8f9fa; border-radius: 4px; padding: 6px 10px; font-size: 12px; margin-bottom: 10px;">
                <span style="color: #198754; font-weight: bold;">✓ Otomatis Dicentang:</span> Centang <em>"Sama dengan ayah kandung"</em> pada bagian Ibu Kandung dan Wali.
            </div>

            <div style="font-weight: bold; color: #0d6efd; font-size: 13px; margin-top: 10px; margin-bottom: 6px; border-bottom: 1px solid #dee2e6; padding-bottom: 3px;">
                3. Bagian Santri & Mobilitas Lembaga:
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 12px;">
                <tr style="background: #f8f9fa;">
                    <td style="padding: 4px; color: #198754; font-weight: bold; width: 45%;">Status Mukim (Auto):</td>
                    <td style="padding: 4px; font-weight: bold; color: #198754;">
                        ${escapeHtml(data.santri_status_mukim || 'Tidak Mukim')}
                        <button class="tpq-copy-badge" data-val="${escapeHtml(data.santri_status_mukim || 'Tidak Mukim')}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #198754; background: #fff; color: #198754; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>
                    </td>
                </tr>
                <tr style="background: #f8f9fa;">
                    <td style="padding: 4px; color: #198754; font-weight: bold;">Status Tempat Tinggal (Auto):</td>
                    <td style="padding: 4px; font-weight: bold; color: #198754;">
                        ${escapeHtml(data.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung')}
                        <button class="tpq-copy-badge" data-val="${escapeHtml(data.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung')}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #198754; background: #fff; color: #198754; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; color: #495057;">Jarak Tempuh:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.santri_jarak_tempuh || 'Kurang dari 5 km')}
                        <button class="tpq-copy-badge" data-val="${escapeHtml(data.santri_jarak_tempuh || 'Kurang dari 5 km')}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; color: #495057;">Transportasi:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.santri_transportasi || 'Jalan Kaki')}
                        <button class="tpq-copy-badge" data-val="${escapeHtml(data.santri_transportasi || 'Jalan Kaki')}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; color: #495057;">Waktu Tempuh:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.santri_waktu_tempuh || '1-10 menit')}
                        <button class="tpq-copy-badge" data-val="${escapeHtml(data.santri_waktu_tempuh || '1-10 menit')}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>
                    </td>
                </tr>
                ${data.santri_titik_koordinat ? `<tr>
                    <td style="padding: 4px; color: #495057;">Koordinat:</td>
                    <td style="padding: 4px; font-weight: bold;">
                        ${escapeHtml(data.santri_titik_koordinat)}
                        <button class="tpq-copy-badge" data-val="${escapeHtml(data.santri_titik_koordinat)}" style="padding: 1px 6px; margin-left: 4px; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 3px; font-size: 10px; cursor: pointer;">Salin</button>
                    </td>
                </tr>` : ''}
            </table>

            <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 14px;">
                <div style="display: flex; gap: 8px;">
                    <button id="tpq-btn-refill-alamat" style="flex: 1; background: #f8f9fa; color: #0d6efd; border: 1px solid #0d6efd; padding: 8px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                        ⚡ Isi Ulang Field & Dropdown
                    </button>
                    <button id="tpq-btn-selesai-alamat" style="flex: 1.5; background: #198754; color: #fff; border: none; padding: 8px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                        ✓ Tandai Selesai di TPQ Smart
                    </button>
                </div>
                <button id="tpq-btn-cancel-update-step3" style="width: 100%; background: #fff5f5; color: #dc3545; border: 1px solid #f5c2c7; padding: 8px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;" title="Batalkan proses update data santri ini agar dapat memilih proses lain">
                    ⏹ <span>Stop / Batalkan Proses Santri Ini</span>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(div);
    makeDraggable(div, document.getElementById('tpq-update-step3-header'));

    document.getElementById('tpq-close-update-step3').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data alamat ditutup dan antrean dibersihkan.');
    });

    document.getElementById('tpq-btn-cancel-update-step3').addEventListener('click', () => {
        cancelUpdateAlamatProcess(`Proses update data untuk ${data.nama_santri || 'santri'} telah dibatalkan.`);
    });

    div.querySelectorAll('.tpq-copy-badge').forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const oldTxt = btn.innerText;
                btn.innerText = 'Tersalin!';
                setTimeout(() => { btn.innerText = oldTxt; }, 1200);
            }
        });
    });

    document.getElementById('tpq-btn-refill-alamat').addEventListener('click', () => {
        ensureDataAlamatTabActive();
        fillUpdateAlamatTextFields(data);
    });

    const btnSelesai = document.getElementById('tpq-btn-selesai-alamat');
    if (btnSelesai && data.id_santri) {
        btnSelesai.addEventListener('click', () => {
            try {
                chrome.runtime.sendMessage({
                    action: 'UPDATE_ALAMAT_SUCCESS_NOTIFY',
                    id_santri: data.id_santri
                });
                isStep2Executing = false;
                isStep2Done = false;
                isStep3Executing = false;
                isStep3Done = false;
                chrome.storage.local.remove(['pendingUpdateAlamatData', 'currentTask', 'waitingForUpdateStep2', 'waitingForUpdateStep3']);
                btnSelesai.innerText = '✅ Ditandai Selesai!';
                btnSelesai.style.background = '#059669';
                btnSelesai.disabled = true;
                setTimeout(() => { div.remove(); }, 1800);
            } catch (err) {
                console.error('[TPQ-EMIS Bridge] Gagal kirim notifikasi selesai:', err);
                btnSelesai.innerText = '✅ Selesai!';
            }
        });
    }
}
