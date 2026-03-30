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
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px;">
        <div style="text-align: left;">
            <h2 style="margin: 0; color: #1e3a8a; font-size: 28px;">Halo, <?= htmlspecialchars($_SESSION['nama']); ?> 👋</h2>
            <p style="margin: 5px 0 0; color: #64748b;">Butuh alat apa hari ini?</p>
        </div>

        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
            <span style="background: #dcfce7; color: #15803d; padding: 6px 20px; border-radius: 50px; font-size: 11px; font-weight: bold; border: 1px solid #bbf7d0;">SISWA / PEMINJAM</span>
            <div style="display: flex; gap: 10px;">
                <a href="../auth/profil.php" style="border: 1px solid #1e3a8a; color: #1e3a8a; padding: 8px 15px; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: bold;">⚙️ Password</a>
                <a href="../auth/logout.php" style="border: 1px solid #ef4444; color: #ef4444; padding: 8px 15px; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: bold;">🚪 Keluar</a>
            </div>
        </div>
    </div>

    <?php if($cek_pinjam > 0): ?>
        <div style="margin-bottom: 25px; background: #fff1f2; border: 1px solid #fecaca; padding: 10px 15px; border-radius: 8px; color: #be123c; font-size: 13px;">
            ⚠️ <b>Perhatian:</b> Anda sedang meminjam <b><?= $cek_pinjam; ?></b> barang. Jangan lupa dikembalikan tepat waktu!
        </div>
    <?php endif; ?>

    <h4 style="text-align: left; margin-bottom: 20px; color: #1e3a8a;">🔍 Kategori Inventaris</h4>
    <div class="menu-grid">
        <a href="pinjam.php?kat=1" class="menu-card" style="border-top: 5px solid #3b82f6; background: #f8fafc;">
            <div style="font-size: 45px; margin-bottom: 15px;">💻</div>
            <span style="font-size: 18px; font-weight: bold; color: #1e3a8a;">Barang Elektronik</span>
            <p style="margin: 10px 0 0; font-size: 12px; color: #64748b;">Laptop, Proyektor, Kamera, dll.</p>
        </a>
        <a href="pinjam.php?kat=2" class="menu-card" style="border-top: 5px solid #10b981; background: #f8fafc;">
            <div style="font-size: 45px; margin-bottom: 15px;">🪑</div>
            <span style="font-size: 18px; font-weight: bold; color: #1e3a8a;">Non-Elektronik</span>
            <p style="margin: 10px 0 0; font-size: 12px; color: #64748b;">Meja, Kursi, Alat Olahraga, dll.</p>
        </a>
    </div>

    <h4 style="text-align: left; margin: 40px 0 20px; color: #1e3a8a;">📋 Menu Pengguna</h4>
    <div class="menu-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <a href="riwayat.php" class="menu-card">⌛ Status & Riwayat</a>
        <a href="log_peminjam.php" class="menu-card">🕒 Aktivitas Saya</a>
    </div>
</div>
</body>
</html>