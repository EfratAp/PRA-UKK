<?php
session_start();
include '../config/database.php';

// 1. Proteksi
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../auth/login.php"); 
    exit;
}

if (isset($_GET['id']) && isset($_GET['kondisi'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_GET['id']);
    $kondisi   = mysqli_real_escape_string($conn, $_GET['kondisi']);

    // 2. Ambil data transaksi JOIN barang untuk dapat HARGA_ASLI
    $sql_data = "SELECT p.*, b.harga_asli, b.id as id_barang, b.nama_barang 
                 FROM peminjaman p 
                 JOIN barang b ON p.barang_id = b.id 
                 WHERE p.id = '$id_pinjam'";
    
    $query_data = mysqli_query($conn, $sql_data);
    $data = mysqli_fetch_assoc($query_data);

    if ($data) {
        $harga_asli = (int)$data['harga_asli'];
        $jumlah     = (int)$data['jumlah'];
        $denda_fisik = 0;
        $denda_telat = 0;

        // --- A. HITUNG DENDA FISIK ---
        if ($kondisi == 'ringan') {
            $denda_fisik = ($harga_asli * 0.10) * $jumlah; // 10% dari harga asli
        } elseif ($kondisi == 'berat') {
            $denda_fisik = $harga_asli * $jumlah; // 100% harga asli
        }

        // --- B. HITUNG DENDA TELAT (Opsional: Misal 5rb/hari) ---
        $tgl_deadline = new DateTime($data['tanggal_kembali']);
        $tgl_sekarang = new DateTime(date('Y-m-d'));
        if ($tgl_sekarang > $tgl_deadline) {
            $selisih = $tgl_sekarang->diff($tgl_deadline);
            $denda_telat = $selisih->days * 5000; 
        }

        $total_denda = $denda_fisik + $denda_telat;

        // 3. UPDATE STATUS PEMINJAMAN
        $update = "UPDATE peminjaman SET 
                   status = 'selesai', 
                   kondisi_kembali = '$kondisi', 
                   denda = '$total_denda', 
                   tanggal_kembali = NOW() 
                   WHERE id = '$id_pinjam'";

        if (mysqli_query($conn, $update)) {
            if ($kondisi !== 'berat') {
                $id_barang = $data['id_barang'];
                mysqli_query($conn, "UPDATE barang SET stok = stok + $jumlah WHERE id = '$id_barang'");
            }

            // 5. LOG AKTIVITAS
            $pesan_log = "Petugas {$_SESSION['nama']} memverifikasi kembali $jumlah unit {$data['nama_barang']} ($kondisi). Denda: Rp$total_denda";
            mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");

            header("Location: verifikasi_pengembalian.php?status=sukses");
            exit;
        }
    }
}
header("Location: verifikasi_pengembalian.php");
?>