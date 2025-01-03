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

    public function getSantriWithPrestasi($IdTahunAjaran, $IdGuru, $IdKelas)  
    {
        // Get the list of Santri
        $santriModel = new SantriModel(); 
        $santriList = $santriModel->GetDataSantriPerKelas($IdTahunAjaran, $IdKelas, $IdGuru);

        // Load the PrestasiModel
        $prestasiModel = new PrestasiModel(); 

        // Load the MateriPelajaranModel
        $materiPelajaranModel = new MateriPelajaranModel();

        // Loop through each Santri and get the last record for each IdMateriPelajaran
        foreach ($santriList as $key => $santri) {
            // Query to get the last record for each IdMateriPelajaran for the current Santri
            $lastPrestasiList = $prestasiModel
                ->select('tbl_prestasi.*, tbl_materi_pelajaran.NamaMateri, tbl_materi_pelajaran.Kategori') // Select fields from both tables
                ->join('tbl_materi_pelajaran', 'tbl_prestasi.IdMateriPelajaran = tbl_materi_pelajaran.IdMateri') // Join with MateriPelajaran table
                ->where('tbl_prestasi.IdSantri', $santri->IdSantri)
                //        ->where('tbl_prestasi.IdTahunAjaran', $IdTahunAjaran[0]) // Ensure it matches the academic year
                ->where('tbl_prestasi.Status !=', 'Selesai') // Exclude records where status is 'Selesai'
                ->groupBy('tbl_prestasi.IdMateriPelajaran')  // Group by IdMateriPelajaran to get different subjects
                ->orderBy('tbl_prestasi.IdMateriPelajaran', 'ASC')  // Optional: to maintain order by subject
                ->orderBy('tbl_prestasi.created_at', 'DESC')        // Ensure the latest record is selected
                ->findAll();  // Get all the latest records

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




}
