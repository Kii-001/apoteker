<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

$errors = [];
$success = '';

// Get medicine data
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "ID obat tidak valid";
    header("Location: obat.php");
    exit();
}

// Get current medicine data
$stmt = $pdo->prepare("SELECT * FROM obat WHERE id = ?");
$stmt->execute([$id]);
$obat = $stmt->fetch();

if (!$obat) {
    $_SESSION['error'] = "Obat tidak ditemukan";
    header("Location: obat.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $nama_obat = trim($_POST['nama_obat'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $stok = trim($_POST['stok'] ?? '');
    
    // Validation
    if (empty($nama_obat)) {
        $errors['nama_obat'] = "Nama obat wajib diisi";
    }
    
    if (empty($kategori)) {
        $errors['kategori'] = "Kategori wajib diisi";
    }
    
    if (empty($harga) || !is_numeric($harga) || $harga < 0) {
        $errors['harga'] = "Harga harus angka positif";
    }
    
    if (empty($stok) || !is_numeric($stok) || $stok < 0) {
        $errors['stok'] = "Stok harus angka positif";
    }
    
    // Handle file upload
    $gambar = $obat['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES['gambar']['size'];
        
        // Check file type
        if (!in_array($filetype, $allowed)) {
            $errors['gambar'] = "Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF";
        }
        
        // Check file size (max 2MB)
        if ($filesize > 2097152) {
            $errors['gambar'] = "Ukuran file maksimal 2MB";
        }
        
        if (empty($errors)) {
            $gambar = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/' . $gambar;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                // Delete old image if not default
                if ($obat['gambar'] && $obat['gambar'] !== 'default.jpg') {
                    $old_file_path = '../assets/uploads/' . $obat['gambar'];
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            } else {
                $errors['gambar'] = "Gagal mengupload gambar";
                $gambar = $obat['gambar'];
            }
        }
    }
    
    // Jika tidak ada error, update ke database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE obat SET nama_obat = ?, deskripsi = ?, kategori = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?");
            $stmt->execute([$nama_obat, $deskripsi, $kategori, $harga, $stok, $gambar, $id]);
            
            $_SESSION['success'] = "Obat berhasil diperbarui";
            header("Location: obat.php");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = "Gagal memperbarui obat: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Obat - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .current-image {
            margin-bottom: 15px;
        }
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .error {
            border-color: #dc3545 !important;
        }
        .error-text {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Edit Obat</h1>
                <p>Perbarui data obat</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($errors['database'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($errors['database']); ?>
            </div>
            <?php endif; ?>

            <div class="content-card">
                <form method="POST" enctype="multipart/form-data" class="form">
                    <div class="form-group">
                        <label for="nama_obat">Nama Obat *</label>
                        <input type="text" id="nama_obat" name="nama_obat" 
                               value="<?php echo htmlspecialchars($_POST['nama_obat'] ?? $obat['nama_obat']); ?>"
                               class="<?php echo isset($errors['nama_obat']) ? 'error' : ''; ?>">
                        <?php if (isset($errors['nama_obat'])): ?>
                            <span class="error-text"><?php echo $errors['nama_obat']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Masukkan deskripsi obat..."><?php echo htmlspecialchars($_POST['deskripsi'] ?? $obat['deskripsi']); ?></textarea>
                    </div>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="kategori">Kategori *</label>
                            <select id="kategori" name="kategori" class="<?php echo isset($errors['kategori']) ? 'error' : ''; ?>">
                                <option value="">Pilih Kategori</option>
                                <option value="Analgesik" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Analgesik' ? 'selected' : ''; ?>>Analgesik</option>
                                <option value="Batuk & Flu" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Batuk & Flu' ? 'selected' : ''; ?>>Batuk & Flu</option>
                                <option value="Antihistamin" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Antihistamin' ? 'selected' : ''; ?>>Antihistamin</option>
                                <option value="Antasida" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Antasida' ? 'selected' : ''; ?>>Antasida</option>
                                <option value="Antidiare" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Antidiare' ? 'selected' : ''; ?>>Antidiare</option>
                                <option value="Elektrolit" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Elektrolit' ? 'selected' : ''; ?>>Elektrolit</option>
                                <option value="Obat Mata" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Obat Mata' ? 'selected' : ''; ?>>Obat Mata</option>
                                <option value="Vitamin" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Vitamin' ? 'selected' : ''; ?>>Vitamin</option>
                                <option value="Antiseptik" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Antiseptik' ? 'selected' : ''; ?>>Antiseptik</option>
                                <option value="Salep" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Salep' ? 'selected' : ''; ?>>Salep</option>
                                <option value="Analgesik Topikal" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Analgesik Topikal' ? 'selected' : ''; ?>>Analgesik Topikal</option>
                                <option value="Obat Luar" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Obat Luar' ? 'selected' : ''; ?>>Obat Luar</option>
                                <option value="Herbal" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Herbal' ? 'selected' : ''; ?>>Herbal</option>
                                <option value="Obat Anak" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Obat Anak' ? 'selected' : ''; ?>>Obat Anak</option>
                                <option value="Vitamin Anak" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Vitamin Anak' ? 'selected' : ''; ?>>Vitamin Anak</option>
                                <option value="Hipertensi" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Hipertensi' ? 'selected' : ''; ?>>Hipertensi</option>
                                <option value="Diabetes" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Diabetes' ? 'selected' : ''; ?>>Diabetes</option>
                                <option value="Kolesterol" <?php echo ($_POST['kategori'] ?? $obat['kategori']) == 'Kolesterol' ? 'selected' : ''; ?>>Kolesterol</option>
                            </select>
                            <?php if (isset($errors['kategori'])): ?>
                                <span class="error-text"><?php echo $errors['kategori']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="harga">Harga (Rp) *</label>
                            <input type="number" id="harga" name="harga" min="0" step="100"
                                   value="<?php echo htmlspecialchars($_POST['harga'] ?? $obat['harga']); ?>"
                                   class="<?php echo isset($errors['harga']) ? 'error' : ''; ?>">
                            <?php if (isset($errors['harga'])): ?>
                                <span class="error-text"><?php echo $errors['harga']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="stok">Stok *</label>
                            <input type="number" id="stok" name="stok" min="0"
                                   value="<?php echo htmlspecialchars($_POST['stok'] ?? $obat['stok']); ?>"
                                   class="<?php echo isset($errors['stok']) ? 'error' : ''; ?>">
                            <?php if (isset($errors['stok'])): ?>
                                <span class="error-text"><?php echo $errors['stok']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Gambar Obat</label>
                        <?php if ($obat['gambar'] && $obat['gambar'] !== 'default.jpg'): ?>
                        <div class="current-image">
                            <img src="../assets/uploads/<?php echo htmlspecialchars($obat['gambar']); ?>" 
                                 alt="Current image" class="preview-image">
                            <small>Gambar saat ini</small>
                        </div>
                        <?php endif; ?>
                        <input type="file" id="gambar" name="gambar" accept="image/*">
                        <small>Format: JPG, PNG, GIF (Maks. 2MB) - Kosongkan jika tidak ingin mengubah</small>
                        <?php if (isset($errors['gambar'])): ?>
                            <span class="error-text"><?php echo $errors['gambar']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <a href="obat.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Perbarui Obat
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/admin.js"></script>
    <script>
        // Preview image before upload
        document.getElementById('gambar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove current image preview if exists
                    const currentPreview = document.querySelector('.current-image');
                    if (currentPreview) {
                        currentPreview.remove();
                    }
                    
                    // Create new preview
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'current-image';
                    previewContainer.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="preview-image">
                        <small>Preview gambar baru</small>
                    `;
                    document.querySelector('label[for="gambar"]').after(previewContainer);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>