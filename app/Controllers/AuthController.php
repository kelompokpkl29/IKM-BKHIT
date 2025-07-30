<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Controller; // Pastikan ini diimpor jika tidak ada di BaseController

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
        // Redirect ke dashboard jika sudah login
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

        // --- VALIDASI UNTUK LOGIN ---
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            // 'validateUser' adalah aturan kustom dari app/Config/Validation.php
            'password' => 'required|min_length[6]|validateUser[username,password]',
        ];

        // Jalankan validasi
        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan ke form dengan error
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Jika validasi berhasil (yaitu, username dan password cocok),
        // ambil data pengguna untuk disimpan di sesi.
        $user = $this->userModel->where('username', $username)->first();

        $ses_data = [
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'logged_in' => TRUE,
            'role'      => 'admin' // Menambahkan role jika Anda memiliki sistem role
        ];
        $session->set($ses_data);

        return redirect()->to(base_url('admin/dashboard'))->with('success', 'Selamat datang, ' . $user['username'] . '!');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}