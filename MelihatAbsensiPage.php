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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
          <a class="navbar-brand" href="#"><i class="fas fa-store"></i> YANTO</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="nav-item"><a class="nav-link" href=""><i class="fas fa-box"></i> Product</a></li>
              <li class="nav-item"><a class="nav-link" href="pageHarga.php"><i class="fas fa-tags"></i> Price</a></li>
              <li class="nav-item"><a class="nav-link" href=""><i class="fas fa-store-alt"></i> Stock Toko</a></li>
              <li class="nav-item"><a class="nav-link" href=""><i class="fas fa-warehouse"></i> Stock Gudang</a></li>
              <li class="nav-item"><a class="nav-link" href="halamanTransaksi.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
              <li class="nav-item"><a class="nav-link" href="MelihatAbsensiPage.php"><i class="fas fa-users"></i> Employee</a></li>
              <li class="nav-item"><a class="nav-link" href="pageLaporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
              <li class="nav-item"><a class="nav-link" href="loginPage.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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

        <!-- Input Search Nama -->
        <div class="flex-grow-1">
            <label for="id_karyawan" class="form-label">Cari ID Karyawan:</label>
            <input type="text" id="id_karyawan" class="form-control" placeholder="ID Karyawan">
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
                    <th>ID Absensi</th>
                    <th>ID Karyawan</th>
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
            const id_karyawan = document.getElementById('id_karyawan').value;

            // Debugging: log URL yang dikirimkan
            console.log(`Fetching data from: absensi.php?tanggal=${tanggal}&id_karyawan=${id_karyawan}`);

            // Panggil API absensi.php
            let url = `MelihatAbsensi.php?tanggal=${tanggal}&id_karyawan=${id_karyawan}`;
            if (!tanggal) {
                url = url.replace("&tanggal=", "");
            }
            if (!id_karyawan) {
                url = url.replace("&id_karyawan=", "");
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
                            <td>${row.id_absensi}</td>
                            <td>${row.id_karyawan}</td>
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
</body>
</html>
