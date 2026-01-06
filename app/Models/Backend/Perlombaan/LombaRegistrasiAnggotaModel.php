<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaRegistrasiAnggotaModel extends Model
{
    protected $table = 'tbl_lomba_registrasi_anggota';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'registrasi_id',
        'peserta_id',
        'created_at',
    ];

    // Matikan auto timestamps karena tidak ada updated_at di tabel
    protected $useTimestamps = false;

    /**
     * Ambil anggota berdasarkan registrasi_id
     */
    public function getAnggotaByRegistrasi($registrasiId)
    {
        $builder = $this->db->table($this->table . ' ra');
        $builder->select('ra.*, p.IdSantri, s.NamaSantri, s.JenisKelamin, s.TanggalLahirSantri, s.PhotoProfil, k.NamaKelas, t.NamaTpq, t.KelurahanDesa');
        $builder->join('tbl_lomba_peserta p', 'p.id = ra.peserta_id', 'left');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = s.IdKelas', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left');
        $builder->where('ra.registrasi_id', $registrasiId);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil semua anggota dari beberapa registrasi sekaligus
     */
    public function getAnggotaByRegistrasiIds($registrasiIds)
    {
        if (empty($registrasiIds)) {
            return [];
        }
        
        $builder = $this->db->table($this->table . ' ra');
        $builder->select('ra.*, p.IdSantri, s.NamaSantri, s.JenisKelamin, t.NamaTpq');
        $builder->join('tbl_lomba_peserta p', 'p.id = ra.peserta_id', 'left');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left');
        $builder->whereIn('ra.registrasi_id', $registrasiIds);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Cek apakah peserta sudah teregistrasi di cabang tertentu
     */
    public function isPesertaRegistered($pesertaId, $cabangId)
    {
        $builder = $this->db->table($this->table . ' ra');
        $builder->join('tbl_lomba_registrasi r', 'r.id = ra.registrasi_id');
        $builder->where('ra.peserta_id', $pesertaId);
        $builder->where('r.cabang_id', $cabangId);
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Ambil registrasi_id berdasarkan peserta_id
     */
    public function getRegistrasiIdByPeserta($pesertaId, $cabangId)
    {
        $builder = $this->db->table($this->table . ' ra');
        $builder->select('ra.registrasi_id');
        $builder->join('tbl_lomba_registrasi r', 'r.id = ra.registrasi_id');
        $builder->where('ra.peserta_id', $pesertaId);
        $builder->where('r.cabang_id', $cabangId);
        
        $result = $builder->get()->getRow();
        
        return $result ? $result->registrasi_id : null;
    }

    /**
     * Hitung jumlah anggota dalam registrasi
     */
    public function countAnggota($registrasiId)
    {
        return $this->where('registrasi_id', $registrasiId)->countAllResults();
    }
}
