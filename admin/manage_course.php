<?php
session_start();
include '../koneksi.php';

// 1. CEK ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$current_page = 'manage_course';

// Variabel SweetAlert
$swal_type = "";
$swal_title = "";
$swal_text = "";
$redirect_url = "";

// VARIABEL FORM DEFAULT
$edit_mode = false;
$edit_data = [
    'id' => '', 'title' => '', 'tagline' => '', 
    'target_muscle' => '', 'thumbnail' => '', 
    'banner' => '', 'description' => ''
];

// --- 1. LOGIC EDIT (AMBIL DATA) ---
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_mode = true;
        $edit_data = $result->fetch_assoc();
    }
}

// --- 2. LOGIC SIMPAN / UPDATE ---
if (isset($_POST['save_course'])) {
    $title         = trim($_POST['title']);
    $tagline       = trim($_POST['tagline']);
    $target_muscle = trim($_POST['target_muscle']);
    $description   = trim($_POST['description']);
    $thumbnail_url = trim($_POST['thumbnail']);
    $banner_url    = trim($_POST['banner']);

    if (!empty($title) && !empty($thumbnail_url)) {
        
        if(empty($banner_url)) $banner_url = $thumbnail_url;

        if (!empty($_POST['course_id'])) {
            // === UPDATE ===
            $id = $_POST['course_id'];
            $stmt = $conn->prepare("UPDATE courses SET title=?, tagline=?, thumbnail=?, banner=?, description=?, target_muscle=? WHERE id=?");
            $stmt->bind_param("ssssssi", $title, $tagline, $thumbnail_url, $banner_url, $description, $target_muscle, $id);
            
            if ($stmt->execute()) {
                $swal_type = "success";
                $swal_title = "Updated!";
                $swal_text = "Course data has been updated.";
                $redirect_url = "manage_course.php";
            } else {
                $swal_type = "error";
                $swal_title = "Failed";
                $swal_text = "Database Error: " . $conn->error;
            }

        } else {
            // === CREATE ===
            $stmt = $conn->prepare("INSERT INTO courses (title, tagline, thumbnail, banner, description, target_muscle) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $tagline, $thumbnail_url, $banner_url, $description, $target_muscle);

            if ($stmt->execute()) {
                $swal_type = "success";
                $swal_title = "Created!";
                $swal_text = "New course added successfully.";
                $redirect_url = "manage_course.php";
            } else {
                $swal_type = "error";
                $swal_title = "Failed";
                $swal_text = "Database Error: " . $conn->error;
            }
        }
    } else {
        $swal_type = "warning";
        $swal_title = "Incomplete Data";
        $swal_text = "Please fill in Title and Thumbnail URL.";
    }
}

// --- 3. LOGIC HAPUS DATA ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_course.php");
    exit();
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
            <a href="../logout.php" class="logout-link"><span class="link-text">Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Manage Courses</h1>
                <p>Create, Edit, or Delete workout albums.</p>
            </div>
        </div>

        <div class="card">
            <div class="form-header">
                <h3><?php echo $edit_mode ? '✏️ Edit Course' : '✨ Create New Course'; ?></h3>
                <?php if($edit_mode): ?>
                    <a href="manage_course.php" class="btn-cancel-edit">✕ Cancel Edit</a>
                <?php endif; ?>
            </div>

            <form action="" method="POST" class="admin-form">
                <input type="hidden" name="course_id" value="<?php echo $edit_data['id']; ?>">

                <div class="form-grid">
                    <div>
                        <label class="form-label">Course Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($edit_data['title']); ?>" required placeholder="Name Course" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Tagline (Singkat)</label>
                        <input type="text" name="tagline" value="<?php echo htmlspecialchars($edit_data['tagline']); ?>" required placeholder="Clickbait" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Muscle</label>
                    <input type="text" name="target_muscle" value="<?php echo htmlspecialchars($edit_data['target_muscle']); ?>" required placeholder="Target Muscle" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Thumbnail Image URL (Card)</label>
                    <input type="text" name="thumbnail" value="<?php echo htmlspecialchars($edit_data['thumbnail']); ?>" required placeholder="https://... (Rasio 16:9)" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Banner Image URL (Header)</label>
                    <input type="text" name="banner" value="<?php echo htmlspecialchars($edit_data['banner']); ?>" placeholder="https://... (Wide Image)" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" required rows="3" class="form-input"><?php echo htmlspecialchars($edit_data['description']); ?></textarea>
                </div>

                <button type="submit" name="save_course" class="btn-submit <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?>">
                    <?php echo $edit_mode ? 'Update Changes' : 'Save New Course'; ?>
                </button>
            </form>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3>Your Course Library</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="80">Cover</th>
                        <th>Info</th>
                        <th width="180">Content</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM courses ORDER BY created_at DESC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><img src='" . htmlspecialchars($row['thumbnail']) . "' width='60' height='60' class='table-thumb' alt='Img'></td>";
                        echo "<td>
                                <strong>" . htmlspecialchars($row['title']) . "</strong><br>
                                <small style='color:#64748b;'>" . htmlspecialchars($row['tagline']) . "</small>
                              </td>";
                        echo "<td>
                                <a href='manage_exercises.php?id=" . $row['id'] . "' class='btn-sm-add'>
                                    + Exercises
                                </a>
                              </td>";
                        echo "<td>
                                <a href='?edit=" . $row['id'] . "' class='action-link text-edit'>Edit</a>
                                <a href='javascript:void(0);' onclick='confirmDelete(" . $row['id'] . ")' class='action-link text-delete'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="../assets/js/dashboard.js"></script>

    <script>
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

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '?delete=' + id;
                }
            })
        }
    </script>
</body>
</html>