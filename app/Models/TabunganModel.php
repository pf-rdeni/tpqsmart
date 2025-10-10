<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * TabunganModel - Optimized for bulk operations
 * 
 * This model has been optimized to reduce database queries by using bulk operations
 * instead of individual loops. The main optimizations include:
 * 
 * 1. getSaldoTabunganSantriBulk() - Single query to get total saldo for all santri
 * 2. getSantriWithBalanceBulk() - Single query to get santri list with balances
 * 3. calculateBalanceBulk() - Single query to calculate balances for multiple santri
 * 
 * Performance improvements:
 * - Before: N+1 queries (1 query to get santri list + N queries for each santri balance)
 * - After: 1 single query with JOINs and aggregation
 * 
 * This can improve performance by 10-100x depending on the number of santri.
 */

class TabunganModel extends Model
{
    protected $table = 'tbl_tabungan_santri';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'IdSantri',
        'IdKelas',
        'IdTpq',
        'IdTahunAjaran',
        'IdGuru',
        'TanggalTransaksi',
        'JenisTransaksi',
        'Nominal',
        'Keterangan',
        'CreatedAt',
        'UpdatedAt'
    ];

    // If you want to automatically handle createdAt and updatedAt fields
    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    // Method to retrieve all transactions for a specific Santri
    public function getTabunganBySantri($idSantri)
    {
        return $this->where('IdSantri', $idSantri)->findAll();
    }

    /**
     * Optimized method to calculate balance for multiple santri at once
     * Returns array with IdSantri as key and balance as value
     */
    public function calculateBalanceBulk($santriIds)
    {
        if (empty($santriIds)) {
            return [];
        }

        $builder = $this->db->table('tbl_tabungan_santri');
        $builder->select('
            IdSantri,
            SUM(
                CASE 
                    WHEN JenisTransaksi = "Setoran" THEN Nominal 
                    WHEN JenisTransaksi = "Penarikan" THEN -Nominal 
                    ELSE 0 
                END
            ) as balance
        ');

        if (is_array($santriIds)) {
            $builder->whereIn('IdSantri', $santriIds);
        } else {
            $builder->where('IdSantri', $santriIds);
        }

        $builder->groupBy('IdSantri');

        $results = $builder->get()->getResult();

        // Convert to associative array
        $balances = [];
        foreach ($results as $result) {
            $balances[$result->IdSantri] = $result->balance ?? 0;
        }

        return $balances;
    }

    // Method to calculate the balance of a specific Santri
    public function calculateBalance($idSantri)
    {
        // Use bulk method for single santri for consistency
        $balances = $this->calculateBalanceBulk([$idSantri]);
        return $balances[$idSantri] ?? 0;
    }

    /**
     * Optimized method to get santri list with their balance using bulk query
     * This method uses a single query with JOIN instead of multiple individual queries
     */
    public function getSantriWithBalanceBulk($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        $builder = $this->db->table('tbl_kelas_santri ks');

        // Build the query with JOINs to get all required data in one go
        $builder->select('
            ks.Id,
            ks.IdTahunAjaran,
            k.IdKelas,
            k.NamaKelas,
            g.IdGuru,
            g.Nama AS GuruNama,
            s.IdSantri,
            s.NamaSantri,
            s.JenisKelamin,
            t.IdTpq,
            t.NamaTpq,
            t.Alamat,
            w.IdJabatan,
            SUM(
                CASE 
                    WHEN tab.JenisTransaksi = "Setoran" THEN tab.Nominal 
                    WHEN tab.JenisTransaksi = "Penarikan" THEN -tab.Nominal 
                    ELSE 0 
                END
            ) as Balance
        ');

        // Join with all required tables
        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_tpq t', 'ks.IdTpq = t.IdTpq', 'left');
        $builder->join('tbl_guru_kelas w', 'w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq', 'left');
        $builder->join('tbl_guru g', 'w.IdGuru = g.IdGuru', 'left');
        $builder->join('tbl_tabungan_santri tab', 'tab.IdSantri = s.IdSantri', 'left');

        // Apply filters
        $builder->where('s.Active', 1);

        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('ks.IdTahunAjaran', (array)$IdTahunAjaran);
            } else {
                $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
            }
        }

        if ($IdGuru !== null && $IdGuru != 0) {
            $builder->where('w.IdGuru', $IdGuru);
        }

        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builder->whereIn('k.IdKelas', (array)$IdKelas);
            } else {
                $builder->where('k.IdKelas', $IdKelas);
            }
        }

        $builder->where('ks.IdTpq', $IdTpq);
        $builder->groupBy('s.IdSantri');
        $builder->orderBy('s.NamaSantri', 'ASC');

        return $builder->get()->getResult();
    }

    // Method to retrive list all dataSantri with their balance in tabungan table join with tbl_kelas_santri and tbl_kelas and tbl_guru_kelas filter by IdTahunAjaran and IdGuru IdKelas and IdTpq and IdSantri = null
    public function getSantriWithBalance($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        // Use the optimized bulk method instead of individual calculations
        return $this->getSantriWithBalanceBulk($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
    }

    /**
     * Optimized method to get total tabungan saldo using bulk query
     * This method uses a single query with JOIN instead of multiple individual queries
     * 
     * @param mixed $IdTpq ID TPQ
     * @param mixed $IdTahunAjaran ID Tahun Ajaran (can be array or single value)
     * @param mixed $IdKelas ID Kelas (can be array or single value)
     * @param mixed $IdGuru ID Guru (can be null)
     * @return float Total saldo tabungan
     * 
     * Performance: Single query instead of N+1 queries
     * Example: For 50 santri, reduces from 51 queries to 1 query
     */
    public function getSaldoTabunganSantriBulk($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        $builder = $this->db->table('tbl_kelas_santri ks');

        // Build the query with JOINs to get all required data in one go
        $builder->select('
            SUM(
                CASE 
                    WHEN t.JenisTransaksi = "Setoran" THEN t.Nominal 
                    WHEN t.JenisTransaksi = "Penarikan" THEN -t.Nominal 
                    ELSE 0 
                END
            ) as total_saldo
        ');

        // Join with tabungan table to get all transactions
        $builder->join('tbl_tabungan_santri t', 't.IdSantri = ks.IdSantri', 'left');

        // Join with other required tables
        $builder->join('tbl_kelas k', 'ks.IdKelas = k.IdKelas', 'left');
        $builder->join('tbl_santri_baru s', 'ks.IdSantri = s.IdSantri', 'left');
        $builder->join('tbl_guru_kelas w', 'w.IdKelas = k.IdKelas AND w.IdTpq = ks.IdTpq', 'left');

        // Apply filters
        $builder->where('ks.IdTpq', $IdTpq);
        $builder->where('s.Active', 1);

        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('ks.IdTahunAjaran', (array)$IdTahunAjaran);
            } else {
                $builder->where('ks.IdTahunAjaran', $IdTahunAjaran);
            }
        }

        if ($IdGuru !== null && $IdGuru != 0) {
            $builder->where('w.IdGuru', $IdGuru);
        }

        if (!empty($IdKelas)) {
            if (is_array($IdKelas)) {
                $builder->whereIn('k.IdKelas', (array)$IdKelas);
            } else {
                $builder->where('k.IdKelas', $IdKelas);
            }
        }

        $result = $builder->get()->getRow();
        return $result->total_saldo ?? 0;
    }

    // fungsi untuk mengambil saldo individual santri atau semua santri ketika sntri != null Total saldo adalah total kategori setoran - total kategori penarikan
    public function getSaldoTabunganSantri($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        // Use the optimized bulk method instead of individual calculations
        return $this->getSaldoTabunganSantriBulk($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
    }
}
