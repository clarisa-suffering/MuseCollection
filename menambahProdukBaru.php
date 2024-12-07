<?php
include 'koneksi.php';
session_set_cookie_params(0);

session_start();  // Start the session

// Check if the session variable 'role' exists and if it's one of the allowed roles
if (!isset($_SESSION['jabatan']) || $_SESSION['jabatan'] !== 'pemilik') {
    // Redirect to login page if not logged in as pemilik
    header("Location: loginPage.php");
    exit();
}

// Fungsi untuk menambah produk baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Ambil data dari form
    $kode_barang = $_POST['kode_barang'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];
    $id_ukuran = $_POST['id_ukuran'];  // Ambil id_ukuran dari form

    // Cek jika harga dan jumlah tidak valid (0 atau negatif)
    if ($harga <= 0 || $jumlah <= 0) {
        $error = true;
        $error_message = "Harga dan Jumlah harus lebih besar dari 0.";
    } else {
        // Cek apakah kode barang sudah ada di database
        $sqlCheckKode = "SELECT * FROM produk WHERE kode_barang = '$kode_barang'";
        $resultCheckKode = $conn->query($sqlCheckKode);

        if ($resultCheckKode->num_rows > 0) {
            // Jika kode barang sudah ada
            $error = true;
            $error_message = "Kode barang sudah terdaftar. Silakan masukkan kode barang yang lain.";
        } else {
            // Query untuk menambah produk baru
            $sql = "INSERT INTO produk (kode_barang, harga) VALUES ('$kode_barang', '$harga')";
            
            if ($conn->query($sql) === TRUE) {
                // Ambil id_barang dari produk yang baru ditambahkan
                $id_barang = $conn->insert_id;
                
                // Menambahkan stok ke detail_produk dengan id_ukuran
                $sqlDetail = "INSERT INTO detail_produk (id_barang, stok_gudang, id_ukuran) VALUES ('$id_barang', '$jumlah', '$id_ukuran')";
                if ($conn->query($sqlDetail) === TRUE) {
                    // Menambahkan data ke detail_laporan
                    $tanggal = date('Y-m-d');  // Tanggal saat ini
                    $status_in_out = "In";
                    $sqlLaporan = "INSERT INTO detail_laporan (id_detprod, quantity, tanggal_in_out, status_in_out) 
                                   VALUES ('$id_barang', '$jumlah', '$tanggal', '$status_in_out')";
                    if ($conn->query($sqlLaporan) === TRUE) {
                        $success = true; // Tanda bahwa produk berhasil ditambahkan
                    } else {
                        $error = true;
                        $error_message = "Gagal menambahkan data ke laporan.";
                    }
                } else {
                    $error = true;
                    $error_message = "Gagal menambahkan detail produk.";
                }
            } else {
                $error = true;
                $error_message = "Gagal menambahkan produk.";
            }
        }
    }
}


// Query untuk menampilkan daftar produk
$sqlProduk = "SELECT
                p.kode_barang,
                p.harga,
                dp.stok_gudang,
                u.ukuran
            FROM 
                produk p
            JOIN 
                detail_produk dp ON p.id_barang = dp.id_barang
            JOIN 
                ukuran u ON dp.id_ukuran = u.id_ukuran
            ORDER BY 
                p.kode_barang ASC";

$resultProduk = $conn->query($sqlProduk);

// Query untuk mendapatkan ukuran yang tersedia
$sqlUkuran = "SELECT * FROM ukuran";
$resultUkuran = $conn->query($sqlUkuran);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.9/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.9/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0 ;
            padding: 0 ;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center; /* Memusatkan secara horizontal */
            justify-content: center; /* Memusatkan secara vertikal */
            text-align: center; /* Menyelaraskan teks ke tengah */
            max-width: 1200px;
            margin: 20px auto;
        }

        h1 {
            margin-bottom: 10px;
        }

        .btn {
            margin-top: 20px; /* Memberikan jarak antara tombol dan elemen sebelumnya */
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
            cursor: pointer;
        }
        table th:hover {
            background-color: #e0e0e0;
        }
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .modal {
        display: none; /* Sembunyikan modal secara default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7); /* Latar belakang semi-transparan */
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease; /* Transisi halus */
        z-index: 1000; /* Pastikan modal di atas elemen lain */
    }
    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 400px; /* Maksimal lebar modal */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Bayangan untuk efek kedalaman */
    }
    .modal-content h3 {
        margin-bottom: 15px; /* Jarak antara judul dan konten */
    }
    .modal-content input, .modal-content select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px; /* Jarak antara input */
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box; /* Pastikan padding tidak menambah lebar */
    }
    .modal-buttons {
        display: flex;
        justify-content: space-between; /* Jarak antara tombol */
    }
    .modal-buttons .btn {
        flex: 1; /* Tombol mengambil ruang yang sama */
        margin: 0 5px; /* Jarak antar tombol */
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
        }  footer {
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
    <h1>Daftar Produk</h1>
    
    <!-- Tombol untuk menambah produk -->
    <button class="btn" id="addProductBtn">Tambah Produk</button>
    
    <!-- Daftar Produk -->
    <?php if ($resultProduk->num_rows > 0): ?>
        <table id="productTable">
    <thead>
        <tr>
            <th id="sortKodeBarang">Kode Barang</th>
            <th id="sortUkuran">Ukuran</th>
            <th id="sortStok">Stok Gudang</th>
            <th id="sortHarga">Harga</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $resultProduk->fetch_assoc()): ?>
            <tr>
                <td><?= $row['kode_barang'] ?></td>
                <td><?= $row['ukuran'] ?></td>
                <td><?= $row['stok_gudang'] ?></td>
                <td><?= number_format($row['harga'], 0, ',', '.') ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    <?php else: ?>
        <p>Tidak ada produk yang tersedia.</p>
    <?php endif; ?>
</div>

<!-- Modal untuk Menambahkan Produk -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <h3>Tambah Produk Baru</h3>
        <form method="POST" action="">
            <label for="kode_barang">Kode Barang:</label>
            <input type="text" id="kode_barang" name="kode_barang" required>
            
            <label for="harga">Harga (min 1000):</label>
            <input type="number" id="harga" name="harga" required>
            
            <label for="jumlah">Stok Gudang:</label>
            <input type="number" id="jumlah" name="jumlah" required>
            
            <label for="id_ukuran">Ukuran:</label>
            <select name="id_ukuran" id="id_ukuran" required>
                <?php while ($row = $resultUkuran->fetch_assoc()): ?>
                    <option value="<?= $row['id_ukuran'] ?>"><?= $row['ukuran'] ?></option>
                <?php endwhile; ?>
            </select>
            
            <div class="modal-buttons">
                <button type="submit" name="submit" class="btn">Simpan Produk</button>
                <button type="button" class="btn" id="closeModalBtn">Tutup</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Menangani tombol "Tambah Produk"
    document.getElementById('addProductBtn').addEventListener('click', function() {
        document.getElementById('productModal').style.display = 'flex';
    });

    // Menangani tombol "Tutup" untuk modal
    document.getElementById('closeModalBtn').addEventListener('click', function() {
        document.getElementById('productModal').style.display = 'none';
    });

    // Mengatur flag untuk arah sorting
    let sortAsc = {
        id_barang: true,
        kode_barang: true,
        harga: true,
        stok: true,
        ukuran: true
    };

    // Definisikan urutan untuk ukuran (Small, Medium, Large, XXL, dsb.)
    const ukuranOrder = ['Small', 'Medium', 'Large', 'X-Large', 'XX-Large'];

    // Mengambil elemen header yang bisa disortir
    const headers = document.querySelectorAll('table th');

    headers.forEach(header => {
        header.addEventListener('click', function() {
            const columnIndex = Array.from(header.parentNode.children).indexOf(header);
            const columnName = header.id.replace('sort', '').toLowerCase();
            sortTable(columnIndex, columnName);
        });
    });

    // Fungsi untuk sorting tabel
    function sortTable(columnIndex, columnName) {
        const table = document.getElementById('productTable');
        const rows = Array.from(table.rows).slice(1); // Mengambil semua baris kecuali header

        // Menentukan apakah urutan akan menaik atau menurun
        const isAscending = sortAsc[columnName];

        // Sorting berdasarkan kolom yang diklik
        rows.sort((rowA, rowB) => {
            const cellA = rowA.cells[columnIndex].textContent.trim();
            const cellB = rowB.cells[columnIndex].textContent.trim();

            // Parsing harga, ukuran, dan ID Barang untuk sorting yang benar
            let valueA, valueB;
            if (columnName === 'harga') {
                valueA = parseFloat(cellA.replace(/[^0-9.-]+/g, "")); // Hapus simbol mata uang dan parse float
                valueB = parseFloat(cellB.replace(/[^0-9.-]+/g, ""));
            } else if (columnName === 'ukuran') {
                valueA = ukuranOrder.indexOf(cellA); // Menyusun berdasarkan urutan ukuran
                valueB = ukuranOrder.indexOf(cellB);
            } else if (columnName === 'id_barang') {
                valueA = parseInt(cellA); // ID Barang disortir secara numerik
                valueB = parseInt(cellB);
            } else {
                valueA = cellA;
                valueB = cellB;
            }

            if (isAscending) {
                return valueA > valueB ? 1 : valueA < valueB ? -1 : 0;
            } else {
                return valueA < valueB ? 1 : valueA > valueB ? -1 : 0;
            }
        });

        // Menyusun ulang baris tabel
        rows.forEach(row => table.appendChild(row));

        // Toggle arah sorting
        sortAsc[columnName] = !isAscending;
    }

    // SweetAlert untuk success atau error setelah simpan produk
    <?php if (isset($success) && $success === true): ?>
        Swal.fire({
            icon: 'success',
            title: 'Produk Berhasil Ditambahkan',
            text: 'Produk baru berhasil disimpan ke dalam database.',
        });
    <?php elseif (isset($error) && $error === true): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= isset($error_message) ? $error_message : "Terjadi kesalahan." ?>',
        });
    <?php endif; ?>
    document.querySelector('form').onsubmit = function(e) {
        let harga = document.getElementById('harga').value;
        if (harga < 1000) {
            e.preventDefault(); // Mencegah form dikirim
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harga harus lebih dari 1000!',
            });
        }
        if (harga % 100 !== 0) {
            e.preventDefault(); // Mencegah form dikirim
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harga harus dalam ribu rupiah! (misal: 1250 (x), 1200 (v))',
            });
        }
    };
</script>
<footer class="text-center py-3">
  <div class="container1">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> MUSE COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@musecollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>