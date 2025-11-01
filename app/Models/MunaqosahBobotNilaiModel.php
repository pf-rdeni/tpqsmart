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
        'IdKategoriMateri',
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
        'IdKategoriMateri' => 'required|max_length[50]',
        'NilaiBobot' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]'
    ];

    protected $validationMessages = [
        'IdTahunAjaran' => [
            'required' => 'Tahun Ajaran harus diisi',
            'max_length' => 'Tahun Ajaran maksimal 50 karakter'
        ],
        'IdKategoriMateri' => [
            'required' => 'Kategori materi harus diisi',
            'max_length' => 'ID kategori materi maksimal 50 karakter'
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
        return $this->getBobotWithKategori($tahunAjaran);
    }

    /**
     * Get default bobot data
     */
    public function getDefaultBobot()
    {
        return $this->getBobotWithKategori('Default');
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

    /**
     * Ambil data bobot beserta nama kategori materi.
     *
     * @param string|null $tahunAjaran
     * @return array
     */
    public function getBobotWithKategori(?string $tahunAjaran = null): array
    {
        $builder = $this->builder();
        $builder->select($this->table . '.*, km.NamaKategoriMateri');
        $builder->join('tbl_munaqosah_kategori_materi km', 'km.IdKategoriMateri = ' . $this->table . '.IdKategoriMateri', 'left');

        if ($tahunAjaran !== null) {
            $builder->where($this->table . '.IdTahunAjaran', $tahunAjaran);
        }

        $builder->orderBy($this->table . '.IdTahunAjaran', 'ASC');
        $builder->orderBy($this->table . '.id', 'ASC');

        return $builder->get()->getResultArray();
    }
}
