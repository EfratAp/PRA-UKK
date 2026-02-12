<?php

include 'config/database.php';

$id = (int) $_GET['id'];



$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT p.*, u.nama, b.nama_barang
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN barang b ON p.barang_id = b.id
    WHERE p.id = $id
"));

?>



<div class="box">

<h2>Struk Peminjaman Barang</h2>



<p>Nama: <b><?= $d['nama']; ?></b></p>

<p>Barang: <b><?= $d['nama_barang']; ?></b></p>

<p>Lama: <?= $d['lama_pinjam']; ?> hari</p>

<p>Total: Rp <?= number_format($d['total_harga']); ?></p>

<p>Status: <b><?= strtoupper($d['status']); ?></b></p>



<button onclick="window.print()">Cetak</button>

</div>