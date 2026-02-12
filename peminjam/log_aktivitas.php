<?php
session_start();
include '../config/database.php';
$user_id = $_SESSION['id'];

$logs = mysqli_query($conn, "SELECT * FROM log_aktivitas WHERE user_id = '$user_id' ORDER BY waktu DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Log Aktivitas Saya</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<div class="box wide">
    <h2>Catatan Aktivitas Anda</h2>
    <div class="timeline" style="margin-top: 20px;">
        <?php while($l = mysqli_fetch_assoc($logs)): ?>
            <div style="padding: 15px; border-left: 4px solid #3b82f6; background: #f8fafc; margin-bottom: 10px; border-radius: 0 10px 10px 0;">
                <small style="color: #64748b; font-weight: bold;"><?= date('d M Y - H:i', strtotime($l['waktu'])); ?></small>
                <p style="margin: 5px 0 0 0; color: #1e293b;"><?= $l['pesan']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
    <a href="dashboard.php" class="btn btn-outline" style="margin-top:20px;">⬅ Kembali</a>
</div>
</body>
</html>