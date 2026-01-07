<?php
session_start();
include '../koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user']) OR $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Layanan</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">

    <div class="sidebar">
        <h2>Admin Panel</h2>
        
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="layanan.php" class="<?= basename($_SERVER['PHP_SELF'])=='layanan.php'?'active':'' ?>"><i class="fas fa-box"></i> Data Layanan</a>
        <a href="kategori.php" class="<?= basename($_SERVER['PHP_SELF'])=='kategori.php'?'active':'' ?>"><i class="fas fa-tags"></i> Data Kategori</a>
        <a href="pesanan.php" class="<?= basename($_SERVER['PHP_SELF'])=='pesanan.php'?'active':'' ?>"><i class="fas fa-shopping-cart"></i> Pesanan Masuk</a>
        <a href="laporan.php" class="<?= basename($_SERVER['PHP_SELF'])=='laporan.php'?'active':'' ?>"><i class="fas fa-file-alt"></i> Laporan</a>

        <a href="tambah_pesanan.php" class="btn-sidebar-new">
            <i class="fas fa-plus-circle"></i> Input Pesanan Baru
        </a>

        <a href="../logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="admin-content">
        <h2>Data Layanan / Produk</h2>
        
        <a href="tambah_layanan.php" class="btn-cta" style="margin-bottom: 20px; display:inline-block;">
            <i class="fas fa-plus"></i> Tambah Data
        </a>

        <div style="background: white; padding: 20px; border-radius: 10px;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee; text-align: left;">
                        <th style="padding: 10px;">No</th>
                        <th style="padding: 10px; width: 150px;">Foto</th>
                        <th style="padding: 10px;">Nama</th>
                        <th style="padding: 10px;">Harga</th>
                        <th style="padding: 10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $nomor=1; ?>
                    <?php $ambil=$koneksi->query("SELECT * FROM layanan ORDER BY id_layanan DESC"); ?>
                    <?php while($pecah = $ambil->fetch_assoc()){ ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;"><?php echo $nomor; ?></td>
                        <td style="padding: 10px;">
                            <?php 
                                // UPDATE: Mengarah ke folder 'uploads'
                                $foto = $pecah['gambar'];
                                $path_file = "../uploads/" . $foto;
                                
                                if (!empty($foto) && file_exists($path_file)) {
                                    echo "<img src='$path_file' width='100' style='border-radius:5px; object-fit: cover; height: 100px;'>";
                                } else {
                                    echo "<div style='color:red; font-size:12px; border:1px dashed red; padding:5px; text-align:center;'>
                                            Foto Tidak Ada di folder uploads
                                          </div>";
                                }
                            ?>
                        </td>
                        <td style="padding: 10px; font-weight: bold;"><?php echo $pecah['nama_layanan']; ?></td>
                        <td style="padding: 10px;">Rp <?php echo number_format($pecah['harga']); ?></td>
                        <td style="padding: 10px;">
                            <a href="edit_layanan.php?id=<?php echo $pecah['id_layanan']; ?>" class="btn-edit" style="color: white; background: #3498db; padding: 5px 10px; border-radius: 4px; text-decoration:none; margin-right: 5px; font-size: 14px;">Edit</a>
                            <a href="hapus_layanan.php?id=<?php echo $pecah['id_layanan']; ?>" onclick="return confirm('Yakin hapus?')" class="btn-hapus" style="color: white; background: #c0392b; padding: 5px 10px; border-radius: 4px; text-decoration:none; font-size: 14px;">Hapus</a>
                        </td>
                    </tr>
                    <?php $nomor++; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>