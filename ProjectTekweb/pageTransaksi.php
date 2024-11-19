<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>coba project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container mb-3">
    <!-- Pelanggan -->
<div class="container mb-3">
    <input class="form-control mb-3" id="nama" type="text" placeholder="Nama" aria-label="Nama">
    <input class="form-control mb-3" id="alamat" type="text" placeholder="Alamat" aria-label="Alamat">
    <input class="form-control mb-3" id="nomorTelepon" type="number" placeholder="Nomor Telepon" aria-label="Nomor Telepon">
    
    <div class="form-check">
        <input class="form-check-input" type="radio" name="transactionType" id="nonpo" value="retail" checked>
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
        <div id="suggestions" style="display:none"></div>
        <input class="form-control mb-3" id="ukuran" type="text" placeholder="Ukuran" aria-label="Ukuran">
        <input class="form-control mb-3" id="jumlah" type="number" placeholder="Jumlah" aria-label="Jumlah">
        <button type="button" id="addDetail" class="btn btn-primary">ADD</button>
    </div>

    <!-- Table to display selected products -->
    <table class="table table-success table-striped">
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
        <input class="form-control" type="text" id="hargatotal" value="Disabled readonly input" aria-label="Disabled harga" disabled readonly>
        <button type="button" id="confirmTransaction" class="btn btn-primary">CONFIRM</button>
    </div>

</div>

<script>

// Fetch product suggestions from the database when the user types in the "Kode Produk"
$('#kodeProduk').on('input', function() {
    var kodeProduk = $(this).val();
    if(kodeProduk.length > 1) {
        $.ajax({
            url: 'getProductSuggestions.php', // PHP script to fetch product suggestions
            method: 'GET',
            data: {kode_barang: kodeProduk},
            success: function(response) {
                $('#suggestions').html(response).show();
            }
        });
    } else {
        $('#suggestions').hide();
    }
});

// Add the product to the table when "ADD" button is clicked
$('#addDetail').on('click', function() {
    var kodeProduk = $('#kodeProduk').val();
    var ukuran = $('#ukuran').val();
    var jumlah = $('#jumlah').val();

    if (kodeProduk && ukuran && jumlah) {
        // Fetch harga from the database
        $.ajax({
            url: 'getHarga.php', // PHP script to get the price
            method: 'GET',
            data: { kode_barang: kodeProduk },
            success: function(harga) {
                harga = parseFloat(harga); // Convert to number if needed
                if (harga > 0) { // Check if harga is valid
                    var subtotal = harga * jumlah;

                    // Add the product to the table
                    var row = `<tr>
                        <td>${kodeProduk}</td>
                        <td>${ukuran}</td>
                        <td>${jumlah}</td>
                        <td>${harga}</td>
                        <td>${subtotal}</td>
                    </tr>`;
                    $('#productTableBody').append(row);

                    updateTotal();
                } else {
                    alert('Product not found');
                }
            },
            error: function() {
                alert('Error fetching harga');
            }
        });
    } else {
        alert('Please fill in all fields');
    }
});

function updateTotal() {
    var total_harga = 0;
    $('#productTableBody tr').each(function() {
        var subtotal = parseFloat($(this).find('td').eq(4).text());
        if (!isNaN(subtotal)) {
            total_harga += subtotal;
        }
    });
    $('#hargatotal').val(total_harga);  // Update the total in the input field
}



// Confirm the transaction when "CONFIRM" button is clicked
$('#confirmTransaction').on('click', function() {
    // Get Pelanggan data
    var nama = $('#nama').val();
    var alamat = $('#alamat').val();
    var nomorTelepon = $('#nomorTelepon').val();
    var kategori_penjualan = $('input[name="transactionType"]:checked').val(); // Get the selected transaction type

    // Get all products in the table
    var details = [];
    var totalRequests = 0; // To track the number of AJAX requests
    var completedRequests = 0; // To track how many AJAX requests have finished

    $('#productTableBody tr').each(function() {
        var kodeProduk = $(this).find('td').eq(0).text();
        var ukuran = $(this).find('td').eq(1).text();
        var jumlah = $(this).find('td').eq(2).text();
        var subtotal = $(this).find('td').eq(4).text();

        // Increment the total request count
        totalRequests++;

        // Fetch the id_detprod using kodeProduk and ukuran
        $.ajax({
            url: 'getDetProdId.php', // PHP script to fetch id_detprod
            method: 'GET',
            data: {
                kode_barang: kodeProduk,
                ukuran: ukuran
            },
            success: function(response) {
                if (response !== "Product size not found") {
                    // Add the fetched id_detprod to the details array
                    details.push({
                        id_detprod: response,
                        jumlah: jumlah,
                        subtotal: subtotal
                    });
                } else {
                    alert(response); // Alert error if no id_detprod is found
                }

                // Increment the completed request counter
                completedRequests++;

                // If all requests are completed, send the transaction data to the server
                if (completedRequests === totalRequests) {
                    confirmTransaction(nama, alamat, nomorTelepon, kategori_penjualan, details);
                }
            },
            error: function() {
                alert('Error fetching product ID');
            }
        });
    });
});

// Function to send the transaction data to the server
function confirmTransaction(nama, alamat, nomorTelepon, kategori_penjualan, details) {
    var harga_total = $('#hargatotal').val();

    $.ajax({
        url: 'confirmTransaction.php', // PHP script to save the transaction in the database
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





</script>
</body>
</html>
