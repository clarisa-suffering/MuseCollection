<?php
// Menghubungkan koneksi database yang sudah ada
include('koneksi.php');  
include('produk.php'); // Menggunakan class Produk

// Inisialisasi class Produk
$produk = new Produk($conn);

// Fungsi untuk melihat stok dan harga barang
function lihatStokHargaBarangGudang($kode_barang = null) {
    global $conn; // Menggunakan koneksi global

    // Jika kode_barang disediakan, tampilkan detail barang tertentu
    if ($kode_barang) {
        $query = "
            SELECT p.kode_barang, d.stok_gudang, p.harga 
            FROM produk p
            LEFT JOIN detail_produk d ON p.id_barang = d.id_barang  -- Menggunakan id_barang untuk JOIN
            WHERE p.kode_barang = ? AND p.status_aktif = 1
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $kode_barang); // Mengikat parameter kode_barang
    } else {
        // Jika tidak ada kode_barang, tampilkan semua barang
        $query = "
            SELECT p.kode_barang, d.stok_gudang, p.harga 
            FROM produk p
            LEFT JOIN detail_produk d ON p.id_barang = d.id_barang  -- Menggunakan id_barang untuk JOIN
            WHERE p.status_aktif = 1
        ";
        $stmt = $conn->prepare($query);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melihat Stok dan Harga Barang</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJQj99xu8pMfdUu6klI6xGbDbfeZJmWx5A6N8pXpR1a2eCZQ5U+1VzZNOh3v" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
       
        h1 {
            text-align: center;
            margin-top: 30px;
            color: black; 
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .btn-custom {
            padding: 10px;
            font-size: 16px; 
            color: white; 
            background-color: #007bff; 
            border: none; 
            border-radius: 4px;
            cursor: pointer; 
            transition: background-color 0.3s ease; 
            width: 100%;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .button-container {
            display: flex;
            justify-content: center; 
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #808080;
            cursor: pointer;
        }
        table th:hover {
            background-color: #e0e0e0;
        }
        .alert {
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
            padding: 10px;
            border-radius: 8px;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }

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
        footer {
            background-color: #332D2D; 
            color: white; 
            margin-top: auto; 
            padding: 20px 0;
            width: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            padding: 0;
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
    <h1>Melihat Stok Gudang</h1>
    <div class="container">
        

        <!-- Form untuk memasukkan kode barang -->
        <form method="GET" action="lihatStokHargaBarangGudang.php">
            <div class="form-group">
                <label for="kode_barang">Masukkan Kode Barang (Optional):</label>
                <input type="text" id="kode_barang" name="kode_barang" class="form-control" placeholder="Masukkan kode barang...">
            </div>
            
            <div class="button-container">
                <button type="submit" class="btn btn-primary btn-custom">Lihat Stok</button>
            </div>
        </form>

        <?php
        // Mendapatkan kode barang dari input pengguna
        $kode_barang = isset($_GET['kode_barang']) ? $_GET['kode_barang'] : null;

        // Menampilkan stok barang hanya jika form disubmit
        if ($kode_barang !== null) {
            // Menampilkan stok barang
            $result = lihatStokHargaBarangGudang($kode_barang);

            if ($result && $result->num_rows > 0) {
                // Menampilkan data produk dalam tabel
                echo "<table class='table table-bordered mt-4'>
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Stok Gudang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["kode_barang"]) . "</td>
                            <td>" . htmlspecialchars($row["stok_gudang"]) . "</td>
                            <td>" . number_format($row["harga"], 2) . "</td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-danger'>Tidak ada data barang yang ditemukan.</div>";
            }
        }
        ?>

    </div>

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybB6jl2HffQ2X7Scz5ptF5cH0g6XPtjAdfXJHeKSzTZRcd0i9m" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0V8Fq1FnD5k0lGc9eQ7o5PHDaMXYe+J6/9PqBO8WyEXpA9OZ" crossorigin="anonymous"></script>
    <footer class="text-center py-3">
  <div class="container1">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> MUSE COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@musecollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>
