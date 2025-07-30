<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Controller; 

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
            'password' => 'required|min_length[6]|validateUser[username,password]', 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $user = $this->userModel->where('username', $username)->first(); 

        $ses_data = [
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'logged_in' => TRUE,
            'role'      => 'admin' 
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