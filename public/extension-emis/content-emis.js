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

const EXTENSION_VERSION = '1.6.1';
console.log(`[TPQ-EMIS Bridge v${EXTENSION_VERSION}] Content script aktif di EMIS Kemenag.`);

// Flag status pengecekan NIK Step 1
window.__tpqNikFoundInEmis = false;
window.__tpqStep1CheckingNik = false;

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
            if (window.__tpqNikFoundInEmis) {
                console.log('[TPQ-EMIS Bridge] NIK ditemukan di EMIS. Melewati pengisian otomatis Step 2.');
                chrome.storage.local.remove(['waitingForStep2', 'pendingSantriData']);
                return;
            }
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
        chrome.storage.local.get(['pendingSantriData', 'waitingForStep2', 'pendingUpdateAlamatData', 'currentTask', 'waitingForUpdateStep2'], (res) => {
            if (res && res.currentTask === 'UPDATE_ALAMAT' && res.pendingUpdateAlamatData) {
                const stage = detectUpdateAlamatStage();
                // Transisi dari Step 1 ke Step 2 (Tab DATA SANTRI) dikendalikan otomatis
                if (stage === 2 && res.waitingForUpdateStep2) {
                    console.log('[TPQ-EMIS Bridge] Transisi otomatis ke Update Data Step 2 (DATA SANTRI) terdeteksi!');
                    const oldP1 = document.getElementById('tpq-update-alamat-step1-popup');
                    if (oldP1) oldP1.remove();
                    executeStep2DataSantri(res.pendingUpdateAlamatData);
                    chrome.storage.local.remove(['waitingForUpdateStep2']);
                }
                // Catatan: Transisi ke Step 3 (Tab DATA ALAMAT) sekarang sepenuhnya dipicu oleh
                // satu pintu (Single-Flight): Callback resmi switchToDataAlamatTab()
                // untuk menghindari dual-trigger / eksekusi ganda yang membingungkan.
            } else if (res && res.pendingSantriData) {
                const step = detectCurrentEmisStep();
                if (step === 1) {
                    // Pantau dan otomatis isi jika user beralih ke Nama & TTL di formulir EMIS
                    checkAndAutoFillStep1Watcher(res.pendingSantriData);
                } else if (step === 2 && res.waitingForStep2) {
                    if (window.__tpqNikFoundInEmis || window.__tpqStep1CheckingNik) {
                        console.log('[TPQ-EMIS Bridge] Step 2 terdeteksi saat pengecekan NIK. NIK santri ditemukan!');
                        handleNikFoundInEmis(res.pendingSantriData, 'Halaman beralih ke Formulir Utama (Step 2)');
                        return;
                    }
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
 * Memilih radio button 'NIK' di Step 1
 */
function selectRadioNikStep1() {
    const radios = Array.from(document.querySelectorAll('input[type="radio"], [role="radio"]'));
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
        if (upper.includes('NIK') && !upper.includes('NISN') && !upper.includes('TEMPAT')) {
            if (!r.checked) {
                console.log('[TPQ-EMIS Bridge] Memilih radio NIK:', r);
                r.checked = true;
                r.dispatchEvent(new Event('change', { bubbles: true }));
                triggerNativeClick(r);
            }
            return true;
        }
    }
    return false;
}

/**
 * Memilih radio button 'Nama, Tempat, dan tanggal lahir' secara spesifik
 */
function selectRadioStep1() {
    const radios = Array.from(document.querySelectorAll('input[type="radio"], [role="radio"]'));
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
            console.log('[TPQ-EMIS Bridge] Memilih radio Nama/TTL:', r);
            r.checked = true;
            r.dispatchEvent(new Event('change', { bubbles: true }));
            triggerNativeClick(r);
            if (parent) {
                triggerNativeClick(parent);
            }
            return true;
        }
    }

    // Fallback: cari label atau span yang memuat teks "Nama, Tempat, dan tanggal lahir"
    const allLabels = Array.from(document.querySelectorAll('label, span, div, p'));
    for (const el of allLabels) {
        if (el.children.length > 3) continue;
        const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
        if (txt === 'NAMA, TEMPAT, DAN TANGGAL LAHIR' || (txt.includes('TEMPAT') && txt.includes('TANGGAL LAHIR') && txt.length <= 40)) {
            const r = el.querySelector('input[type="radio"], [role="radio"]') || 
                      el.parentElement?.querySelector('input[type="radio"], [role="radio"]') ||
                      (el.htmlFor ? document.getElementById(el.htmlFor) : null);
            if (r) {
                r.checked = true;
                r.dispatchEvent(new Event('change', { bubbles: true }));
                triggerNativeClick(r);
            }
            triggerNativeClick(el);
            if (el.parentElement) triggerNativeClick(el.parentElement);
            return true;
        }
    }
    return false;
}

/**
 * Mencari tombol hijau 'SELANJUTNYA' di formulir Step 1
 */
function findStep1SelanjutnyaButton() {
    const candidates = Array.from(document.querySelectorAll('button, [role="button"], div[role="button"], input[type="button"], input[type="submit"], a'));
    const visible = candidates.filter(btn => {
        if (btn.closest && (
            btn.closest('#tpq-step0-alert') ||
            btn.closest('#tpq-step1-alert') ||
            btn.closest('#tpq-step2-alert') ||
            btn.closest('#tpq-reopen-step0-pill') ||
            btn.closest('#tpq-pending-banner') ||
            btn.closest('#tpq-nik-found-alert')
        )) return false;
        const rect = btn.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) return false;
        if (btn.disabled || btn.getAttribute('aria-disabled') === 'true') return false;
        return true;
    });

    // 1. Tombol dengan teks persis "SELANJUTNYA"
    let found = visible.find(btn => (btn.innerText || btn.textContent || btn.value || '').trim().toUpperCase() === 'SELANJUTNYA');
    if (found) return found;

    // 2. Tombol dengan teks mengandung "SELANJUTNYA"
    found = visible.find(btn => {
        const t = (btn.innerText || btn.textContent || btn.value || '').trim().toUpperCase();
        return t.includes('SELANJUTNYA') && !t.includes('BATAL') && !t.includes('KEMBALI');
    });
    if (found) return found;

    // 3. Elemen leaf yang memuat teks "SELANJUTNYA"
    const allTextEls = Array.from(document.querySelectorAll('div, span, p, strong, b'));
    for (const el of allTextEls) {
        if (el.closest && el.closest('[id^="tpq-"]')) continue;
        const t = (el.innerText || el.textContent || '').trim().toUpperCase();
        if (t === 'SELANJUTNYA') {
            const btnParent = el.closest('button, [role="button"], [tabindex="0"], .css-18t94o4, a');
            if (btnParent) {
                const rect = btnParent.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) return btnParent;
            }
            const rect = el.getBoundingClientRect();
            if (rect.width > 0 && rect.height > 0) return el;
        }
    }

    return null;
}

/**
 * Menekan tombol hijau 'SELANJUTNYA' di Step 1 untuk beralih ke Step 2
 */
function clickStep1SelanjutnyaButton() {
    let attempts = 0;
    const maxAttempts = 12;

    function tryClick() {
        attempts++;
        const btn = findStep1SelanjutnyaButton();
        if (btn) {
            console.log('%c[TPQ-EMIS Bridge] [Step 1] ✓ Tombol SELANJUTNYA ditemukan. Menekan otomatis menuju Step 2...', 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px;', btn);
            try { btn.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (_) {}

            setTimeout(() => {
                invokeReactHandlers(btn);
                triggerNativeClick(btn);
                try { btn.click(); } catch (_) {}

                setTimeout(() => {
                    const alert1 = document.getElementById('tpq-step1-alert');
                    if (alert1) alert1.remove();
                }, 500);
            }, 150);
            return;
        }

        if (attempts < maxAttempts) {
            setTimeout(tryClick, 250);
        } else {
            console.warn('[TPQ-EMIS Bridge] [Step 1] Tombol SELANJUTNYA tidak ditemukan/belum aktif setelah 3 detik.');
        }
    }

    tryClick();
}

/**
 * Helper terpusat setelah tanggal lahir Step 1 berhasil dipilih di kalender:
 * memperbarui status UI popup Step 1 lalu menekan tombol SELANJUTNYA ke Step 2.
 */
function onStep1CalendarPickedSuccess() {
    const sEl = document.getElementById('tpq-step1-tgl-status');
    const dEl = document.getElementById('tpq-step1-tgl-desc');
    if (sEl) {
        sEl.innerText = '✓ Berhasil';
        sEl.style.background = '#dcfce7';
        sEl.style.color = '#15803d';
    }
    if (dEl) {
        dEl.innerHTML = '<span style="color: #16a34a; font-weight: bold;">✓ Tanggal lahir berhasil dipilih!</span> Menekan <strong>SELANJUTNYA</strong> ke Step 2...';
    }
    setTimeout(() => {
        clickStep1SelanjutnyaButton();
    }, 600);
}

/**
 * Mendeteksi modal popup "NIK Tidak Ditemukan" dan tombol "TUTUP" di EMIS 4.0
 */
function findNikTidakDitemukanModal() {
    const allEls = Array.from(document.querySelectorAll('div, [role="dialog"], [role="alertdialog"], section, h1, h2, h3, h4, p, span'));
    for (const el of allEls) {
        if (el.closest && el.closest('[id^="tpq-"]')) continue;
        const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
        if (txt === 'NIK TIDAK DITEMUKAN' || (txt.includes('NIK TIDAK DITEMUKAN') && txt.length <= 60)) {
            let modalContainer = el.closest('[role="dialog"], [role="alertdialog"]') || el;
            if (!modalContainer || modalContainer === el) {
                let p = el;
                for (let i = 0; i < 6 && p && p !== document.body; i++) {
                    if (p.querySelector('button, [role="button"], div[role="button"]')) {
                        modalContainer = p;
                        break;
                    }
                    p = p.parentElement;
                }
            }

            const btns = Array.from(modalContainer.querySelectorAll('button, [role="button"], div[role="button"], div, a, span'))
                .filter(b => {
                    if (b.closest && b.closest('[id^="tpq-"]')) return false;
                    const bt = (b.innerText || b.textContent || b.value || '').trim().toUpperCase();
                    return bt === 'TUTUP' || bt === 'CLOSE';
                });

            if (btns.length > 0) {
                const tutupBtn = btns.find(b => b.tagName === 'BUTTON' || b.getAttribute('role') === 'button') || btns[0];
                return { modal: modalContainer, tutupBtn };
            }
        }
    }

    // Fallback global di document.body
    const bodyText = (document.body.innerText || document.body.textContent || '').toUpperCase();
    if (bodyText.includes('NIK TIDAK DITEMUKAN')) {
        const allBtns = Array.from(document.querySelectorAll('button, [role="button"], div[role="button"], div'))
            .filter(b => {
                if (b.closest && b.closest('[id^="tpq-"]')) return false;
                const rect = b.getBoundingClientRect();
                if (rect.width <= 0 || rect.height <= 0) return false;
                const bt = (b.innerText || b.textContent || b.value || '').trim().toUpperCase();
                return bt === 'TUTUP' || bt === 'CLOSE';
            });
        if (allBtns.length > 0) {
            const tutupBtn = allBtns.find(b => b.tagName === 'BUTTON' || b.getAttribute('role') === 'button') || allBtns[0];
            return { modal: tutupBtn.closest('[role="dialog"]') || tutupBtn.parentElement, tutupBtn };
        }
    }

    return null;
}

/**
 * Mengecek apakah respons validasi NIK menandakan santri ditemukan di EMIS
 */
function isNikFoundResponsePresent() {
    // 1. Cek jika halaman beralih ke Step 2 (Formulir Utama)
    if (detectCurrentEmisStep() === 2) {
        return { type: 'step2', message: 'Halaman beralih ke Formulir Utama (Step 2)' };
    }

    // 2. Cek modal atau notifikasi data ditemukan
    const allModals = Array.from(document.querySelectorAll('div, [role="dialog"], [role="alertdialog"], section'));
    for (const el of allModals) {
        if (el.closest && el.closest('[id^="tpq-"]')) continue;
        const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
        if (txt.includes('NIK TIDAK DITEMUKAN') || txt.includes('TIDAK DITEMUKAN')) continue;

        if (
            txt.includes('DATA DITEMUKAN') ||
            txt.includes('DATA SANTRI DITEMUKAN') ||
            txt.includes('NIK SUDAH TERDAFTAR') ||
            txt.includes('SUDAH PERNAH TERDAFTAR') ||
            txt.includes('SUDAH TERDAFTAR DI') ||
            txt.includes('SUDAH AKTIF') ||
            txt.includes('SANTRI TELAH TERDAFTAR')
        ) {
            return { type: 'modal', message: txt.substring(0, 80) };
        }
    }

    return null;
}

/**
 * Menangani kondisi ketika NIK ditemukan di EMIS: Menghentikan proses dan TIDAK melanjutkan.
 */
function handleNikFoundInEmis(santriData, reason) {
    console.log(`%c[TPQ-EMIS Bridge] [Step 1] ⚠️ NIK DITEMUKAN DI EMIS! (${reason || ''}) Sesuai instruksi, proses pengisian dihentikan dan TIDAK dilanjutkan.`, 'background: #dc2626; color: #fff; font-weight: bold; font-size: 13px; padding: 4px 8px; border-radius: 4px;');
    
    window.__tpqNikFoundInEmis = true;
    window.__tpqStep1CheckingNik = false;

    chrome.storage.local.remove(['pendingSantriData', 'waitingForStep2']);

    const p0 = document.getElementById('tpq-step0-alert');
    if (p0) p0.remove();
    const p1 = document.getElementById('tpq-step1-alert');
    if (p1) p1.remove();
    const pill = document.getElementById('tpq-reopen-step0-pill');
    if (pill) pill.remove();
    const pending = document.getElementById('tpq-pending-banner');
    if (pending) pending.remove();

    showNikFoundAlert(santriData, reason);
}

/**
 * Menampilkan banner peringatan bahwa NIK ditemukan dan proses dihentikan
 */
function showNikFoundAlert(santriData, reason) {
    const old = document.getElementById('tpq-nik-found-alert');
    if (old) old.remove();

    const div = document.createElement('div');
    div.id = 'tpq-nik-found-alert';
    div.style.cssText = `
        position: fixed;
        top: 25px;
        right: 25px;
        z-index: 9999999;
        width: 380px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #dc2626;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(220, 38, 38, 0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #1e293b;
        overflow: hidden;
        animation: tpqFadeIn 0.3s ease-out;
    `;

    div.innerHTML = `
        <div style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: #fff; padding: 10px 14px; font-weight: bold; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="font-size: 16px;">🛑</span>
                <span>NIK DITEMUKAN DI EMIS</span>
                <span style="font-size: 9.5px; background: rgba(255,255,255,0.25); padding: 1px 4px; border-radius: 3px;">v${EXTENSION_VERSION}</span>
            </div>
            <span id="tpq-close-nik-found-btn" style="cursor: pointer; font-size: 18px; line-height: 1; padding: 1px 5px; opacity: 0.85;" title="Tutup">&times;</span>
        </div>
        <div style="padding: 14px 16px;">
            <div style="margin-bottom: 8px; font-size: 13px; line-height: 1.4;">
                Data calon santri: <strong>${escapeHtml(santriData?.nama_santri || 'Santri')}</strong>
            </div>
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 8px 10px; margin-bottom: 12px; font-size: 12px; color: #991b1b;">
                <div>NIK: <strong style="font-family: monospace; font-size: 13px;">${escapeHtml(santriData?.nik || '-')}</strong></div>
                <div style="margin-top: 4px; font-size: 11.5px; color: #b91c1c;">
                    ⚠️ Data dengan NIK tersebut <strong>ditemukan</strong> di sistem EMIS Kemenag (${escapeHtml(reason || 'Data ada')}).
                </div>
            </div>
            <div style="background: #f8fafc; border-left: 3px solid #dc2626; padding: 8px 10px; font-size: 11.5px; color: #475569; line-height: 1.45; margin-bottom: 12px;">
                🛑 <strong>Proses dihentikan:</strong> Sesuai instruksi sistem, pengisian otomatis tidak dilanjutkan untuk menghindari duplikasi atau kesalahan data santri yang sudah terdaftar.
            </div>
            <button id="tpq-btn-dismiss-nik-found" style="
                width: 100%; background: #64748b; color: #ffffff; border: none;
                padding: 7px 12px; border-radius: 5px; font-size: 12px; font-weight: bold;
                cursor: pointer;
            ">
                Tutup Notifikasi
            </button>
        </div>
    `;

    document.body.appendChild(div);

    const closeBtn = document.getElementById('tpq-close-nik-found-btn');
    const dismissBtn = document.getElementById('tpq-btn-dismiss-nik-found');
    if (closeBtn) closeBtn.addEventListener('click', () => div.remove());
    if (dismissBtn) dismissBtn.addEventListener('click', () => div.remove());
}

/**
 * ============================================================================
 * OTOMASI SELEKSI KALENDER EMIS 4.0 (FLICKER DATEPICKER)
 * ============================================================================
 */

const NAMA_BULAN_INDO = [
    '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
const NAMA_BULAN_INDO_SHORT = [
    '', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
];
const NAMA_BULAN_EN = [
    '', 'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];
const NAMA_BULAN_EN_SHORT = [
    '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
];

/**
 * Parsing nomor bulan (1-12) dari teks label kalender (misal: "November 2026" -> 11)
 */
function parseBulanFromText(rawText) {
    if (!rawText) return null;
    const upper = String(rawText).trim().toUpperCase();
    for (let m = 1; m <= 12; m++) {
        const fullId = NAMA_BULAN_INDO[m].toUpperCase();
        const shortId = NAMA_BULAN_INDO_SHORT[m].toUpperCase();
        const fullEn = NAMA_BULAN_EN[m].toUpperCase();
        const shortEn = NAMA_BULAN_EN_SHORT[m].toUpperCase();

        const regex = new RegExp(`\\b(${fullId}|${shortId}|${fullEn}|${shortEn})\\b`, 'i');
        if (regex.test(upper) || upper.includes(fullId) || upper.includes(fullEn)) {
            return m;
        }
    }
    return null;
}

/**
 * Helper untuk memanggil handler React internal (onPress / onClick) jika tersedia
 */
function invokeReactHandlers(el) {
    if (!el) return;
    try {
        const propKey = Object.keys(el).find(k => k.startsWith('__reactProps$') || k.startsWith('__reactFiber$') || k.startsWith('__reactInternalInstance$'));
        if (propKey) {
            const node = el[propKey];
            const props = node?.memoizedProps || node;
            if (props) {
                if (typeof props.onPress === 'function') {
                    console.log('[TPQ-EMIS Bridge] [Otomasi Kalender] Menjalankan React props.onPress()...');
                    props.onPress({
                        nativeEvent: {},
                        preventDefault: () => {},
                        stopPropagation: () => {},
                        persist: () => {}
                    });
                }
                if (typeof props.onClick === 'function') {
                    console.log('[TPQ-EMIS Bridge] [Otomasi Kalender] Menjalankan React props.onClick()...');
                    props.onClick({
                        nativeEvent: {},
                        preventDefault: () => {},
                        stopPropagation: () => {},
                        persist: () => {}
                    });
                }
            }
        }
    } catch (e) {
        console.warn('[TPQ-EMIS Bridge] Error invoke React handlers:', e);
    }
}

/**
 * Mencari elemen field tanggal React Native for Web (Tanggal Lahir Step 1 / Tanggal Masuk Step 2)
 * berdasarkan label teks, ikon material-community, atau pesan error wajib diisi.
 */
function findCalendarFieldElements(labelKeyword, fallbackKeyword) {
    // Strategi 1: Cari elemen yang berisi teks label target (misal "TANGGAL LAHIR" atau "TANGGAL MASUK *")
    const allEls = Array.from(document.querySelectorAll('div, span, p, label')).filter(el => {
        if (el.closest && el.closest('[id^="tpq-"]')) return false;
        if (el.children.length > 3) return false;
        const txt = (el.innerText || el.textContent || '').replace(/\*/g, '').replace(/\s+/g, ' ').trim().toUpperCase();
        return txt === labelKeyword;
    });

    for (const textEl of allEls) {
        let parent = textEl.parentElement;
        for (let i = 0; i < 7 && parent && parent !== document.body; i++) {
            const btn = parent.querySelector('[role="button"]') || parent.querySelector('.css-18t94o4');
            const icon = parent.querySelector('[style*="material-community"]') || parent.querySelector('.r-1niwhzg');
            const focusable = parent.querySelector('[tabindex="0"]') || (parent.getAttribute('tabindex') === '0' ? parent : null);

            if (btn || icon || focusable) {
                return { root: parent, button: btn, icon: icon, focusable: focusable };
            }
            parent = parent.parentElement;
        }
    }

    // Strategi 2: Cari icon dengan font-family 'material-community' yang root-nya memuat kata kunci
    const iconCandidates = Array.from(document.querySelectorAll('[style*="material-community"], .r-1niwhzg, .css-18t94o4')).filter(el => {
        if (el.closest && el.closest('[id^="tpq-"]')) return false;
        const rect = el.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0;
    });

    for (const el of iconCandidates) {
        const root = el.closest('.css-1dbjc4n') || el.parentElement?.parentElement;
        const txt = (root?.innerText || root?.textContent || '').toUpperCase();
        if (txt.includes('TANGGAL') && txt.includes(fallbackKeyword)) {
            const btn = el.closest('[role="button"]') || el.closest('[tabindex="0"]') || el;
            const focusable = root?.querySelector('[tabindex="0"]') || btn;
            return { root: root, button: btn, icon: el, focusable: focusable };
        }
    }

    // Strategi 3: Cari container yang memiliki pesan error wajib diisi
    const errorKeyword = `${labelKeyword} WAJIB DIISI`;
    const errorEls = Array.from(document.querySelectorAll('div, span')).filter(el => {
        if (el.closest && el.closest('[id^="tpq-"]')) return false;
        const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
        return txt.includes(errorKeyword);
    });

    for (const errEl of errorEls) {
        const root = errEl.parentElement;
        if (root) {
            const btn = root.querySelector('[role="button"]') || root.querySelector('.css-18t94o4');
            const icon = root.querySelector('[style*="material-community"]');
            const focusable = root.querySelector('[tabindex="0"]');
            if (btn || focusable) {
                return { root: root, button: btn, icon: icon, focusable: focusable };
            }
        }
    }

    return null;
}

function findTanggalLahirElements() {
    return findCalendarFieldElements('TANGGAL LAHIR', 'LAHIR');
}

function findTanggalMasukElements() {
    return findCalendarFieldElements('TANGGAL MASUK', 'MASUK');
}

/**
 * Mencari elemen modal dialog kalender picker EMIS
 */
function findCalendarModal() {
    const dialogs = Array.from(document.querySelectorAll('[role="dialog"], [role="alertdialog"], [aria-modal="true"], .MuiDialog-paper, .MuiPickersModal-dialogRoot, .MuiModal-root, .css-1dbjc4n, div'));
    for (const d of dialogs) {
        if (d.closest && d.closest('[id^="tpq-"]')) continue;
        const rect = d.getBoundingClientRect();
        if (rect.width < 150 || rect.height < 150) continue;

        const txt = (d.innerText || d.textContent || '').toUpperCase();
        // Ciri khas modal picker: memiliki tombol CANCEL dan OK serta memuat tahun atau tanggal
        if (txt.includes('CANCEL') && txt.includes('OK')) {
            if (/\b(19\d\d|20\d\d)\b/.test(txt) || txt.includes('SEL') || txt.includes('RAB') || txt.includes('NOV') || txt.includes('MIN') || txt.includes('SEN')) {
                return d;
            }
        }
    }
    return null;
}

/**
 * Trigger bersama untuk membuka modal kalender picker EMIS pada field target
 */
function triggerOpenCalendarField(tglInfo, fieldLabel) {
    if (!tglInfo) {
        console.warn(`[TPQ-EMIS Bridge] [Otomasi Kalender] Komponen ${fieldLabel} belum terdeteksi di DOM.`);
        return false;
    }

    console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Komponen ${fieldLabel} EMIS ditemukan:`, tglInfo);

    const { button, icon, focusable } = tglInfo;

    if (button) {
        console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Memicu klik pada button kalender ${fieldLabel}...`);
        invokeReactHandlers(button);
        triggerNativeClick(button);
        try { button.click(); } catch (_) {}
        try {
            button.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', code: 'Enter', keyCode: 13, which: 13, bubbles: true }));
            button.dispatchEvent(new KeyboardEvent('keyup', { key: 'Enter', code: 'Enter', keyCode: 13, which: 13, bubbles: true }));
        } catch (_) {}
    }

    if (icon && icon !== button) {
        console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Memicu klik pada icon kalender ${fieldLabel}...`);
        invokeReactHandlers(icon);
        triggerNativeClick(icon);
        try { icon.click(); } catch (_) {}
    }

    if (focusable && focusable !== button) {
        console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Memicu fokus & klik pada focusable container ${fieldLabel}...`);
        invokeReactHandlers(focusable);
        triggerNativeClick(focusable);
        try { focusable.focus(); } catch (_) {}
        try { focusable.click(); } catch (_) {}
        try {
            focusable.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', code: 'Enter', keyCode: 13, which: 13, bubbles: true }));
            focusable.dispatchEvent(new KeyboardEvent('keyup', { key: 'Enter', code: 'Enter', keyCode: 13, which: 13, bubbles: true }));
        } catch (_) {}
    }

    return true;
}

function triggerOpenCalendarPicker() {
    return triggerOpenCalendarField(findTanggalLahirElements(), 'Tanggal Lahir');
}

function triggerOpenTanggalMasukPicker() {
    return triggerOpenCalendarField(findTanggalMasukElements(), 'Tanggal Masuk');
}

/**
 * Mencari elemen header tahun pada banner biru modal kalender (misal: "2026")
 */
function findModalYearHeader(modal) {
    const modalRect = modal.getBoundingClientRect();
    const candidates = Array.from(modal.querySelectorAll('button, div, span, h6, h5, h4, p, a')).filter(el => {
        if (el.children.length > 1) return false;
        const txt = (el.innerText || el.textContent || '').trim();
        if (!/^(19|20)\d{2}$/.test(txt)) return false;

        const rect = el.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) return false;
        // Posisinya di bagian atas banner modal (top 40%)
        if (rect.top - modalRect.top > modalRect.height * 0.4) return false;
        return true;
    });

    if (candidates.length > 0) {
        candidates.sort((a, b) => a.getBoundingClientRect().top - b.getBoundingClientRect().top);
        return candidates[0];
    }
    return null;
}

/**
 * Memilih tahun target dari daftar tahun (Gambar 3)
 */
function selectYearFromList(modal, targetYearStr, onYearSelected) {
    let attempts = 0;
    const maxAttempts = 30; // 3 detik

    const yearTimer = setInterval(() => {
        attempts++;

        // Cari elemen dengan teks tahun persis di dalam modal
        const allYearEls = Array.from(modal.querySelectorAll('button, div, li, span, p')).filter(el => {
            if (el.children.length > 0) return false;
            return (el.innerText || el.textContent || '').trim() === targetYearStr;
        });

        if (allYearEls.length > 0) {
            clearInterval(yearTimer);
            const targetEl = allYearEls[0];
            console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Tahun ${targetYearStr} ditemukan di DOM! Melakukan auto-scroll & klik...`);

            try {
                targetEl.scrollIntoView({ behavior: 'instant', block: 'center' });
            } catch (_) {
                try { targetEl.scrollIntoView(true); } catch (_) {}
            }

            setTimeout(() => {
                invokeReactHandlers(targetEl);
                triggerNativeClick(targetEl);
                try { targetEl.click(); } catch (_) {}
                console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] ✓ Tahun ${targetYearStr} berhasil diklik.`);
                if (typeof onYearSelected === 'function') onYearSelected(true);
            }, 120);
            return;
        }

        // Jika belum ada di viewport DOM (misal virtualized list), cari container scroll
        const scrollContainers = Array.from(modal.querySelectorAll('div, ul')).filter(c => {
            return c.scrollHeight > c.clientHeight + 40;
        });
        if (scrollContainers.length > 0) {
            const sc = scrollContainers[0];
            const curYear = new Date().getFullYear();
            const targetNum = parseInt(targetYearStr, 10);
            if (targetNum < curYear) {
                sc.scrollTop -= 150;
            } else {
                sc.scrollTop += 150;
            }
        }

        if (attempts >= maxAttempts) {
            clearInterval(yearTimer);
            console.warn(`[TPQ-EMIS Bridge] [Otomasi Kalender] Gagal menemukan tahun ${targetYearStr} di daftar tahun.`);
            if (typeof onYearSelected === 'function') onYearSelected(false);
        }
    }, 100);
}

/**
 * Mengambil komponen navigasi bulan di modal kalender
 * Menjamin hanya mengambil tombol panah di header (bukan tombol sel hari pada grid)
 */
function getMonthNavigation(modal) {
    // 1. Cari container header navigasi bulan Material-UI (.MuiPickersCalendarHeader-switchHeader)
    let headerContainer = modal.querySelector('.MuiPickersCalendarHeader-switchHeader, [class*="switchHeader"]');
    if (!headerContainer) {
        headerContainer = modal.querySelector('.MuiPickersCalendarHeader-root, [class*="PickersCalendarHeader-root"]');
    }

    // Fallback: cari container yang memuat 2 tombol dan teks nama bulan
    if (!headerContainer) {
        const allDivs = Array.from(modal.querySelectorAll('div'));
        for (const d of allDivs) {
            const btns = Array.from(d.querySelectorAll(':scope > button, :scope > [role="button"]'));
            if (btns.length === 2) {
                const txt = (d.innerText || d.textContent || '').trim();
                if (parseBulanFromText(txt) !== null) {
                    headerContainer = d;
                    break;
                }
            }
        }
    }

    if (headerContainer) {
        // Tombol panah bulan HANYA yang ada di dalam headerContainer (pasti hanya 2 tombol: kiri & kanan)
        const buttons = Array.from(headerContainer.querySelectorAll('button, [role="button"]')).filter(b => {
            const rect = b.getBoundingClientRect();
            return rect.width > 0 && rect.height > 0;
        });
        buttons.sort((a, b) => a.getBoundingClientRect().left - b.getBoundingClientRect().left);
        const prevBtn = buttons[0];
        const nextBtn = buttons[buttons.length - 1];

        // Teks bulan diambil dari transitionContainer atau elemen teks di dalam headerContainer
        const labelContainer = headerContainer.querySelector('.MuiPickersCalendarHeader-transitionContainer, [class*="transitionContainer"]') || headerContainer;
        const textNodes = Array.from(labelContainer.querySelectorAll('p, h6, h5, span, div')).filter(el => {
            if (el.children.length > 0) return false;
            const cls = String(el.className || '').toLowerCase();
            if (cls.includes('exit')) return false; // Abaikan teks bulan yang sedang slide exit
            const txt = (el.innerText || el.textContent || '').trim();
            return parseBulanFromText(txt) !== null;
        });

        let monthTextEl = textNodes[0] || null;
        let displayedMonth = null;
        let rawMonthText = '';
        if (monthTextEl) {
            rawMonthText = (monthTextEl.innerText || monthTextEl.textContent || '').trim();
            displayedMonth = parseBulanFromText(rawMonthText);
        } else {
            rawMonthText = (labelContainer.innerText || labelContainer.textContent || headerContainer.innerText || '').trim();
            displayedMonth = parseBulanFromText(rawMonthText);
        }

        return { monthTextEl, displayedMonth, rawMonthText, prevBtn, nextBtn };
    }

    // Fallback global jika class berbeda: cari 2 tombol ikon yang memiliki SVG panah di bagian atas modal
    const modalRect = modal.getBoundingClientRect();
    const iconButtons = Array.from(modal.querySelectorAll('button, [role="button"]')).filter(b => {
        const r = b.getBoundingClientRect();
        if (r.width <= 0 || r.height <= 0) return false;
        const txt = (b.innerText || b.textContent || '').trim().toUpperCase();
        if (txt === 'OK' || txt === 'CANCEL') return false;
        if (b.querySelector('svg') === null && !txt.includes('<') && !txt.includes('>')) return false;
        // Ukuran tombol panah navigasi kecil (< 55x55) dan berada di bagian atas kalender (< 45% tinggi modal)
        return r.width < 55 && r.height < 55 && (r.top - modalRect.top < modalRect.height * 0.45);
    });

    if (iconButtons.length >= 2) {
        iconButtons.sort((a, b) => a.getBoundingClientRect().left - b.getBoundingClientRect().left);
        const prevBtn = iconButtons[0];
        const nextBtn = iconButtons[iconButtons.length - 1];

        const textEls = Array.from(modal.querySelectorAll('p, h6, h5, span, div')).filter(el => {
            if (el.children.length > 0) return false;
            const r = el.getBoundingClientRect();
            if (r.top - modalRect.top > modalRect.height * 0.45) return false;
            const txt = (el.innerText || el.textContent || '').trim();
            return parseBulanFromText(txt) !== null;
        });

        const monthTextEl = textEls[0] || null;
        const rawMonthText = monthTextEl ? (monthTextEl.innerText || monthTextEl.textContent || '').trim() : '';
        const displayedMonth = monthTextEl ? parseBulanFromText(rawMonthText) : null;
        return { monthTextEl, displayedMonth, rawMonthText, prevBtn, nextBtn };
    }

    return { monthTextEl: null, displayedMonth: null, rawMonthText: '', prevBtn: null, nextBtn: null };
}

/**
 * Navigasi bulan menggunakan tombol panah (< / >) hingga bulan target tercapai
 */
function navigateToTargetMonth(modal, targetMonthNum, onMonthReached) {
    let steps = 0;
    const maxSteps = 20;

    function step() {
        steps++;
        const nav = getMonthNavigation(modal);
        console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Cek navigasi bulan (Langkah ${steps}):`, {
            rawTeks: nav.rawMonthText || '(kosong)',
            bulanTampil: nav.displayedMonth ? NAMA_BULAN_INDO[nav.displayedMonth] : 'tidak terdeteksi',
            bulanTarget: NAMA_BULAN_INDO[targetMonthNum]
        });

        if (nav.displayedMonth === targetMonthNum) {
            console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] ✓ Bulan target (${NAMA_BULAN_INDO[targetMonthNum]}) telah tampil.`);
            if (typeof onMonthReached === 'function') onMonthReached(true);
            return;
        }

        if (steps >= maxSteps || !nav.prevBtn || !nav.nextBtn || nav.displayedMonth === null) {
            console.warn('[TPQ-EMIS Bridge] [Otomasi Kalender] Navigasi bulan selesai/fallback.');
            if (typeof onMonthReached === 'function') onMonthReached(false);
            return;
        }

        const btnToClick = nav.displayedMonth > targetMonthNum ? nav.prevBtn : nav.nextBtn;
        console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Klik tombol navigasi bulan (${nav.displayedMonth > targetMonthNum ? 'MUNDUR' : 'MAJU'}):`, btnToClick);
        
        // HANYA 1 KALI KLIK NATIVE!
        // Jangan panggil invokeReactHandlers() atau triggerNativeClick() di sini!
        // Tombol navigasi bulan di modal EMIS adalah HTML <button> Material-UI standar.
        // Memanggil invokeReactHandlers + triggerNativeClick + .click() memicu 5x event click berturut-turut,
        // yang menyebabkan kalender melompat 5 bulan sekaligus per langkah (misal Juni loncat langsung ke Januari).
        try {
            btnToClick.click();
        } catch (_) {
            btnToClick.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
        }

        // Jeda 450ms agar animasi CSS slide transition Material-UI selesai sebelum langkah berikutnya
        setTimeout(step, 450);
    }

    setTimeout(step, 100);
}

/**
 * Memilih tanggal target (misal: 13) pada grid kalender Material-UI
 */
function selectTargetDay(modal, targetDayNum, onDaySelected) {
    const targetDayStr = String(targetDayNum);

    // Cari semua tombol hari di kalender (Material-UI menggunakan .MuiPickersDay-day)
    const dayButtons = Array.from(modal.querySelectorAll('.MuiPickersDay-day, [class*="PickersDay-day"], .MuiPickersCalendar-week button, button')).filter(btn => {
        const rect = btn.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) return false;
        if (btn.disabled || btn.getAttribute('aria-disabled') === 'true') return false;
        
        // Pastikan teks angka hari cocok
        const txt = (btn.innerText || btn.textContent || '').trim();
        if (txt !== targetDayStr) return false;
        
        // Pastikan bukan tombol navigasi atau footer (tombol navigasi memiliki SVG)
        if (btn.querySelector('svg')) return false;
        
        // Jangan ambil hari yang didisable / disembunyikan dari bulan lain
        const cls = String(btn.className || '');
        if (cls.includes('hidden') || cls.includes('dayDisabled')) return false;

        return true;
    });

    if (dayButtons.length > 0) {
        const targetBtn = dayButtons[0];
        console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Memilih tanggal ${targetDayStr}:`, targetBtn);
        invokeReactHandlers(targetBtn);
        triggerNativeClick(targetBtn);
        try { targetBtn.click(); } catch (_) {}
        setTimeout(() => {
            if (typeof onDaySelected === 'function') onDaySelected(true);
        }, 200);
    } else {
        console.warn(`[TPQ-EMIS Bridge] [Otomasi Kalender] Tanggal ${targetDayStr} tidak ditemukan pada grid.`);
        if (typeof onDaySelected === 'function') onDaySelected(false);
    }
}

/**
 * Menekan tombol "OK" pada footer modal kalender
 */
function clickModalOkButton(modal, getElementsFn, onDone) {
    const btns = Array.from(modal.querySelectorAll('button, [role="button"], div[role="button"], a')).filter(b => {
        const txt = (b.innerText || b.textContent || b.value || '').trim().toUpperCase();
        return txt === 'OK';
    });

    if (btns.length > 0) {
        const okBtn = btns.find(b => b.tagName === 'BUTTON') || btns[0];
        console.log('[TPQ-EMIS Bridge] [Otomasi Kalender] Menekan tombol OK di modal:', okBtn);
        invokeReactHandlers(okBtn);
        triggerNativeClick(okBtn);
        try { okBtn.click(); } catch (_) {}
    } else {
        console.warn('[TPQ-EMIS Bridge] [Otomasi Kalender] Tombol OK tidak ditemukan di footer.');
    }

    setTimeout(() => {
        const tglInfo = typeof getElementsFn === 'function' ? getElementsFn() : findTanggalLahirElements();
        const displayVal = tglInfo?.root ? (tglInfo.root.innerText || tglInfo.root.textContent || '') : '';
        console.log('[TPQ-EMIS Bridge] [Otomasi Kalender] Nilai form setelah tombol OK:', displayVal.trim());

        if (typeof onDone === 'function') onDone(true);
    }, 300);
}

/**
 * Alur Utama Universal: Otomatisasi Pemilihan Tanggal pada Kalender Material-UI EMIS
 * Digunakan oleh Tanggal Lahir (Step 1) dan Tanggal Masuk (Step 2).
 */
function autoPickDateFromCalendar(triggerOpenerFn, getElementsFn, tanggalStr, fieldLabel, callback) {
    if (!tanggalStr) {
        if (typeof callback === 'function') callback(false);
        return;
    }

    const parts = String(tanggalStr).trim().split('-');
    if (parts.length !== 3) {
        console.warn(`[TPQ-EMIS Bridge] [Otomasi Kalender] Format ${fieldLabel} tidak valid:`, tanggalStr);
        if (typeof callback === 'function') callback(false);
        return;
    }

    const targetYear = parseInt(parts[0], 10);
    const targetMonth = parseInt(parts[1], 10);
    const targetDay = parseInt(parts[2], 10);

    console.log(`%c[TPQ-EMIS Bridge] [Otomasi Kalender] Memulai pemilihan otomatis ${fieldLabel}: ${targetDay} ${NAMA_BULAN_INDO[targetMonth]} ${targetYear}`, 'background: #0284c7; color: #fff; font-weight: bold; padding: 2px 6px;');

    if (typeof triggerOpenerFn === 'function') triggerOpenerFn();

    let openAttempts = 0;
    const maxOpenAttempts = 35;

    const openTimer = setInterval(() => {
        openAttempts++;
        const modal = findCalendarModal();

        if (modal) {
            clearInterval(openTimer);
            console.log(`%c[TPQ-EMIS Bridge] [Otomasi Kalender] ✓ Modal kalender ${fieldLabel} terdeteksi di layar!`, 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px;');

            setTimeout(() => {
                const yearHeader = findModalYearHeader(modal);
                if (yearHeader) {
                    console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Mengklik header tahun (${fieldLabel}):`, yearHeader.innerText.trim());
                    invokeReactHandlers(yearHeader);
                    triggerNativeClick(yearHeader);
                    try { yearHeader.click(); } catch (_) {}

                    setTimeout(() => {
                        selectYearFromList(modal, String(targetYear), () => {
                            setTimeout(() => {
                                navigateToTargetMonth(modal, targetMonth, () => {
                                    setTimeout(() => {
                                        selectTargetDay(modal, targetDay, () => {
                                            setTimeout(() => {
                                                clickModalOkButton(modal, getElementsFn, () => {
                                                    console.log(`%c[TPQ-EMIS Bridge] [Otomasi Kalender] ✓ Selesai ${fieldLabel}! Target: ${tanggalStr}`, 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px;');
                                                    if (typeof callback === 'function') callback(true);
                                                });
                                            }, 200);
                                        });
                                    }, 200);
                                });
                            }, 400);
                        });
                    }, 200);
                } else {
                    console.warn(`[TPQ-EMIS Bridge] [Otomasi Kalender] Header tahun (${fieldLabel}) tidak ditemukan.`);
                    if (typeof callback === 'function') callback(false);
                }
            }, 200);
        } else {
            if (openAttempts % 5 === 0) {
                console.log(`[TPQ-EMIS Bridge] [Otomasi Kalender] Mencoba trigger buka kalender ${fieldLabel} kembali...`);
                if (typeof triggerOpenerFn === 'function') triggerOpenerFn();
            }

            if (openAttempts >= maxOpenAttempts) {
                clearInterval(openTimer);
                console.warn(`[TPQ-EMIS Bridge] [Otomasi Kalender] Modal kalender ${fieldLabel} tidak muncul setelah 3.5 detik.`);
                if (typeof callback === 'function') callback(false);
            }
        }
    }, 100);
}

function autoPickTanggalLahirStep1(tanggalStr, callback) {
    return autoPickDateFromCalendar(triggerOpenCalendarPicker, findTanggalLahirElements, tanggalStr, 'Tanggal Lahir', callback);
}

function autoPickTanggalMasukStep2(tanggalStr, callback) {
    return autoPickDateFromCalendar(triggerOpenTanggalMasukPicker, findTanggalMasukElements, tanggalStr, 'Tanggal Masuk', callback);
}

/**
 * Mencari input Nama Lengkap, Tempat Lahir, dan Tanggal Lahir di Step 1
 */
function findStep1Fields() {
    let inputNama = null;
    let inputTempat = null;
    let inputTanggal = null;

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
        } else if (combined.includes('NAMA') && !combined.includes('TEMPAT') && !inputNama) {
            inputNama = inp;
        }
    }

    // 2. Fallback posisi geometris: input pertama adalah Nama, kedua Tempat Lahir
    // Pastikan tidak menyertakan input NIK dalam fallback geometris
    if (!inputNama || !inputTempat) {
        const nonNikInputs = allInputs.filter(inp => {
            const ph = (inp.getAttribute('placeholder') || '').toUpperCase();
            const nm = (inp.getAttribute('name') || '').toUpperCase();
            const id = (inp.getAttribute('id') || '').toUpperCase();
            let lbl = '';
            let p = inp.parentElement;
            for (let i = 0; i < 3 && p; i++) {
                lbl += ' ' + (p.innerText || p.textContent || '').toUpperCase();
                p = p.parentElement;
            }
            const c = `${ph} ${nm} ${id} ${lbl}`;
            return (!c.includes('NIK') || c.includes('NAMA')) && !c.includes('NISN');
        });

        if (nonNikInputs.length >= 2) {
            const sorted = [...nonNikInputs].sort((a, b) => {
                const rectA = a.getBoundingClientRect();
                const rectB = b.getBoundingClientRect();
                if (Math.abs(rectA.top - rectB.top) > 20) {
                    return rectA.top - rectB.top;
                }
                return rectA.left - rectB.left;
            });

            if (!inputNama) inputNama = sorted[0];
            if (!inputTempat) inputTempat = sorted[1];
        }
    }

    // Tanggal Lahir di EMIS 4.0 adalah komponen React Native Web, bukan elemen <input> biasa
    const tglInfo = findTanggalLahirElements();
    if (tglInfo) {
        inputTanggal = tglInfo.button || tglInfo.focusable || tglInfo.root;
    }

    return { inputNama, inputTempat, inputTanggal };
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
    const { inputNama, inputTempat, inputTanggal } = fields;

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

    // Tutup popup start Step 0 jika sedang ada agar tidak bertumpuk
    const oldStep0 = document.getElementById('tpq-step0-alert');
    if (oldStep0) oldStep0.remove();
    const oldPill = document.getElementById('tpq-reopen-step0-pill');
    if (oldPill) oldPill.remove();

    // 4. Tampilkan floating notification Step 1 (dengan fitur salin NIK, Nama, TTL & draggable)
    showStep1Assistant(data);

    // 5. Otomatis pilih Tanggal Lahir pada Kalender EMIS (Flicker Calendar Picker)
    if (data.tanggal_lahir) {
        const statusEl = document.getElementById('tpq-step1-tgl-status');
        const descEl = document.getElementById('tpq-step1-tgl-desc');
        if (statusEl) {
            statusEl.innerText = '⚡ Memilih...';
            statusEl.style.background = '#e0f2fe';
            statusEl.style.color = '#0369a1';
        }
        if (descEl) {
            descEl.innerHTML = '⚡ Sedang memilih tanggal lahir otomatis di kalender EMIS...';
        }

        setTimeout(() => {
            autoPickTanggalLahirStep1(data.tanggal_lahir, (success) => {
                const sEl = document.getElementById('tpq-step1-tgl-status');
                const dEl = document.getElementById('tpq-step1-tgl-desc');
                if (success) {
                    onStep1CalendarPickedSuccess();
                } else {
                    if (sEl) {
                        sEl.innerText = 'Manual';
                        sEl.style.background = '#fef3c7';
                        sEl.style.color = '#b45309';
                    }
                    if (dEl) {
                        dEl.innerHTML = 'Pilih tanggal di atas pada kalender EMIS, lalu klik tombol <strong>SELANJUTNYA</strong>.';
                    }
                }
            });
        }, 300);
    }

    // 6. Simpan state antrean untuk Step 2
    chrome.storage.local.set({
        pendingSantriData: data,
        waitingForStep2: true
    });
}

/**
 * ============================================================================
 * STEP 1 AUTO-DETECTION & AUTO-FILL SYSTEM
 * Mendeteksi perpindahan pilihan/tab di formulir EMIS (NIK vs Nama & TTL)
 * secara otomatis mengisi input tanpa mengharuskan user menekan tombol di popup
 * ============================================================================
 */
let step1FilledSantriKey = null; // Menyimpan ID santri + mode agar tidak looping mengisi terus-menerus
let isStep1AutoPollActive = false;

function isNamaTtlRadioElement(target) {
    if (!target) return false;

    // PENTING: JANGAN PERNAH mendeteksi text input, textarea, select, tombol sebagai radio!
    const tag = (target.tagName || '').toUpperCase();
    if (tag === 'INPUT') {
        const type = (target.type || '').toLowerCase();
        if (type !== 'radio') return false;
    } else if (tag === 'TEXTAREA' || tag === 'SELECT' || tag === 'BUTTON') {
        return false;
    }

    // Abaikan elemen dari popup ekstensi kita sendiri
    if (target.closest && (
        target.closest('#tpq-step0-alert') ||
        target.closest('#tpq-step1-alert') ||
        target.closest('#tpq-step2-alert') ||
        target.closest('#tpq-reopen-step0-pill') ||
        target.closest('#tpq-pending-banner')
    )) {
        return false;
    }

    // Pastikan klik berada di elemen radio atau label "Nama, Tempat, dan tanggal lahir"
    let curr = target;
    for (let i = 0; i < 4 && curr && curr !== document.body; i++) {
        // Jika elemen ini membungkus input teks (misal baris form tempat lahir), ini BUKAN radio!
        if (curr.querySelector && curr.querySelector('input:not([type="radio"]):not([type="hidden"]):not([type="checkbox"])')) {
            return false;
        }

        const text = (curr.innerText || curr.textContent || '').trim().toUpperCase();
        if (text === 'NAMA, TEMPAT, DAN TANGGAL LAHIR' ||
            (text.includes('TEMPAT') && text.includes('TANGGAL LAHIR') && !text.includes('NISN') && text.length <= 40)) {
            return true;
        }
        curr = curr.parentElement;
    }
    return false;
}

function isNikRadioElement(target) {
    if (!target) return false;

    const tag = (target.tagName || '').toUpperCase();
    if (tag === 'INPUT') {
        const type = (target.type || '').toLowerCase();
        if (type !== 'radio') return false;
    } else if (tag === 'TEXTAREA' || tag === 'SELECT' || tag === 'BUTTON') {
        return false;
    }

    if (target.closest && (
        target.closest('#tpq-step0-alert') ||
        target.closest('#tpq-step1-alert') ||
        target.closest('#tpq-step2-alert') ||
        target.closest('#tpq-reopen-step0-pill') ||
        target.closest('#tpq-pending-banner')
    )) {
        return false;
    }

    let curr = target;
    for (let i = 0; i < 4 && curr && curr !== document.body; i++) {
        if (curr.querySelector && curr.querySelector('input:not([type="radio"]):not([type="hidden"]):not([type="checkbox"])')) {
            return false;
        }

        const text = (curr.innerText || curr.textContent || '').trim().toUpperCase();
        if (text === 'NIK' && text.length <= 10) {
            return true;
        }
        curr = curr.parentElement;
    }
    return false;
}

function setupStep1AutoDetection(santriData) {
    if (!santriData) return;

    // HANYA pasang event listener 'click' ke document (JANGAN 'change' atau 'input' agar tidak looping)
    if (!window.__tpqStep1ListenerAttached) {
        window.__tpqStep1ListenerAttached = true;

        document.addEventListener('click', (e) => {
            handleStep1OptionSwitch(e.target);
        }, true);
    }

    triggerStep1InitialFill(santriData);
}

function handleStep1OptionSwitch(target) {
    if (!target) return;
    if (detectCurrentEmisStep() !== 1) return;

    if (target.closest && (
        target.closest('#tpq-step0-alert') ||
        target.closest('#tpq-step1-alert') ||
        target.closest('#tpq-step2-alert') ||
        target.closest('#tpq-reopen-step0-pill') ||
        target.closest('#tpq-pending-banner')
    )) {
        return;
    }

    chrome.storage.local.get(['pendingSantriData'], (res) => {
        if (!res || !res.pendingSantriData) return;
        const santriData = res.pendingSantriData;

        if (isNamaTtlRadioElement(target)) {
            console.log('[TPQ-EMIS Bridge] Pilihan "Nama, Tempat, dan tanggal lahir" diklik di EMIS!');
            autoFillStep1NamaTtl(santriData);
        } else if (isNikRadioElement(target)) {
            console.log('[TPQ-EMIS Bridge] Pilihan "NIK" diklik di EMIS!');
            autoFillStep1Nik(santriData);
        }
    });
}

function autoFillStep1NamaTtl(santriData) {
    const santriKey = (santriData.id_santri || santriData.nama_santri) + '_nama_ttl';
    if (step1FilledSantriKey === santriKey) {
        return; // Sudah pernah diisi, hindari looping
    }

    if (isStep1AutoPollActive) return;
    isStep1AutoPollActive = true;

    let attempts = 0;
    const maxAttempts = 20;

    const pollTimer = setInterval(() => {
        attempts++;
        const fields = findStep1Fields();

        if (fields.inputNama && fields.inputTempat) {
            clearInterval(pollTimer);
            isStep1AutoPollActive = false;
            step1FilledSantriKey = santriKey;
            console.log('[TPQ-EMIS Bridge] Field Nama & TTL terdeteksi di DOM, mengisi satu kali...');
            eksekusiIsiStep1(fields, santriData);
        } else if (attempts >= maxAttempts) {
            clearInterval(pollTimer);
            isStep1AutoPollActive = false;
            if (fields.inputNama || fields.inputTempat) {
                step1FilledSantriKey = santriKey;
                eksekusiIsiStep1(fields, santriData);
            }
        }
    }, 120);
}

function autoFillStep1Nik(santriData) {
    if (!santriData.nik || santriData.belum_punya_nik) return;
    const santriKey = (santriData.id_santri || santriData.nama_santri) + '_nik';
    if (step1FilledSantriKey === santriKey) return;

    let attempts = 0;
    const maxAttempts = 15;

    // Pastikan radio NIK terpilih
    selectRadioNikStep1();

    const pollTimer = setInterval(() => {
        attempts++;
        const filled = tryFillNikInput(santriData.nik);
        if (filled || attempts >= maxAttempts) {
            clearInterval(pollTimer);
            if (filled) {
                step1FilledSantriKey = santriKey;
                // Lanjut: Klik tombol SELANJUTNYA & pantau respons EMIS
                handleStep1NikSubmission(santriData);
            }
        }
    }, 120);
}

function handleStep1NikSubmission(santriData) {
    console.log(`%c[TPQ-EMIS Bridge] [Step 1] NIK ${santriData.nik} berhasil diisi. Menyiapkan penekanan tombol SELANJUTNYA...`, 'background: #0284c7; color: #fff; font-weight: bold; padding: 2px 6px;');

    window.__tpqStep1CheckingNik = true;

    // Beri jeda 350ms agar React selesai memproses input state
    setTimeout(() => {
        let attempts = 0;
        const maxAttempts = 15; // 3 detik

        const btnTimer = setInterval(() => {
            attempts++;
            const btn = findStep1SelanjutnyaButton();
            if (btn) {
                clearInterval(btnTimer);
                console.log('%c[TPQ-EMIS Bridge] [Step 1] ✓ Tombol SELANJUTNYA ditemukan. Menekan tombol...', 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px;');
                triggerNativeClick(btn);
                try { btn.click(); } catch (_) {}

                // Mulai memantau respons modal / transisi EMIS
                monitorStep1NikValidationResult(santriData);
            } else if (attempts >= maxAttempts) {
                clearInterval(btnTimer);
                window.__tpqStep1CheckingNik = false;
                console.warn('[TPQ-EMIS Bridge] [Step 1] Tombol SELANJUTNYA tidak ditemukan setelah 3 detik.');
            }
        }, 200);
    }, 350);
}

function monitorStep1NikValidationResult(santriData) {
    console.log('[TPQ-EMIS Bridge] [Step 1] Memantau respons EMIS (Modal NIK Tidak Ditemukan vs NIK Ditemukan)...');

    let attempts = 0;
    const maxAttempts = 60; // 12 detik (60 * 200ms)

    const monitorTimer = setInterval(() => {
        attempts++;

        // 1. Prioritas Pertama: Cek Modal "NIK Tidak Ditemukan" (Gambar 2)
        const modalInfo = findNikTidakDitemukanModal();
        if (modalInfo) {
            clearInterval(monitorTimer);
            window.__tpqStep1CheckingNik = false;
            console.log('%c[TPQ-EMIS Bridge] [Step 1] ✓ Popup "NIK Tidak Ditemukan" terdeteksi! Mengklik tombol TUTUP...', 'background: #2563eb; color: #fff; font-weight: bold; padding: 2px 6px;');
            
            // Klik tombol TUTUP di modal
            triggerNativeClick(modalInfo.tutupBtn);
            try { modalInfo.tutupBtn.click(); } catch (_) {}

            // Beri jeda 350ms agar modal tertutup di DOM, lalu beralih ke radio Nama & TTL
            setTimeout(() => {
                console.log('%c[TPQ-EMIS Bridge] [Step 1] Memilih radio "Nama, Tempat, dan tanggal lahir" dan otomatis mengisi data...', 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px;');
                selectRadioStep1();

                // Reset flag pengisian agar autoFillStep1NamaTtl dapat berjalan lancar
                isStep1AutoPollActive = false;
                step1FilledSantriKey = null;

                setTimeout(() => {
                    autoFillStep1NamaTtl(santriData);
                }, 300);
            }, 350);
            return;
        }

        // 2. Prioritas Kedua: Cek jika NIK Ditemukan (Halaman berpindah ke Step 2 atau muncul notifikasi data ditemukan)
        const foundResponse = isNikFoundResponsePresent();
        if (foundResponse) {
            clearInterval(monitorTimer);
            handleNikFoundInEmis(santriData, foundResponse.message);
            return;
        }

        // 3. Batas waktu tercapai tanpa respons
        if (attempts >= maxAttempts) {
            clearInterval(monitorTimer);
            window.__tpqStep1CheckingNik = false;
            console.log('[TPQ-EMIS Bridge] [Step 1] Waktu pantau respons NIK selesai (12 detik).');
        }
    }, 200);
}

function checkAndAutoFillStep1Watcher(santriData) {
    if (!santriData) return;

    const santriKeyTtl = (santriData.id_santri || santriData.nama_santri) + '_nama_ttl';
    if (step1FilledSantriKey === santriKeyTtl) return; // Sudah pernah diisi, abaikan

    const fields = findStep1Fields();
    if (fields.inputNama && fields.inputTempat) {
        const curNama = (fields.inputNama.value || '').trim();
        const curTempat = (fields.inputTempat.value || '').trim();

        // Hanya isi jika KEDUA field benar-benar masih kosong dan user tidak sedang mengetik
        if (!curNama && !curTempat &&
            document.activeElement !== fields.inputNama &&
            document.activeElement !== fields.inputTempat &&
            !isStep1AutoPollActive) {
            step1FilledSantriKey = santriKeyTtl;
            console.log('[TPQ-EMIS Bridge] Watcher mendeteksi field kosong, mengisi satu kali...');
            eksekusiIsiStep1(fields, santriData);
        }
    }
}

function triggerStep1InitialFill(santriData) {
    if (santriData.belum_punya_nik || !santriData.nik) {
        console.log('[TPQ-EMIS Bridge] Santri belum punya NIK, otomatis memilih tab Nama & TTL...');
        selectRadioStep1();
        autoFillStep1NamaTtl(santriData);
        return;
    }

    const fields = findStep1Fields();
    if (fields.inputNama && fields.inputTempat) {
        const curNama = (fields.inputNama.value || '').trim();
        if (!curNama) {
            console.log('[TPQ-EMIS Bridge] Form telah berada di tab Nama & TTL, langsung mengisi...');
            autoFillStep1NamaTtl(santriData);
        }
        return;
    }

    setTimeout(() => {
        autoFillStep1Nik(santriData);
    }, 250);
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
    window.__tpqStep2TglMasukAutoStarted = false;
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
 * Normalisasi nilai jarak tempuh dari database / format apapun ke format opsi EMIS
 */
function normalizeJarakTempuhEmis(rawJarak) {
    if (!rawJarak) return 'Kurang dari 5 km';
    const str = String(rawJarak).trim().toLowerCase();
    if (str.includes('kurang') || str.includes('<') || str.includes('5 km') || str === '1' || str.includes('kurang dari 5')) {
        return 'Kurang dari 5 km';
    }
    if (str.includes('5') && str.includes('10')) {
        return 'Antara 5 – 10 Km';
    }
    if (str.includes('11') && str.includes('20')) {
        return 'Antara 11 – 20 Km';
    }
    if (str.includes('21') && str.includes('30')) {
        return 'Antara 21 – 30 Km';
    }
    if (str.includes('lebih') || str.includes('>')) {
        return 'Lebih dari 30 Km';
    }
    return 'Kurang dari 5 km';
}

/**
 * Helper untuk mengecek apakah dropdown Jarak sudah benar-benar terpilih di DOM
 */
function checkIsJarakSelected() {
    const selects = Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0) continue;
        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const hasKurang5Km = optionTexts.some(t => t.includes('kurang dari 5 km') || t.includes('kurang dari 5'));
        const hasLebih30Km = optionTexts.some(t => t.includes('lebih dari 30 km') || t.includes('lebih dari 30'));
        if (hasKurang5Km && hasLebih30Km) {
            return sel.value === '1' || sel.selectedIndex === 1;
        }
    }
    return false;
}

/**
 * Normalisasi nilai transportasi santri ke format teks pilihan EMIS
 */
function normalizeTransportasiEmis(rawTransport) {
    if (!rawTransport) return 'Jalan Kaki';
    const str = String(rawTransport).trim().toLowerCase();
    if (str.includes('motor')) return 'Sepeda Motor';
    if (str.includes('sepeda')) return 'Sepeda';
    if (str.includes('jalan')) return 'Jalan Kaki';
    if (str.includes('mobil')) return 'Mobil Pribadi';
    if (str.includes('ojek')) return 'Ojek';
    if (str.includes('antar') || str.includes('jemput')) return 'Antar Jemput Sekolah';
    if (str.includes('angkutan') || str.includes('umum')) return 'Angkutan Umum';
    if (str.includes('perahu') || str.includes('sampan')) return 'Perahu/Sampan';
    if (str.includes('andong') || str.includes('delman') || str.includes('becak')) return 'Andong/Bendi/Sado/Dokar/Delman/Becak';
    return String(rawTransport).trim();
}

/**
 * Normalisasi nilai waktu tempuh santri ke format teks pilihan EMIS
 */
function normalizeWaktuTempuhEmis(rawWaktu) {
    if (!rawWaktu) return '1-10 menit';
    const str = String(rawWaktu).trim().toLowerCase();
    if (str.includes('1-10') || str.includes('1 - 10') || str.includes('kurang dari 10')) return '1-10 menit';
    if (str.includes('10-19') || str.includes('10 - 19') || str.includes('11-20') || str.includes('11 - 20')) return '10-19 menit';
    if (str.includes('20-29') || str.includes('20 - 29') || str.includes('21-30') || str.includes('21 - 30')) return '20-29 menit';
    if (str.includes('30-39') || str.includes('30 - 39') || str.includes('31-40') || str.includes('31 - 40')) return '30-39 menit';
    if (str.includes('1-2 jam') || str.includes('1 - 2 jam')) return '1-2 jam';
    if (str.includes('> 2 jam') || str.includes('>2 jam') || str.includes('lebih dari 2')) return '> 2 jam';
    return String(rawWaktu).trim();
}

/**
 * Helper untuk mengecek apakah dropdown Transportasi sudah benar-benar terpilih di DOM
 */
function checkIsTransportasiSelected() {
    const selects = Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0) continue;
        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const hasJalanKaki = optionTexts.some(t => t.includes('jalan kaki'));
        const hasSepeda = optionTexts.some(t => t.includes('sepeda'));
        if (hasJalanKaki && hasSepeda) {
            return (sel.value !== '' && sel.value !== '0') || sel.selectedIndex > 0;
        }
    }
    return false;
}

/**
 * Helper untuk mengecek apakah dropdown Waktu Tempuh sudah benar-benar terpilih di DOM
 */
function checkIsWaktuTempuhSelected() {
    const selects = Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0) continue;
        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const hasMenit = optionTexts.some(t => t.includes('menit'));
        const hasKm = optionTexts.some(t => t.includes('km'));
        if (hasMenit && !hasKm) {
            return (sel.value !== '' && sel.value !== '0') || sel.selectedIndex > 0;
        }
    }
    return false;
}

/**
 * Mencari dan memilih dropdown Transportasi ke Lembaga di Tab DATA ALAMAT
 */
function selectTransportasiEmis(rawTransport) {
    const target = normalizeTransportasiEmis(rawTransport);
    console.log('[TPQ-EMIS Bridge] Mencari dan memilih dropdown Transportasi:', target, '(raw:', rawTransport, ')');

    const selects = Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0) continue;

        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const hasJalanKaki = optionTexts.some(t => t.includes('jalan kaki'));
        const hasSepeda = optionTexts.some(t => t.includes('sepeda'));
        const hasMotor = optionTexts.some(t => t.includes('motor'));

        let contextText = (sel.name || '') + ' ' + (sel.id || '');
        let p = sel.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            contextText += ' ' + (p.innerText || p.textContent || '');
            p = p.parentElement;
        }
        const contextUpper = contextText.toUpperCase();
        const hasTransportLabel = contextUpper.includes('TRANSPORTASI') || contextUpper.includes('KENDARAAN');

        if ((hasJalanKaki && (hasSepeda || hasMotor)) || (hasJalanKaki && hasTransportLabel) || (hasTransportLabel && options.length > 3)) {
            console.log('[TPQ-EMIS Bridge] Dropdown Transportasi ditemukan! Menyetel ke:', target);
            let success = setNativeSelectValue(sel, target);
            if (!success || sel.value === '' || sel.value === '0') {
                success = setNativeSelectValue(sel, 'Jalan Kaki');
                if (!success && options.length > 1) {
                    success = setNativeSelectValue(sel, options[1].value || options[1].text);
                }
            }
            if (success || (sel.value && sel.value !== '0') || sel.selectedIndex > 0) {
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

/**
 * Mencari dan memilih dropdown Waktu Tempuh ke Lembaga di Tab DATA ALAMAT
 */
function selectWaktuTempuhEmis(rawWaktu) {
    const target = normalizeWaktuTempuhEmis(rawWaktu);
    console.log('[TPQ-EMIS Bridge] Mencari dan memilih dropdown Waktu Tempuh:', target, '(raw:', rawWaktu, ')');

    const selects = Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0) continue;

        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const hasMenit = optionTexts.some(t => t.includes('menit'));
        const hasJam = optionTexts.some(t => t.includes('jam'));
        const hasKm = optionTexts.some(t => t.includes('km'));

        let contextText = (sel.name || '') + ' ' + (sel.id || '');
        let p = sel.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            contextText += ' ' + (p.innerText || p.textContent || '');
            p = p.parentElement;
        }
        const contextUpper = contextText.toUpperCase();
        const hasWaktuLabel = contextUpper.includes('WAKTU') || contextUpper.includes('TEMPUH');

        if ((hasMenit && hasJam && !hasKm) || (hasMenit && hasWaktuLabel && !hasKm)) {
            console.log('[TPQ-EMIS Bridge] Dropdown Waktu Tempuh ditemukan! Menyetel ke:', target);
            let success = setNativeSelectValue(sel, target);
            if (!success || sel.value === '' || sel.value === '0') {
                success = setNativeSelectValue(sel, '1-10 menit');
                if (!success || sel.value === '' || sel.value === '0') {
                    success = setNativeSelectValue(sel, '1');
                }
            }
            if (success || (sel.value && sel.value !== '0') || sel.selectedIndex > 0) {
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

/**
 * Mencari dan memilih dropdown Jarak Tempat Tinggal ke Lembaga di Tab DATA ALAMAT
 * Ciri opsi: "Kurang dari 5 km" (value 1), "Antara 5 – 10 Km" (value 2), dst
 */
function selectJarakTempatTinggalEmis(rawJarak) {
    const target = normalizeJarakTempuhEmis(rawJarak);
    console.log('[TPQ-EMIS Bridge] Mencari dan memilih dropdown Jarak:', target, '(raw:', rawJarak, ')');

    const selects = Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0) continue;

        const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const optionValues = options.map(o => String(o.value ?? '').trim());

        // Ciri unik dropdown Jarak di EMIS:
        const hasKurang5Km = optionTexts.some(t => t.includes('kurang dari 5 km') || t.includes('kurang dari 5'));
        const hasLebih30Km = optionTexts.some(t => t.includes('lebih dari 30 km') || t.includes('lebih dari 30'));
        const hasKm = optionTexts.some(t => t.includes('km'));

        let contextText = (sel.name || '') + ' ' + (sel.id || '');
        let p = sel.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            contextText += ' ' + (p.innerText || p.textContent || '');
            p = p.parentElement;
        }
        const contextUpper = contextText.toUpperCase();
        const hasJarakLabel = contextUpper.includes('JARAK');

        if ((hasKurang5Km && hasLebih30Km) || (hasKm && hasJarakLabel) || (hasKurang5Km && hasJarakLabel)) {
            console.log('[TPQ-EMIS Bridge] Dropdown Jarak ditemukan! Menyetel ke:', target);
            // 1. Coba dengan teks target ("Kurang dari 5 km")
            let success = setNativeSelectValue(sel, target);
            // 2. Jika belum terpilih (value masih kosong), paksa coba dengan value "1"
            if (!success || sel.value === '' || sel.value === '0') {
                success = setNativeSelectValue(sel, '1');
            }
            if (success || sel.value === '1' || sel.selectedIndex === 1) {
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

/**
 * Helper untuk mem-parse format titik koordinat ("lat, lng" atau single string)
 */
function parseTitikKoordinat(rawCoord) {
    if (!rawCoord) return { raw: '', lat: '', lng: '' };
    const str = String(rawCoord).trim();
    const parts = str.split(',').map(s => s.trim());
    if (parts.length >= 2) {
        return { raw: str, lat: parts[0], lng: parts[1] };
    }
    return { raw: str, lat: str, lng: '' };
}

/**
 * Mencari dan mengisi input Titik Koordinat di Tab DATA ALAMAT
 */
function fillTitikKoordinatEmis(santriCoord) {
    if (!santriCoord) return false;
    const coords = parseTitikKoordinat(santriCoord);
    if (!coords.raw) return false;

    console.log('[TPQ-EMIS Bridge] Mencari dan menyetel field Koordinat di form EMIS:', coords.raw);
    const inputs = Array.from(document.querySelectorAll('input[type="text"], input[type="number"], input:not([type]), textarea'));
    let filled = false;

    for (const inp of inputs) {
        if (inp.disabled || inp.readOnly) continue;

        let labelText = (inp.placeholder || '') + ' ' + (inp.name || '') + ' ' + (inp.id || '');
        let p = inp.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            const txt = (p.innerText || p.textContent || '').trim();
            if (txt.length < 50) labelText += ' ' + txt;
            p = p.parentElement;
        }
        labelText = labelText.toUpperCase();

        if (labelText.includes('KOORDINAT') || labelText.includes('TITIK KOORDINAT')) {
            console.log('[TPQ-EMIS Bridge] Field Titik Koordinat ditemukan! Menyetel ke:', coords.raw);
            setNativeValue(inp, coords.raw);
            try {
                inp.style.transition = 'background-color 0.3s ease';
                inp.style.backgroundColor = '#d1e7dd';
            } catch (_) {}
            filled = true;
        } else if (labelText.includes('LINTANG') || labelText.includes('LATITUDE')) {
            if (coords.lat) {
                console.log('[TPQ-EMIS Bridge] Field Lintang/Latitude ditemukan! Menyetel ke:', coords.lat);
                setNativeValue(inp, coords.lat);
                filled = true;
            }
        } else if (labelText.includes('BUJUR') || labelText.includes('LONGITUDE')) {
            if (coords.lng) {
                console.log('[TPQ-EMIS Bridge] Field Bujur/Longitude ditemukan! Menyetel ke:', coords.lng);
                setNativeValue(inp, coords.lng);
                filled = true;
            }
        }
    }
    return filled;
}

/**
 * Helper untuk mengecek apakah field Koordinat sudah terisi di DOM
 */
function checkIsKoordinatFilled(santriCoord) {
    if (!santriCoord) return true; // Jika tidak ada koordinat dari database, anggap valid
    const coords = parseTitikKoordinat(santriCoord);
    const inputs = Array.from(document.querySelectorAll('input[type="text"], input[type="number"], input:not([type]), textarea'));

    for (const inp of inputs) {
        let labelText = (inp.placeholder || '') + ' ' + (inp.name || '') + ' ' + (inp.id || '');
        let p = inp.parentElement;
        for (let i = 0; i < 4 && p; i++) {
            const txt = (p.innerText || p.textContent || '').trim();
            if (txt.length < 50) labelText += ' ' + txt;
            p = p.parentElement;
        }
        labelText = labelText.toUpperCase();

        if (labelText.includes('KOORDINAT') || labelText.includes('TITIK KOORDINAT')) {
            return (inp.value || '').trim() !== '';
        }
        if (labelText.includes('LINTANG') || labelText.includes('LATITUDE')) {
            if (coords.lat && (inp.value || '').trim() !== '') return true;
        }
    }
    return false;
}

/**
 * Helper khusus memilih opsi pada elemen <select> (kompatibel React Native for Web / React Synthetic Events)
 */
function setNativeSelectValue(selectElement, targetValueOrText) {
    if (!selectElement || targetValueOrText === undefined || targetValueOrText === null) return false;

    try {
        const rawTarget = String(targetValueOrText).trim();
        const target = rawTarget.toLowerCase();
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

        // 3. Khusus pencocokan angka jarak / km (misal: "Kurang dari 5 km", "Kurang Dari 5 Km", "< 5 km", "1")
        if (!matchedOption && (target.includes('5') || target.includes('kurang') || target === '1')) {
            matchedOption = options.find(opt => {
                const val = String(opt.value ?? '').trim();
                const lbl = String(opt.label || opt.text || opt.innerText || '').trim().toLowerCase();
                return val === '1' || (lbl.includes('5') && (lbl.includes('kurang') || lbl.includes('<')));
            });
        }

        if (!matchedOption) {
            console.warn('[TPQ-EMIS Bridge] Opsi tidak ditemukan pada <select> untuk nilai:', targetValueOrText);
            return false;
        }

        const valToSet = matchedOption.value;
        const lastValue = selectElement.value;

        // Fokus ke elemen sebelum perubahan
        try { selectElement.focus(); } catch (_) {}

        // Tandai option yang dipilih
        const targetIndex = options.indexOf(matchedOption);
        if (targetIndex >= 0) {
            selectElement.selectedIndex = targetIndex;
        }
        options.forEach(opt => {
            opt.selected = (opt === matchedOption);
            if (opt === matchedOption) {
                opt.setAttribute('selected', 'selected');
            } else {
                opt.removeAttribute('selected');
            }
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

        // Integrasi langsung dengan React Fiber / React Native for Web internal state
        try {
            const reactPropKey = Object.keys(selectElement).find(k => k.startsWith('__reactProps$') || k.startsWith('__reactFiber$') || k.startsWith('__reactInternalInstance$'));
            if (reactPropKey) {
                const node = selectElement[reactPropKey];
                const props = node?.memoizedProps || node;
                if (props && typeof props.onChange === 'function') {
                    props.onChange({
                        target: selectElement,
                        currentTarget: selectElement,
                        bubbles: true,
                        preventDefault: () => {},
                        stopPropagation: () => {}
                    });
                }
                if (props && typeof props.onValueChange === 'function') {
                    props.onValueChange(valToSet, targetIndex);
                }
            }
        } catch (_) {}

        // Trigger input & change events dengan bubbles: true & cancelable: true
        selectElement.dispatchEvent(new Event('input', { bubbles: true, cancelable: true }));
        selectElement.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));

        // Highlight visual hijau
        try {
            selectElement.style.transition = 'background-color 0.3s ease, border-color 0.3s ease';
            selectElement.style.backgroundColor = '#d1e7dd';
            selectElement.style.borderColor = '#198754';
            const container = selectElement.closest('.r-16y2uox') || selectElement.closest('.r-crgep1') || selectElement.parentElement;
            if (container) {
                container.style.backgroundColor = '#d1e7dd';
            }
            setTimeout(() => {
                try {
                    selectElement.style.backgroundColor = '';
                    selectElement.style.borderColor = '';
                    if (container) container.style.backgroundColor = '';
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
 * Mengambil teks konteks label sekitar elemen <select> atau input (aman, cepat, tidak memicu reflow DOM berlebih)
 */
function getFieldLabelText(el) {
    if (!el) return '';
    let str = (el.name || '') + ' ' + (el.id || '') + ' ' + (el.getAttribute('aria-label') || '');
    let cur = el;
    for (let i = 0; i < 4 && cur; i++) {
        if (cur.previousElementSibling) {
            str += ' ' + (cur.previousElementSibling.textContent || '').substring(0, 150);
        }
        if (cur.parentElement) {
            str += ' ' + (cur.parentElement.textContent || '').substring(0, 150);
        }
        cur = cur.parentElement;
    }
    return str.toUpperCase();
}

/**
 * Helper untuk memeriksa apakah elemen <select> sedang disabled / non-aktif
 */
function isSelectDisabled(sel) {
    if (!sel) return true;
    if (sel.disabled || sel.hasAttribute('disabled') || sel.getAttribute('aria-disabled') === 'true') return true;
    if (sel.style && (sel.style.pointerEvents === 'none' || sel.style.opacity === '0.5')) return true;
    const fieldset = sel.closest('fieldset[disabled]');
    if (fieldset) return true;
    return false;
}

/**
 * Mencari elemen <select> Status Kepemilikan Rumah (Ayah) di Tab DATA ALAMAT
 * Prioritas:
 * 1. Label DOM bertuliskan "STATUS KEPEMILIKAN RUMAH" atau "KEPEMILIKAN RUMAH"
 * 2. Dropdown tepat sebelum dropdown Provinsi (provIdx - 1)
 * 3. Dropdown dengan opsi spesifik kepemilikan rumah (Milik Sendiri / Orang Tua / Sewa / Kontrak / Dinas)
 * 4. Dropdown pertama di formulir jika opsinya <= 15
 */
function findStatusKepemilikanRumahSelect() {
    // 1. Prioritas Utama: Cari berdasarkan teks label "STATUS KEPEMILIKAN RUMAH" atau "KEPEMILIKAN RUMAH"
    const allLabels = Array.from(document.querySelectorAll('div, label, span, p, h1, h2, h3, h4'));
    for (const el of allLabels) {
        if (el.children.length > 2) continue;
        const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
        if ((txt.includes('KEPEMILIKAN RUMAH') || txt.includes('STATUS RUMAH')) && txt.length <= 50) {
            let p = el.parentElement;
            for (let i = 0; i < 6 && p && p !== document.body; i++) {
                const sel = p.querySelector('select:not([disabled])');
                if (sel) {
                    console.log('[TPQ-EMIS Bridge] ✓ findStatusKepemilikanRumahSelect ditemukan via Label DOM:', sel);
                    return sel;
                }
                p = p.parentElement;
            }
        }
    }

    // 2. Prioritas Kedua: Temukan dropdown Provinsi lebih dulu, lalu ambil dropdown tepat sebelum Provinsi!
    const allSelects = Array.from(document.querySelectorAll('select:not([disabled])'));
    const selProv = findProvinsiSelect(allSelects);
    if (selProv) {
        const provIdx = allSelects.indexOf(selProv);
        if (provIdx > 0 && allSelects[provIdx - 1]) {
            console.log(`[TPQ-EMIS Bridge] ✓ findStatusKepemilikanRumahSelect ditemukan sebelum Provinsi (index ${provIdx - 1}):`, allSelects[provIdx - 1]);
            return allSelects[provIdx - 1];
        }
    }

    // 3. Prioritas Ketiga: Cari dropdown yang memiliki opsi kepemilikan rumah
    for (const sel of allSelects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0 || options.length > 20) continue;
        const texts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        if (texts.some(t => t.includes('km') || t.includes('jalan kaki') || t.includes('mukim') || (t.includes('menit') && !t.includes('rumah')))) continue;

        const hasMilikSendiri = texts.some(t => t.includes('milik sendiri') || t.includes('sendiri'));
        const hasOther = texts.some(t => t.includes('orang tua') || t.includes('sewa') || t.includes('kontrak') || t.includes('dinas') || t.includes('menumpang'));
        if (hasMilikSendiri || hasOther) {
            console.log('[TPQ-EMIS Bridge] ✓ findStatusKepemilikanRumahSelect ditemukan via opsi dropdown:', sel);
            return sel;
        }
    }

    // 4. Fallback Posisi: Dropdown pertama di formulir jika opsinya <= 15
    if (allSelects.length > 0 && allSelects[0].options.length > 0 && allSelects[0].options.length <= 15) {
        console.log('[TPQ-EMIS Bridge] findStatusKepemilikanRumahSelect fallback dropdown pertama:', allSelects[0]);
        return allSelects[0];
    }

    return null;
}

/**
 * Mengecek apakah nilai Status Kepemilikan Rumah yang terpilih sudah cocok dengan target
 */
function isStatusRumahTargetSelected(sel, targetRaw) {
    if (!sel || sel.selectedIndex < 0) return false;
    const selectedOpt = sel.options[sel.selectedIndex];
    if (!selectedOpt) return false;
    const selectedText = (selectedOpt.text || selectedOpt.label || '').toLowerCase().trim();
    const selectedVal = String(sel.value ?? '').trim().toLowerCase();

    // Abaikan jika masih placeholder ("pilih", "", "0")
    if (selectedVal === '' || selectedVal === '0' || selectedText.includes('pilih') || selectedText === '') {
        return false;
    }

    const target = (targetRaw || 'Milik Sendiri').toLowerCase().trim();
    if (target.includes('sendiri')) {
        return selectedText.includes('sendiri') || selectedVal === '9';
    }
    if (target.includes('orang tua')) {
        return selectedText.includes('orang tua');
    }
    if (target.includes('sewa') || target.includes('kontrak')) {
        return selectedText.includes('sewa') || selectedText.includes('kontrak');
    }
    if (target.includes('dinas')) {
        return selectedText.includes('dinas');
    }
    return selectedText.includes(target) || target.includes(selectedText);
}

/**
 * Memilih Status Kepemilikan Rumah (Ayah) di Tab DATA ALAMAT
 */
function selectStatusKepemilikanRumahEmis(targetRaw) {
    const target = targetRaw || 'Milik Sendiri';
    const sel = findStatusKepemilikanRumahSelect();
    if (!sel) return false;

    console.log('[TPQ-EMIS Bridge] Menyetel Status Kepemilikan Rumah:', target);
    let ok = setNativeSelectValue(sel, target);
    if (!ok || !isStatusRumahTargetSelected(sel, target)) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        const isSendiri = target.toLowerCase().includes('sendiri');
        const opt = options.find(o => {
            const txt = (o.label || o.text || '').toLowerCase();
            return isSendiri ? (txt.includes('sendiri') || o.value === '9') : txt.includes(target.toLowerCase());
        });
        if (opt) {
            ok = setNativeSelectValue(sel, opt.value || opt.text);
        } else if (options.length > 1) {
            ok = setNativeSelectValue(sel, options[1].value || options[1].text);
        }
    }
    return ok;
}

/**
 * Menunggu dan menyetel Status Kepemilikan Rumah (Ayah) sampai benar-benar terverifikasi terpilih
 * Menampilkan status real-time di UI popup (seperti cascading Provinsi)
 */
function waitAndSelectStatusRumah(targetRaw, timeoutMs = 8000) {
    return new Promise((resolve) => {
        const target = targetRaw || 'Milik Sendiri';
        console.log('[TPQ-EMIS Bridge] Menyetel Status Kepemilikan Rumah (Ayah):', target);
        updateWilayahStatusUI('status-rumah', '[⏳ Memilih...]', '#0d6efd');

        const startTime = Date.now();

        const timer = setInterval(() => {
            const sel = findStatusKepemilikanRumahSelect();
            if (sel) {
                // Log opsi yang tersedia untuk visibilitas debugging
                if (sel.options && sel.options.length > 0) {
                    console.log(`[TPQ-EMIS Bridge] Opsi Status Rumah ditemukan (${sel.options.length} opsi):`, Array.from(sel.options).map(o => `[${o.value}] ${o.text}`).join(', '));
                }

                if (isStatusRumahTargetSelected(sel, target)) {
                    clearInterval(timer);
                    console.log(`[TPQ-EMIS Bridge] ✓ Status Kepemilikan Rumah terverifikasi terpilih: "${sel.options[sel.selectedIndex]?.text || sel.value}"`);
                    updateWilayahStatusUI('status-rumah', '[✓ Terpilih]', '#198754');
                    return resolve(true);
                }

                // Coba setel nilainya
                updateWilayahStatusUI('status-rumah', '[⏳ Mengisi...]', '#0d6efd');
                setNativeSelectValue(sel, target);

                // Jika belum terpilih, coba paksa via pencarian opsi manual
                if (!isStatusRumahTargetSelected(sel, target)) {
                    const opts = Array.from(sel.options || sel.querySelectorAll('option'));
                    const isSendiri = target.toLowerCase().includes('sendiri');
                    const opt = opts.find(o => {
                        const txt = (o.text || o.label || '').toLowerCase();
                        return isSendiri ? (txt.includes('sendiri') || o.value === '9') : txt.includes(target.toLowerCase());
                    });
                    if (opt) setNativeSelectValue(sel, opt.value || opt.text);
                    else if (opts.length > 1) setNativeSelectValue(sel, opts[1].value || opts[1].text);
                }
            } else {
                updateWilayahStatusUI('status-rumah', '[⏳ Mencari field...]', '#0d6efd');
            }

            if (Date.now() - startTime >= timeoutMs) {
                clearInterval(timer);
                console.warn('[TPQ-EMIS Bridge] ⚠️ Batas waktu tunggu Status Kepemilikan Rumah tercapai.');
                updateWilayahStatusUI('status-rumah', '[⚠️ Gagal / Pilih Manual]', '#dc3545');
                resolve(false);
            }
        }, 300);
    });
}

/**
 * Mencari elemen <select> Status Mukim (Santri) di Tab DATA ALAMAT
 * Diidentifikasi dari opsi uniknya ("Tidak Mukim" dan "Mukim")
 */
function findStatusMukimSelect() {
    const allSelects = Array.from(document.querySelectorAll('select'));
    for (const sel of allSelects) {
        const options = Array.from(sel.options || sel.querySelectorAll('option'));
        if (options.length === 0 || options.length > 5) continue;
        const texts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
        const hasTidakMukim = texts.some(t => t.includes('tidak mukim'));
        const hasMukim = texts.some(t => t === 'mukim' || t.includes('mukim'));
        if (hasTidakMukim && hasMukim) {
            return sel;
        }
    }
    return null;
}

/**
 * Helper untuk memeriksa apakah Status Mukim sudah benar-benar terpilih sesuai target
 */
function isStatusMukimTargetSelected(sel, targetRaw) {
    if (!sel || !sel.options || sel.options.length === 0 || sel.selectedIndex < 0) return false;
    const opt = sel.options[sel.selectedIndex];
    const val = String(sel.value ?? '').trim();
    const txt = (opt?.text || opt?.label || '').toLowerCase().trim();
    // Catatan: di EMIS 4.0, "Tidak Mukim" bernilai value="0", jadi JANGAN tolak val === '0'!
    if (val === '' || txt === '' || txt.includes('pilih') || txt.startsWith('--')) return false;

    const target = String(targetRaw || 'Tidak Mukim').toLowerCase().trim();
    const wantTidak = target.includes('tidak');
    return wantTidak
        ? (txt.includes('tidak') && txt.includes('mukim'))
        : (!txt.includes('tidak') && txt.includes('mukim'));
}

/**
 * Menunggu dan menyetel Status Mukim (Santri) sampai benar-benar terpilih sesuai target
 */
function waitAndSelectStatusMukim(targetRaw, timeoutMs = 4000) {
    return new Promise((resolve) => {
        const target = targetRaw || 'Tidak Mukim';
        console.log('[TPQ-EMIS Bridge] Menyetel Status Mukim (Santri):', target);
        updateWilayahStatusUI('status-mukim', '[⏳ Memilih...]', '#0d6efd');

        const startTime = Date.now();
        let lastAttemptAt = 0;
        let stableCount = 0;

        const timer = setInterval(() => {
            const sel = findStatusMukimSelect();
            if (sel) {
                if (isStatusMukimTargetSelected(sel, target)) {
                    stableCount++;
                    if (stableCount >= 2) {
                        clearInterval(timer);
                        console.log(`[TPQ-EMIS Bridge] ✓ Status Mukim terpilih: "${sel.options[sel.selectedIndex]?.text || sel.value}"`);
                        updateWilayahStatusUI('status-mukim', '[✓ Terpilih]', '#198754');
                        return resolve(true);
                    }
                    return;
                }

                stableCount = 0;
                if (Date.now() - lastAttemptAt >= 250) {
                    lastAttemptAt = Date.now();
                    updateWilayahStatusUI('status-mukim', '[⏳ Mengisi...]', '#0d6efd');
                    const opts = Array.from(sel.options || sel.querySelectorAll('option'));
                    const isTidak = target.toLowerCase().includes('tidak');
                    const opt = opts.find(o => {
                        const txt = (o.label || o.text || '').toLowerCase().trim();
                        return isTidak
                            ? (txt.includes('tidak') && txt.includes('mukim'))
                            : (!txt.includes('tidak') && txt.includes('mukim'));
                    });
                    if (opt) {
                        setNativeSelectValue(sel, opt.value || opt.text);
                    } else {
                        setNativeSelectValue(sel, target);
                    }
                }
            } else {
                stableCount = 0;
                updateWilayahStatusUI('status-mukim', '[⏳ Mencari dropdown...]', '#0d6efd');
            }

            if (Date.now() - startTime >= timeoutMs) {
                clearInterval(timer);
                console.warn('[TPQ-EMIS Bridge] Batas waktu tunggu Status Mukim.');
                updateWilayahStatusUI('status-mukim', '[⚠️ Gagal / Pilih Manual]', '#dc3545');
                resolve(false);
            }
        }, 120);
    });
}

/**
 * Mencari container section SANTRI di formulir
 */
function findSantriSectionContainer() {
    const headings = Array.from(document.querySelectorAll('div, h1, h2, h3, h4, span, p'));
    for (const h of headings) {
        if (h.children.length > 2) continue;
        const txt = (h.innerText || h.textContent || '').trim().toUpperCase();
        if (txt === 'SANTRI' || txt === 'TEMPAT TINGGAL SANTRI') {
            let p = h.parentElement;
            for (let i = 0; i < 5 && p && p !== document.body; i++) {
                if (p.querySelectorAll('select').length >= 2) {
                    return p;
                }
                p = p.parentElement;
            }
        }
    }
    return null;
}

/**
 * Helper untuk memvalidasi apakah suatu elemen <select> benar-benar dropdown Status Tempat Tinggal (Santri)
 * dan BUKAN dropdown Provinsi/Wilayah, Status Rumah, Status Mukim, atau Mobilitas!
 */
function isNonTempatTinggalSelect(sel) {
    if (!sel || !sel.options || sel.options.length === 0) return true;
    if (sel.options.length > 20) return true;

    const texts = Array.from(sel.options).map(o => (o.text || o.label || '').toLowerCase().trim());

    // 1. EKSKLUSI MUTLAK: Nama-nama provinsi / wilayah
    if (texts.some(t =>
        t.includes('aceh') || t.includes('lampung') || t.includes('jawa') ||
        t.includes('bali') || t.includes('sumatera') || t.includes('kalimantan') ||
        t.includes('sulawesi') || t.includes('papua') || t.includes('banten') ||
        t.includes('bengkulu') || t.includes('riau') || t.includes('jambi') ||
        t.includes('maluku') || t.includes('gorontalo')
    )) {
        return true;
    }

    // 2. EKSKLUSI MUTLAK: Opsi kepemilikan rumah, mukim, mobilitas
    if (texts.some(t =>
        t.includes('milik sendiri') || t.includes('rumah dinas') ||
        t.includes('tidak mukim') || t === 'mukim' ||
        t.includes('jalan kaki') || t.includes('sepeda') ||
        t.includes('km') || (t.includes('menit') && !t.includes('tinggal'))
    )) {
        return true;
    }

    return false;
}

function isStatusTempatTinggalSelect(sel) {
    if (!sel || !sel.options || sel.options.length < 3) return false;
    if (isNonTempatTinggalSelect(sel)) return false;

    const texts = Array.from(sel.options).map(o => (o.text || o.label || '').toLowerCase().trim());

    // Memiliki teks khas Status Tempat Tinggal di EMIS 4.0
    const hasAyah = texts.some(t => t.includes('tinggal dengan ayah') || (t.includes('ayah') && t.includes('kandung')));
    const hasWali = texts.some(t => t.includes('tinggal dengan wali') || t.includes('wali'));
    const hasIbu = texts.some(t => t.includes('tinggal dengan ibu') || (t.includes('ibu') && t.includes('kandung')));
    const hasKostOrAsrama = texts.some(t => t.includes('kost') || t.includes('kontrak') || t.includes('asrama') || t.includes('panti') || t.includes('kerabat') || t.includes('orang tua'));

    return hasAyah || (hasWali && (hasIbu || hasKostOrAsrama));
}

/**
 * Helper untuk memetakan target teks/nilai Status Tempat Tinggal ke elemen <option> yang tepat
 * Berdasarkan opsi resmi EMIS 4.0:
 * - 18: "Tinggal dengan Ayah Kandung"
 * - 19: "Tinggal dengan Ibu Kandung"
 * - 20: "Tinggal dengan Wali"
 * - 2:  "Ikut Saudara/Kerabat"
 * - 4:  "Kontrak/Kost"
 * - 15: "Tinggal di Asrama Bukan Milik Pesantren"
 * - 6:  "Panti Asuhan"
 * - 7:  "Rumah Singgah"
 * - 8:  "Lainnya"
 */
function mapStatusTempatTinggalOption(sel, targetRaw) {
    if (!sel || !sel.options || sel.options.length === 0) return null;
    const target = String(targetRaw || 'Tinggal dengan Ayah Kandung').toLowerCase().trim();
    const opts = Array.from(sel.options);

    // Filter agar option yang dipilih bukan nama provinsi
    const isOptionValid = (o) => {
        const txt = (o.text || o.label || '').toLowerCase();
        return !txt.includes('lampung') && !txt.includes('aceh') && !txt.includes('jawa') && !txt.includes('sumatera');
    };

    let matchedOpt = null;
    if (target.includes('ayah') || target.includes('orang tua') || target === '18' || !target) {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('ayah') || (o.value === '18' && (o.text || '').toLowerCase().includes('tinggal'))));
        if (!matchedOpt) {
            matchedOpt = opts.find(o => isOptionValid(o) && (o.text || '').toLowerCase().includes('ayah'));
        }
    } else if (target.includes('ibu') || target === '19') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('ibu') || (o.value === '19' && (o.text || '').toLowerCase().includes('tinggal'))));
    } else if (target.includes('wali') || target === '20') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('wali') || (o.value === '20' && (o.text || '').toLowerCase().includes('tinggal'))));
    } else if (target.includes('saudara') || target.includes('kerabat') || target === '2') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('saudara') || (o.text || '').toLowerCase().includes('kerabat')));
    } else if (target.includes('kost') || target.includes('kontrak') || target === '4') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('kost') || (o.text || '').toLowerCase().includes('kontrak')));
    } else if (target.includes('asrama') || target === '15') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('asrama') || (o.value === '15' && (o.text || '').toLowerCase().includes('tinggal'))));
    } else if (target.includes('panti') || target === '6') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('panti') || o.value === '6'));
    } else if (target.includes('singgah') || target === '7') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('singgah') || o.value === '7'));
    } else if (target.includes('lain') || target === '8') {
        matchedOpt = opts.find(o => isOptionValid(o) && ((o.text || '').toLowerCase().includes('lain') || o.value === '8'));
    }

    if (!matchedOpt) {
        matchedOpt = opts.find(o => {
            if (!isOptionValid(o)) return false;
            const txt = (o.text || o.label || '').toLowerCase();
            return (txt && (txt.includes(target) || target.includes(txt))) && o.value !== '';
        });
    }

    if (!matchedOpt) {
        matchedOpt = opts.find(o => isOptionValid(o) && o.value !== '' && o.value !== '0' && !(o.text || '').toLowerCase().includes('pilih'));
    }

    return matchedOpt || null;
}

/**
 * Mencari elemen <select> Status Tempat Tinggal (Santri) di Tab DATA ALAMAT
 * Menggunakan validasi ketat isStatusTempatTinggalSelect agar TIDAK PERNAH salah memilih Provinsi!
 */
function findStatusTempatTinggalSelect() {
    // 1. Prioritas Utama: Label DOM bertuliskan "STATUS TEMPAT TINGGAL", "JENIS TEMPAT TINGGAL", atau "TEMPAT TINGGAL SANTRI"
    const allLabels = Array.from(document.querySelectorAll('div, label, span, p, h1, h2, h3, h4'));
    for (const el of allLabels) {
        if (el.closest && el.closest('[id^="tpq-"]')) continue;
        if (el.children.length > 2) continue;
        const txt = (el.innerText || el.textContent || '').replace(/\*/g, '').trim().toUpperCase();
        if ((txt.includes('STATUS TEMPAT TINGGAL') || txt.includes('JENIS TEMPAT TINGGAL') || (txt.includes('TEMPAT TINGGAL') && !txt.includes('JARAK') && !txt.includes('DOMISILI') && !txt.includes('KEPEMILIKAN'))) && txt.length <= 50) {
            let p = el.parentElement;
            for (let i = 0; i < 6 && p && p !== document.body; i++) {
                const selects = Array.from(p.querySelectorAll('select'));
                // Ambil hanya jika container sempit (maksimal 2 select agar tidak melompat ke seluruh form)
                if (selects.length > 0 && selects.length <= 2) {
                    const sel = selects.find(s => !isSelectDisabled(s) && !isNonTempatTinggalSelect(s) && s.options.length >= 2);
                    if (sel) return sel;
                }
                p = p.parentElement;
            }
        }
    }

    // 2. Prioritas Kedua: Cari di dalam container section SANTRI
    const santriSection = findSantriSectionContainer();
    if (santriSection) {
        const santriSelects = Array.from(santriSection.querySelectorAll('select')).filter(s => !isSelectDisabled(s));
        for (const sel of santriSelects) {
            if (isStatusTempatTinggalSelect(sel)) {
                return sel;
            }
        }
    }

    // 3. Prioritas Ketiga: Cari di semua <select> yang memenuhi kriteria isStatusTempatTinggalSelect
    const allSelects = Array.from(document.querySelectorAll('select'));
    for (let i = 0; i < allSelects.length; i++) {
        const sel = allSelects[i];
        if (!isSelectDisabled(sel) && isStatusTempatTinggalSelect(sel)) {
            return sel;
        }
    }

    return null;
}

/**
 * Mengecek apakah nilai Status Tempat Tinggal yang terpilih sudah cocok dengan target
 */
function isStatusTempatTinggalTargetSelected(sel, targetRaw) {
    if (!sel || sel.selectedIndex < 0) return false;
    const selectedOpt = sel.options ? sel.options[sel.selectedIndex] : null;
    if (!selectedOpt) return false;
    const selectedText = (selectedOpt.text || selectedOpt.label || '').toLowerCase().trim();
    const selectedVal = String(sel.value ?? '').trim().toLowerCase();

    // Mutlak tolak jika yang terpilih adalah nama provinsi!
    if (selectedText.includes('lampung') || selectedText.includes('jawa') || selectedText.includes('aceh') || selectedText.includes('sumatera') || selectedText.includes('kalimantan') || selectedText.includes('sulawesi') || selectedText.includes('bali')) {
        return false;
    }

    // Abaikan jika masih placeholder ("pilih", "", "0")
    if (selectedVal === '' || selectedVal === '0' || selectedText.includes('pilih') || selectedText === '') {
        return false;
    }

    const target = (targetRaw || 'Tinggal dengan Ayah Kandung').toLowerCase().trim();
    if (target.includes('ayah') || target.includes('orang tua')) {
        return selectedText.includes('ayah') || (selectedVal === '18' && selectedText.includes('tinggal'));
    }
    if (target.includes('ibu')) {
        return selectedText.includes('ibu') || (selectedVal === '19' && selectedText.includes('tinggal'));
    }
    if (target.includes('wali')) {
        return selectedText.includes('wali') || (selectedVal === '20' && selectedText.includes('tinggal'));
    }
    if (target.includes('saudara') || target.includes('kerabat')) {
        return selectedText.includes('saudara') || selectedText.includes('kerabat') || (selectedVal === '2' && selectedText.includes('tinggal'));
    }
    if (target.includes('kost') || target.includes('kontrak')) {
        return selectedText.includes('kost') || selectedText.includes('kontrak');
    }
    if (target.includes('asrama')) {
        return selectedText.includes('asrama');
    }
    if (target.includes('panti')) {
        return selectedText.includes('panti');
    }
    if (target.includes('singgah')) {
        return selectedText.includes('singgah');
    }
    if (target.includes('lain')) {
        return selectedText.includes('lain');
    }
    return (selectedText.includes(target) || target.includes(selectedText)) && selectedText.includes('tinggal');
}

/**
 * Memilih Status Tempat Tinggal (Santri) di Tab DATA ALAMAT
 */
function selectStatusTempatTinggalEmis(targetRaw) {
    const target = targetRaw || 'Tinggal dengan Ayah Kandung';
    const sel = findStatusTempatTinggalSelect();
    if (!sel) {
        console.warn('[TPQ-EMIS Bridge] [Status Tempat Tinggal] selectStatusTempatTinggalEmis: Dropdown tidak ditemukan');
        return false;
    }

    console.log('[TPQ-EMIS Bridge] [Status Tempat Tinggal] Menyetel Status Tempat Tinggal Santri:', target);
    const targetOpt = mapStatusTempatTinggalOption(sel, target);
    let ok = false;
    if (targetOpt) {
        ok = setNativeSelectValue(sel, targetOpt.value);
    }
    if (!ok || !isStatusTempatTinggalTargetSelected(sel, target)) {
        ok = setNativeSelectValue(sel, target);
    }
    return ok;
}

/**
 * Menunggu dan menyetel Status Tempat Tinggal (Santri) sampai benar-benar terverifikasi terpilih
 * Menampilkan status real-time di UI popup dan console log diagnostik lengkap jika gagal/berhasil
 */
function waitAndSelectStatusTempatTinggal(targetRaw, timeoutMs = 5000) {
    return new Promise((resolve) => {
        const target = targetRaw || 'Tinggal dengan Ayah Kandung';
        console.log(`%c[TPQ-EMIS Bridge] [Status Tempat Tinggal] Memulai proses pengisian. Target: "${target}"`, 'background: #2563eb; color: #fff; font-weight: bold; padding: 2px 6px; border-radius: 3px;');
        updateWilayahStatusUI('tempat-tinggal', '[⏳ Memilih...]', '#0d6efd');

        const startTime = Date.now();
        let lastLoggedSel = false;
        let lastAttemptAt = 0;
        let stableCount = 0;

        const timer = setInterval(() => {
            const sel = findStatusTempatTinggalSelect();
            if (sel) {
                if (!lastLoggedSel) {
                    lastLoggedSel = true;
                    console.log('[TPQ-EMIS Bridge] [Status Tempat Tinggal] ✓ Dropdown STATUS TEMPAT TINGGAL valid ditemukan di DOM:', sel);
                    const optList = Array.from(sel.options || []).map(o => `[val="${o.value}"] ${o.text}`).join(' | ');
                    console.log(`[TPQ-EMIS Bridge] [Status Tempat Tinggal] Daftar Opsi (${sel.options.length}):`, optList);
                }

                // Cek apakah sudah terpilih sesuai target; wajib bertahan 2x cek berturut-turut agar kebal terhadap reset re-render React
                if (isStatusTempatTinggalTargetSelected(sel, target)) {
                    stableCount++;
                    if (stableCount >= 2) {
                        clearInterval(timer);
                        const curOpt = sel.options[sel.selectedIndex];
                        console.log(`%c[TPQ-EMIS Bridge] [Status Tempat Tinggal] ✓ BERHASIL TERVERIFIKASI STABIL! [val="${sel.value}"] "${curOpt?.text}"`, 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px; border-radius: 3px;');
                        updateWilayahStatusUI('tempat-tinggal', '[✓ Terpilih]', '#198754');
                        return resolve(true);
                    }
                    return;
                }

                stableCount = 0;

                // Debounce 250ms antar percobaan set agar tidak men-spam React onChange berulang kali
                if (Date.now() - lastAttemptAt >= 250) {
                    lastAttemptAt = Date.now();
                    const targetOpt = mapStatusTempatTinggalOption(sel, target);
                    if (targetOpt) {
                        console.log(`[TPQ-EMIS Bridge] [Status Tempat Tinggal] Mencoba menyetel ke opsi: [val="${targetOpt.value}"] "${targetOpt.text}"`);
                        updateWilayahStatusUI('tempat-tinggal', '[⏳ Mengisi...]', '#0d6efd');
                        const ok = setNativeSelectValue(sel, targetOpt.value);
                        if (!ok) {
                            console.warn('[TPQ-EMIS Bridge] [Status Tempat Tinggal] setNativeSelectValue mengembalikan false, mencoba direct value setter');
                            sel.value = targetOpt.value;
                            sel.dispatchEvent(new Event('input', { bubbles: true }));
                            sel.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    } else {
                        console.warn(`[TPQ-EMIS Bridge] [Status Tempat Tinggal] Tidak menemukan opsi yang cocok dengan target "${target}"`);
                    }
                }
            } else {
                stableCount = 0;
                updateWilayahStatusUI('tempat-tinggal', '[⏳ Mencari dropdown...]', '#0d6efd');
            }

            if (Date.now() - startTime >= timeoutMs) {
                clearInterval(timer);
                const finalSel = findStatusTempatTinggalSelect();
                console.group('%c[TPQ-EMIS Bridge] [Status Tempat Tinggal] ❌ GAGAL SETELAH ' + (timeoutMs / 1000) + ' DETIK!', 'background: #dc2626; color: #fff; font-weight: bold; padding: 2px 6px; border-radius: 3px;');
                console.error('Target yang diminta:', target);
                if (!finalSel) {
                    console.error('Penyebab Gagal: Elemen <select> Status Tempat Tinggal yang valid TIDAK DITEMUKAN di DOM halaman!');
                    document.querySelectorAll('select').forEach((s, idx) => {
                        console.error(`Select #${idx} (Total ${s.options.length} opsi):`, s, 'Contoh Opsi:', Array.from(s.options).slice(0, 5).map(o => `[${o.value}] ${o.text}`).join(', '));
                    });
                } else {
                    const curOpt = finalSel.options[finalSel.selectedIndex];
                    console.error('Penyebab Gagal: Dropdown ditemukan tetapi nilai belum cocok atau gagal disimpan ke React state.');
                    console.error('Nilai saat ini:', finalSel.value, `(Teks: "${curOpt?.text}")`);
                    console.error('Status Disabled:', finalSel.disabled, 'aria-disabled:', finalSel.getAttribute('aria-disabled'));
                    console.error('Daftar opsi tersedia:', Array.from(finalSel.options).map(o => `[${o.value}] ${o.text}`));
                }
                console.groupEnd();

                updateWilayahStatusUI('tempat-tinggal', '[⚠️ Gagal / Pilih Manual]', '#dc3545');
                resolve(false);
            }
        }, 120);
    });
}

/**
 * Menunggu dan menyetel Mobilitas Santri (Jarak, Transportasi, Waktu Tempuh, Koordinat) sampai stabil
 */
function waitAndSelectMobilitas(santriData, timeoutMs = 6000) {
    return new Promise((resolve) => {
        console.log('[TPQ-EMIS Bridge] Menyetel Mobilitas Santri (Jarak, Transportasi, Waktu, Koordinat)...');
        updateWilayahStatusUI('jarak', '[⏳ Memilih...]', '#0d6efd');
        updateWilayahStatusUI('transportasi', '[⏳ Memilih...]', '#0d6efd');
        updateWilayahStatusUI('waktu', '[⏳ Memilih...]', '#0d6efd');
        if (santriData.santri_titik_koordinat) {
            updateWilayahStatusUI('koordinat', '[⏳ Mengisi...]', '#0d6efd');
        }

        const startTime = Date.now();
        let consecutiveSuccess = 0;

        const timer = setInterval(() => {
            const isJarakOk = checkIsJarakSelected();
            const isTransportOk = checkIsTransportasiSelected();
            const isWaktuOk = checkIsWaktuTempuhSelected();
            const isKoordinatOk = checkIsKoordinatFilled(santriData.santri_titik_koordinat);

            if (!isJarakOk) {
                selectJarakTempatTinggalEmis(santriData.santri_jarak_tempuh || santriData.santri_jarak);
            } else {
                updateWilayahStatusUI('jarak', '[✓ Terpilih]', '#198754');
            }
            if (!isTransportOk) {
                selectTransportasiEmis(santriData.santri_transportasi);
            } else {
                updateWilayahStatusUI('transportasi', '[✓ Terpilih]', '#198754');
            }
            if (!isWaktuOk) {
                selectWaktuTempuhEmis(santriData.santri_waktu_tempuh);
            } else {
                updateWilayahStatusUI('waktu', '[✓ Terpilih]', '#198754');
            }
            if (santriData.santri_titik_koordinat) {
                if (!isKoordinatOk) {
                    fillTitikKoordinatEmis(santriData.santri_titik_koordinat);
                } else {
                    updateWilayahStatusUI('koordinat', '[✓ Terisi]', '#198754');
                }
            }

            if (isJarakOk && isTransportOk && isWaktuOk && isKoordinatOk) {
                consecutiveSuccess++;
                if (consecutiveSuccess >= 2) {
                    clearInterval(timer);
                    console.log('[TPQ-EMIS Bridge] ✓ Mobilitas Santri terkonfirmasi stabil!');
                    return resolve(true);
                }
            } else {
                consecutiveSuccess = 0;
            }

            if (Date.now() - startTime >= timeoutMs) {
                clearInterval(timer);
                console.warn('[TPQ-EMIS Bridge] Batas waktu tunggu Mobilitas Santri.');
                if (!isJarakOk) updateWilayahStatusUI('jarak', '[⚠️ Manual]', '#dc3545');
                if (!isTransportOk) updateWilayahStatusUI('transportasi', '[⚠️ Manual]', '#dc3545');
                if (!isWaktuOk) updateWilayahStatusUI('waktu', '[⚠️ Manual]', '#dc3545');
                if (santriData.santri_titik_koordinat && !isKoordinatOk) {
                    updateWilayahStatusUI('koordinat', '[⚠️ Manual]', '#dc3545');
                }
                resolve(false);
            }
        }, 250);
    });
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
        const aria = (inp.getAttribute('aria-label') || '').toUpperCase();

        let labelText = '';
        let parent = inp.parentElement;
        for (let i = 0; i < 4 && parent; i++) {
            labelText += ' ' + (parent.innerText || parent.textContent || '').trim().toUpperCase();
            parent = parent.parentElement;
        }

        const combined = `${ph} ${nm} ${id} ${aria} ${labelText}`;
        if (combined.includes('NIK') && !combined.includes('NISN') && !combined.includes('TEMPAT') && !combined.includes('TANGGAL')) {
            if ((inp.value || '').trim() !== String(nik).trim()) {
                console.log('[TPQ-EMIS Bridge] Otomatis mengisi NIK Step 1:', nik);
                setNativeValue(inp, nik);
            }
            return true;
        } else if (inputs.length === 1 && !combined.includes('TEMPAT') && !combined.includes('TANGGAL')) {
            if ((inp.value || '').trim() !== String(nik).trim()) {
                console.log('[TPQ-EMIS Bridge] Otomatis mengisi NIK Step 1 (single input):', nik);
                setNativeValue(inp, nik);
            }
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

    // Aktifkan pengawas otomatis tab/radio Step 1
    setupStep1AutoDetection(data);

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
        right: 25px;
        z-index: 999999;
        width: 350px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
        transition: width 0.25s ease;
    `;

    div.innerHTML = `
        <div id="tpq-step0-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; font-weight: bold; font-size: 13px; display: flex; justify-content: space-between; align-items: center; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; gap: 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 195px;" title="${escapeHtml(data.nama_santri)} - ${escapeHtml(tglLahirLabel)}">
                <span style="font-size: 12px;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
                <span style="font-size: 9.5px; background: rgba(255,255,255,0.25); padding: 1px 4px; border-radius: 3px; font-weight: normal;">v1.6.1</span>
                <span style="font-size: 10.5px; background: rgba(255,255,255,0.25); border-radius: 3px; padding: 1px 5px; font-weight: 500;" title="Tanggal Lahir Santri">📅 ${escapeHtml(tglLahirLabel)}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <button id="tpq-btn-quick-step1" style="background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4); border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.15);" title="Mulai Isi Step 1 (Nama & TTL)">
                    ⚡ Step 1
                </button>
                <button id="tpq-toggle-step0" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.45); color: #fff; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer;" title="Buka panel panduan">
                    ▼ Buka
                </button>
                <span id="tpq-close-step0-alert" style="cursor: pointer; font-size: 17px; line-height: 1; padding: 1px 4px; opacity: 0.85;" title="Tutup">&times;</span>
            </div>
        </div>

        <div id="tpq-step0-body" style="display: none; padding: 12px 14px; max-height: 75vh; overflow-y: auto;">
            <!-- Info Singkat Santri -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <div style="font-weight: bold; font-size: 14px; color: #0d6efd;">${escapeHtml(data.nama_santri)}</div>
                    <button class="tpq-copy-btn" data-val="${escapeHtml(data.nama_santri)}" style="border: 1px solid #ced4da; background: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; cursor: pointer; color: #495057;" title="Salin Nama">📋 Salin</button>
                </div>
                <div style="font-size: 12px; color: #64748b;">
                    Kelas: <strong>${escapeHtml(data.tingkat_kelas || 'Santri Baru')}</strong>
                </div>
            </div>

            <!-- Opsi 1: Step 0 (Cari via NIK) -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <strong style="color: #1d4ed8; font-size: 12px;">🔍 Opsi 1: Cari via NIK (Step 0)</strong>
                    <span style="font-size: 10px; background: #dbeafe; color: #1e40af; padding: 1px 6px; border-radius: 10px; font-weight: bold;">Auto-Fill</span>
                </div>
                <div style="font-size: 11.5px; color: #475569; margin-bottom: 6px;">
                    ${data.belum_punya_nik ? '⚠️ Santri belum punya NIK (otomatis beralih ke Opsi 2)' : 'NIK otomatis diisikan ke kolom pencarian NIK di EMIS:'}
                </div>
                ${data.belum_punya_nik ? `
                    <div style="font-size: 11.5px; color: #b45309; font-style: italic; background: #fff; border: 1px solid #fde68a; padding: 5px 8px; border-radius: 4px;">
                        ⚠️ Santri belum punya NIK (Silakan gunakan Opsi 2)
                    </div>
                ` : `
                    <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid #bfdbfe; padding: 6px 10px; border-radius: 4px;">
                        <span style="font-family: monospace; font-size: 13px; font-weight: bold; color: #1e293b;">${escapeHtml(data.nik || '-')}</span>
                        <button id="tpq-btn-copy-nik-step0" class="tpq-copy-btn" data-val="${escapeHtml(data.nik || '')}" style="border: 1px solid #bfdbfe; background: #eff6ff; padding: 2px 8px; border-radius: 4px; font-size: 11px; cursor: pointer; color: #1d4ed8; font-weight: bold;" title="Salin NIK">📋 Salin NIK</button>
                    </div>
                `}
            </div>

            <!-- Opsi 2: Step 1 (Cari via Nama & TTL) -->
            <div id="tpq-opsi2-box" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 10px 12px; margin-bottom: 12px;">
                <div style="margin-bottom: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #166534; font-size: 12px;">📝 Opsi 2: Cari via Nama & TTL (Step 1)</strong>
                    <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 10px; font-weight: bold;">Auto-Detect</span>
                </div>
                <div style="font-size: 11.5px; color: #475569; margin-bottom: 6px; line-height: 1.4;">
                    Pilih radio <em>"Nama, Tempat, dan tanggal lahir"</em> di EMIS. Formulir akan <strong>otomatis terisi</strong> tanpa perlu menekan tombol lagi:
                </div>
                <div style="font-size: 11.5px; background: #fff; border: 1px solid #bbf7d0; padding: 6px 8px; border-radius: 4px; margin-bottom: 8px; line-height: 1.5;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                        <span>Tempat: <strong>${escapeHtml(data.tempat_lahir || '-')}</strong></span>
                        <button class="tpq-copy-btn" data-val="${escapeHtml(data.tempat_lahir || '')}" style="border: 1px solid #ced4da; background: #f8fafc; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Tgl Lahir: <strong>${escapeHtml(tglLahirLabel)}</strong></span>
                        <button class="tpq-copy-btn" data-val="${escapeHtml(data.tanggal_lahir || '')}" style="border: 1px solid #ced4da; background: #f8fafc; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>
                    </div>
                </div>
                <button id="tpq-btn-start-step1" style="
                    width: 100%; background: #198754; color: #ffffff; border: none;
                    padding: 8px 12px; border-radius: 5px; font-size: 12px; font-weight: bold;
                    cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.15);
                    display: flex; align-items: center; justify-content: center; gap: 6px;
                ">
                    <span>⚡ Mulai Isi Step 1 (Manual Trigger)</span>
                </button>
            </div>

            <!-- Footer Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 6px;">
                ${!isPpdbUrl ? `
                    <button id="tpq-btn-nav-ppdb" style="
                        background: #f8fafc; color: #0d6efd; border: 1px solid #0d6efd; padding: 6px 10px;
                        border-radius: 5px; font-size: 11px; font-weight: bold; cursor: pointer;
                    ">🔗 Buka PPDB Baru</button>
                ` : '<span></span>'}
                <div style="display: flex; gap: 6px;">
                    <button id="tpq-btn-cancel-antrean" style="
                        background: transparent; color: #dc3545; border: 1px solid #fecaca;
                        padding: 6px 10px; border-radius: 5px; font-size: 11px; cursor: pointer;
                    ">Batal</button>
                    <button id="tpq-btn-close-step0-body" style="
                        background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1;
                        padding: 6px 12px; border-radius: 5px; font-size: 11px; cursor: pointer;
                    ">Tutup</button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(div);

    // Draggable
    const headerEl = document.getElementById('tpq-step0-header');
    makeDraggable(div, headerEl);

    // Toggle Collapse / Expand Step 0
    let isStep0Expanded = false;
    const btnToggleStep0 = document.getElementById('tpq-toggle-step0');
    const bodyStep0 = document.getElementById('tpq-step0-body');
    function toggleStep0(forceState) {
        isStep0Expanded = typeof forceState === 'boolean' ? forceState : !isStep0Expanded;
        if (isStep0Expanded) {
            div.style.width = '390px';
            bodyStep0.style.display = 'block';
            btnToggleStep0.innerHTML = '▲ Tutup';
            btnToggleStep0.style.background = 'rgba(255,255,255,0.35)';
        } else {
            div.style.width = '350px';
            bodyStep0.style.display = 'none';
            btnToggleStep0.innerHTML = '▼ Buka';
            btnToggleStep0.style.background = 'rgba(255,255,255,0.25)';
        }
    }
    if (btnToggleStep0 && bodyStep0) {
        btnToggleStep0.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleStep0();
        });
    }

    // Pasang listener copy ke semua tombol .tpq-copy-btn di dalam Step 0
    div.querySelectorAll('.tpq-copy-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                if (btn.id === 'tpq-btn-copy-nik-step0') {
                    tryFillNikInput(val);
                }
                const oldContent = btn.innerHTML;
                btn.innerHTML = '✓';
                btn.style.color = '#198754';
                btn.style.borderColor = '#198754';
                setTimeout(() => {
                    btn.innerHTML = oldContent;
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 1200);
            }
        });
    });

    // Listener Tombol Mulai Isi Step 1
    const handleTriggerStep1 = () => {
        const pill = document.getElementById('tpq-reopen-step0-pill');
        if (pill) pill.remove();
        div.remove();
        fillEmisStep1(data);
    };

    const btnStart1 = document.getElementById('tpq-btn-start-step1');
    if (btnStart1) btnStart1.addEventListener('click', handleTriggerStep1);

    const btnQuickStep1 = document.getElementById('tpq-btn-quick-step1');
    if (btnQuickStep1) btnQuickStep1.addEventListener('click', handleTriggerStep1);

    // Listener Buka PPDB jika di luar halaman
    const btnNav = document.getElementById('tpq-btn-nav-ppdb');
    if (btnNav) {
        btnNav.addEventListener('click', () => {
            window.location.href = "https://emis.kemenag.go.id/search-data-ppdb/baru";
        });
    }

    // Tutup popup -> Munculkan mini pill mengambang agar mudah dibuka kembali tanpa refresh
    const handleCloseStep0 = () => {
        div.remove();
        showStep0MiniPill(data);
    };
    document.getElementById('tpq-close-step0-alert').addEventListener('click', handleCloseStep0);
    const btnCloseBody = document.getElementById('tpq-btn-close-step0-body');
    if (btnCloseBody) btnCloseBody.addEventListener('click', handleCloseStep0);

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
        top: 20px;
        right: 25px;
        z-index: 999999;
        width: 350px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
        transition: width 0.25s ease;
    `;

    // Format Tampilan Tanggal Lahir (YYYY-NamaBulan-DD agar mudah di kalender picker EMIS)
    const tglLahirLabel = data.tanggal_lahir_label || formatTanggalNamaBulan(data.tanggal_lahir);

    div.innerHTML = `
        <div id="tpq-step1-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; font-weight: bold; font-size: 13px; display: flex; justify-content: space-between; align-items: center; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; gap: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;" title="${escapeHtml(data.nama_santri)}">
                <span style="font-size: 12px;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
                <span style="font-size: 9.5px; background: rgba(255,255,255,0.25); padding: 1px 4px; border-radius: 3px; font-weight: normal; margin-left: 2px;">v1.6.1</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <button id="tpq-btn-quick-copy-tgl-step1" style="background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4); border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.15);" title="Salin Tanggal Lahir">
                    📅 Salin Tgl
                </button>
                <button id="tpq-toggle-step1-body" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.45); color: #fff; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer;" title="Buka detail data">
                    ▼ Detail
                </button>
                <span id="tpq-close-step1-alert" style="cursor: pointer; font-size: 17px; line-height: 1; padding: 1px 4px; opacity: 0.85;" title="Tutup">&times;</span>
            </div>
        </div>

        <!-- Bagian Langsung Muncul: Tanggal Lahir yang Harus Dipilih di Kalender EMIS (Tanpa Perlu Buka Manual) -->
        <div style="padding: 10px 12px; background: #ffffff;">
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 10px; font-size: 12px;">
                <div style="font-size: 11px; font-weight: bold; color: #166534; margin-bottom: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <span>📅 TANGGAL LAHIR (PILIH DI EMIS):</span>
                    <span id="tpq-step1-tgl-status" style="font-size: 9.5px; background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 3px; font-weight: bold;">⚡ Otomatis</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 3px;">
                    <span style="font-size: 15px; font-weight: bold; color: #15803d;">📅 ${escapeHtml(tglLahirLabel)}</span>
                    <div style="display: flex; gap: 4px;">
                        <button id="tpq-btn-retry-pick-tgl" style="
                            background: #0d6efd; color: #fff; border: none; padding: 3px 8px;
                            border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;
                        " title="Pilihkan otomatis di kalender EMIS">⚡ Pilihkan</button>
                        <button id="tpq-btn-copy-tgl-step1" class="tpq-copy-btn" data-val="${escapeHtml(data.tanggal_lahir || '')}" style="
                            background: #198754; color: #fff; border: none; padding: 3px 8px;
                            border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;
                        ">📋 Salin</button>
                    </div>
                </div>
                <div id="tpq-step1-tgl-desc" style="font-size: 11px; color: #64748b; margin-top: 5px; line-height: 1.35;">
                    ⚡ Memilih tanggal lahir otomatis pada kalender EMIS...
                </div>
            </div>
        </div>

        <!-- Bagian Tersembunyi (Detail Santri): Dibuka via Tombol ▼ Detail -->
        <div id="tpq-step1-body" style="display: none; padding: 0 12px 10px; max-height: 50vh; overflow-y: auto;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; font-size: 12px;">
                <div style="font-size: 11px; font-weight: bold; color: #0d6efd; margin-bottom: 4px;">Detail Santri:</div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                    <span style="color: #64748b;">Nama:</span>
                    <strong>${escapeHtml(data.nama_santri)}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                    <span style="color: #64748b;">Tempat Lahir:</span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong>${escapeHtml(data.tempat_lahir || '-')}</strong>
                        ${data.tempat_lahir ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.tempat_lahir)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>` : ''}
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: #64748b;">NIK:</span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong>${escapeHtml(data.nik || '(Belum Ada)')}</strong>
                        ${data.nik ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.nik)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>` : ''}
                    </div>
                </div>
                <button id="tpq-btn-close-step1-body" style="
                    width: 100%; background: #fff; color: #64748b; border: 1px solid #cbd5e1;
                    padding: 5px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;
                ">Tutup Detail</button>
            </div>
        </div>
    `;

    document.body.appendChild(div);

    // Draggable
    const headerEl = document.getElementById('tpq-step1-header');
    makeDraggable(div, headerEl);

    // Toggle Collapse / Expand Step 1 Detail
    let isStep1Expanded = false;
    const btnToggleStep1 = document.getElementById('tpq-toggle-step1-body');
    const bodyStep1 = document.getElementById('tpq-step1-body');
    function toggleStep1(forceState) {
        isStep1Expanded = typeof forceState === 'boolean' ? forceState : !isStep1Expanded;
        if (isStep1Expanded) {
            div.style.width = '390px';
            bodyStep1.style.display = 'block';
            btnToggleStep1.innerHTML = '▲ Tutup';
            btnToggleStep1.style.background = 'rgba(255,255,255,0.35)';
        } else {
            div.style.width = '350px';
            bodyStep1.style.display = 'none';
            btnToggleStep1.innerHTML = '▼ Detail';
            btnToggleStep1.style.background = 'rgba(255,255,255,0.25)';
        }
    }
    if (btnToggleStep1 && bodyStep1) {
        btnToggleStep1.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleStep1();
        });
    }

    // Pasang listener copy ke semua tombol .tpq-copy-btn di dalam Step 1
    div.querySelectorAll('.tpq-copy-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const oldContent = btn.innerHTML;
                btn.innerHTML = '✓';
                btn.style.color = '#198754';
                btn.style.borderColor = '#198754';
                setTimeout(() => {
                    btn.innerHTML = oldContent;
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 1200);
            }
        });
    });

    // Quick copy button in header
    const btnQuickTgl = document.getElementById('tpq-btn-quick-copy-tgl-step1');
    if (btnQuickTgl && data.tanggal_lahir) {
        btnQuickTgl.addEventListener('click', (e) => {
            e.stopPropagation();
            navigator.clipboard.writeText(data.tanggal_lahir);
            const oldText = btnQuickTgl.innerHTML;
            btnQuickTgl.innerHTML = '✓ Tersalin!';
            setTimeout(() => { btnQuickTgl.innerHTML = oldText; }, 1500);
        });
    }

    // Tombol Pilihkan Kalender Ulang
    const btnRetryPick = div.querySelector('#tpq-btn-retry-pick-tgl');
    if (btnRetryPick && data.tanggal_lahir) {
        btnRetryPick.addEventListener('click', (e) => {
            e.stopPropagation();
            btnRetryPick.disabled = true;
            btnRetryPick.innerText = '⏳ Memproses...';
            autoPickTanggalLahirStep1(data.tanggal_lahir, (success) => {
                btnRetryPick.disabled = false;
                btnRetryPick.innerText = success ? '✓ Selesai' : '⚡ Pilihkan';
                if (success) {
                    onStep1CalendarPickedSuccess();
                }
            });
        });
    }

    // Close handlers
    document.getElementById('tpq-close-step1-alert').addEventListener('click', () => div.remove());
    const btnCloseBody = document.getElementById('tpq-btn-close-step1-body');
    if (btnCloseBody) btnCloseBody.addEventListener('click', () => div.remove());

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
        top: 20px;
        right: 25px;
        z-index: 999999;
        width: 350px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
        transition: width 0.25s ease;
    `;

    // Format Tampilan Tanggal Masuk & Tanggal Lahir (YYYY-NamaBulan-DD)
    const tglMasukLabel = data.tanggal_masuk_label || formatTanggalNamaBulan(data.tanggal_masuk);
    const formattedHp = formatEmisPhoneNumber(data.no_hp);

    div.innerHTML = `
        <div id="tpq-step2-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; font-weight: bold; font-size: 13px; display: flex; justify-content: space-between; align-items: center; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; gap: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 160px;" title="${escapeHtml(data.nama_santri)}">
                <span style="font-size: 12px;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
                <span style="font-size: 9.5px; background: rgba(255,255,255,0.25); padding: 1px 4px; border-radius: 3px; font-weight: normal; margin-left: 2px;">v1.6.1</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <button id="tpq-btn-quick-selesai-step2" style="background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4); border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.15);" title="Selesaikan pendaftaran & tandai selesai">
                    ✓ Selesai
                </button>
                <button id="tpq-toggle-step2-asst" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.45); color: #fff; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer;" title="Buka detail data">
                    ▼ Detail
                </button>
                <span id="tpq-close-step2-alert-top" style="cursor: pointer; font-size: 17px; line-height: 1; padding: 1px 4px; opacity: 0.85;" title="Tutup">&times;</span>
            </div>
        </div>

        <!-- Bagian Langsung Muncul: Tugas Manual yang Harus Dipilih di EMIS (Tanpa Perlu Buka Manual) -->
        <div style="padding: 10px 12px; background: #ffffff;">
            <!-- Manual Checklist Card -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 10px; font-size: 12px; margin-bottom: 8px;">
                <div style="font-weight: bold; color: #1d4ed8; font-size: 11px; margin-bottom: 5px; text-transform: uppercase; display: flex; justify-content: space-between; align-items: center;">
                    <span>👉 TGL MASUK & STATUS ORTU:</span>
                    <span id="tpq-step2-tgl-masuk-status" style="font-size: 9.5px; background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 3px; font-weight: bold;">⚡ Otomatis</span>
                </div>

                <!-- 1. Tanggal Masuk -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; border-bottom: 1px dashed #bfdbfe; padding-bottom: 4px;">
                    <span style="color: #334155;">1. <strong>Tgl Masuk:</strong></span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong style="color: #1e40af; font-size: 13px;">📅 ${escapeHtml(tglMasukLabel)}</strong>
                        <button id="tpq-btn-retry-pick-tgl-masuk" style="background: #0d6efd; color: #fff; border: none; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; cursor: pointer;" title="Pilihkan otomatis di kalender EMIS">⚡ Pilihkan</button>
                        <button class="tpq-copy-btn" data-val="${escapeHtml(data.tanggal_masuk || '')}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Tgl Masuk">📋</button>
                    </div>
                </div>

                <!-- 2. Status Ortu & Wali -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #334155;">2. <strong>Status Ortu:</strong></span>
                    <span style="color: #475569; font-weight: 500;">Masih Hidup | Wali: Ayah</span>
                </div>
            </div>

            <!-- Action Buttons (Langsung Dapat Diakses) -->
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <button id="tpq-btn-lanjut-update-alamat" style="
                    background: #0d6efd; color: #ffffff; border: none; padding: 7px 10px;
                    border-radius: 5px; font-size: 12px; font-weight: bold; cursor: pointer; width: 100%;
                ">
                    🚀 Simpan & Lanjut Update Alamat di Kesiswaan
                </button>
                <div style="display: flex; gap: 6px;">
                    <button id="tpq-btn-tandai-selesai-step2" style="
                        background: #198754; color: #ffffff; border: none; padding: 5px 8px;
                        border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer; flex: 1;
                    ">✓ Selesai Saja</button>
                    <button id="tpq-close-step2-alert" style="
                        background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1;
                        padding: 5px 8px; border-radius: 4px; font-size: 11px; cursor: pointer; flex: 0.6;
                    ">Tutup</button>
                </div>
            </div>
        </div>

        <!-- Bagian Tersembunyi (Detail Data Terisi Otomatis): Dibuka via Tombol ▼ Detail -->
        <div id="tpq-step2-body" style="display: none; padding: 0 12px 10px; max-height: 50vh; overflow-y: auto;">
            <!-- Info Singkat Santri -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; font-size: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                    <div style="font-weight: bold; font-size: 13px; color: #0d6efd;">${escapeHtml(data.nama_santri)}</div>
                    <button class="tpq-copy-btn" data-val="${escapeHtml(data.nama_santri)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #495057;" title="Salin Nama">📋 Salin</button>
                </div>
                <div style="font-size: 11.5px; color: #64748b;">
                    Kelas: <strong>${escapeHtml(normalizedKelas || data.tingkat_kelas || '-')}</strong>
                </div>
            </div>

            <!-- Auto-filled Data Card -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 10px; font-size: 12px;">
                <div style="font-size: 10.5px; font-weight: bold; color: #166534; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">
                    <span>✨ DATA TERISI OTOMATIS:</span>
                    <span style="font-size: 9.5px; background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 8px; font-weight: bold;">Auto-Filled ✓</span>
                </div>

                <!-- NIK -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                    <span>NIK:</span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong>${escapeHtml(data.nik || '(Belum Ada)')}</strong>
                        ${data.nik ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.nik)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>` : ''}
                    </div>
                </div>

                <!-- NISN -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                    <span>NISN:</span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong>${escapeHtml(data.nisn || '(Belum Ada)')}</strong>
                        ${data.nisn ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.nisn)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>` : ''}
                    </div>
                </div>

                <!-- No HP -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                    <span>No HP:</span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong>${escapeHtml(formattedHp || '(Tidak Ada)')}</strong>
                        ${formattedHp ? `<button class="tpq-copy-btn" data-val="${escapeHtml(formattedHp)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>` : ''}
                    </div>
                </div>

                <!-- Ayah -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                    <span>Ayah:</span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong>${escapeHtml(data.nama_ayah || '-')}</strong>
                        ${data.nama_ayah ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.nama_ayah)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>` : ''}
                    </div>
                </div>

                <!-- Ibu -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                    <span>Ibu:</span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong>${escapeHtml(data.nama_ibu || '-')}</strong>
                        ${data.nama_ibu ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.nama_ibu)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋</button>` : ''}
                    </div>
                </div>

                <!-- JK & Kelas -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>JK / Kelas:</span>
                    <strong>${escapeHtml(data.jenis_kelamin || '-')} | ${escapeHtml(normalizedKelas || data.tingkat_kelas || '-')}</strong>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(div);

    // Draggable
    const headerEl = document.getElementById('tpq-step2-header');
    makeDraggable(div, headerEl);

    // Toggle Collapse / Expand Step 2 Detail
    let isStep2AsstExpanded = false;
    const btnToggleStep2Asst = document.getElementById('tpq-toggle-step2-asst');
    const bodyStep2Asst = document.getElementById('tpq-step2-body');
    function toggleStep2Asst(forceState) {
        isStep2AsstExpanded = typeof forceState === 'boolean' ? forceState : !isStep2AsstExpanded;
        if (isStep2AsstExpanded) {
            div.style.width = '390px';
            bodyStep2Asst.style.display = 'block';
            btnToggleStep2Asst.innerHTML = '▲ Tutup';
            btnToggleStep2Asst.style.background = 'rgba(255,255,255,0.35)';
        } else {
            div.style.width = '350px';
            bodyStep2Asst.style.display = 'none';
            btnToggleStep2Asst.innerHTML = '▼ Detail';
            btnToggleStep2Asst.style.background = 'rgba(255,255,255,0.25)';
        }
    }
    if (btnToggleStep2Asst && bodyStep2Asst) {
        btnToggleStep2Asst.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleStep2Asst();
        });
    }
    if (btnToggleStep2Asst && bodyStep2Asst) {
        btnToggleStep2Asst.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleStep2Asst();
        });
    }

    // Otomatis pilih Tanggal Masuk di Kalender EMIS Step 2 + tombol Pilihkan ulang
    const updateTglMasukBadge = (text, bg, color) => {
        const badge = document.getElementById('tpq-step2-tgl-masuk-status');
        if (badge) {
            badge.innerText = text;
            badge.style.background = bg;
            badge.style.color = color;
        }
    };

    const runPickTglMasuk = () => {
        if (!data.tanggal_masuk) return;
        const btnPick = document.getElementById('tpq-btn-retry-pick-tgl-masuk');
        if (btnPick) {
            btnPick.disabled = true;
            btnPick.innerText = '⏳...';
        }
        updateTglMasukBadge('⚡ Memilih...', '#e0f2fe', '#0369a1');
        autoPickTanggalMasukStep2(data.tanggal_masuk, (success) => {
            if (btnPick) {
                btnPick.disabled = false;
                btnPick.innerText = success ? '✓ Selesai' : '⚡ Pilihkan';
            }
            if (success) {
                updateTglMasukBadge('✓ Tgl Masuk OK', '#dcfce7', '#15803d');
                setTimeout(() => {
                    triggerAutoSimpanPendaftaranStep2((statusText, type) => {
                        if (type === 'success') {
                            updateTglMasukBadge(statusText, '#dcfce7', '#15803d');
                        } else if (type === 'warn') {
                            updateTglMasukBadge(statusText, '#fef3c7', '#b45309');
                        } else {
                            updateTglMasukBadge(statusText, '#e0f2fe', '#0369a1');
                        }
                    }, (savedOk) => {
                        if (savedOk) {
                            console.log('%c[TPQ-EMIS Bridge] [Step 2] ✓ Simpan berhasil! Melanjutkan otomatis ke Update Alamat Kesiswaan...', 'background: #0d6efd; color: #fff; font-weight: bold; padding: 2px 6px;');
                            setTimeout(() => {
                                lanjutUpdateAlamatKesiswaan();
                            }, 500);
                        }
                    });
                }, 400);
            } else {
                updateTglMasukBadge('Manual', '#fef3c7', '#b45309');
            }
        });
    };

    const btnRetryPickTglMasuk = document.getElementById('tpq-btn-retry-pick-tgl-masuk');
    if (btnRetryPickTglMasuk) {
        btnRetryPickTglMasuk.addEventListener('click', (e) => {
            e.stopPropagation();
            runPickTglMasuk();
        });
    }

    if (data.tanggal_masuk && !window.__tpqStep2TglMasukAutoStarted) {
        window.__tpqStep2TglMasukAutoStarted = true;
        setTimeout(runPickTglMasuk, 300);
    }

    // Pasang listener copy ke semua tombol .tpq-copy-btn di dalam Step 2
    div.querySelectorAll('.tpq-copy-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const oldContent = btn.innerHTML;
                btn.innerHTML = '✓';
                btn.style.color = '#198754';
                btn.style.borderColor = '#198754';
                setTimeout(() => {
                    btn.innerHTML = oldContent;
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 1200);
            }
        });
    });

    const handleCloseStep2 = () => {
        chrome.storage.local.remove(['pendingSantriData', 'waitingForStep2']);
        div.remove();
        showTemporaryFloatingToast('Antrean pendaftaran santri ditutup.', 'danger');
    };

    const closeTop = document.getElementById('tpq-close-step2-alert-top');
    if (closeTop) closeTop.addEventListener('click', handleCloseStep2);

    const btnCloseBottom = document.getElementById('tpq-close-step2-alert');
    if (btnCloseBottom) btnCloseBottom.addEventListener('click', handleCloseStep2);

    // Helper & Tombol Lanjut Update Alamat (Satu Paket Pendaftaran + Update Alamat)
    const btnLanjutUpdate = document.getElementById('tpq-btn-lanjut-update-alamat');
    let isRedirectingKesiswaan = false;
    function lanjutUpdateAlamatKesiswaan() {
        if (isRedirectingKesiswaan) return;
        isRedirectingKesiswaan = true;

        if (data.id_santri) {
            try {
                chrome.runtime.sendMessage({
                    action: 'FORM_FILLED_NOTIFY',
                    id_santri: data.id_santri
                });
            } catch (_) {}
        }

        chrome.storage.local.set({
            pendingUpdateAlamatData: data,
            currentTask: 'UPDATE_ALAMAT',
            waitingForUpdateStep2: false
        }, () => {
            chrome.storage.local.remove(['pendingSantriData', 'waitingForStep2']);
            if (btnLanjutUpdate) {
                btnLanjutUpdate.innerHTML = '🔄 Mengalihkan ke Kesiswaan...';
                btnLanjutUpdate.style.background = '#0b5ed7';
                btnLanjutUpdate.disabled = true;
            }

            showTemporaryFloatingToast('Pendaftaran selesai! Mengalihkan ke menu Kesiswaan untuk update alamat...', 'success');

            setTimeout(() => {
                div.remove();
                window.location.href = "https://emis.kemenag.go.id/kesiswaan";
            }, 600);
        });
    }

    if (btnLanjutUpdate && data.id_santri) {
        btnLanjutUpdate.addEventListener('click', lanjutUpdateAlamatKesiswaan);
    }

    // Handler tandai selesai saja (dihubungkan ke tombol bottom & tombol quick di header)
    const handleTandaiSelesai = () => {
        if (!data.id_santri) {
            div.remove();
            return;
        }
        try {
            chrome.runtime.sendMessage({
                action: 'FORM_FILLED_NOTIFY',
                id_santri: data.id_santri
            });
            const btnSelesai = document.getElementById('tpq-btn-tandai-selesai-step2');
            if (btnSelesai) {
                btnSelesai.innerText = '✅ Ditandai Selesai!';
                btnSelesai.style.background = '#059669';
                btnSelesai.disabled = true;
            }
            const btnQuick = document.getElementById('tpq-btn-quick-selesai-step2');
            if (btnQuick) {
                btnQuick.innerText = '✓ Selesai!';
                btnQuick.disabled = true;
            }
            showTemporaryFloatingToast('Pendaftaran berhasil ditandai selesai!', 'success');
            setTimeout(() => { div.remove(); }, 1200);
        } catch (_) {
            div.remove();
        }
    };

    const btnSelesai = document.getElementById('tpq-btn-tandai-selesai-step2');
    if (btnSelesai) btnSelesai.addEventListener('click', handleTandaiSelesai);

    const btnQuickSelesai = document.getElementById('tpq-btn-quick-selesai-step2');
    if (btnQuickSelesai) btnQuickSelesai.addEventListener('click', handleTandaiSelesai);
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

// ==============================================================
// PENGATURAN MODE AUTOMASI (Full Otomatis vs Semi-Otomatis)
// ==============================================================
let currentAutoMode = 'FULL'; // Default: FULL (Hands-Free), pilihan lain: SEMI (Manual Klik)

chrome.storage.local.get(['emisAutoMode'], (res) => {
    if (res && res.emisAutoMode) {
        currentAutoMode = res.emisAutoMode;
    } else {
        currentAutoMode = 'FULL';
        chrome.storage.local.set({ emisAutoMode: 'FULL' });
    }
    updateAllPopupsModeUI();
});

function toggleAutoMode() {
    currentAutoMode = (currentAutoMode === 'FULL') ? 'SEMI' : 'FULL';
    chrome.storage.local.set({ emisAutoMode: currentAutoMode }, () => {
        console.log('[TPQ-EMIS Bridge] Mode automasi berganti ke:', currentAutoMode);
        updateAllPopupsModeUI();
        const modeLabel = (currentAutoMode === 'FULL') ? 'Full Otomatis (Hands-Free)' : 'Semi-Otomatis (Klik Manual)';
        showTemporaryFloatingToast(`Mode diubah: ${modeLabel}`, currentAutoMode === 'FULL' ? 'success' : 'info');
    });
}

function updateAllPopupsModeUI() {
    const isFull = currentAutoMode === 'FULL';
    const text = isFull ? '⚡ FULL AUTO' : '✋ SEMI AUTO';
    const bg = isFull ? '#059669' : '#d97706';
    const title = isFull 
        ? 'Mode Full Otomatis aktif (klik untuk beralih ke Semi-Otomatis)' 
        : 'Mode Semi-Otomatis aktif (klik untuk beralih ke Full Otomatis)';

    ['tpq-btn-mode-step1', 'tpq-btn-mode-step2', 'tpq-btn-mode-step3'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.innerText = text;
            btn.style.background = bg;
            btn.title = title;
        }
    });
}

// ==============================================================
// HELPER DETEKSI TOMBOL SIMPAN & PENANGANAN DIALOG MODAL EMIS
// ==============================================================

/**
 * Mencari tombol Simpan utama pada formulir EMIS (mengabaikan popup ekstensi kita)
 * Di EMIS (React Native Web), tombol yang aktif ditandai dengan warna HIJAU,
 * sedangkan tombol nonaktif/disabled berwarna ABU-ABU (gray) tanpa atribut disabled HTML standar.
 */
function findEmisSimpanButton(options = {}) {
    const { preferGreen = true, preferLast = false } = options;
    const candidates = Array.from(document.querySelectorAll('button, [role="button"], div[tabindex="0"], a'));
    const matches = [];

    for (const el of candidates) {
        if (el.closest('#tpq-update-alamat-step1-popup, #tpq-update-santri-step2-popup, #tpq-update-alamat-step3-popup, #tpq-temp-toast, #tpq-step1-alert, #tpq-step2-alert, #tpq-step0-alert')) {
            continue;
        }
        const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
        if (txt === 'SIMPAN' || txt === 'SIMPAN DATA' || txt === 'SIMPAN PERUBAHAN') {
            const rect = el.getBoundingClientRect();
            if (rect.width <= 0 || rect.height <= 0) continue;

            // Pastikan tombol dan parent-nya benar-benar terlihat (bukan di tab yang tersembunyi)
            let isHiddenInTab = false;
            let visCur = el;
            for (let i = 0; i < 8 && visCur && visCur !== document.body; i++) {
                const vs = window.getComputedStyle(visCur);
                if (vs.display === 'none' || vs.visibility === 'hidden' || parseFloat(vs.opacity || '1') === 0) {
                    isHiddenInTab = true;
                    break;
                }
                visCur = visCur.parentElement;
            }
            if (isHiddenInTab) continue;

            const ariaDisabled = el.getAttribute('aria-disabled') === 'true';
            const nativeDisabled = el.disabled === true;
            const style = window.getComputedStyle(el);
            const pointerEventsNone = style.pointerEvents === 'none';
            const opacityLow = parseFloat(style.opacity || '1') < 0.55;

            // Telusuri background color tombol atau container terdekatnya
            let cur = el;
            let bgFound = '';
            for (let i = 0; i < 4 && cur; i++) {
                const bg = window.getComputedStyle(cur).backgroundColor;
                if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') {
                    bgFound = bg;
                    break;
                }
                cur = cur.parentElement;
            }
            const isGreen = isGreenColor(bgFound || style.backgroundColor);

            matches.push({
                element: el,
                isGreen: isGreen,
                isDisabled: !isGreen || ariaDisabled || nativeDisabled || pointerEventsNone || opacityLow
            });
        }
    }

    if (matches.length === 0) return null;
    if (preferGreen) {
        const greenMatches = matches.filter(m => !m.isDisabled && m.isGreen);
        if (greenMatches.length > 0) {
            return preferLast ? greenMatches[greenMatches.length - 1] : greenMatches[0];
        }
    }
    return preferLast ? matches[matches.length - 1] : matches[0];
}

/**
 * Helper untuk memastikan elemen (seperti tombol YA atau TUTUP) benar-benar berada di dalam overlay modal/dialog riil
 */
function isElementInsideModal(el) {
    if (!el) return false;
    if (el.closest('#tpq-update-alamat-step1-popup, #tpq-update-santri-step2-popup, #tpq-update-alamat-step3-popup, #tpq-temp-toast, #tpq-step1-alert, #tpq-step2-alert, #tpq-step0-alert')) {
        return false;
    }
    // Cek elemen standar modal / dialog HTML5 / Bootstrap / SweetAlert dsb
    if (el.closest('[role="dialog"], .modal, .swal2-modal, .swal2-container, [aria-modal="true"], .modal-dialog, .modal-content')) {
        return true;
    }
    // Cek apakah elemen berada di dalam fixed / absolute overlay dengan z-index tinggi (React Native for Web Modal)
    let p = el.parentElement;
    for (let i = 0; i < 8 && p && p !== document.body; i++) {
        const s = window.getComputedStyle(p);
        const z = parseInt(s.zIndex || '0', 10);
        if ((s.position === 'fixed' || s.position === 'absolute') && (z >= 900 || s.backgroundColor.includes('rgba(0, 0, 0'))) {
            return true;
        }
        p = p.parentElement;
    }
    return false;
}

/**
 * Menangani dialog konfirmasi ("Batal" / "Ya") lalu dialog sukses ("Tutup")
 * dengan timeout aman dan notifikasi status
 */
function handleEmisConfirmAndSuccessModal(options = {}) {
    const {
        onSuccess,
        onTimeout,
        onStatusUpdate,
        stepName = 'Formulir',
        skipConfirm = false
    } = options;

    const reportStatus = (text, type = 'info') => {
        if (typeof onStatusUpdate === 'function') {
            onStatusUpdate(text, type);
        }
    };

    reportStatus(
        skipConfirm
            ? `Menunggu popup sukses Simpan ${stepName}...`
            : `Menunggu dialog konfirmasi Simpan ${stepName}...`,
        'info'
    );
    console.log(`[TPQ-EMIS Bridge] Menunggu ${skipConfirm ? 'popup sukses' : 'dialog konfirmasi'} untuk ${stepName}...`);

    let confirmClicked = Boolean(skipConfirm);
    let successHandled = false;
    const startTime = Date.now();
    const timeoutMs = 12000; // Maksimal 12 detik

    const pollTimer = setInterval(() => {
        const elapsed = Date.now() - startTime;
        if (elapsed > timeoutMs) {
            clearInterval(pollTimer);
            console.warn(`[TPQ-EMIS Bridge] Timeout menunggu dialog konfirmasi/sukses ${stepName}.`);
            if (typeof onTimeout === 'function') onTimeout();
            return;
        }

        // TAHAP 1: Dialog Konfirmasi ("Ya" / "Ya, Simpan")
        if (!confirmClicked) {
            const allElements = Array.from(document.querySelectorAll('button, [role="button"], div[tabindex="0"], a'));
            for (const el of allElements) {
                if (!isElementInsideModal(el)) {
                    continue;
                }
                const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
                if (txt === 'YA' || txt === 'YA, SIMPAN' || txt === 'YA, LANJUTKAN' || txt === 'YA, YAKIN') {
                    const rect = el.getBoundingClientRect();
                    if (rect.width > 0 && rect.height > 0) {
                        console.log(`[TPQ-EMIS Bridge] ✓ Dialog konfirmasi terdeteksi! Menekan '${txt}'...`);
                        reportStatus(`Dialog konfirmasi: Menekan '${txt}'...`, 'process');
                        triggerNativeClick(el);
                        confirmClicked = true;
                        break;
                    }
                }
            }
        }

        // TAHAP 2: Dialog Sukses ("Tutup" / "OK" / "Selesai")
        if (confirmClicked && !successHandled) {
            const allElements = Array.from(document.querySelectorAll('button, [role="button"], div[tabindex="0"], a'));
            for (const el of allElements) {
                if (!isElementInsideModal(el)) {
                    continue;
                }
                const txt = (el.innerText || el.textContent || '').trim().toUpperCase();
                if (txt === 'TUTUP' || txt === 'OK' || txt === 'SELESAI') {
                    const rect = el.getBoundingClientRect();
                    if (rect.width > 0 && rect.height > 0) {
                        console.log(`[TPQ-EMIS Bridge] ✓ Dialog sukses terdeteksi! Menekan '${txt}'...`);
                        reportStatus(`Berhasil disimpan! Menutup notifikasi...`, 'success');
                        triggerNativeClick(el);
                        successHandled = true;
                        clearInterval(pollTimer);

                        setTimeout(() => {
                            if (typeof onSuccess === 'function') onSuccess();
                        }, 600);
                        break;
                    }
                }
            }
        }
    }, 300);
}

/**
 * Menunggu tombol SIMPAN pada Pendaftaran Step 2 berubah menjadi HIJAU (aktif),
 * menekan tombol SIMPAN, lalu otomatis menekan 'YA' pada popup konfirmasi "Apakah Anda Yakin?"
 */
function triggerAutoSimpanPendaftaranStep2(onStatusUpdate, onDone) {
    const report = (text, type = 'info') => {
        if (typeof onStatusUpdate === 'function') onStatusUpdate(text, type);
    };

    console.log('[TPQ-EMIS Bridge] [Step 2] Menunggu tombol SIMPAN berwarna hijau (aktif)...');
    report('⏳ Cek Simpan...', 'info');

    let attempts = 0;
    const maxAttempts = 20; // 6 detik

    const waitGreenTimer = setInterval(() => {
        attempts++;
        const simpanObj = findEmisSimpanButton();

        if (simpanObj && simpanObj.isGreen && !simpanObj.isDisabled) {
            clearInterval(waitGreenTimer);
            console.log('%c[TPQ-EMIS Bridge] [Step 2] ✓ Tombol SIMPAN sudah HIJAU! Menekan tombol SIMPAN...', 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px;', simpanObj.element);
            report('⚡ Klik Simpan...', 'process');

            try {
                simpanObj.element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (_) {}

            setTimeout(() => {
                triggerNativeClick(simpanObj.element);

                handleEmisConfirmAndSuccessModal({
                    stepName: 'Pendaftaran Step 2',
                    onStatusUpdate: (msg, type) => {
                        if (msg.includes("Menekan 'YA")) {
                            report('⚡ Klik YA...', 'process');
                        } else if (type === 'success') {
                            report('✓ Tersimpan!', 'success');
                        }
                    },
                    onSuccess: () => {
                        console.log('%c[TPQ-EMIS Bridge] [Step 2] ✓ Data Pendaftaran Step 2 berhasil disimpan!', 'background: #16a34a; color: #fff; font-weight: bold; padding: 2px 6px;');
                        report('✓ Tersimpan!', 'success');
                        if (typeof onDone === 'function') onDone(true);
                    },
                    onTimeout: () => {
                        console.warn('[TPQ-EMIS Bridge] [Step 2] Timeout menunggu dialog konfirmasi/sukses SIMPAN.');
                        report('Cek Manual', 'warn');
                        if (typeof onDone === 'function') onDone(false);
                    }
                });
            }, 250);
            return;
        }

        if (attempts >= maxAttempts) {
            clearInterval(waitGreenTimer);
            console.warn('[TPQ-EMIS Bridge] [Step 2] Tombol SIMPAN belum hijau/aktif setelah 6 detik (mungkin masih ada field wajib yang kosong).');
            report('Simpan Manual', 'warn');
            if (typeof onDone === 'function') onDone(false);
        }
    }, 300);
}

/**
 * Deteksi apakah halaman berada pada Step 1 (Pencarian Kesiswaan), Step 2 (Tab DATA SANTRI), atau Step 3 (Tab DATA ALAMAT)
 */
function detectUpdateAlamatStage() {
    const textContent = (document.body.innerText || document.body.textContent || '').toUpperCase();

    // Ciri Step 3: Tab DATA ALAMAT aktif (ada teks field alamat domisili atau tab Alamat berlatar hijau)
    const hasDataAlamatText = textContent.includes('TEMPAT TINGGAL DOMISILI') || 
                              textContent.includes('STATUS KEPEMILIKAN RUMAH') ||
                              textContent.includes('JENIS TEMPAT TINGGAL') ||
                              textContent.includes('JARAK TEMPAT TINGGAL') ||
                              textContent.includes('TITIK KOORDINAT');
    if (hasDataAlamatText) {
        return 3;
    }

    try {
        const tabAlamat = findDataAlamatTabElement();
        if (tabAlamat && isGreenColor(window.getComputedStyle(tabAlamat).backgroundColor)) {
            return 3;
        }
    } catch (_) {}

    // Ciri Step 2: Halaman Form Detail/Edit Santri
    // Ada tab DATA SANTRI + DATA ALAMAT, ATAU ada teks field spesifik detail santri
    const hasDetailTabs = textContent.includes('DATA SANTRI') && textContent.includes('DATA ALAMAT') && (textContent.includes('DATA ORANG TUA') || textContent.includes('DATA AKADEMIK') || textContent.includes('ORANG TUA'));
    const hasDetailField = textContent.includes('MEMBIAYAI') || textContent.includes('KEBUTUHAN KHUSUS') || textContent.includes('JUMLAH SAUDARA KANDUNG');

    if (hasDetailTabs || hasDetailField) {
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
 * Mengecek apakah daftar santri pada halaman Kesiswaan EMIS sudah selesai dimuat dari server
 */
function isKesiswaanTableReady() {
    // 1. Cek tombol aksi "Ubah Data" atau "Detail" di baris tabel
    const allButtons = Array.from(document.querySelectorAll('button, [role="button"], a, div'));
    const hasRowBtn = allButtons.some(b => {
        const txt = (b.innerText || b.textContent || '').trim().toUpperCase();
        return txt === 'UBAH DATA' || txt === 'UBAH' || txt === 'DETAIL';
    });
    if (hasRowBtn) return true;

    // 2. Cek apakah ada baris data tabel HTML <tr>
    const rows = document.querySelectorAll('table tbody tr');
    if (rows.length > 0) return true;

    // 3. Cek apakah ada elemen cell / row React Native Web yang memuat teks khas data santri
    const bodyText = (document.body.innerText || document.body.textContent || '').toUpperCase();
    if (bodyText.includes('UBAH DATA') || (bodyText.includes('LAKI-LAKI') && bodyText.includes('AKTIF'))) {
        return true;
    }

    return false;
}

/**
 * Menerapkan filter pencarian nama santri ke kolom cari Kesiswaan dengan event lengkap
 */
function applyKesiswaanFilter(searchInput, query) {
    if (!searchInput || !query) return false;
    try {
        searchInput.focus();
        setNativeValue(searchInput, query);

        // Kirim event Keyboard Enter secara lengkap (keydown, keypress, keyup)
        const keyEvents = ['keydown', 'keypress', 'keyup'];
        keyEvents.forEach(type => {
            searchInput.dispatchEvent(new KeyboardEvent(type, {
                key: 'Enter',
                code: 'Enter',
                keyCode: 13,
                which: 13,
                bubbles: true,
                cancelable: true
            }));
        });

        // Trigger change dan blur tambahan agar React Native Web memicu onSubmitEditing / onChangeText
        searchInput.dispatchEvent(new Event('change', { bubbles: true }));

        // Cek jika ada ikon/tombol cari di dalam wrapper input yang sama
        const wrapper = searchInput.closest('.r-18u37iz') || searchInput.parentElement;
        if (wrapper) {
            const btn = wrapper.querySelector('button, [role="button"], svg, i');
            if (btn && btn !== searchInput) {
                try { btn.click(); } catch (_) {}
            }
        }
        console.log('[TPQ-EMIS Bridge] Filter nama santri diterapkan:', query);
        return true;
    } catch (e) {
        console.error('[TPQ-EMIS Bridge] Gagal menerapkan filter nama:', e);
        return false;
    }
}

/**
 * Helper deteksi warna hijau (berbagai variasi hijau tombol EMIS / Bootstrap / Tailwind)
 */
function isGreenColor(colorStr) {
    if (!colorStr) return false;
    const m = colorStr.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
    if (m) {
        const r = parseInt(m[1], 10), g = parseInt(m[2], 10), b = parseInt(m[3], 10);
        // Dominan hijau: hijau tinggi dan lebih besar dari merah dan biru
        return (g > 65 && g > r * 1.15 && g > b * 1.15) || (r <= 60 && g >= 85 && b <= 120);
    }
    return false;
}

/**
 * Memicu event klik lengkap pada elemen React Native Web (PointerEvent, MouseEvent, TouchEvent, dan Native Click)
 * Menghitung koordinat tepat di tengah elemen agar React Native Web Responder System merespons dengan benar
 */
function triggerNativeClick(el) {
    if (!el) return;
    try {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } catch (_) {}
    try { el.focus?.(); } catch (_) {}

    const rect = el.getBoundingClientRect();
    const clientX = Math.round(rect.left + (rect.width > 0 ? rect.width / 2 : 10));
    const clientY = Math.round(rect.top + (rect.height > 0 ? rect.height / 2 : 10));
    const screenX = window.screenX + clientX;
    const screenY = window.screenY + clientY;

    const baseEventInit = {
        bubbles: true,
        cancelable: true,
        composed: true,
        view: window,
        detail: 1,
        clientX: clientX,
        clientY: clientY,
        screenX: screenX,
        screenY: screenY,
        button: 0,
        buttons: 1
    };

    // 1. PointerEvent (Sangat esensial untuk React Native Web Responder System)
    if (typeof PointerEvent !== 'undefined') {
        const pointerInit = Object.assign({}, baseEventInit, {
            pointerId: 1,
            pointerType: 'mouse',
            isPrimary: true,
            width: 1,
            height: 1,
            pressure: 0.5
        });
        ['pointerover', 'pointerenter', 'pointerdown', 'pointerup'].forEach(type => {
            try { el.dispatchEvent(new PointerEvent(type, pointerInit)); } catch (_) {}
        });
    }

    // 2. MouseEvent standar
    ['mouseover', 'mouseenter', 'mousedown', 'mouseup', 'click'].forEach(type => {
        try { el.dispatchEvent(new MouseEvent(type, baseEventInit)); } catch (_) {}
    });

    // 3. TouchEvent fallback jika platform mobile/touch responder aktif
    if (typeof Touch !== 'undefined' && typeof TouchEvent !== 'undefined') {
        try {
            const touch = new Touch({
                identifier: Date.now(),
                target: el,
                clientX: clientX,
                clientY: clientY,
                screenX: screenX,
                screenY: screenY,
                pageX: clientX + window.scrollX,
                pageY: clientY + window.scrollY
            });
            const touchInit = {
                bubbles: true,
                cancelable: true,
                touches: [touch],
                targetTouches: [touch],
                changedTouches: [touch],
                view: window
            };
            el.dispatchEvent(new TouchEvent('touchstart', touchInit));
            el.dispatchEvent(new TouchEvent('touchend', touchInit));
        } catch (_) {}
    }

    // 4. Native DOM .click()
    try { el.click(); } catch (_) {}

    // 5. Cek apakah elemen atau parent adalah tag <a> dengan href
    const anchor = el.closest('a') || el.querySelector('a');
    if (anchor && anchor.href && !anchor.href.startsWith('javascript:void') && !anchor.href.endsWith('#')) {
        console.log('[TPQ-EMIS Bridge] Mengikuti link anchor Ubah Data:', anchor.href);
        try { anchor.click(); } catch (_) {}
    }

    // 6. Dispatch juga ke child pertama jika child adalah container teks
    if (el.firstElementChild && el.firstElementChild !== el) {
        try {
            el.firstElementChild.dispatchEvent(new MouseEvent('click', baseEventInit));
            el.firstElementChild.click();
        } catch (_) {}
    }
}

/**
 * Mencari semua tombol Ubah Data hijau atau tombol aksi pada tabel kesiswaan EMIS
 * (mengabaikan elemen popup ekstensi kita)
 */
function getAllUbahDataButtonsInTable() {
    const all = Array.from(document.querySelectorAll('*'));
    const buttons = [];

    for (const el of all) {
        // Abaikan elemen dari popup ekstensi kita sendiri
        if (el.closest('#tpq-update-alamat-step1-popup, #tpq-update-santri-step2-popup, #tpq-update-alamat-step3-popup, #tpq-temp-toast, #tpq-step1-alert, #tpq-step2-alert, #tpq-step0-alert')) {
            continue;
        }

        const rawTxt = (el.innerText || el.textContent || '');
        const cleanTxt = rawTxt.replace(/\s+/g, ' ').trim().toUpperCase();
        const ariaTxt = (el.getAttribute('aria-label') || '').toUpperCase();

        const hasUbahText = cleanTxt === 'UBAH DATA' || cleanTxt === 'UBAH' || ariaTxt.includes('UBAH') || (cleanTxt.includes('UBAH') && cleanTxt.includes('DATA'));
        if (!hasUbahText) continue;

        const rect = el.getBoundingClientRect();
        if (rect.width < 15 || rect.height < 15 || rect.top <= 0) continue;

        const style = window.getComputedStyle(el);
        const hasGreenBg = isGreenColor(style.backgroundColor);
        const hasRoleBtn = el.getAttribute('role') === 'button' || el.tagName === 'BUTTON' || el.tagName === 'A';

        // Hanya masukkan tombol dengan background hijau atau role button dengan ukuran tombol wajar
        if ((hasGreenBg || hasRoleBtn) && rect.width <= 260 && rect.height <= 80) {
            buttons.push({
                el: el,
                hasGreenBg: hasGreenBg,
                hasRoleBtn: hasRoleBtn,
                width: rect.width,
                height: rect.height
            });
        }
    }

    // Ambil elemen terdalam (innermost) agar tidak duplikat dengan container
    const innermost = buttons.filter(b => !buttons.some(other => other !== b && other.el.contains(b.el)));

    // Jika elemen innermost tidak memiliki latar hijau sendiri tetapi parent-nya hijau, ambil parent hijau
    return innermost.map(b => {
        if (b.hasGreenBg) return b.el;
        let cur = b.el.parentElement;
        for (let i = 0; i < 3 && cur; i++) {
            if (isGreenColor(window.getComputedStyle(cur).backgroundColor)) {
                return cur;
            }
            cur = cur.parentElement;
        }
        return b.el;
    });
}

/**
 * Mengambil seluruh teks dalam baris tabel tempat tombol berada
 */
function getRowTextForButton(btn) {
    if (!btn) return '';
    let row = btn.closest('tr, [role="row"]');
    if (!row) {
        let cur = btn;
        for (let i = 0; i < 7 && cur; i++) {
            if (cur.parentElement) {
                const siblings = cur.parentElement.children;
                if (siblings.length >= 3) {
                    row = cur.parentElement;
                    break;
                }
            }
            cur = cur.parentElement;
        }
    }
    return row ? (row.innerText || row.textContent || '').toUpperCase() : '';
}

/**
 * Mengecek apakah teks dalam baris tabel cocok dengan nama santri target
 */
function isRowMatchingSantri(rowText, santriName) {
    if (!rowText || !santriName) return false;
    const cleanName = santriName.trim().toUpperCase().replace(/\s+/g, ' ');
    const normRowText = rowText.toUpperCase().replace(/\s+/g, ' ');
    if (normRowText.includes(cleanName)) return true;

    const nameParts = cleanName.split(' ').filter(p => p.length >= 3);
    if (nameParts.length > 0 && nameParts.every(p => normRowText.includes(p))) return true;

    if (nameParts.length >= 2) {
        const matches = nameParts.filter(p => normRowText.includes(p));
        if (matches.length >= Math.ceil(nameParts.length * 0.7)) return true;
    }
    return false;
}

/**
 * Mencari tombol Ubah Data santri pada tabel kesiswaan EMIS
 * HANYA mengembalikan tombol jika tabel sudah terfilter dan cocok dengan santri target
 */
function findUbahDataButton(santriName = null) {
    const allButtons = getAllUbahDataButtonsInTable();
    if (allButtons.length === 0) return null;

    if (santriName) {
        // Cek tombol yang barisnya benar-benar memuat nama santri
        const matchingButtons = allButtons.filter(btn => {
            const rowTxt = getRowTextForButton(btn);
            return isRowMatchingSantri(rowTxt, santriName);
        });

        if (matchingButtons.length === 1) {
            return matchingButtons[0];
        }

        // Jika tombol lebih dari 1 dan belum terfilter spesifik, JANGAN kembalikan sembarang tombol!
        if (allButtons.length > 1) {
            console.log(`[TPQ-EMIS Bridge] Tabel masih memuat ${allButtons.length} santri (belum terfilter 1 baris). Menunggu...`);
            return null;
        }

        // Jika hanya ada 1 tombol di seluruh tabel tapi baris belum terdeteksi sempurna,
        // periksa teks dokumen secara keseluruhan
        if (allButtons.length === 1) {
            const bodyTxt = (document.body.innerText || '').toUpperCase();
            if (isRowMatchingSantri(bodyTxt, santriName)) {
                return allButtons[0];
            }
        }

        return null;
    }

    // Jika tanpa santriName (fallback manual) dan hanya ada 1 tombol di layar
    if (allButtons.length === 1) {
        return allButtons[0];
    }
    return null;
}

/**
 * Menunggu tabel EMIS BENAR-BENAR terfilter hingga hanya ada 1 baris santri target,
 * baru kemudian membungkus tombol dengan outline kuning dan mengeksekusi klik.
 */
function waitAndAutoClickUbahData(santriData) {
    const statusBadge = document.getElementById('tpq-step1-filter-status');
    let attempts = 0;
    const maxAttempts = 45; // 45 x 350ms = ~15.7 detik
    let reFilterTriggered = false;

    console.log('[TPQ-EMIS Bridge] waitAndAutoClickUbahData aktif, target:', santriData.nama_santri, 'Mode:', currentAutoMode);

    const poll = setInterval(() => {
        attempts++;

        const allButtons = getAllUbahDataButtonsInTable();
        const searchInput = findKesiswaanSearchInput();

        // 1. Kondisi: Tabel masih memuat BANYAK santri (> 1 baris)
        // EMIS belum selesai memfilter. JANGAN BUNGKUS KUNING, JANGAN KLIK!
        if (allButtons.length > 1) {
            if (statusBadge) {
                statusBadge.innerHTML = `<span>⏳</span> <span>Memfilter tabel kesiswaan... (masih ada ${allButtons.length} santri)</span>`;
                statusBadge.style.color = '#1d4ed8';
                statusBadge.style.background = '#eff6ff';
                statusBadge.style.borderColor = '#bfdbfe';
            }

            // Jika setelah 8 percobaan (~2.8s) tabel masih banyak, re-trigger Enter pada kolom pencarian
            if (attempts >= 8 && !reFilterTriggered && searchInput) {
                console.log('[TPQ-EMIS Bridge] Re-trigger Enter filter pencarian karena tabel masih memuat banyak baris...');
                reFilterTriggered = true;
                applyKesiswaanFilter(searchInput, santriData.nama_santri);
            }
            return;
        }

        // 2. Kondisi: Tabel kosong / sedang memuat data (0 baris)
        if (allButtons.length === 0) {
            if (statusBadge) {
                statusBadge.innerHTML = `<span>⏳</span> <span>Memuat hasil filter santri... (${attempts}/${maxAttempts})</span>`;
            }
            return;
        }

        // 3. Kondisi: Tabel HANYA MEMILIKI 1 TOMBOL / 1 ROW!
        if (allButtons.length === 1) {
            const candidateBtn = allButtons[0];
            const rowTxt = getRowTextForButton(candidateBtn);
            const isMatch = isRowMatchingSantri(rowTxt, santriData.nama_santri) || 
                            isRowMatchingSantri(document.body.innerText, santriData.nama_santri);

            if (!isMatch) {
                // Baris tunggal ini belum sesuai dengan nama santri yang dicari (mungkin delay render)
                if (statusBadge) {
                    statusBadge.innerHTML = `<span>⏳</span> <span>Menunggu kecocokan data santri...</span>`;
                }
                return;
            }

            // =========================================================================
            // DATA TELAH BENAR-BENAR TERFILTER DAN HANYA TERSISA 1 ROW YANG SESUAI!
            // =========================================================================
            clearInterval(poll);
            console.log('[TPQ-EMIS Bridge] ✓ Filter tuntas! Ditemukan tepat 1 baris santri yang cocok pada attempt #' + attempts + ':', candidateBtn);

            // LANGKAH 1: Baru membungkus kuning area button hijau
            try {
                candidateBtn.style.outline = '4px solid #ffc107';
                candidateBtn.style.outlineOffset = '2px';
                candidateBtn.style.boxShadow = '0 0 14px rgba(255, 193, 7, 0.9)';
                candidateBtn.style.transition = 'all 0.25s ease';
            } catch (_) {}

            if (statusBadge) {
                statusBadge.innerHTML = `<span>✓</span> <span>Santri terfilter (1 baris): <strong>${escapeHtml(santriData.nama_santri)}</strong></span>`;
                statusBadge.style.color = '#15803d';
                statusBadge.style.background = '#dcfce7';
                statusBadge.style.borderColor = '#86efac';
            }

            // LANGKAH 2: Baru eksekusi menekan button setelah jeda visual
            if (currentAutoMode === 'FULL') {
                setTimeout(() => {
                    if (statusBadge) {
                        statusBadge.innerHTML = '<span>⚡</span> <span>Tombol Ubah Data aktif! Membuka formulir data santri...</span>';
                    }

                    console.log('[TPQ-EMIS Bridge] Memicu klik otomatis pada tombol Ubah Data hijau...');
                    chrome.storage.local.set({ waitingForUpdateStep2: true }, () => {
                        triggerNativeClick(candidateBtn);
                    });

                    // Supervisor pengawas transisi ke Step 2 (tanpa menutup popup)
                    let retryCount = 0;
                    const retryTimer = setInterval(() => {
                        const curStage = detectUpdateAlamatStage();
                        if (curStage === 2 || curStage === 3) {
                            clearInterval(retryTimer);
                            console.log('[TPQ-EMIS Bridge] Berhasil masuk ke Detail Santri!');
                            return;
                        }

                        retryCount++;
                        if (retryCount <= 4) {
                            console.log(`[TPQ-EMIS Bridge] Retry klik #${retryCount} pada tombol Ubah Data...`);
                            triggerNativeClick(candidateBtn);
                            if (statusBadge) {
                                statusBadge.innerHTML = `<span>⚡</span> <span>Membuka formulir (percobaan ${retryCount + 1})... Atau <strong id="tpq-manual-retry-link" style="color:#0d6efd;text-decoration:underline;cursor:pointer;">klik di sini</strong></span>`;
                                const retryLink = document.getElementById('tpq-manual-retry-link');
                                if (retryLink) {
                                    retryLink.onclick = (e) => {
                                        e.preventDefault();
                                        triggerNativeClick(candidateBtn);
                                    };
                                }
                            }
                        } else {
                            clearInterval(retryTimer);
                            console.warn('[TPQ-EMIS Bridge] Klik otomatis belum memicu perpindahan halaman. Meminta klik manual operator.');
                            if (statusBadge) {
                                statusBadge.innerHTML = '<span>✋</span> <span>Silakan <strong>klik langsung tombol "Ubah Data" bergaris kuning</strong> di tabel kesiswaan untuk melanjutkan.</span>';
                                statusBadge.style.color = '#b45309';
                                statusBadge.style.background = '#fef3c7';
                                statusBadge.style.borderColor = '#fde68a';
                            }
                            if (typeof window.__tpqToggleStep1Popup === 'function') {
                                window.__tpqToggleStep1Popup(true);
                            }
                        }
                    }, 1400);

                }, 750); // Jeda 750ms agar operator melihat outline kuning membungkus tombol sebelum klik
            } else {
                // Mode SEMI-OTOMATIS
                if (statusBadge) {
                    statusBadge.innerHTML = '<span>✓</span> <span>Santri terfilter (1 baris). Silakan <strong>klik tombol "Ubah Data" bergaris kuning</strong> di tabel.</span>';
                    statusBadge.style.color = '#1d4ed8';
                    statusBadge.style.background = '#eff6ff';
                    statusBadge.style.borderColor = '#bfdbfe';
                }
            }
            return;
        }

        // 4. Batas Waktu Terlewati
        if (attempts >= maxAttempts) {
            clearInterval(poll);
            console.warn('[TPQ-EMIS Bridge] Filter santri belum menyisakan 1 baris setelah batas waktu.');
            if (statusBadge) {
                statusBadge.innerHTML = `<span>⚠️</span> <span>Tabel belum terfilter sempurna (${allButtons.length} santri). Silakan filter ulang atau klik tombol Ubah Data secara manual.</span>`;
                statusBadge.style.color = '#b45309';
                statusBadge.style.background = '#fef3c7';
                statusBadge.style.borderColor = '#fde68a';
            }
            if (typeof window.__tpqToggleStep1Popup === 'function') {
                window.__tpqToggleStep1Popup(true);
            }
        }
    }, 350);
}

/**
 * Eksekusi Step 1 Update Alamat: Pencarian Nama Santri dengan Delay & Pengecekan Daftar Siap
 */
function executeStep1UpdateAlamat(santriData) {
    console.log('[TPQ-EMIS Bridge] Eksekusi Step 1 Update Alamat: Menunggu daftar santri EMIS siap...');

    // 1. Tampilkan popup panduan langsung agar user mengetahui status proses
    showUpdateAlamatStep1Popup(santriData);

    const statusBadge = document.getElementById('tpq-step1-filter-status');
    if (statusBadge) {
        statusBadge.innerHTML = '<span>⏳</span> <span>Menunggu tabel daftar santri EMIS siap...</span>';
    }

    const startTime = Date.now();
    const minDelayMs = 2000; // Jeda minimal 2.0 detik agar request list awal EMIS selesai
    const maxWaitMs = 6500;  // Batas maksimal tunggu 6.5 detik

    const timer = setInterval(() => {
        const elapsed = Date.now() - startTime;
        const searchInput = findKesiswaanSearchInput();
        const tableReady = isKesiswaanTableReady();

        // Terapkan filter jika:
        // (Input ada DAN sudah lewat minimal delay DAN tabel sudah memuat data)
        // ATAU sudah lewat batas maksimal tunggu
        if (searchInput && ((elapsed >= minDelayMs && tableReady) || elapsed >= maxWaitMs)) {
            clearInterval(timer);
            console.log(`[TPQ-EMIS Bridge] Menerapkan filter nama setelah ${elapsed}ms (tableReady: ${tableReady})`);
            
            if (statusBadge) {
                statusBadge.innerHTML = `<span>⏳</span> <span>Menerapkan filter nama: <strong>${escapeHtml(santriData.nama_santri)}</strong>...</span>`;
            }

            applyKesiswaanFilter(searchInput, santriData.nama_santri);

            // Jalankan deteksi tombol Ubah Data & auto-click dengan alur ketat:
            // Hanya bungkus kuning & klik setelah tabel BENAR-BENAR terfilter 1 baris
            waitAndAutoClickUbahData(santriData);
        }
    }, 300);

    // Listener global: jika tombol Ubah Data diklik manual, bersiap untuk Step 2
    document.addEventListener('click', (e) => {
        const target = e.target;
        if (!target) return;
        const btn = target.closest('button, [role="button"], a') || target;
        const txt = (btn.innerText || btn.textContent || '').trim().toUpperCase();
        if (txt.includes('UBAH DATA') || txt.includes('EDIT')) {
            console.log('[TPQ-EMIS Bridge] Tombol Ubah Data diklik! Bersiap untuk Step 2...');
            // JANGAN hapus popup Step 1 di sini!
            // Popup Step 1 akan ditutup otomatis oleh initStepTransitionWatcher
            // saat halaman formulir Step 2 terkonfirmasi aktif di DOM.
            chrome.storage.local.set({ waitingForUpdateStep2: true });
            const badge = document.getElementById('tpq-step1-filter-status');
            if (badge) {
                badge.innerHTML = '<span>⚡</span> <span>Membuka data santri... Menunggu formulir terbuka...</span>';
                badge.style.color = '#15803d';
                badge.style.background = '#dcfce7';
                badge.style.borderColor = '#86efac';
            }
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
    isWilayahCascadeRunning = false;
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
        width: 370px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
        transition: width 0.25s ease;
    `;

    const hdrBtnStyle = 'flex: 1; height: 26px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; padding: 0 8px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer; white-space: nowrap;';

    div.innerHTML = `
        <div id="tpq-update-step1-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
                <div style="display: flex; align-items: center; gap: 5px; flex: 1; min-width: 0;" title="${escapeHtml(data.nama_santri)}">
                    <span style="font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
                    <span style="font-size: 9.5px; background: rgba(255,255,255,0.25); padding: 1px 5px; border-radius: 3px; font-weight: normal; flex-shrink: 0;">v1.6.1</span>
                </div>
                <span id="tpq-close-update-step1" style="cursor: pointer; font-size: 18px; line-height: 1; padding: 0 4px; opacity: 0.9; flex-shrink: 0;" title="Tutup & batalkan antrean">&times;</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <button id="tpq-btn-mode-step1" style="${hdrBtnStyle} background: ${currentAutoMode === 'FULL' ? '#059669' : '#d97706'}; color: #fff; border: 1px solid rgba(255,255,255,0.4);" title="${currentAutoMode === 'FULL' ? 'Mode Full Otomatis aktif (klik untuk ganti ke Semi-Otomatis)' : 'Mode Semi-Otomatis aktif (klik untuk ganti ke Full Otomatis)'}">
                    ${currentAutoMode === 'FULL' ? '⚡ FULL AUTO' : '✋ SEMI AUTO'}
                </button>
                <button id="tpq-btn-quick-step2" style="${hdrBtnStyle} background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4);" title="Langsung masuk ke Step 2 (DATA SANTRI)">
                    ⚡ Step 2
                </button>
                <button id="tpq-toggle-step1" style="${hdrBtnStyle} background: rgba(255,255,255,0.22); border: 1px solid rgba(255,255,255,0.45); color: #fff;" title="Buka panel panduan">
                    ▼ Buka
                </button>
            </div>
        </div>

        <!-- Banner Status Real-Time Step 1 (Selalu Terlihat) -->
        <div id="tpq-step1-filter-status" style="padding: 7px 12px; background: #eff6ff; border-bottom: 1px solid #bfdbfe; font-size: 11.5px; color: #1d4ed8; font-weight: 500; display: flex; align-items: center; gap: 6px;">
            <span>⏳</span> <span>Menunggu tabel santri EMIS selesai memuat...</span>
        </div>

        <div id="tpq-update-step1-body" style="display: none; padding: 10px 12px; max-height: 70vh; overflow-y: auto;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: bold; font-size: 13px; color: #0d6efd;">${escapeHtml(data.nama_santri)}</div>
                <button class="tpq-copy-btn" data-val="${escapeHtml(data.nama_santri)}" style="border: 1px solid #ced4da; background: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; cursor: pointer; color: #495057;" title="Salin Nama">📋 Salin</button>
            </div>

            <div style="display: flex; gap: 6px; margin-bottom: 8px;">
                <button id="tpq-btn-refilter-nama" style="flex: 1; background: #f8fafc; color: #0d6efd; border: 1px solid #0d6efd; padding: 7px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    🔍 Filter Ulang
                </button>
                <button id="tpq-btn-force-step2" style="flex: 1.2; background: #198754; color: #fff; border: none; padding: 7px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    ⚡ Masuk Step 2
                </button>
            </div>

            <button id="tpq-btn-cancel-update-step1" style="width: 100%; background: transparent; color: #dc3545; border: 1px solid #fecaca; padding: 6px 10px; border-radius: 5px; font-size: 11px; cursor: pointer;">
                Batalkan Santri Ini
            </button>
        </div>
    `;

    document.body.appendChild(div);
    makeDraggable(div, document.getElementById('tpq-update-step1-header'));

    let isStep1Expanded = false;
    const btnToggleStep1 = document.getElementById('tpq-toggle-step1');
    const bodyStep1 = document.getElementById('tpq-update-step1-body');

    function toggleStep1(forceState) {
        isStep1Expanded = typeof forceState === 'boolean' ? forceState : !isStep1Expanded;
        if (isStep1Expanded) {
            div.style.width = '390px';
            bodyStep1.style.display = 'block';
            btnToggleStep1.innerHTML = '▲ Tutup';
            btnToggleStep1.style.background = 'rgba(255,255,255,0.35)';
        } else {
            div.style.width = '370px';
            bodyStep1.style.display = 'none';
            btnToggleStep1.innerHTML = '▼ Buka';
            btnToggleStep1.style.background = 'rgba(255,255,255,0.22)';
        }
    }
    window.__tpqToggleStep1Popup = toggleStep1;

    btnToggleStep1.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleStep1();
    });

    const btnMode1 = document.getElementById('tpq-btn-mode-step1');
    if (btnMode1) {
        btnMode1.addEventListener('click', toggleAutoMode);
    }

    document.getElementById('tpq-close-update-step1').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data ditutup dan antrean telah dibersihkan.');
    });

    document.getElementById('tpq-btn-cancel-update-step1').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data santri dibatalkan. Anda dapat memilih santri lain di TPQ Smart.');
    });

    div.querySelectorAll('.tpq-copy-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const oldText = btn.innerHTML;
                btn.innerHTML = '✓';
                btn.style.color = '#198754';
                btn.style.borderColor = '#198754';
                setTimeout(() => {
                    btn.innerHTML = oldText;
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 1200);
            }
        });
    });

    const btnRefilter = document.getElementById('tpq-btn-refilter-nama');
    if (btnRefilter) {
        btnRefilter.addEventListener('click', () => {
            const inp = findKesiswaanSearchInput();
            if (inp) {
                btnRefilter.innerText = '⏳ Memfilter...';
                applyKesiswaanFilter(inp, data.nama_santri);
                setTimeout(() => {
                    btnRefilter.innerText = '✓ Berhasil Difilter!';
                    setTimeout(() => { btnRefilter.innerText = '🔍 Filter Ulang'; }, 1800);
                }, 400);
                const statusBadge = document.getElementById('tpq-step1-filter-status');
                if (statusBadge) {
                    statusBadge.innerHTML = '<span>✓</span> <span>Nama telah difilter ulang. Mencari tombol Ubah Data...</span>';
                    statusBadge.style.color = '#198754';
                    statusBadge.style.background = '#d1e7dd';
                    statusBadge.style.borderColor = '#badbcc';
                }
                waitAndAutoClickUbahData(data);
            } else {
                btnRefilter.innerText = '⚠️ Kolom Cari Tidak Ditemukan';
                setTimeout(() => { btnRefilter.innerText = '🔍 Filter Ulang'; }, 1800);
            }
        });
    }

    const goToStep2 = () => {
        const currentStage = detectUpdateAlamatStage();
        if (currentStage === 1) {
            const btnUbah = findUbahDataButton(data.nama_santri);
            if (btnUbah) {
                console.log('[TPQ-EMIS Bridge] Menekan tombol Ubah Data via tombol Step 2 popup...');
                chrome.storage.local.set({ waitingForUpdateStep2: true }, () => {
                    triggerNativeClick(btnUbah);
                });
                const statusBadge = document.getElementById('tpq-step1-filter-status');
                if (statusBadge) {
                    statusBadge.innerHTML = '<span>⚡</span> <span>Membuka data santri... Menunggu formulir terbuka...</span>';
                    statusBadge.style.color = '#15803d';
                    statusBadge.style.background = '#dcfce7';
                    statusBadge.style.borderColor = '#86efac';
                }
            } else {
                console.warn('[TPQ-EMIS Bridge] Tombol Ubah Data tidak ditemukan saat tombol Step 2 popup ditekan.');
            }
        } else {
            div.remove();
            executeStep2DataSantri(data);
        }
    };

    document.getElementById('tpq-btn-force-step2').addEventListener('click', goToStep2);
    document.getElementById('tpq-btn-quick-step2').addEventListener('click', goToStep2);
}

/**
 * Mencari elemen tombol tab DATA ALAMAT secara presisi di baris navigasi tab
 */
function findDataAlamatTabElement() {
    const all = Array.from(document.querySelectorAll('*'));
    const candidates = [];

    for (const el of all) {
        if (el.closest('#tpq-update-alamat-step1-popup, #tpq-update-santri-step2-popup, #tpq-update-alamat-step3-popup, #tpq-temp-toast, #tpq-step1-alert, #tpq-step2-alert, #tpq-step0-alert')) {
            continue;
        }

        const rawTxt = (el.innerText || el.textContent || '');
        const cleanTxt = rawTxt.replace(/\s+/g, ' ').trim().toUpperCase();

        if (cleanTxt === 'DATA ALAMAT') {
            const rect = el.getBoundingClientRect();
            if (rect.width >= 30 && rect.height >= 12 && rect.top > 0) {
                // Cari container tombol tab terdekat (role="tab", role="button", dsb)
                const clickable = el.closest('[role="tab"], [role="button"], button, a, div[tabindex="0"]') || el;
                const cRect = clickable.getBoundingClientRect();
                if (cRect.width <= 350 && cRect.height <= 80) {
                    candidates.push(clickable);
                }
            }
        }
    }

    if (candidates.length === 0) return null;

    // Prioritaskan kandidat yang berada di dalam baris tab (memiliki tetangga "DATA SANTRI" atau "DATA ORANG TUA")
    for (const cand of candidates) {
        let parent = cand.parentElement;
        for (let i = 0; i < 4 && parent; i++) {
            const pTxt = (parent.innerText || parent.textContent || '').toUpperCase();
            if (pTxt.includes('DATA SANTRI') && (pTxt.includes('DATA ORANG TUA') || pTxt.includes('AKTIVITAS'))) {
                console.log('[TPQ-EMIS Bridge] ✓ Tab bar container terdeteksi, memilih tab DATA ALAMAT:', cand);
                return cand;
            }
            parent = parent.parentElement;
        }
    }

    return candidates[0];
}

/**
 * Berpindah ke tab DATA ALAMAT dan MENUNGGU secara aktif sampai tab tersebut terkonfirmasi aktif
 */
function switchToDataAlamatTab(onSuccess, onFail) {
    console.log('[TPQ-EMIS Bridge] Memulai proses perpindahan ke tab DATA ALAMAT...');

    // Jika tab DATA ALAMAT sudah aktif (Stage 3 terdeteksi)
    if (detectUpdateAlamatStage() === 3) {
        console.log('[TPQ-EMIS Bridge] Tab DATA ALAMAT sudah dalam keadaan aktif.');
        if (typeof onSuccess === 'function') onSuccess();
        return;
    }

    let attempts = 0;
    const maxAttempts = 30; // 30 x 250ms = 7.5 detik

    const timer = setInterval(() => {
        attempts++;

        // 1. Cek apakah Stage 3 sudah terkonfirmasi aktif
        const stage = detectUpdateAlamatStage();
        if (stage === 3) {
            clearInterval(timer);
            console.log('[TPQ-EMIS Bridge] ✓ Tab DATA ALAMAT terkonfirmasi aktif pada attempt #' + attempts + '!');
            if (typeof onSuccess === 'function') onSuccess();
            return;
        }

        // 2. Cari elemen tab DATA ALAMAT dan kirim event klik lengkap
        const tabEl = findDataAlamatTabElement();
        if (tabEl) {
            try {
                tabEl.style.outline = '3px solid #198754';
                tabEl.style.outlineOffset = '2px';
            } catch (_) {}

            console.log(`[TPQ-EMIS Bridge] Memicu triggerNativeClick pada tab DATA ALAMAT (attempt #${attempts})...`);
            triggerNativeClick(tabEl);
        } else {
            console.warn(`[TPQ-EMIS Bridge] Elemen tab DATA ALAMAT belum ditemukan (attempt #${attempts})...`);
        }

        // 3. Batas waktu habis
        if (attempts >= maxAttempts) {
            clearInterval(timer);
            console.warn('[TPQ-EMIS Bridge] Batas waktu menunggu aktivasi tab DATA ALAMAT tercapai.');
            if (typeof onFail === 'function') {
                onFail();
            } else if (typeof onSuccess === 'function') {
                onSuccess();
            }
        }
    }, 250);
}

/**
 * Mengisi field teks domisili Ayah Kandung (RT, RW, Alamat, Kode Pos) di tab DATA ALAMAT
 */
function fillUpdateAlamatAyahTextFields(santri) {
    if (!santri) return;
    console.log('[TPQ-EMIS Bridge] Mengisi field teks domisili Ayah di tab DATA ALAMAT...');
    updateWilayahStatusUI('alamat', '[⏳ Mengisi...]', '#0d6efd');
    if (santri.ayah_kode_pos) updateWilayahStatusUI('kodepos', '[⏳ Mengisi...]', '#0d6efd');

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

    updateWilayahStatusUI('alamat', (filledAlamat || filledRt || filledRw) ? '[✓ Terisi]' : '[⚠️ Manual]', (filledAlamat || filledRt || filledRw) ? '#198754' : '#dc3545');
    if (santri.ayah_kode_pos) {
        updateWilayahStatusUI('kodepos', filledKodePos ? '[✓ Terisi]' : '[⚠️ Manual]', filledKodePos ? '#198754' : '#dc3545');
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

/**
 * Auto-Simpan untuk Step 2 (Tab DATA SANTRI):
 * Menunggu tombol Simpan aktif, klik Simpan, klik Ya pada dialog konfirmasi, klik Tutup pada dialog sukses,
 * lalu memicu callback untuk beralih ke tab DATA ALAMAT (Step 3).
 */
function triggerAutoSimpanStep2(santriData, onFinishCallback) {
    console.log('[TPQ-EMIS Bridge] Memulai auto-simpan Step 2 (DATA SANTRI)...');
    const statusBox = document.getElementById('tpq-step2-status-banner');
    const updateBanner = (msg, bg = '#eff6ff', color = '#1d4ed8', border = '#bfdbfe') => {
        if (statusBox) {
            statusBox.innerHTML = `<span>⚡</span> <span>${escapeHtml(msg)}</span>`;
            statusBox.style.background = bg;
            statusBox.style.color = color;
            statusBox.style.borderColor = border;
        }
    };

    updateBanner('Biaya sekolah terpilih. Memeriksa tombol Simpan...');

    let attempts = 0;
    const maxAttempts = 15; // 15 x 300ms = 4.5 detik

    const checkTimer = setInterval(() => {
        attempts++;
        const simpanObj = findEmisSimpanButton();

        if (simpanObj && !simpanObj.isDisabled && simpanObj.isGreen) {
            clearInterval(checkTimer);
            updateBanner('Menekan tombol Simpan di Tab Data Santri...', '#fef3c7', '#b45309', '#fde68a');

            setTimeout(() => {
                try {
                    simpanObj.element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    simpanObj.element.click();
                } catch (e) {
                    console.error('[TPQ-EMIS Bridge] Gagal klik tombol Simpan Step 2:', e);
                }

                handleEmisConfirmAndSuccessModal({
                    stepName: 'Data Santri',
                    onStatusUpdate: (txt, type) => {
                        if (type === 'success') {
                            updateBanner(txt, '#dcfce7', '#15803d', '#86efac');
                        } else {
                            updateBanner(txt, '#eff6ff', '#1d4ed8', '#bfdbfe');
                        }
                    },
                    onSuccess: () => {
                        updateBanner('Data Santri tersimpan! Berpindah ke Tab Data Alamat...', '#dcfce7', '#15803d', '#86efac');
                        setTimeout(() => {
                            if (typeof onFinishCallback === 'function') {
                                onFinishCallback();
                            }
                        }, 400);
                    },
                    onTimeout: () => {
                        updateBanner('Dialog Simpan tidak terdeteksi. Silakan klik manual tombol Lanjut ke Step 3.', '#fff1f2', '#e11d48', '#fecdd3');
                        if (typeof window.__tpqToggleStep2Popup === 'function') window.__tpqToggleStep2Popup(true);
                    }
                });
            }, 600);
        } else if (attempts >= maxAttempts) {
            clearInterval(checkTimer);
            console.warn('[TPQ-EMIS Bridge] Tombol Simpan Step 2 belum aktif atau tidak ditemukan.');
            updateBanner('Tombol Simpan belum aktif di EMIS. Silakan simpan manual jika diperlukan lalu klik Step 3.', '#fff1f2', '#e11d48', '#fecdd3');
            if (typeof window.__tpqToggleStep2Popup === 'function') window.__tpqToggleStep2Popup(true);
        }
    }, 300);
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

            // Jika Mode FULL OTOMATIS: Otomatis tekan Simpan, konfirmasi Ya, Tutup, dan pindah ke Step 3
            if (currentAutoMode === 'FULL') {
                triggerAutoSimpanStep2(santriData, () => {
                    console.log('[TPQ-EMIS Bridge] Auto-simpan Step 2 selesai. Berpindah ke Tab DATA ALAMAT...');
                    const statusBanner = document.getElementById('tpq-step2-status-banner');
                    if (statusBanner) {
                        statusBanner.innerHTML = '<span>🔄</span> <span>Menutup dialog sukses & berpindah ke Tab DATA ALAMAT...</span>';
                        statusBanner.style.background = '#dcfce7';
                        statusBanner.style.color = '#15803d';
                        statusBanner.style.borderColor = '#86efac';
                    }

                    // Pindah tab terlebih dahulu dan tunggu sampai tab DATA ALAMAT benar-benar aktif!
                    switchToDataAlamatTab(() => {
                        const p2 = document.getElementById('tpq-update-santri-step2-popup');
                        if (p2) p2.remove();
                        executeStep3DataAlamat(santriData);
                    });
                });
            }
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
        width: 370px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
        transition: width 0.25s ease;
    `;

    const targetBiaya = data.yang_biaya_sekolah || 'Orang Tua';
    const hdrBtnStyle = 'flex: 1; height: 26px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; padding: 0 8px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer; white-space: nowrap;';

    div.innerHTML = `
        <div id="tpq-update-santri-step2-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
                <div style="display: flex; align-items: center; gap: 5px; flex: 1; min-width: 0;" title="${escapeHtml(data.nama_santri)}">
                    <span style="font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
                    <span style="font-size: 9.5px; background: rgba(255,255,255,0.25); padding: 1px 5px; border-radius: 3px; font-weight: normal; flex-shrink: 0;">v1.6.1</span>
                </div>
                <span id="tpq-close-update-santri-step2" style="cursor: pointer; font-size: 18px; line-height: 1; padding: 0 4px; opacity: 0.9; flex-shrink: 0;" title="Tutup & batalkan antrean">&times;</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <button id="tpq-btn-mode-step2" style="${hdrBtnStyle} background: ${currentAutoMode === 'FULL' ? '#059669' : '#d97706'}; color: #fff; border: 1px solid rgba(255,255,255,0.4);" title="${currentAutoMode === 'FULL' ? 'Mode Full Otomatis aktif (klik untuk ganti ke Semi-Otomatis)' : 'Mode Semi-Otomatis aktif (klik untuk ganti ke Full Otomatis)'}">
                    ${currentAutoMode === 'FULL' ? '⚡ FULL AUTO' : '✋ SEMI AUTO'}
                </button>
                <button id="tpq-btn-quick-lanjut-step2" style="${hdrBtnStyle} background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4);" title="Lanjut ke Tab DATA ALAMAT (Step 3)">
                    ➡️ Step 3
                </button>
                <button id="tpq-toggle-step2" style="${hdrBtnStyle} background: rgba(255,255,255,0.22); border: 1px solid rgba(255,255,255,0.45); color: #fff;" title="Buka panel panduan">
                    ▼ Buka
                </button>
            </div>
        </div>

        <!-- Banner Status Real-Time Step 2 (Selalu Terlihat) -->
        <div id="tpq-step2-status-banner" style="padding: 7px 12px; background: #eff6ff; border-bottom: 1px solid #bfdbfe; font-size: 11.5px; color: #1d4ed8; font-weight: 500; display: flex; align-items: center; gap: 6px;">
            <span>⚡</span> <span>Biaya Sekolah: <strong>${escapeHtml(targetBiaya)}</strong> — Menunggu Simpan...</span>
        </div>

        <div id="tpq-update-santri-step2-body" style="display: none; padding: 10px 12px; max-height: 70vh; overflow-y: auto;">
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; font-size: 12px; color: #166534;">
                <div style="font-weight: bold; margin-bottom: 4px;">Yang Membiayai Sekolah:</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: bold;">${escapeHtml(targetBiaya)}</span>
                    <button class="tpq-copy-btn" data-val="${escapeHtml(targetBiaya)}" style="border: 1px solid #86efac; background: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; cursor: pointer; color: #166534;" title="Salin">📋</button>
                </div>
            </div>

            <div style="font-size: 11px; color: #64748b; line-height: 1.4; margin-bottom: 10px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; padding: 8px 10px;">
                Klik tombol <strong>Simpan</strong> di EMIS jika diperlukan, lalu klik <strong>Lanjut ke Step 3</strong> di bawah ini.
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <button id="tpq-btn-lanjut-step3" style="width: 100%; background: #198754; color: #fff; border: none; padding: 8px 12px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    ➡️ Lanjut ke DATA ALAMAT (Step 3)
                </button>
                <div style="display: flex; gap: 6px;">
                    <button id="tpq-btn-refill-biaya" style="flex: 1; background: #f8fafc; color: #0d6efd; border: 1px solid #0d6efd; padding: 6px 8px; border-radius: 5px; font-size: 11px; font-weight: 500; cursor: pointer;">
                        Setel Ulang Dropdown
                    </button>
                    <button id="tpq-btn-cancel-update-santri-step2" style="flex: 1; background: transparent; color: #dc3545; border: 1px solid #fecaca; padding: 6px 8px; border-radius: 5px; font-size: 11px; cursor: pointer;">
                        Batalkan Santri Ini
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(div);
    makeDraggable(div, document.getElementById('tpq-update-santri-step2-header'));

    let isStep2Expanded = false;
    const btnToggleStep2 = document.getElementById('tpq-toggle-step2');
    const bodyStep2 = document.getElementById('tpq-update-santri-step2-body');

    function toggleStep2(forceState) {
        isStep2Expanded = typeof forceState === 'boolean' ? forceState : !isStep2Expanded;
        if (isStep2Expanded) {
            div.style.width = '390px';
            bodyStep2.style.display = 'block';
            btnToggleStep2.innerHTML = '▲ Tutup';
            btnToggleStep2.style.background = 'rgba(255,255,255,0.35)';
        } else {
            div.style.width = '370px';
            bodyStep2.style.display = 'none';
            btnToggleStep2.innerHTML = '▼ Buka';
            btnToggleStep2.style.background = 'rgba(255,255,255,0.22)';
        }
    }
    window.__tpqToggleStep2Popup = toggleStep2;

    btnToggleStep2.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleStep2();
    });

    document.getElementById('tpq-close-update-santri-step2').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data santri ditutup dan antrean dibersihkan.');
    });

    document.getElementById('tpq-btn-cancel-update-santri-step2').addEventListener('click', () => {
        cancelUpdateAlamatProcess(`Proses update data untuk ${data.nama_santri || 'santri'} telah dibatalkan.`);
    });

    document.getElementById('tpq-btn-refill-biaya').addEventListener('click', () => {
        fillDataSantriYangMembiayaiSekolah(data);
    });

    div.querySelectorAll('.tpq-copy-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const oldContent = btn.innerHTML;
                btn.innerHTML = '✓';
                btn.style.color = '#198754';
                btn.style.borderColor = '#198754';
                setTimeout(() => {
                    btn.innerHTML = oldContent;
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 1200);
            }
        });
    });

    const handleLanjutStep3 = () => {
        const statusBanner = document.getElementById('tpq-step2-status-banner');
        if (statusBanner) {
            statusBanner.innerHTML = '<span>🔄</span> <span>Berpindah ke Tab DATA ALAMAT...</span>';
            statusBanner.style.background = '#dcfce7';
            statusBanner.style.color = '#15803d';
            statusBanner.style.borderColor = '#86efac';
        }
        switchToDataAlamatTab(() => {
            div.remove();
            executeStep3DataAlamat(data);
        });
    };

    const btnMode2 = document.getElementById('tpq-btn-mode-step2');
    if (btnMode2) {
        btnMode2.addEventListener('click', toggleAutoMode);
    }

    document.getElementById('tpq-btn-lanjut-step3').addEventListener('click', handleLanjutStep3);
    document.getElementById('tpq-btn-quick-lanjut-step2').addEventListener('click', handleLanjutStep3);
}

let isStep3Executing = false;
let isStep3Done = false;
let isWilayahCascadeRunning = false;
let lastStep3ExecutionTime = 0;

/**
 * Normalisasi nama wilayah (menghapus awalan KAB, KABUPATEN, KOTA, KEC, KEL, DESA, tanda baca, dll)
 */
function cleanWilayahName(str) {
    if (!str) return '';
    return String(str)
        .toUpperCase()
        .replace(/\b(KAB\.|KABUPATEN|KOTA|KEC\.|KECAMATAN|KEL\.|KELURAHAN|DESA)\b/gi, '')
        .replace(/[^A-Z0-9\s]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

/**
 * Mencari dropdown Provinsi (pasti memiliki >25 opsi dan memuat nama provinsi Indonesia)
 */
function findProvinsiSelect(allSelects = null) {
    const selects = allSelects || Array.from(document.querySelectorAll('select'));
    for (const sel of selects) {
        const opts = Array.from(sel.options || sel.querySelectorAll('option'));
        if (opts.length < 10) continue; // Provinsi di Indonesia pasti > 30 opsi
        const texts = opts.map(o => (o.label || o.text || o.innerText || '').toUpperCase());
        if (texts.includes('ACEH') || texts.includes('KEPULAUAN RIAU') || texts.includes('JAWA BARAT')) {
            return sel;
        }
    }
    return null;
}

/**
 * Mencari elemen dropdown Wilayah: Provinsi, Kabupaten/Kota, Kecamatan, Kelurahan/Desa
 */
function findWilayahSelectElements() {
    const allSelects = Array.from(document.querySelectorAll('select'));
    console.log(`[TPQ-EMIS Bridge] [Wilayah Scanner] Total elemen <select> di halaman: ${allSelects.length}`);

    const selProvinsi = findProvinsiSelect(allSelects);
    let selKabupaten = null;
    let selKecamatan = null;
    let selDesa = null;

    if (selProvinsi) {
        const provIdx = allSelects.indexOf(selProvinsi);
        console.log(`[TPQ-EMIS Bridge] [Wilayah Scanner] ✓ Ditemukan dropdown Provinsi pada index [${provIdx}] dengan ${selProvinsi.options.length} opsi`);

        // Di form alamat EMIS, 3 dropdown tepat setelah Provinsi secara berurutan adalah:
        // [provIdx + 1] -> Kabupaten/Kota
        // [provIdx + 2] -> Kecamatan
        // [provIdx + 3] -> Kelurahan/Desa
        if (allSelects[provIdx + 1]) selKabupaten = allSelects[provIdx + 1];
        if (allSelects[provIdx + 2]) selKecamatan = allSelects[provIdx + 2];
        if (allSelects[provIdx + 3]) selDesa = allSelects[provIdx + 3];
    } else {
        console.log('[TPQ-EMIS Bridge] [Wilayah Scanner] ⚠️ Dropdown Provinsi belum terdeteksi dari daftar opsi.');
    }

    // Sebagai pencocokan cadangan/verifikasi, cek konteks teks label
    if (!selKabupaten || !selKecamatan || !selDesa) {
        for (const sel of allSelects) {
            if (sel === selProvinsi) continue;
            const lbl = getFieldLabelText(sel);
            if (!selKabupaten && (lbl.includes('KABUPATEN') || lbl.includes('KAB/KOTA') || lbl.includes('KOTA'))) {
                selKabupaten = sel;
            } else if (!selKecamatan && lbl.includes('KECAMATAN')) {
                selKecamatan = sel;
            } else if (!selDesa && (lbl.includes('KELURAHAN') || lbl.includes('DESA') || lbl.includes('KEL/DESA'))) {
                selDesa = sel;
            }
        }
    }

    console.log('[TPQ-EMIS Bridge] [Wilayah Scanner] Hasil deteksi dropdown:', {
        provinsi: !!selProvinsi,
        kabupaten: !!selKabupaten,
        kecamatan: !!selKecamatan,
        desa: !!selDesa
    });

    return { selProvinsi, selKabupaten, selKecamatan, selDesa };
}

/**
 * Mencari elemen <option> yang cocok pada dropdown wilayah EMIS
 */
function findMatchingWilayahOption(selectEl, targetName, isKabupaten = false) {
    if (!selectEl || !targetName) return null;
    const cleanTarget = cleanWilayahName(targetName);
    if (!cleanTarget) return null;

    const rawTargetUpper = String(targetName).trim().toUpperCase();
    const isTargetKota = rawTargetUpper.includes('KOTA');

    const options = Array.from(selectEl.options || selectEl.querySelectorAll('option'));
    // Filter opsi kosong / placeholder
    const validOptions = options.filter(o => {
        const val = String(o.value ?? '').trim();
        const lbl = String(o.label || o.text || o.innerText || '').trim();
        return val !== '' && lbl !== '';
    });

    if (validOptions.length === 0) return null;

    // 1. Coba exact match pada label/text asli
    let found = validOptions.find(o => {
        const lbl = String(o.label || o.text || o.innerText || '').trim().toUpperCase();
        return lbl === rawTargetUpper;
    });
    if (found) return found;

    // 2. Coba exact match pada cleaned name
    const cleanedMatches = validOptions.filter(o => {
        const lbl = String(o.label || o.text || o.innerText || '').trim();
        return cleanWilayahName(lbl) === cleanTarget;
    });

    if (cleanedMatches.length === 1) {
        return cleanedMatches[0];
    } else if (cleanedMatches.length > 1) {
        // Jika ada KAB. X dan KOTA X
        if (isKabupaten) {
            if (isTargetKota) {
                const kotaMatch = cleanedMatches.find(o => (o.label || o.text || '').toUpperCase().includes('KOTA'));
                if (kotaMatch) return kotaMatch;
            } else {
                const kabMatch = cleanedMatches.find(o => (o.label || o.text || '').toUpperCase().includes('KAB'));
                if (kabMatch) return kabMatch;
            }
        }
        return cleanedMatches[0];
    }

    // 3. Coba substring match jika panjang string target minimal 3 karakter
    if (cleanTarget.length >= 3) {
        found = validOptions.find(o => {
            const lbl = String(o.label || o.text || o.innerText || '').trim();
            const cleanLbl = cleanWilayahName(lbl);
            return cleanLbl && (cleanLbl.includes(cleanTarget) || cleanTarget.includes(cleanLbl));
        });
    }

    return found || null;
}

/**
 * Polling menunggu opsi dropdown terisi dari API backend EMIS (menggantikan opsi kosong tunggal)
 * Mendukung fungsi getter dinamis agar selalu memeriksa elemen live DOM terbaru
 */
function waitForSelectOptions(getSelectFn, timeoutMs = 9500, pollIntervalMs = 300) {
    return new Promise((resolve) => {
        const startTime = Date.now();
        let checks = 0;
        const interval = setInterval(() => {
            checks++;
            let selectEl = null;
            try {
                selectEl = typeof getSelectFn === 'function' ? getSelectFn() : getSelectFn;
            } catch (_) {}

            if (selectEl) {
                const opts = Array.from(selectEl.options || selectEl.querySelectorAll('option'));
                const validCount = opts.filter(o => {
                    const val = String(o.value ?? '').trim();
                    const lbl = String(o.label || o.text || o.innerText || '').trim();
                    return val !== '' && lbl !== '';
                }).length;

                if (validCount > 1) {
                    clearInterval(interval);
                    console.log(`[TPQ-EMIS Bridge] Opsi dropdown terdeteksi terisi (${validCount} opsi) setelah ${checks * pollIntervalMs}ms`);
                    return resolve(selectEl);
                }
            }

            if (Date.now() - startTime >= timeoutMs) {
                clearInterval(interval);
                let finalEl = null;
                try {
                    finalEl = typeof getSelectFn === 'function' ? getSelectFn() : getSelectFn;
                } catch (_) {}
                console.log(`[TPQ-EMIS Bridge] Timeout menunggu opsi dropdown setelah ${timeoutMs}ms.`);
                resolve(finalEl || null);
            }
        }, pollIntervalMs);
    });
}

/**
 * Memperbarui teks status indikator wilayah di popup Step 3
 */
function updateWilayahStatusUI(field, text, color = '#198754') {
    const el = document.getElementById(`tpq-status-${field}`);
    if (el) {
        el.innerText = text;
        el.style.color = color;
        el.style.fontWeight = 'bold';
    }

    const tracker = window.__tpqStep3FieldTracker;
    const banner = document.getElementById('tpq-step3-status-banner');
    if (tracker && banner) {
        const idx = tracker.fields.findIndex(f => f.key === field);
        if (idx !== -1) {
            const total = tracker.fields.length;
            const item = tracker.fields[idx];
            const valText = item.value ? `: <strong>${escapeHtml(item.value)}</strong>` : '';
            if (text.includes('✓')) {
                tracker.doneSet.add(field);
                const doneCount = tracker.doneSet.size;
                banner.innerHTML = `<span>${doneCount >= total ? '✅' : '⚡'}</span> <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">(${doneCount}/${total}) ${escapeHtml(item.label)}${valText} ✓</span>`;
                banner.style.background = doneCount >= total ? '#dcfce7' : '#eff6ff';
                banner.style.color = doneCount >= total ? '#15803d' : '#1d4ed8';
            } else if (text.includes('⚠️')) {
                banner.innerHTML = `<span>⚠️</span> <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">(${idx + 1}/${total}) Gagal ${escapeHtml(item.label)}${valText}</span>`;
                banner.style.background = '#fee2e2';
                banner.style.color = '#b91c1c';
            } else if (text.includes('⏳')) {
                banner.innerHTML = `<span>⚡</span> <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">(${idx + 1}/${total}) ${escapeHtml(item.label)}${valText}...</span>`;
                banner.style.background = '#eff6ff';
                banner.style.color = '#1d4ed8';
            }
        }
    }
}

/**
 * Otomasi Update Berantai (Cascading) Dropdown Wilayah di EMIS:
 * Provinsi -> Kabupaten/Kota -> Kecamatan -> Kelurahan/Desa
 * Dijalankan di akhir setelah field teks dan mobilitas selesai dikunci.
 */
async function startCascadingWilayahUpdate(santriData) {
    if (!santriData) return;
    if (isWilayahCascadeRunning) {
        console.log('[TPQ-EMIS Bridge] Cascading Wilayah sedang berlangsung, melewati panggilan ganda.');
        return;
    }
    isWilayahCascadeRunning = true;
    console.log('[TPQ-EMIS Bridge] Memulai update berantai (cascading) dropdown Wilayah untuk:', santriData.nama_santri);

    try {
        const targetProvinsi = santriData.ayah_provinsi || 'KEPULAUAN RIAU';
        const targetKabupaten = santriData.ayah_kabupaten_kota || 'BINTAN';
        const targetKecamatan = santriData.ayah_kecamatan || '';
        const targetDesa = santriData.ayah_kelurahan_desa || '';

        console.log('[TPQ-EMIS Bridge] Target Wilayah:', {
            provinsi: targetProvinsi,
            kabupaten: targetKabupaten,
            kecamatan: targetKecamatan,
            desa: targetDesa
        });

        const badgeOverall = document.getElementById('tpq-status-wilayah-badge');
        if (badgeOverall) {
            badgeOverall.innerText = 'Memproses...';
            badgeOverall.style.background = '#fef3c7';
            badgeOverall.style.color = '#92400e';
        }

        // ==========================================
        // TAHAP 1: PROVINSI
        // ==========================================
        updateWilayahStatusUI('provinsi', '[⏳ Memilih...]', '#0d6efd');
        let { selProvinsi, selKabupaten, selKecamatan, selDesa } = findWilayahSelectElements();

        if (!selProvinsi) {
            console.log('[TPQ-EMIS Bridge] Menunggu elemen dropdown Provinsi muncul di DOM...');
            await new Promise(r => setTimeout(r, 800));
            ({ selProvinsi, selKabupaten, selKecamatan, selDesa } = findWilayahSelectElements());
        }

        if (!selProvinsi) {
            console.log('[TPQ-EMIS Bridge] ⚠️ Elemen dropdown Provinsi tidak ditemukan di layar!');
            updateWilayahStatusUI('provinsi', '[⚠️ Tak Ditemukan]', '#dc3545');
            isWilayahCascadeRunning = false;
            return;
        }

        const optProvinsi = findMatchingWilayahOption(selProvinsi, targetProvinsi, false);
        if (optProvinsi) {
            console.log(`[TPQ-EMIS Bridge] ✓ Memilih Provinsi: "${optProvinsi.label || optProvinsi.text}" (value: ${optProvinsi.value})`);
            setNativeSelectValue(selProvinsi, optProvinsi.value);
            updateWilayahStatusUI('provinsi', '[✓ Terpilih]', '#198754');
        } else {
            console.log(`[TPQ-EMIS Bridge] ⚠️ Opsi Provinsi tidak cocok untuk: "${targetProvinsi}"`);
            updateWilayahStatusUI('provinsi', '[⚠️ Pilih manual]', '#dc3545');
            isWilayahCascadeRunning = false;
            return;
        }

        // ==========================================
        // TAHAP 2: KABUPATEN / KOTA
        // ==========================================
        if (!targetKabupaten) {
            console.log('[TPQ-EMIS Bridge] Data Kabupaten kosong, cascade selesai.');
            isWilayahCascadeRunning = false;
            return;
        }

        updateWilayahStatusUI('kabupaten', '[⏳ Menunggu API...]', '#0d6efd');
        console.log(`[TPQ-EMIS Bridge] Menunggu opsi Kabupaten dimuat oleh EMIS untuk Provinsi: ${optProvinsi.label || optProvinsi.text}...`);

        // Smart polling dengan dynamic element getter
        const liveKabSelect = await waitForSelectOptions(() => findWilayahSelectElements().selKabupaten, 9500, 300);
        if (!liveKabSelect || liveKabSelect.options.length <= 1) {
            console.log('[TPQ-EMIS Bridge] ⚠️ Timeout menunggu opsi Kabupaten dari EMIS API.');
            updateWilayahStatusUI('kabupaten', '[⚠️ Timeout API]', '#d97706');
            isWilayahCascadeRunning = false;
            return;
        }

        const optKabupaten = findMatchingWilayahOption(liveKabSelect, targetKabupaten, true);
        if (optKabupaten) {
            console.log(`[TPQ-EMIS Bridge] ✓ Memilih Kabupaten: "${optKabupaten.label || optKabupaten.text}" (value: ${optKabupaten.value})`);
            setNativeSelectValue(liveKabSelect, optKabupaten.value);
            updateWilayahStatusUI('kabupaten', '[✓ Terpilih]', '#198754');
        } else {
            console.log(`[TPQ-EMIS Bridge] ⚠️ Opsi Kabupaten tidak cocok untuk: "${targetKabupaten}"`);
            updateWilayahStatusUI('kabupaten', '[⚠️ Pilih manual]', '#dc3545');
            isWilayahCascadeRunning = false;
            return;
        }

        // ==========================================
        // TAHAP 3: KECAMATAN
        // ==========================================
        if (!targetKecamatan) {
            console.log('[TPQ-EMIS Bridge] Data Kecamatan kosong, cascade selesai.');
            isWilayahCascadeRunning = false;
            return;
        }

        updateWilayahStatusUI('kecamatan', '[⏳ Menunggu API...]', '#0d6efd');
        console.log(`[TPQ-EMIS Bridge] Menunggu opsi Kecamatan dimuat oleh EMIS untuk Kabupaten: ${optKabupaten.label || optKabupaten.text}...`);

        const liveKecSelect = await waitForSelectOptions(() => findWilayahSelectElements().selKecamatan, 9500, 300);
        if (!liveKecSelect || liveKecSelect.options.length <= 1) {
            console.log('[TPQ-EMIS Bridge] ⚠️ Timeout menunggu opsi Kecamatan dari EMIS API.');
            updateWilayahStatusUI('kecamatan', '[⚠️ Timeout API]', '#d97706');
            isWilayahCascadeRunning = false;
            return;
        }

        const optKecamatan = findMatchingWilayahOption(liveKecSelect, targetKecamatan, false);
        if (optKecamatan) {
            console.log(`[TPQ-EMIS Bridge] ✓ Memilih Kecamatan: "${optKecamatan.label || optKecamatan.text}" (value: ${optKecamatan.value})`);
            setNativeSelectValue(liveKecSelect, optKecamatan.value);
            updateWilayahStatusUI('kecamatan', '[✓ Terpilih]', '#198754');
        } else {
            console.log(`[TPQ-EMIS Bridge] ⚠️ Opsi Kecamatan tidak cocok untuk: "${targetKecamatan}"`);
            updateWilayahStatusUI('kecamatan', '[⚠️ Pilih manual]', '#dc3545');
            isWilayahCascadeRunning = false;
            return;
        }

        // ==========================================
        // TAHAP 4: KELURAHAN / DESA
        // ==========================================
        if (!targetDesa) {
            console.log('[TPQ-EMIS Bridge] Data Desa kosong, cascade selesai.');
            isWilayahCascadeRunning = false;
            return;
        }

        updateWilayahStatusUI('desa', '[⏳ Menunggu API...]', '#0d6efd');
        console.log(`[TPQ-EMIS Bridge] Menunggu opsi Desa dimuat oleh EMIS untuk Kecamatan: ${optKecamatan.label || optKecamatan.text}...`);

        const liveDesaSelect = await waitForSelectOptions(() => findWilayahSelectElements().selDesa, 9500, 300);
        if (!liveDesaSelect || liveDesaSelect.options.length <= 1) {
            console.log('[TPQ-EMIS Bridge] ⚠️ Timeout menunggu opsi Desa dari EMIS API.');
            updateWilayahStatusUI('desa', '[⚠️ Timeout API]', '#d97706');
            isWilayahCascadeRunning = false;
            return;
        }

        const optDesa = findMatchingWilayahOption(liveDesaSelect, targetDesa, false);
        if (optDesa) {
            console.log(`[TPQ-EMIS Bridge] ✓ Memilih Desa/Kelurahan: "${optDesa.label || optDesa.text}" (value: ${optDesa.value})`);
            setNativeSelectValue(liveDesaSelect, optDesa.value);
            updateWilayahStatusUI('desa', '[✓ Terpilih]', '#198754');
            return true;
        } else {
            console.log(`[TPQ-EMIS Bridge] ⚠️ Opsi Desa tidak cocok untuk: "${targetDesa}"`);
            updateWilayahStatusUI('desa', '[⚠️ Pilih manual]', '#dc3545');
            return false;
        }

    } catch (err) {
        console.error('[TPQ-EMIS Bridge] Kesalahan saat cascading Wilayah:', err);
        return false;
    } finally {
        isWilayahCascadeRunning = false;
    }
}

/**
 * Menandai santri selesai di tab DATA ALAMAT dan mengirim sinyal sukses ke TPQ Smart
 */
function finishUpdateAlamatStep3(data) {
    if (!data) return;
    try {
        console.log('[TPQ-EMIS Bridge] Mengirim notifikasi UPDATE_ALAMAT_SUCCESS_NOTIFY untuk:', data.nama_santri);
        chrome.runtime.sendMessage({
            action: 'UPDATE_ALAMAT_SUCCESS_NOTIFY',
            id_santri: data.id_santri
        });
        isStep2Executing = false;
        isStep2Done = false;
        isStep3Executing = false;
        isStep3Done = true;
        isWilayahCascadeRunning = false;
        chrome.storage.local.remove(['pendingUpdateAlamatData', 'currentTask', 'waitingForUpdateStep2', 'waitingForUpdateStep3']);
        
        const btnQuick = document.getElementById('tpq-btn-quick-selesai-step3');
        if (btnQuick) {
            btnQuick.innerText = '✅ Selesai!';
            btnQuick.style.background = '#059669';
            btnQuick.disabled = true;
        }
        const btnSelesai = document.getElementById('tpq-btn-selesai-alamat');
        if (btnSelesai) {
            btnSelesai.innerText = '✅ Ditandai Selesai!';
            btnSelesai.style.background = '#059669';
            btnSelesai.disabled = true;
        }
        const badgeOverall = document.getElementById('tpq-status-wilayah-badge');
        if (badgeOverall) {
            badgeOverall.innerText = '✅ Selesai';
            badgeOverall.style.background = '#dcfce7';
            badgeOverall.style.color = '#15803d';
        }

        const p3 = document.getElementById('tpq-update-alamat-step3-popup') || document.getElementById('tpq-update-alamat-step2-popup');
        setTimeout(() => {
            if (p3) p3.remove();
        }, 1500);
    } catch (err) {
        console.error('[TPQ-EMIS Bridge] Gagal kirim notifikasi selesai:', err);
    }
}

/**
 * Auto-Simpan untuk Step 3 (Tab DATA ALAMAT):
 * Menunggu tombol Simpan aktif (berubah warna menjadi HIJAU), klik Simpan, klik Ya pada dialog konfirmasi, klik Tutup pada dialog sukses,
 * lalu memicu callback untuk menandai santri Selesai di TPQ Smart.
 */
function triggerAutoSimpanStep3(santriData, onFinishCallback) {
    console.log('[TPQ-EMIS Bridge] Memulai auto-simpan Step 3 (DATA ALAMAT)...');
    const badgeOverall = document.getElementById('tpq-status-wilayah-badge');
    const step3Banner = document.getElementById('tpq-step3-status-banner');
    const updateStatus = (msg, bg = '#eff6ff', color = '#1d4ed8') => {
        if (badgeOverall) {
            badgeOverall.innerText = msg;
            badgeOverall.style.background = bg;
            badgeOverall.style.color = color;
        }
        if (step3Banner) {
            const icon = bg === '#dcfce7' ? '✅' : (bg === '#fee2e2' || bg === '#fff1f2' ? '⚠️' : '⚡');
            step3Banner.innerHTML = `<span>${icon}</span> <span>${escapeHtml(msg)}</span>`;
            step3Banner.style.background = bg;
            step3Banner.style.color = color;
        }
    };

    updateStatus('Menunggu Simpan...', '#fef3c7', '#b45309');

    let attempts = 0;
    const maxAttempts = 30; // 30 x 300ms = 9 detik

    const checkTimer = setInterval(() => {
        attempts++;
        const simpanObj = findEmisSimpanButton({ preferGreen: true, preferLast: true });

        // Tombol hanya boleh diklik jika BENAR-BENAR HIJAU dan aktif
        if (simpanObj && !simpanObj.isDisabled && simpanObj.isGreen) {
            clearInterval(checkTimer);
            updateStatus('Klik Simpan...', '#fef3c7', '#b45309');

            setTimeout(() => {
                try {
                    simpanObj.element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    triggerNativeClick(simpanObj.element);
                } catch (e) {
                    console.error('[TPQ-EMIS Bridge] Gagal klik tombol Simpan Step 3:', e);
                }

                handleEmisConfirmAndSuccessModal({
                    stepName: 'Data Alamat',
                    skipConfirm: true,
                    onStatusUpdate: (txt, type) => {
                        updateStatus(txt.length > 20 ? txt.substring(0, 18) + '...' : txt, type === 'success' ? '#dcfce7' : '#eff6ff', type === 'success' ? '#15803d' : '#1d4ed8');
                    },
                    onSuccess: () => {
                        updateStatus('✓ Tersimpan!', '#dcfce7', '#15803d');
                        setTimeout(() => {
                            if (typeof onFinishCallback === 'function') {
                                onFinishCallback();
                            }
                        }, 500);
                    },
                    onTimeout: () => {
                        console.warn('[TPQ-EMIS Bridge] Timeout menunggu dialog konfirmasi/sukses Step 3.');
                        updateStatus('⚠️ Simpan Manual (Popup Tutup tidak terdeteksi)', '#fee2e2', '#b91c1c');
                        if (typeof window.__tpqToggleStep3Popup === 'function') window.__tpqToggleStep3Popup(true);
                    }
                });
            }, 700);
        } else if (attempts >= maxAttempts) {
            clearInterval(checkTimer);
            console.warn('[TPQ-EMIS Bridge] Tombol Simpan Step 3 belum aktif (masih abu-abu) atau tidak ditemukan.');
            updateStatus('⚠️ Tombol Simpan belum aktif — cek kolom di bawah lalu simpan manual', '#fee2e2', '#b91c1c');
            if (typeof window.__tpqToggleStep3Popup === 'function') window.__tpqToggleStep3Popup(true);
            // JANGAN panggil onFinishCallback jika tombol belum aktif!
        }
    }, 300);
}

/**
 * Eksekusi Step 3: Tab DATA ALAMAT (Alur Sekuensial Kolom-demi-Kolom dari Atas ke Bawah)
 * Menggunakan Single-Flight Mutex Lock agar tidak pernah berjalan ganda.
 */
async function executeStep3DataAlamat(santriData) {
    if (!santriData) return;
    const now = Date.now();
    if (isStep3Executing || isStep3Done || (now - lastStep3ExecutionTime < 2500)) {
        console.log('[TPQ-EMIS Bridge] Step 3 sedang aktif atau baru saja dijalankan (< 2.5s), mengabaikan panggilan ganda.');
        return;
    }

    // Pastikan kita BENAR-BENAR berada di Tab DATA ALAMAT (Stage 3)!
    if (detectUpdateAlamatStage() !== 3) {
        console.log('[TPQ-EMIS Bridge] executeStep3DataAlamat dipanggil namun tab DATA ALAMAT belum aktif. Memindahkan tab...');
        switchToDataAlamatTab(() => {
            executeStep3DataAlamat(santriData);
        });
        return;
    }

    isStep3Executing = true;
    lastStep3ExecutionTime = now;
    console.log('[TPQ-EMIS Bridge] >>> Memulai alur sekuensial tunggal Step 3 Update Alamat untuk:', santriData.nama_santri);

    // Pastikan popup Step 1 & Step 2 tertutup
    const p1 = document.getElementById('tpq-update-alamat-step1-popup');
    if (p1) p1.remove();
    const p2 = document.getElementById('tpq-update-santri-step2-popup');
    if (p2) p2.remove();

    showUpdateAlamatStep3Popup(santriData);

    const alamatRingkas = [
        santriData.ayah_alamat || '',
        santriData.ayah_rt ? `RT ${santriData.ayah_rt}` : '',
        santriData.ayah_rw ? `RW ${santriData.ayah_rw}` : ''
    ].filter(Boolean).join(' ') || '-';

    const activeFields = [
        { key: 'status-rumah', label: 'Status Rumah', value: santriData.ayah_status_kepemilikan_rumah || 'Milik Sendiri' },
        { key: 'provinsi', label: 'Provinsi', value: santriData.ayah_provinsi || 'KEPULAUAN RIAU' },
        { key: 'kabupaten', label: 'Kab/Kota', value: santriData.ayah_kabupaten_kota || 'BINTAN' },
        { key: 'kecamatan', label: 'Kecamatan', value: santriData.ayah_kecamatan || '-' },
        { key: 'desa', label: 'Kel/Desa', value: santriData.ayah_kelurahan_desa || '-' },
        { key: 'alamat', label: 'Alamat', value: alamatRingkas },
        ...(santriData.ayah_kode_pos ? [{ key: 'kodepos', label: 'Kode Pos', value: santriData.ayah_kode_pos }] : []),
        { key: 'status-mukim', label: 'Status Mukim', value: santriData.santri_status_mukim || 'Tidak Mukim' },
        { key: 'tempat-tinggal', label: 'Tempat Tinggal', value: santriData.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung' },
        { key: 'jarak', label: 'Jarak', value: santriData.santri_jarak_tempuh || santriData.santri_jarak || 'Kurang dari 5 km' },
        { key: 'waktu', label: 'Waktu Tempuh', value: santriData.santri_waktu_tempuh || '1-10 menit' },
        { key: 'transportasi', label: 'Transportasi', value: santriData.santri_transportasi || 'Jalan Kaki' },
        ...(santriData.santri_titik_koordinat ? [{ key: 'koordinat', label: 'Koordinat', value: santriData.santri_titik_koordinat }] : [])
    ];
    const totalFields = activeFields.length;
    window.__tpqStep3FieldTracker = { fields: activeFields, doneSet: new Set() };

    const updateProgress = (text, bg = '#eff6ff', color = '#1d4ed8', autoExpand = false) => {
        const badge = document.getElementById('tpq-status-wilayah-badge');
        if (badge) {
            badge.innerText = text;
            badge.style.background = bg;
            badge.style.color = color;
        }
        const banner = document.getElementById('tpq-step3-status-banner');
        if (banner) {
            const icon = bg === '#dcfce7' ? '✅' : (bg === '#fee2e2' || bg === '#fff1f2' ? '⚠️' : '⚡');
            banner.innerHTML = `<span>${icon}</span> <span>${escapeHtml(text)}</span>`;
            banner.style.background = bg;
            banner.style.color = color;
        }
        if (autoExpand && typeof window.__tpqToggleStep3Popup === 'function') {
            window.__tpqToggleStep3Popup(true);
        }
    };

    // Beri jeda 250ms agar DOM tab DATA ALAMAT siap
    await new Promise(r => setTimeout(r, 250));

    // ========================================================
    // TAHAP 1: AYAH KANDUNG - STATUS KEPEMILIKAN RUMAH
    // Wajib terisi terlebih dahulu, JANGAN lanjut ke step berikutnya jika belum terisi!
    // ========================================================
    updateProgress(`(1/${totalFields}) Mengisi Status Rumah...`, '#fef3c7', '#92400e');
    const isRumahOk = await waitAndSelectStatusRumah(santriData.ayah_status_kepemilikan_rumah || 'Milik Sendiri', 6000);
    if (!isRumahOk) {
        console.warn('[TPQ-EMIS Bridge] ⛔ Status Kepemilikan Rumah belum terisi! Menghentikan alur otomatis sesuai instruksi.');
        updateProgress(`(1/${totalFields}) Gagal: Isi Status Rumah manual!`, '#fee2e2', '#b91c1c', true);
        isStep3Executing = false;
        window.__tpqStep3FieldTracker = null;
        return; // BERHENTI! JANGAN LANJUTKAN KE STEP BERIKUTNYA!
    }
    await new Promise(r => setTimeout(r, 150));

    // ========================================================
    // TAHAP 2: AYAH KANDUNG - CASCADING WILAYAH (PROVINSI -> KAB -> KEC -> DESA)
    // ========================================================
    updateProgress(`(2/${totalFields}) Mengisi Wilayah Ayah...`, '#eff6ff', '#1d4ed8');
    await startCascadingWilayahUpdate(santriData);
    await new Promise(r => setTimeout(r, 100));

    // ========================================================
    // TAHAP 3: AYAH KANDUNG - FIELD TEKS (RT, RW, ALAMAT, KODEPOS)
    // ========================================================
    updateProgress(`(6/${totalFields}) Mengisi Alamat & Kode Pos...`, '#eff6ff', '#1d4ed8');
    fillUpdateAlamatAyahTextFields(santriData);
    await new Promise(r => setTimeout(r, 100));

    // ========================================================
    // TAHAP 4: IBU KANDUNG & WALI
    // DILEWATI: Checkbox Ibu sudah tercentang & disable otomatis dari Data Orang Tua.
    // ========================================================

    // ========================================================
    // TAHAP 5A: SANTRI - STATUS MUKIM
    // Wajib terisi terlebih dahulu; jika gagal BERHENTI, jika sukses lanjut ke Status Tempat Tinggal!
    // ========================================================
    const mukimIdx = activeFields.findIndex(f => f.key === 'status-mukim') + 1;
    updateProgress(`(${mukimIdx}/${totalFields}) Mengisi Status Mukim...`, '#fef3c7', '#92400e');
    const isMukimOk = await waitAndSelectStatusMukim(santriData.santri_status_mukim || 'Tidak Mukim', 4000);
    if (!isMukimOk) {
        console.warn('[TPQ-EMIS Bridge] ⛔ Status Mukim belum terisi! Menghentikan alur otomatis sesuai instruksi.');
        updateProgress(`(${mukimIdx}/${totalFields}) Gagal: Isi Status Mukim manual!`, '#fee2e2', '#b91c1c', true);
        isStep3Executing = false;
        window.__tpqStep3FieldTracker = null;
        return; // BERHENTI! JANGAN LANJUTKAN KE STATUS TEMPAT TINGGAL!
    }
    await new Promise(r => setTimeout(r, 150));

    // ========================================================
    // TAHAP 5B: SANTRI - STATUS TEMPAT TINGGAL
    // Wajib terisi sebelum lanjut ke mobilitas santri!
    // ========================================================
    const tempatIdx = activeFields.findIndex(f => f.key === 'tempat-tinggal') + 1;
    updateProgress(`(${tempatIdx}/${totalFields}) Mengisi Tempat Tinggal...`, '#fef3c7', '#92400e');
    const isTempatOk = await waitAndSelectStatusTempatTinggal(santriData.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung', 5000);
    if (!isTempatOk) {
        console.warn('[TPQ-EMIS Bridge] ⛔ Status Tempat Tinggal belum terisi! Menghentikan alur otomatis sesuai instruksi.');
        updateProgress(`(${tempatIdx}/${totalFields}) Gagal: Isi Tempat Tinggal manual!`, '#fee2e2', '#b91c1c', true);
        isStep3Executing = false;
        window.__tpqStep3FieldTracker = null;
        return; // BERHENTI! JANGAN LANJUTKAN KE STEP BERIKUTNYA!
    }
    await new Promise(r => setTimeout(r, 150));

    // ========================================================
    // TAHAP 6: SANTRI - MOBILITAS (JARAK, TRANSPORTASI, WAKTU) & KOORDINAT
    // ========================================================
    const jarakIdx = activeFields.findIndex(f => f.key === 'jarak') + 1;
    updateProgress(`(${jarakIdx}/${totalFields}) Mengisi Mobilitas & Koordinat...`, '#eff6ff', '#1d4ed8');
    const isMobilitasOk = await waitAndSelectMobilitas(santriData, 6000);
    if (!isMobilitasOk) {
        console.warn('[TPQ-EMIS Bridge] ⚠️ Sebagian kolom Mobilitas/Koordinat belum lengkap! Membuka detail untuk review...');
        updateProgress(`(${jarakIdx}/${totalFields}) Cek Mobilitas / Koordinat manual!`, '#fee2e2', '#b91c1c', true);
        isStep3Executing = false;
        window.__tpqStep3FieldTracker = null;
        return;
    }
    await new Promise(r => setTimeout(r, 150));

    // Verifikasi akhir memastikan Status Kepemilikan Rumah & Status Tempat Tinggal benar-benar terpilih
    const finalSelRumah = findStatusKepemilikanRumahSelect();
    if (finalSelRumah && !isStatusRumahTargetSelected(finalSelRumah, santriData.ayah_status_kepemilikan_rumah || 'Milik Sendiri')) {
        console.log('[TPQ-EMIS Bridge] Verifikasi akhir: Menyetel ulang Status Kepemilikan Rumah...');
        selectStatusKepemilikanRumahEmis(santriData.ayah_status_kepemilikan_rumah || 'Milik Sendiri');
    }
    const finalSelTempat = findStatusTempatTinggalSelect();
    if (finalSelTempat && !isStatusTempatTinggalTargetSelected(finalSelTempat, santriData.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung')) {
        console.log('[TPQ-EMIS Bridge] Verifikasi akhir: Menyetel ulang Status Tempat Tinggal...');
        await waitAndSelectStatusTempatTinggal(santriData.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung', 3000);
    }
    await new Promise(r => setTimeout(r, 100));

    // Selesai seluruh pengisian kolom per kolom
    isStep3Executing = false;
    isStep3Done = true;
    window.__tpqStep3FieldTracker = null;
    console.log('[TPQ-EMIS Bridge] ✓ Seluruh kolom Step 3 telah terisi secara sekuensial!');
    updateProgress(`(${totalFields}/${totalFields}) Semua kolom lengkap!`, '#dcfce7', '#15803d');

    // ========================================================
    // TAHAP 7: SIMPAN OTOMATIS (JIKA MODE FULL OTOMATIS)
    // ========================================================
    if (currentAutoMode === 'FULL') {
        setTimeout(() => {
            triggerAutoSimpanStep3(santriData, () => {
                finishUpdateAlamatStep3(santriData);
            });
        }, 200);
    }
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
        width: 370px;
        max-width: 95vw;
        background: #ffffff;
        border: 2px solid #0d6efd;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        color: #212529;
        overflow: hidden;
        transition: width 0.25s ease;
    `;

    const hdrBtnStyle = 'flex: 1; height: 26px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; padding: 0 8px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer; white-space: nowrap;';

    div.innerHTML = `
        <div id="tpq-update-step3-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
                <div style="display: flex; align-items: center; gap: 5px; flex: 1; min-width: 0;" title="${escapeHtml(data.nama_santri)}">
                    <span style="font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
                    <span style="font-size: 9.5px; background: rgba(255,255,255,0.25); padding: 1px 5px; border-radius: 3px; font-weight: normal; flex-shrink: 0;">v1.6.1</span>
                </div>
                <span id="tpq-close-update-step3" style="cursor: pointer; font-size: 18px; line-height: 1; padding: 0 4px; opacity: 0.9; flex-shrink: 0;" title="Tutup & batalkan antrean">&times;</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <button id="tpq-btn-mode-step3" style="${hdrBtnStyle} background: ${currentAutoMode === 'FULL' ? '#059669' : '#d97706'}; color: #fff; border: 1px solid rgba(255,255,255,0.4);" title="${currentAutoMode === 'FULL' ? 'Mode Full Otomatis aktif (klik untuk ganti ke Semi-Otomatis)' : 'Mode Semi-Otomatis aktif (klik untuk ganti ke Full Otomatis)'}">
                    ${currentAutoMode === 'FULL' ? '⚡ FULL AUTO' : '✋ SEMI AUTO'}
                </button>
                <button id="tpq-btn-quick-selesai-step3" style="${hdrBtnStyle} background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4);" title="Tandai update selesai & simpan ke TPQ Smart">
                    ✓ Selesai
                </button>
                <button id="tpq-toggle-step3" style="${hdrBtnStyle} background: rgba(255,255,255,0.22); border: 1px solid rgba(255,255,255,0.45); color: #fff;" title="Buka detail data alamat & koordinat">
                    ▼ Detail
                </button>
            </div>
        </div>

        <!-- Banner Status Real-Time Step 3 (Selalu Terlihat saat Tertutup maupun Terbuka) -->
        <div id="tpq-step3-status-banner" style="padding: 7px 12px; background: #eff6ff; border-bottom: 1px solid #bfdbfe; font-size: 11.5px; color: #1d4ed8; font-weight: 500; display: flex; align-items: center; gap: 6px;">
            <span>⚡</span> <span>Mempersiapkan pengisian Tab DATA ALAMAT...</span>
        </div>

        <!-- Bagian Tersembunyi (Monitoring Wilayah, Isian Otomatis, & Tombol Aksi): Dibuka via ▼ Detail atau Otomatis saat Gagal -->
        <div id="tpq-update-step3-body" style="display: none; padding: 10px 12px; max-height: 68vh; overflow-y: auto; background: #ffffff;">
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 10px; font-size: 12px; margin-bottom: 8px;">
                <div style="font-size: 11px; font-weight: bold; color: #1d4ed8; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; display: flex; justify-content: space-between; align-items: center;">
                    <span>📍 DATA ALAMAT & WILAYAH DOMISILI:</span>
                    <span id="tpq-status-wilayah-badge" style="font-size: 9.5px; background: #dbeafe; color: #1e40af; padding: 1px 5px; border-radius: 3px; font-weight: bold;">Auto-Cascade</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <!-- 1. Status Kepemilikan Rumah (Ayah) -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #bfdbfe; padding-bottom: 5px; margin-bottom: 3px;">
                        <span style="color: #64748b; font-weight: 500;">1. Status Rumah:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-status-rumah" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>
                            <strong style="color: #166534;">${escapeHtml(data.ayah_status_kepemilikan_rumah || 'Milik Sendiri')}</strong>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_status_kepemilikan_rumah || 'Milik Sendiri')}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Status Kepemilikan Rumah">📋</button>
                        </div>
                    </div>
                    <!-- 2. Provinsi -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">2. Provinsi:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-provinsi" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_provinsi || '-')}</strong>
                            ${data.ayah_provinsi ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_provinsi)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Provinsi">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- 3. Kab / Kota -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">3. Kab/Kota:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-kabupaten" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_kabupaten_kota || '-')}</strong>
                            ${data.ayah_kabupaten_kota ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kabupaten_kota)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Kab/Kota">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- 4. Kecamatan -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">4. Kecamatan:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-kecamatan" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_kecamatan || '-')}</strong>
                            ${data.ayah_kecamatan ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kecamatan)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Kecamatan">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- 5. Kelurahan / Desa -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">5. Kel/Desa:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-desa" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_kelurahan_desa || '-')}</strong>
                            ${data.ayah_kelurahan_desa ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kelurahan_desa)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Kel/Desa">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- Status Mukim -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #bfdbfe; padding-top: 4px; margin-top: 2px;">
                        <span style="color: #64748b;">Status Mukim:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-status-mukim" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>
                            <strong style="color: #166534;">${escapeHtml(data.santri_status_mukim || 'Tidak Mukim')}</strong>
                        </div>
                    </div>
                    <!-- Status Tempat Tinggal -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">Tempat Tinggal:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-tempat-tinggal" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>
                            <strong style="color: #166534; font-size: 11px;">${escapeHtml(data.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung')}</strong>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung')}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Tempat Tinggal">📋</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Terisi Otomatis + Status Real-Time -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; font-size: 12px;">
                <div style="font-size: 10.5px; font-weight: bold; color: #166534; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>✨ DATA TERISI OTOMATIS:</span>
                    <span style="font-size: 9.5px; background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 8px; font-weight: bold;">Real-Time</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <span style="color: #4b5563;">Alamat:</span>
                        <div style="display: flex; align-items: center; gap: 4px; text-align: right; max-width: 72%;">
                            <span id="tpq-status-alamat" style="font-size: 9.5px; color: #64748b; font-weight: bold; white-space: nowrap;">[⏳ Tunggu]</span>
                            <span style="font-weight: 500;">${escapeHtml(data.ayah_alamat || '-')} RT ${escapeHtml(data.ayah_rt || '-')} RW ${escapeHtml(data.ayah_rw || '-')}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_alamat || '')} RT ${escapeHtml(data.ayah_rt || '')} RW ${escapeHtml(data.ayah_rw || '')}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4b5563;">Kode Pos:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            ${data.ayah_kode_pos ? `<span id="tpq-status-kodepos" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>` : ''}
                            <strong>${escapeHtml(data.ayah_kode_pos || '-')}</strong>
                            ${data.ayah_kode_pos ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kode_pos)}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>` : ''}
                        </div>
                    </div>
                    ${data.santri_titik_koordinat ? `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4b5563;">Koordinat:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-koordinat" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>
                            <span style="font-family: monospace; font-size: 11px; font-weight: 600;">${escapeHtml(data.santri_titik_koordinat)}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_titik_koordinat)}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>` : ''}
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #bbf7d0; padding-top: 4px;">
                        <span style="color: #4b5563;">Jarak:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-jarak" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>
                            <span style="font-weight: 500;">${escapeHtml(data.santri_jarak_tempuh || 'Kurang dari 5 km')}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_jarak_tempuh || 'Kurang dari 5 km')}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4b5563;">Waktu Tempuh:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-waktu" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>
                            <span style="font-weight: 500;">${escapeHtml(data.santri_waktu_tempuh || '1-10 menit')}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_waktu_tempuh || '1-10 menit')}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4b5563;">Transportasi:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-transportasi" style="font-size: 9.5px; color: #64748b; font-weight: bold;">[⏳ Tunggu]</span>
                            <span style="font-weight: 500;">${escapeHtml(data.santri_transportasi || 'Jalan Kaki')}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_transportasi || 'Jalan Kaki')}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 6px; margin-bottom: 6px;">
                <button id="tpq-btn-selesai-alamat" style="flex: 1.2; background: #198754; color: #fff; border: none; padding: 7px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    ✓ Selesai di TPQ Smart
                </button>
                <button id="tpq-btn-refill-alamat" style="flex: 1; background: #f8fafc; color: #0d6efd; border: 1px solid #0d6efd; padding: 7px 8px; border-radius: 5px; font-weight: 500; font-size: 11px; cursor: pointer;">
                    ⚡ Isi Ulang
                </button>
            </div>

            <button id="tpq-btn-cancel-update-step3" style="width: 100%; background: transparent; color: #dc3545; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 5px; font-size: 11px; cursor: pointer;">
                Batalkan Santri Ini
            </button>
        </div>
    `;

    document.body.appendChild(div);
    makeDraggable(div, document.getElementById('tpq-update-step3-header'));

    let isStep3Expanded = false;
    const btnToggleStep3 = document.getElementById('tpq-toggle-step3');
    const bodyStep3 = document.getElementById('tpq-update-step3-body');

    function toggleStep3(forceState) {
        isStep3Expanded = typeof forceState === 'boolean' ? forceState : !isStep3Expanded;
        if (isStep3Expanded) {
            div.style.width = '390px';
            bodyStep3.style.display = 'block';
            btnToggleStep3.innerHTML = '▲ Tutup';
            btnToggleStep3.style.background = 'rgba(255,255,255,0.35)';
        } else {
            div.style.width = '370px';
            bodyStep3.style.display = 'none';
            btnToggleStep3.innerHTML = '▼ Detail';
            btnToggleStep3.style.background = 'rgba(255,255,255,0.22)';
        }
    }
    window.__tpqToggleStep3Popup = toggleStep3;

    btnToggleStep3.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleStep3();
    });

    document.getElementById('tpq-close-update-step3').addEventListener('click', () => {
        cancelUpdateAlamatProcess('Proses update data alamat ditutup dan antrean dibersihkan.');
    });

    document.getElementById('tpq-btn-cancel-update-step3').addEventListener('click', () => {
        cancelUpdateAlamatProcess(`Proses update data untuk ${data.nama_santri || 'santri'} telah dibatalkan.`);
    });

    div.querySelectorAll('.tpq-copy-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = btn.getAttribute('data-val');
            if (val) {
                navigator.clipboard.writeText(val);
                const oldContent = btn.innerHTML;
                btn.innerHTML = '✓';
                btn.style.color = '#198754';
                btn.style.borderColor = '#198754';
                setTimeout(() => {
                    btn.innerHTML = oldContent;
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 1200);
            }
        });
    });

    const btnMode3 = document.getElementById('tpq-btn-mode-step3');
    if (btnMode3) {
        btnMode3.addEventListener('click', toggleAutoMode);
    }

    document.getElementById('tpq-btn-refill-alamat').addEventListener('click', () => {
        isStep3Executing = false;
        isStep3Done = false;
        isWilayahCascadeRunning = false;
        lastStep3ExecutionTime = 0;
        executeStep3DataAlamat(data);
    });

    const handleFinishStep3 = () => {
        finishUpdateAlamatStep3(data);
    };

    const btnSelesai = document.getElementById('tpq-btn-selesai-alamat');
    if (btnSelesai && data.id_santri) {
        btnSelesai.addEventListener('click', handleFinishStep3);
    }
    const btnQuickSelesai = document.getElementById('tpq-btn-quick-selesai-step3');
    if (btnQuickSelesai && data.id_santri) {
        btnQuickSelesai.addEventListener('click', handleFinishStep3);
    }
}
