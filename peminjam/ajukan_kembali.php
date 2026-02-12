<?php

session_start();

include '../config/database.php';



if (!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {

    header("Location: ../auth/login.php");

    exit;

}



if (!isset($_GET['id'])) {

    header("Location: riwayat.php");

    exit;

}



$id      = (int) $_GET['id'];

$user_id = $_SESSION['id'];



$cek = mysqli_query($conn, "

    SELECT * FROM peminjaman

    WHERE id = $id

      AND user_id = $user_id

      AND status = 'dipinjam'

");



if (mysqli_num_rows($cek) == 0) {

    header("Location: riwayat.php");

    exit;

}



mysqli_query($conn, "

    UPDATE peminjaman

    SET status = 'menunggu_kembali'

    WHERE id = $id

");



header("Location: riwayat.php");

exit;