<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah ini adalah form untuk aksi stok (add/subtract)
    if (isset($_POST['action'])) {
        // Ambil data dari form aksi stok
        $action = $_POST['action'];
        $id_detprod = intval($_POST['id_detprod']);
        $jumlah = intval($_POST['jumlah']);

        // Validasi jumlah input harus lebih besar dari 0
        if ($jumlah <= 0) {
            header("Location: pageStokToko.php?alert=Jumlah harus lebih besar dari 0.");
            exit;
        }

        if ($action == 'add') {
            // Cek stok gudang saat ini
            $query = "SELECT stok_gudang, stok_toko FROM detail_produk WHERE id_detprod = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_detprod);
            $stmt->execute();
            $stmt->bind_result($stok_gudang, $stok_toko);
            $stmt->fetch();
            $stmt->close();

            if ($stok_gudang === null) {
                header("Location: pageStokToko.php?alert=Data tidak ditemukan untuk id_detprod = $id_detprod.");
                exit;
            }

            // Validasi stok gudang cukup untuk menambah stok toko
            if ($stok_gudang < $jumlah) {
                header("Location: pageStokToko.php?alert=Stok gudang tidak cukup untuk menambah stok toko sebanyak $jumlah.");
                exit;
            }

            // Tambah stok toko dan kurangi stok gudang
            $new_stok_toko = $stok_toko + $jumlah;
            $new_stok_gudang = $stok_gudang - $jumlah;

            $query = "UPDATE detail_produk SET stok_toko = ?, stok_gudang = ? WHERE id_detprod = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $new_stok_toko, $new_stok_gudang, $id_detprod);

            if ($stmt->execute()) {
                header("Location: pageStokToko.php");
            } else {
                header("Location: pageStokToko.php?alert=Terjadi kesalahan saat menambah stok.");
            }
            $stmt->close();
        } elseif ($action == 'subtract') {
            // Cek stok toko saat ini
            $query = "SELECT stok_toko FROM detail_produk WHERE id_detprod = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_detprod);
            $stmt->execute();
            $stmt->bind_result($stok_toko);
            $stmt->fetch();
            $stmt->close();

            if ($stok_toko === null) {
                header("Location: pageStokToko.php?alert=Data tidak ditemukan untuk id_detprod = $id_detprod.");
                exit;
            }

            // Validasi stok cukup untuk dikurangi
            if ($stok_toko < $jumlah) {
                header("Location: pageStokToko.php?alert=Stok toko tidak cukup untuk mengurangi sebanyak $jumlah.");
                exit;
            }

            // Kurangi stok toko (stok gudang tidak terpengaruh)
            $new_stok_toko = $stok_toko - $jumlah;

            $query = "UPDATE detail_produk SET stok_toko = ? WHERE id_detprod = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $new_stok_toko, $id_detprod);

            if ($stmt->execute()) {
                header("Location: pageStokToko.php");
            } else {
                header("Location: pageStokToko.php?alert=Terjadi kesalahan saat mengurangi stok.");
            }
            $stmt->close();
        } else {
            // Jika action tidak valid
            header("Location: pageStokToko.php?alert=Aksi tidak valid.");
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Cek jika pencarian dilakukan, hilangkan parameter 'alert'
    if (isset($_GET['search'])) {
        // Ambil parameter search
        $search_term = $_GET['search'];

        // Redirect ke halaman yang sama, tanpa parameter 'alert'
        header("Location: pageStokToko.php?search=" . urlencode($search_term));
        exit;
    }
} else {
    header("Location: pageStokToko.php?alert=Metode request tidak valid.");
}
?>