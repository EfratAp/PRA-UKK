<?php
session_start();
include '../config/database.php';

// Proteksi Halaman: Pastikan hanya petugas yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

// AMBIL DATA SAJA (Tanpa proses INSERT/UPDATE/DELETE)
$data = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Sarana Prasarana - Petugas</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .img-preview {
            width: 50px; 
            height: 50px; 
            object-fit: cover; 
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        /* Menghapus warna biru/hijau pada teks kategori agar lebih netral untuk petugas */
        .kat-elektronik { color: #3b82f6; font-weight: 500; }
        .kat-nonelektronik { color: #64748b; font-weight: 500; }
    </style>
</head>
<body class="dashboard-page">

    <div class="box wide">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="margin:0;">📦 Daftar Barang</h2>
            <p style="color: #64748b; margin-top: 5px;">Data inventaris sarana prasarana saat ini.</p>
            
            <div style="margin-top: 15px;">
                <a href="dashboard.php" class="btn btn-outline" style="padding: 8px 20px;">Kembali ke Dashboard</a>
            </div>
        </div>

        <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Gambar</th>
                    <th style="text-align: center;">Nama Barang</th> <th style="text-align: center;">Kategori</th>
                    <th style="text-align: center;">Status Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td style="text-align: center;">
                        <img src="../assets/img/barang/<?= $b['gambar']; ?>" class="img-preview" onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
                    </td>
                    <td style="text-align: center;"> <strong style="font-size: 16px;"><?= htmlspecialchars($b['nama_barang']); ?></strong>
                        </td>
                    <td style="text-align: center;">
                        <?php if ($b['kategori_id'] == 1): ?>
                            <span class="kat-elektronik">💻 Elektronik</span>
                        <?php else: ?>
                            <span class="kat-nonelektronik">🪑 Non-Elektronik</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if($b['stok'] > 0): ?>
                            <span class="badge badge-disetujui" style="background-color: #dcfce7; color: #166534; border: 1px solid #166534;">TERSEDIA: <?= $b['stok']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-menunggu" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #991b1b;">HABIS</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>