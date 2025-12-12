<?php

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

