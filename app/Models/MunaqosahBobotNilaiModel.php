<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahBobotNilaiModel extends Model
{
    protected $table = 'tbl_munaqosah_bobot_nilai';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'IdTahunAjaran',
        'KategoriMateriUjian',
        'NilaiBobot'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'IdTahunAjaran' => 'required|max_length[50]',
        'KategoriMateriUjian' => 'required|max_length[100]',
        'NilaiBobot' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]'
    ];

    protected $validationMessages = [
        'IdTahunAjaran' => [
            'required' => 'Tahun Ajaran harus diisi',
            'max_length' => 'Tahun Ajaran maksimal 50 karakter'
        ],
        'KategoriMateriUjian' => [
            'required' => 'Kategori Materi Ujian harus diisi',
            'max_length' => 'Kategori Materi Ujian maksimal 100 karakter'
        ],
        'NilaiBobot' => [
            'required' => 'Nilai Bobot harus diisi',
            'decimal' => 'Nilai Bobot harus berupa angka desimal',
            'greater_than_equal_to' => 'Nilai Bobot minimal 0',
            'less_than_equal_to' => 'Nilai Bobot maksimal 100'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get bobot data by tahun ajaran
     */
    public function getBobotByTahunAjaran($tahunAjaran)
    {
        return $this->where('IdTahunAjaran', $tahunAjaran)
                   ->orderBy('id', 'ASC')
                   ->findAll();
    }

    /**
     * Get default bobot data
     */
    public function getDefaultBobot()
    {
        return $this->where('IdTahunAjaran', 'Default')
                   ->orderBy('id', 'ASC')
                   ->findAll();
    }

    /**
     * Check if tahun ajaran exists
     */
    public function isTahunAjaranExists($tahunAjaran)
    {
        return $this->where('IdTahunAjaran', $tahunAjaran)->countAllResults() > 0;
    }

    /**
     * Delete all data by tahun ajaran
     */
    public function deleteByTahunAjaran($tahunAjaran)
    {
        return $this->where('IdTahunAjaran', $tahunAjaran)->delete();
    }
}
