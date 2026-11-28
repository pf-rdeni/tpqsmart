<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Helpers\PenghargaanHelper;
use App\Models\HelpFunctionModel;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;
use App\Models\Backend\Penghargaan\PenghargaanPenerimaModel;
use App\Models\Backend\Penghargaan\PenghargaanSertifikatModel;
use App\Models\Backend\Penghargaan\PenghargaanTemplateFieldModel;
use App\Models\Backend\Penghargaan\PenghargaanTemplateModel;
use App\Models\SantriBaruModel;
use App\Models\TpqModel;

/**
 * Controller Manajemen Sertifikat, Release, Cetak PDF (mPDF/ZIP), & Portal Santri.
 */
class PenghargaanSertifikat extends BaseController
{
    protected $sertifikatModel;
    protected $penerimaModel;
    protected $templateModel;
    protected $fieldModel;
    protected $jenisModel;
    protected $tpqModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->sertifikatModel = new PenghargaanSertifikatModel();
        $this->penerimaModel   = new PenghargaanPenerimaModel();
        $this->templateModel   = new PenghargaanTemplateModel();
        $this->fieldModel      = new PenghargaanTemplateFieldModel();
        $this->jenisModel      = new PenghargaanJenisModel();
        $this->tpqModel        = new TpqModel();
        $this->helpFunction    = new HelpFunctionModel();
    }

    /**
     * Daftar seluruh batch sertifikat yang telah di-generate.
     */
    public function index()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idTahunAjaran = $this->request->getGet('tahun_ajaran') ?: '';
        $status        = $this->request->getGet('status') ?: '';
        $roleCtx       = PenghargaanHelper::getRoleContext($idTahunAjaran ?: null);

        $filters = [
            'IdTahunAjaran' => $idTahunAjaran,
            'Status'        => $status,
        ];
        if ($roleCtx['isGuruOnly']) {
            $filters['IdKelasList'] = $roleCtx['guruKelasIds'];
        }

        $list = $this->sertifikatModel->getListByTpq($idTpq, $filters);

        return view('backend/penghargaan/sertifikat/index', [
            'page_title'    => 'Daftar & Cetak Sertifikat',
            'roleCtx'       => $roleCtx,
            'list'          => $list,
            'idTahunAjaran' => $idTahunAjaran,
            'status'        => $status,
        ]);
    }

    /**
     * Detail 1 batch sertifikat + daftar penerima + pilih template + aksi cetak.
     */
    public function detail(int $idSertifikat)
    {
        $sertifikat = $this->sertifikatModel->find($idSertifikat);
        if (!$sertifikat) {
            return redirect()->to(base_url('backend/penghargaan/sertifikat'))
                ->with('error', 'Batch sertifikat tidak ditemukan.');
        }

        $roleCtx   = PenghargaanHelper::getRoleContext((string) ($sertifikat['IdTahunAjaran'] ?? ''));
        $idTpq     = (string) $sertifikat['IdTpq'];
        $jenis     = $this->jenisModel->find((int) $sertifikat['IdJenis']);
        $penerima  = $this->penerimaModel->getBySertifikat(
            $idSertifikat,
            $roleCtx['isGuruOnly'] ? $roleCtx['guruKelasIds'] : null
        );
        $templates = $this->templateModel->getAvailableByTpq($idTpq, (int) $sertifikat['IdJenis'], 'aktif');

        return view('backend/penghargaan/sertifikat/detail', [
            'page_title' => 'Detail Sertifikat: ' . ($jenis['NamaPenghargaan'] ?? ''),
            'roleCtx'    => $roleCtx,
            'sertifikat' => $sertifikat,
            'jenis'      => $jenis,
            'penerima'   => $penerima,
            'templates'  => $templates,
        ]);
    }

    /**
     * Ubah template desain yang digunakan oleh batch sertifikat.
     */
    public function setTemplate(int $idSertifikat)
    {
        if (PenghargaanHelper::getRoleContext()['isGuruOnly']) {
            return redirect()->back()->with('error', 'Akses ditolak: Peran Guru bersifat read-only.');
        }
        $idTemplate = (int) $this->request->getPost('IdTemplate');
        $this->sertifikatModel->update($idSertifikat, ['IdTemplate' => $idTemplate ?: null]);

        return redirect()->to(base_url('backend/penghargaan/sertifikat/detail/' . $idSertifikat))
            ->with('success', 'Template sertifikat berhasil diperbarui.');
    }

    /**
     * Ubah tanggal penetapan sertifikat secara manual.
     */
    public function setTanggal(int $idSertifikat)
    {
        if (PenghargaanHelper::getRoleContext()['isGuruOnly']) {
            return redirect()->back()->with('error', 'Akses ditolak: Peran Guru bersifat read-only.');
        }
        $tgl = trim((string) $this->request->getPost('TanggalSertifikat'));
        if ($tgl === '') {
            return redirect()->back()->with('error', 'Tanggal sertifikat tidak boleh kosong.');
        }

        $this->sertifikatModel->update($idSertifikat, ['TanggalSertifikat' => $tgl]);

        return redirect()->to(base_url('backend/penghargaan/sertifikat/detail/' . $idSertifikat))
            ->with('success', 'Tanggal sertifikat berhasil diperbarui.');
    }

    /**
     * Release batch sertifikat (status: draft -> final) agar terlihat oleh santri.
     */
    public function release(int $idSertifikat)
    {
        if (PenghargaanHelper::getRoleContext()['isGuruOnly']) {
            return redirect()->back()->with('error', 'Akses ditolak: Peran Guru bersifat read-only.');
        }
        $sertifikat = $this->sertifikatModel->find($idSertifikat);
        if (!$sertifikat) {
            return redirect()->back()->with('error', 'Batch sertifikat tidak ditemukan.');
        }

        $this->sertifikatModel->update($idSertifikat, [
            'Status'         => 'final',
            'TanggalRelease' => date('Y-m-d H:i:s'),
            'ReleasedBy'     => function_exists('user_id') ? user_id() : null,
        ]);

        return redirect()->back()->with('success', 'Sertifikat berhasil di-release (Final) dan kini dapat dilihat oleh santri.');
    }

    /**
     * Batalkan / cabut batch sertifikat (status -> dibatal).
     */
    public function cancel(int $idSertifikat)
    {
        if (PenghargaanHelper::getRoleContext()['isGuruOnly']) {
            return redirect()->back()->with('error', 'Akses ditolak: Peran Guru bersifat read-only.');
        }
        $sertifikat = $this->sertifikatModel->find($idSertifikat);
        if (!$sertifikat) {
            return redirect()->back()->with('error', 'Batch sertifikat tidak ditemukan.');
        }

        $this->sertifikatModel->update($idSertifikat, ['Status' => 'dibatal']);

        return redirect()->back()->with('success', 'Sertifikat berhasil dibatalkan.');
    }

    /**
     * Cetak PDF Individual untuk 1 penerima.
     */
    public function cetakIndividual(int $idPenerima)
    {
        $bundle = $this->loadPenerimaBundle($idPenerima);
        if (!$bundle) {
            return redirect()->back()->with('error', 'Data penerima sertifikat tidak ditemukan.');
        }

        [$penerima, $sertifikat, $template, $fields] = $bundle;

        $roleCtx = PenghargaanHelper::getRoleContext((string) ($sertifikat['IdTahunAjaran'] ?? ''));
        if ($roleCtx['isGuruOnly'] && !in_array((string) ($penerima['IdKelas'] ?? ''), $roleCtx['guruKelasIds'], true)) {
            return redirect()->back()->with('error', 'Akses ditolak: Sertifikat bukan dari kelas yang Anda ampu.');
        }

        $this->penerimaModel->update($idPenerima, [
            'TanggalCetak' => date('Y-m-d H:i:s'),
            'CetakBy'      => function_exists('user_id') ? user_id() : null,
        ]);

        $mpdf = $this->createMpdfInstance($template);
        $html = $this->buildCertificatePageHtml($penerima, $sertifikat, $template, $fields);
        $mpdf->WriteHTML($html);

        $filename = 'Sertifikat_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $penerima['NamaSantri']) . '.pdf';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($mpdf->Output($filename, 'S'));
    }

    /**
     * Cetak Bulk PDF (semua penerima dalam 1 batch digabung dalam 1 PDF multi-halaman).
     */
    public function cetakBulk(int $idSertifikat)
    {
        $sertifikat = $this->sertifikatModel->find($idSertifikat);
        if (!$sertifikat) {
            return redirect()->back()->with('error', 'Batch sertifikat tidak ditemukan.');
        }

        $roleCtx      = PenghargaanHelper::getRoleContext((string) ($sertifikat['IdTahunAjaran'] ?? ''));
        $penerimaList = $this->penerimaModel->getBySertifikat(
            $idSertifikat,
            $roleCtx['isGuruOnly'] ? $roleCtx['guruKelasIds'] : null
        );
        if (empty($penerimaList)) {
            return redirect()->back()->with('error', 'Tidak ada penerima untuk dicetak.');
        }

        $template = $this->resolveTemplate($sertifikat);
        $fields   = $this->fieldModel->getByTemplate((int) $template['Id'], true);
        $mpdf     = $this->createMpdfInstance($template);

        foreach ($penerimaList as $idx => $p) {
            if ($idx > 0) {
                $mpdf->AddPage();
            }
            $html = $this->buildCertificatePageHtml($p, $sertifikat, $template, $fields);
            $mpdf->WriteHTML($html);
        }

        $filename = 'Sertifikat_Batch_' . $idSertifikat . '.pdf';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($mpdf->Output($filename, 'S'));
    }

    /**
     * Cetak Massal ZIP (1 PDF per santri dikemas dalam file ZIP).
     */
    public function cetakZip(int $idSertifikat)
    {
        $sertifikat = $this->sertifikatModel->find($idSertifikat);
        if (!$sertifikat) {
            return redirect()->back()->with('error', 'Batch sertifikat tidak ditemukan.');
        }

        $roleCtx      = PenghargaanHelper::getRoleContext((string) ($sertifikat['IdTahunAjaran'] ?? ''));
        $penerimaList = $this->penerimaModel->getBySertifikat(
            $idSertifikat,
            $roleCtx['isGuruOnly'] ? $roleCtx['guruKelasIds'] : null
        );
        if (empty($penerimaList)) {
            return redirect()->back()->with('error', 'Tidak ada penerima untuk dicetak.');
        }

        $template = $this->resolveTemplate($sertifikat);
        $fields   = $this->fieldModel->getByTemplate((int) $template['Id'], true);

        $tmpDir = WRITEPATH . 'uploads/tmp_cert_' . $idSertifikat . '_' . time() . '/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $zipPath = $tmpDir . 'Sertifikat_Batch_' . $idSertifikat . '.zip';
        $zip     = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Gagal membuat arsip ZIP.');
        }

        foreach ($penerimaList as $p) {
            $mpdf = $this->createMpdfInstance($template);
            $html = $this->buildCertificatePageHtml($p, $sertifikat, $template, $fields);
            $mpdf->WriteHTML($html);
            $pdfName = ($p['Peringkat'] ? $p['Peringkat'] . '_' : '') . preg_replace('/[^A-Za-z0-9_-]/', '_', $p['NamaSantri']) . '.pdf';
            $zip->addFromString($pdfName, $mpdf->Output($pdfName, 'S'));
        }
        $zip->close();

        $zipContent = (string) file_get_contents($zipPath);
        @unlink($zipPath);
        @rmdir($tmpDir);

        return $this->response
            ->setHeader('Content-Type', 'application/zip')
            ->setHeader('Content-Disposition', 'attachment; filename="Sertifikat_Batch_' . $idSertifikat . '.zip"')
            ->setBody($zipContent);
    }

    /**
     * Portal Penghargaan Santri (halaman terpisah saat santri login).
     */
    public function portalSantri()
    {
        $idSantri = $this->resolveLoggedInSantriId();
        if (!$idSantri) {
            return redirect()->to(base_url())->with('error', 'Data santri tidak ditemukan untuk akun ini.');
        }

        $idTahunAjaran = $this->request->getGet('tahun_ajaran') ?: '';
        $idJenis       = $this->request->getGet('jenis') ?: '';

        $listPenghargaan = $this->penerimaModel->getFinalBySantri($idSantri, [
            'IdTahunAjaran' => $idTahunAjaran,
            'IdJenis'       => $idJenis,
        ]);

        $idTpq     = (string) (session()->get('IdTpq') ?? '0');
        $listJenis = $this->jenisModel->getAvailableByTpq($idTpq, true);

        return view('backend/penghargaan/portal_santri/index', [
            'page_title'      => 'Penghargaan Saya',
            'listPenghargaan' => $listPenghargaan,
            'listJenis'       => $listJenis,
            'idTahunAjaran'   => $idTahunAjaran,
            'idJenis'         => $idJenis,
        ]);
    }

    /**
     * Preview sertifikat di browser untuk santri yang bersangkutan (status Final only).
     */
    public function portalPreview(int $idPenerima)
    {
        $idSantri = $this->resolveLoggedInSantriId();
        $bundle   = $this->loadPenerimaBundle($idPenerima);

        if (!$bundle || (string) $bundle[0]['IdSantri'] !== (string) $idSantri || ($bundle[1]['Status'] ?? '') !== 'final') {
            return redirect()->to(base_url('backend/penghargaan/portal-santri'))
                ->with('error', 'Sertifikat tidak ditemukan atau belum dirilis.');
        }

        [$penerima, $sertifikat, $template, $fields] = $bundle;

        $roleCtx = PenghargaanHelper::getRoleContext((string) ($sertifikat['IdTahunAjaran'] ?? ''));
        if ($roleCtx['isGuruOnly'] && !in_array((string) ($penerima['IdKelas'] ?? ''), $roleCtx['guruKelasIds'], true)) {
            return redirect()->back()->with('error', 'Akses ditolak: Sertifikat bukan dari kelas yang Anda ampu.');
        }
        $values = $this->buildFieldValues($penerima, $sertifikat);

        return view('backend/penghargaan/portal_santri/preview', [
            'page_title' => 'Preview Sertifikat Saya',
            'penerima'   => $penerima,
            'sertifikat' => $sertifikat,
            'template'   => $template,
            'fields'     => $fields,
            'values'     => $values,
        ]);
    }

    /**
     * Unduh PDF sertifikat untuk santri yang bersangkutan (status Final only).
     */
    public function portalDownload(int $idPenerima)
    {
        $idSantri = $this->resolveLoggedInSantriId();
        $bundle   = $this->loadPenerimaBundle($idPenerima);

        if (!$bundle || (string) $bundle[0]['IdSantri'] !== (string) $idSantri || ($bundle[1]['Status'] ?? '') !== 'final') {
            return redirect()->to(base_url('backend/penghargaan/portal-santri'))
                ->with('error', 'Sertifikat tidak ditemukan atau belum dirilis.');
        }

        return $this->cetakIndividual($idPenerima);
    }

    /**
     * Ambil IdSantri dari session atau lookup NIK user login.
     */
    private function resolveLoggedInSantriId(): ?string
    {
        $idSantri = session()->get('IdSantri');
        if (!empty($idSantri)) {
            return (string) $idSantri;
        }

        $nik = function_exists('user') && user() ? (user()->nik ?? null) : null;
        if ($nik) {
            $santriModel = new SantriBaruModel();
            $santri      = $santriModel->getSantriByNik($nik);
            if (!empty($santri['IdSantri'])) {
                session()->set('IdSantri', $santri['IdSantri']);
                return (string) $santri['IdSantri'];
            }
        }

        return null;
    }

    /**
     * Muat paket lengkap [penerima, sertifikat, template, fields] untuk 1 IdPenerima.
     */
    private function loadPenerimaBundle(int $idPenerima): ?array
    {
        $db       = \Config\Database::connect();
        $penerima = $db->table('tbl_penghargaan_penerima p')
            ->select('p.*, sb.NamaSantri, sb.NISN as NIS, k.NamaKelas')
            ->join('tbl_santri_baru sb', 'sb.IdSantri = p.IdSantri', 'left')
            ->join('tbl_kelas k', 'k.IdKelas = p.IdKelas', 'left')
            ->where('p.Id', $idPenerima)
            ->get()
            ->getRowArray();

        if (!$penerima) {
            return null;
        }

        $sertifikat = $this->sertifikatModel->find((int) $penerima['IdSertifikat']);
        if (!$sertifikat) {
            return null;
        }

        $template = $this->resolveTemplate($sertifikat);
        $fields   = $this->fieldModel->getByTemplate((int) $template['Id'], true);

        return [$penerima, $sertifikat, $template, $fields];
    }

    /**
     * Pilih template aktif untuk sertifikat (spesifik -> default TPQ -> starter sistem).
     */
    private function resolveTemplate(array $sertifikat): array
    {
        if (!empty($sertifikat['IdTemplate'])) {
            $tpl = $this->templateModel->find((int) $sertifikat['IdTemplate']);
            if ($tpl) {
                return $tpl;
            }
        }

        $available = $this->templateModel->getAvailableByTpq(
            (string) $sertifikat['IdTpq'],
            (int) $sertifikat['IdJenis'],
            'aktif'
        );

        if (!empty($available)) {
            return $available[0];
        }

        $any = $this->templateModel->first();
        return $any ?: [
            'Id'             => 0,
            'BackgroundFile' => 'uploads/penghargaan/templates/starter_formal.svg',
            'UkuranKertas'   => 'A4',
            'Orientasi'      => 'landscape',
        ];
    }

    /**
     * Buat instance mPDF sesuai ukuran kertas & orientasi template.
     */
    private function createMpdfInstance(array $template): \Mpdf\Mpdf
    {
        @ini_set('pcre.backtrack_limit', '5000000');
        $format      = ($template['UkuranKertas'] ?? 'A4') . '-' . (($template['Orientasi'] ?? 'landscape') === 'portrait' ? 'P' : 'L');
        $tempDir     = WRITEPATH . 'cache/mpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        return new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => $format,
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'tempDir'       => $tempDir,
        ]);
    }

    /**
     * Bangun peta nilai placeholder untuk 1 penerima sertifikat.
     */
    private function buildFieldValues(array $penerima, array $sertifikat): array
    {
        $jenis   = $this->jenisModel->find((int) $sertifikat['IdJenis']);
        $tpqRows = $this->tpqModel->GetData($sertifikat['IdTpq']);
        $tpq     = $tpqRows[0] ?? [];

        $logoHtml = '';
        if (!empty($tpq['LogoLembaga']) && is_file(FCPATH . 'uploads/logo/' . $tpq['LogoLembaga'])) {
            $logoPath = FCPATH . 'uploads/logo/' . $tpq['LogoLembaga'];
            $mime     = mime_content_type($logoPath) ?: 'image/png';
            $logoHtml = '<img src="data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath)) . '" style="max-width:100%;max-height:100%;">';
        }

        $signers      = PenghargaanHelper::resolvePenandatanganSertifikat($penerima, $sertifikat, true);
        $settingModel = new \App\Models\Backend\Penghargaan\PenghargaanSettingModel();
        $setting      = $settingModel->getOrCreateByTpq((string) $sertifikat['IdTpq']);

        $customWilayah = trim((string) ($setting['WilayahSertifikat'] ?? ''));
        $fallbackDesa  = trim((string) ($tpq['KelurahanDesa'] ?? ''));
        $wilayah       = $customWilayah !== ''
            ? $customWilayah
            : ($fallbackDesa !== '' ? ucwords(strtolower($fallbackDesa)) : '');
        $rawTanggal   = !empty($sertifikat['TanggalSertifikat'])
            ? $sertifikat['TanggalSertifikat']
            : (!empty($sertifikat['TanggalGenerate']) ? date('Y-m-d', strtotime($sertifikat['TanggalGenerate'])) : date('Y-m-d'));
        $tglIndo      = function_exists('formatTanggalIndonesia')
            ? formatTanggalIndonesia($rawTanggal, 'd F Y')
            : date('d F Y', strtotime($rawTanggal));
        $tempatTgl    = ($wilayah !== '' ? $wilayah . ', ' : '') . $tglIndo;

        $values = [
            'LOGO_LEMBAGA'      => $logoHtml,
            'NAMA_LEMBAGA'      => $tpq['NamaTpq'] ?? 'Lembaga TPQ',
            'JUDUL_SERTIFIKAT'  => 'SERTIFIKAT PENGHARGAAN',
            'NOMOR_SERTIFIKAT'  => 'No: ' . ($penerima['NomorSeri'] ?? '-'),
            'NAMA_SANTRI'       => $penerima['NamaSantri'] ?? '-',
            'KELAS'             => !empty($penerima['NamaKelas']) ? 'Kelas ' . $penerima['NamaKelas'] : '',
            'JENIS_PENGHARGAAN' => $jenis['NamaPenghargaan'] ?? '-',
            'PERINGKAT'         => !empty($penerima['Peringkat']) ? 'Peringkat ' . $penerima['Peringkat'] : ($penerima['CatatanKhusus'] ?? ''),
            'PERIODE'           => 'Semester ' . ($sertifikat['Semester'] ?? '-') . ' · TA ' . ($sertifikat['IdTahunAjaran'] ?? '-'),
            'NILAI_RATA_RATA'   => 'Nilai Rata-rata: ' . number_format((float) ($penerima['NilaiRataRata'] ?? 0), 2),
            'PERSEN_KEHADIRAN'  => 'Kehadiran: ' . number_format((float) ($penerima['PersenKehadiran'] ?? 0), 2) . '%',
            'TANGGAL_CETAK'     => $tempatTgl,
        ];

        for ($i = 1; $i <= 3; $i++) {
            $s = $signers[$i] ?? null;
            if (!$s) {
                $values["TTD_{$i}_JABATAN"] = '';
                $values["TTD_{$i}_NAMA"]    = '';
                $values["TTD_{$i}_QR"]      = '';
                continue;
            }

            $values["TTD_{$i}_JABATAN"] = $s['jabatan'] ?? $s['label'] ?? '';
            $values["TTD_{$i}_NAMA"]    = $s['nama'] ?? '';

            if ($s['mode'] === 'qr' && !empty($s['qr_data'])) {
                $values["TTD_{$i}_QR"] = '<img src="' . $s['qr_data'] . '" style="width:100%;height:100%;">';
            } elseif ($s['mode'] === 'upload' && !empty($s['ttd_upload'])) {
                $values["TTD_{$i}_QR"] = '<img src="' . $s['ttd_upload'] . '" style="max-width:100%;max-height:100%;">';
            } else {
                $values["TTD_{$i}_QR"] = '<div style="width:100%;height:100%;border-bottom:1px solid #333;"></div>';
            }
        }

        return $values;
    }

    /**
     * Render HTML 1 halaman sertifikat untuk mPDF (mengkonversi koordinat 1123x794px ke mm).
     */
    private function buildCertificatePageHtml(array $penerima, array $sertifikat, array $template, array $fields): string
    {
        $isPortrait = ($template['Orientasi'] ?? 'landscape') === 'portrait';
        $isA5       = ($template['UkuranKertas'] ?? 'A4') === 'A5';
        $pageWmm    = $isA5 ? ($isPortrait ? 148 : 210) : ($isPortrait ? 210 : 297);
        $pageHmm    = $isA5 ? ($isPortrait ? 210 : 148) : ($isPortrait ? 297 : 210);
        $baseWpx    = $isPortrait ? 794 : 1123;
        $baseHpx    = $isPortrait ? 1123 : 794;

        $scaleX = $pageWmm / $baseWpx;
        $scaleY = $pageHmm / $baseHpx;

        $bgCss = '';
        if (!empty($template['BackgroundFile'])) {
            $absBg = FCPATH . ltrim((string) $template['BackgroundFile'], '/');
            if (is_file($absBg)) {
                // Gunakan path file langsung agar tidak memicu pcre.backtrack_limit pada CssManager::ReadCSS() mPDF
                $safePath = str_replace('\\', '/', $absBg);
                $bgCss    = "background-image: url('{$safePath}'); background-image-resize: 6;";
            }
        }

        $values     = $this->buildFieldValues($penerima, $sertifikat);
        $isDraft    = ($sertifikat['Status'] ?? 'draft') === 'draft';
        $htmlFields = '';

        foreach ($fields as $f) {
            $key         = $f['NamaField'];
            $val         = $values[$key] ?? '';
            $isHtmlField = in_array($key, ['LOGO_LEMBAGA', 'TTD_1_QR', 'TTD_2_QR', 'TTD_3_QR'], true);
            $content     = $isHtmlField ? $val : esc($val);

            $xMm = round((float) $f['PosisiX'] * $scaleX, 2);
            $yMm = round((float) $f['PosisiY'] * $scaleY, 2);
            $wMm = round((float) $f['Lebar'] * $scaleX, 2);
            $hMm = round((float) $f['Tinggi'] * $scaleY, 2);
            $pt  = round((float) $f['FontSize'] * 0.75, 1);

            if ($key === 'LOGO_LEMBAGA' && !empty($val)) {
                $content = preg_replace('/style="[^"]*"/', 'style="width:' . $wMm . 'mm;height:' . $hMm . 'mm;object-fit:contain;"', $val);
            } elseif (in_array($key, ['TTD_1_QR', 'TTD_2_QR', 'TTD_3_QR'], true) && strpos($val, '<img') !== false) {
                $content = preg_replace('/style="[^"]*"/', 'style="width:' . $wMm . 'mm;height:' . $hMm . 'mm;"', $val);
            }

            $htmlFields .= sprintf(
                '<div style="position:fixed;left:%smm;top:%smm;width:%smm;height:%smm;margin:0;padding:0;font-size:%spt;font-family:%s,sans-serif;font-weight:%s;color:%s;text-align:%s;line-height:1.2;">%s</div>',
                $xMm,
                $yMm,
                $wMm,
                $hMm,
                $pt,
                esc($f['FontFamily']),
                !empty($f['FontBold']) ? 'bold' : 'normal',
                esc($f['FontColor']),
                esc($f['TextAlign']),
                $content
            );
        }

        $watermark = $isDraft
            ? sprintf(
                '<div style="position:fixed;left:0mm;top:%smm;width:%smm;text-align:center;font-size:64pt;font-weight:bold;color:rgba(220,38,38,0.18);">DRAFT</div>',
                round($pageHmm * 0.42, 1),
                $pageWmm
            )
            : '';

        return "<style>@page { margin: 0; {$bgCss} }</style>{$htmlFields}{$watermark}";
    }
}
