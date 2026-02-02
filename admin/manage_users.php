<?php
session_start();
include '../koneksi.php';

// 1. CEK ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$current_page = 'manage_users';
$swal_type = ""; $swal_title = ""; $swal_text = "";

// ============================================================
// 🛡️ KONFIGURASI SUPER ADMIN
// ============================================================
$super_admins = [
    'drcswpyt01@gmail.com', 
];

// --- FUNGSI BANTUAN: Cek apakah target adalah Super Admin? ---
function isSuperAdmin($email, $list) {
    return in_array($email, $list);
}


// --- LOGIC 1: UPDATE ROLE USER ---
if (isset($_POST['update_role'])) {
    $user_id  = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    // Ambil Email Target dulu dari Database untuk dicek
    $stmt_cek = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt_cek->bind_param("i", $user_id);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();
    $data_target = $res_cek->fetch_assoc();

    // VALIDASI KEAMANAN BERLAPIS
    if ($user_id == $_SESSION['user_id']) {
        // 1. Gak boleh ubah role diri sendiri
        $swal_type = "error"; $swal_title = "Action Denied"; $swal_text = "You cannot change your own role!";
    } elseif (isSuperAdmin($data_target['email'], $super_admins)) {
        // 2. Gak boleh ubah role Super Admin
        $swal_type = "error"; $swal_title = "Protected Account"; $swal_text = "This user is a Super Admin and cannot be modified!";
    } else {
        // LOLOS VALIDASI -> JALANKAN UPDATE
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        
        if ($stmt->execute()) {
            $swal_type = "success"; $swal_title = "Role Updated"; $swal_text = "User role has been changed.";
        } else {
            $swal_type = "error"; $swal_title = "Failed"; $swal_text = $conn->error;
        }
    }
}

// --- LOGIC 2: DELETE USER ---
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Ambil Email Target dulu
    $stmt_cek = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt_cek->bind_param("i", $user_id);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();
    $data_target = $res_cek->fetch_assoc();

    if ($user_id == $_SESSION['user_id']) {
        $swal_type = "error"; $swal_title = "Action Denied"; $swal_text = "You cannot delete your own account!";
    } elseif (isSuperAdmin($data_target['email'], $super_admins)) {
        $swal_type = "error"; $swal_title = "Protected Account"; $swal_text = "You cannot delete a Super Admin!";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if($stmt->execute()){
            header("Location: manage_users.php?deleted=1");
            exit();
        }
    }
}

if(isset($_GET['deleted'])){
    $swal_type = "success"; $swal_title = "Deleted"; $swal_text = "User has been removed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/manage_users.css">
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
            <li><a href="manage_course.php"><span class="link-text">Manage Courses</span></a></li>
            <li class="active"><a href="manage_users.php"><span class="link-text">Manage Users</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../logout.php" class="logout-link"><span class="link-text">Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>Manage Users</h1>
                <p style="color:#64748b;">View, manage roles, or remove users.</p>
            </div>
            <div class="user-search-box">
                <input type="text" id="userSearch" class="search-input" placeholder="Search by name or email...">
            </div>
        </div>

        <div class="card">
            <table class="user-table">
                <thead>
                    <tr>
                        <th width="300">User Profile</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM users ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $initial = strtoupper(substr($row['username'], 0, 1));
                            
                            // Cek Status User
                            $is_me = ($row['id'] == $_SESSION['user_id']);
                            $is_protected = isSuperAdmin($row['email'], $super_admins);
                            ?>
                            
                            <tr class="user-row">
                                <td>
                                    <div class="user-profile">
                                        <div class="avatar-circle"><?php echo $initial; ?></div>
                                        <div class="user-details">
                                            <h4 class="user-name">
                                                <?php echo htmlspecialchars($row['username']); ?> 
                                                <?php 
                                                    if($is_me) echo "<span style='color:#2563eb; font-size:0.7rem;'>(You)</span>"; 
                                                    // Tambah ikon mahkota/bintang kalo super admin
                                                    if($is_protected) echo " <span title='Super Admin'>👑</span>";
                                                ?>
                                            </h4>
                                            <span class="user-email"><?php echo htmlspecialchars($row['email']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <?php if ($is_protected): ?>
                                        <span class="badge" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fcd34d;">
                                            Super Admin
                                        </span>
                                    <?php elseif ($is_me): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <form action="" method="POST" class="role-form">
                                            <input type="hidden" name="update_role" value="1">
                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                            
                                            <select name="role" class="role-select" onchange="this.form.submit()">
                                                <option value="user" <?php if($row['role']=='user') echo 'selected'; ?>>User</option>
                                                <option value="admin" <?php if($row['role']=='admin') echo 'selected'; ?>>Admin</option>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <span style="color:#64748b; font-size:0.9rem;">
                                        <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <?php if ($is_protected || $is_me): ?>
                                        <span style="color:#cbd5e1; font-size:0.8rem; font-weight:500;">Protected</span>
                                    <?php else: ?>
                                        <button onclick="confirmDeleteUser(<?php echo $row['id']; ?>)" class="btn-action btn-delete">Delete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No users found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script src="../assets/js/manage_users.js"></script>

    <script>
        <?php if (!empty($swal_type)): ?>
            Swal.fire({
                icon: '<?php echo $swal_type; ?>',
                title: '<?php echo $swal_title; ?>',
                text: '<?php echo $swal_text; ?>',
                showConfirmButton: false,
                timer: 2000
            });
        <?php endif; ?>
    </script>

</body>
</html>