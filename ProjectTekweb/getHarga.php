<?php
include 'koneksi.php'; // Database connection
include 'produk.php'; // Your Produk class

$produk = new produk($conn);
$kode_barang = $_GET['kode_barang'];
$harga = $produk->getPrice($kode_barang);
if ($harga != null) {
    echo $harga; // Return the price to the AJAX call
} else {
    echo '0'; // Return 0 if no price is found
}
?>
