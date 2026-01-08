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
    public function getSignaturesWithPosition($idSantri = null, $idKelas = null, $idTpq = null, $idTahunAjaran = null, $semester = null, $typeLembaga= null)
    {
        // Query untuk guru kelas (Wali Kelas, Guru Kelas)
        $builder1 = $this->db->table('tbl_tanda_tangan s');
        $builder1->select('s.*, j.NamaJabatan, g.Nama as NamaGuru, gk.IdKelas');
        $builder1->join('tbl_guru_kelas gk', 'gk.IdGuru = s.IdGuru AND gk.IdTpq = s.IdTpq AND gk.IdTahunAjaran = s.IdTahunAjaran AND gk.IdKelas = s.IdKelas');
        $builder1->join('tbl_jabatan j', 'j.IdJabatan = gk.IdJabatan');
        $builder1->join('tbl_guru g', 'g.IdGuru = s.IdGuru');

        if ($idSantri) {
            $builder1->where('s.IdSantri', $idSantri);
        }
        if ($idKelas) {
            if (is_array($idKelas)) {
                $builder1->whereIn('s.IdKelas', $idKelas);
            } else {
                $builder1->where('s.IdKelas', $idKelas);
            }
        }
        if ($idTpq) {
            $builder1->where('s.IdTpq', $idTpq);
        }
        if ($idTahunAjaran) {
            if (is_array($idTahunAjaran)) {
                $builder1->whereIn('s.IdTahunAjaran', $idTahunAjaran);
            } else {
                $builder1->where('s.IdTahunAjaran', $idTahunAjaran);
            }
        }
        if ($semester) {
            $builder1->where('s.Semester', $semester);
        }
        $builder1->where('s.JenisDokumen', 'Rapor');
        $builder1->where('s.Status', 'active');

        $result1 = $builder1->get()->getResultArray();

        // Query untuk Kepala TPQ dari struktur lembaga
        $builder2 = $this->db->table('tbl_tanda_tangan s');
        $builder2->select('s.*, j.NamaJabatan, g.Nama as NamaGuru, NULL as IdKelas');
        $builder2->join('tbl_struktur_lembaga sl', 'sl.IdGuru = s.IdGuru AND sl.IdTpq = s.IdTpq');
        $builder2->join('tbl_jabatan j', 'j.IdJabatan = sl.IdJabatan');
        $builder2->join('tbl_guru g', 'g.IdGuru = s.IdGuru');
        if($typeLembaga === 'MDA') {
            $builder2->where('j.NamaJabatan', 'Kepala MDTA');
        }else{
            $builder2->where('j.NamaJabatan', 'Kepala TPQ');
        }


        if ($idSantri) {
            $builder2->where('s.IdSantri', $idSantri);
        }
        if ($idTpq) {
            $builder2->where('s.IdTpq', $idTpq);
        }
        if ($idTahunAjaran) {
            if (is_array($idTahunAjaran)) {
                $builder2->whereIn('s.IdTahunAjaran', $idTahunAjaran);
            } else {
                $builder2->where('s.IdTahunAjaran', $idTahunAjaran);
            }
        }
        if ($semester) {
            $builder2->where('s.Semester', $semester);
        }
        $builder2->where('s.JenisDokumen', 'Rapor');
        $builder2->where('s.Status', 'active');

        $result2 = $builder2->get()->getResultArray();

        // Gabungkan hasil dari kedua query
        $allResults = array_merge($result1, $result2);

        // Urutkan berdasarkan IdKelas dan IdGuru
        usort($allResults, function ($a, $b) {
            // Urutkan berdasarkan IdKelas (null di akhir)
            $kelasA = $a['IdKelas'] ?? 999999;
            $kelasB = $b['IdKelas'] ?? 999999;

            if ($kelasA == $kelasB) {
                return $a['IdGuru'] <=> $b['IdGuru'];
            }
            return $kelasA <=> $kelasB;
        });

        return $allResults;
    }
}
