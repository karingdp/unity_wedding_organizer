<?php
session_start();

// 1. Ambil ID produk yang dikirim dari tombol "Hapus"
$id_produk = $_GET['id'];

// 2. Cek apakah ID tersebut ada di keranjang?
if (isset($_SESSION['keranjang'][$id_produk])) {
    
    // Hapus produk tersebut dari session
    unset($_SESSION['keranjang'][$id_produk]);
    
    // Tampilkan pesan sukses
    echo "<script>alert('Produk berhasil dihapus dari keranjang');</script>";
}
else {
    // Jaga-jaga jika user iseng hapus barang yang tidak ada
    echo "<script>alert('Produk tidak ditemukan di keranjang');</script>";
}

// 3. Larikan kembali ke halaman keranjang
echo "<script>location='keranjang.php';</script>";

?>