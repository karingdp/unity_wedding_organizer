<?php
session_start();
include '../koneksi.php';

$data_kategori = $koneksi->query("SELECT * FROM kategori");

if (isset($_POST['save'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $id_kategori = $_POST['id_kategori'];
    
    $nama_foto = $_FILES['foto']['name'];
    $lokasi_foto = $_FILES['foto']['tmp_name'];
    
    $nama_foto_fix = date("dmYHis") . "_" . $nama_foto;
    
    // UPDATE: Upload ke folder 'uploads'
    move_uploaded_file($lokasi_foto, "../uploads/" . $nama_foto_fix);
    
    $koneksi->query("INSERT INTO layanan (id_kategori, nama_layanan, harga, deskripsi, gambar) 
                     VALUES ('$id_kategori', '$nama', '$harga', '$deskripsi', '$nama_foto_fix')");
    
    echo "<script>alert('Data Berhasil Disimpan'); window.location='layanan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Layanan</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="layanan.php" class="active"><i class="fas fa-box"></i> Data Layanan</a>
        <a href="kategori.php"><i class="fas fa-tags"></i> Data Kategori</a>
        <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan Masuk</a>
        <a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="tambah_pesanan.php" class="btn-sidebar-new"><i class="fas fa-plus-circle"></i> Input Pesanan Baru</a>
    </div>

    <div class="admin-content">
        <h2>Tambah Data Layanan</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; max-width: 800px;">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-control" required style="width: 100%; padding: 10px; border:1px solid #ddd;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php while($kat = $data_kategori->fetch_assoc()){ ?>
                            <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Nama Layanan</label>
                    <input type="text" name="nama" required class="form-control" style="width: 100%; padding: 10px; border:1px solid #ddd;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" required class="form-control" style="width: 100%; padding: 10px; border:1px solid #ddd;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="5" required class="form-control" style="width: 100%; padding: 10px; border:1px solid #ddd;"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Foto</label>
                    <input type="file" name="foto" required class="form-control" style="width: 100%; padding: 10px;">
                </div>
                <button class="btn-cta" name="save" style="width: 100%; padding: 12px;">Simpan Data</button>
            </form>
        </div>
    </div>
</body>
</html>