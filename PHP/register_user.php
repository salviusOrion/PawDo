<?php
require_once 'Koneksi.php';
header('Content-Type: application/json');

// Tangkap input JSON dari Frontend
$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

// 1. Validasi Input
if (empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Username dan Password wajib diisi!']);
    exit;
}

try {
    // 2. Cek apakah username sudah dipakai di tabel profiles
    $stmtCek = $pdo->prepare("SELECT id FROM profiles WHERE username = :username");
    $stmtCek->execute(['username' => $username]);
    
    if ($stmtCek->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan!']);
        exit;
    }

    // 3. Enkripsi password demi keamanan
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert data user baru ke tabel profiles (role otomatis 'user' sesuai default Supabase)
    $stmt = $pdo->prepare("INSERT INTO profiles (username, password) VALUES (:username, :password)");
    $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword
    ]);

    $userIdBaru = $pdo->lastInsertId();

    // 5. Otomatis buatkan data pet awal di user_pets
    $stmtPet = $pdo->prepare("INSERT INTO user_pets (user_id, rank_id, xp, stage) VALUES (:user_id, 1, 0, 'Telur')");
    $stmtPet->execute(['user_id' => $userIdBaru]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Registrasi berhasil! Silakan login.'
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal registrasi: ' . $e->getMessage()]);
}
?>
