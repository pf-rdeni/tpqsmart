<?php

namespace App\Models;

use CodeIgniter\Model;

class MdaModel extends Model
{
    protected $table      = 'tbl_mda';
    protected $primaryKey = 'IdTpq';
    protected $useAutoIncrement = false;
    protected $useTimestamps = true;
    protected $allowedFields = ['IdTpq', 'IdMda', 'NamaTpq', 'Alamat', 'TahunBerdiri', 'TempatBelajar', 'KepalaSekolah', 'NoHp', 'LogoLembaga', 'KopLembaga', 'Visi', 'Misi'];
    
    public function GetData($id = false)
    {
        if ($id) {
            return $this->where(['IdTpq' => $id])->find();
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

