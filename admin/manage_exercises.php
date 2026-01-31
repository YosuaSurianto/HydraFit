<?php
session_start();
include '../koneksi.php';

// 1. CEK ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. TANGKAP ID COURSE (Wajib ada, kalau gak ada tendang balik)
if (!isset($_GET['id'])) {
    header("Location: manage_course.php");
    exit();
}
$course_id = $_GET['id'];

// 3. AMBIL DATA COURSE (Buat Judul Halaman)
$q_course = mysqli_query($conn, "SELECT * FROM courses WHERE id='$course_id'");
$course = mysqli_fetch_assoc($q_course);

// Kalau ID ngawur (course gak ketemu)
if (!$course) {
    header("Location: manage_course.php");
    exit();
}

$success_msg = "";
$error_msg = "";

// --- LOGIC TAMBAH EXERCISE ---
if (isset($_POST['add_exercise'])) {
    $name        = trim($_POST['name']);
    $duration    = trim($_POST['duration']);
    $gif_url     = trim($_POST['gif_url']); // Kita pakai URL lagi biar gampang
    $instruction = trim($_POST['instruction']);

    if (!empty($name) && !empty($gif_url)) {
        $stmt = $conn->prepare("INSERT INTO exercises (course_id, name, duration, gif_image, instruction) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $course_id, $name, $duration, $gif_url, $instruction);

        if ($stmt->execute()) {
            $success_msg = "Exercise added successfully!";
        } else {
            $error_msg = "Error: " . $conn->error;
        }
    } else {
        $error_msg = "Name and GIF URL are required!";
    }
}

// --- LOGIC HAPUS EXERCISE ---
if (isset($_GET['delete_ex'])) {
    $ex_id = $_GET['delete_ex'];
    mysqli_query($conn, "DELETE FROM exercises WHERE id='$ex_id'");
    header("Location: manage_exercises.php?id=$course_id"); // Balik ke halaman course ini
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exercises - HydraFit Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
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
            <a href="../logout.php" class="logout-link"><span class="link-text">Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <a href="manage_course.php" style="text-decoration: none; color: #64748b; font-size: 0.9rem;">← Back to Courses</a>
                <h1 style="margin-top: 10px;">Exercises for: <?php echo htmlspecialchars($course['title']); ?></h1>
                <p>Add the step-by-step workouts for this course.</p>
            </div>
        </div>

        <?php if($success_msg): ?><div style="background:#dcfce7;color:#166534;padding:15px;border-radius:8px;margin-bottom:20px;"><?php echo $success_msg; ?></div><?php endif; ?>
        <?php if($error_msg): ?><div style="background:#fee2e2;color:#b91c1c;padding:15px;border-radius:8px;margin-bottom:20px;"><?php echo $error_msg; ?></div><?php endif; ?>

        <div class="card">
            <h3>Add New Exercise</h3>
            <form action="" method="POST" style="margin-top: 1.5rem;">
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div>
                        <label style="display:block;margin-bottom:5px;font-weight:500;">Exercise Name</label>
                        <input type="text" name="name" required placeholder="e.g. Standard Push-Up" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:5px;font-weight:500;">Duration / Reps</label>
                        <input type="text" name="duration" required placeholder="e.g. 12 Reps or 30 Sec" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;">
                    </div>
                </div>

                <div style="margin-top:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:500;">GIF Image URL</label>
                    <input type="text" name="gif_url" required placeholder="Paste GIF link here (e.g. https://site.com/pushup.gif)" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;">
                    <small style="color:#64748b;">Tips: Cari di Google "Push up GIF" -> Klik Kanan -> Copy Image Address</small>
                </div>

                <div style="margin-top:15px;">
                    <label style="display:block;margin-bottom:5px;font-weight:500;">Instructions</label>
                    <textarea name="instruction" required rows="2" placeholder="Explain how to do it correctly..." style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;"></textarea>
                </div>

                <button type="submit" name="add_exercise" style="margin-top:20px;background:#0f172a;color:white;padding:12px 24px;border:none;border-radius:8px;cursor:pointer;font-weight:600;">+ Add Exercise</button>
            </form>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3>Exercise List</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="80">GIF</th>
                        <th>Name</th>
                        <th>Duration</th>
                        <th>Instruction</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil exercises CUMA untuk course ini
                    $q_ex = mysqli_query($conn, "SELECT * FROM exercises WHERE course_id='$course_id' ORDER BY id ASC");
                    
                    if (mysqli_num_rows($q_ex) > 0) {
                        while ($row = mysqli_fetch_assoc($q_ex)) {
                            echo "<tr>";
                            echo "<td><img src='" . htmlspecialchars($row['gif_image']) . "' width='60' height='60' style='border-radius:6px;object-fit:cover;background:#f1f5f9;'></td>";
                            echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
                            echo "<td><span style='background:#f1f5f9;padding:4px 8px;border-radius:4px;font-size:0.85rem;font-weight:600;'>" . htmlspecialchars($row['duration']) . "</span></td>";
                            echo "<td><small style='color:#64748b;'>" . substr(htmlspecialchars($row['instruction']), 0, 50) . "...</small></td>";
                            echo "<td>
                                    <a href='?id=$course_id&delete_ex=" . $row['id'] . "' onclick='return confirm(\"Delete this exercise?\")' style='color:#ef4444;font-weight:600;text-decoration:none;'>Del</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;padding:20px;color:#64748b;'>No exercises added yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>