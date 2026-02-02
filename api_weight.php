<?php
session_start();
header('Content-Type: application/json'); // Wajib biar JS tau ini data JSON
include 'koneksi.php';

// Cek Login (Security Layer)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// ---  AMBIL DATA (GET) ---
// Dipanggil saat chart mau digambar atau ganti timeframe
if ($method === 'GET') {
    $range = isset($_GET['range']) ? $_GET['range'] : '1W';
    $limit_sql = "";

    // Logika Timeframe Sederhana (Limit Data)
    if ($range === '1W') {
        $limit_sql = "LIMIT 20"; // Ambil 20 data terakhir
    } elseif ($range === '1M') {
        $limit_sql = "LIMIT 60"; // Ambil 60 data terakhir
    } else {
        $limit_sql = "LIMIT 500"; // ALL (Maksimal 500 biar gak berat)
    }

    // Ambil history berat badan user ini
    // diurutkan dari yang terlama ke terbaru (ASC) biar grafiknya nyambung
    // Note:gunakan subquery agar LIMIT bekerja pada data terbaru, lalu diurutkan ulang
    $query = "
        SELECT * FROM (
            SELECT weight, recorded_at 
            FROM weight_tracking 
            WHERE user_id = '$user_id' 
            ORDER BY recorded_at DESC 
            $limit_sql
        ) AS sub
        ORDER BY recorded_at ASC
    ";
    
    $result = mysqli_query($conn, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        // Format tanggal biar cantik di grafik (Contoh: 20 Jan, 14:30)
        $date = date('d M, H:i', strtotime($row['recorded_at']));
        $data[] = [
            'label' => $date,
            'value' => $row['weight']
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
    exit();
}
// --- UPDATE BERAT (POST) ---
if ($method === 'POST') {
    // Ambil data JSON yang dikirim JS
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['weight'])) {
        $new_weight = floatval($input['weight']);

        if ($new_weight > 0) {
            // Masukkan ke tabel history
            $query_history = "INSERT INTO weight_tracking (user_id, weight) VALUES ('$user_id', '$new_weight')";
            mysqli_query($conn, $query_history);

            // Update berat saat ini di tabel users
            $query_update = "UPDATE users SET current_weight = '$new_weight' WHERE id = '$user_id'";
            mysqli_query($conn, $query_update);

            // ---HITUNG BMI BARU (LOGIKA TAMBAHAN) ---
            // Ambil tinggi badan user untuk hitung ulang BMI
            $q_user = mysqli_query($conn, "SELECT height FROM users WHERE id = '$user_id'");
            $d_user = mysqli_fetch_assoc($q_user);
            $height = $d_user['height'];

            $bmi_score = 0;
            $bmi_status = "No Data";
            $bmi_color = "#64748b";

            if ($height > 0) {
                $height_m = $height / 100;
                $bmi_score = $new_weight / ($height_m * $height_m);
                $bmi_score = number_format($bmi_score, 1); // Format 1 desimal

                if ($bmi_score < 18.5) {
                    $bmi_status = "Underweight";
                    $bmi_color = "#3b82f6";
                } elseif ($bmi_score >= 18.5 && $bmi_score < 24.9) {
                    $bmi_status = "Normal";
                    $bmi_color = "#22c55e";
                } elseif ($bmi_score >= 25 && $bmi_score < 29.9) {
                    $bmi_status = "Overweight";
                    $bmi_color = "#f97316";
                } else {
                    $bmi_status = "Obesity";
                    $bmi_color = "#ef4444";
                }
            }

            // Kirim balasan lengkap (Status + Data BMI Baru)
            echo json_encode([
                'status' => 'success',
                'message' => 'Berat berhasil diupdate!',
                'new_bmi' => [
                    'score' => $bmi_score,
                    'status' => $bmi_status,
                    'color' => $bmi_color
                ]
            ]);

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Berat tidak valid!']);
        }
    }
    exit();
}
?>