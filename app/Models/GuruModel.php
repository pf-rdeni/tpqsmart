<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table      = 'tbl_guru';
    //tambhkan setting lainya
    protected $primaryKey = 'IdGuru';
    protected $useAutoIncrement = false;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdGuru',
        'Nama',
        'JenisKelamin',
        'TempatLahir',
        'TanggalLahir',
        'TanggalMulaiTugas',
        'TempatTugas',
        'PendidikanTerakhir',
        'JurusanPendidikanTerakhir',
        'NamaIbuKandung',
        'NamaAyahKandung',
        'Alamat',
        'Rt',
        'Rw',
        'KelurahanDesa',
        'Kecamatan',
        'KabupatenKota',
        'Provinsi',
        'NoHp',
        'Status',
        'IdTpq',
        'NoRekBpr',
        'NoRekRiauKepri',
        'JenisPenerimaInsentif'
    ];
    public function GetData()
    {
        return $this->findAll();
    }
}