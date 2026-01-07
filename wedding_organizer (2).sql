-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Jan 2026 pada 21.32
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wedding_organizer`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `custom_request`
--

CREATE TABLE `custom_request` (
  `id_request` int(11) NOT NULL,
  `id_pemesanan` int(11) NOT NULL,
  `catatan_request` text NOT NULL,
  `biaya_tambahan` decimal(10,2) DEFAULT 0.00,
  `status_request` enum('pending','disetujui','ditolak') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pemesanan`
--

CREATE TABLE `detail_pemesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pemesanan` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL,
  `harga_saat_ini` decimal(10,2) NOT NULL,
  `jumlah` int(11) DEFAULT 1,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_pemesanan`
--

INSERT INTO `detail_pemesanan` (`id_detail`, `id_pemesanan`, `id_layanan`, `harga_saat_ini`, `jumlah`, `subtotal`) VALUES
(10, 9, 4, 1800000.00, 1, 1800000.00),
(11, 10, 18, 85000000.00, 1, 85000000.00),
(12, 10, 6, 3500000.00, 1, 3500000.00),
(13, 11, 17, 60000000.00, 1, 60000000.00),
(14, 11, 13, 1200000.00, 1, 1200000.00),
(15, 12, 17, 60000000.00, 1, 60000000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `slug`) VALUES
(1, 'Wedding Cake', 'wedding-cake'),
(2, 'Dekorasi', 'dekorasi'),
(3, 'Undangan ', 'undangan-'),
(4, 'Paket Wedding', 'paket-wedding');

-- --------------------------------------------------------

--
-- Struktur dari tabel `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','habis') DEFAULT 'tersedia',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `id_kategori`, `nama_layanan`, `deskripsi`, `harga`, `gambar`, `status`, `created_at`) VALUES
(3, 1, 'Premium Custom Cake', 'Desain sesuai request', 8000000.00, '04012026075739_Make your special day unforgettable with Luxury….jfif', 'tersedia', '2026-01-04 13:57:39'),
(4, 1, 'Minimalist Wedding Cake', 'Simple modern design', 1800000.00, '04012026080032_Less is always more_ This minimalist draped….jfif', 'tersedia', '2026-01-04 14:00:32'),
(5, 1, 'Elegant Floral Cake', 'Vanilla + strawberry, dekor bunga', 6500000.00, '04012026080314_A true showstopper! This elegant cake is only a….jfif', 'tersedia', '2026-01-04 14:03:14'),
(6, 1, 'Classic White Wedding Cake', 'Vanilla cake, buttercream', 3500000.00, '04012026081316_classic_white_cake.jfif.jfif', 'tersedia', '2026-01-04 14:13:17'),
(7, 2, 'Dekorasi Akad Minimalis', 'Background simple & bunga', 5000000.00, '04012026082911_Dekorasi PERNIKAHAN Minimalis sederhana;_Dekorasi….jfif', 'tersedia', '2026-01-04 14:29:11'),
(9, 2, 'Dekorasi Resepsi Indoor', 'Pelaminan + bunga full', 18000000.00, '04012026084341_download.jfif', 'tersedia', '2026-01-04 14:43:41'),
(10, 2, 'Dekorasi Akad Premium', 'Backdrop custom + lighting', 10000000.00, '04012026084441_Dekorasi pernikahan Simple tapi Elegan; Dekorasi….jfif', 'tersedia', '2026-01-04 14:44:41'),
(11, 2, 'Dekorasi Resepsi Outdoor', 'Dekorasi Resepsi Outdoor', 25000000.00, '04012026084851_download (2).jfif', 'tersedia', '2026-01-04 14:48:51'),
(12, 3, 'Undangan Cetak Minimalis', 'Harga / 100 pcs, model bisa custom', 600000.00, '04012026085334_Undangan pernikahan dengan desain kekinian….jfif', 'tersedia', '2026-01-04 14:53:34'),
(13, 3, 'Undangan Cetak Elegant', 'Harga / 100 pcs, model bisa custom ', 1200000.00, '04012026085519_Wedding invitations (only samples) ✅The price….jfif', 'tersedia', '2026-01-04 14:55:19'),
(14, 3, 'Undangan Cetak Premium', 'Harga / 100 pcs, model bisa costum', 2000000.00, '04012026090019_Custom Wedding Invitation _  Undangan Pernikahan _ Wedding Flat Lay _ Art _ Design.jfif', 'tersedia', '2026-01-04 15:00:19'),
(15, 4, 'Paket Silver', 'Akad + dekor basic + undangan digital', 15000000.00, '04012026090912_f2233bc5-219b-44eb-aeae-c7cfcc5f15f4.png', 'tersedia', '2026-01-04 15:09:12'),
(16, 4, 'Paket Gold', 'Akad + resepsi + dekor + cake', 35000000.00, '04012026091124_paket_gold.png', 'tersedia', '2026-01-04 15:11:24'),
(17, 4, 'Paket Platinum', 'Full service + custom dekor', 60000000.00, '04012026091354_paket_platinum.png', 'tersedia', '2026-01-04 15:13:54'),
(18, 4, 'Paket Exclusive', 'All-in + vendor premium, custom dekor', 85000000.00, '04012026091458_paket_exclusive.png', 'tersedia', '2026-01-04 15:14:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pemesanan` int(11) NOT NULL,
  `tanggal_bayar` datetime NOT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','valid','invalid') DEFAULT 'menunggu',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pemesanan`, `tanggal_bayar`, `jumlah_bayar`, `bukti_pembayaran`, `status_verifikasi`, `created_at`) VALUES
(5, 10, '2026-01-05 13:26:18', 88550000.00, '20260105132618_WhatsApp Image 2026-01-03 at 14.19.34.jpeg', 'menunggu', '2026-01-05 19:26:18'),
(6, 12, '2026-01-06 19:57:56', 60050000.00, '20260106195756_WhatsApp Image 2026-01-03 at 14.19.34.jpeg', 'valid', '2026-01-07 01:57:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` int(11) NOT NULL,
  `kode_transaksi` varchar(20) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_acara` date NOT NULL,
  `lokasi_acara` text NOT NULL,
  `catatan` text DEFAULT NULL,
  `opsi_pengiriman` varchar(50) DEFAULT 'Diantar',
  `total_harga` decimal(12,2) NOT NULL,
  `status_pemesanan` enum('pending','menunggu_pembayaran','menunggu_verifikasi','lunas','selesai','batal') DEFAULT 'pending',
  `metode_pemesanan` enum('online','offline') DEFAULT 'online',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `kode_transaksi`, `id_user`, `tanggal_acara`, `lokasi_acara`, `catatan`, `opsi_pengiriman`, `total_harga`, `status_pemesanan`, `metode_pemesanan`, `created_at`) VALUES
(9, 'INV-20260105130546', 1, '2026-01-05', 'w', 'q', 'Diantar', 1850000.00, 'pending', 'online', '2026-01-05 19:05:46'),
(10, 'INV-20260105132556', 2, '2026-01-24', 's', 's', 'Diantar', 88550000.00, 'pending', 'online', '2026-01-05 19:25:56'),
(11, 'INV-20260106-95CD', 2, '2026-01-06', 'sby', NULL, 'Diantar', 61200000.00, 'menunggu_verifikasi', 'online', '2026-01-06 16:23:30'),
(12, 'TRX-20260106-1131', 1, '2026-01-07', 'sby', '', 'Diantar / Dilayani di Lokasi', 60050000.00, 'lunas', 'online', '2026-01-07 01:57:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('admin','pelanggan') DEFAULT 'pelanggan',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `no_hp`, `alamat`, `role`, `created_at`) VALUES
(1, 'karin', '3130024039@student.unusa.ac.id', '$2y$10$nc7gHAwNAcHBKiEByIsmR.8OypCVkHwS3.L8b/dKC7uX7g8J6IIBW', '089675647937', NULL, 'pelanggan', '2026-01-03 12:04:00'),
(2, 'admin', 'admin@wo.com', '$2y$10$z7rr1YAbV5xWS2tJKWgK5.9XwkVq3MC9Aa8IITu78MZhv4hLyNMdK', '089675647937', NULL, 'admin', '2026-01-03 12:21:48');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `custom_request`
--
ALTER TABLE `custom_request`
  ADD PRIMARY KEY (`id_request`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indeks untuk tabel `detail_pemesanan`
--
ALTER TABLE `detail_pemesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pemesanan` (`id_pemesanan`),
  ADD KEY `id_layanan` (`id_layanan`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `custom_request`
--
ALTER TABLE `custom_request`
  MODIFY `id_request` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_pemesanan`
--
ALTER TABLE `detail_pemesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `custom_request`
--
ALTER TABLE `custom_request`
  ADD CONSTRAINT `custom_request_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Ketidakleluasaan untuk tabel `detail_pemesanan`
--
ALTER TABLE `detail_pemesanan`
  ADD CONSTRAINT `detail_pemesanan_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`),
  ADD CONSTRAINT `detail_pemesanan_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`);

--
-- Ketidakleluasaan untuk tabel `layanan`
--
ALTER TABLE `layanan`
  ADD CONSTRAINT `layanan_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Ketidakleluasaan untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
