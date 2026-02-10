<?php
session_start();
include 'koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// TANGKAP ID DARI URL
if (!isset($_GET['id'])) {
    header("Location: course.php");
    exit();
}
$course_id = $_GET['id'];

// AMBIL DATA COURSE (HEADER)
$query_course = "SELECT * FROM courses WHERE id = '$course_id'";
$result_course = mysqli_query($conn, $query_course);
$course = mysqli_fetch_assoc($result_course);

// Kalau course tidak ditemukan
if (!$course) {
    echo "<script>alert('Course not found!'); window.location='course.php';</script>";
    exit();
}

// --- LOGIC BANNER  ---
// Kalau kolom 'banner' ada isinya, pakai banner. Kalau kosong, pakai thumbnail.
$bg_image = !empty($course['banner']) ? $course['banner'] : $course['thumbnail'];

// AMBIL DATA EXERCISES (ISI GERAKAN)
$query_exercises = "SELECT * FROM exercises WHERE course_id = '$course_id' ORDER BY id ASC";
$result_exercises = mysqli_query($conn, $query_exercises);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - HydraFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/course.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <div class="logo-icon"> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
                <span class="logo-text">HydraFit</span>
            </a>
            <button class="btn-toggle" id="sidebarToggle"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5"/></svg></button>
        </div>
        <ul class="menu-list">
            <li><a href="dashboard.php">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span class="link-text">Dashboard</span>
            </a>
        </li>

            <li class="active"><a href="course.php">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                <span class="link-text">Course</span>
            </a>
        </li>
        </ul>

    <!-- SIDE BAR FOOTER -->
       <div class="sidebar-footer-settings">
            <a href="settings.php" style="<?php echo ($current_page == 'settings') ? 'color: #2563eb;' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            <span class="link-text">Settings</span>
            </a>
        </div>
    <div class="sidebar-footer">
            <a href="logout.php" class="logout-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="link-text">Logout</span>
            </a>

        </div>
    
    </div>
    </div>

    <div class="main-content">
        
        <div class="workout-header" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo $course['banner']; ?>');">
            <a href="course.php" class="btn-back">← Back to Library</a>
            
            <div class="header-content">
                <h1><?php echo htmlspecialchars($course['title']); ?></h1>
                <p>"<?php echo htmlspecialchars($course['tagline']); ?>"</p>
                
                <div class="badges">
                    <span class="badge">Target: <?php echo htmlspecialchars($course['target_muscle']); ?></span>
                </div>
            </div>
        </div>

        <div class="exercise-list">
            
            <?php
            if (mysqli_num_rows($result_exercises) > 0) {
                $no = 1;
                while ($ex = mysqli_fetch_assoc($result_exercises)) {
            ?>
                <div class="exercise-card">
                    <div class="gif-container">
                        <img src="<?php echo htmlspecialchars($ex['gif_image']); ?>" alt="Exercise GIF" onerror="this.src='assets/image/placeholder.png'">
                    </div>
                    <div class="exercise-info">
                        <h3><?php echo $no++; ?>. <?php echo htmlspecialchars($ex['name']); ?></h3>
                        <span class="rep-badge"><?php echo htmlspecialchars($ex['duration']); ?></span>
                        <p class="desc-text"><?php echo htmlspecialchars($ex['instruction']); ?></p>
                    </div>
                </div>
            <?php 
                } // End While
            } else {
                echo "
                <div style='text-align:center; padding: 40px; background: white; border-radius: 12px;'>
                    <h3>🚧 No Exercises Yet</h3>
                    <p>Admin hasn't added any workout steps for this course.</p>
                </div>";
            }
            ?>

        </div>
        
        <?php if (mysqli_num_rows($result_exercises) > 0): ?>
            <div style="margin-top: 30px; text-align: center;">
                <a href="course.php" id="btnFinishWorkout">
                    🎉 I'm Finished!
                </a>
            </div>
        <?php endif; ?>

    </div>
    <script>
    document.getElementById('btnFinishWorkout').addEventListener('click', function(e) {
        e.preventDefault(); // Cegah pindah halaman dulu
        Swal.fire({
            title: 'Workout Completed! 🔥',
            text: 'Great job! You are one step closer to your goal.',
            icon: 'success',
            confirmButtonText: 'Yeah!',
            confirmButtonColor: '#22c55e'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'course.php'; // Baru pindah halaman
            }
        });
    });
</script>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>