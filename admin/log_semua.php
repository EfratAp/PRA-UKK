<?php
session_start();
include '../config/database.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// QUERY UTAMA: Menggabungkan log_aktivitas dengan users untuk mengambil NAMA
$query = "SELECT log_aktivitas.*, users.nama, users.role 
          FROM log_aktivitas 
          LEFT JOIN users ON log_aktivitas.user_id = users.id 
          ORDER BY log_aktivitas.id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Audit Log - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?= time(); ?>">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div class="dashboard-header">
        <div class="header-title-group">
            <span class="badge badge-admin">Audit System</span>
            <h2>🕵️ Audit Log Aktivitas</h2>
            <p class="subtitle">Rekam jejak seluruh aksi pengguna dalam sistem secara real-time.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline">⬅ Dashboard</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">WAKTU</th>
                    <th style="width: 20%;">PENGGUNA</th>
                    <th style="width: 15%;">ROLE</th>
                    <th class="text-left">AKTIVITAS / PESAN</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <?= date('d/m/Y H:i', strtotime($row['waktu'])); ?>
                    </td>

                    <td style="font-weight: 700;">
                        <?= htmlspecialchars($row['nama'] ?? 'User Dihapus'); ?>
                    </td>

                    <td>
                        <?php 
                        $r = strtolower($row['role'] ?? 'peminjam');
                        $badge = ($r == 'admin') ? 'badge-admin' : (($r == 'petugas') ? 'badge-petugas' : 'badge-peminjam');
                        ?>
                        <span class="badge <?= $badge; ?>">
                            <?= strtoupper($r); ?>
                        </span>
                    </td>

                    <td class="text-left">
                        <?= htmlspecialchars($row['pesan']); ?>
                    </td>
                </tr>
                <?php endwhile; ?>

                <?php if (mysqli_num_rows($result) == 0): ?>
                <tr>
                    <td colspan="4" style="padding: 40px; color: #666;">Belum ada aktivitas terekam.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>