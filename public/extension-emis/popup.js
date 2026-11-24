document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnToggleMode');
    if (!btn) return;

    function renderMode(mode) {
        if (mode === 'FULL') {
            btn.innerText = '⚡ Full Otomatis';
            btn.style.background = '#d1e7dd';
            btn.style.color = '#0f5132';
            btn.style.borderColor = '#198754';
            btn.title = 'Mode Full Otomatis aktif (klik untuk ganti ke Semi-Otomatis)';
        } else {
            btn.innerText = '✋ Semi-Otomatis';
            btn.style.background = '#fff3cd';
            btn.style.color = '#664d03';
            btn.style.borderColor = '#ffc107';
            btn.title = 'Mode Semi-Otomatis aktif (klik untuk ganti ke Full Otomatis)';
        }
    }

    chrome.storage.local.get(['emisAutoMode'], (res) => {
        const mode = (res && res.emisAutoMode) ? res.emisAutoMode : 'FULL';
        renderMode(mode);
    });

    btn.addEventListener('click', () => {
        chrome.storage.local.get(['emisAutoMode'], (res) => {
            const cur = (res && res.emisAutoMode) ? res.emisAutoMode : 'FULL';
            const next = (cur === 'FULL') ? 'SEMI' : 'FULL';
            chrome.storage.local.set({ emisAutoMode: next }, () => {
                renderMode(next);
            });
        });
    });
});
