<?php
// Solusi sederhana - langsung sebagai array
$cart = $_SESSION['cart'] ?? [];
$cart_count = 0;

// Hitung total items di cart
foreach ($cart as $item) {
    $cart_count += $item['quantity'] ?? 0;
}
?>
<header>
    <div class="container">
        <div class="header-content">
            <a href="index.php" class="logo">
                <i class="fas fa-clinic-medical"></i> Apotek Sehat
            </a>
            
            <nav>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="produk.php">Produk</a></li>
                    <li><a href="about.php">Tentang</a></li>
                    <li><a href="contact.php">Kontak</a></li>
                    <li>
                        <a href="keranjang.php" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if ($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>