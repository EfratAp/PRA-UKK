<?php
session_start();
include '../config/database.php';

// Proteksi: Hanya petugas yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data peminjaman yang masih menunggu persetujuan
$data = mysqli_query($conn, "
    SELECT p.*, u.nama, b.nama_barang, b.gambar
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN barang b ON p.barang_id = b.id
    WHERE p.status = 'menunggu_pinjam'
    ORDER BY p.id ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Peminjaman - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #1e3a8a;">📋 Approval Peminjaman Barang</h2>
            <p style="color: #64748b; margin-top: 5px;">Daftar permintaan masuk yang memerlukan persetujuan petugas.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline" style="font-size: 13px;">⬅ Dashboard</a>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 12px; margin-bottom: 20px; font-weight: 600; border: 1px solid #bbf7d0;">
            ✅ Keputusan berhasil disimpan dan stok telah diperbarui.
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Informasi Barang</th>
                    <th style="text-align: center;">Lama Pinjam</th>
                    <th style="text-align: center;">Tgl Pengajuan</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($data) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($p['nama']); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="../assets/img/barang/<?= $p['gambar']; ?>" width="45" height="45" style="object-fit: cover; border-radius: 10px; background: #f1f5f9; border: 1px solid #e2e8f0;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
                                <div>
                                    <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($p['nama_barang']); ?></div>
                                    <div style="font-size: 11px; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-top: 2px;">
                                        Jumlah: <?= $p['jumlah']; ?> Unit
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 500; color: #475569;">
                            <?= $p['lama_pinjam']; ?> Hari
                        </td>
                        <td style="text-align: center; color: #64748b; font-size: 13px;">
                            <?= date('d/m/Y', strtotime($p['tanggal_pinjam'])); ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="proses_approval.php?id=<?= $p['id']; ?>&aksi=setuju" 
                                   class="btn" style="background: #10b981; color: white; padding: 8px 16px; font-size: 12px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s; border: none; cursor: pointer;">
                                   ✔ Setuju
                                </a>
                                
                                <a href="proses_approval.php?id=<?= $p['id']; ?>&aksi=tolak" 
                                   class="btn" style="background: #ef4444; color: white; padding: 8px 16px; font-size: 12px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s; border: none; cursor: pointer;"
                                   onclick="return confirm('Apakah Anda yakin ingin menolak permintaan ini?')">
                                   ✖ Tolak
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <div style="font-size: 50px; margin-bottom: 15px;">✨</div>
                            <div style="font-weight: 600; font-size: 16px;">Semua Beres!</div>
                            <div style="font-size: 13px;">Tidak ada permintaan peminjaman baru yang menunggu.</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>