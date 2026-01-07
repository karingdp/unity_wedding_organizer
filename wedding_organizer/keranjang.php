<?php
session_start();
include 'koneksi.php';
// Hapus logika redirect paksa di sini agar halaman tetap terbuka meski kosong
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <h2 class="section-title" style="margin-top: 40px;">Keranjang Belanjaan Anda</h2>

        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); max-width: 900px; margin: 0 auto;">
            
            <?php if (empty($_SESSION['keranjang']) OR !isset($_SESSION['keranjang'])): ?>
                
                <div style="text-align: center; padding: 50px 20px;">
                    <h3 style="color: #888;">Wah, keranjang belanjaanmu masih kosong.</h3>
                    <p style="color: #aaa;">Yuk, cari paket layanan favoritmu sekarang!</p>
                    <a href="index.php" class="btn-cta" style="margin-top: 20px; display: inline-block;">Mulai Belanja</a>
                </div>

            <?php else: ?>
                
                <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee;">
                            <th style="padding: 15px; text-align: left;">No</th>
                            <th style="padding: 15px; text-align: left;">Layanan / Paket</th>
                            <th style="padding: 15px; text-align: left;">Harga</th>
                            <th style="padding: 15px; text-align: center;">Jumlah</th>
                            <th style="padding: 15px; text-align: right;">Subtotal</th>
                            <th style="padding: 15px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $nomor = 1;
                        $total_belanja = 0;
                        foreach ($_SESSION['keranjang'] as $id_layanan => $jumlah): 
                            $ambil = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id_layanan'");
                            $pecah = $ambil->fetch_assoc();
                            $subharga = $pecah['harga'] * $jumlah;
                            $total_belanja += $subharga;
                        ?>
                        <tr style="border-bottom: 1px solid #f9f9f9;">
                            <td style="padding: 15px;"><?php echo $nomor; ?></td>
                            <td style="padding: 15px; font-weight: bold;"><?php echo $pecah['nama_layanan']; ?></td>
                            <td style="padding: 15px;">Rp <?php echo number_format($pecah['harga']); ?></td>
                            <td style="padding: 15px; text-align: center;"><?php echo $jumlah; ?></td>
                            <td style="padding: 15px; text-align: right; color: #B76E79; font-weight: bold;">Rp <?php echo number_format($subharga); ?></td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="hapus_keranjang.php?id=<?php echo $id_layanan; ?>" class="btn-hapus" style="color: red; text-decoration: none; font-weight: bold;">
                                    Hapus <i class="fas fa-times"></i>
                                </a>
                            </td>
                        </tr>
                        <?php $nomor++; ?>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align: right; padding: 20px; font-size: 18px;">Total Belanja:</th>
                            <th colspan="2" style="text-align: left; padding: 20px; font-size: 18px; color: #B76E79;">Rp <?php echo number_format($total_belanja); ?></th>
                        </tr>
                    </tfoot>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                    <a href="index.php" class="btn-cta" style="background: #ccc; color: #333 !important;">&larr; Lanjut Belanja</a>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="checkout.php" class="btn-cta">Checkout Sekarang &rarr;</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-cta" style="background: #333;">Login untuk Checkout</a>
                    <?php endif; ?>
                </div>

            <?php endif; ?> </div>
    </div>

</body>
</html>