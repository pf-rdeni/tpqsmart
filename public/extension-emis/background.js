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

        // Cari apakah tab EMIS sudah terbuka
        chrome.tabs.query({ url: "*://emis.kemenag.go.id/*" }, (tabs) => {
            if (tabs && tabs.length > 0) {
                // Gunakan tab EMIS yang sudah ada
                const emisTab = tabs[0];
                chrome.tabs.update(emisTab.id, { active: true });
                
                // Kirim data ke content script di tab EMIS
                chrome.tabs.sendMessage(emisTab.id, {
                    action: 'EXECUTE_FILL_FORM',
                    data: santriData
                }, (response) => {
                    if (chrome.runtime.lastError) {
                        console.warn('[TPQ-EMIS] Gagal kirim pesan langsung, simpan ke storage:', chrome.runtime.lastError.message);
                        // Simpan ke storage dan refresh jika perlu
                        chrome.storage.local.set({ pendingSantriData: santriData }, () => {
                            chrome.tabs.reload(emisTab.id);
                        });
                    } else {
                        console.log('[TPQ-EMIS] Respon dari form EMIS:', response);
                    }
                });

                sendResponse({ success: true, message: 'Data dikirim ke tab EMIS yang aktif.' });
            } else {
                // Simpan data ke storage lokal, lalu buka tab baru EMIS
                chrome.storage.local.set({ pendingSantriData: santriData }, () => {
                    chrome.tabs.create({ url: "https://emis.kemenag.go.id" }, (newTab) => {
                        console.log('[TPQ-EMIS] Membuka tab EMIS baru:', newTab.id);
                    });
                });

                sendResponse({
                    success: true,
                    message: 'Tab EMIS sedang dibuka. Data akan otomatis diisi saat halaman formulir terbuka.'
                });
            }
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
