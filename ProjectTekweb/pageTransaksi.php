<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>coba project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="container mb-3">
    <!-- Pelanggan -->
    <div class="container mb-3">
        <input class="form-control mb-3" id="nama" type="text" placeholder="Nama" aria-label="Nama">
        <input class="form-control mb-3" id="alamat" type="text" placeholder="Alamat" aria-label="Alamat">
        <input class="form-control mb-3" id="nomorTelepon" type="number" placeholder="Nomor Telepon" aria-label="Nomor Telepon">

        <div class="form-check">
            <input class="form-check-input" type="radio" name="transactionType" id="nonpo" value="retail">
            <label class="form-check-label" for="nonpo">
                Transaksi langsung (Non-PO)
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="transactionType" id="po" value="PO">
            <label class="form-check-label" for="po">
                PO
            </label>
        </div>
    </div>

    <!-- Product Selection -->
    <div class="container mb-3">
        <input class="form-control mb-3" id="kodeProduk" type="text" placeholder="Kode Produk" aria-label="Kode Produk">
        <input class="form-control mb-3" id="ukuran" type="text" placeholder="Ukuran" aria-label="Ukuran">
        <input class="form-control mb-3" id="jumlah" type="number" placeholder="Jumlah" aria-label="Jumlah">
        <button type="button" id="addDetail" class="btn btn-primary">ADD</button>
    </div>

    <!-- Table to display selected products (Hidden by default) -->
    <table id="productTable" class="table table-success table-striped hidden">
      <thead>
        <tr>
          <th scope="col">Produk</th>
          <th scope="col">Ukuran</th>
          <th scope="col">Jumlah</th>
          <th scope="col">Harga Satuan</th>
          <th scope="col">Subtotal</th>
        </tr>
      </thead>
      <tbody id="productTableBody">
        <!-- Table rows will be added here dynamically -->
      </tbody>
    </table>

    <!-- Confirm Transaction -->
    <div class="container mb-3">
        <input class="form-control" type="text" id="hargatotal" value="" aria-label="Disabled harga" disabled readonly>
        <button type="button" id="confirmTransaction" class="btn btn-primary">CONFIRM</button>
    </div>
</div>

<script>
$(document).ready(function() {
    // Function to validate input fields based on transaction type
    function validateInputs() {
        var kategori_penjualan = $('input[name="transactionType"]:checked').val();
        var nama = $('#nama').val();
        var alamat = $('#alamat').val();
        var nomorTelepon = $('#nomorTelepon').val();

        if (kategori_penjualan === 'PO') {
            if (!nama || !alamat || !nomorTelepon) {
                alert('For PO transactions, please fill in all fields (Name, Address, and Phone Number).');
                return false;
            }
        } else if (kategori_penjualan === 'retail') {
            if (!nama) {
                alert('For Non-PO transactions, please fill in the Name field.');
                return false;
            }
        }
        return true;
    }

    // Function to clear the product table
    function clearTable() {
        $('#productTableBody').empty();
        $('#productTable').addClass('hidden');
        $('#hargatotal').val('0');
    }

    // Monitor changes in transaction type and clear table if changed
    $('input[name="transactionType"]').on('change', function() {
        if (validateInputs()) {
            $('#productTable').removeClass('hidden');
        } else {
            clearTable();
        }
    });

    // Real-time validation for PO fields
    $('#alamat, #nomorTelepon').on('input', function() {
        if ($('input[name="transactionType"]:checked').val() === 'PO') {
            var alamat = $('#alamat').val();
            var nomorTelepon = $('#nomorTelepon').val();
            if (!alamat || !nomorTelepon) {
                alert('For PO transactions, please do not leave the Address or Phone Number field empty.');
            }
        }
    });

    // Add product to the table when "ADD" button is clicked
    $('#addDetail').on('click', function() {
    if (!validateInputs()) {
        return;
    }

    var kodeProduk = $('#kodeProduk').val();
    var ukuran = $('#ukuran').val();
    var jumlah = $('#jumlah').val();

    if (kodeProduk && ukuran && jumlah) {
        // AJAX call to check stock availability using your PHP code
        $.ajax({
            url: 'checkStock.php', // Your PHP code for stock check
            method: 'GET',
            data: {
                kode_barang: kodeProduk,
                ukuran: ukuran,
                jumlah: jumlah
            },
            success: function(response) {

                if (response >= jumlah) {
                    // If stock is sufficient, proceed with getting the price and adding the product to the table
                    $.ajax({
                        url: 'getHarga.php', // Fetch price using another PHP script
                        method: 'GET',
                        data: { kode_barang: kodeProduk },
                        success: function(harga) {
                            harga = parseFloat(harga);
                            if (harga > 0) {
                                var subtotal = harga * jumlah;

                                // Add the product to the table
                                var row = `<tr>
                                    <td>${kodeProduk}</td>
                                    <td>${ukuran}</td>
                                    <td>${jumlah}</td>
                                    <td>${harga}</td>
                                    <td>${subtotal}</td>
                                </tr>`;

                                // Add the row to the table body
                                $('#productTableBody').append(row);

                                // Update the total price after adding the new row
                                updateTotal();

                                // Make sure the table is visible
                                $('#productTable').removeClass('hidden');
                            } else {
                                alert('Product not found');
                            }
                        },
                        error: function() {
                            alert('Error fetching price');
                        }
                    });
                } else if (response<jumlah){
                    // If stock is not available, show an alert
                    alert('Not enough stock. Available stock: ' + response);
                }
                else{
                    alert('Product not found');
                }
            },
            error: function() {
                alert('Error checking stock');
            }
        });
    } else {
        alert('Please fill in all fields');
    }
});


    // Update total price in the hargatotal input
    function updateTotal() {
        var total_harga = 0;
        $('#productTableBody tr').each(function() {
            var subtotal = parseFloat($(this).find('td').eq(4).text());
            if (!isNaN(subtotal)) {
                total_harga += subtotal;
            }
        });
        $('#hargatotal').val(total_harga);
    }

    // Confirm transaction when "CONFIRM" button is clicked
    $('#confirmTransaction').on('click', function() {
        if (!validateInputs()) {
            return;
        }

        var nama = $('#nama').val();
        var alamat = $('#alamat').val();
        var nomorTelepon = $('#nomorTelepon').val();
        var kategori_penjualan = $('input[name="transactionType"]:checked').val();

        var details = [];
        var totalRequests = 0;
        var completedRequests = 0;

        $('#productTableBody tr').each(function() {
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
                    var data = JSON.parse(response); // Parse the response
                    if (data.error) {
                        alert('Product not found');
                        return;
                    }

                    details.push({
                        id_detprod: data.id_detprod,
                        jumlah: jumlah,
                        subtotal: subtotal
                    });

                    completedRequests++;

                    if (completedRequests === totalRequests) {
                        confirmTransaction(nama, alamat, nomorTelepon, kategori_penjualan, details);
                    }
                },
                error: function() {
                    alert('Error fetching product details');
                }
            });
        });
});

function confirmTransaction(nama, alamat, nomorTelepon, kategori_penjualan, details) {
        var harga_total = $('#hargatotal').val();

        $.ajax({
            url: 'confirmTransaction.php',
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
                if (response == 'Success') {
                    alert('Transaction confirmed');
                } else {
                    alert('Error confirming transaction: ' + response);
                }
            },
            error: function() {
                alert('Error processing transaction');
            }
        });
    }
});
</script>
</body>
</html>