<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePemetaanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_portofolio' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'id_cpl' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_cpmk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_sub_cpmk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_portofolio', 'id_cpl', 'id_cpmk', 'id_sub_cpmk']);
        $this->forge->addForeignKey('id_portofolio', 'portofolio', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_cpl', 'cpl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_cpmk', 'cpmk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_sub_cpmk', 'sub_cpmk', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('pemetaan');
    }

    public function down()
    {
        $this->forge->dropTable('pemetaan');
    }
}
