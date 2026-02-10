<?php
session_start();
header('Content-Type: application/json');
include 'koneksi.php';

// Cek Login (Security Layer)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// ---  AMBIL DATA (GET) ---
if ($method === 'GET') {
    $range = isset($_GET['range']) ? $_GET['range'] : '1W';
    $limit_sql = "";

    // Logika Timeframe (Safe karena nilainya di-hardcode di script kita)
    if ($range === '1W') {
        $limit_sql = "LIMIT 20";
    } elseif ($range === '1M') {
        $limit_sql = "LIMIT 60";
    } else {
        $limit_sql = "LIMIT 500";
    }

    // QUERY AMAN DENGAN PREPARED STATEMENT 🛡️
    // Kita bind $user_id agar aman dari injeksi
    $query = "
        SELECT * FROM (
            SELECT weight, recorded_at 
            FROM weight_tracking 
            WHERE user_id = ? 
            ORDER BY recorded_at DESC 
            $limit_sql
        ) AS sub
        ORDER BY recorded_at ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id); // Bind user_id sebagai integer
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];

    while ($row = $result->fetch_assoc()) {
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
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['weight'])) {
        $new_weight = floatval($input['weight']);

        if ($new_weight > 0) {
            
            // 1. INSERT HISTORY (SECURE) 🛡️
            $stmt_hist = $conn->prepare("INSERT INTO weight_tracking (user_id, weight) VALUES (?, ?)");
            $stmt_hist->bind_param("id", $user_id, $new_weight); // "id" = Integer, Double
            $stmt_hist->execute();

            // 2. UPDATE USER CURRENT WEIGHT (SECURE) 
            $stmt_upd = $conn->prepare("UPDATE users SET current_weight = ? WHERE id = ?");
            $stmt_upd->bind_param("di", $new_weight, $user_id); // "di" = Double, Integer
            $stmt_upd->execute();

            // --- HITUNG BMI BARU ---
            // AMBIL TINGGI BADAN (SECURE) 
            $stmt_user = $conn->prepare("SELECT height FROM users WHERE id = ?");
            $stmt_user->bind_param("i", $user_id);
            $stmt_user->execute();
            $res_user = $stmt_user->get_result();
            $d_user = $res_user->fetch_assoc();
            
            $height = $d_user['height'];

            $bmi_score = 0;
            $bmi_status = "No Data";
            $bmi_color = "#64748b";

            if ($height > 0) {
                $height_m = $height / 100;
                $bmi_score = $new_weight / ($height_m * $height_m);
                $bmi_score = number_format($bmi_score, 1);

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