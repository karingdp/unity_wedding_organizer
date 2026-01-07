<?php
session_start();
include 'koneksi.php';

// 1. CEK LOGIN
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login dulu!'); location='login.php';</script>";
    exit();
}

// 2. CEK KERANJANG
if (empty($_SESSION['keranjang']) OR !isset($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang kosong, silakan belanja dulu'); location='katalog.php';</script>";
    exit();
}

// 3. LOGIKA CHECKOUT
if (isset($_POST['checkout'])) {
    $id_user = $_SESSION['user']['id_user'];
    $tanggal = $_POST['tanggal_acara'];
    $lokasi  = $_POST['lokasi_acara'];
    $catatan_user = $_POST['catatan'];
    $opsi    = $_POST['opsi_pengiriman'];

    // Hitung Total Barang
    $total_belanja = 0;
    foreach ($_SESSION['keranjang'] as $id_layanan => $jumlah) {
        $ambil = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id_layanan'");
        $pecah = $ambil->fetch_assoc();
        $total_belanja += $pecah['harga'] * $jumlah;
    }

    // Hitung Ongkir
    $ongkir = 0;
    if ($opsi == 'Diantar / Dilayani di Lokasi') {
        $ongkir = 50000; 
    }
    $total_final = $total_belanja + $ongkir;

    // Gabungkan Info Pengiriman ke CATATAN (Supaya tidak error kolom hilang)
    // Format: [Opsi Pengiriman] - Catatan User
    $catatan_lengkap = "[$opsi] " . $catatan_user;

    // Buat Kode Transaksi
    $kode_transaksi = "TRX-" . date("Ymd") . "-" . rand(1000,9999);
    $created_at = date("Y-m-d H:i:s");

    // --- SIMPAN KE DATABASE (Versi Aman) ---
    // Kita gunakan kolom standar: 'metode_pemesanan' diisi 'online'
    $sql_simpan = "INSERT INTO pemesanan 
        (kode_transaksi, id_user, tanggal_acara, lokasi_acara, catatan, total_harga, status_pemesanan, metode_pemesanan, created_at)
        VALUES 
        ('$kode_transaksi', '$id_user', '$tanggal', '$lokasi', '$catatan_lengkap', '$total_final', 'pending', 'online', '$created_at')";
    
    if ($koneksi->query($sql_simpan)) {
        // SUKSES SIMPAN
        $id_pemesanan_barusan = $koneksi->insert_id;

        // Simpan Detail Barang
        foreach ($_SESSION['keranjang'] as $id_layanan => $jumlah) {
            $ambil = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id_layanan'");
            $pecah = $ambil->fetch_assoc();

            $harga = $pecah['harga'];
            $subharga = $pecah['harga'] * $jumlah;

            $koneksi->query("INSERT INTO detail_pemesanan (id_pemesanan, id_layanan, harga_saat_ini, jumlah, subtotal)
                             VALUES ('$id_pemesanan_barusan', '$id_layanan', '$harga', '$jumlah', '$subharga')");
        }

        // BARU KOSONGKAN KERANJANG SETELAH SUKSES
        unset($_SESSION['keranjang']);

        echo "<script>alert('Pembelian Sukses!'); location='nota.php?id=$id_pemesanan_barusan';</script>";
    } else {
        // GAGAL SIMPAN
        echo "<script>alert('Gagal Checkout: " . $koneksi->error . "');</script>";
        // Keranjang TIDAK dihapus, jadi user bisa coba lagi
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Wedding Organizer</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<?php include 'header.php'; ?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <h2 class="section-title">Checkout Pesanan</h2>
    
    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 300px;">
            <table class="table" style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <thead>
                    <tr style="background: #B76E79; color: white;">
                        <th style="padding: 10px;">Layanan</th>
                        <th style="padding: 10px;">Harga</th>
                        <th style="padding: 10px;">Jml</th>
                        <th style="padding: 10px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total_awal = 0; ?>
                    <?php foreach ($_SESSION['keranjang'] as $id_layanan => $jumlah): ?>
                    <?php
                        $ambil = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id_layanan'");
                        $pecah = $ambil->fetch_assoc();
                        $subharga = $pecah['harga'] * $jumlah;
                        $total_awal += $subharga;
                    ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $pecah['nama_layanan']; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">Rp <?php echo number_format($pecah['harga']); ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;"><?php echo $jumlah; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">Rp <?php echo number_format($subharga); ?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right; padding: 10px;">Total Barang:</th>
                        <th style="padding: 10px;">Rp <?php echo number_format($total_awal); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="flex: 1; min-width: 300px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <form method="POST">
                
                <h3 style="margin-top: 0; color: #B76E79;">Informasi Pemesan</h3>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;">Nama</label>
                    <input type="text" readonly value="<?php echo $_SESSION['user']['nama']; ?>" class="form-control" style="background: #eee;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;">No. HP</label>
                    <input type="text" readonly value="<?php echo $_SESSION['user']['no_hp']; ?>" class="form-control" style="background: #eee;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;">Tanggal Acara</label>
                    <input type="date" name="tanggal_acara" required class="form-control">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;">Opsi Layanan</label>
                    <select name="opsi_pengiriman" required class="form-control">
                        <option value="Diantar / Dilayani di Lokasi">Diantar / Pasang di Lokasi (+Rp 50.000)</option>
                        <option value="Ambil Sendiri">Ambil Sendiri / Datang ke Kantor</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;">Lokasi Acara</label>
                    <textarea name="lokasi_acara" rows="3" required class="form-control" placeholder="Alamat lengkap..."></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold;">Catatan</label>
                    <textarea name="catatan" rows="2" class="form-control" placeholder="Request khusus..."></textarea>
                </div>

                <button type="submit" name="checkout" class="btn-cta" style="width: 100%;">Proses Pesanan</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>