<?php
session_start();
include 'koneksi.php';
include 'header.php';

// Ambil ID dari URL
$id_produk = $_GET['id'];

// Query ambil data detail produk
$ambil = $koneksi->query("SELECT * FROM layanan WHERE id_layanan='$id_produk'");
$detail = $ambil->fetch_assoc();

// Jika data tidak ada (misal ID salah)
if (empty($detail)) {
    echo "<script>alert('Produk tidak ditemukan'); location='katalog.php';</script>";
    exit();
}
?>

<div class="container" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="row" style="display: flex; gap: 40px; flex-wrap: wrap;">
        
        <div class="col-gambar" style="flex: 1; min-width: 300px;">
            <img src="uploads/<?php echo $detail['gambar']; ?>" alt="" style="width: 100%; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        </div>

        <div class="col-detail" style="flex: 1; min-width: 300px;">
            <h2 style="font-size: 28px; margin-bottom: 10px;"><?php echo $detail['nama_layanan']; ?></h2>
            <h3 style="color: #B76E79; font-size: 24px;">Rp <?php echo number_format($detail['harga']); ?></h3>
            
            <p style="margin-top: 20px; line-height: 1.6; color: #555;">
                <?php echo $detail['deskripsi']; ?>
            </p>

            <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; margin-top: 30px; border: 1px solid #eee;">
                
                <form method="post" action="beli.php?id=<?php echo $detail['id_layanan']; ?>">
                    <label style="font-weight: bold; display: block; margin-bottom: 10px;">Mau pesan berapa?</label>
                    
                    <div style="display: flex; gap: 10px;">
                        <input type="number" min="1" name="jumlah" value="1" class="form-control" required 
                               style="width: 80px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
                        
                        <button type="submit" class="btn-cta" 
                                style="background: #B76E79; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; flex: 1;">
                             + Masukkan Keranjang
                        </button>
                    </div>
                </form>

            </div>

            <div style="margin-top: 20px;">
                <a href="katalog.php" style="color: #888; text-decoration: none;">&larr; Kembali ke Katalog</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>