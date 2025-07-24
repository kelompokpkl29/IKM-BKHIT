<?php
// public/test_password.php

$password_input = 'password123';
$correct_hash_from_db = '$2y$10$Q7eY/0eS0g8c5D9L6M7G4uM5v2z1x0w9t8r7p6o5i4u3y2t1r0e9q8w7e6r5t4y3u2i1o0p9a8s7d6f5g4h3j2k1l0z9x8c7v6b5n4m3'; // Hash contoh dari 'password123'

echo "<h1>Uji Fungsi Password PHP</h1>";
echo "<p>Password yang diuji: <strong>" . htmlspecialchars($password_input) . "</strong></p>";

// Uji password_hash()
$generated_hash = password_hash($password_input, PASSWORD_DEFAULT);
echo "<p>Hash yang Dihasilkan `password_hash()` di Sistem Anda: <br><strong>" . htmlspecialchars($generated_hash) . "</strong></p>";

// Uji password_verify() dengan hash yang baru dihasilkan
$verify_self = password_verify($password_input, $generated_hash);
echo "<p>Verifikasi (`password_verify()`) terhadap hash yang Dihasilkan Sendiri: " . ($verify_self ? "<strong>BERHASIL (TRUE)</strong>" : "<strong>GAGAL (FALSE)</strong>") . "</p>";

// Uji password_verify() dengan hash yang diharapkan dari database (yang dibuat seeder)
$verify_db_hash = password_verify($password_input, $correct_hash_from_db);
echo "<p>Verifikasi (`password_verify()`) terhadap Hash Yang Diharapkan dari DB: " . ($verify_db_hash ? "<strong>BERHASIL (TRUE)</strong>" : "<strong>GAGAL (FALSE)</strong>") . "</p>";

if (!$verify_self || !$verify_db_hash) {
    echo "<p style='color:red;'><strong>PERINGATAN:</strong> Ada masalah dengan fungsi `password_hash()` atau `password_verify()` di instalasi PHP Anda, atau hash yang diharapkan salah.</p>";
} else {
    echo "<p style='color:green;'><strong>KABAR BAIK:</strong> Fungsi `password_hash()` dan `password_verify()` di sistem Anda berfungsi dengan benar.</p>";
    echo "<p>Jika Anda masih tidak bisa login di aplikasi, kemungkinan hash di database Anda saat ini tidak cocok. Mohon ulangi Langkah 1 untuk perbaikan database.</p>";
}
?>