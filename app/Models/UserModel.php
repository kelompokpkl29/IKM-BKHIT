<?php
namespace App\Models;
use CodeIgniter\Model;
class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'email'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Callback untuk hashing password sebelum insert/update (PENTING untuk keamanan)
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}