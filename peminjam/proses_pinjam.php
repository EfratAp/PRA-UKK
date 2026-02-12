<?php
session_start();
include '../config/database.php';

// Pastikan hanya peminjam yang bisa memproses
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'peminjam') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['pinjam'])) {
    $user_id     = $_SESSION['id'];
    $user_nama   = $_SESSION['nama']; // Diambil untuk pesan log
    $barang_id   = $_POST['barang_id'];
    $jumlah      = (int)$_POST['jumlah'];
    $lama_pinjam = (int)$_POST['lama_pinjam'];

    // 1. Ambil data barang untuk cek stok, harga, dan nama barang (untuk log)
    $query_b = mysqli_query($conn, "SELECT nama_barang, harga, stok FROM barang WHERE id = '$barang_id'");
    $b = mysqli_fetch_assoc($query_b);

    // Cek apakah stok mencukupi
    if ($jumlah > $b['stok']) {
        echo "<script>alert('Stok tidak cukup! Tersisa: {$b['stok']}'); window.location='pinjam.php';</script>";
        exit;
    }

    // 2. Hitung total harga dan tanggal
    $total_harga     = $b['harga'] * $jumlah * $lama_pinjam;
    $tanggal_pinjam  = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime("+$lama_pinjam days"));
    $status          = 'menunggu_pinjam'; 

    // 3. Simpan data ke tabel peminjaman
    $sql = "INSERT INTO peminjaman (user_id, barang_id, jumlah, tanggal_pinjam, tanggal_kembali, status, lama_pinjam, total_harga) 
            VALUES ('$user_id', '$barang_id', '$jumlah', '$tanggal_pinjam', '$tanggal_kembali', '$status', '$lama_pinjam', '$total_harga')";

    if (mysqli_query($conn, $sql)) {
        // 4. Kurangi stok barang
        mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id = '$barang_id'");

        // 5. CATAT HISTORY DETAIL (Audit Trail)
        // Mencatat aktivitas agar muncul di log admin dan log peminjam sendiri
        $nama_brg  = $b['nama_barang'];
        $pesan_log = "Peminjam $user_nama mengajukan pinjaman: $nama_brg ($jumlah Unit) selama $lama_pinjam hari.";
        
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('$user_id', '$pesan_log')");

        echo "<script>alert('Peminjaman $nama_brg Berhasil Diajukan!'); window.location='riwayat.php';</script>";
    } else {
        echo "Error Database: " . mysqli_error($conn);
    }
} else {
    header("Location: pinjam.php");
    exit;
}
?>