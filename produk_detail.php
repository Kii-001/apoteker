<?php
session_start();
include 'config/database.php';
include 'config/functions.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: produk.php");
    exit();
}

// Get product details
$stmt = $pdo->prepare("SELECT * FROM obat WHERE id = ?");
$stmt->execute([$id]);
$obat = $stmt->fetch();

if (!$obat) {
    header("Location: produk.php");
    exit();
}

// Get related products
$stmt = $pdo->prepare("SELECT * FROM obat WHERE kategori = ? AND id != ? AND stok > 0 ORDER BY RAND() LIMIT 4");
$stmt->execute([$obat['kategori'], $id]);
$related_products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $obat['nama_obat']; ?> - Apotek Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="product-detail-section">
        <div class="container">
            <nav class="breadcrumb">
                <a href="index.php">Beranda</a>
                <span>/</span>
                <a href="produk.php">Produk</a>
                <span>/</span>
                <span><?php echo $obat['nama_obat']; ?></span>
            </nav>

            <div class="product-detail">
                <div class="product-gallery">
                    <div class="main-image">
                        <img src="assets/uploads/<?php echo $obat['gambar'] ?: 'default.jpg'; ?>" 
                             alt="<?php echo $obat['nama_obat']; ?>" id="mainImage">
                    </div>
                </div>

                <div class="product-info">
                    <h1><?php echo $obat['nama_obat']; ?></h1>
                    
                    <div class="product-meta">
                        <span class="category"><?php echo $obat['kategori']; ?></span>
                        <span class="stock <?php echo $obat['stok'] == 0 ? 'out-of-stock' : ($obat['stok'] < 10 ? 'low-stock' : 'in-stock'); ?>">
                            <?php echo $obat['stok'] == 0 ? 'Stok Habis' : 'Stok: ' . $obat['stok']; ?>
                        </span>
                    </div>

                    <div class="product-price">
                        <?php echo formatRupiah($obat['harga']); ?>
                    </div>

                    <?php if ($obat['stok'] > 0): ?>
                    <div class="product-actions">
                        <div class="quantity-selector">
                            <button type="button" id="decreaseQty">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="<?php echo $obat['stok']; ?>">
                            <button type="button" id="increaseQty">+</button>
                        </div>
                        <button class="btn btn-primary add-to-cart-detail" 
                                data-id="<?php echo $obat['id']; ?>"
                                data-name="<?php echo $obat['nama_obat']; ?>"
                                data-price="<?php echo $obat['harga']; ?>"
                                data-image="<?php echo $obat['gambar'] ?: 'default.jpg'; ?>"
                                data-stock="<?php echo $obat['stok']; ?>">
                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="out-of-stock-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Maaf, produk ini sedang tidak tersedia</p>
                    </div>
                    <?php endif; ?>

                    <div class="product-description">
                        <h3>Deskripsi Produk</h3>
                        <p><?php echo nl2br($obat['deskripsi'] ?: 'Tidak ada deskripsi tersedia.'); ?></p>
                    </div>

                    <div class="product-features">
                        <div class="feature">
                            <i class="fas fa-shield-alt"></i>
                            <span>Obat Terjamin Keasliannya</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Gratis Ongkir</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-headset"></i>
                            <span>Konsultasi Gratis</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($related_products)): ?>
            <section class="related-products">
                <h2>Produk Serupa</h2>
                <div class="products-grid">
                    <?php foreach ($related_products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/uploads/<?php echo $product['gambar'] ?: 'default.jpg'; ?>" 
                                 alt="<?php echo $product['nama_obat']; ?>">
                            <?php if ($product['stok'] < 10): ?>
                            <span class="stock-badge low-stock">Stok Menipis</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?php echo $product['nama_obat']; ?></h3>
                            <p class="product-category"><?php echo $product['kategori']; ?></p>
                            <p class="product-price"><?php echo formatRupiah($product['harga']); ?></p>
                            <div class="product-actions">
                                <a href="produk_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <button class="btn btn-primary add-to-cart" 
                                        data-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </section>

    <script>
    // Quantity selector functionality
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decreaseQty');
        const increaseBtn = document.getElementById('increaseQty');
        const addToCartBtn = document.querySelector('.add-to-cart-detail');
        
        if (decreaseBtn && increaseBtn) {
            decreaseBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                const maxStock = parseInt(quantityInput.max);
                if (currentValue < maxStock) {
                    quantityInput.value = currentValue + 1;
                }
            });
            
            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value);
                const maxStock = parseInt(this.max);
                const minStock = parseInt(this.min);
                
                if (value < minStock) {
                    this.value = minStock;
                } else if (value > maxStock) {
                    this.value = maxStock;
                }
            });
        }
        
        // Add to cart functionality for detail page
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const productId = this.dataset.id;
                const productName = this.dataset.name;
                const productPrice = parseFloat(this.dataset.price);
                const productImage = this.dataset.image;
                const quantity = parseInt(document.getElementById('quantity').value);
                
                const product = {
                    id: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: quantity
                };
                
                // Add to cart
                fetch('proses/update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product: product
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Produk berhasil ditambahkan ke keranjang!');
                        updateCartCount(data.cartCount);
                    } else {
                        alert('Gagal menambahkan produk: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menambahkan produk ke keranjang');
                });
            });
        }
        
        // Add to cart for related products
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const product = {
                    id: this.dataset.id,
                    name: productCard.querySelector('h3').textContent,
                    price: parseFloat(productCard.querySelector('.product-price').textContent.replace(/[^\d]/g, '')),
                    image: productCard.querySelector('img').src.split('/').pop(),
                    quantity: 1
                };
                
                fetch('proses/update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product: product
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Produk berhasil ditambahkan ke keranjang!');
                        updateCartCount(data.cartCount);
                    } else {
                        alert('Gagal menambahkan produk: ' + data.message);
                    }
                });
            });
        });
        
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-check-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--success);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                box-shadow: var(--shadow);
                z-index: 10000;
                animation: slideInRight 0.3s ease-out;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        function updateCartCount(count) {
            const cartCounts = document.querySelectorAll('.cart-count');
            cartCounts.forEach(cartCount => {
                cartCount.textContent = count;
                cartCount.style.display = count > 0 ? 'flex' : 'none';
            });
        }
    });
    </script>

    <style>
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
        font-size: 0.875rem;
        color: var(--secondary);
    }
    
    .breadcrumb a {
        color: var(--secondary);
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        color: var(--primary);
    }
    
    .product-detail {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-bottom: 4rem;
    }
    
    .product-gallery {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .main-image {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: var(--shadow);
        text-align: center;
    }
    
    .main-image img {
        max-width: 100%;
        height: 400px;
        object-fit: contain;
    }
    
    .product-info h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--dark);
    }
    
    .product-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: center;
    }
    
    .category {
        background: var(--primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .stock {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .in-stock {
        background: var(--success);
        color: white;
    }
    
    .low-stock {
        background: var(--warning);
        color: white;
    }
    
    .out-of-stock {
        background: var(--danger);
        color: white;
    }
    
    .product-price {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary);
        margin-bottom: 2rem;
    }
    
    .product-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        border: 2px solid var(--border);
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .quantity-selector button {
        background: white;
        border: none;
        padding: 0.75rem 1rem;
        cursor: pointer;
        font-size: 1.125rem;
        font-weight: bold;
    }
    
    .quantity-selector input {
        width: 60px;
        border: none;
        text-align: center;
        font-size: 1rem;
        font-weight: bold;
        padding: 0.75rem 0;
    }
    
    .quantity-selector input:focus {
        outline: none;
    }
    
    .add-to-cart-detail {
        flex: 1;
        justify-content: center;
    }
    
    .out-of-stock-message {
        text-align: center;
        padding: 2rem;
        background: #fee2e2;
        border-radius: 0.5rem;
        color: #991b1b;
        margin-bottom: 2rem;
    }
    
    .out-of-stock-message i {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .product-description {
        margin-bottom: 2rem;
    }
    
    .product-description h3 {
        margin-bottom: 1rem;
        color: var(--dark);
    }
    
    .product-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .feature {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--light);
        border-radius: 0.5rem;
    }
    
    .feature i {
        color: var(--primary);
        font-size: 1.25rem;
    }
    
    .related-products {
        border-top: 1px solid var(--border);
        padding-top: 3rem;
    }
    
    .related-products h2 {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 1.75rem;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @media (max-width: 768px) {
        .product-detail {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .product-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .quantity-selector {
            justify-content: center;
        }
    }
    </style>

    <?php include 'includes/footer.php'; ?>
</body>
</html>