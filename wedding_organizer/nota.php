<?php
session_start();
include 'koneksi.php';

// CEK LOGIN (Wajib login untuk lihat nota)
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login dulu!'); location='login.php';</script>";
    exit();
}

// 1. AMBIL ID DARI URL
$id_pemesanan = $_GET['id'];

// 2. AMBIL DATA PESANAN & USER DARI DATABASE
// Kita join tabel pemesanan dengan users agar dapat nama & detail lainnya
$ambil = $koneksi->query("SELECT * FROM pemesanan 
    JOIN users ON pemesanan.id_user = users.id_user 
    WHERE pemesanan.id_pemesanan = '$id_pemesanan'");
$detail = $ambil->fetch_assoc();

// Jika data tidak ditemukan
if (empty($detail)) {
    echo "<script>alert('Data pesanan tidak ditemukan'); location='riwayat.php';</script>";
    exit();
}

// Keamanan: Pastikan yang login adalah pemilik pesanan (Kecuali Admin)
// Jika user biasa mencoba buka nota orang lain, tolak.
if ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['id_user'] !== $detail['id_user']) {
   echo "<script>alert('Anda tidak berhak melihat nota ini'); location='riwayat.php';</script>";
   exit();
}

// 3. AMBIL DETAIL ITEM LAYANAN YANG DIBELI
$items = array();
$total_harga_barang = 0; // Variabel untuk menghitung murni harga barang

$ambil_item = $koneksi->query("SELECT * FROM detail_pemesanan 
    JOIN layanan ON detail_pemesanan.id_layanan = layanan.id_layanan 
    WHERE id_pemesanan = '$id_pemesanan'");

while($pecah = $ambil_item->fetch_assoc()){
    $items[] = $pecah;
    // Hitung subtotal per item (Harga x Jumlah)
    // Asumsi: Di tabel detail_pemesanan ada kolom 'harga_saat_ini' dan 'jumlah'
    $subtotal_item = $pecah['harga_saat_ini'] * $pecah['jumlah'];
    $total_harga_barang += $subtotal_item;
}

// 4. HITUNG ONGKOS KIRIM (LOGIKA MATEMATIKA)
// Ongkir = Total Bayar di Database - Total Harga Barang
$ongkir = $detail['total_harga'] - $total_harga_barang;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pembayaran #<?= $detail['kode_transaksi'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { font-family: 'Roboto', sans-serif; color: #444; background: #fdfdfd; padding: 40px 0; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 8px; border-top: 5px solid #B76E79; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px dashed #eee; padding-bottom: 30px; }
        .header h1 { font-family: 'Playfair Display', serif; color: #B76E79; margin: 0; font-size: 32px; letter-spacing: 1px; }
        .header p { margin: 5px 0; font-size: 14px; color: #888; }

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

        /* Status Stamp */
        .stamp { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; }
        .lunas { background: #e8f5e9; color: #2e7d32; border: 1px solid #2e7d32; }
        .pending { background: #fff3e0; color: #ef6c00; border: 1px solid #ef6c00; }

        /* Tombol Cetak (Hilang saat print) */
        .action-btn { text-align: center; margin-bottom: 30px; }
        .btn { padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; transition: 0.3s; }
        .btn-print { background: #333; color: white; }
        .btn-print:hover { background: #000; }
        .btn-back { background: #eee; color: #333; margin-left: 10px; }

        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; border: none; width: 100%; max-width: 100%; padding: 0; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="action-btn no-print">
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak Nota</button>
        <a href="riwayat.php" class="btn btn-back">Kembali</a>
    </div>

    <div class="container">
        
        <div class="header">
            <h1>Unity Wedding Organizer</h1>
            <p>Jalan Kenangan No. 123, Surabaya | info@unitywo.com | 0812-3456-7890</p>
        </div>

        <div class="invoice-info">
            <div class="info-box">
                <h3>Info Pelanggan</h3>
                <table class="info-table">
                    <tr><td>Nama</td><td>: <b><?= $detail['nama']; ?></b></td></tr>
                    <tr><td>Email</td><td>: <?= $detail['email']; ?></td></tr>
                    <tr><td>No. HP</td><td>: <?= $detail['no_hp']; ?></td></tr>
                    <tr><td>Tgl Acara</td><td>: <?= date("d F Y", strtotime($detail['tanggal_acara'])); ?></td></tr>
                </table>
            </div>
            <div class="info-box text-right">
                <h3>Detail Order</h3>
                <table class="info-table" align="right">
                    <tr><td>No. Invoice</td><td>: <b>#<?= $detail['kode_transaksi']; ?></b></td></tr>
                    <tr><td>Tanggal Pesan</td><td>: <?= date("d/m/Y", strtotime($detail['created_at'])); ?></td></tr>
                    <tr><td>Metode</td><td>: <?= ($ongkir > 0) ? 'Diantar / Pasang di Lokasi' : 'Ambil Sendiri'; ?></td></tr>
                    <tr><td>Status</td><td>: 
                        <?php if ($detail['status_pemesanan']=="lunas"): ?>
                            <span class="stamp lunas">LUNAS</span>
                        <?php else: ?>
                            <span class="stamp pending"><?= strtoupper($detail['status_pemesanan']); ?></span>
                        <?php endif; ?>
                    </td></tr>
                </table>
            </div>
        </div>

        <table class="table-item">
            <thead>
                <tr>
                    <th>Layanan / Produk</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="center">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <b><?= $item['nama_layanan']; ?></b>
                    </td>
                    <td class="text-right">Rp <?= number_format($item['harga_saat_ini']); ?></td>
                    <td class="center"><?= $item['jumlah']; ?></td>
                    <td class="text-right">Rp <?= number_format($item['harga_saat_ini'] * $item['jumlah']); ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr class="row-summary">
                    <td colspan="3" class="text-right">Total Harga Barang</td>
                    <td class="text-right">Rp <?= number_format($total_harga_barang); ?></td>
                </tr>

                <?php if ($ongkir > 0): ?>
                <tr class="row-summary" style="color: #d35400;">
                    <td colspan="3" class="text-right"><b>Biaya Transport / Pengiriman</b></td>
                    <td class="text-right"><b>Rp <?= number_format($ongkir); ?></b></td>
                </tr>
                <?php endif; ?>

                <tr class="grand-total">
                    <td colspan="3" class="text-right">TOTAL YANG HARUS DIBAYAR</td>
                    <td class="text-right">Rp <?= number_format($detail['total_harga']); ?></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 50px; border-top: 1px dashed #ccc; padding-top: 20px; color: #666; font-size: 13px;">
            <p><b>Catatan:</b></p>
            <ul>
                <li>Simpan nota ini sebagai bukti pembayaran yang sah.</li>
                <li>Untuk pembayaran transfer, silakan kirim ke BCA 123-456-7890 a.n Unity WO.</li>
            </ul>
            <center style="margin-top: 30px; color: #aaa;">&copy; 2026 Unity Wedding Organizer</center>
        </div>

    </div>
</body>
</html>