<?php
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function generateKodeTransaksi() {
    return 'TRX' . date('YmdHis') . rand(100, 999);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: /apotek-web/admin/");
        exit();
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('/index.php', '', $script);
    return $protocol . "://" . $host . $path;
}

function getCartTotal() {
    $cart = json_decode($_SESSION['cart'] ?? '[]', true);
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['harga'] * $item['quantity'];
    }
    return $total;
}

function getCartItemCount() {
    $cart = json_decode($_SESSION['cart'] ?? '[]', true);
    $count = 0;
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function sendEmailNotification($to, $subject, $message) {
    // Basic email function - implement according to your server setup
    $headers = "From: no-reply@apoteksehat.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function logActivity($activity) {
    // Simple activity logging
    $log = date('Y-m-d H:i:s') . " - " . $activity . "\n";
    file_put_contents('../logs/activity.log', $log, FILE_APPEND | LOCK_EX);
}
?>