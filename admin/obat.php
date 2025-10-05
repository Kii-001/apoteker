<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($action == 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM obat WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Obat berhasil dihapus";
    header("Location: obat.php");
    exit();
}

// Get all medicines
$stmt = $pdo->query("SELECT * FROM obat ORDER BY created_at DESC");
$obat = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Obat - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Kelola Obat</h1>
                <p>Manajemen data obat dan stok</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>

            <div class="content-card">
                <div class="card-header">
                    <h3>Daftar Obat</h3>
                    <a href="../admin/obat_tambah.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Obat
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($obat as $item): ?>
                                <tr>
                                    <td>
                                        <img src="../assets/uploads/<?php echo $item['gambar'] ?: 'default.jpg'; ?>" alt="<?php echo $item['nama_obat']; ?>" class="table-image">
                                    </td>
                                    <td><?php echo $item['nama_obat']; ?></td>
                                    <td><?php echo $item['kategori']; ?></td>
                                    <td><?php echo formatRupiah($item['harga']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $item['stok'] == 0 ? 'status-danger' : ($item['stok'] < 10 ? 'status-warning' : 'status-success'); ?>">
                                            <?php echo $item['stok']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="obat_edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="obat.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
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
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>