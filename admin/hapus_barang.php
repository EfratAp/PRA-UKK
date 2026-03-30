<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. CEK: Apakah barang sedang dipinjam?
$cek_pinjam = mysqli_query($conn, "SELECT id FROM peminjaman WHERE barang_id = '$id' AND status = 'dipinjam'");
if (mysqli_num_rows($cek_pinjam) > 0) {
    echo "<script>alert('Gagal menghapus! Barang ini masih dalam status DIPINJAM oleh user.'); window.location='barang.php';</script>";
    exit;
}

// 2. Ambil data gambar untuk dihapus dari folder
$query = mysqli_query($conn, "SELECT nama_barang, gambar FROM barang WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    $nama_barang = $data['nama_barang'];
    $foto = $data['gambar'];

    if(!empty($foto) && file_exists("../assets/img/barang/" . $foto)) {
        unlink("../assets/img/barang/" . $foto);
    }

    // 3. Hapus dari database
    $delete = mysqli_query($conn, "DELETE FROM barang WHERE id = '$id'");

    if ($delete) {
        $pesan_log = "Admin {$_SESSION['nama']} menghapus barang: $nama_barang (ID: $id)";
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");
        header("Location: barang.php?status=hapus_sukses");
    }
} else {
    header("Location: barang.php");
}