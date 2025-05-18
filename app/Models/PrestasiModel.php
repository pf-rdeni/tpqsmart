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
        $santriModel = new SantriModel();
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
