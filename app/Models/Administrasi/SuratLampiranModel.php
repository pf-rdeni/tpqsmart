<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratLampiranModel extends Model
{
    protected $table            = 'tbl_surat_lampiran';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdSurat',
        'NamaFile',
        'PathFile',
        'Ukuran',
        'TipeFile',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = '';

    public function getBySurat(int $idSurat): array
    {
        return $this->where('IdSurat', $idSurat)->orderBy('Id', 'ASC')->findAll();
    }
}
