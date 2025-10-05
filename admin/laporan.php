<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

// Filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$kategori = $_GET['kategori'] ?? '';

// Build query for sales report
$query = "
    SELECT 
        o.kategori,
        COUNT(dt.id) as total_terjual,
        SUM(dt.jumlah) as total_qty,
        SUM(dt.harga * dt.jumlah) as total_pendapatan
    FROM detail_transaksi dt
    JOIN obat o ON dt.obat_id = o.id
    JOIN transaksi t ON dt.transaksi_id = t.id
    WHERE t.status = 'paid'
    AND DATE(t.created_at) BETWEEN ? AND ?
";

$params = [$start_date, $end_date];

if (!empty($kategori)) {
    $query .= " AND o.kategori = ?";
    $params[] = $kategori;
}

$query .= " GROUP BY o.kategori ORDER BY total_pendapatan DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$laporan = $stmt->fetchAll();

// Get total summary
$summary_query = "
    SELECT 
        COUNT(*) as total_transaksi,
        SUM(total_harga) as total_pendapatan
    FROM transaksi 
    WHERE status = 'paid'
    AND DATE(created_at) BETWEEN ? AND ?
";

$summary_stmt = $pdo->prepare($summary_query);
$summary_stmt->execute([$start_date, $end_date]);
$summary = $summary_stmt->fetch();

// Get categories for filter
$kategories = $pdo->query("SELECT DISTINCT kategori FROM obat WHERE kategori IS NOT NULL")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Laporan Penjualan</h1>
                <p>Analisis data penjualan dan performa produk</p>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h3>Filter Laporan</h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="filter-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" id="start_date" name="start_date" 
                                       value="<?php echo $start_date; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="end_date">Tanggal Akhir</label>
                                <input type="date" id="end_date" name="end_date" 
                                       value="<?php echo $end_date; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="kategori">Kategori</label>
                                <select id="kategori" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($kategories as $kat): ?>
                                    <option value="<?php echo $kat['kategori']; ?>" 
                                            <?php echo $kategori == $kat['kategori'] ? 'selected' : ''; ?>>
                                        <?php echo $kat['kategori']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="laporan.php" class="btn btn-outline">Reset</a>
                            <button type="button" onclick="exportToExcel()" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Pendapatan</h3>
                        <span class="stat-number">
                            <?php echo formatRupiah($summary['total_pendapatan'] ?? 0); ?>
                        </span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Transaksi</h3>
                        <span class="stat-number">
                            <?php echo $summary['total_transaksi'] ?? 0; ?>
                        </span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Kategori Terjual</h3>
                        <span class="stat-number">
                            <?php echo count($laporan); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h3>Detail Laporan per Kategori</h3>
                    <p>Periode: <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?></p>
                </div>
                <div class="card-body">
                    <?php if (count($laporan) > 0): ?>
                    <div class="table-responsive">
                        <table class="data-table" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Total Terjual</th>
                                    <th>Total Qty</th>
                                    <th>Total Pendapatan</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_pendapatan = array_sum(array_column($laporan, 'total_pendapatan'));
                                foreach ($laporan as $item): 
                                    $persentase = $total_pendapatan > 0 ? ($item['total_pendapatan'] / $total_pendapatan) * 100 : 0;
                                ?>
                                <tr>
                                    <td><?php echo $item['kategori'] ?: 'Tidak Berkategori'; ?></td>
                                    <td><?php echo $item['total_terjual']; ?></td>
                                    <td><?php echo $item['total_qty']; ?></td>
                                    <td><?php echo formatRupiah($item['total_pendapatan']); ?></td>
                                    <td>
                                        <div class="progress-container">
                                            <div class="progress-bar" style="width: <?php echo $persentase; ?>%"></div>
                                            <span><?php echo number_format($persentase, 1); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo array_sum(array_column($laporan, 'total_terjual')); ?></strong></td>
                                    <td><strong><?php echo array_sum(array_column($laporan, 'total_qty')); ?></strong></td>
                                    <td><strong><?php echo formatRupiah($total_pendapatan); ?></strong></td>
                                    <td><strong>100%</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-chart-bar"></i>
                        <h3>Tidak ada data</h3>
                        <p>Tidak ada transaksi pada periode yang dipilih</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <style>
    .progress-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .progress-bar {
        height: 8px;
        background: var(--primary);
        border-radius: 4px;
        min-width: 50px;
    }
    
    .no-data {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--secondary);
    }
    
    .no-data i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--border);
    }
    </style>
    
    <script>
    function exportToExcel() {
        // Simple CSV export
        const table = document.getElementById('reportTable');
        let csv = [];
        
        // Headers
        const headers = [];
        for (let i = 0; i < table.rows[0].cells.length; i++) {
            headers.push(table.rows[0].cells[i].textContent);
        }
        csv.push(headers.join(','));
        
        // Data
        for (let i = 1; i < table.rows.length; i++) {
            const row = [];
            const cells = table.rows[i].cells;
            
            for (let j = 0; j < cells.length; j++) {
                // Remove progress bar from export
                if (j === 4) {
                    const percent = cells[j].querySelector('span')?.textContent || '0%';
                    row.push(percent);
                } else {
                    row.push(cells[j].textContent.replace(/[$,]/g, ''));
                }
            }
            
            csv.push(row.join(','));
        }
        
        // Download
        const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "laporan_penjualan_<?php echo date('Y-m-d'); ?>.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    </script>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>