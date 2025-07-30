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

        // NEW TABLE: respondents (untuk data demografi responden)
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'response_session_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true], // UUID dari sesi
            'age_group_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'gender_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'education_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'occupation_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'service_type_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'ip_address'          => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'submission_timestamp' => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        // Menambahkan foreign key ke opsi_jawaban untuk setiap demografi
        $this->forge->addForeignKey('age_group_id', 'opsi_jawaban', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('gender_id', 'opsi_jawaban', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('education_id', 'opsi_jawaban', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('occupation_id', 'opsi_jawaban', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('service_type_id', 'opsi_jawaban', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('respondents');


        // Table: jawaban_pengguna
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kuesioner_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pertanyaan_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'opsi_jawaban_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'jawaban_teks'      => ['type' => 'TEXT', 'null' => true],
            'ip_address'        => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'response_session_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // Untuk multi-page, akan jadi penghubung ke respondents
            'timestamp_isi'     => ['type' => 'DATETIME', 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('kuesioner_id', 'kuesioner', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pertanyaan_id', 'pertanyaan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('opsi_jawaban_id', 'opsi_jawaban', 'id', 'SET NULL', 'CASCADE');
        // Optional: Foreign key from jawaban_pengguna.response_session_id to respondents.response_session_id
        // $this->forge->addForeignKey('response_session_id', 'respondents', 'response_session_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jawaban_pengguna');


        // NEW TABLE: ikm_pdf_data (untuk data ringkasan mirip dari PDF)
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // "Name" dari PDF
            'date_recorded'     => ['type' => 'DATE', 'null' => true], // "Date" dari PDF
            'timestamp_recorded'=> ['type' => 'DATETIME', 'null' => true], // "Timestamp" dari PDF
            'team_name'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // "Team Name" dari PDF
            'total_score'       => ['type' => 'INT', 'constraint' => 11, 'null' => true], // "Total Score" dari PDF
            // Kolom aspek generik (contoh, berdasarkan '1's di PDF)
            'aspect_a'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'aspect_b'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'aspect_c'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'aspect_d'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'aspect_e'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'aspect_f'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'aspect_g'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'aspect_h'          => ['type' => 'INT', 'constraint' => 1, 'null' => true], 
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('ikm_pdf_data');

    }

    public function down()
    {
        $this->forge->dropTable('ikm_pdf_data');
        $this->forge->dropTable('jawaban_pengguna');
        $this->forge->dropTable('respondents');
        $this->forge->dropTable('opsi_jawaban');
        $this->forge->dropTable('pertanyaan');
        $this->forge->dropTable('kuesioner');
        $this->forge->dropTable('users');
    }
}