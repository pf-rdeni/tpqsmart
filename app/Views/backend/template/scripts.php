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
    });
</script>