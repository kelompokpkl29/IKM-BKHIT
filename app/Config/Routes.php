<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Pengaturan Default CodeIgniter ---
// Ini menentukan controller dan metode default ketika tidak ada segmen URL.
// Pastikan namespace yang benar untuk controller Anda.
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home'); // Controller default jika URL kosong
$routes->setDefaultMethod('index');    // Metode default jika tidak disebutkan di URL
$routes->setTranslateURIDashes(false); // Mengizinkan tanda hubung di URL
$routes->set404Override();             // Menentukan penanganan error 404 kustom
$routes->setAutoRoute(false);          // NONAKTIFKAN auto-routing untuk rute manual

// --- Rute Redirect Umum ---
// Ini akan mengarahkan permintaan ke 'index.php' ke root URL
$routes->addRedirect('index.php', '/');

// --- Rute Aplikasi Publik (Tanpa Filter) ---
// Halaman Landing Page (Beranda)
$routes->get('/', 'Home::index');

// Daftar Kuesioner (untuk Pengguna)
$routes->get('/kuesioner', 'KuesionerController::index');

// Halaman Isi Kuesioner
// :num adalah placeholder untuk ID numerik kuesioner
$routes->get('/kuesioner/isi/(:num)', 'KuesionerController::isi/$1');

// Proses Submit Kuesioner
// Menggunakan metode POST karena ini adalah pengiriman formulir
$routes->post('/kuesioner/submit', 'KuesionerController::submit');

// Halaman Terima Kasih Setelah Submit Kuesioner
$routes->get('/kuesioner/terimakasih', 'KuesionerController::terimakasih');

// Halaman Login Admin
$routes->get('/login', 'AuthController::login');

// Proses Login Admin
// Menggunakan metode POST karena ini adalah pengiriman formulir login
$routes->post('/login/process', 'AuthController::processLogin');

// Proses Logout Admin
$routes->get('/logout', 'AuthController::logout');


// --- Rute Admin (Dalam Aplikasi Nyata, Ini Akan Dilindungi Filter) ---
// Karena kita menonaktifkan filter untuk demo ini, rute ini akan langsung bisa diakses.
// Di aplikasi produksi, Anda akan mengelompokkannya dengan filter 'authAdmin'.

// Dashboard Admin
$routes->get('/admin/dashboard', 'AdminController::dashboard');

// Manajemen Kuesioner
$routes->get('/admin/kuesioner', 'AdminController::kuesioner');
$routes->get('/admin/kuesioner/create', 'AdminController::createKuesioner');
$routes->post('/admin/kuesioner/store', 'AdminController::storeKuesioner'); // POST untuk menyimpan
$routes->get('/admin/kuesioner/edit/(:num)', 'AdminController::editKuesioner/$1');
$routes->post('/admin/kuesioner/update/(:num)', 'AdminController::updateKuesioner/$1'); // POST untuk update
$routes->get('/admin/kuesioner/delete/(:num)', 'AdminController::deleteKuesioner/$1'); // GET untuk delete (untuk demo, di produksi lebih baik POST)

// Manajemen Pertanyaan (terkait dengan Kuesioner tertentu)
$routes->get('/admin/pertanyaan/(:num)', 'AdminController::pertanyaan/$1'); // Menampilkan pertanyaan dari kuesioner ID
$routes->get('/admin/pertanyaan/create/(:num)', 'AdminController::createPertanyaan/$1'); // Form tambah pertanyaan untuk kuesioner ID
$routes->post('/admin/pertanyaan/store', 'AdminController::storePertanyaan'); // POST untuk menyimpan pertanyaan
$routes->get('/admin/pertanyaan/edit/(:num)', 'AdminController::editPertanyaan/$1');
$routes->post('/admin/pertanyaan/update/(:num)', 'AdminController::updatePertanyaan/$1'); // POST untuk update
$routes->get('/admin/pertanyaan/delete/(:num)', 'AdminController::deletePertanyaan/$1'); // GET untuk delete (untuk demo)

// Manajemen Profil Admin
$routes->get('/admin/profile', 'AdminController::profile');
$routes->post('/admin/profile/update', 'AdminController::updateProfile'); // POST untuk update profil

// Halaman Hasil IKM (Placeholder)
$routes->get('/admin/hasil', 'AdminController::hasil');