<?php
include '../koneksi.php';

$id = $_GET['id'];

// Hapus data
$query = "DELETE FROM kategori WHERE id_kategori='$id'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Kategori terhapus!'); location='kategori.php';</script>";
} else {
    // Peringatan jika kategori sedang dipakai oleh layanan (Relasi Database)
    echo "<script>alert('Gagal hapus! Kategori ini mungkin sedang dipakai oleh Layanan/Produk.'); location='kategori.php';</script>";
}
?>