<?php
session_start();
include '../config/database.php';
$kat = isset($_GET['kat']) ? (int)$_GET['kat'] : 1;
$barang = mysqli_query($conn, "SELECT * FROM barang WHERE kategori_id = '$kat' AND stok > 0");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pinjam Barang</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Agar radio button asli sembunyi, tapi card tetap bisa diklik */
        .mobil-card input[type="radio"] {
            display: none;
        }
        /* Efek saat card dipilih */
        .mobil-card:has(input:checked) {
            border: 3px solid var(--primary-light);
            background: #eff6ff;
            transform: scale(1.02);
        }
        /* Menampilkan kembali tag stok */
        .price-tag {
            display: block !important;
            background: var(--primary);
            color: white;
            padding: 5px 10px;
            border-radius: 8px;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body class="dashboard-page">
<div class="box wide">
    <h2>Pilih Barang</h2>
    <p>Klik pada gambar barang untuk memilih, lalu tentukan jumlah unit.</p>
    
    <form action="proses_pinjam.php" method="POST">
        <div class="mobil-grid">
            <?php while($b = mysqli_fetch_assoc($barang)): ?>
            <label class="mobil-card" onclick="setMaxStok(<?= $b['stok']; ?>)">
                <input type="radio" name="barang_id" value="<?= $b['id']; ?>" required>
                <img src="../assets/img/barang/<?= $b['gambar']; ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
                <h4><?= htmlspecialchars($b['nama_barang']); ?></h4>
                
                <div class="price-tag">
                    Stok: <?= $b['stok']; ?> <small>Unit</small>
                </div>
            </label>
            <?php endwhile; ?>
        </div>

        <div style="max-width: 400px; margin: 30px auto;">
            <div class="form-group">
                <label id="label_jumlah">Jumlah Unit</label>
                <input type="number" name="jumlah" id="jumlah_input" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label>Durasi (Hari)</label>
                <input type="number" name="lama_pinjam" min="1" value="1" required>
            </div>
            <button type="submit" name="pinjam" class="btn btn-primary" style="width: 100%;">🚀 Ajukan Sekarang</button>
            <a href="dashboard.php" class="btn btn-outline" style="width: 100%; margin-top: 10px; text-align: center; display: block;">Batal</a>
        </div>
    </form>
</div>

<script>
function setMaxStok(stok) {
    var input = document.getElementById('jumlah_input');
    var label = document.getElementById('label_jumlah');
    
    input.max = stok; 
    label.innerHTML = "Jumlah Unit (Maks: " + stok + ")";
    
    if (parseInt(input.value) > stok) {
        input.value = stok;
    }
}
</script>
</body>
</html>