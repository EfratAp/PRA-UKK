<?php
session_start();
include '../config/database.php';

// Proteksi role: Admin dan Petugas bisa akses
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../auth/login.php"); exit;
}

// Ambil data peminjaman yang sudah selesai
$query = "SELECT p.*, u.nama as nama_peminjam, b.nama_barang 
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.id 
          JOIN barang b ON p.barang_id = b.id 
          WHERE p.status = 'selesai' 
          ORDER BY p.id DESC";
$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>📄 Laporan Pengembalian & Denda</h2>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Laporan (Print)</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Barang</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Denda</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_pendapatan_denda = 0;
            while($row = mysqli_fetch_assoc($data)): 
                $total_pendapatan_denda += $row['denda'];
            ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                <td>Rp <?= number_format($row['denda'], 0, ',', '.'); ?></td>
                <td><span class="badge shadow-sm" style="background:#10b981; color:white; padding:4px 8px; border-radius:5px;">Selesai</span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="4" style="text-align: right;">Total Denda Terkumpul:</td>
                <td colspan="2">Rp <?= number_format($total_pendapatan_denda, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>