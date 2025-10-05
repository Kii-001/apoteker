<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

// Handle status update
if (isset($_POST['update_status'])) {
    $transaksi_id = $_POST['transaksi_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE transaksi SET status = ? WHERE id = ?");
    $stmt->execute([$status, $transaksi_id]);
    
    $_SESSION['success'] = "Status transaksi berhasil diupdate";
    header("Location: transaksi.php");
    exit();
}

// Get all transactions
$stmt = $pdo->query("
    SELECT t.*, COUNT(dt.id) as item_count 
    FROM transaksi t 
    LEFT JOIN detail_transaksi dt ON t.id = dt.transaksi_id 
    GROUP BY t.id 
    ORDER BY t.created_at DESC
");
$transaksi = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Manajemen Transaksi</h1>
                <p>Kelola semua transaksi pembelian</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>

            <div class="content-card">
                <div class="card-header">
                    <h3>Daftar Transaksi</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pembeli</th>
                                    <th>Telepon</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksi as $trx): ?>
                                <tr>
                                    <td><?php echo $trx['kode_transaksi']; ?></td>
                                    <td><?php echo $trx['nama_pembeli']; ?></td>
                                    <td><?php echo $trx['no_telepon']; ?></td>
                                    <td><?php echo $trx['item_count']; ?> item</td>
                                    <td><?php echo formatRupiah($trx['total_harga']); ?></td>
                                    <td>
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="transaksi_id" value="<?php echo $trx['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" class="status-select <?php echo $trx['status']; ?>">
                                                <option value="pending" <?php echo $trx['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="paid" <?php echo $trx['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                <option value="cancelled" <?php echo $trx['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($trx['created_at'])); ?></td>
                                    <td>
                                        <a href="transaksi_detail.php?id=<?php echo $trx['id']; ?>" class="btn btn-sm btn-outline">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <style>
    .status-select {
        padding: 0.25rem 0.5rem;
        border: none;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-select.pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-select.paid {
        background: #dcfce7;
        color: #166534;
    }
    
    .status-select.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    </style>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>