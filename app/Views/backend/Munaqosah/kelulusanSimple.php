<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Kelulusan Ujian (Simple)</h3>
                        <div class="d-flex">
                            <div class="mr-2">
                                <label class="mb-0 small">Tahun Ajaran</label>
                                <input type="text" id="filterTahunAjaran" class="form-control form-control-sm" value="<?= esc($current_tahun_ajaran) ?>">
                            </div>
                            <div class="mr-2">
                                <label class="mb-0 small">TPQ</label>
                                <select id="filterTpq" class="form-control form-control-sm">
                                    <option value="0">Semua TPQ</option>
                                    <?php if (!empty($tpqDropdown)) : foreach ($tpqDropdown as $tpq): ?>
                                            <option value="<?= esc($tpq['IdTpq']) ?>"><?= esc($tpq['NamaTpq']) ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                            <div class="mr-2">
                                <label class="mb-0 small">Type Ujian</label>
                                <select id="filterTypeUjian" class="form-control form-control-sm">
                                    <?php if ($isAdmin || ($aktiveTombolKelulusan && ($isOperator || $isKepalaTpq))): ?>
                                        <option value="munaqosah">Munaqosah</option>
                                    <?php endif; ?>
                                    <option value="pra-munaqosah">Pra-Munaqosah</option>
                                </select>
                            </div>
                            <div class="align-self-end">
                                <button id="btnReloadKelulusan" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> Muat</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Hidden input untuk role user -->
                        <input type="hidden" id="userRole" value="<?= (in_groups('Operator') || $isKepalaTpq || (!in_groups('Admin') && session()->get('IdTpq'))) ? 'operator' : 'admin' ?>">

                        <div class="table-responsive" id="kelulusanTableWrapper">
                            <table id="tblKelulusan" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="dt-center">Aksi</th>
                                        <th class="dt-left">Peserta</th>
                                        <th class="dt-center">Kelulusan</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyKelulusan"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<?php
// Cek apakah user adalah admin
$isAdmin = function_exists('in_groups') && in_groups('Admin');
?>
<style>
    .badge-status {
        font-size: 0.85rem;
        padding: 0.4rem 0.75rem;
    }

    .dt-center {
        text-align: center;
    }

    .dt-left {
        text-align: left;
    }

    .copy-link-btn-kelulusan {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .copy-link-btn-kelulusan:hover:not(:disabled) {
        transform: scale(1.05);
    }

    .copy-link-btn-kelulusan:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
<script>
    let kelulusanTable = null;

    function buildKelulusanRows(rows) {
        const body = [];
        rows.forEach(row => {
            const params = new URLSearchParams({
                NoPeserta: row.NoPeserta || '',
                IdTahunAjaran: row.IdTahunAjaran || '',
                TypeUjian: row.TypeUjian || '',
                IdTpq: row.IdTpq || ''
            }).toString();

            const viewUrl = '<?= base_url('backend/munaqosah/kelulusan-peserta') ?>' + '?' + params;
            const pdfUrl = '<?= base_url('backend/munaqosah/printKelulusanPesertaUjian') ?>' + '?' + params;
            const suratUrl = '<?= base_url('backend/munaqosah/printSuratKelulusanPesertaUjian') ?>' + '?' + params;

            const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
            const hasKey = row.HasKey || null;
            const noPeserta = row.NoPeserta || '-';
            const namaSantri = row.NamaSantri || '-';

            let actionHtml = '<div class="btn-group btn-group-sm" role="group">' +
                `<a class="btn btn-outline-primary" href="${pdfUrl}" target="_blank"><i class="fas fa-file-pdf"></i> Pdf</a>` +
                `<a class="btn btn-outline-success" href="${suratUrl}" target="_blank"><i class="fas fa-file-alt"></i> Surat</a>`;
            if (isAdmin) {
                actionHtml += `<a class="btn btn-outline-secondary" href="${viewUrl}" target="_blank"><i class="fas fa-eye"></i> View</a>`;
            }
            // Tambahkan button copy link jika HasKey tersedia
            if (hasKey) {
                const noHpAyah = row.NoHpAyah || '';
                const noHpIbu = row.NoHpIbu || '';
                const namaAyah = row.NamaAyah || '';
                const namaIbu = row.NamaIbu || '';
                actionHtml += `<button type="button" class="btn btn-outline-info btn-sm copy-link-btn-kelulusan" 
                                data-no-peserta="${noPeserta}" 
                                data-nama-santri="${namaSantri}"
                                data-haskey="${hasKey}"
                                data-no-hp-ayah="${noHpAyah}"
                                data-no-hp-ibu="${noHpIbu}"
                                data-nama-ayah="${namaAyah}"
                                data-nama-ibu="${namaIbu}"
                                title="Copy link WhatsApp untuk ${namaSantri}">
                            <i class="fas fa-copy"></i> Copy Link
                        </button>`;
            }
            actionHtml += '</div>';

            // Format Peserta: No Peserta, Nama Santri, TPQ
            const pesertaInfo = `<div><strong>${row.NoPeserta || '-'}</strong></div>` +
                `<div>${row.NamaSantri || '-'}</div>` +
                `<div class="text-muted small">${row.NamaTpq || '-'}</div>`;

            // Format Kelulusan: Status Kelulusan, Type Ujian, Tahun Ajaran
            const totalWeighted = parseFloat(row.total_weighted ?? 0).toFixed(2);
            const threshold = parseFloat(row.kelulusan_threshold ?? 0).toFixed(2);
            const status = row.kelulusan_status || '-';
            const diff = parseFloat(row.kelulusan_difference ?? 0).toFixed(2);
            const passed = !!row.kelulusan_met;
            const badgeClass = passed ? 'badge badge-success' : 'badge badge-danger';
            const badgeText = `${status} (${totalWeighted} / ${threshold})`;

            const kelulusanInfo = `<div><span class="badge badge-status ${badgeClass}" title="Selisih ${diff}">${badgeText}</span></div>` +
                `<div class="mt-1">${row.TypeUjian || '-'}</div>` +
                `<div class="text-muted small">${row.IdTahunAjaran || '-'}</div>`;

            const tds = `<td class="dt-center">${actionHtml}</td>` +
                `<td class="dt-left">${pesertaInfo}</td>` +
                `<td class="dt-center">${kelulusanInfo}</td>`;

            body.push(`<tr>${tds}</tr>`);
        });

        $('#tbodyKelulusan').html(body.join(''));
    }

    function loadKelulusan() {
        const tahun = $('#filterTahunAjaran').val().trim();
        const tpq = $('#filterTpq').val();
        const type = $('#filterTypeUjian').val();

        if (!tahun) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi',
                text: 'Tahun ajaran tidak boleh kosong'
            });
            return;
        }

        const url = '<?= base_url('backend/munaqosah/kelulusan-data') ?>' + `?IdTahunAjaran=${encodeURIComponent(tahun)}&IdTpq=${encodeURIComponent(tpq)}&TypeUjian=${encodeURIComponent(type)}`;

        Swal.fire({
            title: 'Memuat... ',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.getJSON(url, function(resp) {
            Swal.close();
            if (!resp.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: resp.message || 'Gagal memuat data'
                });
                return;
            }

            const data = resp.data || {
                rows: []
            };
            const rows = data.rows || [];

            if (kelulusanTable) {
                kelulusanTable.destroy();
                kelulusanTable = null;
            }

            $('#kelulusanTableWrapper').html(
                '<table id="tblKelulusan" class="table table-bordered table-striped" style="width:100%">' +
                '<thead><tr><th class="dt-center">Aksi</th><th class="dt-left">Peserta</th><th class="dt-center">Kelulusan</th></tr></thead>' +
                '<tbody id="tbodyKelulusan"></tbody>' +
                '</table>'
            );

            buildKelulusanRows(rows);

            kelulusanTable = $('#tblKelulusan').DataTable({
                scrollX: true,
                order: [[2, 'desc']], // Sort by Kelulusan column
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: ['colvis', 'excel', 'print'],
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                    searchable: false
                }]
            });
        }).fail(function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Tidak dapat memuat data'
            });
        });
    }

    $(function() {
        const userRole = $('#userRole').val() || 'admin';
        const isOperator = userRole === 'operator';
        const aktiveTombolKelulusan = <?= ($aktiveTombolKelulusan ?? false) ? 'true' : 'false' ?>;
        const isAdmin = <?= ($isAdmin ?? false) ? 'true' : 'false' ?>;

        const $tpqSelect = $('#filterTpq');
        const $typeUjianSelect = $('#filterTypeUjian');

        // Cek apakah opsi munaqosah ada di dropdown
        const hasMunaqosahOption = $typeUjianSelect.find('option[value="munaqosah"]').length > 0;

        const nonZeroOptions = $tpqSelect.find('option').filter(function() {
            return $(this).val() !== '0';
        });

        if (nonZeroOptions.length === 1) {
            const onlyId = $(nonZeroOptions[0]).val();
            $tpqSelect.val(onlyId).prop('disabled', true);

            if (!isOperator && isAdmin) {
                $typeUjianSelect.val('pra-munaqosah').prop('disabled', true);
            } else if (isOperator) {
                if (!hasMunaqosahOption || !aktiveTombolKelulusan) {
                    $typeUjianSelect.val('pra-munaqosah');
                }
            }
        } else {
            if (isOperator && (!hasMunaqosahOption || !aktiveTombolKelulusan)) {
                $typeUjianSelect.val('pra-munaqosah');
            }
        }

        $('#btnReloadKelulusan').on('click', loadKelulusan);
        $('#filterTpq').on('change', loadKelulusan);
        $('#filterTypeUjian').on('change', loadKelulusan);

        // Copy link button click handler untuk kelulusan
        $(document).on('click', '.copy-link-btn-kelulusan:not(:disabled)', function() {
            const noPeserta = $(this).data('no-peserta');
            const namaSantri = $(this).data('nama-santri');
            const hasKey = $(this).data('haskey');
            const noHpAyah = $(this).data('no-hp-ayah') || '';
            const noHpIbu = $(this).data('no-hp-ibu') || '';
            const namaAyah = $(this).data('nama-ayah') || '';
            const namaIbu = $(this).data('nama-ibu') || '';

            if (!hasKey) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'HasKey tidak ditemukan untuk peserta ini',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Format teks yang akan dicopy
            const baseUrl = '<?= base_url('cek-status/') ?>';
            const statusUrl = baseUrl + hasKey;
            const copyText = `${noPeserta}-${namaSantri}\nCheck Status:\n${statusUrl}`;

            // Format nomor HP untuk WhatsApp (hapus karakter non-digit)
            const formatNoHp = (noHp) => {
                if (!noHp) return '';
                return noHp.replace(/\D/g, '');
            };

            const noHpAyahFormatted = formatNoHp(noHpAyah);
            const noHpIbuFormatted = formatNoHp(noHpIbu);

            // Buat pesan untuk WhatsApp
            const pesanWhatsApp = `Assalamu'alaikum\n\nHasil kelulusan ujian munaqosah untuk ${namaSantri} (No. Peserta: ${noPeserta}) sudah dapat dilihat melalui link berikut:\n\n${statusUrl}\n\nTerima kasih.`;
            const pesanEncoded = encodeURIComponent(pesanWhatsApp);

            // Buat HTML untuk opsi WhatsApp
            let whatsappOptionsHtml = '';
            if (noHpAyahFormatted || noHpIbuFormatted) {
                whatsappOptionsHtml = '<div class="mt-3"><strong>Kirim ke WhatsApp:</strong><div class="mt-2">';

                if (noHpAyahFormatted) {
                    const waLinkAyah = `https://wa.me/${noHpAyahFormatted}?text=${pesanEncoded}`;
                    const labelAyah = namaAyah ? `Kirim ke WhatsApp Ayah (${namaAyah})` : `Kirim ke WhatsApp Ayah (${noHpAyah})`;
                    whatsappOptionsHtml += `
                        <a href="${waLinkAyah}" target="_blank" class="btn btn-success btn-sm btn-block mb-2" style="text-decoration: none;">
                            <i class="fab fa-whatsapp"></i> ${labelAyah}
                        </a>`;
                }

                if (noHpIbuFormatted) {
                    const waLinkIbu = `https://wa.me/${noHpIbuFormatted}?text=${pesanEncoded}`;
                    const labelIbu = namaIbu ? `Kirim ke WhatsApp Ibu (${namaIbu})` : `Kirim ke WhatsApp Ibu (${noHpIbu})`;
                    whatsappOptionsHtml += `
                        <a href="${waLinkIbu}" target="_blank" class="btn btn-success btn-sm btn-block mb-2" style="text-decoration: none;">
                            <i class="fab fa-whatsapp"></i> ${labelIbu}
                        </a>`;
                }

                whatsappOptionsHtml += '</div></div>';
            }

            // Tampilkan popup dengan informasi yang akan dicopy dan opsi WhatsApp
            Swal.fire({
                title: 'Copy Link Status Ujian',
                html: `
                    <div class="text-left">
                        <p><strong>Nama Santri:</strong> ${namaSantri}</p>
                        <p><strong>No Peserta:</strong> ${noPeserta}</p>
                        <p><strong>Konten yang akan dicopy:</strong></p>
                        <div class="border p-3 bg-light rounded mt-2" style="font-family: monospace; font-size: 0.9rem; white-space: pre-wrap; word-break: break-all;">
${copyText}
                        </div>
                        ${whatsappOptionsHtml}
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-copy"></i> Copy ke Clipboard',
                cancelButtonText: 'Batal',
                footer: 'Klik "Copy ke Clipboard" untuk menyalin konten atau klik tombol WhatsApp untuk mengirim langsung',
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Copy ke clipboard
                    copyToClipboardKelulusan(copyText, namaSantri);
                }
            });
        });

        // Fungsi untuk copy ke clipboard untuk kelulusan
        function copyToClipboardKelulusan(text, namaSantri) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Link untuk ${namaSantri} telah disalin ke clipboard`,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }).catch(function(err) {
                    console.error('Error copying to clipboard:', err);
                    fallbackCopyToClipboardKelulusan(text, namaSantri);
                });
            } else {
                fallbackCopyToClipboardKelulusan(text, namaSantri);
            }
        }

        // Fallback method untuk copy ke clipboard untuk kelulusan
        function fallbackCopyToClipboardKelulusan(text, namaSantri) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Link untuk ${namaSantri} telah disalin ke clipboard`,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    throw new Error('Copy command failed');
                }
            } catch (err) {
                console.error('Error copying to clipboard:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: `
                        <div class="text-left">
                            <p>Gagal menyalin ke clipboard. Silakan copy manual:</p>
                            <div class="border p-2 bg-light rounded mt-2" style="font-family: monospace; font-size: 0.85rem; white-space: pre-wrap; word-break: break-all;">
${text}
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                });
            } finally {
                document.body.removeChild(textArea);
            }
        }

        loadKelulusan();
    });
</script>
<?= $this->endSection(); ?>

