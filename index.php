<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: auth/login.php");
    exit;
}

if ($_SESSION['role'] == 'peminjam') {
    header("Location: peminjam/dashboard.php");
} elseif ($_SESSION['role'] == 'admin') {
    header("Location: admin/dashboard.php");
} elseif ($_SESSION['role'] == 'petugas') {
    header("Location: petugas/dashboard.php");
}

