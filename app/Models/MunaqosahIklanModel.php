<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahIklanModel extends Model
{
    protected $table      = 'tbl_munaqosah_iklan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'IdTpq',
        'Tahun',
        'Judul',
        'FileName',
        'MediaType',
        'SourceType',
        'ExternalUrl',
        'Urutan',
        'IsActive',
        'CreatedAt',
        'UpdatedAt',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'CreatedAt';
    protected $updatedField   = 'UpdatedAt';

    protected $validationRules = [
        'Judul'       => 'required|max_length[255]',
        'FileName'    => 'permit_empty|max_length[255]',
        'MediaType'   => 'permit_empty|in_list[image,video,youtube]',
        'SourceType'  => 'permit_empty|in_list[local,youtube,gdrive,external]',
        'ExternalUrl' => 'permit_empty',
        'IdTpq'       => 'permit_empty|max_length[50]',
        'Tahun'       => 'permit_empty|max_length[20]',
    ];

    // -------------------------------------------------------------------------
    // Public Methods
    // -------------------------------------------------------------------------

    /**
     * Ambil semua iklan (aktif & tidak aktif) berdasarkan IdTpq untuk halaman manajemen.
     *
     * @param string      $idTpq  ID TPQ spesifik atau '0' (semua TPQ)
     * @param string|null $tahun  Tahun ajaran atau null untuk semua tahun
     * @return array
     */
    public function getAllByTpq(string $idTpq = '0', ?string $tahun = null): array
    {
        $builder = $this->db->table($this->table);
        $builder->where('IdTpq', $idTpq);

        if ($tahun !== null) {
            $builder->groupStart()
                        ->where('Tahun', $tahun)
                        ->orWhere('Tahun IS NULL', null, false)
                    ->groupEnd();
        }

        $builder->orderBy('Urutan', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Ambil iklan aktif berdasarkan IdTpq (dan opsional Tahun).
     *
     * Logika filter:
     *  - Selalu ambil yang IsActive = 1
     *  - Jika $idTpq != '0', WHERE (IdTpq = $idTpq OR IdTpq = '0')
     *  - Jika $tahun tidak null, WHERE (Tahun = $tahun OR Tahun IS NULL)
     *  - Urutan: ASC berdasarkan kolom Urutan
     *
     * @param string      $idTpq  ID TPQ spesifik atau '0' (semua TPQ)
     * @param string|null $tahun  Tahun ajaran atau null untuk semua tahun
     * @return array
     */
    public function getActiveByTpq(string $idTpq = '0', ?string $tahun = null): array
    {
        $builder = $this->db->table($this->table);
        $builder->where('IsActive', 1);

        // Filter IdTpq: '0' untuk Munaqosah pusat, ID spesifik hanya untuk TPQ itu sendiri (Pra-munaqosah)
        $builder->where('IdTpq', $idTpq);

        // Filter Tahun
        if ($tahun !== null) {
            $builder->groupStart()
                        ->where('Tahun', $tahun)
                        ->orWhere('Tahun IS NULL', null, false)
                    ->groupEnd();
        }

        $builder->orderBy('Urutan', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Dapatkan versi (timestamp UNIX tertinggi dari UpdatedAt) untuk cache-busting.
     *
     * @param string $idTpq  ID TPQ spesifik atau '0'
     * @return int   UNIX timestamp atau 0 jika tidak ada data
     */
    public function getLatestVersion(string $idTpq = '0'): int
    {
        $builder = $this->db->table($this->table);
        $builder->selectMax('UNIX_TIMESTAMP(UpdatedAt)', 'latest_ts');
        $builder->where('IsActive', 1);

        $builder->where('IdTpq', $idTpq);

        $row = $builder->get()->getRowArray();

        return isset($row['latest_ts']) ? (int) $row['latest_ts'] : 0;
    }

    /**
     * Update kolom Urutan berdasarkan posisi (index) di dalam array $ids.
     *
     * @param int[] $ids  Array ID iklan berurutan sesuai urutan tampil yang diinginkan
     * @return bool
     */
    public function reorder(array $ids): bool
    {
        if (empty($ids)) {
            return true;
        }

        $this->db->transStart();

        foreach ($ids as $position => $id) {
            $this->db->table($this->table)
                     ->where('id', (int) $id)
                     ->update(['Urutan' => (int) $position]);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
