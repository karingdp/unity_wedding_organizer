<?php
include 'koneksi.php'; 
include 'header.php'; 
?>

<div class="hero">
    <h1>Wujudkan Pernikahan Impianmu</h1>
    <p>Bingung mulai dari mana? Diskusikan rencana acaramu bersama kami.</p>
    
    <a href="https://wa.me/6289675647937?text=Halo%20Unity%20WO,%20saya%20tertarik%20konsultasi" target="_blank" class="btn-cta" style="padding: 12px 30px; font-size: 18px;">
        <i class="fab fa-whatsapp"></i> Konsultasi Gratis
    </a>
</div>

<div class="container">
    <h2 class="section-title">Layanan Terbaru</h2>
    
    <div class="layanan-grid">
        <?php
        // Query mengambil 3 data terakhir yang diinput admin
        $query = mysqli_query($koneksi, "SELECT * FROM layanan ORDER BY id_layanan DESC LIMIT 3");
        
        // Cek apakah ada data?
        if(mysqli_num_rows($query) > 0){
            while($data = mysqli_fetch_array($query)){
        ?>
            <div class="card">
                <img src="uploads/<?php echo $data['gambar']; ?>" alt="<?php echo $data['nama_layanan']; ?>">
                
                <div class="card-body">
                    <h3><?php echo $data['nama_layanan']; ?></h3>
                    
                    <span class="harga">
                        Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?>
                    </span>
                    
                    <a href="detail.php?id=<?php echo $data['id_layanan']; ?>" class="btn-cta" style="padding: 5px 15px; font-size: 14px;">Detail</a>
                </div>
            </div>
        <?php 
            } // Tutup While
        } else {
            echo "<p style='text-align:center; width:100%'>Belum ada layanan yang tersedia.</p>";
        }
        ?>
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="katalog.php" style="color: var(--primary); font-weight: bold;">Lihat Semua Layanan &rarr;</a>
    </div>
</div>

</body>
</html>