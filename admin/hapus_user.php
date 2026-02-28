<?php
session_start();
include '../config/database.php';

// 1. Proteksi: Hanya Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 2. Cegah Hapus Diri Sendiri
    if ($id == $_SESSION['id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='kelola_user.php';</script>";
        exit;
    }

    // 3. Ambil Nama User untuk Keperluan Log
    $user_query = mysqli_query($conn, "SELECT nama FROM users WHERE id = '$id'");
    $u = mysqli_fetch_assoc($user_query);
    $nama_dihapus = $u['nama'] ?? "User Unknown";

    // 4. Eksekusi Hapus
    $delete = mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");

    if ($delete) {
        // 5. Catat ke Log Aktivitas
        $pesan_log = "Admin {$_SESSION['nama']} menghapus akun user: $nama_dihapus (ID: $id)";
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");

        echo "<script>alert('User $nama_dihapus berhasil dihapus!'); window.location='kelola_user.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus: " . mysqli_error($conn) . "'); window.location='kelola_user.php';</script>";
    }
} else {
    header("Location: kelola_user.php");
}
?>