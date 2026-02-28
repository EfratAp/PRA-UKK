<?php
session_start();
include 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php"); exit;
}

$id = (int) $_GET['id'];

$query = "SELECT p.*, u.nama, b.nama_barang, b.gambar, b.harga_asli
          FROM peminjaman p
          JOIN users u ON p.user_id = u.id
          JOIN barang b ON p.barang_id = b.id
          WHERE p.id = $id";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data peminjaman tidak ditemukan.");
}

// Logika Jatuh Tempo
if (!empty($data['tanggal_pinjam']) && $data['tanggal_pinjam'] != '0000-00-00') {
    $tgl_pinjam_obj = new DateTime($data['tanggal_pinjam']);
    $durasi = (int)$data['lama_pinjam'];
    $tgl_pinjam_obj->modify("+$durasi days");
    $jatuh_tempo = $tgl_pinjam_obj->format('d F Y');
} else {
    $jatuh_tempo = "Menunggu Persetujuan";
}

// Tentukan arah kembali berdasarkan Role (Agar link di atas tetap jalan)
$back_link = "index.php";
if(isset($_SESSION['role'])) {
    $back_link = ($_SESSION['role'] == 'peminjam') ? "peminjam/dashboard.php" : "petugas/peminjaman.php";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Peminjaman #<?= $data['id']; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <style>
        body { background: #f1f5f9; font-family: 'Inter', sans-serif; margin: 0; padding: 20px; }
        .card { background: white; max-width: 450px; margin: auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        .text-center { text-align: center; }
        .header h2 { margin: 0; color: #0f172a; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .info-table td { padding: 12px 0; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; }
        .value { text-align: right; font-weight: 600; }
        .deadline-box { margin-top: 20px; padding: 15px; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 8px; text-align: center; }
        .deadline-date { display: block; font-size: 18px; color: #9a3412; font-weight: 700; }
        .qr-section { margin-top: 25px; padding-top: 20px; border-top: 1px dashed #e2e8f0; }
        #qrcode { display: flex; justify-content: center; margin-bottom: 10px; }
        .btn-group { margin-top: 25px; display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; border-radius: 8px; font-weight: 600; text-decoration: none; text-align: center; font-size: 14px; border: none; cursor: pointer; }
        .btn-print { background: #0f172a; color: white; }
        .btn-status { background: #f59e0b; color: white; cursor: default; }
        .btn-return { background: #10b981; color: white; }
        .btn-back-top { margin-bottom: 15px; display: inline-block; text-decoration: none; color: #64748b; font-size: 14px; font-weight: 600; }
        @media print { .btn-group, .btn-back-top { display: none; } }
    </style>
</head>
<body>

<div style="max-width: 450px; margin: auto;">
    <a href="<?= $back_link; ?>" class="btn-back-top">⬅ Kembali ke Dashboard</a>
    
    <div class="card">
        <div class="header text-center">
            <h2>STRUK SARPRAS</h2>
            <p>SMK Sarpras Indonesia</p>
            <span style="font-size: 10px; padding: 4px 10px; background: #fef3c7; color: #92400e; border-radius: 20px; text-transform: uppercase; font-weight: 700;">
                Status: <?= str_replace('_', ' ', $data['status']); ?>
            </span>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">ID Transaksi</td>
                <td class="value">#INV-<?= str_pad($data['id'], 4, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td class="label">Peminjam</td>
                <td class="value"><?= htmlspecialchars($data['nama']); ?></td>
            </tr>
            <tr>
                <td class="label">Barang</td>
                <td class="value"><?= htmlspecialchars($data['nama_barang']); ?> (<?= $data['jumlah']; ?> unit)</td>
            </tr>
        </table>

        <div class="deadline-box">
            <small style="color: #c2410c; font-weight: 600; text-transform: uppercase; font-size: 10px;">Wajib Kembali Sebelum</small>
            <span class="deadline-date"><?= $jatuh_tempo; ?></span>
        </div>

        <div class="qr-section text-center">
            <div id="qrcode"></div>
            <p style="color: #94a3b8; font-size: 11px;">Tunjukkan QR ini ke petugas</p>
        </div>

        <div class="btn-group">
            <button class="btn btn-print" onclick="window.print()">Cetak Struk</button>
            
            <?php if($data['status'] == 'dipinjam'): ?>
                <a href="peminjam/ajukan_kembali.php?id=<?= $id; ?>" class="btn btn-return" onclick="return confirm('Ajukan pengembalian?')">Kembalikan</a>
            
            <?php elseif($data['status'] == 'menunggu_pinjam'): ?>
                <button class="btn btn-status" disabled>⏳ Menunggu Approval</button>
            
            <?php elseif($data['status'] == 'menunggu_kembali'): ?>
                <button class="btn" style="background: #334155; color: white;" disabled>🔎 Verifikasi...</button>
            
            <?php elseif($data['status'] == 'selesai'): ?>
                <button class="btn" style="background: #10b981; color: white;" disabled>✅ Selesai</button>
            <?php endif; ?>
            
            </div>
    </div>
</div>

<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "VALID-TRX-<?= $data['id']; ?>",
        width: 120, height: 120
    });
</script>
</body>
</html>