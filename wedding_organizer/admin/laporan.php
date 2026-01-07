<?php
session_start();
include '../koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user']) OR $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../login.php';</script>";
    exit();
}

// LOGIKA FILTER TANGGAL
$semuadata = array();
$tgl_mulai = "-";
$tgl_selesai = "-";

// Array untuk Grafik
$chart_labels = [];
$chart_data = [];

if (isset($_POST['kirim'])) {
    $tgl_mulai = $_POST['tglm'];
    $tgl_selesai = $_POST['tgls'];
    
    // Ambil data yang statusnya LUNAS saja
    $ambil = $koneksi->query("SELECT * FROM pemesanan JOIN users ON pemesanan.id_user=users.id_user 
        WHERE status_pemesanan='lunas' AND tanggal_acara BETWEEN '$tgl_mulai' AND '$tgl_selesai'
        ORDER BY tanggal_acara ASC"); // Diurutkan berdasarkan tanggal agar grafik rapi
    
    while($pecah = $ambil->fetch_assoc()){
        $semuadata[] = $pecah;

        // Masukkan data ke array grafik (Format Label: Nama Pelanggan - Tgl)
        $chart_labels[] = $pecah['nama'] . ' (' . date("d/m", strtotime($pecah['tanggal_acara'])) . ')';
        $chart_data[] = $pecah['total_harga'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* CSS Tambahan untuk Grafik & Print */
        .chart-container {
            position: relative; 
            height: 40vh; 
            width: 100%; 
            margin-bottom: 30px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* MEDIA QUERY UNTUK CETAK (PRINT) */
        @media print {
            .sidebar, .filter-form, .btn-print, .no-print {
                display: none !important;
            }
            .admin-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            body {
                background: white;
                -webkit-print-color-adjust: exact;
            }
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border: 1px solid #000;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
            }
        }
    </style>
</head>
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
        <h2>Laporan Keuangan (Pesanan Lunas)</h2>

        <div class="filter-form" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <form method="post" style="display: flex; gap: 10px; align-items: flex-end;">
                <div class="form-group" style="margin:0;">
                    <label>Dari Tanggal</label>
                    <input type="date" name="tglm" class="form-control" value="<?= $tgl_mulai ?>" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="tgls" class="form-control" value="<?= $tgl_selesai ?>" required>
                </div>
                <button class="btn-cta" name="kirim"><i class="fas fa-search"></i> Lihat</button>
                
                <?php if (!empty($semuadata)): ?>
                <button type="button" class="btn-cta btn-print" onclick="window.print()" style="background: #27ae60;">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!empty($semuadata)): ?>
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>
        <?php endif; ?>

        <div style="background: white; padding: 20px; border-radius: 10px;">
            
            <?php if ($tgl_mulai != "-"): ?>
                <p class="no-print" style="margin-bottom:10px;">Periode: <b><?= date("d M Y", strtotime($tgl_mulai)) ?> s/d <?= date("d M Y", strtotime($tgl_selesai)) ?></b></p>
            <?php endif; ?>

            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee; text-align: left;">
                        <th style="padding: 10px;">No</th>
                        <th style="padding: 10px;">Pelanggan</th>
                        <th style="padding: 10px;">Tanggal Acara</th>
                        <th style="padding: 10px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($semuadata as $key => $value): ?>
                    <?php $total += $value['total_harga']; ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;"><?= $key+1; ?></td>
                        <td style="padding: 10px;"><?= $value['nama']; ?></td>
                        <td style="padding: 10px;"><?= date("d M Y", strtotime($value['tanggal_acara'])); ?></td>
                        <td style="padding: 10px;">Rp <?= number_format($value['total_harga']); ?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right; padding: 10px;">TOTAL PENDAPATAN</th>
                        <th style="padding: 10px;">Rp <?= number_format($total); ?></th>
                    </tr>
                </tfoot>
            </table>
            
            <?php if (empty($semuadata)): ?>
                <p style="text-align: center; margin-top: 20px; color: #777;">Silakan pilih tanggal untuk melihat data.</p>
            <?php endif; ?>
        </div>

    </div>

    <script>
        <?php if (!empty($semuadata)): ?>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar', // Bisa diganti 'line' jika ingin garis
            data: {
                labels: <?= json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($chart_data); ?>,
                    backgroundColor: 'rgba(231, 76, 60, 0.6)', // Warna batang (sesuai tema merahmu)
                    borderColor: 'rgba(192, 57, 43, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // Format Rupiah di sumbu Y
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Grafik Pendapatan Periode Ini'
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>