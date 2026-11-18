<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratKopCustomModel extends Model
{
    protected $table            = 'tbl_surat_kop_custom';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdTpq',
        'NamaKop',
        'GambarKop',
        'Keterangan',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    // ----------------------------------------------------------------
    // Helper Methods
    // ----------------------------------------------------------------

    /**
     * Ambil semua kop aktif: global (IdTpq NULL) + milik TPQ tertentu.
     */
    public function getAktifByTpq(int $idTpq): array
    {
        return $this->where('IsActive', 1)
                    ->groupStart()
                        ->where('IdTpq', $idTpq)
                        ->orWhere('IdTpq IS NULL', null, false)
                    ->groupEnd()
                    ->orderBy('NamaKop', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil kop global (tidak terikat TPQ).
     */
    public function getGlobal(): array
    {
        return $this->where('IdTpq IS NULL', null, false)
                    ->where('IsActive', 1)
                    ->findAll();
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleAktif(int $id): bool
    {
        $kop = $this->find($id);
        if (!$kop) {
            return false;
        }
        return $this->update($id, ['IsActive' => $kop['IsActive'] ? 0 : 1]);
    }
}
