<?php
// Include koneksi database
include 'koneksi.php';

$alert = ''; // Variabel untuk notifikasi
$details = ''; // Variabel untuk rincian perhitungan

// Fungsi untuk menampilkan detail gaji
function detailGaji($id_karyawan, $conn) {
    $result = $conn->query("SELECT k.nama, k.gaji, MIN(a.jam) AS absensi_pertama FROM karyawan k LEFT JOIN absensi a ON k.id_karyawan = a.id_karyawan WHERE k.id_karyawan = $id_karyawan GROUP BY k.id_karyawan");
    $row = $result->fetch_assoc();

    if ($row) {
        $start_date = new DateTime($row['absensi_pertama']);
        $current_date = new DateTime();
        $interval = $start_date->diff($current_date);
        $years_of_work = $interval->y;

        return [
            'nama' => $row['nama'],
            'gaji' => $row['gaji'],
            'years_of_work' => $years_of_work,
        ];
    }
    return null;
}

// Fungsi untuk menghitung gaji
function hitungGaji($id_karyawan, $conn) {
    $result = $conn->query("SELECT id_karyawan, nama, gaji, periode_terakhir FROM karyawan WHERE id_karyawan = $id_karyawan");
    $row = $result->fetch_assoc();

    if ($row) {
        // Periksa periode terakhir
        $start_date = $row['periode_terakhir'] ? new DateTime($row['periode_terakhir']) : null;
        if (!$start_date) {
            // Jika periode_terakhir kosong, gunakan absensi pertama
            $absensi_result = $conn->query("SELECT MIN(jam) AS tanggal_absensi_pertama FROM absensi WHERE id_karyawan = $id_karyawan");
            $absensi_data = $absensi_result->fetch_assoc();
            $start_date = new DateTime($absensi_data['tanggal_absensi_pertama']);
        }
        $end_date = new DateTime(); // Tanggal saat ini

        // Hitung lama kerja dalam tahun
        $interval = $start_date->diff($end_date);
        $years_of_work = $interval->y;

        // Perhitungan gaji pokok + kenaikan berdasarkan lama kerja
        $base_salary = 3000000; // Gaji pokok
        $increment = ($years_of_work > 0) ? ($years_of_work - 1) * 2000000 : 0;
        $calculated_salary = $base_salary + $increment;

        // Perhitungan bonus kehadiran
        $bonus = 0;
        $attendance_result = $conn->query("SELECT COUNT(*) AS hadir FROM absensi 
                                           WHERE id_karyawan = $id_karyawan 
                                           AND DATE(jam) BETWEEN '{$start_date->format('Y-m-d')}' AND '{$end_date->format('Y-m-d')}'");
        $attendance_data = $attendance_result->fetch_assoc();
        $attendance_count = $attendance_data['hadir'];

        // Asumsikan jumlah hari kerja dalam setahun adalah 312 (6 hari/minggu)
        $work_days = 312;
        if ($attendance_count >= $work_days) {
            $bonus = 500000;
        }
        $calculated_salary += $bonus;

        // Update gaji karyawan dan periode terakhir
        $stmt = $conn->prepare("UPDATE karyawan SET gaji = ?, periode_terakhir = CURDATE() WHERE id_karyawan = ?");
        $stmt->bind_param("ii", $calculated_salary, $id_karyawan);
        $stmt->execute();

        return [
            'nama' => $row['nama'],
            'base_salary' => $base_salary,
            'increment' => $increment,
            'bonus' => $bonus,
            'total' => $calculated_salary,
            'years_of_work' => $years_of_work,
        ];
    }

    return null;
}


// Perhitungan gaji per karyawan atau semua karyawan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'all') {
        $all_result = $conn->query("SELECT id_karyawan FROM karyawan");
        while ($row = $all_result->fetch_assoc()) {
            hitungGaji($row['id_karyawan'], $conn);
        }
        $alert = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire('Berhasil!', 'Gaji semua karyawan berhasil dihitung!', 'success');
                });
            </script>
        ";
    } elseif (isset($_POST['action']) && $_POST['action'] === 'detail') {
        $id_karyawan = $_POST['id_karyawan'];
        $detail = detailGaji($id_karyawan, $conn);
        if ($detail) {
            $details = "
                <div class='alert alert-secondary mt-3'>
                    <h5>Detail Gaji untuk <strong>{$detail['nama']}</strong></h5>
                    <ul>
                        <li><strong>Total Gaji:</strong> Rp " . number_format($detail['gaji'], 0, ',', '.') . "</li>
                        <li><strong>Lama Kerja:</strong> {$detail['years_of_work']} tahun</li>
                    </ul>
                </div>
            ";
        }
    } elseif (isset($_POST['id_karyawan'])) {
        $id_karyawan = $_POST['id_karyawan'];
        $rincian = hitungGaji($id_karyawan, $conn);
        if ($rincian) {
            $details = "
                <div class='alert alert-info mt-3'>
                    <h5>Rincian Perhitungan Gaji untuk <strong>{$rincian['nama']}</strong></h5>
                    <ul>
                        <li><strong>Gaji Pokok:</strong> Rp " . number_format($rincian['base_salary'], 0, ',', '.') . "</li>
                        <li><strong>Kenaikan Gaji:</strong> Rp " . number_format($rincian['increment'], 0, ',', '.') . "</li>
                        <li><strong>Bonus Kehadiran:</strong> Rp " . number_format($rincian['bonus'], 0, ',', '.') . "</li>
                        <li><strong>Total Gaji:</strong> Rp " . number_format($rincian['total'], 0, ',', '.') . "</li>
                        <li><strong>Lama Kerja:</strong> {$rincian['years_of_work']} tahun</li>
                    </ul>
                </div>
            ";
            $alert = "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire('Berhasil!', 'Gaji untuk {$rincian['nama']} berhasil dihitung!', 'success');
                    });
                </script>
            ";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perhitungan Gaji Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Daftar Karyawan</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="all">
            <button type="submit" class="btn btn-success mb-3">Hitung Gaji Semua Karyawan</button>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Gaji</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $result = $conn->query("SELECT k.id_karyawan, k.nama, k.gaji, k.periode_terakhir FROM karyawan k");
    while ($row = $result->fetch_assoc()) {
        // Gunakan periode_terakhir untuk menentukan status
        $periode_terakhir = $row['periode_terakhir'] ? new DateTime($row['periode_terakhir']) : null;
        $current_date = new DateTime();

        // Jika periode_terakhir belum diatur atau sudah lebih dari 1 tahun, status adalah "Perlu Diperbarui"
        $status = ($periode_terakhir === null || $periode_terakhir->diff($current_date)->y >= 1) 
            ? '<span class="text-danger">Perlu Diperbarui</span>' 
            : '<span class="text-success">Terkini</span>';

        echo "<tr>
            <td>{$row['nama']}</td>
            <td>Rp " . number_format($row['gaji'], 0, ',', '.') . "</td>
            <td>{$status}</td>
            <td>
                <form method='POST' action='' style='display:inline;'>
                    <input type='hidden' name='id_karyawan' value='{$row['id_karyawan']}'>
                    <button type='submit' name='action' value='hitung' class='btn btn-primary btn-sm'>Hitung Gaji</button>
                </form>
                <form method='POST' action='' style='display:inline;'>
                    <input type='hidden' name='id_karyawan' value='{$row['id_karyawan']}'>
                    <button type='submit' name='action' value='detail' class='btn btn-secondary btn-sm'>Detail Gaji</button>
                </form>
            </td>
        </tr>";
    }
    ?>
</tbody>

        </table>
    </div>

    <!-- Rincian Perhitungan -->
    <?php echo $details; ?>

    <!-- Tampilkan Notifikasi -->
    <?php echo $alert; ?>
</body>
</html>