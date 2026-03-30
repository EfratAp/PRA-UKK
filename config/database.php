<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ukk final";
$port = 3307;

$conn = mysqli_connect($host, $user, $pass, $db, $port);
//$conn = mysqli_connect("localhost", "root", "", "nama_database_anda");

mysqli_query($conn, "SET NAMES 'utf8'");
date_default_timezone_set('Asia/Jakarta');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>