<?php
session_set_cookie_params(0);

session_start();  // Start the session

// Check if the session variable 'role' exists and if it's one of the allowed roles
if (!isset($_SESSION['jabatan']) || $_SESSION['jabatan'] !== 'pemilik') {
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
    <title>Edit Harga</title>
    <!-- <link rel="stylesheet" href="Latihan Test (card form) Style.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
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
    <div class="container text-center mt-3">
        <div class="col-12 col-md-6">
          <form id = "price-form">
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
            <button type="submit" id="btnEditHarga" class="btn btn-primary">Edit Harga</button>
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
                                      $("#inputKode").val("");
                                      $("#inputStok").val("");
                                      $("#inputHarga").val("");
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
                              $("#inputKode").val("");
                              $("#inputStok").val("");
                              $("#inputHarga").val("");
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
</body>
</html>