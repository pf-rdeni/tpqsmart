/**
 * TPQ Smart - EMIS 4.0 Bridge
 * Background Service Worker (Manifest V3)
 */

chrome.runtime.onInstalled.addListener(() => {
    console.log('[TPQ-EMIS] Ekstensi berhasil diinstal.');
});

// Listener komunikasi dari content script
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request.action === 'PING') {
        sendResponse({ status: 'PONG', version: '1.0.0' });
        return true;
    }

    if (request.action === 'SEND_SANTRI_TO_EMIS') {
        const santriData = request.data;
        console.log('[TPQ-EMIS] Menerima data santri untuk dikirim ke EMIS:', santriData);

        const EMIS_PPDB_URL = "https://emis.kemenag.go.id/search-data-ppdb/baru";

        // Simpan data antrean ke storage terlebih dahulu
        chrome.storage.local.set({ 
            pendingSantriData: santriData,
            waitingForStep2: false 
        }, () => {
            // Cari apakah tab EMIS sudah terbuka di browser
            chrome.tabs.query({ url: "*://emis.kemenag.go.id/*" }, (tabs) => {
                if (tabs && tabs.length > 0) {
                    const emisTab = tabs[0];
                    const currentUrl = emisTab.url || '';
                    const isAlreadyOnPpdb = currentUrl.includes('search-data-ppdb');

                    if (isAlreadyOnPpdb) {
                        // Jika sudah berada di halaman PPDB, aktifkan tab dan kirim instruksi isi
                        chrome.tabs.update(emisTab.id, { active: true });
                        chrome.tabs.sendMessage(emisTab.id, {
                            action: 'EXECUTE_FILL_FORM',
                            data: santriData
                        }, (response) => {
                            if (chrome.runtime.lastError) {
                                console.warn('[TPQ-EMIS] Tab perlu dimuat ulang:', chrome.runtime.lastError.message);
                                chrome.tabs.update(emisTab.id, { url: EMIS_PPDB_URL });
                            } else {
                                console.log('[TPQ-EMIS] Respon dari form EMIS:', response);
                            }
                        });
                    } else {
                        // Jika berada di halaman EMIS lain, langsung arahkan ke link PPDB baru
                        chrome.tabs.update(emisTab.id, {
                            url: EMIS_PPDB_URL,
                            active: true
                        });
                    }

                    sendResponse({ success: true, message: 'Tab EMIS PPDB diaktifkan.' });
                } else {
                    // Jika belum ada tab EMIS, langsung buka tab baru ke link PPDB baru
                    chrome.tabs.create({
                        url: EMIS_PPDB_URL,
                        active: true
                    }, (newTab) => {
                        console.log('[TPQ-EMIS] Membuka tab EMIS PPDB baru:', newTab.id);
                    });

                    sendResponse({
                        success: true,
                        message: 'Membuka link PPDB EMIS 4.0 dan data santri akan otomatis diisikan.'
                    });
                }
            });
        });

        return true;
    }

    if (request.action === 'FORM_FILLED_NOTIFY') {
        console.log('[TPQ-EMIS] Formulir EMIS berhasil diisi untuk santri:', request.id_santri);
        
        // Teruskan kabar sukses ke tab TPQ Smart agar UI terupdate
        chrome.tabs.query({}, (tabs) => {
            tabs.forEach(tab => {
                if (tab.url && (tab.url.includes('tpqsmart') || tab.url.includes('localhost') || tab.url.includes('127.0.0.1'))) {
                    chrome.tabs.sendMessage(tab.id, {
                        action: 'NOTIFY_TPQ_SUCCESS',
                        id_santri: request.id_santri
                    }).catch(() => {});
                }
            });
        });

        sendResponse({ received: true });
        return true;
    }
});
