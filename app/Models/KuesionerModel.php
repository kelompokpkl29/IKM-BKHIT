<?php
namespace App\Models;
use CodeIgniter\Model;

class KuesionerModel extends Model
{
    protected $table      = 'kuesioner'; // Nama tabel di database
    protected $primaryKey = 'id';    // Nama kolom primary key
    protected $allowedFields = ['nama_kuesioner', 'deskripsi', 'is_active']; // Kolom yang diizinkan untuk diisi/diupdate

    protected $useTimestamps = true; // Mengaktifkan otomatisasi kolom created_at dan updated_at
    protected $createdField  = 'created_at'; // Nama kolom untuk timestamp pembuatan
    protected $updatedField  = 'updated_at'; // Nama kolom untuk timestamp pembaruan
}