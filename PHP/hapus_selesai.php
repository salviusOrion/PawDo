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
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE user_id = :user_id AND status = 'completed'");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $removedCount = $stmt->rowCount();

    echo json_encode([
        'status'  => 'success',
        'removed' => $removedCount
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
