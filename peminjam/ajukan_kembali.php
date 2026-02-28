<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') { 
    header("Location: ../auth/login.php"); exit; 
}

if (!isset($_GET['id'])) { 
    header("Location: riwayat.php"); exit; 
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['id'];

// Cek apakah barang memang sedang 'dipinjam'
$cek = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id = $id AND user_id = $user_id AND status = 'dipinjam'");

if (mysqli_num_rows($cek) > 0) {
    $sql = "UPDATE peminjaman SET status = 'menunggu_kembali' WHERE id = $id";
    if(mysqli_query($conn, $sql)) {
        header("Location: ../struk.php?id=$id&pesan=proses_kembali");
        exit;
    }
}
header("Location: riwayat.php?pesan=gagal");
exit;
?>