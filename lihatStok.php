<?php
// Menghubungkan koneksi database yang sudah ada
include('koneksi.php');  
include('produk.php'); // Menggunakan class Produk

// Inisialisasi class Produk
$produk = new Produk($conn);

// Fungsi untuk melihat stok dan harga barang
function lihatStok($kode_barang = null) {
    global $conn; // Menggunakan koneksi global

    // Jika kode_barang disediakan, tampilkan detail barang tertentu
    if ($kode_barang) {
        $query = "
            SELECT p.kode_barang, d.stok_gudang, p.harga 
            FROM produk p
            LEFT JOIN detail_produk d ON p.id_barang = d.id_barang  -- Menggunakan id_barang untuk JOIN
            WHERE p.kode_barang = ? AND p.status_aktif = 1
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $kode_barang); // Mengikat parameter kode_barang
    } else {
        // Jika tidak ada kode_barang, tampilkan semua barang
        $query = "
            SELECT p.kode_barang, d.stok_gudang, p.harga 
            FROM produk p
            LEFT JOIN detail_produk d ON p.id_barang = d.id_barang  -- Menggunakan id_barang untuk JOIN
            WHERE p.status_aktif = 1
        ";
        $stmt = $conn->prepare($query);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melihat Stok dan Harga Barang</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJQj99xu8pMfdUu6klI6xGbDbfeZJmWx5A6N8pXpR1a2eCZQ5U+1VzZNOh3v" crossorigin="anonymous">
    <style>
        /* Styling untuk tema biru */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #007bff;
            text-align: center;
            margin-top: 30px;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"], input[type="submit"] {
            font-size: 16px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .alert {
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
            padding: 10px;
            border-radius: 8px;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }

    </style>
</head>
<body>

    <div class="container">
        <h1>Melihat Stok dan Harga Barang</h1>

        <!-- Form untuk memasukkan kode barang -->
        <form method="GET" action="lihatStok.php">
            <div class="form-group">
                <label for="kode_barang">Masukkan Kode Barang (Optional):</label>
                <input type="text" id="kode_barang" name="kode_barang" class="form-control" placeholder="Masukkan kode barang...">
            </div>

            <button type="submit" class="btn btn-primary">Lihat Stok</button>
        </form>

        <?php
        // Mendapatkan kode barang dari input pengguna
        $kode_barang = isset($_GET['kode_barang']) ? $_GET['kode_barang'] : null;

        // Menampilkan stok barang hanya jika form disubmit
        if ($kode_barang !== null) {
            // Menampilkan stok barang
            $result = lihatStok($kode_barang);

            if ($result && $result->num_rows > 0) {
                // Menampilkan data produk dalam tabel
                echo "<table class='table table-bordered mt-4'>
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Stok Gudang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["kode_barang"]) . "</td>
                            <td>" . htmlspecialchars($row["stok_gudang"]) . "</td>
                            <td>" . number_format($row["harga"], 2) . "</td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-danger'>Tidak ada data barang yang ditemukan.</div>";
            }
        }
        ?>

    </div>

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybB6jl2HffQ2X7Scz5ptF5cH0g6XPtjAdfXJHeKSzTZRcd0i9m" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0V8Fq1FnD5k0lGc9eQ7o5PHDaMXYe+J6/9PqBO8WyEXpA9OZ" crossorigin="anonymous"></script>
</body>
</html>
