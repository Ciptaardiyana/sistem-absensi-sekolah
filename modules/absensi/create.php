<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
require_once '../../helpers/middleware.php';
use Rakit\Validation\Validator;

// Cek Token & Role Guru
$user = cekToken();
if($user['role'] != 'guru') jsonResponse('error', 'Hanya Guru!', null, 403);

$input = json_decode(file_get_contents('php://input'), true);

// Validasi Library Rakit
$validator = new Validator;
$validation = $validator->make($input, [
    'user_id' => 'required|numeric',
    'keterangan' => 'required|in:Hadir,Sakit,Izin,Alpha',
    'tanggal' => 'required|date'
]);
$validation->validate();

if ($validation->fails()) {
    jsonResponse('fail', 'Data Salah', $validation->errors()->firstOfAll(), 400);
}

// Simpan ke DB
$stmt = $koneksi->prepare("INSERT INTO absensi (user_id, jadwal_id, tanggal, keterangan) VALUES (?, 1, ?, ?)");
$stmt->bind_param("iss", $input['user_id'], $input['tanggal'], $input['keterangan']);

if($stmt->execute()) jsonResponse('success', 'Absen Tersimpan');
else jsonResponse('error', 'Gagal Simpan');
?>