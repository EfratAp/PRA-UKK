<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../auth/login.php");
    exit;
}

$u_id = $_SESSION['id'];
$data = mysqli_query($conn, "SELECT p.*, b.nama_barang 
                              FROM peminjaman p 
                              JOIN barang b ON p.barang_id = b.id 
                              WHERE p.user_id = '$u_id' 
                              ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pinjam - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; table-layout: fixed; background: white; }
        th, td { padding: 15px; text-align: center; border-bottom: 1px solid #e2e8f0; vertical-align: middle; font-size: 14px; }
        th:first-child, td:first-child { text-align: left; width: 30%; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 50px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .badge-menunggu { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-dipinjam { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-kembali { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-selesai { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-ditolak { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .btn-action { font-size: 11px; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-weight: 700; display: inline-block; }
    </style>
</head>
<body class="dashboard-page">
<div class="box wide">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="margin:0;">📋 Riwayat & Status Pinjaman</h2>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Informasi Barang</th>
                    <th style="width: 15%;">Durasi</th>
                    <th style="width: 15%;">Total Denda</th>
                    <th style="width: 20%;">Status</th>
                    <th style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($data) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($r['nama_barang']); ?></strong>
                            <div style="font-size: 11px; color: #64748b;"><?= $r['jumlah']; ?> Unit</div>
                        </td>
                        <td><?= $r['lama_pinjam']; ?> Hari</td>
                        <td style="font-weight: 700; color: <?= ($r['denda'] > 0) ? '#ef4444' : '#64748b'; ?>">
                            <?= ($r['denda'] > 0) ? "Rp ".number_format($r['denda'], 0, ',', '.') : "-"; ?>
                        </td>
                        <td>
                            <?php 
                                $status = $r['status'];
                                $class = 'badge-menunggu';
                                if($status == 'dipinjam') $class = 'badge-dipinjam';
                                elseif($status == 'menunggu_kembali') $class = 'badge-kembali';
                                elseif($status == 'selesai') $class = 'badge-selesai';
                                elseif($status == 'ditolak') $class = 'badge-ditolak';
                            ?>
                            <span class="badge <?= $class; ?>">
                                <?= str_replace('_', ' ', $status); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($status == 'menunggu_pinjam'): ?>
                                <a href="../struk.php?id=<?= $r['id']; ?>" class="btn-action" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">🖨️ Struk</a>
                            <?php elseif ($status == 'dipinjam'): ?>
                                <a href="ajukan_kembali.php?id=<?= $r['id']; ?>" class="btn-action" style="background: #3b82f6; color: white;" onclick="return confirm('Kembalikan barang ini?')">🔄 Kembalikan</a>
                            <?php elseif ($status == 'selesai' || $status == 'ditolak'): ?>
                                <a href="../struk.php?id=<?= $r['id']; ?>" class="btn-action" style="background: #f1f5f9; color: #475569;">📄 Detail</a>
                            <?php else: ?>
                                <small style="color: #94a3b8;">Sedang diproses</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">Belum ada data peminjaman.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 30px;"><a href="dashboard.php" class="btn btn-outline">⬅ Dashboard</a></div>
</div>
</body>
</html>