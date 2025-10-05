<?php
session_start();
include '../../config/database.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_obat = $_POST['nama_obat'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Handle file upload
    $gambar = 'default.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $gambar = uniqid() . '.' . $filetype;
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../../assets/uploads/' . $gambar);
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO obat (nama_obat, deskripsi, kategori, harga, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama_obat, $deskripsi, $kategori, $harga, $stok, $gambar]);
        
        $_SESSION['success'] = "Obat berhasil ditambahkan";
        header("Location: ../obat.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal menambahkan obat: " . $e->getMessage();
        header("Location: ../obat.php?action=tambah");
        exit();
    }
}
?>