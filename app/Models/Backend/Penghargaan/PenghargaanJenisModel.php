<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model master jenis penghargaan (akademik, kehadiran, gabungan, khusus).
 */
class PenghargaanJenisModel extends Model
{
    protected $table          = 'tbl_penghargaan_jenis';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'Kode',
        'NamaPenghargaan',
        'Deskripsi',
        'Kategori',
        'Momen',
        'IsDefault',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil jenis penghargaan yang tersedia untuk lembaga (master global + custom TPQ).
     */
    public function getAvailableByTpq(?string $idTpq = null, bool $onlyActive = true): array
    {
        $builder = $this->builder();
        $builder->groupStart()
            ->where('IdTpq IS NULL');

        if ($idTpq !== null && $idTpq !== '') {
            $builder->orWhere('IdTpq', $idTpq);
        }
        $builder->groupEnd();

        if ($onlyActive) {
            $builder->where('IsActive', 1);
        }

        return $builder->orderBy('Kategori', 'ASC')
            ->orderBy('Kode', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Toggle status aktif jenis penghargaan.
     */
    public function toggleActive(int $id): bool
    {
        $row = $this->find($id);
        if (!$row) {
            return false;
        }

        return $this->update($id, ['IsActive' => $row['IsActive'] ? 0 : 1]);
    }
}
