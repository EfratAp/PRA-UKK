<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}
$id = (int) $_GET['id'];
$p = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM peminjaman WHERE id=$id")
);

mysqli_query($conn, "
    UPDATE peminjaman
    SET status='selesai'
    WHERE id=$id
");
mysqli_query($conn, "
    UPDATE barang
    SET stok = stok + 1
    WHERE id = {$p['barang_id']}
");
header("Location: pengembalian.php");
exit;