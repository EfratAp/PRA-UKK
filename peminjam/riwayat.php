<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../auth/login.php");
    exit;
}

$data = mysqli_query($conn, "
    SELECT p.*, b.nama_barang
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    WHERE p.user_id = {$_SESSION['id']}
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Peminjaman</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide"> <h2>Riwayat Peminjaman</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Lama</th>
                    <th>Total Sewa</th>
                    <th>Denda</th> <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($r = mysqli_fetch_assoc($data)) { ?>
            <tr>
                <td><strong><?= htmlspecialchars($r['nama_barang']); ?></strong></td>
                <td><?= $r['lama_pinjam']; ?> hari</td>
                <td>Rp <?= number_format($r['total_harga'], 0, ',', '.'); ?></td>
                <td style="color: red; font-weight: bold;">
                    <?= ($r['denda'] > 0) ? "Rp " . number_format($r['denda'], 0, ',', '.') : "-"; ?>
                </td>
                <td>
                    <span class="badge badge-menunggu"><?= str_replace('_', ' ', $r['status']); ?></span>
                </td>
                <td>
                    <?php if ($r['status'] == 'dipinjam') { ?>
                        <a class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;"
                        href="ajukan_kembali.php?id=<?= $r['id']; ?>"
                        onclick="return confirm('Ajukan pengembalian barang?')">
                        Kembalikan
                        </a>
                    <?php } elseif ($r['status'] == 'selesai') { ?>
                        <a class="btn btn-outline" style="padding: 5px 10px; font-size: 12px;" href="../struk.php?id=<?= $r['id']; ?>" target="_blank">Struk</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        <a href="dashboard.php" class="btn btn-outline">⬅ Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>