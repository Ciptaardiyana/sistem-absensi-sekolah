<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
require_once '../../helpers/middleware.php';

// 1. Cek Security (Harus Login)
$userData = cekToken(); 

// 2. Logika Ambil Data
$query = mysqli_query($koneksi, "SELECT id, nama_lengkap, kelas FROM users WHERE role='siswa' ORDER BY kelas ASC");
$data = [];

while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

jsonResponse('success', 'Data Siswa Berhasil Diambil', [
    'request_by' => $userData['id'], // Bukti token bekerja
    'total' => count($data),
    'siswa' => $data
]);
?>