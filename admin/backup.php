<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

if (isset($_POST['backup'])) {
    try {
        $backup_file = 'backup/apotek_backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        // Create backup directory if not exists
        if (!is_dir('backup')) {
            mkdir('backup', 0755, true);
        }
        
        // Get all tables
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        $output = "";
        foreach ($tables as $table) {
            // Drop table if exists
            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            
            // Create table structure
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $output .= $row[1] . ";\n\n";
            
            // Table data
            $stmt = $pdo->query("SELECT * FROM `$table`");
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $output .= "INSERT INTO `$table` VALUES(";
                foreach ($row as $value) {
                    $value = addslashes($value);
                    $value = str_replace("\n", "\\n", $value);
                    $output .= "'$value',";
                }
                $output = rtrim($output, ',');
                $output .= ");\n";
            }
            $output .= "\n";
        }
        
        // Save backup file
        file_put_contents($backup_file, $output);
        
        $_SESSION['success'] = "Backup database berhasil dibuat: " . $backup_file;
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal membuat backup: " . $e->getMessage();
    }
    
    header("Location: backup.php");
    exit();
}

// Get existing backup files
$backup_files = [];
if (is_dir('backup')) {
    $files = scandir('backup');
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backup_files[] = [
                'name' => $file,
                'path' => 'backup/' . $file,
                'size' => filesize('backup/' . $file),
                'time' => filemtime('backup/' . $file)
            ];
        }
    }
    
    // Sort by modification time (newest first)
    usort($backup_files, function($a, $b) {
        return $b['time'] - $a['time'];
    });
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Backup Database</h1>
                <p>Kelola backup dan restore data sistem</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>

            <div class="content-grid">
                <div class="content-card">
                    <div class="card-header">
                        <h3>Buat Backup Baru</h3>
                    </div>
                    <div class="card-body">
                        <p>Backup database akan menyimpan semua data transaksi, obat, dan pengguna ke dalam file SQL.</p>
                        <form method="POST">
                            <button type="submit" name="backup" class="btn btn-primary">
                                <i class="fas fa-database"></i> Buat Backup Sekarang
                            </button>
                        </form>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3>File Backup Tersedia</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($backup_files)): ?>
                        <div class="no-data">
                            <i class="fas fa-database"></i>
                            <p>Belum ada file backup</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama File</th>
                                        <th>Ukuran</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($backup_files as $file): ?>
                                    <tr>
                                        <td><?php echo $file['name']; ?></td>
                                        <td><?php echo formatBytes($file['size']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', $file['time']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?php echo $file['path']; ?>" download class="btn btn-sm btn-outline">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <a href="backup.php?delete=<?php echo urlencode($file['name']); ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Hapus file backup ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php
    function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    ?>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>