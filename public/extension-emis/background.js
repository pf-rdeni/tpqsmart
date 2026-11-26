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
        sendResponse({ status: 'PONG', version: '1.7.3' });
        return true;
    }

    if (request.action === 'SEND_SANTRI_TO_EMIS') {
        const santriData = request.data;
        const originTpqTabId = sender?.tab?.id || null;
        const originTpqWindowId = sender?.tab?.windowId || null;
        console.log('[TPQ-EMIS] Menerima data santri untuk dikirim ke EMIS:', santriData, 'originTab:', originTpqTabId);

        const EMIS_PPDB_URL = "https://emis.kemenag.go.id/search-data-ppdb/baru";

        // Simpan data antrean & tab asal TPQ Smart ke storage terlebih dahulu
        chrome.storage.local.set({ 
            pendingSantriData: santriData,
            waitingForStep2: false,
            isBatchMode: Boolean(request.isBatch),
            originTpqTabId: originTpqTabId,
            originTpqWindowId: originTpqWindowId
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

    if (request.action === 'SEND_UPDATE_ALAMAT_TO_EMIS') {
        const santriData = request.data;
        const originTpqTabId = sender?.tab?.id || null;
        const originTpqWindowId = sender?.tab?.windowId || null;
        console.log('[TPQ-EMIS] Menerima data santri untuk UPDATE ALAMAT di EMIS:', santriData, 'originTab:', originTpqTabId);

        const EMIS_KESISWAAN_URL = "https://emis.kemenag.go.id/kesiswaan";

        // Simpan data antrean & tab asal TPQ Smart ke storage
        chrome.storage.local.set({ 
            pendingUpdateAlamatData: santriData,
            currentTask: 'UPDATE_ALAMAT',
            originTpqTabId: originTpqTabId,
            originTpqWindowId: originTpqWindowId
        }, () => {
            chrome.tabs.query({ url: "*://emis.kemenag.go.id/*" }, (tabs) => {
                if (tabs && tabs.length > 0) {
                    const emisTab = tabs[0];
                    const currentUrl = emisTab.url || '';
                    const isAlreadyOnKesiswaan = currentUrl.includes('kesiswaan');

                    if (isAlreadyOnKesiswaan) {
                        chrome.tabs.update(emisTab.id, { active: true });
                        chrome.tabs.sendMessage(emisTab.id, {
                            action: 'EXECUTE_UPDATE_ALAMAT',
                            data: santriData
                        }, (response) => {
                            if (chrome.runtime.lastError) {
                                console.warn('[TPQ-EMIS] Tab perlu dimuat ulang:', chrome.runtime.lastError.message);
                                chrome.tabs.update(emisTab.id, { url: EMIS_KESISWAAN_URL });
                            } else {
                                console.log('[TPQ-EMIS] Respon dari Kesiswaan EMIS:', response);
                            }
                        });
                    } else {
                        chrome.tabs.update(emisTab.id, {
                            url: EMIS_KESISWAAN_URL,
                            active: true
                        });
                    }

                    sendResponse({ success: true, message: 'Tab EMIS Kesiswaan diaktifkan.' });
                } else {
                    chrome.tabs.create({
                        url: EMIS_KESISWAAN_URL,
                        active: true
                    }, (newTab) => {
                        console.log('[TPQ-EMIS] Membuka tab EMIS Kesiswaan baru:', newTab.id);
                    });

                    sendResponse({
                        success: true,
                        message: 'Membuka halaman Kesiswaan EMIS 4.0 untuk pembaruan alamat.'
                    });
                }
            });
        });

        return true;
    }

    const notifyTpqAndOptionallyFocusOrigin = (actionName, extraPayload, focusOrigin) => {
        const activateTabAndWindow = (tab) => {
            if (!tab) return;
            if (tab.windowId) {
                chrome.windows.update(tab.windowId, { focused: true }, () => {
                    if (chrome.runtime.lastError) { /* abaikan */ }
                });
            }
            chrome.tabs.update(tab.id, { active: true }, () => {
                if (chrome.runtime.lastError) {
                    console.warn('[TPQ-EMIS] Gagal mengaktifkan tab asal TPQ Smart:', chrome.runtime.lastError.message);
                } else {
                    console.log('[TPQ-EMIS] Tab aktif berpindah kembali ke TPQ Smart:', tab.id);
                }
            });
        };

        chrome.storage.local.get(['originTpqTabId'], (res) => {
            const originTabId = res.originTpqTabId || null;

            chrome.tabs.query({}, (tabs) => {
                const tpqTabs = tabs.filter(tab =>
                    tab.url && (tab.url.includes('tpqsmart') || tab.url.includes('localhost') || tab.url.includes('127.0.0.1'))
                );

                tpqTabs.forEach(tab => {
                    chrome.tabs.sendMessage(tab.id, {
                        action: actionName,
                        ...extraPayload
                    }).catch(() => {});
                });

                if (focusOrigin) {
                    if (originTabId) {
                        chrome.tabs.get(originTabId, (originTab) => {
                            if (!chrome.runtime.lastError && originTab) {
                                activateTabAndWindow(originTab);
                            } else if (tpqTabs.length > 0) {
                                const fallbackTab = tpqTabs.find(t => t.url.includes('showSantriEmis')) || tpqTabs[0];
                                activateTabAndWindow(fallbackTab);
                            }
                        });
                    } else if (tpqTabs.length > 0) {
                        const fallbackTab = tpqTabs.find(t => t.url.includes('showSantriEmis')) || tpqTabs[0];
                        activateTabAndWindow(fallbackTab);
                    }
                    chrome.storage.local.remove(['originTpqTabId', 'originTpqWindowId', 'isBatchMode']);
                }
            });
        });
    };

    if (request.action === 'UPDATE_ALAMAT_SUCCESS_NOTIFY') {
        console.log('[TPQ-EMIS] Update alamat EMIS berhasil untuk santri:', request.id_santri);
        notifyTpqAndOptionallyFocusOrigin('NOTIFY_TPQ_UPDATE_ALAMAT_SUCCESS', { id_santri: request.id_santri }, true);
        sendResponse({ received: true });
        return true;
    }

    if (request.action === 'FORM_FILLED_NOTIFY') {
        console.log('[TPQ-EMIS] Formulir EMIS berhasil diisi untuk santri:', request.id_santri, 'returnToTpq:', Boolean(request.returnToTpq));
        notifyTpqAndOptionallyFocusOrigin('NOTIFY_TPQ_SUCCESS', {
            id_santri: request.id_santri,
            returnToTpq: Boolean(request.returnToTpq)
        }, Boolean(request.returnToTpq));
        sendResponse({ received: true });
        return true;
    }

    if (request.action === 'BATCH_ITEM_FAILED_NOTIFY') {
        console.log('[TPQ-EMIS] Batch item gagal/konflik untuk santri:', request.id_santri, request.status, request.catatan);
        notifyTpqAndOptionallyFocusOrigin('NOTIFY_TPQ_BATCH_ITEM_FAILED', {
            id_santri: request.id_santri,
            status: request.status || 'belum',
            catatan: request.catatan || ''
        }, true);
        sendResponse({ received: true });
        return true;
    }
});
