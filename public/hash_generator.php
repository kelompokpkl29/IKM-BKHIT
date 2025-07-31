<?php
$plainPassword = 'admin123'; // Ini adalah password yang Anda gunakan untuk login
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
echo "Hash untuk 'admin123': " . $hashedPassword;
?>