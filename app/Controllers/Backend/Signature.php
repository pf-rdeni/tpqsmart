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

    /**
     * Verifikasi publik Sertifikat Penghargaan via QR Code.
     * URL: /signature/verifikasi/{token}
     */
    public function verifikasi($token = null)
    {
        try {
            if (empty($token)) {
                return view('frontend/signature/invalid', [
                    'type'    => 'danger',
                    'title'   => 'Token Tidak Valid',
                    'message' => 'Kode verifikasi tidak ditemukan.',
                ]);
            }

            $signature = $this->signatureModel
                ->where('Token', $token)
                ->where('JenisDokumen', 'Sertifikat')
                ->first();

            if (!$signature) {
                return view('frontend/signature/invalid', [
                    'type'    => 'danger',
                    'title'   => 'Sertifikat Tidak Terdaftar',
                    'message' => 'QR Code verifikasi sertifikat tidak terdaftar dalam sistem.',
                ]);
            }

            if (($signature['StatusValidasi'] ?? '') !== 'Valid') {
                return view('frontend/signature/invalid', [
                    'type'    => 'warning',
                    'title'   => 'Tanda Tangan Tidak Valid',
                    'message' => 'Tanda tangan digital sertifikat ini telah dibatalkan atau dicabut.',
                ]);
            }

            $sigMeta    = json_decode((string) ($signature['SignatureData'] ?? '{}'), true) ?: [];
            $idPenerima = (int) ($sigMeta['IdPenerima'] ?? 0);

            $db       = \Config\Database::connect();
            $penerima = $db->table('tbl_penghargaan_penerima p')
                ->select('p.*, s.IdTahunAjaran, s.Semester, s.Momen, s.Status as StatusSertifikat, s.TanggalRelease, j.Kode, j.NamaPenghargaan, sb.NamaSantri, sb.NISN, k.NamaKelas, t.NamaTpq')
                ->join('tbl_penghargaan_sertifikat s', 's.Id = p.IdSertifikat')
                ->join('tbl_penghargaan_jenis j', 'j.Id = s.IdJenis', 'left')
                ->join('tbl_santri_baru sb', 'sb.IdSantri = p.IdSantri', 'left')
                ->join('tbl_kelas k', 'k.IdKelas = p.IdKelas', 'left')
                ->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left')
                ->where('p.Id', $idPenerima)
                ->get()
                ->getRowArray();

            if (!$penerima || ($penerima['StatusSertifikat'] ?? '') === 'dibatal') {
                return view('frontend/signature/invalid', [
                    'type'    => 'warning',
                    'title'   => 'Sertifikat Dicabut',
                    'message' => 'Sertifikat penghargaan ini telah dibatalkan atau dicabut oleh lembaga.',
                ]);
            }

            return view('frontend/signature/valid_sertifikat', [
                'signature' => $signature,
                'sigMeta'   => $sigMeta,
                'penerima'  => $penerima,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Certificate verification failed: ' . $e->getMessage());
            return view('frontend/signature/invalid', [
                'message' => 'Terjadi kesalahan saat memverifikasi sertifikat.',
            ]);
        }
    }

    /**
     * Validasi Keaslian Surat via Token Unik 10 Karakter (Clean URL)
     * URL: /signature/v/{token10}
     */
    public function verifyToken($token = null)
    {
        try {
            if ($token === 'preview') {
                return view('frontend/signature/invalid', [
                    'type'    => 'info',
                    'title'   => 'SIMULASI PRATINJAU DRAF SURAT',
                    'message' => 'Dokumen ini masih dalam tahap penyusunan / pratinjau draf. Tanda tangan QR Code verifikasi resmi 10-karakter akan diaktifkan secara permanen setelah surat ini difinalisasi dan disimpan.'
                ]);
            }

            if (empty($token) || strlen($token) !== 10) {
                return view('frontend/signature/invalid', [
                    'type'    => 'danger',
                    'title'   => 'Kode Tidak Valid',
                    'message' => 'Format kode verifikasi tidak valid atau tidak terdaftar dalam sistem.'
                ]);
            }

            $tokenModel = new \App\Models\Administrasi\SuratSignerTokenModel();
            $tokenData  = $tokenModel->getDetailByToken($token);

            if (!$tokenData) {
                return view('frontend/signature/invalid', [
                    'type'    => 'danger',
                    'title'   => 'QR Code Tidak Terdaftar',
                    'message' => 'QR Code verifikasi tidak terdaftar atau tidak ditemukan dalam pangkalan data resmi.'
                ]);
            }

            // State 2: Jika surat ditarik kembali / direvokasi / status bukan Final
            if ((int)$tokenData['IsRevoked'] === 1 || ($tokenData['StatusSurat'] ?? '') !== 'Final') {
                $alasan = !empty($tokenData['RevokedReason']) ? $tokenData['RevokedReason'] : 'Dokumen telah ditarik kembali atau direvisi.';
                return view('frontend/signature/invalid', [
                    'type'    => 'warning',
                    'title'   => 'DOKUMEN TIDAK BERLAKU / DIREVISI',
                    'message' => 'PERINGATAN: Dokumen ini telah ditarik kembali atau direvisi oleh penerbit (' . esc($alasan) . '). Tanda tangan elektronik pada dokumen fisik ini tidak lagi berlaku secara hukum.'
                ]);
            }

            // State 3: Dokumen Valid Terverifikasi
            $surat = [
                'Id'                  => $tokenData['IdSurat'],
                'NomorSurat'          => $tokenData['NomorSurat'],
                'Hal'                 => $tokenData['Hal'],
                'Tanggal'             => $tokenData['Tanggal'],
                'NamaTpq'             => $tokenData['NamaTpq'] ?? 'Lembaga TPQ',
                'Status'              => $tokenData['StatusSurat'],
                'TempatTtd'           => $tokenData['TempatTtd'] ?? 'Bintan',
                'TanggalTtd'          => $tokenData['TanggalTtd'] ?? $tokenData['Tanggal'],
                'PenandaTanganConfig' => $tokenData['PenandaTanganConfig'] ?? null,
            ];

            // Parse daftar seluruh signer dari konfigurasi
            $allSigners = [];
            if (!empty($tokenData['PenandaTanganConfig'])) {
                $cfg = json_decode($tokenData['PenandaTanganConfig'], true);
                if (is_array($cfg) && !empty($cfg['signers'])) {
                    $allSigners = $cfg['signers'];
                }
            }

            // Pejabat yang QR-nya discan
            $scannedSigner = [
                'Token'     => $tokenData['Token'],
                'Nama'      => $tokenData['Nama'],
                'Jabatan'   => $tokenData['Jabatan'],
                'Instansi'  => $tokenData['Instansi'],
                'CreatedAt' => $tokenData['CreatedAt'],
            ];

            return view('frontend/signature/valid_surat', [
                'surat'          => $surat,
                'scannedSigner'  => $scannedSigner,
                'allSigners'     => $allSigners,
                'penandaTangan1' => null,
                'penandaTangan2' => null,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Token verification failed: ' . $e->getMessage());
            return view('frontend/signature/invalid', [
                'message' => 'Terjadi kesalahan saat memverifikasi tanda tangan elektronik.'
            ]);
        }
    }

    /**
     * Validasi Keaslian Surat Resmi (Fase 3 - Legacy ID)
     */
    public function validateSurat($id = null)
    {
        try {
            if (empty($id) || $this->request->getGet('preview') == 1) {
                return view('frontend/signature/invalid', [
                    'type'    => 'info',
                    'title'   => 'SIMULASI PRATINJAU DRAF SURAT',
                    'message' => 'Dokumen ini masih dalam tahap penyusunan / pratinjau draf. Tanda tangan QR Code verifikasi resmi akan aktif secara penuh setelah surat ini difinalisasi dan disimpan.'
                ]);
            }

            $suratModel = new \App\Models\Administrasi\SuratModel();
            $guruModel  = new \App\Models\GuruModel();

            $surat = $suratModel->select('tbl_surat.*, t.NamaTpq')
                                ->join('tbl_tpq t', 't.IdTpq = tbl_surat.IdTpq', 'left')
                                ->where('tbl_surat.Id', $id)
                                ->first();

            if (!$surat) {
                return view('frontend/signature/invalid', [
                    'type'    => 'danger',
                    'title'   => 'Surat Tidak Ditemukan',
                    'message' => 'Surat tidak ditemukan atau nomor surat tidak terdaftar dalam pangkalan data resmi.'
                ]);
            }

            if ($surat['Status'] !== 'Final') {
                return view('frontend/signature/invalid', [
                    'type'    => 'warning',
                    'title'   => 'DOKUMEN BELUM FINAL',
                    'message' => 'Surat ini masih berstatus ' . esc($surat['Status']) . '. Tanda tangan digital hanya terverifikasi valid setelah surat difinalisasi secara resmi.'
                ]);
            }

            $penandaTangan1 = !empty($surat['IdPenandaTangan1']) ? $guruModel->find($surat['IdPenandaTangan1']) : null;
            $penandaTangan2 = !empty($surat['IdPenandaTangan2']) ? $guruModel->find($surat['IdPenandaTangan2']) : null;

            return view('frontend/signature/valid_surat', [
                'surat'          => $surat,
                'penandaTangan1' => $penandaTangan1,
                'penandaTangan2' => $penandaTangan2,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Surat validation failed: ' . $e->getMessage());
            return view('frontend/signature/invalid', [
                'message' => 'Terjadi kesalahan saat memvalidasi keaslian surat.'
            ]);
        }
    }
}
