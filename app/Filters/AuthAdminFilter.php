<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthAdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika user belum login, arahkan ke halaman login
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Anda harus login untuk mengakses halaman admin.');
        }
        // Jika user sudah login, biarkan permintaan berlanjut
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada yang dilakukan setelah permintaan
    }
}
