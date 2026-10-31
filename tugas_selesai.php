<?php
require_once 'Koneksi.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$task_id = $input['task_id'] ?? null;
$user_id = $input['user_id'] ?? null;

if (!$task_id || !$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Mark tugas sebagai selesai
    $stmtTask = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = :task_id");
    $stmtTask->execute(['task_id' => $task_id]);

    // 2. Tambah 10 XP ke pet user
    $stmtPet = $pdo->prepare("UPDATE user_pets SET xp = xp + 10 WHERE user_id = :user_id");
    $stmtPet->execute(['user_id' => $user_id]);

    // 3. Ambil data XP dan Rank terbaru untuk dicek
    $stmtCheck = $pdo->prepare("SELECT xp, stage, rank_id FROM user_pets WHERE user_id = :user_id");
    $stmtCheck->execute(['user_id' => $user_id]);
    $pet = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    $is_naik_rank = false;
    $rank_baru = "";

    // Contoh Logika Evolusi & Naik Rank Otomatis:
    // Jika XP sudah mencapai 400 (Dewasa Maksimal), Naik Rank dan Reset ke Telur Baru!
    if ($pet['xp'] >= 400) {
        $is_naik_rank = true;
        
        // Query UPDATE untuk naik ke rank_id berikutnya dan reset XP ke 0 (Telur baru)
        $stmtRank = $pdo->prepare("UPDATE user_pets SET rank_id = rank_id + 1, xp = 0, stage = 'Telur' WHERE user_id = :user_id");
        $stmtRank->execute(['user_id' => $user_id]);
        
        $rank_baru = "HUNTER"; // Contoh nama rank baru untuk pop-up
    }

    $pdo->commit();

    // Kirim respon lengkap ke JS (supaya Frontend tahu kapan harus memicu Pop-up Selamat Naik Rank!)
    echo json_encode([
        'status' => 'success',
        'message' => 'Tugas selesai! +10 XP',
        'xp_sekarang' => $pet['xp'] + 10,
        'naik_rank' => $is_naik_rank,
        'rank_baru' => $rank_baru
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>