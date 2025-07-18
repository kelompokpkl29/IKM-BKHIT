<?php
namespace App\Models;
use CodeIgniter\Model;
class OpsiJawabanModel extends Model
{
    protected $table      = 'opsi_jawaban';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pertanyaan_id', 'opsi_teks', 'nilai'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}