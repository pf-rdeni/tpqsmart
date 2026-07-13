<?php

namespace App\Models\Frontend\Infografis;

use CodeIgniter\Model;

class InfografisGaleriModel extends Model
{
    protected $table            = 'tbl_infografis_galeri';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'IdTpq',
        'Judul',
        'NamaFile',
        'Keterangan',
        'TanggalKegiatan',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get galeri by IdTpq (semua, termasuk nonaktif)
     */
    public function getGaleriByTpq($idTpq): array
    {
        if (empty($idTpq) || $idTpq == '0' || $idTpq == 0) {
            return $this->groupStart()
                        ->where('IdTpq', '0')
                        ->orWhere('IdTpq', null)
                        ->orWhere('IdTpq', '')
                        ->groupEnd()
                        ->orderBy('TanggalKegiatan', 'DESC')
                        ->findAll();
        }

        return $this->where('IdTpq', $idTpq)
                    ->orderBy('TanggalKegiatan', 'DESC')
                    ->findAll();
    }

    /**
     * Get active galeri saja (untuk tampilan TV)
     */
    public function getActiveGaleri($idTpq, int $limit = 20): array
    {
        $builder = $this->where('IsActive', 1);

        if (empty($idTpq) || $idTpq == '0' || $idTpq == 0) {
            $builder->groupStart()
                    ->where('IdTpq', '0')
                    ->orWhere('IdTpq', null)
                    ->orWhere('IdTpq', '')
                    ->groupEnd();
        } else {
            $builder->where('IdTpq', $idTpq);
        }

        return $builder->orderBy('TanggalKegiatan', 'DESC')
                       ->limit($limit)
                       ->findAll();
    }
}
