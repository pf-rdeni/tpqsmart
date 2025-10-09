<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\SignatureModel;

class Signature extends BaseController
{
    protected $signatureModel;

    public function __construct()
    {
        $this->signatureModel = new SignatureModel();
    }

    /**
     * Validasi tanda tangan berdasarkan token
     */
    public function validateSignature($token)
    {
        try {
            // Cari data tanda tangan berdasarkan token
            $signature = $this->signatureModel->where('Token', $token)->first();

            if (!$signature) {
                return view('frontend/signature/invalid', [
                    'message' => 'Token tidak valid atau tidak ditemukan.'
                ]);
            }

            // Cek status validasi
            if ($signature['StatusValidasi'] !== 'Valid') {
                return view('frontend/signature/invalid', [
                    'message' => 'Tanda tangan tidak valid atau telah dibatalkan.'
                ]);
            }

            // Tampilkan halaman validasi yang berhasil
            return view('frontend/signature/valid', [
                'signature' => $signature,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Signature validation failed: ' . $e->getMessage());
            return view('frontend/signature/invalid', [
                'message' => 'Terjadi kesalahan dalam validasi tanda tangan.'
            ]);
        }
    }
}
