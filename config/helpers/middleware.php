<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function cekToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        jsonResponse('error', 'Akses Ditolak: Token tidak ditemukan', null, 401);
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);

    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return (array) $decoded->data; // Return data user (id, role)
    } catch (Exception $e) {
        jsonResponse('error', 'Akses Ditolak: Token Invalid/Expired', null, 401);
    }
}
?>