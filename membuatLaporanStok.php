<?php
include 'koneksi.php';

// Fungsi untuk mendapatkan bulan dan tahun unik dari detail_laporan
function getUniqueMonthsAndYears($conn) {
    $sql = "SELECT DISTINCT 
                YEAR(tanggal_in_out) AS tahun, 
                MONTH(tanggal_in_out) AS bulan
            FROM detail_laporan
            ORDER BY tahun DESC, bulan DESC";
    $result = $conn->query($sql);

    $months = [];
    while ($row = $result->fetch_assoc()) {
        $months[] = [
            'tahun' => $row['tahun'], 
            'bulan' => $row['bulan']
        ];
    }

    return $months;
}

// Mendapatkan daftar bulan dan tahun unik
$uniqueMonths = getUniqueMonthsAndYears($conn);

// Menyusun daftar tahun yang unik untuk dropdown
$years = array_unique(array_column($uniqueMonths, 'tahun'));
sort($years); // Urutkan tahun dari yang terbaru

// Mengatur bulan dan tahun default atau dari input
$currentMonth = date('m');
$currentYear = date('Y');

// Menangani jika ada input dari form
if (isset($_POST['pilih_bulan']) && isset($_POST['pilih_tahun'])) {
    $bulanLaporan = $_POST['pilih_bulan'];
    $tahunLaporan = $_POST['pilih_tahun'];
} else {
    $bulanLaporan = $currentMonth;
    $tahunLaporan = $currentYear;
}

// Format bulan menjadi dua digit jika bukan "Semua"
if ($bulanLaporan !== 'Semua') {
    $bulanLaporan = str_pad($bulanLaporan, 2, '0', STR_PAD_LEFT);
}

// Daftar nama bulan dalam bahasa Indonesia
$namaBulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
    '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
    'Semua' => 'Semua Periode'
];

// Mendapatkan input search
$searchQuery = isset($_POST['search']) ? trim($_POST['search']) : '';

// Menentukan apakah input adalah angka (jumlah) atau kode barang
$searchCondition = "";
if ($searchQuery !== '') {
    if (is_numeric($searchQuery)) {
        // Search berdasarkan jumlah
        $searchCondition = " AND d.quantity = " . intval($searchQuery);
    } else {
        // Search berdasarkan kode barang
        $searchCondition = " AND p.kode_barang LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
    }
}

// Menentukan kondisi untuk periode
$periodCondition = "";
if ($bulanLaporan !== 'Semua' && $tahunLaporan !== 'Semua') {
    $periodCondition = "WHERE MONTH(d.tanggal_in_out) = $bulanLaporan AND YEAR(d.tanggal_in_out) = $tahunLaporan";
} elseif ($bulanLaporan !== 'Semua' && $tahunLaporan === 'Semua') {
    $periodCondition = "WHERE MONTH(d.tanggal_in_out) = $bulanLaporan";
} elseif ($bulanLaporan === 'Semua' && $tahunLaporan !== 'Semua') {
    $periodCondition = "WHERE YEAR(d.tanggal_in_out) = $tahunLaporan";
}

// Query untuk menarik data laporan
$sqlLaporan = "SELECT 
                    d.id_detail_laporan,
                    d.tanggal_in_out,
                    dp.id_barang,
                    d.quantity,
                    d.status_in_out,
                    p.kode_barang
               FROM 
                    detail_laporan d
               JOIN 
                    detail_produk dp ON d.id_detprod = dp.id_detprod
               JOIN 
                    produk p ON dp.id_barang = p.id_barang
               $periodCondition
               $searchCondition
               ORDER BY d.tanggal_in_out ASC";

$resultLaporan = $conn->query($sqlLaporan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Barang</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* CSS untuk Navbar dan halaman */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #343a40;
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

        /* Dropdown */
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

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Form dan Table Styling */
        .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .filter-section select,
        .filter-section input {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .submit-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }
        footer {
            background-color: #332D2D; /* Warna latar belakang footer */
            color: white; /* Warna teks footer */
            margin-top: auto; /* Membuat footer menempel di bawah */
            padding: 20px 0;
            width: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
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
    <div class="container">
        <h1>Laporan Stok Barang Gudang</h1>
        
        <div class="filter-section">
            <form method="POST" action="">
                <!-- Dropdown Bulan -->
                <select name="pilih_bulan" required>
                    <option value="Semua" <?= ($bulanLaporan == 'Semua') ? 'selected' : '' ?>>Semua Bulan</option>
                    <?php 
                    // Menampilkan semua bulan, meskipun tidak ada data transaksi
                    for ($i = 1; $i <= 12; $i++) {
                        $bulan = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $selected = ($bulan == $bulanLaporan) ? 'selected' : '';
                        $bulanLabel = $namaBulan[$bulan];
                        echo "<option value='$bulan' $selected>$bulanLabel</option>";
                    }
                    ?>
                </select>

                <!-- Dropdown Tahun -->
                <select name="pilih_tahun" required>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>" <?= ($year == $tahunLaporan) ? 'selected' : '' ?>>
                            <?= $year ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="text" name="search" placeholder="Cari kode barang atau jumlah" value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit" class="submit-btn">Tampilkan Laporan</button>
            </form>
        </div>

        <?php if ($resultLaporan->num_rows > 0): ?>
            <h3>Laporan Stok <?= $namaBulan[$bulanLaporan] . ' ' . $tahunLaporan ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultLaporan->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['kode_barang'] ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal_in_out'])) ?></td>
                            <td><?= abs($row['quantity']) ?></td>
                            <td><?= $row['status_in_out'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada barang masuk/keluar untuk bulan <?= $namaBulan[$bulanLaporan] ?> <?= $tahunLaporan ?>.</p>
        <?php endif; ?>
    </div>
    <footer class="text-center py-3">
  <div class="container1">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> MUSE COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@musecollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>