<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: transaksi.php");
    exit();
}

// Get transaction details
$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id = ?");
$stmt->execute([$id]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    $_SESSION['error'] = "Transaksi tidak ditemukan";
    header("Location: transaksi.php");
    exit();
}

// Get transaction items
$stmt = $pdo->prepare("
    SELECT dt.*, o.nama_obat, o.gambar 
    FROM detail_transaksi dt 
    JOIN obat o ON dt.obat_id = o.id 
    WHERE dt.transaksi_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Detail Transaksi</h1>
                <p>Kode: <?php echo $transaksi['kode_transaksi']; ?></p>
            </div>

            <div class="content-grid">
                <div class="content-card">
                    <div class="card-header">
                        <h3>Informasi Pembeli</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Nama Lengkap:</strong>
                                <span><?php echo $transaksi['nama_pembeli']; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <span><?php echo $transaksi['email_pembeli'] ?: '-'; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>No. Telepon:</strong>
                                <span><?php echo $transaksi['no_telepon']; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Alamat:</strong>
                                <span><?php echo $transaksi['alamat']; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Metode Pembayaran:</strong>
                                <span><?php echo strtoupper($transaksi['metode_pembayaran']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="content-card">
                    <div class="card-header">
                        <h3>Status Transaksi</h3>
                    </div>
                    <div class="card-body">
                        <div class="status-info">
                            <div class="status-item">
                                <strong>Status:</strong>
                                <span class="status-badge <?php echo $transaksi['status']; ?>">
                                    <?php echo strtoupper($transaksi['status']); ?>
                                </span>
                            </div>
                            <div class="status-item">
                                <strong>Total:</strong>
                                <span class="amount"><?php echo formatRupiah($transaksi['total_harga']); ?></span>
                            </div>
                            <div class="status-item">
                                <strong>Tanggal:</strong>
                                <span><?php echo date('d/m/Y H:i', strtotime($transaksi['created_at'])); ?></span>
                            </div>
                            
                            <form method="POST" action="transaksi.php" class="status-form">
                                <input type="hidden" name="transaksi_id" value="<?php echo $transaksi['id']; ?>">
                                <div class="form-group">
                                    <label for="status">Update Status:</label>
                                    <select name="status" id="status" class="status-select <?php echo $transaksi['status']; ?>">
                                        <option value="pending" <?php echo $transaksi['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="paid" <?php echo $transaksi['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="cancelled" <?php echo $transaksi['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sync"></i> Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h3>Item yang Dibeli</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Obat</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <img src="../assets/uploads/<?php echo $item['gambar'] ?: 'default.jpg'; ?>" 
                                             alt="<?php echo $item['nama_obat']; ?>" class="table-image">
                                    </td>
                                    <td><?php echo $item['nama_obat']; ?></td>
                                    <td><?php echo formatRupiah($item['harga']); ?></td>
                                    <td><?php echo $item['jumlah']; ?></td>
                                    <td><?php echo formatRupiah($item['harga'] * $item['jumlah']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                                    <td style="font-weight: bold;"><?php echo formatRupiah($transaksi['total_harga']); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="content-actions">
                <a href="transaksi.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Cetak Invoice
                </button>
            </div>
        </main>
    </div>
    
    <style>
    .info-grid {
        display: grid;
        gap: 1rem;
    }
    
    .info-item {
        display: grid;
        grid-template-columns: 200px 1fr;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border);
    }
    
    .status-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border);
    }
    
    .amount {
        font-weight: bold;
        color: var(--primary);
        font-size: 1.125rem;
    }
    
    .content-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border);
    }
    
    @media print {
        .sidebar, .admin-header, .content-actions {
            display: none !important;
        }
        
        .main-content {
            margin-left: 0 !important;
        }
    }
    </style>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>