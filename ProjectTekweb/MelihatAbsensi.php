<?php
// Koneksi ke database
include 'koneksi.php'; 

// Ambil parameter filter
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$id_karyawan = isset($_GET['id_karyawan']) ? $_GET['id_karyawan'] : '';

// Query dasar
$query = "
    SELECT id_absensi, id_karyawan, jam, status 
    FROM absensi
    WHERE 1=1
";


// Tambahkan parameter filter
$filter_params = [];
$filter_types = '';

if (!empty($tanggal)) {
    $query .= " AND DATE(jam) = ?";
    $filter_params[] = $tanggal;
    $filter_types .= 's';
}

if (!empty($id_karyawan)) {
    $query .= " AND id_karyawan = ?";
    $filter_params[] = $id_karyawan;
    $filter_types .= 'i'; // 'i' untuk tipe integer
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
