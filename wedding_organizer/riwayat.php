<?php
session_start();
include 'koneksi.php';

// 1. CEK LOGIN
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login dulu!'); location='login.php';</script>";
    exit();
}

// Ambil ID User dari session
$id_user = $_SESSION['user']['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <h2 class="section-title" style="margin-top: 30px;">Riwayat Pesanan Anda</h2>
        
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #ddd;">
                        <th style="padding: 15px;">No</th>
                        <th style="padding: 15px;">Tanggal</th>
                        <th style="padding: 15px;">Status</th>
                        <th style="padding: 15px;">Total</th>
                        <th style="padding: 15px; text-align: center;">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil data milik user ini saja, urutkan dari terbaru
                    $query = mysqli_query($koneksi, "SELECT * FROM pemesanan WHERE id_user='$id_user' ORDER BY created_at DESC");
                    
                    if(mysqli_num_rows($query) == 0) {
                        echo "<tr><td colspan='5' style='text-align:center; padding:30px;'>Belum ada riwayat pesanan.</td></tr>";
                    }

                    $no = 1;
                    while ($pecah = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;"><?php echo $no++; ?></td>
                        <td style="padding: 15px;">
                            <?php echo date("d M Y", strtotime($pecah['tanggal_acara'])); ?><br>
                            <small style="color:#777;"><?php echo $pecah['kode_transaksi']; ?></small>
                        </td>
                        <td style="padding: 15px;">
                            <?php 
                            $status = $pecah['status_pemesanan'];
                            if($status == 'pending') echo "<span style='color:#e67e22; font-weight:bold;'>Menunggu Pembayaran</span>";
                            elseif($status == 'lunas') echo "<span style='color:green; font-weight:bold;'>Lunas</span>";
                            else echo ucfirst($status);
                            ?>
                        </td>
                        <td style="padding: 15px;">Rp <?php echo number_format($pecah['total_harga']); ?></td>
                        
                        <td style="padding: 15px; text-align: center;">
                            
                            <a href="cetak_nota_pelanggan.php?id=<?php echo $pecah['id_pemesanan']; ?>" target="_blank" class="btn-cta" style="padding: 8px 15px; font-size: 12px; background: #B76E79; display:inline-block; margin-bottom: 5px;">
                                <i class="fas fa-print"></i> Cetak Nota
                            </a>

                            <?php if ($pecah['status_pemesanan'] == 'pending' OR $pecah['status_pemesanan'] == 'belum_bayar'): ?>
                                <a href="pembayaran.php?id=<?php echo $pecah['id_pemesanan']; ?>" class="btn-cta" style="padding: 8px 15px; font-size: 12px; background: #27ae60; display:inline-block;">
                                    <i class="fas fa-money-bill-wave"></i> Bayar
                                </a>
                            <?php endif; ?>

                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>