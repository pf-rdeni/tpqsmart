<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikasiNilaiModel extends Model
{
    protected $table = 'tbl_sertifikasi_nilai';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'IdGroupMateri',
        'IdMateri',
        'IdJuri',
        'Nilai',
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'IdGroupMateri' => 'required|max_length[50]',
        'IdMateri' => 'permit_empty|max_length[50]',
        'IdJuri' => 'required|max_length[50]',
        'Nilai' => 'required|decimal',
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
        'IdGroupMateri' => [
            'required' => 'ID Group Materi harus diisi',
            'max_length' => 'ID Group Materi maksimal 50 karakter'
        ],
        'IdJuri' => [
            'required' => 'ID Juri harus diisi',
            'max_length' => 'ID Juri maksimal 50 karakter'
        ],
        'Nilai' => [
            'required' => 'Nilai harus diisi',
            'decimal' => 'Nilai harus berupa angka desimal'
        ],
    ];

    /**
     * Get nilai by NoPeserta
     */
    public function getNilaiByNoPeserta($noPeserta)
    {
        $builder = $this->db->table($this->table . ' sn');
        $builder->select('sn.*, sj.IdJuri, sj.usernameJuri, sgm.NamaMateri, sg.Nama as NamaGuru, sg.NoRek, sg.NamaTpq');
        $builder->join('tbl_sertifikasi_juri sj', 'sj.IdJuri = sn.IdJuri', 'left');
        $builder->join('tbl_sertifikasi_group_materi sgm', 'sgm.IdGroupMateri = sn.IdGroupMateri', 'left');
        $builder->join('tbl_sertifikasi_guru sg', 'sg.NoPeserta = sn.NoPeserta', 'left');
        $builder->where('sn.NoPeserta', $noPeserta);
        $builder->orderBy('sgm.IdGroupMateri', 'ASC');
        $builder->orderBy('sj.IdJuri', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get nilai by IdJuri and IdGroupMateri
     */
    public function getNilaiByJuriAndGroup($idJuri, $idGroupMateri)
    {
        return $this->where('IdJuri', $idJuri)
            ->where('IdGroupMateri', $idGroupMateri)
            ->findAll();
    }

    /**
     * Check if nilai already exists for NoPeserta, IdJuri, and IdGroupMateri
     */
    public function checkNilaiExists($noPeserta, $idJuri, $idGroupMateri)
    {
        return $this->where('NoPeserta', $noPeserta)
            ->where('IdJuri', $idJuri)
            ->where('IdGroupMateri', $idGroupMateri)
            ->first();
    }

    /**
     * Check if nilai already exists for NoPeserta, IdJuri, IdGroupMateri, and IdMateri
     */
    public function checkNilaiExistsByMateri($noPeserta, $idJuri, $idGroupMateri, $idMateri)
    {
        return $this->where('NoPeserta', $noPeserta)
            ->where('IdJuri', $idJuri)
            ->where('IdGroupMateri', $idGroupMateri)
            ->where('IdMateri', $idMateri)
            ->first();
    }

    /**
     * Check if nilai exists for NoPeserta, IdGroupMateri, and IdMateri (any IdJuri)
     */
    public function checkNilaiExistsByMateriAnyJuri($noPeserta, $idGroupMateri, $idMateri)
    {
        return $this->where('NoPeserta', $noPeserta)
            ->where('IdGroupMateri', $idGroupMateri)
            ->where('IdMateri', $idMateri)
            ->first();
    }

    /**
     * Get all nilai for NoPeserta and IdGroupMateri (any IdJuri)
     */
    public function getAllNilaiByPesertaAndGroup($noPeserta, $idGroupMateri)
    {
        return $this->where('NoPeserta', $noPeserta)
            ->where('IdGroupMateri', $idGroupMateri)
            ->findAll();
    }

    /**
     * Get all nilai with relations
     */
    public function getAllNilaiWithRelations()
    {
        $builder = $this->db->table($this->table . ' sn');
        $builder->select('sn.*, sj.IdJuri, sj.usernameJuri, sgm.NamaMateri as NamaGroupMateri, sm.NamaMateri, sm.IdMateri, sg.Nama as NamaGuru, sg.NoRek, sg.NamaTpq, sg.NoPeserta as noTest, sn.NoPeserta');
        $builder->join('tbl_sertifikasi_juri sj', 'sj.IdJuri = sn.IdJuri', 'left');
        $builder->join('tbl_sertifikasi_group_materi sgm', 'sgm.IdGroupMateri = sn.IdGroupMateri', 'left');
        $builder->join('tbl_sertifikasi_materi sm', 'sm.IdMateri = sn.IdMateri', 'left');
        $builder->join('tbl_sertifikasi_guru sg', 'sg.NoPeserta = sn.NoPeserta', 'left');
        $builder->orderBy('sg.Nama', 'ASC');
        $builder->orderBy('sgm.IdGroupMateri', 'ASC');
        $builder->orderBy('sm.IdMateri', 'ASC');
        $builder->orderBy('sj.IdJuri', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get all nilai by IdJuri with relations
     */
    public function getAllNilaiByIdJuri($idJuri)
    {
        $builder = $this->db->table($this->table . ' sn');
        $builder->select('sn.*, sj.IdJuri, sj.usernameJuri, sgm.NamaMateri as NamaGroupMateri, sm.NamaMateri, sm.IdMateri, sg.Nama as NamaGuru, sg.NoRek, sg.NamaTpq, sg.NoPeserta as noTest, sn.NoPeserta');
        $builder->join('tbl_sertifikasi_juri sj', 'sj.IdJuri = sn.IdJuri', 'left');
        $builder->join('tbl_sertifikasi_group_materi sgm', 'sgm.IdGroupMateri = sn.IdGroupMateri', 'left');
        $builder->join('tbl_sertifikasi_materi sm', 'sm.IdMateri = sn.IdMateri', 'left');
        $builder->join('tbl_sertifikasi_guru sg', 'sg.NoPeserta = sn.NoPeserta', 'left');
        $builder->where('sn.IdJuri', $idJuri);
        $builder->orderBy('sg.Nama', 'ASC');
        $builder->orderBy('sgm.IdGroupMateri', 'ASC');
        $builder->orderBy('sm.IdMateri', 'ASC');
        return $builder->get()->getResultArray();
    }
}

