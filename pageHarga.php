<?php
session_set_cookie_params(0);

session_start();  // Start the session

// Check if the session variable 'role' exists and if it's one of the allowed roles
if (!isset($_SESSION['jabatan']) || $_SESSION['jabatan'] !== 'pemilik') {
    // Redirect to login page if not logged in as pemilik
    header("Location: loginPage.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Harga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <style>
      /* Pastikan navbar dan container-fluid mengambil lebar penuh */
/* Pastikan navbar dan container-fluid mengambil lebar penuh */
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
  <!-- Search Bar -->
    <div class="container text-center mt-5">
    <h1>Edit Harga</h1>
      <div class="input-group">
        <div class="form-outline border rounded" data-mdb-input-init>
          <input type="search" id="findKode" class="form-control" placeholder="Search">
        </div>
        <button type="button" id="btnSearch" class="btn btn-primary" data-mdb-ripple-init>
          <i class="fas fa-search"></i>
        </button>
      </div>
    </div>


<!-- Price Form -->
<div class="container mt-3">
    <div class="col-12 col-md-6 mx-auto">
        <form id="price-form">
            <div class="row mb-3 align-items-center">
                <label for="inputKode" class="col-sm-3 col-form-label">Kode</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="inputKode" disabled>
                </div>
            </div>
            <div class="row mb-3 align-items-center">
                <label for="inputStok" class="col-sm-3 col-form-label">Stok</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="inputStok" disabled>
                </div>
            </div>
            <div class="row mb-3 align-items-center">
                <label for="inputHarga" class="col-sm-3 col-form-label">Harga</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="inputHarga" disabled>
                </div>
            </div>
            
            <!-- Menambahkan Flexbox untuk tombol agar berada di tengah -->
            <div class="d-flex justify-content-center">
                <button type="submit" id="btnEditHarga" class="btn btn-primary">Edit Harga</button>
            </div>
        </form>
    </div>
</div>

  
  <script>
    $(document).ready(function () {

      // SEARCH KODE
      $("#btnSearch").on("click", function () {
          const inputKode = $("#findKode").val();

          if (inputKode !== '') {
              $.ajax({
                  url: "searchKode.php", // File pencarian
                  method: "GET",
                  data: { inputKode: inputKode },
                  success: function (response) {
                      const data = JSON.parse(response);

                      // Jika data tidak ditemukan, tampilkan pesan error
                      if (data.error) {
                        Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Kode barang tidak ditemukan',
                        }).then((result) => {
                            // Setelah tombol "OK" diklik, kosongkan field
                            if (result.isConfirmed) {
                                $("#findKode").val("");
                            }
                          });
                      } 
                      // Jika data ditemukan, isi dengan data dari database
                      else {
                          $("#inputKode").val(data.kode_barang).prop("disabled", true); // Disable input kode
                          $("#inputStok").val(data.stok_toko).prop("disabled", true); // Disable input stok
                          $("#inputHarga").val(data.harga); // Isi harga

                          // Jika stok toko tidak ada, harga tidak bisa diedit
                          if (data.stok_toko == 0) {
                            $("#inputHarga").prop("disabled", true);
                            $("#btnEditHarga").prop("disabled", true);
                            // Tampilkan alert
                            setTimeout(function () {
                              Swal.fire({
                                icon: 'error',
                                title: 'Stok Barang Tidak Tersedia',
                                text: 'Harga tidak dapat diedit',
                              }).then((result) => {
                                  // Setelah tombol "OK" diklik, kosongkan field
                                  if (result.isConfirmed) {
                                      $("#findKode").val("");
                                      $("#inputKode").val("").prop("disabled", true);
                                      $("#inputStok").val("").prop("disabled", true);
                                      $("#inputHarga").val("").prop("disabled", true);
                                  }
                              });
                            }, 100); // Tunggu 100ms untuk memastikan data terlihat
                          } 
                          // Jika stok toko ada, maka harga bisa diedit
                          else {
                            $("#inputHarga").prop("disabled", false);
                            $("#btnEditHarga").prop("disabled", false);
                          }
                        }
                  },
                  error: function () {
                      console.log("Gagal mengambil data");
                  }
              });
        }
    });

    // EDIT HARGA
    $("#btnEditHarga").on("click", function() {
      event.preventDefault();
      const kodeBarang = $("#inputKode").val();
      const hargaBaru = $("#inputHarga").val();

      // Cek apakah textfield ada isinya
      if (kodeBarang !== "" && hargaBaru !== "") {
        // Validasi BR2: Harga harus bilangan positif dalam ribuan
        if (isNaN(hargaBaru) || parseInt(hargaBaru) <= 0 || parseInt(hargaBaru) % 100 !== 0) {
            Swal.fire({
              icon: 'error',
              title: 'Harga Tidak Valid',
              html: 'Harga harus dalam bentuk bilangan positif <br> dalam ribu rupiah',
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#inputHarga").val("");
                }
            });
            return;
        }

        // Harga harus lebih dari 1000
        if (parseInt(hargaBaru) < 1000) {
            Swal.fire({
                icon: 'error',
                title: 'Harga Tidak Valid',
                text: 'Harga harus lebih dari 1000 rupiah',
            });
            return;
        }

      // Log data yang dikirim ke server untuk debugging
      console.log("Kode Barang:", kodeBarang);
      console.log("Harga Baru:", hargaBaru);

      $.ajax({
        url: 'editHarga.php', // Endpoint untuk menyimpan data harga yang diedit
        method: 'POST',
        data: {
          kode_barang: kodeBarang,
          harga_baru: hargaBaru
        },
        success: function (response) {
            try {
                if (response.success) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: 'Perubahan harga berhasil disimpan!',
                    }).then((result) => {
                          if (result.isConfirmed) {
                              $("#findKode").val("");
                              $("#inputKode").val("").prop("disabled", true);
                              $("#inputStok").val("").prop("disabled", true);
                              $("#inputHarga").val("").prop("disabled", true);
                          }
                      });
                } 
                else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Gagal memperbarui harga',
                  }).then((result) => {
                          if (result.isConfirmed) {
                              $("#inputHarga").val("");
                          }
                      });
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                console.log('Response from server:', response); // Untuk debugging
            }
        },
        error: function () {
            console.log('Error saving data');
        }
    });
  } else {
      Swal.fire({
        icon: 'warning',
        title: 'Data Tidak Lengkap',
        text: 'Mohon isi semua data',
    });
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