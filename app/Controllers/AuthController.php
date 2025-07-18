<?php
namespace App\Controllers;
use App\Models\UserModel;
class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }
        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // --- DEBUGGING SINI ---
        echo "Mencoba login dengan Username: " . esc($username) . " dan Password: " . esc($password) . "<br>";
        // --- DEBUGGING SINI ---

        // Cari user di database
        $user = $this->userModel->where('username', $username)->first();

        if ($user) {
            echo "User ditemukan di DB: " . esc($user['username']) . "<br>";
            echo "Hash dari DB: " . esc($user['password']) . "<br>";
            // Verifikasi password yang diinput dengan hash dari DB
            $password_matches = password_verify($password, $user['password']);
            echo "Hasil password_verify(): " . ($password_matches ? "TRUE" : "FALSE") . "<br>";
            // --- DEBUGGING SINI ---
            if ($password_matches) {
                echo "Password Cocok! Data Sesi yang akan diatur:<br>";
                dd(['user_id' => $user['id'], 'username' => $user['username'], 'logged_in' => TRUE]);
            } else {
                dd("Password TIDAK cocok untuk user " . esc($user['username']));
            }
            // --- DEBUGGING SINI ---

            if ($password_matches) {
                $ses_data = [
                    'user_id'   => $user['id'],
                    'username'  => $user['username'],
                    'email'     => $user['email'],
                    'logged_in' => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to(base_url('admin/dashboard'))->with('success', 'Selamat datang, ' . $user['username'] . '!');
            } else {
                $session->setFlashdata('error', 'Username atau Password salah.');
                return redirect()->back()->withInput();
            }
        } else {
            // --- DEBUGGING SINI ---
            dd("User '" . esc($username) . "' tidak ditemukan di database.");
            // --- DEBUGGING SINI ---
            $session->setFlashdata('error', 'Username atau Password salah.');
            return redirect()->back()->withInput();
        }
    }
    // ... (fungsi logout) ...
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}