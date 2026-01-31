<?php
session_start();
include '../koneksi.php';

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$current_page = 'manage_course';
$success_msg = "";
$error_msg = "";

// --- LOGIC TAMBAH COURSE (VERSI URL GAMBAR) ---
if (isset($_POST['add_course'])) {
    $title         = trim($_POST['title']);
    $tagline       = trim($_POST['tagline']);
    $target_muscle = trim($_POST['target_muscle']);
    $description   = trim($_POST['description']);
    $thumbnail_url = trim($_POST['thumbnail']); // Ambil Link Gambar

    // Validasi sederhana: Pastikan semua terisi
    if (!empty($title) && !empty($thumbnail_url)) {
        
        // Simpan ke Database (Langsung simpan URL-nya)
        $stmt = $conn->prepare("INSERT INTO courses (title, tagline, thumbnail, description, target_muscle) VALUES (?, ?, ?, ?, ?)");
        // Perhatikan tipe datanya semua string (sssss)
        $stmt->bind_param("sssss", $title, $tagline, $thumbnail_url, $description, $target_muscle);

        if ($stmt->execute()) {
            $success_msg = "New Course created successfully!";
        } else {
            $error_msg = "Database Error: " . $conn->error;
        }
    } else {
        $error_msg = "Please fill in all fields.";
    }
}

// --- LOGIC HAPUS COURSE ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Karena sekarang cuma URL, kita tidak perlu unlink/hapus file fisik.
    // Langsung hapus data di database saja.
    mysqli_query($conn, "DELETE FROM courses WHERE id='$id'");
    
    header("Location: manage_course.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <div class="logo-icon" style="background-color: #0f172a;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
                <span class="logo-text">Admin Panel</span>
            </a>
            <button class="btn-toggle" id="sidebarToggle"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5"/></svg></button>
        </div>
        <ul class="menu-list">
            <li><a href="dashboard.php"><span class="link-text">Dashboard</span></a></li>
            <li class="active"><a href="manage_course.php"><span class="link-text">Manage Courses</span></a></li>
            <li><a href="manage_users.php"><span class="link-text">Manage Users</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-link"><span class="link-text">Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Manage Courses</h1>
                <p>Create the workout album first, then add the exercises.</p>
            </div>
        </div>

        <?php if($success_msg): ?><div style="background:#dcfce7;color:#166534;padding:15px;border-radius:8px;margin-bottom:20px;"><?php echo $success_msg; ?></div><?php endif; ?>
        <?php if($error_msg): ?><div style="background:#fee2e2;color:#b91c1c;padding:15px;border-radius:8px;margin-bottom:20px;"><?php echo $error_msg; ?></div><?php endif; ?>

        <div class="card">
            <h3>Step 1: Create New Course</h3>
            <form action="" method="POST" style="margin-top: 1.5rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display:block;margin-bottom:5px;font-weight:500;">Course Title</label>
                        <input type="text" name="title" required placeholder="e.g. Home Warrior" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:5px;font-weight:500;">Tagline (Singkat)</label>
                        <input type="text" name="tagline" required placeholder="e.g. Badan Bagus Tanpa Modal" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;">
                    </div>
                </div>

                <div style="margin-top:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:500;">Target Muscle</label>
                    <input type="text" name="target_muscle" required placeholder="e.g. Dada, Paha, Core" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;">
                </div>

                <div style="margin-top:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:500;">Cover Image URL</label>
                    <input type="text" name="thumbnail" required placeholder="Paste image link here (e.g. https://image.com/foto.jpg)" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;">
                    <small style="color:#64748b;">Tips: Cari gambar di Google -> Klik Kanan -> Copy Image Address</small>
                </div>

                <div style="margin-top:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:500;">Description</label>
                    <textarea name="description" required rows="2" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;"></textarea>
                </div>

                <button type="submit" name="add_course" style="margin-top:20px;background:#0f172a;color:white;padding:12px 24px;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Save Course</button>
            </form>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3>Your Course Library</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="80">Cover</th>
                        <th>Info</th>
                        <th width="200">Content</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM courses ORDER BY created_at DESC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        
                        // PERUBAHAN DI SINI: Langsung echo link URL, tidak pakai path assets/uploads/
                        echo "<td><img src='" . htmlspecialchars($row['thumbnail']) . "' width='60' height='60' style='border-radius:6px;object-fit:cover;aspect-ratio:1/1;background:#f1f5f9;' alt='Img'></td>";
                        
                        echo "<td>
                                <strong>" . htmlspecialchars($row['title']) . "</strong><br>
                                <small style='color:#64748b;'>" . htmlspecialchars($row['tagline']) . "</small>
                              </td>";
                        
                        echo "<td>
                                <a href='manage_exercises.php?id=" . $row['id'] . "' style='background:#eff6ff; color:#2563eb; padding:6px 12px; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.85rem;'>
                                    + Add Exercises
                                </a>
                              </td>";
                        
                        echo "<td>
                                <a href='?delete=" . $row['id'] . "' onclick='return confirm(\"Hapus course ini?\")' style='color:#ef4444;font-weight:600;text-decoration:none;'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>