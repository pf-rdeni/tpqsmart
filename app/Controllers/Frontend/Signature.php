<?php

namespace App\Controllers\Frontend;

use App\Models\SignatureModel;
use App\Models\SantriModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\TpqModel;
use App\Models\HelpFunctionModel;

class Signature extends \App\Controllers\BaseController
{
    protected $signatureModel;
    protected $santriModel;
    protected $guruModel;
    protected $kelasModel;
    protected $tpqModel;
    protected $helpfunctionModel;

    public function __construct()
    {
        $this->signatureModel = new SignatureModel();
        $this->santriModel = new SantriModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->tpqModel = new TpqModel();
        $this->helpfunctionModel = new HelpFunctionModel();
    }

    public function validateSignature($token)
    {
        $signature = $this->signatureModel->validateSignature($token);

        if (!$signature) {
            return view('frontend/signature/invalid', [
                'message' => 'Tanda tangan tidak valid atau telah kadaluarsa'
            ]);
        }

        // Handle signature untuk Surat Rekomendasi (menggunakan IdGuru)
        if ($signature['JenisDokumen'] === 'Surat Rekomendasi' && $signature['SignatureData'] === 'Ketua FKPQ') {
            // Ambil data guru
            $guru = null;
            if (!empty($signature['IdGuru'])) {
                $guru = $this->guruModel->find($signature['IdGuru']);
            }

            // Ambil data TPQ
            $tpq = null;
            if (!empty($signature['IdTpq'])) {
                $tpq = $this->tpqModel->find($signature['IdTpq']);
            }

            // Ambil data FKPQ
            $fkpqModel = new \App\Models\FkpqModel();
            $fkpqData = $fkpqModel->GetData();
            $fkpq = !empty($fkpqData) ? $fkpqData[0] : null;

            return view('frontend/signature/valid', [
                'signature' => $signature,
                'santri' => null,
                'guru' => $guru,
                'kelas' => null,
                'tpq' => $tpq,
                'fkpq' => $fkpq,
                'pesertaMunaqosah' => null
            ]);
        }

        // Ambil data santri hanya jika IdSantri ada
        $santri = null;
        if (!empty($signature['IdSantri'])) {
            $santri = $this->santriModel->where('IdSantri', $signature['IdSantri'])->first();
        }

        // Handle signature untuk Munaqosah (Ketua FKPQ)
        if ($signature['JenisDokumen'] === 'Munaqosah' && $signature['SignatureData'] === 'Ketua FKPQ') {
            $tpq = $this->tpqModel->find($signature['IdTpq']);

            // Ambil data FKPQ
            $fkpqModel = new \App\Models\FkpqModel();
            $fkpqData = $fkpqModel->GetData();
            $fkpq = !empty($fkpqData) ? $fkpqData[0] : null;

            // Ambil data peserta Munaqosah
            $munaqosahPesertaModel = new \App\Models\MunaqosahPesertaModel();
            $pesertaMunaqosah = $munaqosahPesertaModel
                ->where('IdSantri', $signature['IdSantri'])
                ->where('IdTahunAjaran', $signature['IdTahunAjaran'])
                ->first();

            return view('frontend/signature/valid', [
                'signature' => $signature,
                'santri' => $santri,
                'guru' => null,
                'kelas' => null,
                'tpq' => $tpq,
                'fkpq' => $fkpq,
                'pesertaMunaqosah' => $pesertaMunaqosah
            ]);
        }

        // Handle signature untuk Rapor (logic lama)
        // Ambil data guru kelas
        $guruKelas = [];
        if (!empty($signature['IdGuru']) && !empty($signature['IdKelas'])) {
            $guruKelas = $this->helpfunctionModel->getDataGuruKelas($signature['IdGuru'], $signature['IdTpq'], $signature['IdKelas']);
        }

        // Ambil data kepala TPQ
        $kepalaTpq = [];
        if (!empty($signature['IdGuru'])) {
            $kepalaTpq = $this->helpfunctionModel->getDataKepalaTpqStrukturLembaga($signature['IdGuru'], $signature['IdTpq']);
        }

        // Gabungkan data guru
        $guru = array_merge($guruKelas, $kepalaTpq);

        // Ambil data guru pertama yang ditemukan (bisa guru kelas atau kepala TPQ)
        $guruData = !empty($guru) ? $guru[0] : null;

        $kelas = null;
        if (!empty($signature['IdKelas'])) {
            $kelas = $this->kelasModel->find($signature['IdKelas']);
            if ($kelas) {
                $namaKelas = $this->helpfunctionModel->getNamaKelas($signature['IdKelas']);
                $kelas['NamaKelas'] = $namaKelas;
            }
        }
        $tpq = $this->tpqModel->find($signature['IdTpq']);

        return view('frontend/signature/valid', [
            'signature' => $signature,
            'santri' => $santri,
            'guru' => $guruData,
            'kelas' => $kelas,
            'tpq' => $tpq
        ]);
    }

    public function getSignaturesBySantri($idSantri)
    {
        $signatures = $this->signatureModel->getSignaturesBySantri($idSantri);
        return $this->response->setJSON($signatures);
    }

    public function getSignaturesByGuru($idGuru)
    {
        $signatures = $this->signatureModel->getSignaturesByGuru($idGuru);
        return $this->response->setJSON($signatures);
    }

    public function getSignaturesByTpq($idTpq)
    {
        $signatures = $this->signatureModel->getSignaturesByTpq($idTpq);
        return $this->response->setJSON($signatures);
    }
}
