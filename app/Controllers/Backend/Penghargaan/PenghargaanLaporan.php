<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;
use Config\Database;

/**
 * Controller Laporan, Rekapitulasi, Top Santri, & Audit Trail Penghargaan.
 */
class PenghargaanLaporan extends BaseController
{
    protected $jenisModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->jenisModel   = new PenghargaanJenisModel();
        $this->helpFunction = new HelpFunctionModel();
    }

    /**
     * Halaman utama Laporan & Rekap Penghargaan.
     */
    public function index()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idTahunAjaran = $this->request->getGet('tahun_ajaran') ?: (session()->get('IdTahunAjaran') ?: $this->helpFunction->getTahunAjaranSaatIni());
        $semester      = $this->request->getGet('semester') ?: '';
        $idJenis       = $this->request->getGet('jenis') ?: '';
        $status        = $this->request->getGet('status') ?: '';

        $data = $this->queryLaporanData($idTpq, $idTahunAjaran, $semester, $idJenis, $status);

        $db = Database::connect();
        $tahunAjaranList = $db->table('tbl_kelas_santri')
            ->select('IdTahunAjaran')
            ->distinct()
            ->orderBy('IdTahunAjaran', 'DESC')
            ->get()
            ->getResultArray();

        return view('backend/penghargaan/laporan/index', array_merge($data, [
            'page_title'      => 'Laporan & Rekap Penghargaan',
            'idTahunAjaran'   => $idTahunAjaran,
            'semester'        => $semester,
            'idJenis'         => $idJenis,
            'status'          => $status,
            'listJenis'       => $this->jenisModel->getAvailableByTpq($idTpq, true),
            'tahunAjaranList' => $tahunAjaranList,
        ]));
    }

    /**
     * Export rekap penerima penghargaan ke file CSV (kompatibel Excel).
     */
    public function exportCsv()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idTahunAjaran = $this->request->getGet('tahun_ajaran') ?: '';
        $semester      = $this->request->getGet('semester') ?: '';
        $idJenis       = $this->request->getGet('jenis') ?: '';
        $status        = $this->request->getGet('status') ?: '';

        $data     = $this->queryLaporanData($idTpq, $idTahunAjaran, $semester, $idJenis, $status);
        $penerima = $data['penerima'];

        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, ['No', 'Nomor Seri', 'Nama Santri', 'Kelas', 'Jenis Penghargaan', 'Semester', 'Tahun Ajaran', 'Peringkat', 'Nilai Rata-rata', '% Hadir', 'Skor Gabungan', 'Status']);

        foreach ($penerima as $i => $row) {
            fputcsv($fh, [
                $i + 1,
                $row['NomorSeri'],
                $row['NamaSantri'],
                $row['NamaKelas'] ?? '-',
                $row['NamaPenghargaan'],
                $row['Semester'],
                $row['IdTahunAjaran'],
                $row['Peringkat'],
                $row['NilaiRataRata'],
                $row['PersenKehadiran'],
                $row['SkorGabungan'],
                strtoupper((string) $row['StatusSertifikat']),
            ]);
        }

        rewind($fh);
        $csv = (string) stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="Laporan_Penghargaan_' . date('Ymd_His') . '.csv"')
            ->setBody("\xEF\xBB\xBF" . $csv);
    }

    /**
     * Export rekap penerima penghargaan ke PDF via mPDF.
     */
    public function exportPdf()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idTahunAjaran = $this->request->getGet('tahun_ajaran') ?: '';
        $semester      = $this->request->getGet('semester') ?: '';
        $idJenis       = $this->request->getGet('jenis') ?: '';
        $status        = $this->request->getGet('status') ?: '';

        $data = $this->queryLaporanData($idTpq, $idTahunAjaran, $semester, $idJenis, $status);

        $rowsHtml = '';
        foreach ($data['penerima'] as $i => $r) {
            $rowsHtml .= sprintf(
                '<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td align="center">%s</td><td align="right">%s</td><td align="right">%s%%</td><td align="center">%s</td></tr>',
                $i + 1,
                esc($r['NomorSeri']),
                esc($r['NamaSantri']),
                esc($r['NamaKelas'] ?? '-'),
                esc($r['NamaPenghargaan']),
                esc($r['Semester'] . ' ' . $r['IdTahunAjaran']),
                esc($r['Peringkat']),
                number_format((float) $r['NilaiRataRata'], 2),
                number_format((float) $r['PersenKehadiran'], 2),
                esc(strtoupper((string) $r['StatusSertifikat']))
            );
        }

        $html = '
        <h3 style="text-align:center;margin-bottom:4px;">LAPORAN REKAPITULASI PENGHARGAAN SANTRI</h3>
        <p style="text-align:center;font-size:10pt;color:#555;margin-top:0;">Dicetak: ' . date('d/m/Y H:i') . '</p>
        <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size:9pt;border-collapse:collapse;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th>No</th><th>Nomor Seri</th><th>Nama Santri</th><th>Kelas</th><th>Penghargaan</th><th>Periode</th><th>Rank</th><th>Nilai</th><th>Hadir</th><th>Status</th>
                </tr>
            </thead>
            <tbody>' . ($rowsHtml ?: '<tr><td colspan="10" align="center">Tidak ada data</td></tr>') . '</tbody>
        </table>';

        $tempDir = WRITEPATH . 'cache/mpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L', 'tempDir' => $tempDir]);
        $mpdf->WriteHTML($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Laporan_Penghargaan.pdf"')
            ->setBody($mpdf->Output('Laporan_Penghargaan.pdf', 'S'));
    }

    /**
     * Query terpusat untuk data penerima, top santri, rekap jenis, dan audit trail.
     */
    private function queryLaporanData(string $idTpq, string $idTahunAjaran, string $semester, string $idJenis, string $status): array
    {
        $db = Database::connect();

        $builder = $db->table('tbl_penghargaan_penerima p')
            ->select('p.*, s.IdTahunAjaran, s.Semester, s.Momen, s.Status as StatusSertifikat, s.TanggalGenerate, s.TanggalRelease, j.Kode, j.NamaPenghargaan, j.Kategori, sb.NamaSantri, k.NamaKelas')
            ->join('tbl_penghargaan_sertifikat s', 's.Id = p.IdSertifikat')
            ->join('tbl_penghargaan_jenis j', 'j.Id = s.IdJenis', 'left')
            ->join('tbl_santri_baru sb', 'sb.IdSantri = p.IdSantri', 'left')
            ->join('tbl_kelas k', 'k.IdKelas = p.IdKelas', 'left')
            ->where('p.IdTpq', $idTpq);

        if ($idTahunAjaran !== '') {
            $builder->where('s.IdTahunAjaran', $idTahunAjaran);
        }
        if ($semester !== '') {
            $builder->where('s.Semester', $semester);
        }
        if ($idJenis !== '') {
            $builder->where('s.IdJenis', (int) $idJenis);
        }
        if ($status !== '') {
            $builder->where('s.Status', $status);
        }

        $penerima = $builder->orderBy('s.TanggalGenerate', 'DESC')
            ->orderBy('p.Peringkat', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung Top Santri & Rekap per Jenis
        $santriCount = [];
        $jenisCount  = [];
        foreach ($penerima as $row) {
            $sid = (string) $row['IdSantri'];
            if (!isset($santriCount[$sid])) {
                $santriCount[$sid] = [
                    'NamaSantri' => $row['NamaSantri'],
                    'NamaKelas'  => $row['NamaKelas'] ?? '-',
                    'Total'      => 0,
                ];
            }
            $santriCount[$sid]['Total']++;

            $jName = $row['NamaPenghargaan'] ?? '-';
            $jenisCount[$jName] = ($jenisCount[$jName] ?? 0) + 1;
        }
        usort($santriCount, static fn ($a, $b) => $b['Total'] <=> $a['Total']);

        // Audit Trail Batch
        $auditBuilder = $db->table('tbl_penghargaan_sertifikat s')
            ->select('s.*, j.NamaPenghargaan, ug.username as GeneratorName, ur.username as ReleaserName, COUNT(p.Id) as JumlahPenerima')
            ->join('tbl_penghargaan_jenis j', 'j.Id = s.IdJenis', 'left')
            ->join('users ug', 'ug.id = s.GeneratedBy', 'left')
            ->join('users ur', 'ur.id = s.ReleasedBy', 'left')
            ->join('tbl_penghargaan_penerima p', 'p.IdSertifikat = s.Id', 'left')
            ->where('s.IdTpq', $idTpq);

        if ($idTahunAjaran !== '') {
            $auditBuilder->where('s.IdTahunAjaran', $idTahunAjaran);
        }
        $auditTrail = $auditBuilder->groupBy('s.Id')->orderBy('s.TanggalGenerate', 'DESC')->limit(20)->get()->getResultArray();

        return [
            'penerima'   => $penerima,
            'topSantri'  => array_slice($santriCount, 0, 10),
            'rekapJenis' => $jenisCount,
            'auditTrail' => $auditTrail,
        ];
    }
}
