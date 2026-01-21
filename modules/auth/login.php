<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
use Firebase\JWT\JWT;

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'];
$password = $input['password'];

// Cek User
$q = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
$user = mysqli_fetch_assoc($q);

if ($user) {
    // Bikin Token JWT
    $payload = [
        'iss' => 'localhost',
        'iat' => time(),
        'exp' => time() + 3600, // 1 Jam
        'data' => ['id' => $user['id'], 'role' => $user['role']]
    ];
    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    
    jsonResponse('success', 'Login Berhasil', ['token' => $jwt, 'role' => $user['role']]);
} else {
    jsonResponse('error', 'Username/Password Salah', null, 401);
}
?>