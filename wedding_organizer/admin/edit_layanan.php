<?php
session_start();
include '../koneksi.php';

$id = $_GET['id'];
$ambil = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id'");
$pecah = $ambil->fetch_assoc();
$data_kategori = $koneksi->query("SELECT * FROM kategori");

if (isset($_POST['ubah'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $id_kategori = $_POST['id_kategori'];
    
    $nama_foto = $_FILES['foto']['name'];
    $lokasi_foto = $_FILES['foto']['tmp_name'];

    if (!empty($lokasi_foto)) {
        $nama_foto_fix = date("dmYHis") . "_" . $nama_foto;
        
        // UPDATE: Upload ke folder 'uploads'
        move_uploaded_file($lokasi_foto, "../uploads/" . $nama_foto_fix);

        $koneksi->query("UPDATE layanan SET 
            id_kategori='$id_kategori', nama_layanan='$nama', harga='$harga', deskripsi='$deskripsi', gambar='$nama_foto_fix' 
            WHERE id_layanan='$id'");
    } else {
        $koneksi->query("UPDATE layanan SET 
            id_kategori='$id_kategori', nama_layanan='$nama', harga='$harga', deskripsi='$deskripsi' 
            WHERE id_layanan='$id'");
    }
    
    echo "<script>alert('Data Berhasil Diubah'); window.location='layanan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Layanan</title>
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
        <h2>Edit Layanan</h2>
        <div style="background: white; padding: 20px; border-radius: 10px; max-width: 800px;">
            <form method="post" enctype="multipart/form-data">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-control" required style="width: 100%; padding: 10px; border:1px solid #ddd;">
                        <?php while($kat = $data_kategori->fetch_assoc()){ ?>
                            <option value="<?= $kat['id_kategori'] ?>" <?= $kat['id_kategori'] == $pecah['id_kategori'] ? 'selected' : '' ?>>
                                <?= $kat['nama_kategori'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Nama Layanan</label>
                    <input type="text" name="nama" class="form-control" value="<?php echo $pecah['nama_layanan']; ?>" style="width: 100%; padding: 10px; border:1px solid #ddd;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" value="<?php echo $pecah['harga']; ?>" style="width: 100%; padding: 10px; border:1px solid #ddd;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="5" class="form-control" style="width: 100%; padding: 10px; border:1px solid #ddd;"><?php echo $pecah['deskripsi']; ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Ganti Foto</label><br>
                    
                    <?php if (!empty($pecah['gambar']) && file_exists("../uploads/" . $pecah['gambar'])): ?>
                        <img src="../uploads/<?php echo $pecah['gambar']; ?>" width="100" style="margin-bottom: 10px; border-radius:5px;">
                    <?php else: ?>
                        <div style="display:inline-block; padding:5px 10px; background:#ffe6e6; color:red; border:1px solid red; font-size:12px; margin-bottom:10px;">
                            Foto fisik tidak ditemukan di folder 'uploads'
                        </div>
                    <?php endif; ?>

                    <input type="file" name="foto" class="form-control" style="width: 100%; padding: 10px;">
                </div>
                
                <button class="btn-cta" name="ubah" style="width: 100%; padding: 10px;">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>
</html>