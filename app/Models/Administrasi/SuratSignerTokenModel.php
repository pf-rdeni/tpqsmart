<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratSignerTokenModel extends Model
{
    protected $table            = 'tbl_surat_signer_tokens';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdSurat',
        'SignerIndex',
        'Token',
        'Nama',
        'Jabatan',
        'Instansi',
        'IsRevoked',
        'RevokedAt',
        'RevokedReason',
        'CreatedAt',
        'UpdatedAt',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Generate token unik 10 karakter acak (lowercase alphanumeric, collision-safe)
     */
    public function generateUniqueToken(): string
    {
        do {
            // 10 karakter acak alphanumeric menggunakan cryptographic random_bytes
            $bytes = random_bytes(6);
            $token = substr(bin2hex($bytes), 0, 10);
            $exists = $this->where('Token', $token)->countAllResults();
        } while ($exists > 0);

        return $token;
    }

    /**
     * Revoke seluruh token aktif milik surat tertentu
     */
    public function revokeBySurat(int $idSurat, string $reason = 'Surat direvisi atau dibatalkan'): bool
    {
        return (bool) $this->where('IdSurat', $idSurat)
                           ->where('IsRevoked', 0)
                           ->set([
                               'IsRevoked'     => 1,
                               'RevokedAt'     => date('Y-m-d H:i:s'),
                               'RevokedReason' => $reason,
                           ])
                           ->update();
    }

    /**
     * Cari detail token lengkap beserta info surat
     */
    public function getDetailByToken(string $token): ?array
    {
        return $this->select('tbl_surat_signer_tokens.*, s.NomorSurat, s.Hal, s.Tanggal, s.Status as StatusSurat, s.IdTpq, s.PenandaTanganConfig, s.TempatTtd, s.TanggalTtd, t.NamaTpq')
                    ->join('tbl_surat s', 's.Id = tbl_surat_signer_tokens.IdSurat', 'left')
                    ->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left')
                    ->where('tbl_surat_signer_tokens.Token', $token)
                    ->first();
    }
}
