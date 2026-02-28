<?php
session_start();
include '../config/database.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') { header("Location: ../auth/login.php"); exit; }

$u_id = $_SESSION['id'];
$cek_pinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = '$u_id' AND status = 'dipinjam'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peminjam Dashboard - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 40px;">
        <div style="text-align: left;">
            <h2 style="margin: 0;">Halo, <?= htmlspecialchars($_SESSION['nama']); ?> 👋</h2>
            <p style="margin: 5px 0 0;">Butuh alat apa hari ini?</p>
        </div>
        <div style="text-align: right;">
            <span class="badge badge-disetujui" style="display: block; margin-bottom: 5px;">SISWA / PEMINJAM</span>
            <?php if($cek_pinjam > 0): ?>
                <small style="color: var(--danger); font-weight: bold;">⚠️ Kamu punya <?= $cek_pinjam; ?> alat dipinjam</small>
            <?php endif; ?>
        </div>
    </div>

    <h4 style="text-align: left; margin-bottom: 20px; color: var(--primary);">🔍 Kategori Inventaris</h4>
    <div class="menu-grid">
        <a href="pinjam.php?kat=1" class="menu-card" style="border-top: 5px solid var(--primary-light); background: #f0f7ff;">
            <div style="font-size: 45px; margin-bottom: 15px;">💻</div>
            <span style="font-size: 18px;">Barang Elektronik</span>
            <p style="margin: 10px 0 0; font-size: 12px; color: var(--text-muted);">Laptop, Proyektor, Kamera, dll.</p>
        </a>
        <a href="pinjam.php?kat=2" class="menu-card" style="border-top: 5px solid var(--success); background: #f0fdf4;">
            <div style="font-size: 45px; margin-bottom: 15px;">🪑</div>
            <span style="font-size: 18px;">Non-Elektronik</span>
            <p style="margin: 10px 0 0; font-size: 12px; color: var(--text-muted);">Meja, Kursi, Alat Olahraga, dll.</p>
        </a>
    </div>

    <h4 style="text-align: left; margin: 40px 0 20px; color: var(--primary);">📋 Menu Pengguna</h4>
    <div class="menu-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
        <a href="riwayat.php" class="menu-card">
            <div style="font-size: 25px;">⏳</div>
            <span style="font-size: 14px;">Status & Riwayat</span>
        </a>
        <a href="log_peminjam.php" class="menu-card">
            <div style="font-size: 25px;">🕒</div>
            <span style="font-size: 14px;">Aktivitas</span>
        </a>
        <a href="../auth/logout.php" class="menu-card" style="border-color: var(--danger); color: var(--danger);">
            <div style="font-size: 25px;">🚪</div>
            <span style="font-size: 14px;">Keluar</span>
        </a>
    </div>
</div>
</body>
</html>