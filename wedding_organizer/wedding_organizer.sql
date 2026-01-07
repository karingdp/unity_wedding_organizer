-- 1. Tabel Users (Menampung Admin & Pelanggan)
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    no_hp VARCHAR(15),
    alamat TEXT,
    role ENUM('admin', 'pelanggan') DEFAULT 'pelanggan',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Kategori Layanan (Supaya dinamis: Paket, Dekor, Cake, Undangan)
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL, -- Contoh isi: 'Wedding Cake', 'Dekorasi', 'Undangan'
    slug VARCHAR(50) NOT NULL -- Contoh: 'wedding-cake' (untuk URL friendly)
);

-- 3. Tabel Layanan (Gabungan semua katalog)
CREATE TABLE layanan (
    id_layanan INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_layanan VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    gambar VARCHAR(255), -- Path foto layanan
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);

-- 4. Tabel Pemesanan (Header Transaksi)
CREATE TABLE pemesanan (
    id_pemesanan INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(20) UNIQUE, -- Contoh: WO-20231001-001
    id_user INT NOT NULL,
    tanggal_acara DATE NOT NULL, -- Tanggal Nikah (PENTING)
    lokasi_acara TEXT NOT NULL,
    total_harga DECIMAL(12, 2) NOT NULL,
    status_pemesanan ENUM('pending', 'menunggu_pembayaran', 'lunas', 'selesai', 'batal') DEFAULT 'pending',
    metode_pemesanan ENUM('online', 'offline') DEFAULT 'online', -- Offline = datang ke lokasi
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, -- Waktu input data
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- 5. Tabel Detail Pemesanan (Barang apa saja yang dibeli)
CREATE TABLE detail_pemesanan (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pemesanan INT NOT NULL,
    id_layanan INT NOT NULL,
    harga_saat_ini DECIMAL(10, 2) NOT NULL, -- Harga dicatat agar jika harga layanan naik, history aman
    jumlah INT DEFAULT 1,
    subtotal DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (id_pemesanan) REFERENCES pemesanan(id_pemesanan),
    FOREIGN KEY (id_layanan) REFERENCES layanan(id_layanan)
);

-- 6. Tabel Custom Request (Permintaan Khusus)
CREATE TABLE custom_request (
    id_request INT AUTO_INCREMENT PRIMARY KEY,
    id_pemesanan INT NOT NULL,
    catatan_request TEXT NOT NULL, -- Contoh: "Bunga ganti mawar merah semua"
    biaya_tambahan DECIMAL(10, 2) DEFAULT 0, -- Diisi admin jika request butuh biaya extra
    status_request ENUM('pending', 'disetujui', 'ditolak') DEFAULT 'pending',
    FOREIGN KEY (id_pemesanan) REFERENCES pemesanan(id_pemesanan)
);

-- 7. Tabel Pembayaran
CREATE TABLE pembayaran (
    id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
    id_pemesanan INT NOT NULL,
    tanggal_bayar DATETIME NOT NULL,
    jumlah_bayar DECIMAL(12, 2) NOT NULL,
    bukti_pembayaran VARCHAR(255), -- Foto struk transfer
    status_verifikasi ENUM('menunggu', 'valid', 'invalid') DEFAULT 'menunggu',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pemesanan) REFERENCES pemesanan(id_pemesanan)
);