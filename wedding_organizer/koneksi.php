<?php
// Konfigurasi Database
$host       = "localhost"; // Server database (biasanya localhost)
$user       = "root";      // Username database (default XAMPP: root)
$password   = "";          // Password database (default XAMPP: kosong)
$database   = "wedding_organizer"; // Nama database yang kita buat tadi

// Perintah untuk mengkoneksikan
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi, jika gagal tampilkan pesan error
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>