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

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
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
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}