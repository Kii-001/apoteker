<?php
session_start();
include 'config/database.php';
include 'config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek Sehat - Beli Obat Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Kesehatan Anda Prioritas Kami</h1>
                <p>Dapatkan obat-obatan berkualitas dengan mudah dan cepat. Layanan apotek online terpercaya.</p>
                <a href="produk.php" class="btn btn-primary">Lihat Produk</a>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="feature-grid">
                <div class="feature-item">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>Gratis Ongkir</h3>
                    <p>Untuk pembelian di atas Rp 100.000</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Obat Terjamin</h3>
                    <p>100% obat asli dan berkualitas</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <h3>24/7 Support</h3>
                    <p>Layanan konsultasi tersedia</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-qrcode"></i>
                    <h3>QRIS Payment</h3>
                    <p>Bayar mudah dengan scan QR</p>
                </div>
            </div>
        </div>
    </section>

    <section class="products-section">
        <div class="container">
            <h2>Produk Terpopuler</h2>
            <div class="products-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM obat WHERE stok > 0 ORDER BY created_at DESC LIMIT 8");
                while ($obat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/uploads/<?php echo $obat['gambar'] ?: 'default.jpg'; ?>" alt="<?php echo $obat['nama_obat']; ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo $obat['nama_obat']; ?></h3>
                        <p class="product-category"><?php echo $obat['kategori']; ?></p>
                        <p class="product-price"><?php echo formatRupiah($obat['harga']); ?></p>
                        <div class="product-actions">
                            <button class="btn btn-outline add-to-cart" data-id="<?php echo $obat['id']; ?>">
                                <i class="fas fa-cart-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="text-center">
                <a href="produk.php" class="btn btn-secondary">Lihat Semua Produk</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>