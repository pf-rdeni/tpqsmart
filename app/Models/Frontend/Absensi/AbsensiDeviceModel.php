<?php

namespace App\Models\Frontend\Absensi;

use CodeIgniter\Model;

class AbsensiDeviceModel extends Model
{
    protected $table            = 'tbl_absensi_device';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['DeviceToken', 'IdGuru', 'LastAccess', 'UserAgent', 'CreatedAt'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = '';
    protected $deletedField  = '';

    public function getDevice($token)
    {
        return $this->where('DeviceToken', $token)->first();
    }
}
