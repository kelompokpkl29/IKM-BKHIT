<?php

namespace App\Models;

use CodeIgniter\Model;

class OpsiJawabanModel extends Model
{
    protected $table      = 'opsi_jawaban'; // Nama tabel di database
    protected $primaryKey = 'id';    // Nama kolom primary key
    protected $allowedFields = ['pertanyaan_id', 'opsi_teks', 'nilai']; // Kolom yang diizinkan untuk diisi/diupdate

    protected $useTimestamps = true; // Mengaktifkan otomatisasi kolom created_at dan updated_at
    protected $createdField  = 'created_at'; // Nama kolom untuk timestamp pembuatan
    protected $updatedField  = 'updated_at'; // Nama kolom untuk timestamp pembaruan
}
