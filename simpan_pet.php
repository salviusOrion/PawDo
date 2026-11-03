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
$pet_name = trim($input['pet_name'] ?? '');

if (empty($pet_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Nama pet tidak boleh kosong']);
    exit;
}

try {
    // Query ini otomatis meng-update nama pet milik user (bisa buat pertama kali / ganti nama)
    $stmt = $pdo->prepare("UPDATE user_pets SET pet_name = :pet_name WHERE user_id = :user_id");
    $stmt->execute([
        'pet_name' => $pet_name,
        'user_id'  => $_SESSION['user_id']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Nama pet berhasil diperbarui!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>