<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BobotNilaiSeeder extends Seeder
{
    /**
     * Generate a safe kategori ID from the given name.
     */
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
        $kategoriNames = ['SHOLAT', 'AYAT PILIHAN', 'SURAH PENDEK', 'DOA', 'IMLA', 'UMUM'];

        // Pastikan master kategori tersedia
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

        // Data default untuk bobot nilai
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

        $this->db->table('tbl_munaqosah_bobot_nilai')->insertBatch($bobotData);
    }
}
