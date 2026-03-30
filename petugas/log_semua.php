<?php
session_start();
include '../config/database.php';

// Proteksi: Hanya Petugas dan Admin
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../auth/login.php");
    exit;
}

// Query untuk mengambil semua log dan menggabungkan dengan tabel user agar muncul namanya
$query = mysqli_query($conn, "SELECT l.*, u.nama, u.role 
                              FROM log_aktivitas l 
                              JOIN users u ON l.user_id = u.id 
                              ORDER BY l.waktu DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Audit Log Sistem - Petugas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>🕵️ Audit Log Sistem</h2>
        <a href="dashboard.php" class="btn btn-outline" style="font-size: 12px;">⬅ Kembali</a>
    </div>
    
    <p style="margin-bottom: 20px; color: #64748b;">Menampilkan 100 aktivitas terbaru di sistem.</p>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Nama User</th>
                    <th>Role</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                <?php while($l = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td style="white-space: nowrap; color: #64748b; font-size: 13px;">
                        <?= date('d M Y, H:i', strtotime($l['waktu'])); ?>
                    </td>
                    <td><strong><?= htmlspecialchars($l['nama']); ?></strong></td>
                    <td>
                        <span class="badge" style="background: <?= ($l['role'] == 'petugas') ? '#f59e0b' : '#3b82f6'; ?>; color: white;">
                            <?= $l['role']; ?>
                        </span>
                    </td>
                    <td style="color: #1e293b;"><?= htmlspecialchars($l['pesan']); ?></td>
                </tr>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($query) == 0): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Belum ada aktivitas tercatat.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>