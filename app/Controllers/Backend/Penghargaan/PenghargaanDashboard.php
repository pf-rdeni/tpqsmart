<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;
use App\Models\Backend\Penghargaan\PenghargaanJenjangModel;
use App\Models\Backend\Penghargaan\PenghargaanPenerimaModel;
use App\Models\Backend\Penghargaan\PenghargaanSertifikatModel;
use App\Models\Backend\Penghargaan\PenghargaanSettingModel;
use App\Models\Backend\Penghargaan\PenghargaanTemplateModel;

/**
 * Controller Dashboard & Setting Global Modul Penghargaan.
 */
class PenghargaanDashboard extends BaseController
{
    protected $jenisModel;
    protected $jenjangModel;
    protected $sertifikatModel;
    protected $penerimaModel;
    protected $settingModel;
    protected $templateModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->jenisModel      = new PenghargaanJenisModel();
        $this->jenjangModel    = new PenghargaanJenjangModel();
        $this->sertifikatModel = new PenghargaanSertifikatModel();
        $this->penerimaModel   = new PenghargaanPenerimaModel();
        $this->settingModel    = new PenghargaanSettingModel();
        $this->templateModel   = new PenghargaanTemplateModel();
        $this->helpFunction    = new HelpFunctionModel();
    }

    /**
     * Halaman Dashboard utama modul Penghargaan.
     */
    public function index()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idTahunAjaran = $this->request->getGet('tahun_ajaran') ?: (session()->get('IdTahunAjaran') ?: $this->helpFunction->getTahunAjaranSaatIni());
        $semester      = $this->request->getGet('semester') ?: '';
        $idJenis       = $this->request->getGet('jenis') ?: '';
        $roleCtx       = \App\Helpers\PenghargaanHelper::getRoleContext($idTahunAjaran);

        $setting    = $this->settingModel->getOrCreateByTpq($idTpq);
        $listJenis  = $this->jenisModel->getAvailableByTpq($idTpq, true);
        $listJenjang = $this->jenjangModel->getWithKelasByTpq($idTpq, true);
        $templates  = $this->templateModel->getAvailableByTpq($idTpq, null, 'aktif');

        $filters = [
            'IdTahunAjaran' => $idTahunAjaran,
            'Semester'      => $semester,
            'IdJenis'       => $idJenis,
        ];
        if ($roleCtx['isGuruOnly']) {
            $filters['IdKelasList'] = $roleCtx['guruKelasIds'];
        }
        $sertifikatTerbaru = $this->sertifikatModel->getListByTpq($idTpq, $filters);

        $totalPenerima = 0;
        $totalFinal    = 0;
        $totalDraft    = 0;
        foreach ($sertifikatTerbaru as $row) {
            $totalPenerima += (int) ($row['TotalPenerima'] ?? 0);
            if (($row['Status'] ?? '') === 'final') {
                $totalFinal++;
            } elseif (($row['Status'] ?? '') === 'draft') {
                $totalDraft++;
            }
        }

        // Ambil daftar tahun ajaran unik dari tbl_kelas_santri
        $db = \Config\Database::connect();
        $tahunAjaranList = $db->table('tbl_kelas_santri')
            ->select('IdTahunAjaran')
            ->distinct()
            ->orderBy('IdTahunAjaran', 'DESC')
            ->get()
            ->getResultArray();

        return view('backend/penghargaan/dashboard/index', [
            'page_title'        => 'Dashboard Sertifikat & Penghargaan',
            'menu_open'         => 'penghargaan',
            'menu_active'       => 'penghargaan-dashboard',
            'roleCtx'           => $roleCtx,
            'idTpq'             => $idTpq,
            'idTahunAjaran'     => $idTahunAjaran,
            'semester'          => $semester,
            'idJenis'           => $idJenis,
            'setting'           => $setting,
            'listJenis'         => $listJenis,
            'listJenjang'       => $listJenjang,
            'templates'         => $templates,
            'sertifikatTerbaru' => array_slice($sertifikatTerbaru, 0, 10),
            'tahunAjaranList'   => $tahunAjaranList,
            'stats'             => [
                'totalJenis'      => count($listJenis),
                'totalSertifikat' => count($sertifikatTerbaru),
                'totalPenerima'   => $totalPenerima,
                'totalFinal'      => $totalFinal,
                'totalDraft'      => $totalDraft,
                'totalTemplate'   => count($templates),
            ],
        ]);
    }

    /**
     * Halaman Pengaturan Global Modul Penghargaan per Lembaga.
     */
    public function setting()
    {
        $roleCtx = \App\Helpers\PenghargaanHelper::getRoleContext();
        if ($roleCtx['isGuruOnly']) {
            return redirect()->to(base_url('backend/penghargaan/dashboard'))
                ->with('error', 'Akses ditolak: Pengaturan global hanya untuk Operator/Admin.');
        }

        $idTpq   = (string) (session()->get('IdTpq') ?? '0');
        $setting = $this->settingModel->getOrCreateByTpq($idTpq);

        return view('backend/penghargaan/setting/index', [
            'page_title'  => 'Pengaturan Global Penghargaan',
            'menu_open'   => 'penghargaan',
            'menu_active' => 'penghargaan-setting',
            'setting'     => $setting,
        ]);
    }

    /**
     * Simpan perubahan Pengaturan Global.
     */
    public function saveSetting()
    {
        $roleCtx = \App\Helpers\PenghargaanHelper::getRoleContext();
        if ($roleCtx['isGuruOnly']) {
            return redirect()->to(base_url('backend/penghargaan/dashboard'))
                ->with('error', 'Akses ditolak: Peran Guru bersifat read-only.');
        }

        $idTpq   = (string) (session()->get('IdTpq') ?? '0');
        $setting = $this->settingModel->getOrCreateByTpq($idTpq);

        $bobotNilai = (int) $this->request->getPost('BobotNilaiDefault');
        $bobotHadir = (int) $this->request->getPost('BobotHadirDefault');

        if ($bobotNilai + $bobotHadir !== 100) {
            return redirect()->back()->withInput()->with('error', 'Total Bobot Nilai dan Bobot Kehadiran harus sama dengan 100%.');
        }

        $data = [
            'BobotNilaiDefault'      => $bobotNilai,
            'BobotHadirDefault'      => $bobotHadir,
            'ThresholdHadirSempurna' => max(1, min(100, (int) $this->request->getPost('ThresholdHadirSempurna'))),
            'JumlahPemenangDefault'  => max(0, (int) $this->request->getPost('JumlahPemenangDefault')),
            'FormatNomorSeri'        => trim((string) $this->request->getPost('FormatNomorSeri')) ?: 'TPQ/SNRT/{TAHUN}/{NO}',
            'WilayahSertifikat'      => trim((string) $this->request->getPost('WilayahSertifikat')) ?: null,
            'MomenAktif'             => $this->request->getPost('MomenAktif') ?: 'ganjil',
            'IsGroupByJenjang'       => $this->request->getPost('IsGroupByJenjang') ? 1 : 0,
        ];

        $this->settingModel->update($setting['Id'], $data);

        return redirect()->to(base_url('backend/penghargaan/setting'))
            ->with('success', 'Pengaturan global penghargaan berhasil disimpan.');
    }

    /**
     * Placeholder sementara untuk modul Phase 3-7 yang belum diimplementasikan.
     */
    public function comingSoon(string $modul = 'Fitur Penghargaan', string $phase = 'berikutnya')
    {
        return view('backend/penghargaan/coming_soon', [
            'page_title'  => $modul,
            'modul'       => $modul,
            'phase'       => $phase,
            'menu_open'   => 'penghargaan',
            'menu_active' => '',
        ]);
    }
}
