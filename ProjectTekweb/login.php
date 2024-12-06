<?php
session_set_cookie_params(0);
session_start();  // Start the session

include 'koneksi.php'; 
// header('Content-Type: application/json');

// Mendapatkan data dari frontend
$data = json_decode(file_get_contents('php://input'), true);
$inputUsername = $data['username'];
$inputPassword = $data['password'];

// Query data karyawan dari database
$query = "SELECT kode_karyawan, nama, start_date, jabatan FROM karyawan";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $isValid = false;

    while ($row = $result->fetch_assoc()) {
        // Membentuk username (nama tanpa spasi)
        $username = str_replace(' ', '', $row['nama']);

        // Membentuk password
        $kodeKaryawan = $row['kode_karyawan'];
        $startDate = $row['start_date'];
        $tahunMasuk = substr($startDate, 2, 2); // Dua digit akhir tahun
        $bulanMasuk = substr($startDate, 5, 2); // Dua digit bulan
        $password = $kodeKaryawan . $tahunMasuk . $bulanMasuk;

        // Validasi username dan password
        if ($inputUsername === $username && $inputPassword === $password) {
            $isValid = true;
            $_SESSION['jabatan'] = $row['jabatan'];
            break;
        }
    }

    if ($isValid) {
        echo json_encode(['success' => true, 'message' => 'Login berhasil!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Username atau password salah.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada data karyawan.']);
}

$conn->close();
?>
