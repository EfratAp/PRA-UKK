<?php
session_start();
include '../config/database.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

// Ambil data laporan gabungan (Semua yang sudah selesai/diproses)
$query = mysqli_query($conn, "SELECT p.*, u.nama, b.nama_barang 
                             FROM peminjaman p 
                             JOIN users u ON p.user_id = u.id 
                             JOIN barang b ON p.barang_id = b.id 
                             ORDER BY p.tanggal_pinjam DESC");

// Hitung total denda untuk ringkasan di atas (statistik)
$total_denda_query = mysqli_query($conn, "SELECT SUM(denda) as total FROM peminjaman WHERE status = 'selesai'");
$res_denda = mysqli_fetch_assoc($total_denda_query);
$sum_denda = $res_denda['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Resmi - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    
    <style>
        .box { max-width: 1100px; margin: 20px auto; }
        
        /* Statistik Cards (Hanya muncul di layar) */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: white;
        }

        /* Badge Status */
        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: white;
        }

        /* PRINT LOGIC */
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-family: 'Times New Roman', serif; }
            .box { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; }
            table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px; }
            th, td { border: 1px solid black !important; padding: 8px !important; font-size: 12px !important; }
            .badge-status { color: black !important; background: transparent !important; border: none !important; padding: 0; text-transform: uppercase; }
            .signature { display: block !important; margin-top: 40px; }
            @page { size: A4 portrait; margin: 2cm; }
        }

        .signature { display: none; }
    </style>
</head>
<body class="dashboard-page">

<div class="box wide">
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <div>
            <h2 style="margin:0;">📋 Laporan & Audit Transaksi</h2>
            <p style="margin:5px 0 0; color: #64748b;">Rekapitulasi denda dan riwayat barang sarpras.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary" style="background: #0f172a;">🖨️ Cetak Laporan</button>
            <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
        </div>
    </div>

    <div class="no-print stat-grid">
        <div class="stat-card" style="border-left: 5px solid #10b981;">
            <small style="color: #64748b;">Total Pendapatan Denda</small>
            <h3 style="margin: 5px 0 0; color: #059669;">Rp <?= number_format($sum_denda, 0, ',', '.'); ?></h3>
        </div>
        <div class="stat-card" style="border-left: 5px solid #3b82f6;">
            <small style="color: #64748b;">Total Record Data</small>
            <h3 style="margin: 5px 0 0; color: #1d4ed8;"><?= mysqli_num_rows($query); ?> Transaksi</h3>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="margin:0; font-size: 22px; text-transform: uppercase;">Laporan Inventaris Sarana Prasarana</h1>
        <p style="margin:5px 0; font-size: 14px;">Data periode berjalan - Dicetak oleh: <?= $_SESSION['nama']; ?></p>
        <div style="border-bottom: 2px solid black; margin-top: 10px;"></div>
        <div style="border-bottom: 1px solid black; margin-top: 2px;"></div>
    </div>

    <table>
        <thead>
            <tr style="background: #f8fafc;">
                <th width="5%">NO</th>
                <th width="15%">PEMINJAM</th>
                <th width="20%">BARANG</th>
                <th width="12%">TGL PINJAM</th>
                <th width="12%">TGL KEMBALI</th>
                <th width="13%">STATUS</th>
                <th width="23%">DENDA</th>
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
            ?>
            <tr>
                <td style="text-align: center;"><?= $no++; ?></td>
                <td><strong><?= htmlspecialchars($l['nama']); ?></strong></td>
                <td><?= htmlspecialchars($l['nama_barang']); ?> (<?= $l['jumlah']; ?> Unit)</td>
                <td style="text-align: center;"><?= date('d/m/Y', strtotime($l['tanggal_pinjam'])); ?></td>
                <td style="text-align: center;"><?= date('d/m/Y', strtotime($l['tanggal_kembali'])); ?></td>
                <td style="text-align: center;">
                    <span class="badge-status" style="background: <?= $warna_status ?>;">
                        <?= strtoupper($l['status']); ?>
                    </span>
                </td>
                <td style="text-align: right; font-family: monospace; font-weight: bold;">
                    <?= ($l['denda'] > 0) ? 'Rp ' . number_format($l['denda'], 0, ',', '.') : '-'; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #94a3b8;">Data transaksi tidak ditemukan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="6" style="text-align: right; padding-right: 15px;">TOTAL SALDO DENDA MASUK :</td>
                <td style="text-align: right; font-family: monospace; font-size: 14px; background: #f1f5f9;">
                    Rp <?= number_format($total_denda_table, 0, ',', '.'); ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div style="float: right; text-align: center; width: 250px;">
            <p>Dicetak pada: <?= date('d/m/Y H:i'); ?></p>
            <p style="margin-bottom: 70px;">Administrator Sarpras,</p>
            <p><strong><u>( <?= strtoupper($_SESSION['nama']); ?> )</u></strong></p>
            <p style="font-size: 11px; margin-top: 5px;">ID Pegawai: <?= $_SESSION['id']; ?></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

</body>
</html>