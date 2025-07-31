<?php

namespace App\Controllers;

use App\Models\UserModel; // Pastikan ini diimpor untuk menggunakan UserModel

// Pastikan BaseController Anda tidak memiliki import CodeIgniter\Controller yang duplikat
// Jika Anda mengimplementasikan BaseController Anda sendiri dan mengimpor Controller di sana,
// maka tidak perlu import di sini. Jika tidak, tambahkan:
// use CodeIgniter\Controller; 

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // Pastikan helper ini dimuat untuk fungsi-fungsi seperti base_url(), session(), getPost()
        helper(['form', 'url', 'session']);
    }

    public function login()
    {
        // Jika pengguna sudah login (ada data 'logged_in' di sesi), arahkan ke dashboard admin
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }
        // Jika belum login, tampilkan halaman login
        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session(); // Ambil instance sesi
        $username = $this->request->getPost('username'); // Ambil username dari POST request
        $password = $this->request->getPost('password'); // Ambil password dari POST request

        // --- VALIDASI UNTUK PROSES LOGIN ---
        // Aturan validasi yang akan diterapkan pada input username dan password
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]', // Username wajib, min 3, max 50 karakter
            // 'password' wajib, min 6 karakter, dan harus lolos aturan kustom 'validateUser'
            // 'validateUser[username,password]' akan memanggil metode validateUser di app/Config/Validation.php
            // dan akan membandingkan password yang diinput dengan hash di database.
            'password' => 'required|min_length[6]|validateUser[username,password]',
        ];

        // Jalankan validasi menggunakan data dari request
        if (!$this->validate($rules)) {
            // Jika validasi gagal (misalnya username/password kosong, terlalu pendek, atau tidak cocok),
            // kembalikan pengguna ke halaman sebelumnya dengan input yang sudah diisi dan pesan error validasi.
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Jika validasi berhasil sampai sini, itu berarti aturan 'validateUser' telah mengkonfirmasi
        // bahwa username dan password cocok dengan yang ada di database.
        // Ambil data pengguna lagi (karena validasi tidak mengembalikan objek user secara langsung)
        $user = $this->userModel->where('username', $username)->first();

        // Set data sesi untuk pengguna yang berhasil login
        $ses_data = [
            'user_id'   => $user['id'],      // ID pengguna dari database
            'username'  => $user['username'], // Username pengguna
            'email'     => $user['email'],    // Email pengguna
            'logged_in' => TRUE,              // Penanda bahwa pengguna sudah login
            'role'      => 'admin'            // Contoh: Menetapkan peran admin
        ];
        $session->set($ses_data); // Simpan data ke sesi

        // Arahkan pengguna ke dashboard admin dengan pesan sukses
        return redirect()->to(base_url('admin/dashboard'))->with('success', 'Selamat datang, ' . $user['username'] . '!');
    }

    public function logout()
    {
        $session = session(); // Ambil instance sesi
        $session->destroy(); // Hancurkan semua data sesi, mengakhiri sesi login

        // Arahkan pengguna kembali ke halaman login dengan pesan sukses logout
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}
