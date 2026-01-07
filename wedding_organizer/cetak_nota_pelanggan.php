<?php
session_start();
include 'koneksi.php';

// CEK LOGIN PELANGGAN
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login dulu!'); location='login.php';</script>";
    exit();
}

$id_pemesanan = $_GET['id'];
$id_user_login = $_SESSION['user']['id_user'];

// 1. AMBIL DATA PESANAN
$ambil = $koneksi->query("SELECT * FROM pemesanan 
    JOIN users ON pemesanan.id_user = users.id_user 
    WHERE pemesanan.id_pemesanan = '$id_pemesanan' AND pemesanan.id_user = '$id_user_login'");
$detail = $ambil->fetch_assoc();

if (empty($detail)) {
    echo "<script>alert('Data pesanan tidak ditemukan'); location='riwayat.php';</script>";
    exit();
}

// 2. AMBIL ITEM & HITUNG SUBTOTAL MURNI BARANG
$items = array();
$total_barang = 0; // Variabel untuk menampung total harga barang saja
$ambil_item = $koneksi->query("SELECT * FROM detail_pemesanan 
    JOIN layanan ON detail_pemesanan.id_layanan = layanan.id_layanan 
    WHERE id_pemesanan = '$id_pemesanan'");

while($pecah = $ambil_item->fetch_assoc()){
    $items[] = $pecah;
    // Asumsi di tabel detail_pemesanan ada kolom 'subtotal' (harga x jumlah)
    // Jika tidak ada, gunakan $pecah['harga_saat_ini'] * $pecah['jumlah']
    $total_barang += $pecah['subtotal']; 
}

// 3. HITUNG ONGKIR (Total Tagihan DB - Total Barang)
$ongkir = $detail['total_harga'] - $total_barang;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $detail['kode_transaksi'] ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: #fff; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #eee; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #B76E79; padding-bottom: 20px; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .table-item { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-item th { background: #B76E79; color: white; padding: 12px; text-align: left; }
        .table-item td { padding: 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; font-size: 16px; background: #FFF5F7; color: #B76E79; }
        @media print { .no-print { display: none !important; } .container { border: none; } }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; padding: 20px; background: #f9f9f9; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #B76E79; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">
            <i class="fas fa-print"></i> Cetak Invoice
        </button>
        <a href="riwayat.php" style="margin-left: 10px; color: #555; text-decoration: none;">Kembali ke Riwayat</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>Unity Wedding Organizer</h1>
            <p>Invoice Resmi / Bukti Pemesanan</p>
        </div>

        <div class="invoice-info">
            <div class="info-box">
                <h3>DITAGIHKAN KEPADA:</h3>
                <p>
                    <b><?= $detail['nama']; ?></b><br>
                    <?= $detail['no_hp']; ?><br>
                    <?= $detail['lokasi_acara']; ?>
                </p>
            </div>
            <div class="info-box text-right">
                <h3>NO. INVOICE: <?= $detail['kode_transaksi']; ?></h3>
                <p>Status: <b><?= strtoupper($detail['status_pemesanan']); ?></b></p>
            </div>
        </div>

        <table class="table-item">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th class="text-right">Harga</th>
                    <th style="text-align: center;">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <b><?= $item['nama_layanan']; ?></b><br>
                        <small style="color: #888;">ID: <?= $item['id_layanan']; ?></small>
                    </td>
                    <td class="text-right">Rp <?= number_format($item['harga_saat_ini']); ?></td>
                    <td style="text-align: center;"><?= $item['jumlah']; ?></td>
                    <td class="text-right">Rp <?= number_format($item['subtotal']); ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right" style="border:none;">Total Layanan</td>
                    <td class="text-right" style="border:none;">Rp <?= number_format($total_barang); ?></td>
                </tr>

                <?php if ($ongkir > 0): ?>
                <tr>
                    <td colspan="3" class="text-right" style="border:none;">Biaya Transport / Pengiriman</td>
                    <td class="text-right" style="border:none;">Rp <?= number_format($ongkir); ?></td>
                </tr>
                <?php endif; ?>

                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL TAGIHAN</td>
                    <td class="text-right">Rp <?= number_format($detail['total_harga']); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <center style="margin-top: 40px; color: #bbb;">&copy; 2026 Unity Wedding Organizer</center>
    </div>
</body>
</html>