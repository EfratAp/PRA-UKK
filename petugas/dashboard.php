<?php
session_start();
// Proteksi halaman: jika belum login atau bukan petugas, tendang ke login
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0;">Dashboard Petugas</h2>
            <p style="color: #64748b; margin-top: 5px;">Selamat bekerja, <b><?= htmlspecialchars($_SESSION['nama']); ?></b>. Kelola sarpras di bawah ini.</p>
        </div>
        <span class="badge badge-disetujui" style="text-transform: uppercase; letter-spacing: 1px;">Petugas</span>
    </div>

    <h4 style="margin-bottom: 20px; color: #1e3a8a;">Manajemen & Operasional</h4>
    <div class="menu-grid" style="margin-bottom: 40px;">
        <a href="barang.php" class="menu-card" style="border-bottom: 5px solid #3b82f6;">
            <div style="font-size: 40px; margin-bottom: 15px;">📦</div>
            <span style="font-weight: 700; display: block; font-size: 1.1rem;">Data Barang</span>
            <p style="margin: 5px 0 0; font-size: 12px; color: #64748b;">Kelola stok & inventaris.</p>
        </a>

        <a href="peminjaman.php" class="menu-card" style="border-bottom: 5px solid #f59e0b;">
            <div style="font-size: 40px; margin-bottom: 15px;">📋</div>
            <span style="font-weight: 700; display: block; font-size: 1.1rem;">Approval Pinjam</span>
            <p style="margin: 5px 0 0; font-size: 12px; color: #64748b;">Verifikasi permintaan baru.</p>
        </a>

        <a href="pengembalian.php" class="menu-card" style="border-bottom: 5px solid #10b981;">
            <div style="font-size: 40px; margin-bottom: 15px;">🔄</div>
            <span style="font-weight: 700; display: block; font-size: 1.1rem;">Pengembalian</span>
            <p style="margin: 5px 0 0; font-size: 12px; color: #64748b;">Proses barang kembali.</p>
        </a>
    </div>

    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

    <h4 style="margin-bottom: 20px; color: #1e3a8a;">Sistem & Keamanan</h4>
    <div class="menu-grid">
        <a href="log_semua.php" class="menu-card" style="border-left: 5px solid #6366f1;">
            <div style="font-size: 24px; margin-bottom: 10px;">🕵️‍♂️</div>
            <span style="font-weight: 600;">Audit Log Sistem</span>
            <p style="margin: 5px 0 0; font-size: 11px; color: #64748b;">Pantau aktivitas seluruh user.</p>
        </a>

        <a href="../auth/logout.php" class="menu-card" style="border-left: 5px solid #ef4444; color: #ef4444;">
            <div style="font-size: 24px; margin-bottom: 10px;">🚪</div>
            <span style="font-weight: 600;">Keluar Aplikasi</span>
            <p style="margin: 5px 0 0; font-size: 11px; color: #64748b;">Akhiri sesi kerja petugas.</p>
        </a>
    </div>
</div>

</body>
</html>