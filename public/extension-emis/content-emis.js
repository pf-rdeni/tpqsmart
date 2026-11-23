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
        } else {
            console.log('[TPQ-EMIS Bridge] Menampilkan popup start pengisian (Step 0 / Step 1)...');
            showStep0StartPopup(santri);
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
        chrome.storage.local.get(['pendingSantriData', 'waitingForStep2'], (res) => {
            if (res && res.pendingSantriData) {
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
            const formattedHp = formatEmisPhoneNumber(data.no_hp);

            if (formattedHp && inputHp && !data.tidak_punya_hp) {
                console.log('[TPQ-EMIS Bridge] Mengisi No Handphone Calon Santri (+62):', formattedHp);
                setNativeValue(inputHp, formattedHp);
                filledHp = true;
            } else if (data.tidak_punya_hp || !formattedHp) {
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
 * - Bagian Atas: Data yang terisi otomatis / bisa disalin
 * - Bagian Bawah: 👉 Tugas Anda (Pilihan manual di EMIS: Tanggal Masuk, Tingkat Kelas, JK, Status)
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

            <!-- No HP -->
            <div class="tpq-data-row">
                <span>• <strong>No HP:</strong></span>
                <div>${hpDisplay}</div>
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
            <div class="tpq-data-row" style="margin-bottom: 0;">
                <span>• <strong>Ibu:</strong></span>
                <div>
                    <span style="font-weight: 600; color: #fff;">${data.nama_ibu || '-'}</span>
                    ${data.nama_ibu ? `<button class="tpq-copy-badge" data-val="${data.nama_ibu}">Salin</button>` : ''}
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

            <!-- 2. Tingkat Kelas -->
            <div class="tpq-manual-row">
                <span>2. <strong>Tingkat Kelas:</strong></span>
                <strong style="color: #ffc107; font-size: 13.5px;">${data.tingkat_kelas || '-'}</strong>
            </div>

            <!-- 3. Jenis Kelamin -->
            <div class="tpq-manual-row">
                <span>3. <strong>Jenis Kelamin:</strong></span>
                <strong style="color: #ffc107; font-size: 13.5px;">${data.jenis_kelamin || '-'}</strong>
            </div>

            <!-- 4. Status Ortu & Wali -->
            <div class="tpq-manual-row" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
                <span>4. <strong>Status Ortu:</strong></span>
                <span style="color: #fff; font-size: 12px;">Masih Hidup | Wali: Ayah</span>
            </div>
        </div>

        <div style="font-size: 11.5px; opacity: 0.95; line-height: 1.4; margin-bottom: 10px;">
            Setelah semua opsi dipilih, tekan tombol <strong style="background: rgba(255,255,255,0.25); padding: 1px 6px; border-radius: 3px;">SIMPAN</strong> di pojok kanan bawah EMIS.<br>
            <span style="font-size: 11px; opacity: 0.85;">(Popup ini dapat digeser jika menutupi formulir)</span>
        </div>

        <div style="display: flex; gap: 8px;">
            <button id="tpq-btn-tandai-selesai-step2" style="
                background: #198754; color: #ffffff; border: none; padding: 7px 12px;
                border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; flex: 1;
                box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            ">✓ Tandai Selesai di TPQ Smart</button>
            <button id="tpq-close-step2-alert" style="
                background: rgba(255,255,255,0.25); color: white; border: none;
                padding: 7px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;
            ">Tutup</button>
        </div>
    `;

    document.body.appendChild(div);
    document.getElementById('tpq-close-step2-alert').addEventListener('click', () => div.remove());

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
    showStep0StartPopup(santri);
}
