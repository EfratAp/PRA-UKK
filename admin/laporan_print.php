<?php
session_start();
include '../config/database.php';
if($_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }

$data = mysqli_query($conn, "SELECT p.*, u.nama, b.nama_barang 
                             FROM peminjaman p 
                             JOIN users u ON p.user_id = u.id 
                             JOIN barang b ON p.barang_id = b.id 
                             WHERE p.status = 'selesai'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>CETAK LAPORAN SARPRAS</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .header { text-align: center; margin-bottom: 30px; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMINJAMAN SARPRAS</h1>
        <p>Dicetak pada: <?= date('d-m-Y H:i:s'); ?></p>
        <button onclick="window.print()" class="btn-print">KLIK UNTUK PRINT / SAVE PDF</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Barang</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Total Bayar</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($d = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $d['nama']; ?></td>
                <td><?= $d['nama_barang']; ?></td>
                <td><?= $d['tanggal_pinjam']; ?></td>
                <td><?= $d['tanggal_kembali']; ?></td>
                <td>Rp <?= number_format($d['total_harga']); ?></td>
                <td>Rp <?= number_format($d['denda']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>