<?php
namespace App\Controllers;
class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function processLogin()
    {
        // Langsung redirect ke dashboard admin tanpa cek kredensial
        // (HANYA UNTUK DEMO TANPA VALIDASI/FILTER)
        return redirect()->to(base_url('admin/dashboard'))->with('success', 'Selamat datang!');
    }

    public function logout()
    {
        // Langsung redirect ke login tanpa menghancurkan sesi
        // (HANYA UNTUK DEMO TANPA VALIDASI/FILTER)
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}