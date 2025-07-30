<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users'; // Nama tabel di database
    protected $primaryKey = 'id';    // Nama kolom primary key
    protected $allowedFields = ['username', 'password', 'email']; // Kolom yang diizinkan untuk diisi/diupdate

    protected $useTimestamps = true; // Mengaktifkan otomatisasi kolom created_at dan updated_at
    protected $createdField  = 'created_at'; // Nama kolom untuk timestamp pembuatan
    protected $updatedField  = 'updated_at'; // Nama kolom untuk timestamp pembaruan

    // Callbacks yang dijalankan sebelum operasi insert atau update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            // Menggunakan password_hash untuk mengenkripsi password
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}