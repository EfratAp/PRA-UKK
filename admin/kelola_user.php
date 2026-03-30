<?php
session_start();
include '../config/database.php';

// 1. Proteksi: Hanya Admin yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); 
    exit;
}

// 2. LOGIKA TUKAR ROLE (Petugas <-> Peminjam)
if (isset($_GET['toggle_role'])) {
    $id = (int)$_GET['toggle_role'];
    
    // Ambil data user untuk pengecekan
    $check_user = mysqli_query($conn, "SELECT nama, role FROM users WHERE id = '$id'");
    $u = mysqli_fetch_assoc($check_user);
    
    // Admin tidak bisa diubah role-nya dari sini demi keamanan
    if ($u && $u['role'] != 'admin') {
        $new_role = ($u['role'] == 'petugas') ? 'peminjam' : 'petugas';
        
        $update = mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE id = '$id'");
        
        if($update) {
            // Catat aktivitas ke log
            $admin_nama = $_SESSION['nama'];
            $pesan_log = "Admin $admin_nama mengubah role {$u['nama']} dari {$u['role']} menjadi $new_role";
            mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, pesan) VALUES ('{$_SESSION['id']}', '$pesan_log')");
            
            header("Location: kelola_user.php?status=sukses"); 
            exit;
        }
    }
}

// 3. Ambil semua data user
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Admin Sarpras</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .badge-admin { background: #ef4444; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .badge-petugas { background: #3b82f6; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .badge-peminjam { background: #10b981; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        
        .btn-tukar { 
            text-decoration: none; 
            background: #f1f5f9; 
            color: #6366f1; 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-size: 12px; 
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        .btn-tukar:hover { background: #e0e7ff; }
        
        .nav-header {
            max-width: 900px;
            margin: 0 auto 15px auto;
        }
        .btn-back {
            text-decoration: none;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body class="dashboard-page">

<div class="nav-header">
    <a href="dashboard.php" class="btn-back">⬅ Kembali ke Dashboard</a>
</div>

<div class="box wide" style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 25px;">
        <h2 style="margin:0;">👥 Manajemen User</h2>
        <p style="color: #64748b; margin-top: 5px;">Kelola hak akses pengguna sistem (Admin, Petugas, Peminjam).</p>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            ✅ Berhasil memperbarui hak akses user.
        </div>
    <?php endif; ?>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; background: #f8fafc;">
                <th style="padding: 15px; color: #64748b; font-size: 12px; text-transform: uppercase;">Nama Pengguna</th>
                <th style="padding: 15px; color: #64748b; font-size: 12px; text-transform: uppercase;">Role / Hak Akses</th>
                <th style="padding: 15px; color: #64748b; font-size: 12px; text-transform: uppercase; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($users)): ?>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 15px;">
                    <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($row['nama']); ?></div>
                    <div style="font-size: 12px; color: #94a3b8;"><?= htmlspecialchars($row['username']); ?></div>
                </td>
                <td style="padding: 15px;">
                    <span class="badge-<?= $row['role']; ?>"><?= strtoupper($row['role']); ?></span>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <?php if($row['role'] != 'admin'): ?>
                        <a href="?toggle_role=<?= $row['id']; ?>" class="btn-tukar" 
                           onclick="return confirm('Tukar role user <?= $row['nama']; ?>?')">
                           🔄 Tukar Role
                        </a>
                    <?php else: ?>
                        <span style="color: #cbd5e1; font-size: 12px;">Utama</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>