<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratDisposisiModel extends Model
{
    protected $table            = 'tbl_surat_disposisi';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdSurat',
        'DariUser',
        'KeUser',
        'Instruksi',
        'Prioritas',
        'Deadline',
        'StatusBaca',
        'CatatanBalas',
        'TanggalBaca',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    public function getBySurat(int $idSurat): array
    {
        return $this->select('tbl_surat_disposisi.*, u1.username as DariUsername, u2.username as KeUsername')
                    ->join('users u1', 'u1.id = tbl_surat_disposisi.DariUser', 'left')
                    ->join('users u2', 'u2.id = tbl_surat_disposisi.KeUser', 'left')
                    ->where('tbl_surat_disposisi.IdSurat', $idSurat)
                    ->orderBy('tbl_surat_disposisi.Id', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil disposisi yang membutuhkan perhatian (Belum dibaca / Mendekati Deadline)
     */
    public function getReminderDisposisi(?int $idTpq = null, ?int $userId = null): array
    {
        $builder = $this->select('tbl_surat_disposisi.*, s.NomorSurat, s.Hal, s.AsalSurat, s.TanggalTerima, u1.username as DariUsername, u2.username as KeUsername')
                        ->join('tbl_surat s', 's.Id = tbl_surat_disposisi.IdSurat', 'inner')
                        ->join('users u1', 'u1.id = tbl_surat_disposisi.DariUser', 'left')
                        ->join('users u2', 'u2.id = tbl_surat_disposisi.KeUser', 'left');

        if (!empty($idTpq)) {
            $builder->where('s.IdTpq', $idTpq);
        }

        if (!empty($userId)) {
            $builder->groupStart()
                    ->where('tbl_surat_disposisi.KeUser', $userId)
                    ->orWhere('tbl_surat_disposisi.DariUser', $userId)
                    ->groupEnd();
        }

        // Tampilkan disposisi yang belum dibaca ATAU belum dibalas
        $builder->groupStart()
                ->where('tbl_surat_disposisi.StatusBaca', 0)
                ->orWhere('tbl_surat_disposisi.CatatanBalas IS NULL')
                ->orWhere('tbl_surat_disposisi.CatatanBalas', '')
                ->groupEnd();

        $builder->orderBy('tbl_surat_disposisi.Deadline', 'ASC');
        $builder->orderBy('tbl_surat_disposisi.Id', 'DESC');

        return $builder->limit(10)->get()->getResultArray();
    }
}
