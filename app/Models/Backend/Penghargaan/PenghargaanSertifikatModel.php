<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model header batch penerbitan sertifikat penghargaan.
 */
class PenghargaanSertifikatModel extends Model
{
    protected $table          = 'tbl_penghargaan_sertifikat';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'IdJenis',
        'IdTemplate',
        'IdTahunAjaran',
        'Semester',
        'Momen',
        'TanggalSertifikat',
        'TanggalGenerate',
        'GeneratedBy',
        'TanggalRelease',
        'ReleasedBy',
        'Status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil daftar batch sertifikat beserta info jenis dan jumlah penerima.
     */
    public function getListByTpq(string $idTpq, array $filters = []): array
    {
        $builder = $this->db->table($this->table . ' s');
        $builder->select('s.*, j.Kode, j.NamaPenghargaan, j.Kategori, t.NamaTemplate, COUNT(p.Id) as TotalPenerima');
        $builder->join('tbl_penghargaan_jenis j', 'j.Id = s.IdJenis', 'left');
        $builder->join('tbl_penghargaan_template t', 't.Id = s.IdTemplate', 'left');
        $builder->join('tbl_penghargaan_penerima p', 'p.IdSertifikat = s.Id', 'left');
        $builder->where('s.IdTpq', $idTpq);

        if (!empty($filters['IdTahunAjaran'])) {
            $builder->where('s.IdTahunAjaran', $filters['IdTahunAjaran']);
        }
        if (!empty($filters['Semester'])) {
            $builder->where('s.Semester', $filters['Semester']);
        }
        if (!empty($filters['IdJenis'])) {
            $builder->where('s.IdJenis', $filters['IdJenis']);
        }
        if (!empty($filters['Status'])) {
            $builder->where('s.Status', $filters['Status']);
        }
        if (isset($filters['IdKelasList']) && is_array($filters['IdKelasList'])) {
            if (empty($filters['IdKelasList'])) {
                $builder->where('1 = 0');
            } else {
                $builder->whereIn('p.IdKelas', $filters['IdKelasList']);
            }
        }

        $builder->groupBy('s.Id');
        $builder->orderBy('s.TanggalGenerate', 'DESC');

        return $builder->get()->getResultArray();
    }
}
