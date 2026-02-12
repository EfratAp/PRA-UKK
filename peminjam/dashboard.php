<?php
session_start();
include '../config/database.php'; // Tambahkan ini agar koneksi db tersedia jika butuh query

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peminjam - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0;">Halo, <?= htmlspecialchars($_SESSION['nama']); ?> 👋</h2>
            <p style="color: #64748b;">Selamat datang di Sistem Peminjaman Sarpras.</p>
        </div>
        <span class="badge badge-menunggu" style="text-transform: uppercase;"><?= $_SESSION['role']; ?></span>
    </div>

    <h4 style="margin-bottom: 15px; color: #1e3a8a;">Pilih Kategori Barang</h4>
    <div class="menu-grid" style="margin-bottom: 30px;">
        <a href="pinjam.php?kat=1" class="menu-card" style="border-bottom: 5px solid #3b82f6;">
            <div style="font-size: 40px; margin-bottom: 10px;">💻</div>
            <span style="font-weight: 700;">Barang Elektronik</span>
            <p style="margin: 0; font-size: 12px; color: #64748b;">Laptop, Proyektor, dsb.</p>
        </a>
        <a href="pinjam.php?kat=2" class="menu-card" style="border-bottom: 5px solid #10b981;">
            <div style="font-size: 40px; margin-bottom: 10px;">🪑</div>
            <span style="font-weight: 700;">Non-Elektronik</span>
            <p style="margin: 0; font-size: 12px; color: #64748b;">Kursi, Meja, dsb.</p>
        </a>
    </div>

    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

    <h4 style="margin-bottom: 15px; color: #1e3a8a;">Menu Akun & Riwayat</h4>
    <div class="menu-grid">
        <a href="riwayat.php" class="menu-card">
            <div style="font-size: 24px; margin-bottom: 5px;">⏳</div>
            <span style="font-weight: 600;">Status Pinjaman</span>
        </a>

        <a href="log_peminjam.php" class="menu-card">
            <div style="font-size: 24px; margin-bottom: 5px;">🕒</div>
            <span style="font-weight: 600;">Aktivitas Saya</span>
        </a>

        <a href="../auth/logout.php" class="menu-card" style="border-left: 4px solid #ef4444;">
            <div style="font-size: 24px; margin-bottom: 5px;">🚪</div>
            <span style="font-weight: 600; color: #ef4444;">Keluar</span>
        </a>
    </div>
</div>

</body>
</html>