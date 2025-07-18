<?php
namespace App\Models;
use CodeIgniter\Model;
class KuesionerModel extends Model
{
    protected $table      = 'kuesioner';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_kuesioner', 'deskripsi', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}