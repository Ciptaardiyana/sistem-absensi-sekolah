<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
require_once '../../helpers/middleware.php';
use Rakit\Validation\Validator;

// 1. Cek Token & Role (Hanya Guru)
$userData = cekToken();
if($userData['role'] !== 'guru') {
    jsonResponse('error', 'Akses Ditolak: Hanya Guru yang boleh hapus!', null, 403);
}

// 2. Ambil Input JSON
$input = json_decode(file_get_contents('php://input'), true);

// 3. Validasi
$validator = new Validator;
$validation = $validator->make($input, [
    'id' => 'required|numeric' // ID Absensi yang mau dihapus
]);

$validation->validate();

if ($validation->fails()) {
    jsonResponse('fail', 'ID Absensi Wajib Diisi', $validation->errors()->firstOfAll(), 400);
}

// 4. Cek apakah data ada?
$id_absen = $input['id'];
$cek = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id = '$id_absen'");
if(mysqli_num_rows($cek) == 0){
    jsonResponse('error', 'Data Absensi tidak ditemukan', null, 404);
}

// 5. Hapus dari Database
$stmt = $koneksi->prepare("DELETE FROM absensi WHERE id = ?");
$stmt->bind_param("i", $id_absen);

if ($stmt->execute()) {
    jsonResponse('success', 'Data Absensi Berhasil Dihapus');
} else {
    jsonResponse('error', 'Gagal Menghapus Data');
}
?>