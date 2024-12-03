<?php
// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'toko_baju');

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil parameter filter
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$nama_karyawan = isset($_GET['nama_karyawan']) ? $_GET['nama_karyawan'] : '';

// Query dasar dengan JOIN
$query = "
    SELECT k.nama, a.jam, a.status 
    FROM absensi a
    JOIN karyawan k ON a.id_karyawan = k.id_karyawan
    WHERE 1=1
";

// Filter berdasarkan tanggal
if (!empty($tanggal)) {
    $query .= " AND DATE(a.jam) = '$tanggal'";
}

// Filter berdasarkan nama karyawan
if (!empty($nama_karyawan)) {
    $query .= " AND k.nama LIKE '%$nama_karyawan%'";
}

// Eksekusi query
$result = $conn->query($query);

// Format data ke JSON
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Kirim data ke frontend
header('Content-Type: application/json');
echo json_encode($data);
?>
    