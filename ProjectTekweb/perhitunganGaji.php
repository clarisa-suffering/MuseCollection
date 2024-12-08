<?php
// Include koneksi database dan kelas layanan baru

session_set_cookie_params(0);

session_start();  // Start the session

// Check if the session variable 'role' exists and if it's one of the allowed roles
if (!isset($_SESSION['jabatan']) || ($_SESSION['jabatan'] !== 'pemilik')) {
    // Redirect to login page if not logged in as kasir or pemilik
    header("Location: loginPage.php");
    exit();
}


include 'koneksi.php';
include 'KaryawanService.php';

// Inisialisasi service
$service = new KaryawanService($conn);

$alert = '';
$details = '';

// Cek apakah ada aksi yang dilakukan pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'all') {
        // Hitung gaji untuk semua karyawan non-pemilik
        $result = $conn->query("SELECT id_karyawan FROM karyawan WHERE kode_karyawan NOT RLIKE '^[pP][0-9]+'");
        while ($row = $result->fetch_assoc()) {
            $service->hitungGaji($row['id_karyawan']);
        }

        $alert = "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Gaji semua karyawan berhasil diperbarui.'
            });
        </script>";
    } elseif ($action === 'hitung' && !empty($_POST['id_karyawan'])) {
        $id_karyawan = (int)$_POST['id_karyawan'];
        $detail = $service->hitungGaji($id_karyawan);
        if ($detail) {
            // Buat tampilan detail dalam card yang lebih menarik
            $details = "
            <div class='container mt-5'>
                <div class='card shadow-sm'>
                    <div class='card-header bg-primary text-white'>
                        <h5 class='mb-0'>Detail Gaji - {$detail['nama']}</h5>
                    </div>
                    <div class='card-body'>
                        <table class='table table-bordered table-striped table-hover'>
                            <tr><th width='30%'>Base Salary</th><td>Rp " . number_format($detail['base_salary'], 0, ',', '.') . "</td></tr>
                            <tr><th>Increment</th><td>Rp " . number_format($detail['increment'], 0, ',', '.') . "</td></tr>
                            <tr><th>Bonus</th><td>Rp " . number_format($detail['bonus'], 0, ',', '.') . "</td></tr>
                            <tr class='table-info'><th>Total Salary</th><td><strong>Rp " . number_format($detail['total'], 0, ',', '.') . "</strong></td></tr>
                            <tr><th>Years of Work</th><td>{$detail['years_of_work']} tahun</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            ";

            $alert = "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Gaji Diperbarui',
                    text: 'Gaji karyawan {$detail['nama']} berhasil diperbarui!'
                });
            </script>";
        }
    } elseif ($action === 'detail' && !empty($_POST['id_karyawan'])) {
        $id_karyawan = (int)$_POST['id_karyawan'];
        $detail = $service->detailGaji($id_karyawan);
        if ($detail) {
            // Tabel detail dengan tampilan yang sama
            $details = "
            <div class='container mt-5'>
                <div class='card shadow-sm'>
                    <div class='card-header bg-secondary text-white'>
                        <h5 class='mb-0'>Detail Gaji - {$detail['nama']}</h5>
                    </div>
                    <div class='card-body'>
                        <table class='table table-bordered table-striped table-hover'>
                            <tr><th width='30%'>Base Salary</th><td>Rp " . number_format($detail['base_salary'], 0, ',', '.') . "</td></tr>
                            <tr><th>Increment</th><td>Rp " . number_format($detail['increment'], 0, ',', '.') . "</td></tr>
                            <tr><th>Bonus</th><td>Rp " . number_format($detail['bonus'], 0, ',', '.') . "</td></tr>
                            <tr class='table-info'><th>Total Salary</th><td><strong>Rp " . number_format($detail['total'], 0, ',', '.') . "</strong></td></tr>
                            <tr><th>Years of Work</th><td>{$detail['years_of_work']} tahun</td></tr>
                        </table>
                    </div>
                </div>
            </div>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
          <img src="" style="height: 50px; width: auto;"> HARTONO COLLECTION
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
                <li class="nav-item"><a class="nav-link" href="loginPage.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </li>
            </ul>
        </div>
    </div>
  </nav>
  <div class="container mt-5">
        <div class="text-center">
        <h2>Daftar Karyawan</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="all">
            <button type="submit" class="btn btn-success mb-3">Hitung Gaji Semua Karyawan</button>
        </form>
        </div>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Gaji</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
    <?php
    // Menampilkan karyawan non-pemilik
    $result = $conn->query("SELECT k.id_karyawan, k.nama, k.gaji, k.periode_terakhir, k.kode_karyawan
                            FROM karyawan k 
                            WHERE k.kode_karyawan NOT RLIKE '^[pP][0-9]+'");
    while ($row = $result->fetch_assoc()) {
        $periode_terakhir = $row['periode_terakhir'] ? new DateTime($row['periode_terakhir']) : null;
        $current_date = new DateTime();

        $status = ($periode_terakhir === null || $periode_terakhir->diff($current_date)->y >= 1) 
            ? '<span class="text-danger">Perlu Diperbarui</span>' 
            : '<span class="text-success">Terkini</span>';

        // Menentukan pekerjaan berdasarkan kode_karyawan
        $kode = $row['kode_karyawan'];
        if (preg_match('/^PG/i', $kode)) {
            $pekerjaan = "Penjaga Gudang";
        } elseif (preg_match('/^K/i', $kode)) {
            $pekerjaan = "Kasir";
        } else {
            $pekerjaan = "Lainnya";
        }

        echo "<tr>
            <td>{$row['nama']}</td>
            <td>{$pekerjaan}</td>
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
    <p class="mb-0">&copy; <?php echo date("Y"); ?> HARTONO COLLECTION. All rights reserved.</p>
    <p class="mb-0">Email: info@hartonocollection.com | Phone: (123) 456-7890</p>
  </div>
</footer>
</body>
</html>
