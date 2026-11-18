<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratKategoriModel extends Model
{
    protected $table            = 'tbl_surat_kategori';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'NamaKategori',
        'Kode',
        'JenisSurat',
        'Deskripsi',
        'Icon',
        'Warna',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    public function getActive(): array
    {
        return $this->where('IsActive', 1)->orderBy('NamaKategori', 'ASC')->findAll();
    }

    public function getByJenis(string $jenis): array
    {
        return $this->where('IsActive', 1)
                    ->groupStart()
                        ->where('JenisSurat', $jenis)
                        ->orWhere('JenisSurat', 'Keduanya')
                    ->groupEnd()
                    ->orderBy('NamaKategori', 'ASC')
                    ->findAll();
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
