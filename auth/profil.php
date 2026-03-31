<?php
session_start();
include '../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['id'])) { 
    header("Location: login.php"); 
    exit; 
}

$id = $_SESSION['id'];
$pesan = "";
$tipe_pesan = "";

// Ambil data terbaru dari database
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$user = mysqli_fetch_assoc($query_user);

if (isset($_POST['update_profil'])) {
    $email_baru = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    if (!empty($password_input)) {
        // Jika password diisi, update email & password
        $hash_password = password_hash($password_input, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET email = '$email_baru', password = '$hash_password' WHERE id = '$id'";
    } else {
        // Jika password kosong, hanya update email
        $sql = "UPDATE users SET email = '$email_baru' WHERE id = '$id'";
    }

    if (mysqli_query($conn, $sql)) {
        // Update data di session agar realtime tanpa relogin
        $_SESSION['email'] = $email_baru;
        
        $pesan = "✅ Profil berhasil diperbarui secara realtime!";
        $tipe_pesan = "disetujui";
        
        // Refresh variabel user untuk tampilan di form
        $user['email'] = $email_baru;
    } else {
        $pesan = "❌ Gagal memperbarui data.";
        $tipe_pesan = "ditolak";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="auth-page">

<div class="box">
    <h2>⚙️ Pengaturan Profil</h2>
    <p>Kelola alamat email dan keamanan akun Anda.</p>

    <?php if ($pesan != ""): ?>
        <div class="badge badge-<?= $tipe_pesan; ?>" style="display: block; margin-bottom: 20px; text-align: center;">
            <?= $pesan; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" value="<?= htmlspecialchars($user['nama']); ?>" disabled style="background: #f9f9f9; cursor: not-allowed;">
            <small style="color: #999;">Nama tidak dapat diubah</small>
        </div>

        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin ganti password">
            <small style="color: #999;">Gunakan kombinasi huruf dan angka.</small>
        </div>

        <button type="submit" name="update_profil" class="btn btn-primary" style="width: 100%;">Simpan Perubahan</button>
    </form>

    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
        <a href="../admin/dashboard.php" class="btn btn-outline" style="width: 100%; text-align: center; display: block;">Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>