<?php

if (!function_exists('generateUniqueSignatureToken')) {
    /**
     * Generate unique token untuk signature
     * 
     * @param \App\Models\SignatureModel $signatureModel
     * @return string
     */
    function generateUniqueSignatureToken($signatureModel)
    {
        do {
            $token = base64_encode(random_bytes(24));
            $token = str_replace(['+', '/', '='], ['-', '_', ''], $token); // URL-safe

        } while ($signatureModel->where('Token', $token)->first());

        return $token;
    }
}

if (!function_exists('generateSignatureQRCode')) {
    /**
     * Generate QR Code untuk validasi tanda tangan signature
     * 
     * @param string $token
     * @return array|false Array dengan 'filename' dan 'url', atau false jika gagal
     */
    function generateSignatureQRCode($token)
    {
        try {
            // URL untuk validasi tanda tangan
            $validationUrl = base_url("signature/validateSignature/{$token}");

            // Buat direktori jika belum ada
            if (!is_dir(FCPATH . 'uploads/qr')) {
                mkdir(FCPATH . 'uploads/qr', 0777, true);
            }

            // Generate QR Code
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
                'scale' => 300,
                'imageBase64' => false,
                'addQuietzone' => true,
                'quietzoneSize' => 4,
            ]);

            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrString = $qrcode->render($validationUrl);

            // Simpan QR code sebagai file SVG
            $filename = 'signature_' . $token . '.svg';
            file_put_contents(FCPATH . 'uploads/qr/' . $filename, $qrString);

            return [
                'filename' => $filename,
                'url' => $validationUrl
            ];
        } catch (\Exception $e) {
            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
            return false;
        }
    }
}

