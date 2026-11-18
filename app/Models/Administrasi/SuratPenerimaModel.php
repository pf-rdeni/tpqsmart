<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratPenerimaModel extends Model
{
    protected $table            = 'tbl_surat_penerima';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdSurat',
        'TipePenerima',
        'IdReferensi',
        'NamaPenerima',
        'JabatanPenerima',
        'StatusKirim',
        'TanggalKirim',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = '';

    public function getBySurat(int $idSurat): array
    {
        return $this->where('IdSurat', $idSurat)->orderBy('Id', 'ASC')->findAll();
    }
}
