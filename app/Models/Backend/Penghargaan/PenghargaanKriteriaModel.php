<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model kriteria threshold dan bobot kalkulasi per jenis penghargaan.
 */
class PenghargaanKriteriaModel extends Model
{
    protected $table          = 'tbl_penghargaan_kriteria';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'IdJenis',
        'IdTahunAjaran',
        'NilaiMin',
        'PersenHadirMin',
        'BobotNilai',
        'BobotHadir',
        'JumlahPemenang',
        'GroupByJenjang',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil kriteria untuk jenis penghargaan tertentu.
     * Prioritas: spesifik tahun ajaran -> fallback umum (IdTahunAjaran IS NULL).
     */
    public function getByJenisAndTpq(int $idJenis, string $idTpq, ?string $idTahunAjaran = null): ?array
    {
        if ($idTahunAjaran !== null && $idTahunAjaran !== '') {
            $specific = $this->where('IdTpq', $idTpq)
                ->where('IdJenis', $idJenis)
                ->where('IdTahunAjaran', $idTahunAjaran)
                ->first();
            if ($specific) {
                return $specific;
            }
        }

        return $this->where('IdTpq', $idTpq)
            ->where('IdJenis', $idJenis)
            ->where('IdTahunAjaran IS NULL')
            ->first();
    }
}
