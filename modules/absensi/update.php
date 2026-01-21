<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
require_once '../../helpers/middleware.php';
use Rakit\Validation\Validator;

// 1. Cek Token & Role (Hanya Guru)
$userData = cekToken();
if($userData['role'] !== 'guru') {
    jsonResponse('error', 'Akses Ditolak: Hanya Guru yang boleh edit!', null, 403);
}

// 2. Ambil Input JSON
$input = json_decode(file_get_contents('php://input'), true);

// 3. Validasi
$validator = new Validator;
$validation = $validator->make($input, [
    'id'         => 'required|numeric', // ID Absensi (Bukan ID Siswa)
    'keterangan' => 'required|in:Hadir,Sakit,Izin,Alpha'
]);

$validation->validate();

if ($validation->fails()) {
    jsonResponse('fail', 'Data Tidak Valid', $validation->errors()->firstOfAll(), 400);
}

// 4. Cek apakah data absennya ada?
$id_absen = $input['id'];
$cek = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id = '$id_absen'");
if(mysqli_num_rows($cek) == 0){
    jsonResponse('error', 'Data Absensi tidak ditemukan', null, 404);
}

// 5. Update Database
$stmt = $koneksi->prepare("UPDATE absensi SET keterangan = ? WHERE id = ?");
$stmt->bind_param("si", $input['keterangan'], $id_absen);

if ($stmt->execute()) {
    jsonResponse('success', 'Data Absensi Berhasil Diupdate');
} else {
    jsonResponse('error', 'Gagal Update Database');
}
?>