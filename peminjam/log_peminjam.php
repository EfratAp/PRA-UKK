<?php
session_start();
include '../config/database.php';
$user_id = $_SESSION['id'];

$query = mysqli_query($conn, "SELECT * FROM log_aktivitas WHERE user_id = '$user_id' ORDER BY waktu DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Aktivitas Saya</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <h2>Riwayat Aktivitas & Keamanan</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                <?php while($l = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($l['waktu'])); ?></td>
                    <td><?= htmlspecialchars($l['pesan']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <a href="dashboard.php" class="btn btn-outline" style="margin-top:20px;">⬅ Kembali</a>
</div>
</body>
</html>