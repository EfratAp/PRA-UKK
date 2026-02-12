<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php"); exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id = '$id'");
$b = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $stok     = $_POST['stok'];
    $harga    = $_POST['harga'];
    $kategori = $_POST['kategori_id'];
    $gambar_lama = $_POST['gambar_lama'];

    // Cek apakah ada upload gambar baru
    if ($_FILES['gambar']['name'] != "") {
        $gambar   = $_FILES['gambar']['name'];
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $nama_baru = time() . "_" . $gambar;
        move_uploaded_file($tmp_name, "../assets/img/barang/" . $nama_baru);
        
        // Hapus gambar lama agar storage tidak penuh
        if(file_exists("../assets/img/barang/" . $gambar_lama)) {
            unlink("../assets/img/barang/" . $gambar_lama);
        }
    } else {
        $nama_baru = $gambar_lama; // Gunakan gambar lama jika tidak ganti
    }

    $update = mysqli_query($conn, "UPDATE barang SET 
                nama_barang = '$nama', 
                stok = '$stok', 
                harga = '$harga', 
                gambar = '$nama_baru', 
                kategori_id = '$kategori' 
                WHERE id = '$id'");

    if ($update) {
        header("Location: barang.php?msg=updated");
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
            <label>Harga per Hari</label>
            <input type="number" name="harga" value="<?= $b['harga']; ?>" required>
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
            <small>Gambar saat ini: <?= $b['gambar']; ?></small>
        </div>
        
        <button type="submit" name="update" class="btn btn-primary" style="width: 100%;">Update Barang</button>
        <a href="barang.php" class="btn btn-outline" style="display: block; text-align: center; margin-top: 10px;">Batal</a>
    </form>
</div>
</body>
</html>