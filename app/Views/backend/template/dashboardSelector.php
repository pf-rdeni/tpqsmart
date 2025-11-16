<!-- Modal Pilih Dashboard -->
<div class="modal fade" id="modalPilihDashboard" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fas fa-tachometer-alt"></i> Pilih Dashboard</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="btnCloseModal" style="display: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-center mb-4">Silakan pilih dashboard yang ingin Anda akses:</p>
                <div id="dashboardOptionsContainer">
                    <div class="dashboard-option-wrapper">
                        <button type="button" class="btn btn-outline-primary btn-lg btn-block h-100 dashboard-option" data-dashboard="semester">
                            <i class="fas fa-book fa-3x mb-2"></i><br>
                            <strong>Ujian Semester</strong>
                        </button>
                    </div>
                    <div class="dashboard-option-wrapper">
                        <button type="button" class="btn btn-outline-success btn-lg btn-block h-100 dashboard-option" data-dashboard="munaqosah">
                            <i class="fas fa-graduation-cap fa-3x mb-2"></i><br>
                            <strong>Munaqosah</strong>
                        </button>
                    </div>
                    <?php if (in_groups('Admin')): ?>
                        <div class="dashboard-option-wrapper">
                            <button type="button" class="btn btn-outline-warning btn-lg btn-block h-100 dashboard-option" data-dashboard="sertifikasi">
                                <i class="fas fa-certificate fa-3x mb-2"></i><br>
                                <strong>Sertifikasi</strong>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #dashboardOptionsContainer {
        display: grid;
        gap: 1.5rem;
        justify-items: stretch;
        align-items: stretch;
    }

    .dashboard-option-wrapper {
        min-width: 0;
    }

    .dashboard-option {
        padding: 2.5rem 1.5rem;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border-width: 2px;
        cursor: pointer;
    }

    .dashboard-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .dashboard-option i {
        transition: transform 0.3s ease;
        margin-bottom: 0.5rem;
    }

    .dashboard-option:hover i {
        transform: scale(1.15);
    }

    .dashboard-option strong {
        font-size: 1.1rem;
        line-height: 1.4;
    }

    /* Untuk 2 button: tampil 2 kolom dengan ukuran lebih besar */
    #dashboardOptionsContainer.dashboard-count-2 {
        grid-template-columns: repeat(2, 1fr);
        max-width: 700px;
        margin: 0 auto;
    }

    #dashboardOptionsContainer.dashboard-count-2 .dashboard-option {
        min-height: 220px;
        padding: 3rem 2rem;
    }

    #dashboardOptionsContainer.dashboard-count-2 .dashboard-option i {
        font-size: 4rem !important;
    }

    /* Untuk 3 button: tampil 3 kolom */
    #dashboardOptionsContainer.dashboard-count-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    #dashboardOptionsContainer.dashboard-count-3 .dashboard-option {
        min-height: 200px;
    }

    /* Untuk 4 button: tampil 4 kolom */
    #dashboardOptionsContainer.dashboard-count-4 {
        grid-template-columns: repeat(4, 1fr);
    }

    #dashboardOptionsContainer.dashboard-count-4 .dashboard-option {
        min-height: 180px;
        padding: 2rem 1rem;
    }

    /* Untuk 5 button atau lebih: tampil dengan auto-fit */
    #dashboardOptionsContainer.dashboard-count-5,
    #dashboardOptionsContainer.dashboard-count-6 {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    /* Responsive adjustments untuk mobile */
    @media (max-width: 768px) {
        #dashboardOptionsContainer {
            grid-template-columns: 1fr !important;
            gap: 1rem;
        }

        #dashboardOptionsContainer .dashboard-option {
            min-height: 160px;
            padding: 2rem 1rem;
        }

        #dashboardOptionsContainer .dashboard-option i {
            font-size: 2.5rem !important;
        }
    }

    /* Responsive untuk tablet */
    @media (min-width: 769px) and (max-width: 991px) {

        #dashboardOptionsContainer.dashboard-count-3,
        #dashboardOptionsContainer.dashboard-count-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        #dashboardOptionsContainer.dashboard-count-2 {
            grid-template-columns: repeat(2, 1fr);
            max-width: 100%;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Hitung jumlah dashboard option yang tersedia
        const dashboardCount = $('.dashboard-option-wrapper').length;

        // Tambahkan class berdasarkan jumlah dashboard
        if (dashboardCount > 0) {
            $('#dashboardOptionsContainer').addClass('dashboard-count-' + dashboardCount);
        }

        // Cek apakah user adalah Admin atau Operator
        const isAdmin = <?= in_groups('Admin') ? 'true' : 'false' ?>;
        const isOperator = <?= in_groups('Operator') ? 'true' : 'false' ?>;

        // Skip untuk Juri, Panitia, JuriSertifikasi, PanitiaSertifikasi (sudah dihandle di controller)
        const isJuri = <?= in_groups('Juri') ? 'true' : 'false' ?>;
        const isPanitia = <?= in_groups('Panitia') ? 'true' : 'false' ?>;
        const isJuriSertifikasi = <?= in_groups('JuriSertifikasi') ? 'true' : 'false' ?>;
        const isPanitiaSertifikasi = <?= in_groups('PanitiaSertifikasi') ? 'true' : 'false' ?>;

        // Jika bukan Admin atau Operator, atau sudah dihandle khusus, skip modal
        if (!isAdmin && !isOperator) {
            return;
        }

        if (isJuri || isPanitia || isJuriSertifikasi || isPanitiaSertifikasi) {
            return;
        }

        // Fungsi untuk update label dashboard di navbar
        function updateDashboardLabel() {
            const selectedDashboard = localStorage.getItem('selectedDashboard');
            const labelMap = {
                'semester': 'Ujian Semester',
                'munaqosah': 'Munaqosah',
                'sertifikasi': 'Sertifikasi'
            };
            const currentLabel = labelMap[selectedDashboard] || 'Dashboard';
            $('#currentDashboardLabel').text(currentLabel);
        }

        // Fungsi untuk menampilkan modal (dengan opsi untuk bisa ditutup jika dipanggil manual)
        function showDashboardModal(allowClose = false) {
            if (allowClose) {
                $('#modalPilihDashboard').attr('data-backdrop', 'true');
                $('#modalPilihDashboard').attr('data-keyboard', 'true');
                $('#btnCloseModal').show();
            } else {
                $('#modalPilihDashboard').attr('data-backdrop', 'static');
                $('#modalPilihDashboard').attr('data-keyboard', 'false');
                $('#btnCloseModal').hide();
            }
            $('#modalPilihDashboard').modal('show');
        }

        // Cek localStorage untuk pilihan dashboard
        const selectedDashboard = localStorage.getItem('selectedDashboard');
        const currentPath = window.location.pathname;
        const currentUrl = window.location.href.toLowerCase();

        // Update label dashboard di navbar
        updateDashboardLabel();

        // Jika belum ada pilihan, tampilkan modal (tidak bisa ditutup)
        // Tapi cek juga query parameter untuk menghindari flash content
        const urlParams = new URLSearchParams(window.location.search);
        const dashboardParam = urlParams.get('dashboard');

        // Jika ada query parameter dashboard, set localStorage dan redirect jika perlu
        if (dashboardParam && !selectedDashboard) {
            localStorage.setItem('selectedDashboard', dashboardParam);
            updateDashboardLabel();
            if (dashboardParam === 'munaqosah') {
                window.location.href = '<?= base_url("backend/munaqosah/dashboard-munaqosah") ?>';
                return;
            }
            if (dashboardParam === 'sertifikasi' && isAdmin) {
                window.location.href = '<?= base_url("backend/sertifikasi/dashboard-admin") ?>';
                return;
            }
            // Jika dashboard=semester, biarkan halaman tetap tampil
        }

        if (!selectedDashboard && !dashboardParam) {
            showDashboardModal(false);
        } else {
            // Jika sudah ada pilihan, cek apakah perlu redirect
            // Hanya redirect jika user berada di dashboard default (ujian semester)
            // dan pilihan bukan semester
            const isDashboardDefault = currentPath === '/' ||
                currentPath.includes('/dashboard/index') ||
                (currentUrl.includes('dashboard') &&
                    !currentUrl.includes('munaqosah') &&
                    !currentUrl.includes('sertifikasi'));

            // Jika user berada di dashboard default dan pilihan bukan semester,
            // redirect melalui query parameter untuk server-side redirect (lebih cepat)
            if (selectedDashboard !== 'semester' && isDashboardDefault) {
                // Redirect ke / dengan query parameter dashboard untuk server-side redirect
                // Ini mencegah flash content sebelum redirect
                window.location.href = '<?= base_url("/") ?>?dashboard=' + selectedDashboard;
                return;
            }
            // Jika pilihan adalah semester dan user sudah di dashboard semester, tidak perlu redirect
        }

        // Handle klik tombol "Pilih Dashboard" di navbar
        $('#btnPilihDashboard').click(function(e) {
            e.preventDefault();
            showDashboardModal(true); // Bisa ditutup karena dipanggil manual
        });

        // Handle klik tombol dashboard
        $('.dashboard-option').click(function() {
            const dashboard = $(this).data('dashboard');
            localStorage.setItem('selectedDashboard', dashboard);
            updateDashboardLabel();
            $('#modalPilihDashboard').modal('hide');

            // Cek apakah perlu redirect
            const currentPath = window.location.pathname;
            const currentUrl = window.location.href.toLowerCase();

            // Jika user sudah di dashboard yang dipilih, tidak perlu redirect
            if ((dashboard === 'semester' && (currentPath === '/' || currentPath.includes('/dashboard/index'))) ||
                (dashboard === 'munaqosah' && currentUrl.includes('munaqosah')) ||
                (dashboard === 'sertifikasi' && currentUrl.includes('sertifikasi'))) {
                // Sudah di dashboard yang dipilih, tidak perlu redirect
                return;
            }

            redirectToDashboard(dashboard);
        });

        function redirectToDashboard(dashboard) {
            let url = '';
            switch (dashboard) {
                case 'munaqosah':
                    // Redirect melalui query parameter untuk server-side redirect yang lebih cepat
                    url = '<?= base_url("/") ?>?dashboard=munaqosah';
                    break;
                case 'sertifikasi':
                    // Redirect melalui query parameter untuk server-side redirect yang lebih cepat
                    url = '<?= base_url("/") ?>?dashboard=sertifikasi';
                    break;
                case 'semester':
                default:
                    // Redirect ke dashboard semester sesuai role
                    // Index akan otomatis redirect ke dashboard yang sesuai
                    url = '<?= base_url("/") ?>?dashboard=semester';
                    break;
            }

            if (url) {
                window.location.href = url;
            }
        }

        // Handle klik menu Dashboard/Home di sidebar dan brand logo
        // Redirect ke dashboard sesuai pilihan di localStorage
        $(document).on('click', 'a[href="<?= base_url("/") ?>"]', function(e) {
            // Hanya handle untuk Admin atau Operator
            if (!isAdmin && !isOperator) {
                return; // Biarkan default behavior untuk user lain
            }

            // Skip untuk Juri, Panitia, JuriSertifikasi, PanitiaSertifikasi
            if (isJuri || isPanitia || isJuriSertifikasi || isPanitiaSertifikasi) {
                return; // Biarkan default behavior
            }

            // Cek localStorage untuk pilihan dashboard
            const selectedDashboard = localStorage.getItem('selectedDashboard');

            // Jika ada pilihan dashboard selain semester, intercept dan redirect
            if (selectedDashboard && selectedDashboard !== 'semester') {
                e.preventDefault(); // Prevent default navigation
                redirectToDashboard(selectedDashboard);
                return false;
            }

            // Jika pilihan adalah semester atau tidak ada, biarkan default behavior
        });

        // Clear localStorage saat logout
        // Handle logout event
        $(document).on('click', 'a[href*="logout"]', function() {
            localStorage.removeItem('selectedDashboard');
        });

        // Reset modal settings saat modal ditutup
        $('#modalPilihDashboard').on('hidden.bs.modal', function() {
            // Reset ke default (tidak bisa ditutup) untuk next time
            $('#modalPilihDashboard').attr('data-backdrop', 'static');
            $('#modalPilihDashboard').attr('data-keyboard', 'false');
            $('#btnCloseModal').hide();
        });
    });
</script>