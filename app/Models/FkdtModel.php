<?php

namespace App\Models;

use CodeIgniter\Model;

class FkdtModel extends Model
{
    protected $table      = 'tbl_fkdt';
    protected $primaryKey = 'IdFkdt';
    protected $useAutoIncrement = false;
    protected $useTimestamps = true;
    protected $allowedFields = ['IdFkdt', 'NamaFkdt', 'Alamat', 'Kecamatan', 'TahunBerdiri', 'TempatBelajar', 'KepalaSekolah', 'NoHp', 'LogoLembaga', 'KopLembaga', 'Visi', 'Misi'];
    
    public function GetData($id = false)
    {
        if ($id) {
            return $this->where(['IdFkdt' => $id])->find();
        } else {
            return $this->findAll();
        }
    }

    public function updateLogo($idFkdt, $logoName)
    {
        // Update logo berdasarkan IdFkdt yang spesifik
        return $this->update($idFkdt, ['LogoLembaga' => $logoName]);
    }

    public function updateKop($idFkdt, $kopName)
    {
        // Update kop_lembaga berdasarkan IdFkdt yang spesifik
        return $this->update($idFkdt, ['KopLembaga' => $kopName]);
    }
}

