<?php
require_once 'Koneksi.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$judul = $input['judul'] ?? '';
$deskripsi = $input['deskripsi'] ?? '';

if (empty(trim($judul))) {
    echo json_encode(['status' => 'error', 'message' => 'Judul tugas wajib diisi!']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description) VALUES (:title, :description)");
    $stmt->execute(['title' => $judul, 'description' => $deskripsi]);

    echo json_encode(['status' => 'success', 'message' => 'Tugas berhasil disimpan!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>