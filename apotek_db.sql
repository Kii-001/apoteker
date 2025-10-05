-- Buat database
CREATE DATABASE apotek_db;
USE apotek_db;

-- Tabel admin
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel obat
CREATE TABLE obat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_obat VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    kategori VARCHAR(50),
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel transaksi
CREATE TABLE transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(20) NOT NULL UNIQUE,
    nama_pembeli VARCHAR(100) NOT NULL,
    email_pembeli VARCHAR(100),
    no_telepon VARCHAR(20) NOT NULL,
    alamat TEXT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    metode_pembayaran VARCHAR(50),
    bukti_pembayaran VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel detail transaksi
CREATE TABLE detail_transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaksi_id INT NOT NULL,
    obat_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (obat_id) REFERENCES obat(id) ON DELETE CASCADE
);

-- Insert admin default (password: admin123)
INSERT INTO admin (username, password, nama_lengkap) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Apotek');