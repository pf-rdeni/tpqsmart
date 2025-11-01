<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MunaqosahSeeder extends Seeder
{
    protected function buildKategoriId(string $name): string
    {
        $id = strtoupper($name);
        $id = str_replace([' ', '\'', '"'], ['_', '', ''], $id);
        $id = preg_replace('/[^A-Z0-9_]/', '', $id);
        return 'KAT_' . $id;
    }

    public function run()
    {
        $timestamp = date('Y-m-d H:i:s');

        // Pastikan kategori utama tersedia
        $kategoriNames = ['SHOLAT', 'AYAT PILIHAN', 'SURAH PENDEK', 'DOA', 'IMLA', 'UMUM', 'Iqra', "Qur'an"];
        $kategoriTable = $this->db->table('tbl_munaqosah_kategori_materi');
        foreach ($kategoriNames as $name) {
            $kategoriId = $this->buildKategoriId($name);
            $exists = $kategoriTable->where('IdKategoriMateri', $kategoriId)->countAllResults();
            $kategoriTable->resetQuery();

            if ($exists === 0) {
                $kategoriTable->insert([
                    'IdKategoriMateri' => $kategoriId,
                    'NamaKategoriMateri' => $name,
                    'Status' => 'Aktif',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
        }

        // Sample data untuk bobot nilai default
        $bobotData = [
            [
                'IdTahunAjaran' => 'Default',
                'IdKategoriMateri' => $this->buildKategoriId('SHOLAT'),
                'NilaiBobot' => 30.00,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdTahunAjaran' => 'Default',
                'IdKategoriMateri' => $this->buildKategoriId('AYAT PILIHAN'),
                'NilaiBobot' => 10.00,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdTahunAjaran' => 'Default',
                'IdKategoriMateri' => $this->buildKategoriId('SURAH PENDEK'),
                'NilaiBobot' => 10.00,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdTahunAjaran' => 'Default',
                'IdKategoriMateri' => $this->buildKategoriId('DOA'),
                'NilaiBobot' => 10.00,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdTahunAjaran' => 'Default',
                'IdKategoriMateri' => $this->buildKategoriId('IMLA'),
                'NilaiBobot' => 10.00,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'IdTahunAjaran' => 'Default',
                'IdKategoriMateri' => $this->buildKategoriId('UMUM'),
                'NilaiBobot' => 30.00,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]
        ];

        $bobotTable = $this->db->table('tbl_munaqosah_bobot_nilai');
        if ($bobotTable->countAll() === 0) {
            $bobotTable->insertBatch($bobotData);
        }

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
                'IdKategoriMateri' => $this->buildKategoriId('Iqra'),
                'Status' => false,
                'Keterangan' => 'Antrian ujian Iqra',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'NoPeserta' => 'PESERTA002',
                'IdTahunAjaran' => '2024/2025',
                'IdKategoriMateri' => $this->buildKategoriId("Qur'an"),
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
                'IdKategoriMateri' => $this->buildKategoriId('Iqra'),
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
                'IdKategoriMateri' => $this->buildKategoriId("Qur'an"),
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
