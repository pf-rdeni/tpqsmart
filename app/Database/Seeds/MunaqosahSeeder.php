<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MunaqosahSeeder extends Seeder
{
    public function run()
    {
        // Sample data untuk bobot nilai default
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

        $this->db->table('tbl_bobot_nilai_munaqosah')->insertBatch($bobotData);

        // Sample data untuk peserta munaqosah
        $pesertaData = [
            [
                'IdSantri' => 'SANTRI001',
                'IdTpq' => 'TPQ001',
                'IdTahunAjaran' => '2024/2025',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdSantri' => 'SANTRI002',
                'IdTpq' => 'TPQ001',
                'IdTahunAjaran' => '2024/2025',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'IdSantri' => 'SANTRI003',
                'IdTpq' => 'TPQ001',
                'IdTahunAjaran' => '2024/2025',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('tbl_peserta_munaqosah')->insertBatch($pesertaData);

        // Sample data untuk antrian munaqosah
        $antrianData = [
            [
                'NoPeserta' => 'PESERTA001',
                'IdTahunAjaran' => '2024/2025',
                'KategoriMateriUjian' => 'Iqra',
                'Status' => false,
                'Keterangan' => 'Antrian ujian Iqra',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'NoPeserta' => 'PESERTA002',
                'IdTahunAjaran' => '2024/2025',
                'KategoriMateriUjian' => 'Qur\'an',
                'Status' => true,
                'Keterangan' => 'Ujian Qur\'an selesai',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('tbl_munaqosah_antrian')->insertBatch($antrianData);

        // Sample data untuk nilai munaqosah
        $nilaiData = [
            [
                'NoPeserta' => 'PESERTA001',
                'IdSantri' => 'SANTRI001',
                'IdTpq' => 'TPQ001',
                'IdJuri' => 'GURU001',
                'IdTahunAjaran' => '2024/2025',
                'IdMateri' => 1,
                'KategoriMateriUjian' => 'Iqra',
                'TypeUjian' => 'munaqosah',
                'Nilai' => 85.50,
                'Catatan' => 'Sangat baik dalam membaca Iqra',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'NoPeserta' => 'PESERTA002',
                'IdSantri' => 'SANTRI002',
                'IdTpq' => 'TPQ001',
                'IdJuri' => 'GURU001',
                'IdTahunAjaran' => '2024/2025',
                'IdMateri' => 2,
                'KategoriMateriUjian' => 'Qur\'an',
                'TypeUjian' => 'pra-munaqosah',
                'Nilai' => 78.25,
                'Catatan' => 'Perlu latihan lebih dalam tajwid',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('tbl_nilai_munaqosah')->insertBatch($nilaiData);
    }
}
