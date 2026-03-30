<?php
session_start();
include '../config/database.php';

// Proteksi halaman admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

// Statistik
$total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang"))['total'];
$total_user   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role != 'admin'"))['total'];
$pinjam_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'"))['total'];

// Hitung denda dari transaksi yang sudah 'kembali' atau 'selesai'
$total_denda  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(denda) as total FROM peminjaman WHERE status = 'kembali' OR status = 'selesai'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* CSS Tambahan agar tombol sejajar rapi */
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .btn-profil {
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            font-size: 14px;
        }
        .btn-profil:hover {
            background: var(--primary);
            color: white;
        }
        .btn-logout {
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            font-size: 14px;
        }
        .btn-logout:hover {
            background: var(--danger);
            color: white;
        }
    </style>
</head>
<body class="dashboard-page">   
<div class="box wide">
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div class="header-title-group">
            <span class="badge badge-admin">ADMINISTRATOR</span>
            <h2 style="margin-top: 10px;">🏰 Dashboard Utama</h2>
            <p style="margin-top: 5px; color: #666;">
                Selamat datang, 
                    <?= htmlspecialchars($_SESSION['nama']); ?>
                </a>
            </p>
        </div>

        <!-- TOMBOL AKSI DI SEBELAH KANAN -->
        <div class="header-actions">
            <a href="../auth/profil.php" class="btn-profil">⚙️ Password</a>
            <a href="../auth/logout.php" class="btn-logout">🚪 Keluar</a>
        </div>
    </div>

    <div class="stats-grid" style="margin-top: 30px;">
        <div class="stats-card">
            <small>TOTAL INVENTARIS</small>
            <h3><?= $total_barang; ?> Unit</h3>
        </div>
        <div class="stats-card">
            <small>PENGGUNA AKTIF</small>
            <h3><?= $total_user; ?> Akun</h3>
        </div>
        <div class="stats-card">
            <small>PINJAMAN AKTIF</small>
            <h3><?= $pinjam_aktif; ?> Sesi</h3>
        </div>
        <div class="stats-card" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
            <small style="color: #15803d; font-weight: bold;">TOTAL KAS DENDA</small>
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