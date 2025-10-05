<?php
session_start();
include 'config/database.php';
include 'config/functions.php';

// Filter dan pencarian
$kategori = $_GET['kategori'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM obat WHERE stok > 0";
$params = [];

if (!empty($kategori)) {
    $query .= " AND kategori = ?";
    $params[] = $kategori;
}

if (!empty($search)) {
    $query .= " AND nama_obat LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$obat = $stmt->fetchAll();

// Ambil kategori unik untuk filter
$kategori_stmt = $pdo->query("SELECT DISTINCT kategori FROM obat WHERE kategori IS NOT NULL");
$kategories = $kategori_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Apotek Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="products-page">
        <div class="container">
            <div class="page-header">
                <h1>Daftar Obat</h1>
                <p>Temukan obat yang Anda butuhkan</p>
            </div>

            <div class="products-filter">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Cari obat..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <select name="kategori" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategories as $kat): ?>
                        <option value="<?php echo $kat['kategori']; ?>" <?php echo $kategori == $kat['kategori'] ? 'selected' : ''; ?>>
                            <?php echo $kat['kategori']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="products-grid">
                <?php if (count($obat) > 0): ?>
                    <?php foreach ($obat as $item): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <a href="produk_detail.php?id=<?php echo $item['id']; ?>">
                                <img src="assets/uploads/<?php echo $item['gambar'] ?: 'default.jpg'; ?>" alt="<?php echo $item['nama_obat']; ?>">
                            </a>
                            <?php if ($item['stok'] < 10): ?>
                            <span class="stock-badge low-stock">Stok Menipis</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3>
                                <a href="produk_detail.php?id=<?php echo $item['id']; ?>" class="product-link">
                                    <?php echo $item['nama_obat']; ?>
                                </a>
                            </h3>
                            <p class="product-category"><?php echo $item['kategori']; ?></p>
                            <p class="product-description"><?php echo substr($item['deskripsi'], 0, 100); ?>...</p>
                            <div class="product-meta">
                                <span class="stock">Stok: <?php echo $item['stok']; ?></span>
                                <span class="price"><?php echo formatRupiah($item['harga']); ?></span>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-outline add-to-cart" data-id="<?php echo $item['id']; ?>">
                                    <i class="fas fa-cart-plus"></i> Tambah
                                </button>
                                <a href="produk_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <h3>Obat tidak ditemukan</h3>
                        <p>Coba gunakan kata kunci lain atau lihat kategori yang berbeda</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>