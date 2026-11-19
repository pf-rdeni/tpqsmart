<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratTujuanMasterModel extends Model
{
    protected $table            = 'tbl_surat_tujuan_master';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdTpq',
        'KategoriTujuan',
        'NamaTujuan',
        'Jabatan',
        'Instansi',
        'Alamat',
        'Kota',
        'NoHp',
        'Email',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    public function getByTpq($idTpq = null): array
    {
        $builder = $this->select('tbl_surat_tujuan_master.*, t.NamaTpq')
                        ->join('tbl_tpq t', 't.IdTpq = tbl_surat_tujuan_master.IdTpq', 'left');

        if (!empty($idTpq)) {
            $builder->groupStart()
                    ->where('tbl_surat_tujuan_master.IdTpq', $idTpq)
                    ->orWhere('tbl_surat_tujuan_master.IdTpq IS NULL', null, false)
                    ->orWhere('tbl_surat_tujuan_master.IdTpq', 0)
                    ->groupEnd();
        }

        return $builder->orderBy('tbl_surat_tujuan_master.NamaTujuan', 'ASC')->findAll();
    }

    public function getActiveByKategori(string $kategori, $idTpq = null): array
    {
        $builder = $this->where('IsActive', 1)
                        ->where('KategoriTujuan', $kategori);

        if (!empty($idTpq)) {
            $builder->groupStart()
                    ->where('IdTpq', $idTpq)
                    ->orWhere('IdTpq IS NULL', null, false)
                    ->groupEnd();
        }

        return $builder->orderBy('NamaTujuan', 'ASC')->findAll();
    }

    public function toggleAktif(int $id): bool
    {
        $data = $this->find($id);
        if (!$data) {
            return false;
        }
        return $this->update($id, ['IsActive' => $data['IsActive'] ? 0 : 1]);
    }
}
