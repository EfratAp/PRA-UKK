<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

// Ambil data barang dan kategorinya
$query = mysqli_query($conn, "SELECT b.*, k.nama_kategori 
                              FROM barang b 
                              LEFT JOIN kategori k ON b.kategori_id = k.id 
                              ORDER BY b.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Master Barang - Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="margin:0;">📦 Master Inventaris Alat</h2>
            <p style="color: #64748b;">Kelola stok dan informasi alat sarpras.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="tambah_barang.php" class="btn btn-primary">+ Tambah Barang</a>
            <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama Barang</th>
                <th>Stok</th>
                <th>Harga Asli</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($b = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><img src="../assets/img/barang/<?= $b['gambar']; ?>" width="50" style="border-radius:5px;"></td>
                <td>
                    <strong><?= htmlspecialchars($b['nama_barang']); ?></strong><br>
                    <small style="color: #64748b;">Kat: <?= $b['nama_kategori']; ?></small>
                </td>
                <td><?= $b['stok']; ?> unit</td>
                <td>Rp <?= number_format($b['harga_asli'], 0, ',', '.'); ?></td>
                <td>
                    <a href="edit_barang.php?id=<?= $b['id']; ?>" style="color: #6366f1;">Edit</a> | 
                    <a href="hapus_barang.php?id=<?= $b['id']; ?>" style="color: #ef4444;" onclick="return confirm('Hapus barang ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>