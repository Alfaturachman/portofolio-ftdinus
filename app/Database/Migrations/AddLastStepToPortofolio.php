<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Run:  php spark migrate
 *
 * Adds `last_step` to the `portofolio` table so the form can resume
 * from where the user left off.
 */
class AddLastStepToPortofolio extends Migration
{
    public function up()
    {
        // Add last_step column (default 1 = step pertama)
        $this->forge->addColumn('portofolio', [
            'last_step' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
                'default'    => 1,
                'after'      => 'id_perkuliahan',
                'comment'    => 'Step terakhir yang sudah disimpan (1-10)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('portofolio', 'last_step');
    }
}
