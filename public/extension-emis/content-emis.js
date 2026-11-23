/**
 * TPQ Smart - EMIS 4.0 Bridge
 * Content Script for emis.kemenag.go.id
 * 
 * Alur Kerja Semi-Otomatis:
 * Step 1:
 *  - Memilih radio "Nama, Tempat, dan tanggal lahir" secara spesifik
 *  - Mengisi Nama Lengkap & Tempat Lahir santri
 *  - Menyalin Tanggal Lahir ke clipboard
 *  - Menampilkan floating card Step 1 (otomatis tertutup saat klik SELANJUTNYA)
 * 
 * Step 2:
 *  - Fokus HANYA mengisi otomatis: Nama Lengkap Ayah Kandung & Nama Lengkap Ibu Kandung
 *  - Proteksi mutlak agar tidak pernah menyentuh / menimpa field Calon Santri
 *  - Floating card Step 2 fokus memandu checklist isian manual (Tgl Masuk, Tingkat Kelas, dll)
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
            console.log('[TPQ-EMIS Bridge] Form utama (Step 2) terdeteksi, mengisi Nama Ortu...');
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
                    console.log('[TPQ-EMIS Bridge] Transisi otomatis ke Step 2 terdeteksi! Mengisi Nama Ortu...');
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
 * Memilih radio button 'Nama, Tempat, dan tanggal lahir' secara akurat
 * Menghindari salah klik ke radio NIK atau NISN
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
        
        // Pastikan radio button dipilih di setiap loop jika belum terpilih
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

    // 4. Tampilkan floating notification Step 1
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
 * Fokus HANYA mengisi Nama Ayah Kandung & Ibu Kandung secara otomatis.
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
        // Harus berada SETELAH el dalam urutan dokumen
        if ((el.compareDocumentPosition(inp) & Node.DOCUMENT_POSITION_FOLLOWING) !== 0) {
            // Jika ada stopEl, harus berada SEBELUM stopEl
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

    console.log('[TPQ-EMIS Bridge] Headings Orang Tua terdeteksi:', {
        hasAyah: !!headingAyah,
        hasIbu: !!headingIbu,
        hasWali: !!headingWali
    });

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
    console.log('[TPQ-EMIS Bridge] Mengisi Step 2 (Fokus Nama Ayah & Ibu)...', data.nama_santri);

    // Pastikan popup Step 1 tertutup
    const step1Alert = document.getElementById('tpq-step1-alert');
    if (step1Alert) step1Alert.remove();

    let filledAyah = false;
    let filledIbu = false;
    let attempts = 0;
    const maxAttempts = 15; // 3 detik

    const timer = setInterval(() => {
        attempts++;
        const { inputAyah, inputIbu } = findOrangTuaInputs(data);

        // 1. Isi Nama Lengkap Ayah Kandung
        if (inputAyah && data.nama_ayah && !filledAyah) {
            console.log('[TPQ-EMIS Bridge] Mengisi Nama Ayah Kandung:', data.nama_ayah);
            setNativeValue(inputAyah, data.nama_ayah);
            filledAyah = true;
        }

        // 2. Isi Nama Lengkap Ibu Kandung
        if (inputIbu && data.nama_ibu && !filledIbu) {
            console.log('[TPQ-EMIS Bridge] Mengisi Nama Ibu Kandung:', data.nama_ibu);
            setNativeValue(inputIbu, data.nama_ibu);
            filledIbu = true;
        }

        if ((filledAyah && filledIbu) || attempts >= maxAttempts) {
            clearInterval(timer);
            // Tampilkan Floating Checklist Assistant untuk isian manual Step 2
            showStep2Assistant(data, filledAyah, filledIbu);
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
 * ============================================================================
 * FLOATING ASSISTANT BANNERS
 * ============================================================================
 */

/**
 * Banner Asisten Step 1: Memberikan tanggal lahir dan otomatis tertutup saat klik SELANJUTNYA
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
        max-width: 400px;
    `;

    div.innerHTML = `
        <div style="font-weight: bold; font-size: 15px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
            <span>✅ Step 1: Nama & Tempat Terisi</span>
            <span style="font-size: 11px; background: rgba(255,255,255,0.25); padding: 2px 6px; border-radius: 4px;">TPQ Smart</span>
        </div>
        <div style="margin-bottom: 12px; line-height: 1.5;">
            <div>• <strong>Nama:</strong> ${data.nama_santri}</div>
            <div>• <strong>Tempat Lahir:</strong> ${data.tempat_lahir || '-'}</div>
            
            <div style="background: rgba(0,0,0,0.25); padding: 10px 12px; border-radius: 6px; margin: 10px 0;">
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

            <div style="margin-top: 8px; font-size: 12px;">
                Setelah memilih tanggal lahir, klik tombol <strong style="background: rgba(255,255,255,0.25); padding: 1px 6px; border-radius: 3px;">SELANJUTNYA</strong> di formulir EMIS.<br>
                <span style="font-size: 11px; opacity: 0.9;">(Pesan ini akan otomatis tertutup saat tombol ditekan)</span>
            </div>
        </div>
        <button id="tpq-close-step1-alert" style="
            background: rgba(255,255,255,0.25); color: white; border: none;
            padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;
        ">Tutup</button>
    `;

    document.body.appendChild(div);
    document.getElementById('tpq-close-step1-alert').addEventListener('click', () => div.remove());

    const btnCopy = document.getElementById('tpq-btn-copy-tgl-step1');
    if (btnCopy && data.tanggal_lahir) {
        btnCopy.addEventListener('click', () => {
            navigator.clipboard.writeText(data.tanggal_lahir);
            btnCopy.innerText = 'Tersalin!';
            setTimeout(() => { btnCopy.innerText = 'Salin Tanggal'; }, 1500);
        });
    }

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
 * Banner Asisten Step 2: Fokus pada Checklist yang Perlu Dipilih Manual oleh Operator
 */
function showStep2Assistant(data, filledAyah = false, filledIbu = false) {
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

    // Status isian Orang Tua
    const statusOrtuBadge = (filledAyah && filledIbu)
        ? '<span style="font-size: 11px; background: #059669; color: white; padding: 2px 7px; border-radius: 4px; font-weight: 600;">Ayah & Ibu Terisi ✓</span>'
        : '<span style="font-size: 11px; background: #d97706; color: white; padding: 2px 7px; border-radius: 4px; font-weight: 600;">Periksa Nama Ortu</span>';

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

    // Siapkan teks copy NIK, NISN, HP
    const nikDisplay = data.belum_punya_nik 
        ? '<span style="color: #fbbf24; font-weight: bold;">Centang: [✓] Belum Punya NIK</span>'
        : `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.nik || '-'}</span>
           <button class="tpq-copy-badge" data-val="${data.nik || ''}">Salin</button>`;

    const nisnDisplay = data.belum_punya_nisn
        ? '<span style="color: #fbbf24; font-weight: bold;">Centang: [✓] Belum Punya NISN</span>'
        : `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.nisn || '-'}</span>
           <button class="tpq-copy-badge" data-val="${data.nisn || ''}">Salin</button>`;

    const hpDisplay = data.tidak_punya_hp
        ? '<span style="color: #fbbf24; font-weight: bold;">Centang: [✓] Tidak memiliki HP</span>'
        : `<span style="font-family: monospace; font-size: 13px; color: #fff;">${data.no_hp || '-'}</span>
           <button class="tpq-copy-badge" data-val="${data.no_hp || ''}">Salin</button>`;

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

        <div style="font-weight: bold; font-size: 15px; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
            <span style="color: #38bdf8;">📋 Panduan Isian Manual Step 2</span>
            ${statusOrtuBadge}
        </div>

        <div style="font-size: 12px; margin-bottom: 10px; color: #94a3b8; line-height: 1.4;">
            Nama Ayah & Ibu Kandung diisikan otomatis. Silakan pilih / sesuaikan field berikut di formulir EMIS:
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

            <!-- 4. NIK -->
            <div class="tpq-check-row">
                <span>4. <strong>NIK:</strong></span>
                <div>${nikDisplay}</div>
            </div>

            <!-- 5. NISN -->
            <div class="tpq-check-row">
                <span>5. <strong>NISN:</strong></span>
                <div>${nisnDisplay}</div>
            </div>

            <!-- 6. No HP -->
            <div class="tpq-check-row" style="border-bottom: none;">
                <span>6. <strong>No Handphone:</strong></span>
                <div>${hpDisplay}</div>
            </div>
        </div>

        <div style="margin-bottom: 12px; font-size: 12px; line-height: 1.4; color: #cbd5e1;">
            👉 <strong>Setelah semua opsi dipilih, tekan tombol SIMPAN di pojok kanan bawah EMIS.</strong>
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
        <div style="font-weight: bold; margin-bottom: 6px; font-size: 15px;">
            ⚡ TPQ Smart Bridge
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
