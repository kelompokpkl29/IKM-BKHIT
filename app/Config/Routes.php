<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Pengaturan Default CodeIgniter ---
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home'); // Controller default jika URL kosong
$routes->setDefaultMethod('index');    // Metode default jika tidak disebutkan di URL
$routes->setTranslateURIDashes(false); // Mengizinkan tanda hubung di URL
$routes->set404Override();             // Menentukan penanganan error 404 kustom
$routes->setAutoRoute(false);          // NONAKTIFKAN auto-routing untuk rute manual dan keamanan

// --- Rute Redirect Umum ---
// Ini akan mengarahkan permintaan ke 'index.php' ke root URL
$routes->addRedirect('index.php', '/');

// --- Rute Aplikasi Publik ---
$routes->get('/', 'Home::index');

// Daftar Kuesioner (untuk Pengguna)
$routes->get('/kuesioner', 'KuesionerController::index');

// Memulai Survei Kuesioner Multi-Halaman (dari daftar kuesioner)
$routes->get('/kuesioner/start/(:num)', 'KuesionerController::start_survey/$1'); 

// CARA ALTERNATIF: Langsung mulai kuesioner dari /kuesioner/isi/{id}
// Ini akan memicu start_survey yang benar.
$routes->get('/kuesioner/isi/(:num)', 'KuesionerController::start_survey_direct/$1'); 


// Menampilkan Halaman Pertanyaan Spesifik dalam Survei
$routes->get('/kuesioner/question/(:num)', 'KuesionerController::question/$1'); 

// Memproses Jawaban dari Satu Halaman Pertanyaan (untuk Lanjut ke Pertanyaan Berikutnya)
$routes->post('/kuesioner/process_answer', 'KuesionerController::process_answer');

// Memproses Navigasi Mundur di Kuesioner
$routes->post('/kuesioner/previous_question', 'KuesionerController::previous_question');

// Menyimpan Semua Jawaban Kuesioner Final ke Database
$routes->post('/kuesioner/submit_final', 'KuesionerController::submit_final');

// Halaman Terima Kasih Setelah Submit Kuesioner
$routes->get('/kuesioner/terimakasih', 'KuesionerController::terimakasih');

// Halaman Login Admin
$routes->get('/login', 'AuthController::login');

// Proses Login Admin (POST)
$routes->post('/login/process', 'AuthController::processLogin');

// Proses Logout Admin
$routes->get('/logout', 'AuthController::logout');


// --- Rute Admin (Dilindungi oleh filter 'authAdmin') ---
$routes->group('admin', ['filter' => 'authAdmin'], function($routes) {
    // Dashboard Admin
    $routes->get('dashboard', 'AdminController::dashboard');

    // Manajemen Kuesioner
    $routes->get('kuesioner', 'AdminController::kuesioner');
    $routes->get('kuesioner/create', 'AdminController::createKuesioner');
    $routes->post('kuesioner/store', 'AdminController::storeKuesioner');
    $routes->get('kuesioner/edit/(:num)', 'AdminController::editKuesioner/$1');
    $routes->post('kuesioner/update/(:num)', 'AdminController::updateKuesioner/$1');
    $routes->get('kuesioner/delete/(:num)', 'AdminController::deleteKuesioner/$1');

    // Manajemen Pertanyaan (terkait dengan Kuesioner tertentu)
    $routes->get('pertanyaan/(:num)', 'AdminController::pertanyaan/$1'); 
    $routes->get('pertanyaan/create/(:num)', 'AdminController::createPertanyaan/$1'); 
    $routes->post('pertanyaan/store', 'AdminController::storePertanyaan');
    $routes->get('pertanyaan/edit/(:num)', 'AdminController::editPertanyaan/$1');
    $routes->post('pertanyaan/update/(:num)', 'AdminController::updatePertanyaan/$1');
    $routes->get('pertanyaan/delete/(:num)', 'AdminController::deletePertanyaan/$1');

    // Manajemen Profil Admin
    $routes->get('profile', 'AdminController::profile');
    $routes->post('profile/update', 'AdminController::updateProfile');

    // Halaman Hasil IKM
    $routes->get('hasil', 'AdminController::hasil');
    // Export Hasil ke CSV
    $routes->get('hasil/export/csv', 'AdminController::exportCsvHasil');
});