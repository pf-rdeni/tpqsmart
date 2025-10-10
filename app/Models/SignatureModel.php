<?php

namespace App\Models;

use CodeIgniter\Model;

class SignatureModel extends Model
{
    protected $table = 'tbl_tanda_tangan';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'Token',
        'IdSantri',
        'IdKelas',
        'IdTahunAjaran',
        'Semester',
        'IdGuru',
        'IdTpq',
        'JenisDokumen',
        'SignatureData',
        'QrCode',
        'StatusValidasi',
        'TanggalTtd',
        'CreatedAt',
        'UpdatedAt'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'CreatedAt';
    protected $updatedField = 'UpdatedAt';

    public function validateSignature($token)
    {
        return $this->where('Token', $token)
            ->where('StatusValidasi', 'Valid')
            ->first();
    }

    public function getSignaturesBySantri($idSantri)
    {
        return $this->where('IdSantri', $idSantri)
            ->findAll();
    }

    public function getSignaturesByGuru($idGuru)
    {
        return $this->where('IdGuru', $idGuru)
            ->findAll();
    }

    public function getSignaturesByTpq($idTpq)
    {
        return $this->where('IdTpq', $idTpq)
            ->findAll();
    }

    /**
     * Get signatures with teacher position information for rapor
     */
    public function getSignaturesWithPosition($idSantri = null, $idKelas = null, $idTpq = null, $idTahunAjaran = null, $semester = null)
    {
        $builder = $this->db->table('tbl_tanda_tangan s');
        $builder->select('s.*, j.NamaJabatan, g.Nama as NamaGuru');
        $builder->join('tbl_guru_kelas gk', 'gk.IdGuru = s.IdGuru AND gk.IdTpq = s.IdTpq AND gk.IdTahunAjaran = s.IdTahunAjaran AND gk.IdKelas = s.IdKelas');
        $builder->join('tbl_jabatan j', 'j.IdJabatan = gk.IdJabatan');
        $builder->join('tbl_guru g', 'g.IdGuru = s.IdGuru');
        if ($idSantri) {
            $builder->where('s.IdSantri', $idSantri);
        }
        if ($idKelas) {
            if (is_array($idKelas)) {
                $builder->whereIn('s.IdKelas', $idKelas);
            } else {
                $builder->where('s.IdKelas', $idKelas);
            }
        }
        if ($idTpq) {
            $builder->where('s.IdTpq', $idTpq);
        }
        if ($idTahunAjaran) {
            if (is_array($idTahunAjaran)) {
                $builder->whereIn('s.IdTahunAjaran', $idTahunAjaran);
            } else {
                $builder->where('s.IdTahunAjaran', $idTahunAjaran);
            }
        }
        if ($semester) {
            $builder->where('s.Semester', $semester);
        }
        $builder->where('s.JenisDokumen', 'Rapor');
        $builder->where('s.Status', 'active');
        $builder->orderBy('s.IdKelas', 'ASC');
        $builder->orderBy('s.IdGuru', 'ASC');

        return $builder->get()->getResultArray();
    }
}
