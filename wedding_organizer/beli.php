<?php
session_start();

// 1. Ambil ID Produk dari URL (beli.php?id=...)
$id_produk = $_GET['id'];

// 2. Ambil Jumlah dari Formulir (Input name="jumlah")
// Jika user input angka, pakai angka itu. Jika tidak, default 1.
if (isset($_POST['jumlah'])) {
    $jumlah = $_POST['jumlah'];
} else {
    $jumlah = 1;
}

// 3. Validasi ID
if (empty($id_produk)) {
    echo "<script>alert('Produk salah'); location='katalog.php';</script>";
    exit();
}

// 4. Cek Keranjang (Buat wadah jika belum ada)
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// 5. Masukkan ke Keranjang
if (isset($_SESSION['keranjang'][$id_produk])) {
    // Jika barang SUDAH ada, tambahkan jumlahnya dengan yang baru diinput
    $_SESSION['keranjang'][$id_produk] += $jumlah;
} else {
    // Jika barang BARU, masukkan sesuai jumlah input
    $_SESSION['keranjang'][$id_produk] = $jumlah;
}

// 6. Redirect ke Keranjang
echo "<script>alert('Berhasil memasukkan ke keranjang!'); location='keranjang.php';</script>";
?>