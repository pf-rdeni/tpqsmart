<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Models\HelpFunctionModel;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;
use App\Models\Backend\Penghargaan\PenghargaanKriteriaModel;
use App\Models\Backend\Penghargaan\PenghargaanSettingModel;

/**
 * Controller Kelola Kriteria & Threshold per Jenis Penghargaan.
 */
class PenghargaanKriteria extends BaseController
{
    protected $kriteriaModel;
    protected $jenisModel;
    protected $settingModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->kriteriaModel = new PenghargaanKriteriaModel();
        $this->jenisModel    = new PenghargaanJenisModel();
        $this->settingModel  = new PenghargaanSettingModel();
        $this->helpFunction  = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idTahunAjaran = $this->request->getGet('tahun_ajaran') ?: (session()->get('IdTahunAjaran') ?: $this->helpFunction->getTahunAjaranSaatIni());

        $setting   = $this->settingModel->getOrCreateByTpq($idTpq);
        $listJenis = $this->jenisModel->getAvailableByTpq($idTpq, true);

        $kriteriaMap = [];
        foreach ($listJenis as $jenis) {
            $kriteriaMap[$jenis['Id']] = $this->kriteriaModel->getByJenisAndTpq((int) $jenis['Id'], $idTpq, $idTahunAjaran);
        }

        return view('backend/penghargaan/kriteria/index', [
            'page_title'    => 'Kriteria & Threshold Penghargaan',
            'menu_open'     => 'penghargaan',
            'menu_active'   => 'penghargaan-kriteria',
            'idTahunAjaran' => $idTahunAjaran,
            'setting'       => $setting,
            'listJenis'     => $listJenis,
            'kriteriaMap'   => $kriteriaMap,
        ]);
    }

    public function save()
    {
        $idTpq         = (string) (session()->get('IdTpq') ?? '0');
        $idJenis       = (int) $this->request->getPost('IdJenis');
        $idTahunAjaran = trim((string) $this->request->getPost('IdTahunAjaran')) ?: null;

        if (!$idJenis) {
            return redirect()->back()->with('error', 'Jenis penghargaan tidak valid.');
        }

        $bobotNilai = $this->request->getPost('BobotNilai');
        $bobotHadir = $this->request->getPost('BobotHadir');

        if ($bobotNilai !== '' && $bobotHadir !== '' && ((int) $bobotNilai + (int) $bobotHadir !== 100)) {
            return redirect()->back()->with('error', 'Jika mengisi bobot custom, total Bobot Nilai + Bobot Kehadiran harus 100%.');
        }

        $existing = $this->kriteriaModel
            ->where('IdTpq', $idTpq)
            ->where('IdJenis', $idJenis);

        if ($idTahunAjaran !== null) {
            $existing->where('IdTahunAjaran', $idTahunAjaran);
        } else {
            $existing->where('IdTahunAjaran IS NULL');
        }
        $row = $existing->first();

        $payload = [
            'IdTpq'          => $idTpq,
            'IdJenis'        => $idJenis,
            'IdTahunAjaran'  => $idTahunAjaran,
            'NilaiMin'       => $this->request->getPost('NilaiMin') !== '' ? (float) $this->request->getPost('NilaiMin') : null,
            'PersenHadirMin' => $this->request->getPost('PersenHadirMin') !== '' ? (int) $this->request->getPost('PersenHadirMin') : null,
            'BobotNilai'     => $bobotNilai !== '' ? (int) $bobotNilai : null,
            'BobotHadir'     => $bobotHadir !== '' ? (int) $bobotHadir : null,
            'JumlahPemenang' => $this->request->getPost('JumlahPemenang') !== '' ? (int) $this->request->getPost('JumlahPemenang') : null,
            'GroupByJenjang' => $this->request->getPost('GroupByJenjang') ? 1 : 0,
        ];

        if ($row) {
            $this->kriteriaModel->update($row['Id'], $payload);
        } else {
            $this->kriteriaModel->insert($payload);
        }

        return redirect()->to(base_url('backend/penghargaan/kriteria' . ($idTahunAjaran ? '?tahun_ajaran=' . urlencode($idTahunAjaran) : '')))
            ->with('success', 'Kriteria penghargaan berhasil disimpan.');
    }
}
