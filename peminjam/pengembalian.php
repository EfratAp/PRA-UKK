<?php

session_start();

include '../config/database.php';



// =======================

// CEK ROLE PEMINJAM

// =======================

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {

    header("Location: ../auth/login.php");

    exit;

}



// =======================

// AMBIL DATA PEMINJAMAN YANG SEDANG DIPINJAM

// =======================

$data = mysqli_query($conn, "

    SELECT p.*, b.nama_barang

    FROM peminjaman p

    JOIN barang b ON p.barang_id = b.id

    WHERE p.user_id = {$_SESSION['id']}

      AND p.status = 'dipinjam'

");

?>



<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Pengembalian Barang</title>

    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>



<div class="box">

<h2>Pengembalian Barang Sarpras</h2>



<table>

<tr>

    <th>Barang</th>

    <th>Aksi</th>

</tr>



<?php while ($p = mysqli_fetch_assoc($data)) { ?>

<tr>

    <td><?= $p['nama_barang']; ?></td>

    <td>

        <a class="btn"

           href="ajukan_kembali.php?id=<?= $p['id']; ?>"

           onclick="return confirm('Ajukan pengembalian barang ini?')">

           Ajukan Pengembalian

        </a>

    </td>

</tr>

<?php } ?>



</table>



<a href="dashboard.php">⬅ Kembali</a>

</div>



</body>

</html>

