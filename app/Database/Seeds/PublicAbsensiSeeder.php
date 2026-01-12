<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PublicAbsensiSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('tbl_absensi_santri_link')->insert([
            'IdTpq' => 1, // Adjust based on active TPQ
            'IdTahunAjaran' => 1, // Adjust based on active Tahun Ajaran
            'HashKey' => 'public-absen-test',
            'CreatedAt' => date('Y-m-d H:i:s'),
        ]);
        
        echo "Seeder PublicAbsensiSeeder run successfully. HashKey: public-absen-test\n";
    }
}
