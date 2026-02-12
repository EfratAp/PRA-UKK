<?php
session_start();
include '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: riwayat_petugas.php");
    exit;
}

$id_pinjam = $_GET['id'];

// 1. Ambil data peminjaman
$query = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id = '$id_pinjam'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    $tgl_kembali_seharusnya = new DateTime($data['tanggal_kembali']);
    $tgl_sekarang = new DateTime(date('Y-m-d'));
    
    $denda_total = 0;
    $pesan_log = "";

    // 2. Hitung Denda Keterlambatan (Misal: Rp 10.000 / hari)
    if ($tgl_sekarang > $tgl_kembali_seharusnya) {
        $selisih = $tgl_sekarang->diff($tgl_kembali_seharusnya)->days;
        $denda_terlambat = $selisih * 10000;
        $denda_total += $denda_terlambat;
        $pesan_log .= "Terlambat $selisih hari (Rp " . number_format($denda_terlambat) . "). ";
    }

    // 3. Cek Denda Kerusakan (Jika petugas mengirim parameter 'rusak' via URL/Form)
    if (isset($_GET['kondisi']) && $_GET['kondisi'] == 'rusak') {
        $denda_rusak = 50000; // Biaya rusak flat Rp 50.000
        $denda_total += $denda_rusak;
        $pesan_log .= "Barang rusak (Rp " . number_format($denda_rusak) . ").";
    }

    // 4. Update Database
    $update = mysqli_query($conn, "UPDATE peminjaman SET 
        status = 'selesai', 
        denda = '$denda_total' 
        WHERE id = '$id_pinjam'");

    // 5. Kembalikan Stok Barang
    $id_barang = $data['barang_id'];
    $jml = $data['jumlah'];
    mysqli_query($conn, "UPDATE barang SET stok = stok + $jml WHERE id = '$id_barang'");

    // 6. Catat ke Log Aktivitas
    $user_id = $data['user_id'];
    $catatan = "Pengembalian ID #$id_pinjam selesai. Total denda: Rp " . number_format($denda_total) . ". " . $pesan_log;
    mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('$user_id', '$catatan')");

    echo "<script>
            alert('Pengembalian Berhasil! Total Denda: Rp " . number_format($denda_total) . "');
            window.location='dashboard.php';
          </script>";
}
?>