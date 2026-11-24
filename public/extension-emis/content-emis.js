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
                if (step === 1) {
                    // Pantau dan otomatis isi jika user beralih ke Nama & TTL di formulir EMIS
                    checkAndAutoFillStep1Watcher(res.pendingSantriData);
                } else if (step === 2 && res.waitingForStep2) {
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

    // Tutup popup start Step 0 jika sedang ada agar tidak bertumpuk
    const oldStep0 = document.getElementById('tpq-step0-alert');
    if (oldStep0) oldStep0.remove();
    const oldPill = document.getElementById('tpq-reopen-step0-pill');
    if (oldPill) oldPill.remove();

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

    const pollTimer = setInterval(() => {
        attempts++;
        const filled = tryFillNikInput(santriData.nik);
        if (filled || attempts >= maxAttempts) {
            clearInterval(pollTimer);
            if (filled) {
                step1FilledSantriKey = santriKey;
            }
        }
    }, 120);
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
                    <span style="font-size: 9.5px; background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 3px; font-weight: bold;">Manual</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 3px;">
                    <span style="font-size: 15px; font-weight: bold; color: #15803d;">📅 ${escapeHtml(tglLahirLabel)}</span>
                    <button id="tpq-btn-copy-tgl-step1" class="tpq-copy-btn" data-val="${escapeHtml(data.tanggal_lahir || '')}" style="
                        background: #198754; color: #fff; border: none; padding: 3px 8px;
                        border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;
                    ">📋 Salin</button>
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 5px; line-height: 1.35;">
                    Pilih tanggal di atas pada kalender EMIS, lalu klik tombol <strong>SELANJUTNYA</strong>.
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
                    <span>👉 PILIH MANUAL DI EMIS:</span>
                    <span style="font-size: 9.5px; background: #dbeafe; color: #1e40af; padding: 1px 5px; border-radius: 3px; font-weight: bold;">Manual</span>
                </div>

                <!-- 1. Tanggal Masuk -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; border-bottom: 1px dashed #bfdbfe; padding-bottom: 4px;">
                    <span style="color: #334155;">1. <strong>Tgl Masuk:</strong></span>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <strong style="color: #1e40af; font-size: 13.5px;">📅 ${escapeHtml(tglMasukLabel)}</strong>
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

    // Tombol Lanjut Update Alamat (Satu Paket Pendaftaran + Update Alamat)
    const btnLanjutUpdate = document.getElementById('tpq-btn-lanjut-update-alamat');
    if (btnLanjutUpdate && data.id_santri) {
        btnLanjutUpdate.addEventListener('click', () => {
            try {
                chrome.runtime.sendMessage({
                    action: 'FORM_FILLED_NOTIFY',
                    id_santri: data.id_santri
                });
            } catch (_) {}

            chrome.storage.local.set({
                pendingUpdateAlamatData: data,
                currentTask: 'UPDATE_ALAMAT',
                waitingForUpdateStep2: false
            }, () => {
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
 * Eksekusi Step 1 Update Alamat: Pencarian Nama Santri dengan Delay & Pengecekan Daftar Siap
 */
function executeStep1UpdateAlamat(santriData) {
    console.log('[TPQ-EMIS Bridge] Eksekusi Step 1 Update Alamat: Menunggu daftar santri EMIS siap...');

    // 1. Tampilkan popup panduan langsung agar user mengetahui status proses
    showUpdateAlamatStep1Popup(santriData);

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
            applyKesiswaanFilter(searchInput, santriData.nama_santri);

            // Verifikasi sekunder: 1.2 detik kemudian pastikan input tidak terhapus oleh re-render EMIS
            setTimeout(() => {
                const curInput = findKesiswaanSearchInput();
                if (curInput) {
                    const currentVal = (curInput.value || '').trim();
                    if (!currentVal || currentVal !== santriData.nama_santri.trim()) {
                        console.log('[TPQ-EMIS Bridge] Re-triggering filter nama santri setelah verifikasi...');
                        applyKesiswaanFilter(curInput, santriData.nama_santri);
                    }
                }
            }, 1200);

            // Perbarui status pada popup
            const statusBadge = document.getElementById('tpq-step1-filter-status');
            if (statusBadge) {
                statusBadge.innerHTML = '<span>✓</span> <span>Nama telah dimasukkan & difilter otomatis</span>';
                statusBadge.style.color = '#198754';
                statusBadge.style.background = '#d1e7dd';
                statusBadge.style.borderColor = '#badbcc';
            }
        }
    }, 300);

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
        <div id="tpq-update-step1-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; font-weight: bold; font-size: 13px; display: flex; justify-content: space-between; align-items: center; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; gap: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;" title="${escapeHtml(data.nama_santri)}">
                <span style="font-size: 12px;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <button id="tpq-btn-quick-step2" style="background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4); border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.15);" title="Langsung masuk ke Step 2 (DATA SANTRI)">
                    ⚡ Step 2
                </button>
                <button id="tpq-toggle-step1" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.45); color: #fff; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer;" title="Buka panel panduan">
                    ▼ Buka
                </button>
                <span id="tpq-close-update-step1" style="cursor: pointer; font-size: 17px; line-height: 1; padding: 1px 4px; opacity: 0.85;" title="Tutup & batalkan antrean">&times;</span>
            </div>
        </div>
        <div id="tpq-update-step1-body" style="display: none; padding: 12px 14px; max-height: 75vh; overflow-y: auto;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <div style="font-weight: bold; font-size: 14px; color: #0d6efd;">${escapeHtml(data.nama_santri)}</div>
                    <button class="tpq-copy-btn" data-val="${escapeHtml(data.nama_santri)}" style="border: 1px solid #ced4da; background: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; cursor: pointer; color: #495057;" title="Salin Nama">📋 Salin</button>
                </div>
                <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
                    <span>NIK: <strong>${escapeHtml(data.nik || '-')}</strong></span>
                    ${data.nik ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.nik)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #495057;" title="Salin NIK">📋</button>` : ''}
                </div>
            </div>

            <div id="tpq-step1-filter-status" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 10px; margin-bottom: 10px; font-size: 12px; color: #1d4ed8; font-weight: 500;">
                Menunggu tabel santri EMIS selesai memuat...
            </div>

            <div style="display: flex; gap: 6px; margin-bottom: 10px;">
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
            div.style.width = '380px';
            bodyStep1.style.display = 'block';
            btnToggleStep1.innerHTML = '▲ Tutup';
            btnToggleStep1.style.background = 'rgba(255,255,255,0.35)';
        } else {
            div.style.width = '350px';
            bodyStep1.style.display = 'none';
            btnToggleStep1.innerHTML = '▼ Buka';
            btnToggleStep1.style.background = 'rgba(255,255,255,0.25)';
        }
    }

    btnToggleStep1.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleStep1();
    });

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
                    statusBadge.innerHTML = '<span>✓</span> <span>Nama telah dimasukkan & difilter manual</span>';
                    statusBadge.style.color = '#198754';
                    statusBadge.style.background = '#d1e7dd';
                    statusBadge.style.borderColor = '#badbcc';
                }
            } else {
                btnRefilter.innerText = '⚠️ Kolom Cari Tidak Ditemukan';
                setTimeout(() => { btnRefilter.innerText = '🔍 Filter Ulang'; }, 1800);
            }
        });
    }

    const goToStep2 = () => {
        div.remove();
        executeStep2DataSantri(data);
    };

    document.getElementById('tpq-btn-force-step2').addEventListener('click', goToStep2);
    document.getElementById('tpq-btn-quick-step2').addEventListener('click', goToStep2);
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
        } else if (labelText.includes('KOORDINAT') || labelText.includes('TITIK KOORDINAT')) {
            if (santri.santri_titik_koordinat) {
                console.log('[TPQ-EMIS Bridge] Menyetel Titik Koordinat Santri:', santri.santri_titik_koordinat);
                setNativeValue(inp, santri.santri_titik_koordinat);
            }
        } else if (labelText.includes('LINTANG') || labelText.includes('LATITUDE')) {
            const coords = parseTitikKoordinat(santri.santri_titik_koordinat);
            if (coords.lat) {
                console.log('[TPQ-EMIS Bridge] Menyetel Lintang/Latitude:', coords.lat);
                setNativeValue(inp, coords.lat);
            }
        } else if (labelText.includes('BUJUR') || labelText.includes('LONGITUDE')) {
            const coords = parseTitikKoordinat(santri.santri_titik_koordinat);
            if (coords.lng) {
                console.log('[TPQ-EMIS Bridge] Menyetel Bujur/Longitude:', coords.lng);
                setNativeValue(inp, coords.lng);
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

    // Auto-select dropdown khusus di tab DATA ALAMAT (Status Kepemilikan Rumah, Status Mukim, Status Tempat Tinggal, Jarak Tempuh)
    const selects = Array.from(document.querySelectorAll('select'));
    if (selects.length > 0) {
        console.log(`[TPQ-EMIS Bridge] Menemukan ${selects.length} elemen <select> di tab DATA ALAMAT, menyetel pilihan otomatis...`);

        selects.forEach(sel => {
            const options = Array.from(sel.options || sel.querySelectorAll('option'));
            if (options.length === 0) return;

            const optionTexts = options.map(o => (o.label || o.text || o.innerText || '').toLowerCase().trim());
            const optionValues = options.map(o => String(o.value || '').trim());

            let contextText = (sel.name || '') + ' ' + (sel.id || '');
            let p = sel.parentElement;
            for (let i = 0; i < 4 && p; i++) {
                contextText += ' ' + (p.innerText || p.textContent || '');
                p = p.parentElement;
            }
            contextText = contextText.toUpperCase();

            // 1. Jarak Tempat Tinggal - Lembaga (Santri)
            // Identifikasi STRICT berdasarkan opsi uniknya: "kurang dari 5 km", "antara 5", atau "lebih dari 30 km"
            const isJarakSelect = optionTexts.some(t => t.includes('kurang dari 5 km') || t.includes('lebih dari 30 km') || (t.includes('antara 5') && t.includes('km'))) ||
                (optionValues.includes('1') && optionValues.includes('5') && optionTexts.some(t => t.includes('km')));

            // 2. Status Kepemilikan Rumah (Ayah)
            // Ciri unik: opsi value '9' atau teks 'milik sendiri'
            const isRumahSelect = optionValues.includes('9') || optionTexts.some(t => t.includes('milik sendiri'));

            // 3. Status Mukim (Santri)
            // Ciri unik: opsi 'tidak mukim' dan 'mukim'
            const isMukimSelect = optionTexts.includes('tidak mukim') && optionTexts.includes('mukim');

            // 4. Status Tempat Tinggal (Santri)
            // Ciri unik: opsi value '18' atau teks 'tinggal dengan ayah kandung' / 'tinggal dengan orang tua'
            const isTempatTinggalSelect = optionValues.includes('18') || optionTexts.some(t => t.includes('tinggal dengan ayah') || t.includes('tinggal dengan orang tua'));

            // 5. Transportasi ke Lembaga (Santri)
            const isTransportSelect = (optionTexts.some(t => t.includes('jalan kaki')) && (optionTexts.some(t => t.includes('sepeda')) || optionTexts.some(t => t.includes('motor')))) ||
                (contextText.includes('TRANSPORTASI') && options.length > 3);

            // 6. Waktu Tempuh (Santri)
            const isWaktuSelect = (optionTexts.some(t => t.includes('menit')) && !optionTexts.some(t => t.includes('km'))) ||
                (contextText.includes('WAKTU') && optionTexts.some(t => t.includes('menit')));

            if (isJarakSelect) {
                const normalizedTarget = normalizeJarakTempuhEmis(santri.santri_jarak_tempuh || santri.santri_jarak);
                console.log('[TPQ-EMIS Bridge] Menyetel Jarak Tempat Tinggal ke Lembaga:', normalizedTarget, '(raw:', santri.santri_jarak_tempuh, ')');
                let ok = setNativeSelectValue(sel, normalizedTarget);
                if (!ok || sel.value === '' || sel.value === '0') {
                    setNativeSelectValue(sel, '1');
                }
            } else if (isRumahSelect) {
                const target = santri.ayah_status_kepemilikan_rumah || 'Milik Sendiri';
                console.log('[TPQ-EMIS Bridge] Menyetel Status Kepemilikan Rumah:', target);
                setNativeSelectValue(sel, target);
            } else if (isMukimSelect) {
                const target = santri.santri_status_mukim || 'Tidak Mukim';
                console.log('[TPQ-EMIS Bridge] Menyetel Status Mukim:', target);
                setNativeSelectValue(sel, target);
            } else if (isTempatTinggalSelect) {
                const target = santri.santri_status_tempat_tinggal || 'Tinggal dengan Ayah Kandung';
                console.log('[TPQ-EMIS Bridge] Menyetel Status Tempat Tinggal Santri:', target);
                setNativeSelectValue(sel, target);
            } else if (isTransportSelect) {
                const target = normalizeTransportasiEmis(santri.santri_transportasi);
                console.log('[TPQ-EMIS Bridge] Menyetel Transportasi ke Lembaga:', target, '(raw:', santri.santri_transportasi, ')');
                let ok = setNativeSelectValue(sel, target);
                if (!ok || sel.value === '' || sel.value === '0') {
                    setNativeSelectValue(sel, 'Jalan Kaki');
                }
            } else if (isWaktuSelect) {
                const target = normalizeWaktuTempuhEmis(santri.santri_waktu_tempuh);
                console.log('[TPQ-EMIS Bridge] Menyetel Waktu Tempuh ke Lembaga:', target, '(raw:', santri.santri_waktu_tempuh, ')');
                let ok = setNativeSelectValue(sel, target);
                if (!ok || sel.value === '' || sel.value === '0') {
                    setNativeSelectValue(sel, '1-10 menit');
                }
            }
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

    const targetBiaya = data.yang_biaya_sekolah || 'Orang Tua';

    div.innerHTML = `
        <div id="tpq-update-santri-step2-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; font-weight: bold; font-size: 13px; display: flex; justify-content: space-between; align-items: center; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; gap: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;" title="${escapeHtml(data.nama_santri)}">
                <span style="font-size: 12px;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <button id="tpq-btn-quick-lanjut-step2" style="background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4); border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.15);" title="Lanjut ke Tab DATA ALAMAT (Step 3)">
                    ➡️ Step 3
                </button>
                <button id="tpq-toggle-step2" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.45); color: #fff; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer;" title="Buka panel panduan">
                    ▼ Buka
                </button>
                <span id="tpq-close-update-santri-step2" style="cursor: pointer; font-size: 17px; line-height: 1; padding: 1px 4px; opacity: 0.85;" title="Tutup & batalkan antrean">&times;</span>
            </div>
        </div>
        <div id="tpq-update-santri-step2-body" style="display: none; padding: 12px 14px; max-height: 75vh; overflow-y: auto;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px;">
                <div style="font-weight: bold; font-size: 14px; color: #0d6efd; margin-bottom: 4px;">${escapeHtml(data.nama_santri)}</div>
                <div style="font-size: 12px; color: #64748b;">NIK: <strong>${escapeHtml(data.nik || '-')}</strong></div>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; font-size: 12px; color: #166534;">
                <div style="font-weight: bold; margin-bottom: 4px;">Yang Membiayai Sekolah:</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: bold;">${escapeHtml(targetBiaya)}</span>
                    <button class="tpq-copy-btn" data-val="${escapeHtml(targetBiaya)}" style="border: 1px solid #86efac; background: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; cursor: pointer; color: #166534;" title="Salin">📋</button>
                </div>
            </div>

            <div style="font-size: 11px; color: #64748b; line-height: 1.4; margin-bottom: 12px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; padding: 8px 10px;">
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
            div.style.width = '350px';
            bodyStep2.style.display = 'none';
            btnToggleStep2.innerHTML = '▼ Buka';
            btnToggleStep2.style.background = 'rgba(255,255,255,0.25)';
        }
    }

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
        div.remove();
        isStep3Done = false;
        ensureDataAlamatTabActive();
        setTimeout(() => {
            executeStep3DataAlamat(data);
        }, 400);
    };

    document.getElementById('tpq-btn-lanjut-step3').addEventListener('click', handleLanjutStep3);
    document.getElementById('tpq-btn-quick-lanjut-step2').addEventListener('click', handleLanjutStep3);
}

let isStep3Executing = false;
let isStep3Done = false;
let isWilayahCascadeRunning = false;

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
 * Mengambil teks konteks label sekitar elemen <select> (aman, cepat, tidak memicu reflow DOM berlebih)
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
            if (badgeOverall) {
                badgeOverall.innerText = '✓ Selesai Auto';
                badgeOverall.style.background = '#dcfce7';
                badgeOverall.style.color = '#15803d';
            }
        } else {
            console.log(`[TPQ-EMIS Bridge] ⚠️ Opsi Desa tidak cocok untuk: "${targetDesa}"`);
            updateWilayahStatusUI('desa', '[⚠️ Pilih manual]', '#dc3545');
        }

    } catch (err) {
        console.error('[TPQ-EMIS Bridge] Kesalahan saat cascading Wilayah:', err);
    } finally {
        isWilayahCascadeRunning = false;
    }
}

/**
 * Eksekusi Step 3: Tab DATA ALAMAT (Dengan Interval Retry untuk mengunci dropdown Jarak)
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

    // 2. Tampilkan popup dan isi field domisili Ayah, centang checkbox, dan set status tinggal
    setTimeout(() => {
        showUpdateAlamatStep3Popup(santriData);
        fillUpdateAlamatTextFields(santriData);

        // 3. Beri jeda 400ms setelah checkbox & dropdown lain diproses
        // agar re-render React pada bagian bawah form selesai terlebih dahulu
        setTimeout(() => {
            let consecutiveSuccess = 0;
            let attempts = 0;
            const maxAttempts = 15; // Berjalan hingga 4.5 detik (15 x 300ms)

            const step3Timer = setInterval(() => {
                attempts++;
                const isJarakOk = checkIsJarakSelected();
                const isTransportOk = checkIsTransportasiSelected();
                const isWaktuOk = checkIsWaktuTempuhSelected();
                const isKoordinatOk = checkIsKoordinatFilled(santriData.santri_titik_koordinat);

                if (!isJarakOk) {
                    selectJarakTempatTinggalEmis(santriData.santri_jarak_tempuh || santriData.santri_jarak);
                }
                if (!isTransportOk) {
                    selectTransportasiEmis(santriData.santri_transportasi);
                }
                if (!isWaktuOk) {
                    selectWaktuTempuhEmis(santriData.santri_waktu_tempuh);
                }
                if (!isKoordinatOk && santriData.santri_titik_koordinat) {
                    fillTitikKoordinatEmis(santriData.santri_titik_koordinat);
                }

                if (isJarakOk && isTransportOk && isWaktuOk && isKoordinatOk) {
                    consecutiveSuccess++;
                    console.log(`[TPQ-EMIS Bridge] [Mobilitas Timer #${attempts}] Jarak, Transportasi, Waktu & Koordinat terdeteksi terkunci (stabil ${consecutiveSuccess}/3)`);
                } else {
                    consecutiveSuccess = 0;
                    console.log(`[TPQ-EMIS Bridge] [Mobilitas Timer #${attempts}] Status kunci - Jarak: ${isJarakOk}, Transportasi: ${isTransportOk}, Waktu: ${isWaktuOk}, Koordinat: ${isKoordinatOk}`);
                }

                // Berhenti hanya jika sudah stabil terpilih selama 3 siklus berturut-turut ATAU waktu habis
                if (consecutiveSuccess >= 3 || attempts >= maxAttempts) {
                    clearInterval(step3Timer);
                    isStep3Executing = false;
                    isStep3Done = true;
                    console.log('[TPQ-EMIS Bridge] Penguncian Mobilitas (Jarak, Transportasi, Waktu, Koordinat) Step 3 selesai. Status stabil:', consecutiveSuccess >= 3);

                    // Jalankan update berantai dropdown wilayah (Provinsi -> Kab -> Kec -> Desa) di akhir
                    setTimeout(() => {
                        startCascadingWilayahUpdate(santriData);
                    }, 300);
                }
            }, 300);
        }, 400);
    }, 250);
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
        <div id="tpq-update-step3-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #fff; padding: 8px 12px; font-weight: bold; font-size: 13px; display: flex; justify-content: space-between; align-items: center; cursor: move; user-select: none;">
            <div style="display: flex; align-items: center; gap: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 160px;" title="${escapeHtml(data.nama_santri)}">
                <span style="font-size: 12px;">⚡ <strong>${escapeHtml(data.nama_santri)}</strong></span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <button id="tpq-btn-quick-selesai-step3" style="background: #198754; color: #fff; border: 1px solid rgba(255,255,255,0.4); border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.15);" title="Tandai update selesai & simpan ke TPQ Smart">
                    ✓ Selesai
                </button>
                <button id="tpq-toggle-step3" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.45); color: #fff; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer;" title="Buka detail data alamat & koordinat">
                    ▼ Detail
                </button>
                <span id="tpq-close-update-step3" style="cursor: pointer; font-size: 17px; line-height: 1; padding: 1px 4px; opacity: 0.85;" title="Tutup & batalkan antrean">&times;</span>
            </div>
        </div>

        <!-- Bagian Langsung Muncul: Wilayah Domisili (Otomatis & Salin Manual) & Tombol Selesai (Tanpa Perlu Buka Manual) -->
        <div style="padding: 10px 12px; background: #ffffff;">
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 10px; font-size: 12px; margin-bottom: 8px;">
                <div style="font-size: 11px; font-weight: bold; color: #1d4ed8; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; display: flex; justify-content: space-between; align-items: center;">
                    <span>📍 WILAYAH DOMISILI (OTOMATIS / SALIN):</span>
                    <span id="tpq-status-wilayah-badge" style="font-size: 9.5px; background: #dbeafe; color: #1e40af; padding: 1px 5px; border-radius: 3px; font-weight: bold;">Auto-Cascade</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <!-- Provinsi -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">Provinsi:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-provinsi" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_provinsi || '-')}</strong>
                            ${data.ayah_provinsi ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_provinsi)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Provinsi">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- Kab / Kota -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">Kab/Kota:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-kabupaten" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_kabupaten_kota || '-')}</strong>
                            ${data.ayah_kabupaten_kota ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kabupaten_kota)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Kab/Kota">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- Kecamatan -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">Kecamatan:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-kecamatan" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_kecamatan || '-')}</strong>
                            ${data.ayah_kecamatan ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kecamatan)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Kecamatan">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- Kelurahan / Desa -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b;">Kel/Desa:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span id="tpq-status-desa" style="font-size: 9.5px; color: #64748b;">[⏳ Tunggu]</span>
                            <strong style="color: #1e293b;">${escapeHtml(data.ayah_kelurahan_desa || '-')}</strong>
                            ${data.ayah_kelurahan_desa ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kelurahan_desa)}" style="border: 1px solid #bfdbfe; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #1d4ed8;" title="Salin Kel/Desa">📋</button>` : ''}
                        </div>
                    </div>
                    <!-- Status Rumah -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #bfdbfe; padding-top: 4px; margin-top: 2px;">
                        <span style="color: #64748b;">Status Rumah:</span>
                        <strong style="color: #166534;">${escapeHtml(data.ayah_status_kepemilikan_rumah || 'Milik Sendiri')}</strong>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi (Langsung Dapat Diakses) -->
            <div style="display: flex; gap: 6px;">
                <button id="tpq-btn-selesai-alamat" style="flex: 1.2; background: #198754; color: #fff; border: none; padding: 7px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; cursor: pointer;">
                    ✓ Selesai di TPQ Smart
                </button>
                <button id="tpq-btn-refill-alamat" style="flex: 1; background: #f8fafc; color: #0d6efd; border: 1px solid #0d6efd; padding: 7px 8px; border-radius: 5px; font-weight: 500; font-size: 11px; cursor: pointer;">
                    ⚡ Isi Ulang
                </button>
            </div>
        </div>

        <!-- Bagian Tersembunyi (Detail Data Alamat Terisi): Dibuka via Tombol ▼ Detail -->
        <div id="tpq-update-step3-body" style="display: none; padding: 0 12px 10px; max-height: 50vh; overflow-y: auto;">
            <!-- Info Santri -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; font-size: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                    <div style="font-weight: bold; font-size: 13px; color: #0d6efd;">${escapeHtml(data.nama_santri)}</div>
                    <button class="tpq-copy-btn" data-val="${escapeHtml(data.nama_santri)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #495057;" title="Salin Nama">📋 Salin</button>
                </div>
                <div style="font-size: 11.5px; color: #64748b; display: flex; align-items: center; gap: 6px;">
                    <span>NIK: <strong>${escapeHtml(data.nik || '-')}</strong></span>
                    ${data.nik ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.nik)}" style="border: 1px solid #ced4da; background: #fff; padding: 1px 4px; border-radius: 3px; font-size: 9px; cursor: pointer; color: #495057;" title="Salin NIK">📋</button>` : ''}
                </div>
            </div>

            <!-- Data Terisi Otomatis -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; font-size: 12px;">
                <div style="font-size: 10.5px; font-weight: bold; color: #166534; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>✨ DATA TERISI OTOMATIS:</span>
                    <span style="font-size: 9.5px; background: #dcfce7; color: #15803d; padding: 1px 5px; border-radius: 8px; font-weight: bold;">Auto-Filled ✓</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <span style="color: #4b5563;">Alamat:</span>
                        <div style="display: flex; align-items: center; gap: 4px; text-align: right; max-width: 65%;">
                            <span style="font-weight: 500;">${escapeHtml(data.ayah_alamat || '-')} RT ${escapeHtml(data.ayah_rt || '-')} RW ${escapeHtml(data.ayah_rw || '-')}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_alamat || '')} RT ${escapeHtml(data.ayah_rt || '')} RW ${escapeHtml(data.ayah_rw || '')}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4b5563;">Kode Pos:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <strong>${escapeHtml(data.ayah_kode_pos || '-')}</strong>
                            ${data.ayah_kode_pos ? `<button class="tpq-copy-btn" data-val="${escapeHtml(data.ayah_kode_pos)}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>` : ''}
                        </div>
                    </div>
                    ${data.santri_titik_koordinat ? `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4b5563;">Koordinat:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span style="font-family: monospace; font-size: 11px; font-weight: 600;">${escapeHtml(data.santri_titik_koordinat)}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_titik_koordinat)}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>` : ''}
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #bbf7d0; padding-top: 4px;">
                        <span style="color: #4b5563;">Jarak / Waktu:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span style="font-weight: 500;">${escapeHtml(data.santri_jarak_tempuh || 'Kurang dari 5 km')} · ${escapeHtml(data.santri_waktu_tempuh || '1-10 menit')}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_jarak_tempuh || 'Kurang dari 5 km')}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4b5563;">Transportasi:</span>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span style="font-weight: 500;">${escapeHtml(data.santri_transportasi || 'Jalan Kaki')}</span>
                            <button class="tpq-copy-btn" data-val="${escapeHtml(data.santri_transportasi || 'Jalan Kaki')}" style="border: 1px solid #86efac; background: #fff; padding: 1px 5px; border-radius: 3px; font-size: 10px; cursor: pointer; color: #166534;">📋</button>
                        </div>
                    </div>
                </div>
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
            div.style.width = '350px';
            bodyStep3.style.display = 'none';
            btnToggleStep3.innerHTML = '▼ Detail';
            btnToggleStep3.style.background = 'rgba(255,255,255,0.25)';
        }
    }

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

    document.getElementById('tpq-btn-refill-alamat').addEventListener('click', () => {
        ensureDataAlamatTabActive();
        fillUpdateAlamatTextFields(data);
        selectJarakTempatTinggalEmis(data.santri_jarak_tempuh || data.santri_jarak);
        selectTransportasiEmis(data.santri_transportasi);
        selectWaktuTempuhEmis(data.santri_waktu_tempuh);
        if (data.santri_titik_koordinat) {
            fillTitikKoordinatEmis(data.santri_titik_koordinat);
        }
        setTimeout(() => {
            isWilayahCascadeRunning = false;
            startCascadingWilayahUpdate(data);
        }, 300);
    });

    const handleFinishStep3 = () => {
        try {
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
            setTimeout(() => { div.remove(); }, 1200);
        } catch (err) {
            console.error('[TPQ-EMIS Bridge] Gagal kirim notifikasi selesai:', err);
        }
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
