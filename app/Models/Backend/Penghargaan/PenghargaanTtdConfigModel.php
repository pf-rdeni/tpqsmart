<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model konfigurasi penandatangan dinamis (1-3 slot) per jenis penghargaan.
 */
class PenghargaanTtdConfigModel extends Model
{
    protected $table          = 'tbl_penghargaan_ttd_config';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'IdJenisPenghargaan',
        'UrutanTtd',
        'SumberTtd',
        'IdJabatan',
        'NamaManual',
        'JabatanManual',
        'LabelTtd',
        'ModeTtd',
        'FileTtd',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil daftar konfigurasi penandatangan beserta nama jabatan untuk jenis tertentu.
     */
    public function getByJenisAndTpq(int $idJenis, string $idTpq, bool $onlyActive = true): array
    {
        $builder = $this->db->table($this->table . ' tc');
        $builder->select('tc.*, j.NamaJabatan');
        $builder->join('tbl_jabatan j', 'j.IdJabatan = tc.IdJabatan', 'left');
        $builder->where('tc.IdJenisPenghargaan', $idJenis);
        $builder->where('tc.IdTpq', $idTpq);

        if ($onlyActive) {
            $builder->where('tc.IsActive', 1);
        }

        return $builder->orderBy('tc.UrutanTtd', 'ASC')
            ->get()
            ->getResultArray();
    }
}
