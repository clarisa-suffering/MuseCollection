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
          <th scope="col">Subtotal</th>
        </tr>
      </thead>
      <tbody id="productTableBody">
        <!-- Table rows will be added here dynamically -->
      </tbody>
    </table>

    <!-- Confirm Transaction -->
    <div class="container mb-3">
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
                        <td>${subtotal}</td>
                    </tr>`;
                    $('#productTableBody').append(row);
                } else {
                    alert('Price not found for the selected product');
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


// Confirm the transaction when "CONFIRM" button is clicked
$('#confirmTransaction').on('click', function() {
    // Get all products in the table
    var products = [];
    $('#productTableBody tr').each(function() {
        var kodeProduk = $(this).find('td').eq(0).text();
        var ukuran = $(this).find('td').eq(1).text();
        var jumlah = $(this).find('td').eq(2).text();
        var subtotal = $(this).find('td').eq(3).text();
        
        products.push({
            kode_produk: kodeProduk,
            ukuran: ukuran,
            jumlah: jumlah,
            subtotal: subtotal
        });
    });

    // Send the products to the server to insert into the database
    $.ajax({
        url: 'confirmTransaction.php', // PHP script to save the transaction in database
        method: 'POST',
        data: {products: JSON.stringify(products)},
        success: function(response) {
            if(response == 'Success') {
                alert('Transaction confirmed');
            } else {
                alert('Error confirming transaction');
            }
        }
    });
});
</script>
</body>
</html>
