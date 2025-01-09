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
            "pageLength": 10,
            "lengthMenu": [ // Kustomisasi opsi jumlah entri yang tersedia
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
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
</script>