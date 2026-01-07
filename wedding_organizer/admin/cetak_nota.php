<?php
session_start();
// Koneksi naik satu folder ke atas
include '../koneksi.php';

// Cek apakah admin
if (!isset($_SESSION['user']) OR $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Anda harus login sebagai admin'); location='../login.php';</script>";
    exit();
}

$id_pemesanan = $_GET['id'];

// 1. AMBIL DATA PESANAN UTAMA
$ambil = $koneksi->query("SELECT * FROM pemesanan JOIN users ON pemesanan.id_user = users.id_user WHERE pemesanan.id_pemesanan = '$id_pemesanan'");
$detail = $ambil->fetch_assoc();

// 2. AMBIL DETAIL ITEM
$items = array();
$total_barang_murni = 0;

$ambil_item = $koneksi->query("SELECT * FROM detail_pemesanan 
    JOIN layanan ON detail_pemesanan.id_layanan = layanan.id_layanan 
    WHERE id_pemesanan = '$id_pemesanan'");

while($pecah = $ambil_item->fetch_assoc()){
    $items[] = $pecah;
    $subtotal = $pecah['harga_saat_ini'] * $pecah['jumlah'];
    $total_barang_murni += $subtotal;
}

// 3. HITUNG ONGKIR
$ongkir = $detail['total_harga'] - $total_barang_murni;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Admin - <?= $detail['kode_transaksi'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { font-family: 'Roboto', sans-serif; color: #444; background: #555; padding: 40px 0; } /* Background abu gelap biar fokus ke kertas */
        .container { width: 100%; max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border-radius: 8px; border-top: 5px solid #B76E79; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px dashed #eee; padding-bottom: 30px; }
        .header h1 { font-family: 'Playfair Display', serif; color: #B76E79; margin: 0; font-size: 32px; letter-spacing: 1px; }
        .header p { margin: 5px 0; font-size: 14px; color: #888; }
        .admin-badge { background: #333; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; vertical-align: middle; }

        /* Info Invoice */
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box h3 { margin-top: 0; font-size: 14px; color: #B76E79; text-transform: uppercase; letter-spacing: 1px; }
        .info-table td { padding: 4px 10px 4px 0; font-size: 14px; color: #555; }
        
        /* Tabel Item */
        .table-item { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-item th { background: #f8f8f8; color: #333; padding: 15px; text-align: left; font-size: 14px; text-transform: uppercase; font-weight: 600; border-bottom: 2px solid #ddd; }
        .table-item td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .text-right { text-align: right; }
        .center { text-align: center; }

        /* Total & Ongkir Area */
        .total-area { border-top: 2px solid #333; }
        .row-summary td { padding: 10px 15px; font-size: 14px; }
        .grand-total td { font-weight: bold; font-size: 18px; color: #B76E79; background: #fff5f7; padding: 20px 15px; }

        /* Tombol Cetak */
        .action-btn { text-align: center; margin-bottom: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin: 5px; }
        .btn-print { background: #27ae60; color: white; }
        .btn-close { background: #c0392b; color: white; }

        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; border: none; width: 100%; max-width: 100%; padding: 0; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="action-btn no-print">
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak</button>
        <button onclick="window.close()" class="btn btn-close"><i class="fas fa-times"></i> Tutup</button>
    </div>

    <div class="container">
        
        <div class="header">
            <h1>Unity Wedding Organizer <span ></span></h1>
        </div>

        <div class="invoice-info">
            <div class="info-box">
                <h3>Data Klien</h3>
                <table class="info-table">
                    <tr><td>Nama</td><td>: <b><?= $detail['nama']; ?></b></td></tr>
                    <tr><td>Email</td><td>: <?= $detail['email']; ?></td></tr>
                    <tr><td>No. HP</td><td>: <?= $detail['no_hp']; ?></td></tr>
                    <tr><td>Lokasi</td><td>: <?= $detail['lokasi_acara']; ?></td></tr>
                </table>
            </div>
            <div class="info-box text-right">
                <h3>Detail Transaksi</h3>
                <table class="info-table" align="right">
                    <tr><td>No. Invoice</td><td>: <b>#<?= $detail['kode_transaksi']; ?></b></td></tr>
                    <tr><td>Tanggal Pesan</td><td>: <?= date("d/m/Y", strtotime($detail['created_at'])); ?></td></tr>
                    <tr><td>Tgl Acara</td><td>: <?= date("d/m/Y", strtotime($detail['tanggal_acara'])); ?></td></tr>
                    <tr><td>Metode</td><td>: <?= ($ongkir > 0) ? 'Diantar / Pasang' : 'Ambil Sendiri'; ?></td></tr>
                </table>
            </div>
        </div>

        <table class="table-item">
            <thead>
                <tr>
                    <th>Layanan / Produk</th>
                    <th class="text-right">Harga</th>
                    <th class="center">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><b><?= $item['nama_layanan']; ?></b></td>
                    <td class="text-right">Rp <?= number_format($item['harga_saat_ini']); ?></td>
                    <td class="center"><?= $item['jumlah']; ?></td>
                    <td class="text-right">Rp <?= number_format($item['harga_saat_ini'] * $item['jumlah']); ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr class="row-summary">
                    <td colspan="3" class="text-right">Total Harga Barang</td>
                    <td class="text-right">Rp <?= number_format($total_barang_murni); ?></td>
                </tr>

                <?php if ($ongkir > 0): ?>
                <tr class="row-summary" style="color: #d35400;">
                    <td colspan="3" class="text-right"><b>Biaya Transport / Pengiriman</b></td>
                    <td class="text-right"><b>Rp <?= number_format($ongkir); ?></b></td>
                </tr>
                <?php endif; ?>

                <tr class="grand-total">
                    <td colspan="3" class="text-right">TOTAL TAGIHAN</td>
                    <td class="text-right">Rp <?= number_format($detail['total_harga']); ?></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 50px; font-size: 12px; color: #888; text-align: center;">
            <p>Dokumen ini dicetak oleh Administrator pada tanggal <?= date("d F Y H:i"); ?></p>
        </div>

    </div>
</body>
</html>