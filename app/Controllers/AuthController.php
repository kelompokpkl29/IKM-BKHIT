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

        echo "<h2>DEBUG LOGIN PROCESS</h2>";
        echo "Mencoba login dengan Username: <strong>" . esc($username) . "</strong> dan Password: <strong>" . esc($password) . "</strong><br>";

        // Cari user di database
        $user = $this->userModel->where('username', $username)->first();

        if ($user) {
            echo "User ditemukan di DB. ID: " . esc($user['id']) . ", Username: " . esc($user['username']) . ", Email: " . esc($user['email']) . "<br>";
            echo "Hash Password dari DB: <strong>" . esc($user['password']) . "</strong><br>";
            
            // Lakukan verifikasi password
            $password_matches = password_verify($password, $user['password']);
            
            echo "Hasil password_verify('input_password', 'hash_dari_db'): <strong>" . ($password_matches ? "TRUE" : "FALSE") . "</strong><br>";
            
            // --- DEBUG: Output kondisi sebelum redirect ---
            if ($password_matches) {
                echo "<p style='color:green;'><strong>LOGIN BERHASIL!</strong> Seharusnya redirect ke dashboard.</p>";
                $ses_data = [
                    'user_id'   => $user['id'],
                    'username'  => $user['username'],
                    'email'     => $user['email'],
                    'logged_in' => TRUE
                ];
                $session->set($ses_data);
                echo "Data Sesi Disimpan:<pre>"; print_r($session->get()); echo "</pre>";
                // Ini akan menghentikan eksekusi setelah menampilkan debug,
                // jika Anda melihat ini, artinya login berhasil secara logika.
                // Hapus baris 'exit;' untuk melanjutkan redirect.
                exit('DEBUG: Login berhasil, sesi diatur. Mohon hapus baris `exit();` untuk melanjutkan.');
                
                // Jika sudah yakin, uncomment baris ini dan hapus exit; di atas
                // return redirect()->to(base_url('admin/dashboard'))->with('success', 'Selamat datang, ' . $user['username'] . '!');
            } else {
                echo "<p style='color:red;'><strong>PASSWORD TIDAK COCOK!</strong> Seharusnya kembali ke halaman login.</p>";
                // Ini akan menghentikan eksekusi setelah menampilkan debug
                exit('DEBUG: Login gagal karena password tidak cocok. Mohon hapus baris `exit();` untuk melanjutkan.');
                
                // Jika sudah yakin, uncomment baris ini dan hapus exit; di atas
                // $session->setFlashdata('error', 'Username atau Password salah.');
                // return redirect()->back()->withInput();
            }
        } else {
            echo "<p style='color:red;'><strong>USER TIDAK DITEMUKAN!</strong> User dengan username '" . esc($username) . "' tidak ada di database.</p>";
            // Ini akan menghentikan eksekusi setelah menampilkan debug
            exit('DEBUG: Login gagal karena user tidak ditemukan. Mohon hapus baris `exit();` untuk melanjutkan.');
            
            // Jika sudah yakin, uncomment baris ini dan hapus exit; di atas
            // $session->setFlashdata('error', 'Username atau Password salah.');
            // return redirect()->back()->withInput();
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah logout.');
    }
}