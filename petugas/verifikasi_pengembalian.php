<?php
session_start();
include '../config/database.php';

// 1. Proteksi: Hanya Petugas atau Admin
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../auth/login.php"); 
    exit;
}

// 2. Query mengambil data yang statusnya 'menunggu_kembali'
$query = mysqli_query($conn, "SELECT p.*, u.nama, b.nama_barang, b.gambar 
                              FROM peminjaman p 
                              JOIN users u ON p.user_id = u.id 
                              JOIN barang b ON p.barang_id = b.id 
                              WHERE p.status = 'menunggu_kembali' 
                              ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pengembalian - Petugas</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin:0;">🔄 Verifikasi Pengembalian</h2>
            <p style="color: #64748b; margin-top: 5px;">Daftar barang yang dikembalikan user dan butuh pengecekan fisik.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline">⬅ Dashboard</a>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 12px; margin-bottom: 20px; border: 1px solid #bbf7d0; font-weight: 600;">
            ✅ Data pengembalian dan denda berhasil diproses ke sistem.
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th style="text-align: left; padding: 15px;">Peminjam</th>
                    <th style="text-align: left; padding: 15px;">Barang</th>
                    <th style="text-align: center; padding: 15px;">Deadline</th>
                    <th style="text-align: center; padding: 15px;">Aksi Verifikasi Kondisi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($query) > 0): ?>
                    <?php while($p = mysqli_fetch_assoc($query)): 
                        $tgl_deadline = new DateTime($p['tanggal_kembali']);
                        $tgl_sekarang = new DateTime(date('Y-m-d'));
                        
                        $hari_telat = 0;
                        $warna_tgl = "color: #1e293b;"; 
                        
                        if ($tgl_sekarang > $tgl_deadline) {
                            $selisih = $tgl_sekarang->diff($tgl_deadline);
                            $hari_telat = $selisih->days;
                            $warna_tgl = "color: #ef4444; font-weight: bold;"; 
                        }
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 15px;">
                            <strong><?= htmlspecialchars($p['nama']); ?></strong>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="../assets/img/barang/<?= $p['gambar']; ?>" width="40" height="40" style="border-radius: 8px; object-fit: cover;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
                                <div>
                                    <?= htmlspecialchars($p['nama_barang']); ?>
                                    <div style="font-size: 11px; color: #64748b;">Jumlah: <?= $p['jumlah']; ?> unit</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px; text-align: center; <?= $warna_tgl ?>">
                            <?= date('d/m/Y', strtotime($p['tanggal_kembali'])); ?>
                            <?php if($hari_telat > 0): ?>
                                <br><span style="font-size: 10px; background: #fee2e2; padding: 2px 6px; border-radius: 4px;">Telat <?= $hari_telat ?> Hari</span>
                            <?php else: ?>
                                <br><span style="font-size: 10px; color: #10b981;">Tepat Waktu</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="proses_verifikasi.php?id=<?= $p['id']; ?>&kondisi=bagus" 
                                   class="btn" style="background: #10b981; color: white; font-size: 11px; padding: 8px 12px; text-decoration: none; border-radius: 6px;">✅ Bagus</a>
                                
                                <a href="proses_verifikasi.php?id=<?= $p['id']; ?>&kondisi=ringan" 
                                   class="btn" style="background: #f59e0b; color: white; font-size: 11px; padding: 8px 12px; text-decoration: none; border-radius: 6px;">⚠️ Ringan</a>

                                <a href="proses_verifikasi.php?id=<?= $p['id']; ?>&kondisi=berat" 
                                   class="btn" style="background: #ef4444; color: white; font-size: 11px; padding: 8px 12px; text-decoration: none; border-radius: 6px;"
                                   onclick="return confirm('Rusak berat akan dikenakan denda maksimal. Lanjutkan?')">💀 Berat</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                            <p>Tidak ada pengembalian yang perlu diverifikasi.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>