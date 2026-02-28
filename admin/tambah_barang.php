<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");

if (isset($_POST['submit'])) {
    $nama       = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $stok       = (int)$_POST['stok'];
    $harga_asli = (int)$_POST['harga_asli']; 
    $kategori   = mysqli_real_escape_string($conn, $_POST['kategori_id']);
    
    $gambar    = $_FILES['gambar']['name'];
    $tmp_name  = $_FILES['gambar']['tmp_name'];
    $ekstensi  = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ekstensi, $allowed)) {
        $nama_baru = time() . "_" . uniqid() . "." . $ekstensi; 
        $tujuan    = "../assets/img/barang/" . $nama_baru;

        if (move_uploaded_file($tmp_name, $tujuan)) {
            // Sesuai struktur tabel: harga (untuk sewa) diset 0, harga_asli diisi nominal
            $sql = "INSERT INTO barang (nama_barang, stok, harga, harga_asli, gambar, kategori_id) 
                    VALUES ('$nama', '$stok', 0, '$harga_asli', '$nama_baru', '$kategori')";
            
            if (mysqli_query($conn, $sql)) {
                $pesan_log = "Admin " . $_SESSION['nama'] . " menambah barang baru: $nama";
                mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");
                echo "<script>alert('Barang berhasil ditambahkan!'); window.location='barang.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Format file tidak didukung!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang - Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box">
    <h2>➕ Tambah Barang Baru</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" required>
        </div>
        <div class="form-group">
            <label>Stok Awal</label>
            <input type="number" name="stok" min="1" required>
        </div>
        <div class="form-group">
            <label>Harga Asli Barang (Rp)</label>
            <input type="number" name="harga_asli" required>
        </div>
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while($kat = mysqli_fetch_assoc($kategori_query)): ?>
                    <option value="<?= $kat['id']; ?>"><?= $kat['nama_kategori']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Foto Barang</label>
            <input type="file" name="gambar" accept="image/*" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary" style="width:100%;">Simpan Barang</button>
        <a href="barang.php" class="btn btn-outline" style="width:100%; text-align:center; display:block; margin-top:10px;">Batal</a>
    </form>
</div>
</body>
</html>