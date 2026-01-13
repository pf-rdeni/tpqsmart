<?php

namespace App\Models;

use CodeIgniter\Model;


class PrestasiModel extends Model
{
    protected $table = 'tbl_prestasi'; // Update to your actual table name
    protected $primaryKey = 'id'; // Update to your actual primary key
    protected $allowedFields = [
        'IdSantri', 
        'IdTpq', 
        'IdTahunAjaran', 
        'IdKelas', 
        'IdGuru', 
        'IdMateriPelajaran', 
        'JenisPrestasi', 
        'Tingkatan', 
        'Status', 
        'Tanggal', 
        'Keterangan'
    ];

    public function getSantriWithPrestasi($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        // Ambil data santri per kelas
        $santriModel = new SantriBaruModel();
        $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

        // Load the PrestasiModel
        $prestasiModel = new PrestasiModel();

        // Loop through each Santri and get the last record for each IdMateriPelajaran
        foreach ($santriList as $key => $santri) {
            // Query to get the last record for each IdMateriPelajaran for the current Santri
            $lastPrestasiList = $prestasiModel
                ->select('tbl_prestasi.*, tbl_materi_pelajaran.NamaMateri, tbl_materi_pelajaran.Kategori') // Select fields from both tables
                ->join('tbl_materi_pelajaran', 'tbl_prestasi.IdMateriPelajaran = tbl_materi_pelajaran.IdMateri') // Join with MateriPelajaran table
                ->where('tbl_prestasi.IdSantri', $santri->IdSantri)
                ->whereIn('tbl_prestasi.Id', function ($builder) use ($santri) {
                    $builder->select('MAX(Id)')
                        ->from('tbl_prestasi')
                        ->where('IdSantri', $santri->IdSantri)
                        ->groupBy('IdMateriPelajaran');
                })
                ->orderBy('tbl_prestasi.updated_at', 'DESC')
                ->findAll();

            // Check tbl_prestasi.JenisPrestasi = Iqra atau Al-Quran

            // jika lastPrestasiList ada, maka ambil JenisPrestasi dari record pertama
            if (count($lastPrestasiList) > 0) {
                $JenisPrestasi = $lastPrestasiList[0]['JenisPrestasi'];
            } else {
                $JenisPrestasi = '';
            }
            // Append the list of last prestasi records (with MateriPelajaran data) to the current Santri record
            $santriList[$key]->lastPrestasiList = array_map(function($item) {
                return (object)$item;
            }, $lastPrestasiList);

        }
        

        return $santriList;
    }

    /**
     * OPTIMIZED: Get santri with prestasi using bulk operations
     * Mengurangi N+1 query problem dengan menggunakan single query dengan window functions
     * 
     * @param string $IdTpq
     * @param string $IdTahunAjaran
     * @param string $IdKelas
     * @param string $IdGuru
     * @return array
     */
    public function getSantriWithPrestasiOptimized($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        // Start database transaction for consistency
        $this->db->transStart();

        try {
            // 1. Get santri data
            $santriModel = new SantriBaruModel();
            $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

            if (empty($santriList)) {
                $this->db->transComplete();
                return [];
            }

            // 2. Extract santri IDs
            $santriIds = array_column($santriList, 'IdSantri');

            // 3. Get all prestasi data for all santri in one query using window function
            $prestasiQuery = "
                SELECT 
                    p.*,
                    mp.NamaMateri,
                    mp.Kategori,
                    ROW_NUMBER() OVER (
                        PARTITION BY p.IdSantri, p.IdMateriPelajaran 
                        ORDER BY p.updated_at DESC, p.Id DESC
                    ) as rn
                FROM tbl_prestasi p
                INNER JOIN tbl_materi_pelajaran mp ON p.IdMateriPelajaran = mp.IdMateri
                WHERE p.IdSantri IN (" . implode(',', array_map('intval', $santriIds)) . ")
            ";

            $allPrestasi = $this->db->query($prestasiQuery)->getResultArray();

            // 4. Filter to get only the latest record for each santri-materi combination
            $latestPrestasi = array_filter($allPrestasi, function ($item) {
                return $item['rn'] == 1;
            });

            // 5. Group prestasi by santri ID
            $prestasiBySantri = [];
            foreach ($latestPrestasi as $prestasi) {
                $prestasiBySantri[$prestasi['IdSantri']][] = (object)$prestasi;
            }

            // 6. Attach prestasi data to santri objects
            foreach ($santriList as $key => $santri) {
                $santriList[$key]->lastPrestasiList = $prestasiBySantri[$santri->IdSantri] ?? [];

                // Set JenisPrestasi from first record if exists
                if (!empty($santriList[$key]->lastPrestasiList)) {
                    $santriList[$key]->JenisPrestasi = $santriList[$key]->lastPrestasiList[0]->JenisPrestasi ?? '';
                } else {
                    $santriList[$key]->JenisPrestasi = '';
                }
            }

            $this->db->transComplete();

            return $santriList;
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Log error
            log_message('error', 'Error in getSantriWithPrestasiOptimized: ' . $e->getMessage());

            // Fallback to original method
            return $this->getSantriWithPrestasi($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);
        }
    }

    // buat fungsi untuk mengambil  data materi pelajaran getMateriPelajaran
    public function getMateriPelajaran($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        // Load getKelasMateriPelajaran dari helpfunctionModel
        $helpFunctionModel = new HelpFunctionModel();

        // Get the list of Materi Pelajaran return 
        $materiList = $helpFunctionModel->getKelasMateriPelajaran($IdKelas, $IdTpq);

        return $materiList;
    }
}
