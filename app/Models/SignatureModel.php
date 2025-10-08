<?php

namespace App\Models;

use CodeIgniter\Model;

class SignatureModel extends Model
{
    protected $table = 'tbl_tanda_tangan';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'Token',
        'IdSantri',
        'IdKelas',
        'IdTahunAjaran',
        'Semester',
        'IdGuru',
        'IdTpq',
        'JenisDokumen',
        'QrCode',
        'StatusValidasi',
        'TanggalTtd',
        'CreatedAt',
        'UpdatedAt'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'CreatedAt';
    protected $updatedField = 'UpdatedAt';

    public function validateSignature($token)
    {
        return $this->where('Token', $token)
            ->where('StatusValidasi', 'Valid')
            ->first();
    }

    public function getSignaturesBySantri($idSantri)
    {
        return $this->where('IdSantri', $idSantri)
            ->findAll();
    }

    public function getSignaturesByGuru($idGuru)
    {
        return $this->where('IdGuru', $idGuru)
            ->findAll();
    }

    public function getSignaturesByTpq($idTpq)
    {
        return $this->where('IdTpq', $idTpq)
            ->findAll();
    }
}
