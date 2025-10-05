<?php
session_start();
include 'config/database.php';
include 'config/functions.php';

// Langsung gunakan sebagai array
$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Apotek Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="cart-section">
        <div class="container">
            <h1>Keranjang Belanja</h1>
            
            <?php if (empty($cart)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Keranjang Anda kosong</h2>
                <p>Silakan tambahkan obat yang Anda butuhkan</p>
                <a href="produk.php" class="btn btn-primary">Belanja Sekarang</a>
            </div>
            <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php 
                    $total = 0;
                    foreach ($cart as $item): 
                        $subtotal = $item['harga'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <div class="cart-item" data-id="<?php echo $item['id']; ?>">
                        <div class="item-image">
                            <img src="assets/uploads/default.jpg" alt="<?php echo $item['nama']; ?>">
                        </div>
                        <div class="item-details">
                            <h3><?php echo $item['nama']; ?></h3>
                            <p class="item-price"><?php echo formatRupiah($item['harga']); ?></p>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn minus" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">-</button>
                            <span class="quantity"><?php echo $item['quantity']; ?></span>
                            <button class="quantity-btn plus" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                        </div>
                        <div class="item-subtotal">
                            <?php echo formatRupiah($subtotal); ?>
                        </div>
                        <button class="item-remove" onclick="removeItem(<?php echo $item['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <div class="summary-card">
                        <h3>Ringkasan Belanja</h3>
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span><?php echo formatRupiah($total); ?></span>
                        </div>
                        <div class="summary-line">
                            <span>Ongkos Kirim</span>
                            <span>Gratis</span>
                        </div>
                        <div class="summary-line total">
                            <span>Total</span>
                            <span><?php echo formatRupiah($total); ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary btn-block">
                            <i class="fas fa-credit-card"></i> Lanjut ke Checkout
                        </a>
                        <a href="produk.php" class="btn btn-outline btn-block">
                            <i class="fas fa-arrow-left"></i> Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
    function updateQuantity(productId, change) {
        fetch('proses/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                productId: productId,
                change: change
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function removeItem(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
            fetch('proses/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    productId: productId,
                    remove: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>