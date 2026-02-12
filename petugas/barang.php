<?php
session_start();
include '../config/database.php';

// Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

// Proses Tambah Barang
if (isset($_POST['tambah'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $stok     = mysqli_real_escape_string($conn, $_POST['stok']);
    $harga    = mysqli_real_escape_string($conn, $_POST['harga']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori_id']);

    $gambar   = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $nama_baru = time() . "_" . $gambar; 

    if (move_uploaded_file($tmp_name, "../assets/img/barang/" . $nama_baru)) {
        $query = "INSERT INTO barang (nama_barang, stok, harga, gambar, kategori_id) 
                  VALUES ('$nama', '$stok', '$harga', '$nama_baru', '$kategori')";
        
        if (mysqli_query($conn, $query)) {
            $pesan_log = "Menambah barang baru: $nama";
            mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");
            
            header("Location: barang.php?status=success");
            exit;
        }
    } else {
        echo "<script>alert('Gagal upload gambar!');</script>";
    }
}

$data = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* CSS Tambahan agar Form Tidak Terjepit */
        .form-container {
            background: #f8fafc; 
            padding: 25px; 
            border-radius: 16px; 
            border: 1px solid #e2e8f0;
            margin-bottom: 40px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Responsif otomatis */
            gap: 15px;
            align-items: end;
        }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; } /* Stack ke bawah di HP */
        }
        .img-preview {
            width: 50px; 
            height: 50px; 
            object-fit: cover; 
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body class="dashboard-page">

<div class="box wide">
    <h2>Manajemen Barang</h2>
    <p>Tambah dan kelola daftar sarana prasarana inventaris.</p>

    <div class="form-container">
        <h4 style="margin-bottom: 20px; color: #1e3a8a; display: flex; align-items: center; gap: 10px;">
            <span>➕</span> Tambah Barang Baru
        </h4>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Nama Barang</label>
                    <input type="text" name="nama" placeholder="Proyektor" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Stok</label>
                    <input type="number" name="stok" placeholder="0" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" placeholder="10000" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Kategori</label>
                    <select name="kategori_id" required>
                        <option value="1">Elektronik</option>
                        <option value="2">Non-Elektronik</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Gambar Produk</label>
                    <input type="file" name="gambar" accept="image/*" required style="padding: 8px; background: white;">
                </div>
                <button type="submit" name="tambah" class="btn btn-primary" style="height: 48px; width: 100%;">Simpan</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Gambar</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th style="text-align: center;">Harga/Hari</th>
                    <th style="text-align: center;">Stok</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td style="text-align: center;">
                        <img src="../assets/img/barang/<?= $b['gambar']; ?>" class="img-preview" onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
                    </td>
                    <td><strong><?= htmlspecialchars($b['nama_barang']); ?></strong></td>
                    <td>
                        <?= ($b['kategori_id'] == 1) ? '<span style="color:#3b82f6;">💻 Elektronik</span>' : '<span style="color:#10b981;">🪑 Non-Elektronik</span>'; ?>
                    </td>
                    <td style="text-align: center; font-weight: bold;">Rp <?= number_format($b['harga'], 0, ',', '.'); ?></td>
                    <td style="text-align: center;">
                        <span class="badge badge-disetujui"><?= $b['stok']; ?> Unit</span>
                    </td>
                    <td style="text-align: center;">
                        <a href="edit_barang.php?id=<?= $b['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                    </td>
                </tr>
                <?php } ?>
                
                <?php if(mysqli_num_rows($data) == 0): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">Belum ada data barang. Silakan tambah barang di atas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <a href="dashboard.php" class="btn btn-outline">⬅ Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>