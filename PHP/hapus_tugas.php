<?php
require_once 'Koneksi.php';
header('Content-Type: application/json');

// Tangkap ID tugas yang mau dihapus dari JS
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID Tugas tidak ditemukan!']);
    exit;
}

try {
    // Query DELETE berdasarkan ID tugas
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Tugas berhasil dihapus!'
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
