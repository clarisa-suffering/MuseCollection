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

// Fungsi untuk memindahkan stok dari gudang ke toko
function pindahStok($id_detprod, $jumlah) {
    global $conn;

    // Cek stok gudang sebelum dipindahkan
    $stok_gudang = cekStokGudang($id_detprod);
    if ($stok_gudang >= $jumlah) {
        // Kurangi stok dari gudang
        $query_update_gudang = "UPDATE detail_produk SET stok_gudang = stok_gudang - ? WHERE id_detprod = ?";
        $stmt = $conn->prepare($query_update_gudang);
        $stmt->bind_param("ii", $jumlah, $id_detprod);
        $stmt->execute();
        $stmt->close();

        // Tambahkan stok ke toko
        $query_update_toko = "UPDATE detail_produk SET stok_toko = stok_toko + ? WHERE id_detprod = ?";
        $stmt = $conn->prepare($query_update_toko);
        $stmt->bind_param("ii", $jumlah, $id_detprod);
        $stmt->execute();
        $stmt->close();

        return true; // Stok berhasil dipindahkan
    } else {
        return false; // Stok gudang tidak cukup
    }
}

// Proses jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_detprod']) && isset($_POST['jumlah'])) {
        $id_detprod = $_POST['id_detprod']; // Mendapatkan id_detprod yang dipilih
        $jumlah = $_POST['jumlah'];

        // Pindahkan stok
        $berhasil = pindahStok($id_detprod, $jumlah);

        // Menampilkan notifikasi
        if ($berhasil) {
            echo "<div class='notification success'>Stok telah berhasil dipindahkan ke toko.</div>";
        } else {
            echo "<div class='notification error'>Stok gudang tidak cukup untuk dipindahkan.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memindah Stok Barang ke Toko</title>
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

    <h1>Memindah Stok Barang ke Toko</h1>

    <div class="container">
        <!-- Form untuk memasukkan kode barang -->
        <form method="POST" action="pindah_stok.php">
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
                echo '<form method="POST" action="pindah_stok.php">';
                echo '<div class="form-group">';
                echo '<label for="id_detprod">Pilih Produk:</label>';
                echo '<select name="id_detprod" required>';
                foreach ($produk_list as $produk) {
                    echo '<option value="' . $produk['id_detprod'] . '">' . $produk['kode_barang'] . ' - ' . $produk['ukuran'] . ' - Rp ' . number_format($produk['harga'], 0, ',', '.') . '</option>';
                }
                echo '</select>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<label for="jumlah">Jumlah Stok yang Dipindahkan:</label>';
                echo '<input type="number" id="jumlah" name="jumlah" value="' . (isset($_POST['jumlah']) ? $_POST['jumlah'] : '') . '" required>';
                echo '</div>';

                echo '<div class="form-group">';
                echo '<input type="submit" value="Konfirmasi Pemindahan">';
                echo '</div>';
                echo '</form>';
            } else {
                echo "<div class='notification error'>Produk dengan kode barang '$kode_barang' tidak ditemukan.</div>";
            }
        }
        ?>
    </div>
</body>
</html>
