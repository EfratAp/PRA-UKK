<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistem_peminjaman_sarpras";
$port = 3307;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>