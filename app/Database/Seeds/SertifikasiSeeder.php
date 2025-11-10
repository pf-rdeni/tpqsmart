<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SertifikasiSeeder extends Seeder
{
    public function run()
    {
        $timestamp = date('Y-m-d H:i:s');

        // 1. Insert data tbl_sertifikasi_group_materi
        $groupMateriData = [
            [
                'IdGroupMateri' => 'GMS001',
                'NamaMateri' => 'Materi Pilihan Ganda',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdGroupMateri' => 'GMS002',
                'NamaMateri' => 'Materi Praktek Baca, Tulis dan sholat',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
        ];

        $groupMateriTable = $this->db->table('tbl_sertifikasi_group_materi');
        foreach ($groupMateriData as $data) {
            $exists = $groupMateriTable->where('IdGroupMateri', $data['IdGroupMateri'])->countAllResults();
            $groupMateriTable->resetQuery();

            if ($exists === 0) {
                $groupMateriTable->insert($data);
            }
        }

        // 2. Insert data tbl_sertifikasi_juri
        $juriData = [
            [
                'IdJuri' => 'JS001',
                'IdGroupMateri' => 'GMS001',
                'usernameJuri' => 'juri.materi.pg',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdJuri' => 'JS002',
                'IdGroupMateri' => 'GMS002',
                'usernameJuri' => 'juri.praktek.1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdJuri' => 'JS003',
                'IdGroupMateri' => 'GMS002',
                'usernameJuri' => 'juri.praktek.2',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdJuri' => 'JS004',
                'IdGroupMateri' => 'GMS002',
                'usernameJuri' => 'juri.praktek.3',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdJuri' => 'JS005',
                'IdGroupMateri' => 'GMS002',
                'usernameJuri' => 'juri.praktek.4',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdJuri' => 'JS006',
                'IdGroupMateri' => 'GMS002',
                'usernameJuri' => 'juri.praktek.5',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdJuri' => 'JS007',
                'IdGroupMateri' => 'GMS002',
                'usernameJuri' => 'juri.praktek.6',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
        ];

        $juriTable = $this->db->table('tbl_sertifikasi_juri');
        foreach ($juriData as $data) {
            $exists = $juriTable->where('IdJuri', $data['IdJuri'])->countAllResults();
            $juriTable->resetQuery();

            if ($exists === 0) {
                $juriTable->insert($data);
            }
        }

        // Catatan: Data tbl_sertifikasi_guru dan tbl_sertifikasi_nilai akan diinsert manual
        // sesuai dengan permintaan awal yang menyatakan "Insert data Guru akan dilakukan manual"
    }
}
