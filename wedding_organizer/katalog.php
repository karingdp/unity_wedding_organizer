<?php
include 'koneksi.php';
include 'header.php';
?>

<div class="container">
    <h2 class="section-title" style="margin-top: 40px;">Katalog Lengkap</h2>

    <div class="layanan-grid">
        <?php
        // Query mengambil SEMUA data layanan
        $query = mysqli_query($koneksi, "SELECT * FROM layanan ORDER BY id_layanan DESC");
        
        while($data = mysqli_fetch_array($query)){
        ?>
            <div class="card">
                <img src="uploads/<?php echo $data['gambar']; ?>" alt="<?php echo $data['nama_layanan']; ?>">
                <div class="card-body">
                    <h3><?php echo $data['nama_layanan']; ?></h3>
                    <span class="harga">
                        Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?>
                    </span>
                    
                    <div style="margin-top: 10px; display: flex; gap: 5px;">
                        <a href="detail.php?id=<?php echo $data['id_layanan']; ?>" class="btn-cta" style="background: #ccc; color: #333; font-size: 14px; flex: 1; text-align: center;">Detail</a>
                        
                        <a href="beli.php?id=<?php echo $data['id_layanan']; ?>" class="btn-cta" style="font-size: 14px; flex: 1; text-align: center;">+ Keranjang</a>
                    </div>
                    
                </div>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>