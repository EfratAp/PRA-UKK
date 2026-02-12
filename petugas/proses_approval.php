<?php
session_start();
include '../config/database.php';

// Proteksi: Hanya petugas yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data dari URL
$id = $_GET['id'];
$aksi = $_GET['aksi'];

// 1. Ambil informasi detail pinjaman untuk pencatatan Log
$query_p = mysqli_query($conn, "SELECT p.*, u.nama as nama_user, b.nama_barang 
                                FROM peminjaman p 
                                JOIN users u ON p.user_id = u.id 
                                JOIN barang b ON p.barang_id = b.id 
                                WHERE p.id = '$id'");
$data = mysqli_fetch_assoc($query_p);

if ($aksi == 'setuju') {
    // JIKA DISETUJUI: Status berubah jadi 'dipinjam'
    $status_baru = 'dipinjam';
    $pesan_log = "Petugas {$_SESSION['nama']} MENYETUJUI peminjaman {$data['nama_barang']} oleh {$data['nama_user']}.";
} else {
    // JIKA DITOLAK: Status berubah jadi 'ditolak' dan STOK DIKEMBALIKAN
    $status_baru = 'ditolak';
    $jumlah_pinjam = $data['jumlah'];
    $barang_id = $data['barang_id'];
    
    // Kembalikan stok barang karena tidak jadi dipinjam
    mysqli_query($conn, "UPDATE barang SET stok = stok + $jumlah_pinjam WHERE id = '$barang_id'");
    
    $pesan_log = "Petugas {$_SESSION['nama']} MENOLAK peminjaman {$data['nama_barang']} oleh {$data['nama_user']}.";
}

// 2. Update status transaksi di tabel peminjaman
$update = mysqli_query($conn, "UPDATE peminjaman SET status = '$status_baru' WHERE id = '$id'");

if ($update) {
    // 3. Catat ke Log Aktivitas (Audit Trail)
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");

    // Catat log juga untuk sisi peminjam agar dia tahu statusnya berubah
    $pesan_untuk_user = "Permintaan pinjaman {$data['nama_barang']} Anda telah " . ($aksi == 'setuju' ? "DISETUJUI" : "DITOLAK") . ".";
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$data['user_id']}', '$pesan_untuk_user')");

    // Redirect kembali ke halaman peminjaman dengan status sukses
    header("Location: peminjaman.php?status=sukses");
} else {
    echo "Gagal memperbarui data: " . mysqli_error($conn);
}
?>