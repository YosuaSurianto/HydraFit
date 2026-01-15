<?php
session_start();
include 'koneksi.php';

// 1. CEK SESSION: Wajib Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = ""; // Variabel untuk pesan alert

// --- FITUR A: UPDATE BERAT BADAN BARU ---
if (isset($_POST['update_weight'])) {
    $new_weight = floatval($_POST['new_weight']);

    if ($new_weight > 0) {
        // A. Catat ke History (Grafik) - Jam otomatis tersimpan berkat DATETIME
        $query_history = "INSERT INTO weight_tracking (user_id, weight, recorded_at) VALUES ('$user_id', '$new_weight', NOW())";
        mysqli_query($conn, $query_history);

        // B. Update Data Utama User (Tampilan Dashboard)
        $query_user = "UPDATE users SET current_weight = '$new_weight' WHERE id = '$user_id'";
        mysqli_query($conn, $query_user);

        // Refresh halaman biar data terupdate
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Berat badan tidak valid!";
    }
}

// --- FITUR B: AMBIL DATA USER & HITUNG BMI ---
$query_user = "SELECT * FROM users WHERE id = '$user_id'";
$result_user = mysqli_query($conn, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Hitung BMI
$height_m = $user['height'] / 100; // Ubah cm ke meter
$bmi = 0;
$bmi_status = "Unknown";
$bmi_color = "#334155";

if ($height_m > 0) {
    $bmi = $user['current_weight'] / ($height_m * $height_m);
    
    // Tentukan Status BMI
    if ($bmi < 18.5) { 
        $bmi_status = "Underweight"; $bmi_color = "#3b82f6"; // Biru
    } elseif ($bmi >= 18.5 && $bmi < 24.9) { 
        $bmi_status = "Normal"; $bmi_color = "#22c55e"; // Hijau
    } elseif ($bmi >= 25 && $bmi < 29.9) { 
        $bmi_status = "Overweight"; $bmi_color = "#f97316"; // Oranye
    } else { 
        $bmi_status = "Obesity"; $bmi_color = "#ef4444"; // Merah
    }
}

// --- FITUR C: SIAPKAN DATA GRAFIK (CRYPTO STYLE) ---
// Kita ambil SEMUA data, diurutkan dari yang terlama ke terbaru (ASC)
$query_chart = "SELECT weight, recorded_at FROM weight_tracking WHERE user_id = '$user_id' ORDER BY recorded_at ASC";
$result_chart = mysqli_query($conn, $query_chart);

$labels = []; // Sumbu X (Waktu)
$data_points = []; // Sumbu Y (Berat)

while ($row = mysqli_fetch_assoc($result_chart)) {
    // Format tanggal jadi cantik: "15 Jan 14:30"
    $labels[] = date('d M H:i', strtotime($row['recorded_at'])); 
    $data_points[] = $row['weight'];
}

// Konversi array PHP ke JSON biar bisa dibaca JavaScript
$json_labels = json_encode($labels);
$json_data = json_encode($data_points);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <span class="logo-text">HydraFit</span>
            </a>
            <button class="btn-toggle" id="toggleSidebar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
        </div>

        <ul class="menu-list">
            <li class="active">
                <a href="#">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span class="link-text">Dashboard</span>
                </a>
            </li>
            </ul>

        <div class="menu-spacer"></div>

        <a href="logout.php" class="logout-link" id="btnLogout">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            <span class="link-text">Logout</span>
        </a>
    </nav>

    <main class="main-content">
        
        <header class="top-header">
            <div class="user-welcome">
                <h1>Hello, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h1>
                <p>Let's check your progress today.</p>
            </div>
            <div class="avatar" id="userAvatar">
                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
            </div>
        </header>

        <div class="dashboard-grid">
            
            <div class="left-column">
                
                <div class="card">
                    <div class="card-header">
                        <h3>Weight History</h3>
                        <span class="badge-soft">Real-time Data</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="weightChart"></canvas>
                    </div>

                    <form method="POST" action="" class="weight-input-area">
                        <input type="number" name="new_weight" id="newWeight" step="0.1" placeholder="Enter current weight (kg)" required>
                        <button type="submit" name="update_weight" class="btn-update">Update Now</button>
                    </form>
                </div>

            </div>

            <div class="right-column">
                
                <div class="card stat-card">
                    <div class="icon-box blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div class="stat-info">
                        <span class="label">Current Weight</span>
                        <span class="value" id="displayWeight"><?php echo $user['current_weight']; ?> kg</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>BMI Calculator</h3>
                    </div>
                    <div style="text-align: center;">
                        <h1 id="bmiValue" style="font-size: 3rem; color: #1e293b; margin: 10px 0;">
                            <?php echo number_format($bmi, 1); ?>
                        </h1>
                        <p id="bmiStatus" style="font-weight: 700; font-size: 1.2rem; color: <?php echo $bmi_color; ?>;">
                            <?php echo $bmi_status; ?>
                        </p>
                        <p style="color: #64748b; font-size: 0.9rem; margin-top: 5px;">
                            Based on height: <?php echo $user['height']; ?> cm
                        </p>
                    </div>
                </div>

                <div class="card tip-card">
                    <h4>💡 Daily Tip</h4>
                    <p>Consistency is key! Try to weigh yourself at the same time every day for best accuracy.</p>
                </div>

            </div>
        </div>

    </main>

    <script>
        // 1. Ambil Data dari PHP (JSON)
        const labelsPHP = <?php echo $json_labels; ?>;
        const dataPHP = <?php echo $json_data; ?>;

        // 2. Render Chart
        const ctx = document.getElementById('weightChart');

        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsPHP,
                    datasets: [{
                        label: 'Weight (kg)',
                        data: dataPHP,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.4, // Garis melengkung halus
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointRadius: 4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false } // Sembunyikan legenda biar bersih
                    },
                    scales: {
                        y: {
                            beginAtZero: false, // Biar grafik fokus ke range berat badan
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 3. Script Toggle Sidebar
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        if(toggleBtn){
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }
    </script>
</body>
</html>