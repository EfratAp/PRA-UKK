<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../auth/login.php"); exit;
}

$id = (int)$_GET['id']; // Casting ke integer untuk keamanan
$aksi = $_GET['aksi'];
$petugas_id = $_SESSION['id'];
$petugas_nama = $_SESSION['nama'];

$cek = mysqli_query($conn, "SELECT p.*, b.nama_barang FROM peminjaman p JOIN barang b ON p.barang_id = b.id WHERE p.id = $id");
$p = mysqli_fetch_assoc($cek);

if (!$p) { header("Location: peminjaman.php?msg=notfound"); exit; }

$barang_id = $p['barang_id'];
$jumlah = $p['jumlah'];
$nama_barang = $p['nama_barang'];

if ($aksi == 'setuju') {
    $stok_q = mysqli_query($conn, "SELECT stok FROM barang WHERE id = $barang_id");
    $s = mysqli_fetch_assoc($stok_q);

    if ($s['stok'] >= $jumlah) {
        mysqli_query($conn, "UPDATE peminjaman SET status = 'dipinjam' WHERE id = $id");
        mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id");
        
        // CATAT LOG AKTIVITAS
        $pesan_log = "Petugas $petugas_nama MENYETUJUI peminjaman $nama_barang ($jumlah unit)";
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('$petugas_id', '$pesan_log')");

        header("Location: peminjaman.php?status=sukses");
    } else {
        header("Location: peminjaman.php?status=stok_kurang");
    }
} else {
    mysqli_query($conn, "UPDATE peminjaman SET status = 'ditolak' WHERE id = $id");
    
    // CATAT LOG AKTIVITAS
    $pesan_log = "Petugas $petugas_nama MENOLAK peminjaman $nama_barang";
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('$petugas_id', '$pesan_log')");

    header("Location: peminjaman.php?status=ditolak");
}
?>