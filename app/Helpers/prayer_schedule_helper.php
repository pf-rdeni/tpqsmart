<?php

if (!function_exists('prayer_schedule_widget')) {
    /**
     * Render widget jadwal sholat lengkap (HTML + CSS + Modal)
     * 
     * @return string HTML widget jadwal sholat
     */
    function prayer_schedule_widget()
    {
        return '
        <!-- Jadwal Sholat -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-mosque"></i> Jadwal Sholat Hari Ini
                        </h3>
                        <div class="card-tools">
                            <button type="button" id="openLocationSettings" class="btn btn-tool" title="Pengaturan Lokasi">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="prayerScheduleLoading" class="text-center py-3">
                            <i class="fas fa-spinner fa-spin"></i> Mengambil lokasi GPS...
                        </div>
                        <div id="prayerScheduleError" class="alert alert-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> <span id="prayerScheduleErrorMsg"></span>
                        </div>
                        <div id="prayerScheduleContent" style="display: none;">
                            ' . prayer_schedule_css() . '
                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span id="prayerLocation">Lokasi: -</span>
                                    </small>
                                    <div class="mt-1 text-secondary">
                                        <i class="far fa-clock"></i>
                                        <span id="currentDateTime">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-stretch">
                                <div class="col-12 col-lg-9">
                                    <div class="row">
                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="fajr">
                                                <div class="prayer-title text-muted">
                                                    <i class="fas fa-mosque prayer-icon"></i>Subuh
                                                </div>
                                                <div class="h5 mb-0" id="time-fajr">-</div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="shurooq">
                                                <div class="prayer-title text-muted">
                                                    <i class="fas fa-mosque prayer-icon"></i>Syuruq
                                                </div>
                                                <div class="h5 mb-0" id="time-shurooq">-</div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="dhuhr">
                                                <div class="prayer-title text-muted">
                                                    <i class="fas fa-mosque prayer-icon"></i>Dzuhur
                                                </div>
                                                <div class="h5 mb-0" id="time-dhuhr">-</div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="asr">
                                                <div class="prayer-title text-muted">
                                                    <i class="fas fa-mosque prayer-icon"></i>Ashar
                                                </div>
                                                <div class="h5 mb-0" id="time-asr">-</div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="maghrib">
                                                <div class="prayer-title text-muted">
                                                    <i class="fas fa-mosque prayer-icon"></i>Maghrib
                                                </div>
                                                <div class="h5 mb-0" id="time-maghrib">-</div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-2 mb-3 d-flex">
                                            <div class="prayer-card text-center prayer-time w-100" data-prayer="isha">
                                                <div class="prayer-title text-muted">
                                                    <i class="fas fa-mosque prayer-icon"></i>Isya
                                                </div>
                                                <div class="h5 mb-0" id="time-isha">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-3 d-flex">
                                    <div class="prayer-card text-center w-100 bg-info text-white">
                                        <div class="prayer-title" id="nextPrayerName">-</div>
                                        <div class="h5 mb-0" id="countdown">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">Jadwal Besok <span id="nextDayDate">-</span></small>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0 text-center align-middle">
                                            <thead class="bg-light">
                                                <tr class="text-muted" style="font-size: 0.5rem;">
                                                    <th class="py-1">Subuh</th>
                                                    <th class="py-1">Syuruq</th>
                                                    <th class="py-1">Dzuhur</th>
                                                    <th class="py-1">Ashar</th>
                                                    <th class="py-1">Maghrib</th>
                                                    <th class="py-1">Isya</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="small font-weight-bold">
                                                    <td class="py-1" id="nextday-time-fajr">-</td>
                                                    <td class="py-1" id="nextday-time-shurooq">-</td>
                                                    <td class="py-1" id="nextday-time-dhuhr">-</td>
                                                    <td class="py-1" id="nextday-time-asr">-</td>
                                                    <td class="py-1" id="nextday-time-maghrib">-</td>
                                                    <td class="py-1" id="nextday-time-isha">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ' . prayer_schedule_modal();
    }
}

if (!function_exists('prayer_schedule_modal')) {
    /**
     * Render modal untuk pengaturan lokasi jadwal sholat
     * 
     * @return string HTML modal
     */
    function prayer_schedule_modal()
    {
        return '
        <!-- Modal Pengaturan Lokasi -->
        <div class="modal fade" id="locationSettingsModal" tabindex="-1" role="dialog" aria-labelledby="locationSettingsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="locationSettingsModalLabel">
                            <i class="fas fa-cog"></i> Pengaturan Lokasi
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="locationModeModal">Pilih Sumber Lokasi</label>
                            <select id="locationModeModal" class="form-control">
                                <option value="gps">Lokasi GPS</option>
                                <option value="default">Default (Bintan)</option>
                                <option value="manual">Manual (Ketik Kota)</option>
                            </select>
                        </div>
                        <div class="form-group" id="manualCityContainerModal" style="display: none;">
                            <label for="manualCityModal">Nama Kota</label>
                            <div style="position: relative;">
                                <input type="text" id="manualCityModal" class="form-control" placeholder="Ketik kota..." autocomplete="off">
                                <div id="citySuggestionsModal" class="city-suggestions" style="display: none;"></div>
                            </div>
                            <small class="form-text text-muted">Ketik nama kota untuk mencari jadwal sholat</small>
                        </div>
                        <div class="alert alert-info" id="locationInfoModal" style="display: none; margin-top: 15px;">
                            <i class="fas fa-info-circle"></i> <span id="locationInfoTextModal"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="applyLocationSettings">
                            <i class="fas fa-check"></i> Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }
}

if (!function_exists('prayer_schedule_css')) {
    /**
     * Render CSS untuk jadwal sholat
     * 
     * @return string CSS styles
     */
    function prayer_schedule_css()
    {
        return '
        <style>
            .prayer-card {
                border: 1px solid #e3e6f0;
                border-radius: 10px;
                padding: 12px;
                background: #f8f9fa;
                transition: all 0.2s ease;
                height: 100%;
            }

            .prayer-card.bg-success {
                background: #28a745 !important;
                color: #fff !important;
                border: none;
            }

            .prayer-card.bg-success .prayer-title {
                color: #e8f5e9 !important;
            }

            .prayer-card .prayer-icon {
                display: none;
            }

            .prayer-card.bg-success .prayer-icon {
                display: inline-block;
                margin-right: 4px;
            }

            .prayer-card.bg-warning {
                background: #ffc107 !important;
                color: #212529 !important;
                border: none;
            }

            .prayer-card.bg-warning .prayer-title {
                color: #5c4a00 !important;
            }

            .prayer-card:hover {
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
                transform: translateY(-2px);
            }

            .prayer-title {
                font-size: 0.7rem;
                letter-spacing: 0.3px;
            }

            .city-suggestions {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
                margin-top: 2px;
            }

            .city-suggestion-item {
                padding: 8px 12px;
                cursor: pointer;
                border-bottom: 1px solid #f0f0f0;
                transition: background-color 0.2s;
            }

            .city-suggestion-item:hover {
                background-color: #f8f9fa;
            }

            .city-suggestion-item:last-child {
                border-bottom: none;
            }

            .city-suggestion-item.highlight {
                background-color: #e3f2fd;
            }
        </style>';
    }
}

if (!function_exists('prayer_schedule_settings_js')) {
    /**
     * Render JavaScript untuk pengaturan lokasi jadwal sholat
     * 
     * @param string $baseUrl Base URL untuk API endpoint
     * @return string JavaScript code
     */
    function prayer_schedule_settings_js($baseUrl = '')
    {
        if (empty($baseUrl)) {
            $baseUrl = base_url('backend/jadwal-sholat');
        }
        
        return '
        <script>
        (function() {
            const DEFAULT_CITY = "Bintan";
            const LOCATION_SETTING_KEY = "prayerLocationSetting";
            let citySuggestionsTimeout = null;
            let currentSuggestionIndex = -1;

            // List kota populer untuk autocomplete
            const popularCities = [
                "Jakarta", "Bandung", "Surabaya", "Medan", "Semarang", "Makassar", "Palembang",
                "Depok", "Tangerang", "Bekasi", "Yogyakarta", "Malang", "Surakarta", "Bogor",
                "Batam", "Pekanbaru", "Padang", "Denpasar", "Banjarmasin", "Pontianak", "Bintan",
                "Tanjung Pinang", "Jakarta Selatan", "Jakarta Utara", "Jakarta Timur", "Jakarta Barat",
                "Jakarta Pusat", "Bandung Barat", "Surabaya Utara", "Surabaya Selatan", "Medan Selayang",
                "Semarang Tengah", "Makassar Utara", "Palembang Ilir", "Depok Timur", "Tangerang Selatan",
                "Bekasi Timur", "Yogyakarta Utara", "Malang Utara", "Bogor Selatan", "Batam Center",
                "Pekanbaru Barat", "Padang Barat", "Denpasar Selatan", "Banjarmasin Utara", "Pontianak Utara"
            ];

            // Load location setting from localStorage
            function loadLocationSetting() {
                try {
                    const saved = localStorage.getItem(LOCATION_SETTING_KEY);
                    if (!saved) return {
                        mode: "gps",
                        city: DEFAULT_CITY
                    };
                    const parsed = JSON.parse(saved);
                    if (!parsed.mode) return {
                        mode: "gps",
                        city: DEFAULT_CITY
                    };
                    return {
                        mode: parsed.mode || "gps",
                        city: parsed.city || DEFAULT_CITY
                    };
                } catch (e) {
                    return {
                        mode: "gps",
                        city: DEFAULT_CITY
                    };
                }
            }

            // Save location setting to localStorage
            function saveLocationSetting(setting) {
                localStorage.setItem(LOCATION_SETTING_KEY, JSON.stringify(setting));
            }

            // Toggle manual input visibility in modal
            function toggleManualInputVisibilityModal(mode) {
                const manualInputContainer = document.getElementById("manualCityContainerModal");
                const suggestionsEl = document.getElementById("citySuggestionsModal");
                
                if (mode === "manual") {
                    if (manualInputContainer) manualInputContainer.style.display = "block";
                } else {
                    if (manualInputContainer) manualInputContainer.style.display = "none";
                    if (suggestionsEl) suggestionsEl.style.display = "none";
                }
            }

            // Open location settings modal
            window.openLocationSettingsModal = function() {
                const modal = document.getElementById("locationSettingsModal");
                const modeEl = document.getElementById("locationModeModal");
                const manualInput = document.getElementById("manualCityModal");
                const saved = loadLocationSetting();
                
                if (!modal || !modeEl) return;
                
                // Set values from saved settings
                if (modeEl) modeEl.value = saved.mode;
                if (manualInput) manualInput.value = saved.city || "";
                toggleManualInputVisibilityModal(saved.mode);
                
                // Show modal using Bootstrap
                if (typeof $ !== "undefined") {
                    $(modal).modal("show");
                } else {
                    modal.classList.add("show");
                    modal.style.display = "block";
                }
            };

            // Apply location settings
            window.applyLocationSettings = function() {
                const modeEl = document.getElementById("locationModeModal");
                const manualInput = document.getElementById("manualCityModal");
                const modal = document.getElementById("locationSettingsModal");
                
                if (!modeEl) return;
                
                const mode = modeEl.value;
                const manualCity = manualInput ? manualInput.value.trim() : "";
                
                // Close modal
                if (modal) {
                    if (typeof $ !== "undefined") {
                        $(modal).modal("hide");
                    } else {
                        modal.classList.remove("show");
                        modal.style.display = "none";
                    }
                }
                
                // Save settings
                if (mode === "gps") {
                    saveLocationSetting({
                        mode: "gps",
                        city: DEFAULT_CITY
                    });
                } else if (mode === "default") {
                    saveLocationSetting({
                        mode: "default",
                        city: DEFAULT_CITY
                    });
                } else {
                    const targetCity = manualCity || DEFAULT_CITY;
                    saveLocationSetting({
                        mode: "manual",
                        city: targetCity
                    });
                }
                
                // Trigger refresh if callback exists
                if (typeof window.refreshPrayerTimesByMode === "function") {
                    window.refreshPrayerTimesByMode();
                } else if (typeof window.refreshPrayerTimesByModeFromModal === "function") {
                    window.refreshPrayerTimesByModeFromModal(mode, manualCity);
                }
            };

            // Show city suggestions for modal
            function showCitySuggestionsModal(query) {
                const suggestionsEl = document.getElementById("citySuggestionsModal");
                if (!suggestionsEl) return;

                if (citySuggestionsTimeout) {
                    clearTimeout(citySuggestionsTimeout);
                }

                if (!query || query.trim().length < 2) {
                    suggestionsEl.style.display = "none";
                    currentSuggestionIndex = -1;
                    return;
                }

                const queryLower = query.toLowerCase().trim();
                const filtered = popularCities.filter(city => 
                    city.toLowerCase().includes(queryLower)
                ).slice(0, 10);

                if (filtered.length === 0) {
                    suggestionsEl.style.display = "none";
                    currentSuggestionIndex = -1;
                    return;
                }

                suggestionsEl.innerHTML = "";
                filtered.forEach((city, index) => {
                    const item = document.createElement("div");
                    item.className = "city-suggestion-item";
                    item.textContent = city;
                    item.dataset.city = city;
                    
                    const regex = new RegExp(`(${query})`, "gi");
                    item.innerHTML = city.replace(regex, "<strong>$1</strong>");
                    
                    item.addEventListener("click", function() {
                        selectCityModal(city);
                    });
                    
                    suggestionsEl.appendChild(item);
                });

                suggestionsEl.style.display = "block";
                currentSuggestionIndex = -1;
            }

            // Select city from suggestion in modal
            function selectCityModal(city) {
                const manualInput = document.getElementById("manualCityModal");
                const suggestionsEl = document.getElementById("citySuggestionsModal");
                
                if (manualInput) {
                    manualInput.value = city;
                }
                
                if (suggestionsEl) {
                    suggestionsEl.style.display = "none";
                }
                
                currentSuggestionIndex = -1;
            }

            // Initialize location controls
            window.initPrayerScheduleSettings = function() {
                const openBtn = document.getElementById("openLocationSettings");
                const modeElModal = document.getElementById("locationModeModal");
                const manualInputModal = document.getElementById("manualCityModal");
                const applyBtn = document.getElementById("applyLocationSettings");

                // Open modal button event listener
                if (openBtn) {
                    openBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        window.openLocationSettingsModal();
                    });
                }

                // Location mode change in modal
                if (modeElModal) {
                    modeElModal.addEventListener("change", function() {
                        toggleManualInputVisibilityModal(this.value);
                    });
                }

                // Live search suggestion untuk manual input di modal
                if (manualInputModal) {
                    manualInputModal.addEventListener("input", function() {
                        const query = this.value;
                        
                        if (citySuggestionsTimeout) {
                            clearTimeout(citySuggestionsTimeout);
                        }
                        citySuggestionsTimeout = setTimeout(() => {
                            showCitySuggestionsModal(query);
                        }, 300);
                    });

                    // Keyboard navigation untuk suggestion di modal
                    manualInputModal.addEventListener("keydown", function(e) {
                        const suggestionsEl = document.getElementById("citySuggestionsModal");
                        if (!suggestionsEl || suggestionsEl.style.display === "none") {
                            return;
                        }

                        const items = suggestionsEl.querySelectorAll(".city-suggestion-item");
                        if (items.length === 0) return;

                        if (e.key === "ArrowDown") {
                            e.preventDefault();
                            currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, items.length - 1);
                            items.forEach((item, index) => {
                                item.classList.toggle("highlight", index === currentSuggestionIndex);
                            });
                        } else if (e.key === "ArrowUp") {
                            e.preventDefault();
                            currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
                            items.forEach((item, index) => {
                                item.classList.toggle("highlight", index === currentSuggestionIndex);
                            });
                        } else if (e.key === "Enter" && currentSuggestionIndex >= 0) {
                            e.preventDefault();
                            const selectedCity = items[currentSuggestionIndex].dataset.city;
                            if (selectedCity) {
                                selectCityModal(selectedCity);
                            }
                        } else if (e.key === "Escape") {
                            suggestionsEl.style.display = "none";
                            currentSuggestionIndex = -1;
                        }
                    });
                }

                // Apply button event listener
                if (applyBtn) {
                    applyBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        window.applyLocationSettings();
                    });
                }

                // Tutup suggestion saat klik di luar (di modal)
                document.addEventListener("click", function(e) {
                    const manualInputContainer = document.getElementById("manualCityContainerModal");
                    const suggestionsEl = document.getElementById("citySuggestionsModal");
                    
                    if (manualInputContainer && suggestionsEl && 
                        !manualInputContainer.contains(e.target)) {
                        suggestionsEl.style.display = "none";
                        currentSuggestionIndex = -1;
                    }
                });
            };

            // Auto initialize when DOM is ready
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", function() {
                    window.initPrayerScheduleSettings();
                });
            } else {
                window.initPrayerScheduleSettings();
            }
        })();
        </script>';
    }
}

if (!function_exists('prayer_schedule_js')) {
    /**
     * Render JavaScript lengkap untuk jadwal sholat
     * 
     * @param string $baseUrl Base URL untuk API endpoint
     * @return string JavaScript code
     */
    function prayer_schedule_js($baseUrl = '')
    {
        if (empty($baseUrl)) {
            $baseUrl = base_url('backend/jadwal-sholat');
        }
        
        return '
        <script>
        $(document).ready(function() {
            // Jadwal Sholat
            let prayerTimes = {};
            let currentPrayerIndex = -1;
            let nextPrayerIndex = -1;
            let countdownInterval = null;
            let nextPrayerTimeStr = null;
            let nextIsTomorrow = false;
            let clockInterval = null;
            let nextDayPrayerTimes = {};
            let lastLocationSetting = null;
            let manualInputTimeout = null;
            let citySuggestionsTimeout = null;
            let currentSuggestionIndex = -1;
            let currentLocation = null;
            let isInitialLoad = true;
            const DEFAULT_CITY = "Bintan";
            const LOCATION_SETTING_KEY = "prayerLocationSetting";

            // List kota populer untuk autocomplete
            const popularCities = [
                "Jakarta", "Bandung", "Surabaya", "Medan", "Semarang", "Makassar", "Palembang",
                "Depok", "Tangerang", "Bekasi", "Yogyakarta", "Malang", "Surakarta", "Bogor",
                "Batam", "Pekanbaru", "Padang", "Denpasar", "Banjarmasin", "Pontianak", "Bintan",
                "Tanjung Pinang", "Jakarta Selatan", "Jakarta Utara", "Jakarta Timur", "Jakarta Barat",
                "Jakarta Pusat", "Bandung Barat", "Surabaya Utara", "Surabaya Selatan", "Medan Selayang",
                "Semarang Tengah", "Makassar Utara", "Palembang Ilir", "Depok Timur", "Tangerang Selatan",
                "Bekasi Timur", "Yogyakarta Utara", "Malang Utara", "Bogor Selatan", "Batam Center",
                "Pekanbaru Barat", "Padang Barat", "Denpasar Selatan", "Banjarmasin Utara", "Pontianak Utara"
            ];

            // Prayer order
            const prayerOrder = ["fajr", "shurooq", "dhuhr", "asr", "maghrib", "isha"];
            const prayerNames = {
                "fajr": "Subuh",
                "shurooq": "Syuruq",
                "dhuhr": "Dzuhur",
                "asr": "Ashar",
                "maghrib": "Maghrib",
                "isha": "Isya"
            };

            function loadLocationSetting() {
                try {
                    const saved = localStorage.getItem(LOCATION_SETTING_KEY);
                    if (!saved) return { mode: "gps", city: DEFAULT_CITY };
                    const parsed = JSON.parse(saved);
                    if (!parsed.mode) return { mode: "gps", city: DEFAULT_CITY };
                    return { mode: parsed.mode || "gps", city: parsed.city || DEFAULT_CITY };
                } catch (e) {
                    return { mode: "gps", city: DEFAULT_CITY };
                }
            }

            function saveLocationSetting(setting) {
                lastLocationSetting = setting;
                localStorage.setItem(LOCATION_SETTING_KEY, JSON.stringify(setting));
            }

            function showSuccessNotification(message) {
                if (typeof toastr !== "undefined") {
                    toastr.success(message, "", {
                        timeOut: 3000,
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-top-right",
                        preventDuplicates: true,
                        newestOnTop: true
                    });
                } else {
                    const notification = document.createElement("div");
                    notification.className = "alert alert-success alert-dismissible fade show";
                    notification.style.cssText = "position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 250px; max-width: 350px; font-size: 0.85rem; padding: 10px 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.15);";
                    notification.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-check-circle" style="font-size: 1rem;"></i>
                            <span style="flex: 1;">${message}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="opacity: 0.7; font-size: 1.2rem;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    setTimeout(() => {
                        notification.classList.remove("show");
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }, 3000);
                }
            }

            function showWarningNotification(message) {
                if (typeof toastr !== "undefined") {
                    toastr.warning(message, "Peringatan", {
                        timeOut: 4000,
                        closeButton: true,
                        progressBar: true
                    });
                } else {
                    const notification = document.createElement("div");
                    notification.className = "alert alert-warning alert-dismissible fade show";
                    notification.style.cssText = "position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
                    notification.innerHTML = `
                        <strong>Peringatan!</strong> ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    `;
                    document.body.appendChild(notification);
                    setTimeout(() => {
                        notification.classList.remove("show");
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }, 4000);
                }
            }

            function setLoadingState() {
                const loadingEl = document.getElementById("prayerScheduleLoading");
                const errorEl = document.getElementById("prayerScheduleError");
                const contentEl = document.getElementById("prayerScheduleContent");
                if (loadingEl) loadingEl.style.display = "block";
                if (errorEl) errorEl.style.display = "none";
                if (contentEl) contentEl.style.display = "none";
            }

            function showError(message) {
                const loadingEl = document.getElementById("prayerScheduleLoading");
                const errorEl = document.getElementById("prayerScheduleError");
                const errorMsgEl = document.getElementById("prayerScheduleErrorMsg");
                const contentEl = document.getElementById("prayerScheduleContent");
                if (loadingEl) loadingEl.style.display = "none";
                if (errorEl) errorEl.style.display = "block";
                if (errorMsgEl) errorMsgEl.textContent = message;
                if (contentEl) contentEl.style.display = "none";
            }

            function formatTime24(timeString) {
                const parsed = parseTime(timeString);
                if (!parsed) return timeString || "-";
                const hh = parsed.hours.toString().padStart(2, "0");
                const mm = parsed.minutes.toString().padStart(2, "0");
                return `${hh}:${mm}`;
            }

            function parseTime(timeString) {
                if (!timeString) return null;
                const trimmed = timeString.trim().toLowerCase();
                const hasAm = trimmed.includes("am");
                const hasPm = trimmed.includes("pm");
                const timeOnly = trimmed.replace(/\s*(am|pm)\s*/gi, "");
                const parts = timeOnly.split(":");
                if (parts.length !== 2) return null;
                let hours = parseInt(parts[0]);
                const minutes = parseInt(parts[1]);
                if (isNaN(hours) || isNaN(minutes)) return null;
                if (hasPm && hours !== 12) {
                    hours += 12;
                } else if (hasAm && hours === 12) {
                    hours = 0;
                }
                return { hours, minutes };
            }

            function getCurrentTime() {
                const now = new Date();
                return {
                    hours: now.getHours(),
                    minutes: now.getMinutes(),
                    seconds: now.getSeconds(),
                    totalMinutes: now.getHours() * 60 + now.getMinutes()
                };
            }

            function timeToMinutes(timeObj) {
                if (!timeObj) return null;
                return timeObj.hours * 60 + timeObj.minutes;
            }

            function displayPrayerTimes() {
                prayerOrder.forEach(prayer => {
                    const timeEl = document.getElementById(`time-${prayer}`);
                    if (timeEl && prayerTimes[prayer]) {
                        timeEl.textContent = formatTime24(prayerTimes[prayer]);
                    }
                });
            }

            function displayNextDayPrayerTimes() {
                prayerOrder.forEach(prayer => {
                    const el = document.getElementById(`nextday-time-${prayer}`);
                    if (el && nextDayPrayerTimes[prayer]) {
                        el.textContent = formatTime24(nextDayPrayerTimes[prayer]);
                    } else if (el) {
                        el.textContent = "-";
                    }
                });
            }

            function getTomorrowDates() {
                const t = new Date();
                t.setDate(t.getDate() + 1);
                const y = t.getFullYear();
                const m = String(t.getMonth() + 1).padStart(2, "0");
                const d = String(t.getDate()).padStart(2, "0");
                const iso = `${y}-${m}-${d}`;
                const display = t.toLocaleDateString("id-ID", {
                    weekday: "long",
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
                return { iso, display };
            }

            function fetchNextDayPrayerTimes(params) {
                const { mode, city, lat, lng, isDefault } = params;
                const { iso, display } = getTomorrowDates();
                const nextDayDateEl = document.getElementById("nextDayDate");
                if (nextDayDateEl) {
                    nextDayDateEl.textContent = display;
                }
                let url = "";
                if (mode === "city") {
                    url = `' . $baseUrl . '/${encodeURIComponent(city)}?format=json&date=${iso}`;
                } else {
                    url = `' . $baseUrl . '/${lat}/${lng}?format=json&date=${iso}`;
                }
                fetch(url)
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.success && data.prayer_times) {
                            nextDayPrayerTimes = data.prayer_times;
                            displayNextDayPrayerTimes();
                        } else if (isDefault) {
                            displayNextDayPrayerTimes();
                        } else {
                            fetchNextDayPrayerTimes({
                                mode: "city",
                                city: DEFAULT_CITY,
                                isDefault: true
                            });
                        }
                    })
                    .catch(() => {
                        if (!isDefault) {
                            fetchNextDayPrayerTimes({
                                mode: "city",
                                city: DEFAULT_CITY,
                                isDefault: true
                            });
                        }
                    });
            }

            function fetchPrayerTimesByCity(cityName, isDefault = false) {
                const loadingEl = document.getElementById("prayerScheduleLoading");
                const errorEl = document.getElementById("prayerScheduleError");
                const errorMsgEl = document.getElementById("prayerScheduleErrorMsg");
                const contentEl = document.getElementById("prayerScheduleContent");

                if (isDefault) {
                    const locEl = document.getElementById("prayerLocation");
                    if (locEl) locEl.textContent = `Lokasi: ${cityName} (Default)`;
                } else {
                    const locEl = document.getElementById("prayerLocation");
                    if (locEl) locEl.textContent = `Lokasi: ${cityName}`;
                }

                fetch(`' . $baseUrl . '/${encodeURIComponent(cityName)}?format=json`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.prayer_times) {
                            prayerTimes = data.prayer_times;
                            displayPrayerTimes();
                            updateCurrentAndNextPrayer();
                            fetchNextDayPrayerTimes({
                                mode: "city",
                                city: cityName,
                                isDefault
                            });
                            startCountdown();
                            if (loadingEl) loadingEl.style.display = "none";
                            if (contentEl) contentEl.style.display = "block";

                            const locationText = isDefault ? `${cityName} (Default)` : cityName;
                            const newLocation = `${isDefault ? "default" : "city"}:${cityName}`;

                            if (!isInitialLoad && currentLocation !== newLocation) {
                                showSuccessNotification(`Jadwal sholat untuk ${locationText} berhasil diperbarui`);
                            }

                            currentLocation = newLocation;
                            isInitialLoad = false;
                        } else {
                            if (!isDefault) {
                                const originalCity = cityName;
                                showWarningNotification(`Kota "${originalCity}" tidak ditemukan. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                                fetchPrayerTimesByCity(DEFAULT_CITY, true);
                            } else {
                                showError(data.error || "Gagal mengambil jadwal sholat");
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        if (!isDefault) {
                            showWarningNotification(`Terjadi kesalahan saat mengambil jadwal sholat. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                            fetchPrayerTimesByCity(DEFAULT_CITY, true);
                        } else {
                            showError("Terjadi kesalahan saat mengambil jadwal sholat");
                        }
                    });
            }

            function fetchPrayerTimesByCoordinate(lat, lng) {
                const loadingEl = document.getElementById("prayerScheduleLoading");
                const errorEl = document.getElementById("prayerScheduleError");
                const errorMsgEl = document.getElementById("prayerScheduleErrorMsg");
                const contentEl = document.getElementById("prayerScheduleContent");

                const locEl = document.getElementById("prayerLocation");
                if (locEl) locEl.textContent = `Lokasi: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;

                fetch(`' . $baseUrl . '/${lat}/${lng}?format=json`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.prayer_times) {
                            prayerTimes = data.prayer_times;
                            displayPrayerTimes();
                            updateCurrentAndNextPrayer();
                            fetchNextDayPrayerTimes({
                                mode: "coordinate",
                                lat,
                                lng,
                                isDefault: false
                            });
                            startCountdown();
                            if (loadingEl) loadingEl.style.display = "none";
                            if (contentEl) contentEl.style.display = "block";

                            const newLocation = `gps:${lat.toFixed(4)},${lng.toFixed(4)}`;

                            if (!isInitialLoad && currentLocation !== newLocation) {
                                showSuccessNotification("Jadwal sholat berdasarkan lokasi GPS berhasil diperbarui");
                            }

                            currentLocation = newLocation;
                            isInitialLoad = false;
                        } else {
                            console.warn("Gagal mengambil jadwal berdasarkan koordinat, menggunakan default " + DEFAULT_CITY);
                            showWarningNotification(`Gagal mengambil jadwal berdasarkan koordinat. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                            fetchPrayerTimesByCity(DEFAULT_CITY, true);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        showWarningNotification(`Terjadi kesalahan saat mengambil jadwal. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                        fetchPrayerTimesByCity(DEFAULT_CITY, true);
                    });
            }

            function getPrayerTimes() {
                const loadingEl = document.getElementById("prayerScheduleLoading");
                const errorEl = document.getElementById("prayerScheduleError");
                const errorMsgEl = document.getElementById("prayerScheduleErrorMsg");
                const contentEl = document.getElementById("prayerScheduleContent");

                if (loadingEl) loadingEl.style.display = "block";
                if (errorEl) errorEl.style.display = "none";
                if (contentEl) contentEl.style.display = "none";

                if (!navigator.geolocation) {
                    console.warn("Geolocation tidak didukung, menggunakan default: " + DEFAULT_CITY);
                    if (errorEl) errorEl.className = "alert alert-info";
                    if (errorEl) errorEl.style.display = "block";
                    if (errorMsgEl) errorMsgEl.innerHTML = "<i class=\"fas fa-info-circle\"></i> Geolocation tidak didukung oleh browser. Menggunakan lokasi default: " + DEFAULT_CITY;
                    showWarningNotification(`Geolocation tidak didukung oleh browser. Beralih ke lokasi default: ${DEFAULT_CITY}`);
                    fetchPrayerTimesByCity(DEFAULT_CITY, true);
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        fetchPrayerTimesByCoordinate(lat, lng);
                    },
                    function(error) {
                        console.warn("Tidak dapat mengakses lokasi GPS, menggunakan default: " + DEFAULT_CITY);
                        let errorMessage = "";
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "Akses lokasi ditolak. Menggunakan lokasi default: " + DEFAULT_CITY;
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "Informasi lokasi tidak tersedia. Menggunakan lokasi default: " + DEFAULT_CITY;
                                break;
                            case error.TIMEOUT:
                                errorMessage = "Waktu permintaan lokasi habis. Menggunakan lokasi default: " + DEFAULT_CITY;
                                break;
                            default:
                                errorMessage = "Error tidak diketahui. Menggunakan lokasi default: " + DEFAULT_CITY;
                                break;
                        }
                        if (errorEl) errorEl.className = "alert alert-info";
                        if (errorEl) errorEl.style.display = "block";
                        if (errorMsgEl) errorMsgEl.innerHTML = "<i class=\"fas fa-info-circle\"></i> " + errorMessage;
                        showWarningNotification(errorMessage);
                        fetchPrayerTimesByCity(DEFAULT_CITY, true);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            function updateCurrentAndNextPrayer() {
                const now = getCurrentTime();
                const currentTotalMinutes = now.totalMinutes;

                document.querySelectorAll(".prayer-time").forEach(el => {
                    el.classList.remove("bg-success", "bg-warning", "text-white", "font-weight-bold");
                });

                currentPrayerIndex = -1;
                nextPrayerIndex = -1;

                let lastPassedIndex = -1;

                for (let i = 0; i < prayerOrder.length; i++) {
                    const prayer = prayerOrder[i];
                    const timeStr = prayerTimes[prayer];
                    if (!timeStr) continue;

                    const prayerTime = parseTime(timeStr);
                    if (!prayerTime) continue;

                    const prayerTotalMinutes = timeToMinutes(prayerTime);
                    const prayerEl = document.querySelector(`[data-prayer="${prayer}"]`);
                    const hasPassed = prayerTotalMinutes <= currentTotalMinutes;

                    if (hasPassed) {
                        lastPassedIndex = i;
                    } else {
                        if (nextPrayerIndex === -1) {
                            nextPrayerIndex = i;
                            if (prayerEl) {
                                prayerEl.classList.add("bg-warning", "font-weight-bold");
                            }
                        }
                    }
                }

                if (lastPassedIndex >= 0) {
                    const lastPrayer = prayerOrder[lastPassedIndex];
                    currentPrayerIndex = lastPassedIndex;
                    const prayerEl = document.querySelector(`[data-prayer="${lastPrayer}"]`);
                    if (prayerEl) {
                        prayerEl.classList.add("bg-success", "text-white", "font-weight-bold");
                    }
                }

                if (nextPrayerIndex === -1) {
                    nextPrayerIndex = 0;
                    const prayer = prayerOrder[0];
                    const prayerEl = document.querySelector(`[data-prayer="${prayer}"]`);
                    if (prayerEl) {
                        prayerEl.classList.add("bg-warning", "font-weight-bold");
                    }
                }

                updateNextPrayerInfo();
            }

            function updateNextPrayerInfo() {
                if (nextPrayerIndex < 0) {
                    nextPrayerIndex = 0;
                }

                const nextPrayer = prayerOrder[nextPrayerIndex];
                const nextPrayerName = prayerNames[nextPrayer] || nextPrayer;
                const nextPrayerTime = prayerTimes[nextPrayer];

                const nextPrayerNameEl = document.getElementById("nextPrayerName");
                if (nextPrayerNameEl) {
                    nextPrayerNameEl.textContent = "Waktu Berikutnya: " + nextPrayerName || "-";
                }

                const now = getCurrentTime();
                const lastPrayer = prayerOrder[prayerOrder.length - 1];
                const lastPrayerTime = parseTime(prayerTimes[lastPrayer]);
                nextIsTomorrow = nextPrayerIndex === 0 && lastPrayerTime &&
                    now.totalMinutes > timeToMinutes(lastPrayerTime);
                nextPrayerTimeStr = nextPrayerTime;

                updateCountdown(nextPrayerTime, nextIsTomorrow);
            }

            function updateCountdown(nextPrayerTimeStr, isTomorrow = false) {
                const countdownEl = document.getElementById("countdown");
                if (!nextPrayerTimeStr || !countdownEl) {
                    if (countdownEl) countdownEl.textContent = "-";
                    return;
                }

                const nextPrayerTime = parseTime(nextPrayerTimeStr);
                if (!nextPrayerTime) {
                    countdownEl.textContent = "-";
                    return;
                }

                const now = new Date();
                const target = new Date();
                target.setHours(nextPrayerTime.hours, nextPrayerTime.minutes, 0, 0);
                if (isTomorrow || target.getTime() <= now.getTime()) {
                    target.setDate(target.getDate() + 1);
                }

                let diffSeconds = Math.floor((target.getTime() - now.getTime()) / 1000);
                if (diffSeconds < 0) diffSeconds = 0;

                const hours = Math.floor(diffSeconds / 3600);
                const minutes = Math.floor((diffSeconds % 3600) / 60);
                const seconds = diffSeconds % 60;

                const hh = String(hours).padStart(2, "0");
                const mm = String(minutes).padStart(2, "0");
                const ss = String(seconds).padStart(2, "0");
                countdownEl.textContent = `${hh}:${mm}:${ss}`;
            }

            function startCountdown() {
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
                if (clockInterval) {
                    clearInterval(clockInterval);
                }

                updateCurrentAndNextPrayer();
                updateCurrentDateTime();

                countdownInterval = setInterval(function() {
                    updateCurrentAndNextPrayer();
                }, 1000);

                clockInterval = setInterval(function() {
                    updateCurrentDateTime();
                }, 1000);
            }

            function updateCurrentDateTime() {
                const now = new Date();
                const optionsDate = {
                    weekday: "long",
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                };
                const optionsTime = {
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit"
                };
                const dateStr = now.toLocaleDateString("id-ID", optionsDate);
                const timeStr = now.toLocaleTimeString("id-ID", optionsTime);
                const el = document.getElementById("currentDateTime");
                if (el) {
                    el.textContent = `${dateStr} | ${timeStr}`;
                }
            }

            function refreshPrayerTimesByMode() {
                const saved = loadLocationSetting();
                const mode = saved.mode || "gps";
                const manualCity = saved.city || DEFAULT_CITY;

                setLoadingState();

                if (mode === "gps") {
                    getPrayerTimes();
                    return;
                }

                if (mode === "default") {
                    fetchPrayerTimesByCity(DEFAULT_CITY, true);
                    return;
                }

                const targetCity = manualCity || DEFAULT_CITY;
                fetchPrayerTimesByCity(targetCity, false);
            }

            window.refreshPrayerTimesByModeFromModal = function(mode, manualCity) {
                setLoadingState();

                if (mode === "gps") {
                    saveLocationSetting({ mode: "gps", city: DEFAULT_CITY });
                    getPrayerTimes();
                    return;
                }

                if (mode === "default") {
                    saveLocationSetting({ mode: "default", city: DEFAULT_CITY });
                    fetchPrayerTimesByCity(DEFAULT_CITY, true);
                    return;
                }

                const targetCity = manualCity || DEFAULT_CITY;
                saveLocationSetting({ mode: "manual", city: targetCity });
                fetchPrayerTimesByCity(targetCity, false);
            };

            function initLocationControls() {
                const saved = loadLocationSetting();
                lastLocationSetting = saved;
                refreshPrayerTimesByMode();
            }

            initLocationControls();
        });
        </script>';
    }
}

