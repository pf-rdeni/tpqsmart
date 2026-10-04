<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary" style="border-radius:12px;">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="card-title mb-0" id="dtTitle"><i class="fas fa-list mr-1"></i> Detail Statistik Hasil Munaqosah</h3>
                    <div class="small text-muted" id="dtSub"></div>
                </div>
                <div class="mt-1">
                    <input type="search" id="dtSearch" class="form-control form-control-sm d-inline-block" style="width:210px" placeholder="Cari nama / no peserta…">
                    <button class="btn btn-sm btn-outline-success" id="dtCsv"><i class="fas fa-file-csv"></i> CSV</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.close()"><i class="fas fa-times"></i> Tutup</button>
                </div>
            </div>
            <div class="card-body">
                <div id="dtLoading" class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                <div id="dtError" class="alert alert-danger" style="display:none;"></div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" id="dtTable" style="display:none;">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th data-k="np" class="sortable">No Peserta</th>
                                <th data-k="nm" class="sortable">Nama Santri</th>
                                <th data-k="tpqName" class="sortable">TPQ</th>
                                <th data-k="y" class="sortable">Tahun Ajaran</th>
                                <th data-k="type" class="sortable">Type Ujian</th>
                                <th data-k="catName" class="sortable">Materi</th>
                                <th data-k="v" class="sortable text-center">Nilai Rata-rata</th>
                                <th style="width:60px" class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody id="dtBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    th.sortable { cursor: pointer; user-select: none; }
    th.sortable:hover { background: #eef1f6; }
</style>
<script>
    (function() {
        const q = new URLSearchParams(window.location.search);
        const $ = id => document.getElementById(id);
        const fmtTA = t => /^\d{8}$/.test(String(t)) ? 'T.A ' + String(t).slice(2, 4) + '/' + String(t).slice(6, 8) : String(t);
        const fmtType = t => t === 'pra-munaqosah' ? 'Pra-Munaqosah' : (t === 'munaqosah' ? 'Munaqosah' : (t || '-'));
        const badgeType = t => t === 'munaqosah'
            ? '<span class="badge badge-success">Munaqosah</span>'
            : '<span class="badge badge-info">Pra-Munaqosah</span>';
        const esc = s => String(s).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));
        let items = [], meta = {}, sortK = 'v', sortDir = -1, term = '';

        const judul = q.get('Judul') || 'Detail Statistik Hasil Munaqosah';
        $('dtTitle').innerHTML = '<i class="fas fa-list mr-1"></i> ' + esc(judul);
        document.title = judul;

        function view() {
            let rows = items.filter(i => !term || (i.nm + ' ' + i.np).toLowerCase().includes(term));
            rows.sort((a, b) => {
                const x = a[sortK], y = b[sortK];
                return (typeof x === 'number' ? x - y : String(x).localeCompare(String(y))) * sortDir;
            });
            $('dtSub').textContent = rows.length + ' data' + (term ? ' (dari ' + items.length + ')' : '');
            $('dtBody').innerHTML = rows.map((i, n) => {
                const href = '<?= base_url('backend/munaqosah/kelulusan-peserta') ?>?' + new URLSearchParams({
                    NoPeserta: i.np, IdTahunAjaran: i.y, TypeUjian: i.type || meta.TypeUjian || '', IdTpq: i.tpq
                }).toString();
                return `<tr><td>${n + 1}</td><td>${esc(i.np)}</td><td>${esc(i.nm)}</td><td>${esc(i.tpqName)}</td><td>${esc(fmtTA(i.y))}</td><td>${badgeType(i.type)}</td><td>${esc(i.catName)}</td>` +
                    `<td class="text-center">${i.v > 0 ? i.v.toFixed(2) : '<span class="badge badge-secondary">0</span>'}</td>` +
                    `<td class="text-center"><a class="btn btn-xs btn-outline-primary" target="_blank" href="${href}"><i class="fas fa-eye"></i></a></td></tr>`;
            }).join('') || '<tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>';
            return rows;
        }

        document.querySelectorAll('th.sortable').forEach(th => th.addEventListener('click', () => {
            const k = th.dataset.k;
            sortDir = (sortK === k) ? -sortDir : 1;
            sortK = k;
            view();
        }));
        $('dtSearch').addEventListener('input', e => { term = e.target.value.toLowerCase(); view(); });
        $('dtCsv').addEventListener('click', () => {
            const rows = view();
            const c = v => '"' + String(v).replace(/"/g, '""') + '"';
            const lines = [['No', 'No Peserta', 'Nama Santri', 'TPQ', 'Tahun Ajaran', 'Type Ujian', 'Materi', 'Nilai'].map(c).join(',')];
            rows.forEach((i, n) => lines.push([n + 1, c(i.np), c(i.nm), c(i.tpqName), c(fmtTA(i.y)), c(fmtType(i.type)), c(i.catName), i.v].join(',')));
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob(['\ufeff' + lines.join('\n')], {type: 'text/csv;charset=utf-8;'}));
            a.download = judul.replace(/[^\w\-]+/g, '_') + '.csv';
            a.click();
        });

        fetch('<?= base_url('backend/munaqosah/statistik-hasil-detail-data') ?>' + window.location.search, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(r => r.json())
            .then(j => {
                if (!j.success) throw new Error(j.message || 'Gagal memuat data');
                items = j.data.items;
                meta = j.data.meta || {};
                $('dtTable').style.display = '';
                view();
            })
            .catch(e => { $('dtError').style.display = ''; $('dtError').textContent = e.message; })
            .finally(() => $('dtLoading').style.display = 'none');
    })();
</script>
<?= $this->endSection(); ?>
