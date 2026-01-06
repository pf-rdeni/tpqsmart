<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaSertifikatFieldModel extends Model
{
    protected $table = 'tbl_lomba_sertifikat_fields';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'template_id',
        'FieldName',
        'FieldLabel',
        'PosX',
        'PosY',
        'FontFamily',
        'FontSize',
        'FontStyle',
        'TextAlign',
        'TextColor',
        'MaxWidth'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'template_id' => 'required|integer',
        'FieldName' => 'required|max_length[100]',
        'FieldLabel' => 'required|max_length[100]',
        'PosX' => 'required|integer',
        'PosY' => 'required|integer',
    ];

    /**
     * Get all fields for a template
     */
    public function getFieldsByTemplate($templateId)
    {
        return $this->where('template_id', $templateId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Get available field definitions
     */
    public function getAvailableFields()
    {
        return [
            [
                'name' => 'nama_santri',
                'label' => 'Nama Santri',
                'sample' => 'Ahmad Fauzi bin Abdullah'
            ],
            [
                'name' => 'nama_lomba',
                'label' => 'Nama Lomba',
                'sample' => 'Lomba Tahfidz Juz 30'
            ],
            [
                'name' => 'nama_cabang',
                'label' => 'Nama Cabang',
                'sample' => 'Putra Kelas 1-3'
            ],
            [
                'name' => 'kategori',
                'label' => 'Kategori',
                'sample' => 'Putra'
            ],
            [
                'name' => 'peringkat',
                'label' => 'Peringkat (Angka)',
                'sample' => 'Juara 1'
            ],
            [
                'name' => 'peringkat_text',
                'label' => 'Peringkat (Teks)',
                'sample' => 'JUARA PERTAMA'
            ],
            [
                'name' => 'nama_tpq',
                'label' => 'Nama TPQ',
                'sample' => 'TPQ Al-Ikhlas'
            ],
            [
                'name' => 'tanggal_lomba',
                'label' => 'Tanggal Lomba',
                'sample' => '15 Januari 2026'
            ],
            [
                'name' => 'tempat_lomba',
                'label' => 'Tempat Lomba',
                'sample' => 'Aula MDA Kota Bandung'
            ],
            [
                'name' => 'nilai_akhir',
                'label' => 'Nilai Akhir',
                'sample' => '95.50'
            ],
            [
                'name' => 'no_peserta',
                'label' => 'Nomor Peserta',
                'sample' => 'TH-001'
            ],
        ];
    }

    /**
     * Delete all fields for a template
     */
    public function deleteByTemplate($templateId)
    {
        return $this->where('template_id', $templateId)->delete();
    }

    /**
     * Save multiple fields at once
     */
    public function saveMultiple($fields)
    {
        return $this->insertBatch($fields);
    }
}
