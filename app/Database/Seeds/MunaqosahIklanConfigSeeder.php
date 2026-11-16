<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MunaqosahIklanConfigSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $table     = $this->db->table('tbl_munaqosah_konfigurasi');

        $configs = [
            [
                'IdTpq'        => 'default',
                'SettingKey'   => 'Monitoring_Iklan_Enabled',
                'SettingValue' => '0',
                'SettingType'  => 'boolean',
                'Description'  => 'Aktifkan tampilan iklan di monitoring antrian',
            ],
            [
                'IdTpq'        => 'default',
                'SettingKey'   => 'Monitoring_Iklan_Interval',
                'SettingValue' => '10',
                'SettingType'  => 'number',
                'Description'  => 'Durasi tampil per gambar iklan (detik)',
            ],
            [
                'IdTpq'        => 'default',
                'SettingKey'   => 'Monitoring_Iklan_Mode',
                'SettingValue' => 'loop',
                'SettingType'  => 'text',
                'Description'  => 'Mode putar iklan: loop, random, single',
            ],
            [
                'IdTpq'        => 'default',
                'SettingKey'   => 'Monitoring_Iklan_SingleId',
                'SettingValue' => '0',
                'SettingType'  => 'number',
                'Description'  => 'ID gambar iklan untuk mode single',
            ],
            [
                'IdTpq'        => 'default',
                'SettingKey'   => 'Monitoring_Iklan_VideoEnabled',
                'SettingValue' => '0',
                'SettingType'  => 'boolean',
                'Description'  => 'Aktifkan dukungan video MP4',
            ],
            [
                'IdTpq'        => 'default',
                'SettingKey'   => 'Monitoring_Header_Format',
                'SettingValue' => 'A',
                'SettingType'  => 'text',
                'Description'  => 'Format header strip: A (dual-column) atau B (pita bertingkat)',
            ],
            [
                'IdTpq'        => 'default',
                'SettingKey'   => 'Monitoring_Iklan_Fit',
                'SettingValue' => 'contain_blur',
                'SettingType'  => 'text',
                'Description'  => 'Penyesuaian rasio gambar: contain_blur, fill, cover, contain',
            ],
        ];

        foreach ($configs as $config) {
            // Cek duplikasi sebelum insert
            $exists = $table
                ->where('IdTpq', $config['IdTpq'])
                ->where('SettingKey', $config['SettingKey'])
                ->countAllResults();

            // Reset query builder agar tidak carry over kondisi ke iterasi berikutnya
            $table->resetQuery();

            if ($exists === 0) {
                $table->insert(array_merge($config, [
                    'CreatedAt' => $timestamp,
                    'UpdatedAt' => $timestamp,
                ]));

                // Reset setelah insert
                $table->resetQuery();
            }
        }
    }
}
