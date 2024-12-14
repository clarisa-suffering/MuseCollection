<?php
// Koneksi ke database
include 'koneksi.php'; 

// Ambil parameter filter
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$nama = isset($_GET['nama']) ? $_GET['nama'] : '';

// Query dasar dengan JOIN
$query = "
    SELECT karyawan.kode_karyawan, karyawan.nama, absensi.jam, absensi.status
    FROM absensi
    INNER JOIN karyawan ON absensi.id_karyawan = karyawan.id_karyawan
    WHERE 1=1
";

// Tambahkan parameter filter
$filter_params = [];
$filter_types = '';

if (!empty($tanggal)) {
    $query .= " AND DATE(absensi.jam) = ?";
    $filter_params[] = $tanggal;
    $filter_types .= 's';
}

if (!empty($nama)) {
    $query .= " AND karyawan.nama LIKE ?";
    $filter_params[] = "%" . $nama . "%";  // Untuk pencarian berdasarkan nama
    $filter_types .= 's';
}

// Siapkan dan eksekusi query
$stmt = $conn->prepare($query);

if ($filter_params) {
    $stmt->bind_param($filter_types, ...$filter_params);
}

$stmt->execute();
$result = $stmt->get_result();

// Format data ke JSON
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Tutup koneksi
$stmt->close();
$conn->close();

// Kirim data ke frontend
header('Content-Type: application/json');
echo json_encode($data);
?>