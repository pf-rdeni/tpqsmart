<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model pengelompokan kelas per jenjang untuk Juara Umum Lembaga.
 */
class PenghargaanJenjangModel extends Model
{
    protected $table          = 'tbl_penghargaan_jenjang';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'NamaJenjang',
        'IdKelas',
        'Urutan',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil daftar mapping jenjang beserta nama kelas dari tbl_kelas.
     */
    public function getWithKelasByTpq(string $idTpq, bool $onlyActive = true): array
    {
        $builder = $this->db->table($this->table . ' pj');
        $builder->select('pj.*, k.NamaKelas');
        $builder->join('tbl_kelas k', 'k.IdKelas = pj.IdKelas', 'left');
        $builder->where('pj.IdTpq', $idTpq);

        if ($onlyActive) {
            $builder->where('pj.IsActive', 1);
        }

        return $builder->orderBy('pj.Urutan', 'ASC')
            ->orderBy('pj.NamaJenjang', 'ASC')
            ->orderBy('k.NamaKelas', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar IdKelas yang termasuk dalam suatu jenjang.
     */
    public function getKelasIdsByJenjang(string $idTpq, string $namaJenjang): array
    {
        $rows = $this->select('IdKelas')
            ->where('IdTpq', $idTpq)
            ->where('NamaJenjang', $namaJenjang)
            ->where('IsActive', 1)
            ->findAll();

        return array_column($rows, 'IdKelas');
    }
}
