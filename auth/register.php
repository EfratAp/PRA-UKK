<?php
session_start();
include '../config/database.php';

$error = "";

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        $error = "Email sudah terdaftar, silakan login.";
    } else {
        mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'peminjam')");
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page"> <div class="box">
    <h2>Buat Akun</h2>
    <p>Daftar untuk mengakses peminjaman sarpras.</p>

    <?php if ($error != ""): ?>
        <div class="badge badge-ditolak" style="display: block; margin-bottom: 20px; text-align: center;">
            <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" placeholder="nama@email.com" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" name="register" class="btn btn-primary">Daftar Sekarang</button>
    </form>

    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border);">
        <a href="login.php" class="btn btn-outline" style="width: 100%;">Sudah punya akun? Login</a>
    </div>
</div>

</body>
</html>