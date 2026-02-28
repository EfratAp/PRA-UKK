<?php
session_start();
include '../config/database.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); 
    exit;
}

// Ambil data transaksi yang sudah selesai
$data = mysqli_query($conn, "SELECT p.*, u.nama, b.nama_barang 
                             FROM peminjaman p 
                             JOIN users u ON p.user_id = u.id 
                             JOIN barang b ON p.barang_id = b.id 
                             WHERE p.status = 'selesai'
                             ORDER BY p.tanggal_kembali DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Sarpras - Administrator</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .header h1 { margin: 0; text-transform: uppercase; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 12px 8px; text-align: left; font-size: 13px; }
        th { background-color: #f2f2f2; text-transform: uppercase; }
        
        .total-row { background-color: #f9f9f9; font-weight: bold; }
        .footer-sign { margin-top: 50px; float: right; text-align: center; width: 250px; }
        
        .btn-print { 
            background: #2563eb; color: white; border: none; padding: 10px 20px; 
            border-radius: 5px; cursor: pointer; font-weight: bold; margin-bottom: 20px;
        }

        @media print { 
            .btn-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Peminjaman & Pengembalian Barang</h1>
        <p>Sistem Informasi Sarana dan Prasarana (SARPRAS)</p>
        <p style="font-size: 12px;">Dicetak pada: <?= date('d F Y, H:i'); ?> oleh <?= htmlspecialchars($_SESSION['nama']); ?></p>
    </div>

    <button onclick="window.print()" class="btn-print">🖨️ Cetak Laporan / Simpan PDF</button>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Nama Barang</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th style="text-align: right;">Denda (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $total_denda = 0;
            while($d = mysqli_fetch_assoc($data)): 
                $total_denda += $d['denda'];
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($d['nama']); ?></td>
                <td><?= htmlspecialchars($d['nama_barang']); ?></td>
                <td><?= date('d/m/Y', strtotime($d['tanggal_pinjam'])); ?></td>
                <td><?= date('d/m/Y', strtotime($d['tanggal_kembali'])); ?></td>
                <td style="text-align: right;"><?= number_format($d['denda'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            
            <?php if(mysqli_num_rows($data) == 0): ?>
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data transaksi selesai.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">TOTAL PENDAPATAN DENDA :</td>
                <td style="text-align: right;">Rp <?= number_format($total_denda, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-sign">
        <p>Mengetahui,</p>
        <p>Administrator Sarpras</p>
        <br><br><br>
        <p><b>( <?= htmlspecialchars($_SESSION['nama']); ?> )</b></p>
        <hr style="border: 0; border-top: 1px solid #000; width: 80%;">
    </div>

</body>
</html>