<?php
session_start();
include '../koneksi.php'; // Mundur satu langkah untuk cari koneksi

// SECURITY CHECK (WAJIB ADMIN)

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Kalau dia login tapi BUKAN admin, tendang ke dashboard user biasa
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

$current_page = 'dashboard';
$user_id = $_SESSION['user_id'];


// AMBIL DATA ADMIN & STATISTIK

$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);
$admin_name = $admin['first_name'] ?? 'Admin';

// Hitung Total User
$user_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
$user_count = mysqli_fetch_assoc($user_count_query)['total'];

// Hitung Total Course
$course_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM courses");
$course_count = mysqli_fetch_assoc($course_count_query)['total'];

// Hutung Total Admin
$admin_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='admin'");
$admin_count = mysqli_fetch_assoc($admin_count_query)['total']; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="logo">
                <div class="logo-icon" style="background-color: #0f172a;"> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
                <span class="logo-text">Admin Panel</span>
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

            <li>
                <a href="manage_course.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    <span class="link-text">Manage Courses</span>
                </a>
            </li>

            <li>
                <a href="manage_users.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="link-text">Manage Users</span>
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
            <div class="user-welcome">
                <h1>Hello, Admin <?php echo htmlspecialchars($admin_name); ?>! 👮‍♂️</h1>
                <p>Here is what's happening today.</p>
            </div>
            <div class="avatar" style="background-color: #0f172a;">
                A
            </div>
        </div>

        <div class="dashboard-grid admin-mode">
            
            <div class="card stat-card admin-card">
                <div class="icon-box purple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
                <div class="stat-info">
                    <span class="label">Total Users</span>
                    <h2 class="value"><?php echo $user_count; ?></h2>
                </div>
            </div>

            <div class="card stat-card admin-card">
    <div class="icon-box" style="background-color: #f59e0b; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
    </div>
    <div class="stat-info">
        <span class="label">Total Admins</span>
        <h2 class="value"><?php echo $admin_count; ?></h2>
    </div>
</div>

            <div class="card stat-card admin-card">
                <div class="icon-box green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                </div>
                <div class="stat-info">
                    <span class="label">Active Courses</span>
                    <h2 class="value"><?php echo $course_count; ?></h2>
                </div>
            </div>

        </div>

        <div class="card">
            <h3>🚀 Quick Actions</h3>
            <p style="margin-top: 10px; color: #64748b;">Select "Manage Courses" to add new content or "Manage Users" to reset passwords.</p>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>

</body>
</html>