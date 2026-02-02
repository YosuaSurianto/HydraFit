<?php
session_start();
include '../koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// TANGKAP ID COURSE (PARENT)
// Halaman ini tidak boleh dibuka tanpa ID Course
if (!isset($_GET['id'])) {
    header("Location: manage_course.php");
    exit();
}
$course_id = $_GET['id'];

// Ambil data Course induknya (Buat Judul)
$stmt_course = $conn->prepare("SELECT title FROM courses WHERE id = ?");
$stmt_course->bind_param("i", $course_id);
$stmt_course->execute();
$result_course = $stmt_course->get_result();
$course_data = $result_course->fetch_assoc();

if (!$course_data) {
    header("Location: manage_course.php"); // Kalau ID ngawur langsung tendang balik
    exit();
}

$current_page = 'manage_course'; // Biar menu sidebar aktif

// Variabel SweetAlert
$swal_type = ""; $swal_title = ""; $swal_text = ""; $redirect_url = "";

// VARIABEL FORM EXERCISE
$edit_mode = false;
$edit_data = [
    'id' => '', 'name' => '', 'duration' => '', 
    'instruction' => '', 'gif_image' => ''
];

// ---LOGIC EDIT: AMBIL DATA LAMA ---
if (isset($_GET['edit_exercise'])) {
    $ex_id = $_GET['edit_exercise'];
    $stmt = $conn->prepare("SELECT * FROM exercises WHERE id = ?");
    $stmt->bind_param("i", $ex_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_mode = true;
        $edit_data = $result->fetch_assoc();
    }
}

// ---LOGIC SIMPAN / UPDATE ---
if (isset($_POST['save_exercise'])) {
    $name        = trim($_POST['name']);
    $duration    = trim($_POST['duration']);
    $instruction = trim($_POST['instruction']);
    $gif_image   = trim($_POST['gif_image']);

    if (!empty($name)) {
        
        if (!empty($_POST['exercise_id'])) {
            // UPDATE EXISTING EXERCISE 
            $ex_id = $_POST['exercise_id'];
            $stmt = $conn->prepare("UPDATE exercises SET name=?, duration=?, instruction=?, gif_image=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $duration, $instruction, $gif_image, $ex_id);
            
            if ($stmt->execute()) {
                $swal_type = "success";
                $swal_title = "Updated!";
                $swal_text = "Exercise has been updated.";
                // Redirect balik ke halaman list course ini
                $redirect_url = "manage_exercises.php?id=" . $course_id;
            } else {
                $swal_type = "error"; $swal_title = "Failed"; $swal_text = $conn->error;
            }

        } else {
            // CREATE NEW EXERCISE
            $stmt = $conn->prepare("INSERT INTO exercises (course_id, name, duration, instruction, gif_image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $course_id, $name, $duration, $instruction, $gif_image);

            if ($stmt->execute()) {
                $swal_type = "success";
                $swal_title = "Added!";
                $swal_text = "New exercise added to the list.";
                $redirect_url = "manage_exercises.php?id=" . $course_id;
            } else {
                $swal_type = "error"; $swal_title = "Failed"; $swal_text = $conn->error;
            }
        }
    } else {
        $swal_type = "warning"; $swal_title = "Empty Name"; $swal_text = "Please enter exercise name.";
    }
}

// --- LOGIC DELETE ---
if (isset($_GET['delete_exercise'])) {
    $ex_id = $_GET['delete_exercise'];
    $stmt = $conn->prepare("DELETE FROM exercises WHERE id = ?");
    $stmt->bind_param("i", $ex_id);
    $stmt->execute();
    
    // Redirect PENTING: Harus bawa ID course lagi
    header("Location: manage_exercises.php?id=" . $course_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exercises - Admin</title>
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
            <a href="../logout.php" class="logout-link"><span class="link-text">Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Manage: <?php echo htmlspecialchars($course_data['title']); ?></h1>
                <p>Add or Edit exercises for this course.</p>
            </div>
            
            <div>
                <a href="manage_course.php" class="btn-cancel-edit" style="font-size: 1rem;">← Back to Courses</a>
            </div>
        </div>

        <div class="card">
            <div class="form-header">
                <h3><?php echo $edit_mode ? '✏️ Edit Exercise' : '💪 Add New Exercise'; ?></h3>
                <?php if($edit_mode): ?>
                    <a href="manage_exercises.php?id=<?php echo $course_id; ?>" class="btn-cancel-edit">✕ Cancel Edit</a>
                <?php endif; ?>
            </div>

            <form action="" method="POST" class="admin-form">
                <input type="hidden" name="exercise_id" value="<?php echo $edit_data['id']; ?>">

                <div class="form-grid">
                    <div>
                        <label class="form-label">Exercise Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($edit_data['name']); ?>" required placeholder="e.g. Push Up" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Duration / Reps</label>
                        <input type="text" name="duration" value="<?php echo htmlspecialchars($edit_data['duration']); ?>" required placeholder="e.g. 30 Seconds / 15 Reps" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">GIF / Image URL</label>
                    <input type="text" name="gif_image" value="<?php echo htmlspecialchars($edit_data['gif_image']); ?>" placeholder="https://... (Animation)" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Instructions</label>
                    <textarea name="instruction" required rows="3" placeholder="How to do this exercise..." class="form-input"><?php echo htmlspecialchars($edit_data['instruction']); ?></textarea>
                </div>

                <button type="submit" name="save_exercise" class="btn-submit <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?>">
                    <?php echo $edit_mode ? 'Update Exercise' : 'Add Exercise'; ?>
                </button>
            </form>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3>Exercise List</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="80">Preview</th>
                        <th>Exercise Info</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil exercise HANYA untuk course ini
                    $stmt_list = $conn->prepare("SELECT * FROM exercises WHERE course_id = ? ORDER BY id ASC");
                    $stmt_list->bind_param("i", $course_id);
                    $stmt_list->execute();
                    $result_list = $stmt_list->get_result();

                    if ($result_list->num_rows > 0) {
                        while ($row = $result_list->fetch_assoc()) {
                            echo "<tr>";
                            // Tampilkan GIF
                            echo "<td><img src='" . htmlspecialchars($row['gif_image']) . "' width='60' height='60' class='table-thumb' alt='Gif' onerror=\"this.src='../assets/image/placeholder.png'\"></td>";
                            
                            echo "<td>
                                    <strong>" . htmlspecialchars($row['name']) . "</strong><br>
                                    <span style='background:#eff6ff; color:#2563eb; padding:2px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;'>" . htmlspecialchars($row['duration']) . "</span>
                                    <p style='color:#64748b; font-size:0.85rem; margin-top:5px; line-height:1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;'>" . htmlspecialchars($row['instruction']) . "</p>
                                  </td>";
                            
                            // Tombol Action (Penting: Bawa ID Course & ID Exercise)
                            echo "<td>
                                    <a href='?id=" . $course_id . "&edit_exercise=" . $row['id'] . "' class='action-link text-edit'>Edit</a>
                                    <a href='javascript:void(0);' onclick='confirmDelete(" . $row['id'] . ")' class='action-link text-delete'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding:20px; color:#94a3b8;'>No exercises yet. Add one above! 👆</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="../assets/js/dashboard.js"></script>

    <script>
        // ALERT SUKSES / ERROR
        <?php if (!empty($swal_type)): ?>
            Swal.fire({
                icon: '<?php echo $swal_type; ?>',
                title: '<?php echo $swal_title; ?>',
                text: '<?php echo $swal_text; ?>',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                <?php if (!empty($redirect_url)): ?>
                    window.location = '<?php echo $redirect_url; ?>';
                <?php endif; ?>
            });
        <?php endif; ?>

        // KONFIRMASI DELETE
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this exercise?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // PENTING: URL Delete juga harus bawa ID Course biar gak tersesat
                    window.location = '?id=<?php echo $course_id; ?>&delete_exercise=' + id;
                }
            })
        }
    </script>
</body>
</html>