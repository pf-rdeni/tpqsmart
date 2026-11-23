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
        console.log('[TPQ-EMIS Bridge] Tombol SELANJUTNYA diklik, menutup popup Step 1...');
        const step1Alert = document.getElementById('tpq-step1-alert');
        if (step1Alert) step1Alert.remove();
    }
}, true);

// Cek antrean data santri saat halaman pertama kali dibuka / direload
chrome.storage.local.get(['pendingSantriData', 'waitingForStep2'], (result) => {
    if (result && result.pendingSantriData) {
        const santri = result.pendingSantriData;
        console.log('[TPQ-EMIS Bridge] Ditemukan data santri dalam antrean:', santri.nama_santri);
        
        const currentStep = detectCurrentEmisStep();
        console.log('[TPQ-EMIS Bridge] Posisi halaman saat inisialisasi: Step', currentStep);

        if (currentStep === 2) {
            console.log('[TPQ-EMIS Bridge] Form utama (Step 2) terdeteksi, mengisi NIK, HP & Nama Ortu...');
            fillEmisStep2(santri);
            chrome.storage.local.remove(['waitingForStep2']);
        } else if (currentStep === 1) {
            console.log('[TPQ-EMIS Bridge] Step 1 terdeteksi, memulai pengisian...');
            fillEmisStep1(santri);
        } else {
            showPendingBanner(santri, currentStep);
        }
    }
});

// Listener pesan langsung dari background worker ekstensi
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request && request.action === 'EXECUTE_FILL_FORM') {
        const santriData = request.data;
        console.log('[TPQ-EMIS Bridge] Menerima instruksi pengisian untuk:', santriData.nama_santri);
        
        const currentStep = detectCurrentEmisStep();
        console.log('[TPQ-EMIS Bridge] Langkah halaman EMIS saat ini: Step', currentStep);

        if (currentStep === 1) {
            fillEmisStep1(santriData);
            sendResponse({ success: true, step: 1 });
        } else if (currentStep === 2) {
            fillEmisStep2(santriData);
            sendResponse({ success: true, step: 2 });
        } else {
            chrome.storage.local.set({ pendingSantriData: santriData });
            showPendingBanner(santriData, 0);
            sendResponse({ success: false, message: 'Silakan buka menu Pendaftaran Santri Baru' });
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
    // Ada pilihan "BERDASARKAN", "NAMA, TEMPAT, DAN TANGGAL LAHIR", atau tombol "SELANJUTNYA"
    const hasBerdasarkan = textContent.includes('BERDASARKAN');
    const hasOpsiTtl = textContent.includes('NAMA, TEMPAT, DAN TANGGAL LAHIR');
    const hasNextBtn = Array.from(document.querySelectorAll('button')).some(btn => 
        (btn.innerText || btn.textContent || '').trim().toUpperCase().includes('SELANJUTNYA')
    );

    if (hasBerdasarkan || hasOpsiTtl || hasNextBtn) {
        return 1;
    }

    return 0; // Halaman dashboard / daftar
}

/**
 * Pengawas transisi: memantau kapan Step 1 selesai dan Step 2 muncul di layar
 */
function initStepTransitionWatcher() {
    setInterval(() => {
        chrome.storage.local.get(['pendingSantriData', 'waitingForStep2'], (res) => {
            if (res && res.pendingSantriData) {
                const step = detectCurrentEmisStep();
                if (step === 2 && res.waitingForStep2) {
                    console.log('[TPQ-EMIS Bridge] Transisi otomatis ke Step 2 terdeteksi! Mengisi NIK, HP & Ortu...');
                    const oldStep1 = document.getElementById('tpq-step1-alert');
                    if (oldStep1) oldStep1.remove();

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

    // 1. Cari checkbox 'Belum Punya NIK'
    const checkboxes = Array.from(document.querySelectorAll('input[type="checkbox"]'));
    for (const cb of checkboxes) {
        let labelText = '';
        if (cb.id) {
            const lbl = document.querySelector(`label[for="${cb.id}"]`);
            if (lbl) labelText += ' ' + (lbl.innerText || lbl.textContent || '');
        }
        const parent = cb.closest('label') || cb.parentElement;
        if (parent) {
            labelText += ' ' + (parent.innerText || parent.textContent || '');
        }

        const t = labelText.toUpperCase();
        if (t.includes('BELUM') && t.includes('NIK')) {
            checkboxNik = cb;
            break;
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
 * Mencari input No Handphone dan Checkbox 'Tidak memiliki nomor handphone' di Step 2
 */
function findStep2HpElements() {
    let inputHp = null;
    let checkboxHp = null;

    // 1. Cari checkbox 'Tidak memiliki nomor handphone'
    const checkboxes = Array.from(document.querySelectorAll('input[type="checkbox"]'));
    for (const cb of checkboxes) {
        let labelText = '';
        if (cb.id) {
            const lbl = document.querySelector(`label[for="${cb.id}"]`);
            if (lbl) labelText += ' ' + (lbl.innerText || lbl.textContent || '');
        }
        const parent = cb.closest('label') || cb.parentElement;
        if (parent) {
            labelText += ' ' + (parent.innerText || parent.textContent || '');
        }

        const t = labelText.toUpperCase();
        if ((t.includes('TIDAK') || t.includes('BELUM')) && (t.includes('HANDPHONE') || t.includes('HP') || t.includes('NOMOR'))) {
            checkboxHp = cb;
            break;
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
        if (combined.includes('HANDPHONE') || combined.includes('NO HP') || combined.includes('NOMOR HP') || combined.includes('TELEPON') || ph.includes('08') || ph.includes('CONTOH: 02') || ph.includes('CONTOH: 08')) {
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

function fillEmisStep2(data) {
    console.log('[TPQ-EMIS Bridge] Mengisi Step 2 (NIK, No HP, Nama Ayah & Ibu)...', data.nama_santri);

    // Pastikan popup Step 1 tertutup
    const step1Alert = document.getElementById('tpq-step1-alert');
    if (step1Alert) step1Alert.remove();

    let filledAyah = false;
    let filledIbu = false;
    let filledNik = false;
    let filledHp = false;
    let attempts = 0;
    const maxAttempts = 15; // 3 detik

    const timer = setInterval(() => {
        attempts++;

        // 1. Isi NIK Calon Santri / Centang Belum Punya NIK secara otomatis
        if (!filledNik) {
            const { inputNik, checkboxNik } = findStep2NikElements();
            if (data.belum_punya_nik) {
                if (checkboxNik) {
                    if (!checkboxNik.checked) {
                        console.log('[TPQ-EMIS Bridge] Mencentang [✓] Belum Punya NIK');
                        checkboxNik.checked = true;
                        checkboxNik.dispatchEvent(new Event('change', { bubbles: true }));
                        checkboxNik.dispatchEvent(new Event('click', { bubbles: true }));
                    }
                    filledNik = true;
                }
            } else if (data.nik && inputNik) {
                console.log('[TPQ-EMIS Bridge] Mengisi NIK Calon Santri:', data.nik);
                setNativeValue(inputNik, data.nik);
                filledNik = true;
            }
        }

        // 2. Isi No Handphone Calon Santri / Centang Tidak Memiliki HP secara otomatis
        if (!filledHp) {
            const { inputHp, checkboxHp } = findStep2HpElements();
            let cleanHp = String(data.no_hp || '').trim().replace(/[^0-9]/g, '');
            if (cleanHp.startsWith('62')) {
                cleanHp = '0' + cleanHp.substring(2);
            }

            if (cleanHp.length >= 8 && inputHp && !data.tidak_punya_hp) {
                console.log('[TPQ-EMIS Bridge] Mengisi No Handphone Calon Santri:', cleanHp);
                setNativeValue(inputHp, cleanHp);
                filledHp = true;
            } else if (data.tidak_punya_hp || !cleanHp) {
                if (checkboxHp) {
                    if (!checkboxHp.checked) {
                        console.log('[TPQ-EMIS Bridge] Mencentang [✓] Tidak Memiliki No Handphone');
                        checkboxHp.checked = true;
                        checkboxHp.dispatchEvent(new Event('change', { bubbles: true }));
                        checkboxHp.dispatchEvent(new Event('click', { bubbles: true }));
                    }
                    filledHp = true;
                }
            }
        }

        // 3. Isi Nama Ayah & Ibu Kandung
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

        if ((filledAyah && filledIbu && filledNik && filledHp) || attempts >= maxAttempts) {
            clearInterval(timer);
            // Tampilkan Floating Checklist Assistant untuk isian manual Step 2
            showStep2Assistant(data, filledAyah, filledIbu, filledNik, filledHp);
        }
    }, 200);

    return { success: true, step: 2 };
}

/**
 * ============================================================================
 * HELPER FORM INTERACTION (REACT / VUE / ANT-DESIGN COMPATIBLE)
 * ============================================================================
 */
function setNativeValue(element, value) {
    if (!element) return false;

    try {
        const val = String(value ?? '').trim();
        if (!val) return false;

        const wasReadOnly = element.readOnly;
        if (wasReadOnly) {
            element.readOnly = false;
        }

        const lastValue = element.value;

        // Bypass React overridden setter dengan prototype HTMLInputElement asli
        const proto = window.HTMLInputElement.prototype;
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
                <span style="font-size: 16px; font-weight: bold; color: #fff;">📅 ${data.tanggal_lahir || '-'}</span>
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
 * Banner Asisten Step 2: Fokus pada Checklist yang Perlu Dipilih Manual oleh Operator + draggable
 */
function showStep2Assistant(data, filledAyah = false, filledIbu = false, filledNik = false, filledHp = false) {
    const alertId = 'tpq-step2-alert';
    const old = document.getElementById(alertId);
    if (old) old.remove();

    const div = document.createElement('div');
    div.id = alertId;
    div.style.cssText = `
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9999999;
        background: #1e293b;
        color: #f8fafc;
        padding: 18px 22px;
        border-radius: 8px;
        border: 1px solid #334155;
        box-shadow: 0 10px 30px rgba(0,0,0,0.45);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        max-width: 440px;
    `;

    // Status isian Otomatis
    const allAutoFilled = filledAyah && filledIbu && filledNik && filledHp;
    const statusOrtuBadge = allAutoFilled
        ? '<span style="font-size: 11px; background: #059669; color: white; padding: 2px 7px; border-radius: 4px; font-weight: 600;">NIK, HP, Ayah & Ibu Terisi ✓</span>'
        : '<span style="font-size: 11px; background: #059669; color: white; padding: 2px 7px; border-radius: 4px; font-weight: 600;">Ayah & Ibu Terisi ✓</span>';

    // Backup tombol salin cepat jika ada kendala di browser
    let ortuFallbackHtml = '';
    if (!filledAyah || !filledIbu) {
        ortuFallbackHtml = `
            <div style="background: rgba(217, 119, 6, 0.15); border: 1px solid #d97706; border-radius: 6px; padding: 8px 10px; margin-bottom: 10px; font-size: 11px;">
                <div style="color: #fcd34d; font-weight: bold; margin-bottom: 4px;">⚠️ Salin Cepat Nama Orang Tua (Jika Belum Terisi):</div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    ${data.nama_ayah ? `<button class="tpq-copy-badge" data-val="${data.nama_ayah}">Salin Ayah: ${data.nama_ayah}</button>` : ''}
                    ${data.nama_ibu ? `<button class="tpq-copy-badge" data-val="${data.nama_ibu}">Salin Ibu: ${data.nama_ibu}</button>` : ''}
                </div>
            </div>
        `;
    }

    // Tampilan NIK di Step 2 (Otomatis terisi)
    let nikDisplay = '';
    if (data.belum_punya_nik) {
        nikDisplay = '<span style="color: #10b981; font-weight: bold;">[✓] Belum Punya NIK (Tercentang Otomatis)</span>';
    } else {
        nikDisplay = `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.nik || '-'}</span>
           <span style="font-size: 11px; color: #10b981; font-weight: bold; margin-left: 4px;">(Terisi Otomatis ✓)</span>
           <button class="tpq-copy-badge" data-val="${data.nik || ''}">Salin</button>`;
    }

    // Tampilan NISN di Step 2
    const nisnDisplay = data.belum_punya_nisn
        ? '<span style="color: #fbbf24; font-weight: bold;">Centang: [✓] Belum Punya NISN</span>'
        : `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.nisn || '-'}</span>
           <button class="tpq-copy-badge" data-val="${data.nisn || ''}">Salin</button>`;

    // Tampilan No Handphone di Step 2 (Otomatis terisi)
    let cleanHp = String(data.no_hp || '').trim().replace(/[^0-9]/g, '');
    if (cleanHp.startsWith('62')) cleanHp = '0' + cleanHp.substring(2);

    let hpDisplay = '';
    if (data.tidak_punya_hp || !cleanHp) {
        hpDisplay = '<span style="color: #10b981; font-weight: bold;">[✓] Tidak Ada HP (Tercentang Otomatis)</span>';
    } else {
        hpDisplay = `<span style="font-family: monospace; font-size: 13px; color: #fff;">${cleanHp}</span>
           <span style="font-size: 11px; color: #10b981; font-weight: bold; margin-left: 4px;">(Terisi Otomatis ✓)</span>
           <button class="tpq-copy-badge" data-val="${cleanHp}">Salin</button>`;
    }

    div.innerHTML = `
        <style>
            .tpq-copy-badge {
                background: #334155;
                color: #38bdf8;
                border: 1px solid #475569;
                padding: 2px 7px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: bold;
                cursor: pointer;
                margin-left: 6px;
                transition: background 0.2s;
            }
            .tpq-copy-badge:hover {
                background: #475569;
                color: #fff;
            }
            .tpq-check-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #334155;
                padding: 6px 0;
            }
        </style>

        <div id="tpq-step2-header" style="cursor: move; user-select: none; font-weight: bold; font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
            <span style="color: #38bdf8;">📋 Panduan Isian Step 2</span>
            <div style="display: flex; align-items: center; gap: 6px;">
                ${statusOrtuBadge}
                <span style="font-size: 11px; background: #334155; color: #cbd5e1; padding: 2px 6px; border-radius: 4px;">⠿ Geser</span>
            </div>
        </div>

        <div style="font-size: 12px; margin-bottom: 10px; color: #94a3b8; line-height: 1.4;">
            NIK, No HP, Nama Ayah & Ibu diisikan otomatis. Silakan lengkapi / sesuaikan opsi berikut:
        </div>

        ${ortuFallbackHtml}

        <div style="background: #0f172a; padding: 10px 12px; border-radius: 6px; font-size: 12px; margin-bottom: 12px; border: 1px solid #334155;">
            <!-- 1. Tanggal Masuk -->
            <div class="tpq-check-row">
                <span>1. <strong>Tgl Masuk:</strong></span>
                <div>
                    <span style="font-family: monospace; color: #fff;">${data.tanggal_masuk || '-'}</span>
                    <button class="tpq-copy-badge" data-val="${data.tanggal_masuk || ''}">Salin</button>
                </div>
            </div>

            <!-- 2. Tingkat Kelas -->
            <div class="tpq-check-row">
                <span>2. <strong>Tingkat Kelas:</strong></span>
                <strong style="color: #facc15; font-size: 13px;">${data.tingkat_kelas || '-'}</strong>
            </div>

            <!-- 3. Jenis Kelamin -->
            <div class="tpq-check-row">
                <span>3. <strong>Jenis Kelamin:</strong></span>
                <strong style="color: #facc15; font-size: 13px;">${data.jenis_kelamin || '-'}</strong>
            </div>

            <!-- 4. NIK (Otomatis) -->
            <div class="tpq-check-row">
                <span>4. <strong>NIK:</strong></span>
                <div>${nikDisplay}</div>
            </div>

            <!-- 5. NISN -->
            <div class="tpq-check-row">
                <span>5. <strong>NISN:</strong></span>
                <div>${nisnDisplay}</div>
            </div>

            <!-- 6. No HP (Otomatis) -->
            <div class="tpq-check-row" style="border-bottom: none;">
                <span>6. <strong>No Handphone:</strong></span>
                <div>${hpDisplay}</div>
            </div>
        </div>

        <div style="margin-bottom: 12px; font-size: 12px; line-height: 1.4; color: #cbd5e1;">
            👉 <strong>Setelah semua opsi dipilih, tekan tombol SIMPAN di pojok kanan bawah EMIS.</strong><br>
            <span style="font-size: 11px; opacity: 0.8; color: #94a3b8;">(Popup ini dapat digeser bila menutupi tombol atau form)</span>
        </div>

        <div style="display: flex; gap: 8px;">
            <button id="tpq-btn-tandai-selesai-step2" style="
                background: #10b981; color: #ffffff; border: none; padding: 7px 12px;
                border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; flex: 1;
            ">✓ Tandai Selesai di TPQ Smart</button>
            <button id="tpq-close-step2-alert" style="
                background: #475569; color: white; border: none;
                padding: 7px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;
            ">Tutup</button>
        </div>
    `;

    document.body.appendChild(div);
    document.getElementById('tpq-close-step2-alert').addEventListener('click', () => div.remove());

    // Jadikan popup Step 2 dapat digeser (draggable)
    const headerEl = document.getElementById('tpq-step2-header');
    makeDraggable(div, headerEl);

    // Pasang listener copy badge di dalam checklist
    const copyBtns = div.querySelectorAll('.tpq-copy-badge');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const originalText = btn.innerText;
                btn.innerText = 'Tersalin!';
                btn.style.color = '#10b981';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.color = '#38bdf8';
                }, 1200);
            }
        });
    });

    // Tombol tandai selesai
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
    const old = document.getElementById('tpq-pending-banner');
    if (old) old.remove();

    const banner = document.createElement('div');
    banner.id = 'tpq-pending-banner';
    banner.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999999;
        background: #0d6efd;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 14px;
        max-width: 400px;
    `;

    const stepLabel = currentStep === 1 ? 'Step 1: Pencarian Awal' : (currentStep === 2 ? 'Step 2: Form Utama' : 'Buka Menu Pendaftaran Santri Baru');

    banner.innerHTML = `
        <div id="tpq-pending-header" style="cursor: move; user-select: none; font-weight: bold; margin-bottom: 6px; font-size: 15px; display: flex; justify-content: space-between; align-items: center;">
            <span>⚡ TPQ Smart Bridge</span>
            <span style="font-size: 11px; background: rgba(255,255,255,0.25); padding: 1px 6px; border-radius: 4px;">⠿ Geser</span>
        </div>
        <div style="margin-bottom: 12px; line-height: 1.4;">
            Data santri siap diisi:<br>
            <strong>${santri.nama_santri}</strong> (${santri.tingkat_kelas || 'Santri Baru'})<br>
            <span style="font-size: 12px; opacity: 0.9;">Halaman terdeteksi: <strong>${stepLabel}</strong></span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button id="tpq-btn-fill-now" style="
                background: #198754; color: white; border: none; padding: 6px 14px;
                border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px;
            ">Isi Form Sekarang</button>
            <button id="tpq-btn-cancel-fill" style="
                background: #6c757d; color: white; border: none; padding: 6px 10px;
                border-radius: 4px; cursor: pointer; font-size: 13px;
            ">Batal</button>
        </div>
    `;

    document.body.appendChild(banner);

    const header = document.getElementById('tpq-pending-header');
    makeDraggable(banner, header);

    document.getElementById('tpq-btn-fill-now').addEventListener('click', () => {
        const step = detectCurrentEmisStep();
        if (step === 1) {
            fillEmisStep1(santri);
        } else if (step === 2) {
            fillEmisStep2(santri);
            chrome.storage.local.remove(['pendingSantriData']);
        } else {
            alert('Silakan buka menu Pendaftaran Santri Baru di EMIS terlebih dahulu.');
        }
        banner.remove();
    });

    document.getElementById('tpq-btn-cancel-fill').addEventListener('click', () => {
        chrome.storage.local.remove(['pendingSantriData', 'waitingForStep2']);
        banner.remove();
    });
}
