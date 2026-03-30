<?php
session_start();
include '../config/database.php';

// Proteksi: Hanya peminjam yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data barang yang stoknya masih ada
$query = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Barang - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0;">📦 Katalog Sarpras</h2>
            <p style="color: #64748b; margin-top: 5px;">Pilih barang yang ingin Anda pinjam hari ini.</p>
        </div>
        <a href="../auth/logout.php" class="btn btn-outline" style="background: #fee2e2; color: #ef4444; border: none;">Keluar</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php while ($b = mysqli_fetch_assoc($query)): ?>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 15px; overflow: hidden; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <img src="../assets/img/barang/<?= $b['gambar']; ?>" style="width: 100%; height: 180px; object-fit: cover;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
            
            <div style="padding: 20px;">
                <h3 style="margin: 0; font-size: 18px; color: #1e293b;"><?= htmlspecialchars($b['nama_barang']); ?></h3>
                <p style="font-size: 13px; color: #64748b; margin: 10px 0;">Stok Tersedia: <strong><?= $b['stok']; ?> Unit</strong></p>
                
                <form action="proses_pinjam.php" method="POST">
                    <input type="hidden" name="barang_id" value="<?= $b['id']; ?>">
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 15px;">
                        <input type="number" name="jumlah" value="1" min="1" max="<?= $b['stok']; ?>" style="width: 60px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px;" required>
                        <small style="color: #94a3b8;">Unit</small>
                    </div>
                    <button type="submit" name="pinjam" class="btn" style="width: 100%; background: #2563eb; color: white; border-radius: 10px; font-weight: 600;"> Pinjam Sekarang</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>