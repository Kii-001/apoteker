<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-clinic-medical"></i> Apotek Sehat</h2>
        <p>Admin Panel</p>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="obat.php" class="<?php echo $current_page == 'obat.php' ? 'active' : ''; ?>">
                    <i class="fas fa-pills"></i>
                    <span>Kelola Obat</span>
                </a>
            </li>
            <li>
                <a href="transaksi.php" class="<?php echo $current_page == 'transaksi.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Transaksi</span>
                </a>
            </li>
            <li>
                <a href="laporan.php" class="<?php echo $current_page == 'laporan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</div>