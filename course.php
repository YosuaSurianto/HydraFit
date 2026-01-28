<?php
session_start();
include 'koneksi.php'; // Langsung include karena sejajar

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_page = 'course'; // Penanda biar menu Course menyala
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course - HydraFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
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
        <li>
            <a href="dashboard.php">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span class="link-text">Dashboard</span>
            </a>
        </li>

        <li class="active">
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
        <h1>Course Library</h1>
    </div>
    
    <div class="card">
        <p>Halaman Course siap diisi fitur! 🚀</p>
    </div>
</div>

<script src="assets/js/dashboard.js"></script>

</body>
</html>