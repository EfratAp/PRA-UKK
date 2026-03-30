<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'peminjam') { 
    header("Location: ../auth/login.php"); exit; 
}

if (isset($_POST['pinjam'])) {
    $user_id = $_SESSION['id'];
    $user_nama = $_SESSION['nama'];
    $barang_id = mysqli_real_escape_string($conn, $_POST['barang_id']);
    $jumlah = (int)$_POST['jumlah'];
    $lama_pinjam = (int)$_POST['lama_pinjam'];

    $query_b = mysqli_query($conn, "SELECT nama_barang, stok FROM barang WHERE id = '$barang_id'");
    $b = mysqli_fetch_assoc($query_b);

    if ($jumlah > $b['stok']) {
        echo "<script>alert('Stok tidak cukup!'); window.location='pinjam.php';</script>"; exit;
    }

    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime("+$lama_pinjam days"));
    
    // PERBAIKAN: Status diubah menjadi 'menunggu_pinjam' agar sinkron dengan database
    $sql = "INSERT INTO peminjaman (user_id, barang_id, jumlah, tanggal_pinjam, tanggal_kembali, lama_pinjam, status) 
            VALUES ('$user_id', '$barang_id', '$jumlah', '$tgl_pinjam', '$tgl_kembali', '$lama_pinjam', 'menunggu_pinjam')";
            
    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn); 
        $pesan_log = "Peminjam $user_nama mengajukan pinjam {$b['nama_barang']} ($jumlah unit)";
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('$user_id', '$pesan_log')");
        
        echo "<script>alert('Berhasil diajukan!'); window.location='../struk.php?id=$last_id';</script>";
    } else {
        die("Error Database: " . mysqli_error($conn));
    }
}
?>