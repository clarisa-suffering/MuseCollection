<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
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

      <div class="container mt-4">
    <!-- Judul -->
    <div class="text-center mb-4">
        <h1>Absensi Karyawan</h1>
    </div>

    <!-- Form Input Bersampingan -->
    <div class="d-flex align-items-center mb-3">
        <!-- Dropdown Filter Tanggal -->
        <div class="me-3 flex-grow-1">
            <label for="tanggal" class="form-label">Filter Tanggal:</label>
            <input type="date" id="tanggal" class="form-control">
        </div>

        <!-- Input Search Nama Karyawan -->
        <div class="flex-grow-1">
            <label for="nama" class="form-label">Cari Nama Karyawan:</label>
            <input type="text" id="nama" class="form-control" placeholder="Nama Karyawan">
        </div>
    </div>

    <!-- Tombol Filter -->
    <div class="container text-center mb-4">
        <button onclick="filterAbsensi()" class="btn btn-primary">Tampilkan</button>
    </div>

    <!-- Tabel Absensi -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Kode Karyawan</th>
                <th>Nama Karyawan</th>
                <th>Jam</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="data-absensi">
            <tr>
                <td colspan="4">Tidak ada data.</td>
            </tr>
        </tbody>
    </table>
    </div>

    <script>
        // Function to fetch and display all attendance data on page load
        window.onload = function() {
            // Call the function to fetch all data
            filterAbsensi();
        };

        // Fungsi untuk menampilkan data absensi
        function filterAbsensi() {
            const tanggal = document.getElementById('tanggal').value;
            const nama = document.getElementById('nama').value;

            // Debugging: log URL yang dikirimkan
            console.log(`Fetching data from: MelihatAbsensi.php?tanggal=${tanggal}&nama=${nama}`);

            // Panggil API MelihatAbsensi.php
            let url = `MelihatAbsensi.php?tanggal=${tanggal}&nama=${nama}`;
            if (!tanggal) {
                url = url.replace("&tanggal=", "");
            }
            if (!nama) {
                url = url.replace("&nama=", "");
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache',  // Memastikan cache tidak digunakan
                }
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('data-absensi');
                tbody.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(row => {
                        const status = row.status === 1 ? 'Hadir' : 'Tidak Hadir';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.kode_karyawan}</td>
                            <td>${row.nama}</td>
                            <td>${row.jam}</td>
                            <td>${status}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4">Tidak ada data.</td></tr>';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
<footer class="text-center py-3">
  <div class="container">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> MUSE COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@musecollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>