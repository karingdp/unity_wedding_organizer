<?php
session_start();
include 'koneksi.php';

// Jika sudah login, lempar ke index (biar gak bisa buka halaman login lagi)
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data user berdasarkan email
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // Cek Password
        if (password_verify($password, $data['password'])) {
            
            // --- PERBAIKAN UTAMA ADA DI SINI ---
            // Kita simpan seluruh data user ke dalam array 'user'
            // Ini agar cocok dengan checkout.php dan header.php
            $_SESSION['user'] = $data; 

            // Redirect sesuai Role
            if ($data['role'] == 'admin') {
                echo "<script>alert('Login Admin Berhasil'); location='admin/index.php';</script>";
            } else {
                echo "<script>alert('Login Berhasil'); location='index.php';</script>";
            }
            exit;
            
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Unity WO</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; background-color: var(--secondary);
            font-family: 'Segoe UI', sans-serif;
        }
        .login-box {
            background: white; padding: 40px; border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 350px; text-align: center;
        }
        input {
            width: 100%; padding: 12px; margin: 10px 0;
            border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;
        }
        .btn-cta {
            width:100%; border:none; cursor:pointer; padding: 12px; font-size: 16px;
        }
        .error-msg { 
            color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;
            padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <div style="margin-bottom: 20px;">
            <i class="fas fa-ring" style="font-size: 40px; color: var(--primary);"></i>
        </div>

        <h2 style="color: var(--primary); margin-bottom: 20px;">Login Unity WO</h2>
        
        <?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn-cta">Masuk Sekarang</button>
        </form>
        
        <div style="margin-top: 20px; font-size: 14px; color: #666;">
            <p>Belum punya akun? <a href="register.php" style="color: var(--primary); font-weight: bold;">Daftar</a></p>
            <p style="margin-top: 10px;"><a href="index.php" style="color: gray;">← Kembali ke Home</a></p>
        </div>
    </div>

</body>
</html>