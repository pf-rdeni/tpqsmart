<?php

namespace App\Models;

use CodeIgniter\Model;

class FkpqModel extends Model
{
    protected $table      = 'tbl_fkpq';
    protected $primaryKey = 'IdFkpq';
    protected $useAutoIncrement = false;
    protected $useTimestamps = true;
    protected $allowedFields = ['IdFkpq', 'NamaFkpq', 'Alamat', 'Kecamatan', 'TahunBerdiri', 'TempatBelajar', 'KetuaFkpq', 'NoHp', 'LogoLembaga', 'KopLembaga', 'Visi', 'Misi'];
    
    public function GetData($id = false)
    {
        if ($id) {
            return $this->where(['IdFkpq' => $id])->find();
        } else {
            return $this->findAll();
        }
    }

    public function updateLogo($idFkpq, $logoName)
    {
        // Update logo berdasarkan IdFkpq yang spesifik
        return $this->update($idFkpq, ['LogoLembaga' => $logoName]);
    }

    public function updateKop($idFkpq, $kopName)
    {
        // Update kop_lembaga berdasarkan IdFkpq yang spesifik
        return $this->update($idFkpq, ['KopLembaga' => $kopName]);
    }
}

