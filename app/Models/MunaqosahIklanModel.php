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
     * Dapatkan versi tanda tangan data aktif untuk deteksi perubahan real-time (tambah, edit, hapus, status aktif/nonaktif, urutan).
     *
     * @param string      $idTpq  ID TPQ spesifik atau '0'
     * @param string|null $tahun  Tahun ajaran atau null
     * @return string     Hash versi unik
     */
    public function getLatestVersion(string $idTpq = '0', ?string $tahun = null): string
    {
        $builder = $this->db->table($this->table);
        $builder->where('IdTpq', $idTpq);
        $builder->where('IsActive', 1);

        if ($tahun !== null) {
            $builder->groupStart()
                        ->where('Tahun', $tahun)
                        ->orWhere('Tahun IS NULL', null, false)
                    ->groupEnd();
        }

        $builder->select("COUNT(*) as total, MAX(UNIX_TIMESTAMP(UpdatedAt)) as latest_ts, GROUP_CONCAT(CONCAT(id, '_', UNIX_TIMESTAMP(UpdatedAt), '_', Urutan) ORDER BY Urutan ASC) as signature");
        $row = $builder->get()->getRowArray();

        if (!$row || empty($row['total'])) {
            return 'empty_0';
        }

        return md5($row['total'] . '|' . ($row['latest_ts'] ?? 0) . '|' . ($row['signature'] ?? ''));
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
                     ->update([
                         'Urutan'    => (int) ($position + 1),
                         'UpdatedAt' => date('Y-m-d H:i:s')
                     ]);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
