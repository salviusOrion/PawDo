<?php
session_start();
require_once 'Koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized / Belum Login']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT p.id as user_id, p.username, up.pet_name, up.current_stage_id, up.current_xp 
        FROM profiles p
        LEFT JOIN user_pets up ON p.id = up.user_id
        WHERE p.id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => [
            'user_id'   => (int)($data['user_id'] ?? 0),
            'username'  => $data['username'] ?? '',
            'pet_name'  => $data['pet_name'] ?? '',
            'rank_id'   => (int)($data['current_stage_id'] ?? 1),
            'xp'        => (int)($data['current_xp'] ?? 0)
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>