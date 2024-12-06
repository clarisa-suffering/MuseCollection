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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top mb-4">
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
    <div class="container mb-3 pt-6">
        <div class="container text-center">
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
            if (!nama || !alamat || !nomorTelepon) {
                Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Untuk transaksi PO, input semua data pelanggan (nama, alamat, dan nomor telepon).',
                });
                return false;
            }
        } else if (kategori_penjualan === 'retail') {
            if (!nama) {
                Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Untuk transaksi Non-PO, input nama.',
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

                                
                                var row = `<tr>
                                    <td>${kodeProduk}</td>
                                    <td>${ukuran}</td>
                                    <td>${jumlah}</td>
                                    <td>${harga}</td>
                                    <td>${subtotal}</td>
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
            var subtotal = parseFloat($(this).find('td').eq(4).text());
            if (!isNaN(subtotal)) {
                total_harga += subtotal;
            }
        });
        $('#hargatotal').val(total_harga);
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
        var subtotal = $(this).find('td').eq(4).text();

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
    var harga_total = $('#hargatotal').val();

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
</body>
</html>