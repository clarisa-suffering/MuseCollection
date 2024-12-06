<?php
// Menghubungkan koneksi database yang sudah ada
include('koneksi.php');

// Fungsi untuk mencari produk berdasarkan kode barang
function cariProduk($kode_barang) {
    global $conn;

    // Query untuk mengambil produk berdasarkan kode_barang
    $query = "SELECT p.id_barang, p.kode_barang, u.ukuran, p.harga, dp.id_detprod 
              FROM produk p
              JOIN detail_produk dp ON p.id_barang = dp.id_barang
              JOIN ukuran u ON dp.id_ukuran = u.id_ukuran
              WHERE p.kode_barang = ? AND dp.status_aktif = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $kode_barang);
    $stmt->execute();
    $result = $stmt->get_result();

    $produk = [];
    while ($row = $result->fetch_assoc()) {
        $produk[] = $row; // Menyimpan produk yang ditemukan dalam array
    }
    $stmt->close();
    return $produk;
}

// Fungsi untuk mengecek ketersediaan stok gudang berdasarkan id_detprod
function cekStokGudang($id_detprod) {
    global $conn;
    $query = "SELECT stok_gudang FROM detail_produk WHERE id_detprod = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_detprod);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['stok_gudang'];
    } else {
        return 0; // Tidak ditemukan
    }
}

// Fungsi untuk menambahkan stok ke gudang
// Fungsi untuk menambahkan stok ke gudang dan mencatat di detail_laporan
function tambahStokGudang($id_detprod, $jumlah) {
    global $conn;

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Tambahkan stok ke gudang
        $query_update_gudang = "UPDATE detail_produk SET stok_gudang = stok_gudang + ? WHERE id_detprod = ?";
        $stmt = $conn->prepare($query_update_gudang);
        $stmt->bind_param("ii", $jumlah, $id_detprod);
        $stmt->execute();
        $stmt->close();

        // Catat ke detail_laporan
        $query_insert_laporan = "INSERT INTO detail_laporan (id_detprod, quantity, status_in_out, tanggal_in_out) 
                                 VALUES (?, ?, ?, NOW())";
        $status_in_out = 'IN'; // Karena stok bertambah
        $stmt = $conn->prepare($query_insert_laporan);
        $stmt->bind_param("iis", $id_detprod, $jumlah, $status_in_out);
        $stmt->execute();
        $stmt->close();

        // Commit transaksi
        $conn->commit();

        return true; // Stok berhasil ditambahkan dan tercatat
    } catch (Exception $e) {
        // Rollback transaksi jika ada kesalahan
        $conn->rollback();
        return false; // Gagal
    }
}


// Proses jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_detprod']) && isset($_POST['jumlah'])) {
        $id_detprod = $_POST['id_detprod']; // Mendapatkan id_detprod yang dipilih
        $jumlah = $_POST['jumlah'];

        // Tambahkan stok ke gudang
        $berhasil = tambahStokGudang($id_detprod, $jumlah);

        // Menampilkan notifikasi
        if ($berhasil) {
            echo "<div class='notification success'>Stok telah berhasil ditambahkan ke gudang.</div>";
        } else {
            echo "<div class='notification error'>Terjadi kesalahan saat menambahkan stok.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menambahkan Stok Gudang</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
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

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .notification {
            padding: 15px;
            margin-top: 20px;
            text-align: center;
            border-radius: 6px;
            font-size: 16px;
        }

        .notification.success {
            background-color: #4CAF50;
            color: #fff;
        }

        .notification.error {
            background-color: #f44336;
            color: #fff;
        }

        select {
            font-size: 16px;
        }

        .form-group input[type="number"] {
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <h1>Menambahkan Stok Gudang</h1>

    <div class="container">
        <!-- Form untuk memasukkan kode barang -->
        <form method="POST" action="tambahStokGudang.php">
            <div class="form-group">
                <label for="kode_barang">Kode Barang:</label>
                <input type="text" id="kode_barang" name="kode_barang" value="<?php echo isset($_POST['kode_barang']) ? htmlspecialchars($_POST['kode_barang']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Cari Produk">
            </div>
        </form>

        <?php
        // Cek apakah kode_barang telah di-submit
        if (isset($_POST['kode_barang'])) {
            $kode_barang = $_POST['kode_barang'];

            // Cari produk berdasarkan kode_barang
            $produk_list = cariProduk($kode_barang);

            if (count($produk_list) > 0) {
                echo '<form method="POST" action="tambahStokGudang.php">';
                echo '<div class="form-group">';
                echo '<label for="id_detprod">Pilih Produk:</label>';
                echo '<select name="id_detprod" required>';
                foreach ($produk_list as $produk) {
                    echo '<option value="' . $produk['id_detprod'] . '">' . $produk['kode_barang'] . ' - ' . $produk['ukuran'] . ' - Rp ' . number_format($produk['harga'], 0, ',', '.') . '</option>';
                }
                echo '</select>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<label for="jumlah">Jumlah Stok yang Ditambahkan:</label>';
                echo '<input type="number" id="jumlah" name="jumlah" value="' . (isset($_POST['jumlah']) ? $_POST['jumlah'] : '') . '" required>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<input type="submit" value="Konfirmasi Penambahan">';
                echo '</div>';
                echo '</form>';
            } 
            else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Kode barang tidak ditemukan.'
                    });
            </script>";
            }
        }
        ?>
    </div>
</body>
</html>
<script>
        // Automatic fade-out for success/error notification
        setTimeout(function() {
            const notification = document.querySelector('.notification');
            if (notification) {
                notification.style.transition = 'opacity 1s ease-out';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 1000); // Remove the element after fade-out
            }
        }, 1000); // 3 seconds delay
</script>