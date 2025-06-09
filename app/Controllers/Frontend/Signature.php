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

        $santri = $this->santriModel->where('IdSantri', $signature['IdSantri'])->first();
        $guru = $this->helpfunctionModel->getDataGuruKelas($signature['IdGuru'], $signature['IdTpq'], $signature['IdKelas'])[0];

        $kelas = $this->kelasModel->find($signature['IdKelas']);
        $namaKelas = $this->helpfunctionModel->getNamaKelas($signature['IdKelas']);
        $kelas['NamaKelas'] = $namaKelas;
        $tpq = $this->tpqModel->find($signature['IdTpq']);

        return view('frontend/signature/valid', [
            'signature' => $signature,
            'santri' => $santri,
            'guru' => $guru,
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
