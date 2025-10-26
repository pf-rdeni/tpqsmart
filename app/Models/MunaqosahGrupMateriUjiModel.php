<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahGrupMateriUjiModel extends Model
{
    protected $table = 'tbl_munaqosah_grup_materi_uji';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'IdGrupMateriUjian',
        'NamaMateriGrup',
        'Status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'IdGrupMateriUjian' => 'required|max_length[50]|is_unique[tbl_munaqosah_grup_materi_uji.IdGrupMateriUjian,id,{id}]',
        'NamaMateriGrup' => 'required|max_length[100]',
        'Status' => 'required|in_list[Aktif,Tidak Aktif]'
    ];

    protected $validationMessages = [
        'IdGrupMateriUjian' => [
            'required' => 'ID Grup Materi Ujian harus diisi',
            'max_length' => 'ID Grup Materi Ujian maksimal 50 karakter',
            'is_unique' => 'ID Grup Materi Ujian sudah ada'
        ],
        'NamaMateriGrup' => [
            'required' => 'Nama Materi Grup harus diisi',
            'max_length' => 'Nama Materi Grup maksimal 100 karakter'
        ],
        'Status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus Aktif atau Tidak Aktif'
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

    public function getGrupMateriAktif()
    {
        return $this->where('Status', 'Aktif')->orderBy('NamaMateriGrup', 'ASC')->findAll();
    }

    public function getGrupMateriById($id)
    {
        return $this->find($id);
    }

    public function checkGrupMateriUsed($idGrupMateriUjian)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_munaqosah_materi');
        $builder->where('GrupMateriUjian', $idGrupMateriUjian);
        $count = $builder->countAllResults();
        
        return $count > 0;
    }

    public function getGrupMateriUsageInfo($idGrupMateriUjian)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_munaqosah_materi');
        $builder->select('GrupMateriUjian, COUNT(GrupMateriUjian) as usage_count');
        $builder->where('GrupMateriUjian', $idGrupMateriUjian);
        $builder->groupBy('GrupMateriUjian');
        
        return $builder->get()->getRow();
    }

    public function generateNextIdGrupMateriUjian()
    {
        // Ambil ID terakhir yang dimulai dengan 'GM'
        $builder = $this->db->table($this->table);
        $builder->select('IdGrupMateriUjian');
        $builder->like('IdGrupMateriUjian', 'GM', 'after');
        $builder->orderBy('IdGrupMateriUjian', 'DESC');
        $builder->limit(1);
        
        $result = $builder->get()->getRow();
        
        if ($result) {
            // Extract number dari ID terakhir (misal GM001 -> 001)
            $lastId = $result->IdGrupMateriUjian;
            $number = intval(substr($lastId, 2)); // Ambil angka setelah 'GM'
            $nextNumber = $number + 1;
            return 'GM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada data, mulai dari GM001
            return 'GM001';
        }
    }

    public function checkNamaMateriGrupExists($namaMateriGrup, $excludeId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, NamaMateriGrup');
        $builder->where('LOWER(NamaMateriGrup)', strtolower($namaMateriGrup));
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        $result = $builder->get()->getRow();
        return $result;
    }
}
