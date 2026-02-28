<?php
session_start();
include '../config/database.php';

// PROTEKSI: Hanya ADMIN yang boleh edit
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

// 1. Ambil Data Barang Berdasarkan ID
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id = '$id'");
$b = mysqli_fetch_assoc($query);

// Jika ID tidak ditemukan
if (!$b) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='barang.php';</script>";
    exit;
}

// 2. Ambil Daftar Kategori untuk Dropdown
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");

if (isset($_POST['update'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $stok        = (int)$_POST['stok'];
    $harga_asli  = (int)$_POST['harga_asli']; // Mengambil harga asli untuk denda
    $kategori    = $_POST['kategori_id'];
    $gambar_lama = $_POST['gambar_lama'];

    // Logika Ganti Gambar
    if ($_FILES['gambar']['name'] != "") {
        $gambar    = $_FILES['gambar']['name'];
        $tmp_name  = $_FILES['gambar']['tmp_name'];
        $ekstensi  = pathinfo($gambar, PATHINFO_EXTENSION);
        $nama_baru = time() . "_" . $id . "." . $ekstensi; 
        
        // Hapus gambar lama secara fisik jika ada
        if (!empty($gambar_lama) && file_exists("../assets/img/barang/" . $gambar_lama)) {
            unlink("../assets/img/barang/" . $gambar_lama);
        }
        
        move_uploaded_file($tmp_name, "../assets/img/barang/" . $nama_baru);
        $sql = "UPDATE barang SET 
                nama_barang = '$nama', 
                stok = '$stok', 
                harga_asli = '$harga_asli', 
                kategori_id = '$kategori', 
                gambar = '$nama_baru' 
                WHERE id = '$id'";
    } else {
        $sql = "UPDATE barang SET 
                nama_barang = '$nama', 
                stok = '$stok', 
                harga_asli = '$harga_asli', 
                kategori_id = '$kategori' 
                WHERE id = '$id'";
    }

    if (mysqli_query($conn, $sql)) {
        // Catat ke Log Aktivitas
        $pesan_log = "Admin {$_SESSION['nama']} memperbarui data barang: $nama (ID: $id)";
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");
        
        echo "<script>alert('Data $nama berhasil diupdate!'); window.location='barang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang - Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box">
    <h2>Edit Data Barang</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="gambar_lama" value="<?= $b['gambar']; ?>">
        
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" value="<?= htmlspecialchars($b['nama_barang']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" value="<?= $b['stok']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Harga Asli Barang (Rp)</label>
            <input type="number" name="harga_asli" value="<?= $b['harga_asli']; ?>" required>
            <small style="color: #64748b;">* Digunakan sebagai dasar perhitungan denda kerusakan.</small>
        </div>
        
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" required>
                <?php while($kat = mysqli_fetch_assoc($kategori_query)): ?>
                    <option value="<?= $kat['id']; ?>" <?= ($b['kategori_id'] == $kat['id']) ? 'selected' : ''; ?>>
                        <?= $kat['nama_kategori']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Foto Barang</label>
            <div style="margin-bottom: 10px;">
                <img src="../assets/img/barang/<?= $b['gambar']; ?>" width="80" style="border-radius: 5px; border: 1px solid #ddd;">
            </div>
            <input type="file" name="gambar" accept="image/*">
            <p><small style="color: #64748b;">*Kosongkan jika tidak ingin mengganti foto.</small></p>
        </div>
        
        <button type="submit" name="update" class="btn btn-primary" style="width:100%;">Update Barang</button>
        <a href="barang.php" class="btn btn-outline" style="width:100%; text-align:center; margin-top:10px; display:block;">Batal</a>
    </form>
</div>
</body>
</html>