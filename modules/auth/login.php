<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
use Firebase\JWT\JWT;
use Rakit\Validation\Validator;

// 1. Ambil Input JSON
$input = json_decode(file_get_contents('php://input'), true);

// 2. Validasi Input (Library Rakit)
$validator = new Validator;
$validation = $validator->make($input, [
    'username' => 'required',
    'password' => 'required'
]);
$validation->validate();

if ($validation->fails()) {
    jsonResponse('fail', 'Input Data Tidak Valid', $validation->errors()->firstOfAll(), 400);
}

// 3. Cek User di Database (Prepared Statement - Anti SQL Injection)
$username = $input['username'];
$password = $input['password'];

$stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// 4. Verifikasi Password & Generate Token JWT
// (Catatan: Di DB idealnya password sudah di-hash pakai password_hash)
if ($user && $password == $user['password']) { // Ubah jadi password_verify($password, $user['password']) jika sudah di-hash
    
    $payload = [
        'iss' => 'localhost',
        'iat' => time(),
        'exp' => time() + (60 * 60), // Token expired 1 jam
        'data' => [
            'id' => $user['id'],
            'role' => $user['role']
        ]
    ];

    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    jsonResponse('success', 'Login Berhasil', ['token' => $jwt, 'role' => $user['role']]);
} else {
    jsonResponse('error', 'Username atau Password Salah', null, 401);
}
?>