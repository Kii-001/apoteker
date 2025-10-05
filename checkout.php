<?php
session_start();
include 'config/database.php';
include 'config/functions.php';

// Ambil data dari cart - langsung sebagai array
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: keranjang.php");
    exit();
}

// Hitung total
$total = 0;
foreach ($cart as $item) {
    $total += $item['harga'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Apotek Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="checkout-section">
        <div class="container">
            <h1>Checkout</h1>
            
            <div class="checkout-grid">
                <div class="checkout-form">
                    <h2>Informasi Pembeli</h2>
                    <form action="proses/proses_pembelian.php" method="POST">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap *</label>
                            <input type="text" id="nama" name="nama" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                        
                        <div class="form-group">
                            <label for="telepon">No. Telepon *</label>
                            <input type="tel" id="telepon" name="telepon" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat">Alamat Lengkap *</label>
                            <textarea id="alamat" name="alamat" rows="4" required></textarea>
                        </div>
                        
                        <h2>Metode Pembayaran</h2>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="qris" name="metode_pembayaran" value="qris" checked>
                                <label for="qris">
                                    <i class="fas fa-qrcode"></i>
                                    <span>QRIS</span>
                                    <p>Bayar dengan scan QR code melalui aplikasi e-wallet atau mobile banking</p>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-credit-card"></i> Lanjutkan ke Pembayaran
                        </button>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h2>Ringkasan Pesanan</h2>
                    <div class="summary-items">
                        <?php foreach ($cart as $item): ?>
                        <div class="summary-item">
                            <div class="item-info">
                                <h4><?php echo $item['nama']; ?></h4>
                                <p><?php echo formatRupiah($item['harga']); ?> x <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="item-total">
                                <?php echo formatRupiah($item['harga'] * $item['quantity']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-total">
                        <div class="total-line">
                            <span>Subtotal</span>
                            <span><?php echo formatRupiah($total); ?></span>
                        </div>
                        <div class="total-line">
                            <span>Ongkos Kirim</span>
                            <span>Gratis</span>
                        </div>
                        <div class="total-line grand-total">
                            <span>Total</span>
                            <span><?php echo formatRupiah($total); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>