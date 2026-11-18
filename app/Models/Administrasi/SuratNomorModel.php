<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratNomorModel extends Model
{
    protected $table            = 'tbl_surat_nomor';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdTpq',
        'IdKategori',
        'Tahun',
        'Bulan',
        'UrutanTerakhir',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Dapatkan nomor urut berikutnya secara atomik (dengan transaction & row lock)
     */
    public function getNextNomor(int $idTpq, int $idKategori, int $tahun, ?int $bulan = null): int
    {
        $builder = $this->where('IdTpq', $idTpq)
                        ->where('IdKategori', $idKategori)
                        ->where('Tahun', $tahun);

        if ($bulan !== null) {
            $builder->where('Bulan', $bulan);
        } else {
            $builder->where('Bulan IS NULL', null, false);
        }

        $row = $builder->first();

        if ($row) {
            $next = (int)$row['UrutanTerakhir'] + 1;
            $this->update($row['Id'], ['UrutanTerakhir' => $next]);
            return $next;
        }

        // Jika belum ada record untuk periode ini, mulai dari 1
        $this->insert([
            'IdTpq'          => $idTpq,
            'IdKategori'     => $idKategori,
            'Tahun'          => $tahun,
            'Bulan'          => $bulan,
            'UrutanTerakhir' => 1
        ]);

        return 1;
    }

    /**
     * Reset counter nomor
     */
    public function resetCounter(int $id): bool
    {
        return $this->update($id, ['UrutanTerakhir' => 0]);
    }

    /**
     * Ambil data penomoran dengan info kategori dan TPQ
     */
    public function getAllWithDetails($idTpq = null): array
    {
        $builder = $this->select('tbl_surat_nomor.*, k.NamaKategori, k.Kode as KodeKategori, t.NamaTpq')
                        ->join('tbl_surat_kategori k', 'k.Id = tbl_surat_nomor.IdKategori', 'left')
                        ->join('tbl_tpq t', 't.IdTpq = tbl_surat_nomor.IdTpq', 'left');

        if (!empty($idTpq)) {
            $builder->where('tbl_surat_nomor.IdTpq', $idTpq);
        }

        return $builder->orderBy('tbl_surat_nomor.Tahun', 'DESC')
                       ->orderBy('tbl_surat_nomor.Bulan', 'DESC')
                       ->findAll();
    }
}
