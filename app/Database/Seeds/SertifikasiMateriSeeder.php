<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SertifikasiMateriSeeder extends Seeder
{
    public function run()
    {
        $timestamp = date('Y-m-d H:i:s');

        // Insert data tbl_sertifikasi_materi
        // Catatan: IdGrupMateri harus sesuai dengan IdGroupMateri di tbl_sertifikasi_group_materi (GMS001, GMS002)
        $materiData = [
            [
                'IdMateri' => 'SM001',
                'NamaMateri' => 'Materi Pilihan Ganda',
                'IdGrupMateri' => 'GMS001', // Sesuai dengan IdGroupMateri di tbl_sertifikasi_group_materi
                'Status' => 'Aktif',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdMateri' => 'SM002',
                'NamaMateri' => 'Baca Al-Quran',
                'IdGrupMateri' => 'GMS002', // Sesuai dengan IdGroupMateri di tbl_sertifikasi_group_materi
                'Status' => 'Aktif',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdMateri' => 'SM003',
                'NamaMateri' => 'Praktek Sholat',
                'IdGrupMateri' => 'GMS002', // Sesuai dengan IdGroupMateri di tbl_sertifikasi_group_materi
                'Status' => 'Aktif',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdMateri' => 'SM004',
                'NamaMateri' => 'Tulis Al-Quran',
                'IdGrupMateri' => 'GMS002', // Sesuai dengan IdGroupMateri di tbl_sertifikasi_group_materi
                'Status' => 'Aktif',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
        ];

        $materiTable = $this->db->table('tbl_sertifikasi_materi');
        foreach ($materiData as $data) {
            $exists = $materiTable->where('IdMateri', $data['IdMateri'])->countAllResults();
            $materiTable->resetQuery();

            if ($exists === 0) {
                $materiTable->insert($data);
            }
        }
    }
}
