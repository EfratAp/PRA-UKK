<?php
session_start();
include 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

$query = "SELECT p.*, u.nama, b.nama_barang, b.harga
          FROM peminjaman p
          JOIN users u ON p.user_id = u.id
          JOIN barang b ON p.barang_id = b.id
          WHERE p.id = $id";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data peminjaman tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #<?= $data['id']; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <style>
        body { background: #f3f4f6; font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; }
        .card { 
            background: white; 
            max-width: 500px; 
            margin: auto; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .text-center { text-align: center; }
        .header h2 { margin: 0; color: #1e3a8a; letter-spacing: 1px; }
        .header p { margin: 5px 0 20px; color: #666; font-size: 14px; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .info-table td { padding: 8px 0; font-size: 15px; border-bottom: 1px solid #f0f0f0; }
        .label { color: #777; }
        .value { text-align: right; font-weight: bold; color: #333; }
        
        .total-box { 
            margin-top: 20px; 
            padding: 15px; 
            background: #f8fafc; 
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-price { font-size: 20px; color: #1e3a8a; font-weight: 800; }
        
        .status-badge {
            display: block;
            text-align: center;
            margin: 20px 0;
            padding: 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }
        .status-dipinjam { background: #dcfce7; color: #166534; }
        .status-menunggu_pinjam { background: #fef3c7; color: #92400e; }

        .qr-section { margin-top: 25px; border-top: 2px dashed #eee; padding-top: 20px; }
        #qrcode { display: flex; justify-content: center; margin-bottom: 10px; }
        
        .btn-group { margin-top: 25px; display: flex; gap: 10px; }
        .btn { 
            flex: 1; padding: 12px; border: none; border-radius: 6px; 
            cursor: pointer; font-weight: bold; text-decoration: none; text-align: center;
        }
        .btn-print { background: #2563eb; color: white; }
        .btn-back { background: #9ca3af; color: white; }

        @media print {
            body { background: white; padding: 0; }
            .card { box-shadow: none; border: 1px solid #eee; max-width: 100%; }
            .btn-group { display: none; }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="header text-center">
        <h2>STRUK SARPRAS</h2>
        <p>Bukti Resmi Peminjaman Barang</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">No. Transaksi</td>
            <td class="value">#TRX-00<?= $data['id']; ?></td>
        </tr>
        <tr>
            <td class="label">Nama Peminjam</td>
            <td class="value"><?= $data['nama']; ?></td>
        </tr>
        <tr>
            <td class="label">Nama Barang</td>
            <td class="value"><?= $data['nama_barang']; ?></td>
        </tr>
        <tr>
            <td class="label">Tanggal Pinjam</td>
            <td class="value"><?= date('d F Y', strtotime($data['tanggal_pinjam'])); ?></td>
        </tr>
        <tr>
            <td class="label">Durasi Pinjam</td>
            <td class="value"><?= $data['lama_pinjam']; ?> Hari</td>
        </tr>
    </table>

    <div class="total-box">
        <span style="font-weight: bold; color: #64748b;">Total Bayar</span>
        <span class="total-price">Rp <?= number_format($data['total_harga']); ?></span>
    </div>

    <div class="status-badge status-<?= $data['status']; ?>">
        Status: <?= str_replace('_', ' ', strtoupper($data['status'])); ?>
    </div>

    <div class="qr-section text-center">
        <div id="qrcode"></div>
        <small style="color: #999;">Scan QR untuk validasi petugas</small>
    </div>

    <div class="btn-group">
        <button class="btn btn-print" onclick="window.print()">Cetak Struk</button>
        <a href="javascript:history.back()" class="btn btn-back">Kembali</a>
    </div>
</div>

<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "VALID: ID <?= $data['id']; ?> - <?= $data['nama_barang']; ?>",
        width: 100,
        height: 100
    });
</script>

</body>
</html>