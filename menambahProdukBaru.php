<?php
include 'koneksi.php';

$error = false;
$error_message = '';
$success = false;

// Proses Hapus Produk
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id_barang = (int)$_GET['id_barang'];

    // Hapus dari detail_produk
    $sqlDeleteDetail = "DELETE FROM detail_produk WHERE id_barang = $id_barang";
    $conn->query($sqlDeleteDetail);

    // Hapus dari produk
    $sqlDeleteProduk = "DELETE FROM produk WHERE id_barang = $id_barang";
    if ($conn->query($sqlDeleteProduk) === TRUE) {
        $success = true;
    } else {
        $error = true;
        $error_message = "Gagal menghapus produk.";
    }
}

// Proses Update Produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id_barang = (int)$_POST['id_barang'];
    $harga = (int)$_POST['harga'];
    $stok_gudang = (int)$_POST['stok_gudang'];
    $id_ukuran = (int)$_POST['id_ukuran'];

    if ($harga <= 0 || $stok_gudang < 0) {
        $error = true;
        $error_message = "Harga harus > 0 dan stok ≥ 0.";
    } else {
        // Update produk
        $sqlUpdateProduk = "UPDATE produk SET harga = '$harga' WHERE id_barang = $id_barang";
        if ($conn->query($sqlUpdateProduk) === TRUE) {
            // Update detail_produk
            $sqlUpdateDetail = "UPDATE detail_produk SET stok_gudang = '$stok_gudang', id_ukuran = '$id_ukuran' WHERE id_barang = $id_barang";
            if ($conn->query($sqlUpdateDetail) === TRUE) {
                $success = true;
            } else {
                $error = true;
                $error_message = "Gagal mengupdate detail produk.";
            }
        } else {
            $error = true;
            $error_message = "Gagal mengupdate produk.";
        }
    }
}

// Ambil ukuran untuk pilihan edit
$sqlUkuran = "SELECT * FROM ukuran";
$resultUkuran = $conn->query($sqlUkuran);

// Search
$searchTerm = '';
$whereClause = '';
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $searchTerm = $conn->real_escape_string($_GET['search']);
    // Pencarian pada kode_barang, ukuran, stok_gudang, dan harga
    $whereClause = "WHERE p.kode_barang LIKE '%$searchTerm%'
                    OR u.ukuran LIKE '%$searchTerm%'
                    OR dp.stok_gudang LIKE '%$searchTerm%'
                    OR p.harga LIKE '%$searchTerm%'";
}

// Query untuk menampilkan daftar produk
$sqlProduk = "SELECT
                p.id_barang,
                p.kode_barang,
                p.harga,
                dp.stok_gudang,
                u.id_ukuran,
                u.ukuran
            FROM 
                produk p
            JOIN 
                detail_produk dp ON p.id_barang = dp.id_barang
            JOIN 
                ukuran u ON dp.id_ukuran = u.id_ukuran
            $whereClause
            ORDER BY 
                p.kode_barang ASC";

$resultProduk = $conn->query($sqlProduk);
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
            max-width: 1200px;
            margin: 20px auto;
            text-align: center;
        }

        h1 {
            margin-bottom: 10px;
        }

        .btn {
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
        }
        table th {
            background-color: #f2f2f2;
        }
        .btn-edit, .btn-delete {
            margin-right: 5px;
        }

        .modal {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); 
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease; 
            z-index: 1000; 
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); 
        }
        .modal-content h3 {
            margin-bottom: 15px; 
        }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px; 
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; 
        }
        .modal-buttons {
            display: flex;
            justify-content: flex-end; 
        }
        .modal-buttons .btn {
            margin: 0 5px; 
        }

        .search-container {
            margin-top: 20px;
            text-align: center;
        }

        .search-container input[type="text"] {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-container button {
            padding: 5px 10px;
            margin-left: 5px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            text-align: center;
        }
        .btn-edit {
            background-color: #ffc107; /* Warna kuning */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-edit:hover {
            background-color: #e0a800; /* Warna kuning tua */
        }

        /* Style untuk tombol Delete */
        .btn-delete {
            background-color: #dc3545; /* Warna merah */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #c82333; /* Warna merah tua */
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
        .navbar-nav .nav-item .nav-link[href="loginPage.php"]:hover {
        background-color: red;
        border-radius: 5px; /* Opsional, untuk konsistensi dengan hover lainnya */
        }

      @media (min-width: 992px) {
          .dropdown-submenu:hover .dropdown-menu {
              display: block;
          }
      }

      .dropdown-submenu.show .dropdown-menu {
          display: block;
      }

      @media (max-width: 991px) {
          .dropdown-menu .show {
              display: block !important;
          }

          .dropdown-submenu .dropdown-menu {
              position: relative;
              left: 0;
              top: 0;
              margin-left: 1rem;
          }
      }

       .navbar-toggler-icon {
        background-image: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 24px;
        position: relative;
    }
    .navbar-toggler-icon::before,
    .navbar-toggler-icon::after,
    .navbar-toggler-icon div {
        content: '';
        background-color: white; /* Warna garis putih */
        width: 100%;
        height: 3px;
        position: absolute;
        left: 0;
    }
    .navbar-toggler-icon::before {
        top: 0;
    }
    .navbar-toggler-icon div {
        top: 50%;
        transform: translateY(-50%);
    }
    .navbar-toggler-icon::after {
        bottom: 0;
    }
    </style>
      <script>
      document.addEventListener('DOMContentLoaded', function () {
          document.querySelectorAll('.dropdown-submenu > a').forEach(function (dropdownToggle) {
              dropdownToggle.addEventListener('click', function (e) {
                  var submenu = this.nextElementSibling;
                  if (submenu) {
                      submenu.classList.toggle('show');
                  }
                  e.preventDefault();
                  e.stopPropagation(); // Mencegah penutupan dropdown utama
              });
          });

          // Menutup dropdown saat klik di luar
          document.addEventListener('click', function (e) {
              document.querySelectorAll('.dropdown-menu .show').forEach(function (openSubmenu) {
                  openSubmenu.classList.remove('show');
              });
          });
      });
  </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
          <img src="/img/logomuse.jpg" style="height: 50px; width: auto;"> MUSE COLLECTION
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon">
            <div></div>
          </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="menambahProdukBaru.php"><i class="fas fa-box"></i> Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="pageHarga.php"><i class="fas fa-tags"></i> Harga</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-store-alt"></i> Stok
                    </a>
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
                <li class="nav-item"><a class="nav-link" href="loginPage.php"><i class="fas fa-exchange-alt"></i> Logout</a></li>
                </li>
            </ul>
        </div>
    </div>
  </nav>
<div class="container">
    <h1>Daftar Produk</h1>

    <!-- Pesan sukses/error -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">Aksi berhasil dilakukan!</div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Cari kode, ukuran, stok, harga" value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Barang</th>
                <th>Harga</th>
                <th>Stok Gudang</th>
                <th>Ukuran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $resultProduk->fetch_assoc()): ?>
            <tr>
                <td><?= $row['kode_barang'] ?></td>
                <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>
                <td><?= $row['stok_gudang'] ?></td>
                <td><?= $row['ukuran'] ?></td>
                <td>
                    <button class="btn btn-warning btn-sm btn-edit" onclick="openEditModal(<?= $row['id_barang'] ?>, <?= $row['harga'] ?>, <?= $row['stok_gudang'] ?>, <?= $row['id_ukuran'] ?>)">Edit</button>
                    <a href="?action=delete&id_barang=<?= $row['id_barang'] ?>" class="btn btn-danger btn-sm btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Edit -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <h3>Edit Produk</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_barang" id="edit_id_barang">
            <label>Harga:</label>
            <input type="number" name="harga" id="edit_harga" required>
            <label>Stok Gudang:</label>
            <input type="number" name="stok_gudang" id="edit_stok_gudang" required min="0">
            <label>Ukuran:</label>
            <select name="id_ukuran" id="edit_id_ukuran" required>
                <?php 
                $resultUkuran->data_seek(0); // Reset pointer ukuran
                while ($u = $resultUkuran->fetch_assoc()): ?>
                    <option value="<?= $u['id_ukuran'] ?>"><?= $u['ukuran'] ?></option>
                <?php endwhile; ?>
            </select>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

</script>

<script>
function openEditModal(id_barang, harga, stok_gudang, id_ukuran) {
    document.getElementById('edit_id_barang').value = id_barang;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_stok_gudang').value = stok_gudang;
    document.getElementById('edit_id_ukuran').value = id_ukuran;

    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<footer class="text-center py-3">
  <div class="container1">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> MUSE COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@musecollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>