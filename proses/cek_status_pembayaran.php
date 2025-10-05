<?php
session_start();
include '../config/database.php';

$kode_transaksi = $_GET['kode'] ?? '';

if (empty($kode_transaksi)) {
    echo json_encode(['status' => 'error']);
    exit;
}

$stmt = $pdo->prepare("SELECT status FROM transaksi WHERE kode_transaksi = ?");
$stmt->execute([$kode_transaksi]);
$transaksi = $stmt->fetch();

if ($transaksi) {
    echo json_encode(['status' => $transaksi['status']]);
} else {
    echo json_encode(['status' => 'error']);
}
?>