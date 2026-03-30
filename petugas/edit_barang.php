<?php
session_start();
include '../config/database.php';

// PROTEKSI: Hanya ADMIN yang boleh edit
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']); // Keamanan tambahan
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id = '$id'");
$b = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $stok        = $_POST['stok'];
    $harga_asli  = $_POST['harga_asli']; // <-- SINKRON: Mengambil dari name="harga_asli"
    $kategori    = $_POST['kategori_id'];
    $gambar_lama = $_POST['gambar_lama'];

    if ($_FILES['gambar']['name'] != "") {
        $gambar    = $_FILES['gambar']['name'];
        $tmp_name  = $_FILES['gambar']['tmp_name'];
        $nama_baru = time() . "_" . $gambar;
        move_uploaded_file($tmp_name, "../assets/img/barang/" . $nama_baru);
        
        if(file_exists("../assets/img/barang/" . $gambar_lama) && $gambar_lama != "") {
            unlink("../assets/img/barang/" . $gambar_lama);
        }
    } else {
        $nama_baru = $gambar_lama;
    }

    $update = mysqli_query($conn, "UPDATE barang SET 
                nama_barang = '$nama', 
                stok = '$stok', 
                harga_asli = '$harga_asli', 
                gambar = '$nama_baru', 
                kategori_id = '$kategori' 
                WHERE id = '$id'");

    if ($update) {
        header("Location: barang.php?msg=updated");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Barang - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box">
    <h2>Edit Barang</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="gambar_lama" value="<?= $b['gambar']; ?>">
        
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama" value="<?= $b['nama_barang']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" value="<?= $b['stok']; ?>" required>
        </div>

        <div class="form-group">
            <label>Harga Barang (Dasar Denda)</label>
            <input type="number" name="harga_asli" value="<?= $b['harga_asli']; ?>" required>
            <small style="color: red;">* Digunakan untuk menghitung denda kerusakan berat.</small>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" required>
                <option value="1" <?= $b['kategori_id'] == 1 ? 'selected' : ''; ?>>Elektronik</option>
                <option value="2" <?= $b['kategori_id'] == 2 ? 'selected' : ''; ?>>Non-Elektronik</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Ganti Gambar (Kosongkan jika tidak ganti)</label>
            <input type="file" name="gambar" accept="image/*">
            <p><small>Gambar saat ini: <b><?= $b['gambar']; ?></b></small></p>
        </div>
        
        <button type="submit" name="update" class="btn btn-primary" style="width: 100%;">Update Barang</button>
        <a href="barang.php" class="btn btn-outline" style="display: block; text-align: center; margin-top: 10px;">Batal</a>
    </form>
</div>
</body>
</html>