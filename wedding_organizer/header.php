<?php
// 1. Cek session di paling atas agar aman
if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

// 2. Deteksi nama halaman yang sedang dibuka (contoh: index.php)
$halaman_saat_ini = basename($_SERVER['PHP_SELF']);

// 3. Hitung jumlah keranjang
$jml_keranjang = 0;
if(isset($_SESSION['keranjang'])){
    $jml_keranjang = array_sum($_SESSION['keranjang']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unity Wedding Organizer</title>
    
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* CSS TAMBAHAN UNTUK MENU AKTIF */
        /* Ini akan membuat menu yang sedang dibuka berwarna Pink & Tebal */
        .nav-menu a.menu-aktif {
            color: #B76E79 !important; /* Warna Pink Brand */
            font-weight: bold;
            border-bottom: 2px solid #B76E79; /* Garis bawah pink */
            padding-bottom: 2px;
        }

        /* Sedikit perbaikan agar layout header rapi */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 40px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .header-left { display: flex; align-items: center; }
        
        .brand-link { 
            display: flex; 
            align-items: center; 
            text-decoration: none; 
            color: #333; 
            font-family: 'Playfair Display', serif; /* Opsional: Font elegan */
        }
        
        .brand-text {
            margin-left: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #B76E79;
        }

        .nav-menu a {
            margin-left: 20px;
            text-decoration: none;
            color: #555;
            font-size: 16px;
            transition: 0.3s;
        }

        .nav-menu a:hover {
            color: #B76E79;
        }
    </style>
</head>
<body>

<header class="main-header">
    
    <div class="header-left">
        <a href="index.php" class="brand-link">
            <img src="images/logo_unity.png" alt="Logo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #B76E79;">
            <span class="brand-text">Unity Wedding Organizer</span>
        </a>
    </div>

    <div class="header-right">
        <nav class="nav-menu">
            
            <a href="index.php" 
               class="<?= ($halaman_saat_ini == 'index.php') ? 'menu-aktif' : '' ?>">
               Home
            </a>

            <a href="katalog.php" 
               class="<?= ($halaman_saat_ini == 'katalog.php' || $halaman_saat_ini == 'detail.php') ? 'menu-aktif' : '' ?>">
               Katalog
            </a>
            
            <a href="keranjang.php" 
               class="<?= ($halaman_saat_ini == 'keranjang.php') ? 'menu-aktif' : '' ?>">
               Keranjang (<?= $jml_keranjang; ?>)
            </a>

            <?php if (isset($_SESSION['user'])): ?>
                
                <a href="riwayat.php" 
                   class="<?= ($halaman_saat_ini == 'riwayat.php') ? 'menu-aktif' : '' ?>">
                   Riwayat
                </a>
                
                <a href="logout.php" class="btn-logout" style="color: red;">Logout</a>

            <?php else: ?>
                
                <a href="login.php" 
                   class="btn-login <?= ($halaman_saat_ini == 'login.php') ? 'menu-aktif' : '' ?>">
                   Login / Daftar
                </a>

            <?php endif; ?>

        </nav>
    </div>

</header>