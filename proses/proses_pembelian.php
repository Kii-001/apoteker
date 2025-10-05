<?php
session_start();
include '../config/database.php';
include '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $nama = sanitizeInput($_POST['nama']);
    $email = sanitizeInput($_POST['email']);
    $telepon = sanitizeInput($_POST['telepon']);
    $alamat = sanitizeInput($_POST['alamat']);
    $metode_pembayaran = sanitizeInput($_POST['metode_pembayaran']);
    
    // Validasi data
    if (empty($nama) || empty($telepon) || empty($alamat)) {
        $_SESSION['error'] = "Harap lengkapi semua field yang wajib diisi!";
        header("Location: ../checkout.php");
        exit();
    }
    
    // Ambil cart dari session - langsung sebagai array
    $cart = $_SESSION['cart'] ?? [];
    
    if (empty($cart)) {
        $_SESSION['error'] = "Keranjang belanja kosong!";
        header("Location: ../keranjang.php");
        exit();
    }
    
    try {
        $pdo->beginTransaction();
        
        // Hitung total dan validasi stok
        $total = 0;
        $items_detail = [];
        
        foreach ($cart as $item) {
            // Cek stok tersedia
            $stmt = $pdo->prepare("SELECT nama_obat, stok, harga FROM obat WHERE id = ?");
            $stmt->execute([$item['id']]);
            $obat = $stmt->fetch();
            
            if (!$obat) {
                throw new Exception("Obat tidak ditemukan: " . $item['nama']);
            }
            
            if ($obat['stok'] < $item['quantity']) {
                throw new Exception("Stok tidak cukup untuk: " . $obat['nama_obat'] . ". Stok tersedia: " . $obat['stok']);
            }
            
            $subtotal = $obat['harga'] * $item['quantity'];
            $total += $subtotal;
            
            $items_detail[] = [
                'obat_id' => $item['id'],
                'nama_obat' => $obat['nama_obat'],
                'jumlah' => $item['quantity'],
                'harga' => $obat['harga'],
                'subtotal' => $subtotal
            ];
        }
        
        // Generate kode transaksi
        $kode_transaksi = generateKodeTransaksi();
        
        // Insert transaksi
        $stmt = $pdo->prepare("INSERT INTO transaksi (kode_transaksi, nama_pembeli, email_pembeli, no_telepon, alamat, total_harga, metode_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode_transaksi, $nama, $email, $telepon, $alamat, $total, $metode_pembayaran]);
        
        $transaksi_id = $pdo->lastInsertId();
        
        // Insert detail transaksi dan update stok
        foreach ($items_detail as $item) {
            // Insert detail transaksi
            $stmt = $pdo->prepare("INSERT INTO detail_transaksi (transaksi_id, obat_id, jumlah, harga) VALUES (?, ?, ?, ?)");
            $stmt->execute([$transaksi_id, $item['obat_id'], $item['jumlah'], $item['harga']]);
            
            // Update stok obat
            $stmt = $pdo->prepare("UPDATE obat SET stok = stok - ? WHERE id = ?");
            $stmt->execute([$item['jumlah'], $item['obat_id']]);
        }
        
        $pdo->commit();
        
        // Clear cart session
        unset($_SESSION['cart']);
        
        // Log activity
        logActivity("Transaksi baru: $kode_transaksi - $nama - " . formatRupiah($total));
        
        // Redirect ke halaman pembayaran
        header("Location: ../pembayaran.php?kode=" . $kode_transaksi);
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: ../checkout.php");
        exit();
    }
} else {
    header("Location: ../checkout.php");
    exit();
}
?>