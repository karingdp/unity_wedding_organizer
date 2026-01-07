<?php
session_start();
include '../koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user']) OR $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../login.php';</script>";
    exit();
}

// Ambil data pelanggan lama untuk dropdown
$data_pelanggan = mysqli_query($koneksi, "SELECT * FROM users WHERE role='pelanggan' ORDER BY nama ASC");

if (isset($_POST['buat_nota'])) {
    $tanggal_acara = $_POST['tanggal_acara'];
    $lokasi_acara  = $_POST['lokasi_acara'];
    $tipe_pelanggan = $_POST['tipe_pelanggan']; // 'lama' atau 'baru'
    $id_user = 0;

    // LOGIKA PENENTUAN USER
    if ($tipe_pelanggan == 'baru') {
        // 1. Tangkap Data User Baru
        $nama_baru  = $_POST['nama_baru'];
        $email_baru = $_POST['email_baru'];
        $pass_baru  = $_POST['password_baru'];
        $hp_baru    = $_POST['hp_baru'];

        // Cek email
        $cek_email = $koneksi->query("SELECT * FROM users WHERE email='$email_baru'");
        if ($cek_email->num_rows > 0) {
            echo "<script>alert('Gagal! Email pelanggan sudah terdaftar. Gunakan pilihan Pelanggan Lama.'); window.history.back();</script>";
            exit();
        }

        // Simpan User Baru
        $koneksi->query("INSERT INTO users (nama, email, password, no_hp, role) VALUES ('$nama_baru', '$email_baru', '$pass_baru', '$hp_baru', 'pelanggan')");
        $id_user = $koneksi->insert_id;

    } else {
        // Pelanggan Lama
        $id_user = $_POST['id_user_lama'];
    }

    // 2. BUAT NOTA PESANAN
    if ($id_user > 0) {
        $kode = "OFF-" . date("Ymd") . "-" . strtoupper(substr(md5(time()), 0, 4));

        $query = "INSERT INTO pemesanan (kode_transaksi, id_user, tanggal_acara, lokasi_acara, total_harga, status_pemesanan, metode_pemesanan)
                  VALUES ('$kode', '$id_user', '$tanggal_acara', '$lokasi_acara', 0, 'pending', 'offline')";

        if (mysqli_query($koneksi, $query)) {
            $id_baru = mysqli_insert_id($koneksi);
            echo "<script>alert('Nota berhasil dibuat! Silakan tambahkan layanan.'); location='detail_pesanan.php?id=$id_baru';</script>";
        } else {
            echo "<script>alert('Gagal membuat nota.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Pesanan Baru</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-section { padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; }
        .hidden { display: none; }
    </style>
</head>
<body class="admin-body">

    <div class="sidebar">
        <h2>Admin Panel</h2>
        
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="layanan.php"><i class="fas fa-box"></i> Data Layanan</a>
        <a href="kategori.php"><i class="fas fa-tags"></i> Data Kategori</a>
        <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan Masuk</a>
        <a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a>
        
        <a href="tambah_pesanan.php" class="btn-sidebar-new">
            <i class="fas fa-plus-circle"></i> Input Pesanan Baru
        </a>

        <a href="../logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="admin-content">
        <h2><i class="fas fa-user-plus"></i> Buat Pesanan (Walk-in / Offline)</h2>
        
        <div style="background: white; padding: 25px; border-radius: 10px; max-width: 600px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <form method="POST">
                
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold;">Jenis Pelanggan:</label><br>
                    <label style="margin-right: 15px; cursor:pointer;">
                        <input type="radio" name="tipe_pelanggan" value="lama" checked onclick="togglePelanggan('lama')"> Pelanggan Sudah Terdaftar
                    </label>
                    <label style="cursor:pointer;">
                        <input type="radio" name="tipe_pelanggan" value="baru" onclick="togglePelanggan('baru')"> <b>+ Tambah Pelanggan Baru</b>
                    </label>
                </div>

                <div id="form-lama" class="form-section">
                    <label style="font-weight: bold;">Cari Nama Pelanggan</label>
                    <select name="id_user_lama" class="form-control">
                        <option value="">-- Pilih Akun Pelanggan --</option>
                        <?php while($p = mysqli_fetch_assoc($data_pelanggan)): ?>
                            <option value="<?= $p['id_user']; ?>">
                                <?= $p['nama']; ?> (<?= $p['email']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="form-baru" class="form-section hidden" style="background: #e8f6f3; border-color: #1abc9c;">
                    <h4 style="margin-top:0; color: #16a085;">Registrasi Cepat Pelanggan</h4>
                    
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_baru" class="form-control" placeholder="Nama Pelanggan">
                    
                    <label>Email (Untuk Login)</label>
                    <input type="email" name="email_baru" class="form-control" placeholder="contoh@gmail.com">
                    
                    <label>Password Sementara</label>
                    <input type="text" name="password_baru" class="form-control" placeholder="Isi password">
                    
                    <label>No. HP</label>
                    <input type="text" name="hp_baru" class="form-control" placeholder="08xxxxx">
                </div>

                <div class="form-group">
                    <label style="font-weight: bold;">Tanggal Acara</label>
                    <input type="date" name="tanggal_acara" required class="form-control">
                </div>

                <div class="form-group">
                    <label style="font-weight: bold;">Lokasi Acara</label>
                    <textarea name="lokasi_acara" rows="3" required class="form-control" placeholder="Alamat lengkap lokasi..."></textarea>
                </div>

                <button type="submit" name="buat_nota" class="btn-cta" style="width:100%; padding: 12px; font-size: 16px;">
                    <i class="fas fa-arrow-right"></i> Lanjut Pilih Layanan
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePelanggan(tipe) {
            const formLama = document.getElementById('form-lama');
            const formBaru = document.getElementById('form-baru');
            const inputUserLama = document.querySelector('select[name="id_user_lama"]');
            const inputsBaru = document.querySelectorAll('#form-baru input');

            if (tipe === 'baru') {
                formLama.classList.add('hidden');
                formBaru.classList.remove('hidden');
                inputUserLama.removeAttribute('required');
                inputsBaru.forEach(input => input.setAttribute('required', 'required'));
            } else {
                formLama.classList.remove('hidden');
                formBaru.classList.add('hidden');
                inputUserLama.setAttribute('required', 'required');
                inputsBaru.forEach(input => input.removeAttribute('required'));
            }
        }
    </script>

</body>
</html>