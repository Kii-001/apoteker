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

// Ambil detail transaksi
$detail_stmt = $pdo->prepare("
    SELECT dt.*, o.nama_obat 
    FROM detail_transaksi dt 
    JOIN obat o ON dt.obat_id = o.id 
    WHERE dt.transaksi_id = ?
");
$detail_stmt->execute([$transaksi['id']]);
$detail_transaksi = $detail_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - Apotek Sehat</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="payment-status-section">
        <div class="container">
            <div class="status-container">
                <div class="status-header">
                    <?php if ($transaksi['status'] == 'paid'): ?>
                    <div class="status-success">
                        <i class="fas fa-check-circle"></i>
                        <h1>Pembayaran Berhasil!</h1>
                        <p>Terima kasih telah berbelanja di Apotek Sehat</p>
                    </div>
                    <?php elseif ($transaksi['status'] == 'pending'): ?>
                    <div class="status-pending">
                        <i class="fas fa-clock"></i>
                        <h1>Menunggu Pembayaran</h1>
                        <p>Silakan selesaikan pembayaran Anda</p>
                    </div>
                    <?php else: ?>
                    <div class="status-cancelled">
                        <i class="fas fa-times-circle"></i>
                        <h1>Pembayaran Dibatalkan</h1>
                        <p>Transaksi Anda telah dibatalkan</p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="status-content">
                    <div class="order-details">
                        <h3>Detail Pesanan</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span>Kode Transaksi:</span>
                                <strong><?php echo $transaksi['kode_transaksi']; ?></strong>
                            </div>
                            <div class="detail-item">
                                <span>Nama Pembeli:</span>
                                <span><?php echo $transaksi['nama_pembeli']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span>No. Telepon:</span>
                                <span><?php echo $transaksi['no_telepon']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span>Alamat:</span>
                                <span><?php echo $transaksi['alamat']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span>Tanggal:</span>
                                <span><?php echo date('d/m/Y H:i', strtotime($transaksi['created_at'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span>Status:</span>
                                <span class="status-badge <?php echo $transaksi['status']; ?>">
                                    <?php echo strtoupper($transaksi['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="order-items">
                        <h3>Item yang Dipesan</h3>
                        <div class="items-list">
                            <?php foreach ($detail_transaksi as $item): ?>
                            <div class="order-item">
                                <div class="item-info">
                                    <h4><?php echo $item['nama_obat']; ?></h4>
                                    <p><?php echo formatRupiah($item['harga']); ?> x <?php echo $item['jumlah']; ?></p>
                                </div>
                                <div class="item-total">
                                    <?php echo formatRupiah($item['harga'] * $item['jumlah']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-total">
                            <div class="total-line">
                                <span>Total Pembayaran:</span>
                                <strong><?php echo formatRupiah($transaksi['total_harga']); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="status-actions">
                    <?php if ($transaksi['status'] == 'pending'): ?>
                    <a href="pembayaran.php?kode=<?php echo $transaksi['kode_transaksi']; ?>" class="btn btn-primary">
                        <i class="fas fa-qrcode"></i> Lanjutkan Pembayaran
                    </a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                    
                    <?php if ($transaksi['status'] == 'paid'): ?>
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Cetak Invoice
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <style>
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
    }
    
    .status-badge.paid {
        background: #dcfce7;
        color: #166534;
    }
    
    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-badge.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    </style>

    <?php include 'includes/footer.php'; ?>
</body>
</html>