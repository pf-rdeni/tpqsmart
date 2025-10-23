<?php

namespace App\Models;

use CodeIgniter\Model;

class TpqModel extends Model
{
    protected $table      = 'tbl_tpq';
    protected $primaryKey = 'IdTpq';
    protected $useAutoIncrement = false;
    protected $useTimestamps = true;
    protected $allowedFields = ['IdTpq', 'NamaTpq', 'Alamat', 'TahunBerdiri', 'TempatBelajar', 'KepalaSekolah', 'NoHp', 'LogoLembaga', 'KopLembaga'];
    
    public function GetData($id = false)
    {
        if ($id) {
            return $this->where(['IdTpq' => $id])->find();
            //return $this->find(['IdTpq' => $id]);
        } else {
            return $this->findAll();
        }
    }

    public function updateLogo($idTpq, $logoName)
    {
        // Update logo berdasarkan IdTpq yang spesifik
        return $this->update($idTpq, ['LogoLembaga' => $logoName]);
    }

    public function updateKop($idTpq, $kopName)
    {
        // Update kop_lembaga berdasarkan IdTpq yang spesifik
        return $this->update($idTpq, ['KopLembaga' => $kopName]);
    }
}
