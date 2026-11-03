<?php
session_start();
require_once 'Koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Belum Login']);
    exit;
}

try {
    // Ambil tugas yang HANYA milik user_id dari session aktif
    $stmt = $pdo->prepare("SELECT id, title, status, marked FROM tasks WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format marked ke boolean agar sesuai kebutuhan frontend JS
    foreach ($tasks as &$task) {
        $task['marked'] = (bool)$task['marked'];
    }

    echo json_encode([
        'status' => 'success',
        'data'   => $tasks
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>