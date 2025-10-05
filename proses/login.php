<?php
session_start();
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama_lengkap'];
        $_SESSION['admin_username'] = $admin['username'];
        
        header("Location: ../dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Username atau password salah!";
        header("Location: ../index.php");
        exit();
    }
}
?>