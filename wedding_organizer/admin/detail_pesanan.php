<?php
session_start();
include '../koneksi.php';
// (Letaknya setelah include koneksi.php)
$ambil = $koneksi->query("SELECT * FROM pemesanan JOIN users ON pemesanan.id_user=users.id_user WHERE pemesanan.id_pemesanan='$_GET[id]'");
$detail = $ambil->fetch_assoc();
$id_pemesanan = $_GET['id'];

// -----------------------------------------------------------
// LOGIKA 1: TAMBAH ITEM LAYANAN (Khusus Admin input manual)
// -----------------------------------------------------------
if (isset($_POST['tambah_item'])) {
    $id_layanan = $_POST['id_layanan'];
    $jumlah     = $_POST['jumlah'];

    // Ambil info harga layanan
    $ambil_l = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id_layanan'");
    $detail_l = $ambil_l->fetch_assoc();
    
    $harga    = $detail_l['harga'];
    $subtotal = $harga * $jumlah;

    // Masukkan ke detail_pemesanan
    $koneksi->query("INSERT INTO detail_pemesanan (id_pemesanan, id_layanan, harga_saat_ini, jumlah, subtotal)
                     VALUES ('$id_pemesanan', '$id_layanan', '$harga', '$jumlah', '$subtotal')");

    // Update TOTAL HARGA di tabel pemesanan (re-calculate)
    $koneksi->query("UPDATE pemesanan SET total_harga = total_harga + $subtotal WHERE id_pemesanan='$id_pemesanan'");

    echo "<script>alert('Item berhasil ditambahkan!'); location='detail_pesanan.php?id=$id_pemesanan';</script>";
}

// -----------------------------------------------------------
// LOGIKA 2: UPDATE STATUS PEMESANAN
// -----------------------------------------------------------
if (isset($_POST['proses'])) {
    $status_baru = $_POST['status'];
    $koneksi->query("UPDATE pemesanan SET status_pemesanan='$status_baru' WHERE id_pemesanan='$id_pemesanan'");
    
    // Update pembayaran otomatis jika lunas (opsional, biar rapi)
    if($status_baru == 'lunas'){
         $koneksi->query("UPDATE pembayaran SET status_verifikasi='valid' WHERE id_pemesanan='$id_pemesanan'");
    }
    
    echo "<script>alert('Status pesanan berhasil diupdate!'); location='pesanan.php';</script>";
}

// Ambil Data Utama
$ambil = $koneksi->query("SELECT * FROM pemesanan JOIN users ON pemesanan.id_user = users.id_user WHERE pemesanan.id_pemesanan='$id_pemesanan'");
$detail = $ambil->fetch_assoc();

// Ambil Pembayaran
$ambil_bayar = $koneksi->query("SELECT * FROM pembayaran WHERE id_pemesanan='$id_pemesanan'");
$bayar = $ambil_bayar->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; background: #f4f4f4;}
        .content { flex: 1; padding: 40px; }
        .box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge { padding: 5px 10px; border-radius: 5px; font-weight: bold; font-size: 12px; }
        .offline { background: #e2e6ea; color: #333; border: 1px solid #ccc; }
        .online { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="content">
        <h2>
            Detail Pesanan: <?= $detail['kode_transaksi']; ?> 
            <?php if($detail['metode_pemesanan']=='offline'): ?>
                <span class="badge offline">OFFLINE / WALK-IN</span>
            <?php else: ?>
                <span class="badge online">ONLINE</span>
            <?php endif; ?>
        </h2>
        
        <div style="display: flex; gap: 20px;">
            
            <div style="flex: 2;">
                
                <div class="box">
                    <h3>Data Pemesan</h3>
                    <p>
                        <strong>Nama:</strong> <?= $detail['nama']; ?><br>
                        <strong>No HP:</strong> <?= $detail['no_hp']; ?><br>
                        <strong>Tanggal Acara:</strong> <?= date("d F Y", strtotime($detail['tanggal_acara'])); ?><br>
                        <strong>Lokasi:</strong> <?= $detail['lokasi_acara']; ?>
                    </p>
                </div>

                <?php if($detail['status_pemesanan'] == 'pending'): ?>
                <div class="box" style="background: #e3f2fd; border: 1px dashed #2196f3;">
                    <h4>+ Tambah Layanan Manual</h4>
                    <form method="POST" style="display: flex; gap: 10px;">
                        <select name="id_layanan" required style="flex: 2; padding: 10px;">
                            <option value="">-- Pilih Layanan --</option>
                            <?php 
                            $layanan = $koneksi->query("SELECT * FROM layanan WHERE status='tersedia'");
                            while($l = $layanan->fetch_assoc()){
                                echo "<option value='".$l['id_layanan']."'>".$l['nama_layanan']." - Rp ".number_format($l['harga'])."</option>";
                            }
                            ?>
                        </select>
                        <input type="number" name="jumlah" value="1" min="1" style="width: 80px; padding: 10px;" required>
                        <button type="submit" name="tambah_item" class="btn-cta" style="border:none; cursor:pointer;">Tambah</button>
                    </form>
                </div>
                <?php endif; ?>

                <div class="box">
                    <h3>Rincian Item</h3>
                    <table class="table table-bordered" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Layanan</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $nomor = 1; 
        $total_barang = 0;
        ?>
        <?php $ambil = $koneksi->query("SELECT * FROM detail_pemesanan JOIN layanan ON detail_pemesanan.id_layanan=layanan.id_layanan WHERE detail_pemesanan.id_pemesanan='$_GET[id]'"); ?>
        <?php while($pecah = $ambil->fetch_assoc()){ ?>
        
        <?php 
            $subtotal = $pecah['harga_saat_ini'] * $pecah['jumlah'];
            $total_barang += $subtotal;
        ?>
        <tr>
            <td><?php echo $nomor; ?></td>
            <td><?php echo $pecah['nama_layanan']; ?></td>
            <td style="padding: 10px;">Rp <?php echo number_format($pecah['harga_saat_ini']); ?></td>
            <td style="padding: 10px; text-align: center;"><?php echo $pecah['jumlah']; ?></td>
            <td style="padding: 10px;">Rp <?php echo number_format($subtotal); ?></td>
        </tr>
        <?php $nomor++; ?>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" style="text-align: right; padding: 10px;">Total Harga Barang</th>
            <th style="padding: 10px;">Rp <?php echo number_format($total_barang); ?></th>
        </tr>

        <?php 
            // Total Tagihan (dari tabel pemesanan) - Total Barang = Ongkir
            // Pastikan variabel $detail sudah diambil di atas (lihat langkah 3)
            $ongkir = $detail['total_harga'] - $total_barang;
        ?>
        
        <?php if($ongkir > 0): ?>
        <tr>
            <th colspan="4" style="text-align: right; color: red; padding: 10px;">Biaya Transport / Pengiriman</th>
            <th style="color: red; padding: 10px;">Rp <?php echo number_format($ongkir); ?></th>
        </tr>
        <?php endif; ?>

        <tr>
            <th colspan="4" style="text-align: right; font-size: 18px; padding: 10px;">TOTAL TAGIHAN</th>
            <th style="font-size: 18px; color: #B76E79; padding: 10px;">Rp <?php echo number_format($detail['total_harga']); ?></th>
        </tr>
    </tfoot>
</table>

                    <h3 style="text-align:right; color:var(--primary);">Total Tagihan: Rp <?= number_format($detail['total_harga']); ?></h3>
                </div>
            </div>

            <div style="flex: 1;">
                <div class="box">
                    <h3>Bukti Pembayaran</h3>
                    <?php if (isset($bayar['bukti_pembayaran'])): ?>
                        <img src="../uploads/<?= $bayar['bukti_pembayaran']; ?>" style="width:100%; border-radius:5px;">
                        <p>Total Bayar: Rp <?= number_format($bayar['jumlah_bayar']); ?></p>
                    <?php else: ?>
                        <p style="color:gray;">Belum ada bukti pembayaran (Mungkin bayar Cash).</p>
                    <?php endif; ?>

                    <hr>
                    <h3>Verifikasi / Update Status</h3>
                    <form method="POST">
                        <select name="status" style="width:100%; padding:10px; margin-bottom:10px;">
                            <option value="pending" <?= $detail['status_pemesanan']=='pending'?'selected':''; ?>>Pending</option>
                            <option value="menunggu_verifikasi" <?= $detail['status_pemesanan']=='menunggu_verifikasi'?'selected':''; ?>>Menunggu Verifikasi</option>
                            <option value="lunas" <?= $detail['status_pemesanan']=='lunas'?'selected':''; ?>>✅ LUNAS (Cash/Transfer)</option>
                            <option value="batal" <?= $detail['status_pemesanan']=='batal'?'selected':''; ?>>❌ BATAL</option>
                        </select>
                        <button name="proses" class="btn-cta" style="width:100%; border:none; cursor:pointer;">Simpan Status</button>
                    </form>
                    <br>
                    <a href="pesanan.php" style="display:block; text-align:center;">Kembali</a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>