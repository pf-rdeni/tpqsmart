<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratNomorModel;
use App\Models\Administrasi\SuratKategoriModel;
use App\Models\HelpFunctionModel;

class SuratPenomoran extends BaseController
{
    protected $nomorModel;
    protected $kategoriModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->nomorModel    = new SuratNomorModel();
        $this->kategoriModel = new SuratKategoriModel();
        $this->helpFunction  = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $penomoran = $this->nomorModel->getAllWithDetails($isAdmin ? null : $idTpq);
        $kategori  = $this->kategoriModel->getActive();
        $listTpq   = $this->helpFunction->getDataTpq(false);

        $data = [
            'page_title' => 'Pengaturan Penomoran Surat',
            'penomoran'  => $penomoran,
            'kategori'   => $kategori,
            'listTpq'    => $listTpq,
            'idTpq'      => $idTpq,
            'isAdmin'    => $isAdmin,
        ];

        return view('backend/administrasi/penomoran/index', $data);
    }

    public function save()
    {
        $idTpq   = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

        $rules = [
            'IdKategori'     => 'required|numeric',
            'Tahun'          => 'required|numeric',
            'UrutanTerakhir' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $targetTpq = $isAdmin ? ($this->request->getPost('IdTpq') ?: $idTpq) : $idTpq;
        $idKategori = $this->request->getPost('IdKategori');
        $tahun = $this->request->getPost('Tahun');
        $bulan = $this->request->getPost('Bulan') ?: null;
        $urutan = (int)$this->request->getPost('UrutanTerakhir');

        // Check if existing
        $builder = $this->nomorModel->where('IdTpq', $targetTpq)
                                    ->where('IdKategori', $idKategori)
                                    ->where('Tahun', $tahun);
        if ($bulan !== null) {
            $builder->where('Bulan', $bulan);
        } else {
            $builder->where('Bulan IS NULL', null, false);
        }
        $existing = $builder->first();

        if ($existing) {
            $this->nomorModel->update($existing['Id'], ['UrutanTerakhir' => $urutan]);
        } else {
            $this->nomorModel->insert([
                'IdTpq'          => $targetTpq,
                'IdKategori'     => $idKategori,
                'Tahun'          => $tahun,
                'Bulan'          => $bulan,
                'UrutanTerakhir' => $urutan,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengaturan counter penomoran berhasil disimpan'
        ]);
    }

    public function resetCounter($id = null)
    {
        if ($this->nomorModel->resetCounter($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Counter nomor berhasil di-reset ke 0'
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mereset counter'
        ]);
    }

    public function previewNomor()
    {
        $format  = $this->request->getPost('format') ?: '[NO:3]/[KODE-TPQ]/[BLN-ROMAWI]/[TAHUN]';
        $urutan  = (int)($this->request->getPost('urutan') ?: 1);
        $kodeTpq = $this->request->getPost('kode_tpq') ?: 'TPQ-01';
        $kodeKat = $this->request->getPost('kode_kat') ?: 'UND';
        $bulan   = (int)($this->request->getPost('bulan') ?: date('n'));
        $tahun   = (int)($this->request->getPost('tahun') ?: date('Y'));

        $romawiMap = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ];
        $bulanRomawi = $romawiMap[$bulan] ?? 'I';

        $output = $format;
        // Token [NO:x]
        if (preg_match('/\[NO:(\d+)\]/', $output, $matches)) {
            $padLen = (int)$matches[1];
            $output = str_replace($matches[0], str_pad($urutan, $padLen, '0', STR_PAD_LEFT), $output);
        } else {
            $output = str_replace('[NO]', str_pad($urutan, 3, '0', STR_PAD_LEFT), $output);
        }

        $output = str_replace('[KODE-TPQ]', $kodeTpq, $output);
        $output = str_replace('[KODE-KAT]', $kodeKat, $output);
        $output = str_replace('[BLN-ROMAWI]', $bulanRomawi, $output);
        $output = str_replace('[BLN]', str_pad($bulan, 2, '0', STR_PAD_LEFT), $output);
        $output = str_replace('[TAHUN]', $tahun, $output);
        $output = str_replace('[TAHUN-PENDEK]', substr($tahun, -2), $output);

        return $this->response->setJSON([
            'success' => true,
            'preview' => $output
        ]);
    }
}
