<?php
session_start();
include '../config/database.php';

// Proteksi Petugas
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') { 
    header("Location: ../auth/login.php"); exit; 
}

// Ambil data laporan gabungan
// Catatan: Saya menggunakan Alias (AS) agar nama kolom konsisten di bawah
$query = mysqli_query($conn, "SELECT p.*, u.nama, b.nama_barang, 
                             p.tanggal_pinjam AS tgl_p, 
                             p.tanggal_kembali AS tgl_k 
                             FROM peminjaman p 
                             JOIN users u ON p.user_id = u.id 
                             JOIN barang b ON p.barang_id = b.id 
                             ORDER BY p.id DESC");

// Hitung total denda
$total_denda_query = mysqli_query($conn, "SELECT SUM(denda) as total FROM peminjaman WHERE status = 'selesai'");
$res_denda = mysqli_fetch_assoc($total_denda_query);
$sum_denda = $res_denda['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Global - Petugas Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    
    <style>
        .box { max-width: 1100px; margin: 20px auto; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); background: white; padding: 25px; }
        
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; }

        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; color: white; display: inline-block; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase; padding: 15px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; }
        td { padding: 12px 10px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-family: 'Times New Roman', serif; }
            .box { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; margin: 0; padding: 0; }
            table { width: 100% !important; border: 1px solid black !important; }
            th, td { border: 1px solid black !important; padding: 8px !important; color: black !important; }
            .badge-status { color: black !important; background: transparent !important; border: none !important; padding: 0; text-transform: uppercase; }
            .signature { display: block !important; margin-top: 50px; }
            @page { size: A4 portrait; margin: 1.5cm; }
        }

        .signature { display: none; }
    </style>
</head>
<body class="dashboard-page">

<div class="box wide">
    <!-- Header Utama -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: #fff; padding: 10px 0;">
        <div>
            <h2 style="margin:0; color: #1e3a8a;">📄 Laporan Global Petugas</h2>
            <p style="margin:5px 0 0; color: #64748b;">Monitoring aktivitas peminjaman & denda.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn" style="background: #1e3a8a; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">🖨️ Cetak Laporan</button>
            <a href="dashboard.php" class="btn" style="background: white; border: 1px solid #cbd5e1; color: #64748b; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold;">Dashboard</a>
        </div>
    </div>

    <!-- Statistik -->
    <div class="no-print stat-grid">
        <div class="stat-card" style="border-left: 5px solid #10b981;">
            <small style="color: #64748b; font-weight: bold;">TOTAL KAS DENDA</small>
            <h3 style="margin: 5px 0 0; color: #059669;">Rp <?= number_format($sum_denda, 0, ',', '.'); ?></h3>
        </div>
        <div class="stat-card" style="border-left: 5px solid #3b82f6;">
            <small style="color: #64748b; font-weight: bold;">TOTAL TRANSAKSI</small>
            <h3 style="margin: 5px 0 0; color: #1d4ed8;"><?= mysqli_num_rows($query); ?> Record</h3>
        </div>
    </div>

    <!-- Header Cetak -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin:0; font-size: 22px; text-transform: uppercase; color: #1e3a8a;">Laporan Inventaris Sarana Prasarana</h1>
        <p style="margin:5px 0; font-size: 14px; color: #64748b;">Data Operasional Petugas - Dicetak pada: <?= date('d/m/Y H:i'); ?></p>
        <div style="border-bottom: 3px double #1e3a8a; margin-top: 15px;"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>PEMINJAM</th>
                <th>BARANG</th>
                <th style="text-align: center;">TGL PINJAM</th>
                <th style="text-align: center;">TGL KEMBALI</th>
                <th style="text-align: center;">STATUS</th>
                <th style="text-align: right;">DENDA</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $total_denda_table = 0;
            if(mysqli_num_rows($query) > 0):
                while($l = mysqli_fetch_assoc($query)): 
                $total_denda_table += $l['denda'];
                $warna_status = ($l['status'] == 'selesai') ? '#10b981' : (($l['status'] == 'dipinjam') ? '#3b82f6' : '#f59e0b');
                
                // Logika agar tidak 1970 jika data null/kosong
                $tgl_pinjam = ($l['tgl_p'] && $l['tgl_p'] != '0000-00-00') ? date('d/m/Y', strtotime($l['tgl_p'])) : '-';
                $tgl_kembali = ($l['tgl_k'] && $l['tgl_k'] != '0000-00-00') ? date('d/m/Y', strtotime($l['tgl_k'])) : '<span style="color:#cbd5e1;">—</span>';
            ?>
            <tr>
                <td style="text-align: center; color: #94a3b8;"><?= $no++; ?></td>
                <td><strong><?= htmlspecialchars($l['nama']); ?></strong></td>
                <td><?= htmlspecialchars($l['nama_barang']); ?></td>
                <td style="text-align: center;"><?= $tgl_pinjam; ?></td>
                <td style="text-align: center;"><?= $tgl_kembali; ?></td>
                <td style="text-align: center;">
                    <span class="badge-status" style="background: <?= $warna_status ?>;">
                        <?= strtoupper(str_replace('_', ' ', $l['status'])); ?>
                    </span>
                </td>
                <td style="text-align: right; font-family: monospace; font-weight: bold;">
                    <?= ($l['denda'] > 0) ? 'Rp ' . number_format($l['denda'], 0, ',', '.') : '-'; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">Data tidak ditemukan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="6" style="text-align: right; padding: 15px;">TOTAL SALDO DENDA :</td>
                <td style="text-align: right; font-size: 15px; color: #1e3a8a; background: #f1f5f9;">
                    Rp <?= number_format($total_denda_table, 0, ',', '.'); ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div style="float: right; text-align: center; width: 250px;">
            <p>Medan, <?= date('d F Y'); ?></p>
            <p style="margin-bottom: 60px;">Petugas Operasional,</p>
            <p><strong><u>( <?= strtoupper($_SESSION['nama']); ?> )</u></strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

</body>
</html>