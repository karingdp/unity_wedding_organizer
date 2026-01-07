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
    <title>Pesanan Masuk</title>
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
        <h2 style="color:var(--primary);">Daftar Pesanan Masuk</h2>
        
        <div style="background: white; padding: 20px; border-radius: 10px;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee; text-align: left;">
                        <th style="padding: 10px;">No</th>
                        <th style="padding: 10px;">Nama Pelanggan</th>
                        <th style="padding: 10px;">Tanggal Acara</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">Total</th>
                        <th style="padding: 10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
    <?php $nomor=1; ?>
    <?php 
    // PERBAIKAN: Ganti 'ORDER BY tanggal_pemesanan' menjadi 'ORDER BY id_pemesanan'
    // id_pemesanan DESC otomatis akan mengurutkan dari yang paling baru masuk
    $ambil = $koneksi->query("SELECT * FROM pemesanan JOIN users ON pemesanan.id_user=users.id_user ORDER BY id_pemesanan DESC"); 
    ?>
    <?php while($pecah = $ambil->fetch_assoc()){ ?>
    
    <tr style="border-bottom: 1px solid #ddd;">
        
        <td style="padding: 15px 10px;"><?php echo $nomor; ?></td>
        
        <td style="padding: 15px 10px;">
            <b><?php echo $pecah['nama']; ?></b><br>
            <small style="color: #777;"><?php echo $pecah['kode_transaksi']; ?></small>
        </td>
        
        <td style="padding: 15px 10px;">
            <?php echo date("d M Y", strtotime($pecah['tanggal_acara'])); ?>
        </td>
        
        <td style="padding: 15px 10px;">
            <?php 
                // Warna status
                $status = $pecah['status_pemesanan'];
                if($status=='pending') { echo "<span style='color:orange; font-weight:bold;'>Pending</span>"; }
                elseif($status=='lunas') { echo "<span style='color:green; font-weight:bold;'>Lunas</span>"; }
                elseif($status=='menunggu_pembayaran') { echo "<span style='color:#e67e22; font-weight:bold;'>Belum Bayar</span>"; }
                elseif($status=='menunggu_verifikasi') { echo "<span style='color:#f39c12; font-weight:bold;'>Cek Bukti</span>"; }
                else { echo "<span style='color:red; font-weight:bold;'>".ucfirst($status)."</span>"; }
            ?>
        </td>
        
        <td style="padding: 15px 10px;">
            Rp <?php echo number_format($pecah['total_harga']); ?>
        </td>
        
        <td style="padding: 15px 10px;">
            <div style="display: flex; gap: 5px; align-items: center;">
                
                <a href="detail_pesanan.php?id=<?php echo $pecah['id_pemesanan']; ?>" 
                   style="background: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; display: inline-block;">
                    <i class="fas fa-info-circle"></i> Detail
                </a>

                <a href="cetak_nota.php?id=<?php echo $pecah['id_pemesanan']; ?>" target="_blank" 
                   style="background: #f39c12; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; display: inline-block;">
                    <i class="fas fa-print"></i> Cetak
                </a>

            </div>
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