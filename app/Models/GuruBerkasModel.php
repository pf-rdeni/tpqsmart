<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruBerkasModel extends Model
{
    protected $table      = 'tbl_guru_berkas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdGuru',
        'IdTpq',
        'NamaBerkas',
        'DataBerkas',
        'NamaFile',
        'Status'
    ];

    /**
     * Mengambil semua berkas untuk guru tertentu
     * @param string $idGuru
     * @return array
     */
    public function getBerkasByGuru($idGuru)
    {
        return $this->where('IdGuru', $idGuru)
            ->orderBy('NamaBerkas', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Mengambil hanya berkas aktif untuk guru tertentu
     * @param string $idGuru
     * @return array
     */
    public function getBerkasAktifByGuru($idGuru)
    {
        return $this->where('IdGuru', $idGuru)
            ->where('Status', 1)
            ->orderBy('NamaBerkas', 'ASC')
            ->findAll();
    }

    /**
     * Mengambil berkas aktif berdasarkan IdGuru dan NamaBerkas
     * @param string $idGuru
     * @param string $namaBerkas
     * @param string|null $dataBerkas
     * @return array|null
     */
    public function getBerkasAktifByGuruAndType($idGuru, $namaBerkas, $dataBerkas = null)
    {
        $builder = $this->where('IdGuru', $idGuru)
            ->where('NamaBerkas', $namaBerkas)
            ->where('Status', 1);
        
        if ($dataBerkas !== null) {
            $builder->where('DataBerkas', $dataBerkas);
        }
        
        return $builder->first();
    }

    /**
     * Mengambil semua berkas aktif Buku Rekening untuk guru tertentu (multiple files)
     * @param string $idGuru
     * @return array
     */
    public function getBukuRekeningAktifByGuru($idGuru)
    {
        return $this->where('IdGuru', $idGuru)
            ->where('NamaBerkas', 'Buku Rekening')
            ->where('Status', 1)
            ->orderBy('DataBerkas', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Set semua berkas dengan tipe tertentu menjadi nonaktif
     * @param string $idGuru
     * @param string $namaBerkas
     * @param string|null $dataBerkas
     * @return bool
     */
    public function deactivateBerkasByType($idGuru, $namaBerkas, $dataBerkas = null)
    {
        $builder = $this->where('IdGuru', $idGuru)
            ->where('NamaBerkas', $namaBerkas)
            ->where('Status', 1);
        
        if ($dataBerkas !== null) {
            $builder->where('DataBerkas', $dataBerkas);
        }
        
        return $builder->set('Status', 0)->update();
    }
}

