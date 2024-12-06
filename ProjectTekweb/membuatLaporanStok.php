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
    <title>Laporan Stok Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .filter-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .filter-section select, .filter-section input {
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
        table th, table td {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Laporan Stok Barang</h1>
        
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
                        <th>ID Barang</th>
                        <th>Kode Barang</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultLaporan->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_barang'] ?></td>
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
</body>
</html>
