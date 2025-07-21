<?php
namespace App\Models;
use CodeIgniter\Model;
class JawabanPenggunaModel extends Model
{
    protected $table      = 'jawaban_pengguna';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kuesioner_id', 'pertanyaan_id', 'opsi_jawaban_id', 'jawaban_teks', 'ip_address', 'timestamp_isi'];
    protected $useTimestamps = false; 
    protected $createdField  = 'timestamp_isi'; 
    protected $updatedField  = false;
}