<?php
// Koneksi ke database
$conn = mysqli_connect('localhost', 'root', '', 'project_tekweb');

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari database
$query = "SELECT * FROM stok_barang_gudang"; // Changed the table name to stok_barang_gudang
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Manajemen Stok Barang</title>
    <style>
        /* Modal Style */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Manajemen Stok Barang Gudang</h1>

    <!-- Tabel Data Stok Barang -->
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Barang</th>
                <th>Stok Barang</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['kode_barang']; ?></td>
                    <td><?= $row['nama_barang']; ?></td>
                    <td><?= $row['harga_barang']; ?></td>
                    <td><?= $row['stok_barang']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Tombol Add dan Sub -->
    <button onclick="document.getElementById('addModal').style.display='block'">Add</button>
    <button onclick="document.getElementById('subModal').style.display='block'">Sub</button>

    <!-- Modal Add -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
            <form action="proses.php" method="POST">
                <h2>Tambah Stok Barang</h2>
                <label>Kode Barang:</label><br>
                <input type="text" name="kode_barang" required><br>
                <label>Nama Barang:</label><br>
                <input type="text" name="nama_barang" required><br>
                <label>Harga Barang:</label><br>
                <input type="number" name="harga_barang" step="0.01" required><br>
                <label>Stok Barang:</label><br>
                <input type="number" name="stok_barang" required><br>
                <button type="submit" name="add">Tambah</button>
            </form>
        </div>
    </div>

    <!-- Modal Sub -->
    <div id="subModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('subModal').style.display='none'">&times;</span>
            <form action="proses.php" method="POST">
                <h2>Pindah Stok ke Toko</h2>
                <label>Kode Barang:</label><br>
                <input type="text" name="kode_barang" required><br>
                <label>Jumlah Pindah:</label><br>
                <input type="number" name="jumlah" required><br>
                <button type="submit" name="sub">Pindah</button>
            </form>
        </div>
    </div>

    <script>
        // Menutup modal jika klik di luar modal
        window.onclick = function(event) {
            if (event.target === document.getElementById('addModal')) {
                document.getElementById('addModal').style.display = "none";
            } else if (event.target === document.getElementById('subModal')) {
                document.getElementById('subModal').style.display = "none";
            }
        }
    </script>
</body>
</html>

<?php
// Proses tambah dan pindah stok
if (isset($_POST['add'])) {
    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $harga_barang = $_POST['harga_barang'];
    $stok_barang = $_POST['stok_barang'];

    $query = "INSERT INTO stok_barang_gudang (kode_barang, nama_barang, harga_barang, stok_barang) 
              VALUES ('$kode_barang', '$nama_barang', '$harga_barang', '$stok_barang')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
}

if (isset($_POST['sub'])) {
    $kode_barang = $_POST['kode_barang'];
    $jumlah = $_POST['jumlah'];

    // Update stok di gudang
    $query = "UPDATE stok_barang_gudang SET stok_barang = stok_barang - $jumlah WHERE kode_barang = '$kode_barang'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Stok berhasil dipindahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "Gagal memindahkan stok: " . mysqli_error($conn);
    }
}

// Menutup koneksi
mysqli_close($conn);
?>
