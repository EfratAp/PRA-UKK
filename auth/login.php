<?php
session_start();
include '../config/database.php';

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    $data = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($data);

    if ($user && password_verify($pass, $user['password'])) {

        $_SESSION['id']   = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];

$pesan_log = $_SESSION['nama'] . " (".$_SESSION['role'].") berhasil login ke sistem.";
mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$user['id']}', '$pesan_log')");

        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($user['role'] == 'petugas') {
            header("Location: ../petugas/dashboard.php");
        } else {
            header("Location: ../peminjam/dashboard.php");
        }
        exit;
    }
        $error = "Email atau password salah!";
    }

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page">

<div class="box">
    <h2>Login Sarpras</h2>
    <p>Masuk untuk mengelola peminjaman barang</p>

    <?php if ($error != "") : ?>
        <div class="badge badge-danger" style="display: block; margin-bottom: 20px; padding: 10px;">
            <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" placeholder="contoh@email.com" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary">Masuk Sekarang</button>
    </form>

    <div style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
        <p style="margin-bottom: 10px;">Belum memiliki akun?</p>
        <a href="register.php" class="btn btn-outline" style="width: 100%;">Daftar Akun Baru</a>
    </div>
</div>

</body>
</html>