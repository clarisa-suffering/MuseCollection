<?php
// Include koneksi database
include 'koneksi.php';

$alert = ''; // Variabel untuk menyimpan notifikasi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_karyawan = $_POST['id_karyawan'];
    $current_time = date('H:i'); // Waktu sekarang (jam:menit)
    $status = 1; // Status hadir

    // Validasi keterlambatan
    if ($current_time > '07:30') {
        $alert = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const notyf = new Notyf();
                    notyf.error('Terlambat! Hubungi pemilik untuk klarifikasi.');
                });
            </script>
        ";
    } else {
        // Query untuk mencatat absensi
        $stmt = $conn->prepare("INSERT INTO absensi (id_karyawan, jam, status) VALUES (?, NOW(), ?)");
        $stmt->bind_param("ii", $id_karyawan, $status);

        if ($stmt->execute()) {
            $alert = "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const notyf = new Notyf();
                        notyf.success('Absensi berhasil dicatat!');
                    });
                </script>
            ";
        } else {
            $alert = "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const notyf = new Notyf();
                        notyf.error('Gagal mencatat absensi: {$stmt->error}');
                    });
                </script>
            ";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" rel="stylesheet"> <!-- Notyf CSS -->
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script> <!-- Notyf JS -->
</head>
<body>
    <div class="container mt-5">
        <h2>Form Absensi Karyawan</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="id_karyawan" class="form-label">Nama Karyawan</label>
                <select class="form-select" id="id_karyawan" name="id_karyawan" required>
                    <option value="" disabled selected>Pilih Nama Karyawan</option>
                    <?php
                    // Query untuk mendapatkan daftar karyawan
                    $result = $conn->query("SELECT id_karyawan, nama FROM karyawan ORDER BY nama ASC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_karyawan']}'>{$row['nama']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Catat Absensi</button>
        </form>
    </div>

    <div class="container mt-5">
        <h2>Daftar Absensi Hari Ini</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jam Absensi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query untuk menampilkan absensi hari ini
                $today = date('Y-m-d');
                $result = $conn->query("SELECT k.nama, a.jam, a.status 
                                        FROM absensi a 
                                        JOIN karyawan k ON a.id_karyawan = k.id_karyawan 
                                        WHERE DATE(a.jam) = '$today'");
                while ($row = $result->fetch_assoc()) {
                    $status_text = $row['status'] == 1 ? 'Hadir' : 'Tidak Hadir';
                    echo "<tr>
                        <td>{$row['nama']}</td>
                        <td>{$row['jam']}</td>
                        <td>{$status_text}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Tampilkan Notifikasi -->
    <?php echo $alert; ?>
</body>
</html>
