<?php
session_start();
include '../config/database.php';

// Proteksi Petugas
if ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$query = mysqli_query($conn, "SELECT p.*, u.nama, b.nama_barang 
                              FROM peminjaman p 
                              JOIN users u ON p.user_id = u.id 
                              JOIN barang b ON p.barang_id = b.id 
                              WHERE p.status = 'menunggu_kembali' OR p.status = 'dipinjam'
                              ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Verifikasi Pengembalian</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <h2>Verifikasi Pengembalian Barang</h2>
    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Barang</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Aksi Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($r = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= $r['nama']; ?></td>
                <td><?= $r['nama_barang']; ?></td>
                <td><span class="badge"><?= $r['status']; ?></span></td>
                <td>
                    <?php if($r['status'] == 'menunggu_kembali'): ?>
                        <div style="display: flex; gap: 5px;">
                            <a href="proses_verifikasi.php?id=<?= $r['id']; ?>&kondisi=bagus" 
                            class="btn" style="background: #22c55e; color: white; font-size: 11px;">
                            ✅ Bagus
                            </a>
                            
                            <a href="proses_verifikasi.php?id=<?= $r['id']; ?>&kondisi=rusak" 
                            class="btn" style="background: #ef4444; color: white; font-size: 11px;"
                            onclick="return confirm('Apakah Anda yakin barang ini rusak? Denda Rp 50.000 akan dikenakan!')">
                            ⚠️ Rusak (Denda)
                            </a>
                        </div>
                    <?php else: ?>
                        <small>Selesai</small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-outline" style="margin-top:20px;">⬅ Kembali</a>
</div>
</body>
</html>