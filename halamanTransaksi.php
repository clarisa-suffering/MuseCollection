
<?php
session_set_cookie_params(0);

session_start();  // Start the session

// Check if the session variable 'role' exists and if it's one of the allowed roles
if (!isset($_SESSION['jabatan']) || ($_SESSION['jabatan'] !== 'kasir' && $_SESSION['jabatan'] !== 'pemilik')) {
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
    <title>Halaman Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* untuk hide table */
        .hidden {
            display: none;
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
        html, body {
            height: 100%; 
            margin: 0; 
            display: flex; 
            flex-direction: column; 
        }

        footer {
            background-color: #332D2D;
            color: white;
            text-align: center;
            padding: 20px 0;
            width: 100%;
            margin-top: auto;
            position: relative; 
            z-index: 1; 
        }

        .container {
            flex: 1 0 auto; 
        }

        .modal {
            z-index: 1050; 
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
    <div class="container mb-3 pt-6">
        <div class="container text-center">
            <h1></h1>
    <h1>Transaksi</h1>
    </div>
    <div class="row justify-content-center">
    <!-- Pelanggan dan jenis transaksi -->
    <div class="container mb-3 pt-6">
        <input class="form-control mb-3" id="nama" type="text" placeholder="Nama" aria-label="Nama">
        <input class="form-control mb-3" id="alamat" type="text" placeholder="Alamat" aria-label="Alamat">
        <input class="form-control mb-3" id="nomorTelepon" type="number" placeholder="Nomor Telepon" aria-label="Nomor Telepon">

        <div class="form-check">
            <input class="form-check-input" type="radio" name="kategori" id="nonpo" value="retail">
            <label class="form-check-label" for="nonpo">
                Transaksi langsung (Non-PO)
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="kategori" id="po" value="PO">
            <label class="form-check-label" for="po">
                PO
            </label>
        </div>
    </div>
    </div>

    <!-- Input detail transaksi default hidden -->
    <div class="container mb-3 hidden" id="divDetail">
        <div class="container mb-3">
            <input class="form-control mb-3" id="kodeProduk" type="text" placeholder="Kode Produk" aria-label="Kode Produk">
            <input class="form-control mb-3" id="ukuran" type="text" placeholder="Ukuran" aria-label="Ukuran">
            <input class="form-control mb-3" id="jumlah" type="number" placeholder="Jumlah" aria-label="Jumlah">
            <button type="button" id="btnAddDetail" class="btn btn-primary">ADD</button>
        </div>

        <!-- tabel produk-->
        <table id="tblProdukHead" class="table table-success table-striped">
        <thead>
            <tr>
            <th scope="col">Produk</th>
            <th scope="col">Ukuran</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Harga Satuan</th>
            <th scope="col">Subtotal</th>
            </tr>
        </thead>
        <tbody id="tblProduk">
            <!-- menampung detail transaksi -->
        </tbody>
        </table>

        <!-- konfirm transaksi -->
        <div class="container mb-3">
            <div class="row">
                <div class="col-6 d-flex flex-column align-items-end">
                <label for="hargatotal" class="form-label">Total</label>
                </div>
                <div class="col-6 d-flex flex-column align-items-end">
                    <input class="form-control" type="text" id="hargatotal" value="" aria-label="Disabled harga" disabled readonly>
                </div>
                <div class="container mb-3">
                    <button type="button" id="btnKonfirmasiTransaksi" class="btn btn-primary">CONFIRM</button>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<div class="modal fade" id="modalSuksesReduce" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">PENGURANGAN STOK BERHASIL</h1>
            </div>
            <div class="modal-body">
                Pengurangan stok terkonfirmasi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk transaksi sukses -->
<div class="modal fade" id="modalSukses" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">PENCATATAN TRANSAKSI BERHASIL</h1>
            </div>
            <div class="modal-body">
                Transaksi terkonfirmasi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // validasi input data pelanggan utk kategori
    function validasiInput() {
        var kategori_penjualan = $('input[name="kategori"]:checked').val();
        var nama = $('#nama').val();
        var alamat = $('#alamat').val();
        var nomorTelepon = $('#nomorTelepon').val();

        if (kategori_penjualan === 'PO') {
            if ((!nama || !alamat || !nomorTelepon) || !/^08[0-9]{8,13}$/.test(nomorTelepon)) {
                Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Untuk transaksi PO, input semua data pelanggan (nama, alamat, dan nomor telepon) secara valid.',
                }).then(() => {
                    // Membuat kategori penjualan tidak tercentang
                    $('input[name="kategori"]').prop('checked', false);
                    $('#divDetail').addClass('hidden');
                    });
                    return false;
                
            }
        } else if (kategori_penjualan === 'retail') {
            if ((!nama )) {
                Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Untuk transaksi Non-PO, input nama.',
                }).then(() => {
                    // Membuat kategori penjualan tidak tercentang
                    $('input[name="kategori"]').prop('checked', false);
                    $('#divDetail').addClass('hidden');
                    });
                return false;
            }
        }
        return true;
    }

    // clear tabel
    function clearTable() {
        $('#tblProduk').empty();
        $('#hargatotal').val('0');
    }

    // clear tabel if ganti kategori transaksi
    $('input[name="kategori"]').on('change', function() {
        if (validasiInput()) {
            $('#divDetail').removeClass('hidden');
            clearTable();
        } else {
            clearTable();
        }
    });

    $('#alamat, #nomorTelepon').on('input', function() {
        if ($('input[name="kategori"]:checked').val() === 'PO') {
            var alamat = $('#alamat').val();
            var nomorTelepon = $('#nomorTelepon').val();
            if (!alamat || !nomorTelepon) {
                Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Untuk transaksi PO, input alamat dan nomor telepon.',
                });
            }
        }
    });

    $('#btnAddDetail').on('click', function() {
    if (!validasiInput()) {
        return;
    }

    var kodeProduk = $('#kodeProduk').val();
    var ukuran = $('#ukuran').val();
    var jumlah = parseInt($('#jumlah').val(), 10);
    var kategori_penjualan = $('input[name="kategori"]:checked').val();

    if (kodeProduk && ukuran && jumlah) {
        $.ajax({
            url: 'cekStok.php',
            method: 'GET',
            data: {
                kategori_penjualan,
                kode_barang: kodeProduk,
                ukuran: ukuran,
                jumlah: jumlah
            },
            success: function(response) {
                console.log(jumlah)
                console.log(response)

                if (response >= jumlah) {
                    $.ajax({
                        url: 'getHargaBarang.php',
                        method: 'GET',
                        data: { kode_barang: kodeProduk },
                        success: function(harga) {
                            harga = parseFloat(harga);
                            if (harga > 0) {
                                var subtotal = hitungSubtotal(jumlah, harga);

                                var hargaFormatted = new Intl.NumberFormat('id-ID').format(harga);
                                var subtotalFormatted= new Intl.NumberFormat('id-ID').format(subtotal);
                                var row = `<tr>
                                    <td>${kodeProduk}</td>
                                    <td>${ukuran}</td>
                                    <td>${jumlah}</td>
                                    <td>${hargaFormatted}</td>
                                    <td>${subtotalFormatted}</td>
                                </tr>`;

                                $('#tblProduk').append(row);

                                hitungTotal();
                               
                                $('#divDetail').removeClass('hidden');
                            } else {
                                Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Produk tidak ditemukan.',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Harga produk tidak ditemukan.',
                            });
                        }
                    });
                } else if (response< jumlah){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Stok tidak cukup. Stok tersedia: ' + response,
                    });
                }
                else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                    });
                }
            },
            error: function() {
                Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error mengecek stok.',
                    });
            }
        });
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Input tidak lengkap',
            text: 'Isi semua field.',
        });
    }

    $('#kodeProduk').val('');
    $('#ukuran').val('');
    $('#jumlah').val('');  
});

    function hitungSubtotal(jumlah, harga){
        var subtotal= jumlah*harga;
        return subtotal
    }

// hitung total
function hitungTotal() {
    var total_harga = 0;
    $('#tblProduk tr').each(function() {
        var subtotalText = $(this).find('td').eq(4).text(); // Ambil teks subtotal
        var subtotal = parseFloat(subtotalText.replace(/\./g, '')); // Hapus pemisah ribuan
        if (!isNaN(subtotal)) {
            total_harga += subtotal;
        }
    });
    // Format total harga sebelum menampilkan
    var totalFormatted = new Intl.NumberFormat('id-ID').format(total_harga);
    $('#hargatotal').val(totalFormatted);
}

// konfirmasi transaksi
$('#btnKonfirmasiTransaksi').on('click', function() {
    if (!validasiInput()) {
        return;
    }

    var nama = $('#nama').val();
    var alamat = $('#alamat').val();
    var nomorTelepon = $('#nomorTelepon').val();
    var kategori_penjualan = $('input[name="kategori"]:checked').val();

    var details = [];
    var totalRequests = 0;
    var completedRequests = 0;

    if ($('#tblProduk tr').length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Input tidak lengkap',
            text: 'Tidak ada produk yang ditambahkan. Tambahkan produk terlebih dahulu.',
        });
        return;
    }

    $('#tblProduk tr').each(function() {
        var kodeProduk = $(this).find('td').eq(0).text();
        var ukuran = $(this).find('td').eq(1).text();
        var jumlah = $(this).find('td').eq(2).text();
        var subtotalText = $(this).find('td').eq(4).text();
        var subtotal = parseFloat(subtotalText.replace(/\./g, ''));

        totalRequests++;

        $.ajax({
            url: 'getDetProdId.php',
            method: 'GET',
            data: {
                kode_barang: kodeProduk,
                ukuran: ukuran
            },
            success: function(response) {
                if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Produk tidak ditemukan.',
                    });
                    return;
                }

                details.push({
                    id_detprod: response,
                    jumlah: jumlah,
                    subtotal: subtotal
                });

                completedRequests++;

                if (completedRequests === totalRequests) {
                    mengurangiStok(nama, alamat, nomorTelepon, kategori_penjualan, details);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error mencari data produk.',
                });
            }
        });
    });
});

function mengurangiStok(nama, alamat, nomorTelepon, kategori_penjualan, details) {
    var hargaTotalText = $('#hargatotal').val();
    var harga_total = parseFloat(hargaTotalText.replace(/\./g, ''));

    $.ajax({
        url: 'mengurangiStok.php',
        method: 'POST',
        data: {
            kategori_penjualan: kategori_penjualan,
            details: JSON.stringify(details)
        },
        success: function(response) {
            if (response === 'Sukses') {
                window.transactionData = {
                    nama: nama,
                    alamat: alamat,
                    nomorTelepon: nomorTelepon,
                    kategori_penjualan: kategori_penjualan,
                    harga_total: harga_total,
                    details: details
                };

                Swal.fire({
                    icon: 'success',
                    title: 'Stok Berhasil Dikurangi',
                    text: 'Pengurangan stok berhasil diproses.',
                }).then((result) => {
                    if (result.isConfirmed || result.isDismissed) {
                        var data = window.transactionData;
                        if (data) {
                            mencatatTransaksi(data.nama, data.alamat, data.nomorTelepon, data.kategori_penjualan, data.harga_total, data.details);
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response,
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error mengurangi stok.',
            });
        }
    });
}


function mencatatTransaksi(nama, alamat, nomorTelepon, kategori_penjualan, harga_total, details) {
    $.ajax({
        url: 'konfirmasiTransaksi.php',
        method: 'POST',
        data: {
            nama: nama,
            alamat: alamat,
            nomor_telepon: nomorTelepon,
            kategori_penjualan: kategori_penjualan,
            harga_total: harga_total,
            details: JSON.stringify(details)
        },
        success: function(response) {
            if (response == 'Sukses') {
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil',
                    text: 'Transaksi telah dicatat.',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Clear all input fields
                        $('#nama').val('');
                        $('#alamat').val('');
                        $('#nomorTelepon').val('');
                        $('#kodeProduk').val('');
                        $('#ukuran').val('');
                        $('#jumlah').val('');
                        $('#hargatotal').val('0');

                        $('#tblProduk').empty();

                        $('#divDetail').addClass('hidden');

                        $('input[name="kategori"]').prop('checked', false);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response,
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error mencatat transaksi.',
            });
        }
    });
}

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
