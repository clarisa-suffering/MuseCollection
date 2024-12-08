<?php
session_set_cookie_params(0);

session_start();  // Start the session

// Check if the session variable 'role' exists and if it's one of the allowed roles
if (!isset($_SESSION['jabatan']) || $_SESSION['jabatan'] !== 'pemilik' && $_SESSION['jabatan'] !== 'kasir') {
    // Redirect to login page if not logged in as kasir or pemilik
    header("Location: loginPage.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<style>
    #laporanTable thead th {
        background-color: #f2f2f2; 
        color: #333;         
        font-weight: bold; 
        text-align: center;
        border-bottom: 2px solid #ddd;
    }

    #laporanTable thead th:hover {
        background-color: #e0e0e0;
    }

    table th, table td {
        padding: 10px;
        text-align: left;
    }
    table td {
        vertical-align: middle;
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
        html, body {
            height: 100%; /* Mengatur tinggi html dan body 100% */
            margin: 0; /* Menghilangkan margin default */
            display: flex; /* Menggunakan flexbox */
            flex-direction: column; /* Mengatur arah flex menjadi kolom */
        }

        footer {
            position: fixed; /* Mengatur posisi footer tetap */
            left: 0; /* Mengatur posisi kiri */
            bottom: 0; /* Mengatur posisi bawah */
            width: 100%; /* Mengatur lebar footer 100% */
            background-color: #332D2D; /* Warna latar belakang footer */
            color: white; /* Warna teks footer */
            text-align: center; /* Menyelaraskan teks ke tengah */
            padding: 20px 0; /* Padding atas dan bawah */
            z-index: 1000; /* Pastikan footer di atas elemen lain */
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
<div class="container mt-5">
    <div class="text-center mb-4">
        <h1>Pilih Periode Laporan</h1>
    </div>

    <!-- FORM LAPORAN (datepicker) -->
    <form id="formLaporan" method="POST" class="row g-3 justify-content-center">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Tanggal Awal:</label>
            <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="YYYY-MM-DD" 
                   value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>" required>
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Tanggal Akhir:</label>
            <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="YYYY-MM-DD"
                   value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Go</button>
        </div>
    </form>
</div>

<!-- TABEL LAPORAN -->
<div class="container mt-4">
    <table id="laporanTable" class="table table-bordered mx-auto" style="width: auto;">
        <thead>
            <tr>
                <th>Timestamp Transaksi</th>
                <th>Nama Pelanggan</th>
                <th>Kode Barang</th>
                <th>Ukuran</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
                <th class="harga-total">Harga Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Data hanya ditampilkan setelah form dikirim
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_date']) && isset($_POST['end_date']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                    include 'laporanTransaksi.php';  // Menampilkan data berdasarkan rentang tanggal (sesuai php laporanTransaksi)
                }
            ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        // DATEPICKER
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        });

        // FORM LAPORAN
        $("#formLaporan").on("submit", function(event) {
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            var today = new Date().toISOString().split("T")[0];

            // BR1: Validasi Tanggal tidak lebih dari hari ini
            if (startDate > today || endDate > today) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal yang dimasukkan tidak boleh lebih dari hari ini'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#start_date").val("");
                        $("#end_date").val("");
                    }
                });
                return;
            }

            // BR2: Validasi Tanggal pertama harus kurang dari tanggal terakhir
            if (startDate > endDate) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal awal harus lebih kecil dari tanggal akhir'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#start_date").val("");
                        $("#end_date").val("");
                    }
                });
                return;
            }
        });
    });
</script>
<footer class="text-center py-3">
  <div class="container">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> MUSE COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@musecollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>