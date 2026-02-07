<?php
session_start();
include 'koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_page = 'settings'; // Penanda halaman aktif

// AMBIL DATA USER TERBARU
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Variabel data user
$first_name = $user['first_name'] ?? '';
$last_name  = $user['last_name'] ?? '';
$height     = $user['height'] ?? '';
$target_weight = $user['target_weight'] ?? '';
$avatar_url    = $user['avatar'] ?? '';
$username      = $user['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - HydraFit</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/settings.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
                <span class="logo-text">HydraFit</span>
            </a>
            <button class="btn-toggle" id="sidebarToggle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 17l-5-5 5-5M18 17l-5-5 5-5" />
                </svg>
            </button>
        </div>

        <ul class="menu-list">
            <li class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <a href="dashboard.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span class="link-text">Dashboard</span>
                </a>
            </li>

            <li class="<?php echo ($current_page == 'course') ? 'active' : ''; ?>">
                <a href="course.php">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span class="link-text">Course</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer-settings">
            <a href="settings.php" style="<?php echo ($current_page == 'settings') ? 'color: #2563eb;' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="<?php echo ($current_page == 'settings') ? '#2563eb' : '#64748b'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span class="link-text">Settings</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="logout.php" class="logout-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="link-text">Logout</span>
            </a>
        </div>

    </div>

    <div class="main-content">
        <div class="top-header">
            <div class="user-welcome">
                <h1>Account Settings ⚙️</h1>
                <p>Manage your profile and security preferences.</p>
            </div>
            <div class="avatar">
                <?php if (!empty($avatar_url)): ?>
                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                <?php else: ?>
                    <?php echo strtoupper(substr($first_name, 0, 1)); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="settings-grid">

            <div class="card setting-card">
                <div class="card-header">
                    <h3>Edit Profile</h3>
                </div>

                <form id="profileForm">
                    <div class="avatar-upload-wrapper">
                        <div class="avatar-preview-box">
                            <div id="initialAvatar" class="avatar-placeholder-large" style="<?php echo $avatar_url ? 'display:none' : ''; ?>">
                                <?php echo strtoupper(substr($first_name, 0, 1)); ?>
                            </div>
                            <img id="imagePreview" src="<?php echo $avatar_url; ?>" style="<?php echo $avatar_url ? 'display:block' : 'display:none'; ?>" alt="Profile">
                        </div>

                        <div class="upload-btn-group">
                            <label for="avatarInput" class="btn-upload">Change Photo</label>
                            <input type="file" id="avatarInput" accept="image/*" hidden>
                            <p class="text-hint">Max size 2MB (JPG/PNG)</p>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="firstName" value="<?php echo htmlspecialchars($first_name); ?>" class="input-setting">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="lastName" value="<?php echo htmlspecialchars($last_name); ?>" class="input-setting">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" id="username" value="<?php echo htmlspecialchars($username); ?>" class="input-setting" placeholder="Enter your unique Username">
                        </div>

                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Height (cm)</label>
                            <input type="number" id="height" value="<?php echo htmlspecialchars($height); ?>" class="input-setting">
                        </div>
                        <div class="form-group">
                            <label>Target Weight (kg)</label>
                            <input type="number" id="targetWeight" value="<?php echo htmlspecialchars($target_weight); ?>" class="input-setting" placeholder="Set your goal">
                        </div>
                    </div>

                    <button type="submit" class="btn-save primary">Save Changes</button>
                </form>
            </div>

            <div class="card setting-card">
                <div class="card-header">
                    <h3>Security</h3>
                </div>
                <form id="securityForm">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" id="currentPass" placeholder="••••••" class="input-setting">
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" id="newPass" placeholder="••••••" class="input-setting">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" id="confirmPass" placeholder="••••••" class="input-setting">
                        </div>
                    </div>

                    <button type="submit" class="btn-save secondary">Update Password</button>
                </form>
            </div>

            <div class="card setting-card danger-zone">
                <div class="card-header">
                    <h3 class="text-red">Danger Zone</h3>
                </div>
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                <button id="btnDeleteAccount" class="btn-save danger">Delete Account</button>
            </div>

        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/settings.js"></script>
</body>

</html>