<?php
session_start();
require_once 'Koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Belum Login']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$title = trim($input['title'] ?? '');

if (empty($title)) {
    echo json_encode(['status' => 'error', 'message' => 'Judul tugas tidak boleh kosong']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO tasks (title, status, marked, user_id) VALUES (:title, 'pending', false, :user_id)");
    $stmt->execute([
        'title'   => $title,
        'user_id' => $_SESSION['user_id']
    ]);

    echo json_encode([
        'status'  => 'success',
        'id'      => $pdo->lastInsertId(),
        'message' => 'Tugas berhasil ditambahkan'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>