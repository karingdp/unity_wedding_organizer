<?php
session_start();
include '../koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user']) OR $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../login.php';</script>";
    exit();
}

$ambil = $koneksi->query("SELECT * FROM kategori ORDER BY id_kategori DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kategori</title>
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
        <h2>Data Kategori</h2>
        
        <a href="tambah_kategori.php" class="btn-cta" style="margin-bottom: 20px; display:inline-block;">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>

        <div style="background: white; padding: 20px; border-radius: 10px;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee; text-align: left;">
                        <th style="padding: 10px;">No</th>
                        <th style="padding: 10px;">Nama Kategori</th>
                        <th style="padding: 10px;">Slug</th>
                        <th style="padding: 10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $nomor=1; ?>
                    <?php while($pecah = $ambil->fetch_assoc()){ ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;"><?php echo $nomor; ?></td>
                        <td style="padding: 10px;"><?php echo $pecah['nama_kategori']; ?></td>
                        <td style="padding: 10px; font-style: italic; color: #666;"><?php echo $pecah['slug']; ?></td>
                        <td style="padding: 10px;">
                            <a href="edit_kategori.php?id=<?php echo $pecah['id_kategori']; ?>" class="btn-edit" style="color: blue; margin-right: 10px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="hapus_kategori.php?id=<?php echo $pecah['id_kategori']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn-delete" style="color: red;">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
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