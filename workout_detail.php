<?php
session_start();
include 'koneksi.php';

// 1. CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. TANGKAP ID DARI URL
if (!isset($_GET['id'])) {
    header("Location: course.php");
    exit();
}
$course_id = $_GET['id'];

// 3. AMBIL DATA COURSE (HEADER)
$query_course = "SELECT * FROM courses WHERE id = '$course_id'";
$result_course = mysqli_query($conn, $query_course);
$course = mysqli_fetch_assoc($result_course);

// Kalau course tidak ditemukan
if (!$course) {
    echo "<script>alert('Course not found!'); window.location='course.php';</script>";
    exit();
}

// --- LOGIC BANNER (BARU!) ---
// Kalau kolom 'banner' ada isinya, pakai banner. Kalau kosong, pakai thumbnail.
$bg_image = !empty($course['banner']) ? $course['banner'] : $course['thumbnail'];

// 4. AMBIL DATA EXERCISES (ISI GERAKAN)
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
    <link rel="stylesheet" href="assets/css/dashboard.css">
    
    <style>
        /* STYLE HALAMAN DETAIL */
        
        /* Header Banner Dynamic Background */
.workout-header {
    /* KEMBALIKAN KE TENGAH (CENTER) */
    /* Biar fokus ke "Safe Area" di tengah gambar */
    background-position: center center; 
    
    background-size: cover;
    min-height: 300px; /* Settingan aman kamu tadi */
    
    /* Sisanya sama... */
    color: white;
    padding: 4rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    position: relative;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
}

        .btn-back {
            position: absolute;
            top: 20px; left: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.85rem;
            backdrop-filter: blur(5px);
            transition: 0.3s;
            z-index: 10;
        }
        .btn-back:hover { background: rgba(255,255,255,0.4); }

        .header-content {
            position: relative;
            z-index: 5;
            max-width: 600px;
        }

        .header-content h1 { 
            font-size: 2.5rem; /* Judul lebih besar */
            margin-bottom: 10px; 
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .header-content p { 
            color: #e2e8f0; 
            font-size: 1.1rem; 
            margin-bottom: 15px;
        }
        
        .badges { display: flex; gap: 10px; flex-wrap: wrap; }
        .badge { 
            background: #2563eb; color: white;
            padding: 6px 14px; border-radius: 50px; 
            font-size: 0.85rem; font-weight: 600; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* List Gerakan */
        .exercise-list { display: flex; flex-direction: column; gap: 1.5rem; }

        .exercise-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: 0.2s;
        }
        .exercise-card:hover { transform: translateX(5px); border-color: #cbd5e1; }

        .gif-container {
            width: 150px;
            height: 150px;
            flex-shrink: 0;
            background-color: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
        }
        .gif-container img { width: 100%; height: 100%; object-fit: cover; }

        .exercise-info { flex-grow: 1; }
        .exercise-info h3 { font-size: 1.2rem; color: #0f172a; margin-bottom: 0.5rem; }
        
        .rep-badge {
            display: inline-block;
            background: #eff6ff; color: #2563eb;
            padding: 4px 10px; border-radius: 20px;
            font-size: 0.75rem; font-weight: 700;
            margin-bottom: 10px;
        }
        
        .desc-text { 
            color: #475569; 
            line-height: 1.6; 
            font-size: 0.95rem; 
            white-space: pre-line;
        }

        @media (max-width: 768px) {
            .exercise-card { flex-direction: column; }
            .gif-container { width: 100%; height: 200px; }
            .header-content h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <div class="logo-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
                <span class="logo-text">HydraFit</span>
            </a>
            <button class="btn-toggle" id="sidebarToggle"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5"/></svg></button>
        </div>
        <ul class="menu-list">
            <li><a href="dashboard.php"><span class="link-text">Dashboard</span></a></li>
            <li class="active"><a href="course.php"><span class="link-text">Course</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-link"><span class="link-text">Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="workout-header">
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
                <a href="course.php" onclick="return alert('Good job! Workout Completed! 🔥')" style="background: #22c55e; color: white; padding: 15px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);">
                    🎉 I'm Finished!
                </a>
            </div>
        <?php endif; ?>

    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>