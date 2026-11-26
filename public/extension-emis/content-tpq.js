/**
 * TPQ Smart - EMIS 4.0 Bridge
 * Content Script for TPQ Smart Pages
 */

console.log('[TPQ-EMIS Bridge] Content script aktif di halaman TPQ Smart.');

/**
 * Cek apakah runtime ekstensi masih terhubung
 */
function isExtensionValid() {
    try {
        return Boolean(typeof chrome !== 'undefined' && chrome && chrome.runtime && chrome.runtime.id);
    } catch (e) {
        return false;
    }
}

/**
 * Beritahu halaman web bahwa ekstensi sudah terpasang
 */
function broadcastExtensionReady() {
    if (!isExtensionValid()) return;
    try {
        window.postMessage({
            type: 'EMIS_EXTENSION_PONG',
            source: 'tpq-emis-extension',
            version: '1.7.3'
        }, '*');
    } catch (e) {
        // Abaikan
    }
}

// Kirim sinyal siap saat script pertama kali dimuat
broadcastExtensionReady();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', broadcastExtensionReady);
} else {
    // Beri jeda kecil untuk memastikan script web sudah siap menerima pesan
    setTimeout(broadcastExtensionReady, 200);
}

// Listener pesan dari halaman web TPQ Smart
window.addEventListener('message', (event) => {
    // Pastikan berasal dari window yang sama
    if (event.source !== window || !event.data) return;

    // Respon jika halaman melakukan ping
    if (event.data.type === 'TPQ_TO_EMIS_EXTENSION_PING') {
        broadcastExtensionReady();
        return;
    }

    // Tangani pengiriman data santri ke EMIS
    if (event.data.type === 'TPQ_SEND_TO_EMIS') {
        const santriData = event.data.data;
        console.log('[TPQ-EMIS Bridge] Meneruskan data santri ke background script:', santriData);

        if (!isExtensionValid()) {
            console.warn('[TPQ-EMIS Bridge] Extension context invalidated (ekstensi baru di-reload).');
            window.postMessage({
                type: 'EMIS_EXTENSION_NEED_REFRESH',
                message: 'Ekstensi baru saja dimuat ulang di Chrome. Silakan Refresh (tekan F5) halaman TPQ Smart ini.'
            }, '*');
            return;
        }

        try {
            chrome.runtime.sendMessage({
                action: 'SEND_SANTRI_TO_EMIS',
                data: santriData,
                isBatch: Boolean(event.data.isBatch)
            }, (response) => {
                if (chrome.runtime.lastError) {
                    console.warn('[TPQ-EMIS Bridge] Runtime warning:', chrome.runtime.lastError.message);
                    return;
                }
                console.log('[TPQ-EMIS Bridge] Balasan dari background:', response);
            });
        } catch (err) {
            console.error('[TPQ-EMIS Bridge] Gagal mengirim pesan:', err);
            window.postMessage({
                type: 'EMIS_EXTENSION_NEED_REFRESH',
                message: 'Koneksi ekstensi terputus. Silakan Refresh (F5) halaman TPQ Smart.'
            }, '*');
        }
    }

    // Tangani pengiriman data santri untuk UPDATE ALAMAT di Kesiswaan EMIS
    if (event.data.type === 'TPQ_SEND_UPDATE_ALAMAT_TO_EMIS') {
        const santriData = event.data.data;
        console.log('[TPQ-EMIS Bridge] Meneruskan data santri untuk UPDATE ALAMAT ke background:', santriData);

        if (!isExtensionValid()) {
            console.warn('[TPQ-EMIS Bridge] Extension context invalidated (ekstensi baru di-reload).');
            window.postMessage({
                type: 'EMIS_EXTENSION_NEED_REFRESH',
                message: 'Ekstensi baru saja dimuat ulang di Chrome. Silakan Refresh (tekan F5) halaman TPQ Smart ini.'
            }, '*');
            return;
        }

        try {
            chrome.runtime.sendMessage({
                action: 'SEND_UPDATE_ALAMAT_TO_EMIS',
                data: santriData
            }, (response) => {
                if (chrome.runtime.lastError) {
                    console.warn('[TPQ-EMIS Bridge] Runtime warning:', chrome.runtime.lastError.message);
                    return;
                }
                console.log('[TPQ-EMIS Bridge] Balasan background update alamat:', response);
            });
        } catch (err) {
            console.error('[TPQ-EMIS Bridge] Gagal mengirim pesan update alamat:', err);
            window.postMessage({
                type: 'EMIS_EXTENSION_NEED_REFRESH',
                message: 'Koneksi ekstensi terputus. Silakan Refresh (F5) halaman TPQ Smart.'
            }, '*');
        }
    }
});

// Listener pesan dari background worker ekstensi
try {
    if (isExtensionValid() && chrome.runtime && chrome.runtime.onMessage) {
        chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
            if (request && request.action === 'NOTIFY_TPQ_SUCCESS') {
                console.log('[TPQ-EMIS Bridge] Mendapat konfirmasi form pendaftaran terisi:', request.id_santri);
                window.postMessage({
                    type: 'EMIS_EXTENSION_FILLED_SUCCESS',
                    id_santri: request.id_santri,
                    returnToTpq: Boolean(request.returnToTpq)
                }, '*');
                sendResponse({ ok: true });
            } else if (request && request.action === 'NOTIFY_TPQ_UPDATE_ALAMAT_SUCCESS') {
                console.log('[TPQ-EMIS Bridge] Mendapat konfirmasi update alamat selesai:', request.id_santri);
                window.postMessage({
                    type: 'EMIS_EXTENSION_UPDATE_ALAMAT_SUCCESS',
                    id_santri: request.id_santri
                }, '*');
                sendResponse({ ok: true });
            } else if (request && request.action === 'NOTIFY_TPQ_BATCH_ITEM_FAILED') {
                console.log('[TPQ-EMIS Bridge] Mendapat laporan gagal/konflik batch:', request.id_santri, request.status, request.catatan);
                window.postMessage({
                    type: 'EMIS_EXTENSION_BATCH_ITEM_FAILED',
                    id_santri: request.id_santri,
                    status: request.status,
                    catatan: request.catatan
                }, '*');
                sendResponse({ ok: true });
            }
            return true;
        });
    }
} catch (e) {
    // Abaikan jika listener gagal diinisialisasi
}
