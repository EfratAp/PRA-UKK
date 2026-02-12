<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

// Query mengambil semua aktivitas dari semua role
$query = mysqli_query($conn, "SELECT l.*, u.nama, u.role 
                              FROM log_aktivitas l 
                              JOIN users u ON l.user_id = u.id 
                              ORDER BY l.waktu DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Audit Log Sistem - Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>🕵️ Audit Log Seluruh Sistem</h2>
        <a href="dashboard.php" class="btn btn-outline">⬅ Kembali</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Aktivitas / History</th>
                </tr>
            </thead>
            <tbody>
                <?php while($l = mysqli_fetch_assoc($query)): ?>
                <tr style="<?= ($l['role'] == 'petugas') ? 'background: #fffbeb;' : ''; ?>">
                    <td><?= date('d/m/H i:s', strtotime($l['waktu'])); ?></td>
                    <td><strong><?= htmlspecialchars($l['nama']); ?></strong></td>
                    <td><span class="badge"><?= $l['role']; ?></span></td>
                    <td><?= htmlspecialchars($l['pesan']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>