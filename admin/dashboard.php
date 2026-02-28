<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

// Statistik
$total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang"))['total'];
$total_user   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role != 'admin'"))['total'];
$pinjam_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'"))['total'];

// PERBAIKAN: Hanya hitung denda dari transaksi yang sudah 'selesai'
$total_denda  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(denda) as total FROM peminjaman WHERE status = 'selesai'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Panel - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <div class="dashboard-header">
        <div class="header-title-group">
            <span class="badge badge-admin">Administrator</span>
            <h2>🏰 Dashboard Utama</h2>
            <p>Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']); ?></b>.</p>
        </div>
        <a href="../auth/logout.php" class="btn btn-logout">🚪 Keluar</a>
    </div>

    <div class="stats-grid">
        <div class="stats-card">
            <small>Total Inventaris</small>
            <h3><?= $total_barang; ?> Unit</h3>
        </div>
        <div class="stats-card">
            <small>Pengguna Aktif</small>
            <h3><?= $total_user; ?> Akun</h3>
        </div>
        <div class="stats-card">
            <small>Pinjaman Aktif</small>
            <h3><?= $pinjam_aktif; ?> Sesi</h3>
        </div>
        <div class="stats-card" style="background: #f0fdf4;">
            <small style="color: #15803d;">Total Kas Denda</small>
            <h3 style="color: #16a34a;">Rp <?= number_format($total_denda ?? 0, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

    <div class="menu-grid">
        <a href="barang.php" class="menu-card">📦 Master Barang</a>
        <a href="kelola_user.php" class="menu-card">👥 Manajemen User</a>
        <a href="laporan.php" class="menu-card">📄 Laporan Global</a>
        <a href="log_semua.php" class="menu-card">🕵️ Audit Log</a>
    </div>
</div>
</body>
</html>