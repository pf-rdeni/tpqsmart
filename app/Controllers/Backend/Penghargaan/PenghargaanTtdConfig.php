<?php

namespace App\Controllers\Backend\Penghargaan;

use App\Controllers\BaseController;
use App\Models\Backend\Penghargaan\PenghargaanJenisModel;
use App\Models\Backend\Penghargaan\PenghargaanTtdConfigModel;
use Config\Database;

/**
 * Controller Setting Penandatangan (TTD QR / Kosong / Upload) per Jenis Penghargaan.
 */
class PenghargaanTtdConfig extends BaseController
{
    protected $ttdConfigModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->ttdConfigModel = new PenghargaanTtdConfigModel();
        $this->jenisModel     = new PenghargaanJenisModel();
    }

    /**
     * Daftar jenis penghargaan beserta ringkasan konfigurasi penandatangannya.
     */
    public function index()
    {
        $idTpq     = (string) (session()->get('IdTpq') ?? '0');
        $listJenis = $this->jenisModel->getAvailableByTpq($idTpq, true);

        $configMap = [];
        foreach ($listJenis as $j) {
            $configMap[$j['Id']] = $this->ttdConfigModel->getByJenisAndTpq((int) $j['Id'], $idTpq, true);
        }

        return view('backend/penghargaan/ttd_config/index', [
            'page_title' => 'Setting TTD Penandatangan Sertifikat',
            'listJenis'  => $listJenis,
            'configMap'  => $configMap,
        ]);
    }

    /**
     * Form konfigurasi 1-3 slot penandatangan untuk jenis penghargaan tertentu.
     */
    public function form(int $idJenis)
    {
        $idTpq = (string) (session()->get('IdTpq') ?? '0');
        $jenis = $this->jenisModel->find($idJenis);

        if (!$jenis) {
            return redirect()->to(base_url('backend/penghargaan/ttd-config'))
                ->with('error', 'Jenis penghargaan tidak ditemukan.');
        }

        $configs = $this->ttdConfigModel->getByJenisAndTpq($idJenis, $idTpq, false);
        $slots   = [];
        foreach ($configs as $c) {
            $slots[(int) $c['UrutanTtd']] = $c;
        }

        $db          = Database::connect();
        $listJabatan = $db->table('tbl_jabatan')
            ->orderBy('NamaJabatan', 'ASC')
            ->get()
            ->getResultArray();

        return view('backend/penghargaan/ttd_config/form', [
            'page_title'  => 'Atur Penandatangan: ' . $jenis['NamaPenghargaan'],
            'jenis'       => $jenis,
            'slots'       => $slots,
            'listJabatan' => $listJabatan,
        ]);
    }

    /**
     * Simpan konfigurasi 1-3 slot penandatangan untuk jenis penghargaan tertentu.
     */
    public function save(int $idJenis)
    {
        $idTpq = (string) (session()->get('IdTpq') ?? '0');
        $jenis = $this->jenisModel->find($idJenis);

        if (!$jenis) {
            return redirect()->to(base_url('backend/penghargaan/ttd-config'))
                ->with('error', 'Jenis penghargaan tidak ditemukan.');
        }

        $uploadDir = FCPATH . 'uploads/penghargaan/ttd/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        for ($urutan = 1; $urutan <= 3; $urutan++) {
            $isActive      = $this->request->getPost("slot_{$urutan}_active") ? 1 : 0;
            $sumberTtd     = (string) ($this->request->getPost("slot_{$urutan}_sumber") ?: 'jabatan_lembaga');
            $idJabatan     = $this->request->getPost("slot_{$urutan}_jabatan") ?: null;
            $namaManual    = trim((string) $this->request->getPost("slot_{$urutan}_nama_manual"));
            $jabatanManual = trim((string) $this->request->getPost("slot_{$urutan}_jabatan_manual"));
            $labelTtd      = trim((string) $this->request->getPost("slot_{$urutan}_label"));
            $modeTtd       = (string) ($this->request->getPost("slot_{$urutan}_mode") ?: 'qr');

            $existing = $this->ttdConfigModel
                ->where('IdTpq', $idTpq)
                ->where('IdJenisPenghargaan', $idJenis)
                ->where('UrutanTtd', $urutan)
                ->first();

            $fileTtd = $existing['FileTtd'] ?? null;
            $file    = $this->request->getFile("slot_{$urutan}_file");
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
                    $newName = 'ttd_' . $idTpq . '_' . $idJenis . '_' . $urutan . '_' . time() . '.' . $ext;
                    $file->move($uploadDir, $newName);
                    $fileTtd = 'uploads/penghargaan/ttd/' . $newName;
                }
            }

            $payload = [
                'IdTpq'              => $idTpq,
                'IdJenisPenghargaan' => $idJenis,
                'UrutanTtd'          => $urutan,
                'SumberTtd'          => $sumberTtd,
                'IdJabatan'          => $sumberTtd !== 'manual' ? ($idJabatan ? (int) $idJabatan : null) : null,
                'NamaManual'         => $sumberTtd === 'manual' ? $namaManual : null,
                'JabatanManual'      => $sumberTtd === 'manual' ? $jabatanManual : null,
                'LabelTtd'           => $labelTtd ?: ($sumberTtd === 'manual' ? ($jabatanManual ?: 'Mengetahui') : 'Penandatangan ' . $urutan),
                'ModeTtd'            => $modeTtd,
                'FileTtd'            => $fileTtd,
                'IsActive'           => $isActive,
            ];

            if ($existing) {
                $this->ttdConfigModel->update($existing['Id'], $payload);
            } elseif ($isActive) {
                $this->ttdConfigModel->insert($payload);
            }
        }

        return redirect()->to(base_url('backend/penghargaan/ttd-config'))
            ->with('success', 'Konfigurasi penandatangan untuk "' . $jenis['NamaPenghargaan'] . '" berhasil disimpan.');
    }
}
