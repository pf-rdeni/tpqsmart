<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model field overlay dan styling posisi teks/QR pada template sertifikat.
 */
class PenghargaanTemplateFieldModel extends Model
{
    protected $table          = 'tbl_penghargaan_template_field';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTemplate',
        'NamaField',
        'IsAktif',
        'PosisiX',
        'PosisiY',
        'FontSize',
        'FontFamily',
        'FontBold',
        'FontColor',
        'TextAlign',
        'Lebar',
        'Tinggi',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil daftar field konfigurasi untuk template tertentu.
     */
    public function getByTemplate(int $idTemplate, bool $onlyActive = false): array
    {
        $builder = $this->where('IdTemplate', $idTemplate);
        if ($onlyActive) {
            $builder->where('IsAktif', 1);
        }

        return $builder->orderBy('Id', 'ASC')->findAll();
    }
}
