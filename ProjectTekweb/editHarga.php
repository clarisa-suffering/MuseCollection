<?php
// Termasuk file koneksi dan kelas-kelas yang diperlukan
include 'koneksi.php';
include 'Produk.php';
include 'RiwayatHarga.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = $_POST['kode_barang'];
    $harga_baru = $_POST['harga_baru'];

    var_dump($kode_barang);
    var_dump($harga_baru);  

    // Buat objek Produk
    $produk = new Produk($conn);
    $produk->kode_barang = $kode_barang; // Atur kode barang

    // Mengecek apakah produk dengan kode tersebut ada
    if ($produk->getHargaBarang($kode_barang)) {
        // Mengubah harga produk
        if ($produk->perubahanHarga($harga_baru)) {
            echo json_encode(['success' => true, 'message' => 'Harga produk berhasil diubah dan disimpan ke riwayat.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengubah harga produk.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan.']);
    }
}
?>
