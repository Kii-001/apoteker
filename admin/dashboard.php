<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Dashboard</h1>
                <p>Selamat datang, <?php echo $_SESSION['admin_nama']; ?></p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Obat</h3>
                        <span class="stat-number">
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM obat");
                            echo $stmt->fetch()['total'];
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Transaksi</h3>
                        <span class="stat-number">
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM transaksi");
                            echo $stmt->fetch()['total'];
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pendapatan Bulan Ini</h3>
                        <span class="stat-number">
                            <?php
                            $stmt = $pdo->query("SELECT SUM(total_harga) as total FROM transaksi WHERE status = 'paid' AND MONTH(created_at) = MONTH(CURRENT_DATE())");
                            $total = $stmt->fetch()['total'] ?: 0;
                            echo formatRupiah($total);
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Stok Menipis</h3>
                        <span class="stat-number">
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM obat WHERE stok < 10");
                            echo $stmt->fetch()['total'];
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="content-grid">
                <div class="content-card">
                    <div class="card-header">
                        <h3>Transaksi Terbaru</h3>
                        <a href="transaksi.php" class="btn-link">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Pembeli</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM transaksi ORDER BY created_at DESC LIMIT 5");
                                while ($transaksi = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $statusClass = $transaksi['status'] == 'paid' ? 'status-success' : 
                                                 ($transaksi['status'] == 'pending' ? 'status-warning' : 'status-danger');
                                ?>
                                <tr>
                                    <td><?php echo $transaksi['kode_transaksi']; ?></td>
                                    <td><?php echo $transaksi['nama_pembeli']; ?></td>
                                    <td><?php echo formatRupiah($transaksi['total_harga']); ?></td>
                                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $transaksi['status']; ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($transaksi['created_at'])); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="content-card">
                    <div class="card-header">
                        <h3>Stok Menipis</h3>
                        <a href="obat.php" class="btn-link">Kelola Stok</a>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM obat WHERE stok < 10 ORDER BY stok ASC LIMIT 5");
                                while ($obat = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $stockClass = $obat['stok'] == 0 ? 'status-danger' : 'status-warning';
                                ?>
                                <tr>
                                    <td><?php echo $obat['nama_obat']; ?></td>
                                    <td><span class="status-badge <?php echo $stockClass; ?>"><?php echo $obat['stok']; ?></span></td>
                                    <td><?php echo formatRupiah($obat['harga']); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>