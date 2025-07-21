<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
// Sesuaikan import ini dengan versi CodeIgniter Anda (StrictRules atau tidak)
// Berdasarkan screenshot Anda sebelumnya, Anda menggunakan StrictRules.
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

// Untuk aturan kustom seperti validateUser dan check_old_password
// Pastikan Anda juga memiliki Models/UserModel.php
use App\Models\UserModel; 

class Validation extends BaseConfig
{
    public array $ruleSets = [
        Rules::class,          // Ini mendaftarkan aturan bawaan seperti 'required', 'array', 'min_length', dll.
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --- Aturan Validasi Kustom (yang kita buat sebelumnya) ---
    // Ini diperlukan oleh AuthController dan AdminController
    public function validateUser(string $password, string $fields, array $data, ?string &$error = null): bool
    {
        $userModel = new UserModel();
        $username = $data['username'] ?? null;
        $user = $userModel->where('username', $username)->first();

        if (!$user) {
            $error = 'Username atau password tidak cocok.';
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            $error = 'Username atau password tidak cocok.';
            return false;
        }
        return true;
    }

    public function check_old_password(string $inputPassword, string $field, array $data, ?string &$error = null): bool
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            $error = 'Pengguna tidak ditemukan.';
            return false;
        }
        if (!password_verify($inputPassword, $user['password'])) {
            $error = 'Password lama salah.';
            return false;
        }
        return true;
    }
}