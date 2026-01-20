<?php
session_start();
include 'koneksi.php';

// 1. CEK LOGIN (Security)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. AMBIL DATA USER
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Default values jika data kosong
$first_name = $user['first_name'] ?? 'User';
$weight = $user['current_weight'] ?? 0;
$height = $user['height'] ?? 0;

// 3. HITUNG BMI (Server Side Logic)
$bmi_score = 0;
$bmi_status = "No Data";
$bmi_color = "#64748b"; // Abu-abu

if ($weight > 0 && $height > 0) {
    $height_m = $height / 100;
    $bmi_score = $weight / ($height_m * $height_m);
    $bmi_score = number_format($bmi_score, 1);

    if ($bmi_score < 18.5) {
        $bmi_status = "Underweight";
        $bmi_color = "#3b82f6"; // Biru
    } elseif ($bmi_score >= 18.5 && $bmi_score < 24.9) {
        $bmi_status = "Normal";
        $bmi_color = "#22c55e"; // Hijau
    } elseif ($bmi_score >= 25 && $bmi_score < 29.9) {
        $bmi_status = "Overweight";
        $bmi_color = "#f97316"; // Oranye
    } else {
        $bmi_status = "Obesity";
        $bmi_color = "#ef4444"; // Merah
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <span class="logo-text">HydraFit</span>
            </a>
            <button id="toggleSidebar" class="btn-toggle">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
        </div>

        <ul class="menu-list">
            <li class="active">
                <a href="#">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span class="link-text">Dashboard</span>
                </a>
            </li>
            <li class="menu-spacer"></li>
            <li>
                <a href="logout.php" class="logout-link" onclick="return confirm('Are you sure to logout?');">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span class="link-text">Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-content" id="mainContent">
        
        <header class="top-header">
            <div class="user-welcome">
                <h1>Hello, <?php echo htmlspecialchars($first_name); ?>! 👋</h1>
                <p>Track your progress and stay healthy.</p>
            </div>
            <div class="user-profile">
                <div class="avatar"><?php echo strtoupper(substr($first_name, 0, 1)); ?></div>
            </div>
        </header>

        <div class="dashboard-grid">
            
            <div class="grid-left">
                <div class="card chart-card">
                    <div class="card-header">
                        <h3>Weight Tracker</h3>
                        
                        <div class="timeframe-buttons">
                            <button class="time-btn active" data-time="1W">1W</button>
                            <button class="time-btn" data-time="1M">1M</button>
                            <button class="time-btn" data-time="ALL">ALL</button>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <canvas id="weightChart"></canvas>
                    </div>

                    <div class="weight-input-area">
                        <input type="number" id="newWeight" placeholder="Enter weight (kg)" step="0.1">
                        <button class="btn-update" id="btnUpdateWeight">Update Weight</button>
                    </div>
                </div>
            </div>

            <div class="grid-right">
                
                <div class="card stat-card">
                    <div class="icon-box blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7"/><path d="M15 7h6v6"/></svg>
                    </div>
                    <div class="stat-info">
                        <span class="label">Current Weight</span>
                        <h2 class="value" id="displayWeight"><?php echo $weight; ?> kg</h2>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="icon-box orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div class="stat-info">
                        <span class="label">BMI Score</span>
                        <h2 class="value" id="bmiValue"><?php echo $bmi_score; ?></h2>
                        <span class="sub-text" id="bmiStatus" style="color: <?php echo $bmi_color; ?>;">
                            <?php echo $bmi_status; ?>
                        </span>
                    </div>
                </div>

                <div class="card tip-card">
                    <h4>💡 Daily Tip</h4>
                    <p>Drinking 500ml of water before meals can help with weight loss.</p>
                </div>

            </div>
        </div>
    </main>

    <div id="customAlert" class="modal-overlay hidden">
        <div class="modal-box fade-in-up">
            <div class="modal-icon-wrapper">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h3>Success!</h3>
            <p id="modalMessage">Data updated successfully.</p>
            <button id="closeModalBtn">OK, Great!</button>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>

</body>
</html>