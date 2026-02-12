<?php
session_start();
include '../config/database.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Logika Update Role (Tambahkan Bagian Ini)
if (isset($_GET['id']) && isset($_GET['role'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $role = mysqli_real_escape_string($conn, $_GET['role']);
    
    // Pastikan role yang dimasukkan valid
    if ($role == 'petugas' || $role == 'peminjam') {
        $update = mysqli_query($conn, "UPDATE users SET role = '$role' WHERE id = '$id'");
        if ($update) {
            echo "<script>alert('Role berhasil diperbarui!'); window.location='kelola_user.php';</script>";
        }
    }
}

// 3. Ambil data user terbaru
$users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Perbaikan agar tombol tidak berhimpitan */
        .action-btns {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .role-badge {
            text-transform: uppercase;
            font-size: 11px;
            padding: 4px 8px;
        }
    </style>
</head>
<body class="dashboard-page">
<div class="box wide">
    <h2>Kelola Akun Pengguna</h2>
    <p>Ubah status pengguna menjadi Petugas atau Peminjam.</p>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>NAMA</th>
                    <th>EMAIL</th>
                    <th style="text-align: center;">ROLE SAAT INI</th>
                    <th style="text-align: center;">AKSI UBAH ROLE</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = mysqli_fetch_assoc($users)) : ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['nama']); ?></strong></td>
                    <td><?= htmlspecialchars($u['email']); ?></td>
                    <td style="text-align: center;">
                        <span class="badge <?= ($u['role'] == 'petugas') ? 'badge-disetujui' : 'badge-menunggu'; ?> role-badge">
                            <?= $u['role']; ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <?php if($u['role'] !== 'petugas'): ?>
                                <a href="kelola_user.php?id=<?= $u['id']; ?>&role=petugas" 
                                   class="btn btn-outline" 
                                   style="padding: 5px 10px; font-size: 11px; border-color: #f59e0b; color: #b45309;"
                                   onclick="return confirm('Jadikan Petugas?')">Jadikan Petugas</a>
                            <?php endif; ?>

                            <?php if($u['role'] !== 'peminjam'): ?>
                                <a href="kelola_user.php?id=<?= $u['id']; ?>&role=peminjam" 
                                   class="btn btn-outline" 
                                   style="padding: 5px 10px; font-size: 11px;"
                                   onclick="return confirm('Jadikan Peminjam?')">Jadikan Peminjam</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($users) == 0): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Tidak ada pengguna lain selain Admin.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="dashboard.php" class="btn btn-outline" style="margin-top:20px; display: inline-block;">⬅ Kembali</a>
</div>
</body>
</html>