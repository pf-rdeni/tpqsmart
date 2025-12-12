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
                color: #212529;
                transition: all 0.2s ease;
                height: 100%;
            }

            /* Dark mode support */
            .dark-mode .prayer-card {
                background: #2d3748;
                border-color: #4a5568;
                color: #e2e8f0;
            }

            .dark-mode .prayer-card .prayer-title {
                color: #cbd5e0;
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

            .dark-mode .prayer-card:hover {
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
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

            .dark-mode .city-suggestions {
                background: #2d3748;
                border-color: #4a5568;
            }

            .city-suggestion-item {
                padding: 8px 12px;
                cursor: pointer;
                border-bottom: 1px solid #f0f0f0;
                transition: background-color 0.2s;
                color: #212529;
            }

            .dark-mode .city-suggestion-item {
                border-bottom-color: #4a5568;
                color: #e2e8f0;
            }

            .city-suggestion-item:hover {
                background-color: #f8f9fa;
            }

            .dark-mode .city-suggestion-item:hover {
                background-color: #4a5568;
            }

            .city-suggestion-item:last-child {
                border-bottom: none;
            }

            .city-suggestion-item.highlight {
                background-color: #e3f2fd;
            }

            .dark-mode .city-suggestion-item.highlight {
                background-color: #2c5282;
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
                        mode: "default",
                        city: DEFAULT_CITY
                    };
                    const parsed = JSON.parse(saved);
                    if (!parsed.mode) return {
                        mode: "default",
                        city: DEFAULT_CITY
                    };
                    return {
                        mode: parsed.mode || "default",
                        city: parsed.city || DEFAULT_CITY
                    };
                } catch (e) {
                    return {
                        mode: "default",
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
                    if (!saved) return { mode: "default", city: DEFAULT_CITY };
                    const parsed = JSON.parse(saved);
                    if (!parsed.mode) return { mode: "default", city: DEFAULT_CITY };
                    return { mode: parsed.mode || "default", city: parsed.city || DEFAULT_CITY };
                } catch (e) {
                    return { mode: "default", city: DEFAULT_CITY };
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
                            
                            // Update prayer notification times
                            if (typeof window.updatePrayerNotificationTimes === "function") {
                                window.updatePrayerNotificationTimes(prayerTimes);
                            }

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
                            
                            // Update prayer notification times
                            if (typeof window.updatePrayerNotificationTimes === "function") {
                                window.updatePrayerNotificationTimes(prayerTimes);
                            }
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
                const mode = saved.mode || "default";
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

if (!function_exists('prayer_notification_floating')) {
    /**
     * Render floating notification untuk waktu sholat (HTML + CSS + JavaScript)
     * Notifikasi muncul otomatis di semua halaman
     * 
     * @param string $baseUrl Base URL untuk API endpoint
     * @return string HTML, CSS, dan JavaScript untuk floating notification
     */
    function prayer_notification_floating($baseUrl = '')
    {
        if (empty($baseUrl)) {
            $baseUrl = base_url('backend/jadwal-sholat');
        }
        
        return '
        <style>
        .prayer-notification-floating {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10000;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            min-width: 320px;
            max-width: 400px;
            cursor: move;
            display: none;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .prayer-notification-floating.show {
            display: block;
            animation: slideInDown 0.5s ease-out;
        }
        
        .prayer-notification-floating.hide {
            animation: slideOutUp 0.3s ease-in forwards;
        }
        
        @keyframes slideInDown {
            from {
                transform: translate(-50%, -60%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }
        
        @keyframes slideOutUp {
            from {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
            to {
                transform: translate(-50%, -60%);
                opacity: 0;
            }
        }
        
        .prayer-notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .prayer-notification-title {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .prayer-notification-title i {
            font-size: 1.3rem;
        }
        
        .prayer-notification-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            font-size: 1.1rem;
            padding: 0;
        }
        
        .prayer-notification-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .prayer-notification-body {
            text-align: center;
        }
        
        .prayer-notification-message {
            font-size: 0.95rem;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .prayer-notification-countdown {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
            font-family: "Courier New", monospace;
        }
        
        .prayer-notification-prayer-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 8px;
        }
        
        .prayer-notification-time {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        </style>
        
        <div id="prayerNotificationFloating" class="prayer-notification-floating">
            <div class="prayer-notification-header">
                <div class="prayer-notification-title">
                    <i class="fas fa-mosque"></i>
                    <span>Waktu Sholat</span>
                </div>
                <button type="button" class="prayer-notification-close" id="prayerNotificationClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="prayer-notification-body">
                <div class="prayer-notification-message" id="prayerNotificationMessage"></div>
                <div class="prayer-notification-countdown" id="prayerNotificationCountdown" style="display: none;"></div>
                <div class="prayer-notification-prayer-name" id="prayerNotificationPrayerName"></div>
                <div class="prayer-notification-time" id="prayerNotificationTime"></div>
            </div>
        </div>
        
        <script>
        (function() {
            // Global state untuk prayer notification
            window.prayerNotificationState = {
                prayerTimes: {},
                nextPrayerTime: null,
                nextPrayerName: null,
                notificationShown: {
                    10: false,
                    5: false,
                    2: false,
                    1: false
                },
                checkInterval: null,
                countdownInterval: null,
                isDragging: false,
                dragOffset: { x: 0, y: 0 },
                currentPosition: { x: 50, y: 50 } // percentage
            };
            
            const state = window.prayerNotificationState;
            const DEFAULT_CITY = "Bintan";
            const LOCATION_SETTING_KEY = "prayerLocationSetting";
            const prayerOrder = ["fajr", "shurooq", "dhuhr", "asr", "maghrib", "isha"];
            const prayerNames = {
                "fajr": "Subuh",
                "shurooq": "Syuruq",
                "dhuhr": "Dzuhur",
                "asr": "Ashar",
                "maghrib": "Maghrib",
                "isha": "Isya"
            };
            
            // Load saved position
            function loadSavedPosition() {
                try {
                    const saved = localStorage.getItem("prayerNotificationPosition");
                    if (saved) {
                        const pos = JSON.parse(saved);
                        state.currentPosition = pos;
                        updateNotificationPosition();
                    }
                } catch (e) {
                    console.warn("Error loading saved position:", e);
                }
            }
            
            // Save position
            function savePosition() {
                try {
                    localStorage.setItem("prayerNotificationPosition", JSON.stringify(state.currentPosition));
                } catch (e) {
                    console.warn("Error saving position:", e);
                }
            }
            
            // Update notification position
            function updateNotificationPosition() {
                const notification = document.getElementById("prayerNotificationFloating");
                if (notification) {
                    notification.style.left = state.currentPosition.x + "%";
                    notification.style.top = state.currentPosition.y + "%";
                    notification.style.transform = "translate(-50%, -50%)";
                }
            }
            
            // Parse time string to minutes
            function parseTimeToMinutes(timeString) {
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
                
                return hours * 60 + minutes;
            }
            
            // Get current time in minutes
            function getCurrentTimeMinutes() {
                const now = new Date();
                return now.getHours() * 60 + now.getMinutes();
            }
            
            // Get next prayer time
            function getNextPrayerTime() {
                const currentMinutes = getCurrentTimeMinutes();
                let nextPrayer = null;
                let nextPrayerName = null;
                let nextPrayerMinutes = null;
                
                for (let i = 0; i < prayerOrder.length; i++) {
                    const prayer = prayerOrder[i];
                    const timeStr = state.prayerTimes[prayer];
                    if (!timeStr) continue;
                    
                    const prayerMinutes = parseTimeToMinutes(timeStr);
                    if (prayerMinutes === null) continue;
                    
                    if (prayerMinutes > currentMinutes) {
                        nextPrayer = prayer;
                        nextPrayerName = prayerNames[prayer];
                        nextPrayerMinutes = prayerMinutes;
                        break;
                    }
                }
                
                // If no next prayer today, use tomorrow\'s fajr
                if (!nextPrayer) {
                    const fajrTime = state.prayerTimes["fajr"];
                    if (fajrTime) {
                        const fajrMinutes = parseTimeToMinutes(fajrTime);
                        if (fajrMinutes !== null) {
                            nextPrayer = "fajr";
                            nextPrayerName = "Subuh";
                            nextPrayerMinutes = fajrMinutes + (24 * 60); // Add 24 hours
                        }
                    }
                }
                
                return {
                    prayer: nextPrayer,
                    name: nextPrayerName,
                    minutes: nextPrayerMinutes,
                    timeStr: nextPrayer ? state.prayerTimes[nextPrayer] : null
                };
            }
            
            // Format time for display
            function formatTime24(timeString) {
                if (!timeString) return "-";
                const trimmed = timeString.trim().toLowerCase();
                const hasAm = trimmed.includes("am");
                const hasPm = trimmed.includes("pm");
                const timeOnly = trimmed.replace(/\s*(am|pm)\s*/gi, "");
                const parts = timeOnly.split(":");
                if (parts.length !== 2) return timeString;
                
                let hours = parseInt(parts[0]);
                const minutes = parseInt(parts[1]);
                if (isNaN(hours) || isNaN(minutes)) return timeString;
                
                if (hasPm && hours !== 12) {
                    hours += 12;
                } else if (hasAm && hours === 12) {
                    hours = 0;
                }
                
                return String(hours).padStart(2, "0") + ":" + String(minutes).padStart(2, "0");
            }
            
            // Show notification
            function showNotification(minutesLeft, nextPrayer) {
                const notification = document.getElementById("prayerNotificationFloating");
                const messageEl = document.getElementById("prayerNotificationMessage");
                const countdownEl = document.getElementById("prayerNotificationCountdown");
                const prayerNameEl = document.getElementById("prayerNotificationPrayerName");
                const timeEl = document.getElementById("prayerNotificationTime");
                
                if (!notification || !nextPrayer) return;
                
                // Update content
                if (minutesLeft === 1) {
                    messageEl.textContent = "Waktu sholat akan masuk dalam:";
                    countdownEl.style.display = "block";
                    startCountdown(nextPrayer.minutes);
                } else {
                    messageEl.textContent = `Waktu sholat akan masuk dalam ${minutesLeft} menit`;
                    countdownEl.style.display = "none";
                    if (state.countdownInterval) {
                        clearInterval(state.countdownInterval);
                        state.countdownInterval = null;
                    }
                }
                
                prayerNameEl.textContent = nextPrayer.name;
                timeEl.textContent = formatTime24(nextPrayer.timeStr);
                
                // Show notification
                notification.classList.remove("hide");
                notification.classList.add("show");
            }
            
            // Hide notification
            function hideNotification() {
                const notification = document.getElementById("prayerNotificationFloating");
                if (notification) {
                    notification.classList.add("hide");
                    setTimeout(() => {
                        notification.classList.remove("show", "hide");
                        if (state.countdownInterval) {
                            clearInterval(state.countdownInterval);
                            state.countdownInterval = null;
                        }
                    }, 300);
                }
            }
            
            // Start countdown from 60 seconds
            function startCountdown(targetMinutes) {
                if (state.countdownInterval) {
                    clearInterval(state.countdownInterval);
                }
                
                const countdownEl = document.getElementById("prayerNotificationCountdown");
                if (!countdownEl) return;
                
                function updateCountdown() {
                    const now = new Date();
                    const currentMinutes = now.getHours() * 60 + now.getMinutes();
                    const currentSeconds = now.getSeconds();
                    const currentTotalSeconds = currentMinutes * 60 + currentSeconds;
                    
                    // Calculate target time
                    const targetHours = Math.floor(targetMinutes / 60);
                    const targetMins = targetMinutes % 60;
                    const targetDate = new Date();
                    targetDate.setHours(targetHours, targetMins, 0, 0);
                    
                    // If target is tomorrow
                    if (targetMinutes < currentMinutes || (targetMinutes === currentMinutes && currentSeconds > 0)) {
                        targetDate.setDate(targetDate.getDate() + 1);
                    }
                    
                    const targetTotalSeconds = Math.floor(targetDate.getTime() / 1000);
                    const diffSeconds = targetTotalSeconds - Math.floor(now.getTime() / 1000);
                    
                    if (diffSeconds <= 0) {
                        countdownEl.textContent = "00:00";
                        // Reset notification flags and hide after prayer time passes
                        state.notificationShown = { 10: false, 5: false, 2: false, 1: false };
                        setTimeout(() => {
                            hideNotification();
                        }, 5000); // Hide after 5 seconds
                        return;
                    }
                    
                    const mins = Math.floor(diffSeconds / 60);
                    const secs = diffSeconds % 60;
                    countdownEl.textContent = String(mins).padStart(2, "0") + ":" + String(secs).padStart(2, "0");
                }
                
                updateCountdown();
                state.countdownInterval = setInterval(updateCountdown, 1000);
            }
            
            // Check prayer time and show notification
            function checkPrayerTime() {
                if (!state.prayerTimes || Object.keys(state.prayerTimes).length === 0) {
                    return;
                }
                
                const nextPrayer = getNextPrayerTime();
                if (!nextPrayer || !nextPrayer.minutes) {
                    return;
                }
                
                const currentMinutes = getCurrentTimeMinutes();
                const currentSeconds = new Date().getSeconds();
                const minutesLeft = nextPrayer.minutes - currentMinutes;
                
                // Check for 10, 5, 2, and 1 minute notifications
                // Use exact minute check (e.g., exactly 10 minutes left, not 10.5)
                if (minutesLeft === 10 && currentSeconds < 30 && !state.notificationShown[10]) {
                    state.notificationShown[10] = true;
                    showNotification(10, nextPrayer);
                } else if (minutesLeft === 5 && currentSeconds < 30 && !state.notificationShown[5]) {
                    state.notificationShown[5] = true;
                    showNotification(5, nextPrayer);
                } else if (minutesLeft === 2 && currentSeconds < 30 && !state.notificationShown[2]) {
                    state.notificationShown[2] = true;
                    showNotification(2, nextPrayer);
                } else if (minutesLeft === 1 && currentSeconds < 30 && !state.notificationShown[1]) {
                    state.notificationShown[1] = true;
                    showNotification(1, nextPrayer);
                }
                
                // Reset flags when prayer time passes
                if (minutesLeft < 0) {
                    state.notificationShown = { 10: false, 5: false, 2: false, 1: false };
                    if (state.countdownInterval) {
                        clearInterval(state.countdownInterval);
                        state.countdownInterval = null;
                    }
                }
            }
            
            // Fetch prayer times
            function fetchPrayerTimesForNotification() {
                function loadLocationSetting() {
                    try {
                        const saved = localStorage.getItem(LOCATION_SETTING_KEY);
                        if (!saved) return { mode: "default", city: DEFAULT_CITY };
                        const parsed = JSON.parse(saved);
                        if (!parsed.mode) return { mode: "default", city: DEFAULT_CITY };
                        return { mode: parsed.mode || "default", city: parsed.city || DEFAULT_CITY };
                    } catch (e) {
                        return { mode: "default", city: DEFAULT_CITY };
                    }
                }
                
                const setting = loadLocationSetting();
                let url = "";
                
                if (setting.mode === "gps") {
                    if (!navigator.geolocation) {
                        url = `' . $baseUrl . '/${encodeURIComponent(DEFAULT_CITY)}?format=json`;
                    } else {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                const lat = position.coords.latitude;
                                const lng = position.coords.longitude;
                                url = `' . $baseUrl . '/${lat}/${lng}?format=json`;
                                fetch(url)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success && data.prayer_times) {
                                            state.prayerTimes = data.prayer_times;
                                            state.nextPrayerTime = getNextPrayerTime();
                                            checkPrayerTime();
                                        }
                                    })
                                    .catch(() => {
                                        url = `' . $baseUrl . '/${encodeURIComponent(DEFAULT_CITY)}?format=json`;
                                        fetch(url)
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success && data.prayer_times) {
                                                    state.prayerTimes = data.prayer_times;
                                                    state.nextPrayerTime = getNextPrayerTime();
                                                    checkPrayerTime();
                                                }
                                            });
                                    });
                            },
                            function() {
                                url = `' . $baseUrl . '/${encodeURIComponent(DEFAULT_CITY)}?format=json`;
                                fetch(url)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success && data.prayer_times) {
                                            state.prayerTimes = data.prayer_times;
                                            state.nextPrayerTime = getNextPrayerTime();
                                            checkPrayerTime();
                                        }
                                    });
                            }
                        );
                        return;
                    }
                } else {
                    url = `' . $baseUrl . '/${encodeURIComponent(setting.city || DEFAULT_CITY)}?format=json`;
                }
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.prayer_times) {
                            state.prayerTimes = data.prayer_times;
                            state.nextPrayerTime = getNextPrayerTime();
                            checkPrayerTime();
                        }
                    })
                    .catch(() => {
                        // Fallback to default
                        fetch(`' . $baseUrl . '/${encodeURIComponent(DEFAULT_CITY)}?format=json`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.prayer_times) {
                                    state.prayerTimes = data.prayer_times;
                                    state.nextPrayerTime = getNextPrayerTime();
                                    checkPrayerTime();
                                }
                            });
                    });
            }
            
            // Initialize drag functionality
            function initDrag() {
                const notification = document.getElementById("prayerNotificationFloating");
                if (!notification) return;
                
                const header = notification.querySelector(".prayer-notification-header");
                if (!header) return;
                
                header.addEventListener("mousedown", function(e) {
                    if (e.target.closest(".prayer-notification-close")) return;
                    
                    state.isDragging = true;
                    const rect = notification.getBoundingClientRect();
                    state.dragOffset.x = e.clientX - rect.left - rect.width / 2;
                    state.dragOffset.y = e.clientY - rect.top - rect.height / 2;
                    notification.style.cursor = "grabbing";
                });
                
                document.addEventListener("mousemove", function(e) {
                    if (!state.isDragging) return;
                    
                    const x = (e.clientX / window.innerWidth) * 100;
                    const y = (e.clientY / window.innerHeight) * 100;
                    
                    // Constrain to viewport
                    state.currentPosition.x = Math.max(10, Math.min(90, x));
                    state.currentPosition.y = Math.max(10, Math.min(90, y));
                    
                    updateNotificationPosition();
                });
                
                document.addEventListener("mouseup", function() {
                    if (state.isDragging) {
                        state.isDragging = false;
                        const notification = document.getElementById("prayerNotificationFloating");
                        if (notification) {
                            notification.style.cursor = "move";
                            savePosition();
                        }
                    }
                });
            }
            
            // Close button handler
            function initCloseButton() {
                const closeBtn = document.getElementById("prayerNotificationClose");
                if (closeBtn) {
                    closeBtn.addEventListener("click", function() {
                        hideNotification();
                    });
                }
            }
            
            // Initialize
            function init() {
                loadSavedPosition();
                initDrag();
                initCloseButton();
                
                // Fetch prayer times initially
                fetchPrayerTimesForNotification();
                
                // Check every 10 seconds for more accurate timing
                state.checkInterval = setInterval(function() {
                    checkPrayerTime();
                }, 10000);
                
                // Re-fetch prayer times every hour
                setInterval(function() {
                    fetchPrayerTimesForNotification();
                }, 3600000);
            }
            
            // Start when DOM is ready
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", init);
            } else {
                init();
            }
            
            // Expose function to update prayer times from widget
            window.updatePrayerNotificationTimes = function(prayerTimes) {
                if (prayerTimes && Object.keys(prayerTimes).length > 0) {
                    state.prayerTimes = prayerTimes;
                    state.nextPrayerTime = getNextPrayerTime();
                    checkPrayerTime();
                }
            };
        })();
        </script>';
    }
}

