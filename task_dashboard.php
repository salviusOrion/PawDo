<?php
require_once 'Koneksi.php';
header('Content-Type: application/json');

try {
    // Ambil SEMUA tugas (baik yang belum selesai maupun yang sudah)
    // Diurutkan dari yang paling baru dibuat
    $stmtTask = $pdo->query("SELECT id, title, description, status, created_at FROM tasks ORDER BY created_at DESC");
    $tasks = $stmtTask->fetchAll(PDO::FETCH_ASSOC);

    // Kirim data ke JS
    echo json_encode([
        'status' => 'success',
        'tasks' => $tasks
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>