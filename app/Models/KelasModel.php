<?php

namespace App\Models;
use CodeIgniter\Model;

class KelasModel extends Model
{
    // Define the table name
    protected $table = 'tbl_kelas_santri';

    // Define the primary key
    protected $primaryKey = 'Id';

    // Fields that can be manipulated (inserted or updated)
    protected $allowedFields = [
        'IdKelas', 
        'IdTpq', 
        'IdSantri', 
        'IdTahunAjaran', 
        'Status',
        'created_at', 
        'updated_at'
    ];

    // Enable automatic handling of created_at and updated_at fields
    protected $useTimestamps = true;

    // Define the created and updated fields
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mendapatkan data duplikasi IdSantri di tbl_kelas_santri
     * Duplikasi terjadi jika IdSantri yang sama memiliki IdKelas yang sama pada IdTahunAjaran yang sama dan IdTpq yang sama
     * @param mixed $IdTpq
     * @return array
     */
    public function getDuplikasiKelasSantri($IdTpq = null)
    {
        $builder = $this->db->table('tbl_kelas_santri ks');
        $builder->select('
            ks.IdSantri,
            ks.IdKelas,
            ks.IdTahunAjaran,
            ks.IdTpq,
            COUNT(*) as jumlah_duplikasi,
            GROUP_CONCAT(ks.Id ORDER BY ks.Id) as list_id
        ');
        $builder->groupBy('ks.IdSantri, ks.IdKelas, ks.IdTahunAjaran, ks.IdTpq');
        $builder->having('COUNT(*) >', 1);

        if ($IdTpq != null && $IdTpq != 0) {
            $builder->where('ks.IdTpq', $IdTpq);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan detail duplikasi dengan informasi santri, kelas, dan TPQ
     * @param array $duplikasi Array hasil dari getDuplikasiKelasSantri
     * @return array
     */
    public function getDetailDuplikasiKelasSantri($duplikasi)
    {
        $result = [];

        foreach ($duplikasi as $dup) {
            // Ambil semua record yang duplikasi
            $builderDetail = $this->db->table('tbl_kelas_santri ks');
            $builderDetail->select('
                ks.Id,
                ks.IdSantri,
                ks.IdKelas,
                ks.IdTahunAjaran,
                ks.IdTpq,
                ks.Status,
                ks.created_at,
                ks.updated_at,
                s.NamaSantri,
                k.NamaKelas,
                t.NamaTpq
            ');
            $builderDetail->join('tbl_santri_baru s', 's.IdSantri = ks.IdSantri', 'left');
            $builderDetail->join('tbl_kelas k', 'k.IdKelas = ks.IdKelas', 'left');
            $builderDetail->join('tbl_tpq t', 't.IdTpq = ks.IdTpq', 'left');
            $builderDetail->where('ks.IdSantri', $dup['IdSantri']);
            $builderDetail->where('ks.IdKelas', $dup['IdKelas']);
            $builderDetail->where('ks.IdTahunAjaran', $dup['IdTahunAjaran']);
            $builderDetail->where('ks.IdTpq', $dup['IdTpq']);
            $builderDetail->orderBy('ks.Id', 'ASC');

            $detail = $builderDetail->get()->getResultArray();

            $result[] = [
                'IdSantri' => $dup['IdSantri'],
                'NamaSantri' => $detail[0]['NamaSantri'] ?? 'Tidak ditemukan',
                'IdKelas' => $dup['IdKelas'],
                'NamaKelas' => $detail[0]['NamaKelas'] ?? 'Tidak ditemukan',
                'IdTahunAjaran' => $dup['IdTahunAjaran'],
                'IdTpq' => $dup['IdTpq'],
                'NamaTpq' => $detail[0]['NamaTpq'] ?? 'Tidak ditemukan',
                'jumlah_duplikasi' => $dup['jumlah_duplikasi'],
                'detail' => $detail
            ];
        }

        return $result;
    }

    /**
     * Menormalisasi duplikasi dengan menghapus record duplikasi
     * Menyisakan 1 record per kombinasi IdSantri, IdKelas, IdTahunAjaran, IdTpq
     * Menyisakan record dengan Id terkecil (terlama)
     * @param mixed $IdTpq
     * @return array ['total_groups' => int, 'total_deleted' => int]
     */
    public function normalisasiDuplikasiKelasSantri($IdTpq = null)
    {
        // Ambil data duplikasi
        $duplikasi = $this->getDuplikasiKelasSantri($IdTpq);

        $totalDeleted = 0;
        $totalGroups = 0;

        foreach ($duplikasi as $dup) {
            // Ambil semua ID yang duplikasi
            $builderDetail = $this->db->table('tbl_kelas_santri');
            $builderDetail->select('Id');
            $builderDetail->where('IdSantri', $dup['IdSantri']);
            $builderDetail->where('IdKelas', $dup['IdKelas']);
            $builderDetail->where('IdTahunAjaran', $dup['IdTahunAjaran']);
            $builderDetail->where('IdTpq', $dup['IdTpq']);
            $builderDetail->orderBy('Id', 'ASC');

            $allIds = $builderDetail->get()->getResultArray();
            $allIdValues = array_column($allIds, 'Id');

            // Hapus semua kecuali yang tertua (Id terkecil)
            if (count($allIdValues) > 1) {
                $idTertua = min($allIdValues);
                $idsToDelete = array_filter($allIdValues, function ($id) use ($idTertua) {
                    return $id != $idTertua;
                });

                if (!empty($idsToDelete)) {
                    $deleted = $this->whereIn('Id', $idsToDelete)->delete();
                    $totalDeleted += $deleted;
                    $totalGroups++;
                }
            }
        }

        return [
            'total_groups' => $totalGroups,
            'total_deleted' => $totalDeleted
        ];
    }

    /**
     * Menormalisasi duplikasi untuk data yang dipilih saja
     * Menyisakan 1 record per kombinasi IdSantri, IdKelas, IdTahunAjaran, IdTpq
     * Menyisakan record dengan Id terkecil (terlama)
     * @param array $selectedData Array data duplikasi yang dipilih
     * @return array ['total_groups' => int, 'total_deleted' => int]
     */
    public function normalisasiDuplikasiKelasSantriSelected($selectedData)
    {
        $totalDeleted = 0;
        $totalGroups = 0;

        foreach ($selectedData as $item) {
            // Ambil semua ID yang duplikasi untuk item ini
            $builderDetail = $this->db->table('tbl_kelas_santri');
            $builderDetail->select('Id');
            $builderDetail->where('IdSantri', $item['IdSantri']);
            $builderDetail->where('IdKelas', $item['IdKelas']);
            $builderDetail->where('IdTahunAjaran', $item['IdTahunAjaran']);
            $builderDetail->where('IdTpq', $item['IdTpq']);
            $builderDetail->orderBy('Id', 'ASC');

            $allIds = $builderDetail->get()->getResultArray();
            $allIdValues = array_column($allIds, 'Id');

            // Hapus semua kecuali yang tertua (Id terkecil)
            if (count($allIdValues) > 1) {
                $idTertua = min($allIdValues);
                $idsToDelete = array_filter($allIdValues, function ($id) use ($idTertua) {
                    return $id != $idTertua;
                });

                if (!empty($idsToDelete)) {
                    $deleted = $this->whereIn('Id', $idsToDelete)->delete();
                    $totalDeleted += $deleted;
                    $totalGroups++;
                }
            }
        }

        return [
            'total_groups' => $totalGroups,
            'total_deleted' => $totalDeleted
        ];
    }

    /**
     * Mendapatkan data kelas dan jumlah santri berdasarkan tahun ajaran
     * @param mixed $IdTpq
     * @param array $tahunAjaran Array tahun ajaran yang akan difilter
     * @return array
     */
    public function getKelasPerTahunAjaran($IdTpq = null, $tahunAjaran = [])
    {
        $builder = $this->select('tbl_kelas_santri.IdTahunAjaran, tbl_kelas_santri.IdKelas, tbl_kelas.NamaKelas, COUNT(tbl_kelas_santri.IdSantri) AS SumIdKelas')
            ->join('tbl_kelas', 'tbl_kelas_santri.IdKelas = tbl_kelas.IdKelas')
            ->groupBy('tbl_kelas_santri.IdTahunAjaran, tbl_kelas_santri.IdKelas')
            ->orderBy('tbl_kelas_santri.IdTahunAjaran', 'ASC')
            ->orderBy('tbl_kelas_santri.IdKelas', 'ASC')
            ->where('tbl_kelas_santri.status', true);

        if ($IdTpq != null && $IdTpq != 0) {
            $builder->where('tbl_kelas_santri.IdTpq', $IdTpq);
        }

        if (!empty($tahunAjaran)) {
            $builder->whereIn('tbl_kelas_santri.IdTahunAjaran', $tahunAjaran);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan list santri berdasarkan tahun ajaran dan kelas
     * @param mixed $idTahunAjaran
     * @param mixed $idKelas
     * @param mixed $IdTpq
     * @return array
     */
    public function getSantriByTahunAjaranDanKelas($idTahunAjaran, $idKelas, $IdTpq = null)
    {
        $builder = $this->where('IdTahunAjaran', $idTahunAjaran)
            ->where('IdKelas', $idKelas)
            ->where('Status', 1);

        if (!empty($IdTpq) && $IdTpq != 0) {
            $builder->where('IdTpq', $IdTpq);
        }

        return $builder->findAll();
    }

    /**
     * Update status kelas lama menjadi tidak aktif
     * @param array $ids Array ID yang akan diupdate
     * @return bool
     */
    public function updateStatusKelasLama($ids)
    {
        if (empty($ids)) {
            return false;
        }

        return $this->whereIn('Id', $ids)->set(['Status' => 0])->update();
    }

    /**
     * Insert batch data kelas baru
     * @param array $data Array data kelas baru
     * @return bool|int
     */
    public function insertKelasBaruBatch($data)
    {
        if (empty($data)) {
            return false;
        }

        return $this->insertBatch($data);
    }

    /**
     * Mendapatkan semua IdKelas yang memiliki santri aktif di TPQ tertentu
     * @param mixed $IdTpq
     * @return array Array of IdKelas
     */
    public function getAllKelasAktifByTpq($IdTpq)
    {
        $builder = $this->db->table('tbl_kelas_santri');
        $builder->select('IdKelas')
            ->distinct()
            ->where('IdTpq', $IdTpq)
            ->where('status', true);

        $result = $builder->get()->getResultArray();
        return array_column($result, 'IdKelas');
    }
}
