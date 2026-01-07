<?php
session_start();
include '../koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user']) OR $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../login.php';</script>";
    exit();
}

// 1. HITUNG RINGKASAN DATA
$pesanan_pending = $koneksi->query("SELECT * FROM pemesanan WHERE status_pemesanan='pending' OR status_pemesanan='menunggu_verifikasi'")->num_rows;
$jumlah_layanan = $koneksi->query("SELECT * FROM layanan")->num_rows;
$jumlah_pelanggan = $koneksi->query("SELECT * FROM users WHERE role='pelanggan'")->num_rows;

// Hitung Total Pendapatan (Hanya yang LUNAS)
$result_pendapatan = $koneksi->query("SELECT SUM(total_harga) AS total FROM pemesanan WHERE status_pemesanan='lunas'");
$row_pendapatan = $result_pendapatan->fetch_assoc();
$total_pendapatan = $row_pendapatan['total'];

// 2. DATA UNTUK GRAFIK (Pendapatan Per Bulan di Tahun Ini)
$tahun_ini = date('Y');
$data_grafik = [];
$label_grafik = [];

// Loop 12 Bulan
for ($i = 1; $i <= 12; $i++) {
    $query_bulan = $koneksi->query("SELECT SUM(total_harga) AS total FROM pemesanan 
        WHERE status_pemesanan='lunas' AND MONTH(tanggal_acara)='$i' AND YEAR(tanggal_acara)='$tahun_ini'");
    $data = $query_bulan->fetch_assoc();
    $data_grafik[] = $data['total'] ? $data['total'] : 0; // Jika null ganti 0
    $label_grafik[] = date("F", mktime(0, 0, 0, $i, 10)); // Nama Bulan
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> </head>
<body class="admin-body">

    <div class="sidebar">
        <h2>Admin Panel</h2>
        
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="layanan.php" class="<?= basename($_SERVER['PHP_SELF'])=='layanan.php'?'active':'' ?>"><i class="fas fa-box"></i> Data Layanan</a>
        <a href="kategori.php" class="<?= basename($_SERVER['PHP_SELF'])=='kategori.php'?'active':'' ?>"><i class="fas fa-tags"></i> Data Kategori</a>
        <a href="pesanan.php" class="<?= basename($_SERVER['PHP_SELF'])=='pesanan.php'?'active':'' ?>"><i class="fas fa-shopping-cart"></i> Pesanan Masuk</a>
        <a href="laporan.php" class="<?= basename($_SERVER['PHP_SELF'])=='laporan.php'?'active':'' ?>"><i class="fas fa-file-alt"></i> Laporan</a>

        <a href="tambah_pesanan.php" class="btn-sidebar-new">
            <i class="fas fa-plus-circle"></i> Input Pesanan Baru
        </a>

        <a href="../logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="admin-content">
        <h2>Dashboard Ringkasan</h2>

        <div class="dashboard-cards">
            <div class="card" style="border-left: 5px solid #e67e22;">
                <h3 style="color: #e67e22;"><?php echo $pesanan_pending; ?></h3>
                <p>Pesanan Pending</p>
            </div>

            <div class="card" style="border-left: 5px solid #3498db;">
                <h3 style="color: #3498db;"><?php echo $jumlah_layanan; ?></h3>
                <p>Jumlah Layanan</p>
            </div>

            <div class="card" style="border-left: 5px solid #9b59b6;">
                <h3 style="color: #9b59b6;"><?php echo $jumlah_pelanggan; ?></h3>
                <p>Pelanggan</p>
            </div>

            <div class="card" style="border-left: 5px solid #27ae60;">
                <h3 style="color: #27ae60;">Rp <?php echo number_format($total_pendapatan); ?></h3>
                <p>Total Pendapatan</p>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; margin-top: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px; color: #444;">Statistik Pendapatan Tahun <?= $tahun_ini ?></h3>
            <div style="height: 350px;">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>

    </div>

    <script>
        const ctx = document.getElementById('incomeChart').getContext('2d');
        const incomeChart = new Chart(ctx, {
            type: 'line', // Ganti 'bar' jika ingin diagram batang
            data: {
                labels: <?= json_encode($label_grafik); ?>,
                datasets: [{
                    label: 'Pendapatan Bulanan (Rp)',
                    data: <?= json_encode($data_grafik); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 2,
                    tension: 0.3, // Membuat garis agak melengkung
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                        }
                    }
                }
            }
        });
    </script>

    <style>
        .dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h3 { font-size: 28px; margin-bottom: 5px; color: #c0392b; }
        .card p { color: #777; font-size: 14px; }
    </style>
</body>
</html>