<?php
session_start();
include '../config/database.php';

// Proteksi: Hanya petugas yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

$data = mysqli_query($conn, "
    SELECT p.*, u.nama, b.nama_barang, b.gambar, b.harga_asli
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN barang b ON p.barang_id = b.id
    WHERE p.status = 'menunggu_kembali' 
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pengembalian - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0;">🔄 Verifikasi Pengembalian</h2>
            <p style="color: #64748b; margin-top: 5px;">Periksa kondisi barang fisik sebelum menyetujui pengembalian.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline" style="font-size: 13px;">⬅ Dashboard</a>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            ✅ Berhasil memverifikasi pengembalian dan mencatat denda (jika ada).
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Informasi Barang</th>
                    <th>Tgl Seharusnya Kembali</th>
                    <th style="text-align: center;">Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($data) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($p['nama']); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="../assets/img/barang/<?= $p['gambar']; ?>" width="40" height="40" style="object-fit: cover; border-radius: 5px; background: #eee;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
                                <div>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($p['nama_barang']); ?></div>
                                    <div style="font-size: 11px; color: #64748b;"><?= $p['jumlah']; ?> Unit</div>
                                </div>
                            </div>
                        </td>
                        <td style="color: #ef4444; font-weight: 600;">
                            <?= date('d M Y', strtotime($p['tanggal_kembali'])); ?>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 8px; align-items: center;">
                                <a href="proses_verifikasi.php?id=<?= $p['id']; ?>&kondisi=bagus" 
                                class="btn" style="background: #10b981; color: white; width: 180px; font-size: 11px; padding: 6px;">
                                ✅ Kondisi Baik (Gratis)
                                </a>
                                
                                <a href="proses_verifikasi.php?id=<?= $p['id']; ?>&kondisi=ringan" 
                                class="btn" style="background: #f59e0b; color: white; width: 180px; font-size: 11px; padding: 6px;">
                                ⚠️ Rusak Ringan (Rp 20rb)
                                </a>

                                <a href="proses_verifikasi.php?id=<?= $p['id']; ?>&kondisi=sedang" 
                                class="btn" style="background: #ef4444; color: white; width: 180px; font-size: 11px; padding: 6px;">
                                ⚠️ Rusak Sedang (Rp 50rb)
                                </a>

                                <a href="proses_verifikasi.php?id=<?= $p['id']; ?>&kondisi=berat" 
                                class="btn" style="background: #1e293b; color: white; width: 180px; font-size: 11px; padding: 6px;"
                                onclick="return confirm('Denda Berat: User harus membayar seharga barang (Rp <?= number_format($p['harga_asli']); ?>). Lanjutkan?')">
                                💀 Rusak Berat (Ganti Alat)
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                            <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                            Tidak ada pengembalian yang perlu diverifikasi saat ini.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>