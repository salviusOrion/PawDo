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
$id = $input['id'] ?? null;
$status = $input['status'] ?? 'completed'; // 'completed' atau 'pending'

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID Tugas tidak ditemukan']);
    exit;
}

try {
    // Update status tugas milik user yang sedang login
    $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'status'  => $status,
        'id'      => $id,
        'user_id' => $_SESSION['user_id']
    ]);

    // Jika diselesaikan, tambahkan XP pet user (+10 XP per tugas selesai)
    if ($status === 'completed') {
        $xpStmt = $pdo->prepare("UPDATE user_pets SET current_xp = current_xp + 10 WHERE user_id = :user_id");
        $xpStmt->execute(['user_id' => $_SESSION['user_id']]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Status tugas berhasil diperbarui']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
