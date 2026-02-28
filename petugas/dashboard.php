<?php
session_start();
include '../config/database.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'petugas') { header("Location: ../auth/login.php"); exit; }

// Sinkronisasi Status: Menghitung antrean yang SESUAI dengan halaman tujuan
$antrean_pinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'menunggu_pinjam'"))['total'];
$antrean_kembali = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'menunggu_kembali'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Petugas Dashboard - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 40px;">
        <div style="text-align: left;">
            <h2 style="margin: 0;">Petugas Operational</h2>
            <p style="margin: 5px 0 0;">Standby, <b><?= htmlspecialchars($_SESSION['nama']); ?></b>.</p>
        </div>
        <span class="badge badge-disetujui">STAFF PETUGAS</span>
    </div>

    <h4 style="text-align: left; margin-bottom: 20px; color: var(--primary);">⚡ Aksi Cepat Operasional</h4>
    <div class="menu-grid">
        <a href="peminjaman.php" class="menu-card" style="border-bottom: 5px solid var(--warning); position: relative;">
            <div style="font-size: 40px; margin-bottom: 10px;">📋</div>
            <span>Approval Pinjam</span>
            <?php if($antrean_pinjam > 0): ?>
                <span style="position: absolute; top: 10px; right: 10px; background: red; color: white; border-radius: 50%; width: 25px; height: 25px; font-size: 12px; display: flex; align-items: center; justify-content: center;"><?= $antrean_pinjam; ?></span>
            <?php endif; ?>
        </a>

        <a href="verifikasi_pengembalian.php" class="menu-card" style="border-bottom: 5px solid var(--success); position: relative;">
            <div style="font-size: 40px; margin-bottom: 10px;">🔄</div>
            <span>Proses Kembali</span>
            <?php if($antrean_kembali > 0): ?>
                <span style="position: absolute; top: 10px; right: 10px; background: red; color: white; border-radius: 50%; width: 25px; height: 25px; font-size: 12px; display: flex; align-items: center; justify-content: center;"><?= $antrean_kembali; ?></span>
            <?php endif; ?>
        </a>

        <a href="barang.php" class="menu-card" style="border-bottom: 5px solid var(--primary-light);">
            <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
            <span>Stok Barang</span>
        </a>
    </div>

    <div class="menu-grid" style="margin-top: 25px; grid-template-columns: 2fr 1fr;">
        <a href="log_semua.php" class="menu-card" style="display: flex; align-items: center; justify-content: center; gap: 20px;">
            <span style="font-size: 30px;">🕵️‍♂️</span>
            <div style="text-align: left;">
                <span style="display: block;">Audit Log Sistem</span>
                <small style="color: var(--text-muted);">Pantau aktivitas masuk/keluar barang.</small>
            </div>
        </a>
        <a href="../auth/logout.php" class="menu-card" style="border-color: var(--danger); color: var(--danger); display: flex; align-items: center; justify-content: center; gap: 10px;">
            <span style="font-size: 20px;">🚪</span>
            <span>Keluar</span>
        </a>
    </div>
</div>
</body>
</html>