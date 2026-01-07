<?php
include 'koneksi.php';

if (isset($_POST['daftar'])) {
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $no_hp    = $_POST['no_hp'];
    $alamat   = $_POST['alamat'];

    // 1. Cek apakah email sudah dipakai?
    $cek = $koneksi->query("SELECT * FROM users WHERE email='$email'");
    if ($cek->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar! Gunakan email lain.');</script>";
    } else {
        // 2. Enkripsi Password (Wajib agar bisa login)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // 3. Simpan ke Database
        $query = "INSERT INTO users (nama, email, password, no_hp, alamat, role) 
                  VALUES ('$nama', '$email', '$password_hash', '$no_hp', '$alamat', 'pelanggan')";
        
        if ($koneksi->query($query)) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); location='login.php';</script>";
        } else {
            echo "<script>alert('Gagal mendaftar.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun Baru</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: var(--secondary); }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 400px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
    </style>
</head>
<body>

    <div class="login-box">
        <h2 style="text-align:center; color: var(--primary);">Buat Akun Baru</h2>
        
        <form method="POST">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>No HP / WhatsApp</label>
            <input type="text" name="no_hp" required>

            <label>Alamat</label>
            <textarea name="alamat" rows="2" required></textarea>

            <button type="submit" name="daftar" class="btn-cta" style="width:100%; border:none; cursor:pointer; margin-top:10px;">Daftar Sekarang</button>
        </form>
        
        <p style="text-align:center; margin-top: 15px; font-size: 14px;">
            Sudah punya akun? <a href="login.php" style="color:var(--primary);">Login disini</a>
        </p>
        <p style="text-align:center; font-size: 12px;">
            <a href="index.php" style="color: gray;">← Kembali ke Home</a>
        </p>
    </div>

</body>
</html>