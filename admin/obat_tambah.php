<?php
session_start();
include '../config/database.php';
include '../config/functions.php';
requireAdminLogin();

$errors = [];
$success = '';

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
    $gambar = 'default.jpg';
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
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $errors['gambar'] = "Gagal mengupload gambar";
                $gambar = 'default.jpg';
            }
        }
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO obat (nama_obat, deskripsi, kategori, harga, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama_obat, $deskripsi, $kategori, $harga, $stok, $gambar]);
            
            $_SESSION['success'] = "Obat berhasil ditambahkan";
            header("Location: obat.php");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = "Gagal menambahkan obat: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Obat - Apotek Sehat</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <main>
            <div class="page-header">
                <h1>Tambah Obat Baru</h1>
                <p>Tambahkan data obat baru ke dalam sistem</p>
            </div>

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
                               value="<?php echo htmlspecialchars($_POST['nama_obat'] ?? ''); ?>"
                               class="<?php echo isset($errors['nama_obat']) ? 'error' : ''; ?>">
                        <?php if (isset($errors['nama_obat'])): ?>
                            <span class="error-text"><?php echo $errors['nama_obat']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi *</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="<?php echo isset($errors['deskripsi']) ? 'error' : ''; ?>"><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                        <?php if (isset($errors['deskripsi'])): ?>
                            <span class="error-text"><?php echo $errors['deskripsi']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kategori">Kategori *</label>
                            <select id="kategori" name="kategori" class="<?php echo isset($errors['kategori']) ? 'error' : ''; ?>">
                                <option value="">Pilih Kategori</option>
                                <option value="Analgesik" <?php echo ($_POST['kategori'] ?? '') == 'Analgesik' ? 'selected' : ''; ?>>Analgesik</option>
                                <option value="Batuk & Flu" <?php echo ($_POST['kategori'] ?? '') == 'Batuk & Flu' ? 'selected' : ''; ?>>Batuk & Flu</option>
                                <option value="Antihistamin" <?php echo ($_POST['kategori'] ?? '') == 'Antihistamin' ? 'selected' : ''; ?>>Antihistamin</option>
                                <option value="Antasida" <?php echo ($_POST['kategori'] ?? '') == 'Antasida' ? 'selected' : ''; ?>>Antasida</option>
                                <option value="Antidiare" <?php echo ($_POST['kategori'] ?? '') == 'Antidiare' ? 'selected' : ''; ?>>Antidiare</option>
                                <option value="Elektrolit" <?php echo ($_POST['kategori'] ?? '') == 'Elektrolit' ? 'selected' : ''; ?>>Elektrolit</option>
                                <option value="Obat Mata" <?php echo ($_POST['kategori'] ?? '') == 'Obat Mata' ? 'selected' : ''; ?>>Obat Mata</option>
                                <option value="Vitamin" <?php echo ($_POST['kategori'] ?? '') == 'Vitamin' ? 'selected' : ''; ?>>Vitamin</option>
                                <option value="Antiseptik" <?php echo ($_POST['kategori'] ?? '') == 'Antiseptik' ? 'selected' : ''; ?>>Antiseptik</option>
                                <option value="Salep" <?php echo ($_POST['kategori'] ?? '') == 'Salep' ? 'selected' : ''; ?>>Salep</option>
                                <option value="Analgesik Topikal" <?php echo ($_POST['kategori'] ?? '') == 'Analgesik Topikal' ? 'selected' : ''; ?>>Analgesik Topikal</option>
                                <option value="Obat Luar" <?php echo ($_POST['kategori'] ?? '') == 'Obat Luar' ? 'selected' : ''; ?>>Obat Luar</option>
                                <option value="Herbal" <?php echo ($_POST['kategori'] ?? '') == 'Herbal' ? 'selected' : ''; ?>>Herbal</option>
                                <option value="Obat Anak" <?php echo ($_POST['kategori'] ?? '') == 'Obat Anak' ? 'selected' : ''; ?>>Obat Anak</option>
                                <option value="Vitamin Anak" <?php echo ($_POST['kategori'] ?? '') == 'Vitamin Anak' ? 'selected' : ''; ?>>Vitamin Anak</option>
                                <option value="Hipertensi" <?php echo ($_POST['kategori'] ?? '') == 'Hipertensi' ? 'selected' : ''; ?>>Hipertensi</option>
                                <option value="Diabetes" <?php echo ($_POST['kategori'] ?? '') == 'Diabetes' ? 'selected' : ''; ?>>Diabetes</option>
                                <option value="Kolesterol" <?php echo ($_POST['kategori'] ?? '') == 'Kolesterol' ? 'selected' : ''; ?>>Kolesterol</option>
                            </select>
                            <?php if (isset($errors['kategori'])): ?>
                                <span class="error-text"><?php echo $errors['kategori']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="harga">Harga (Rp) *</label>
                            <input type="number" id="harga" name="harga" min="0" step="100"
                                   value="<?php echo htmlspecialchars($_POST['harga'] ?? ''); ?>"
                                   class="<?php echo isset($errors['harga']) ? 'error' : ''; ?>">
                            <?php if (isset($errors['harga'])): ?>
                                <span class="error-text"><?php echo $errors['harga']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="stok">Stok *</label>
                            <input type="number" id="stok" name="stok" min="0"
                                   value="<?php echo htmlspecialchars($_POST['stok'] ?? ''); ?>"
                                   class="<?php echo isset($errors['stok']) ? 'error' : ''; ?>">
                            <?php if (isset($errors['stok'])): ?>
                                <span class="error-text"><?php echo $errors['stok']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Gambar Obat</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*">
                        <small>Format: JPG, PNG, GIF (Maks. 2MB)</small>
                        <?php if (isset($errors['gambar'])): ?>
                            <span class="error-text"><?php echo $errors['gambar']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <a href="obat.php" class="btn btn-outline">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Obat</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>