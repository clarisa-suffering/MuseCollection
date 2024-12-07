<?php
// Sertakan file koneksi
include 'koneksi.php';

// Ambil data dari AJAX (input kode)
if (isset($_GET['inputKode'])) {
    $kode = $conn->real_escape_string($_GET['inputKode']);

    // Query untuk mencari data berdasarkan kode
    $sql = "SELECT * FROM produk JOIN detail_produk 
            ON produk.id_barang = detail_produk.id_barang 
            WHERE produk.kode_barang = '$kode'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data); // Kirim data dalam format JSON
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]); // Kirim pesan error
    }
}

$conn->close();
?>
