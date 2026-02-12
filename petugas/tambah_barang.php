<?php
session_start();
include '../config/database.php';

if (isset($_POST['submit'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $stok     = $_POST['stok'];
    $harga    = $_POST['harga'];
    $kategori = $_POST['kategori_id'];
    
    // Proses Gambar
    $gambar   = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    
    // Generate nama file unik agar tidak bentrok
    $ekstensi = pathinfo($gambar, PATHINFO_EXTENSION);
    $nama_baru = time() . "_" . $gambar; 

    if (move_uploaded_file($tmp_name, "../assets/img/barang/" . $nama_baru)) {
        $sql = "INSERT INTO barang (nama_barang, stok, harga, gambar, kategori_id) 
                VALUES ('$nama', '$stok', '$harga', '$nama_baru', '$kategori')";
        
        if (mysqli_query($conn, $sql)) {
            // Catat ke Log Aktivitas
            $pesan_log = "Petugas " . $_SESSION['nama'] . " menambah barang baru: $nama";
            mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");

            echo "<script>alert('Barang berhasil ditambahkan!'); window.location='barang.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal upload gambar!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box">
    <h2>Tambah Barang Baru</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" required placeholder="Contoh: Proyektor Epson">
        </div>
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" required>
        </div>
        <div class="form-group">
            <label>Harga Sewa per Hari (Rp)</label>
            <input type="number" name="harga" required>
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" required>
                <option value="1">Elektronik</option>
                <option value="2">Non-Elektronik</option>
            </select>
        </div>
        <div class="form-group">
            <label>Foto Barang (.jpg/.png)</label>
            <input type="file" name="gambar" accept="image/*" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary" style="width:100%;">Simpan Barang</button>
        <a href="barang.php" class="btn btn-outline" style="width:100%; text-align:center; margin-top:10px;">Batal</a>
    </form>
</div>
</body>
</html>