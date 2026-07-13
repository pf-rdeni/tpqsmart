<?php

namespace App\Models\Frontend\Infografis;

use CodeIgniter\Model;

class InfografisAgendaModel extends Model
{
    protected $table            = 'tbl_infografis_agenda';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'IdTpq',
        'NamaKegiatan',
        'TanggalMulai',
        'TanggalSelesai',
        'JamMulai',
        'JamSelesai',
        'Tempat',
        'Keterangan',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get semua agenda by IdTpq (termasuk nonaktif, untuk backend)
     */
    public function getAgendaByTpq($idTpq): array
    {
        if (empty($idTpq) || $idTpq == '0' || $idTpq == 0) {
            return $this->groupStart()
                        ->where('IdTpq', '0')
                        ->orWhere('IdTpq', null)
                        ->orWhere('IdTpq', '')
                        ->groupEnd()
                        ->orderBy('TanggalMulai', 'ASC')
                        ->findAll();
        }

        return $this->where('IdTpq', $idTpq)
                    ->orderBy('TanggalMulai', 'ASC')
                    ->findAll();
    }

    /**
     * Get upcoming agenda yang masih aktif dan belum lewat (untuk tampilan TV)
     */
    public function getUpcomingAgenda($idTpq, int $limit = 10): array
    {
        $today = date('Y-m-d');
        $builder = $this->where('IsActive', 1)
                        ->where('TanggalMulai >=', $today);

        if (empty($idTpq) || $idTpq == '0' || $idTpq == 0) {
            $builder->groupStart()
                    ->where('IdTpq', '0')
                    ->orWhere('IdTpq', null)
                    ->orWhere('IdTpq', '')
                    ->groupEnd();
        } else {
            $builder->where('IdTpq', $idTpq);
        }

        return $builder->orderBy('TanggalMulai', 'ASC')
                       ->limit($limit)
                       ->findAll();
    }
}
