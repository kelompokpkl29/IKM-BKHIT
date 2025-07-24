<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateIkmTables extends Migration
{
    public function up()
    {
        // Table: users (for admin)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'       => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');

        // Table: kuesioner
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_kuesioner' => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'      => ['type' => 'TEXT', 'null' => true],
            'is_active'      => ['type' => 'BOOLEAN', 'default' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('kuesioner');

        // Table: pertanyaan
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kuesioner_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'teks_pertanyaan'   => ['type' => 'TEXT'],
            'jenis_jawaban'     => ['type' => 'ENUM', 'constraint' => ['skala', 'pilihan_ganda', 'isian']],
            'urutan'            => ['type' => 'INT', 'constraint' => 11],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('kuesioner_id', 'kuesioner', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pertanyaan');

        // Table: opsi_jawaban
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pertanyaan_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'opsi_teks'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'nilai'          => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pertanyaan_id', 'pertanyaan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('opsi_jawaban');

        // Table: jawaban_pengguna
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kuesioner_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pertanyaan_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'opsi_jawaban_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'jawaban_teks'      => ['type' => 'TEXT', 'null' => true],
            'ip_address'        => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'timestamp_isi'     => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('kuesioner_id', 'kuesioner', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pertanyaan_id', 'pertanyaan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('opsi_jawaban_id', 'opsi_jawaban', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('jawaban_pengguna');
    }

    public function down()
    {
        $this->forge->dropTable('jawaban_pengguna');
        $this->forge->dropTable('opsi_jawaban');
        $this->forge->dropTable('pertanyaan');
        $this->forge->dropTable('kuesioner');
        $this->forge->dropTable('users');
    }
}