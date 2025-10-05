<header class="admin-header">
    <div class="header-left">
        <button class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1><?php echo $page_title ?? 'Admin Panel'; ?></h1>
    </div>
    
    <div class="header-actions">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['admin_nama'], 0, 1)); ?>
            </div>
            <span><?php echo $_SESSION['admin_nama']; ?></span>
        </div>
    </div>
</header>