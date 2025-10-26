<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GrupMateriUjiMunaqosahSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'IdGrupMateriUjian' => 'GM001',
                'NamaMateriGrup' => 'BACA QURAN',
                'Status' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdGrupMateriUjian' => 'GM002',
                'NamaMateriGrup' => 'TULIS QURAN',
                'Status' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdGrupMateriUjian' => 'GM003',
                'NamaMateriGrup' => 'PRAKTIK SHOLAT',
                'Status' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('tbl_munaqosah_grup_materi_uji')->insertBatch($data);
    }
}
