<?php
session_start();
include '../config/database.php';

// Proteksi akses role
if ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); exit;
}

$id = $_GET['id'];
$kondisi = $_GET['kondisi'];

// 1. Ambil data transaksi peminjaman untuk mendapatkan tanggal kembali dan nama barang
$query = mysqli_query($conn, "SELECT p.*, b.nama_barang, u.nama as nama_peminjam 
                              FROM peminjaman p 
                              JOIN barang b ON p.barang_id = b.id 
                              JOIN users u ON p.user_id = u.id 
                              WHERE p.id = '$id'");
$data = mysqli_fetch_assoc($query);

// --- LOGIKA HITUNG DENDA TERLAMBAT PER HARI ---
$tgl_kembali = new DateTime($data['tanggal_kembali']);
$tgl_sekarang = new DateTime(date('Y-m-d')); // Mengambil tanggal hari ini (2026-02-13)
$denda_terlambat = 0;
$selisih_hari = 0;
$pesan_terlambat = "";

// Cek jika hari ini sudah melewati batas tanggal kembali
if ($tgl_sekarang > $tgl_kembali) {
    $diff = $tgl_sekarang->diff($tgl_kembali);
    $selisih_hari = $diff->days;
    
    $tarif_denda = 5000; // Tarif denda keterlambatan per hari
    $denda_terlambat = $selisih_hari * $tarif_denda;
    $pesan_terlambat = " (Terlambat $selisih_hari hari, Denda Keterlambatan Rp " . number_format($denda_terlambat, 0, ',', '.') . ")";
}

// 2. Logika Denda Kerusakan
$denda_rusak = ($kondisi == 'rusak') ? 50000 : 0;
$total_denda_final = $denda_terlambat + $denda_rusak;
$status_kondisi = ($kondisi == 'rusak') ? "RUSAK (Denda Fisik Rp 50.000)" : "BAIK";

// 3. Update Status Peminjaman ke Database
$update = mysqli_query($conn, "UPDATE peminjaman SET 
    status = 'selesai', 
    denda = '$total_denda_final' 
    WHERE id = '$id'");

if ($update) {
    // 4. Catat Log Audit Trail (Agar muncul di History)
    $nama_petugas = $_SESSION['nama'];
    $pesan_log = "Petugas $nama_petugas memverifikasi kembali {$data['nama_barang']} ({$data['nama_peminjam']}). Kondisi: $status_kondisi $pesan_terlambat.";
    
    // Log untuk Admin/Petugas
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");
    
    // Log untuk Peminjam (User)
    $pesan_user = "Barang {$data['nama_barang']} sudah diterima petugas. Kondisi: $status_kondisi $pesan_terlambat.";
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$data['user_id']}', '$pesan_user')");

    // Redirect kembali ke halaman pengembalian dengan notifikasi sukses
    header("Location: pengembalian.php?status=sukses");
    exit();
} else {
    echo "Gagal memproses data: " . mysqli_error($conn);
}
?>