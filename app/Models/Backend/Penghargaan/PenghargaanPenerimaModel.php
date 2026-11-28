<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model detail santri penerima sertifikat penghargaan.
 */
class PenghargaanPenerimaModel extends Model
{
    protected $table          = 'tbl_penghargaan_penerima';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'IdSertifikat',
        'IdSantri',
        'IdKelas',
        'IdJenjang',
        'Peringkat',
        'NilaiRataRata',
        'PersenKehadiran',
        'SkorGabungan',
        'CatatanKhusus',
        'NomorSeri',
        'TokenVerifikasi',
        'TanggalCetak',
        'CetakBy',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil daftar penerima pada satu batch sertifikat beserta data santri dan kelas.
     */
    public function getBySertifikat(int $idSertifikat, ?array $allowedKelasIds = null): array
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, sb.NamaSantri, sb.NISN as NIS, k.NamaKelas');
        $builder->join('tbl_santri_baru sb', 'sb.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = p.IdKelas', 'left');
        $builder->where('p.IdSertifikat', $idSertifikat);

        if ($allowedKelasIds !== null) {
            if (empty($allowedKelasIds)) {
                return [];
            }
            $builder->whereIn('p.IdKelas', $allowedKelasIds);
        }

        return $builder->orderBy('p.Peringkat', 'ASC')
            ->orderBy('sb.NamaSantri', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar penghargaan berstatus Final untuk portal santri.
     */
    public function getFinalBySantri(string $idSantri, array $filters = []): array
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, s.IdTahunAjaran, s.Semester, s.Momen, s.TanggalRelease, j.Kode, j.NamaPenghargaan, j.Kategori, k.NamaKelas');
        $builder->join('tbl_penghargaan_sertifikat s', 's.Id = p.IdSertifikat');
        $builder->join('tbl_penghargaan_jenis j', 'j.Id = s.IdJenis', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = p.IdKelas', 'left');
        $builder->where('p.IdSantri', $idSantri);
        $builder->where('s.Status', 'final');

        if (!empty($filters['IdTahunAjaran'])) {
            $builder->where('s.IdTahunAjaran', $filters['IdTahunAjaran']);
        }
        if (!empty($filters['IdJenis'])) {
            $builder->where('s.IdJenis', $filters['IdJenis']);
        }

        return $builder->orderBy('s.TanggalRelease', 'DESC')
            ->orderBy('p.Id', 'DESC')
            ->get()
            ->getResultArray();
    }
}
