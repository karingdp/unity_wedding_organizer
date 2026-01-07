<?php
session_start();
include 'koneksi.php';
include 'header.php';

// Ambil ID Pemesanan dari URL
$id_pemesanan = $_GET['id'];

// Ambil data pesanan agar user tahu berapa yang harus dibayar
$ambil = mysqli_query($koneksi, "SELECT * FROM pemesanan WHERE id_pemesanan='$id_pemesanan'");
$detpem = mysqli_fetch_assoc($ambil);

// Cek jika tombol Kirim ditekan
if (isset($_POST['kirim'])) {
    $jumlah  = $_POST['jumlah'];
    $tanggal = date("Y-m-d H:i:s");
    
    // Upload Bukti
    $nama_bukti = $_FILES['bukti']['name'];
    $lokasi_bukti = $_FILES['bukti']['tmp_name'];
    $nama_fiks = date("YmdHis")."_".$nama_bukti;
    
    // Pindahkan file
    move_uploaded_file($lokasi_bukti, "uploads/".$nama_fiks);

    // 1. Simpan ke tabel pembayaran
    $koneksi->query("INSERT INTO pembayaran (id_pemesanan, tanggal_bayar, jumlah_bayar, bukti_pembayaran, status_verifikasi)
                     VALUES ('$id_pemesanan', '$tanggal', '$jumlah', '$nama_fiks', 'menunggu')");

    // 2. Update status di tabel pemesanan jadi 'menunggu_verifikasi'
    $koneksi->query("UPDATE pemesanan SET status_pemesanan='menunggu_verifikasi' WHERE id_pemesanan='$id_pemesanan'");

    echo "<script>alert('Terima kasih! Bukti pembayaran sudah terkirim. Tunggu konfirmasi admin ya.'); location='riwayat.php';</script>";
}
?>

<div class="container" style="margin-top: 50px;">
    <h2 class="section-title">Konfirmasi Pembayaran</h2>
    
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <p>Anda akan membayar untuk tagihan: <strong><?php echo $detpem['kode_transaksi']; ?></strong></p>
        <p>Total Tagihan: <strong style="color: var(--primary); font-size: 1.2rem;">Rp <?php echo number_format($detpem['total_harga']); ?></strong></p>

        <form method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label>Jumlah yang Anda Transfer</label>
                <input type="number" name="jumlah" class="form-control" value="<?php echo $detpem['total_harga']; ?>" required style="width:100%; padding:10px; border:1px solid #ccc;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label>Foto Bukti Transfer</label>
                <input type="file" name="bukti" required style="width:100%; padding:10px;">
                <small style="color: red;">*Format foto JPG/PNG max 2MB</small>
            </div>

            <button type="submit" name="kirim" class="btn-cta" style="width:100%; border:none; cursor:pointer;">Kirim Bukti Pembayaran</button>
        </form>
    </div>
</div>

</body>
</html>