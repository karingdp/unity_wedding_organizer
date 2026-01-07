<?php
include '../koneksi.php';

$id_layanan = $_GET['id'];

// 1. Ambil data dulu untuk tahu nama file gambarnya
$ambil = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id_layanan'");
$pecah = $ambil->fetch_assoc();
$fotoproduk = $pecah['gambar'];

// 2. Cek apakah layanan ini sudah pernah dipesan?
// Jika sudah ada di tabel 'detail_pemesanan', kita tidak boleh menghapusnya sembarangan karena akan merusak data laporan.
$cek_pesanan = $koneksi->query("SELECT * FROM detail_pemesanan WHERE id_layanan='$id_layanan'");
if($cek_pesanan->num_rows > 0) {
    echo "<script>alert('Gagal menghapus! Layanan ini sudah pernah dipesan oleh pelanggan (Ada di riwayat transaksi). Sebaiknya ubah statusnya menjadi HABIS saja, jangan dihapus.');</script>";
    echo "<script>location='layanan.php';</script>";
    exit();
}

// 3. Jika aman (belum pernah dipesan), Lanjut Hapus

// Hapus file gambar dari folder 'uploads' jika ada
if (file_exists("../uploads/$fotoproduk")) {
    unlink("../uploads/$fotoproduk");
}

// Hapus data dari database
$koneksi->query("DELETE FROM layanan WHERE id_layanan='$id_layanan'");

echo "<script>alert('Layanan terhapus');</script>";
echo "<script>location='layanan.php';</script>";
?>