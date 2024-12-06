<?php
include 'koneksi.php';
$id_karyawan = $_GET['id'];

$result = $conn->query("SELECT nama, gaji FROM karyawan WHERE id_karyawan = $id_karyawan");
$karyawan = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Gaji Karyawan</title>
</head>
<body>
    <h2>Detail Gaji untuk: <?php echo $karyawan['nama']; ?></h2>
    <p><strong>Total Gaji:</strong> Rp <?php echo number_format($karyawan['gaji'], 0, ',', '.'); ?></p>
</body>
</html>