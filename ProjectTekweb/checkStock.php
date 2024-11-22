<?php
include 'koneksi.php'; // Database connection
include 'detailProduk.php'; // Your DetailProduk class

$detailProduk = new DetailProduk($conn);
$kode_barang = $_GET['kode_barang'];
$ukuran = $_GET['ukuran'];
$jumlah = $_GET['jumlah'];

// Get the current stock from the database
$stock = $detailProduk->checkStockToko($kode_barang, $ukuran);

// Check if stock is a valid number and return it
if (is_numeric($stock)) {
    echo $stock;
} else {
    echo 0; // Return 0 if the stock is not a valid number
}
?>
