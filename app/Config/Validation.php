<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
// PENTING: Perhatikan penggunaan backslash (\) sebagai pemisah namespace.
// Ini harus 'CodeIgniter\Validation\StrictRules\...'
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

use App\Models\UserModel; // Pastikan ini diimpor untuk aturan kustom Anda

class Validation extends BaseConfig
{
    public array $ruleSets = [
        Rules::class,          // Ini mendaftarkan aturan dasar seperti 'required', 'array', 'min_length', dll.
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --- Aturan Validasi Kustom ---
    // Digunakan di AuthController untuk validasi login
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

    // Digunakan di AdminController untuk update profil (memeriksa password lama)
    public function check_old_password(string $inputPassword, string $field, array $data, ?string &$error = null): bool
    {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            // Jika user tidak ditemukan di sesi, anggap password lama tidak cocok
            $error = 'Password lama tidak cocok.';
            return false;
        }
        if (!password_verify($inputPassword, $user['password'])) {
            $error = 'Password lama tidak cocok.';
            return false;
        }
        return true;
    }
}