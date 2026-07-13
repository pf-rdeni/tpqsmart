<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddThemeToTvDigitalTables extends Migration
{
    public function up()
    {
        $fields = [
            'Theme' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'dark',
                'after'      => 'NamaLink',
                'comment'    => 'Pilihan tema: dark, colorful, light',
            ],
        ];
        $this->forge->addColumn('tbl_infografis_link', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_infografis_link', 'Theme');
    }
}
