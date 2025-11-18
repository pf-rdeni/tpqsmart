<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Ayat Al-Qur'an</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('backend/surah') ?>">Surah</a></li>
                    <li class="breadcrumb-item active">Ayat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cari Ayat</h3>
                    </div>
                    <div class="card-body">
                        <!-- Tabs untuk memilih mode pencarian -->
                        <ul class="nav nav-tabs mb-3" id="ayahTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="single-tab" data-toggle="tab" href="#singleAyah" role="tab">
                                    <i class="fas fa-bookmark"></i> Ayat Tunggal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="range-tab" data-toggle="tab" href="#rangeAyah" role="tab">
                                    <i class="fas fa-list"></i> Range Ayat
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="ayahTabContent">
                            <!-- Tab Ayat Tunggal -->
                            <div class="tab-pane fade show active" id="singleAyah" role="tabpanel">
                                <form method="GET" action="<?= base_url('backend/surah') ?>" class="mb-4" id="formCariAyat">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nomor Surah (1-114)</label>
                                                <input type="number" name="surah" id="inputSurah" class="form-control" 
                                                       value="<?= esc($surah_id ?? '') ?>" 
                                                       min="1" max="114" 
                                                       placeholder="Contoh: 1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nomor Ayat</label>
                                                <input type="number" name="ayah" id="inputAyah" class="form-control" 
                                                       value="<?= esc($ayah_id ?? '') ?>" 
                                                       min="1" 
                                                       placeholder="Contoh: 1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-search"></i> Cari Ayat
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Tab Range Ayat -->
                            <div class="tab-pane fade" id="rangeAyah" role="tabpanel">
                                <form method="GET" action="<?= base_url('backend/surah') ?>" class="mb-4" id="formCariRangeAyat">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nomor Surah (1-114)</label>
                                                <input type="number" name="surah" id="inputSurahRange" class="form-control" 
                                                       value="<?= esc($surah_id ?? '') ?>" 
                                                       min="1" max="114" 
                                                       placeholder="Contoh: 2" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Ayat Awal</label>
                                                <input type="number" name="ayah" id="inputAyahStart" class="form-control" 
                                                       value="<?= esc($ayah_id ?? '') ?>" 
                                                       min="1" 
                                                       placeholder="Contoh: 3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Ayat Akhir</label>
                                                <input type="number" name="ayah_end" id="inputAyahEnd" class="form-control" 
                                                       value="<?= esc($ayah_end ?? '') ?>" 
                                                       min="1" 
                                                       placeholder="Contoh: 20" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-search"></i> Cari Range
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <small><i class="fas fa-info-circle"></i> Contoh: Surah 2, Ayat 3-20 akan menampilkan ayat 3 sampai 20 dari surah Al-Baqarah</small>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <script>
                            // Simpan dan pulihkan tab yang dipilih
                            (function() {
                                const STORAGE_KEY_TAB = 'quran_ayah_selected_tab';
                                
                                // Fungsi untuk menyimpan tab yang dipilih
                                function saveSelectedTab(tabId) {
                                    localStorage.setItem(STORAGE_KEY_TAB, tabId);
                                }
                                
                                // Fungsi untuk memulihkan tab yang tersimpan
                                function restoreSelectedTab() {
                                    const savedTab = localStorage.getItem(STORAGE_KEY_TAB);
                                    if (savedTab) {
                                        // Hapus class active dari semua tab
                                        document.querySelectorAll('#ayahTabs .nav-link').forEach(function(tab) {
                                            tab.classList.remove('active');
                                        });
                                        document.querySelectorAll('#ayahTabContent .tab-pane').forEach(function(pane) {
                                            pane.classList.remove('show', 'active');
                                        });
                                        
                                        // Aktifkan tab yang tersimpan
                                        const targetTab = document.querySelector('#ayahTabs a[href="#' + savedTab + '"]');
                                        const targetPane = document.getElementById(savedTab);
                                        
                                        if (targetTab && targetPane) {
                                            targetTab.classList.add('active');
                                            targetPane.classList.add('show', 'active');
                                        }
                                    }
                                }
                                
                                // Event listener untuk tab click
                                document.querySelectorAll('#ayahTabs .nav-link').forEach(function(tab) {
                                    // Gunakan event Bootstrap jika tersedia
                                    tab.addEventListener('shown.bs.tab', function(e) {
                                        const targetId = e.target.getAttribute('href').substring(1); // Hapus # dari href
                                        saveSelectedTab(targetId);
                                    });
                                    
                                    // Fallback untuk click biasa (jika Bootstrap event tidak tersedia)
                                    tab.addEventListener('click', function(e) {
                                        const targetId = this.getAttribute('href').substring(1); // Hapus # dari href
                                        // Delay sedikit untuk memastikan tab sudah aktif
                                        setTimeout(function() {
                                            saveSelectedTab(targetId);
                                        }, 100);
                                    });
                                });
                                
                                // Pulihkan tab saat halaman dimuat
                                if (document.readyState === 'loading') {
                                    document.addEventListener('DOMContentLoaded', restoreSelectedTab);
                                } else {
                                    restoreSelectedTab();
                                }
                            })();
                            
                            // Form ayat tunggal
                            document.getElementById('formCariAyat').addEventListener('submit', function(e) {
                                e.preventDefault();
                                const surah = document.getElementById('inputSurah').value;
                                const ayah = document.getElementById('inputAyah').value;
                                
                                if (surah && ayah) {
                                    window.location.href = '<?= base_url('backend/surah') ?>/' + surah + '/' + ayah;
                                } else {
                                    alert('Silakan isi nomor surah dan nomor ayat');
                                }
                            });
                            
                            // Form range ayat
                            document.getElementById('formCariRangeAyat').addEventListener('submit', function(e) {
                                e.preventDefault();
                                const surah = document.getElementById('inputSurahRange').value;
                                const ayahStart = document.getElementById('inputAyahStart').value;
                                const ayahEnd = document.getElementById('inputAyahEnd').value;
                                
                                if (!surah || !ayahStart || !ayahEnd) {
                                    alert('Silakan isi nomor surah, ayat awal, dan ayat akhir');
                                    return;
                                }
                                
                                if (parseInt(ayahStart) > parseInt(ayahEnd)) {
                                    alert('Ayat awal tidak boleh lebih besar dari ayat akhir');
                                    return;
                                }
                                
                                window.location.href = '<?= base_url('backend/surah') ?>/' + surah + '/' + ayahStart + '/' + ayahEnd;
                            });
                        </script>

                        <?php if (isset($result) && $result['success']): ?>
                            <?php if (isset($is_range) && $is_range): ?>
                                <!-- Tampilan Range Ayat -->
                                <div class="alert alert-success">
                                    <h5><i class="icon fas fa-check"></i> Range Ayat Ditemukan</h5>
                                    <p><strong>Surah:</strong> <?= esc($result['surah_name'] ?? '-') ?> (<?= esc($result['surah_name_english'] ?? '-') ?>)</p>
                                    <p><strong>Range Ayat:</strong> <?= esc($result['ayah_start'] ?? '-') ?> - <?= esc($result['ayah_end'] ?? '-') ?></p>
                                    <p><strong>Total Ayat:</strong> <?= esc($result['total_ayahs'] ?? 0) ?> ayat</p>
                                </div>

                                <div class="card">
                                    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                                        <h3 class="card-title text-white mb-0">
                                            Surah <?= esc($result['surah_name_english'] ?? '') ?> 
                                            Ayat <?= esc($result['ayah_start'] ?? '') ?> - <?= esc($result['ayah_end'] ?? '') ?>
                                        </h3>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-light btn-sm" id="btnZoomOutRange" title="Zoom Out">
                                                <i class="fas fa-search-minus"></i> Zoom Out
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm" id="btnZoomInRange" title="Zoom In">
                                                <i class="fas fa-search-plus"></i> Zoom In
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm" id="btnResetZoomRange" title="Reset Zoom">
                                                <i class="fas fa-undo"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($result['ayahs'])): ?>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width: 100px;">No. Ayat</th>
                                                            <th>Ayat (Arab)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($result['ayahs'] as $ayah): ?>
                                                            <tr>
                                                                <td class="text-center align-middle" style="vertical-align: middle;">
                                                                    <strong style="font-size: 18px; color: #007bff;"><?= esc($ayah['ayah_number'] ?? '-') ?></strong>
                                                                </td>
                                                                <td class="ayah-text-range" style="text-align: right; direction: rtl; font-family: 'Amiri', 'Traditional Arabic', 'Arial', serif; padding: 10px 15px;">
                                                                    <?= esc($ayah['text'] ?? '-') ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted">Tidak ada ayat yang ditemukan.</p>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3">
                                            <a href="<?= base_url('backend/surah/' . $result['surah_number']) ?>" 
                                               class="btn btn-info mr-2">
                                                <i class="fas fa-book"></i> Lihat Seluruh Surah
                                            </a>
                                            <a href="<?= base_url('backend/ayah') ?>" 
                                               class="btn btn-secondary">
                                                <i class="fas fa-search"></i> Cari Ayat Lain
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Tampilan Ayat Tunggal -->
                                <div class="alert alert-success">
                                    <h5><i class="icon fas fa-check"></i> Ayat Ditemukan</h5>
                                    <p><strong>Surah:</strong> <?= esc($result['surah_name'] ?? '-') ?> (<?= esc($result['surah_name_english'] ?? '-') ?>)</p>
                                    <p><strong>Nomor Ayat:</strong> <?= esc($result['ayah_number'] ?? '-') ?></p>
                                    <p><strong>Juz:</strong> <?= esc($result['juz'] ?? '-') ?></p>
                                    <p><strong>Halaman:</strong> <?= esc($result['page'] ?? '-') ?></p>
                                </div>

                                <div class="card">
                                    <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                                        <h3 class="card-title text-white mb-0">
                                            Surah <?= esc($result['surah_name_english'] ?? '') ?> 
                                            Ayat <?= esc($result['ayah_number'] ?? '') ?>
                                        </h3>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-light btn-sm" id="btnZoomOutSingle" title="Zoom Out">
                                                <i class="fas fa-search-minus"></i> Zoom Out
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm" id="btnZoomInSingle" title="Zoom In">
                                                <i class="fas fa-search-plus"></i> Zoom In
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm" id="btnResetZoomSingle" title="Reset Zoom">
                                                <i class="fas fa-undo"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center p-3 mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                            <div style="background-color: rgba(255,255,255,0.95); padding: 15px 20px; border-radius: 8px; margin: 5px;">
                                                <p id="ayah-text-single" class="ayah-text-single" style="text-align: right; direction: rtl; font-family: 'Amiri', 'Traditional Arabic', 'Arial', serif; color: #2c3e50; margin: 0; transition: font-size 0.3s ease;">
                                                    <?= esc($result['text'] ?? '-') ?>
                                                </p>
                                            </div>
                                        </div>
                                    
                                    <!-- Navigation Ayat -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <?php if (isset($result['ayah_number']) && $result['ayah_number'] > 1): ?>
                                                <a href="<?= base_url('backend/surah/' . $result['surah_number'] . '/' . ($result['ayah_number'] - 1)) ?>" 
                                                   class="btn btn-outline-primary btn-block">
                                                    <i class="fas fa-arrow-right"></i> Ayat Sebelumnya (<?= $result['ayah_number'] - 1 ?>)
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="<?= base_url('backend/surah/' . $result['surah_number'] . '/' . ($result['ayah_number'] + 1)) ?>" 
                                               class="btn btn-outline-primary btn-block">
                                                Ayat Selanjutnya (<?= $result['ayah_number'] + 1 ?>) <i class="fas fa-arrow-left"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h5>Informasi Ayat:</h5>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 200px;">Nomor dalam Al-Qur'an</th>
                                                <td><?= esc($result['number'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Nomor dalam Surah</th>
                                                <td><?= esc($result['number_in_surah'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Juz</th>
                                                <td><?= esc($result['juz'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Manzil</th>
                                                <td><?= esc($result['manzil'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Halaman</th>
                                                <td><?= esc($result['page'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Ruku</th>
                                                <td><?= esc($result['ruku'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Hizb Quarter</th>
                                                <td><?= esc($result['hizb_quarter'] ?? '-') ?></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <a href="<?= base_url('backend/surah/' . $result['surah_number']) ?>" 
                                           class="btn btn-info mr-2">
                                            <i class="fas fa-book"></i> Lihat Seluruh Surah
                                        </a>
                                        <a href="<?= base_url('backend/quran/search') ?>" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-search"></i> Cari Ayat Lain
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php elseif (isset($result) && !$result['success']): ?>
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <p><?= esc($result['error'] ?? 'Terjadi kesalahan saat mengambil data ayat') ?></p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Informasi</h5>
                                <p>Silakan masukkan nomor surah dan nomor ayat untuk melihat ayat spesifik.</p>
                                <p><strong>Contoh:</strong></p>
                                <ul>
                                    <li>Surah: 1, Ayat: 1 = Al-Fatihah ayat 1</li>
                                    <li>Surah: 2, Ayat: 255 = Al-Baqarah ayat 255 (Ayat Kursi)</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Tunggu DOM selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi Zoom untuk Ayat Tunggal
        (function() {
            const STORAGE_KEY_SINGLE = 'quran_ayah_zoom_single';
            const DEFAULT_FONT_SIZE_SINGLE = 40;
            const MIN_FONT_SIZE_SINGLE = 20;
            const MAX_FONT_SIZE_SINGLE = 72;
            const ZOOM_STEP_SINGLE = 6;

            let currentFontSizeSingle = DEFAULT_FONT_SIZE_SINGLE;
            
            // Load saved zoom level
            const savedZoom = localStorage.getItem(STORAGE_KEY_SINGLE);
            if (savedZoom) {
                currentFontSizeSingle = parseInt(savedZoom);
            }

            const ayahTextSingle = document.getElementById('ayah-text-single');
            const btnZoomInSingle = document.getElementById('btnZoomInSingle');
            const btnZoomOutSingle = document.getElementById('btnZoomOutSingle');
            const btnResetZoomSingle = document.getElementById('btnResetZoomSingle');

            if (ayahTextSingle) {
                // Apply saved zoom
                ayahTextSingle.style.fontSize = currentFontSizeSingle + 'px';
                ayahTextSingle.style.lineHeight = '1.4';
                
                // Update line height based on font size
                const updateLineHeight = () => {
                    ayahTextSingle.style.lineHeight = '1.4';
                };

                if (btnZoomInSingle) {
                    btnZoomInSingle.addEventListener('click', function() {
                        if (currentFontSizeSingle < MAX_FONT_SIZE_SINGLE) {
                            currentFontSizeSingle = Math.min(currentFontSizeSingle + ZOOM_STEP_SINGLE, MAX_FONT_SIZE_SINGLE);
                            ayahTextSingle.style.fontSize = currentFontSizeSingle + 'px';
                            updateLineHeight();
                            localStorage.setItem(STORAGE_KEY_SINGLE, currentFontSizeSingle);
                        }
                    });
                }

                if (btnZoomOutSingle) {
                    btnZoomOutSingle.addEventListener('click', function() {
                        if (currentFontSizeSingle > MIN_FONT_SIZE_SINGLE) {
                            currentFontSizeSingle = Math.max(currentFontSizeSingle - ZOOM_STEP_SINGLE, MIN_FONT_SIZE_SINGLE);
                            ayahTextSingle.style.fontSize = currentFontSizeSingle + 'px';
                            updateLineHeight();
                            localStorage.setItem(STORAGE_KEY_SINGLE, currentFontSizeSingle);
                        }
                    });
                }

                if (btnResetZoomSingle) {
                    btnResetZoomSingle.addEventListener('click', function() {
                        currentFontSizeSingle = DEFAULT_FONT_SIZE_SINGLE;
                        ayahTextSingle.style.fontSize = currentFontSizeSingle + 'px';
                        updateLineHeight();
                        localStorage.setItem(STORAGE_KEY_SINGLE, currentFontSizeSingle);
                    });
                }
            }
        })();

        // Fungsi Zoom untuk Range Ayat
        (function() {
            const STORAGE_KEY_RANGE = 'quran_ayah_zoom_range';
            const DEFAULT_FONT_SIZE_RANGE = 36;
            const MIN_FONT_SIZE_RANGE = 20;
            const MAX_FONT_SIZE_RANGE = 64;
            const ZOOM_STEP_RANGE = 6;

            let currentFontSizeRange = DEFAULT_FONT_SIZE_RANGE;
            
            // Load saved zoom level
            const savedZoom = localStorage.getItem(STORAGE_KEY_RANGE);
            if (savedZoom) {
                currentFontSizeRange = parseInt(savedZoom);
            }

            const ayahTextsRange = document.querySelectorAll('.ayah-text-range');
            const btnZoomInRange = document.getElementById('btnZoomInRange');
            const btnZoomOutRange = document.getElementById('btnZoomOutRange');
            const btnResetZoomRange = document.getElementById('btnResetZoomRange');

            if (ayahTextsRange.length > 0) {
                // Apply saved zoom to all range ayah texts
                ayahTextsRange.forEach(function(element) {
                    element.style.fontSize = currentFontSizeRange + 'px';
                    element.style.transition = 'font-size 0.3s ease';
                    element.style.lineHeight = '1.3';
                });

                const updateAllRangeFonts = () => {
                    ayahTextsRange.forEach(function(element) {
                        element.style.fontSize = currentFontSizeRange + 'px';
                        element.style.lineHeight = '1.3';
                    });
                };

                if (btnZoomInRange) {
                    btnZoomInRange.addEventListener('click', function() {
                        if (currentFontSizeRange < MAX_FONT_SIZE_RANGE) {
                            currentFontSizeRange = Math.min(currentFontSizeRange + ZOOM_STEP_RANGE, MAX_FONT_SIZE_RANGE);
                            updateAllRangeFonts();
                            localStorage.setItem(STORAGE_KEY_RANGE, currentFontSizeRange);
                        }
                    });
                }

                if (btnZoomOutRange) {
                    btnZoomOutRange.addEventListener('click', function() {
                        if (currentFontSizeRange > MIN_FONT_SIZE_RANGE) {
                            currentFontSizeRange = Math.max(currentFontSizeRange - ZOOM_STEP_RANGE, MIN_FONT_SIZE_RANGE);
                            updateAllRangeFonts();
                            localStorage.setItem(STORAGE_KEY_RANGE, currentFontSizeRange);
                        }
                    });
                }

                if (btnResetZoomRange) {
                    btnResetZoomRange.addEventListener('click', function() {
                        currentFontSizeRange = DEFAULT_FONT_SIZE_RANGE;
                        updateAllRangeFonts();
                        localStorage.setItem(STORAGE_KEY_RANGE, currentFontSizeRange);
                    });
                }
            }
        })();
    });
</script>

<?= $this->endSection(); ?>

