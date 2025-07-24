<?php
namespace App\Controllers;
use App\Models\UserModel; 

class AuthController extends BaseController 
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel(); 
        helper(['form', 'url', 'session']); 
    }

    public function login()
    {
        // Jika user sudah login, arahkan langsung ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }
        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session(); // Ambil instance session
        $username = $this->request->getPost('username'); // Ambil username dari form
        $password = $this->request->getPost('password'); // Ambil password dari form

        // Cari user di database berdasarkan username
        $user = $this->userModel->where('username', $username)->first();

        // Cek apakah user ditemukan DAN password cocok dengan hash di database
        if ($user && password_verify($password, $user['password'])) {
            // Jika kredensial cocok, set data sesi
            $ses_data = [
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'email'     => $user['email'],
                'logged_in' => TRUE // Indikator bahwa user sudah login
            ];
            $session->set($ses_data); // Simpan data sesi

            // Redirect ke dashboard admin dengan pesan sukses
            return redirect()->to(base_url('admin/dashboard'))->with('success', 'Selamat datang, ' . $user['username'] . '!');
        } else {
            // Jika user tidak ditemukan atau password tidak cocok, set pesan error flashdata
            $session->setFlashdata('error', 'Username atau Password salah.');
            // Kembali ke halaman login dengan input lama agar user tidak perlu mengetik ulang username
            return redirect()->back()->withInput();
        }
    }

    public function logout()
    {
        $session = session(); // Ambil instance session
        $session->destroy(); // Hancurkan semua data sesi
        // Redirect ke halaman login dengan pesan sukses logout
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}