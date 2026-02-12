<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0;">Dashboard Admin</h2>
            <p style="color: #64748b; margin-top: 5px;">Halo Administrator, <b><?= htmlspecialchars($_SESSION['nama']); ?></b>. Kendalikan sistem sepenuhnya di sini.</p>
        </div>
        <span class="badge badge-danger" style="text-transform: uppercase; letter-spacing: 1px; padding: 8px 15px;">Administrator</span>
    </div>

    <h4 style="margin-bottom: 20px; color: #1e3a8a;">Kontrol Pengguna & Data</h4>
    <div class="menu-grid" style="margin-bottom: 40px;">
        <a href="kelola_user.php" class="menu-card" style="border-bottom: 5px solid #3b82f6;">
            <div style="font-size: 40px; margin-bottom: 15px;">👥</div>
            <span style="font-weight: 700; display: block; font-size: 1.1rem;">Kelola User</span>
            <p style="margin: 5px 0 0; font-size: 12px; color: #64748b;">Atur hak akses & role pengguna.</p>
        </a>

        <a href="laporan.php" class="menu-card" style="border-bottom: 5px solid #10b981;">
            <div style="font-size: 40px; margin-bottom: 15px;">📄</div>
            <span style="font-weight: 700; display: block; font-size: 1.1rem;">Laporan Pinjam</span>
            <p style="margin: 5px 0 0; font-size: 12px; color: #64748b;">Cetak data transaksi & denda.</p>
        </a>
    </div>

    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

    <h4 style="margin-bottom: 20px; color: #1e3a8a;">Sistem & Keamanan</h4>
    <div class="menu-grid">
        <a href="log_semua.php" class="menu-card" style="border-left: 5px solid #6366f1;">
            <div style="font-size: 24px; margin-bottom: 10px;">🕵️‍♂️</div>
            <span style="font-weight: 600;">Audit Log</span>
            <p style="margin: 5px 0 0; font-size: 11px; color: #64748b;">Pantau rekam jejak aktivitas sistem.</p>
        </a>

        <a href="../auth/logout.php" class="menu-card" style="border-left: 5px solid #ef4444; color: #ef4444;">
            <div style="font-size: 24px; margin-bottom: 10px;">🚪</div>
            <span style="font-weight: 600;">Logout</span>
            <p style="margin: 5px 0 0; font-size: 11px; color: #64748b;">Keluar dari sesi administrator.</p>
        </a>
    </div>
</div>

</body>
</html>