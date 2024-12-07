<?php
// Include koneksi database dan kelas Karyawan
include 'koneksi.php';
include 'Karyawan.php';

// Variabel untuk menyimpan alert jika ada
$alert = isset($_GET['alert']) ? $_GET['alert'] : '';

// Ambil data pencarian dari URL jika ada
$search = isset($_GET['cari']) ? $_GET['cari'] : '';

// Query untuk mengambil data karyawan berdasarkan pencarian
if ($search) {
    // Jika ada kata pencarian
    $sql = "SELECT * FROM karyawan WHERE nama LIKE ? OR kode_karyawan LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Jika tidak ada pencarian, tampilkan semua data
    $result = $conn->query("SELECT * FROM karyawan");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan</title>
    <!-- Tambahkan Bootstrap untuk styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
    <div class="container mt-5">
        <h2 class="text-center mb-4">Manajemen Karyawan</h2>

        <!-- Tampilkan alert jika ada -->
        <?php if (!empty($alert)) : ?>
            <script>
                Swal.fire(<?= $alert ?>);
            </script>
        <?php endif; ?>

        <!-- Form Tambah/Edit Karyawan -->
        <form action="proses_karyawan.php" method="POST">
            <h4 id="form-title">Tambah Karyawan</h4>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" required>
            </div>
            <div class="mb-3">
                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" placeholder="Masukkan nomor telepon" required>
            </div>
            <div class="mb-3">
                <label for="kode_karyawan" class="form-label">Kode Karyawan</label>
                <input type="text" class="form-control" id="kode_karyawan" name="kode_karyawan" placeholder="Contoh: K123, PG456, P789" required>
            </div>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="action" id="action" value="add">
            <button type="submit" class="btn btn-primary w-100" id="submit-button">Tambah Karyawan</button>
        </form>

        <hr class="my-5">

        <!-- Tabel Daftar Karyawan -->
        <h4>Daftar Karyawan</h4>

        <!-- Form Pencarian -->
        <form method="GET" action="pageKaryawan.php" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="cari" placeholder="Cari nama atau kode karyawan" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Karyawan</th>
                    <th>Nama</th>
                    <th>Nomor Telepon</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Tampilkan hasil pencarian atau semua data karyawan
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id_karyawan']}</td>
                            <td>{$row['kode_karyawan']}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['nomor_telepon']}</td>
                            <td>{$row['jabatan']}</td>
                            <td>
                                <button class='btn btn-warning btn-sm edit-button' 
                                    data-id='{$row['id_karyawan']}' 
                                    data-nama='{$row['nama']}' 
                                    data-nomor='{$row['nomor_telepon']}' 
                                    data-kode='{$row['kode_karyawan']}'>Edit</button>
                                <a href='proses_karyawan.php?action=delete&id={$row['id_karyawan']}' class='btn btn-danger btn-sm'>Hapus</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada karyawan yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- JavaScript untuk Edit Data -->
    <script>
     document.querySelectorAll('.edit-button').forEach(button => {
    button.addEventListener('click', function () {
        document.getElementById('form-title').textContent = "Edit Karyawan";
        document.getElementById('submit-button').textContent = "Update Karyawan";
        document.getElementById('action').value = "edit";

        // Isi form dengan data yang akan di-edit
        document.getElementById('id').value = this.getAttribute('data-id');
        document.getElementById('nama').value = this.getAttribute('data-nama');
        document.getElementById('nomor_telepon').value = this.getAttribute('data-nomor');
        document.getElementById('kode_karyawan').value = this.getAttribute('data-kode');

        // Scroll ke atas form
        window.scrollTo({
            top: 0,               // Posisi paling atas
            behavior: 'smooth'
        });
    });
});

    </script>

    <script>
        // Fungsi untuk membaca parameter URL
        function getUrlParameter(name) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            var results = regex.exec(window.location.href);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        // Ambil parameter alert
        var alertMessage = getUrlParameter('alert');
        if (alertMessage) {
            // Format pesan sesuai SweetAlert
            const alertParts = alertMessage.split(", ");
            Swal.fire({
                title: alertParts[0].replace(/'/g, ""), // Judul alert
                text: alertParts[1].replace(/'/g, ""),  // Isi pesan
                icon: alertParts[2].replace(/'/g, "")   // Tipe alert
            });
        }
    </script>

    <script>
        // Hapus parameter alert dari URL
        if (alertMessage) {
            const url = new URL(window.location.href);
            url.searchParams.delete('alert');
            window.history.replaceState(null, null, url.toString());
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