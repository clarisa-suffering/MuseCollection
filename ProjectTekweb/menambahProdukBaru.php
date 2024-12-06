<?php
include 'koneksi.php';

// Fungsi untuk menambah produk baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Ambil data dari form
    $kode_barang = $_POST['kode_barang'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];
    $id_ukuran = $_POST['id_ukuran'];  // Ambil id_ukuran dari form

    // Cek jika harga dan jumlah tidak valid (0 atau negatif)
    if ($harga <= 0 || $jumlah <= 0) {
        $error = true;
        $error_message = "Harga dan Jumlah harus lebih besar dari 0.";
    } else {
        // Query untuk menambah produk baru
        $sql = "INSERT INTO produk (kode_barang, harga) VALUES ('$kode_barang', '$harga')";
        
        if ($conn->query($sql) === TRUE) {
            // Ambil id_barang dari produk yang baru ditambahkan
            $id_barang = $conn->insert_id;
            
            // Menambahkan stok ke detail_produk dengan id_ukuran
            $sqlDetail = "INSERT INTO detail_produk (id_barang, stok_gudang, id_ukuran) VALUES ('$id_barang', '$jumlah', '$id_ukuran')";
            if ($conn->query($sqlDetail) === TRUE) {
                // Ambil id_detprod yang baru ditambahkan dari detail_produk
                $id_detprod = $conn->insert_id;

                // Setelah produk berhasil ditambahkan, masukkan transaksi ke detail_laporan
                $tanggal = date('Y-m-d'); // Tanggal hari ini
                $status_in_out = "+"; // Status in (produk masuk)
                $quantity = $jumlah; // Jumlah yang dimasukkan

                // Query untuk memasukkan data ke detail_laporan
                $sqlLaporan = "INSERT INTO detail_laporan (id_detprod, quantity, tanggal_in_out, status_in_out)
                               VALUES ('$id_detprod', '$quantity', '$tanggal', '$status_in_out')";
                if ($conn->query($sqlLaporan) === TRUE) {
                    $success = true; // Tanda bahwa produk dan laporan berhasil ditambahkan
                } else {
                    $error = true; // Jika gagal memasukkan ke detail_laporan
                    $error_message = "Gagal mencatat transaksi di detail_laporan.";
                }
            } else {
                $error = true; // Jika gagal memasukkan ke detail_produk
                $error_message = "Gagal menambahkan detail produk.";
            }
        } else {
            $error = true; // Jika gagal memasukkan ke produk
            $error_message = "Gagal menambahkan produk.";
        }
    }
}

// Query untuk menampilkan daftar produk
$sqlProduk = "SELECT 
                p.id_barang,
                p.kode_barang,
                p.harga,
                dp.stok_gudang,
                u.ukuran
            FROM 
                produk p
            JOIN 
                detail_produk dp ON p.id_barang = dp.id_barang
            JOIN 
                ukuran u ON dp.id_ukuran = u.id_ukuran
            ORDER BY 
                p.kode_barang ASC";

$resultProduk = $conn->query($sqlProduk);

// Query untuk mendapatkan ukuran yang tersedia
$sqlUkuran = "SELECT * FROM ukuran";
$resultUkuran = $conn->query($sqlUkuran);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.9/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.9/dist/sweetalert2.all.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            cursor: pointer;
        }
        table th:hover {
            background-color: #e0e0e0;
        }
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
        }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar Produk</h1>
    
    <!-- Tombol untuk menambah produk -->
    <button class="btn" id="addProductBtn">Tambah Produk</button>
    
    <!-- Daftar Produk -->
    <?php if ($resultProduk->num_rows > 0): ?>
        <table id="productTable">
            <thead>
                <tr>
                    <th id="sortIdBarang">ID Barang</th>
                    <th id="sortKodeBarang">Kode Barang</th>
                    <th id="sortHarga">Harga</th>
                    <th id="sortStok">Stok Gudang</th>
                    <th id="sortUkuran">Ukuran</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultProduk->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_barang'] ?></td>
                        <td><?= $row['kode_barang'] ?></td>
                        <td><?= number_format($row['harga'], 2) ?></td>
                        <td><?= $row['stok_gudang'] ?></td>
                        <td><?= $row['ukuran'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada produk yang tersedia.</p>
    <?php endif; ?>
</div>

<!-- Modal untuk Menambahkan Produk -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <h3>Tambah Produk Baru</h3>
        <form method="POST" action="">
            <label for="kode_barang">Kode Barang:</label>
            <input type="text" id="kode_barang" name="kode_barang" required>
            
            <label for="harga">Harga:</label>
            <input type="number" id="harga" name="harga" required>
            
            <label for="jumlah">Stok Gudang:</label>
            <input type="number" id="jumlah" name="jumlah" required>
            
            <label for="id_ukuran">Ukuran:</label>
            <select name="id_ukuran" id="id_ukuran" required>
                <?php while ($row = $resultUkuran->fetch_assoc()): ?>
                    <option value="<?= $row['id_ukuran'] ?>"><?= $row['ukuran'] ?></option>
                <?php endwhile; ?>
            </select>
            
            <button type="submit" name="submit" class="btn">Simpan Produk</button>
            <button type="button" class="btn" id="closeModalBtn">Tutup</button>
        </form>
    </div>
</div>

<script>
    // Menangani tombol "Tambah Produk"
    document.getElementById('addProductBtn').addEventListener('click', function() {
        document.getElementById('productModal').style.display = 'flex';
    });

    // Menangani tombol "Tutup" untuk modal
    document.getElementById('closeModalBtn').addEventListener('click', function() {
        document.getElementById('productModal').style.display = 'none';
    });

    // Mengatur flag untuk arah sorting
    let sortAsc = {
        id_barang: true,
        kode_barang: true,
        harga: true,
        stok: true,
        ukuran: true
    };

    // Definisikan urutan untuk ukuran (Small, Medium, Large, XXL, dsb.)
    const ukuranOrder = ['Small', 'Medium', 'Large', 'X-Large', 'XX-Large'];

    // Mengambil elemen header yang bisa disortir
    const headers = document.querySelectorAll('table th');

    headers.forEach(header => {
        header.addEventListener('click', function() {
            const columnIndex = Array.from(header.parentNode.children).indexOf(header);
            const columnName = header.id.replace('sort', '').toLowerCase();
            sortTable(columnIndex, columnName);
        });
    });

    // Fungsi untuk sorting tabel
    function sortTable(columnIndex, columnName) {
        const table = document.getElementById('productTable');
        const rows = Array.from(table.rows).slice(1); // Mengambil semua baris kecuali header

        // Menentukan apakah urutan akan menaik atau menurun
        const isAscending = sortAsc[columnName];

        // Sorting berdasarkan kolom yang diklik
        rows.sort((rowA, rowB) => {
            const cellA = rowA.cells[columnIndex].textContent.trim();
            const cellB = rowB.cells[columnIndex].textContent.trim();

            // Parsing harga, ukuran, dan ID Barang untuk sorting yang benar
            let valueA, valueB;
            if (columnName === 'harga') {
                valueA = parseFloat(cellA.replace(/[^0-9.-]+/g, "")); // Hapus simbol mata uang dan parse float
                valueB = parseFloat(cellB.replace(/[^0-9.-]+/g, ""));
            } else if (columnName === 'ukuran') {
                valueA = ukuranOrder.indexOf(cellA); // Menyusun berdasarkan urutan ukuran
                valueB = ukuranOrder.indexOf(cellB);
            } else if (columnName === 'id_barang') {
                valueA = parseInt(cellA); // ID Barang disortir secara numerik
                valueB = parseInt(cellB);
            } else {
                valueA = cellA;
                valueB = cellB;
            }

            if (isAscending) {
                return valueA > valueB ? 1 : valueA < valueB ? -1 : 0;
            } else {
                return valueA < valueB ? 1 : valueA > valueB ? -1 : 0;
            }
        });

        // Menyusun ulang baris tabel
        rows.forEach(row => table.appendChild(row));

        // Toggle arah sorting
        sortAsc[columnName] = !isAscending;
    }

    // SweetAlert untuk success atau error setelah simpan produk
    <?php if (isset($success) && $success === true): ?>
        Swal.fire({
            icon: 'success',
            title: 'Produk Berhasil Ditambahkan',
            text: 'Produk baru berhasil disimpan ke dalam database.',
        });
    <?php elseif (isset($error) && $error === true): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= isset($error_message) ? $error_message : "Terjadi kesalahan." ?>',
        });
    <?php endif; ?>
</script>

</body>
</html>