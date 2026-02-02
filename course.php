<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_page = 'course';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Library - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <span class="logo-text">HydraFit</span>
            </a>
            <button class="btn-toggle" id="sidebarToggle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5"/></svg>
            </button>
        </div>

        <ul class="menu-list">
            <li class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <a href="dashboard.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span class="link-text">Dashboard</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'course') ? 'active' : ''; ?>">
                <a href="course.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    <span class="link-text">Course</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="logout.php" class="logout-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span class="link-text">Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Course Library</h1>
                <p>Select a workout plan to start training.</p>
            </div>
        </div>

        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search workout..." class="search-input">
        </div>
        
        <div class="course-grid">
            
            <?php
            // 1. QUERY KE DATABASE
            $query = "SELECT * FROM courses ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);

            // 2. LOOPING DATA
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="course-card">
                        <div class="course-thumb" style="background: url('<?php echo htmlspecialchars($row['thumbnail']); ?>') center/cover no-repeat;">
                        </div>

                        <div class="course-content">
                            <span class="course-tag"><?php echo htmlspecialchars($row['tagline']); ?></span>
                            
                            <h3 class="course-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            
                            <p class="course-desc">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>
                            
                            <div class="muscle-group">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5"/></svg>
                                Target: <?php echo htmlspecialchars($row['target_muscle']); ?>
                            </div>

                            <a href="workout_detail.php?id=<?php echo $row['id']; ?>" class="btn-start">Start Workout →</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='color:#64748b; grid-column: 1/-1; text-align:center;'>No courses available yet. Admin needs to add some!</p>";
            }
            ?>

        </div>
        
        <p id="noResultMsg">No course found with that name.</p>

    </div>

    <script src="assets/js/dashboard.js"></script>

</body>
</html>