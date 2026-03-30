<?php
session_start();
include '../config/database.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'petugas') { header("Location: ../auth/login.php"); exit; }

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
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px;">
        <div style="text-align: left;">
            <h2 style="margin: 0; color: #1e3a8a; font-size: 28px;">Petugas Operational</h2>
            <p style="margin: 5px 0 0; color: #64748b;">Standby, <b><?= htmlspecialchars($_SESSION['nama']); ?></b>.</p>
        </div>

        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
            <span style="background: #e0e7ff; color: #4338ca; padding: 6px 20px; border-radius: 50px; font-size: 11px; font-weight: bold; border: 1px solid #c7d2fe;">STAFF PETUGAS</span>
            <div style="display: flex; gap: 10px;">
                <a href="../auth/profil.php" style="border: 1px solid #1e3a8a; color: #1e3a8a; padding: 8px 15px; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: bold;">⚙️ Password</a>
                <a href="../auth/logout.php" style="border: 1px solid #ef4444; color: #ef4444; padding: 8px 15px; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: bold;">🚪 Keluar</a>
            </div>
        </div>
    </div>

    <h4 style="text-align: left; margin-bottom: 20px; color: #1e3a8a;">⚡ Aksi Cepat Operasional</h4>
    <div class="menu-grid">
        <a href="peminjaman.php" class="menu-card" style="border-bottom: 5px solid #f59e0b; position: relative;">
            <div style="font-size: 40px; margin-bottom: 10px;">📋</div>
            <span style="font-weight: bold; color: #1e3a8a;">Approval Pinjam</span>
            <?php if($antrean_pinjam > 0): ?>
                <span style="position: absolute; top: -10px; right: -10px; background: #ef4444; color: white; border-radius: 50%; width: 25px; height: 25px; font-size: 11px; display: flex; align-items: center; justify-content: center;"><?= $antrean_pinjam; ?></span>
            <?php endif; ?>
        </a>

        <a href="verifikasi_pengembalian.php" class="menu-card" style="border-bottom: 5px solid #10b981; position: relative;">
            <div style="font-size: 40px; margin-bottom: 10px;">🔄</div>
            <span style="font-weight: bold; color: #1e3a8a;">Proses Kembali</span>
            <?php if($antrean_kembali > 0): ?>
                <span style="position: absolute; top: -10px; right: -10px; background: #ef4444; color: white; border-radius: 50%; width: 25px; height: 25px; font-size: 11px; display: flex; align-items: center; justify-content: center;"><?= $antrean_kembali; ?></span>
            <?php endif; ?>
        </a>

        <a href="barang.php" class="menu-card" style="border-bottom: 5px solid #3b82f6;">
            <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
            <span style="font-weight: bold; color: #1e3a8a;">Stok Barang</span>
        </a>
    </div>

    <div class="menu-grid" style="margin-top: 25px; grid-template-columns: 2fr 1fr;">
        <a href="log_semua.php" class="menu-card" style="display: flex; align-items: center; justify-content: center; gap: 20px; background: #f8fafc;">
            <span style="font-size: 30px;">🕵️‍♂️</span>
            <div style="text-align: left;">
                <span style="display: block; font-weight: bold; color: #1e3a8a;">Audit Log Sistem</span>
                <small style="color: #64748b;">Pantau aktivitas keluar/masuk barang.</small>
            </div>
        </a>
        <a href="laporan.php" class="menu-card" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
            <span style="font-size: 20px;">📄</span>
            <span style="font-weight: bold; color: #1e3a8a;">Laporan</span>
        </a>
    </div>
</div>
</body>
</html>