<?php
session_start();
header('Content-Type: application/json');
include 'koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// HANYA TERIMA POST
if ($method === 'POST') {

    // --- UPDATE PROFILE (Foto, Nama, Tinggi, Target) ---
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {

        $first_name = $_POST['first_name'] ?? '';
        $last_name  = $_POST['last_name'] ?? '';
        $height     = $_POST['height'] ?? 0;
        $target     = $_POST['target_weight'] ?? 0;
        $username = trim($_POST['username'] ?? ''); // trim itu biar spasi depan/belakang hilang

        // CEK USERNAME UNIK 
        // cek Ada gak ada user LAIN yang punya username sama
        $cek = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $cek->bind_param("si", $username, $user_id);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username is already taken!']);
            exit(); // Stop proses
        }

        // LOGIC UPLOAD KE IMGBB 
        $avatar_url = null; // Default null kalau gak upload

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $api_key = 'a16527c1858fe40089a90d1f2f4fca8d';
            $image_path = $_FILES['avatar']['tmp_name'];

            // Baca file gambar jadi binary
            $img_data = file_get_contents($image_path);

            // Kirim ke ImgBB pakai cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload?key=' . $api_key);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['image' => base64_encode($img_data)]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            // Jika sukses upload, ambil URL-nya
            if (isset($result['data']['url'])) {
                $avatar_url = $result['data']['url'];
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal upload ke ImgBB']);
                exit();
            }
        }

        // UPDATE DATABASE 
        // Cek dulu apakah user upload foto baru atau tidak
        if ($avatar_url) {
            // 
            // Skenario pertama, Kalau ada foto baru, update kolom avatar juga

            $stmt = $conn->prepare("UPDATE users SET username=?, first_name=?, last_name=?, height=?, target_weight=?, avatar=? WHERE id=?");
            $stmt->bind_param("sssddsi", $username, $first_name, $last_name, $height, $target, $avatar_url, $user_id);
        } else {
            // Skenario kedua,  Kalau gak ada foto, update data teks aja (biar foto lama gak ilang/ke-reset)
            $stmt = $conn->prepare("UPDATE users SET username=?, first_name=?, last_name=?, height=?, target_weight=? WHERE id=?");
            $stmt->bind_param("sssddi", $username, $first_name, $last_name, $height, $target, $user_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Profil berhasil diupdate!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
        exit();
    }

    // --- GANTI PASSWORD ---
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current = $_POST['current_password'];
        $new     = $_POST['new_password'];

        // Ambil password lama dari DB
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = $res->fetch_assoc();

        // Verifikasi password lama
        if (password_verify($current, $data['password'])) {
            // Hash password baru
            $new_hash = password_hash($new, PASSWORD_DEFAULT);

            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->bind_param("si", $new_hash, $user_id);

            if ($upd->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Password berhasil diganti!']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Password lama salah!']);
        }
        exit();
    }

    // ---DELETE ACCOUNT ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        // Hapus data terkait dulu (Foreign Key) biar bersih
        $conn->query("DELETE FROM weight_tracking WHERE user_id = $user_id");

        // Hapus user
        $del = $conn->prepare("DELETE FROM users WHERE id = ?");
        $del->bind_param("i", $user_id);

        if ($del->execute()) {
            session_destroy(); // Hancurkan sesi login
            echo json_encode(['status' => 'success', 'message' => 'Akun dihapus. Bye!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus akun.']);
        }
        exit();
    }
}
