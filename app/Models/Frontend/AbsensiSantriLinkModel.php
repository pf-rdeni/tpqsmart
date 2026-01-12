<?php

namespace App\Models\Frontend;

use CodeIgniter\Model;

class AbsensiSantriLinkModel extends Model
{
    protected $table            = 'tbl_absensi_santri_link';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['IdTpq', 'IdTahunAjaran', 'HashKey', 'CreatedAt'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = '';
    protected $deletedField  = '';

    public function getLinkByKey($hashKey)
    {
        return $this->where('HashKey', $hashKey)->first();
    }
}
