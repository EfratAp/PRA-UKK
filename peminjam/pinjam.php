<?php
session_start();
include '../config/database.php';

$kat = isset($_GET['kat']) ? $_GET['kat'] : 1; 

$barang = mysqli_query($conn, "SELECT * FROM barang WHERE kategori_id = '$kat' AND stok > 0");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Barang - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">

<div class="box wide">
    <h2>Form Peminjaman Barang</h2>
    <p>Klik pada kartu barang untuk memilih, kemudian tentukan jumlahnya.</p>

    <form action="proses_pinjam.php" method="POST">
        <div class="mobil-grid">
            <?php while($b = mysqli_fetch_assoc($barang)): ?>
            <label class="mobil-card">
                <input type="radio" name="barang_id" value="<?= $b['id']; ?>" data-harga="<?= $b['harga']; ?>" required>
                
                <img src="../assets/img/barang/<?= $b['gambar']; ?>" 
                    alt="<?= $b['nama_barang']; ?>" 
                    style="width: 100%; height: 180px; object-fit: contain; background: #fff; border-radius: 12px; margin-bottom: 15px;"
                    onerror="this.src='https://cdn-icons-png.flaticon.com/512/679/679821.png'">
                
                <h4><?= htmlspecialchars($b['nama_barang']); ?></h4>
                <p>Harga: <strong>Rp <?= number_format($b['harga'], 0, ',', '.'); ?></strong> /hari</p>
                <div class="price-tag"><?= $b['stok']; ?> <span style="font-size: 14px; font-weight: 500;">Unit tersedia</span></div>
            </label>
            <?php endwhile; ?>
        </div>

        <div style="max-width: 500px; margin: 40px auto; background: #f8fafc; padding: 30px; border-radius: 20px; border: 2px solid #e2e8f0;">
            <div class="form-group">
                <label>Jumlah yang akan dipinjam</label>
                <input type="number" name="jumlah" id="jumlah" min="1" value="1" required>
            </div>

            <div class="form-group">
                <label>Lama Pinjam (Hari)</label>
                <input type="number" name="lama_pinjam" id="lama_pinjam" min="1" value="1" required>
            </div>

            <div style="margin-top: 15px; padding: 15px; background: #e0f2fe; border-radius: 12px; color: #0369a1;">
                <strong>Estimasi Total: <span id="display_total">Rp 0</span></strong>
            </div>
            
            <button type="submit" name="pinjam" class="btn btn-primary" style="margin-top: 20px; width: 100%;">
                🚀 Ajukan Peminjaman Sekarang
            </button>
        </div>
    </form>

    <div style="margin-top: 20px;">
        <a href="dashboard.php" class="btn btn-outline">⬅ Kembali ke Dashboard</a>
    </div>
</div>

<script>
    const cards = document.querySelectorAll('.mobil-card');
    const displayTotal = document.getElementById('display_total');
    const inputJumlah = document.getElementById('jumlah');
    const inputLama = document.getElementById('lama_pinjam');

    function hitungOtomatis() {
        const selected = document.querySelector('input[name="barang_id"]:checked');
        if(selected) {
            const harga = selected.getAttribute('data-harga');
            const total = harga * inputJumlah.value * inputLama.value;
            displayTotal.innerText = "Rp " + total.toLocaleString('id-ID');
        }
    }

    cards.forEach(card => {
        card.addEventListener('click', () => {
            cards.forEach(c => {
                c.style.borderColor = '#e2e8f0';
                c.style.backgroundColor = '#ffffff';
            });
            card.style.borderColor = '#3b82f6';
            card.style.backgroundColor = '#f0f7ff';
            hitungOtomatis();
        });
    });

    inputJumlah.addEventListener('input', hitungOtomatis);
    inputLama.addEventListener('input', hitungOtomatis);
</script>

</body>
</html>