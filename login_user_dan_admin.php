<?php
// Wajib nyalakan session di baris paling atas
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
    // 1. Cari user di tabel profiles
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM profiles WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verifikasi password hash
    if ($user && password_verify($password, $user['password'])) {
        
        // 3. Simpan data login ke Session PHP
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        // 4. Kirim respon sukses beserta data role ke Frontend
        echo json_encode([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'data' => [
                'user_id'  => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'] // 'user' atau 'admin'
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