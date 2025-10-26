<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahMateriModel extends Model
{
    protected $table = 'tbl_munaqosah_materi';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'IdMateri',
        'KategoriMateri',
        'IdGrupMateriUjian',
        'Status'
    ];

    protected $validationRules = [
        'IdMateri' => 'required|max_length[50]',
        'KategoriMateri' => 'required|max_length[100]',
        'IdGrupMateriUjian' => 'required|max_length[50]',
        'Status' => 'required|max_length[20]'
    ];

    protected $validationMessages = [
        'IdMateri' => [
            'required' => 'ID Materi harus diisi',
            'max_length' => 'ID Materi maksimal 50 karakter'
        ],
        'KategoriMateri' => [
            'required' => 'Kategori materi harus diisi',
            'max_length' => 'Kategori materi maksimal 100 karakter'
        ],
        'IdGrupMateriUjian' => [
            'required' => 'ID Grup Materi Ujian harus diisi',
            'max_length' => 'ID Grup Materi Ujian maksimal 50 karakter'
        ],
        'Status' => [
            'required' => 'Status harus diisi',
            'max_length' => 'Status maksimal 20 karakter'
        ]
    ];

    public function getMateriWithRelations($id = null)
    {
        $builder = $this->db->table($this->table . ' mm');
        $builder->select('mm.*, m.NamaMateri, m.Kategori as KategoriAsli, g.NamaMateriGrup');
        $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = mm.IdMateri', 'left');
        $builder->join('tbl_munaqosah_grup_materi_uji g', 'g.IdGrupMateriUjian = mm.IdGrupMateriUjian', 'left');

        if ($id) {
            $builder->where('mm.id', $id);
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    public function getMateriByGrup($grup)
    {
        $builder = $this->db->table($this->table . ' mm');
        $builder->select('mm.*, mp.NamaMateri, mp.Kategori as KategoriAsli');
        $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = mm.IdMateri', 'left');
        $builder->where('mm.IdGrupMateriUjian', $grup);
        $builder->where('mm.Status', 'Aktif');
        
        return $builder->get()->getResultArray();
    }

    public function getGrupMateri()
    {
        return $this->select('DISTINCT(IdGrupMateriUjian) as grup')
                   ->get()
                   ->getResult();
    }

    public function checkDuplicateMateri($idMateriArray)
    {
        if (empty($idMateriArray)) {
            return [];
        }

        $existingMateri = $this->whereIn('IdMateri', $idMateriArray)
                              ->select('IdMateri')
                              ->findAll();

        return array_column($existingMateri, 'IdMateri');
    }

    public function getMateriInfo($idMateriArray)
    {
        if (empty($idMateriArray)) {
            return [];
        }

        $builder = $this->db->table('tbl_materi_pelajaran');
        $builder->select('IdMateri, NamaMateri, Kategori');
        $builder->whereIn('IdMateri', $idMateriArray);
        
        return $builder->get()->getResult();
    }

    public function checkMateriUsedInNilai($idMateri)
    {
        $builder = $this->db->table('tbl_munaqosah_nilai');
        $builder->where('IdMateri', $idMateri);
        $count = $builder->countAllResults();
        
        return $count > 0;
    }

    public function getMateriUsageInfo($idMateri)
    {
        $builder = $this->db->table('tbl_munaqosah_nilai nm');
        $builder->select('nm.IdMateri, COUNT(nm.IdMateri) as usage_count');
        $builder->where('nm.IdMateri', $idMateri);
        $builder->groupBy('nm.IdMateri');
        
        return $builder->get()->getRow();
    }
}
