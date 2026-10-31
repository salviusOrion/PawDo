<?php
$host     = 'aws-0-ap-southeast-1.pooler.supabase.com';
$port     = '6543';
$dbname   = 'postgres';
$user     = 'postgres.xdoqssyvgcfhdwvegexu';
$password = 'KheyLisKa001021032';

try {
    // Koneksi menggunakan Transaction Pooler Supabase
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Selamat! PHP berhasil terhubung ke Supabase PawDo 🎉";
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>