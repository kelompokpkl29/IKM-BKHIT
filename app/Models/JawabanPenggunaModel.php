<?php

namespace App\Models;

use CodeIgniter\Model;

class JawabanPenggunaModel extends Model
{
    protected $table      = 'jawaban_pengguna'; // Nama tabel di database
    protected $primaryKey = 'id';    // Nama kolom primary key
    // Kolom-kolom yang diizinkan untuk diisi/diupdate
    protected $allowedFields = ['kuesioner_id', 'pertanyaan_id', 'opsi_jawaban_id', 'jawaban_teks', 'ip_address', 'response_session_id', 'timestamp_isi'];

    // Kami menonaktifkan otomatisasi timestamp di sini
    // karena 'timestamp_isi' diatur secara manual di Controller agar konsisten.
    protected $useTimestamps = false;
    protected $createdField  = 'timestamp_isi';
    protected $updatedField  = false;
}
