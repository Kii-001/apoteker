<?php
session_start();
include 'config/database.php';
include 'config/functions.php';

$kode_transaksi = $_GET['kode'] ?? '';

if (empty($kode_transaksi)) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE kode_transaksi = ?");
$stmt->execute([$kode_transaksi]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Apotek Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="payment-section">
        <div class="container">
            <div class="payment-container">
                <div class="payment-header">
                    <h1>Pembayaran QRIS</h1>
                    <p>Kode Transaksi: <strong><?php echo $transaksi['kode_transaksi']; ?></strong></p>
                </div>
                
                <div class="payment-content">
                    <div class="qris-section">
                        <div class="qris-code">
                            <img src="assets/images/qris-placeholder.png" alt="QRIS Code" id="qrisImage">
                            <div class="qris-amount">
                                <h3>Total Pembayaran</h3>
                                <p class="amount"><?php echo formatRupiah($transaksi['total_harga']); ?></p>
                            </div>
                        </div>
                        
                        <div class="payment-steps">
                            <h3>Cara Pembayaran:</h3>
                            <ol>
                                <li>Buka aplikasi e-wallet atau mobile banking Anda</li>
                                <li>Pilih fitur pembayaran QRIS</li>
                                <li>Scan QR code di atas</li>
                                <li>Konfirmasi pembayaran</li>
                                <li>Tunggu konfirmasi otomatis</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="payment-info">
                        <div class="info-card">
                            <h3>Informasi Transaksi</h3>
                            <div class="info-item">
                                <span>Nama Pembeli:</span>
                                <span><?php echo $transaksi['nama_pembeli']; ?></span>
                            </div>
                            <div class="info-item">
                                <span>No. Telepon:</span>
                                <span><?php echo $transaksi['no_telepon']; ?></span>
                            </div>
                            <div class="info-item">
                                <span>Alamat:</span>
                                <span><?php echo $transaksi['alamat']; ?></span>
                            </div>
                            <div class="info-item">
                                <span>Total:</span>
                                <span class="total"><?php echo formatRupiah($transaksi['total_harga']); ?></span>
                            </div>
                        </div>
                        
                        <div class="payment-actions">
                            <a href="status_pembayaran.php?kode=<?php echo $transaksi['kode_transaksi']; ?>" class="btn btn-primary">
                                <i class="fas fa-check"></i> Saya Sudah Bayar
                            </a>
                            <a href="index.php" class="btn btn-outline">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Auto refresh status pembayaran setiap 10 detik
        setInterval(function() {
            fetch('proses/cek_status_pembayaran.php?kode=<?php echo $transaksi['kode_transaksi']; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid') {
                        window.location.href = 'status_pembayaran.php?kode=<?php echo $transaksi['kode_transaksi']; ?>';
                    }
                });
        }, 10000);
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>