<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
require_once '../../helpers/middleware.php';
use Rakit\Validation\Validator;

// 1. Cek Security (Harus Login & Harus Guru)
$userData = cekToken();
if($userData['role'] != 'guru') {
    jsonResponse('error', 'Hanya Guru yang boleh absen', null, 403);
}

// 2. Ambil Input JSON
$input = json_decode(file_get_contents('php://input'), true);

// 3. Validasi
$validator = new Validator;
$validation = $validator->make($input, [
    'user_id'    => 'required|numeric',
    'jadwal_id'  => 'required|numeric',
    'keterangan' => 'required|in:Hadir,Sakit,Izin,Alpha',
    'tanggal'    => 'required|date'
]);

$validation->validate();
if ($validation->fails()) {
    jsonResponse('fail', 'Data Absen Tidak Valid', $validation->errors()->firstOfAll(), 400);
}

// 4. Simpan ke Database
$stmt = $koneksi->prepare("INSERT INTO absensi (user_id, jadwal_id, tanggal, keterangan) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $input['user_id'], $input['jadwal_id'], $input['tanggal'], $input['keterangan']);

if ($stmt->execute()) {
    jsonResponse('success', 'Absensi Berhasil Disimpan');
} else {
    jsonResponse('error', 'Gagal Simpan Database');
}
?>