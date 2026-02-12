<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$query = mysqli_query($conn, "SELECT p.*, u.nama, b.nama_barang 
                              FROM peminjaman p 
                              JOIN users u ON p.user_id = u.id 
                              JOIN barang b ON p.barang_id = b.id 
                              ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    
    <style>
        /* CSS Normal (Tampilan Layar) */
        .box { max-width: 1000px; margin: 20px auto; }
        
        @media print {
            /* 1. Sembunyikan elemen yang tidak perlu */
            .no-print { display: none !important; }

            /* 2. Atur Body agar bersih */
            body { 
                background: white !important; 
                margin: 0; 
                padding: 0;
                font-family: 'Times New Roman', serif; /* Font standar laporan */
            }

            /* 3. Atur Box agar tidak ada margin/shadow */
            .box { 
                box-shadow: none !important; 
                border: none !important; 
                width: 100% !important; 
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* 4. PERBAIKAN TABEL AGAR TIDAK KELUAR JALUR */
            table { 
                width: 100% !important; /* Paksa lebar 100% kertas */
                border-collapse: collapse !important;
                font-size: 12px !important; /* Kecilkan font agar muat */
                table-layout: auto !important;
            }

            th, td { 
                border: 1px solid black !important; /* Garis hitam tegas */
                padding: 8px !important; 
                word-wrap: break-word !important; /* Bungkus teks jika kepanjangan */
            }

            /* 5. Paksa halaman Landscape jika masih kepanjangan (Opsional) */
            @page {
                size: A4 portrait;
                margin: 1cm;
            }

            .signature { display: block !important; margin-top: 30px; }
        }

        .signature { display: none; }
    </style>
</head>
<body class="dashboard-page">

<div class="box wide">
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #f1f5f9; padding: 15px; border-radius: 10px;">
        <h2 style="margin:0;">📄 Menu Laporan</h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak / PDF</button>
            <a href="dashboard.php" class="btn btn-outline">⬅ Kembali</a>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin:0; text-transform: uppercase;">Laporan Peminjaman Sarana Prasarana</h2>
        <p style="margin:5px 0;">Dicetak pada: <?= date('d/m/Y H:i'); ?></p>
        <div style="border-bottom: 2px solid black; margin-top: 10px;"></div>
    </div>

    <table border="1">
        <thead>
            <tr style="background: #eee !important; -webkit-print-color-adjust: exact;">
                <th width="5%">NO</th>
                <th width="20%">PEMINJAM</th>
                <th width="25%">NAMA BARANG</th>
                <th width="12%">PINJAM</th>
                <th width="12%">KEMBALI</th>
                <th width="13%">STATUS</th>
                <th width="13%">DENDA</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; $total_denda = 0;
            while($l = mysqli_fetch_assoc($query)): 
            $total_denda += $l['denda'];
            ?>
            <tr>
                <td style="text-align: center;"><?= $no++; ?></td>
                <td><?= htmlspecialchars($l['nama']); ?></td>
                <td><?= htmlspecialchars($l['nama_barang']); ?></td>
                <td style="text-align: center;"><?= date('d/m/Y', strtotime($l['tanggal_pinjam'])); ?></td>
                <td style="text-align: center;"><?= date('d/m/Y', strtotime($l['tanggal_kembali'])); ?></td>
                <td style="text-align: center;"><?= strtoupper($l['status']); ?></td>
                <td style="text-align: right;">Rp <?= number_format($l['denda'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee !important; -webkit-print-color-adjust: exact;">
                <td colspan="6" style="text-align: right;">TOTAL KAS DENDA :</td>
                <td style="text-align: right;">Rp <?= number_format($total_denda, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div style="float: right; text-align: center; width: 250px;">
            <p>Medan, <?= date('d F Y'); ?></p>
            <p>Mengetahui, Admin Sarpras</p>
            <br><br><br>
            <p><strong>( _________________________ )</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

</body>
</html>