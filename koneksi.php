<?php
// projekbigdata/koneksi.php
$host = "localhost";
$user = "root";
$pass = ""; 
$db = "db_taqwa_mulia"; // Menggunakan nama database yang Anda tentukan

$koneksi = new mysqli($host, $user, $pass, $db);

if ($koneksi->connect_error) {
    die("Koneksi Gagal: " . $koneksi->connect_error);
}
// Tidak perlu echo "Koneksi Berhasil", agar file ini bersih saat di-include
?>