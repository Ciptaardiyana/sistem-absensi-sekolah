<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$koneksi = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);

if (!$koneksi) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Koneksi DB Gagal"]);
    exit;
}
?>