<script>
    $(function() {
        // Date picker initialization
        $('#DateForEdit, #DateForInput').datetimepicker({
            format: 'L'
        });

    });
    // ini untuk script umum yang sering dipakai di semua halaman
    // contoh: initializeDataTableUmum
    function initializeDataTableUmum(selector, paging = true, lengthChange = false, buttons = [], options = {}) {
        $(selector).DataTable({
            "lengthChange": lengthChange,
            "responsive": true,
            "autoWidth": false,
            "paging": paging,
            "buttons": buttons,
            "pageLength": 20,
            "lengthMenu": [ // Kustomisasi opsi jumlah entri yang tersedia
                [10, 20, 30, 50, 100, -1],
                [10, 20, 30, 50, 100, "Semua"]
            ],
            "language": {
                "search": "Pencarian:",
                "paginate": {
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "lengthMenu": "Tampilkan _MENU_ entri",
            },
            ...options
        }).buttons().container().appendTo(`${selector}_wrapper .col-md-6:eq(0)`);
    }
    // Function to initialize DataTable with filter header
    function initializeDataTableWithFilter(selector, paging = true, buttons = [], options = {}) {
        $(selector).DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "paging": paging,
            "buttons": buttons,
            // Menambahkan filter header
            "initComplete": function() {
                this.api().columns().every(function() {
                    var column = this;
                    var select = $('<select class="form-control"><option value="">Pilih Filter</option></select>')
                        .appendTo($(column.header()))
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            },
            ...options
        }).buttons().container().appendTo(`${selector}_wrapper .col-md-6:eq(0)`);
    }

    // Tambahkan event listener untuk tab changes
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        // Dapatkan target tab yang aktif
        let targetTab = $(e.target).attr("href");

        // Cari table di dalam tab yang aktif
        let table = $(targetTab).find('table').DataTable();

        // Adjust columns untuk memastikan responsive bekerja
        table.columns.adjust().responsive.recalc();
    });

    //-=============================================================================================
    // Fungsi untuk mengupdate terbilang
    function capitalizeEachWord(string) {
        return string.split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    function terbilang(angka) {
        const bilangan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas'
        ];
        let temp;
        let hasil = '';

        if (angka < 12) {
            hasil = ' ' + bilangan[angka];
        } else if (angka < 20) {
            hasil = terbilang(angka - 10) + ' belas ';
        } else if (angka < 100) {
            temp = Math.floor(angka / 10);
            hasil = terbilang(temp) + ' puluh ' + terbilang(angka % 10);
        } else if (angka < 200) {
            hasil = ' seratus ' + terbilang(angka - 100);
        } else if (angka < 1000) {
            temp = Math.floor(angka / 100);
            hasil = terbilang(temp) + ' ratus ' + terbilang(angka % 100);
        } else if (angka < 1000000) {
            temp = Math.floor(angka / 1000);
            hasil = terbilang(temp) + ' ribu ' + terbilang(angka % 1000);
        } else if (angka < 1000000000) {
            temp = Math.floor(angka / 1000000);
            hasil = terbilang(temp) + ' juta ' + terbilang(angka % 1000000);
        }
        return capitalizeEachWord(hasil.trim());
    }
    //=============================================================================================
    
    // Handle Role Switcher
    $(document).ready(function() {
        $('.switch-role-btn').on('click', function(e) {
            e.preventDefault();
            const role = $(this).data('role');
            const $btn = $(this);
            
            // Jika sudah aktif, tidak perlu switch
            if ($btn.hasClass('active')) {
                return;
            }
            
            // Disable semua button
            $('.switch-role-btn').addClass('disabled');
            $btn.html('<i class="fas fa-spinner fa-spin"></i> <span class="ml-2">Memproses...</span>');

            // Kirim request untuk switch role
            $.ajax({
                url: '<?= base_url('backend/dashboard/switch-role') ?>',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    role: role
                }),
                success: function(response) {
                    if (response.success) {
                        // Redirect ke dashboard sesuai peran
                        window.location.href = response.redirect;
                    } else {
                        alert('Gagal mengubah peran: ' + (response.message || 'Terjadi kesalahan'));
                        // Enable button kembali
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat mengubah peran. Silakan coba lagi.');
                    // Enable button kembali
                    location.reload();
                }
            });
        });

        // Theme Switcher - menggunakan localStorage saja
        (function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const body = document.body;
            
            if (!themeToggle || !themeIcon) return;
            
            // Get current theme from localStorage, default to 'light'
            let currentTheme = localStorage.getItem('user_theme') || 'light';
            
            // Validate theme value
            if (currentTheme !== 'dark' && currentTheme !== 'light') {
                currentTheme = 'light';
            }
            
            // Update icon based on current theme
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'fas fa-sun';
                    themeToggle.setAttribute('title', 'Ubah ke Mode Terang');
                } else {
                    themeIcon.className = 'fas fa-moon';
                    themeToggle.setAttribute('title', 'Ubah ke Mode Gelap');
                }
            }
            
            // Apply theme
            function applyTheme(theme) {
                if (theme === 'dark') {
                    body.classList.add('dark-mode');
                } else {
                    body.classList.remove('dark-mode');
                }
                localStorage.setItem('user_theme', theme);
                updateThemeIcon(theme);
            }
            
            // Initialize theme on page load - apply immediately to prevent flash
            applyTheme(currentTheme);
            
            // Toggle theme on click
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle theme
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                // Apply theme immediately
                applyTheme(newTheme);
                currentTheme = newTheme;
            });
        })();

        // AdminLTE Customization - Load settings from localStorage
        (function() {
            const settings = JSON.parse(localStorage.getItem('adminlte_settings') || '{}');
            if (Object.keys(settings).length === 0) return;
            
            const body = document.body;
            const sidebar = document.querySelector('.main-sidebar');
            const navbar = document.querySelector('.main-header.navbar');
            const controlSidebar = document.querySelector('.control-sidebar');
            
            // Remove default classes that might conflict
            body.classList.remove('layout-fixed', 'layout-navbar-fixed', 'layout-footer-fixed', 'layout-boxed', 'layout-top-nav', 'sidebar-collapse', 'sidebar-mini', 'sidebar-mini-md', 'sidebar-mini-xs');
            
            // Apply layout classes
            if (settings.layoutFixed) body.classList.add('layout-fixed');
            if (settings.layoutNavbarFixed) body.classList.add('layout-navbar-fixed');
            if (settings.layoutFooterFixed) body.classList.add('layout-footer-fixed');
            if (settings.layoutBoxed) body.classList.add('layout-boxed');
            if (settings.layoutTopNav) body.classList.add('layout-top-nav');
            if (settings.sidebarCollapse) body.classList.add('sidebar-collapse');
            if (settings.sidebarMini) body.classList.add('sidebar-mini');
            if (settings.sidebarMiniMd) body.classList.add('sidebar-mini-md');
            if (settings.sidebarMiniXs) body.classList.add('sidebar-mini-xs');
            
            // Apply sidebar color
            if (settings.sidebarColor && sidebar) {
                sidebar.className = 'main-sidebar elevation-4 ' + settings.sidebarColor;
            }
            
            // Apply navbar variant
            if (settings.navbarVariant && navbar) {
                const classes = settings.navbarVariant.split(' ');
                navbar.className = 'main-header navbar navbar-expand ' + classes.join(' ');
            }
            
            // Apply additional options
            if (settings.textSm) body.classList.add('text-sm');
            if (settings.flatStyle) body.classList.add('flat-style');
            if (settings.legacyStyle) body.classList.add('legacy-style');
        })();
    });
</script>