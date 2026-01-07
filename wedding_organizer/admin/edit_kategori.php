<?php
session_start();
include '../koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user']) OR $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../login.php';</script>";
    exit();
}

$id = $_GET['id'];
$ambil = mysqli_query($koneksi, "SELECT * FROM kategori WHERE id_kategori='$id'");
$data = mysqli_fetch_assoc($ambil);

if (isset($_POST['update'])) {
    $nama = $_POST['nama_kategori'];
    $slug = strtolower(str_replace(' ', '-', $nama));

    $query = "UPDATE kategori SET nama_kategori='$nama', slug='$slug' WHERE id_kategori='$id'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Kategori berhasil diupdate!'); location='kategori.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="kategori.php" class="active"><i class="fas fa-tags"></i> Data Kategori</a>
        <a href="layanan.php"><i class="fas fa-box"></i> Data Layanan</a>
        <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan Masuk</a>
        <a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a>
    </div>

    <div class="admin-content">
        <h2>Edit Kategori</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; max-width: 500px;">
            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-weight:bold; margin-bottom:5px;">Nama Kategori</label>
                    <input type="text" name="nama_kategori" value="<?= $data['nama_kategori']; ?>" required class="form-control" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
                </div>
                <button type="submit" name="update" class="btn-cta" style="width:100%;">Update</button>
                <a href="kategori.php" style="display:block; text-align:center; margin-top:10px; color:#555;">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>