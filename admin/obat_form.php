<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';
$obat = null;

// Jika edit, ambil data obat
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM obat WHERE id = ?");
    $stmt->execute([$id]);
    $obat = $stmt->fetch();
    
    if (!$obat) {
        $_SESSION['error'] = "Obat tidak ditemukan";
        header("Location: obat.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_obat = $_POST['nama_obat'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Handle file upload
    $gambar = $obat['gambar'] ?? 'default.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $gambar = uniqid() . '.' . $filetype;
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/uploads/' . $gambar);
            
            // Delete old image if exists
            if ($obat && $obat['gambar'] && $obat['gambar'] != 'default.jpg') {
                @unlink('../assets/uploads/' . $obat['gambar']);
            }
        }
    }
    
    try {
        if ($action == 'edit' && $id) {
            $stmt = $pdo->prepare("UPDATE obat SET nama_obat = ?, deskripsi = ?, kategori = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?");
            $stmt->execute([$nama_obat, $deskripsi, $kategori, $harga, $stok, $gambar, $id]);
            $_SESSION['success'] = "Obat berhasil diupdate";
        } else {
            $stmt = $pdo->prepare("INSERT INTO obat (nama_obat, deskripsi, kategori, harga, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama_obat, $deskripsi, $kategori, $harga, $stok, $gambar]);
            $_SESSION['success'] = "Obat berhasil ditambahkan";
        }
        
        header("Location: obat.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal menyimpan obat: " . $e->getMessage();
        header("Location: obat.php?action=" . $action . ($id ? "&id=" . $id : ""));
        exit();
    }
}

$page_title = $action == 'edit' ? 'Edit Obat' : 'Tambah Obat';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1><?php echo $page_title; ?></h1>
                <p><?php echo $action == 'edit' ? 'Edit data obat' : 'Tambah obat baru'; ?></p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>

            <div class="content-card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama_obat">Nama Obat *</label>
                                <input type="text" id="nama_obat" name="nama_obat" 
                                       value="<?php echo $obat['nama_obat'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="kategori">Kategori</label>
                                <input type="text" id="kategori" name="kategori" 
                                       value="<?php echo $obat['kategori'] ?? ''; ?>" 
                                       placeholder="Contoh: Antibiotik, Vitamin, dll.">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo $obat['deskripsi'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="harga">Harga (Rp) *</label>
                                <input type="number" id="harga" name="harga" 
                                       value="<?php echo $obat['harga'] ?? ''; ?>" 
                                       min="0" step="100" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="stok">Stok *</label>
                                <input type="number" id="stok" name="stok" 
                                       value="<?php echo $obat['stok'] ?? '0'; ?>" 
                                       min="0" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="gambar">Gambar Obat</label>
                            <input type="file" id="gambar" name="gambar" accept="image/*" 
                                   onchange="previewImage(this, 'imagePreview')">
                            
                            <?php if ($obat && $obat['gambar']): ?>
                            <div class="image-preview" id="imagePreview">
                                <img src="../assets/uploads/<?php echo $obat['gambar']; ?>" 
                                     alt="Preview" style="max-width: 200px; margin-top: 1rem;">
                            </div>
                            <?php else: ?>
                            <div class="image-preview" id="imagePreview" style="display: none;">
                                <img src="" alt="Preview" style="max-width: 200px; margin-top: 1rem;">
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="obat.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width: 200px; margin-top: 1rem;">';
            preview.style.display = 'block';
        }
        
        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            preview.innerHTML = '';
        }
    }
    </script>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>