<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Helpers\PenghargaanHelper;
use App\Models\HelpFunctionModel;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;
use App\Models\Backend\Penghargaan\PenghargaanJenjangModel;
use App\Models\Backend\Penghargaan\PenghargaanKriteriaModel;
use App\Models\Backend\Penghargaan\PenghargaanPenerimaModel;
use App\Models\Backend\Penghargaan\PenghargaanSertifikatModel;
use App\Models\Backend\Penghargaan\PenghargaanSettingModel;

/**
 * Controller Generate Penghargaan Otomatis & Konfirmasi Penerima.
 */
class PenghargaanGenerate extends BaseController
{
    protected $jenisModel;
    protected $kriteriaModel;
    protected $jenjangModel;
    protected $settingModel;
    protected $sertifikatModel;
    protected $penerimaModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->jenisModel      = new PenghargaanJenisModel();
        $this->kriteriaModel   = new PenghargaanKriteriaModel();
        $this->jenjangModel    = new PenghargaanJenjangModel();
        $this->settingModel    = new PenghargaanSettingModel();
        $this->sertifikatModel = new PenghargaanSertifikatModel();
        $this->penerimaModel   = new PenghargaanPenerimaModel();
        $this->helpFunction    = new HelpFunctionModel();
    }

    /**
     * Form pilih parameter Generate Penghargaan.
     */
    public function index()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idTahunAjaran = session()->get('IdTahunAjaran') ?: $this->helpFunction->getTahunAjaranSaatIni();
        $roleCtx       = PenghargaanHelper::getRoleContext($idTahunAjaran);

        if ($roleCtx['isGuruOnly'] && !$roleCtx['isWaliKelas']) {
            return redirect()->to(base_url('backend/penghargaan/dashboard'))
                ->with('error', 'Hanya Wali Kelas atau Operator/Admin yang diizinkan melakukan generate penghargaan.');
        }

        $setting       = $this->settingModel->getOrCreateByTpq($idTpq);
        $listJenis     = $this->jenisModel->getAvailableByTpq($idTpq, true);
        $listKelas     = $this->helpFunction->getDataKelas();
        $jenjangRows   = $this->jenjangModel->getWithKelasByTpq($idTpq, true);

        if ($roleCtx['isGuruOnly']) {
            $listKelas = array_values(array_filter($listKelas, static function ($k) use ($roleCtx) {
                return in_array((string) ($k['IdKelas'] ?? ''), $roleCtx['waliKelasIds'], true);
            }));
            $listJenis = array_values(array_filter($listJenis, static function ($j) {
                return !in_array($j['Kode'] ?? '', ['AKD-02', 'HAD-03', 'GAB-02', 'KHS-02'], true);
            }));
        }

        // Kelompokkan nama jenjang unik
        $listJenjang = [];
        foreach ($jenjangRows as $r) {
            $listJenjang[$r['NamaJenjang']] = $r['NamaJenjang'];
        }

        // Daftar tahun ajaran dari tbl_kelas_santri
        $db = \Config\Database::connect();
        $tahunAjaranList = $db->table('tbl_kelas_santri')
            ->select('IdTahunAjaran')
            ->distinct()
            ->orderBy('IdTahunAjaran', 'DESC')
            ->get()
            ->getResultArray();

        return view('backend/penghargaan/generate/form', [
            'page_title'      => 'Generate Penghargaan Otomatis',
            'roleCtx'         => $roleCtx,
            'idTahunAjaran'   => $idTahunAjaran,
            'setting'         => $setting,
            'listJenis'       => $listJenis,
            'listKelas'       => $listKelas,
            'listJenjang'     => array_values($listJenjang),
            'tahunAjaranList' => $tahunAjaranList,
        ]);
    }

    /**
     * Hitung kandidat penerima dan tampilkan halaman Preview Konfirmasi.
     */
    public function preview()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idJenis       = (int) $this->request->getPost('IdJenis');
        $idTahunAjaran = trim((string) $this->request->getPost('IdTahunAjaran'));
        $semester      = trim((string) $this->request->getPost('Semester'));
        $momen         = trim((string) $this->request->getPost('Momen'));
        $scope         = trim((string) $this->request->getPost('Scope')); // 'kelas' | 'jenjang' | 'semua'
        $idKelas       = $scope === 'kelas' ? trim((string) $this->request->getPost('IdKelas')) : null;
        $namaJenjang   = $scope === 'jenjang' ? trim((string) $this->request->getPost('NamaJenjang')) : null;

        $roleCtx = PenghargaanHelper::getRoleContext($idTahunAjaran);
        if ($roleCtx['isGuruOnly']) {
            if (!$roleCtx['isWaliKelas']) {
                return redirect()->to(base_url('backend/penghargaan/dashboard'))
                    ->with('error', 'Akses ditolak: Hanya Wali Kelas yang dapat men-generate penghargaan kelas.');
            }
            $scope       = 'kelas';
            $namaJenjang = null;
            $idKelas     = trim((string) $this->request->getPost('IdKelas'));
            if (!in_array((string) $idKelas, $roleCtx['waliKelasIds'], true)) {
                return redirect()->back()->with('error', 'Anda hanya diizinkan men-generate penghargaan untuk kelas wali Anda sendiri.');
            }
        }

        $jenis = $this->jenisModel->find($idJenis);
        if (!$jenis) {
            return redirect()->back()->with('error', 'Jenis penghargaan tidak ditemukan.');
        }

        if ($scope === 'kelas' && empty($idKelas)) {
            return redirect()->back()->with('error', 'Silakan pilih kelas terlebih dahulu.');
        }
        if ($scope === 'jenjang' && empty($namaJenjang)) {
            return redirect()->back()->with('error', 'Silakan pilih jenjang terlebih dahulu.');
        }

        $setting  = $this->settingModel->getOrCreateByTpq($idTpq);
        $kriteria = $this->kriteriaModel->getByJenisAndTpq($idJenis, $idTpq, $idTahunAjaran);

        $kandidat = PenghargaanHelper::kalkulasiKandidat(
            $jenis,
            $kriteria,
            $setting,
            $idTpq,
            $idTahunAjaran,
            $semester,
            $idKelas,
            $namaJenjang
        );

        return view('backend/penghargaan/generate/preview_penerima', [
            'page_title'    => 'Preview Kandidat Penerima Penghargaan',
            'jenis'         => $jenis,
            'setting'       => $setting,
            'idTahunAjaran' => $idTahunAjaran,
            'semester'      => $semester,
            'momen'         => $momen,
            'scope'         => $scope,
            'idKelas'       => $idKelas,
            'namaJenjang'   => $namaJenjang,
            'kandidat'      => $kandidat,
        ]);
    }

    /**
     * Simpan batch sertifikat (status: draft) dan daftar penerima terpilih.
     */
    public function store()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idJenis       = (int) $this->request->getPost('IdJenis');
        $idTahunAjaran = trim((string) $this->request->getPost('IdTahunAjaran'));
        $semester          = trim((string) $this->request->getPost('Semester'));
        $momen             = trim((string) $this->request->getPost('Momen'));
        $tanggalSertifikat = trim((string) $this->request->getPost('TanggalSertifikat')) ?: date('Y-m-d');
        $selected          = $this->request->getPost('selected');
        $rows          = $this->request->getPost('rows');

        $roleCtx = PenghargaanHelper::getRoleContext($idTahunAjaran);
        if ($roleCtx['isGuruOnly']) {
            if (!$roleCtx['isWaliKelas']) {
                return redirect()->to(base_url('backend/penghargaan/dashboard'))
                    ->with('error', 'Akses ditolak: Hanya Wali Kelas yang diizinkan menyimpan generate penghargaan.');
            }
            if (is_array($selected) && is_array($rows)) {
                foreach ($selected as $idx) {
                    $kls = (string) ($rows[$idx]['IdKelas'] ?? '');
                    if (!in_array($kls, $roleCtx['waliKelasIds'], true)) {
                        return redirect()->to(base_url('backend/penghargaan/generate'))
                            ->with('error', 'Terdapat santri di luar kelas wali Anda.');
                    }
                }
            }
        }

        if (!$idJenis || empty($selected) || !is_array($selected) || empty($rows)) {
            return redirect()->to(base_url('backend/penghargaan/generate'))
                ->with('error', 'Minimal 1 santri penerima harus dipilih untuk disimpan.');
        }

        $setting = $this->settingModel->getOrCreateByTpq($idTpq);
        $format  = $setting['FormatNomorSeri'] ?: 'TPQ/SNRT/{TAHUN}/{NO}';
        $userId  = function_exists('user_id') ? user_id() : null;

        $db = \Config\Database::connect();
        $db->transStart();

        $idSertifikat = $this->sertifikatModel->insert([
            'IdTpq'             => $idTpq,
            'IdJenis'           => $idJenis,
            'IdTemplate'        => null,
            'IdTahunAjaran'     => $idTahunAjaran,
            'Semester'          => $semester,
            'Momen'             => $momen ?: 'ganjil',
            'TanggalSertifikat' => $tanggalSertifikat,
            'TanggalGenerate'   => date('Y-m-d H:i:s'),
            'GeneratedBy'       => $userId,
            'Status'            => 'draft',
        ], true);

        // Hitung nomor urut terakhir agar nomor seri tidak bentrok
        $lastCount = $this->penerimaModel->where('IdTpq', $idTpq)->countAllResults();
        $urutan    = $lastCount + 1;

        foreach ($selected as $idx) {
            if (!isset($rows[$idx])) {
                continue;
            }
            $r         = $rows[$idx];
            $nomorSeri = PenghargaanHelper::generateNomorSeri($format, 'TPQ' . $idTpq, $semester, $urutan++) . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
            $token     = bin2hex(random_bytes(5));

            $this->penerimaModel->insert([
                'IdTpq'           => $idTpq,
                'IdSertifikat'    => $idSertifikat,
                'IdSantri'        => (string) ($r['IdSantri'] ?? ''),
                'IdKelas'         => (string) ($r['IdKelas'] ?? ''),
                'IdJenjang'       => null,
                'Peringkat'       => (int) ($r['Peringkat'] ?? 0),
                'NilaiRataRata'   => (float) ($r['NilaiRataRata'] ?? 0),
                'PersenKehadiran' => (float) ($r['PersenKehadiran'] ?? 0),
                'SkorGabungan'    => (float) ($r['SkorGabungan'] ?? 0),
                'CatatanKhusus'   => trim((string) ($r['CatatanKhusus'] ?? '')),
                'NomorSeri'       => $nomorSeri,
                'TokenVerifikasi' => $token,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(base_url('backend/penghargaan/generate'))
                ->with('error', 'Terjadi kesalahan saat menyimpan data penerima penghargaan.');
        }

        return redirect()->to(base_url('backend/penghargaan/dashboard'))
            ->with('success', 'Batch penghargaan berhasil disimpan sebagai Draft (' . count($selected) . ' penerima).');
    }
}
