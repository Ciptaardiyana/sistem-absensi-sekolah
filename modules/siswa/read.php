<?php
require_once '../../config/database.php';
require_once '../../helpers/response.php';
require_once '../../helpers/middleware.php';

cekToken(); // Wajib Login

$q = mysqli_query($koneksi, "SELECT id, nama_lengkap, kelas FROM users WHERE role='siswa'");
$data = [];
while($r = mysqli_fetch_assoc($q)) $data[] = $r;

jsonResponse('success', 'Data Siswa', $data);
?>