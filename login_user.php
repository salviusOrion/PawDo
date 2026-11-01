<?php
session_start();

require_once 'Koneksi.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Username dan password wajib diisi!']);
    exit;
}

try {
    // Cari user berdasarkan username di tabel profiles
    $stmt = $pdo->prepare("SELECT id, username, password FROM profiles WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifikasi password hash
    if ($user && password_verify($password, $user['password'])) {
        
        // Simpan data user ke Session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Respon sukses masuk ke aplikasi
        echo json_encode([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'data' => [
                'user_id'  => $user['id'],
                'username' => $user['username']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Username atau password salah!'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal login: ' . $e->getMessage()]);
}
?>