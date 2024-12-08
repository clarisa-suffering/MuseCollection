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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <style>
        .navbar {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #332D2D;
        }

        .navbar .container-fluid {
            max-width: 100%;
            padding: 0;
        }

        .navbar-brand {
            color: white;
            font-size: 1.5rem;
        }

        .navbar-nav {
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }

        .navbar-nav .nav-item {
            list-style: none;
        }

        .navbar-nav .nav-item .nav-link {
            color: white;
            padding: 15px 20px;
            display: block;
            text-align: center;
        }

        .navbar-nav .nav-item .nav-link:hover {
            background-color: #007bff;
            border-radius: 5px;
        }

        .dropdown-menu {
            left: 0;
            right: auto;
        }

        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
        }

        .dropdown-submenu:hover .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            color: #333;
            padding: 10px 20px;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        html, body {
            height: 100%; 
            margin: 0; 
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1; 
            overflow-y: auto; 
            padding-bottom: 60px; 
        }

        footer {
            position: sticky; 
            left: 0; 
            bottom: 0;
            width: 100%; 
            background-color: #332D2D; 
            color: white; 
            text-align: center; 
            padding: 20px 0;
            z-index: 1000;
        }

        .navbar-nav .nav-item1 .nav-link {
            color: white;
            padding: 15px 20px;
            display: block;
            text-align: center;
        }

        .navbar-nav .nav-item1 .nav-link:hover {
            background-color: #ff0000;
            border-radius: 5px;
        }

        </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand"href="dashboard.php">  <img src="\img\logomuse.jpg" style="height: 50px; width: auto;"> MUSE COLLECTION</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="menambahProdukBaru.php"><i class="fas fa-box"></i> Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="pageHarga.php"><i class="fas fa-tags"></i> Harga </a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-store-alt"></i> Stok</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="pageStokToko.php">Toko</a></li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gudang</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="lihatStokHargaBarangGudang.php">Lihat Stok</a></li>
                                <li><a class="dropdown-item" href="tambahStokGudang.php">Tambah Stok</a></li>
                                <li><a class="dropdown-item" href="pindah_stokGudang.php">Pindah Stok</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="halamanTransaksi.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-users"></i> Karyawan</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="absensi.php">Absensi</a></li>
                        <li><a class="dropdown-item" href="perhitunganGaji.php">Perhitungan Gaji</a></li>
                        <li><a class="dropdown-item" href="MelihatAbsensiPage.php">List Absensi</a></li>
                        <li><a class="dropdown-item" href="pageKaryawan.php">Manajemen Karyawan</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-file-alt"></i> Laporan</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="pageLaporan.php">Transaksi</a></li>
                        <li><a class="dropdown-item" href="membuatLaporanStok.php">Stok Gudang</a></li>
                    </ul>
                </li>
                <li class="nav-item1"><a class="nav-link" href="loginPage.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav> 
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

    
    <footer class="text-center py-3">
  <div class="container1">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> MUSE COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@musecollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>