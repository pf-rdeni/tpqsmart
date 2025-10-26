<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BobotNilaiSeeder extends Seeder
{
    public function run()
    {
        // Data default untuk bobot nilai
        $bobotData = [
            [
                'IdTahunAjaran' => 'Default',
                'KategoriMateriUjian' => 'SHOLAT',
                'NilaiBobot' => 30.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdTahunAjaran' => 'Default',
                'KategoriMateriUjian' => 'AYAT PILIHAN',
                'NilaiBobot' => 10.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdTahunAjaran' => 'Default',
                'KategoriMateriUjian' => 'SURAH PENDEK',
                'NilaiBobot' => 10.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdTahunAjaran' => 'Default',
                'KategoriMateriUjian' => 'DOA',
                'NilaiBobot' => 10.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdTahunAjaran' => 'Default',
                'KategoriMateriUjian' => 'IMLA',
                'NilaiBobot' => 10.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdTahunAjaran' => 'Default',
                'KategoriMateriUjian' => 'UMUM',
                'NilaiBobot' => 30.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('tbl_munaqosah_bobot_nilai')->insertBatch($bobotData);
    }
}
